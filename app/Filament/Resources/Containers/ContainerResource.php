<?php

namespace App\Filament\Resources\Containers;

use App\Filament\Resources\Containers\Pages\CreateContainer;
use App\Filament\Resources\Containers\Pages\EditContainer;
use App\Filament\Resources\Containers\Pages\ListContainers;
use App\Filament\Resources\Containers\Schemas\ContainerForm;
use App\Filament\Resources\Containers\Tables\ContainersTable;
use App\Models\Container;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Container';

    protected static string|UnitEnum|null $navigationGroup = 'Repository';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ContainerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContainersTable::configure($table);
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
            'index' => ListContainers::route('/'),
            'create' => CreateContainer::route('/create'),
            'edit' => EditContainer::route('/{record}/edit'),
        ];
    }
}
