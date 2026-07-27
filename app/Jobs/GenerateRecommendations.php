<?php

namespace App\Jobs;

use App\Jobs\GenerateRecommendations;
use App\Models\Publication;
use App\Models\Recommendation;
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

    // Menerima data skripsi yang akan dicarikan rekomendasinya
    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    // Fungsi handle() ini adalah tombol "START" dari pekerja belakang layar
    public function handle(): void
    {
        $target = $this->publication;
        $others = Publication::where('id', '!=', $target->id)->get();

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

            Recommendation::updateOrCreate(
                [
                    'publication_id' => $target->id,
                    'recommended_id' => $entry['publication']->id,
                ],
                [
                    'similarity_score' => $entry['score'],
                ]
            );
        }
    }
}