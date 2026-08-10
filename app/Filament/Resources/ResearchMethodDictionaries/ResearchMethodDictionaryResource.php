<?php

namespace App\Filament\Resources\ResearchMethodDictionaries;

use App\Filament\Resources\ResearchMethodDictionaries\Pages\CreateResearchMethodDictionary;
use App\Filament\Resources\ResearchMethodDictionaries\Pages\EditResearchMethodDictionary;
use App\Filament\Resources\ResearchMethodDictionaries\Pages\ListResearchMethodDictionaries;
use App\Filament\Resources\ResearchMethodDictionaries\Schemas\ResearchMethodDictionaryForm;
use App\Filament\Resources\ResearchMethodDictionaries\Tables\ResearchMethodDictionariesTable;
use App\Models\ResearchMethodDictionary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ResearchMethodDictionaryResource extends Resource
{
    protected static ?string $model = ResearchMethodDictionary::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;
    protected static string|UnitEnum|null $navigationGroup = 'Repository';
    protected static ?string $navigationLabel = 'Kamus Metode Riset';
    protected static ?string $modelLabel = 'Kamus Metode Riset';
    protected static ?string $pluralModelLabel = 'Kamus Metode Riset';
    protected static ?string $recordTitleAttribute = 'keyword';

    public static function form(Schema $schema): Schema
    {
        return ResearchMethodDictionaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResearchMethodDictionariesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResearchMethodDictionaries::route('/'),
            'create' => CreateResearchMethodDictionary::route('/create'),
            'edit' => EditResearchMethodDictionary::route('/{record}/edit'),
        ];
    }
}
