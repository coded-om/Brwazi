<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidderDeposit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuctionsTick extends Command
{
    protected $signature = 'auctions:tick {--extend=120 : Anti-sniping extension in seconds (optional)}';
    protected $description = 'Process auction lifecycle: start scheduled, end finished, and apply optional anti-sniping extension.';

    public function handle(): int
    {
        $now = Carbon::now();
        $extendedSec = (int) $this->option('extend');

        // 1) Start scheduled auctions
        $started = Auction::where('status', 'scheduled')
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', $now)
            ->update(['status' => 'live']);
        if ($started) {
            $this->info("Started {$started} auctions");
        }

        // 2) Anti-sniping: extend auctions in last minute if recent bids
        if ($extendedSec > 0) {
            $liveAuctions = Auction::where('status', 'live')
                ->whereNotNull('ends_at')
                ->where('ends_at', '>', $now)
                ->whereRaw('TIMESTAMPDIFF(SECOND, ?, ends_at) <= 60', [$now])
                ->get(['id', 'ends_at']);

            foreach ($liveAuctions as $auction) {
                $lastBid = Bid::where('auction_id', $auction->id)
                    ->orderByDesc('created_at')
                    ->first(['id', 'created_at']);
                if (!$lastBid)
                    continue;

                $threshold = (clone $auction->ends_at)->subSeconds(60);
                if ($lastBid->created_at >= $threshold) {
                    $newEnds = (clone $lastBid->created_at)->addSeconds($extendedSec);
                    if ($auction->ends_at < $newEnds) {
                        Auction::where('id', $auction->id)->update(['ends_at' => $newEnds]);
                        $this->line("Extended auction {$auction->id} to {$newEnds}");
                    }
                }
            }
        }

        // 3) End finished auctions and set winner/highest bid
        $toEnd = Auction::where('status', 'live')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($toEnd as $auction) {
            DB::transaction(function () use ($auction) {
                $topBid = Bid::where('auction_id', $auction->id)
                    ->orderByDesc('amount')
                    ->orderByDesc('created_at')
                    ->first();

                $updates = [
                    'status' => 'ended',
                ];
                if ($topBid) {
                    $meetsStart = $topBid->amount >= $auction->start_price;
                    $meetsReserve = is_null($auction->reserve_price) || $topBid->amount >= $auction->reserve_price;
                    if ($meetsStart && $meetsReserve) {
                        $updates['highest_bid_id'] = $topBid->id;
                        $updates['highest_bid_amount'] = $topBid->amount;
                    } else {
                        // Release deposits for this auction if reserve/start not met
                        BidderDeposit::where('reference', 'auction:' . $auction->id)
                            ->where('status', 'held')
                            ->update(['status' => 'released']);
                    }
                } else {
                    // No bids -> release any held deposits tied to this auction
                    BidderDeposit::where('reference', 'auction:' . $auction->id)
                        ->where('status', 'held')
                        ->update(['status' => 'released']);
                }

                Auction::where('id', $auction->id)->update($updates);
            });
        }

        if ($toEnd->count()) {
            $this->info("Ended {$toEnd->count()} auctions");
        }

        return self::SUCCESS;
    }
}
