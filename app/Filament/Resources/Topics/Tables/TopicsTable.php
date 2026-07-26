<?php

namespace App\Filament\Resources\Topics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TopicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Topik')->searchable()->sortable(),
                TextColumn::make('parent.name')->label('Topik Induk')->placeholder('Topik utama')->searchable(),
                TextColumn::make('publications_count')->counts('publications')->label('Publikasi')->badge(),
                TextColumn::make('is_active')->label('Aktif')->boolean()->sortable(),
                TextColumn::make('merged_into')->label('Merged Into')->formatStateUsing(fn($state) => $state ? \App\Models\Topic::find($state)?->name : null)->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('update', $record)),

                \Filament\Tables\Actions\Action::make('toggleActive')
                    ->label(fn($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-off' : 'heroicon-o-eye')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()?->can('toggleActive', $record))
                    ->action(function ($record) {
                        $record->is_active = ! $record->is_active;
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title($record->is_active ? 'Topik diaktifkan' : 'Topik dinonaktifkan')
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('merge')
                    ->label('Gabungkan')
                    ->icon('heroicon-o-duplicate')
                    ->visible(fn ($record) => auth()->user()?->can('merge', $record))
                    ->form([
                        \Filament\Forms\Components\Select::make('target')
                            ->label('Gabungkan ke')
                            ->options(fn () => \App\Models\Topic::where('id', '!=', \Filament\Tables\getRecord()?->id)->pluck('name', 'id')->toArray())
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($record, array $data) {
                        $target = \App\Models\Topic::find($data['target']);
                        if ($target && $target->id !== $record->id) {
                            $record->mergeInto($target);
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Topik berhasil digabung')
                                ->send();

                            // Create a convenient undo action via notification (link to admin topics list where undo is available)
                            \Filament\Notifications\Notification::make()
                                ->info()
                                ->title('Undo tersedia')
                                ->body('Buka admin Topik untuk membatalkan gabungan (lihat topik sumber yang dinonaktifkan).')
                                ->send();
                        }
                    }),
                \Filament\Tables\Actions\Action::make('undoMerge')
                    ->label('Batalkan gabungan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->visible(fn($record) => ! $record->is_active && $record->merged_into !== null && auth()->user()?->role === 'admin')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $ok = $record->undoMerge();
                        if ($ok) {
                            \Filament\Notifications\Notification::make()->success()->title('Pembatalan gabungan berhasil')->send();
                        } else {
                            \Filament\Notifications\Notification::make()->danger()->title('Tidak ada backup untuk dibatalkan')->send();
                        }
                    }),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkAction::make('merge')
                    ->label('Gabungkan ke topik lain')
                    ->form([
                        \Filament\Forms\Components\Select::make('target')
                            ->label('Topik target')
                            ->options(fn () => \App\Models\Topic::pluck('name', 'id')->toArray())
                            ->required(),
                    ])
                    ->action(function ($records, array $data) {
                        $target = \App\Models\Topic::find($data['target']);
                        if (! $target) {
                            return;
                        }

                        foreach ($records as $record) {
                            if ($record->id === $target->id) continue;
                            if (auth()->user()?->can('merge', $record)) {
                                $record->mergeInto($target);
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Bulk merge selesai')
                            ->send();
                    })
                    ->requiresConfirmation(),
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
