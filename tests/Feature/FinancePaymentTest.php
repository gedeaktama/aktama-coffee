<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancePaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $finance;
    protected User $sales;
    protected User $customerUser;
    protected Customer $b2bCustomer;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->finance = User::factory()->create(['role' => User::ROLE_FINANCE]);
        $this->sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $category = Category::first() ?? Category::create([
            'name' => 'Kategori Finance Test',
            'slug' => 'kategori-finance-test-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Arabika Toraja Finance',
            'brand' => 'Kopi Distro',
            'coffee_type' => 'Arabika',
            'form' => 'both',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'FIN-SKU-' . uniqid(),
            'variant_name' => 'Kemasan 500g Biji',
            'package_weight_grams' => 500,
            'grind_form' => 'biji',
            'cost_price' => 60000,
            'selling_price' => 100000,
            'current_stock' => 100,
            'minimum_stock' => 10,
            'is_active' => true,
        ]);

        $this->b2bCustomer = Customer::create([
            'code' => 'CUST-FIN-' . strtoupper(substr(uniqid(), 0, 4)),
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kafe Senja Finansial',
            'pic_name' => 'Budi Fin',
            'phone' => '081299887766',
            'address' => 'Jl. Keuangan No. 5, Jakarta Selatan',
            'credit_limit' => 2000000, // Rp 2.000.000
            'payment_terms_days' => 14, // 14 hari tempo
            'is_active' => true,
        ]);
    }

    /**
     * Helper untuk membuat confirmed order
     */
    protected function createConfirmedOrder(int $qty = 10, string $paymentType = Order::PAYMENT_CREDIT): Order
    {
        $total = $qty * 100000;

        $order = Order::create([
            'order_number' => 'SO-FIN-' . rand(10000, 99999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => $paymentType,
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => $total,
            'total_amount' => $total,
            'paid_amount' => 0,
            'stock_reduced' => true,
            'confirmed_at' => now(),
            'confirmed_by' => $this->admin->id,
        ]);

        $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'quantity' => $qty,
            'unit_price' => 100000,
            'cost_price' => 60000,
            'subtotal' => $total,
        ]);

        return $order;
    }

    public function test_authorized_user_can_generate_invoice_from_confirmed_order()
    {
        $order = $this->createConfirmedOrder(10); // 10 x 100.000 = Rp 1.000.000

        $response = $this->actingAs($this->finance)
            ->post(route('finance.invoices.generate', $order));

        $order->refresh();
        $invoice = $order->invoice;

        $this->assertNotNull($invoice);
        $response->assertRedirect(route('finance.invoices.show', $invoice));

        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
        $this->assertEquals(1000000, (float)$invoice->total_amount);
        $this->assertEquals(1000000, (float)$invoice->remaining_amount);
        $this->assertEquals(0, (float)$invoice->paid_amount);
        $this->assertEquals(Invoice::STATUS_UNPAID, $invoice->status);
        $this->assertEquals(now()->addDays(14)->toDateString(), $invoice->due_date->toDateString());
    }

    public function test_record_partial_payment_and_update_balances()
    {
        $order = $this->createConfirmedOrder(10); // Rp 1.000.000
        $this->actingAs($this->finance)->post(route('finance.invoices.generate', $order));
        $invoice = $order->fresh()->invoice;

        // Bayar cicilan tahap 1: Rp 400.000
        $response = $this->actingAs($this->finance)
            ->post(route('finance.payments.record', $invoice), [
                'amount' => 400000,
                'payment_method' => Payment::METHOD_TRANSFER_BCA,
                'payment_date' => now()->toDateString(),
                'reference_number' => 'TRF-BCA-889911',
                'notes' => 'Pembayaran tahap 1',
            ]);

        $response->assertRedirect(route('finance.invoices.show', $invoice));
        $response->assertSessionHas('success');

        $invoice->refresh();
        $order->refresh();

        $this->assertEquals(Invoice::STATUS_PARTIAL, $invoice->status);
        $this->assertEquals(400000, (float)$invoice->paid_amount);
        $this->assertEquals(600000, (float)$invoice->remaining_amount);
        $this->assertEquals(Order::PAYMENT_STATUS_PARTIAL, $order->payment_status);
        $this->assertEquals(400000, (float)$order->paid_amount);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'customer_id' => $this->b2bCustomer->id,
            'amount' => 400000,
            'payment_method' => Payment::METHOD_TRANSFER_BCA,
            'reference_number' => 'TRF-BCA-889911',
        ]);
    }

    public function test_record_full_payment_completes_invoice_and_order()
    {
        $order = $this->createConfirmedOrder(5); // Rp 500.000
        $this->actingAs($this->finance)->post(route('finance.invoices.generate', $order));
        $invoice = $order->fresh()->invoice;

        // Bayar lunas penuh: Rp 500.000
        $response = $this->actingAs($this->finance)
            ->post(route('finance.payments.record', $invoice), [
                'amount' => 500000,
                'payment_method' => Payment::METHOD_CASH,
                'payment_date' => now()->toDateString(),
                'notes' => 'Pelunasan kontan kasir',
            ]);

        $response->assertRedirect(route('finance.invoices.show', $invoice));

        $invoice->refresh();
        $order->refresh();

        $this->assertEquals(Invoice::STATUS_PAID, $invoice->status);
        $this->assertEquals(500000, (float)$invoice->paid_amount);
        $this->assertEquals(0, (float)$invoice->remaining_amount);
        $this->assertEquals(Order::PAYMENT_STATUS_PAID, $order->payment_status);
        $this->assertEquals(500000, (float)$order->paid_amount);
    }

    public function test_payment_exceeding_remaining_amount_is_rejected()
    {
        $order = $this->createConfirmedOrder(5); // Rp 500.000
        $this->actingAs($this->finance)->post(route('finance.invoices.generate', $order));
        $invoice = $order->fresh()->invoice;

        // Coba bayar lebih dari sisa: Rp 600.000
        $response = $this->actingAs($this->finance)
            ->post(route('finance.payments.record', $invoice), [
                'amount' => 600000,
                'payment_method' => Payment::METHOD_CASH,
                'payment_date' => now()->toDateString(),
            ]);

        $response->assertSessionHasErrors('amount');

        $invoice->refresh();
        $this->assertEquals(500000, (float)$invoice->remaining_amount);
        $this->assertEquals(Invoice::STATUS_UNPAID, $invoice->status);
    }

    public function test_b2b_payment_automatically_restores_customer_available_credit()
    {
        // Plafon Rp 2.000.000. Buat order tempo Rp 1.500.000 (15 x 100.000)
        $order = $this->createConfirmedOrder(15);
        $this->b2bCustomer->refresh();

        // Piutang = Rp 1.500.000, Sisa Plafon = Rp 500.000
        $this->assertEquals(1500000, (float)$this->b2bCustomer->getCurrentReceivables());
        $this->assertEquals(500000, (float)$this->b2bCustomer->getAvailableCredit());

        // Terbitkan invoice & bayar Rp 1.000.000
        $this->actingAs($this->finance)->post(route('finance.invoices.generate', $order));
        $invoice = $order->fresh()->invoice;

        $this->actingAs($this->finance)->post(route('finance.payments.record', $invoice), [
            'amount' => 1000000,
            'payment_method' => Payment::METHOD_TRANSFER_BCA,
            'payment_date' => now()->toDateString(),
        ]);

        $this->b2bCustomer->refresh();

        // Piutang berkurang menjadi Rp 500.000
        $this->assertEquals(500000, (float)$this->b2bCustomer->getCurrentReceivables());
        // Sisa plafon otomatis pulih bertambah menjadi Rp 1.500.000
        $this->assertEquals(1500000, (float)$this->b2bCustomer->getAvailableCredit());
    }

    public function test_finance_receivables_monitoring_and_customer_ledger_screens()
    {
        $order = $this->createConfirmedOrder(8);
        $this->actingAs($this->finance)->post(route('finance.invoices.generate', $order));

        // 1. Invoices Index
        $responseInvoices = $this->actingAs($this->finance)->get(route('finance.invoices.index'));
        $responseInvoices->assertStatus(200);
        $responseInvoices->assertSee('Faktur Penjualan');

        // 2. Receivables Monitoring
        $responseReceivables = $this->actingAs($this->finance)->get(route('finance.receivables.index'));
        $responseReceivables->assertStatus(200);
        $responseReceivables->assertSee('Kafe Senja Finansial');

        // 3. Customer Ledger
        $responseLedger = $this->actingAs($this->finance)->get(route('finance.receivables.ledger', $this->b2bCustomer));
        $responseLedger->assertStatus(200);
        $responseLedger->assertSee('Kartu Piutang');
        $responseLedger->assertSee('Kafe Senja Finansial');
    }

    public function test_customer_role_cannot_access_finance_modules()
    {
        $response = $this->actingAs($this->customerUser)->get(route('finance.invoices.index'));
        $response->assertStatus(403);

        $responseReceivables = $this->actingAs($this->customerUser)->get(route('finance.receivables.index'));
        $responseReceivables->assertStatus(403);
    }
}
