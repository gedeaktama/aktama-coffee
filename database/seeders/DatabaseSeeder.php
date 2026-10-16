<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with clean official master data and initial stock.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MasterDataSeeder::class,
            InventorySeeder::class,
            StoreDistributionSeeder::class,
        ]);
    }
}
