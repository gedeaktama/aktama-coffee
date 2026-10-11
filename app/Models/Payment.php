<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';
    public const METHOD_TRANSFER_BCA = 'transfer_bca';
    public const METHOD_TRANSFER_MANDIRI = 'transfer_mandiri';
    public const METHOD_TRANSFER_BRI = 'transfer_bri';
    public const METHOD_GIRO = 'giro';

    public const METHODS = [
        self::METHOD_CASH => 'Tunai (Cash)',
        self::METHOD_TRANSFER_BCA => 'Transfer Bank BCA',
        self::METHOD_TRANSFER_MANDIRI => 'Transfer Bank Mandiri',
        self::METHOD_TRANSFER_BRI => 'Transfer Bank BRI',
        self::METHOD_GIRO => 'Cek / Bilyet Giro',
    ];

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'order_id',
        'customer_id',
        'user_id',
        'payment_date',
        'payment_method',
        'amount',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

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

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->payment_method] ?? ucfirst($this->payment_method);
    }
}
