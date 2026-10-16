<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        $users = [
            [
                'name' => 'Pemilik Bisnis (Owner)',
                'email' => 'owner@kopi.com',
                'phone' => '081234567890',
                'role' => User::ROLE_OWNER,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Administrator Toko',
                'email' => 'admin@kopi.com',
                'phone' => '081234567891',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Sales Representative',
                'email' => 'sales@kopi.com',
                'phone' => '081234567892',
                'role' => User::ROLE_SALES,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Staf Gudang (Warehouse)',
                'email' => 'warehouse@kopi.com',
                'phone' => '081234567893',
                'role' => User::ROLE_WAREHOUSE,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Staf Keuangan (Finance)',
                'email' => 'finance@kopi.com',
                'phone' => '081234567894',
                'role' => User::ROLE_FINANCE,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Budi Santoso (Driver Armada 1)',
                'email' => 'driver@kopi.com',
                'phone' => '081234567895',
                'role' => User::ROLE_DRIVER,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Joko Supriyadi (Driver Armada 2)',
                'email' => 'driver2@kopi.com',
                'phone' => '081234567896',
                'role' => User::ROLE_DRIVER,
                'is_active' => true,
                'password' => $defaultPassword,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
