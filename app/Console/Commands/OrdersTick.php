<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Console\Command;

class OrdersTick extends Command
{
    protected $signature = 'orders:tick {--complete-after=7 : Days after delivered to auto-complete}';
    protected $description = 'Periodic tasks for orders: auto-complete delivered orders after a threshold.';

    public function handle(OrderStateService $state): int
    {
        $days = (int) $this->option('complete-after');
        $count = 0;
        Order::where('fulfillment_status', Order::FULFILLMENT_DELIVERED)
            ->whereNotNull('delivered_at')
            ->chunkById(200, function ($orders) use ($state, $days, &$count) {
                foreach ($orders as $order) {
                    $updated = $state->autoCompleteIfElapsed($order, $days);
                    if ($updated)
                        $count++;
                }
            });
        $this->info("Auto-completed {$count} orders (if any)");
        return self::SUCCESS;
    }
}
