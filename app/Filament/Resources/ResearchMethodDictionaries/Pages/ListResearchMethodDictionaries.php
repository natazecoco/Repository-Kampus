<?php

namespace App\Filament\Resources\ResearchMethodDictionaries\Pages;

use App\Filament\Resources\ResearchMethodDictionaries\ResearchMethodDictionaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResearchMethodDictionaries extends ListRecords
{
    protected static string $resource = ResearchMethodDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
