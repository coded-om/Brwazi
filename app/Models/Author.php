<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'avatar_path',
        'slug',
        'phone',
        'email',
        'notes',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)->withPivot('role')->withTimestamps();
    }
}
