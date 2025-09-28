<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'country',
        'city',
        'address',
        'latitude',
        'longitude',
        'starts_at',
        'ends_at',
        'website_url',
        'contact_email',
        'contact_phone',
        'cover_image_path',
        'gallery_images',
        'is_published',
        'is_featured'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'gallery_images' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $base = Str::slug(mb_substr($model->title, 0, 80));
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }
}
