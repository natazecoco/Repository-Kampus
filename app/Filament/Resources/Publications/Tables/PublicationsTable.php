<?php

namespace App\Filament\Resources\Publications\Tables;

// Kembali menggunakan namespace Filament\Actions yang benar untuk versimu
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Publication;

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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis Publikasi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Publication::TYPE_LABELS[$state] ?? ucfirst((string) $state))
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'thesis' => 'primary',
                        'scientific_paper' => 'info',
                        'article' => 'success',
                        'book' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('keywords')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('files_count')
                    ->counts('files')
                    ->label('Jumlah Dokumen')
                    ->badge()
                    ->sortable()
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
                SelectFilter::make('type')
                    ->label('Jenis Publikasi')
                    ->options(Publication::TYPE_LABELS),
                SelectFilter::make('container_id')
                    ->label('Container')
                    ->relationship('container', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn (): array => Publication::query()
                        ->whereNotNull('year')
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->mapWithKeys(fn ($year): array => [(string) $year => (string) $year])
                        ->all()),
                SelectFilter::make('research_method')
                    ->label('Metode Riset')
                    ->options(fn (): array => Publication::query()
                        ->whereNotNull('research_method')
                        ->where('research_method', '!=', '')
                        ->distinct()
                        ->orderBy('research_method')
                        ->pluck('research_method', 'research_method')
                        ->all())
                    ->searchable(),
                TernaryFilter::make('has_files')
                    ->label('Berkas Publikasi')
                    ->queries(
                        true: fn ($query) => $query->has('files'),
                        false: fn ($query) => $query->doesntHave('files'),
                        blank: fn ($query) => $query,
                    ),
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
