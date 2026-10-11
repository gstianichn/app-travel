<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Location;
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
        // 3. MASTER LOKASI / POOL STANDAR
        // -------------------------------------------------------------
        // Bandung Hubs
        $bandungDipatiukur = Location::updateOrCreate(
            ['code' => 'BDO'],
            [
                'name' => 'Pool Dipatiukur (Bandung)',
                'city' => 'Bandung',
                'address' => 'Jl. Dipati Ukur No. 45, Coblong, Bandung',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $bandungPasteur = Location::updateOrCreate(
            ['code' => 'BDO-PST'],
            [
                'name' => 'Pool Pasteur (Bandung)',
                'city' => 'Bandung',
                'address' => 'Jl. Dr. Djunjunan No. 143, Sukajadi, Bandung',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        // Jakarta Hubs
        $jakartaSemanggi = Location::updateOrCreate(
            ['code' => 'JKT'],
            [
                'name' => 'Semanggi (Jakarta)',
                'city' => 'Jakarta',
                'address' => 'Jl. Jend. Sudirman Kav. 50, Semanggi, Jakarta Selatan',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $jakartaTebet = Location::updateOrCreate(
            ['code' => 'JKT-TBT'],
            [
                'name' => 'Tebet (Jakarta)',
                'city' => 'Jakarta',
                'address' => 'Jl. Prof. Dr. Soepomo No. 32, Tebet, Jakarta Selatan',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $jakartaCikini = Location::updateOrCreate(
            ['code' => 'JKT-CKN'],
            [
                'name' => 'Cikini (Jakarta)',
                'city' => 'Jakarta',
                'address' => 'Jl. Cikini Raya No. 18, Menteng, Jakarta Pusat',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        // Jabodetabek Outer Hubs
        $bekasi = Location::updateOrCreate(
            ['code' => 'BKS'],
            [
                'name' => 'Summarecon Bekasi',
                'city' => 'Bekasi',
                'address' => 'Ruko Sinpasa Commercial Blok A-02, Summarecon, Bekasi',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $bogor = Location::updateOrCreate(
            ['code' => 'BGR'],
            [
                'name' => 'Pool Baranangsiang (Bogor)',
                'city' => 'Bogor',
                'address' => 'Jl. Pajajaran No. 8, Baranangsiang, Bogor Timur',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $depok = Location::updateOrCreate(
            ['code' => 'DPK'],
            [
                'name' => 'Margonda (Depok)',
                'city' => 'Depok',
                'address' => 'Jl. Margonda Raya No. 250, Pondok Cina, Beji, Depok',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $airportCgk = Location::updateOrCreate(
            ['code' => 'CGK'],
            [
                'name' => 'Bandara Soekarno-Hatta (CGK)',
                'city' => 'Tangerang',
                'address' => 'Terminal 1, 2, & 3 Kedatangan Bandara Internasional Soekarno-Hatta',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        // Priangan Barat & Pantura Hubs
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

        $sukabumi = Location::updateOrCreate(
            ['code' => 'SMI'],
            [
                'name' => 'Pool Cisaat (Sukabumi)',
                'city' => 'Sukabumi',
                'address' => 'Jl. Raya Cisaat No. 78, Cisaat, Sukabumi',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $cirebon = Location::updateOrCreate(
            ['code' => 'CBN'],
            [
                'name' => 'Pool Kejaksan (Cirebon)',
                'city' => 'Cirebon',
                'address' => 'Jl. Siliwangi No. 101, Kejaksan, Cirebon',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        // Jawa Tengah & Jawa Timur Hubs
        $semarang = Location::updateOrCreate(
            ['code' => 'SMG'],
            [
                'name' => 'Pool Simpang Lima (Semarang)',
                'city' => 'Semarang',
                'address' => 'Jl. Ahmad Yani No. 15, Simpang Lima, Semarang',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $jogja = Location::updateOrCreate(
            ['code' => 'JOG'],
            [
                'name' => 'Pool Jombor (Yogyakarta)',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Magelang KM 6.5, Jombor, Sleman, Yogyakarta',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $solo = Location::updateOrCreate(
            ['code' => 'SOC'],
            [
                'name' => 'Pool Tirtonadi (Solo)',
                'city' => 'Solo',
                'address' => 'Jl. Ahmad Yani No. 200, Gilingan, Solo',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $surabaya = Location::updateOrCreate(
            ['code' => 'SUB'],
            [
                'name' => 'Pool Gubeng (Surabaya)',
                'city' => 'Surabaya',
                'address' => 'Jl. Stasiun Gubeng No. 1, Tambaksari, Surabaya',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        $malang = Location::updateOrCreate(
            ['code' => 'MLG'],
            [
                'name' => 'Pool Arjosari (Malang)',
                'city' => 'Malang',
                'address' => 'Jl. Raden Intan No. 1, Blimbing, Malang',
                'type' => 'pool',
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 4. PEMETAAN RUTE ANTAR-KOTA RESMI (STANDAR KORIDOR TERHUBUNG)
        // -------------------------------------------------------------
        $routeDefinitions = [
            // === KORIDOR 1: BANDUNG - JABODETABEK (PP) ===
            // Bandung <-> Jakarta Semanggi
            ['origin' => $bandungDipatiukur, 'dest' => $jakartaSemanggi, 'price' => 125000, 'duration' => '2 Jam 45 Menit'],
            ['origin' => $jakartaSemanggi, 'dest' => $bandungDipatiukur, 'price' => 125000, 'duration' => '2 Jam 45 Menit'],

            // Bandung <-> Jakarta Tebet
            ['origin' => $bandungPasteur, 'dest' => $jakartaTebet, 'price' => 125000, 'duration' => '2 Jam 30 Menit'],
            ['origin' => $jakartaTebet, 'dest' => $bandungPasteur, 'price' => 125000, 'duration' => '2 Jam 30 Menit'],

            // Bandung <-> Jakarta Cikini
            ['origin' => $bandungDipatiukur, 'dest' => $jakartaCikini, 'price' => 130000, 'duration' => '2 Jam 45 Menit'],
            ['origin' => $jakartaCikini, 'dest' => $bandungDipatiukur, 'price' => 130000, 'duration' => '2 Jam 45 Menit'],

            // Bandung <-> Bekasi (Summarecon)
            ['origin' => $bandungDipatiukur, 'dest' => $bekasi, 'price' => 115000, 'duration' => '2 Jam 15 Menit'],
            ['origin' => $bekasi, 'dest' => $bandungDipatiukur, 'price' => 115000, 'duration' => '2 Jam 15 Menit'],

            // Bandung <-> Bogor (Baranangsiang)
            ['origin' => $bandungDipatiukur, 'dest' => $bogor, 'price' => 110000, 'duration' => '3 Jam 00 Menit'],
            ['origin' => $bogor, 'dest' => $bandungDipatiukur, 'price' => 110000, 'duration' => '3 Jam 00 Menit'],

            // Bandung <-> Depok (Margonda)
            ['origin' => $bandungDipatiukur, 'dest' => $depok, 'price' => 120000, 'duration' => '2 Jam 45 Menit'],
            ['origin' => $depok, 'dest' => $bandungDipatiukur, 'price' => 120000, 'duration' => '2 Jam 45 Menit'],

            // Bandung <-> Bandara Soekarno-Hatta (CGK)
            ['origin' => $bandungPasteur, 'dest' => $airportCgk, 'price' => 175000, 'duration' => '3 Jam 15 Menit'],
            ['origin' => $airportCgk, 'dest' => $bandungPasteur, 'price' => 175000, 'duration' => '3 Jam 15 Menit'],

            // === KORIDOR 2: PRIANGAN BARAT / JALUR PUNCAK - SUKABUMI (PP) ===
            // Bandung <-> Cianjur
            ['origin' => $bandungDipatiukur, 'dest' => $cianjur, 'price' => 60000, 'duration' => '1 Jam 45 Menit'],
            ['origin' => $cianjur, 'dest' => $bandungDipatiukur, 'price' => 60000, 'duration' => '1 Jam 45 Menit'],

            // Cianjur <-> Bogor
            ['origin' => $cianjur, 'dest' => $bogor, 'price' => 55000, 'duration' => '1 Jam 45 Menit'],
            ['origin' => $bogor, 'dest' => $cianjur, 'price' => 55000, 'duration' => '1 Jam 45 Menit'],

            // Sukabumi <-> Bogor
            ['origin' => $sukabumi, 'dest' => $bogor, 'price' => 50000, 'duration' => '1 Jam 30 Menit'],
            ['origin' => $bogor, 'dest' => $sukabumi, 'price' => 50000, 'duration' => '1 Jam 30 Menit'],

            // Sukabumi <-> Jakarta
            ['origin' => $sukabumi, 'dest' => $jakartaSemanggi, 'price' => 95000, 'duration' => '3 Jam 00 Menit'],
            ['origin' => $jakartaSemanggi, 'dest' => $sukabumi, 'price' => 95000, 'duration' => '3 Jam 00 Menit'],

            // Bandung <-> Sukabumi
            ['origin' => $bandungDipatiukur, 'dest' => $sukabumi, 'price' => 75000, 'duration' => '2 Jam 15 Menit'],
            ['origin' => $sukabumi, 'dest' => $bandungDipatiukur, 'price' => 75000, 'duration' => '2 Jam 15 Menit'],

            // === KORIDOR 3: PANTURA / CIREBON (PP) ===
            // Bandung <-> Cirebon
            ['origin' => $bandungDipatiukur, 'dest' => $cirebon, 'price' => 100000, 'duration' => '2 Jam 30 Menit'],
            ['origin' => $cirebon, 'dest' => $bandungDipatiukur, 'price' => 100000, 'duration' => '2 Jam 30 Menit'],

            // Jakarta <-> Cirebon
            ['origin' => $jakartaSemanggi, 'dest' => $cirebon, 'price' => 140000, 'duration' => '3 Jam 30 Menit'],
            ['origin' => $cirebon, 'dest' => $jakartaSemanggi, 'price' => 140000, 'duration' => '3 Jam 30 Menit'],

            // === KORIDOR JAWA TENGAH & JAWA TIMUR (PP) ===
            ['origin' => $semarang, 'dest' => $jogja, 'price' => 85000, 'duration' => '2 Jam 30 Menit'],
            ['origin' => $jogja, 'dest' => $semarang, 'price' => 85000, 'duration' => '2 Jam 30 Menit'],
            ['origin' => $jogja, 'dest' => $solo, 'price' => 45000, 'duration' => '1 Jam 15 Menit'],
            ['origin' => $solo, 'dest' => $jogja, 'price' => 45000, 'duration' => '1 Jam 15 Menit'],
            ['origin' => $semarang, 'dest' => $solo, 'price' => 70000, 'duration' => '1 Jam 45 Menit'],
            ['origin' => $solo, 'dest' => $semarang, 'price' => 70000, 'duration' => '1 Jam 45 Menit'],
            ['origin' => $surabaya, 'dest' => $malang, 'price' => 65000, 'duration' => '1 Jam 45 Menit'],
            ['origin' => $malang, 'dest' => $surabaya, 'price' => 65000, 'duration' => '1 Jam 45 Menit'],
        ];

        $createdRoutes = [];
        foreach ($routeDefinitions as $rDef) {
            $route = Route::updateOrCreate(
                [
                    'origin_id' => $rDef['origin']->id,
                    'destination_id' => $rDef['dest']->id,
                ],
                [
                    'price' => $rDef['price'],
                    'duration' => $rDef['duration'],
                    'is_active' => true,
                ]
            );
            $createdRoutes[] = $route;
        }

        // -------------------------------------------------------------
        // 5. GENERATE JADWAL BERGULIR AKTIF (HARI INI s/d 14 HARI KE DEPAN)
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
