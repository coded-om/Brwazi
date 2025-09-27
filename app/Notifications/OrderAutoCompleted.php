<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderAutoCompleted extends Notification implements ShouldQueue
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
        $url = url('/orders/' . $this->order->id);
        return (new MailMessage)
            ->subject('اكتمل طلبك تلقائياً #' . $this->order->order_no)
            ->line('تم اكتمال الطلب تلقائياً بعد انقضاء مدة التسليم.')
            ->action('عرض الطلب', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تم اكتمال الطلب تلقائياً #' . $this->order->order_no,
        ];
    }
}
