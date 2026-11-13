<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingStatusNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->origin = Location::create([
        'code' => 'BDO-V3',
        'name' => 'Bandung Dipatiukur',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-V3',
        'name' => 'Jakarta Semanggi',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination2 = Location::create([
        'code' => 'SMG-V3',
        'name' => 'Semarang Simpang Lima',
        'city' => 'Semarang',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 125000,
        'weekend_price' => 150000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $this->route2 = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination2->id,
        'price' => 200000,
        'weekend_price' => 225000,
        'duration' => '5 Jam',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 1111 TST',
        'name' => 'Toyota HiAce Test 1',
        'type' => 'Minibus',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->vehicle2 = Vehicle::create([
        'plate_number' => 'D 2222 TST',
        'name' => 'Toyota HiAce Test 2',
        'type' => 'Minibus',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle2->ensureSeatsExist();

    $this->adminUser = User::create([
        'name' => 'Admin Utama Verif 3',
        'email' => 'admin.verif3.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'admin_utama',
        'status' => 'active',
    ]);

    $this->driverUser = User::create([
        'name' => 'Pak Driver Verif 3',
        'email' => 'driver.verif3.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    $this->driver = Driver::create([
        'user_id' => $this->driverUser->id,
        'license_number' => 'SIM-VERIF-3',
        'license_expired_at' => Carbon::now()->addYear(),
        'phone' => '081234567890',
        'status' => 'standby',
    ]);

    $this->customer = User::create([
        'name' => 'Pelanggan Verif 3',
        'email' => 'customer.verif3.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'user',
        'status' => 'active',
        'phone' => '085600001111',
    ]);
});

// ==========================================
// BUG-13 (ADM-SC-06b, ADM-SC-08)
// ==========================================
test('BUG-13: batch generate schedules skips schedule when vehicle has clash on another route at same date and time (ADM-SC-06b)', function () {
    $nextDate = Carbon::now()->addDays(2)->toDateString();

    // Jadwal awal di rute 1
    Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => $nextDate,
        'departure_time' => '10:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    // Generate jadwal massal untuk rute 2 menggunakan vehicle 1 pada jam 10:00 dan 14:00
    $response = $this->actingAs($this->adminUser)->post(route('dashboard.admin.rute.jadwal.batch-generate'), [
        'route_id' => $this->route2->id,
        'vehicle_id' => $this->vehicle->id,
        'start_date' => $nextDate,
        'end_date' => $nextDate,
        'departure_times' => ['10:00', '14:00'],
    ]);

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    // Jam 10:00 harus dilewati karena bentrok di rute 1, jam 14:00 berhasil dibuat
    expect(Schedule::where('route_id', $this->route2->id)->whereDate('departure_date', $nextDate)->where('departure_time', '10:00')->exists())->toBeFalse()
        ->and(Schedule::where('route_id', $this->route2->id)->whereDate('departure_date', $nextDate)->where('departure_time', '14:00')->exists())->toBeTrue();
});

test('BUG-13: assign vehicle and driver rejects vehicle clash and driver clash with other schedules (ADM-SC-08)', function () {
    $date = Carbon::now()->addDays(3)->toDateString();

    $schedule1 = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => $date,
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $schedule2 = Schedule::create([
        'route_id' => $this->route2->id,
        'vehicle_id' => $this->vehicle2->id,
        'departure_date' => $date,
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    // Ganti vehicle pada schedule2 ke vehicle1 (yang sudah dipakai schedule1 di jam dan tanggal yang sama)
    $response = $this->actingAs($this->adminUser)->patch(
        route('dashboard.admin.rute.jadwal.assign', $schedule2->id),
        ['vehicle_id' => $this->vehicle->id]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect($schedule2->fresh()->vehicle_id)->toBe($this->vehicle2->id);
});

test('BUG-13: assign new vehicle remaps passenger seats and rejects if capacity is insufficient (ADM-SC-08)', function () {
    $date = Carbon::now()->addDays(4)->toDateString();

    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => $date,
        'departure_time' => '09:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-MAP-01',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Paid',
    ]);

    $oldSeat = $this->vehicle->seats()->first();
    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $oldSeat->id,
        'name' => 'Penumpang Kursi 1',
        'phone' => '081299990001',
    ]);

    // Ganti ke vehicle 2
    $response = $this->actingAs($this->adminUser)->patch(
        route('dashboard.admin.rute.jadwal.assign', $schedule->id),
        ['vehicle_id' => $this->vehicle2->id]
    );

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    $passenger->refresh();
    // Kursi penumpang sekarang harus menunjuk kursi milik vehicle 2
    $newSeat = Seat::find($passenger->seat_id);
    expect($newSeat->vehicle_id)->toBe($this->vehicle2->id);
});

// ==========================================
// BUG-14 (ADM-SC-10)
// ==========================================
test('BUG-14: changing schedule status to cancelled cancels booking, creates approved refund, and notifies passenger (ADM-SC-10)', function () {
    Notification::fake();

    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::now()->addDays(5)->toDateString(),
        'departure_time' => '11:00',
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-CANC-01',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-CANC-01',
        'qr_code' => 'QR-TRV-CANC-01',
        'status' => 'Valid',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-CANC-01',
        'gross_amount' => 125000,
        'payment_method' => 'BCA Virtual Account',
        'status' => 'settlement',
    ]);

    $response = $this->actingAs($this->adminUser)->patch(
        route('dashboard.admin.rute.jadwal.update-status', $schedule->id),
        ['status' => 'Cancelled']
    );

    $response->assertRedirect(route('dashboard.admin.rute.jadwal'));
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($schedule->fresh()->status)->toBe('Cancelled')
        ->and($booking->status)->toBe('cancelled')
        ->and($booking->ticket->fresh()->status)->toBe('cancelled')
        ->and($booking->payment->fresh()->status)->toBe('Refunded');

    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull()
        ->and($refund->status)->toBe('Disetujui')
        ->and((float) $refund->amount)->toBe(125000.0);

    Notification::assertSentTo($this->customer, BookingStatusNotification::class);
});

// ==========================================
// BUG-15 (ADM-BK-10, ADM-BK-11)
// ==========================================
test('BUG-15: update reschedule rejects cancelled booking, rejects past schedule, and recalculates price difference (ADM-BK-10, ADM-BK-11)', function () {
    $oldSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::now()->addDays(5)->toDateString(),
        'departure_time' => '08:00',
        'price' => 100000,
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $newSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::now()->addDays(6)->toDateString(),
        'departure_time' => '13:00',
        'price' => 150000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-RES-01',
        'user_id' => $this->customer->id,
        'schedule_id' => $oldSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 100000,
        'total_price' => 100000,
        'status' => 'Paid',
    ]);

    $oldSeat = $this->vehicle->seats()->skip(1)->first();
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $oldSeat->id,
        'name' => 'Penumpang Reschedule',
        'phone' => '081299990002',
    ]);

    $targetSeat = $this->vehicle->seats()->skip(2)->first();

    // Uji 1: Sukses reschedule dan menghitung selisih harga (+50.000)
    $response = $this->actingAs($this->adminUser)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $newSchedule->id,
            'new_seats' => [$targetSeat->id],
            'reschedule_reason' => 'Pelanggan minta ganti hari',
        ]
    );

    $response->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));
    $booking->refresh();
    expect($booking->schedule_id)->toBe($newSchedule->id)
        ->and((float) $booking->total_price)->toBe(150000.0)
        ->and($booking->notes)->toContain('Selisih biaya tambahan: Rp 50.000');

    // Uji 2: Reschedule pada booking yang berstatus cancelled ditolak
    $booking->update(['status' => 'cancelled']);
    $response2 = $this->actingAs($this->adminUser)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $oldSchedule->id,
            'new_seats' => [$oldSeat->id],
            'reschedule_reason' => 'Coba ubah booking batal',
        ]
    );

    $response2->assertRedirect();
    $response2->assertSessionHas('error');
});

