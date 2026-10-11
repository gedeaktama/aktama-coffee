<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryPurchaseTest extends TestCase
{
    use DatabaseTransactions;

    protected User $warehouse;
    protected User $admin;
    protected User $customer;
    protected Supplier $supplier;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouse = User::factory()->create(['role' => User::ROLE_WAREHOUSE]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->supplier = Supplier::first() ?? Supplier::create([
            'name' => 'Koperasi Tani Kopi Uji Coba',
            'contact_person' => 'Bapak Uji',
            'phone' => '081299998888',
            'email' => 'supplier.test@kopi.local',
            'is_active' => true,
        ]);

        $category = Category::first() ?? Category::create([
            'name' => 'Kategori Uji',
            'slug' => 'kategori-uji',
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Gayo Test',
            'brand' => 'Kopi Distro',
            'coffee_type' => 'Arabika',
            'form' => 'both',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'TEST-SKU-' . uniqid(),
            'variant_name' => 'Test 250g Biji',
            'package_weight_grams' => 250,
            'grind_form' => 'biji',
            'cost_price' => 60000,
            'selling_price' => 95000,
            'current_stock' => 10,
            'minimum_stock' => 15,
            'is_active' => true,
        ]);
    }

    public function test_authorized_roles_can_view_purchase_and_inventory_index(): void
    {
        // Warehouse staff can access purchase & inventory
        $resPurchases = $this->actingAs($this->warehouse)->get(route('purchases.index'));
        $resPurchases->assertStatus(200);

        $resStocks = $this->actingAs($this->warehouse)->get(route('inventory.index'));
        $resStocks->assertStatus(200);

        $resMovements = $this->actingAs($this->warehouse)->get(route('inventory.movements'));
        $resMovements->assertStatus(200);

        // Customer role is strictly forbidden
        $resCustomer = $this->actingAs($this->customer)->get(route('purchases.index'));
        $resCustomer->assertStatus(403);
    }

    public function test_user_can_create_purchase_order_without_increasing_stock_immediately(): void
    {
        $initialStock = $this->variant->current_stock;
        $orderQty = 50;
        $poNumber = 'PO-TEST-' . rand(1000, 9999);

        $response = $this->actingAs($this->warehouse)->post(route('purchases.store'), [
            'purchase_number' => $poNumber,
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'discount' => 10000,
            'shipping_cost' => 25000,
            'notes' => 'Pemesanan bahan baku batch testing.',
            'items' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'quantity' => $orderQty,
                    'cost_price' => 60000,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchases.index'));

        // PO is stored with ordered status
        $this->assertDatabaseHas('purchases', [
            'purchase_number' => $poNumber,
            'supplier_id' => $this->supplier->id,
            'status' => Purchase::STATUS_ORDERED,
            'total_amount' => (50 * 60000) - 10000 + 25000,
        ]);

        // CRITICAL BUSINESS RULE: Stock MUST NOT increase yet upon PO creation
        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->current_stock, 'Stock should NOT increase until physical receipt is confirmed.');
    }

    public function test_receiving_purchase_order_physically_strictly_increases_stock_and_logs_audit_movement(): void
    {
        $initialStock = $this->variant->current_stock;
        $orderQty = 40;
        $poNumber = 'PO-RCV-' . rand(1000, 9999);

        // 1. Create Purchase
        $purchase = Purchase::create([
            'purchase_number' => $poNumber,
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->warehouse->id,
            'purchase_date' => now()->toDateString(),
            'status' => Purchase::STATUS_ORDERED,
            'subtotal' => $orderQty * 60000,
            'discount' => 0,
            'shipping_cost' => 0,
            'total_amount' => $orderQty * 60000,
        ]);

        $purchase->items()->create([
            'product_variant_id' => $this->variant->id,
            'quantity' => $orderQty,
            'cost_price' => 60000,
            'subtotal' => $orderQty * 60000,
        ]);

        // 2. Warehouse confirms physical goods receipt
        $response = $this->actingAs($this->warehouse)->post(route('purchases.receive', $purchase));
        $response->assertRedirect(route('purchases.show', $purchase));

        // 3. Purchase status is updated to received
        $purchase->refresh();
        $this->assertEquals(Purchase::STATUS_RECEIVED, $purchase->status);
        $this->assertEquals($this->warehouse->id, $purchase->received_by_user_id);
        $this->assertNotNull($purchase->received_at);

        // 4. Stock is verified to increase accurately
        $this->variant->refresh();
        $this->assertEquals($initialStock + $orderQty, $this->variant->current_stock);

        // 5. Stock movement audit record is generated
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $this->variant->id,
            'user_id' => $this->warehouse->id,
            'type' => StockMovement::TYPE_IN,
            'reference_type' => StockMovement::REF_PURCHASE,
            'reference_id' => $purchase->id,
            'quantity' => $orderQty,
            'stock_before' => $initialStock,
            'stock_after' => $initialStock + $orderQty,
        ]);
    }

    public function test_cannot_receive_already_received_or_cancelled_purchase_order(): void
    {
        $purchase = Purchase::create([
            'purchase_number' => 'PO-CAN-' . rand(1000, 9999),
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->warehouse->id,
            'purchase_date' => now()->toDateString(),
            'status' => Purchase::STATUS_CANCELLED,
            'subtotal' => 60000,
            'total_amount' => 60000,
        ]);

        $response = $this->actingAs($this->warehouse)->post(route('purchases.receive', $purchase));
        $response->assertSessionHas('error');
    }

    public function test_user_can_adjust_stock_with_mandatory_audit_reason(): void
    {
        $oldStock = $this->variant->current_stock;
        $newStock = $oldStock + 15;
        $reason = 'Stock opname kuartal 4 rak B3 ditemukan 15 pack terselip';

        $response = $this->actingAs($this->warehouse)->post(route('inventory.adjust'), [
            'product_variant_id' => $this->variant->id,
            'actual_stock' => $newStock,
            'reason' => $reason,
        ]);

        $response->assertRedirect(route('inventory.index'));

        $this->variant->refresh();
        $this->assertEquals($newStock, $this->variant->current_stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $this->variant->id,
            'type' => StockMovement::TYPE_ADJUSTMENT,
            'reference_type' => StockMovement::REF_MANUAL_ADJUSTMENT,
            'quantity' => 15,
            'stock_before' => $oldStock,
            'stock_after' => $newStock,
        ]);
    }

    public function test_stock_adjustment_requires_mandatory_reason(): void
    {
        $response = $this->actingAs($this->warehouse)->post(route('inventory.adjust'), [
            'product_variant_id' => $this->variant->id,
            'actual_stock' => 20,
            'reason' => '', // Empty reason should fail validation
        ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_inventory_service_strictly_prevents_negative_stock(): void
    {
        $inventoryService = app(InventoryService::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Stok tidak mencukupi");

        // Variant has 10 stocks, attempting to reduce 100 must throw exception
        $inventoryService->reduceStock(
            variant: $this->variant,
            quantity: 100,
            referenceType: 'test_order'
        );
    }
}
