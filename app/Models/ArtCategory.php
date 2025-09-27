<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArtCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }

    public static function listOptions(bool $onlyActive = true): array
    {
        $q = static::query();
        if ($onlyActive)
            $q->active();
        return $q->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->toArray();
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = static::uniqueSlugFrom($model->name);
            }
        });
    }

    public static function uniqueSlugFrom(string $name): string
    {
        $base = Str::slug($name, '-');
        $slug = (string) $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
