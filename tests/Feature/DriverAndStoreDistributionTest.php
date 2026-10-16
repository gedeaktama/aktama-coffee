<?php

namespace Tests\Feature;

use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\StoreVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DriverAndStoreDistributionTest extends TestCase
{
    use DatabaseTransactions;

    protected Customer $testStore;
    protected Consignment $testConsignment;
    protected ConsignmentItem $testItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testStore = Customer::firstOrCreate(
            ['code' => 'STORE-TEST-ALFA-01'],
            [
                'customer_type' => Customer::TYPE_KONSINYASI,
                'store_chain' => Customer::STORE_CHAIN_ALFAMART,
                'business_name' => 'Alfamart Test Unit',
                'pic_name' => 'Bapak Kepala Toko',
                'phone' => '081234567890',
                'latitude' => -8.653400,
                'longitude' => 115.228500,
                'qr_token' => 'QR-ALFA-TEST-001',
                'credit_limit' => 5000000,
                'payment_terms_days' => 14,
                'is_active' => true,
            ]
        );

        $variant = ProductVariant::first();
        if (!$variant) {
            $cat = Category::firstOrCreate(['slug' => 'kopi-test'], ['name' => 'Kopi Test', 'is_active' => true]);
            $prod = Product::firstOrCreate(['slug' => 'prod-test'], ['category_id' => $cat->id, 'name' => 'Prod Test', 'brand' => 'Test', 'coffee_type' => 'Robusta', 'form' => 'ground', 'is_active' => true]);
            $variant = ProductVariant::create([
                'product_id' => $prod->id,
                'sku' => 'SKU-TEST-99',
                'variant_name' => 'Varian Test',
                'package_weight_grams' => 250,
                'grind_form' => 'Bubuk',
                'cost_price' => 10000,
                'selling_price' => 20000,
                'current_stock' => 50,
                'minimum_stock' => 5,
                'is_active' => true,
            ]);
        }

        $this->testConsignment = Consignment::firstOrCreate(
            ['customer_id' => $this->testStore->id, 'status' => Consignment::STATUS_ACTIVE],
            ['consignment_number' => 'CS-TEST-001', 'delivery_date' => now()->toDateString(), 'total_items_count' => 20]
        );

        $this->testItem = ConsignmentItem::firstOrCreate(
            ['consignment_id' => $this->testConsignment->id, 'product_variant_id' => $variant->id],
            [
                'initial_quantity' => 20,
                'current_quantity' => 15,
                'sold_quantity' => 5,
                'returned_quantity' => 0,
                'selling_price' => $variant->selling_price,
            ]
        );
    }

    public function test_driver_can_access_driver_dashboard(): void
    {
        $driver = User::factory()->create([
            'role' => User::ROLE_DRIVER,
        ]);

        $response = $this->actingAs($driver)->get(route('driver.dashboard'));
        $response->assertOk();
        $response->assertSee('Portal Sopir');
        $response->assertSee('Daftar Toko');
    }

    public function test_driver_can_access_scan_screen(): void
    {
        $driver = User::factory()->create([
            'role' => User::ROLE_DRIVER,
        ]);

        $response = $this->actingAs($driver)->get(route('driver.scan'));
        $response->assertOk();
        $response->assertSee('Scan Toko Tiba');
        $response->assertSee('Mendeteksi Posisi GPS HP');
    }

    public function test_driver_can_checkin_with_store_qr_and_gps(): void
    {
        $driver = User::factory()->create([
            'role' => User::ROLE_DRIVER,
        ]);

        $response = $this->actingAs($driver)->post(route('driver.checkin'), [
            'qr_token' => $this->testStore->qr_token,
            'latitude' => $this->testStore->latitude,
            'longitude' => $this->testStore->longitude,
            'visit_purpose' => 'audit_delivery',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('store_visits', [
            'user_id' => $driver->id,
            'customer_id' => $this->testStore->id,
            'status' => StoreVisit::STATUS_IN_PROGRESS,
            'is_geofence_valid' => true,
        ]);
    }

    public function test_driver_can_complete_visit_with_consignment_audit(): void
    {
        $driver = User::factory()->create([
            'role' => User::ROLE_DRIVER,
        ]);

        $item = $this->testItem;
        $initialStock = $item->current_quantity;

        // Buat visit in_progress
        $visit = StoreVisit::create([
            'visit_number' => 'SV-TEST-001',
            'user_id' => $driver->id,
            'customer_id' => $this->testStore->id,
            'visit_date' => now()->toDateString(),
            'check_in_at' => now(),
            'status' => StoreVisit::STATUS_IN_PROGRESS,
        ]);

        // Sisa fisik 5 pack lebih sedikit dari stok awal (artinya 5 pack laku), tambah restock 10 pack
        $physicalRemaining = max(0, $initialStock - 5);
        $restockQty = 10;

        $response = $this->actingAs($driver)->post(route('driver.visit.complete', $visit->id), [
            'recipient_name' => 'Kepala Toko Made',
            'recipient_phone' => '08123456789',
            'notes' => 'Audit lancar rak tertata rapi.',
            'audit_items' => [
                $item->id => [
                    'physical_remaining' => $physicalRemaining,
                    'restock_qty' => $restockQty,
                ]
            ]
        ]);

        $response->assertRedirect(route('driver.dashboard'));

        // Cek database
        $this->assertDatabaseHas('store_visits', [
            'id' => $visit->id,
            'status' => StoreVisit::STATUS_COMPLETED,
            'summary_sold_qty' => 5,
            'summary_restock_qty' => 10,
        ]);

        // Cek stok item terupdate: sisa fisik + restock
        $item->refresh();
        $this->assertEquals($physicalRemaining + $restockQty, $item->current_quantity);
    }

    public function test_owner_and_admin_can_view_store_map_and_driver_tracking(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_OWNER,
        ]);

        $response = $this->actingAs($owner)->get(route('maps.stores'));
        $response->assertOk();
        $response->assertSee('Peta Sebaran Toko Mitra');

        $responseTracking = $this->actingAs($owner)->get(route('maps.driver_tracking'));
        $responseTracking->assertOk();
        $responseTracking->assertSee('Peta Jejak & Rute Kunjungan Sopir', false);
    }

    public function test_qr_sticker_can_be_rendered(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_OWNER,
        ]);

        $store = Customer::first();
        $response = $this->actingAs($owner)->get(route('driver.print_qr', $store->id));
        $response->assertOk();
        $response->assertSee('STIKER IDENTITAS RAK DISPLAY');
        $response->assertSee($store->business_name);
    }
}
