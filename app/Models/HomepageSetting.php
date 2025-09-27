<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_text',
        'hero_bg_image',
        'hero_logo',
        'featured_artist_title',
        'featured_artist_description',
        'featured_artist_image',
        'art_slides',
        'auctions_title',
        'auctions_subtitle',
        'events',
        'upload_max_mb',
    ];

    protected function casts(): array
    {
        return [
            'art_slides' => 'array',
            'events' => 'array',
        ];
    }
}
