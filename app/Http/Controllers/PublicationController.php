<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationFile;
use App\Models\Recommendation;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $topicSlug = trim((string) ($request->input('topic') ?? $request->route('slug') ?? ''));
        
        // 1. Tangkap parameter filter metode riset dari URL (?method=...)
        $methodFilter = trim((string) $request->input('method', ''));

        // [MODIFIKASI] Hapus ->latest() di sini agar tidak berbenturan dengan order by bobot search
        $query = Publication::with(['container', 'files', 'topics']);
        $semanticTerms = [];

        $personalizedRecommendations = collect();
        if ($request->user()) {
            $preferredTopicIds = $request->user()->topicPreferences()->pluck('topic_id');
            $bookmarkedPublicationIds = $request->user()->bookmarks()->pluck('publication_id');

            $candidateQuery = Publication::with(['container', 'topics'])
                ->whereNotIn('id', $bookmarkedPublicationIds)
                ->where(function ($q) use ($preferredTopicIds, $bookmarkedPublicationIds) {
                    $q->whereHas('topics', fn ($topicQuery) => $topicQuery->whereIn('topics.id', $preferredTopicIds))
                      ->orWhereIn('id', $bookmarkedPublicationIds);
                });

            if ($preferredTopicIds->isNotEmpty()) {
                $personalizedRecommendations = $candidateQuery->limit(4)->get();
            }
        }

        // [MODIFIKASI] Gunakan scopeSearch dan kirimkan hasil semanticTerms kamu
        if ($search !== '') {
            $semanticTerms = $this->expandSearchTerms($search);
            $query->search($search, $semanticTerms);
        } else {
            // Jika tidak ada keyword pencarian, urutkan dokumen dari terbaru
            $query->latest();
        }

        if ($topicSlug !== '') {
            $query->whereHas('topics', function ($topicQuery) use ($topicSlug): void {
                $topicQuery->where('slug', $topicSlug)
                    ->orWhere('name', $topicSlug);
            });
        }

        // 2. Terapkan filter research_method jika mahasiswa memilih dari dropdown
        if ($methodFilter !== '') {
            $query->where('research_method', $methodFilter);
        }

        // 3. Tambahkan 'method' ke dalam pagination appends
        $publications = $query->paginate(12)->appends($request->only(['search', 'topic', 'method']));

        foreach ($publications as $pub) {
            $pub->highlighted_abstract = $this->highlightKeyword($pub->abstract, $search);
        }

        $topics = Topic::withCount('publications')->active()->orderBy('sort_order')->orderBy('name')->get();
        $activeTopic = $topicSlug !== ''
            ? Topic::where('slug', $topicSlug)->orWhere('name', $topicSlug)->first()
            : null;
        $taxonomyTopics = $topics->whereNull('parent_id');

        // 4. Ambil daftar metode unik yang ada di database untuk opsi dropdown filter
        $availableMethods = Publication::whereNotNull('research_method')
            ->where('research_method', '!=', '')
            ->distinct()
            ->orderBy('research_method')
            ->pluck('research_method');

        return view('index', compact(
            'publications', 
            'search', 
            'topics', 
            'activeTopic', 
            'personalizedRecommendations', 
            'taxonomyTopics', 
            'semanticTerms',
            'availableMethods',
            'methodFilter'
        ));
    }

    public function show(Publication $publication)
    {
        // [BARU - FASE 2B] Increment View Counter (+1) dengan proteksi Session
        $viewKey = 'publication_viewed_' . $publication->id;
        if (!session()->has($viewKey)) {
            $publication->increment('views_count');
            session()->put($viewKey, true);
        }

        $publication->load(['container', 'files', 'topics']);

        $recommendations = Recommendation::where('publication_id', $publication->id)
                            ->with('recommendedPublication.topics')
                            ->orderByDesc('similarity_score')
                            ->get();

        if ($recommendations->isEmpty()) {
            $candidatePublications = Publication::query()
                ->where('id', '!=', $publication->id)
                ->with(['topics', 'container'])
                ->latest()
                ->limit(10)
                ->get();

            $recommendations = $candidatePublications->map(function (Publication $candidate) use ($publication) {
                $score = 0;
                $sharedTopics = $candidate->topics->pluck('id')->intersect($publication->topics->pluck('id'));

                if ($sharedTopics->isNotEmpty()) {
                    $score += 2;
                }

                if ($publication->keywords && $candidate->keywords && str_contains(strtolower($publication->keywords), strtolower($candidate->keywords))) {
                    $score += 1;
                }

                if ($publication->author && $candidate->author && strtolower($publication->author) === strtolower($candidate->author)) {
                    $score += 1;
                }

                return (object) [
                    'recommendedPublication' => $candidate,
                    'similarity_score' => $score,
                    'knowledge_overlap' => $sharedTopics->count(),
                ];
            })->filter(fn ($item) => $item->similarity_score > 0)
              ->sortByDesc('similarity_score')
              ->take(10);
        }

        // --- PEMISAHAN 5 KATEGORI REKOMENDASI ---

        // 1. Dokumen Paling Mirip (Berdasarkan skor tertinggi / TF-IDF)
        $similarRecommendations = $recommendations->sortByDesc('similarity_score')->take(3);

        // 2. Bacaan Pelengkap (Berdasarkan irisan topik / knowledge overlap > 0)
        $complementaryRecommendations = $recommendations->filter(function ($item) {
            return isset($item->knowledge_overlap) && $item->knowledge_overlap > 0;
        })->take(3);

        // 3. Konsep Dasar (Dokumen yang terhubung via topik induk / parent topics)
        $parentTopicIds = $publication->topics->pluck('parent_id')->filter()->unique();
        $basicConcepts = Publication::where('id', '!=', $publication->id)
            ->whereHas('topics', function ($q) use ($parentTopicIds) {
                $q->whereIn('id', $parentTopicIds);
            })
            ->with('topics')
            ->limit(3)
            ->get();

        // 4. Metode Serupa (Cari berdasarkan research_method yang sama dulu, kalau kosong baru fallback ke tipe karya)
        $similarMethods = Publication::where('id', '!=', $publication->id)
            ->where(function ($q) use ($publication) {
                if (!empty($publication->research_method)) {
                    $q->where('research_method', $publication->research_method);
                } else {
                    $q->where('type', $publication->type);
                }
            })
            ->with('topics')
            ->limit(3)
            ->get();

        // 5. Bacaan Lanjutan (Dokumen yang terhubung via topik anak / child topics)
        $publicationTopicIds = $publication->topics->pluck('id');
        $childTopicIds = Topic::whereIn('parent_id', $publicationTopicIds)->pluck('id');
        $advancedReadings = Publication::where('id', '!=', $publication->id)
            ->whereHas('topics', function ($q) use ($childTopicIds) {
                $q->whereIn('id', $childTopicIds);
            })
            ->with('topics')
            ->limit(3)
            ->get();

        return view('show', compact(
            'publication', 
            'recommendations', 
            'similarRecommendations', 
            'complementaryRecommendations', 
            'basicConcepts', 
            'similarMethods', 
            'advancedReadings'
        ));
    }

    /**
     * [BARU - FASE 2B] Handle proses download file dan increment counter (Support Private & Public Storage).
     */
    public function downloadFile(Request $request, PublicationFile $file)
    {
        // 0. Proteksi jika ada record database yang file_path-nya tidak sengaja kosong (NULL)
        if (empty($file->file_path)) {
            abort(404, 'Gagal mengunduh: Path file belum tercatat di database (NULL).');
        }

        // 1. Cek Hak Akses User terhadap File (jika method canBeDownloadedBy tersedia di model)
        if (method_exists($file, 'canBeDownloadedBy') && ! $file->canBeDownloadedBy($request->user())) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen ini. Silakan login sebagai mahasiswa terlebih dahulu.');
        }

        $path = $file->file_path;
        $fileName = $file->original_name ?? basename($path);

        // 2. OPSI A: Cek langsung ke folder private (storage/app/{path})
        $privatePath = storage_path('app/' . ltrim($path, '/'));
        if (file_exists($privatePath) && is_file($privatePath)) {
            $file->increment('downloads_count');
            return response()->download($privatePath, $fileName);
        }

        // 3. OPSI B: Cek langsung ke folder public (storage/app/public/{path})
        $cleanPublicPath = ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
        $publicPath = storage_path('app/public/' . $cleanPublicPath);
        if (file_exists($publicPath) && is_file($publicPath)) {
            $file->increment('downloads_count');
            return response()->download($publicPath, $fileName);
        }

        // 4. OPSI C: Cek via Storage Facade Default (disk lokal)
        if (Storage::exists($path)) {
            $file->increment('downloads_count');
            return Storage::download($path, $fileName);
        }

        // 5. OPSI D: Cek via Storage Facade disk 'public'
        if (Storage::disk('public')->exists($cleanPublicPath)) {
            $file->increment('downloads_count');
            return Storage::disk('public')->download($cleanPublicPath, $fileName);
        }

        // Jika ke-4 cara di atas gagal menemukan fisik filenya:
        abort(404, "File fisik tidak ditemukan di server. (Path tercatat di DB: {$path} | Cek juga folder: storage/app/{$path})");
    }

    private function expandSearchTerms(string $search): array
    {
        $terms = preg_split('/\s+/', strtolower(trim($search))) ?: [];
        $terms = array_values(array_filter($terms));
        $expanded = [];

        $synonyms = [
            'ai' => ['artificial intelligence', 'machine learning'],
            'machine' => ['machine learning'],
            'learning' => ['machine learning', 'deep learning'],
            'recommendation' => ['recommendation systems', 'recommender'],
            'systems' => ['system', 'recommendation systems'],
            'deep' => ['deep learning'],
        ];

        foreach ($terms as $term) {
            $expanded[] = $term;
            $expanded = array_merge($expanded, $synonyms[$term] ?? []);

            $topicMatches = Topic::active()->where(function ($query) use ($term): void {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('slug', 'like', '%' . $term . '%');
            })->get();

            foreach ($topicMatches as $topic) {
                $expanded[] = $topic->name;
                $expanded[] = $topic->slug;

                foreach ($topic->ancestorIds() as $ancestorId) {
                    $ancestor = Topic::find($ancestorId);
                    if ($ancestor) {
                        $expanded[] = $ancestor->name;
                        $expanded[] = $ancestor->slug;
                    }
                }

                foreach ($topic->children()->pluck('name') as $childName) {
                    $expanded[] = $childName;
                }
            }
        }

        return array_values(array_unique(array_filter($expanded)));
    }

    private function highlightKeyword($text, $keyword) 
    {
        if (empty($keyword) || empty($text)) {
            return e($text);
        }

        $safeText = e($text);
        $safeKeyword = preg_quote($keyword, '/');
        $pattern = "/($safeKeyword)/i"; 
        $replacement = '<span class="bg-yellow-100/80 text-yellow-900 px-1 rounded-sm font-medium">$1</span>';

        return preg_replace($pattern, $replacement, $safeText);
    }
}