<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserWorkshopRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow only verified (or premium) users to submit
        $user = $this->user();
        return $user && $user->isVerified();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'presenter_name' => ['required', 'string', 'max:255'],
            'presenter_bio' => ['nullable', 'string', 'max:2000'],
            'presenter_avatar' => ['nullable', 'image', 'max:8192'],
            'art_type' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:1440'],
            'location' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'external_apply_url' => ['nullable', 'url', 'max:500'],
            'cover_image' => ['nullable', 'image', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الورشة مطلوب',
            'presenter_name.required' => 'اسم مقدم الورشة مطلوب',
            'starts_at.required' => 'موعد البداية مطلوب',
            'starts_at.after' => 'يجب أن يكون موعد البداية في المستقبل',
            'external_apply_url.url' => 'رابط التسجيل الخارجي غير صالح',
            'cover_image.image' => 'ملف الغلاف يجب أن يكون صورة',
            'presenter_avatar.image' => 'صورة المقدم يجب أن تكون ملف صورة',
        ];
    }
}
