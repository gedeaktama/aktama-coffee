<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique()->index();
            $table->string('variant_name');
            $table->integer('package_weight_grams')->default(250); // 100, 250, 500, 1000
            $table->string('grind_form')->default('Biji Kopi'); // Biji Kopi, Bubuk Halus, Bubuk Sedang, Bubuk Kasar
            $table->string('barcode')->nullable()->index();
            $table->decimal('cost_price', 15, 2)->default(0); // Harga beli standar dari supplier
            $table->decimal('selling_price', 15, 2); // Satu harga jual standar per SKU
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
