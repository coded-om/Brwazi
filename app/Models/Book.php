<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'publisher_id',
        'title',
        'slug',
        'isbn',
        'description',
        'language',
        'type',
        'publish_year',
        'pages',
        'price_omr',
        'compare_at_price_omr',
        'cover_image_path',
        'stock',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'publish_year' => 'integer',
            'pages' => 'integer',
            'price_omr' => 'decimal:3',
            'compare_at_price_omr' => 'decimal:3',
            'stock' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Book $book) {
            if (empty($book->slug) && !empty($book->title)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)->withPivot('role')->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BookImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
