<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Admin Utama User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.laporantest@travel.test'],
        [
            'name' => 'Admin Utama Laporan Test',
            'phone' => '08123456702',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Regular Customer User
    $this->customer = User::firstOrCreate(
        ['email' => 'customer.laporantest@travel.test'],
        [
            'name' => 'Customer Laporan Test',
            'phone' => '08987654302',
            'role' => 'penumpang',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Lokasi & Rute
    $origin = Location::firstOrCreate(
        ['code' => 'BDG-LAP'],
        ['name' => 'Bandung Central Pool', 'city' => 'Bandung', 'address' => 'Jl. Dipatiukur No. 10', 'type' => 'pool']
    );

    $destination = Location::firstOrCreate(
        ['code' => 'JKT-LAP'],
        ['name' => 'Jakarta Sudirman Drop', 'city' => 'Jakarta', 'address' => 'Jl. Sudirman No. 50', 'type' => 'drop_point']
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $origin->id, 'destination_id' => $destination->id],
        ['price' => 150000, 'duration' => '3 Jam']
    );

    // Armada & Kursi
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 1111 LAP'],
        [
            'name' => 'Toyota HiAce Laporan Unit',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );
    $this->vehicle->ensureSeatsExist();

    // Driver
    $driverUser = User::firstOrCreate(
        ['email' => 'driver.laporantest@travel.test'],
        [
            'name' => 'Sopir Laporan Test',
            'phone' => '087711223302',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $driverUser->id],
        [
            'license_number' => 'SIM-LAPORAN-01',
            'license_type' => 'B1',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Keluarga Sopir',
            'emergency_contact_phone' => '087799887702',
            'status' => 'standby',
        ]
    );

    // Jadwal Perjalanan
    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->toDateString(),
        'departure_time' => '10:00',
        'available_seats' => 9,
        'price' => 150000,
        'status' => 'Scheduled',
    ]);

    // Booking Lunas
    $seat = $this->vehicle->seats()->first();

    $this->booking = Booking::create([
        'booking_code' => 'TRV-LAP-001',
        'user_id' => $this->customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    $this->passenger = BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'seat_id' => $seat->id,
        'name' => 'Budi Laporan',
        'phone' => '08987654302',
    ]);

    Ticket::create([
        'booking_id' => $this->booking->id,
        'ticket_number' => 'TKT-LAP-001',
        'qr_code' => 'QR-LAP-001',
        'status' => 'Issued',
        'issued_at' => now(),
    ]);

    Payment::create([
        'booking_id' => $this->booking->id,
        'order_id' => 'INV-LAP-001',
        'gross_amount' => 150000,
        'payment_method' => 'Transfer Bank BCA',
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    // Refund Disetujui
    Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 25000,
        'amount' => 125000,
        'refund_percentage' => 83.33,
        'reason' => 'Perubahan jadwal dinas',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Laporan Test',
        'status' => 'Disetujui',
        'processed_at' => now(),
        'processed_by' => $this->admin->id,
    ]);
});

// 1. Hak Akses
test('guest and non-admin cannot access report pages or export routes', function () {
    $this->get(route('dashboard.admin.laporan.index'))
        ->assertRedirect(route('login'));

    $this->get(route('dashboard.admin.laporan.export.excel'))
        ->assertRedirect(route('login'));

    $this->get(route('dashboard.admin.laporan.export.pdf'))
        ->assertRedirect(route('login'));

    $this->actingAs($this->customer)
        ->get(route('dashboard.admin.laporan.index'))
        ->assertStatus(403);
});

// 2. Tampilan Index Laporan & Rekonsiliasi Keuangan
test('admin utama can view financial report tab with gross and net calculation', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.index', ['tab' => 'financial']));

    $response->assertStatus(200);
    $response->assertSee('Laporan & Rekonsiliasi Pembukuan');
    $response->assertSee('Rekonsiliasi Keuangan');
    $response->assertSee('TRV-LAP-001');
    $response->assertSee('INV-LAP-001');
    $response->assertSee('Transfer Bank BCA');
    $response->assertSee('Rp 150.000');
});

// 3. Filter Tanggal dan Rute
test('admin utama can filter reports by date range and route', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.index', [
            'tab' => 'financial',
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
            'route_id' => $this->route->id,
        ]));

    $response->assertStatus(200);
    $response->assertSee('TRV-LAP-001');
    $response->assertSee('Bandung Central Pool');
});

// 4. Tab Analisis Keterisian Kursi (Load Factor)
test('admin utama can view seat occupancy and load factor tab', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.index', ['tab' => 'occupancy']));

    $response->assertStatus(200);
    $response->assertSee('Analisis Tingkat Keterisian Kursi (Load Factor)');
    $response->assertSee('Toyota HiAce Laporan Unit');
    $response->assertSee('D 1111 LAP');
    $response->assertSee('10:00');
});

// 5. Tab Manifes Penumpang Terpadu
test('admin utama can view passenger manifest recap tab', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.index', ['tab' => 'manifest']));

    $response->assertStatus(200);
    $response->assertSee('Manifes Lengkap Penumpang Terverifikasi');
    $response->assertSee('Budi Laporan');
    $response->assertSee('TKT-LAP-001');
});

// 6. Tab Rekap Pembatalan & Refund
test('admin utama can view cancellation and refund recap tab', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.index', ['tab' => 'cancellation']));

    $response->assertStatus(200);
    $response->assertSee('Rekapitulasi Pembatalan Tiket', false);
    $response->assertSee('Perubahan jadwal dinas');
    $response->assertSee('Rp 25.000');
    $response->assertSee('Rp 125.000');
});

// 7. Unduh Data Excel / CSV
test('admin utama can export financial report to excel csv stream', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.export.excel', ['tab' => 'financial']));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('content-disposition'))->toContain('Laporan_Financial');
});

// 8. Cetak / Simpan PDF Rekonsiliasi
test('admin utama can access print ready report view', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.laporan.export.pdf', ['tab' => 'financial']));

    $response->assertStatus(200);
    $response->assertSee('TRAVEL TRANS UTAMA');
    $response->assertSee('LAPORAN REKONSILIASI KEUANGAN', false);
    $response->assertSee('Disiapkan Oleh:');
    $response->assertSee('Admin Utama Laporan Test');
});
