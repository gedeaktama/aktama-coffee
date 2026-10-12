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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('price_toko', 15, 2)->nullable()->after('selling_price');
            $table->decimal('price_kafe', 15, 2)->nullable()->after('price_toko');
            $table->json('tier_pricing')->nullable()->after('price_kafe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['price_toko', 'price_kafe', 'tier_pricing']);
        });
    }
};
