<?php

use App\Models\Booking;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->origin = Location::firstOrCreate(
        ['code' => 'BDO-V4'],
        ['name' => 'Bandung Dipatiukur', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-V4'],
        ['name' => 'Jakarta Semanggi', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $this->origin->id, 'destination_id' => $this->destination->id],
        ['price' => 125000, 'duration' => '2 Jam 30 Menit', 'is_active' => true]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 7777 V4'],
        ['name' => 'Toyota HiAce Premio', 'type' => 'HiAce', 'capacity' => 10, 'status' => 'available']
    );

    $this->vehicle->ensureSeatsExist();

    $this->seat1 = Seat::where('vehicle_id', $this->vehicle->id)->where('seat_number', '1')->first();
    $this->seat2 = Seat::where('vehicle_id', $this->vehicle->id)->where('seat_number', '2')->first();
});

test('BUG-20: selectSeat and lockSeats reject schedules that are cancelled or already departed (USR-BK-10)', function () {
    $user = User::factory()->create(['role' => 'user', 'status' => 'active']);

    // 1. Jadwal di masa lalu (sudah lewat waktu keberangkatan)
    $pastSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::yesterday()->toDateString(),
        'departure_time' => '08:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'scheduled',
    ]);

    // Akses selectSeat untuk jadwal kemarin -> Ditolak / redirect error
    $resSelectPast = $this->actingAs($user)->get(route('dashboard.user.booking.pilih-kursi', $pastSchedule));
    $resSelectPast->assertRedirect();
    $resSelectPast->assertSessionHas('error');

    // POST lockSeats untuk jadwal kemarin -> Ditolak / back dengan error
    $resLockPast = $this->actingAs($user)->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $pastSchedule->id,
        'passengers' => 1,
        'seat_ids' => [$this->seat1->id],
    ]);
    $resLockPast->assertRedirect();
    $resLockPast->assertSessionHas('error');

    // 2. Jadwal dibatalkan di masa depan
    $cancelledSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'cancelled',
    ]);

    // Akses selectSeat untuk jadwal dibatalkan -> Ditolak / redirect error
    $resSelectCancelled = $this->actingAs($user)->get(route('dashboard.user.booking.pilih-kursi', $cancelledSchedule));
    $resSelectCancelled->assertRedirect();
    $resSelectCancelled->assertSessionHas('error');

    // POST lockSeats untuk jadwal dibatalkan -> Ditolak / back dengan error
    $resLockCancelled = $this->actingAs($user)->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $cancelledSchedule->id,
        'passengers' => 1,
        'seat_ids' => [$this->seat1->id],
    ]);
    $resLockCancelled->assertRedirect();
    $resLockCancelled->assertSessionHas('error');
});

test('BUG-21: deleting customer account does not delete historical bookings/refunds and admin destroyUser blocks deleting customer with transactions (ADM-USR-09)', function () {
    $admin = User::factory()->create(['role' => 'admin_utama', 'status' => 'active']);
    $customer = User::factory()->create(['role' => 'user', 'name' => 'Customer Transaksi', 'status' => 'active']);

    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '14:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-BUG21-TEST',
        'user_id' => $customer->id,
        'schedule_id' => $schedule->id,
        'contact_name' => $customer->name,
        'contact_phone' => '081234567890',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'ORDER-BUG21-01',
        'payment_method' => 'BCA Virtual Account',
        'gross_amount' => 125000,
        'status' => 'paid',
    ]);

    // Admin mencoba menghapus pelanggan yang memiliki riwayat booking -> Ditolak
    $response = $this->actingAs($admin)->delete(route('dashboard.admin.kelola-user.destroy', $customer));
    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::find($customer->id))->not->toBeNull();

    // Verifikasi pada tingkat database: jika user dihapus langsung, relasi foreign key tidak menghapus booking/payment
    $orphanCustomer = User::factory()->create(['role' => 'user']);
    $orphanBooking = Booking::create([
        'booking_code' => 'TRV-ORPHAN-01',
        'user_id' => $orphanCustomer->id,
        'schedule_id' => $schedule->id,
        'contact_name' => 'Orphan User',
        'contact_phone' => '081299998888',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
    ]);

    $orphanCustomer->delete();
    $freshBooking = Booking::find($orphanBooking->id);
    expect($freshBooking)->not->toBeNull();
    expect($freshBooking->user_id)->toBeNull();
});

test('BUG-22: ensureUserPresets initializes presets consistently without unit test conditional branches', function () {
    $user = User::factory()->create(['role' => 'user', 'name' => 'User Presets Test', 'phone' => '089912345678']);

    // Akses promo yang memanggil ensureUserPresets
    $response = $this->actingAs($user)->get(route('dashboard.user.user-promo'));
    $response->assertOk();

    // Penumpang utama terbuat
    $primaryPassenger = $user->savedPassengers()->where('is_primary', true)->first();
    expect($primaryPassenger)->not->toBeNull();
    expect($primaryPassenger->name)->toBe($user->name);

    // Voucher default terbuat
    expect($user->vouchers()->where('code', 'HEMATLIBURAN')->exists())->toBeTrue();
    expect($user->vouchers()->where('code', 'KOMPENSASI50')->exists())->toBeTrue();
});

