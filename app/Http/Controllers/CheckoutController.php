<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BidderDeposit;
use App\Services\ThawaniService;
use App\Services\OrderStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * GET /checkout/{artwork} — quick checkout page/flow for a single artwork.
     * Here we re-use the existing checkout() by seeding the session cart, then delegating.
     */
    public function beginForArtwork(Request $request, Artwork $artwork, ThawaniService $thawani)
    {
        $request->validate(['quantity' => 'nullable|integer|min:1']);
        $qty = max(1, (int) $request->input('quantity', 1));
        // Block buying own artwork
        if ($artwork->user_id === $request->user()->id) {
            if (function_exists('notify'))
                notify()->warning('لا يمكنك شراء عملك الخاص');
            return redirect()->route('art.show', $artwork);
        }
        // Seed a minimal cart and delegate to checkout()
        session(['cart' => [$artwork->id => $qty]]);
        return $this->checkout($request, $thawani);
    }

    public function checkout(Request $request, ThawaniService $thawani)
    {
        $user = $request->user();
        $cart = session('cart', []);
        if (empty($cart))
            return redirect()->route('cart.index');

        $items = [];
        $subtotal = 0;
        $sellerIds = [];
        foreach ($cart as $artworkId => $qty) {
            $art = Artwork::findOrFail($artworkId);
            $price = (float) ($art->price ?? 0);
            $items[] = compact('art', 'qty', 'price');
            $subtotal += $price * (int) $qty;
            if ($art->user_id)
                $sellerIds[] = (int) $art->user_id;
        }
        $discount = round($subtotal * 0.2, 3);
        $shipping = 15.000;
        $total = max(0, $subtotal - $discount + $shipping);
        $uniqueSellers = collect($sellerIds)->unique()->values();
        $order = Order::create([
            'user_id' => $user->id,
            'order_no' => 'TW' . now()->format('ymd') . Str::upper(Str::random(6)),
            'buyer_id' => $user->id, // new schema alignment
            'seller_id' => $uniqueSellers->count() === 1 ? $uniqueSellers->first() : null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_fee' => $shipping,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => Order::PAYMENT_PENDING,
            'customer_name' => trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')),
            'customer_phone' => $user->phone ?? null,
            'customer_city' => $user->city ?? null,
        ]);

    // Remember last created order id for redirects/fallbacks
    $request->session()->put('last_order_id', $order->id);

        foreach ($items as $row) {
            /** @var Artwork $art */
            $art = $row['art'];
            OrderItem::create([
                'order_id' => $order->id,
                'artwork_id' => $art->id,
                'title' => $art->title,
                'price' => $row['price'],
                'quantity' => $row['qty'],
                'image_url' => $art->primary_image_url,
                'artist_name' => $art->user?->full_name ?? trim(($art->user?->fname ?? '') . ' ' . ($art->user?->lname ?? '')),
                // snapshot fields required by migration
                'title_snapshot' => $art->title,
                'image_snapshot' => $art->primary_image_url,
                'price_snapshot' => (int) round(((float) $row['price']) * 1000), // store in baisa (int)
            ]);
        }

        // Prepare Thawani payload (approximation)
        $payload = [
            'client_reference_id' => (string) $order->id,
            'mode' => 'payment',
            'products' => collect($items)->map(function ($row) {
                return [
                    'name' => $row['art']->title,
                    'quantity' => (int) $row['qty'],
                    // Thawani uses Baisa (1 OMR = 1000 baisa). If prices are SAR/OMR, adapt accordingly.
                    'unit_amount' => (int) round(((float) $row['price']) * 1000),
                ];
            })->values()->all(),
            'success_url' => route('checkout.success', ['order' => $order->id]),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id]),
            'metadata' => ['order_no' => $order->order_no],
        ];

        $result = $thawani->createCheckoutSession($payload);
        if (($result['success'] ?? false) && isset($result['data'])) {
            $data = $result['data'];
            $order->update([
                'payment_reference' => $data['data']['id'] ?? $data['session_id'] ?? null,
            ]);
            // Redirect to hosted page or mock URL
            $url = $data['data']['session_url'] ?? $data['data']['invoice_url'] ?? $data['invoice_url'] ?? null;
            if ($url) {
                return redirect()->away($url);
            }
            // Dev fallback: if no URL and Thawani key missing, go directly to success
            $thawaniKey = config('services.thawani.key') ?? env('THAWANI_SECRET');
            if (empty($thawaniKey)) {
                // Clear cart and go to success
                $request->session()->forget('cart');
                return redirect()->route('checkout.success', ['order' => $order->id]);
            }
        }

        return redirect()->route('cart.index')->with('error', 'تعذر إنشاء جلسة الدفع. حاول لاحقاً.');
    }

    /**
     * GET /payment/return — handler for payment provider returns (success/failure).
     * If using webhooks primarily, this is a UX endpoint.
     */
    public function paymentReturn(Request $request, OrderStateService $state)
    {
        $orderId = $request->integer('order');
        $status = $request->string('status')->toString(); // e.g., success|cancel
        if (!$orderId) {
            return redirect()->route('cart.index')->with('error', 'عودة غير صحيحة من بوابة الدفع.');
        }
        $order = Order::findOrFail($orderId);

        if ($status === 'success') {
            // Best effort mark as paid; webhook will be the source of truth if enabled
            try {
                $state->markPaid($order, $request->user());
            } catch (\Throwable $e) {
                // ignore, webhook may update later
            }
            // Clear cart for the user
            $request->session()->forget('cart');
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        return redirect()->route('checkout.cancel', ['order' => $order->id]);
    }

    /**
     * POST /payment/webhook — trusted final confirmation from the payment provider.
     * This should verify signatures in production. Here we perform a minimal implementation.
     */
    public function webhook(Request $request, OrderStateService $state)
    {
        // TODO: Verify provider signature/secret from headers
        $payload = $request->all();
        $orderId = (int) ($payload['client_reference_id'] ?? $payload['order_id'] ?? 0);
        if (!$orderId) {
            // Try by payment reference if present
            $reference = $payload['payment_reference'] ?? $payload['id'] ?? null;
            if ($reference) {
                $order = Order::where('payment_reference', $reference)->first();
            } else {
                return response()->json(['ok' => false, 'message' => 'no order id'], 400);
            }
        } else {
            $order = Order::find($orderId);
        }

        if (!$order) {
            return response()->json(['ok' => false, 'message' => 'order not found'], 404);
        }

        // Example: if event indicates paid
        $eventType = $payload['event'] ?? $payload['status'] ?? null;
        if (in_array($eventType, ['payment.succeeded', 'paid', 'success'], true)) {
            $state->markPaid($order, null, [
                'payment_provider' => $payload['provider'] ?? 'gateway',
                'payment_reference' => $payload['payment_reference'] ?? ($payload['data']['id'] ?? null),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function success(Request $request)
    {
        $orderId = $request->integer('order') ?: (int) $request->session()->get('last_order_id', 0);
        if (!$orderId) {
            return redirect()->route('cart.index')->with('error', 'رابط غير صحيح لصفحة تأكيد الدفع.');
        }
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'الطلب غير موجود أو تم حذفه.');
        }
        $order->update(['status' => 'confirmed', 'payment_status' => Order::PAYMENT_PAID]);
        // clear cart
        $request->session()->forget('cart');
        // If this was an auction settlement, consume the deposit
        if ($request->session()->has('consume_deposit_auction_id')) {
            $auctionId = $request->session()->pull('consume_deposit_auction_id');
            if ($request->user()) {
                BidderDeposit::where('user_id', $request->user()->id)
                    ->where('status', 'held')
                    ->where('reference', 'auction:' . $auctionId)
                    ->update(['status' => 'consumed']);
            }
        }
        return view('checkout.success', compact('order'));
    }

    public function cancel(Request $request)
    {
        $order = Order::findOrFail($request->integer('order'));
        return redirect()->route('cart.index')->with('error', 'تم إلغاء عملية الدفع.');
    }
}
