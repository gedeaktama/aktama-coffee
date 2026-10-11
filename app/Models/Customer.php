<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    public const TYPE_KAFE = 'kafe';
    public const TYPE_TOKO = 'toko';
    public const TYPE_RESELLER = 'reseller';
    public const TYPE_RETAIL = 'retail';

    public const TYPES = [
        self::TYPE_KAFE => 'Kafe / Kedai Kopi',
        self::TYPE_TOKO => 'Toko Kelontong',
        self::TYPE_RESELLER => 'Reseller Kopi',
        self::TYPE_RETAIL => 'Konsumen Retail',
    ];

    protected $fillable = [
        'code',
        'customer_type',
        'business_name',
        'pic_name',
        'phone',
        'email',
        'address',
        'shipping_address',
        'sales_user_id',
        'user_id',
        'credit_limit',
        'payment_terms_days',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function salesUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isB2B(): bool
    {
        return in_array($this->customer_type, [self::TYPE_KAFE, self::TYPE_TOKO, self::TYPE_RESELLER], true);
    }

    public function hasCreditFacility(): bool
    {
        return $this->credit_limit > 0 && $this->payment_terms_days > 0;
    }

    public function getFormattedCreditLimitAttribute(): string
    {
        return 'Rp ' . number_format($this->credit_limit, 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->customer_type] ?? ucfirst($this->customer_type);
    }
}
