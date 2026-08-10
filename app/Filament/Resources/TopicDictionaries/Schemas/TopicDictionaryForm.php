<?php

namespace App\Filament\Resources\TopicDictionaries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TopicDictionaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('keyword')
                    ->label('Kata kunci')
                    ->placeholder('Contoh: machine learning, Laravel, atau sistem rekomendasi')
                    ->helperText('Istilah yang dicari pada judul, keyword, dan abstrak publikasi.')
                    ->required()
                    ->maxLength(255),
                TextInput::make('target_topic')
                    ->label('Topik tujuan')
                    ->placeholder('Contoh: Machine Learning atau Web Development')
                    ->helperText('Nama topik yang akan ditempelkan ketika kata kunci terdeteksi.')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
