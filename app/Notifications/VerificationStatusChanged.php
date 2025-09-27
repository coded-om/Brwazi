<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public string $status;
    public ?string $notes;

    public function __construct(string $status, ?string $notes = null)
    {
        $this->status = $status;
        $this->notes = $notes;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->status === 'approved' ? 'تم اعتماد طلب التوثيق' : ($this->status === 'rejected' ? 'تم رفض طلب التوثيق' : 'تحديث حالة طلب التوثيق');
        $msg = (new MailMessage)
            ->subject($title)
            ->greeting('مرحباً،')
            ->line($title);

        if ($this->notes) {
            $msg->line('ملاحظات:')->line($this->notes);
        }

        $msg->action('زيارة الحساب', url('/user/dashboard'))
            ->line('شكراً لتعاملك مع بروازي.');

        return $msg;
    }
}
