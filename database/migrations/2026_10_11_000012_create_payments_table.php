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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique()->index(); // PAY-YYYYMM-XXXX
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Petugas finance yang memverifikasi
            $table->date('payment_date')->index();
            $table->string('payment_method', 30)->default('transfer')->index(); // cash, transfer_bca, transfer_mandiri, transfer_bri, giro
            $table->decimal('amount', 15, 2);
            $table->string('reference_number', 100)->nullable(); // Nomor mutasi bank / no bukti transfer / no giro
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
