<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')->where('buyer_id', $request->user()->id)
            ->latest()->paginate(12);
        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items');
        return view('orders.show', compact('order'));
    }

    /**
     * POST /orders/{order}/shipping — seller adds shipping info -> shipped
     */
    public function addShipping(Request $request, Order $order, OrderStateService $state)
    {
        $this->authorize('updateShipping', $order);
        $data = $request->validate([
            'shipping_carrier' => 'required|string|max:191',
            'tracking_number' => 'required|string|max:191',
        ]);
        try {
            $state->ship($order, $request->user(), $data['shipping_carrier'], $data['tracking_number']);
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
        if (function_exists('notify'))
            notify()->success('تم تحديث حالة الطلب إلى: تم الشحن');
        return back();
    }

    /**
     * POST /orders/{order}/delivered — buyer confirms delivery -> delivered
     */
    public function confirmDelivered(Request $request, Order $order, OrderStateService $state)
    {
        $this->authorize('confirmDelivered', $order);
        try {
            $state->markDelivered($order, $request->user());
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
        if (function_exists('notify'))
            notify()->success('تم تأكيد استلام الشحنة');
        return back();
    }

    /**
     * POST /orders/{order}/dispute — buyer opens a dispute
     */
    public function openDispute(Request $request, Order $order, OrderStateService $state)
    {
        $data = $request->validate(['reason' => 'required|string|min:5']);
        try {
            $state->dispute($order, $request->user(), $data['reason']);
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
        if (function_exists('notify'))
            notify()->success('تم فتح النزاع');
        return back();
    }

    /**
     * POST /admin/orders/{order}/resolve-dispute — admin resolves dispute
     */
    public function resolveDispute(Request $request, Order $order, OrderStateService $state)
    {
        $user = $request->user();
        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        if (!$isAdmin)
            throw new AuthorizationException('غير مصرح');

        $data = $request->validate([
            'decision' => 'required|in:refunded,completed',
            'note' => 'nullable|string|max:5000',
        ]);
        try {
            $state->resolveDispute($order, $user, $data['decision'], $data['note'] ?? null);
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
        if (function_exists('notify'))
            notify()->success('تم حسم النزاع');
        return back();
    }

    /**
     * GET /orders/{order}/invoice — PDF download (only parties or admin)
     */
    public function downloadInvoice(Request $request, Order $order)
    {
        // If admin is authenticated via admin guard, allow without policy (different guard than web)
        if (!auth('admin')->check()) {
            $this->authorize('downloadInvoice', $order);
        }

        if (!$order->invoice_pdf_path) {
            abort(404, 'الفاتورة غير متوفرة حالياً');
        }

        $fullPath = storage_path('app/' . ltrim($order->invoice_pdf_path, '/'));
        if (!is_file($fullPath)) {
            abort(404, 'ملف الفاتورة غير موجود');
        }

        return response()->download($fullPath, basename($fullPath));
    }
}
