<?php

namespace App\Filament\Resources\Publications\Tables;

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
                TextColumn::make('container_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50), // Membatasi panjang teks judul agar tabel tetap rapi
                TextColumn::make('author')
                    ->searchable(),
                TextColumn::make('year'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('keywords')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->label('File PDF')
                    // Mengubah teks path mentah menjadi tulisan "Download" yang rapi
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Tidak Ada File')
                    // Memberikan ikon unduh otomatis jika filenya ada
                    ->icon(fn ($state) => $state ? 'heroicon-o-arrow-down-tray' : null)
                    // Memberikan warna biru (primary) untuk link aktif
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    // Membuat kolom bisa diklik langsung menuju ke file aslinya
                    ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    // Mengatur agar file terbuka di tab baru saat diklik
                    ->openUrlInNewTab(),
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
