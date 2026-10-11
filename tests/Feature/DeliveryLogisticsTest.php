<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeliveryLogisticsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $warehouse;
    protected User $sales;
    protected User $customerUser;
    protected Customer $b2bCustomer;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->warehouse = User::factory()->create(['role' => User::ROLE_WAREHOUSE]);
        $this->sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $category = Category::first() ?? Category::create([
            'name' => 'Kategori Logistics Test',
            'slug' => 'kategori-logistics-test-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Robusta Lampung Logistik',
            'brand' => 'Kopi Distro',
            'coffee_type' => 'Robusta',
            'form' => 'both',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'LOG-SKU-' . uniqid(),
            'variant_name' => 'Kemasan 250g Bubuk',
            'package_weight_grams' => 250,
            'grind_form' => 'bubuk',
            'cost_price' => 35000,
            'selling_price' => 60000,
            'current_stock' => 100,
            'minimum_stock' => 10,
            'is_active' => true,
        ]);

        $this->b2bCustomer = Customer::create([
            'code' => 'CUST-LOG-' . strtoupper(substr(uniqid(), 0, 4)),
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kafe Logistik Ekspres',
            'pic_name' => 'Ahmad Logistik',
            'phone' => '081211223344',
            'address' => 'Jl. Pengiriman Raya No. 18, Jakarta Selatan',
            'credit_limit' => 5000000,
            'payment_terms_days' => 14,
            'is_active' => true,
        ]);
    }

    protected function createConfirmedOrder(int $qty = 8): Order
    {
        $total = $qty * 60000;

        $order = Order::create([
            'order_number' => 'SO-LOG-' . rand(10000, 99999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => Order::PAYMENT_CREDIT,
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => $total,
            'total_amount' => $total,
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
            'unit_price' => 60000,
            'cost_price' => 35000,
            'subtotal' => $total,
        ]);

        return $order;
    }

    public function test_warehouse_can_create_delivery_order_for_confirmed_order()
    {
        $order = $this->createConfirmedOrder(10);

        $response = $this->actingAs($this->warehouse)->post(route('delivery.orders.store', $order), [
            'delivery_date' => now()->toDateString(),
            'courier_type' => 'internal',
            'driver_name' => 'Sopir Agus',
            'driver_phone' => '081299998888',
            'vehicle_plate_number' => 'B 1234 LOG',
            'shipping_address' => $order->shipping_address,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'status' => 'processing',
            'notes' => 'Kirim sebelum jam makan siang',
        ]);

        $order->refresh();
        $delivery = $order->deliveryOrder;

        $this->assertNotNull($delivery);
        $response->assertRedirect(route('delivery.show', $delivery));

        $this->assertStringStartsWith('SJ-', $delivery->delivery_number);
        $this->assertEquals(DeliveryOrder::STATUS_PROCESSING, $delivery->status);
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);
        $this->assertEquals('Sopir Agus', $delivery->driver_name);
        $this->assertEquals(10, $delivery->total_units);
        $this->assertCount(1, $delivery->items);
    }

    public function test_unconfirmed_order_cannot_generate_delivery_order()
    {
        $order = Order::create([
            'order_number' => 'SO-PENDING-' . rand(1000, 9999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => Order::PAYMENT_CASH,
            'status' => Order::STATUS_PENDING, // Not confirmed
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'total_amount' => 120000,
        ]);

        $response = $this->actingAs($this->warehouse)->get(route('delivery.orders.create', $order));
        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error');
    }

    public function test_warehouse_can_mark_delivery_as_shipped()
    {
        $order = $this->createConfirmedOrder(6);
        $this->actingAs($this->warehouse)->post(route('delivery.orders.store', $order), [
            'delivery_date' => now()->toDateString(),
            'courier_type' => 'internal',
            'driver_name' => 'Sopir Budi',
            'shipping_address' => $order->shipping_address,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'status' => 'processing',
        ]);

        $delivery = $order->fresh()->deliveryOrder;

        // Dispatch barang ke kurir
        $response = $this->actingAs($this->warehouse)->post(route('delivery.mark_shipped', $delivery), [
            'tracking_number' => 'RESI-INTERNAL-99',
            'notes' => 'Telah berangkat dari gudang blok C',
        ]);

        $response->assertSessionHas('success');
        $delivery->refresh();
        $order->refresh();

        $this->assertEquals(DeliveryOrder::STATUS_SHIPPED, $delivery->status);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);
        $this->assertEquals('RESI-INTERNAL-99', $delivery->tracking_number);
    }

    public function test_confirm_delivery_received_updates_order_to_delivered()
    {
        $order = $this->createConfirmedOrder(6);
        $this->actingAs($this->warehouse)->post(route('delivery.orders.store', $order), [
            'delivery_date' => now()->toDateString(),
            'courier_type' => 'internal',
            'driver_name' => 'Sopir Budi',
            'shipping_address' => $order->shipping_address,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'status' => 'shipped',
        ]);

        $delivery = $order->fresh()->deliveryOrder;

        // Konfirmasi sampai di tujuan
        $response = $this->actingAs($this->warehouse)->post(route('delivery.mark_delivered', $delivery), [
            'received_by' => 'Barista Rian',
            'received_at' => now()->toDateTimeString(),
            'notes' => 'Kemasan segel rapi diterima lengkap',
        ]);

        $response->assertSessionHas('success');
        $delivery->refresh();
        $order->refresh();

        $this->assertEquals(DeliveryOrder::STATUS_DELIVERED, $delivery->status);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->status);
        $this->assertEquals('Barista Rian', $delivery->received_by);
        $this->assertNotNull($delivery->received_at);
    }

    public function test_authorized_staff_can_view_delivery_index_and_print_document()
    {
        $order = $this->createConfirmedOrder(5);
        $this->actingAs($this->warehouse)->post(route('delivery.orders.store', $order), [
            'delivery_date' => now()->toDateString(),
            'courier_type' => 'internal',
            'driver_name' => 'Sopir Joni',
            'shipping_address' => $order->shipping_address,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'status' => 'processing',
        ]);

        $delivery = $order->fresh()->deliveryOrder;

        // 1. Index
        $resIndex = $this->actingAs($this->warehouse)->get(route('delivery.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Surat Jalan');

        // 2. Show
        $resShow = $this->actingAs($this->warehouse)->get(route('delivery.show', $delivery));
        $resShow->assertStatus(200);
        $resShow->assertSee($delivery->delivery_number);

        // 3. Print
        $resPrint = $this->actingAs($this->warehouse)->get(route('delivery.print', $delivery));
        $resPrint->assertStatus(200);
        $resPrint->assertSee('SURAT JALAN');
        $resPrint->assertSee($delivery->delivery_number);
    }

    public function test_customer_role_cannot_manage_deliveries()
    {
        $response = $this->actingAs($this->customerUser)->get(route('delivery.index'));
        $response->assertStatus(403);
    }
}
