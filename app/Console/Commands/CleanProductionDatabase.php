<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Database\Seeders\InventorySeeder;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanProductionDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-production {--force : Jalankan pembersihan tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan database produksi dari seluruh data dummy/pengujian dan hanya menyisakan data kopi resmi PT Kopi Bintang Harapan (Alfamidi) serta akun staf resmi.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin membersihkan database produksi? Seluruh transaksi pengujian, kunjungan dummy, dan produk sampel lama akan dihapus.')) {
            $this->info('Pembersihan database dibatalkan.');
            return 0;
        }

        $this->info('Memulai pembersihan database produksi...');

        // 1. Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Kosongkan seluruh tabel transaksi & data pengujian lapangan
        $transactionTables = [
            'orders',
            'order_items',
            'invoices',
            'payments',
            'deliveries',
            'cart_items',
            'store_visits',
            'consignments',
            'consignment_items',
            'consignment_audits',
            'consignment_audit_items',
            'stock_movements',
        ];

        foreach ($transactionTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line(" - Tabel '{$table}' berhasil dikosongkan.");
            }
        }

        // 3. Bersihkan produk lama selain produk resmi PT Kopi Bintang Harapan
        if (Schema::hasTable('product_variants') && Schema::hasTable('products')) {
            $officialProductIds = Product::whereIn('slug', [
                'kopi-bubuk-robusta-bintang-harapan',
                'kopi-bubuk-arabika-bintang-harapan',
            ])->pluck('id')->toArray();

            ProductVariant::whereNotIn('product_id', $officialProductIds)->delete();
            Product::whereNotIn('slug', [
                'kopi-bubuk-robusta-bintang-harapan',
                'kopi-bubuk-arabika-bintang-harapan',
            ])->delete();
        }

        // 4. Bersihkan kategori selain Robusta & Arabika
        if (Schema::hasTable('categories')) {
            Category::whereNotIn('slug', [
                'kopi-bubuk-robusta',
                'kopi-bubuk-arabika',
            ])->delete();
        }

        // 5. Bersihkan customer selain Alfamidi resmi
        if (Schema::hasTable('customers')) {
            Customer::whereNotIn('code', [
                'CUST-MIDI-001',
                'CUST-MIDI-002',
                'CUST-ALFA-001',
            ])->delete();
        }

        // 6. Bersihkan supplier selain PT Kopi Bintang Harapan
        if (Schema::hasTable('suppliers')) {
            Supplier::whereNotIn('code', ['SUP-KBH-01'])->delete();
        }

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 7. Seed ulang data master resmi perusahaan & saldo awal gudang
        $this->info('Mengisi master data resmi PT Kopi Bintang Harapan (Alfamidi)...');
        $this->call(UserSeeder::class);
        $this->call(MasterDataSeeder::class);
        $this->call(InventorySeeder::class);

        $this->newLine();
        $this->info('==================================================================');
        $this->info('  DATABASE PRODUKSI BERHASIL DIBERSIHKAN (100% CLEAN)!');
        $this->info('==================================================================');

        $this->table(
            ['ID', 'Produk Resmi', 'Kategori', 'Jumlah Varian Gramasi', 'Status'],
            Product::with('category', 'variants')->get()->map(function ($p) {
                return [
                    $p->id,
                    $p->name,
                    $p->category ? $p->category->name : '-',
                    $p->variants->count() . ' Varian (Bungkus & Bal/Karton)',
                    $p->is_active ? 'Aktif' : 'Nonaktif',
                ];
            })
        );

        $this->newLine();
        $this->info('Daftar SKU & Barcode Resmi PT Kopi Bintang Harapan:');
        $this->table(
            ['SKU', 'Varian', 'Netto', 'Barcode Resmi', 'Harga Toko / Alfamidi', 'HET Ritel'],
            ProductVariant::orderBy('package_weight_grams')->get()->map(function ($v) {
                return [
                    $v->sku,
                    $v->variant_name,
                    $v->package_weight_grams . ' g',
                    $v->barcode ?? '-',
                    'Rp ' . number_format($v->price_toko, 2, ',', '.'),
                    'Rp ' . number_format($v->selling_price, 0, ',', '.'),
                ];
            })
        );

        return 0;
    }
}
