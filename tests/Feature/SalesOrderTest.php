<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $sales;
    protected User $customerUser;
    protected Customer $b2bCustomer;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $category = Category::first() ?? Category::create([
            'name' => 'Kategori Sales Test',
            'slug' => 'kategori-sales-test',
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Aceh Gayo Spesial',
            'brand' => 'Kopi Distro',
            'coffee_type' => 'Arabika',
            'form' => 'both',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SO-SKU-' . uniqid(),
            'variant_name' => 'Kemasan 250g Biji',
            'package_weight_grams' => 250,
            'grind_form' => 'biji',
            'cost_price' => 50000,
            'selling_price' => 85000,
            'current_stock' => 50,
            'minimum_stock' => 10,
            'is_active' => true,
        ]);

        $this->b2bCustomer = Customer::create([
            'code' => 'CUST-B2B-' . uniqid(),
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kafe Uji Rasa',
            'pic_name' => 'Barista Uji',
            'phone' => '081234567890',
            'email' => 'kafe.uji@kopi.local',
            'address' => 'Jl. Kopi No. 12',
            'shipping_address' => 'Jl. Kopi No. 12',
            'sales_user_id' => $this->sales->id,
            'credit_limit' => 2000000, // Plafon 2 Juta
            'payment_terms_days' => 14, // Tempo 14 Hari
            'is_active' => true,
        ]);
    }

    public function test_authorized_staff_can_view_orders_index_and_create_form(): void
    {
        $resIndex = $this->actingAs($this->sales)->get(route('orders.index'));
        $resIndex->assertStatus(200);

        $resCreate = $this->actingAs($this->sales)->get(route('orders.create'));
        $resCreate->assertStatus(200);

        // Customer role is strictly forbidden from sales dashboard
        $resCust = $this->actingAs($this->customerUser)->get(route('orders.index'));
        $resCust->assertStatus(403);
    }

    public function test_sales_can_create_b2b_order_and_snapshot_unit_prices(): void
    {
        $initialStock = $this->variant->current_stock;
        $orderNumber = 'SO-TEST-' . rand(1000, 9999);
        $orderQty = 5;

        $response = $this->actingAs($this->sales)->post(route('orders.store'), [
            'order_number' => $orderNumber,
            'customer_id' => $this->b2bCustomer->id,
            'order_date' => now()->toDateString(),
            'payment_type' => 'cash',
            'discount' => 15000,
            'shipping_cost' => 20000,
            'recipient_name' => 'Barista Uji',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Kopi No. 12',
            'notes' => 'Kirim siang hari.',
            'items' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'quantity' => $orderQty,
                ],
            ],
        ]);

        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));

        $expectedTotal = (5 * 85000) - 15000 + 20000;
        $this->assertEquals($expectedTotal, (float)$order->total_amount);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertFalse($order->stock_reduced);

        // Verify snapshot selling price in order_items
        $item = $order->items->first();
        $this->assertEquals(85000, (float)$item->unit_price);
        $this->assertEquals(50000, (float)$item->cost_price);

        // Stock must NOT be reduced while order status is pending
        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->current_stock);
    }

    public function test_confirming_order_reduces_inventory_stock_and_creates_audit_movement(): void
    {
        $initialStock = $this->variant->current_stock;
        $orderQty = 10;

        $order = Order::create([
            'order_number' => 'SO-CONF-' . rand(1000, 9999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => 'cash',
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => 10 * 85000,
            'total_amount' => 10 * 85000,
            'stock_reduced' => false,
        ]);

        $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'quantity' => $orderQty,
            'unit_price' => 85000,
            'cost_price' => 50000,
            'subtotal' => 10 * 85000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('orders.confirm', $order));
        $response->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertEquals(Order::STATUS_CONFIRMED, $order->status);
        $this->assertTrue($order->stock_reduced);

        // Stock decreased
        $this->variant->refresh();
        $this->assertEquals($initialStock - $orderQty, $this->variant->current_stock);

        // Stock movement audit recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $this->variant->id,
            'type' => StockMovement::TYPE_OUT,
            'reference_type' => StockMovement::REF_SALES_ORDER,
            'reference_id' => $order->id,
            'quantity' => -$orderQty,
            'stock_before' => $initialStock,
            'stock_after' => $initialStock - $orderQty,
        ]);
    }

    public function test_cancelling_confirmed_order_returns_stock_back_to_inventory(): void
    {
        $initialStock = $this->variant->current_stock;
        $orderQty = 8;

        // Create confirmed order with stock reduced
        $order = Order::create([
            'order_number' => 'SO-CNC-' . rand(1000, 9999),
            'order_type' => Order::TYPE_B2B,
            'customer_id' => $this->b2bCustomer->id,
            'user_id' => $this->sales->id,
            'order_date' => now()->toDateString(),
            'payment_type' => 'cash',
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'subtotal' => 8 * 85000,
            'total_amount' => 8 * 85000,
            'stock_reduced' => true,
        ]);

        $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'quantity' => $orderQty,
            'unit_price' => 85000,
            'cost_price' => 50000,
            'subtotal' => 8 * 85000,
        ]);

        // Manually simulate reduced stock
        $this->variant->update(['current_stock' => $initialStock - $orderQty]);

        // Cancel order
        $response = $this->actingAs($this->admin)->post(route('orders.cancel', $order));
        $response->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertEquals(Order::STATUS_CANCELLED, $order->status);
        $this->assertFalse($order->stock_reduced);

        // Stock restored back
        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->current_stock);
    }

    public function test_b2b_credit_order_strictly_validates_credit_limit(): void
    {
        // Customer limit is 2.000.000. Request order of 30 packs x 85.000 = 2.550.000 (exceeds limit)
        $orderNumber = 'SO-OVR-' . rand(1000, 9999);

        $response = $this->actingAs($this->sales)->post(route('orders.store'), [
            'order_number' => $orderNumber,
            'customer_id' => $this->b2bCustomer->id,
            'order_date' => now()->toDateString(),
            'payment_type' => 'credit',
            'items' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'quantity' => 30, // 30 * 85.000 = 2.550.000 > 2.000.000
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', ['order_number' => $orderNumber]);
    }

    public function test_retail_storefront_add_to_cart_and_checkout_reduces_stock(): void
    {
        $initialStock = $this->variant->current_stock;
        $buyQty = 3;

        // 1. Add to cart
        $responseAdd = $this->post(route('store.cart.add'), [
            'product_variant_id' => $this->variant->id,
            'quantity' => $buyQty,
        ]);
        $responseAdd->assertRedirect(route('store.cart'));
        $this->assertNotEmpty(session('cart'));

        // 2. Checkout
        $responseCheckout = $this->post(route('store.checkout.process'), [
            'recipient_name' => 'Budi Santoso Konsumen',
            'recipient_phone' => '085712345678',
            'shipping_address' => 'Jl. Melati No. 88, Jakarta Selatan',
            'payment_type' => 'transfer',
            'notes' => 'Tolong di-packing bubble wrap rapi.',
        ]);

        $responseCheckout->assertStatus(200); // Renders store.success
        $this->assertEmpty(session('cart')); // Cart is cleared

        // Retail order created & stock reduced instantly
        $this->variant->refresh();
        $this->assertEquals($initialStock - $buyQty, $this->variant->current_stock);

        $this->assertDatabaseHas('orders', [
            'order_type' => Order::TYPE_RETAIL,
            'recipient_name' => 'Budi Santoso Konsumen',
            'status' => Order::STATUS_CONFIRMED,
            'stock_reduced' => true,
        ]);
    }
}
