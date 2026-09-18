<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    public function run(): void
    {
        $vehicle1 = Vehicle::updateOrCreate(
            ['plate_number' => 'B 1234 TAA'],
            [
                'name' => 'HiAce Premio Eksekutif 01',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21PREMIO202301',
                'engine_number' => '1GD-FTV-88210',
                'stnk_tax_due_date' => now()->addMonths(8)->toDateString(),
                'kir_expiry_date' => now()->addMonths(5)->toDateString(),
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat (Kursi Rebah)', 'Port USB Charger', 'Wi-Fi Cepat', 'Air Mineral Gratis'],
                'notes' => 'Armada utama rute eksekutif Jakarta - Bandung. Servis berkala tuntas.',
            ]
        );

        $vehicle2 = Vehicle::updateOrCreate(
            ['plate_number' => 'B 5678 BDO'],
            [
                'name' => 'HiAce Commuter Shuttle 02',
                'type' => 'Toyota HiAce Commuter',
                'capacity' => 14,
                'manufacture_year' => 2022,
                'chassis_number' => 'MHF21COMMUTER202202',
                'engine_number' => '2KD-FTV-77319',
                'stnk_tax_due_date' => now()->addDays(18)->toDateString(), // Mendekati jatuh tempo
                'kir_expiry_date' => now()->addMonths(4)->toDateString(),
                'status' => 'beroperasi',
                'facilities' => ['AC Dingin', 'Audio & Musik', 'Bagasi Luas'],
                'notes' => 'Sedang aktif melayani rute Bandung - Jakarta PP.',
            ]
        );

        $vehicle3 = Vehicle::updateOrCreate(
            ['plate_number' => 'D 7890 XYZ'],
            [
                'name' => 'Isuzu Elf Long Pariwisata 03',
                'type' => 'Isuzu Elf Long',
                'capacity' => 19,
                'manufacture_year' => 2021,
                'chassis_number' => 'NKR71ELF202103',
                'engine_number' => '4HG1-T-55102',
                'stnk_tax_due_date' => now()->addMonths(6)->toDateString(),
                'kir_expiry_date' => now()->subDays(4)->toDateString(), // Kedaluwarsa uji KIR
                'status' => 'maintenance',
                'facilities' => ['AC Dingin', 'Reclining Seat (Kursi Rebah)', 'Android TV / Hiburan', 'Port USB Charger'],
                'notes' => 'Masuk bengkel rekanan untuk perbaikan suspensi & persiapan perpanjangan uji KIR Dishub.',
            ]
        );

        $vehicle4 = Vehicle::updateOrCreate(
            ['plate_number' => 'B 9012 JKT'],
            [
                'name' => 'HiAce Premio Luxury 04',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2024,
                'chassis_number' => 'MHF21PREMIO202404',
                'engine_number' => '1GD-FTV-99401',
                'stnk_tax_due_date' => now()->addMonths(11)->toDateString(),
                'kir_expiry_date' => now()->addMonths(10)->toDateString(),
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat (Kursi Rebah)', 'Port USB Charger', 'Wi-Fi Cepat', 'Air Mineral Gratis', 'Lampu Baca Pribadi'],
                'notes' => 'Armada baru dengan interior mewah capitano seat.',
            ]
        );

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

        Schedule::firstOrCreate(
            [
                'route_id' => $jakartaBandung->id,
                'departure_date' => now()->addDay()->toDateString(),
                'departure_time' => '07:00',
            ],
            [
                'vehicle_id' => $vehicle1->id,
                'available_seats' => 8,
                'status' => 'Scheduled',
            ]
        );

        Schedule::firstOrCreate(
            [
                'route_id' => $jakartaBandung->id,
                'departure_date' => now()->addDay()->toDateString(),
                'departure_time' => '10:00',
            ],
            [
                'vehicle_id' => $vehicle2->id,
                'available_seats' => 5,
                'status' => 'Scheduled',
            ]
        );

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
