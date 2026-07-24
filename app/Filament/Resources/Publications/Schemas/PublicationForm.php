<?php

namespace App\Filament\Resources\Publications\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

                Select::make('type')
                    ->options([
                        'thesis' => 'Thesis / Skripsi',
                        'article' => 'Artikel Jurnal',
                        'book' => 'Buku',
                    ])
                    ->default('thesis')
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('author')
                    ->required()
                    ->maxLength(255),

                TextInput::make('year')
                    ->numeric()
                    ->required(),

                Textarea::make('abstract')
                    ->rows(5)
                    ->required(),

                TextInput::make('keywords')
                    ->required()
                    ->maxLength(500),

                Repeater::make('files')
                    ->relationship('files')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Bagian')
                            ->placeholder('Cth: Bab 1: Pendahuluan')
                            ->required()
                            ->maxLength(255),

                        Select::make('access_type')
                            ->label('Hak Akses')
                            ->options([
                                'public' => 'Terbuka (Bebas Download)',
                                'restricted' => 'Terkunci (Wajib Login)',
                            ])
                            ->default('restricted')
                            ->required(),

                        FileUpload::make('file_path')
                            ->label('File PDF')
                            ->disk('local')          
                            ->visibility('private')  
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('publications_pdf')
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->columns(2)
                    ->orderColumn('sort_order')
                    ->defaultItems(1),
            ]);
    }
}