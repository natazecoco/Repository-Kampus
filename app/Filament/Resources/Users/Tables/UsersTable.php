<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(), // Disederhanakan: aman dari perubahan API Filament 5
                
                TextColumn::make('npm')
                    ->label('NPM')
                    ->searchable(),
                
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'mahasiswa' => 'info',
                        'umum' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-m-shield-check',
                        'mahasiswa' => 'heroicon-m-academic-cap',
                        'umum' => 'heroicon-m-user',
                        default => 'heroicon-m-user',
                    }),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}