<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    // Admin User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.improve@travel.test'],
        [
            'name' => 'Admin Utama Travel',
            'phone' => '08123456780',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]
    );

    // Active User
    $this->user = User::firstOrCreate(
        ['email' => 'user.improve@travel.test'],
        [
            'name' => 'User Biasa',
            'phone' => '08987654321',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]
    );

    // Inactive/Blocked User
    $this->blockedUser = User::firstOrCreate(
        ['email' => 'blocked.user@travel.test'],
        [
            'name' => 'User Terblokir',
            'phone' => '08987654322',
            'role' => 'user',
            'status' => 'inactive',
            'password' => Hash::make('password123'),
        ]
    );

    // Inactive Staff
    $this->inactiveStaff = User::firstOrCreate(
        ['email' => 'inactive.staff@travel.test'],
        [
            'name' => 'Staf Nonaktif',
            'phone' => '081299990001',
            'role' => 'staff',
            'status' => 'inactive',
            'access_code' => Hash::make('STF-OFF-01'),
            'password' => Hash::make('password123'),
        ]
    );

    // Locations & Route
    $this->origin = Location::firstOrCreate(
        ['code' => 'BDG-IMP'],
        ['name' => 'Bandung Pool Imp', 'city' => 'Bandung', 'address' => 'Jl. Dipatiukur No. 1', 'type' => 'pool', 'is_active' => true]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-IMP'],
        ['name' => 'Jakarta Pool Imp', 'city' => 'Jakarta', 'address' => 'Jl. Tebet No. 1', 'type' => 'drop_point', 'is_active' => true]
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $this->origin->id, 'destination_id' => $this->destination->id],
        ['price' => 120000, 'duration' => '3 Jam', 'is_active' => true]
    );

    // Vehicle & Driver
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 9999 IMP'],
        [
            'name' => 'HiAce Imp Unit',
            'type' => 'Toyota HiAce',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );
    $this->vehicle->ensureSeatsExist();

    $driverUser = User::firstOrCreate(
        ['email' => 'driver.improve@travel.test'],
        [
            'name' => 'Driver Imp',
            'phone' => '0855112233',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $driverUser->id],
        [
            'license_number' => 'SIM-IMP-01',
            'license_type' => 'B1',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Keluarga',
            'emergency_contact_phone' => '0855998877',
            'status' => 'standby',
        ]
    );

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDays(3)->toDateString(),
        'departure_time' => '10:00',
        'available_seats' => 10,
        'price' => 120000,
        'status' => 'Scheduled',
    ]);
});

// Item 1: Deactivated staff cannot login
test('deactivated staff cannot login through staff login page', function () {
    $response = $this->post(route('login.process'), [
        'email' => 'inactive.staff@travel.test',
        'password' => 'password123',
        'role' => 'staff',
        'access_code' => 'STF-OFF-01',
    ]);

    $response->assertSessionHasErrors(['email' => 'Akun Anda telah dinonaktifkan oleh Administrator.']);
    $this->assertGuest();
});

// Item 2: Admin can edit their name in settings and it updates Auth::user()->name
test('admin can update admin name in settings profile', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.pengaturan.profil'), [
            'admin_name' => 'Administrator Baru',
            'company_name' => 'PT Travel Mandiri Baru',
            'company_phone' => '022-123456',
        ]);

    $response->assertRedirect(route('dashboard.admin.pengaturan.index', ['tab' => 'profil']));
    $response->assertSessionHas('success');

    expect($this->admin->fresh()->name)->toBe('Administrator Baru');
});

// Item 3: Blocked user cannot login and sees exact notification "akun anda diblokir karena suatu kesalahan"
test('blocked user cannot login and receives exact error message', function () {
    $response = $this->post(route('user.login.store'), [
        'email' => 'blocked.user@travel.test',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'akun anda diblokir karena suatu kesalahan']);
    $this->assertGuest();
});

// Item 4: Passenger refund sets refund_pending, keeps seat occupied, requires admin approval
test('passenger refund requires admin approval before cancelling booking and releasing seats', function () {
    $seat = $this->vehicle->seats()->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-REFUND-PENDING',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'discount' => 0,
        'total_price' => 120000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang Refund',
        'phone' => '08987654321',
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-PENDING-01',
        'qr_code' => 'QR-PENDING-01',
        'status' => 'Issued',
        'issued_at' => now(),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-PENDING-01',
        'gross_amount' => 120000,
        'payment_method' => 'Transfer Bank BCA',
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $this->schedule->recalculateAvailableSeats();
    expect($this->schedule->available_seats)->toBe(9);

    // User submits refund request
    $response = $this->actingAs($this->user)
        ->post(route('dashboard.user.tiket.refund', $booking), [
            'reason' => 'Ada acara mendadak',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'User Biasa',
        ]);

    $response->assertSessionHas('success');

    // Booking must NOT be cancelled yet; status is refund_pending
    $booking->refresh();
    expect($booking->status)->toBe('refund_pending');

    // Seats must NOT be released yet (still occupied, available_seats remains 9)
    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(9);

    // Verify Refund record is Diajukan
    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull();
    expect($refund->status)->toBe('Diajukan');

    // Admin approves the refund
    $approveResponse = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.refund.approve', $refund), [
            'cancellation_fee' => 30000,
            'admin_note' => 'Disetujui admin',
        ]);

    $approveResponse->assertSessionHas('success');

    // Now booking is cancelled, payment refunded, seats released (available_seats becomes 10)
    $booking->refresh();
    expect($booking->status)->toBe('Cancelled');
    expect($booking->payment->fresh()->status)->toBe('Refunded');

    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(10);
});

// Item 6: Admin cancels ticket -> notifies user and automatically refunds
test('admin cancelling booking automatically refunds paid ticket and notifies user', function () {
    $seat = $this->vehicle->seats()->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-ADMIN-CANCEL',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 120000,
        'discount' => 0,
        'total_price' => 120000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang Admin Cancel',
        'phone' => '08987654321',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-ADMIN-CANCEL',
        'gross_amount' => 120000,
        'payment_method' => 'Transfer Bank BCA',
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $this->schedule->recalculateAvailableSeats();
    expect($this->schedule->available_seats)->toBe(9);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pemesanan.cancel', $booking));

    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('Cancelled');
    expect($booking->payment->fresh()->status)->toBe('Refunded');

    // 100% refund record automatically created and approved
    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull();
    expect($refund->status)->toBe('Disetujui');
    expect((int) $refund->amount)->toBe(120000);
    expect((int) $refund->cancellation_fee)->toBe(0);

    // Notification created for user
    $notification = $this->user->notifications()->latest()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['message'])->toContain('dibatalkan oleh pihak admin');
    expect($notification->data['message'])->toContain('otomatis dikembalikan');

    // Seat freed
    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(10);
});

// Item 7: Logo exists and is visible
test('logo file exists in public images folder', function () {
    expect(file_exists(public_path('images/logo-travelku.png')))->toBeTrue();
    expect(file_exists(public_path('images/logo-bustravelku.png')))->toBeTrue();
});
