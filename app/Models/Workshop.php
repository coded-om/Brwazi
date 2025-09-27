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
        'art_type',
        'starts_at',
        'duration_minutes',
        'location',
        'short_description',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now());
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
