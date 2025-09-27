<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'artwork_id',
        'title',
        'price',
        'quantity',
        'image_url',
        'artist_name',
        // snapshot fields (additive, optional)
        'title_snapshot',
        'image_snapshot',
        'price_snapshot',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
