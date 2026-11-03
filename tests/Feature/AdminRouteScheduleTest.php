<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pool Dipatiukur (Bandung)',
        'code' => 'BDO',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Semanggi (Jakarta)',
        'code' => 'JKT',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'B 1111 RS',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Minibus Eksekutif',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->otherVehicle = Vehicle::create([
        'plate_number' => 'B 2222 RS',
        'name' => 'Toyota HiAce Commuter',
        'type' => 'Minibus Eksekutif',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->otherVehicle->ensureSeatsExist();

    $this->driver = Driver::create([
        'user_id' => User::factory()->create([
            'role' => 'driver',
            'status' => 'active',
        ])->id,
        'phone' => '081211112222',
        'license_number' => 'SIM-1111-2222',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2),
        'emergency_contact_name' => 'Darji',
        'emergency_contact_phone' => '081233334444',
        'emergency_contact_relation' => 'Saudara',
        'address' => 'Jl. Test No. 1',
        'status' => 'standby',
    ]);

    $this->routePayload = function (array $overrides = []): array {
        return array_merge([
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'price' => 125000,
            'weekend_price' => null,
            'duration' => '2 Jam 45 Menit',
            'distance_km' => 150,
            'notes' => 'Rute uji.',
        ], $overrides);
    };

    $this->schedulePayload = function (array $overrides = []): array {
        return array_merge([
            'route_id' => ($this->makeRoute)()->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_date' => Carbon::tomorrow()->toDateString(),
            'departure_time' => '09:00',
            'price' => null,
            'notes' => 'Jadwal uji.',
        ], $overrides);
    };

    $this->makeRoute = function (array $overrides = []): TravelRoute {
        return TravelRoute::create(($this->routePayload)($overrides));
    };

    $this->makeSchedule = function (array $overrides = []): Schedule {
        return Schedule::create(($this->schedulePayload)($overrides));
    };

    $this->makeBooking = function (Schedule $schedule): Booking {
        $booking = Booking::create([
            'booking_code' => 'TRV-RS-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => User::factory()->create(['role' => 'user'])->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 1,
            'subtotal' => 125000,
            'total_price' => 125000,
            'status' => 'Paid',
            'source' => 'online',
        ]);
        $seat = Seat::where('vehicle_id', $schedule->vehicle_id)->where('seat_number', '1')->firstOrFail();
        BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'name' => 'Penumpang Rute',
            'phone' => '081200000001',
        ]);
        Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => 'TKT-RS-01',
            'qr_code' => 'QR-TRV-RS-01',
            'status' => 'Issued',
        ]);

        return $booking;
    };
});

test('tambah rute baru dengan asal, tujuan, harga, dan durasi valid', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), ($this->routePayload)());

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    expect(TravelRoute::where('origin_id', $this->origin->id)
        ->where('destination_id', $this->destination->id)
        ->where('price', 125000)
        ->exists())->toBeTrue();
});

test('rute duplikat, harga kurang dari 10.000, dan asal sama tujuan ditolak', function () {
    // Duplikat asal-tujuan
    $existing = ($this->makeRoute)();

    // Duplikat asal-tujuan
    $dup = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), ($this->routePayload)());
    $dup->assertRedirect();
    $dup->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah terdaftar');
    });

    // Harga di bawah 10.000
    $otherOrigin = Location::create([
        'name' => 'Pool Lain',
        'code' => 'OLN',
        'city' => 'Lain',
        'type' => 'pool',
        'is_active' => true,
    ]);
    $cheap = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), ($this->routePayload)([
            'origin_id' => $otherOrigin->id,
            'price' => 5000,
        ]));
    $cheap->assertRedirect();
    $cheap->assertSessionHasErrors(['price']);

    // Asal sama tujuan
    $same = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.store'), ($this->routePayload)([
            'destination_id' => $this->origin->id,
        ]));
    $same->assertRedirect();
    $same->assertSessionHasErrors(['origin_id']);

    expect(TravelRoute::count())->toBe(1);
});

test('edit rute tersimpan', function () {
    $route = ($this->makeRoute)();

    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.rute.update', $route->id), ($this->routePayload)([
            'price' => 150000,
            'duration' => '3 Jam 00 Menit',
        ]));

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    expect((float) $route->fresh()->price)->toBe(150000.0)
        ->and($route->fresh()->duration)->toBe('3 Jam 00 Menit');
});

