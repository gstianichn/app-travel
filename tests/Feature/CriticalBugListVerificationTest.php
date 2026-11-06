<?php

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
        'access_code' => Hash::make('ADMIN123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pool Buah Batu (Bandung)',
        'code' => 'BDO-BUG',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Pool Semanggi (Jakarta)',
        'code' => 'JKT-BUG',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 150000,
        'weekend_price' => 175000,
        'duration' => '3 Jam',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 9901 BUG',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->driverUserA = User::factory()->create([
        'name' => 'Driver A',
        'email' => 'driver.a@travel.test',
        'phone' => '081211110001',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password123'),
        'access_code' => Hash::make('DRV-2026-AAAA'),
    ]);

    $this->driverA = Driver::create([
        'user_id' => $this->driverUserA->id,
        'phone' => '081211110001',
        'license_number' => 'SIM-DRV-AAAA',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2),
        'emergency_contact_name' => 'Keluarga A',
        'emergency_contact_phone' => '081299990001',
        'emergency_contact_relation' => 'Istri',
        'status' => 'standby',
    ]);

    $this->driverUserB = User::factory()->create([
        'name' => 'Driver B',
        'email' => 'driver.b@travel.test',
        'phone' => '081211110002',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password123'),
        'access_code' => Hash::make('DRV-2026-BBBB'),
    ]);

    $this->driverB = Driver::create([
        'user_id' => $this->driverUserB->id,
        'phone' => '081211110002',
        'license_number' => 'SIM-DRV-BBBB',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2),
        'emergency_contact_name' => 'Keluarga B',
        'emergency_contact_phone' => '081299990002',
        'emergency_contact_relation' => 'Istri',
        'status' => 'standby',
    ]);

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driverA->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '09:00',
        'price' => 150000,
        'available_seats' => 10,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);
});

test('BUG-01 (PAY-05, PAY-06): webhook menolak request tanpa signature_key atau tanpa transaction_status dan menolak signature salah', function () {
    AppSetting::set('gateway_server_key', 'midtrans-secret-key-test');

    $user = User::factory()->create(['role' => 'user', 'status' => 'active']);
    $booking = Booking::create([
        'booking_code' => 'TRV-BUG01-001',
        'user_id' => $user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'ORD-TRV-BUG01-001',
        'payment_method' => 'Virtual Account BCA',
        'gross_amount' => 150000,
        'status' => 'Pending',
    ]);

    // PAY-05: Hanya mengirim order_id tanpa signature_key & tanpa transaction_status -> 403 Forbidden
    $this->postJson(route('payment.webhook'), [
        'order_id' => 'ORD-TRV-BUG01-001',
    ])->assertStatus(403)->assertJson(['message' => 'Invalid signature']);

    // PAY-05b: Mengirim transaction_status settlement tanpa signature_key -> 403 Forbidden
    $this->postJson(route('payment.webhook'), [
        'order_id' => 'ORD-TRV-BUG01-001',
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '150000',
    ])->assertStatus(403)->assertJson(['message' => 'Invalid signature']);

    // PAY-06: Mengirim signature_key palsu -> 403 Forbidden
    $this->postJson(route('payment.webhook'), [
        'order_id' => 'ORD-TRV-BUG01-001',
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '150000',
        'signature_key' => 'invalid-signature-key-12345',
    ])->assertStatus(403)->assertJson(['message' => 'Invalid signature']);

    expect($booking->fresh()->status)->toBe('Pending');
    expect($booking->payment->fresh()->status)->toBe('Pending');
});

