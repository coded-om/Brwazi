<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Tagline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        $forget = function () {
            Cache::forget('taglines.active.list');
        };
        static::created($forget);
        static::updated($forget);
        static::deleted($forget);
    }
}
