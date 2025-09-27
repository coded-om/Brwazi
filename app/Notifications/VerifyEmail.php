<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailBase
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('🔐 تأكيد حسابك في برواز')
            ->greeting('أهلاً وسهلاً ' . $notifiable->fname . '! 🎉')
            ->line('مرحباً بك في **منصة برواز** - المنصة الرائدة للخدمات.')
            ->line('لإتمام تسجيلك وتفعيل حسابك، يرجى الضغط على الرابط التالي:')
            ->action('✅ تفعيل الحساب الآن', $verificationUrl)
            ->line('⏰ هذا الرابط صالح لمدة **60 دقيقة** فقط.')
            ->line('إذا لم تقم بإنشاء هذا الحساب، يرجى تجاهل هذه الرسالة.')
            ->line('---')
            ->line('💡 **نصيحة**: احفظ هذا الإيميل للرجوع إليه لاحقاً.')
            ->line('📧 **تم الإرسال عبر**: Mailgun - خدمة إيميل موثوقة')
            ->salutation('مع أطيب التحيات،' . PHP_EOL . '**فريق منصة برواز** 🚀' . PHP_EOL . 'noreply@brwazi.com');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
