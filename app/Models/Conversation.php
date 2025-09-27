<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * المستخدم الأول في المحادثة
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * المستخدم الثاني في المحادثة
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * جميع رسائل المحادثة
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * آخر رسالة في المحادثة
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * الحصول على المستخدم الآخر في المحادثة
     */
    public function getOtherUser($currentUserId)
    {
        return $this->user1_id == $currentUserId ? $this->user2 : $this->user1;
    }

    /**
     * التحقق من وجود رسائل غير مقروءة للمستخدم
     */
    public function hasUnreadMessages($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->exists();
    }

    /**
     * عد الرسائل غير المقروءة للمستخدم
     */
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * إنشاء محادثة جديدة أو الحصول على الموجودة
     */
    public static function createOrGet($user1Id, $user2Id)
    {
        // ترتيب المستخدمين لضمان عدم إنشاء محادثات مكررة
        $sortedUsers = collect([$user1Id, $user2Id])->sort()->values();

        return static::firstOrCreate([
            'user1_id' => $sortedUsers[0],
            'user2_id' => $sortedUsers[1],
        ]);
    }

    /**
     * تحديث تاريخ آخر رسالة
     */
    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }
}
