<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Services\FinanceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Daftar Faktur Penjualan & Status Pembayaran
     */
    public function invoices(Request $request)
    {
        $query = Invoice::with(['order', 'customer', 'user'])->latest();

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
                      ->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('business_name', 'like', "%{$search}%")
                         ->orWhere('pic_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();
        $customers = Customer::where('is_active', true)->get();

        $metrics = [
            'total_invoiced' => Invoice::sum('total_amount'),
            'total_paid' => Invoice::sum('paid_amount'),
            'total_receivables' => Invoice::whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])->sum('remaining_amount'),
            'overdue_count' => Invoice::whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];

        return view('finance.invoices.index', compact('invoices', 'customers', 'metrics'));
    }

    /**
     * Tampilan Detail Faktur & Form Pembayaran
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['order.items.variant.product', 'customer', 'user', 'payments.user']);
        return view('finance.invoices.show', compact('invoice'));
    }

    /**
     * Terbitkan Faktur dari Order yang Dikonfirmasi
     */
    public function generateInvoice(Order $order)
    {
        if ($order->invoice) {
            return redirect()->route('finance.invoices.show', $order->invoice);
        }

        try {
            $invoice = $this->financeService->createInvoice($order);
            return redirect()->route('finance.invoices.show', $invoice)->with('success', "Faktur Penjualan #{$invoice->invoice_number} berhasil diterbitkan.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menerbitkan faktur: ' . $e->getMessage());
        }
    }

    /**
     * Rekam Pembayaran Tagihan Faktur
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $maxAmount = (float)$invoice->remaining_amount;

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:' . $maxAmount],
            'payment_method' => ['required', 'string'],
            'payment_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.max' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan faktur (Rp ' . number_format($maxAmount, 0, ',', '.') . ').',
        ]);

        try {
            $payment = $this->financeService->recordPayment(
                invoice: $invoice,
                amount: (float)$validated['amount'],
                paymentMethod: $validated['payment_method'],
                referenceNumber: $validated['reference_number'] ?? null,
                paymentDate: $validated['payment_date'],
                notes: $validated['notes'] ?? null,
                userId: Auth::id()
            );

            return redirect()->route('finance.invoices.show', $invoice)->with('success', "Pembayaran #{$payment->payment_number} sebesar {$payment->formatted_amount} berhasil dicatat dan sisa tagihan diperbarui.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Monitoring Piutang Pelanggan B2B (Accounts Receivable Aging)
     */
    public function receivables(Request $request)
    {
        $query = Customer::whereIn('customer_type', [Customer::TYPE_KAFE, Customer::TYPE_TOKO, Customer::TYPE_RESELLER])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $customers = $query->get()->map(function ($c) {
            $receivables = $c->getCurrentReceivables();
            $availableCredit = $c->getAvailableCredit();
            $overdueInvoices = $c->invoices()
                ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
                ->where('due_date', '<', now()->toDateString())
                ->count();

            return [
                'customer' => $c,
                'credit_limit' => (float)$c->credit_limit,
                'receivables' => $receivables,
                'available_credit' => $availableCredit,
                'overdue_count' => $overdueInvoices,
            ];
        });

        // Filter tab
        if ($request->filled('filter')) {
            if ($request->filter === 'has_debt') {
                $customers = $customers->filter(fn($row) => $row['receivables'] > 0);
            } elseif ($request->filter === 'overdue') {
                $customers = $customers->filter(fn($row) => $row['overdue_count'] > 0);
            }
        }

        $metrics = [
            'total_credit_granted' => Customer::whereIn('customer_type', [Customer::TYPE_KAFE, Customer::TYPE_TOKO, Customer::TYPE_RESELLER])->sum('credit_limit'),
            'total_active_receivables' => $customers->sum('receivables'),
            'total_overdue_mitra' => $customers->where('overdue_count', '>', 0)->count(),
        ];

        return view('finance.receivables.index', compact('customers', 'metrics'));
    }

    /**
     * Kartu Piutang Individual Pelanggan (Customer Ledger)
     */
    public function customerLedger(Customer $customer)
    {
        $invoices = $customer->invoices()->with('order')->latest('invoice_date')->get();
        $payments = $customer->payments()->with('invoice')->latest('payment_date')->get();

        $receivables = $customer->getCurrentReceivables();
        $availableCredit = $customer->getAvailableCredit();

        return view('finance.receivables.ledger', compact('customer', 'invoices', 'payments', 'receivables', 'availableCredit'));
    }

    /**
     * Cetak Kuitansi Pembayaran
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['invoice.order', 'customer', 'user']);
        return view('finance.payments.receipt', compact('payment'));
    }
}
