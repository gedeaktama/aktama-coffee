<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    public const TYPE_KAFE = 'kafe';
    public const TYPE_TOKO = 'toko';
    public const TYPE_RESELLER = 'reseller';
    public const TYPE_RETAIL = 'retail';
    public const TYPE_KONSINYASI = 'konsinyasi';

    public const TYPES = [
        self::TYPE_KAFE => 'Kafe / Kedai Kopi',
        self::TYPE_TOKO => 'Toko Kelontong',
        self::TYPE_RESELLER => 'Reseller Kopi',
        self::TYPE_RETAIL => 'Konsumen Retail',
        self::TYPE_KONSINYASI => 'Mitra Titip Jual (Konsinyasi)',
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }

    public function consignmentAudits(): HasMany
    {
        return $this->hasMany(ConsignmentAudit::class);
    }

    /**
     * Hitung total piutang berjalan yang belum lunas
     */
    public function getCurrentReceivables(): float
    {
        return (float) $this->orders()
            ->where('payment_type', Order::PAYMENT_CREDIT)
            ->whereIn('payment_status', [Order::PAYMENT_STATUS_UNPAID, Order::PAYMENT_STATUS_PARTIAL])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum(DB::raw('total_amount - paid_amount'));
    }

    /**
     * Sisa plafon kredit yang masih dapat digunakan
     */
    public function getAvailableCredit(): float
    {
        return max(0, (float)$this->credit_limit - $this->getCurrentReceivables());
    }

    public function getFormattedAvailableCreditAttribute(): string
    {
        return 'Rp ' . number_format($this->getAvailableCredit(), 0, ',', '.');
    }

    public function getFormattedReceivablesAttribute(): string
    {
        return 'Rp ' . number_format($this->getCurrentReceivables(), 0, ',', '.');
    }
}
