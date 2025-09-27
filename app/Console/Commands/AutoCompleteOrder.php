<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Console\Command;

class AutoCompleteOrder extends Command
{
    protected $signature = 'orders:auto-complete {--days=7 : Days after shipped to auto deliver and complete if no dispute}';
    protected $description = 'Auto-complete shipped orders after X days by marking delivered then completed (if not disputed).';

    public function handle(OrderStateService $state): int
    {
        $days = (int) $this->option('days');
        $count = 0;

        Order::where('fulfillment_status', Order::FULFILLMENT_SHIPPED)
            ->whereNull('delivered_at')
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', now()->subDays($days))
            ->chunkById(200, function ($orders) use (&$count, $state) {
                foreach ($orders as $order) {
                    // Skip disputed orders
                    if ($order->fulfillment_status === Order::FULFILLMENT_DISPUTED) {
                        continue;
                    }

                    // Mark delivered then complete (use service only for complete to notify both parties)
                    $order->delivered_at = now();
                    $order->fulfillment_status = Order::FULFILLMENT_DELIVERED;
                    $order->save();

                    try {
                        $state->complete($order, null);
                        $count++;
                    } catch (\Throwable $e) {
                        // ignore and move on
                    }
                }
            });

        $this->info("Auto delivered/completed: {$count} orders");
        return self::SUCCESS;
    }
}
