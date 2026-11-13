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
use Illuminate\Support\Str;

beforeEach(function () {
    $this->staff = User::factory()->create([
        'name' => 'Staf Pool Pasteur',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Pool Pasteur Bandung',
        'password' => Hash::make('password123'),
        'permissions' => ['checkin_ticket', 'view_passengers'],
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
        'plate_number' => 'D 7777 ABC',
        'name' => 'Toyota HiAce Luxury',
        'type' => 'HiAce',
        'capacity' => 10,
        'status' => 'available',
    ]);

    $this->vehicleInnova = Vehicle::create([
        'plate_number' => 'B 8888 DEF',
        'name' => 'Innova Zenix Travel',
        'type' => 'Innova',
        'capacity' => 6,
        'status' => 'available',
    ]);

    $this->seatHiace = Seat::create([
        'vehicle_id' => $this->vehicleHiace->id,
        'seat_number' => '1',
        'seat_row' => 1,
        'seat_column' => 1,
        'is_active' => true,
    ]);

    $this->seatInnova = Seat::create([
        'vehicle_id' => $this->vehicleInnova->id,
        'seat_number' => '1',
        'seat_row' => 1,
        'seat_column' => 1,
        'is_active' => true,
    ]);
});

test('halaman validasi tiket memuat area kamera scan di atas dan tabel data penumpang di bawah', function () {
    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.validasi-tiket'));

    $response->assertStatus(200);
    $response->assertSee('Validasi Tiket');
    $response->assertSee('Area Pemindai Kamera (Scan QR Code)');
    $response->assertSee('Data Penumpang Sudah Check-in');
    $response->assertSee('Tujuan Keberangkatan');
});

test('scan tiket online valid lunas jadwal hari ini berhasil mengubah status is_boarded dan update tabel realtime', function () {
    $today = Carbon::today()->toDateString();
    $schedule = Schedule::create([
        'route_id' => $this->routeJakarta->id,
        'vehicle_id' => $this->vehicleHiace->id,
        'departure_date' => $today,
        'departure_time' => '14:00:00',
        'price' => 120000,
        'available_seats' => 9,
        'status' => 'scheduled',
    ]);

    $passengerUser = User::factory()->create(['role' => 'user']);
    $bookingCode = 'TRV-VAL-'.strtoupper(Str::random(4));

    $booking = Booking::create([
        'booking_code' => $bookingCode,
        'user_id' => $passengerUser->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'paid',
        'source' => 'online',
        'contact_name' => 'Budi Santoso',
        'contact_phone' => '081234567890',
        'paid_at' => Carbon::now(),
    ]);

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->seatHiace->id,
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'boarding_status' => 'pending',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-'.$bookingCode,
        'qr_code' => 'QR-'.$bookingCode,
        'status' => 'Confirmed',
    ]);

    // Staff melakukan scan QR
    $scanResponse = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$bookingCode,
    ]);

    $scanResponse->assertStatus(200);
    $scanResponse->assertJson([
        'success' => true,
        'booking_code' => $bookingCode,
        'destination' => 'Pool Semanggi (Jakarta)',
        'is_boarded' => true,
    ]);

    // Verifikasi perubahan di database
    expect($ticket->fresh()->status)->toBe('Boarded')
        ->and($ticket->fresh()->boarded_at)->not->toBeNull()
        ->and($booking->fresh()->status)->toBe('checked_in')
        ->and($passenger->fresh()->boarding_status)->toBe('boarded')
        ->and($passenger->fresh()->isBoarded())->toBeTrue();

    // Verifikasi endpoint reload tabel: HANYA menampilkan penumpang hari ini yang is_boarded = true
    $tableResponse = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.validasi-tiket.boarded'));

    $tableResponse->assertStatus(200);
    $tableResponse->assertJson([
        'success' => true,
        'total' => 1,
    ]);
    expect($tableResponse->json('passengers.0.booking_code'))->toBe($bookingCode)
        ->and($tableResponse->json('passengers.0.is_boarded'))->toBeTrue()
        ->and($tableResponse->json('grouped_by_destination.Pool Semanggi (Jakarta)'))->toHaveCount(1);
});

