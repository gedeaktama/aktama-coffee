<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrder extends Model
{
    use HasFactory;

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PROCESSING => 'Disiapkan di Gudang',
        self::STATUS_SHIPPED => 'Dalam Pengiriman (Sopir/Kurir)',
        self::STATUS_DELIVERED => 'Selesai Diterima di Tujuan',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public const COURIER_INTERNAL = 'internal';
    public const COURIER_EXPEDITION = 'expedition';

    public const COURIER_TYPES = [
        self::COURIER_INTERNAL => 'Armada Sendiri (Internal Driver)',
        self::COURIER_EXPEDITION => 'Ekspedisi / Logistik Pihak Ke-3',
    ];

    protected $fillable = [
        'delivery_number',
        'order_id',
        'customer_id',
        'user_id',
        'delivery_date',
        'courier_type',
        'courier_name',
        'driver_name',
        'driver_phone',
        'vehicle_plate_number',
        'tracking_number',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'status',
        'received_by',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESSING => 'bg-warning text-dark',
            self::STATUS_SHIPPED => 'bg-info text-dark',
            self::STATUS_DELIVERED => 'bg-success text-white',
            self::STATUS_CANCELLED => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipped(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getTotalUnitsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
