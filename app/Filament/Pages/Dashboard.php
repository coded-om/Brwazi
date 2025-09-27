<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة التحكم';

    public function getColumns(): int|array
    {
        // 6-column grid on xl for flexible half/full widths
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 6,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\ArtworkStats::class,
            \App\Filament\Widgets\RevenueChart::class,
            \App\Filament\Widgets\ArtworkCategoryChart::class,
            \App\Filament\Widgets\RecentArtworksTable::class,
            \App\Filament\Widgets\RecentOrdersTable::class,
            \App\Filament\Widgets\QuickAdminActions::class,
        ];
    }
}
