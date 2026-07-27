<?php

namespace App\Filament\Widgets;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Mengatur urutan agar tampil paling atas di dashboard
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Publikasi', Publication::count())
                ->description('Seluruh karya ilmiah di sistem')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
                
            Stat::make('Mahasiswa Terdaftar', User::where('role', 'student')->count())
                ->description('Akun mahasiswa aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
                
            Stat::make('Topik Aktif', Topic::where('is_active', true)->count())
                ->description('Kategori yang tersedia')
                ->descriptionIcon('heroicon-m-tag')
                ->color('primary'),
        ];
    }
}