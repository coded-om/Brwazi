<?php

namespace App\Filament\Widgets;

use App\Models\Artwork;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ArtworkStats extends BaseWidget
{
    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 6,
    ];

    protected static ?string $pollingInterval = '30s';

    protected function getCards(): array
    {
        $q = Artwork::query()->withoutGlobalScope('owner_not_banned');
        $total = (int) $q->count();
        $published = (int) (clone $q)->where('status', Artwork::STATUS_PUBLISHED)->count();
        $drafts = (int) (clone $q)->where('status', Artwork::STATUS_DRAFT)->count();
        $likes = (int) (clone $q)->sum('likes_count');
        $images = (int) (clone $q)->sum('images_count');

        $topCategoryRow = (clone $q)
            ->selectRaw('category, COUNT(*) as c')
            ->groupBy('category')
            ->orderByDesc('c')
            ->first();
        $topCategory = $topCategoryRow?->category ? (\App\Models\Artwork::categoryLabel($topCategoryRow->category) ?? $topCategoryRow->category) : 'غير مصنّف';

        return [
            Card::make('إجمالي الأعمال', number_format($total))
                ->description('كل الأعمال في المنصة')
                ->descriptionIcon('heroicon-m-photo')
                ->color('primary'),

            Card::make('منشور', number_format($published))
                ->description('الأعمال المنشورة')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Card::make('مسودات', number_format($drafts))
                ->description('تحتاج للمراجعة أو النشر')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),

            Card::make('إجمالي الإعجابات', number_format($likes))
                ->description('مجموع إعجابات الأعمال')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color('info'),

            Card::make('عدد الصور', number_format($images))
                ->description('إجمالي صور الأعمال')
                ->descriptionIcon('heroicon-m-photo')
                ->color('gray'),

            Card::make('الأكثر شيوعاً', $topCategory)
                ->description('الفئة الأعلى عدداً')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('secondary'),
        ];
    }
}
