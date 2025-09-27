<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BidderDeposit;
use App\Services\ThawaniService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MazadController extends Controller
{
    public function index(Request $request)
    {
        // Build base query with effective_price (highest bid if exists else start price)
        $query = Auction::with(['artwork.user'])
            ->whereHas('artwork')
            ->select('*')
            ->selectRaw('CASE WHEN highest_bid_amount > 0 THEN highest_bid_amount ELSE start_price END AS effective_price')
            ->when($request->filled('status'), function ($q) use ($request) {
                $statuses = array_filter((array) $request->input('status'), fn($s) => $s !== null && $s !== '');
                if ($statuses) {
                    $q->whereIn('status', $statuses);
                }
            });

        $min = $request->input('min');
        if (is_numeric($min) && (float) $min > 0) {
            $query->whereRaw('(CASE WHEN highest_bid_amount > 0 THEN highest_bid_amount ELSE start_price END) >= ?', [(float) $min]);
        }
        $max = $request->input('max');
        if (is_numeric($max) && (float) $max > 0) {
            $query->whereRaw('(CASE WHEN highest_bid_amount > 0 THEN highest_bid_amount ELSE start_price END) <= ?', [(float) $max]);
        }

        $sort = $request->string('sort', 'latest');
        match ($sort) {
            'price-asc' => $query->orderBy('effective_price')->orderByDesc('id'),
            'price-desc' => $query->orderByDesc('effective_price')->orderByDesc('id'),
            'bids-desc' => $query->orderByDesc('bids_count')->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };

        $auctions = $query->paginate(12)->withQueryString();

        return view('mazadViwes.index', compact('auctions', 'sort'));
    }

    public function show(Auction $auction)
    {
        $auction->load([
            'artwork.user',
            'bids' => function ($q) {
                $q->latest();
            }
        ]);
        $art = $auction->artwork; // may be null; use fallbacks below
        $bidItems = $auction->bids->map(function ($b) {
            return [
                'user' => Str::limit($b->user?->full_name ?? ('#' . $b->user_id), 10, '***'),
                'amount' => (float) $b->amount,
                'time_ago' => $b->created_at->diffForHumans(),
            ];
        });

        $data = [
            'id' => $auction->id,
            'title' => $art?->title ?? ('مزاد #' . $auction->id),
            'artist' => $art?->user?->full_name ?? '—',
            'image' => 'mazad5.png',
            'image_url' => $art?->primary_image_url ?? null,
            'status' => $auction->status,
            'base_price' => (float) $auction->start_price,
            'currency' => 'ريال',
            'highest_bid' => (float) ($auction->highest_bid_amount ?: $auction->start_price),
            'bidders_count' => $auction->bids_count,
            'size' => $art?->dimensions ?? '—',
            'year' => $art?->year ?? '—',
            'type' => $art?->type ?? '—',
            'condition' => '—',
            'description' => $art?->description ?? '',
            'end_at' => optional($auction->ends_at)->toIso8601String(),
            'min_increment' => (float) $auction->bid_increment,
            'reserve_price' => (float) $auction->reserve_price,
            'has_reserve' => $auction->hasReserve(),
            'reserve_met' => $auction->reserveMet(),
            'ended_unsold_by_reserve' => $auction->endedUnsoldByReserve(),
        ];

        // Optional: flag winner session to show Pay button when ended and current user is the winner
        if (Auth::check() && $auction->status === 'ended' && $auction->highest_bid_id) {
            $hb = Bid::find($auction->highest_bid_id);
            if ($hb && $hb->user_id === Auth::id()) {
                session(['user_is_winner_' . $auction->id => true]);
            } else {
                session()->forget('user_is_winner_' . $auction->id);
            }
        }

        return view('mazadViwes.show', ['auction' => $data, 'bidItems' => $bidItems]);
    }

    public function state(Auction $auction)
    {
        return response()->json([
            'id' => $auction->id,
            'status' => $auction->status,
            'highest_bid_amount' => (float) $auction->highest_bid_amount,
            'bids_count' => (int) $auction->bids_count,
            'ends_at' => optional($auction->ends_at)->toIso8601String(),
        ]);
    }

    public function bid(Request $request, Auction $auction)
    {
        $user = $request->user();
        $request->validate(['amount' => ['required', 'numeric', 'min:0']]);

        // Preconditions
        if ($auction->status !== 'live' || ($auction->ends_at && now()->greaterThanOrEqualTo($auction->ends_at))) {
            return back()->with('error', 'المزاد غير متاح حالياً');
        }
        // Not the artwork owner
        if ($auction->artwork && $auction->artwork->user_id === $user->id) {
            return back()->with('error', 'لا يمكنك المزايدة على عملك');
        }
        // Verified user policy
        if (!$user->isVerified()) {
            return redirect()->route('mazad.insurance', ['auction' => $auction->id])->with('error', 'يلزم التوثيق/التأمين للمزايدة');
        }
        // Deposit required for this auction
        $hasHeld = BidderDeposit::where('user_id', $user->id)
            ->where('status', 'held')
            ->where('reference', 'auction:' . $auction->id)
            ->exists();
        if (!$hasHeld) {
            return redirect()->route('mazad.insurance', ['auction' => $auction->id])
                ->with('error', 'الرجاء إيداع تأمين المزاد لهذه المزايدة');
        }

        $amount = (float) $request->input('amount');
        $minAllowed = max((float) $auction->start_price, (float) $auction->highest_bid_amount + (float) $auction->bid_increment);
        if ($amount < $minAllowed) {
            return back()->with('error', 'المبلغ أقل من الحد الأدنى المسموح به');
        }

        // Transactional place bid
        DB::transaction(function () use ($auction, $user, $amount) {
            // Lock auction row and re-check conditions to avoid race conditions
            $locked = Auction::where('id', $auction->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'live' || ($locked->ends_at && now()->greaterThanOrEqualTo($locked->ends_at))) {
                throw new \RuntimeException('Auction not live');
            }
            $minAllowed = max((float) $locked->start_price, (float) $locked->highest_bid_amount + (float) $locked->bid_increment);
            if ($amount < $minAllowed) {
                throw new \RuntimeException('Amount too low');
            }

            $bid = Bid::create([
                'auction_id' => $locked->id,
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            $locked->bids_count = ($locked->bids_count ?? 0) + 1;
            $locked->highest_bid_id = $bid->id;
            $locked->highest_bid_amount = $amount;
            $locked->save();

            // Detect reserve crossing (previous highest below reserve, new >= reserve)
            if ($locked->hasReserve() && !$locked->reserveMet()) {
                // reload highest_bid_amount state before saving? we already set new value.
            }
        });

        // TODO: Broadcast event for live updates; temporary fallback via polling endpoint
        $reserveMsg = '';
        $auction->refresh();
        if ($auction->hasReserve() && $auction->reserveMet()) {
            // We could track if just met; simplistic approach: always show message once reserve met and session flag missing
            if (!session()->get('reserve_met_notified_' . $auction->id)) {
                session(['reserve_met_notified_' . $auction->id => true]);
                $reserveMsg = ' (تم الوصول للسعر الاحتياطي)';
            }
        }
        return back()->with('success', 'تم تسجيل مزايدتك' . $reserveMsg);
    }

    public function pay(Request $request, Auction $auction, ThawaniService $thawani)
    {
        $user = $request->user();
        if ($auction->status !== 'ended' || !$auction->highest_bid_id) {
            return back()->with('error', 'لا يمكن الدفع حالياً لهذا المزاد');
        }
        $highestBid = Bid::find($auction->highest_bid_id);
        if (!$highestBid || $highestBid->user_id !== $user->id) {
            return back()->with('error', 'هذا الرابط للمزايد الفائز فقط');
        }

        // Create Order and item
        $art = $auction->artwork;
        if (!$art) {
            return back()->with('error', 'هذا العمل لم يعد متاحاً');
        }
        $total = (float) $auction->highest_bid_amount;
        $order = Order::create([
            'user_id' => $user->id,
            'order_no' => 'AUC' . now()->format('ymd') . Str::upper(Str::random(6)),
            'subtotal' => $total,
            'discount' => 0,
            'shipping_fee' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'thawani',
            'customer_name' => $user->full_name,
            'customer_phone' => $user->phone ?? null,
            'customer_city' => $user->city ?? null,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'artwork_id' => $art->id,
            'title' => $art->title,
            'price' => $total,
            'quantity' => 1,
            'image_url' => $art->primary_image_url,
            'artist_name' => $art->user?->full_name ?? '',
            // snapshot fields for data integrity
            'title_snapshot' => $art->title,
            'image_snapshot' => $art->primary_image_url,
            'price_snapshot' => (int) round($total * 1000),
        ]);

        $payload = [
            'client_reference_id' => (string) $order->id,
            'mode' => 'payment',
            'products' => [
                [
                    'name' => 'Auction #' . $auction->id . ' — ' . $art->title,
                    'quantity' => 1,
                    'unit_amount' => (int) round($total * 1000), // baisa
                ]
            ],
            'success_url' => route('checkout.success', ['order' => $order->id]),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id]),
            'metadata' => ['auction_id' => $auction->id],
        ];

        $result = $thawani->createCheckoutSession($payload);
        if (($result['success'] ?? false) && isset($result['data'])) {
            $data = $result['data'];
            $order->update([
                'payment_reference' => $data['data']['id'] ?? $data['session_id'] ?? null,
            ]);
            $url = $data['data']['session_url'] ?? $data['data']['invoice_url'] ?? $data['invoice_url'] ?? null;
            if ($url) {
                // Mark to consume deposit upon success
                $request->session()->put('consume_deposit_auction_id', $auction->id);
                return redirect()->away($url);
            }
        }

        return back()->with('error', 'تعذر إنشاء جلسة الدفع. حاول لاحقاً.');
    }

    public function insuranceHold(Request $request)
    {
        $request->validate([
            'amount' => ['nullable', 'numeric', 'min:1'],
            'auction_id' => ['nullable', 'integer', 'exists:auctions,id'],
        ]);
        $user = $request->user();
        if (!$user)
            return redirect()->route('login');
        $amount = (float) ($request->input('amount') ?? 5.000);
        $ref = $request->filled('auction_id') ? ('auction:' . $request->integer('auction_id')) : 'general';
        BidderDeposit::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'held',
            'reference' => $ref,
        ]);
        return back()->with('success', 'تم حجز التأمين بنجاح');
    }

    /**
     * صفحة إدارة التأمين: عرض المزادات المتاحة لحجز التأمين عليها (live + soon)
     */
    public function insurancePage()
    {
        $request = request();

        // Cache raw active auctions (unfiltered) for 30s to reduce DB hits
        $rawAuctions = Cache::remember('insurance.active_auctions.base', 30, function () {
            return Auction::with(['artwork.user'])
                ->activeForInsurance()
                ->orderByRaw("CASE WHEN status='live' THEN 0 ELSE 1 END")
                ->orderBy('starts_at')
                ->get();
        });

        // Filters
        $filterStatus = array_filter((array) $request->input('status', []));
        $minStart = $request->filled('min_start') ? (float) $request->input('min_start') : null;
        $maxStart = $request->filled('max_start') ? (float) $request->input('max_start') : null;
        $artistFilter = $request->input('artist');
        // ensure variables exist for closure binding (some linters warn otherwise)
        $__min = $minStart;
        $__max = $maxStart;
        $__statuses = $filterStatus;
        $__artist = $artistFilter;

        $filtered = $rawAuctions->filter(function ($a) use ($__statuses, $__min, $__max, $__artist) {
            if ($__statuses && !in_array($a->status, $__statuses))
                return false;
            if ($__min !== null && (float) $a->start_price < $__min)
                return false;
            if ($__max !== null && (float) $a->start_price > $__max)
                return false;
            if ($__artist && $__artist !== 'any') {
                $artistName = $a->artwork?->user?->full_name;
                if (!$artistName || $artistName !== $__artist)
                    return false;
            }
            return true;
        });

        // Map to simple array for Blade
        $auctionItems = $filtered->map(function ($a) {
            return [
                'id' => $a->id,
                'fee' => $a->insuranceFee(),
                'title' => $a->artwork?->title ?? ('مزاد #' . $a->id),
                'artist' => $a->artwork?->user?->full_name ?? '—',
                'image' => $a->artwork?->primary_image_url ?? asset('imgs/mazad/mazad5.png'),
                'status' => $a->status,
                'start_price' => (float) $a->start_price,
            ];
        })->values();

        // Distinct artist list for filter
        $artists = $rawAuctions->pluck('artwork.user.full_name')->filter()->unique()->values();

        // Stats (cached)
        $stats = Cache::remember('insurance.stats', 30, fn() => Auction::insuranceTopStats());
        $coverage = 0.0;
        if (($stats['required_total'] ?? 0) > 0) {
            $coverage = round(($stats['held_total'] / max(1, $stats['required_total'])) * 100, 1);
        }

        return view('mazadViwes.Insurance', [
            'auctionItems' => $auctionItems,
            'filterStatus' => $filterStatus,
            'artists' => $artists,
            'artistSelected' => $artistFilter,
            'minStart' => $minStart,
            'maxStart' => $maxStart,
            'statsCached' => $stats,
            'coveragePercent' => $coverage,
        ]);
    }
}
