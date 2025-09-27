<?php

namespace App\Filament\Widgets;

use App\Models\Auction;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '30s';

    protected function getCards(): array
    {
        $ordersMonth = Order::whereMonth('created_at', now()->month)->count();
        $revenueMonth = (float) Order::whereMonth('created_at', now()->month)->sum('total');
        $newUsersMonth = User::whereMonth('created_at', now()->month)->count();
        $liveAuctions = Auction::where('status', 'live')->count();

        return [
            Card::make('إجمالي الطلبات (هذا الشهر)', number_format($ordersMonth))
                ->description('عدد الطلبات الجديدة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Card::make('إجمالي الإيرادات (OMR)', number_format($revenueMonth, 3))
                ->description('إيرادات هذا الشهر')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Card::make('عملاء جدد', number_format($newUsersMonth))
                ->description('المستخدمون المسجلون هذا الشهر')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            Card::make('مزادات مباشرة الآن', number_format($liveAuctions))
                ->description('عدد المزادات قيد البث')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info'),
        ];
    }
}
