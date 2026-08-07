<?php

namespace App\Filament\Widgets;

use App\Models\Publication;
use App\Models\Topic;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsWidget extends ChartWidget
{
    protected ?string $heading = 'Analitik Repository';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $yearlyCounts = Publication::select('year', DB::raw('count(*) as total'))
            ->whereNotNull('year')
            ->where('year', '!=', '')
            ->groupBy('year')
            ->orderBy('year')
            ->limit(8)
            ->get();

        $labels = $yearlyCounts->pluck('year')->all();
        $values = $yearlyCounts->pluck('total')->all();

        return [
            'datasets' => [[
                'label' => 'Publikasi per Tahun',
                'data' => $values,
                'borderColor' => '#7c3aed',
                'backgroundColor' => 'rgba(124, 58, 237, 0.2)',
                'fill' => true,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
