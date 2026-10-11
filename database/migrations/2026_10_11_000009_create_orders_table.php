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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->index(); // SO-YYYYMM-XXXX (B2B) atau ORD-YYYYMM-XXXX (Retail)
            $table->string('order_type', 20)->default('b2b')->index(); // b2b atau retail
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Pembuat order (Sales / Staf / Pelanggan)
            $table->foreignId('sales_user_id')->nullable()->constrained('users')->nullOnDelete(); // Sales PIC pembina pelanggan
            $table->date('order_date')->index();
            $table->string('payment_type', 30)->default('cash')->index(); // cash, transfer, credit (tempo)
            $table->date('due_date')->nullable()->index(); // Jatuh tempo untuk pembayaran tempo/kredit
            $table->string('status', 30)->default('pending')->index(); // pending, confirmed, processing, shipped, delivered, cancelled
            $table->string('payment_status', 30)->default('unpaid')->index(); // unpaid, partial, paid
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->boolean('stock_reduced')->default(false)->index(); // True jika stok telah dipotong dari persediaan
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
