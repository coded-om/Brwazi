<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Widgets\Widget;

class QuickAdminActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-admin-actions';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 2,
    ];

    protected static ?string $heading = 'اختصارات سريعة';

    public function getActions(): array
    {
        return [
            Action::make('المستخدمون')
                ->icon('heroicon-m-users')
                ->url(url('/admin/users')),
            Action::make('الأعمال')
                ->icon('heroicon-m-photo')
                ->url(url('/admin/artworks')),
            Action::make('المزادات')
                ->icon('heroicon-m-bolt')
                ->url(url('/admin/auctions')),
            Action::make('التصنيفات')
                ->icon('heroicon-m-squares-2x2')
                ->url(url('/admin/art-categories')),
        ];
    }
}
