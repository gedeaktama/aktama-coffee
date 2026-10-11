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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number', 50)->unique()->index(); // SJ-YYYYMM-XXXX
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Pembuat SJ (Gudang/Admin)
            $table->date('delivery_date')->index();
            $table->string('courier_type', 50)->default('internal')->index(); // internal (armada toko) atau ekspedisi (JNE, SiCepat, dll)
            $table->string('courier_name', 100)->nullable(); // Nama ekspedisi / armada
            $table->string('driver_name', 100)->nullable(); // Nama kurir/pengemudi
            $table->string('driver_phone', 50)->nullable();
            $table->string('vehicle_plate_number', 30)->nullable(); // Plat nomor kendaraan
            $table->string('tracking_number', 100)->nullable()->index(); // Nomor resi ekspedisi
            $table->text('shipping_address');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('status', 30)->default('processing')->index(); // processing, shipped, delivered, cancelled
            $table->string('received_by', 100)->nullable(); // Nama penerima fisik di lokasi tujuan
            $table->timestamp('received_at')->nullable(); // Waktu barang diterima fisik
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants');
            $table->integer('quantity');
            $table->string('package_info')->nullable(); // Contoh: "1 Koli Dus (12 Pack)"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
    }
};
