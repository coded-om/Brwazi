<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Artwork;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    // User Status Constants
    const STATUS_NORMAL = 0;       // عادي
    const STATUS_VERIFIED = 1;     // موثق
    const STATUS_PREMIUM = 2;      // مميز
    const STATUS_BANNED = 3;       // محظور

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'birthday',
        'country',
        'ProfileImage',
        'phone_number',
        'password',
        'bio',
        'tagline',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthday' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        $n = trim(($this->fname ?? '') . ' ' . ($this->lname ?? ''));
        return $n !== '' ? $n : ($this->name ?? $this->email);
    }

    /**
     * Get the user's profile image URL or default.
     */
    public function getProfileImageUrlAttribute(): string
    {
        return $this->ProfileImage
            ? asset('storage/' . $this->ProfileImage)
            : asset('imgs/default-avatar.png');
    }

    /**
     * Get available tagline options
     */
    public static function getTaglineOptions(): array
    {
        try {
            if (Schema::hasTable('taglines')) {
                return Cache::remember('taglines.active.list', now()->addMinutes(5), function () {
                    return \App\Models\Tagline::query()
                        ->where('active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->pluck('name')
                        ->filter()
                        ->values()
                        ->all();
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return [];
    }

    /**
     * Get status label in Arabic
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NORMAL => 'عادي',
            self::STATUS_VERIFIED => 'موثق',
            self::STATUS_PREMIUM => 'مميز',
            self::STATUS_BANNED => 'محظور',
            default => 'غير محدد'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NORMAL => 'gray',
            self::STATUS_VERIFIED => 'blue',
            self::STATUS_PREMIUM => 'gold',
            self::STATUS_BANNED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NORMAL => 'fas fa-user',
            self::STATUS_VERIFIED => 'fas fa-check-circle',
            self::STATUS_PREMIUM => 'fas fa-crown',
            self::STATUS_BANNED => 'fas fa-ban',
            default => 'fas fa-user'
        };
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED || $this->status === self::STATUS_PREMIUM;
    }

    /**
     * Check if user is premium
     */
    public function isPremium(): bool
    {
        return $this->status === self::STATUS_PREMIUM;
    }

    /**
     * Check if user is banned
     */
    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BANNED;
    }

    /**
     * رفع صورة البروفايل مع القص التلقائي
     */
    public function uploadProfileImage($file)
    {
        $imageService = app(\App\Services\ImageService::class);

        // حذف الصورة القديمة إن وجدت
        if ($this->ProfileImage) {
            $imageService->deleteImage($this->ProfileImage);
        }

        // رفع الصورة الجديدة مع القص
        $result = $imageService->uploadAndCrop($file, 'profiles', 300, 300);

        // تحديث المستخدم
        $this->update(['ProfileImage' => $result['path']]);

        return $result;
    }

    /**
     * الحصول على معلومات الصورة الشخصية
     */
    public function getProfileImageInfo()
    {
        if (!$this->ProfileImage) {
            return null;
        }

        return [
            'path' => $this->ProfileImage,
            'url' => $this->profile_image_url,
            'exists' => Storage::disk('public')->exists($this->ProfileImage)
        ];
    }

    /**
     * المحادثات التي يكون فيها المستخدم هو المستخدم الأول
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * المحادثات التي يكون فيها المستخدم هو المستخدم الثاني
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * جميع المحادثات للمستخدم
     */
    public function conversations()
    {
        return $this->conversationsAsUser1()
            ->union($this->conversationsAsUser2()->getQuery())
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * الرسائل المرسلة
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * طلبات اللوحات المرسلة من المستخدم
     */
    public function sentArtworkRequests()
    {
        return $this->hasMany(ArtworkRequest::class, 'requester_id');
    }

    /**
     * طلبات اللوحات المستلمة للمستخدم (الفنان)
     */
    public function receivedArtworkRequests()
    {
        return $this->hasMany(ArtworkRequest::class, 'artist_id');
    }

    /**
     * عدد الرسائل غير المقروءة للمستخدم
     */
    public function getUnreadMessagesCountAttribute()
    {
        return Message::whereHas('conversation', function ($query) {
            $query->where('user1_id', $this->id)
                ->orWhere('user2_id', $this->id);
        })
            ->where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * عدد طلبات اللوحات المعلقة للفنان
     */
    public function getPendingArtworkRequestsCountAttribute()
    {
        return $this->receivedArtworkRequests()
            ->where('status', ArtworkRequest::STATUS_PENDING)
            ->count();
    }

    /**
     * أعمال المستخدم (لوحات/فن)
     */
    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class)->latest();
    }

    /**
     * طلبات التوثيق (العضوية) الخاصة بالمستخدم
     */
    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class)->latest();
    }

    /**
     * هل لدى المستخدم طلب توثيق قيد المراجعة
     */
    public function hasPendingVerification(): bool
    {
        return $this->verificationRequests()
            ->where('status', \App\Models\VerificationRequest::STATUS_PENDING)
            ->exists();
    }

    /** البلاغات التي أرسلها المستخدم */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
}
