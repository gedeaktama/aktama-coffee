<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const TYPE_B2B = 'b2b';
    public const TYPE_RETAIL = 'retail';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Menunggu Konfirmasi',
        self::STATUS_CONFIRMED => 'Dikonfirmasi (Stok Dipotong)',
        self::STATUS_PROCESSING => 'Diproses di Gudang',
        self::STATUS_SHIPPED => 'Sedang Dikirim',
        self::STATUS_DELIVERED => 'Selesai Diterima',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_TRANSFER = 'transfer';
    public const PAYMENT_CREDIT = 'credit';

    public const PAYMENT_TYPES = [
        self::PAYMENT_CASH => 'Tunai (Cash)',
        self::PAYMENT_TRANSFER => 'Transfer Bank',
        self::PAYMENT_CREDIT => 'Tempo / Kredit (TOP)',
    ];

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PARTIAL = 'partial';
    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
        self::PAYMENT_STATUS_PARTIAL => 'Dibayar Sebagian',
        self::PAYMENT_STATUS_PAID => 'Lunas',
    ];

    protected $fillable = [
        'order_number',
        'order_type',
        'customer_id',
        'user_id',
        'sales_user_id',
        'order_date',
        'payment_type',
        'due_date',
        'status',
        'payment_status',
        'subtotal',
        'discount',
        'shipping_cost',
        'total_amount',
        'paid_amount',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'stock_reduced',
        'confirmed_at',
        'confirmed_by_user_id',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date',
        'confirmed_at' => 'datetime',
        'stock_reduced' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isB2B(): bool
    {
        return $this->order_type === self::TYPE_B2B;
    }

    public function isRetail(): bool
    {
        return $this->order_type === self::TYPE_RETAIL;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_DELIVERED]);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_CONFIRMED => 'bg-primary text-white',
            self::STATUS_PROCESSING => 'bg-info text-dark',
            self::STATUS_SHIPPED => 'bg-secondary text-white',
            self::STATUS_DELIVERED => 'bg-success text-white',
            self::STATUS_CANCELLED => 'bg-danger text-white',
            default => 'bg-light text-dark',
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return self::PAYMENT_TYPES[$this->payment_type] ?? ucfirst($this->payment_type);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'bg-success',
            self::PAYMENT_STATUS_PARTIAL => 'bg-warning text-dark',
            self::PAYMENT_STATUS_UNPAID => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function deliveryOrder(): HasOne
    {
        return $this->hasOne(DeliveryOrder::class)->latestOfMany();
    }
}
