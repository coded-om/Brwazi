<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Tag;
// Explicit use for clarity (even same namespace) to satisfy linters
use App\Models\ArtworkImage;
use App\Models\ArtworkLike;
use App\Models\User;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'type',
        'medium',
        'weight',
        'year',
        'dimensions',
        'price',
        'likes_count',
        'images_count',
        'sale_mode',
        'allow_offers',
        'edition_type',
        'copy_digital',
        'copy_printed',
        'auction_start_price',
        'status',
        'published_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'auction_start_price' => 'decimal:2',
        'allow_offers' => 'boolean',
        'copy_digital' => 'boolean',
        'copy_printed' => 'boolean',
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    public static function categories(): array
    {
        try {
            if (class_exists(\App\Models\ArtCategory::class)) {
                $opts = \App\Models\ArtCategory::listOptions();
                if (!empty($opts))
                    return $opts;
            }
        } catch (\Throwable $e) {
            // fallback below
        }
        return [
            'digital' => 'فن رقمي',
            'traditional' => 'فن تقليدي',
            'photography' => 'تصوير',
            'calligraphy' => 'خط عربي',
            'sculpture' => 'نحت',
            'mixed' => 'فن مختلط',
        ];
    }

    public static function categoryLabel(?string $slug): ?string
    {
        $map = static::categories();
        return $slug && isset($map[$slug]) ? $map[$slug] : $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtworkImage::class)->orderBy('sort_order');
    }

    public function getPrimaryImageAttribute()
    {
        // Use loaded relation to avoid extra queries when possible
        if ($this->relationLoaded('images')) {
            $collection = $this->getRelation('images');
            return $collection->firstWhere('is_primary', true) ?? $collection->first();
        }
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $img = $this->primary_image;
        return $img ? asset('storage/' . $img->path) : null;
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(ArtworkLike::class);
    }

    public function likedBy(?User $user): bool
    {
        if (!$user?->id)
            return false;
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Automatically exclude artworks that belong to banned users (status=3)
     */
    protected static function booted(): void
    {
        static::addGlobalScope('owner_not_banned', function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('status', '!=', User::STATUS_BANNED);
            });
        });
    }

    /** البلاغات على هذا العمل */
    public function reports()
    {
        return $this->morphMany(Report::class, 'target');
    }
}
