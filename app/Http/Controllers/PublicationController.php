<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Recommendation;
use App\Models\Topic;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $topicSlug = trim((string) ($request->input('topic') ?? $request->route('slug') ?? ''));
        $query = Publication::with(['container', 'files', 'topics'])->latest();
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
            $query->where(function ($q) use ($search, $semanticTerms) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('keywords', 'like', '%' . $search . '%')
                  ->orWhere('year', 'like', '%' . $search . '%')
                  ->orWhere('abstract', 'like', '%' . $search . '%')
                  ->orWhereHas('container', function ($containerQuery) use ($search) {
                      $containerQuery->where('name', 'like', '%' . $search . '%')
                                     ->orWhere('identifier', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->where('name', 'like', '%' . $search . '%'));

                foreach ($semanticTerms as $term) {
                    $q->orWhere('title', 'like', '%' . $term . '%')
                      ->orWhere('abstract', 'like', '%' . $term . '%')
                      ->orWhere('keywords', 'like', '%' . $term . '%')
                      ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->where('name', 'like', '%' . $term . '%'));
                }
            });
        }

        if ($topicSlug !== '') {
            $query->whereHas('topics', function ($topicQuery) use ($topicSlug): void {
                $topicQuery->where('slug', $topicSlug)
                    ->orWhere('name', $topicSlug);
            });
        }

        $publications = $query->paginate(12)->appends($request->only(['search', 'topic']));

        foreach ($publications as $pub) {
            $pub->highlighted_abstract = $this->highlightKeyword($pub->abstract, $search);
        }

        $topics = Topic::withCount('publications')->active()->orderBy('sort_order')->orderBy('name')->get();
        $activeTopic = $topicSlug !== ''
            ? Topic::where('slug', $topicSlug)->orWhere('name', $topicSlug)->first()
            : null;
        $taxonomyTopics = $topics->whereNull('parent_id');

        return view('index', compact('publications', 'search', 'topics', 'activeTopic', 'personalizedRecommendations', 'taxonomyTopics', 'semanticTerms'));
    }

    public function show(Publication $publication)
    {
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

        // 4. Metode Serupa (Dokumen dengan tipe / jenis publikasi yang sama)
        $similarMethods = Publication::where('id', '!=', $publication->id)
            ->where('type', $publication->type)
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