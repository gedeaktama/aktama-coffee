<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $sales;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $this->customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    }

    public function test_admin_can_view_and_create_category(): void
    {
        $response = $this->actingAs($this->admin)->get(route('categories.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->admin)->post(route('categories.store'), [
            'name' => 'Kopi Luwak Liar',
            'description' => 'Kopi langka hasil fermentasi alami luwak liar.',
        ]);

        $createResponse->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Kopi Luwak Liar',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_product_with_initial_sku_variant(): void
    {
        $category = Category::first();

        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Kopi Arabika Wamena Papua',
            'brand' => 'Kopi Distro Origin',
            'coffee_type' => 'Arabika',
            'form' => 'both',
            'description' => 'Aroma floral manis khas tanah Papua.',
            'variant_name' => 'Wamena 250g Biji',
            'sku' => 'SKU-WP-250G',
            'package_weight_grams' => 250,
            'grind_form' => 'Biji Kopi',
            'cost_price' => 75000,
            'selling_price' => 105000,
            'current_stock' => 40,
            'minimum_stock' => 10,
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Arabika Wamena Papua',
            'coffee_type' => 'Arabika',
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => 'SKU-WP-250G',
            'selling_price' => 105000.00,
            'current_stock' => 40,
        ]);
    }

    public function test_sku_must_be_unique_across_variants(): void
    {
        $category = Category::first();

        // Create first variant
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Uji SKU 1',
            'brand' => 'Test',
            'coffee_type' => 'Robusta',
            'form' => 'bean',
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-DUPLICATE-TEST',
            'variant_name' => 'Uji 250g',
            'package_weight_grams' => 250,
            'grind_form' => 'Biji Kopi',
            'cost_price' => 30000,
            'selling_price' => 45000,
            'current_stock' => 10,
            'minimum_stock' => 5,
        ]);

        // Attempt to create second variant with same SKU
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Kopi Uji SKU 2',
            'brand' => 'Test',
            'coffee_type' => 'Robusta',
            'form' => 'bean',
            'variant_name' => 'Uji Duplikat',
            'sku' => 'SKU-DUPLICATE-TEST',
            'package_weight_grams' => 250,
            'grind_form' => 'Biji Kopi',
            'cost_price' => 30000,
            'selling_price' => 45000,
            'current_stock' => 10,
            'minimum_stock' => 5,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_admin_can_create_supplier(): void
    {
        $response = $this->actingAs($this->admin)->post(route('suppliers.store'), [
            'code' => 'SUP-TEST-099',
            'name' => 'PT Perkebunan Kopi Nusantara Baru',
            'pic_name' => 'Pak Ahmad Subagyo',
            'phone' => '081234567899',
            'email' => 'ahmad@perkebunankopi.com',
            'address' => 'Desa Kopi Makmur, Jawa Barat',
        ]);

        $response->assertRedirect(route('suppliers.index'));

        $this->assertDatabaseHas('suppliers', [
            'code' => 'SUP-TEST-099',
            'name' => 'PT Perkebunan Kopi Nusantara Baru',
        ]);
    }

    public function test_admin_and_sales_can_create_b2b_customer(): void
    {
        $response = $this->actingAs($this->admin)->post(route('customers.store'), [
            'code' => 'CUST-KAFE-TEST-01',
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kopi Kenangan Senja',
            'pic_name' => 'Bambang Irawan',
            'phone' => '081999888777',
            'email' => 'senja@kenangan.com',
            'address' => 'Jl. Boulevard No. 88, Tangerang',
            'shipping_address' => 'Jl. Boulevard No. 88, Tangerang',
            'credit_limit' => 4000000,
            'payment_terms_days' => 14,
            'sales_user_id' => $this->sales->id,
        ]);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'code' => 'CUST-KAFE-TEST-01',
            'business_name' => 'Kopi Kenangan Senja',
            'credit_limit' => 4000000.00,
            'payment_terms_days' => 14,
            'sales_user_id' => $this->sales->id,
        ]);
    }

    public function test_sales_cannot_self_grant_unauthorized_credit_limit(): void
    {
        // Sales attempting to grant 5.000.000 credit limit should be rejected
        $response = $this->actingAs($this->sales)->post(route('customers.store'), [
            'code' => 'CUST-KAFE-UNAUTHORIZED',
            'customer_type' => Customer::TYPE_KAFE,
            'business_name' => 'Kafe Tanpa Otorisasi',
            'pic_name' => 'Tono',
            'phone' => '081777666555',
            'credit_limit' => 5000000, // Should be rejected for Sales role
            'payment_terms_days' => 14,
        ]);

        $response->assertSessionHasErrors('credit_limit');
    }

    public function test_customer_role_is_strictly_forbidden_from_master_data(): void
    {
        $responseCategories = $this->actingAs($this->customer)->get(route('categories.index'));
        $responseCategories->assertStatus(403);

        $responseProducts = $this->actingAs($this->customer)->get(route('products.index'));
        $responseProducts->assertStatus(403);

        $responseSuppliers = $this->actingAs($this->customer)->get(route('suppliers.index'));
        $responseSuppliers->assertStatus(403);

        $responseCustomers = $this->actingAs($this->customer)->get(route('customers.index'));
        $responseCustomers->assertStatus(403);
    }
}
