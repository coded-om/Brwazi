<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeOpenedForAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $reason)
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
            ->subject('نزاع جديد على الطلب #' . $this->order->order_no)
            ->line('تم فتح نزاع على الطلب. السبب: ' . $this->reason)
            ->action('عرض الطلب', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تم فتح نزاع على الطلب #' . $this->order->order_no,
            'reason' => $this->reason,
        ];
    }
}
