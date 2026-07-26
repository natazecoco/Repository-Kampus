<?php

namespace App\Filament\Resources\Topics\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama Topik')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('parent_id')
                ->label('Topik Induk')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0),
            Textarea::make('description')
                ->label('Penjelasan Singkat')
                ->rows(3)
                ->maxLength(1000),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }
}
