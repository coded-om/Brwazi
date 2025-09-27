<?php

namespace App\Filament\Pages\Auth;

use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $e) {
            // Show a clear notify inside Filament panel
            Notification::make()
                ->title('تعذّر تسجيل الدخول')
                ->body('بيانات الدخول غير صحيحة. يرجى التأكد من البريد الإلكتروني وكلمة المرور ثم المحاولة مرة أخرى.')
                ->danger()
                ->persistent()
                ->send();

            // Replace the inline field error with an Arabic message
            $errors = $e->errors();
            $message = 'بيانات الدخول غير صحيحة.';

            if (isset($errors['data.email'])) {
                $errors['data.email'] = [$message];
            } elseif (isset($errors['data.password'])) {
                $errors['data.password'] = [$message];
            } else {
                $errors['data.email'] = [$message];
            }

            throw ValidationException::withMessages($errors);
        }
    }
}
