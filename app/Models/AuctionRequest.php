<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'user_id',
        'status',
        'desired_start_price',
        'suggested_start_at',
        'suggested_duration',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'desired_start_price' => 'decimal:2',
        'suggested_start_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
