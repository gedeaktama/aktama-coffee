<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Terbitkan Faktur Penjualan resmi untuk Pesanan Penjualan
     */
    public function createInvoice(
        Order $order,
        ?string $invoiceDate = null,
        ?string $notes = null,
        ?int $userId = null
    ): Invoice {
        if ($order->invoice) {
            return $order->invoice;
        }

        return DB::transaction(function () use ($order, $invoiceDate, $notes, $userId) {
            // Generate Invoice Number: INV-YYYYMM-0001
            $prefix = 'INV-' . date('Ym') . '-';
            $latest = Invoice::where('invoice_number', 'like', "{$prefix}%")->latest('id')->first();
            $nextNumber = 1;
            if ($latest) {
                $nextNumber = (int)substr($latest->invoice_number, -4) + 1;
            }
            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $invDate = $invoiceDate ? Carbon::parse($invoiceDate) : Carbon::parse($order->order_date);
            if ($order->due_date) {
                $dueDate = Carbon::parse($order->due_date);
            } elseif ($order->customer && $order->customer->payment_terms_days > 0) {
                $dueDate = $invDate->copy()->addDays($order->customer->payment_terms_days);
            } else {
                $dueDate = $invDate;
            }

            $totalAmount = (float)$order->total_amount;
            $paidAmount = (float)$order->paid_amount;
            $remainingAmount = max(0, $totalAmount - $paidAmount);

            $status = Invoice::STATUS_UNPAID;
            if ($remainingAmount <= 0) {
                $status = Invoice::STATUS_PAID;
            } elseif ($paidAmount > 0) {
                $status = Invoice::STATUS_PARTIAL;
            }

            return Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'user_id' => $userId ?? Auth::id(),
                'invoice_date' => $invDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $status,
                'notes' => $notes ?? "Faktur untuk pesanan #{$order->order_number}",
            ]);
        });
    }

    /**
     * Catat dan verifikasi pembayaran tagihan (penuh atau bertahap)
     */
    public function recordPayment(
        Invoice $invoice,
        float $amount,
        string $paymentMethod,
        ?string $referenceNumber = null,
        ?string $paymentDate = null,
        ?string $notes = null,
        ?int $userId = null
    ): Payment {
        if ($amount <= 0) {
            throw new Exception("Jumlah pembayaran harus lebih dari Rp 0.");
        }

        if ($amount > (float)$invoice->remaining_amount) {
            $formattedRem = number_format($invoice->remaining_amount, 0, ',', '.');
            $formattedReq = number_format($amount, 0, ',', '.');
            throw new Exception("Jumlah pembayaran (Rp {$formattedReq}) melebihi sisa tagihan faktur (Rp {$formattedRem}).");
        }

        return DB::transaction(function () use ($invoice, $amount, $paymentMethod, $referenceNumber, $paymentDate, $notes, $userId) {
            // Generate Payment Number: PAY-YYYYMM-0001
            $prefix = 'PAY-' . date('Ym') . '-';
            $latest = Payment::where('payment_number', 'like', "{$prefix}%")->latest('id')->first();
            $nextNumber = 1;
            if ($latest) {
                $nextNumber = (int)substr($latest->payment_number, -4) + 1;
            }
            $paymentNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $payDate = $paymentDate ? Carbon::parse($paymentDate)->toDateString() : now()->toDateString();

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'customer_id' => $invoice->customer_id,
                'user_id' => $userId ?? Auth::id(),
                'payment_date' => $payDate,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'reference_number' => $referenceNumber,
                'notes' => $notes ?? "Pembayaran tagihan faktur #{$invoice->invoice_number}",
            ]);

            // Update Invoice balances & status
            $newPaid = (float)$invoice->paid_amount + $amount;
            $newRemaining = max(0, (float)$invoice->total_amount - $newPaid);
            $newStatus = ($newRemaining <= 0) ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIAL;

            $invoice->update([
                'paid_amount' => $newPaid,
                'remaining_amount' => $newRemaining,
                'status' => $newStatus,
            ]);

            // Synchronize Order balances & payment status
            $order = $invoice->order;
            if ($order) {
                $order->update([
                    'paid_amount' => $newPaid,
                    'payment_status' => $newStatus,
                ]);
            }

            return $payment;
        });
    }
}
