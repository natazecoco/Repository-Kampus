<?php

namespace App\Filament\Resources\ResearchMethodDictionaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResearchMethodDictionariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('method_name')->label('Nama metode')->searchable()->sortable(),
                TextColumn::make('aliases')
                    ->label('Alias')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'research' => 'Pendekatan penelitian',
                        'development' => 'Pengembangan sistem',
                        'testing' => 'Pengujian',
                        'analysis' => 'Analisis / keputusan',
                        'technology' => 'Teknologi pendukung',
                        default => ucfirst((string) $state),
                    })
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
