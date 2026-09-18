<?php

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Admin Utama User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.rutetest@travel.test'],
        [
            'name' => 'Admin Utama Rute Test',
            'phone' => '08123456799',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Regular Customer User
    $this->customer = User::firstOrCreate(
        ['email' => 'customer.rutetest@travel.test'],
        [
            'name' => 'Customer Test',
            'phone' => '08987654321',
            'role' => 'penumpang',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Lokasi Awal & Tujuan
    $this->origin = Location::firstOrCreate(
        ['code' => 'BDG-TEST'],
        [
            'name' => 'Bandung Pasteur Pool',
            'city' => 'Bandung',
            'address' => 'Jl. Dr. Djunjunan No. 123',
            'phone' => '022-123456',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-TEST'],
        [
            'name' => 'Jakarta Pancoran Drop Point',
            'city' => 'Jakarta',
            'address' => 'Jl. MT Haryono No. 45',
            'phone' => '021-987654',
            'type' => 'drop_point',
            'is_active' => true,
        ]
    );

    // Rute Travel
    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $this->origin->id, 'destination_id' => $this->destination->id],
        [
            'distance_km' => 150,
            'duration' => '3 Jam 30 Menit',
            'price' => 125000,
            'weekend_price' => 145000,
            'holiday_price' => 165000,
            'notes' => 'Via Tol Cipularang',
            'is_active' => true,
        ]
    );

    // Armada & Kursi
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 9999 RUT'],
        [
            'name' => 'Toyota HiAce Premio Luxury',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );
    $this->vehicle->ensureSeatsExist();

    $this->vehicle2 = Vehicle::firstOrCreate(
        ['plate_number' => 'D 8888 RUT'],
        [
            'name' => 'Isuzu Elf Long Travel',
            'type' => 'Isuzu Elf',
            'capacity' => 14,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );
    $this->vehicle2->ensureSeatsExist();

    // Driver
    $this->driverUser = User::firstOrCreate(
        ['email' => 'driver.rutetest@travel.test'],
        [
            'name' => 'Sopir Handal Rute',
            'phone' => '087711223344',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $this->driverUser->id],
        [
            'license_number' => 'SIM-B1-RUTETEST-01',
            'license_type' => 'B1',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Keluarga Sopir',
            'emergency_contact_phone' => '087799887766',
            'status' => 'standby',
        ]
    );

    $this->driverUser2 = User::firstOrCreate(
        ['email' => 'driver2.rutetest@travel.test'],
        [
            'name' => 'Sopir Cadangan Rute',
            'phone' => '087755667788',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver2 = Driver::firstOrCreate(
        ['user_id' => $this->driverUser2->id],
        [
            'license_number' => 'SIM-B1-RUTETEST-02',
            'license_type' => 'B1',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Keluarga Sopir 2',
            'emergency_contact_phone' => '087799887755',
            'status' => 'standby',
        ]
    );
});

// 1. Hak Akses
test('guest and non-admin cannot access route and schedule pages', function () {
    $this->get(route('dashboard.admin.rute.index'))
        ->assertRedirect(route('login'));

    $this->get(route('dashboard.admin.rute.jadwal'))
        ->assertRedirect(route('login'));

    $this->actingAs($this->customer)
        ->get(route('dashboard.admin.rute.index'))
        ->assertStatus(403);

    $this->actingAs($this->customer)
        ->get(route('dashboard.admin.rute.jadwal'))
        ->assertStatus(403);
});

// 2. Tampilan Rute Index & Metrik
test('admin utama can view route list and summary metrics', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.rute.index'));

    $response->assertStatus(200);
    $response->assertSee('Rute Perjalanan & Tarif');
    $response->assertSee('Bandung Pasteur Pool');
    $response->assertSee('Jakarta Pancoran Drop Point');
    $response->assertSee('Rp 125.000');
    $response->assertSee('Rp 145.000');
});

// 3. Tambah Lokasi / Pool Baru
test('admin utama can store a new location / pool', function () {
    $data = [
        'code' => 'SBY-POOL',
        'name' => 'Surabaya Gubeng Central Pool',
        'city' => 'Surabaya',
        'address' => 'Jl. Stasiun Gubeng No. 10',
        'phone' => '031-555667',
        'type' => 'pool',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.lokasi.store'), $data);

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('locations', [
        'code' => 'SBY-POOL',
        'name' => 'Surabaya Gubeng Central Pool',
        'city' => 'Surabaya',
        'type' => 'pool',
    ]);
});

// 4. Tambah Rute Baru dengan Tarif Dasar & Akhir Pekan
test('admin utama can store a new route with pricing', function () {
    $semarang = Location::firstOrCreate(
        ['code' => 'SMG-TEST'],
        ['name' => 'Semarang Simpang Lima', 'city' => 'Semarang', 'address' => 'Jl. Pandanaran No. 1']
    );

    $data = [
        'origin_id' => $this->origin->id,
        'destination_id' => $semarang->id,
        'distance_km' => 320,
        'duration' => '5 Jam',
        'price' => 200000,
        'weekend_price' => 230000,
        'holiday_price' => 260000,
        'notes' => 'Via Tol Trans Jawa',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), $data);

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('routes', [
        'origin_id' => $this->origin->id,
        'destination_id' => $semarang->id,
        'price' => 200000,
        'weekend_price' => 230000,
    ]);
});

// 5. Validasi: Titik asal dan tujuan tidak boleh sama
test('validation rejects route with identical origin and destination', function () {
    $data = [
        'origin_id' => $this->origin->id,
        'destination_id' => $this->origin->id,
        'duration' => '3 Jam',
        'distance_km' => 10,
        'price' => 50000,
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), $data);

    $response->assertSessionHasErrors(['origin_id']);
});

// 6. Edit dan Update Rute
test('admin utama can update an existing route', function () {
    $data = [
        'origin_id' => $this->route->origin_id,
        'destination_id' => $this->route->destination_id,
        'distance_km' => 155,
        'duration' => '3 Jam 45 Menit',
        'price' => 135000,
        'weekend_price' => 155000,
        'holiday_price' => 175000,
        'notes' => 'Via Tol Pasteur - Cipularang - Cawang',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.rute.update', $this->route), $data);

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('routes', [
        'id' => $this->route->id,
        'price' => 135000,
        'weekend_price' => 155000,
        'notes' => 'Via Tol Pasteur - Cipularang - Cawang',
    ]);
});

// 7. Toggle Status Aktif Rute
test('admin utama can toggle route active status', function () {
    expect($this->route->is_active)->toBeTrue();

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.toggle-status', $this->route));

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $this->route->refresh();
    expect($this->route->is_active)->toBeFalse();

    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.toggle-status', $this->route));

    $this->route->refresh();
    expect($this->route->is_active)->toBeTrue();
});

// 8. Tampilan Jadwal Keberangkatan & Filter
test('admin utama can view schedule index page and see list of schedules', function () {
    Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->toDateString(),
        'departure_time' => '08:00',
        'arrival_time' => '11:30',
        'available_seats' => $this->vehicle->capacity,
        'status' => 'Scheduled',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.rute.jadwal'));

    $response->assertStatus(200);
    $response->assertSee('Jadwal Keberangkatan Harian');
    $response->assertSee('08:00');
    $response->assertSee('Toyota HiAce Premio Luxury');
    $response->assertSee('Sopir Handal Rute');
});

// 9. Tambah Jadwal Tunggal dengan Kapasitas Kursi Otomatis
test('admin utama can create a single departure schedule', function () {
    $tomorrow = now()->addDays(1)->toDateString();

    $data = [
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $tomorrow,
        'departure_time' => '14:00',
        'arrival_time' => '17:30',
        'price' => 140000,
        'notes' => 'Keberangkatan Siang Reguler',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), $data);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    $schedule = Schedule::where('route_id', $this->route->id)
        ->where('vehicle_id', $this->vehicle->id)
        ->where('departure_time', '14:00')
        ->first();

    expect($schedule)->not->toBeNull();
    expect($schedule->departure_date->toDateString())->toBe($tomorrow);
    expect($schedule->available_seats)->toBe(10);
    expect((int) $schedule->price)->toBe(140000);
    expect($schedule->notes)->toBe('Keberangkatan Siang Reguler');
});

// 10. Validasi Konflik Armada atau Sopir di Jam yang Sama
test('validation rejects conflicting schedule for same vehicle or driver at same time', function () {
    $date = now()->addDays(2)->toDateString();

    // Buat jadwal pertama
    Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $date,
        'departure_time' => '10:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    // Percobaan buat jadwal kedua dengan armada yang sama pada tanggal & jam yang sama
    $dataConflictVehicle = [
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver2->id,
        'departure_date' => $date,
        'departure_time' => '10:00',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), $dataConflictVehicle);

    $response->assertSessionHas('error');
});

