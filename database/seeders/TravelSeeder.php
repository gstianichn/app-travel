<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    public function run(): void
    {
        $jakarta = Location::firstOrCreate(
            ['code' => 'JKT'],
            ['name' => 'Jakarta'],
        );

        $bandung = Location::firstOrCreate(
            ['code' => 'BDO'],
            ['name' => 'Bandung'],
        );

        $cianjur = Location::firstOrCreate(
            ['code' => 'CJR'],
            ['name' => 'Cianjur'],
        );


        $jakartaBandung = Route::firstOrCreate(
            [
                'origin_id' => $jakarta->id,
                'destination_id' => $bandung->id,
            ],
            [
                'price' => 100000,
                'duration' => '3 Jam',
            ],
        );


        $bandungJakarta = Route::firstOrCreate(
            [
                'origin_id' => $bandung->id,
                'destination_id' => $jakarta->id,
            ],
            [
                'price' => 100000,
                'duration' => '3 Jam',
            ],
        );


        $jakartaCianjur = Route::firstOrCreate(
            [
                'origin_id' => $jakarta->id,
                'destination_id' => $cianjur->id,
            ],
            [
                'price' => 85000,
                'duration' => '2 Jam 30 Menit',
            ],
        );


        Schedule::firstOrCreate([
            'route_id' => $jakartaBandung->id,
            'departure_date' => now()->addDay()->toDateString(),
            'departure_time' => '07:00',
            'available_seats' => 8,
        ]);

        Schedule::firstOrCreate([
            'route_id' => $jakartaBandung->id,
            'departure_date' => now()->addDay()->toDateString(),
            'departure_time' => '10:00',
            'available_seats' => 5,
        ]);

        Schedule::firstOrCreate([
            'route_id' => $jakartaBandung->id,
            'departure_date' => now()->addDay()->toDateString(),
            'departure_time' => '14:00',
            'available_seats' => 3,
        ]);


        Schedule::firstOrCreate([
            'route_id' => $bandungJakarta->id,
            'departure_date' => now()->addDay()->toDateString(),
            'departure_time' => '08:00',
            'available_seats' => 7,
        ]);


        Schedule::firstOrCreate([
            'route_id' => $jakartaCianjur->id,
            'departure_date' => now()->addDay()->toDateString(),
            'departure_time' => '09:00',
            'available_seats' => 6,
        ]);
    }
}