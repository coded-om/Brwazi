<?php

namespace App\Policies;

use App\Models\Order;
use Illuminate\Contracts\Auth\Authenticatable;

class OrderPolicy
{
    public function view(Authenticatable $user, Order $order): bool
    {
        return $this->isParty($user, $order) || $this->isAdmin($user) || $order->user_id === $user->id;
    }

    /** Update shipping info (seller only) when order is paid and not shipped */
    public function updateShipping(Authenticatable $user, Order $order): bool
    {
        $isSeller = ((int) $order->seller_id === (int) $user->id);
        $paid = $order->payment_status === Order::PAYMENT_PAID;
        $notShipped = $order->fulfillment_status !== Order::FULFILLMENT_SHIPPED;
        return ($isSeller || $this->isAdmin($user)) && $paid && $notShipped;
    }

    public function confirmDelivered(Authenticatable $user, Order $order): bool
    {
        $isBuyer = ((int) $order->buyer_id === (int) $user->id);
        $isShipped = $order->fulfillment_status === Order::FULFILLMENT_SHIPPED;
        return ($isBuyer || $this->isAdmin($user)) && $isShipped;
    }

    public function downloadInvoice(Authenticatable $user, Order $order): bool
    {
        return $this->isParty($user, $order) || $this->isAdmin($user);
    }

    /** Admin only */
    public function resolveDispute(Authenticatable $user, Order $order): bool
    {
        return $this->isAdmin($user);
    }

    private function isParty(Authenticatable $user, Order $order): bool
    {
        return ((int) $order->buyer_id === (int) $user->id) || ((int) $order->seller_id === (int) $user->id);
    }

    private function isAdmin(Authenticatable $user): bool
    {
        if (method_exists($user, 'isAdmin')) {
            return (bool) $user->isAdmin();
        }
        // Try common role checks
        if (property_exists($user, 'role')) {
            return in_array($user->role, ['admin', 'super_admin', 'moderator'], true);
        }
        if (method_exists($user, 'hasRole')) {
            try {
                return $user->hasRole('admin') || $user->hasRole('super_admin') || $user->hasRole('moderator');
            } catch (\Throwable $e) {
            }
        }
        return false;
    }
}
