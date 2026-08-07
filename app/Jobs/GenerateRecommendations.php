<?php

namespace App\Jobs;

use App\Models\Publication;
use App\Models\Recommendation;
use App\Models\Topic;
use App\Services\RecommendationScorer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    public function handle(): void
    {
        $target = $this->publication;
        
        // 1. Ambil semua ID Topik yang dimiliki oleh skripsi target
        //    Expand juga konteks taksonomi (parent & children) agar pre-filtering
        //    tidak melewatkan kandidat yang relevan berada di level anak.
        $topicIds = $target->topics->pluck('id')->toArray();

        // Expand topic IDs to include their parent and children to improve prefilter recall
        if (! empty($topicIds)) {
            $related = Topic::whereIn('id', $topicIds)->with('parent', 'children')->get();
            foreach ($related as $t) {
                $topicIds[] = $t->parent_id ?? null;
                foreach ($t->children as $c) {
                    $topicIds[] = $c->id;
                }
            }
            $topicIds = array_values(array_unique(array_filter($topicIds)));
        }

        // 2. PRE-FILTERING: Jangan ambil semua isi database!
        // Ambil skripsi yang minimal punya 1 kesamaan logis (Topik, Metode, Penulis, atau Tipe)
        $others = Publication::with('topics') // Eager load topics agar tidak N+1 Query
            ->where('id', '!=', $target->id)
            ->where(function($query) use ($target, $topicIds) {
                
                // Syarat A: Punya irisan topik yang sama
                if (!empty($topicIds)) {
                    $query->whereHas('topics', function($q) use ($topicIds) {
                        $q->whereIn('topics.id', $topicIds);
                    });
                }
                
                // Syarat B: Punya metode riset yang sama
                if (!empty($target->research_method)) {
                    $query->orWhere('research_method', $target->research_method);
                }

                // Syarat C: Penulisnya sama
                if (!empty($target->author)) {
                    $query->orWhere('author', $target->author);
                }

                // Syarat D (Fallback): Tipe dokumennya sama (misal sesama "Skripsi")
                $query->orWhere('type', $target->type);
                
            })
            ->get();

        // Jika tidak ada kandidat sama sekali, berhenti.
        if ($others->isEmpty()) {
            return;
        }

        $scorer = app(RecommendationScorer::class);
        $scoredCandidates = $scorer->scoreCandidates($target, $others, $target->created_by_user ?? null);

        Recommendation::where('publication_id', $target->id)->delete();

        foreach (array_slice($scoredCandidates, 0, 3) as $entry) {
            if ($entry['score'] <= 0) {
                continue;
            }

            $recommendedPub = $entry['publication'];
            
            // Hitung jumlah irisan topik antara target dan kandidat rekomendasi
            $sharedTopicsCount = $target->topics->pluck('id')
                ->intersect($recommendedPub->topics->pluck('id'))
                ->count();

            Recommendation::updateOrCreate(
                [
                    'publication_id' => $target->id,
                    'recommended_id' => $recommendedPub->id,
                ],
                [
                    'similarity_score' => $entry['score'],
                    'knowledge_overlap' => $sharedTopicsCount, // Simpan hasil hitungan ke DB!
                ]
            );
        }
    }
}