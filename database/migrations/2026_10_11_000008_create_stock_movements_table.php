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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->index(); // in, out, adjustment
            $table->string('reference_type', 50)->index(); // purchase, sales_order, manual_adjustment, initial_stock
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->integer('quantity'); // Jumlah perubahan (+50 atau -10)
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('notes')->nullable(); // Alasan mutasi yang dapat diaudit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