// 11. Batch Generate Jadwal Keberangkatan (06:00, 10:00, 14:00, 18:00)
test('admin utama can batch generate multiple daily departure schedules', function () {
    $startDate = now()->addDays(5)->toDateString();
    $endDate = now()->addDays(6)->toDateString(); // 2 hari

    $data = [
        'route_id' => $this->route->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'departure_times' => ['06:00', '10:00', '14:00', '18:00'],
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'notes' => 'Batch Generated Schedule',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.batch-generate'), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 2 hari x 4 jam jalan = 8 jadwal terbentuk
    $count = Schedule::where('route_id', $this->route->id)
        ->whereDate('departure_date', '>=', $startDate)
        ->whereDate('departure_date', '<=', $endDate)
        ->whereIn('departure_time', ['06:00', '10:00', '14:00', '18:00'])
        ->count();

    expect($count)->toBe(8);
});

// 12. Penetapan / Tukar Armada dan Sopir (Assign/Swap)
test('admin utama can assign and swap vehicle and driver on existing schedule', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDays(3)->toDateString(),
        'departure_time' => '06:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $data = [
        'vehicle_id' => $this->vehicle2->id, // Ganti ke unit 14 kursi
        'driver_id' => $this->driver2->id,
    ];

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.jadwal.assign', $schedule), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();
    expect($schedule->vehicle_id)->toBe($this->vehicle2->id);
    expect($schedule->driver_id)->toBe($this->driver2->id);
    expect($schedule->available_seats)->toBe(14); // Tersinkronisasi ke kapasitas armada baru
});

// 13. Update Status Jadwal (Scheduled -> Active -> Completed)
test('admin utama can update schedule status', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->toDateString(),
        'departure_time' => '16:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.jadwal.update-status', $schedule), [
            'status' => 'Active',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();
    expect($schedule->status)->toBe('Active');
});

// 14. Hapus Rute Ditolak Jika Memiliki Pesanan Aktif
test('cannot delete route if it has active bookings', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDays(4)->toDateString(),
        'departure_time' => '09:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    Booking::create([
        'booking_code' => 'TRV-RUT-999',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.rute.destroy', $this->route));

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('routes', ['id' => $this->route->id]);
});
