<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

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
                'password' => 'password',
                'role' => 'admin',
            ],
        );

        User::firstOrCreate(
            ['email' => 'staff@travel.test'],
            [
                'name' => 'Admin Biasa',
                'phone' => '081234567891',
                'password' => 'password',
                'role' => 'admin_staff',
            ],
        );

        User::firstOrCreate(
            ['email' => 'driver@travel.test'],
            [
                'name' => 'Driver Travel',
                'phone' => '081234567892',
                'password' => 'password',
                'role' => 'driver',
            ],
        );

        User::firstOrCreate(
            ['email' => 'cs@travel.test'],
            [
                'name' => 'Customer Service',
                'phone' => '081234567893',
                'password' => 'password',
                'role' => 'customer_service',
            ],
        );

        User::firstOrCreate(
            ['email' => 'user@travel.test'],
            [
                'name' => 'Andi Pratama',
                'phone' => '081234567894',
                'password' => 'password',
                'role' => 'user',
            ],
        );
    }
}
