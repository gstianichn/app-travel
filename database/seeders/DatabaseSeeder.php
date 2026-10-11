<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@travel.test'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => 'admin_utama',
                'access_code' => Hash::make('ADMIN123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@travel.test'],
            [
                'name' => 'Staff Travel',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'access_code' => Hash::make('STF-2026-TRAVEL'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'cs@travel.test'],
            [
                'name' => 'Customer Service',
                'password' => Hash::make('password'),
                'role' => 'cs',
                'access_code' => Hash::make('CS-2026-TRAVEL'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'driver@travel.test'],
            [
                'name' => 'Driver Travel',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'access_code' => Hash::make('DRV-2026-TRAVEL'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@travel.test'],
            [
                'name' => 'User Travel',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call(TravelSeeder::class);
    }
}
