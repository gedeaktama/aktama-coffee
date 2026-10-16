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
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('shipping_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('qr_token', 64)->nullable()->unique()->after('longitude');
            $table->string('store_chain', 50)->nullable()->index()->after('customer_type'); // alfamart, indomaret, kelontong, kafe, lainnya
            $table->integer('min_stock_alert')->default(5)->after('payment_terms_days'); // batas minimal stok aman di toko
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'qr_token', 'store_chain', 'min_stock_alert']);
        });
    }
};
