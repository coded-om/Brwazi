<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersTable extends BaseWidget
{
    protected static ?string $heading = 'أحدث الطلبات';
    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'xl' => 6,
    ];

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_no')->label('رقم الطلب')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('العميل')->searchable(),
                Tables\Columns\TextColumn::make('total')->money('OMR')->label('الإجمالي')->sortable(),
                Tables\Columns\BadgeColumn::make('status')->label('الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'delivered',
                        'info' => 'confirmed',
                        'gray' => 'preparing',
                        'danger' => 'canceled',
                    ])->sortable(),
                Tables\Columns\TextColumn::make('created_at')->since()->label('منذ'),
            ]);
    }
}
