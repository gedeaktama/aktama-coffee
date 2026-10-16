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

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->customer_type] ?? ucfirst($this->customer_type);
    }

    public const STORE_CHAIN_ALFAMART = 'alfamart';
    public const STORE_CHAIN_INDOMARET = 'indomaret';
    public const STORE_CHAIN_KELONTONG = 'kelontong';
    public const STORE_CHAIN_KAFE = 'kafe';
    public const STORE_CHAIN_LAINNYA = 'lainnya';

    public const STORE_CHAINS = [
        self::STORE_CHAIN_ALFAMART => 'Alfamart',
        self::STORE_CHAIN_INDOMARET => 'Indomaret',
        self::STORE_CHAIN_KELONTONG => 'Toko Kelontong / Grosir',
        self::STORE_CHAIN_KAFE => 'Kafe / Kedai Kopi',
        self::STORE_CHAIN_LAINNYA => 'Lainnya / Mitra Bebas',
    ];

    protected $fillable = [
        'code',
        'customer_type',
        'store_chain',
        'business_name',
        'pic_name',
        'phone',
        'email',
        'address',
        'shipping_address',
        'latitude',
        'longitude',
        'qr_token',
        'sales_user_id',
        'user_id',
        'credit_limit',
        'payment_terms_days',
        'min_stock_alert',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'min_stock_alert' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
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

    public function visits(): HasMany
    {
        return $this->hasMany(StoreVisit::class);
    }

    /**
     * Pastikan toko memiliki QR token unik
     */
    public function ensureQrToken(): string
    {
        if (empty($this->qr_token)) {
            $this->qr_token = 'QR-' . strtoupper($this->code ?? 'STORE') . '-' . bin2hex(random_bytes(6));
            $this->save();
        }

        return $this->qr_token;
    }

    /**
     * Hitung total sisa stok konsinyasi aktif di toko ini
     */
    public function getActiveConsignmentStock(): int
    {
        return (int) ConsignmentItem::whereHas('consignment', function ($q) {
            $q->where('customer_id', $this->id)
              ->where('status', Consignment::STATUS_ACTIVE);
        })->sum('current_quantity');
    }

    /**
     * Cek apakah toko memiliki faktur yang sudah melewati jatuh tempo (menunggak)
     */
    public function getIsOverdueAttribute(): bool
    {
        return Invoice::where('customer_id', $this->id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
            ->where('due_date', '<', now()->toDateString())
            ->exists();
    }

    /**
     * Hitung total nilai piutang yang sudah jatuh tempo (menunggak)
     */
    public function getOverdueAmountAttribute(): float
    {
        return (float) Invoice::where('customer_id', $this->id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
            ->where('due_date', '<', now()->toDateString())
            ->sum('remaining_amount');
    }

    /**
     * Hitung berapa hari keterlambatan pembayaran terparah
     */
    public function getOverdueDaysAttribute(): int
    {
        $oldestOverdue = Invoice::where('customer_id', $this->id)
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
            ->where('due_date', '<', now()->toDateString())
            ->orderBy('due_date', 'asc')
            ->first();

        if (!$oldestOverdue) {
            return 0;
        }

        return (int) now()->diffInDays($oldestOverdue->due_date);
    }

    /**
     * URL navigasi Google Maps langsung
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
        }

        if (!empty($this->shipping_address ?? $this->address)) {
            $query = urlencode($this->business_name . ' ' . ($this->shipping_address ?? $this->address));
            return "https://www.google.com/maps/search/?api=1&query={$query}";
        }

        return null;
    }
}

