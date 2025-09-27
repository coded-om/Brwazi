<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\LiteratureWorkshopRegistration; // registration model
use App\Models\Concerns\HasWorkshopCommon;

class LiteratureWorkshop extends Model
{
    use HasFactory, HasWorkshopCommon;

    protected $fillable = [
        'title',
        'slug',
        'presenter_name',
        'presenter_bio',
        'presenter_avatar_path',
        'genre',
        'starts_at',
        'duration_minutes',
        'capacity',
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
        return $this->hasMany(LiteratureWorkshopRegistration::class);
    }

    public function registrationsCount(): int
    {
        return $this->registrations()->count();
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    // Shared scopes & accessor via HasWorkshopCommon
}
