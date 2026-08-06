<?php

namespace App\Filament\Resources\TopicDictionaries\Pages;

use App\Filament\Resources\TopicDictionaries\TopicDictionaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTopicDictionary extends EditRecord
{
    protected static string $resource = TopicDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
