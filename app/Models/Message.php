<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'image_path',
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * المحادثة التي تنتمي إليها الرسالة
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * مرسل الرسالة
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * طلب اللوحة المرتبط بالرسالة (إن وجد)
     */
    public function artworkRequest(): HasOne
    {
        return $this->hasOne(ArtworkRequest::class);
    }

    /**
     * الحصول على رابط الصورة المرفقة
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . ltrim($this->image_path, '/')) : null;
    }

    /**
     * التحقق من وجود صورة مرفقة
     */
    public function hasImage()
    {
        return !is_null($this->image_path) && Storage::exists($this->image_path);
    }

    /**
     * تحديد نوع الرسالة تلقائياً حسب المحتوى
     */
    public static function determineType($content, $imagePath, $hasArtworkRequest = false)
    {
        if ($hasArtworkRequest) {
            return 'artwork_request';
        }

        if (!empty($imagePath)) {
            return 'image';
        }

        return 'text';
    }

    /**
     * تعليم الرسالة كمقروءة
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * scope للرسائل غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * scope للرسائل المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * scope لرسائل مستخدم معين
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * حذف الصورة عند حذف الرسالة
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($message) {
            if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
                Storage::disk('public')->delete($message->image_path);
            }
        });
    }
}
