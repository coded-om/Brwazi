<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        // legacy/basic fields
        'user_id',
        'order_no',
        'subtotal',
        'discount',
        'shipping_fee',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'customer_name',
        'customer_phone',
        'customer_city',
        // commerce MVP fields (additive)
        'buyer_id',
        'seller_id',
        'platform_fee',
        'shipping_cost',
        'fulfillment_status',
        'payment_provider',
        'shipping_address',
        'shipping_carrier',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'invoice_number',
        'invoice_pdf_path',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'discount' => 'decimal:3',
        'shipping_fee' => 'decimal:3',
        'total' => 'decimal:3',
        // additive casts
        'platform_fee' => 'decimal:3',
        'shipping_cost' => 'decimal:3',
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationships for commerce MVP
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Payment status constants
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    // Fulfillment status constants
    public const FULFILLMENT_UNFULFILLED = 'unfulfilled';
    public const FULFILLMENT_SHIPPED = 'shipped';
    public const FULFILLMENT_DELIVERED = 'delivered';
    public const FULFILLMENT_COMPLETED = 'completed';
    public const FULFILLMENT_CANCELED = 'canceled';
    public const FULFILLMENT_DISPUTED = 'disputed';

    // Simple scopes
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('fulfillment_status', self::FULFILLMENT_SHIPPED);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('fulfillment_status', self::FULFILLMENT_COMPLETED);
    }

    /**
     * Optional immediate total calculation accessor.
     * If a stored total exists, use it; otherwise compute from parts.
     */
    public function getTotalAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        $subtotal = (float) ($this->attributes['subtotal'] ?? $this->subtotal ?? 0);
        $discount = (float) ($this->attributes['discount'] ?? $this->discount ?? 0);
        // Prefer shipping_cost if present; fallback to legacy shipping_fee
        $shipping = (float) (
            $this->attributes['shipping_cost']
            ?? $this->shipping_cost
            ?? $this->attributes['shipping_fee']
            ?? $this->shipping_fee
            ?? 0
        );
        $platformFee = (float) ($this->attributes['platform_fee'] ?? $this->platform_fee ?? 0);

        $computed = $subtotal - $discount + $shipping + $platformFee;
        return round($computed, 3);
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قيد التأكيد',
            'confirmed' => 'تم التأكيد',
            'preparing' => 'جاري التحضير',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'canceled' => 'أُلغي',
            default => $this->status,
        };
    }
}
