<?php

namespace App\Filament\Resources\Containers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(['university' => 'University', 'journal' => 'Journal', 'publisher' => 'Publisher'])
                    ->required(),
                TextInput::make('identifier'),
            ]);
    }
}
