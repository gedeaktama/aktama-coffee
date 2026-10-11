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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index(); // CUST-KAFE-001, CUST-TOKO-001, CUST-RSL-001
            $table->string('customer_type', 30)->default('kafe')->index(); // kafe, toko, reseller, retail
            $table->string('business_name'); // Nama Usaha / Kafe / Toko
            $table->string('pic_name'); // Nama Penanggung Jawab
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable(); // Alamat Utama / Penagihan
            $table->text('shipping_address')->nullable(); // Alamat Pengiriman
            $table->foreignId('sales_user_id')->nullable()->constrained('users')->nullOnDelete(); // Sales PIC
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Akun User (jika ada)
            $table->decimal('credit_limit', 15, 2)->default(0); // Batas Plafon Kredit (Rp)
            $table->integer('payment_terms_days')->default(0); // Termin Tempo dalam Hari (0 = Tunai/Transfer Langsung)
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
