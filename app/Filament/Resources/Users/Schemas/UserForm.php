<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                TextInput::make('npm')
                    ->label('NPM')
                    ->length(8)
                    ->rule('digits:8')
                    ->unique(ignoreRecord: true),
                
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'mahasiswa' => 'Mahasiswa',
                        'umum' => 'Umum',
                    ])
                    ->required()
                    ->default('umum'),
                
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state) => filled($state)),
            ]);
    }
}