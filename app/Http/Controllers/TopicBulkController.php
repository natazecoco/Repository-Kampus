<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class TopicBulkController extends Controller
{
    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }

    public function export()
    {
        $this->authorizeAdmin();

        $topics = Topic::with(['parent', 'mergedBy'])->orderBy('name')->get();
        $filename = 'topics-export-' . now()->format('Ymd-His') . '.csv';

        return Response::streamDownload(function () use ($topics) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id',
                'name',
                'slug',
                'parent_id',
                'parent_name',
                'parent_slug',
                'is_active',
                'sort_order',
                'merged_into',
                'merged_by',
                'merged_at',
                'created_at',
                'updated_at',
            ]);

            foreach ($topics as $topic) {
                fputcsv($handle, [
                    $topic->id,
                    $topic->name,
                    $topic->slug,
                    $topic->parent_id,
                    $topic->parent?->name,
                    $topic->parent?->slug,
                    $topic->is_active ? '1' : '0',
                    $topic->sort_order,
                    $topic->merged_into,
                    $topic->mergedBy?->name,
                    $topic->merged_at?->toDateTimeString(),
                    $topic->created_at->toDateTimeString(),
                    $topic->updated_at->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function showImport()
    {
        $this->authorizeAdmin();

        return view('admin.topics.import');
    }

    public function import(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'csv' => ['nullable', 'string'],
            'csv_file' => ['nullable', 'file', 'mimes:csv,txt'],
        ]);

        $csvContent = null;
        if ($request->hasFile('csv_file')) {
            $csvContent = file_get_contents($request->file('csv_file')->getRealPath());
        }

        if ($csvContent === null) {
            $csvContent = trim($data['csv'] ?? '');
        }

        if ($csvContent === '') {
            return Redirect::back()->withErrors(['csv' => 'CSV tidak boleh kosong.']);
        }

        $rows = preg_split('/\r\n|\n|\r/', trim($csvContent));
        $created = 0;
        $updated = 0;
        $skipped = 0;

        $headers = null;

        foreach ($rows as $index => $row) {
            if (trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);
            if ($index === 0 && $this->looksLikeHeader($columns)) {
                $headers = array_map(fn ($value) => strtolower(trim($value)), $columns);
                continue;
            }

            $record = $this->parseCsvRow($columns, $headers);
            if (! $record['name']) {
                $skipped++;
                continue;
            }

            $topic = Topic::firstOrNew(['slug' => $record['slug'] ?? Str::slug($record['name'])]);
            $topic->name = $record['name'];
            $topic->slug = $record['slug'] ?: Str::slug($record['name']);
            $topic->is_active = $record['is_active'];
            $topic->sort_order = $record['sort_order'];
            $topic->parent_id = $this->resolveParentId($record['parent']);
            $topic->save();

            if ($topic->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        return Redirect::back()->with('status', "Impor berhasil: {$created} dibuat, {$updated} diperbarui, {$skipped} dilewati.");
    }

    public function duplicates()
    {
        $this->authorizeAdmin();

        $topics = Topic::orderBy('name')->get();
        $pairs = $this->findDuplicatePairs($topics);

        return view('admin.topics.duplicates', [
            'pairs' => $pairs,
        ]);
    }

    protected function looksLikeHeader(array $columns): bool
    {
        $headerFields = ['name', 'slug', 'parent', 'is_active', 'sort_order'];

        foreach ($columns as $column) {
            if (in_array(strtolower(trim($column)), $headerFields, true)) {
                return true;
            }
        }

        return false;
    }

    protected function parseCsvRow(array $columns, ?array $headers): array
    {
        if ($headers) {
            $row = array_combine($headers, array_pad($columns, count($headers), '')) ?: [];
            return [
                'name' => trim($row['name'] ?? ''),
                'slug' => trim($row['slug'] ?? ''),
                'parent' => trim($row['parent'] ?? ''),
                'is_active' => $this->normalizeBoolean($row['is_active'] ?? '1'),
                'sort_order' => $this->normalizeInteger($row['sort_order'] ?? '0'),
            ];
        }

        return [
            'name' => trim($columns[0] ?? ''),
            'slug' => trim($columns[1] ?? ''),
            'parent' => trim($columns[2] ?? ''),
            'is_active' => $this->normalizeBoolean($columns[3] ?? '1'),
            'sort_order' => $this->normalizeInteger($columns[4] ?? '0'),
        ];
    }

    protected function normalizeBoolean(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'y', 'active'], true);
    }

    protected function normalizeInteger(string $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    protected function resolveParentId(?string $parent): ?int
    {
        if (blank($parent)) {
            return null;
        }

        $parent = trim($parent);
        $topic = Topic::where('slug', $parent)
            ->orWhere('name', $parent)
            ->first();

        return $topic?->id;
    }

    protected function findDuplicatePairs($topics): array
    {
        $pairs = [];

        foreach ($topics as $index => $topic) {
            foreach ($topics as $candidate) {
                if ($topic->id >= $candidate->id) {
                    continue;
                }

                $score = $this->duplicateScore($topic->name, $candidate->name);
                if ($score >= 70) {
                    $pairs[] = [
                        'topic' => $topic,
                        'candidate' => $candidate,
                        'score' => round($score, 1),
                    ];
                }
            }
        }

        usort($pairs, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $pairs;
    }

    protected function duplicateScore(string $a, string $b): float
    {
        $cleanA = preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower(trim($a)));
        $cleanB = preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower(trim($b)));

        if ($cleanA === '' || $cleanB === '') {
            return 0.0;
        }

        if ($cleanA === $cleanB) {
            return 100.0;
        }

        similar_text($cleanA, $cleanB, $percent);
        $distance = levenshtein($cleanA, $cleanB);
        $length = max(mb_strlen($cleanA), mb_strlen($cleanB));

        $distanceScore = $length > 0 ? max(0, 100 - ($distance / $length) * 100) : 0;

        return max($percent, $distanceScore);
    }
}
