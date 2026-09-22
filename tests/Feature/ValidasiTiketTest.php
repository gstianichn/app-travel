<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->staff = User::firstOrCreate(
        ['email' => 'staff.test@travel.test'],
        [
            'name' => 'Staf Boarding Loket',
            'phone' => '081234567890',
            'role' => 'staff',
            'status' => 'active',
            'password' => Hash::make('password'),
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
        ['distance_km' => 150, 'duration' => '3 Jam', 'price' => 120000, 'is_active' => true]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 7812 AB'],
        ['name' => 'Toyota HiAce Premio', 'capacity' => 10, 'status' => 'active']
    );

    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'vehicle_id' => $this->vehicle->id,
            'departure_date' => Carbon::today()->toDateString(),
            'departure_time' => '13:00:00',
        ],
        [
            'price' => 120000,
            'status' => 'Boarding Dibuka',
            'available_seats' => 10,
        ]
    );
});

test('staff can access validasi tiket page', function () {
    $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.validasi-tiket'))
        ->assertOk()
        ->assertSee('Validasi Tiket')
        ->assertSee('Boarding Pass')
        ->assertSee('Pemindai QR')
        ->assertSee('Daftar Centang Penumpang (Checklist)');
});

test('guest cannot access validasi tiket page', function () {
    $this->get(route('dashboard.admin-biasa.validasi-tiket'))
        ->assertRedirect();
});

test('valid ticket code is successfully validated and marked as boarded', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-001',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '1A'],
        ['seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']
    );

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Andi Wijaya',
        'phone' => '081234567890',
        'boarding_status' => 'belum_hadir',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-TEST-001',
        'qr_code' => 'QR-TKV-TEST-001',
        'status' => 'Valid',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'QR-TKV-TEST-001',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'passenger_name' => 'Andi Wijaya',
            'seat_number' => '1A',
            'booking_code' => 'TKV-TEST-001',
        ]);

    expect($ticket->fresh()->status)->toBe('Boarded');
    expect($ticket->fresh()->boarded_at)->not->toBeNull();
    expect($passenger->fresh()->boarding_status)->toBe('boarded');
});

test('validation rejects ticket if already scanned and boarded', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-002',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '1B'],
        ['seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Siti Aminah',
        'phone' => '081234567891',
        'boarding_status' => 'boarded',
        'boarded_at' => Carbon::now()->subMinutes(10),
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-TEST-002',
        'qr_code' => 'QR-TKV-TEST-002',
        'status' => 'Boarded',
        'boarded_at' => Carbon::now()->subMinutes(10),
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-002',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'already_boarded',
        ]);
});

test('validation rejects ticket if booking is unpaid', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-003',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'pending',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '2A'],
        ['seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Rian Kurniawan',
        'phone' => '081234567892',
        'boarding_status' => 'belum_hadir',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-003',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'unpaid',
        ]);
});

test('validation rejects ticket if schedule does not match', function () {
    $otherSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '16:00:00',
        'price' => 120000,
        'status' => 'Persiapan',
        'available_seats' => 10,
    ]);

    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-004',
        'schedule_id' => $otherSchedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '2B'],
        ['seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Bambang Soleh',
        'phone' => '081234567893',
        'boarding_status' => 'belum_hadir',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-004',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'wrong_schedule',
        ]);
});

test('staff can update passenger status manually to boarded or no show', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-005',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '3A'],
        ['seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']
    );

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Dewi Lestari',
        'phone' => '081234567894',
        'boarding_status' => 'belum_hadir',
    ]);

    // Ubah ke boarded
    $res1 = $this->actingAs($this->staff)
        ->patchJson(route('dashboard.admin-biasa.validasi-tiket.update-status', $passenger->id), [
            'status' => 'boarded',
        ]);

    $res1->assertOk()->assertJson(['success' => true, 'status' => 'boarded']);
    expect($passenger->fresh()->boarding_status)->toBe('boarded');

    // Ubah ke no_show
    $res2 = $this->actingAs($this->staff)
        ->patchJson(route('dashboard.admin-biasa.validasi-tiket.update-status', $passenger->id), [
            'status' => 'no_show',
        ]);

    $res2->assertOk()->assertJson(['success' => true, 'status' => 'no_show']);
    expect($passenger->fresh()->boarding_status)->toBe('no_show');
});

test('simulated quick chips return expected responses', function () {
    $resSuccess = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-8920-JKT',
        ]);
    $resSuccess->assertOk()->assertJson(['success' => true, 'passenger_name' => 'Siti Rahmawati']);

    $resAlreadyScanned = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-ALREADY-SCAN',
        ]);
    $resAlreadyScanned->assertStatus(422)->assertJson(['success' => false, 'reason' => 'already_boarded']);

    $resUnpaid = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-UNPAID',
        ]);
    $resUnpaid->assertStatus(422)->assertJson(['success' => false, 'reason' => 'unpaid']);
});
