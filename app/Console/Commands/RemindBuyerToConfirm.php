<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\DeliveredReminder;
use Illuminate\Console\Command;

class RemindBuyerToConfirm extends Command
{
    protected $signature = 'orders:remind-buyer {--hours=48 : Hours after shipped to remind buyer to confirm delivery}';
    protected $description = 'Send a reminder to buyers to confirm delivery after a set number of hours from shipment.';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $count = 0;

        Order::where('fulfillment_status', Order::FULFILLMENT_SHIPPED)
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', now()->subHours($hours))
            ->chunkById(200, function ($orders) use (&$count) {
                foreach ($orders as $order) {
                    // Skip if disputed/delivered/completed
                    if (in_array($order->fulfillment_status, [Order::FULFILLMENT_DELIVERED, Order::FULFILLMENT_COMPLETED, Order::FULFILLMENT_DISPUTED], true)) {
                        continue;
                    }
                    try {
                        if ($order->buyer) {
                            $order->buyer->notify(new DeliveredReminder($order));
                            $count++;
                        }
                    } catch (\Throwable $e) {
                    }
                }
            });

        $this->info("Reminders sent: {$count}");
        return self::SUCCESS;
    }
}
