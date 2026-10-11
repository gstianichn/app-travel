<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TravelSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. DATA ARMADA MINIBUS & VAN
        // -------------------------------------------------------------
        $vehiclesData = [
            [
                'plate_number' => 'B 1234 TAA',
                'name' => 'HiAce Premio Eksekutif 01',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21PREMIO202301',
                'engine_number' => '1GD-FTV-88210',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat (Kursi Rebah)', 'Port USB Charger', 'Wi-Fi Cepat', 'Air Mineral Gratis'],
                'notes' => 'Armada utama koridor Bandung - Jakarta Semanggi.',
            ],
            [
                'plate_number' => 'B 5678 BDO',
                'name' => 'HiAce Commuter Shuttle 02',
                'type' => 'Toyota HiAce Commuter',
                'capacity' => 14,
                'manufacture_year' => 2022,
                'chassis_number' => 'MHF21COMMUTER202202',
                'engine_number' => '2KD-FTV-77319',
                'status' => 'beroperasi',
                'facilities' => ['AC Dingin', 'Audio & Musik', 'Bagasi Luas', 'Port USB Charger'],
                'notes' => 'Armada reguler Bandung - Bekasi / Depok.',
            ],
            [
                'plate_number' => 'D 7890 XYZ',
                'name' => 'Isuzu Elf Long Pariwisata 03',
                'type' => 'Isuzu Elf Long',
                'capacity' => 19,
                'manufacture_year' => 2023,
                'chassis_number' => 'NKR71ELF202303',
                'engine_number' => '4HG1-T-55102',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Android TV', 'Port USB Charger'],
                'notes' => 'Armada rombongan koridor Cirebon.',
            ],
            [
                'plate_number' => 'B 9012 JKT',
                'name' => 'HiAce Premio Luxury 04',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2024,
                'chassis_number' => 'MHF21PREMIO202404',
                'engine_number' => '1GD-FTV-99401',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat (Capitano)', 'Port USB Charger', 'Wi-Fi Cepat', 'Air Mineral Gratis', 'Lampu Baca Pribadi'],
                'notes' => 'Armada eksekutif Bandung - Bandara Soekarno Hatta.',
            ],
            [
                'plate_number' => 'D 1102 BDG',
                'name' => 'HiAce Premio Shuttle 05',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21PREMIO202305',
                'engine_number' => '1GD-FTV-66211',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger', 'Wi-Fi Cepat'],
                'notes' => 'Armada Priangan Barat Bandung - Cianjur - Sukabumi.',
            ],
            [
                'plate_number' => 'F 2021 SMI',
                'name' => 'HiAce Premio Shuttle 06',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2024,
                'chassis_number' => 'MHF21PREMIO202406',
                'engine_number' => '1GD-FTV-33109',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger'],
                'notes' => 'Armada Sukabumi - Bogor / Jakarta.',
            ],
            [
                'plate_number' => 'E 7719 CBN',
                'name' => 'HiAce Commuter Pantura 07',
                'type' => 'Toyota HiAce Commuter',
                'capacity' => 14,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21COMMUTER202307',
                'engine_number' => '2KD-FTV-55120',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger'],
                'notes' => 'Armada Pantura Bandung / Jakarta - Cirebon.',
            ],
            [
                'plate_number' => 'H 8812 SMG',
                'name' => 'HiAce Premio Joglosemar 08',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21PREMIO202308',
                'engine_number' => '1GD-FTV-44111',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger', 'Wi-Fi Cepat'],
                'notes' => 'Koridor Semarang - Jogja - Solo.',
            ],
            [
                'plate_number' => 'L 1928 SBY',
                'name' => 'HiAce Premio Jawa Timur 09',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2023,
                'chassis_number' => 'MHF21PREMIO202309',
                'engine_number' => '1GD-FTV-99120',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger', 'Air Mineral Gratis'],
                'notes' => 'Koridor Surabaya - Malang PP.',
            ],
            [
                'plate_number' => 'F 1045 BGR',
                'name' => 'HiAce Premio Shuttle 10',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'manufacture_year' => 2024,
                'chassis_number' => 'MHF21PREMIO202410',
                'engine_number' => '1GD-FTV-77112',
                'status' => 'siap_jalan',
                'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger', 'Wi-Fi Cepat'],
                'notes' => 'Koridor Cianjur - Bogor PP.',
            ],
        ];

        $vehicles = [];
        foreach ($vehiclesData as $vData) {
            $vehicle = Vehicle::updateOrCreate(
                ['plate_number' => $vData['plate_number']],
                array_merge($vData, [
                    'stnk_tax_due_date' => now()->addMonths(8)->toDateString(),
                    'kir_expiry_date' => now()->addMonths(6)->toDateString(),
                ])
            );
            $vehicle->ensureSeatsExist();
            $vehicles[] = $vehicle;
        }

        // -------------------------------------------------------------
        // 2. DATA DRIVER PENGEMUDI
        // -------------------------------------------------------------
        $driversData = [
            ['name' => 'Pak Yayan Supriatna', 'email' => 'driver.yayan@travel.test', 'phone' => '0812-3344-5561'],
            ['name' => 'Pak Bambang Hermawan', 'email' => 'driver.bambang@travel.test', 'phone' => '0812-3344-5562'],
            ['name' => 'Pak Joko Widodo', 'email' => 'driver.joko@travel.test', 'phone' => '0812-3344-5563'],
            ['name' => 'Pak Asep Suryana', 'email' => 'driver.asep@travel.test', 'phone' => '0812-3344-5564'],
            ['name' => 'Pak Hendra Wijaya', 'email' => 'driver.hendra@travel.test', 'phone' => '0812-3344-5565'],
            ['name' => 'Pak Slamet Riyadi', 'email' => 'driver.slamet@travel.test', 'phone' => '0812-3344-5566'],
            ['name' => 'Pak Budi Santoso', 'email' => 'driver.budi@travel.test', 'phone' => '0812-3344-5567'],
            ['name' => 'Pak Agus Kurniawan', 'email' => 'driver.agus@travel.test', 'phone' => '0812-3344-5568'],
        ];

        $drivers = [];
        foreach ($driversData as $dData) {
            $user = User::updateOrCreate(
                ['email' => $dData['email']],
                [
                    'name' => $dData['name'],
                    'phone' => $dData['phone'],
                    'role' => 'driver',
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'access_code' => Hash::make('DRV-2026-TRAVEL'),
                ]
            );

            $driver = Driver::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $dData['phone'],
                    'license_number' => 'SIM-B1-'.rand(10000000, 99999999),
                    'license_type' => 'B1 Umum',
                    'license_expired_at' => now()->addYears(3)->toDateString(),
                    'status' => 'standby',
                    'notes' => 'Pengemudi profesional berpengalaman.',
                ]
            );
            $drivers[] = $driver;
        }

        // -------------------------------------------------------------
        // 3. LOKASI & RUTE: sumbernya migration master data yang sama
        // -------------------------------------------------------------
        (require database_path('migrations/2026_10_11_064005_seed_master_locations_and_routes.php'))->insertMasterData();

        $createdRoutes = Route::query()->where('is_active', true)->orderBy('id')->get()->all();

        // -------------------------------------------------------------
        // 4. GENERATE JADWAL BERGULIR AKTIF (HARI INI s/d 14 HARI KE DEPAN)
        // -------------------------------------------------------------
        $daysCount = 14;
        $totalVehicles = count($vehicles);
        $totalDrivers = count($drivers);

        DB::transaction(function () use ($daysCount, $createdRoutes, $totalVehicles, $totalDrivers, $vehicles, $drivers) {
            for ($d = 0; $d <= $daysCount; $d++) {
                $date = Carbon::today()->addDays($d)->toDateString();

                foreach ($createdRoutes as $routeIndex => $route) {
                    // Pola jam keberangkatan reguler per 1-2 jam sekali pada koridor utama
                    $timesForRoute = match ($routeIndex % 4) {
                        0 => ['06:00', '08:00', '10:00', '13:00', '15:30', '18:00', '20:30'],
                        1 => ['06:30', '09:00', '11:30', '14:00', '16:30', '19:00', '21:00'],
                        2 => ['07:00', '09:30', '12:00', '14:30', '17:00', '19:30'],
                        default => ['07:30', '10:30', '13:30', '16:00', '18:30', '20:00'],
                    };

                    foreach ($timesForRoute as $tIndex => $time) {
                        $vIndex = ($routeIndex + $tIndex + $d) % $totalVehicles;
                        $dIndex = ($routeIndex + $tIndex + $d) % $totalDrivers;

                        $vehicle = $vehicles[$vIndex];
                        $driver = $drivers[$dIndex];

                        $seatCap = $vehicle->capacity ?: 10;
                        $availableSeats = max(4, $seatCap - (($tIndex * 2 + $d) % 3));

                        Schedule::updateOrCreate(
                            [
                                'route_id' => $route->id,
                                'departure_date' => $date,
                                'departure_time' => $time,
                            ],
                            [
                                'vehicle_id' => $vehicle->id,
                                'driver_id' => $driver->id,
                                'available_seats' => $availableSeats,
                                'price' => $route->price,
                                'status' => 'Scheduled',
                                'trip_status' => 'scheduled',
                                'notes' => 'Keberangkatan reguler armada '.$vehicle->name.' dipandu oleh '.$driver->user->name.'.',
                            ]
                        );
                    }
                }
            }
        });
    }
}
