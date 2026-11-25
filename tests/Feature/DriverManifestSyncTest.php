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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('driver dashboard prioritizes schedules with real bookings over earlier empty schedules and does not seed fake passengers', function () {
    $driverUser = User::factory()->create(['role' => 'driver', 'status' => 'active']);
    $driver = Driver::create([
        'user_id' => $driverUser->id,
        'phone' => '081234567890',
        'license_number' => 'SIM-SYNC-01',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);
    $origin = Location::create(['name' => 'Loket Bandung', 'code' => 'BDG-S1', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $dest = Location::create(['name' => 'Loket Jakarta', 'code' => 'JKT-S1', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);
    $route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $dest->id, 'price' => 125000, 'duration' => '3 Jam', 'is_active' => true]);
    $vehicle = Vehicle::create(['plate_number' => 'D 9090 SYN', 'name' => 'HiAce Sync', 'type' => 'Minibus', 'capacity' => 10, 'status' => 'siap_jalan']);
    $vehicle->ensureSeatsExist();

    // Earlier empty schedule at 06:00
    $emptySchedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '06:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    // Later schedule at 14:00 with real bookings from user & staff loket
    $bookedSchedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '14:00:00',
        'price' => 125000,
        'available_seats' => 8,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $seat1 = Seat::where('vehicle_id', $vehicle->id)->orderBy('id')->firstOrFail();
    $seat2 = Seat::where('vehicle_id', $vehicle->id)->orderBy('id')->skip(1)->firstOrFail();

    $onlineBooking = Booking::create([
        'booking_code' => 'TRV-SYNC-ONLINE',
        'user_id' => $customer->id,
        'schedule_id' => $bookedSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $onlineBooking->id,
        'seat_id' => $seat1->id,
        'name' => 'Penumpang Online Asli',
        'phone' => '081200001111',
        'boarding_status' => 'belum_hadir',
    ]);

    $loketBooking = Booking::create([
        'booking_code' => 'TRV-SYNC-LOKET',
        'user_id' => $customer->id,
        'schedule_id' => $bookedSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'checked_in',
        'source' => 'offline',
    ]);

    BookingPassenger::create([
        'booking_id' => $loketBooking->id,
        'seat_id' => $seat2->id,
        'name' => 'Penumpang Loket Asli',
        'phone' => '081200002222',
        'boarding_status' => 'boarded',
    ]);

    $response = $this->actingAs($driverUser)->get(route('dashboard.driver.penumpang'));

    $response->assertOk();
    $response->assertSee('Penumpang Online Asli');
    $response->assertSee('Penumpang Loket Asli');
    $response->assertDontSee('Citra Kirana');
    $response->assertSee('14:00');

    // Empty schedule should remain empty (no fake bookings created)
    expect(Booking::where('schedule_id', $emptySchedule->id)->count())->toBe(0);
});

test('driver dashboard does not fabricate fake passengers when schedule has no bookings', function () {
    $driverUser = User::factory()->create(['role' => 'driver', 'status' => 'active']);
    $driver = Driver::create([
        'user_id' => $driverUser->id,
        'phone' => '081234567891',
        'license_number' => 'SIM-SYNC-02',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $origin = Location::create(['name' => 'Loket Bandung 2', 'code' => 'BDG-S2', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $dest = Location::create(['name' => 'Loket Jakarta 2', 'code' => 'JKT-S2', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);
    $route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $dest->id, 'price' => 125000, 'duration' => '3 Jam', 'is_active' => true]);
    $vehicle = Vehicle::create(['plate_number' => 'D 9091 SYN', 'name' => 'HiAce Empty', 'type' => 'Minibus', 'capacity' => 10, 'status' => 'siap_jalan']);
    $vehicle->ensureSeatsExist();

    $schedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '08:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $response = $this->actingAs($driverUser)->get(route('dashboard.driver.index'));

    $response->assertOk();
    $response->assertDontSee('Citra Kirana');
    expect(Booking::where('schedule_id', $schedule->id)->count())->toBe(0);
});
