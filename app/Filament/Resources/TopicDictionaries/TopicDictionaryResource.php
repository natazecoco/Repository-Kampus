<?php

namespace App\Filament\Resources\TopicDictionaries;

use App\Filament\Resources\TopicDictionaries\Pages\CreateTopicDictionary;
use App\Filament\Resources\TopicDictionaries\Pages\EditTopicDictionary;
use App\Filament\Resources\TopicDictionaries\Pages\ListTopicDictionaries;
use App\Filament\Resources\TopicDictionaries\Schemas\TopicDictionaryForm;
use App\Filament\Resources\TopicDictionaries\Tables\TopicDictionariesTable;
use App\Models\TopicDictionary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TopicDictionaryResource extends Resource
{
    protected static ?string $model = TopicDictionary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Repository';

    protected static ?string $recordTitleAttribute = 'keyword';

    public static function form(Schema $schema): Schema
    {
        return TopicDictionaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TopicDictionariesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTopicDictionaries::route('/'),
            'create' => CreateTopicDictionary::route('/create'),
            'edit' => EditTopicDictionary::route('/{record}/edit'),
        ];
    }
}