test('BUG-02 (AUTH-09, AUTH-16, ADM-USR-07, ADM-STF-06): akun inactive atau blocked ditolak di login penumpang, login petugas, dan login Google', function () {
    $inactiveUser = User::factory()->create([
        'email' => 'inactive.user@travel.test',
        'role' => 'user',
        'status' => 'inactive',
        'password' => Hash::make('password123'),
    ]);

    $inactiveStaff = User::factory()->create([
        'email' => 'inactive.staff@travel.test',
        'role' => 'staff',
        'status' => 'inactive',
        'password' => Hash::make('password123'),
        'access_code' => Hash::make('STF-2026-TEST'),
    ]);

    // AUTH-16 & ADM-USR-07: Login penumpang nonaktif ditolak
    $this->post(route('user.login'), [
        'email' => $inactiveUser->email,
        'password' => 'password123',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();

    // AUTH-09 & ADM-STF-06: Login petugas nonaktif ditolak
    $this->post(route('login'), [
        'email' => $inactiveStaff->email,
        'password' => 'password123',
        'role' => 'staff',
        'access_code' => 'STF-2026-TEST',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();

    // Login Google akun nonaktif ditolak
    $this->post(route('auth.google.verify'), [
        'email' => $inactiveUser->email,
        'name' => $inactiveUser->name,
    ])->assertRedirect(route('user.login'))->assertSessionHasErrors('google');
    $this->assertGuest();
});

test('BUG-03 (ADM-STF-02, ADM-STF-03): kode akses staf di-hash Bcrypt dan staf tanpa input kode akses dibuatkan kode akses otomatis serta dapat login tanpa error 500', function () {
    // ADM-STF-02: Buat staf dengan kode akses eksplisit
    $this->actingAs($this->admin)->post(route('dashboard.admin.kelola-staff.store'), [
        'name' => 'Staf Baru Kode',
        'email' => 'staf.kode@travel.test',
        'phone' => '081233334444',
        'branch' => 'Pool Bandung (Dipatiukur)',
        'position' => 'Staf Loket',
        'access_code' => 'STF-NEW-01',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'permissions' => ['create_booking', 'checkin_ticket'],
    ])->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $staffWithCode = User::where('email', 'staf.kode@travel.test')->firstOrFail();
    expect(Hash::check('STF-NEW-01', $staffWithCode->access_code))->toBeTrue();

    // ADM-STF-03: Buat staf tanpa mengisi kode akses -> kode digenerate otomatis
    $storeWithoutCode = $this->actingAs($this->admin)->post(route('dashboard.admin.kelola-staff.store'), [
        'name' => 'Staf Tanpa Kode',
        'email' => 'staf.auto@travel.test',
        'phone' => '081233335555',
        'branch' => 'Pool Bandung (Dipatiukur)',
        'position' => 'Staf Loket',
        'access_code' => '',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $storeWithoutCode->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $flashMsg = (string) $storeWithoutCode->getSession()->get('success');
    preg_match('/Kode akses login:\s*(STF-\d{4}-[A-Z0-9]{4})/', $flashMsg, $matches);
    $generatedCode = $matches[1] ?? null;
    expect($generatedCode)->not->toBeNull();

    // Login menggunakan staf pertama (STF-NEW-01)
    Auth::logout();
    $this->flushSession();

    $this->post(route('login'), [
        'email' => 'staf.kode@travel.test',
        'password' => 'password123',
        'role' => 'staff',
        'access_code' => 'STF-NEW-01',
    ])->assertRedirect(route('dashboard.admin-biasa.index'));
    $this->assertAuthenticatedAs($staffWithCode);

    // Login menggunakan staf kedua (kode otomatis)
    Auth::logout();
    $this->flushSession();

    $staffAuto = User::where('email', 'staf.auto@travel.test')->firstOrFail();
    $this->post(route('login'), [
        'email' => 'staf.auto@travel.test',
        'password' => 'password123',
        'role' => 'staff',
        'access_code' => $generatedCode,
    ])->assertRedirect(route('dashboard.admin-biasa.index'));
    $this->assertAuthenticatedAs($staffAuto);
});

test('BUG-04 (ADM-DRV-02): driver baru dari Kelola Driver mendapat kode akses ter-hash dan berhasil login', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.kelola-driver.store'), [
        'name' => 'Driver Baru Test',
        'email' => 'driver.baru@travel.test',
        'phone' => '081277778888',
        'password' => 'password123',
        'license_number' => 'SIM-DRV-BARU-01',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2)->toDateString(),
        'emergency_contact_name' => 'Kontak Darurat',
        'emergency_contact_phone' => '081266665555',
        'emergency_contact_relation' => 'Istri',
        'status' => 'standby',
    ]);

    $response->assertRedirect(route('dashboard.admin.kelola-driver.index'));
    $flash = (string) $response->getSession()->get('success');
    preg_match('/kode akses login:\s*(DRV-\d{4}-[A-Z0-9]{4})/', $flash, $matches);
    $driverCode = $matches[1] ?? null;
    expect($driverCode)->not->toBeNull();

    Auth::logout();
    $this->flushSession();

    $this->post(route('login'), [
        'email' => 'driver.baru@travel.test',
        'password' => 'password123',
        'role' => 'driver',
        'access_code' => $driverCode,
    ])->assertRedirect(route('dashboard.driver.index'));

    $driverUser = User::where('email', 'driver.baru@travel.test')->firstOrFail();
    $this->assertAuthenticatedAs($driverUser);
});

test('BUG-05 (USR-BK-09, X-03): kursi booking checked_in maupun Paid/Pending kapital tidak bisa dipesan ulang (anti double booking)', function () {
    $userA = User::factory()->create(['role' => 'user', 'status' => 'active']);
    $userB = User::factory()->create(['role' => 'user', 'status' => 'active']);
    $seats = Seat::where('vehicle_id', $this->vehicle->id)->orderBy('id')->take(3)->get();

    // 1. Booking A berstatus checked_in (setelah penumpang check-in mandiri)
    $bookingCheckedIn = Booking::create([
        'booking_code' => 'TRV-CHKIN-01',
        'user_id' => $userA->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'checked_in',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $bookingCheckedIn->id,
        'seat_id' => $seats[0]->id,
        'name' => 'Penumpang Checked In',
        'boarding_status' => 'boarded',
    ]);

    // 2. Booking manual berstatus 'Paid' (huruf kapital) dan 'Pending' (huruf kapital tanpa expired_at)
    $bookingManualPaid = Booking::create([
        'booking_code' => 'TRV-MAN-PAID',
        'user_id' => $userA->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'walk_in',
    ]);
    BookingPassenger::create([
        'booking_id' => $bookingManualPaid->id,
        'seat_id' => $seats[1]->id,
        'name' => 'Penumpang Manual Paid',
    ]);

    $bookingManualPending = Booking::create([
        'booking_code' => 'TRV-MAN-PEND',
        'user_id' => $userA->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'walk_in',
        'expired_at' => null,
    ]);
    BookingPassenger::create([
        'booking_id' => $bookingManualPending->id,
        'seat_id' => $seats[2]->id,
        'name' => 'Penumpang Manual Pending',
    ]);

    // User B membuka halaman pilih kursi -> ketiga kursi tampil terisi/terkunci di occupancyMap
    $seatPage = $this->actingAs($userB)->get(route('dashboard.user.booking.pilih-kursi', [
        'schedule' => $this->schedule->id,
        'passengers' => 1,
    ]));
    $seatPage->assertOk();
    $seatMap = collect($seatPage->viewData('occupancyMap'))->flatten(1)->keyBy('seat_id');

    expect($seatMap[$seats[0]->id]['occupancy']['status'])->toBe('paid')
        ->and($seatMap[$seats[1]->id]['occupancy']['status'])->toBe('paid')
        ->and($seatMap[$seats[2]->id]['occupancy']['status'])->toBe('locked');

    // User B mencoba mengunci salah satu dari ketiga kursi tersebut -> semua ditolak
    foreach ([$seats[0]->id, $seats[1]->id, $seats[2]->id] as $occupiedSeatId) {
        $lockResp = $this->actingAs($userB)
            ->from(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->schedule->id]))
            ->post(route('dashboard.user.booking.lock-seats'), [
                'schedule_id' => $this->schedule->id,
                'passengers' => 1,
                'seat_ids' => [$occupiedSeatId],
            ]);

        $lockResp->assertSessionHas('error');
    }

    expect(Booking::where('user_id', $userB->id)->count())->toBe(0);
});

