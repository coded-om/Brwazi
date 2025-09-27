<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $carrier = $this->order->shipping_carrier ?: '—';
        $tracking = $this->order->tracking_number ?: '—';
        $url = url('/orders/' . $this->order->id);
        return (new MailMessage)
            ->subject('تم شحن طلبك #' . $this->order->order_no)
            ->line("شركة الشحن: {$carrier}")
            ->line("رقم التتبع: {$tracking}")
            ->action('عرض الطلب', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تم شحن الطلب #' . $this->order->order_no,
        ];
    }
}