test('BUG-23: submitReview rejects uncompleted, unpaid, cancelled, or future trip reviews (USR-TK-12)', function () {
    $user = User::factory()->create(['role' => 'user']);

    // 1. Pesanan masih pending (belum bayar)
    $schedPending = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::yesterday()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'completed',
    ]);

    $bookingPending = Booking::create([
        'booking_code' => 'TRV-REV-PENDING',
        'user_id' => $user->id,
        'schedule_id' => $schedPending->id,
        'contact_name' => $user->name,
        'contact_phone' => '081234567890',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'pending',
    ]);

    $resPending = $this->actingAs($user)->post(route('dashboard.user.tiket.review', $bookingPending), [
        'cleanliness_rating' => 5,
        'driver_rating' => 5,
        'punctuality_rating' => 5,
        'comment' => 'Bagus sekali',
    ]);
    $resPending->assertRedirect();
    $resPending->assertSessionHas('error');
    expect(TripReview::where('booking_id', $bookingPending->id)->exists())->toBeFalse();

    // 2. Pesanan dibatalkan
    $bookingCancelled = Booking::create([
        'booking_code' => 'TRV-REV-CANCEL',
        'user_id' => $user->id,
        'schedule_id' => $schedPending->id,
        'contact_name' => $user->name,
        'contact_phone' => '081234567890',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'cancelled',
    ]);

    $resCancelled = $this->actingAs($user)->post(route('dashboard.user.tiket.review', $bookingCancelled), [
        'cleanliness_rating' => 5,
        'driver_rating' => 5,
        'punctuality_rating' => 5,
    ]);
    $resCancelled->assertRedirect();
    $resCancelled->assertSessionHas('error');
    expect(TripReview::where('booking_id', $bookingCancelled->id)->exists())->toBeFalse();

    // 3. Pesanan lunas tapi jadwal belum jalan (masih di masa depan)
    $schedFuture = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '15:00:00',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'scheduled',
        'trip_status' => 'scheduled',
    ]);

    $bookingFuture = Booking::create([
        'booking_code' => 'TRV-REV-FUTURE',
        'user_id' => $user->id,
        'schedule_id' => $schedFuture->id,
        'contact_name' => $user->name,
        'contact_phone' => '081234567890',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'completed',
    ]);
    Payment::create([
        'booking_id' => $bookingFuture->id,
        'order_id' => 'ORDER-REV-FUTURE',
        'payment_method' => 'Transfer',
        'gross_amount' => 125000,
        'status' => 'paid',
    ]);

    $resFuture = $this->actingAs($user)->post(route('dashboard.user.tiket.review', $bookingFuture), [
        'cleanliness_rating' => 5,
        'driver_rating' => 5,
        'punctuality_rating' => 5,
    ]);
    $resFuture->assertRedirect();
    $resFuture->assertSessionHas('error');
    expect(TripReview::where('booking_id', $bookingFuture->id)->exists())->toBeFalse();

    // 4. Pesanan lunas, status completed, dan jadwal sudah lewat -> Berhasil disimpan
    $bookingValid = Booking::create([
        'booking_code' => 'TRV-REV-VALID',
        'user_id' => $user->id,
        'schedule_id' => $schedPending->id,
        'contact_name' => $user->name,
        'contact_phone' => '081234567890',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'completed',
    ]);
    Payment::create([
        'booking_id' => $bookingValid->id,
        'order_id' => 'ORDER-REV-VALID',
        'payment_method' => 'Transfer',
        'gross_amount' => 125000,
        'status' => 'paid',
    ]);

    $resValid = $this->actingAs($user)->post(route('dashboard.user.tiket.review', $bookingValid), [
        'cleanliness_rating' => 5,
        'driver_rating' => 4,
        'punctuality_rating' => 5,
        'comment' => 'Perjalanan sangat nyaman dan aman.',
    ]);
    $resValid->assertRedirect();
    $resValid->assertSessionHas('success');
    expect(TripReview::where('booking_id', $bookingValid->id)->exists())->toBeTrue();
});

test('BUG-24: DatabaseSeeder calls UserSeeder, and LoginController accepts admin and customer_service role aliases (AUTH-14)', function () {
    // 1. Verifikasi DatabaseSeeder memanggil UserSeeder
    $dbSeederSource = file_get_contents(base_path('database/seeders/DatabaseSeeder.php'));
    expect($dbSeederSource)->toContain('UserSeeder::class');

    // 2. Akun dengan role 'admin' di database dapat login memilih role 'admin_utama'
    $adminUser = User::create([
        'name' => 'Admin Alias Test',
        'email' => 'admin.alias@travel.test',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'access_code' => Hash::make('ADMIN123'),
        'status' => 'active',
    ]);

    $resAdmin = $this->post(route('login.process'), [
        'email' => 'admin.alias@travel.test',
        'password' => 'password123',
        'role' => 'admin_utama',
        'access_code' => 'ADMIN123',
    ]);
    $resAdmin->assertRedirect(route('dashboard.admin.index'));
    $this->assertAuthenticatedAs($adminUser);
    auth()->logout();

    // 3. Akun dengan role 'customer_service' di database dapat login memilih role 'cs'
    $csUser = User::create([
        'name' => 'CS Alias Test',
        'email' => 'cs.alias@travel.test',
        'password' => Hash::make('password123'),
        'role' => 'customer_service',
        'access_code' => Hash::make('CS-2026-TRAVEL'),
        'status' => 'active',
    ]);

    $resCs = $this->post(route('login.process'), [
        'email' => 'cs.alias@travel.test',
        'password' => 'password123',
        'role' => 'cs',
        'access_code' => 'CS-2026-TRAVEL',
    ]);
    $resCs->assertRedirect(route('dashboard.customer-service.index'));
    $this->assertAuthenticatedAs($csUser);
    auth()->logout();

    // 4. Role 'admin' dan 'customer_service' juga valid jika dikirim langsung sebagai input role
    $resDirectAdmin = $this->post(route('login.process'), [
        'email' => 'admin.alias@travel.test',
        'password' => 'password123',
        'role' => 'admin',
        'access_code' => 'ADMIN123',
    ]);
    $resDirectAdmin->assertRedirect(route('dashboard.admin.index'));
    $this->assertAuthenticatedAs($adminUser);
});
