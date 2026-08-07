<?php

namespace App\Filament\Widgets;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminInsightWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $mostViewed = Publication::orderByDesc('views_count')->first();
        $mostDownloaded = Publication::withCount('files')->orderByDesc('files_count')->first();
        $activeTopics = Topic::where('is_active', true)->count();
        $studentUsers = User::where('role', 'student')->count();
        $publications = Publication::count();
        $methods = Publication::whereNotNull('research_method')->where('research_method', '!=', '')->distinct()->count('research_method');
        $bookmarkCount = DB::table('bookmarks')->count();
        $totalViews = Publication::sum('views_count');
        $totalDownloads = DB::table('publication_files')->sum('downloads_count');
        $mostViewedTitle = Str::limit($mostViewed?->title ?? 'Belum ada', 45);

        return [
            Stat::make('Publikasi', $publications)
                ->description('Total dokumen terindeks')
                ->color('success'),
            Stat::make('Mahasiswa', $studentUsers)
                ->description('Akun student yang terdaftar')
                ->color('info'),
            Stat::make('Topik Aktif', $activeTopics)
                ->description('Kategori yang tersedia')
                ->color('primary'),
            Stat::make('Metode Riset', $methods)
                ->description('Variasi metode yang terdeteksi')
                ->color('warning'),
            Stat::make('Bookmark', $bookmarkCount)
                ->description('Total koleksi pengguna')
                ->color('danger'),
            Stat::make('Views', number_format($totalViews))
                ->description('Total kunjungan publikasi')
                ->color('secondary'),
            Stat::make('Unduhan', number_format($totalDownloads))
                ->description('Total unduhan file')
                ->color('gray'),
            Stat::make('Terpopuler', $mostViewedTitle)
                ->description('Publikasi dengan views tertinggi')
                ->color('success'),
        ];
    }
}
