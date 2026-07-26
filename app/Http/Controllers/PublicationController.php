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
        // Accept topic from query string (home) or route parameter (topic page)
        $topicSlug = trim((string) ($request->input('topic') ?? $request->route('slug') ?? ''));
        $query = Publication::with(['container', 'files', 'topics'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('keywords', 'like', '%' . $search . '%')
                  ->orWhere('year', 'like', '%' . $search . '%')
                  ->orWhereHas('container', function ($containerQuery) use ($search) {
                      $containerQuery->where('name', 'like', '%' . $search . '%')
                                     ->orWhere('identifier', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('topics', fn ($topicQuery) => $topicQuery->where('name', 'like', '%' . $search . '%'));
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

        $topics = Topic::active()->withCount('publications')->orderBy('name')->get();
        $activeTopic = $topicSlug !== ''
            ? Topic::active()->where('slug', $topicSlug)->orWhere('name', $topicSlug)->first()
            : null;

        return view('index', compact('publications', 'search', 'topics', 'activeTopic'));
    }

    public function show(Publication $publication)
    {
        $publication->load(['container', 'files', 'topics']);

        // AMBIL DARI DATABASE, BUKAN MENGHITUNG DARI NOL LAGI!
        $recommendations = Recommendation::where('publication_id', $publication->id)
                            ->with('recommendedPublication.topics')
                            ->orderByDesc('similarity_score')
                            ->get();

        return view('show', compact('publication', 'recommendations'));
    }

    // TAMBAHAN: Method private untuk membungkus kata kunci dengan tag highlight Tailwind
    private function highlightKeyword($text, $keyword) 
    {
        if (empty($keyword) || empty($text)) {
            return e($text);
        }

        $safeText = e($text);

        // preg_quote digunakan untuk mengamankan karakter khusus dalam input pencarian
        $safeKeyword = preg_quote($keyword, '/');
        
        // Modifier 'i' pada regex digunakan untuk pencarian case-insensitive
        $pattern = "/($safeKeyword)/i"; 
        
        $replacement = '<span class="bg-yellow-100/80 text-yellow-900 px-1 rounded-sm font-medium">$1</span>';

        return preg_replace($pattern, $replacement, $safeText);
    }
}
