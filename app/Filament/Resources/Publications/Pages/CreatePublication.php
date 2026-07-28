<?php

namespace App\Filament\Resources\Publications\Pages;

use App\Filament\Resources\Publications\PublicationResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\AutoTaggingService;
use App\Jobs\GenerateRecommendations;

class CreatePublication extends CreateRecord
{
    protected static string $resource = PublicationResource::class;

    protected function afterCreate(): void
    {
        $publication = $this->record;
        
        // Panggil service di sini, SETELAH Filament selesai menyimpan semuanya
        app(AutoTaggingService::class)->tag($publication);

        // Panggil job rekomendasi setelah tagging selesai
        GenerateRecommendations::dispatch($publication);
    }
}