<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'منحنى الإيرادات';
    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        $start = now()->subDays(29)->startOfDay();
        for ($i = 0; $i < 30; $i++) {
            $day = (clone $start)->addDays($i);
            $labels[] = $day->format('m-d');
            $data[] = (float) Order::whereDate('created_at', $day)->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيراد اليومي (OMR)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59,130,246,0.3)',
                    'borderColor' => 'rgba(59,130,246,1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
