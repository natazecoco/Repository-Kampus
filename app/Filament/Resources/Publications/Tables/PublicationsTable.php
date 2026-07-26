<?php

namespace App\Filament\Resources\Publications\Tables;

// Kembali menggunakan namespace Filament\Actions yang benar untuk versimu
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PublicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('container.name')
                    ->label('Container')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('author')
                    ->searchable(),
                TextColumn::make('year'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'thesis' => 'primary',
                        'article' => 'success',
                        'book' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('keywords')
                    ->searchable(),
                TextColumn::make('files_count')
                    ->counts('files')
                    ->label('Jumlah Dokumen')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Tombol Lihat PDF dengan namespace Action yang sudah diperbaiki
                Action::make('view_pdf')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(function ($record) {
                        $firstFile = $record->files->first();
                        
                        if ($firstFile) {
                            return route('document.viewer', ['id' => $firstFile->id]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->files->isNotEmpty()),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}