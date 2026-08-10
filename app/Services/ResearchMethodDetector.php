<?php

namespace App\Services;

use App\Models\ResearchMethodDictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ResearchMethodDetector
{
    public function detect(?string $title, ?string $keywords, ?string $abstract): ?string
    {
        $fields = [
            'title' => Str::lower((string) $title),
            'keywords' => Str::lower((string) $keywords),
            'abstract' => Str::lower((string) $abstract),
        ];

        $matches = [];
        foreach ($this->methods() as $method) {
            foreach ($method['aliases'] ?? [] as $keyword) {
                if (blank($keyword)) {
                    continue;
                }

                if (! $method['is_active'] || ! $this->containsKeyword($fields, $keyword)) {
                    continue;
                }

                $score = $method['priority'];
                $score += $this->fieldScore($fields, $keyword, 'title', 100);
                $score += $this->fieldScore($fields, $keyword, 'keywords', 50);
                $score += $this->fieldScore($fields, $keyword, 'abstract', 20);

                $matches[] = ['label' => $method['method_name'], 'score' => $score];
            }
        }

        usort($matches, fn (array $left, array $right): int => $right['score'] <=> $left['score']);

        return $matches[0]['label'] ?? null;
    }

    public function methods(): array
    {
        return Cache::remember('research_method_dictionary', now()->addDay(), function (): array {
            $databaseMethods = ResearchMethodDictionary::query()
                ->where('is_active', true)
                ->orderByDesc('priority')
                ->get(['method_name', 'aliases', 'category', 'priority', 'is_active'])
                ->map(fn (ResearchMethodDictionary $method): array => $method->toArray())
                ->all();

            if ($databaseMethods !== []) {
                return $databaseMethods;
            }

            return collect(config('research.methods', []))
                ->map(fn (string $label, string $keyword): array => [
                    'method_name' => $label,
                    'aliases' => [$keyword],
                    'category' => 'research',
                    'priority' => 50,
                    'is_active' => true,
                ])
                ->values()
                ->all();
        });
    }

    private function containsKeyword(array $fields, string $keyword): bool
    {
        foreach ($fields as $text) {
            if ($this->fieldContainsKeyword($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function fieldScore(array $fields, string $keyword, string $field, int $score): int
    {
        return $this->fieldContainsKeyword($fields[$field], $keyword) ? $score : 0;
    }

    private function fieldContainsKeyword(string $text, string $keyword): bool
    {
        $keyword = trim(Str::lower($keyword));

        if ($keyword === '') {
            return false;
        }

        return preg_match(
            '/(?<![\\pL\\pN])' . preg_quote($keyword, '/') . '(?![\\pL\\pN])/iu',
            $text
        ) === 1;
    }
}
