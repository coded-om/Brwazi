<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * تقرير بلاغ من مستخدم على عنصر (منشور / عمل فني / مستخدم ...)
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'type',
        'target_type',
        'target_id',
        'reason',
        'details',
        'status',
        'handled_by',
        'handled_at',
        'notes'
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    // أنواع البلاغات (يمكن التوسعة لاحقاً)
    const TYPE_MISLEADING = 'misleading';           // تضليل / معلومات مضللة
    const TYPE_RIGHTS = 'rights_violation';         // ينتهك حقوقي
    const TYPE_ILLEGAL = 'illegal_or_harmful';      // ضار أو غير قانوني
    const TYPE_FRAUD = 'fraud';                     // احتيال / نصب
    const TYPE_ADULT = 'adult';                     // محتوى للبالغين
    const TYPE_SPAM = 'spam';                       // بريد مزعج / تكرار
    const TYPE_ABUSE = 'abuse';                     // إساءة / مضايقة
    const TYPE_OTHER = 'other';                     // أخرى

    public static function types(): array
    {
        return [
            self::TYPE_MISLEADING => 'معلومات مضللة',
            self::TYPE_RIGHTS => 'ينتهك الحقوق',
            self::TYPE_ILLEGAL => 'ضار أو غير قانوني',
            self::TYPE_FRAUD => 'احتيال / نصب',
            self::TYPE_ADULT => 'محتوى للبالغين',
            self::TYPE_SPAM => 'مزعج / سبام',
            self::TYPE_ABUSE => 'إساءة',
            self::TYPE_OTHER => 'أخرى',
        ];
    }

    // حالات معالجة البلاغ
    const STATUS_PENDING = 'pending';      // قيد المراجعة
    const STATUS_REVIEWING = 'reviewing';  // جاري التحقق
    const STATUS_RESOLVED = 'resolved';    // تم اتخاذ إجراء
    const STATUS_REJECTED = 'rejected';    // مرفوض

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_REVIEWING => 'جاري التحقق',
            self::STATUS_RESOLVED => 'تم الحل',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'handled_by');
    }

    /** الهدف (منشور – عمل فني – مستخدم – ... polymorphic) */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
