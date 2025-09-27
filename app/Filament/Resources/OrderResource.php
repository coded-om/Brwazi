<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationGroup = 'Commerce';
    protected static ?string $navigationLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Minimal; admin actions done via table actions
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('buyer.full_name')->label('Buyer')->sortable()->searchable(),
                TextColumn::make('seller.full_name')->label('Seller')->sortable()->searchable(),
                TextColumn::make('total')->label('Total')->money('omr')->sortable(),
                BadgeColumn::make('payment_status')->label('Payment')->colors([
                    'secondary',
                    'success' => 'paid',
                    'danger' => 'refunded',
                ])->sortable(),
                BadgeColumn::make('fulfillment_status')->label('Fulfillment')->colors([
                    'secondary',
                    'warning' => 'unfulfilled',
                    'info' => 'shipped',
                    'success' => 'delivered',
                    'gray' => 'completed',
                    'danger' => 'canceled',
                ])->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        Order::PAYMENT_PENDING => 'Pending',
                        Order::PAYMENT_PAID => 'Paid',
                        Order::PAYMENT_REFUNDED => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('fulfillment_status')
                    ->options([
                        Order::FULFILLMENT_UNFULFILLED => 'Unfulfilled',
                        Order::FULFILLMENT_SHIPPED => 'Shipped',
                        Order::FULFILLMENT_DELIVERED => 'Delivered',
                        Order::FULFILLMENT_COMPLETED => 'Completed',
                        Order::FULFILLMENT_CANCELED => 'Canceled',
                        Order::FULFILLMENT_DISPUTED => 'Disputed',
                    ]),
            ])
            ->actions([
                Action::make('downloadInvoice')
                    ->label('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn(Order $record) => url('/legacy-admin/orders/' . $record->id . '/invoice'), true)
                    ->visible(fn(Order $record) => !empty($record->invoice_pdf_path)),
                Action::make('markShipped')
                    ->label('Mark Shipped')
                    ->icon('heroicon-o-truck')
                    ->form([
                        Forms\Components\TextInput::make('shipping_carrier')->required(),
                        Forms\Components\TextInput::make('tracking_number')->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(function (Order $record, array $data): void {
                        $record->shipping_carrier = $data['shipping_carrier'];
                        $record->tracking_number = $data['tracking_number'];
                        $record->shipped_at = now();
                        $record->fulfillment_status = Order::FULFILLMENT_SHIPPED;
                        $record->save();
                    })
                    ->visible(fn(Order $record) => $record->payment_status === Order::PAYMENT_PAID && $record->fulfillment_status !== Order::FULFILLMENT_SHIPPED),
                Action::make('markDelivered')
                    ->label('Mark Delivered')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        $record->delivered_at = now();
                        $record->fulfillment_status = Order::FULFILLMENT_DELIVERED;
                        $record->save();
                    })
                    ->visible(fn(Order $record) => $record->fulfillment_status === Order::FULFILLMENT_SHIPPED),
                Action::make('markCompleted')
                    ->label('Mark Completed')
                    ->icon('heroicon-o-badge-check')
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        $record->fulfillment_status = Order::FULFILLMENT_COMPLETED;
                        $record->save();
                    })
                    ->visible(fn(Order $record) => in_array($record->fulfillment_status, [Order::FULFILLMENT_DELIVERED, Order::FULFILLMENT_SHIPPED], true)),
                Action::make('resolveDispute')
                    ->label('Resolve Dispute')
                    ->icon('heroicon-o-scale')
                    ->form([
                        Forms\Components\Select::make('decision')
                            ->options([
                                'refunded' => 'Refunded',
                                'completed' => 'Completed',
                            ])->required(),
                        Forms\Components\Textarea::make('note')->columnSpanFull(),
                    ])
                    ->requiresConfirmation()
                    ->action(function (Order $record, array $data): void {
                        if ($data['decision'] === 'refunded') {
                            $record->payment_status = Order::PAYMENT_REFUNDED;
                            $record->fulfillment_status = Order::FULFILLMENT_CANCELED;
                        } else {
                            $record->fulfillment_status = Order::FULFILLMENT_COMPLETED;
                        }
                        $note = $data['note'] ?? null;
                        if ($note) {
                            $record->notes = trim(($record->notes ? ($record->notes . "\n") : '') . 'قرار الإدمن: ' . $note);
                        }
                        $record->save();
                    })
                    ->visible(fn(Order $record) => $record->fulfillment_status === Order::FULFILLMENT_DISPUTED),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="orders.csv"',
                            ];
                            return response()->streamDownload(function () use ($records) {
                                $out = fopen('php://output', 'w');
                                fputcsv($out, ['ID', 'Order No', 'Buyer', 'Seller', 'Total', 'Payment', 'Fulfillment', 'Created At']);
                                foreach ($records as $o) {
                                    fputcsv($out, [
                                        $o->id,
                                        $o->order_no,
                                        optional($o->buyer)->full_name,
                                        optional($o->seller)->full_name,
                                        $o->total,
                                        $o->payment_status,
                                        $o->fulfillment_status,
                                        optional($o->created_at)?->toDateTimeString(),
                                    ]);
                                }
                                fclose($out);
                            }, 'orders.csv', $headers);
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
        ];
    }
}
