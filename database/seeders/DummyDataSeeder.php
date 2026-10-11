<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\OperationalLocation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\SavedPassenger;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TripIncident;
use App\Models\TripReview;
use App\Models\User;
use App\Models\UserVoucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan data armada, rute, dan jadwal dasar sudah tersedia
        if (Schedule::count() === 0) {
            $this->call(TravelSeeder::class);
        }

        DB::transaction(function () {
            $this->seedUsers();
            $this->seedAppSettings();
            $this->seedOperationalLocations();
            $this->seedUserVouchers();
            $this->seedSavedPassengers();
            $this->seedBookingsAndTransactions();
        });
    }

    /**
     * Seed akun pengguna dengan berbagai role untuk pengujian login.
     */
    private function seedUsers(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'admin@travel.test'],
            [
                'name' => 'Admin Utama',
                'phone' => '081211112222',
                'password' => Hash::make('password'),
                'role' => 'admin_utama',
                'status' => 'active',
                'access_code' => Hash::make('ADMIN123'),
            ]
        );

        // 2. Staff Loket Bandung Pasteur
        User::updateOrCreate(
            ['email' => 'staff.bandung@travel.test'],
            [
                'name' => 'Siti Rahmawati (Staff Pasteur)',
                'phone' => '081233334441',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status' => 'active',
                'branch' => 'Pool Bandung (Pasteur)',
                'position' => 'Petugas Loket & Kasir',
                'access_code' => Hash::make('STF-BDO-2026'),
                'permissions' => ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket', 'view_reports'],
            ]
        );

        // 3. Staff Loket Jakarta Semanggi / Default Staff
        User::updateOrCreate(
            ['email' => 'staff@travel.test'],
            [
                'name' => 'Rian Hidayat (Staff Semanggi)',
                'phone' => '081233334442',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status' => 'active',
                'branch' => 'Pool Jakarta (Pusat)',
                'position' => 'Petugas Loket & Kasir',
                'access_code' => Hash::make('STF-2026-TRAVEL'),
                'permissions' => ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket', 'view_reports'],
            ]
        );

        // 4. Customer Service
        User::updateOrCreate(
            ['email' => 'cs@travel.test'],
            [
                'name' => 'Maya Anggraini (Customer Service)',
                'phone' => '081255556666',
                'password' => Hash::make('password'),
                'role' => 'cs',
                'status' => 'active',
                'access_code' => Hash::make('CS-2026-TRAVEL'),
            ]
        );

        // 5. Driver Standar
        User::updateOrCreate(
            ['email' => 'driver@travel.test'],
            [
                'name' => 'Pak Yayan Supriatna',
                'phone' => '081233445561',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'status' => 'active',
                'access_code' => Hash::make('DRV-2026-TRAVEL'),
            ]
        );

        // 6. Pelanggan / User Biasa (Demo Utama)
        User::updateOrCreate(
            ['email' => 'user@travel.test'],
            [
                'name' => 'Andi Pratama',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        // 7. Pelanggan Tambahan untuk simulasi transaksi
        $additionalCustomers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@travel.test', 'phone' => '081288881002'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@travel.test', 'phone' => '081288881003'],
            ['name' => 'Rizky Ramadhan', 'email' => 'rizky@travel.test', 'phone' => '081288881004'],
            ['name' => 'Anisa Putri', 'email' => 'anisa@travel.test', 'phone' => '081288881005'],
        ];

        foreach ($additionalCustomers as $cust) {
            User::updateOrCreate(
                ['email' => $cust['email']],
                [
                    'name' => $cust['name'],
                    'phone' => $cust['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'status' => 'active',
                ]
            );
        }
    }

    /**
     * Seed pengaturan aplikasi umum (AppSetting).
     */
    private function seedAppSettings(): void
    {
        $settings = [
            'app_name' => 'Nusantara Trans',
            'site_name' => 'Nusantara Trans - Travel Minibus & Van',
            'company_legal_name' => 'PT Nusantara Trans Gemilang',
            'contact_phone' => '0812-9988-7766',
            'contact_whatsapp' => '6281299887766',
            'contact_email' => 'cs@travel.test',
            'operational_hours' => '05:00 - 22:00 WIB Setiap Hari',
            'address' => 'Jl. Dipati Ukur No. 45, Coblong, Kota Bandung, Jawa Barat',
            'booking_cancellation_hours' => '1',
            'refund_fee_percentage' => '10',
            'midtrans_mode' => 'sandbox',
            'trip_tagline' => 'Perjalanan Nyaman Antar-Kota bersama Minibus & Van Eksekutif',
        ];

        foreach ($settings as $key => $val) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }
    }

    /**
     * Seed titik lokasi operasional / pool resmi (OperationalLocation).
     */
    private function seedOperationalLocations(): void
    {
        $locations = [
            [
                'name' => 'Pool Bandung - Dipatiukur',
                'address' => 'Jl. Dipati Ukur No. 45, Coblong, Kota Bandung',
                'maps_url' => 'https://maps.google.com/?q=-6.8924,107.6186',
                'coordinates' => '-6.8924, 107.6186',
                'phone' => '0812-3344-001',
            ],
            [
                'name' => 'Pool Bandung - Pasteur',
                'address' => 'Jl. Dr. Djunjunan No. 143, Pasteur, Kota Bandung',
                'maps_url' => 'https://maps.google.com/?q=-6.8943,107.5791',
                'coordinates' => '-6.8943, 107.5791',
                'phone' => '0812-3344-002',
            ],
            [
                'name' => 'Pool Jakarta - Semanggi',
                'address' => 'Jl. Jend. Sudirman Kav. 50, Semanggi, Jakarta Selatan',
                'maps_url' => 'https://maps.google.com/?q=-6.2205,106.8166',
                'coordinates' => '-6.2205, 106.8166',
                'phone' => '0812-3344-003',
            ],
            [
                'name' => 'Pool Bogor - Baranangsiang',
                'address' => 'Jl. Pajajaran No. 8, Baranangsiang, Kota Bogor',
                'maps_url' => 'https://maps.google.com/?q=-6.6022,106.8086',
                'coordinates' => '-6.6022, 106.8086',
                'phone' => '0812-3344-004',
            ],
            [
                'name' => 'Pool Bekasi - Summarecon',
                'address' => 'Ruko Sinpasa Commercial Blok A-02, Summarecon Bekasi',
                'maps_url' => 'https://maps.google.com/?q=-6.2255,106.9995',
                'coordinates' => '-6.2255, 106.9995',
                'phone' => '0812-3344-005',
            ],
            [
                'name' => 'Hub Bandara Soekarno-Hatta (CGK)',
                'address' => 'Terminal 1, 2, & 3 Area Kedatangan, Bandara Soekarno-Hatta',
                'maps_url' => 'https://maps.google.com/?q=-6.1275,106.6537',
                'coordinates' => '-6.1275, 106.6537',
                'phone' => '0812-3344-006',
            ],
        ];

        foreach ($locations as $loc) {
            OperationalLocation::updateOrCreate(
                ['name' => $loc['name']],
                $loc
            );
        }
    }

    /**
     * Seed voucher promo diskon untuk pelanggan (UserVoucher).
     */
    private function seedUserVouchers(): void
    {
        $user = User::where('email', 'user@travel.test')->first();
        if (! $user) {
            return;
        }

        $vouchers = [
            [
                'code' => 'WELCOME2026',
                'title' => 'Diskon Pengguna Baru 20%',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'max_discount' => 30000,
                'min_transaction' => 50000,
                'applicable_route' => null,
                'source' => 'registrasi',
                'status' => 'active',
                'expires_at' => Carbon::now()->addMonths(2),
            ],
            [
                'code' => 'PROMOHEMAT',
                'title' => 'Potongan Langsung Rp 15.000',
                'discount_type' => 'fixed',
                'discount_value' => 15000,
                'max_discount' => 15000,
                'min_transaction' => 100000,
                'applicable_route' => null,
                'source' => 'kampanye_bulanan',
                'status' => 'active',
                'expires_at' => Carbon::now()->addMonth(),
            ],
            [
                'code' => 'WEEKENDSERU',
                'title' => 'Diskon Akhir Pekan 10%',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'max_discount' => 25000,
                'min_transaction' => 80000,
                'applicable_route' => null,
                'source' => 'promo_weekend',
                'status' => 'active',
                'expires_at' => Carbon::now()->addWeeks(3),
            ],
            [
                'code' => 'MUDIKHEMAT',
                'title' => 'Diskon Spesial Liburan Rp 20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'max_discount' => 20000,
                'min_transaction' => 120000,
                'applicable_route' => null,
                'source' => 'seasonal',
                'status' => 'used',
                'used_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addMonth(),
            ],
            [
                'code' => 'FLASHDEAL50',
                'title' => 'Flash Sale 50%',
                'discount_type' => 'percent',
                'discount_value' => 50,
                'max_discount' => 50000,
                'min_transaction' => 100000,
                'applicable_route' => null,
                'source' => 'flash_deal',
                'status' => 'expired',
                'expires_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($vouchers as $vData) {
            UserVoucher::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'code' => $vData['code'],
                ],
                $vData
            );
        }
    }

    /**
     * Seed daftar penumpang tersimpan untuk pengguna utama (SavedPassenger).
     */
    private function seedSavedPassengers(): void
    {
        $user = User::where('email', 'user@travel.test')->first();
        if (! $user) {
            return;
        }

        $passengers = [
            [
                'name' => 'Andi Pratama',
                'nik' => '3273011205930001',
                'phone' => '081234567890',
                'is_primary' => true,
            ],
            [
                'name' => 'Citra Kirana',
                'nik' => '3273014508950002',
                'phone' => '081288881009',
                'is_primary' => false,
            ],
            [
                'name' => 'Kenzo Pratama',
                'nik' => '3273012201190003',
                'phone' => '081234567890',
                'is_primary' => false,
            ],
        ];

        foreach ($passengers as $pData) {
            SavedPassenger::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'nik' => $pData['nik'],
                ],
                $pData
            );
        }
    }

    /**
     * Seed transaksi lengkap (Booking, Passengers, Payments, Tickets, Reviews, Refunds, Incidents).
     */
    private function seedBookingsAndTransactions(): void
    {
        $user = User::where('email', 'user@travel.test')->first();
        $budi = User::where('email', 'budi@travel.test')->first() ?? $user;
        $dewi = User::where('email', 'dewi@travel.test')->first() ?? $user;
        $admin = User::where('role', 'admin_utama')->first() ?? $user;

        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Cari jadwal hari ini dan besok
        $schedulesToday = Schedule::whereDate('departure_date', Carbon::today())->with(['vehicle', 'route'])->get();
        if ($schedulesToday->isEmpty()) {
            $schedulesToday = Schedule::with(['vehicle', 'route'])->take(10)->get();
        }

        $schedulesTomorrow = Schedule::whereDate('departure_date', Carbon::tomorrow())->with(['vehicle', 'route'])->get();
        if ($schedulesTomorrow->isEmpty()) {
            $schedulesTomorrow = Schedule::with(['vehicle', 'route'])->skip(10)->take(10)->get();
        }

        // =====================================================================
        // Skenario 1: Perjalanan Kemarin Selesai (Completed Trip + TripReview)
        // =====================================================================
        $schedulePast = $schedulesToday->first();
        if ($schedulePast && $schedulePast->vehicle) {
            $vehicle = $schedulePast->vehicle;
            $vehicle->ensureSeatsExist();
            $seats = $vehicle->seats()->orderBy('id')->take(2)->get();

            if ($seats->count() >= 2) {
                $unitPrice = $schedulePast->price ?: 125000;
                $totalPrice = $unitPrice * 2;

                $bookingCode1 = 'TRV-'.date('Ymd', strtotime('-1 day')).'-1001';
                $booking1 = Booking::updateOrCreate(
                    ['booking_code' => $bookingCode1],
                    [
                        'user_id' => $user->id,
                        'schedule_id' => $schedulePast->id,
                        'total_passenger' => 2,
                        'subtotal' => $totalPrice,
                        'discount' => 0,
                        'total_price' => $totalPrice,
                        'status' => 'paid',
                        'source' => 'online',
                        'notes' => 'Tolong drop di lobi depan Semanggi.',
                        'created_at' => Carbon::now()->subDays(2),
                        'updated_at' => Carbon::now()->subDay(),
                    ]
                );

                // Penumpang 1
                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking1->id,
                        'seat_id' => $seats[0]->id,
                    ],
                    [
                        'name' => 'Andi Pratama',
                        'phone' => '081234567890',
                        'special_request' => 'Kursi dekat jendela',
                        'boarding_status' => 'boarded',
                        'boarded_at' => Carbon::now()->subDay()->setTime(8, 0),
                        'pickup_point' => $schedulePast->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $schedulePast->route?->destination?->name ?? 'Semanggi (Jakarta)',
                        'baggage_notes' => '1 Koper Medium & 1 Ransel',
                    ]
                );

                // Penumpang 2
                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking1->id,
                        'seat_id' => $seats[1]->id,
                    ],
                    [
                        'name' => 'Citra Kirana',
                        'phone' => '081288881009',
                        'special_request' => 'Duduk berdampingan',
                        'boarding_status' => 'boarded',
                        'boarded_at' => Carbon::now()->subDay()->setTime(8, 0),
                        'pickup_point' => $schedulePast->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $schedulePast->route?->destination?->name ?? 'Semanggi (Jakarta)',
                        'baggage_notes' => '1 Tas Jinjing',
                    ]
                );

                // Pembayaran QRIS Lunas
                Payment::updateOrCreate(
                    ['booking_id' => $booking1->id],
                    [
                        'order_id' => 'PAY-'.$booking1->id.'-'.time(),
                        'transaction_id' => 'TRX-QRIS-992101',
                        'payment_method' => 'qris',
                        'gross_amount' => $totalPrice,
                        'status' => 'paid',
                        'paid_at' => Carbon::now()->subDays(2)->addMinutes(10),
                        'notes' => 'Pembayaran via QRIS Statis BCA Berhasil.',
                        'verified_by' => $admin->id,
                        'verified_at' => Carbon::now()->subDays(2)->addMinutes(10),
                    ]
                );

                // Tiket E-Ticket
                $ticketCode1 = 'TKT-202610-001';
                Ticket::updateOrCreate(
                    ['booking_id' => $booking1->id],
                    [
                        'ticket_number' => $ticketCode1,
                        'qr_code' => $ticketCode1,
                        'status' => 'boarded',
                        'boarded_at' => Carbon::now()->subDay()->setTime(8, 5),
                    ]
                );

                // Trip Review Bintang 5
                TripReview::updateOrCreate(
                    ['booking_id' => $booking1->id],
                    [
                        'user_id' => $user->id,
                        'schedule_id' => $schedulePast->id,
                        'vehicle_id' => $schedulePast->vehicle_id,
                        'driver_id' => $schedulePast->driver_id,
                        'cleanliness_rating' => 5,
                        'driver_rating' => 5,
                        'punctuality_rating' => 5,
                        'overall_rating' => 5.0,
                        'comment' => 'Armada HiAce Premio sangat bersih, AC sejuk, kursinya lega dan empuk. Driver Pak Yayan membawa mobil dengan sangat halus dan tiba di Jakarta tepat waktu!',
                    ]
                );
            }
        }

        // =====================================================================
        // Skenario 2: Perjalanan Hari Ini (Aktif / Confirmed Trip)
        // =====================================================================
        $scheduleToday = $schedulesToday->skip(1)->first() ?? $schedulesToday->first();
        if ($scheduleToday && $scheduleToday->vehicle) {
            $vehicle = $scheduleToday->vehicle;
            $vehicle->ensureSeatsExist();
            $seat = $vehicle->seats()->orderBy('id')->skip(2)->first() ?? $vehicle->seats()->first();

            if ($seat) {
                $price = $scheduleToday->price ?: 125000;
                $bookingCode2 = 'TRV-'.date('Ymd').'-2002';

                $booking2 = Booking::updateOrCreate(
                    ['booking_code' => $bookingCode2],
                    [
                        'user_id' => $user->id,
                        'schedule_id' => $scheduleToday->id,
                        'total_passenger' => 1,
                        'subtotal' => $price,
                        'discount' => 15000,
                        'total_price' => max(0, $price - 15000),
                        'status' => 'paid',
                        'source' => 'online',
                        'notes' => 'Gunakan promo PROMOHEMAT.',
                        'created_at' => Carbon::now()->subHours(4),
                        'updated_at' => Carbon::now()->subHours(3),
                    ]
                );

                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking2->id,
                        'seat_id' => $seat->id,
                    ],
                    [
                        'name' => 'Andi Pratama',
                        'phone' => '081234567890',
                        'special_request' => 'Bawa 1 laptop backpack',
                        'boarding_status' => 'belum_hadir',
                        'pickup_point' => $scheduleToday->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $scheduleToday->route?->destination?->name ?? 'Semanggi (Jakarta)',
                        'baggage_notes' => '1 Ransel Kerja',
                    ]
                );

                Payment::updateOrCreate(
                    ['booking_id' => $booking2->id],
                    [
                        'order_id' => 'PAY-'.$booking2->id.'-'.time(),
                        'transaction_id' => 'TRX-VA-773190',
                        'payment_method' => 'virtual_account',
                        'bank_name' => 'BCA Virtual Account',
                        'gross_amount' => max(0, $price - 15000),
                        'status' => 'paid',
                        'paid_at' => Carbon::now()->subHours(3),
                        'verified_by' => $admin->id,
                        'verified_at' => Carbon::now()->subHours(3),
                    ]
                );

                $ticketCode2 = 'TKT-202610-002';
                Ticket::updateOrCreate(
                    ['booking_id' => $booking2->id],
                    [
                        'ticket_number' => $ticketCode2,
                        'qr_code' => $ticketCode2,
                        'status' => 'valid',
                    ]
                );
            }
        }

        // =====================================================================
        // Skenario 3: Pemesanan Besok (Upcoming Confirmed) oleh Budi Santoso
        // =====================================================================
        $scheduleTomorrow = $schedulesTomorrow->first();
        if ($scheduleTomorrow && $scheduleTomorrow->vehicle) {
            $vehicle = $scheduleTomorrow->vehicle;
            $vehicle->ensureSeatsExist();
            $seatBudi = $vehicle->seats()->orderBy('id')->first();

            if ($seatBudi) {
                $price = $scheduleTomorrow->price ?: 110000;
                $bookingCode3 = 'TRV-'.date('Ymd', strtotime('+1 day')).'-3003';

                $booking3 = Booking::updateOrCreate(
                    ['booking_code' => $bookingCode3],
                    [
                        'user_id' => $budi->id,
                        'schedule_id' => $scheduleTomorrow->id,
                        'total_passenger' => 1,
                        'subtotal' => $price,
                        'discount' => 0,
                        'total_price' => $price,
                        'status' => 'paid',
                        'source' => 'whatsapp',
                        'notes' => 'Pemesanan dibantu staf CS via WhatsApp.',
                        'created_at' => Carbon::now()->subHours(2),
                        'updated_at' => Carbon::now()->subHour(),
                    ]
                );

                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking3->id,
                        'seat_id' => $seatBudi->id,
                    ],
                    [
                        'name' => 'Budi Santoso',
                        'phone' => '081288881002',
                        'boarding_status' => 'belum_hadir',
                        'pickup_point' => $scheduleTomorrow->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $scheduleTomorrow->route?->destination?->name ?? 'Pool Baranangsiang (Bogor)',
                        'baggage_notes' => '1 Koper Kabin',
                    ]
                );

                Payment::updateOrCreate(
                    ['booking_id' => $booking3->id],
                    [
                        'order_id' => 'PAY-'.$booking3->id.'-'.time(),
                        'transaction_id' => 'TRX-TRF-551021',
                        'payment_method' => 'bank_transfer',
                        'bank_name' => 'Mandiri',
                        'account_name' => 'Budi Santoso',
                        'account_number' => '1300099887766',
                        'gross_amount' => $price,
                        'status' => 'paid',
                        'paid_at' => Carbon::now()->subHour(),
                        'verified_by' => $admin->id,
                        'verified_at' => Carbon::now()->subHour(),
                        'notes' => 'Bukti transfer dicek valid.',
                    ]
                );

                $ticketCode3 = 'TKT-202610-003';
                Ticket::updateOrCreate(
                    ['booking_id' => $booking3->id],
                    [
                        'ticket_number' => $ticketCode3,
                        'qr_code' => $ticketCode3,
                        'status' => 'valid',
                    ]
                );
            }
        }

        // =====================================================================
        // Skenario 4: Pemesanan Menunggu Pembayaran (Pending Payment)
        // =====================================================================
        $schedulePending = $schedulesTomorrow->skip(1)->first() ?? $schedulesTomorrow->first();
        if ($schedulePending && $schedulePending->vehicle) {
            $vehicle = $schedulePending->vehicle;
            $vehicle->ensureSeatsExist();
            $seatPending = $vehicle->seats()->orderBy('id')->skip(3)->first() ?? $vehicle->seats()->last();

            if ($seatPending) {
                $price = $schedulePending->price ?: 125000;
                $bookingCode4 = 'TRV-'.date('Ymd', strtotime('+1 day')).'-4004';

                $booking4 = Booking::updateOrCreate(
                    ['booking_code' => $bookingCode4],
                    [
                        'user_id' => $dewi->id,
                        'schedule_id' => $schedulePending->id,
                        'total_passenger' => 1,
                        'subtotal' => $price,
                        'discount' => 0,
                        'total_price' => $price,
                        'status' => 'pending',
                        'source' => 'online',
                        'expired_at' => Carbon::now()->addHour(),
                        'created_at' => Carbon::now()->subMinutes(15),
                        'updated_at' => Carbon::now()->subMinutes(15),
                    ]
                );

                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking4->id,
                        'seat_id' => $seatPending->id,
                    ],
                    [
                        'name' => 'Dewi Lestari',
                        'phone' => '081288881003',
                        'boarding_status' => 'belum_hadir',
                        'pickup_point' => $schedulePending->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $schedulePending->route?->destination?->name ?? 'Semanggi (Jakarta)',
                        'baggage_notes' => '1 Ransel',
                    ]
                );

                Payment::updateOrCreate(
                    ['booking_id' => $booking4->id],
                    [
                        'order_id' => 'PAY-'.$booking4->id.'-'.time(),
                        'payment_method' => 'qris',
                        'gross_amount' => $price,
                        'status' => 'pending',
                        'expired_at' => Carbon::now()->addHour(),
                        'notes' => 'Menunggu pemindaian QRIS oleh pembeli.',
                    ]
                );
            }
        }

        // =====================================================================
        // Skenario 5: Pemesanan Dibatalkan & Disetujui Refund (Refund Approval)
        // =====================================================================
        $scheduleRefund = $schedulesToday->last();
        if ($scheduleRefund && $scheduleRefund->vehicle) {
            $vehicle = $scheduleRefund->vehicle;
            $vehicle->ensureSeatsExist();
            $seatRefund = $vehicle->seats()->orderBy('id')->skip(4)->first() ?? $vehicle->seats()->first();

            if ($seatRefund) {
                $price = $scheduleRefund->price ?: 125000;
                $fee = round($price * 0.10);
                $netRefund = $price - $fee;
                $bookingCode5 = 'TRV-'.date('Ymd', strtotime('-3 days')).'-5005';

                $booking5 = Booking::updateOrCreate(
                    ['booking_code' => $bookingCode5],
                    [
                        'user_id' => $user->id,
                        'schedule_id' => $scheduleRefund->id,
                        'total_passenger' => 1,
                        'subtotal' => $price,
                        'discount' => 0,
                        'total_price' => $price,
                        'status' => 'cancelled',
                        'source' => 'online',
                        'notes' => 'Dibatalkan oleh pelanggan: ada jadwal rapat dadakan.',
                        'created_at' => Carbon::now()->subDays(3),
                        'updated_at' => Carbon::now()->subDays(2),
                    ]
                );

                BookingPassenger::updateOrCreate(
                    [
                        'booking_id' => $booking5->id,
                        'seat_id' => $seatRefund->id,
                    ],
                    [
                        'name' => 'Andi Pratama',
                        'phone' => '081234567890',
                        'boarding_status' => 'batal',
                        'pickup_point' => $scheduleRefund->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
                        'dropoff_point' => $scheduleRefund->route?->destination?->name ?? 'Semanggi (Jakarta)',
                        'baggage_notes' => '1 Ransel',
                    ]
                );

                Payment::updateOrCreate(
                    ['booking_id' => $booking5->id],
                    [
                        'order_id' => 'PAY-'.$booking5->id.'-'.time(),
                        'transaction_id' => 'TRX-VA-881299',
                        'payment_method' => 'virtual_account',
                        'bank_name' => 'BCA Virtual Account',
                        'gross_amount' => $price,
                        'status' => 'paid',
                        'paid_at' => Carbon::now()->subDays(3),
                        'verified_by' => $admin->id,
                        'verified_at' => Carbon::now()->subDays(3),
                    ]
                );

                Ticket::updateOrCreate(
                    ['booking_id' => $booking5->id],
                    [
                        'ticket_number' => 'TKT-202610-005',
                        'qr_code' => 'TKT-202610-005',
                        'status' => 'cancelled',
                    ]
                );

                Refund::updateOrCreate(
                    ['booking_id' => $booking5->id],
                    [
                        'user_id' => $user->id,
                        'original_amount' => $price,
                        'cancellation_fee' => $fee,
                        'amount' => $netRefund,
                        'refund_percentage' => 90,
                        'reason' => 'Ada keperluan mendesak di kantor sehingga tiket tidak dapat digunakan.',
                        'bank_name' => 'BCA',
                        'account_number' => '5420199201',
                        'account_holder' => 'Andi Pratama',
                        'status' => 'disetujui',
                        'admin_note' => 'Permohonan refund disetujui. Dana sebesar Rp '.number_format($netRefund, 0, ',', '.').' berhasil ditransfer balik ke rekening BCA pelanggan.',
                        'processed_at' => Carbon::now()->subDays(2),
                        'processed_by' => $admin->id,
                    ]
                );
            }
        }

        // =====================================================================
        // Skenario 6: Contoh Laporan Insiden Perjalanan (Monitoring Operasional)
        // =====================================================================
        $driver = Driver::first();
        if ($driver && $schedulePast) {
            TripIncident::updateOrCreate(
                [
                    'schedule_id' => $schedulePast->id,
                    'category' => 'ban_bocor',
                ],
                [
                    'driver_id' => $driver->id,
                    'severity' => 'sedang',
                    'description' => 'Ban belakang kiri tertusuk serpihan logam saat melintas di bahu jalan Tol Cipularang KM 88.',
                    'location_name' => 'Tol Purbaleunyi KM 88 (Arah Bandung)',
                    'latitude' => -6.6500,
                    'longitude' => 107.4100,
                    'status' => 'resolved',
                    'emergency_dispatched' => false,
                    'reported_at' => Carbon::now()->subDay()->setTime(9, 30),
                    'resolved_at' => Carbon::now()->subDay()->setTime(9, 55),
                    'resolution_notes' => 'Penggantian ban serep telah selesai dilakukan dalam 25 menit. Seluruh penumpang aman dan armada kembali melaju normal.',
                ]
            );
        }
    }
}
