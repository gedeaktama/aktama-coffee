<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for consignment system.
     */
    public function up(): void
    {
        // 1. Penitipan Konsinyasi (Header Titip Barang ke Toko)
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_number', 50)->unique()->index(); // KS-YYYYMM-XXXX
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('sales_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('delivery_date')->index();
            $table->string('status', 30)->default('active')->index(); // active, settled, cancelled
            $table->integer('total_items_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Item Varian yang Dititipkan di Toko
        Schema::create('consignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignments')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('initial_quantity')->default(0); // Kuantitas awal dititipkan
            $table->integer('current_quantity')->default(0); // Sisa stok titipan aktif di toko
            $table->integer('sold_quantity')->default(0); // Total yang sudah laku terjual
            $table->integer('returned_quantity')->default(0); // Total barang retur/ditarik
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 3. Survei Mingguan Konsinyasi & Penyelesaian Barang Laku
        Schema::create('consignment_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number', 50)->unique()->index(); // SRV-YYYYMM-XXXX
            $table->foreignId('consignment_id')->constrained('consignments')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Sales yang melakukan survei
            $table->date('audit_date')->index();
            $table->integer('total_sold_qty')->default(0);
            $table->decimal('total_sold_amount', 15, 2)->default(0);
            $table->integer('total_restock_qty')->default(0);
            $table->string('payment_status', 30)->default('paid')->index(); // paid, unpaid
            $table->string('payment_method', 30)->default('cash')->index(); // cash, transfer
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Rincian Item Hasil Survei Mingguan
        Schema::create('consignment_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_audit_id')->constrained('consignment_audits')->cascadeOnDelete();
            $table->foreignId('consignment_item_id')->constrained('consignment_items')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('stock_before')->default(0); // Stok tercatat sebelum survei
            $table->integer('physical_remaining')->default(0); // Sisa fisik di rak yang dihitung sales
            $table->integer('quantity_sold')->default(0); // Jumlah laku terjual
            $table->integer('quantity_returned')->default(0); // Jumlah retur (rusak/kadaluarsa)
            $table->integer('restock_quantity')->default(0); // Titip baru tambahan hari itu
            $table->integer('stock_after')->default(0); // Stok di toko setelah survei & restock
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_audit_items');
        Schema::dropIfExists('consignment_audits');
        Schema::dropIfExists('consignment_items');
        Schema::dropIfExists('consignments');
    }
};
