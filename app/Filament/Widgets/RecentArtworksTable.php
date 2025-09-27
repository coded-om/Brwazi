<?php

namespace App\Filament\Widgets;

use App\Models\Artwork;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentArtworksTable extends BaseWidget
{
    protected static ?string $heading = 'أحدث الأعمال';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Artwork::query()->withoutGlobalScope('owner_not_banned')->latest()->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('المالك')->searchable(),
                Tables\Columns\TextColumn::make('category')->label('الفئة')->formatStateUsing(fn($state) => \App\Models\Artwork::categoryLabel($state) ?? $state),
                Tables\Columns\BadgeColumn::make('status')->label('الحالة')->colors([
                    'success' => 'published',
                    'gray' => 'draft',
                ]),
                Tables\Columns\TextColumn::make('created_at')->since()->label('منذ'),
            ])
            ->actions([
                Tables\Actions\Action::make('فتح')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn(Artwork $a) => url('/art/' . $a->id), shouldOpenInNewTab: true),
            ]);
    }
}
