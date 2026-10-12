<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // boolean, string, integer, json
            $table->string('group')->default('system');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Default setting: developer_mode = 0 (OFF)
        DB::table('app_settings')->insert([
            'key' => 'developer_mode',
            'value' => '0',
            'type' => 'boolean',
            'group' => 'system',
            'description' => 'Mode Pengembang: Jika aktif (ON), seluruh produk tidak dapat dibeli dan transaksi pembelian dinonaktifkan sementara.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
