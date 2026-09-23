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
use App\Notifications\OperationalInfoNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->driverUser = User::firstOrCreate(
        ['email' => 'driver.test@travel.test'],
        [
            'name' => 'Budi Supir Travel',
            'phone' => '081234567899',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $this->driverUser->id],
        [
            'phone' => '081234567899',
            'status' => 'standby',
            'license_number' => 'SIM-B1-998877',
            'license_type' => 'B1 Umum',
            'license_expired_at' => Carbon::now()->addYear(),
        ]
    );

    $this->origin = Location::firstOrCreate(
        ['name' => 'Pool Dipatiukur (Bandung)'],
        ['code' => 'BDG-DPU', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]
    );

    $this->destination = Location::firstOrCreate(
        ['name' => 'Pool Semanggi (Jakarta)'],
        ['code' => 'JKT-SMG', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $this->origin->id, 'destination_id' => $this->destination->id],
        ['distance_km' => 150, 'duration' => '2 Jam 45 Menit', 'price' => 125000, 'is_active' => true]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 7812 AB'],
        [
            'name' => 'Toyota HiAce Premio Eksekutif',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ]
    );
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_date' => Carbon::today()->toDateString(),
            'departure_time' => '13:00:00',
        ],
        [
            'price' => 125000,
            'available_seats' => 8,
            'status' => 'Scheduled',
            'trip_status' => 'scheduled',
        ]
    );

    $this->customer = User::factory()->create();

    $this->booking = Booking::create([
        'booking_code' => 'TKV-DRV-001',
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->customer->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::where('vehicle_id', $this->vehicle->id)->where('seat_number', '1A')->first();

    $this->passenger = BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'seat_id' => $seat?->id,
        'name' => 'Ahmad Penumpang',
        'phone' => '081298765432',
        'pickup_point' => 'Pool Dipatiukur (Gate 1)',
        'dropoff_point' => 'Pool Semanggi Plaza',
        'baggage_notes' => '1 Koper Medium 24 inch',
        'boarding_status' => 'belum_hadir',
    ]);
});

test('driver can access driver dashboard with vehicle and schedule info', function () {
    $this->vehicle->update([
        'hull_number' => 'HCE-07',
        'parking_location' => 'Bay 3 - Garasi Barat Pool Dipatiukur',
    ]);

    $this->actingAs($this->driverUser)
        ->get(route('dashboard.driver.index'))
        ->assertOk()
        ->assertSee('Toyota HiAce Premio Eksekutif')
        ->assertSee('D 7812 AB')
        ->assertSee('HCE-07')
        ->assertSee('Bay 3 - Garasi Barat Pool Dipatiukur')
        ->assertSee('Nomor Lambung Armada')
        ->assertSee('Lokasi Fisik Parkir')
        ->assertSee('Ahmad Penumpang')
        ->assertSee('1 Koper Medium 24 inch')
        ->assertSee('Mulai Jemput')
        ->assertSee($this->schedule->tripCode())
        ->assertSee('Jam Keberangkatan')
        ->assertSee('Estimasi Waktu Tiba')
        ->assertSee('Panduan Ritme Waktu')
        ->assertSee('Persiapan Armada');
});

test('driver can progressively update trip status to pickup and notifies passenger', function () {
    Notification::fake();

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $this->schedule->id), [
            'status' => 'pickup',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
        ]);

    $response->assertRedirect();
    expect($this->schedule->fresh()->trip_status)->toBe('pickup');
    expect($this->schedule->fresh()->pickup_started_at)->not->toBeNull();
    expect($this->driver->fresh()->status)->toBe('on_duty');

    Notification::assertSentTo($this->customer, OperationalInfoNotification::class);
});

test('driver can update trip status to departed, arrived, and completed', function () {
    // 1. Departed
    $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $this->schedule->id), [
            'status' => 'departed',
        ]);
    expect($this->schedule->fresh()->trip_status)->toBe('departed');
    expect($this->schedule->fresh()->departed_at)->not->toBeNull();

    // 2. Arrived
    $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $this->schedule->id), [
            'status' => 'arrived',
        ]);
    expect($this->schedule->fresh()->trip_status)->toBe('arrived');
    expect($this->schedule->fresh()->arrived_at)->not->toBeNull();

    // 3. Completed
    $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $this->schedule->id), [
            'status' => 'completed',
        ]);
    expect($this->schedule->fresh()->trip_status)->toBe('completed');
    expect($this->schedule->fresh()->completed_at)->not->toBeNull();
    expect($this->driver->fresh()->status)->toBe('standby');
});

test('driver can update background GPS coordinates via AJAX', function () {
    $response = $this->actingAs($this->driverUser)
        ->postJson(route('dashboard.driver.schedule.gps', $this->schedule->id), [
            'latitude' => -6.90389,
            'longitude' => 107.61861,
            'accuracy' => 12.5,
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    expect((float) $this->schedule->fresh()->current_latitude)->toEqualWithDelta(-6.90389, 0.001);
    expect((float) $this->schedule->fresh()->current_longitude)->toEqualWithDelta(107.61861, 0.001);
});

test('driver can check-in passenger and confirm boarding', function () {
    $response = $this->actingAs($this->driverUser)
        ->patch(route('dashboard.driver.passenger.checkin', $this->passenger->id), [
            'boarding_status' => 'boarded',
        ]);

    $response->assertRedirect();
    expect($this->passenger->fresh()->boarding_status)->toBe('boarded');
    expect($this->passenger->fresh()->boarded_at)->not->toBeNull();
});

test('driver can confirm COD payment upon check-in', function () {
    $this->booking->update(['status' => 'pending']);

    $response = $this->actingAs($this->driverUser)
        ->patch(route('dashboard.driver.passenger.checkin', $this->passenger->id), [
            'boarding_status' => 'boarded',
            'mark_paid' => 1,
        ]);

    $response->assertRedirect();
    expect($this->booking->fresh()->status)->toBe('confirmed');
    expect($this->passenger->fresh()->boarding_status)->toBe('boarded');
});

test('driver can report road incident with category and description', function () {
    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.incident', $this->schedule->id), [
            'category' => 'ban_bocor',
            'severity' => 'sedang',
            'description' => 'Ban belakang kiri tertusuk baut di KM 88, sedang proses ganti ban serep.',
            'location_name' => 'Tol Cipularang KM 88',
        ]);

    $response->assertRedirect();

    $incident = TripIncident::where('schedule_id', $this->schedule->id)->first();
    expect($incident)->not->toBeNull();
    expect($incident->category)->toBe('ban_bocor');
    expect($incident->severity)->toBe('sedang');
    expect($incident->driver_id)->toBe($this->driver->id);
});

test('driver can trigger emergency SOS broadcast', function () {
    $response = $this->actingAs($this->driverUser)
        ->postJson(route('dashboard.driver.schedule.sos', $this->schedule->id), [
            'latitude' => -6.89000,
            'longitude' => 107.54000,
            'description' => 'Insiden darurat di jalur tol KM 92',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $sosIncident = TripIncident::where('category', 'sos_emergency')->latest()->first();
    expect($sosIncident)->not->toBeNull();
    expect($sosIncident->severity)->toBe('darurat');
    expect($sosIncident->emergency_dispatched)->toBeTrue();
});
