<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidBuyer extends Notification implements ShouldQueue
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
        $url = url('/orders/' . $this->order->id . '/invoice');
        return (new MailMessage)
            ->subject('تم تأكيد الدفع #' . $this->order->order_no)
            ->line('تم تأكيد الدفع لطلبك.')
            ->action('عرض الفاتورة', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تم تأكيد الدفع #' . $this->order->order_no,
        ];
    }
}
