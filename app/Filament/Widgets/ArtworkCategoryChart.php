<?php

namespace App\Filament\Widgets;

use App\Models\Artwork;
use Filament\Widgets\ChartWidget;

class ArtworkCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الأعمال حسب الفئات';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 2,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rows = Artwork::query()
            ->withoutGlobalScope('owner_not_banned')
            ->selectRaw('category, COUNT(*) as c')
            ->groupBy('category')
            ->orderByDesc('c')
            ->limit(8)
            ->get();

        $labels = [];
        $values = [];
        foreach ($rows as $r) {
            $labels[] = Artwork::categoryLabel($r->category) ?? ($r->category ?: 'غير مصنّف');
            $values[] = (int) $r->c;
        }

        $bg = [
            'rgba(24,18,66,0.85)',
            'rgba(42,33,94,0.85)',
            'rgba(59,130,246,0.85)',
            'rgba(16,185,129,0.85)',
            'rgba(234,179,8,0.85)',
            'rgba(244,63,94,0.85)',
            'rgba(99,102,241,0.85)',
            'rgba(251,146,60,0.85)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => array_slice($bg, 0, max(1, count($values))),
                ]
            ],
            'labels' => $labels,
        ];
    }
}
