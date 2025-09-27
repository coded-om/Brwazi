<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveredReminder extends Notification implements ShouldQueue
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
            ->subject('تذكير: هل استلمت طلبك؟ #' . $this->order->order_no)
            ->line('مر يومان منذ شحن طلبك. يرجى تأكيد الاستلام عند وصول الشحنة.')
            ->action('عرض الطلب', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تذكير بتأكيد الاستلام لطلب #' . $this->order->order_no,
        ];
    }
}
