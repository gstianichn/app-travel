<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
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

test('validation rejects ticket if booking is cancelled', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-CANCELLED',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'cancelled',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '4A'],
        ['seat_row' => '2', 'seat_column' => 1, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Fajar Pratama',
        'phone' => '081234567895',
        'boarding_status' => 'belum_hadir',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-CANCELLED',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'cancelled',
            'title' => 'Tiket Telah Dibatalkan',
        ]);
});

test('validation rejects ticket if refund is approved', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-REFUNDED',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    Refund::create([
        'booking_id' => $booking->id,
        'user_id' => $customer->id,
        'original_amount' => 120000,
        'amount' => 90000,
        'cancellation_fee' => 30000,
        'refund_percentage' => 75,
        'reason' => 'Perubahan rencana mendadak',
        'status' => 'disetujui',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '4B'],
        ['seat_row' => '2', 'seat_column' => 2, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Ayu Lestari',
        'phone' => '081234567896',
        'boarding_status' => 'belum_hadir',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-REFUNDED',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'refunded',
            'title' => 'Tiket Telah Di-refund',
        ]);
});

test('validation rejects ticket if refund is pending', function () {
    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-REFUND-PENDING',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    Refund::create([
        'booking_id' => $booking->id,
        'user_id' => $customer->id,
        'original_amount' => 120000,
        'amount' => 90000,
        'cancellation_fee' => 30000,
        'refund_percentage' => 75,
        'reason' => 'Sakit',
        'status' => 'menunggu',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-REFUND-PENDING',
            'schedule_id' => $this->schedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'refund_pending',
            'title' => 'Tiket Dalam Proses Refund',
        ]);
});

test('validation rejects ticket if trip has already departed', function () {
    $departedSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '08:00:00',
        'price' => 120000,
        'status' => 'Departed',
        'trip_status' => 'departed',
        'departed_at' => Carbon::now()->subMinutes(45),
        'available_seats' => 10,
    ]);

    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-DEPARTED',
        'schedule_id' => $departedSchedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '5A'],
        ['seat_row' => '3', 'seat_column' => 1, 'status' => 'Available']
    );

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Hendro',
        'phone' => '081234567897',
        'boarding_status' => 'belum_hadir',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-DEPARTED',
            'schedule_id' => $departedSchedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'trip_departed',
            'title' => 'Armada Sudah Berangkat',
        ]);
});

test('validation rejects ticket if departure date is expired in the past', function () {
    $pastSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::yesterday()->toDateString(),
        'departure_time' => '13:00:00',
        'price' => 120000,
        'status' => 'Selesai',
        'available_seats' => 10,
    ]);

    $customer = User::factory()->create();

    $booking = Booking::create([
        'booking_code' => 'TKV-TEST-EXPIRED',
        'schedule_id' => $pastSchedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-TEST-EXPIRED',
            'schedule_id' => $pastSchedule->id,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'expired',
            'title' => 'Tiket Sudah Kadaluwarsa',
        ]);
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

    $resCancelled = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-CANCELLED',
        ]);
    $resCancelled->assertStatus(422)->assertJson(['success' => false, 'reason' => 'cancelled']);

    $resRefunded = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-REFUNDED',
        ]);
    $resRefunded->assertStatus(422)->assertJson(['success' => false, 'reason' => 'refunded']);

    $resExpired = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-EXPIRED',
        ]);
    $resExpired->assertStatus(422)->assertJson(['success' => false, 'reason' => 'expired']);

    $resDeparted = $this->actingAs($this->staff)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-DEPARTED',
        ]);
    $resDeparted->assertStatus(422)->assertJson(['success' => false, 'reason' => 'trip_departed']);
});

test('user simulated scan check-in synchronizes to staff validasi-tiket and manifes as boarded', function () {
    $customer = User::factory()->create(['role' => 'user']);

    $booking = Booking::create([
        'booking_code' => 'TKV-USER-CHECKIN',
        'schedule_id' => $this->schedule->id,
        'user_id' => $customer->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'confirmed',
    ]);

    $seat = Seat::firstOrCreate(
        ['vehicle_id' => $this->vehicle->id, 'seat_number' => '10A'],
        ['seat_row' => '5', 'seat_column' => 1, 'status' => 'Available']
    );

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Pratama Wicaksana',
        'phone' => '081234567800',
        'boarding_status' => 'belum_hadir',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-SYNC-001',
        'qr_code' => 'QR-TKV-USER-CHECKIN',
        'status' => 'Valid',
    ]);

    // 1. User performs simulated check-in scan in user account
    $resUser = $this->actingAs($customer)
        ->post(route('dashboard.user.tiket.check-in', $ticket->id));

    $resUser->assertRedirect();
    $resUser->assertSessionHas('success');

    // Verify DB updated
    expect($ticket->fresh()->status)->toBe('Boarded');
    expect($ticket->fresh()->boarded_at)->not->toBeNull();
    expect($passenger->fresh()->boarding_status)->toBe('boarded');
    expect($passenger->fresh()->boarded_at)->not->toBeNull();

    // 2. Staff views validasi tiket page for this schedule
    $resStaff = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.validasi-tiket', ['schedule_id' => $this->schedule->id]));

    $resStaff->assertOk();
    $resStaff->assertSee('Pratama Wicaksana');
    $resStaff->assertSee('TKV-USER-CHECKIN');
    $resStaff->assertSee('Boarded');
    $resStaff->assertSee('Riwayat Validasi E-Ticket Terkini');
});
