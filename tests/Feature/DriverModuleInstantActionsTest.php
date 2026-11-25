<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TripIncident;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupDriverScenario(): array
{
    $admin = User::factory()->create(['name' => 'Admin Pusat', 'role' => 'admin_utama', 'status' => 'active']);
    $driverUser = User::factory()->create(['name' => 'Yayan Supriatna', 'phone' => '081234567001', 'role' => 'driver', 'status' => 'active']);
    $driver = Driver::create([
        'user_id' => $driverUser->id,
        'phone' => '081234567001',
        'license_number' => 'SIM-DRV-1001',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $colleagueUser = User::factory()->create(['name' => 'Hendra Wijaya', 'phone' => '081234567002', 'role' => 'driver', 'status' => 'active']);
    $colleagueDriver = Driver::create([
        'user_id' => $colleagueUser->id,
        'phone' => '081234567002',
        'license_number' => 'SIM-DRV-1002',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $passengerUser = User::factory()->create(['name' => 'Siti Aminah', 'phone' => '081299998888', 'role' => 'user', 'status' => 'active']);

    $origin = Location::create(['name' => 'Loket Pasteur', 'code' => 'BDG-P1', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $dest = Location::create(['name' => 'Loket Semanggi', 'code' => 'JKT-S1', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);
    $route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $dest->id, 'price' => 125000, 'duration' => '3 Jam', 'is_active' => true]);

    $vehicle1 = Vehicle::create(['plate_number' => 'D 1111 AA', 'name' => 'Toyota HiAce Premio', 'type' => 'Minibus', 'capacity' => 10, 'status' => 'siap_jalan']);
    $vehicle1->ensureSeatsExist();

    $vehicle2 = Vehicle::create(['plate_number' => 'D 2222 BB', 'name' => 'Toyota HiAce Commuter', 'type' => 'Minibus', 'capacity' => 10, 'status' => 'siap_jalan']);
    $vehicle2->ensureSeatsExist();

    $schedule1 = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle1->id,
        'driver_id' => $driver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 125000,
        'available_seats' => 9,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $schedule2 = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle2->id,
        'driver_id' => $colleagueDriver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '13:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $seat1 = Seat::where('vehicle_id', $vehicle1->id)->orderBy('id')->firstOrFail();

    $booking = Booking::create([
        'booking_code' => 'TRV-INSTANT-01',
        'user_id' => $passengerUser->id,
        'schedule_id' => $schedule1->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1->id,
        'name' => 'Siti Aminah',
        'phone' => '081299998888',
        'pickup_point' => 'Loket Pasteur',
        'dropoff_point' => 'Halte Semanggi Pintu 1',
        'boarding_status' => 'belum_hadir',
    ]);

    return compact('admin', 'driverUser', 'driver', 'colleagueUser', 'colleagueDriver', 'passengerUser', 'vehicle1', 'schedule1', 'schedule2', 'booking');
}

test('beranda driver displays greeting, weather widget, and live traffic map without heavy action buttons', function () {
    $ctx = setupDriverScenario();

    $response = $this->actingAs($ctx['driverUser'])->get(route('dashboard.driver.index'));

    $response->assertOk();
    $response->assertSee('Halo, selamat datang Pak Yayan Supriatna, semangat beraktivitas!');
    $response->assertSee('Ramalan Cuaca Perjalanan Hari Ini');
    $response->assertSee('Peta Kondisi Lalu Lintas (Live Traffic)');
    $response->assertSee('TrafficLayer');
});

test('driver can confirm departing immediately without admin approval and notifies passengers', function () {
    $ctx = setupDriverScenario();

    $this->actingAs($ctx['driverUser'])
        ->get(route('dashboard.driver.perjalanan'))
        ->assertOk()
        ->assertSee('Toyota HiAce Premio')
        ->assertSee('D 1111 AA')
        ->assertSee('Terisi 1 dari 10 kursi');

    $response = $this->actingAs($ctx['driverUser'])
        ->post(route('dashboard.driver.schedule.departing', $ctx['schedule1']));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $ctx['schedule1']->refresh();
    expect($ctx['schedule1']->trip_status)->toBe('departing');
    expect($ctx['schedule1']->departing_at)->not->toBeNull();

    // Penumpang dan admin otomatis mendapat notifikasi database
    expect($ctx['passengerUser']->notifications()->count())->toBe(1);
    expect($ctx['passengerUser']->notifications()->first()->data['status_type'])->toBe('departing');
    expect($ctx['admin']->notifications()->count())->toBe(1);
});

test('driver can cancel trip immediately without admin approval and notifies passengers and admin', function () {
    $ctx = setupDriverScenario();

    $response = $this->actingAs($ctx['driverUser'])
        ->post(route('dashboard.driver.schedule.cancel', $ctx['schedule1']), [
            'cancellation_reason' => 'Mesin mobil tidak bisa dinyalakan di garasi sebelum berangkat.',
        ]);

    $response->assertRedirect(route('dashboard.driver.perjalanan'));
    $response->assertSessionHas('success');

    $ctx['schedule1']->refresh();
    expect($ctx['schedule1']->trip_status)->toBe('cancelled');
    expect($ctx['schedule1']->status)->toBe('Cancelled');
    expect($ctx['schedule1']->cancellation_reason)->toContain('Mesin mobil');

    expect($ctx['passengerUser']->notifications()->first()->data['status_type'])->toBe('trip_cancelled');
    expect($ctx['admin']->notifications()->first()->data['status_type'])->toBe('trip_cancelled_by_driver');
});

test('penumpang & kursi and jadwal hari ini show cabin manifest and all fleet schedules with contacts', function () {
    $ctx = setupDriverScenario();

    $this->actingAs($ctx['driverUser'])
        ->get(route('dashboard.driver.penumpang'))
        ->assertOk()
        ->assertDontSee('Denah Kursi Kabin')
        ->assertSee('Kode QR')
        ->assertSee('Berangkat Jam')
        ->assertSee('Sampai Jam')
        ->assertSee('Lunas')
        ->assertSee('Siti Aminah')
        ->assertSee('Halte Semanggi Pintu 1')
        ->assertSee('https://wa.me/6281299998888', false);

    $this->actingAs($ctx['driverUser'])
        ->get(route('dashboard.driver.jadwal'))
        ->assertOk()
        ->assertSee('Jadwal Hari Ini (Semua Armada)')
        ->assertSee('D 1111 AA')
        ->assertSee('D 2222 BB')
        ->assertSee('Hendra Wijaya')
        ->assertSee('https://wa.me/6281234567002', false);
});

test('driver emergency incident is immediately active without approval and notifies admin and fellow drivers', function () {
    $ctx = setupDriverScenario();

    $response = $this->actingAs($ctx['driverUser'])
        ->post(route('dashboard.driver.incident.instant'), [
            'category' => 'mesin_mogok',
            'location_name' => 'Mobil Toyota HiAce mogok di Tol Cipularang KM 88',
            'latitude' => -6.582100,
            'longitude' => 107.432100,
            'schedule_id' => $ctx['schedule1']->id,
        ]);

    $response->assertRedirect(route('dashboard.driver.lapor-masalah'));
    $response->assertSessionHas('incident_reported', true);

    $incident = TripIncident::latest()->first();
    expect($incident)->not->toBeNull();
    expect($incident->status)->toBe('active');
    expect($incident->needs_replacement_vehicle)->toBeTrue();

    $ctx['vehicle1']->refresh();
    expect($ctx['vehicle1']->status)->toBe('perbaikan');

    // Admin & rekan driver menerima notifikasi darurat
    expect($ctx['admin']->notifications()->first()->data['status_type'])->toBe('driver_emergency');
    expect($ctx['colleagueUser']->notifications()->first()->data['status_type'])->toBe('driver_emergency');

    // Rekan driver dapat melihat notifikasi & menandai sudah dibaca
    $this->actingAs($ctx['colleagueUser'])
        ->get(route('dashboard.driver.notifikasi'))
        ->assertOk()
        ->assertSee('Darurat Operasional');

    $this->actingAs($ctx['colleagueUser'])
        ->post(route('dashboard.driver.notifikasi.read-all'))
        ->assertRedirect();

    expect($ctx['colleagueUser']->unreadNotifications()->count())->toBe(0);
});

test('admin utama driver detail and trip manifest are synced with driver passenger data', function () {
    $ctx = setupDriverScenario();

    $this->actingAs($ctx['admin'])
        ->get(route('dashboard.admin.kelola-driver.show', $ctx['driver']))
        ->assertOk()
        ->assertSee('Daftar Penumpang di Akun Driver')
        ->assertSee('TRV-INSTANT-01')
        ->assertSee('Siti Aminah')
        ->assertSee('Halte Semanggi Pintu 1')
        ->assertSee('Lunas');

    $this->actingAs($ctx['admin'])
        ->get(route('dashboard.admin.pemesanan.manifes', $ctx['schedule1']))
        ->assertOk()
        ->assertSee('TRV-INSTANT-01')
        ->assertSee('Siti Aminah')
        ->assertSee('Halte Semanggi Pintu 1')
        ->assertSee('LUNAS');
});
