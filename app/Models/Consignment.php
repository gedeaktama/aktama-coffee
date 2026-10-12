<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'Titipan Aktif',
        self::STATUS_SETTLED => 'Selesai / Lunas',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = [
        'consignment_number',
        'customer_id',
        'sales_user_id',
        'created_by_user_id',
        'delivery_date',
        'status',
        'total_items_count',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_items_count' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ConsignmentAudit::class)->latest('audit_date');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-primary',
            self::STATUS_SETTLED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getTotalActiveStockAttribute(): int
    {
        return (int) $this->items()->sum('current_quantity');
    }

    public function getTotalSoldQuantityAttribute(): int
    {
        return (int) $this->items()->sum('sold_quantity');
    }

    public function getTotalStockValueAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->current_quantity * $item->selling_price;
        });
    }

    public function getFormattedTotalStockValueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_stock_value, 0, ',', '.');
    }

    public static function generateNumber(): string
    {
        $prefix = 'KS-' . date('Ym') . '-';
        $latest = static::where('consignment_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            $num = (int) substr($latest->consignment_number, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