// ==========================================
// BUG-16 (ADM-BK-05, ADM-BK-06)
// ==========================================
test('BUG-16: manual booking uses schedule effective price, rejects seats of other vehicles, and does not attach to staff accounts (ADM-BK-05, ADM-BK-06)', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::now()->addDays(5)->toDateString(),
        'departure_time' => '15:00',
        'price' => 175000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $staffUser = User::create([
        'name' => 'Staf Pool Loket',
        'email' => 'staf.pool.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'admin_biasa',
        'status' => 'active',
        'phone' => '087788990011',
    ]);

    // Uji 1: Kursi milik armada lain ditolak
    $foreignSeat = $this->vehicle2->seats()->first();
    $responseForeign = $this->actingAs($this->adminUser)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $schedule->id,
        'selected_seats' => [$foreignSeat->id],
        'customer_name' => 'Pelanggan Uji',
        'customer_phone' => '089900001111',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);
    $responseForeign->assertRedirect();
    $responseForeign->assertSessionHas('error');

    // Uji 2: Booking valid memakai harga jadwal (175.000) dan tidak menempel ke akun staf saat nomor HP sama
    $validSeat = $this->vehicle->seats()->first();
    $responseValid = $this->actingAs($this->adminUser)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $schedule->id,
        'selected_seats' => [$validSeat->id],
        'customer_name' => 'Pelanggan Pakai HP Staf',
        'customer_phone' => '087788990011', // sama dengan staf
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);

    $responseValid->assertRedirect();
    $responseValid->assertSessionHas('success');

    $createdBooking = Booking::latest('id')->first();
    expect((float) $createdBooking->total_price)->toBe(175000.0)
        ->and($createdBooking->user_id)->not->toBe($staffUser->id)
        ->and($createdBooking->user->role)->toBe('user');
});

