<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function open(Request $request, Order $order, OrderStateService $state)
    {
        $data = $request->validate([
            'reason' => 'required|string|min:10',
            // 'images.*' => 'image|max:2048' // يمكن التفعيل لاحقاً
        ]);

        try {
            $state->dispute($order, $request->user(), $data['reason']);
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        if (function_exists('notify'))
            notify()->success('تم فتح النزاع وسيقوم الفريق بمراجعته');
        return back();
    }

    public function resolve(Request $request, Order $order, OrderStateService $state)
    {
        $this->authorize('resolveDispute', $order);

        $data = $request->validate([
            'decision' => 'required|in:refunded,completed',
            'note' => 'nullable|string|max:5000'
        ]);

        try {
            $state->resolveDispute($order, $request->user(), $data['decision'], $data['note'] ?? null);
        } catch (\Throwable $e) {
            if (function_exists('notify'))
                notify()->error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        if (function_exists('notify'))
            notify()->success('تم حسم النزاع');
        return back();
    }
}
