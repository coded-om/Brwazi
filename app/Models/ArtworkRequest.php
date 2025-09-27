<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArtworkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'artist_id',
        'message_id',
        'title',
        'description',
        'status',
        'budget',
        'deadline',
        'reference_images',
        'artist_notes',
        'requester_notes',
    ];

    protected $casts = [
        'reference_images' => 'array',
        'budget' => 'decimal:2',
        'deadline' => 'date',
    ];

    // حالات طلب اللوحة
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * طالب اللوحة
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * الفنان المطلوب منه اللوحة
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    /**
     * الرسالة المرتبطة بالطلب
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * الحصول على روابط الصور المرجعية
     */
    public function getReferenceImageUrlsAttribute()
    {
        if (!$this->reference_images) {
            return [];
        }

        return collect($this->reference_images)
            ->filter(function ($path) {
                return Storage::disk('public')->exists($path);
            })
            ->map(function ($path) {
                return asset('storage/' . ltrim($path, '/'));
            })
            ->toArray();
    }

    /**
     * التحقق من وجود صور مرجعية
     */
    public function hasReferenceImages()
    {
        return !empty($this->reference_images) && is_array($this->reference_images);
    }

    /**
     * الحصول على لون الحالة للواجهة
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_ACCEPTED => 'bg-blue-100 text-blue-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * الحصول على نص الحالة بالعربية
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_ACCEPTED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
            default => 'غير محدد',
        };
    }

    /**
     * الحصول على أيقونة الحالة
     */
    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'fas fa-clock',
            self::STATUS_ACCEPTED => 'fas fa-check',
            self::STATUS_REJECTED => 'fas fa-times',
            self::STATUS_COMPLETED => 'fas fa-check-double',
            self::STATUS_CANCELLED => 'fas fa-ban',
            default => 'fas fa-question',
        };
    }

    /**
     * scope للطلبات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * scope للطلبات المقبولة
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * scope للطلبات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * scope لطلبات فنان معين
     */
    public function scopeForArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    /**
     * scope لطلبات مستخدم معين
     */
    public function scopeForRequester($query, $requesterId)
    {
        return $query->where('requester_id', $requesterId);
    }

    /**
     * قبول الطلب
     */
    public function accept($artistNotes = null)
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'artist_notes' => $artistNotes,
        ]);
    }

    /**
     * رفض الطلب
     */
    public function reject($artistNotes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'artist_notes' => $artistNotes,
        ]);
    }

    /**
     * إكمال الطلب
     */
    public function complete($artistNotes = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'artist_notes' => $artistNotes,
        ]);
    }

    /**
     * إلغاء الطلب
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'requester_notes' => $reason,
        ]);
    }

    /**
     * حذف الصور المرجعية عند حذف الطلب
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($request) {
            if ($request->reference_images && is_array($request->reference_images)) {
                foreach ($request->reference_images as $imagePath) {
                    if (Storage::exists($imagePath)) {
                        Storage::delete($imagePath);
                    }
                }
            }
        });
    }
}
