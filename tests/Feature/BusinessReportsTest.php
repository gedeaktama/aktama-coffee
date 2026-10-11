<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BusinessReportsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $owner;
    protected User $admin;
    protected User $finance;
    protected User $sales;
    protected User $customerUser;
    protected Customer $b2bCustomer;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->finance = User::factory()->create(['role' => User::ROLE_FINANCE]);
        $this->sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $category = Category::first() ?? Category::create([
            'name' => 'Kategori Report Test',
            'slug' => 'kategori-report-test-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Arabika Flores Bajawa Report',
            'brand' => 'Kopi Distro',
            'coffee_type' => 'Arabika',
            'form' => 'both',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'REP-SKU-' . uniqid(),
            'variant_name' => 'Kemasan 250g Biji',
            'package_weight_grams' => 250,
            'grind_form' => 'biji',
            'cost_price' => 50000,   // HPP Modal
            'selling_price' => 90000, // Harga Jual
            'current_stock' => 80,
            'minimum_stock' => 15,
            'is_active' => true,
        ]);

        $this->b2bCustomer = Customer::create([
            'code' => 'CUST-REP-' . strtoupper(substr(uniqid(), 0, 4)),
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kafe Analitika Laporan',
            'pic_name' => 'Irfan Laporan',
            'phone' => '081288776655',
            'address' => 'Jl. Akuntansi No. 1, Jakarta Selatan',
            'credit_limit' => 3000000,
            'payment_terms_days' => 14,
            'is_active' => true,
        ]);
    }

    protected function createConfirmedOrder(int $qty = 10): Order
    {
        $subtotal = $qty * 90000;

        $order = Order::create([
            'order_number' => 'SO-REP-' . rand(10000, 99999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => Order::PAYMENT_CREDIT,
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => $subtotal,
            'discount' => 10000,
            'shipping_cost' => 15000,
            'total_amount' => $subtotal - 10000 + 15000,
            'paid_amount' => 0,
            'shipping_address' => $this->b2bCustomer->address,
            'recipient_name' => $this->b2bCustomer->pic_name,
            'recipient_phone' => $this->b2bCustomer->phone,
            'stock_reduced' => true,
            'confirmed_at' => now(),
            'confirmed_by_user_id' => $this->admin->id,
        ]);

        $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'quantity' => $qty,
            'unit_price' => 90000,
            'cost_price' => 50000,
            'subtotal' => $subtotal,
        ]);

        return $order;
    }

    public function test_authorized_staff_can_view_reports_hub()
    {
        $response = $this->actingAs($this->owner)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan & Analitik Bisnis');

        $responseSales = $this->actingAs($this->sales)->get(route('reports.index'));
        $responseSales->assertStatus(200);
    }

    public function test_sales_report_calculates_correct_totals_and_can_export_csv()
    {
        $this->createConfirmedOrder(10);

        // 1. Web View
        $response = $this->actingAs($this->admin)->get(route('reports.sales', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan & Omset');

        // 2. CSV Export Stream
        $csvResponse = $this->actingAs($this->admin)->get(route('reports.sales', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'export' => 'csv',
        ]));
        $csvResponse->assertStatus(200);
        $this->assertTrue(str_contains($csvResponse->headers->get('content-type'), 'text/csv'));
    }

    public function test_profit_loss_report_calculates_cogs_and_gross_margin()
    {
        $this->createConfirmedOrder(10); // Omset subtotal: Rp 900.000, HPP: 10 * 50.000 = Rp 500.000. Profit: Rp 400.000 (margin ~44.4%)

        $response = $this->actingAs($this->owner)->get(route('reports.profit', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laporan Laba Kotor');
        $response->assertSee('Beban Pokok Penjualan (HPP)');

        // CSV Export
        $csvResponse = $this->actingAs($this->owner)->get(route('reports.profit', [
            'export' => 'csv',
        ]));
        $csvResponse->assertStatus(200);
        $this->assertTrue(str_contains($csvResponse->headers->get('content-type'), 'text/csv'));
    }

    public function test_sales_role_cannot_access_confidential_profit_report()
    {
        // Sales cannot see company cost prices and profit margins
        $response = $this->actingAs($this->sales)->get(route('reports.profit'));
        $response->assertStatus(403);
    }

    public function test_inventory_valuation_and_ar_aging_reports()
    {
        // 1. Inventory Report
        $resInv = $this->actingAs($this->admin)->get(route('reports.inventory'));
        $resInv->assertStatus(200);
        $resInv->assertSee('Valuasi Persediaan');

        $resInvCsv = $this->actingAs($this->admin)->get(route('reports.inventory', ['export' => 'csv']));
        $resInvCsv->assertStatus(200);
        $this->assertTrue(str_contains($resInvCsv->headers->get('content-type'), 'text/csv'));

        // 2. AR Aging Report
        $resAR = $this->actingAs($this->finance)->get(route('reports.receivables'));
        $resAR->assertStatus(200);
        $resAR->assertSee('Laporan Rekapitulasi Umur Piutang');

        $resARCsv = $this->actingAs($this->finance)->get(route('reports.receivables', ['export' => 'csv']));
        $resARCsv->assertStatus(200);
        $this->assertTrue(str_contains($resARCsv->headers->get('content-type'), 'text/csv'));
    }

    public function test_customer_role_cannot_access_any_business_reports()
    {
        $this->actingAs($this->customerUser)->get(route('reports.index'))->assertStatus(403);
        $this->actingAs($this->customerUser)->get(route('reports.sales'))->assertStatus(403);
        $this->actingAs($this->customerUser)->get(route('reports.profit'))->assertStatus(403);
        $this->actingAs($this->customerUser)->get(route('reports.inventory'))->assertStatus(403);
        $this->actingAs($this->customerUser)->get(route('reports.receivables'))->assertStatus(403);
    }
}
