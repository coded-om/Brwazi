<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReportStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Report $report)
    {
    }

    public function via($notifiable): array
    {
        return ['mail']; // يمكن إضافة database لاحقاً
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تحديث حالة البلاغ #' . $this->report->id)
            ->greeting('مرحباً')
            ->line('تم تحديث حالة البلاغ الخاص بك إلى: ' . $this->report->status_label)
            ->when($this->report->notes, function ($msg) {
                return $msg->line('ملاحظات: ' . $this->report->notes);
            })
            ->action('عرض البلاغ', url('/reports/' . $this->report->id))
            ->line('شكراً لتعاونك في تحسين المنصة.');
    }
}
