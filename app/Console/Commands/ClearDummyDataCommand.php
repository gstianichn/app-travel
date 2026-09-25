<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ClearDummyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel:clear-dummy-data {--keep-users : Jangan hapus user non-standar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan semua data dummy/transaksional di setiap role untuk simulasi mandiri yang bersih dari nol.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pembersihan data dummy dan transaksi di semua role...');

        DB::beginTransaction();

        try {
            // 1. Bersihkan tabel transaksional
            $countBP = DB::table('booking_passengers')->count();
            DB::table('booking_passengers')->delete();

            $countTickets = DB::table('tickets')->count();
            DB::table('tickets')->delete();

            $countRefunds = DB::table('refunds')->count();
            DB::table('refunds')->delete();

            $countPayments = DB::table('payments')->count();
            DB::table('payments')->delete();

            if (Schema::hasTable('trip_incidents')) {
                DB::table('trip_incidents')->delete();
            }

            if (Schema::hasTable('trip_reviews')) {
                DB::table('trip_reviews')->delete();
            }

            $countBookings = DB::table('bookings')->count();
            DB::table('bookings')->delete();

            if (Schema::hasTable('saved_passengers')) {
                DB::table('saved_passengers')->delete();
            }

            if (Schema::hasTable('user_vouchers')) {
                DB::table('user_vouchers')->delete();
            }

            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->delete();
            }

            if (Schema::hasTable('login_sessions')) {
                DB::table('login_sessions')->delete();
            }

            // 2. Reset kursi dan kapasitas jadwal
            if (Schema::hasTable('seats')) {
                DB::table('seats')->update(['status' => 'Available']);
            }

            // Kembalikan available_seats pada semua jadwal sesuai kapasitas vehicle
            $vehicles = DB::table('vehicles')->pluck('capacity', 'id');
            foreach ($vehicles as $vehicleId => $capacity) {
                DB::table('schedules')
                    ->where('vehicle_id', $vehicleId)
                    ->update([
                        'available_seats' => $capacity,
                        'status' => 'scheduled',
                        'trip_status' => 'scheduled',
                    ]);
            }

            // Default untuk schedule tanpa vehicle_id
            DB::table('schedules')
                ->whereNull('vehicle_id')
                ->update([
                    'available_seats' => 10,
                    'status' => 'scheduled',
                    'trip_status' => 'scheduled',
                ]);

            // 3. Reset status kendaraan dan supir
            DB::table('vehicles')->update(['status' => 'siap_jalan']);
            DB::table('drivers')->update(['status' => 'standby']);

            // 4. Pastikan 5 akun role utama tersedia dengan password dan kode akses baku
            $roles = [
                [
                    'email' => 'admin@travel.test',
                    'name' => 'Admin Utama',
                    'role' => 'admin_utama',
                    'access_code' => 'ADMIN123',
                ],
                [
                    'email' => 'staff@travel.test',
                    'name' => 'Staff Loket',
                    'role' => 'staff',
                    'access_code' => 'STF-2026-TRAVEL',
                ],
                [
                    'email' => 'cs@travel.test',
                    'name' => 'Customer Service',
                    'role' => 'cs',
                    'access_code' => 'CS-2026-TRAVEL',
                ],
                [
                    'email' => 'driver@travel.test',
                    'name' => 'Driver Travel',
                    'role' => 'driver',
                    'access_code' => 'DRV-2026-TRAVEL',
                ],
                [
                    'email' => 'user@travel.test',
                    'name' => 'User Travel',
                    'role' => 'user',
                    'access_code' => null,
                ],
            ];

            foreach ($roles as $roleData) {
                $user = User::updateOrCreate(
                    ['email' => $roleData['email']],
                    [
                        'name' => $roleData['name'],
                        'password' => Hash::make('password'),
                        'role' => $roleData['role'],
                        'access_code' => $roleData['access_code'] ? Hash::make($roleData['access_code']) : null,
                    ]
                );

                if ($roleData['role'] === 'driver') {
                    Driver::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'phone' => '0812-8899-7711',
                            'status' => 'standby',
                            'license_number' => 'SIM-B1-493497',
                            'license_type' => 'B1 Umum',
                            'license_expired_at' => now()->addYear(),
                        ]
                    );
                }
            }

            // Hapus user pengujian acak (misal factory mailinator) jika ada
            if (! $this->option('keep-users')) {
                User::whereNotIn('email', array_column($roles, 'email'))
                    ->where(function ($q) {
                        $q->where('email', 'like', '%@mailinator.com')
                            ->orWhere('email', 'like', '%@example.%');
                    })
                    ->delete();
            }

            DB::commit();

            $this->info('✓ Pembersihan selesai:');
            $this->line("- Bookings dihapus: {$countBookings}");
            $this->line("- Booking Passengers dihapus: {$countBP}");
            $this->line("- Tickets dihapus: {$countTickets}");
            $this->line("- Payments dihapus: {$countPayments}");
            $this->line("- Refunds dihapus: {$countRefunds}");
            $this->line('- Kursi dan status jadwal telah di-reset ke kapasitas penuh.');
            $this->line('- 5 akun role utama (admin, staff, cs, driver, user) siap digunakan.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal membersihkan data: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
