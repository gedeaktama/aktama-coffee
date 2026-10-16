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

    protected function setUp(): void
    {
        parent::setUp();
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

        $store = Customer::where('store_chain', Customer::STORE_CHAIN_ALFAMART)->first();

        $response = $this->actingAs($driver)->post(route('driver.checkin'), [
            'qr_token' => $store->qr_token,
            'latitude' => $store->latitude,
            'longitude' => $store->longitude,
            'visit_purpose' => 'audit_delivery',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('store_visits', [
            'user_id' => $driver->id,
            'customer_id' => $store->id,
            'status' => StoreVisit::STATUS_IN_PROGRESS,
            'is_geofence_valid' => true,
        ]);
    }

    public function test_driver_can_complete_visit_with_consignment_audit(): void
    {
        $driver = User::factory()->create([
            'role' => User::ROLE_DRIVER,
        ]);

        $store = Customer::where('store_chain', Customer::STORE_CHAIN_ALFAMART)->first();
        $consignment = Consignment::where('customer_id', $store->id)->first();
        $item = ConsignmentItem::where('consignment_id', $consignment->id)->first();

        $initialStock = $item->current_quantity;

        // Buat visit in_progress
        $visit = StoreVisit::create([
            'visit_number' => 'SV-TEST-001',
            'user_id' => $driver->id,
            'customer_id' => $store->id,
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
