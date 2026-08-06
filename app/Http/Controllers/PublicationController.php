<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationFile;
use App\Models\Recommendation;
use App\Models\Topic;
use App\Models\TopicDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $topicSlug = trim((string) ($request->input('topic') ?? $request->route('slug') ?? ''));
        
        $methodFilter = trim((string) $request->input('method', ''));
        $typeFilter = trim((string) $request->input('type', ''));
        $yearFilter = trim((string) $request->input('year', ''));

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

        if ($search !== '') {
            $semanticTerms = $this->expandSearchTerms($search);
            $query->search($search, $semanticTerms);
        }

        if ($search !== '') {
            $query->orderByRaw(
                "CASE " .
                "WHEN LOWER(title) LIKE ? THEN 6 " .
                "WHEN LOWER(keywords) LIKE ? THEN 5 " .
                "WHEN LOWER(abstract) LIKE ? THEN 4 " .
                "WHEN LOWER(author) LIKE ? THEN 3 " .
                "ELSE 0 END DESC",
                [
                    "%{$search}%",
                    "%{$search}%",
                    "%{$search}%",
                    "%{$search}%",
                ]
            );
        }

        $query->orderByDesc('views_count')
            ->orderByDesc('created_at');

        if ($topicSlug !== '') {
            $query->whereHas('topics', function ($topicQuery) use ($topicSlug): void {
                $topicQuery->where('slug', $topicSlug)
                    ->orWhere('name', $topicSlug);
            });
        }

        if ($methodFilter !== '') {
            $query->where('research_method', $methodFilter);
        }

        if ($typeFilter !== '') {
            $query->where('type', $typeFilter);
        }

        if ($yearFilter !== '') {
            $query->where('year', $yearFilter);
        }

        $publications = $query->paginate(12)->appends($request->only(['search', 'topic', 'method', 'type', 'year']));

        foreach ($publications as $pub) {
            $pub->highlighted_abstract = $this->highlightKeyword($pub->abstract, $search);
        }

        $topics = Topic::withCount('publications')->active()->orderBy('sort_order')->orderBy('name')->get();
        $activeTopic = $topicSlug !== ''
            ? Topic::where('slug', $topicSlug)->orWhere('name', $topicSlug)->first()
            : null;
        $taxonomyTopics = $topics->whereNull('parent_id');

        $availableMethods = Publication::whereNotNull('research_method')
            ->where('research_method', '!=', '')
            ->distinct()
            ->orderBy('research_method')
            ->pluck('research_method');

        $availableYears = Publication::whereNotNull('year')
            ->where('year', '!=', '')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $typeOptions = Publication::typeFilterOptions();

        return view('index', compact(
            'publications', 
            'search', 
            'topics', 
            'activeTopic', 
            'personalizedRecommendations', 
            'taxonomyTopics', 
            'semanticTerms',
            'availableMethods',
            'methodFilter',
            'typeFilter',
            'yearFilter',
            'availableYears',
            'typeOptions'
        ));
    }

    public function show(Publication $publication)
    {
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

        $similarRecommendations = $recommendations->sortByDesc('similarity_score')->take(3);

        $complementaryRecommendations = $recommendations->filter(function ($item) {
            return $item->knowledge_overlap > 0;
        })->take(3);

        $parentTopicIds = $publication->topics->pluck('parent_id')->filter()->unique();
        $allRelevantTopicIds = $publication->topics->pluck('id')->merge($parentTopicIds)->unique();
        
        // KHUSUS BUKU: Cari buku yang memiliki irisan dengan topik saat ini atau topik induknya
        $bookReferences = Publication::where('id', '!=', $publication->id)
            ->where('type', 'book')
            ->whereHas('topics', function ($q) use ($allRelevantTopicIds) {
                $q->whereIn('topics.id', $allRelevantTopicIds);
            })
            ->with('topics')
            ->latest()
            ->limit(3)
            ->get();

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
            'bookReferences',
            'similarMethods', 
            'advancedReadings'
        ));
    }

    public function downloadFile(Request $request, Publication $publication, PublicationFile $file)
    {
        if ($file->publication_id !== $publication->id) {
            abort(404, 'File tidak ditemukan pada karya ilmiah ini.');
        }

        if (method_exists($file, 'canBeDownloadedBy') && ! $file->canBeDownloadedBy($request->user())) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen ini. Silakan login sebagai mahasiswa terlebih dahulu.');
        }

        if (empty($file->file_path)) {
            abort(404, 'Gagal mengunduh: Path file belum tercatat di database (NULL).');
        }

        $file->increment('downloads_count');

        $path = ltrim($file->file_path, '/');
        $fileName = $file->original_name ?? basename($path);

        $privateAppPath = storage_path('app/private/' . $path);
        if (file_exists($privateAppPath) && is_file($privateAppPath)) {
            return response()->download($privateAppPath, $fileName);
        }

        $standardPath = storage_path('app/' . $path);
        if (file_exists($standardPath) && is_file($standardPath)) {
            return response()->download($standardPath, $fileName);
        }

        $cleanPublicPath = ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
        $publicPath = storage_path('app/public/' . $cleanPublicPath);
        if (file_exists($publicPath) && is_file($publicPath)) {
            return response()->download($publicPath, $fileName);
        }

        if (Storage::exists($path)) {
            return Storage::download($path, $fileName);
        }

        abort(404, "File fisik tidak ditemukan di server. (Path tercatat di DB: {$path} | Cek folder: storage/app/private/{$path})");
    }

    private function expandSearchTerms(string $search): array
    {
        $terms = preg_split('/\s+/', strtolower(trim($search))) ?: [];
        $terms = array_values(array_filter($terms));
        $expanded = [];

        // [KODE BARU] Ambil dari Database & Cache selama 24 jam (86400 detik)
        $synonyms = Cache::remember('topic_dictionary_mappings', 86400, function () {
            return TopicDictionary::all()
                ->groupBy('target_topic')
                ->map(fn ($items) => $items->pluck('keyword')->toArray())
                ->toArray();
        });

        foreach ($terms as $term) {
            $expanded[] = $term;
            $expanded = array_merge($expanded, $synonyms[$term] ?? []);

            $topicMatches = Topic::with('children')->active()->where(function ($query) use ($term): void {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('slug', 'like', '%' . $term . '%');
            })->get();

            foreach ($topicMatches as $topic) {
                $expanded = array_merge($expanded, $topic->getSemanticKeywords());
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