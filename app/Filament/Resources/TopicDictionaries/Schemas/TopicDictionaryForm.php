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
                    ->required(),
                TextInput::make('target_topic')
                    ->required(),
            ]);
    }
}
