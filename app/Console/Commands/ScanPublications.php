<?php

namespace App\Console\Commands;

use App\Jobs\GenerateRecommendations;
use App\Models\Publication;
use App\Services\AutoTaggingService;
use App\Services\ResearchMethodDetector;
use Illuminate\Console\Command;

class ScanPublications extends Command
{
    protected $signature = 'repo:scan-ulang {--force-method : Timpa metode riset yang sudah ada jika terdeteksi metode baru}';

    protected $description = 'Scan ulang publikasi untuk auto-tagging parent topik dan deteksi metode riset.';

    public function handle(AutoTaggingService $taggingService, ResearchMethodDetector $methodDetector): int
    {
        $publications = Publication::query()->get();

        if ($publications->isEmpty()) {
            $this->info('Tidak ada publikasi untuk di-scan.');
            return self::SUCCESS;
        }

        $this->info("Memulai scan ulang untuk {$publications->count()} publikasi...");

        $bar = $this->output->createProgressBar($publications->count());
        $bar->start();

        $updatedMethodsCount = 0;

        foreach ($publications as $publication) {
            $taggingService->tag($publication);

            $keywords = is_array($publication->keywords)
                ? implode(' ', $publication->keywords)
                : (string) ($publication->keywords ?? '');
            $detectedMethod = $methodDetector->detect(
                $publication->title,
                $keywords,
                $publication->abstract
            );

            if ($detectedMethod) {
                $shouldOverwrite = $this->option('force-method');

                if (empty($publication->research_method) || $shouldOverwrite) {
                    if ($publication->research_method !== $detectedMethod) {
                        $publication->research_method = $detectedMethod;
                        $publication->save();
                        $updatedMethodsCount++;
                    }
                }
            }

            GenerateRecommendations::dispatch($publication);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Scan selesai.');
        $this->info("🔍 {$updatedMethodsCount} publikasi berhasil dilengkapi metode risetnya.");
        $this->comment('⚡ Rekomendasi sedang dibuat ulang di background queue.');

        return self::SUCCESS;
    }
}
