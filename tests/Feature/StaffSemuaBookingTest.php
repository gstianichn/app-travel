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
    $this->staff = User::factory()->create([
        'name' => 'Staf Pool Dipatiukur',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Pool Dipatiukur Bandung',
        'password' => Hash::make('password123'),
        'permissions' => ['create_booking', 'view_passengers', 'manage_bookings', 'cancel_booking', 'reschedule_booking'],
    ]);

    $this->origin = Location::create([
        'code' => 'BDG-DU',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-SMG',
        'name' => 'Pool Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 125000,
        'duration' => '2 Jam 30 Menit',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 9999 MNG',
        'name' => 'Toyota HiAce Commuter',
        'type' => 'HiAce',
        'capacity' => 10,
        'status' => 'available',
    ]);

    $this->seat1 = Seat::create([
        'vehicle_id' => $this->vehicle->id,
        'seat_number' => '1',
        'seat_row' => 1,
        'seat_column' => 1,
        'is_active' => true,
    ]);

    $this->seat2 = Seat::create([
        'vehicle_id' => $this->vehicle->id,
        'seat_number' => '2',
        'seat_row' => 1,
        'seat_column' => 2,
        'is_active' => true,
    ]);

    $this->seat3 = Seat::create([
        'vehicle_id' => $this->vehicle->id,
        'seat_number' => '3',
        'seat_row' => 2,
        'seat_column' => 1,
        'is_active' => true,
    ]);

    $this->scheduleToday = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '15:00:00',
        'price' => 125000,
        'available_seats' => 8,
        'status' => 'scheduled',
    ]);

    $this->scheduleTomorrow = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'scheduled',
    ]);
});

test('halaman semua booking menyatukan pesanan online dan offline dengan indikator tipe pesanan', function () {
    // 1. Pesanan Online
    $onlineBooking = Booking::create([
        'booking_code' => 'TRV-ONL-1234',
        'user_id' => User::factory()->create(['name' => 'Ahmad Online', 'role' => 'user'])->id,
        'schedule_id' => $this->scheduleToday->id,
        'contact_name' => 'Ahmad Online',
        'contact_phone' => '081211112222',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $onlineBooking->id,
        'seat_id' => $this->seat1->id,
        'name' => 'Ahmad Online',
        'phone' => '081211112222',
        'boarding_status' => 'pending',
    ]);

    // 2. Pesanan Offline (Kasir / Loket)
    $offlineBooking = Booking::create([
        'booking_code' => 'TRV-OFF-5678',
        'user_id' => null,
        'schedule_id' => $this->scheduleToday->id,
        'contact_name' => 'Budi Kasir Loket',
        'contact_phone' => '081333334444',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'offline',
    ]);
    BookingPassenger::create([
        'booking_id' => $offlineBooking->id,
        'seat_id' => $this->seat2->id,
        'name' => 'Budi Kasir Loket',
        'phone' => '081333334444',
        'boarding_status' => 'boarded',
    ]);

    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.booking'));

    $response->assertStatus(200);
    $response->assertSee('Semua Booking');
    $response->assertSee('Manajemen Booking');
    $response->assertSee('Tipe Pesanan');

    // Kedua booking harus tampil di satu tabel terpadu
    $response->assertSee('TRV-ONL-1234');
    $response->assertSee('Ahmad Online');
    $response->assertSee('Online (App)');

    $response->assertSee('TRV-OFF-5678');
    $response->assertSee('Budi Kasir Loket');
    $response->assertSee('Offline (Loket)');
});

