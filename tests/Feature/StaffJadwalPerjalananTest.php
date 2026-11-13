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
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->staff = User::factory()->create([
        'name' => 'Staf Pool Pasteur',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Pool Pasteur Bandung',
        'password' => Hash::make('password123'),
        'permissions' => ['view_routes', 'view_passengers', 'create_booking'],
    ]);

    $this->origin = Location::create([
        'code' => 'BDG-PST',
        'name' => 'Pool Pasteur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destJakarta = Location::create([
        'code' => 'JKT-SMG',
        'name' => 'Pool Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destBogor = Location::create([
        'code' => 'BGR-BRN',
        'name' => 'Pool Baranangsiang (Bogor)',
        'city' => 'Bogor',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->routeJakarta = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destJakarta->id,
        'price' => 120000,
        'duration' => '2 Jam 30 Menit',
        'is_active' => true,
    ]);

    $this->routeBogor = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destBogor->id,
        'price' => 110000,
        'duration' => '2 Jam',
        'is_active' => true,
    ]);

    $this->vehicleHiace = Vehicle::create([
        'plate_number' => 'D 1234 ABC',
        'name' => 'Toyota HiAce Premio',
        'type' => 'HiAce',
        'capacity' => 10,
        'status' => 'available',
    ]);

    $this->vehicleInnova = Vehicle::create([
        'plate_number' => 'B 5678 XYZ',
        'name' => 'Innova Zenix Travel',
        'type' => 'Innova',
        'capacity' => 6,
        'status' => 'available',
    ]);

    $driverUser = User::factory()->create(['name' => 'Pak Joko Supir', 'role' => 'driver', 'phone' => '081234567890']);
    $this->driver = Driver::create([
        'user_id' => $driverUser->id,
        'license_number' => 'SIM-B1-123456',
        'license_expired_at' => Carbon::now()->addYears(2),
        'phone' => '081234567890',
        'status' => 'available',
    ]);
});

test('halaman jadwal perjalanan mengelompokkan armada berdasarkan tujuan dan menghitung kapasitas terisi vs kosong', function () {
    $today = Carbon::today()->toDateString();

    // Jadwal 1: Tujuan Jakarta (Kapasitas 10, Terisi 2, Kosong 8)
    $schedJakarta = Schedule::create([
        'route_id' => $this->routeJakarta->id,
        'vehicle_id' => $this->vehicleHiace->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $today,
        'departure_time' => '09:00:00',
        'price' => 120000,
        'status' => 'scheduled',
    ]);

    $seat1 = Seat::create(['vehicle_id' => $this->vehicleHiace->id, 'seat_number' => '1', 'seat_row' => 1, 'seat_column' => 1, 'is_active' => true]);
    $seat2 = Seat::create(['vehicle_id' => $this->vehicleHiace->id, 'seat_number' => '2', 'seat_row' => 1, 'seat_column' => 2, 'is_active' => true]);

    $bookingJkt = Booking::create([
        'booking_code' => 'TRV-JKT-GRP1',
        'schedule_id' => $schedJakarta->id,
        'contact_name' => 'Penumpang Jakarta',
        'contact_phone' => '081200001111',
        'total_passenger' => 2,
        'subtotal' => 240000,
        'total_price' => 240000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create(['booking_id' => $bookingJkt->id, 'seat_id' => $seat1->id, 'name' => 'Penumpang 1', 'boarding_status' => 'boarded']);
    BookingPassenger::create(['booking_id' => $bookingJkt->id, 'seat_id' => $seat2->id, 'name' => 'Penumpang 2', 'boarding_status' => 'pending']);

    // Jadwal 2: Tujuan Bogor (Kapasitas 6, Terisi 0, Kosong 6)
    $schedBogor = Schedule::create([
        'route_id' => $this->routeBogor->id,
        'vehicle_id' => $this->vehicleInnova->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $today,
        'departure_time' => '10:30:00',
        'price' => 110000,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.perjalanan'));

    $response->assertStatus(200);
    $response->assertSee('Jadwal Perjalanan');
    $response->assertSee('Tujuan Jakarta');
    $response->assertSee('Tujuan Bogor');

    // Cek detail jadwal Jakarta: Jam, Plat Mobil, Nama Sopir, dan Kapasitas Tersedia
    $response->assertSee('09:00 WIB');
    $response->assertSee('D 1234 ABC');
    $response->assertSee('Pak Joko Supir');
    $response->assertSee('Kapasitas Tersedia: 8/10 Kursi (8 kosong, 2 terisi)');

    // Cek detail jadwal Bogor
    $response->assertSee('10:30 WIB');
    $response->assertSee('B 5678 XYZ');
    $response->assertSee('Kapasitas Tersedia: 6/6 Kursi (6 kosong, 0 terisi)');
});

test('endpoint manifest penumpang armada mengembalikan daftar penumpang dan nomor kursi', function () {
    $today = Carbon::today()->toDateString();

    $schedule = Schedule::create([
        'route_id' => $this->routeJakarta->id,
        'vehicle_id' => $this->vehicleHiace->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $today,
        'departure_time' => '14:00:00',
        'price' => 120000,
        'status' => 'scheduled',
    ]);

    $seat = Seat::create(['vehicle_id' => $this->vehicleHiace->id, 'seat_number' => '1A', 'seat_row' => 1, 'seat_column' => 1, 'is_active' => true]);

    $booking = Booking::create([
        'booking_code' => 'TRV-MNF-01',
        'schedule_id' => $schedule->id,
        'contact_name' => 'Budi Manifes',
        'contact_phone' => '081299998888',
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Budi Manifes',
        'phone' => '081299998888',
        'boarding_status' => 'boarded',
    ]);

    $response = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.perjalanan.manifest', $schedule->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'schedule' => [
            'id' => $schedule->id,
            'plate_number' => 'D 1234 ABC',
            'driver_name' => 'Pak Joko Supir',
            'occupied_seats' => 1,
            'available_seats' => 9,
        ],
    ]);

    expect($response->json('passengers.0.passenger_name'))->toBe('Budi Manifes')
        ->and($response->json('passengers.0.seat_number'))->toBe('1A')
        ->and($response->json('passengers.0.is_boarded'))->toBeTrue();
});
