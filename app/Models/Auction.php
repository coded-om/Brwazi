<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BidderDeposit;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'status',
        'starts_at',
        'ends_at',
        'start_price',
        'bid_increment',
        'reserve_price',
        'buy_now_price',
        'highest_bid_id',
        'highest_bid_amount',
        'bids_count',
        'approved_by_admin_id',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'start_price' => 'decimal:2',
        'bid_increment' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'buy_now_price' => 'decimal:2',
        'highest_bid_amount' => 'decimal:2',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function highestBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'highest_bid_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by_admin_id');
    }

    // --- Convenience helpers for homepage/UI logic ---
    public function isLive(): bool
    {
        $now = now();
        return $this->starts_at && $this->ends_at && $this->starts_at <= $now && $this->ends_at > $now;
    }

    public function isSoon(): bool
    {
        $now = now();
        return $this->starts_at && $this->starts_at > $now;
    }

    public function isEnded(): bool
    {
        $now = now();
        return $this->ends_at && $this->ends_at <= $now;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->artwork?->primary_image_url;
    }

    // Reserve price helpers
    public function hasReserve(): bool
    {
        return (float) $this->reserve_price > 0;
    }

    public function reserveMet(): bool
    {
        if (!$this->hasReserve())
            return true; // treat no reserve as met
        return (float) $this->highest_bid_amount >= (float) $this->reserve_price;
    }

    public function endedUnsoldByReserve(): bool
    {
        return $this->status === 'ended' && $this->hasReserve() && !$this->reserveMet();
    }

    /* ================= Insurance / Stats Helpers ================= */
    // نسبة التأمين المحسوبة (قد تعتمد لاحقاً على فئات مختلفة)
    public function insuranceRate(): float
    {
        return (float) config('auction.insurance_rate', 0.05);
    }

    // قيمة التأمين المطلوبة لهذا المزاد (مقربة لجزئين عشريين وضمن حد أدنى)
    public function insuranceFee(): float
    {
        $base = (float) $this->start_price * $this->insuranceRate();
        $min = (float) config('auction.minimum_insurance_fee', 5.0);
        $fee = max($base, $min);
        return round($fee, 2);
    }

    // نطاق تأمين (مصفوفة مبسطة للاستخدام في الواجهات)
    public function getInsuranceSummaryAttribute(): array
    {
        return [
            'auction_id' => $this->id,
            'start_price' => (float) $this->start_price,
            'rate' => $this->insuranceRate(),
            'fee' => $this->insuranceFee(),
            'has_reserve' => $this->hasReserve(),
            'reserve_met' => $this->reserveMet(),
        ];
    }

    /* ================= Query Scopes ================= */
    public function scopeLive($q)
    {
        return $q->where('status', 'live');
    }

    public function scopeActiveForInsurance($q)
    {
        // المزادات الجارية أو التي ستبدأ قريباً: يمكن للمستخدم حجز تأمين لها
        return $q->whereIn('status', ['live', 'soon']);
    }

    /* ================= Aggregated Stats (static helpers) ================= */
    public static function insuranceTopStats(): array
    {
        // المشتركين للإيداع = عدد المستخدمين الذين لديهم سجلات BidderDeposit بحالة held خاصة بالمزادات
        $depositBase = BidderDeposit::query()
            ->where('status', 'held')
            ->where(function ($q) {
                $q->where('reference', 'like', 'auction:%')
                  ->orWhere('reference', 'general');
            });
        $uniqueUsers = (clone $depositBase)->distinct('user_id')->count('user_id');

        // التأمين المودع = مجموع المبالغ المحجوزة
        $totalHeld = (float) (clone $depositBase)->sum('amount');

        // مزايدات نشطة = live auctions count
        $activeAuctions = static::where('status', 'live')->count();

        // إجمالي التأمين المطلوب (تقديري) = مجموع insuranceFee لكل مزاد نشط/قريب
        $requiredSum = (float) static::whereIn('status', ['live', 'soon'])->get()->sum(fn($a) => $a->insuranceFee());

        return [
            'participants' => $uniqueUsers,
            'required_total' => round($requiredSum, 2),
            'held_total' => round($totalHeld, 2),
            'active_auctions' => $activeAuctions,
        ];
    }
}
