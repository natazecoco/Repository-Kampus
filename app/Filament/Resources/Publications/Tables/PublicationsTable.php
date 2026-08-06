<?php

namespace App\Filament\Resources\Publications\Tables;

// Kembali menggunakan namespace Filament\Actions yang benar untuk versimu
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
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
                IconColumn::make('admin_completion_state.is_complete')
                    ->label('Kelengkapan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('admin_completion_state.status_label')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => $state === 'Lengkap' ? 'success' : 'warning'),
                TextColumn::make('metadata_summary.category')
                    ->label('Kategori')
                    ->sortable(false),
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
                
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view_publication')
                    ->label('Baca Publikasi')
                    ->icon('heroicon-s-eye')
                    ->color('info')
                    ->url(fn ($record): string => route('publications.viewer', $record))
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
