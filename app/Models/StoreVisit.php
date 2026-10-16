<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreVisit extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_IN_PROGRESS => 'Sedang Berlangsung',
        self::STATUS_COMPLETED => 'Selesai Kunjungan',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public const PURPOSE_AUDIT_DELIVERY = 'audit_delivery';
    public const PURPOSE_DELIVERY = 'delivery';
    public const PURPOSE_AUDIT = 'audit';
    public const PURPOSE_RESTOCK = 'restock';
    public const PURPOSE_COLLECTION = 'collection';
    public const PURPOSE_SURVEY = 'survey';

    public const PURPOSES = [
        self::PURPOSE_AUDIT_DELIVERY => 'Pengiriman & Audit Stok',
        self::PURPOSE_DELIVERY => 'Hanya Pengiriman Barang',
        self::PURPOSE_AUDIT => 'Audit Sisa Stok Rak',
        self::PURPOSE_RESTOCK => 'Restock Tambahan Barang',
        self::PURPOSE_COLLECTION => 'Penagihan Pembayaran',
        self::PURPOSE_SURVEY => 'Kunjungan Rutin / Survei',
    ];

    protected $fillable = [
        'visit_number',
        'user_id',
        'customer_id',
        'visit_date',
        'check_in_at',
        'check_out_at',
        'check_in_lat',
        'check_in_lng',
        'distance_meters',
        'is_geofence_valid',
        'visit_purpose',
        'status',
        'photo_proof',
        'recipient_name',
        'recipient_phone',
        'notes',
        'summary_sold_qty',
        'summary_sold_amount',
        'summary_restock_qty',
        'consignment_audit_id',
        'delivery_order_id',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'distance_meters' => 'integer',
        'is_geofence_valid' => 'boolean',
        'summary_sold_qty' => 'integer',
        'summary_sold_amount' => 'decimal:2',
        'summary_restock_qty' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function consignmentAudit(): BelongsTo
    {
        return $this->belongsTo(ConsignmentAudit::class);
    }

    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPurposeLabelAttribute(): string
    {
        return self::PURPOSES[$this->visit_purpose] ?? ucfirst($this->visit_purpose);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_IN_PROGRESS => 'bg-warning text-dark',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getFormattedSoldAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->summary_sold_amount, 0, ',', '.');
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->check_out_at || !$this->check_in_at) {
            return null;
        }

        return $this->check_in_at->diffInMinutes($this->check_out_at);
    }
}
