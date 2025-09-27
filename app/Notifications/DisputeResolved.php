<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $decision, public ?string $note = null)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url('/orders/' . $this->order->id);
        $statusText = $this->decision === 'refunded' ? 'مسترد' : 'مكتمل';
        $mail = (new MailMessage)
            ->subject('تم حسم النزاع للطلب #' . $this->order->order_no)
            ->line('تم اتخاذ قرار إداري: ' . $statusText);
        if ($this->note)
            $mail->line('ملاحظة: ' . $this->note);
        return $mail->action('عرض الطلب', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'decision' => $this->decision,
            'note' => $this->note,
            'message' => 'تم حسم النزاع للطلب #' . $this->order->order_no,
        ];
    }
}
