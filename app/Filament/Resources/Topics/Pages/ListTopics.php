<?php

namespace App\Filament\Resources\Topics\Pages;

use App\Filament\Resources\Topics\TopicResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTopics extends ListRecords
{
    protected static string $resource = TopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportTopics')
                ->label('Export CSV')
                ->url(route('admin.topics.export'))
                ->openUrlInNewTab(),
            Action::make('importTopics')
                ->label('Import CSV')
                ->url(route('admin.topics.import')),
            Action::make('findDuplicates')
                ->label('Duplicate Detection')
                ->url(route('admin.topics.duplicates')),
            CreateAction::make(),
        ];
    }
}
