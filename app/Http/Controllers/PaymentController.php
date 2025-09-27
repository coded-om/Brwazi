<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaidService;
use App\Services\OrderStateService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function return(Request $request, OrderStateService $state)
    {
        $orderId = $request->integer('order');
        $status = $request->string('status')->toString(); // success|cancel
        if (!$orderId) {
            return redirect()->route('cart.index')->with('error', 'عودة غير صحيحة من بوابة الدفع.');
        }
        $order = Order::findOrFail($orderId);

        if ($status === 'success') {
            // لا نغير الحالة هنا نهائياً، webhook هو الموثوق، لكن ممكن تعلّم مبدئياً.
            try {
                $state->markPaid($order, $request->user());
            } catch (\Throwable $e) {
                // تجاهل؛ سيقوم الويبهوك بالتحديث
            }
            $request->session()->forget('cart');
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        return redirect()->route('checkout.cancel', ['order' => $order->id]);
    }

    public function webhook(Request $request, OrderStateService $state, OrderPaidService $paid)
    {
        // TODO: تحقق من التوقيع/المفاتيح من الهيدر
        $payload = $request->all();
        $orderId = (int) ($payload['client_reference_id'] ?? $payload['order_id'] ?? 0);
        $order = $orderId ? Order::find($orderId) : null;
        if (!$order && ($ref = ($payload['payment_reference'] ?? $payload['id'] ?? null))) {
            $order = Order::where('payment_reference', $ref)->first();
        }
        if (!$order) {
            return response()->json(['ok' => false, 'message' => 'order not found'], 404);
        }

        $eventType = $payload['event'] ?? $payload['status'] ?? null;
        if (in_array($eventType, ['payment.succeeded', 'paid', 'success'], true)) {
            $state->markPaid($order, null, [
                'payment_provider' => $payload['provider'] ?? 'gateway',
                'payment_reference' => $payload['payment_reference'] ?? ($payload['data']['id'] ?? null),
            ]);
            // بعد الدفع، فعّل خدمات ما بعد الدفع
            $paid->handle($order);
        }

        return response()->json(['ok' => true]);
    }
}