test('nonaktifkan rute melalui toggle status', function () {
    $route = ($this->makeRoute)();

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.toggle-status', $route->id));

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    expect($route->fresh()->is_active)->toBeFalse();
});

test('hapus rute yang masih punya tiket aktif ditolak', function () {
    $route = ($this->makeRoute)();
    $schedule = ($this->makeSchedule)(['route_id' => $route->id]);
    ($this->makeBooking)($schedule);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.rute.destroy', $route->id));

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'tidak dapat dihapus');
    });

    expect(TravelRoute::find($route->id))->not->toBeNull();
});

test('tambah lokasi pool/shuttle/drop point dengan kode unik maksimal 10 karakter', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.lokasi.store'), [
        'name' => 'Halte Pasteur (Bandung)',
        'code' => 'PASTEUR',
        'city' => 'Bandung',
        'address' => 'Jl. Pasteur No. 200',
        'type' => 'shuttle',
    ]);

    $response->assertRedirect(route('dashboard.admin.rute.index'));
    $response->assertSessionHas('success');

    expect(Location::where('code', 'PASTEUR')->where('type', 'shuttle')->exists())->toBeTrue();

    // Kode duplikat ditolak
    $dup = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.lokasi.store'), [
        'name' => 'Lokasi Duplikat',
        'code' => 'PASTEUR',
        'type' => 'pool',
    ]);
    $dup->assertRedirect();
    $dup->assertSessionHasErrors(['code']);
});

test('tambah jadwal baru dengan rute, armada, sopir, tanggal, dan jam valid', function () {
    $payload = ($this->schedulePayload)();

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), $payload);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    expect(Schedule::where('route_id', $payload['route_id'])
        ->where('vehicle_id', $this->vehicle->id)
        ->where('driver_id', $this->driver->id)
        ->whereDate('departure_date', Carbon::tomorrow())
        ->count())->toBe(1);
});

test('jadwal dengan tanggal lampau atau format jam salah gagal divalidasi', function () {
    $payload = ($this->schedulePayload)();

    $past = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), array_merge($payload, [
            'departure_date' => Carbon::yesterday()->toDateString(),
        ]));
    $past->assertRedirect();
    $past->assertSessionHasErrors(['departure_date']);

    $badTime = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), array_merge($payload, [
            'departure_time' => '25:00',
        ]));
    $badTime->assertRedirect();
    $badTime->assertSessionHasErrors(['departure_time']);
});

test('jadwal armada yang sama tanggal dan jam sama ditolak', function () {
    ($this->makeSchedule)([]);

    $payload = ($this->schedulePayload)();

    $clash = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), $payload);

    $clash->assertRedirect();
    $clash->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah memiliki jadwal keberangkatan lain');
    });

    expect(Schedule::where('vehicle_id', $this->vehicle->id)
        ->whereDate('departure_date', Carbon::tomorrow())
        ->count())->toBe(1);
});

test('jadwal sopir yang sama tanggal dan jam sama ditolak', function () {
    $otherRoute = ($this->makeRoute)([]);
    ($this->makeSchedule)([]);

    $payload = ($this->schedulePayload)();
    $payload['route_id'] = $otherRoute->id;
    $payload['vehicle_id'] = $this->otherVehicle->id;

    $clash = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.rute.jadwal.store'), $payload);

    $clash->assertRedirect();
    $clash->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah ditugaskan pada jadwal lain');
    });
});

test('generate jadwal massal 7 hari x 3 jam membuat 21 jadwal, jalankan ulang dilewati semua', function () {
    $route = ($this->makeRoute)();

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.jadwal.batch-generate'), [
        'route_id' => $route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'start_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->addDays(6)->toDateString(),
        'departure_times' => ['07:00', '10:00', '14:00'],
        'price' => 125000,
    ]);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success', function (string $message): bool {
        return str_contains($message, 'Berhasil membuat 21 slot');
    });

    expect(Schedule::where('route_id', $route->id)->count())->toBe(21);

    // Jalankan ulang: semua dilewati karena sudah ada
    $again = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.jadwal.batch-generate'), [
        'route_id' => $route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'start_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->addDays(6)->toDateString(),
        'departure_times' => ['07:00', '10:00', '14:00'],
        'price' => 125000,
    ]);

    $again->assertSessionHas('success', function (string $message): bool {
        return str_contains($message, '0 slot') && str_contains($message, '21 jadwal dilewati');
    });

    expect(Schedule::where('route_id', $route->id)
        ->where('vehicle_id', $this->vehicle->id)
        ->count())->toBe(21);
});

