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

        $vehicle1->ensureSeatsExist();
        $vehicle2->ensureSeatsExist();
        $vehicle3->ensureSeatsExist();
        $vehicle4->ensureSeatsExist();

        $jakarta = Location::updateOrCreate(
            ['code' => 'JKT'],
            [
                'name' => 'Semanggi (Jakarta)',
                'city' => 'Jakarta',
                'address' => 'Jl. Jend. Sudirman Kav. 50, Semanggi, Jakarta Selatan',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $bandung = Location::updateOrCreate(
            ['code' => 'BDO'],
            [
                'name' => 'Pool Dipatiukur (Bandung)',
                'city' => 'Bandung',
                'address' => 'Jl. Dipati Ukur No. 45, Lebakgede, Coblong, Bandung',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $cianjur = Location::updateOrCreate(
            ['code' => 'CJR'],
            [
                'name' => 'Terminal Pasirhayam (Cianjur)',
                'city' => 'Cianjur',
                'address' => 'Jl. Raya Cibeber, Pasirhayam, Cianjur',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $jakartaBandung = Route::firstOrCreate(
            [
                'origin_id' => $jakarta->id,
                'destination_id' => $bandung->id,
            ],
            [
                'price' => 125000,
                'duration' => '2 Jam 45 Menit',
            ]
        );

        $bandungJakarta = Route::firstOrCreate(
            [
                'origin_id' => $bandung->id,
                'destination_id' => $jakarta->id,
            ],
            [
                'price' => 125000,
                'duration' => '2 Jam 45 Menit',
            ]
        );

        $jakartaCianjur = Route::firstOrCreate(
            [
                'origin_id' => $jakarta->id,
                'destination_id' => $cianjur->id,
            ],
            [
                'price' => 85000,
                'duration' => '2 Jam 30 Menit',
            ]
        );

        $dates = [now()->toDateString(), now()->addDay()->toDateString()];

        foreach ($dates as $date) {
            // Bandung -> Jakarta
            Schedule::updateOrCreate(
                [
                    'route_id' => $bandungJakarta->id,
                    'departure_date' => $date,
                    'departure_time' => '08:00',
                ],
                [
                    'vehicle_id' => $vehicle1->id,
                    'available_seats' => 8,
                    'price' => 125000,
                    'status' => 'Scheduled',
                ]
            );

            Schedule::updateOrCreate(
                [
                    'route_id' => $bandungJakarta->id,
                    'departure_date' => $date,
                    'departure_time' => '14:30',
                ],
                [
                    'vehicle_id' => $vehicle4->id,
                    'available_seats' => 9,
                    'price' => 125000,
                    'status' => 'Scheduled',
                ]
            );

            // Jakarta -> Bandung
            Schedule::updateOrCreate(
                [
                    'route_id' => $jakartaBandung->id,
                    'departure_date' => $date,
                    'departure_time' => '07:00',
                ],
                [
                    'vehicle_id' => $vehicle1->id,
                    'available_seats' => 7,
                    'price' => 125000,
                    'status' => 'Scheduled',
                ]
            );

            Schedule::updateOrCreate(
                [
                    'route_id' => $jakartaBandung->id,
                    'departure_date' => $date,
                    'departure_time' => '10:00',
                ],
                [
                    'vehicle_id' => $vehicle2->id,
                    'available_seats' => 12,
                    'price' => 110000,
                    'status' => 'Scheduled',
                ]
            );

            Schedule::updateOrCreate(
                [
                    'route_id' => $jakartaBandung->id,
                    'departure_date' => $date,
                    'departure_time' => '14:00',
                ],
                [
                    'vehicle_id' => $vehicle4->id,
                    'available_seats' => 6,
                    'price' => 125000,
                    'status' => 'Scheduled',
                ]
            );

            // Jakarta -> Cianjur
            Schedule::updateOrCreate(
                [
                    'route_id' => $jakartaCianjur->id,
                    'departure_date' => $date,
                    'departure_time' => '09:00',
                ],
                [
                    'vehicle_id' => $vehicle2->id,
                    'available_seats' => 10,
                    'price' => 85000,
                    'status' => 'Scheduled',
                ]
            );
        }
    }
}
