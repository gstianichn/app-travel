<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Master data for pickup points and intercity routes, so a fresh `php artisan migrate`
 * already has departure and arrival points to search with.
 *
 * Places are real terminals, stations and areas; addresses stop at street and district level.
 * Distances are approximate road distances. Weekend and holiday prices are left empty
 * so they fall back to the weekday price until an admin sets them.
 */
return new class extends Migration
{
    /**
     * @var array<int, array{code: string, name: string, city: string, address: string}>
     */
    private array $locations = [
        ['code' => 'BDO', 'name' => 'Pool Dipatiukur (Bandung)', 'city' => 'Bandung', 'address' => 'Jl. Dipati Ukur, Coblong, Bandung'],
        ['code' => 'BDO-PST', 'name' => 'Pool Pasteur (Bandung)', 'city' => 'Bandung', 'address' => 'Jl. Dr. Djunjunan, Sukajadi, Bandung'],
        ['code' => 'JKT', 'name' => 'Semanggi (Jakarta)', 'city' => 'Jakarta', 'address' => 'Jl. Jend. Sudirman, Setiabudi, Jakarta Selatan'],
        ['code' => 'JKT-TBT', 'name' => 'Tebet (Jakarta)', 'city' => 'Jakarta', 'address' => 'Jl. Prof. Dr. Soepomo, Tebet, Jakarta Selatan'],
        ['code' => 'JKT-CKN', 'name' => 'Cikini (Jakarta)', 'city' => 'Jakarta', 'address' => 'Jl. Cikini Raya, Menteng, Jakarta Pusat'],
        ['code' => 'BKS', 'name' => 'Summarecon Bekasi', 'city' => 'Bekasi', 'address' => 'Kawasan Summarecon, Bekasi Utara, Bekasi'],
        ['code' => 'BGR', 'name' => 'Pool Baranangsiang (Bogor)', 'city' => 'Bogor', 'address' => 'Jl. Raya Pajajaran, Baranangsiang, Bogor Timur'],
        ['code' => 'DPK', 'name' => 'Margonda (Depok)', 'city' => 'Depok', 'address' => 'Jl. Margonda Raya, Beji, Depok'],
        ['code' => 'CGK', 'name' => 'Bandara Soekarno-Hatta (CGK)', 'city' => 'Tangerang', 'address' => 'Area kedatangan Bandara Soekarno-Hatta, Tangerang'],
        ['code' => 'CJR', 'name' => 'Terminal Pasirhayam (Cianjur)', 'city' => 'Cianjur', 'address' => 'Pasirhayam, Cilaku, Cianjur'],
        ['code' => 'SMI', 'name' => 'Pool Cisaat (Sukabumi)', 'city' => 'Sukabumi', 'address' => 'Jl. Raya Cisaat, Cisaat, Sukabumi'],
        ['code' => 'CBN', 'name' => 'Pool Kejaksan (Cirebon)', 'city' => 'Cirebon', 'address' => 'Jl. Siliwangi, Kejaksan, Cirebon'],
        ['code' => 'SMG', 'name' => 'Pool Simpang Lima (Semarang)', 'city' => 'Semarang', 'address' => 'Kawasan Simpang Lima, Semarang Tengah, Semarang'],
        ['code' => 'JOG', 'name' => 'Pool Jombor (Yogyakarta)', 'city' => 'Yogyakarta', 'address' => 'Jl. Magelang, Jombor, Sleman'],
        ['code' => 'SOC', 'name' => 'Pool Tirtonadi (Solo)', 'city' => 'Solo', 'address' => 'Jl. Ahmad Yani, Gilingan, Banjarsari, Surakarta'],
        ['code' => 'SUB', 'name' => 'Pool Gubeng (Surabaya)', 'city' => 'Surabaya', 'address' => 'Area Stasiun Gubeng, Gubeng, Surabaya'],
        ['code' => 'MLG', 'name' => 'Pool Arjosari (Malang)', 'city' => 'Malang', 'address' => 'Jl. Raden Intan, Blimbing, Malang'],
    ];

    /**
     * Each entry is served in both directions.
     *
     * @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: int}>
     */
    private array $routes = [
        ['BDO', 'JKT', 125000, '2 Jam 45 Menit', 150],
        ['BDO-PST', 'JKT-TBT', 125000, '2 Jam 30 Menit', 145],
        ['BDO', 'JKT-CKN', 130000, '2 Jam 45 Menit', 155],
        ['BDO', 'BKS', 115000, '2 Jam 15 Menit', 130],
        ['BDO', 'BGR', 110000, '3 Jam 00 Menit', 125],
        ['BDO', 'DPK', 120000, '2 Jam 45 Menit', 160],
        ['BDO-PST', 'CGK', 175000, '3 Jam 15 Menit', 175],
        ['BDO', 'CJR', 60000, '1 Jam 45 Menit', 65],
        ['CJR', 'BGR', 55000, '1 Jam 45 Menit', 60],
        ['SMI', 'BGR', 50000, '1 Jam 30 Menit', 60],
        ['SMI', 'JKT', 95000, '3 Jam 00 Menit', 120],
        ['BDO', 'SMI', 75000, '2 Jam 15 Menit', 95],
        ['BDO', 'CBN', 100000, '2 Jam 30 Menit', 130],
        ['JKT', 'CBN', 140000, '3 Jam 30 Menit', 220],
        ['SMG', 'JOG', 85000, '2 Jam 30 Menit', 120],
        ['JOG', 'SOC', 45000, '1 Jam 15 Menit', 65],
        ['SMG', 'SOC', 70000, '1 Jam 45 Menit', 100],
        ['SUB', 'MLG', 65000, '1 Jam 45 Menit', 90],
    ];

    public function up(): void
    {
        // Tests build their own fixtures; master data here would collide with them.
        if (app()->environment('testing')) {
            return;
        }

        $this->insertMasterData();
    }

    /**
     * Insert or refresh locations and routes. Safe to run more than once.
     */
    public function insertMasterData(): void
    {
        $now = now();

        foreach ($this->locations as $location) {
            DB::table('locations')->updateOrInsert(
                ['code' => $location['code']],
                $location + ['type' => 'pool', 'is_active' => true, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        $locationIds = DB::table('locations')->pluck('id', 'code');

        foreach ($this->routes as [$from, $to, $price, $duration, $distance]) {
            foreach ([[$from, $to], [$to, $from]] as [$origin, $destination]) {
                DB::table('routes')->updateOrInsert(
                    ['origin_id' => $locationIds[$origin], 'destination_id' => $locationIds[$destination]],
                    [
                        'price' => $price,
                        'duration' => $duration,
                        'distance_km' => $distance,
                        'is_active' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ],
                );
            }
        }
    }

    /**
     * Master data stays in place on rollback: bookings and schedules may already point to it.
     */
    public function down(): void
    {
        //
    }
};