test('BUG-06 (DRV-12, DRV-13): driver lain ditolak 403 saat mengakses jadwal driver A dan mark_paid=1 ditolak untuk booking online belum lunas', function () {
    $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);
    $seat = Seat::where('vehicle_id', $this->vehicle->id)->firstOrFail();

    $onlineUnpaidBooking = Booking::create([
        'booking_code' => 'TRV-DRV-UNPAID',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    $passenger = BookingPassenger::create([
        'booking_id' => $onlineUnpaidBooking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang Online Belum Bayar',
        'phone' => '081255556666',
    ]);

    Payment::create([
        'booking_id' => $onlineUnpaidBooking->id,
        'order_id' => 'ORD-TRV-DRV-UNPAID',
        'payment_method' => 'Virtual Account BCA',
        'gross_amount' => 150000,
        'status' => 'Pending',
    ]);

    // DRV-12: Driver B mencoba mengubah status trip, GPS, laporan insiden, SOS, dan check-in pada jadwal milik Driver A -> 403
    $this->actingAs($this->driverUserB)
        ->post(route('dashboard.driver.schedule.status', $this->schedule->id), ['status' => 'pickup'])
        ->assertStatus(403);

    $this->actingAs($this->driverUserB)
        ->postJson(route('dashboard.driver.schedule.gps', $this->schedule->id), [
            'latitude' => -6.9147,
            'longitude' => 107.6098,
        ])
        ->assertStatus(403);

    $this->actingAs($this->driverUserB)
        ->post(route('dashboard.driver.schedule.incident', $this->schedule->id), [
            'category' => 'ban_bocor',
            'severity' => 'sedang',
            'description' => 'Coba lapor di jadwal driver lain',
        ])
        ->assertStatus(403);

    $this->actingAs($this->driverUserB)
        ->postJson(route('dashboard.driver.schedule.sos', $this->schedule->id), [
            'description' => 'Coba SOS di jadwal driver lain',
        ])
        ->assertStatus(403);

    // DRV-13: Driver B mencoba check-in + mark_paid=1 pada penumpang jadwal Driver A -> 403
    $this->actingAs($this->driverUserB)
        ->patch(route('dashboard.driver.passenger.checkin', $passenger->id), [
            'boarding_status' => 'boarded',
            'mark_paid' => 1,
        ])
        ->assertStatus(403);

    // DRV-13b: Bahkan Driver A sendiri tidak bisa menggunakan mark_paid=1 untuk melunasi booking online VA yang belum dibayar -> 403
    $this->actingAs($this->driverUserA)
        ->patch(route('dashboard.driver.passenger.checkin', $passenger->id), [
            'boarding_status' => 'boarded',
            'mark_paid' => 1,
        ])
        ->assertStatus(403);

    expect($onlineUnpaidBooking->fresh()->status)->toBe('Pending');
});