test('scan tiket yang sudah pernah di-scan mengembalikan error 422 untuk memicu alert dan bunyi beep gagal', function () {
    $today = Carbon::today()->toDateString();
    $schedule = Schedule::create([
        'route_id' => $this->routeJakarta->id,
        'vehicle_id' => $this->vehicleHiace->id,
        'departure_date' => $today,
        'departure_time' => '15:00:00',
        'price' => 120000,
        'available_seats' => 9,
        'status' => 'scheduled',
    ]);

    $bookingCode = 'TRV-REP-'.strtoupper(Str::random(4));
    $booking = Booking::create([
        'booking_code' => $bookingCode,
        'user_id' => User::factory()->create()->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'checked_in',
        'source' => 'online',
        'paid_at' => Carbon::now(),
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->seatHiace->id,
        'name' => 'Rina Permata',
        'phone' => '081299990000',
        'boarding_status' => 'boarded',
        'boarded_at' => Carbon::now()->subMinutes(15),
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-'.$bookingCode,
        'qr_code' => 'QR-'.$bookingCode,
        'status' => 'Boarded',
        'boarded_at' => Carbon::now()->subMinutes(15),
    ]);

    // Scan kedua kali
    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$bookingCode,
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'reason' => 'already_boarded',
        'title' => 'Tiket Sudah Pernah Di-Scan',
    ]);
});

test('tabel data penumpang hari ini mengelompokkan penumpang berdasarkan tujuan keberangkatan', function () {
    $today = Carbon::today()->toDateString();

    // Jadwal 1: Tujuan Jakarta
    $schedJkt = Schedule::create([
        'route_id' => $this->routeJakarta->id,
        'vehicle_id' => $this->vehicleHiace->id,
        'departure_date' => $today,
        'departure_time' => '10:00:00',
        'price' => 120000,
        'status' => 'scheduled',
    ]);

    // Jadwal 2: Tujuan Bogor
    $schedBgr = Schedule::create([
        'route_id' => $this->routeBogor->id,
        'vehicle_id' => $this->vehicleInnova->id,
        'departure_date' => $today,
        'departure_time' => '11:00:00',
        'price' => 110000,
        'status' => 'scheduled',
    ]);

    // Penumpang A ke Jakarta (sudah scan/boarded)
    $b1 = Booking::create([
        'booking_code' => 'TRV-JKT-01',
        'user_id' => User::factory()->create()->id,
        'schedule_id' => $schedJkt->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'checked_in',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $b1->id,
        'seat_id' => $this->seatHiace->id,
        'name' => 'Penumpang Jakarta',
        'phone' => '0811111111',
        'boarding_status' => 'boarded',
        'boarded_at' => Carbon::now(),
    ]);
    Ticket::create([
        'booking_id' => $b1->id,
        'ticket_number' => 'TKT-JKT-01',
        'qr_code' => 'QR-TRV-JKT-01',
        'status' => 'Boarded',
        'boarded_at' => Carbon::now(),
    ]);

    // Penumpang B ke Bogor (sudah scan/boarded)
    $b2 = Booking::create([
        'booking_code' => 'TRV-BGR-02',
        'user_id' => User::factory()->create()->id,
        'schedule_id' => $schedBgr->id,
        'total_passenger' => 1,
        'subtotal' => 110000,
        'total_price' => 110000,
        'status' => 'checked_in',
        'source' => 'offline',
    ]);
    BookingPassenger::create([
        'booking_id' => $b2->id,
        'seat_id' => $this->seatInnova->id,
        'name' => 'Penumpang Bogor',
        'phone' => '0822222222',
        'boarding_status' => 'boarded',
        'boarded_at' => Carbon::now(),
    ]);
    Ticket::create([
        'booking_id' => $b2->id,
        'ticket_number' => 'TKT-BGR-02',
        'qr_code' => 'QR-TRV-BGR-02',
        'status' => 'Boarded',
        'boarded_at' => Carbon::now(),
    ]);

    // Penumpang C ke Jakarta tapi BELUM SCAN (tidak boleh muncul di tabel query)
    $b3 = Booking::create([
        'booking_code' => 'TRV-JKT-BELUM',
        'user_id' => User::factory()->create()->id,
        'schedule_id' => $schedJkt->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'total_price' => 120000,
        'status' => 'paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $b3->id,
        'seat_id' => $this->seatHiace->id,
        'name' => 'Penumpang Belum Naik',
        'phone' => '0833333333',
        'boarding_status' => 'pending',
    ]);

    $response = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.validasi-tiket.boarded'));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'total' => 2,
    ]);

    $grouped = $response->json('grouped_by_destination');
    expect(array_keys($grouped))->toContain('Pool Semanggi (Jakarta)', 'Pool Baranangsiang (Bogor)')
        ->and($grouped['Pool Semanggi (Jakarta)'])->toHaveCount(1)
        ->and($grouped['Pool Baranangsiang (Bogor)'])->toHaveCount(1)
        ->and($grouped['Pool Semanggi (Jakarta)'][0]['passenger_name'])->toBe('Penumpang Jakarta')
        ->and($grouped['Pool Baranangsiang (Bogor)'][0]['passenger_name'])->toBe('Penumpang Bogor');
});
