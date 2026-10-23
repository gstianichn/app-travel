<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Admin Utama User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.pemesanantest@travel.test'],
        [
            'name' => 'Admin Utama Pemesanan Test',
            'phone' => '08123456799',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Master Lokasi & Rute
    $origin = Location::firstOrCreate(
        ['code' => 'BDG-TEST'],
        ['name' => 'Bandung Test Pool', 'city' => 'Bandung', 'address' => 'Jl. Dipatiukur No. 1']
    );

    $destination = Location::firstOrCreate(
        ['code' => 'JKT-TEST'],
        ['name' => 'Jakarta Test Pool', 'city' => 'Jakarta', 'address' => 'Jl. Tebet Raya No. 2']
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $origin->id, 'destination_id' => $destination->id],
        ['price' => 150000, 'duration' => '3 jam']
    );

    // Armada & Kursi
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 7777 PMS'],
        [
            'name' => 'HiAce Premio Test Pemesanan',
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
        ['email' => 'driver.pemesanantest@travel.test'],
        [
            'name' => 'Sopir Agus Test',
            'phone' => '087812345678',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $driverUser->id],
        [
            'license_number' => 'SIM-B1-778899',
            'license_type' => 'SIM B1 Umum',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Siti Khadijah',
            'emergency_contact_phone' => '087899887766',
            'status' => 'standby',
        ]
    );

    // Jadwal Aktif Keberangkatan
    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'vehicle_id' => $this->vehicle->id,
            'departure_date' => now()->addDays(2)->toDateString(),
            'departure_time' => '08:00',
        ],
        [
            'driver_id' => $this->driver->id,
            'available_seats' => 10,
            'status' => 'Scheduled',
        ]
    );
});

test('admin utama can view pemesanan index page and summary metrics', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.index'));

    $response->assertOk()
        ->assertSee('Pemesanan Tiket Travel')
        ->assertSee('Input Tiket Loket')
        ->assertSee('Total Pemesanan')
        ->assertSee('Lunas Hari Ini')
        ->assertSee('Menunggu Pembayaran');
});

test('admin utama can view visual seat map of a schedule with accurate occupancy', function () {
    // Buat 1 booking Paid pada kursi 1A
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $customer = User::factory()->create(['role' => 'user']);
    $booking = Booking::create([
        'booking_code' => 'TRV-SEATMAP-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Penumpang Lunas 1A',
        'phone' => '08123456781',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.seat-map', $this->schedule->id));

    $response->assertOk()
        ->assertSee('Denah Kursi Visual')
        ->assertSee('TRV-SEATMAP-01')
        ->assertSee('Penumpang Lunas 1A')
        ->assertSee((string) $seat1A->seat_number);
});

test('admin utama can view manual booking form with available schedules', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.create-manual', ['schedule_id' => $this->schedule->id]));

    $response->assertOk()
        ->assertSee('Input Tiket Loket (Walk-in / WA)')
        ->assertSee('Pilih Jadwal Keberangkatan')
        ->assertSee('HiAce Premio Test Pemesanan')
        ->assertSee('D 7777 PMS');
});

test('admin utama can store manual walk-in or whatsapp booking with seat allocation', function () {
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();
    $seat2A = Seat::where('vehicle_id', $this->vehicle->id)->skip(1)->first();

    $payload = [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => [$seat1A->id, $seat2A->id],
        'customer_name' => 'Rahmat Hidayat (Walk-in)',
        'customer_phone' => '085512345678',
        'customer_email' => 'rahmat.walkin@test.local',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
        'notes' => 'Bawa koper sedang di bagasi',
        'passengers' => [
            $seat1A->id => ['name' => 'Rahmat Hidayat', 'phone' => '085512345678', 'special_request' => 'Depan'],
            $seat2A->id => ['name' => 'Ibu Rahma', 'phone' => '085512345679', 'special_request' => 'Dekat jendela'],
        ],
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.pemesanan.store-manual'), $payload);

    $booking = Booking::where('source', 'walk_in')->latest()->first();
    expect($booking)->not->toBeNull();

    $response->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));

    // Verifikasi data di database
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'total_passenger' => 2,
        'total_price' => 300000,
        'status' => 'Paid',
        'source' => 'walk_in',
    ]);

    $this->assertDatabaseHas('booking_passengers', [
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Rahmat Hidayat',
    ]);

    $this->assertDatabaseHas('booking_passengers', [
        'booking_id' => $booking->id,
        'seat_id' => $seat2A->id,
        'name' => 'Ibu Rahma',
    ]);

    // Verifikasi kuota kursi jadwal terpotong
    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(8);
});

