<?php

namespace App\Models\Concerns;

trait HasWorkshopCommon
{
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
        if ($hours)
            $parts[] = $hours . ' ساعة';
        if ($minutes)
            $parts[] = $minutes . ' دقيقة';
        return implode(' و', $parts);
    }
}
