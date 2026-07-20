<?php

namespace App\Filament\Resources\Publications\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PublicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('container_id')
                    ->relationship('container', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('author')
                    ->required(),
                TextInput::make('year')
                    ->required(),
                Select::make('type')
                    ->options(['thesis' => 'Thesis', 'article' => 'Article', 'book' => 'Book'])
                    ->default('thesis')
                    ->required(),
                Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('keywords')
                    ->required(),
                FileUpload::make('file_path')
                    ->label('Upload File PDF')
                    ->directory('publications_pdf') // Folder tempat nyimpen file nanti
                    ->acceptedFileTypes(['application/pdf']) // Kunci khusus file PDF saja
                    ->maxSize(10240), // Batas maksimal ukuran file 10MB
            ]);
    }
}