test('admin utama can view booking detail and e-ticket summary', function () {
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();
    $customer = User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso']);

    $booking = Booking::create([
        'booking_code' => 'TRV-DETAIL-99',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'walk_in',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Budi Santoso',
        'phone' => '081299998888',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.show', $booking->id));

    $response->assertOk()
        ->assertSee('TRV-DETAIL-99')
        ->assertSee('Budi Santoso')
        ->assertSee('E-TICKET RESMI PERJALANAN TRAVEL')
        ->assertSee('Daftar Penumpang & Alokasi Kursi', false);
});

test('admin utama can view trip manifest and dispatch text for driver', function () {
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();
    $customer = User::factory()->create(['role' => 'user']);

    $booking = Booking::create([
        'booking_code' => 'TRV-MNF-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Pak Joko Manifes',
        'phone' => '081211112222',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.manifes', $this->schedule->id));

    $response->assertOk()
        ->assertSee('MANIFES PERJALANAN')
        ->assertSee('Pak Joko Manifes')
        ->assertSee('Sopir Agus Test')
        ->assertSee('Cetak Manifes (Print / PDF)', false)
        ->assertSee('Kirim WhatsApp ke Sopir');
});

test('admin utama can reschedule passenger booking to a new schedule and seat', function () {
    // Jadwal Baru
    $newSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDays(3)->toDateString(),
        'departure_time' => '14:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();
    $seat2B = Seat::where('vehicle_id', $this->vehicle->id)->skip(1)->first();

    $customer = User::factory()->create(['role' => 'user']);
    $booking = Booking::create([
        'booking_code' => 'TRV-RSCH-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Penumpang Ingin Reschedule',
        'phone' => '081277776666',
    ]);

    // Buka form reschedule
    $formResponse = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.reschedule', $booking->id));

    $formResponse->assertOk()
        ->assertSee('Ubah Jadwal (Reschedule)')
        ->assertSee('Pilih Jadwal Baru')
        ->assertSee('TRV-RSCH-01');

    // Submit reschedule ke newSchedule dengan kursi 2B
    $reschedulePayload = [
        'new_schedule_id' => $newSchedule->id,
        'new_seats' => [$seat2B->id],
        'reschedule_reason' => 'Permintaan penumpang karena ada rapat mendadak.',
    ];

    $updateResponse = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.pemesanan.reschedule.update', $booking->id), $reschedulePayload);

    $updateResponse->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));

    // Verifikasi booking pindah jadwal dan kursi terupdate
    $booking->refresh();
    $passenger->refresh();

    expect($booking->schedule_id)->toBe($newSchedule->id);
    expect($booking->rescheduled_at)->not->toBeNull();
    expect($booking->reschedule_reason)->toBe('Permintaan penumpang karena ada rapat mendadak.');
    expect($passenger->seat_id)->toBe($seat2B->id);
});

test('admin utama can cancel booking and release occupied seats', function () {
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();
    $customer = User::factory()->create(['role' => 'user']);

    $booking = Booking::create([
        'booking_code' => 'TRV-CANCEL-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Penumpang Batal',
    ]);

    $this->schedule->recalculateAvailableSeats();
    expect($this->schedule->available_seats)->toBe(9);

    // Jalankan cancel booking
    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pemesanan.cancel', $booking->id));

    $response->assertRedirect();

    $booking->refresh();
    $this->schedule->refresh();

    expect($booking->status)->toBe('Cancelled');
    expect($this->schedule->available_seats)->toBe(10);
});
