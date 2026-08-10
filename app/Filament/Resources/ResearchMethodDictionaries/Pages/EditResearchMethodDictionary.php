<?php

namespace App\Filament\Resources\ResearchMethodDictionaries\Pages;

use App\Filament\Resources\ResearchMethodDictionaries\ResearchMethodDictionaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResearchMethodDictionary extends EditRecord
{
    protected static string $resource = ResearchMethodDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
