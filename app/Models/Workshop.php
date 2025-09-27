<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'presenter_name',
        'presenter_bio',
        'presenter_avatar_path',
        'art_type',
        'starts_at',
        'duration_minutes',
        'location',
        'external_apply_url',
        'short_description',
        'is_published',
        'is_approved',
        'submitted_by_user_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('is_approved', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function getDurationLabelAttribute(): ?string
    {
        if (!$this->duration_minutes) {
            return null;
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        $parts = [];
        if ($hours) {
            $parts[] = $hours . ' ساعة';
        }
        if ($minutes) {
            $parts[] = $minutes . ' دقيقة';
        }

        return implode(' و', $parts);
    }
}
