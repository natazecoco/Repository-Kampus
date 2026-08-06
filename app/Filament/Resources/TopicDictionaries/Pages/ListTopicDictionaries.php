<?php

namespace App\Filament\Resources\TopicDictionaries\Pages;

use App\Filament\Resources\TopicDictionaries\TopicDictionaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTopicDictionaries extends ListRecords
{
    protected static string $resource = TopicDictionaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