// ==========================================
// BUG-17 (STF-VT-08, STF-VT-12, STF-VT-13, STF-05)
// ==========================================
test('BUG-17: staff permission checkin_ticket is enforced, 404 on missing passenger, and checked_in status returns already boarded (STF-05, STF-VT-08, STF-VT-12)', function () {
    $staffNoPermission = User::create([
        'name' => 'Staf Tanpa Izin Checkin',
        'email' => 'staff.noperm.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'staff',
        'status' => 'active',
        'permissions' => ['view_routes'], // tidak punya checkin_ticket
    ]);

    $staffWithPermission = User::create([
        'name' => 'Staf Berizin Checkin',
        'email' => 'staff.perm.'.time().rand(100, 999).'@travel.test',
        'password' => Hash::make('password'),
        'role' => 'staff',
        'status' => 'active',
        'permissions' => ['checkin_ticket'],
    ]);

    // Uji STF-05: Staf tanpa hak akses ditolak 403
    $respForbidden = $this->actingAs($staffNoPermission)->get(route('dashboard.admin-biasa.validasi-tiket'));
    $respForbidden->assertStatus(403);

    $respAjaxForbidden = $this->actingAs($staffNoPermission)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'TRV-ANY-CODE',
    ]);
    $respAjaxForbidden->assertStatus(403);

    // Uji STF-VT-12: Update boarding passenger ID tidak ada -> 404
    $respNotFound = $this->actingAs($staffWithPermission)->patchJson(
        route('dashboard.admin-biasa.validasi-tiket.update-status', ['passenger' => 999999]),
        ['status' => 'boarded']
    );
    $respNotFound->assertStatus(404)
        ->assertJson(['success' => false]);

    // Uji STF-VT-08: Penumpang sudah check-in mandiri (checked_in) mendapat pesan "Tiket Sudah Pernah Di-Scan"
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '16:00',
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-CHKIN-01',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'checked_in', // penumpang sudah check-in mandiri
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-CHKIN-01',
        'qr_code' => 'QR-TRV-CHKIN-01',
        'status' => 'Boarded',
        'boarded_at' => now(),
    ]);

    $respScan = $this->actingAs($staffWithPermission)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'TRV-CHKIN-01',
    ]);

    $respScan->assertStatus(422)
        ->assertJson([
            'success' => false,
            'reason' => 'already_boarded',
            'title' => 'Tiket Sudah Pernah Di-Scan',
        ]);

    // Uji STF-VT-13: Scan kode demo TKV-UNPAID di production tidak mengembalikan hasil palsu (harus 404)
    app()->detectEnvironment(fn () => 'production');
    $respProd = $this->withoutMiddleware(PreventRequestForgery::class)
        ->actingAs($staffWithPermission)
        ->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => 'TKV-UNPAID',
        ]);
    $respProd->assertStatus(404)
        ->assertJson([
            'success' => false,
            'reason' => 'not_found',
            'title' => 'Tiket Tidak Ditemukan',
        ]);
    app()->detectEnvironment(fn () => 'testing');
});

