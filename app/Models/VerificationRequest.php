<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'form_type', // visual | photo
        'full_name',
        'birth_date',
        'gender',
        'education',
        'address',
        'phone',
        'email',
        'nationality',
        'specialties', // json
        'id_file_path',
        'avatar_file_path',
        'cv_file_path',
        'works_files', // json array of paths
        'status', // pending|approved|rejected
        'decision_notes',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'specialties' => 'array',
        'works_files' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // أنواع الاستمارات
    public const FORM_VISUAL = 'visual';
    public const FORM_PHOTO = 'photo';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected static function booted(): void
    {
        static::saved(function (VerificationRequest $req) {
            // When status changes to approved, mark the user as VERIFIED (but don't downgrade PREMIUM)
            if ($req->wasChanged('status') && $req->status === self::STATUS_APPROVED) {
                $user = $req->user;
                if ($user instanceof User) {
                    if ($user->status !== User::STATUS_PREMIUM) {
                        $user->status = User::STATUS_VERIFIED;
                        $user->saveQuietly();
                    }
                }
            }

            // Notify user on any status change
            if ($req->wasChanged('status')) {
                try {
                    $req->user?->notify(new \App\Notifications\VerificationStatusChanged($req->status, $req->decision_notes));
                } catch (\Throwable $e) {
                    // swallow notification errors
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Scopes للحالات
    public function scopePending($q)
    {
        return $q->where('status', self::STATUS_PENDING);
    }
    public function scopeApproved($q)
    {
        return $q->where('status', self::STATUS_APPROVED);
    }
    public function scopeRejected($q)
    {
        return $q->where('status', self::STATUS_REJECTED);
    }
    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    // أدوات سريعة
    public function isVisual(): bool
    {
        return $this->form_type === self::FORM_VISUAL;
    }
    public function isPhoto(): bool
    {
        return $this->form_type === self::FORM_PHOTO;
    }
    public function worksCount(): int
    {
        return is_array($this->works_files) ? count($this->works_files) : 0;
    }
    public function requiredWorksRange(): array
    {
        return $this->isPhoto() ? [10, 10] : [5, 10];
    }

    // تحويل مسار التخزين إلى URL
    protected function fileUrl(?string $path): ?string
    {
        if (!$path)
            return null;
        // Use Storage disk for reliability; fallback to asset
        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($path)) {
                // Manually build public URL assuming storage symbolic link
                return asset('storage/' . ltrim($path, '/'));
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    // Accessors مفيدة للعرض في لوحة التحكم
    public function getIdFileUrlAttribute(): ?string
    {
        return $this->fileUrl($this->id_file_path ?? null);
    }
    public function getAvatarFileUrlAttribute(): ?string
    {
        return $this->fileUrl($this->avatar_file_path ?? null);
    }
    public function getCvFileUrlAttribute(): ?string
    {
        return $this->fileUrl($this->cv_file_path ?? null);
    }
    public function getWorksFileUrlsAttribute(): array
    {
        $paths = is_array($this->works_files) ? $this->works_files : [];
        return array_values(array_filter(array_map(fn($p) => $this->fileUrl($p), $paths)));
    }

    // اعتماد/رفض للوحة التحكم الإدارية
    public function markApproved(int $adminId, ?string $notes = null): void
    {
        $this->fill([
            'admin_id' => $adminId,
            'status' => self::STATUS_APPROVED,
            'decision_notes' => $notes,
            'reviewed_at' => now(),
        ])->save();
    }
    public function markRejected(int $adminId, ?string $notes = null): void
    {
        $this->fill([
            'admin_id' => $adminId,
            'status' => self::STATUS_REJECTED,
            'decision_notes' => $notes,
            'reviewed_at' => now(),
        ])->save();
    }
}
