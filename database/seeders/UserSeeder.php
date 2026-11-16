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
        User::firstOrCreate(
            ['email' => 'admin@travel.test'],
            [
                'name' => 'Admin Utama',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin_utama',
                'access_code' => Hash::make('ADMIN123'),
                'status' => 'active',
            ],
        );

        User::firstOrCreate(
            ['email' => 'staff@travel.test'],
            [
                'name' => 'Admin Biasa',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'branch' => 'Pool Dipatiukur Bandung',
                'access_code' => Hash::make('STF-2026-TRAVEL'),
                'status' => 'active',
            ],
        );

        User::firstOrCreate(
            ['email' => 'driver@travel.test'],
            [
                'name' => 'Driver Travel',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'access_code' => Hash::make('DRV-2026-TRAVEL'),
                'status' => 'active',
            ],
        );

        User::firstOrCreate(
            ['email' => 'cs@travel.test'],
            [
                'name' => 'Customer Service',
                'phone' => '081234567893',
                'password' => Hash::make('password'),
                'role' => 'cs',
                'access_code' => Hash::make('CS-2026-TRAVEL'),
                'status' => 'active',
            ],
        );

        User::firstOrCreate(
            ['email' => 'user@travel.test'],
            [
                'name' => 'Andi Pratama',
                'phone' => '081234567894',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ],
        );
    }
}