test('filter tipe pesanan memfilter transaksi online atau offline secara tepat', function () {
    // 1. Online
    Booking::create([
        'booking_code' => 'TRV-FILTER-ONL',
        'schedule_id' => $this->scheduleToday->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
        'contact_name' => 'User Mobile',
    ]);

    // 2. Offline
    Booking::create([
        'booking_code' => 'TRV-FILTER-OFF',
        'schedule_id' => $this->scheduleToday->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'offline',
        'contact_name' => 'User Walkin',
    ]);

    // Request filter: Online
    $onlResponse = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.booking', ['type' => 'online']));
    $onlResponse->assertStatus(200);
    $onlResponse->assertSee('TRV-FILTER-ONL');
    $onlResponse->assertDontSee('TRV-FILTER-OFF');

    // Request filter: Offline
    $offResponse = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.booking', ['type' => 'offline']));
    $offResponse->assertStatus(200);
    $offResponse->assertSee('TRV-FILTER-OFF');
    $offResponse->assertDontSee('TRV-FILTER-ONL');
});

test('staff dapat melihat detail booking melalui endpoint JSON modal', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-DETAIL-99',
        'schedule_id' => $this->scheduleToday->id,
        'contact_name' => 'Siti Aminah',
        'contact_phone' => '081299991111',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->seat1->id,
        'name' => 'Siti Aminah',
        'phone' => '081299991111',
        'boarding_status' => 'boarded',
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-DETAIL-99',
        'qr_code' => 'QR-DETAIL-99',
        'status' => 'Boarded',
    ]);

    $response = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.booking.detail', $booking->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'booking' => [
            'booking_code' => 'TRV-DETAIL-99',
            'customer_name' => 'Siti Aminah',
            'customer_phone' => '081299991111',
            'is_offline' => false,
        ],
        'schedule' => [
            'origin' => 'Pool Dipatiukur (Bandung)',
            'destination' => 'Pool Semanggi (Jakarta)',
        ],
    ]);
});

test('staff dapat membatalkan pesanan dari halaman manajemen booking', function () {
    $this->scheduleToday->available_seats = 8;
    $this->scheduleToday->save();

    $booking = Booking::create([
        'booking_code' => 'TRV-BATAL-01',
        'schedule_id' => $this->scheduleToday->id,
        'contact_name' => 'Dedi Batalkan',
        'contact_phone' => '081277778888',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'offline',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->seat1->id,
        'name' => 'Dedi Batalkan',
        'boarding_status' => 'pending',
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-BATAL-01',
        'qr_code' => 'QR-BATAL-01',
        'status' => 'Confirmed',
    ]);

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.booking.cancel', $booking->id), [
        'reason' => 'Customer meminta pembatalan langsung di kasir loket.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    expect($booking->fresh()->status)->toBe('Cancelled')
        ->and($booking->ticket->fresh()->status)->toBe('Cancelled');
});

test('staff dapat mengubah jadwal pelanggan (reschedule) ke jadwal lain', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-RESCH-01',
        'schedule_id' => $this->scheduleToday->id,
        'contact_name' => 'Hendra Reschedule',
        'contact_phone' => '081255556666',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->seat1->id,
        'name' => 'Hendra Reschedule',
        'boarding_status' => 'pending',
    ]);

    // 1. Cek data opsi jadwal alternatif
    $resData = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.booking.reschedule-data', $booking->id));
    $resData->assertStatus(200);
    $resData->assertJson(['success' => true]);
    expect($resData->json('available_schedules'))->not->toBeEmpty();

    // 2. Lakukan reschedule ke jadwal besok
    $updateResponse = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.booking.reschedule', $booking->id), [
        'new_schedule_id' => $this->scheduleTomorrow->id,
        'new_seats' => [$this->seat3->id],
        'reschedule_reason' => 'Pelanggan ingin berangkat besok pagi.',
    ]);

    $updateResponse->assertStatus(200);
    $updateResponse->assertJson(['success' => true]);

    expect($booking->fresh()->schedule_id)->toBe($this->scheduleTomorrow->id)
        ->and($passenger->fresh()->seat_id)->toBe($this->seat3->id)
        ->and($booking->fresh()->reschedule_reason)->toBe('Pelanggan ingin berangkat besok pagi.');
});
