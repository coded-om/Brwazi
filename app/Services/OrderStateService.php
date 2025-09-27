<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\DeliveredReminder;
use App\Notifications\OrderAutoCompleted;
use App\Notifications\OrderShipped;
use App\Notifications\DisputeOpenedForAdmin;
use App\Notifications\DisputeResolved;
use Carbon\Carbon;

/**
 * Centralizes order state transitions and permission checks.
 *
 * Transitions:
 * - pending_payment -> paid (return/webhook)
 * - paid -> shipped (artist adds tracking)
 * - shipped -> delivered (buyer confirms or carrier marks delivered)
 * - delivered -> completed (buyer confirms or auto after X days)
 * - cancel: anytime before shipped
 * - disputes: sub-flow -> refunded or completed by admin decision
 */
class OrderStateService
{
    /**
     * Mark order as paid. Idempotent.
     */
    public function markPaid(Order $order, ?User $actor = null, array $attributes = []): Order
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return $order; // idempotent
        }

        // Authorization: allow webhook/system, buyer, or admin
        if (!$this->canMarkPaid($order, $actor)) {
            throw new \DomainException('غير مسموح بتعليم الطلب كمدفوع.');
        }

        $order->fill(array_intersect_key($attributes, array_flip([
            'buyer_id',
            'payment_provider',
            'payment_reference',
            'platform_fee',
            'shipping_cost',
            'invoice_number',
            'invoice_pdf_path',
            'notes'
        ])));

        $order->payment_status = Order::PAYMENT_PAID;
        // Initialize fulfillment status when payment is captured
        if (!$order->fulfillment_status) {
            $order->fulfillment_status = Order::FULFILLMENT_UNFULFILLED;
        }

        $order->save();
        return $order->refresh();
    }

    /**
     * Seller ships the order: set carrier/tracking and mark shipped.
     */
    public function ship(Order $order, User $actor, string $carrier, string $tracking, ?Carbon $when = null): Order
    {
        if ($order->fulfillment_status === Order::FULFILLMENT_SHIPPED) {
            return $order; // idempotent
        }

        if (!$this->canShip($order, $actor)) {
            throw new \DomainException('غير مسموح بشحن هذا الطلب.');
        }

        if ($order->payment_status !== Order::PAYMENT_PAID) {
            throw new \DomainException('لا يمكن الشحن قبل الدفع.');
        }

        $order->shipping_carrier = $carrier;
        $order->tracking_number = $tracking;
        $order->shipped_at = $when ?: now();
        $order->fulfillment_status = Order::FULFILLMENT_SHIPPED;
        $order->save();
        // Notify buyer that the order has been shipped
        try {
            if ($order->buyer) {
                $order->buyer->notify(new OrderShipped($order));
                // schedule a delivered reminder after 2 days
                $order->buyer->notify((new DeliveredReminder($order))->delay(now()->addDays(2)));
            }
        } catch (\Throwable $e) {
            // ignore for MVP
        }
        return $order->refresh();
    }

    /**
     * Mark as delivered (buyer confirmation or carrier webhook/admin action).
     */
    public function markDelivered(Order $order, User $actor, ?Carbon $when = null): Order
    {
        if ($order->fulfillment_status === Order::FULFILLMENT_DELIVERED) {
            return $order; // idempotent
        }

        if (!$this->canMarkDelivered($order, $actor)) {
            throw new \DomainException('غير مسموح بتعليم هذا الطلب كمسلّم.');
        }

        if ($order->fulfillment_status !== Order::FULFILLMENT_SHIPPED) {
            throw new \DomainException('لا يمكن التعليم كمسلّم قبل الشحن.');
        }

        $order->delivered_at = $when ?: now();
        $order->fulfillment_status = Order::FULFILLMENT_DELIVERED;
        $order->save();
        return $order->refresh();
    }

    /**
     * Complete the order (buyer confirms or automatic after X days from delivered).
     */
    public function complete(Order $order, ?User $actor = null): Order
    {
        if ($order->fulfillment_status === Order::FULFILLMENT_COMPLETED) {
            return $order; // idempotent
        }

        if (!$this->canComplete($order, $actor)) {
            throw new \DomainException('غير مسموح بإكمال الطلب.');
        }

        if ($order->fulfillment_status !== Order::FULFILLMENT_DELIVERED) {
            throw new \DomainException('لا يمكن الإكمال قبل التسليم.');
        }

        $order->fulfillment_status = Order::FULFILLMENT_COMPLETED;
        $order->save();
        // Notify parties that the order is completed
        try {
            if ($order->buyer)
                $order->buyer->notify(new OrderAutoCompleted($order));
            if ($order->seller)
                $order->seller->notify(new OrderAutoCompleted($order));
        } catch (\Throwable $e) {
        }
        return $order->refresh();
    }

    /**
     * Cancel anytime before shipped.
     */
    public function cancel(Order $order, User $actor, ?string $reason = null): Order
    {
        if ($order->fulfillment_status === Order::FULFILLMENT_CANCELED) {
            return $order; // idempotent
        }

        if (!$this->canCancel($order, $actor)) {
            throw new \DomainException('غير مسموح بإلغاء الطلب.');
        }

        if (!in_array($order->fulfillment_status, [null, Order::FULFILLMENT_UNFULFILLED], true)) {
            throw new \DomainException('لا يمكن إلغاء الطلب بعد الشحن.');
        }

        $order->fulfillment_status = Order::FULFILLMENT_CANCELED;
        if ($reason) {
            $order->notes = trim(($order->notes ? ($order->notes . "\n") : '') . 'إلغاء: ' . $reason);
        }
        $order->save();
        return $order->refresh();
    }

    /**
     * Open a dispute (buyer or seller). Moves to DISPUTED.
     */
    public function dispute(Order $order, User $actor, string $reason): Order
    {
        if ($order->fulfillment_status === Order::FULFILLMENT_DISPUTED) {
            return $order; // idempotent
        }

        if (!$this->canDispute($order, $actor)) {
            throw new \DomainException('غير مسموح بفتح نزاع لهذا الطلب.');
        }

        if ($order->payment_status !== Order::PAYMENT_PAID) {
            throw new \DomainException('لا يمكن فتح نزاع قبل الدفع.');
        }

        $order->fulfillment_status = Order::FULFILLMENT_DISPUTED;
        $order->notes = trim(($order->notes ? ($order->notes . "\n") : '') . 'نزاع: ' . $reason);
        $order->save();
        // Notify admins about the dispute
        try {
            if (class_exists(\App\Models\Admin::class)) {
                $admins = \App\Models\Admin::query()->whereIn('role', ['admin', 'super_admin', 'moderator'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new DisputeOpenedForAdmin($order, $reason));
                }
            }
        } catch (\Throwable $e) {
        }
        return $order->refresh();
    }

    /**
     * Resolve a dispute by admin decision: refunded or completed.
     */
    /**
     * @param User|\Illuminate\Contracts\Auth\Authenticatable $admin
     */
    public function resolveDispute(Order $order, $admin, string $adminDecision, ?string $note = null): Order
    {
        if (!$this->isAdmin($admin)) {
            throw new \DomainException('هذا الإجراء يتطلب صلاحيات إدمن.');
        }

        if ($order->fulfillment_status !== Order::FULFILLMENT_DISPUTED) {
            throw new \DomainException('لا يوجد نزاع لحلّه.');
        }

        if (!in_array($adminDecision, ['refunded', 'completed'], true)) {
            throw new \InvalidArgumentException('قرار غير صالح. استخدم refunded أو completed.');
        }

        if ($note) {
            $order->notes = trim(($order->notes ? ($order->notes . "\n") : '') . 'قرار الإدمن: ' . $note);
        }

        if ($adminDecision === 'refunded') {
            $order->payment_status = Order::PAYMENT_REFUNDED;
            $order->fulfillment_status = Order::FULFILLMENT_CANCELED;
        } else { // completed
            $order->fulfillment_status = Order::FULFILLMENT_COMPLETED;
        }

        $order->save();
        // notify both parties about the decision
        try {
            if ($order->buyer)
                $order->buyer->notify(new DisputeResolved($order, $adminDecision, $note));
            if ($order->seller)
                $order->seller->notify(new DisputeResolved($order, $adminDecision, $note));
        } catch (\Throwable $e) {
        }
        return $order->refresh();
    }

    /**
     * Auto-complete delivered orders after X days (to be called by a scheduler).
     */
    public function autoCompleteIfElapsed(Order $order, int $days = 7): ?Order
    {
        if ($order->fulfillment_status !== Order::FULFILLMENT_DELIVERED) {
            return null;
        }

        if (!$order->delivered_at) {
            return null;
        }

        if ($order->delivered_at->lt(now()->subDays($days))) {
            $order->fulfillment_status = Order::FULFILLMENT_COMPLETED;
            $order->save();
            try {
                if ($order->buyer)
                    $order->buyer->notify(new OrderAutoCompleted($order));
                if ($order->seller)
                    $order->seller->notify(new OrderAutoCompleted($order));
            } catch (\Throwable $e) {
            }
            return $order->refresh();
        }

        return null;
    }

    // ---------------- Permission checks ---------------- //

    public function canMarkPaid(Order $order, $actor = null): bool
    {
        // webhook/system (no actor), buyer paying, or admin
        return $actor === null
            || $this->isAdmin($actor)
            || ($order->buyer_id && $actor->id === (int) $order->buyer_id);
    }

    public function canShip(Order $order, $actor): bool
    {
        return $this->isAdmin($actor) || ($order->seller_id && $actor->id === (int) $order->seller_id);
    }

    public function canMarkDelivered(Order $order, $actor): bool
    {
        // buyer confirmation or admin
        return $this->isAdmin($actor) || ($order->buyer_id && $actor->id === (int) $order->buyer_id);
    }

    public function canComplete(Order $order, $actor = null): bool
    {
        // buyer or admin or system (auto) can complete
        return $actor === null || $this->isAdmin($actor) || ($order->buyer_id && $actor->id === (int) $order->buyer_id);
    }

    public function canCancel(Order $order, $actor): bool
    {
        // before shipped, buyer/seller/admin
        $beforeShipped = in_array($order->fulfillment_status, [null, Order::FULFILLMENT_UNFULFILLED], true);
        $isParty = ($order->buyer_id && $actor->id === (int) $order->buyer_id)
            || ($order->seller_id && $actor->id === (int) $order->seller_id);
        return $beforeShipped && ($isParty || $this->isAdmin($actor));
    }

    public function canDispute(Order $order, $actor): bool
    {
        // either party or admin can open dispute after paid
        $isParty = ($order->buyer_id && $actor->id === (int) $order->buyer_id)
            || ($order->seller_id && $actor->id === (int) $order->seller_id);
        return $order->payment_status === Order::PAYMENT_PAID && ($isParty || $this->isAdmin($actor));
    }

    /**
     * @param mixed $user
     */
    private function isAdmin($user): bool
    {
        // Heuristic: you may have roles/permissions; adjust as needed.
        // Try common flags or roles on your User model.
        if ($user === null) return false;
        if (method_exists($user, 'isAdmin')) {
            try { return (bool) $user->isAdmin(); } catch (\Throwable $e) { /* ignore */ }
        }
        if (property_exists($user, 'role')) {
            try { return in_array($user->role, ['admin','super_admin','moderator'], true); } catch (\Throwable $e) {}
        }
        if (method_exists($user, 'hasRole')) {
            try { return $user->hasRole('admin') || $user->hasRole('super_admin') || $user->hasRole('moderator'); } catch (\Throwable $e) {}
        }
        // Also accept App\Models\Admin instances by table name detection
        if (is_object($user) && get_class($user) === \App\Models\Admin::class) {
            return true;
        }
        return false;
    }
}