test('generate massal dengan tanggal akhir kurang dari awal ditolak', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.jadwal.batch-generate'), [
        'route_id' => ($this->makeRoute)()->id,
        'vehicle_id' => $this->vehicle->id,
        'start_date' => Carbon::today()->addDays(5)->toDateString(),
        'end_date' => Carbon::today()->toDateString(),
        'departure_times' => ['09:00'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['end_date']);
});

test('generate massal tidak memeriksa bentrok armada/sopir di rute lain (BUG-13)', function () {
    $routeA = ($this->makeRoute)([]);
    $routeB = ($this->makeRoute)([]);
    $today = Carbon::today()->toDateString();

    // Jadwal armada dan sopir di route A pada jam 09:00
    Schedule::create([
        'route_id' => $routeA->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $today,
        'departure_time' => '09:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.rute.jadwal.batch-generate'), [
        'route_id' => $routeB->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'start_date' => $today,
        'end_date' => $today,
        'departure_times' => ['09:00'],
        'price' => 125000,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', function (string $message): bool {
        return str_contains($message, 'Berhasil membuat 1 slot');
    });

    expect(Schedule::where('route_id', $routeB->id)
        ->where('vehicle_id', $this->vehicle->id)
        ->count())->toBe(1);
});

test('ganti armada dan sopir pada jadwal kosong tersimpan dan kapasitas dihitung ulang', function () {
    $schedule = ($this->makeSchedule)([], []);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.rute.jadwal.assign', $schedule->id), [
        'vehicle_id' => $this->otherVehicle->id,
        'driver_id' => null,
    ]);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    $schedule->refresh();
    expect($schedule->vehicle_id)->toBe($this->otherVehicle->id)
        ->and($schedule->driver_id)->toBeNull()
        ->and($schedule->available_seats)->toBe($this->otherVehicle->capacity);
});

test('ganti armada pada jadwal yang sudah ada penumpang atau armada sedang dipakai (BUG-13)', function () {
    $schedule = ($this->makeSchedule)([], []);
    ($this->makeBooking)($schedule);

    // Armada lain sudah memiliki jadwal pada tanggal dan jam yang sama
    $otherSchedule = ($this->makeSchedule)([
        'vehicle_id' => $this->otherVehicle->id,
        'driver_id' => null,
    ]);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.rute.jadwal.assign', $schedule->id), [
        'vehicle_id' => $this->otherVehicle->id,
        'driver_id' => null,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($schedule->fresh()->vehicle_id)->toBe($this->otherVehicle->id);
});

test('ubah status jadwal (Scheduled/Active/Completed) tersimpan', function () {
    $schedule = ($this->makeSchedule)([]);

    foreach (['Active', 'Completed', 'Scheduled'] as $status) {
        $response = $this->actingAs($this->admin)
            ->patch(route('dashboard.admin.rute.jadwal.update-status', $schedule->id), [
                'status' => $status,
            ]);
        $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
        $response->assertSessionHas('success');
        expect($schedule->fresh()->status)->toBe($status);
    }
});

test('batalkan jadwal yang sudah ada penumpang: booking dibatalkan, refund, penumpang diberi tahu (BUG-14)', function () {
    $schedule = ($this->makeSchedule)([], []);
    $booking = ($this->makeBooking)($schedule);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.rute.jadwal.update-status', $schedule->id), [
            'status' => 'Cancelled',
        ]);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($schedule->fresh()->status)->toBe('Cancelled')
        ->and($booking->status)->toBe('cancelled')
        ->and($booking->ticket->fresh()->status)->toBe('cancelled');

    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull()
        ->and($refund->status)->toBe('Disetujui')
        ->and((float) $refund->amount)->toBe((float) $booking->total_price);
});

test('hapus jadwal yang sudah ada booking ditolak', function () {
    $schedule = ($this->makeSchedule)([], []);
    ($this->makeBooking)($schedule);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.rute.jadwal.destroy', $schedule->id));

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah ada pemesanan tiket');
    });

    expect(Schedule::find($schedule->id))->not->toBeNull();
});

test('hapus jadwal kosong terhapus', function () {
    $schedule = ($this->makeSchedule)([], []);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.rute.jadwal.destroy', $schedule->id));

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    expect(Schedule::find($schedule->id))->toBeNull();
});