// ==========================================
// BUG-18 (DRV-08, DRV-09)
// ==========================================
test('BUG-18: driver trip progression prevents jumping to completed and completion leaves pending bookings unpaid (DRV-08, DRV-09)', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => '17:00',
        'available_seats' => 8,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $paidBooking = Booking::create([
        'booking_code' => 'TRV-DRV-PAID',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Paid',
    ]);

    $pendingBooking = Booking::create([
        'booking_code' => 'TRV-DRV-PEND',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Pending', // Belum bayar
    ]);

    // Uji DRV-08: Lompat status dari scheduled langsung ke completed -> Ditolak 422
    $responseJump = $this->actingAs($this->driverUser)->postJson(
        route('dashboard.driver.schedule.status', $schedule->id),
        ['status' => 'completed']
    );
    $responseJump->assertStatus(422);

    // Ikuti alur status resmi: pickup -> departed -> arrived -> completed
    $this->actingAs($this->driverUser)->post(
        route('dashboard.driver.schedule.status', $schedule->id),
        ['status' => 'pickup']
    );
    $this->actingAs($this->driverUser)->post(
        route('dashboard.driver.schedule.status', $schedule->id),
        ['status' => 'departed']
    );
    $this->actingAs($this->driverUser)->post(
        route('dashboard.driver.schedule.status', $schedule->id),
        ['status' => 'arrived']
    );
    $this->actingAs($this->driverUser)->post(
        route('dashboard.driver.schedule.status', $schedule->id),
        ['status' => 'completed']
    );

    // Uji DRV-09: Booking lunas menjadi completed, booking pending tetap Pending!
    expect($paidBooking->fresh()->status)->toBe('completed')
        ->and($pendingBooking->fresh()->status)->toBe('Pending');
});

// ==========================================
// BUG-19 (USR-BK-18)
// ==========================================
test('BUG-19: saving passenger data or api charge on paid booking does not reset payment to pending (USR-BK-18)', function () {
    $schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::now()->addDays(5)->toDateString(),
        'departure_time' => '18:00',
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-PAID-01',
        'user_id' => $this->customer->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'paid',
    ]);

    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $this->vehicle->seats()->first()->id,
        'name' => 'Budi Santoso',
        'phone' => '081299990003',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-PAID-01',
        'gross_amount' => 125000,
        'payment_method' => 'BCA Virtual Account',
        'status' => 'settlement',
    ]);

    // Kirim ulang POST save-passenger/{id} pada booking yang sudah lunas
    $response = $this->actingAs($this->customer)->post(
        route('dashboard.user.booking.save-passenger', $booking->id),
        [
            'booker_name' => 'Budi Booker',
            'booker_phone' => '081299990003',
            'booker_email' => 'budi@travel.test',
            'passengers' => [
                [
                    'id' => $passenger->id,
                    'name' => 'Budi Santoso',
                    'phone' => '081299990003',
                ],
            ],
        ]
    );

    $response->assertRedirect(route('dashboard.user.booking.sukses', $booking->id));
    $booking->refresh();
    expect($booking->status)->toBe('paid')
        ->and($booking->payment->fresh()->status)->toBe('settlement');

    // Kirim API charge pada booking yang sudah lunas -> ditolak 422
    $respApi = $this->actingAs($this->customer)->postJson(
        route('api.payment.charge'),
        [
            'booking_id' => $booking->id,
            'payment_channel' => 'bca',
        ]
    );

    $respApi->assertStatus(422)
        ->assertJson(['status' => 'error']);

    expect($booking->payment->fresh()->status)->toBe('settlement');
});
