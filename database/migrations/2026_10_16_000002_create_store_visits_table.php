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
        Schema::create('store_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_number', 50)->unique()->index(); // SV-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Sopir / Kurir / Sales
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete(); // Toko Alfamart / Indomaret dll
            $table->date('visit_date')->index();
            $table->dateTime('check_in_at')->index();
            $table->dateTime('check_out_at')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->integer('distance_meters')->nullable(); // Jarak selisih GPS sopir ke toko dalam meter
            $table->boolean('is_geofence_valid')->default(true); // True jika jarak <= 100m dari toko
            $table->string('visit_purpose', 50)->default('audit_delivery')->index(); // audit_delivery, restock, collection, survey
            $table->string('status', 30)->default('in_progress')->index(); // in_progress, completed, cancelled
            $table->string('photo_proof')->nullable(); // Foto bukti rak pajangan / serah terima
            $table->string('recipient_name', 100)->nullable(); // Nama kepala toko / kasir / PIC
            $table->string('recipient_phone', 50)->nullable();
            $table->text('notes')->nullable();
            
            // Ringkasan hasil audit/penjualan di kunjungan ini
            $table->integer('summary_sold_qty')->default(0);
            $table->decimal('summary_sold_amount', 15, 2)->default(0);
            $table->integer('summary_restock_qty')->default(0);
            
            // Relasi opsional ke audit konsinyasi atau surat jalan jika terkait
            $table->foreignId('consignment_audit_id')->nullable()->constrained('consignment_audits')->nullOnDelete();
            $table->foreignId('delivery_order_id')->nullable()->constrained('delivery_orders')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_visits');
    }
};
