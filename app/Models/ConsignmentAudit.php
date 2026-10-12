<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentAudit extends Model
{
    use HasFactory;

    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_TRANSFER = 'transfer';

    protected $fillable = [
        'audit_number',
        'consignment_id',
        'customer_id',
        'user_id',
        'audit_date',
        'total_sold_qty',
        'total_sold_amount',
        'total_restock_qty',
        'payment_status',
        'payment_method',
        'order_id',
        'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'total_sold_qty' => 'integer',
        'total_sold_amount' => 'decimal:2',
        'total_restock_qty' => 'integer',
    ];

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentAuditItem::class);
    }

    public function getFormattedTotalSoldAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_sold_amount, 0, ',', '.');
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'bg-success',
            self::PAYMENT_STATUS_UNPAID => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'Lunas Dibayar',
            self::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
            default => ucfirst($this->payment_status),
        };
    }

    public static function generateNumber(): string
    {
        $prefix = 'SRV-' . date('Ym') . '-';
        $latest = static::where('audit_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            $num = (int) substr($latest->audit_number, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
