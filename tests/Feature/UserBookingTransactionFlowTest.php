<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::firstOrCreate(
        ['email' => 'user.bookingtest@travel.test'],
        [
            'role' => 'user',
            'name' => 'Budi Penumpang Test',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
        ]
    );

    $this->origin = Location::firstOrCreate(
        ['code' => 'BDO-FLOW'],
        [
            'name' => 'Pool Dipatiukur (Bandung)',
            'city' => 'Bandung',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-FLOW'],
        [
            'name' => 'Semanggi (Jakarta)',
            'city' => 'Jakarta',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->route = Route::firstOrCreate(
        [
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
        ],
        [
            'price' => 125000,
            'duration' => '2 Jam 45 Menit',
            'is_active' => true,
        ]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 8888 FLOW'],
        [
            'name' => 'Toyota HiAce Premio Luxury',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'facilities' => ['AC Dingin', 'Reclining Seat', 'Port USB Charger'],
        ]
    );

    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'departure_date' => Carbon::today()->toDateString(),
            'departure_time' => '14:30',
        ],
        [
            'vehicle_id' => $this->vehicle->id,
            'price' => 125000,
            'available_seats' => 10,
            'status' => 'Scheduled',
        ]
    );
});

test('user can view beranda tab with search widget and active promos', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.index'));

    $response->assertOk();
    $response->assertSee('Pool Dipatiukur (Bandung)');
    $response->assertSee('Semanggi (Jakarta)');
    $response->assertSee('HEMATLIBURAN');
    $response->assertSee('Cari jadwal');
});

test('subflow 1: user can search schedules matching origin, destination, and active armada', function () {
    // The 14:30 departure is still bookable in the morning; afternoon runs would hide it as already departed.
    $this->travelTo(Carbon::today()->setTime(8, 0));

    $response = $this->actingAs($this->user)->get(route('dashboard.user.search', [
        'origin' => $this->origin->id,
        'destination' => $this->destination->id,
        'departure_date' => Carbon::today()->toDateString(),
        'passengers' => 2,
    ]));

    $response->assertOk();
    $response->assertSee('Toyota HiAce Premio');
    $response->assertSee('14:30');
    $response->assertSee('Rp 125.000');
    $response->assertSee('Pilih kursi');
});

test('subflow 1: user can search schedules without explicit passengers parameter and it defaults to 1', function () {
    $this->travelTo(Carbon::today()->setTime(8, 0));

    $response = $this->actingAs($this->user)->get(route('dashboard.user.search', [
        'origin' => $this->origin->id,
        'destination' => $this->destination->id,
        'departure_date' => Carbon::today()->toDateString(),
    ]));

    $response->assertOk();
    $response->assertSessionHasNoErrors();
    $response->assertSee('Toyota HiAce Premio');
    $response->assertSee('14:30');
    $response->assertSee('Pilih kursi');
});

test('subflow 2: user can view seat map and lock seats with 15-minute temporary reservation', function () {
    // 1. View seat map
    $response = $this->actingAs($this->user)->get(route('dashboard.user.booking.pilih-kursi', [
        'schedule' => $this->schedule->id,
        'passengers' => 2,
    ]));

    $response->assertOk();
    $response->assertSee('Denah kursi');
    $response->assertSee('15 menit');
    $response->assertSee('Sopir');
    $response->assertSee('Belakang');

    // 2. Lock 2 seats
    $seats = $this->vehicle->seats()->take(2)->get();
    expect($seats)->toHaveCount(2);

    $lockResponse = $this->actingAs($this->user)->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $this->schedule->id,
        'passengers' => 2,
        'seat_ids' => $seats->pluck('id')->toArray(),
    ]);

    $lockResponse->assertRedirect();

    // Verify booking created with pending status and expired_at
    $booking = Booking::where('user_id', $this->user->id)->latest()->first();
    expect($booking)->not->toBeNull();
    expect($booking->status)->toBe('pending');
    expect($booking->total_passenger)->toBe(2);
    expect((float) $booking->subtotal)->toBe(250000.0);
    expect($booking->expired_at->isFuture())->toBeTrue();
    expect($booking->passengers()->count())->toBe(2);
});

test('subflow 3: user can validate promo and save passenger data with quick passenger', function () {
    $seats = $this->vehicle->seats()->take(1)->get();

    $booking = Booking::create([
        'booking_code' => 'TRV-TEST-001',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    $passenger = $booking->passengers()->create([
        'seat_id' => $seats->first()->id,
        'name' => 'Penumpang Temp',
        'phone' => '081234567890',
    ]);

    // AJAX Validate Promo HEMATLIBURAN
    $promoResponse = $this->actingAs($this->user)->postJson(route('dashboard.user.booking.validate-promo'), [
        'booking_id' => $booking->id,
        'promo_code' => 'HEMATLIBURAN',
    ]);

    $promoResponse->assertOk();
    $promoResponse->assertJson([
        'valid' => true,
        'code' => 'HEMATLIBURAN',
    ]);

    // Save passenger data
    $saveResponse = $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking), [
        'booker_name' => 'Budi Santoso',
        'booker_phone' => '081234567890',
        'booker_email' => 'budi@test.com',
        'passengers' => [
            [
                'id' => $passenger->id,
                'name' => 'Budi Santoso (Utama)',
                'phone' => '081234567890',
                'special_request' => 'Dekat jendela',
            ],
        ],
        'promo_code' => 'HEMATLIBURAN',
    ]);

    $saveResponse->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $booking->refresh();
    expect((float) $booking->discount)->toBe(31250.0);
    expect((float) $booking->total_price)->toBe(93750.0);
    expect($passenger->fresh()->name)->toBe('Budi Santoso (Utama)');

    // Payment prepared
    $payment = Payment::where('booking_id', $booking->id)->first();
    expect($payment)->not->toBeNull();
    expect((float) $payment->gross_amount)->toBe(93750.0);
    expect($payment->status)->toBe('pending');
});

test('subflow 4: user can view payment page and simulate payment with instant ticket issuance', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-PAY-002',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-20260921-0002',
        'gross_amount' => 125000,
        'payment_method' => 'Virtual Account BCA',
        'account_number' => '8271000212345678',
        'status' => 'pending',
    ]);

    // View payment page
    $viewResponse = $this->actingAs($this->user)->get(route('dashboard.user.booking.pembayaran', $booking));
    $viewResponse->assertOk();
    $viewResponse->assertSee('Virtual Account BCA');
    $viewResponse->assertSee('8271000212345678');
    $viewResponse->assertSee('Tandai sudah dibayar');

    // Simulate Gateway Payment
    $simResponse = $this->actingAs($this->user)->post(route('dashboard.user.booking.simulate-payment', $booking));
    $simResponse->assertRedirect(route('dashboard.user.booking.sukses', $booking));

    $booking->refresh();
    expect($booking->status)->toBe('confirmed');

    $payment = $booking->payment->fresh();
    expect($payment->status)->toBe('paid');
    expect($payment->paid_at)->not->toBeNull();

    // Ticket generated
    $ticket = Ticket::where('booking_id', $booking->id)->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->qr_code)->toContain('QR-');

    // Success page
    $successResponse = $this->actingAs($this->user)->get(route('dashboard.user.booking.sukses', $booking));
    $successResponse->assertOk();
    $successResponse->assertSee('Tiketmu sudah terbit');
    $successResponse->assertSee($ticket->ticket_number);
});

test('subflow 4: payment gateway webhook automatically verifies payment and updates booking status', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-WH-003',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-WEBHOOK-999',
        'gross_amount' => 125000,
        'payment_method' => 'Virtual Account BCA',
        'account_number' => '8271999123456789',
        'status' => 'pending',
    ]);

    // Send public payment gateway webhook
    $webhookResponse = $this->postJson('/api/payment/webhook', [
        'order_id' => 'INV-WEBHOOK-999',
        'transaction_status' => 'settlement',
        'payment_type' => 'Virtual Account BCA',
        'gross_amount' => '125000.00',
    ]);

    $webhookResponse->assertOk();
    $webhookResponse->assertJson([
        'status' => 'success',
        'booking_code' => 'TRV-WH-003',
    ]);

    $booking->refresh();
    expect($booking->status)->toBe('confirmed');
    expect($booking->payment->fresh()->status)->toBe('paid');
    expect($booking->ticket)->not->toBeNull();
});

test('flow a: user can view active tickets and simulate pool check-in', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-CHK-004',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    $seat = $this->vehicle->seats()->first();
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Budi Penumpang Test',
        'boarding_status' => 'unboarded',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-2609-CHK',
        'qr_code' => 'QR-TRV-CHK-004',
        'status' => 'active',
    ]);

    // View Tiket Saya
    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-tiket'));
    $response->assertOk();
    $response->assertSee('TKT-2609-CHK');
    $response->assertSee('TRV-CHK-004');

    // Simulate Check-in in Pool
    $checkInResponse = $this->actingAs($this->user)->post(route('dashboard.user.tiket.check-in', $ticket));
    $checkInResponse->assertRedirect();

    $ticket->refresh();
    expect(strtolower($ticket->status))->toBeIn(['boarded', 'checked_in']);
    expect($ticket->boarded_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe('checked_in');
    expect($booking->passengers()->first()?->boarding_status)->toBe('boarded');
});

test('flow b: user can cancel booking and submit refund when within cut-off time and seat is freed', function () {
    // Schedule tomorrow to ensure plenty of time before cut-off
    $futureSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '14:30',
        'price' => 125000,
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $seat = $this->vehicle->seats()->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-REF-005',
        'user_id' => $this->user->id,
        'schedule_id' => $futureSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    $booking->passengers()->create([
        'seat_id' => $seat->id,
        'name' => 'Budi Refund',
    ]);

    expect($booking->canRefund())->toBeTrue();

    // Submit refund
    $refundResponse = $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
        'reason' => 'Perubahan rencana perjalanan mendadak',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi Santoso',
    ]);

    $refundResponse->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('refund_pending');

    // Check refund record
    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull();
    expect((float) $refund->cancellation_fee)->toBe(31250.0); // 25% of 125,000
    expect((float) $refund->amount)->toBe(93750.0); // 75% of 125,000
    expect($refund->status)->toBe('Diajukan');

    // Seat remains held until admin ACCs
    $futureSchedule->refresh();
    expect($futureSchedule->available_seats)->toBe(9);

    // Admin approves refund
    $admin = User::firstOrCreate(
        ['email' => 'admin.flowb@travel.test'],
        ['name' => 'Admin Flow B', 'role' => 'admin_utama', 'status' => 'active', 'password' => Hash::make('password')]
    );
    $this->actingAs($admin)->patch(route('dashboard.admin.refund.approve', $refund), [
        'cancellation_fee' => 31250,
        'admin_note' => 'ACC admin',
    ]);

    // Now booking is cancelled and seat freed back to public inventory!
    $booking->refresh();
    expect($booking->status)->toBe('Cancelled');
    $futureSchedule->refresh();
    expect($futureSchedule->available_seats)->toBe(10);
});

test('flow b: user cannot cancel booking if cut-off time has passed (within H-1 hour)', function () {
    // Schedule departing in 30 minutes (within H-1 hour)
    $urgentSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => Carbon::now()->addMinutes(30)->format('H:i'),
        'price' => 125000,
        'available_seats' => 9,
        'status' => 'Scheduled',
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-LATE-006',
        'user_id' => $this->user->id,
        'schedule_id' => $urgentSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'confirmed',
    ]);

    expect($booking->canRefund())->toBeFalse();

    $refundResponse = $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
        'reason' => 'Perubahan rencana mendadak',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi Santoso',
    ]);

    $refundResponse->assertSessionHas('error');
    $booking->refresh();
    expect($booking->status)->toBe('confirmed'); // Not cancelled
});

test('flow c: completed trips allow submitting multi-metric reviews connected to vehicle and driver', function () {
    $completedBooking = Booking::create([
        'booking_code' => 'TRV-REV-007',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'completed',
    ]);

    $reviewResponse = $this->actingAs($this->user)->post(route('dashboard.user.tiket.review', $completedBooking), [
        'cleanliness_rating' => 5,
        'driver_rating' => 5,
        'punctuality_rating' => 4,
        'comment' => 'Minibus sangat bersih, AC dingin, dan supir mengemudi dengan sangat hati-hati!',
    ]);

    $reviewResponse->assertRedirect();

    $review = TripReview::where('booking_id', $completedBooking->id)->first();
    expect($review)->not->toBeNull();
    expect($review->cleanliness_rating)->toBe(5);
    expect($review->driver_rating)->toBe(5);
    expect($review->punctuality_rating)->toBe(4);
    expect((float) $review->overall_rating)->toBe(4.67);
    expect($review->vehicle_id)->toBe($this->vehicle->id);
    expect($review->comment)->toContain('Minibus sangat bersih');
});

test('user searching connected route sees active available schedules with pricing and booking button', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.search', [
        'origin' => $this->origin->id,
        'destination' => $this->destination->id,
        'departure_date' => Carbon::today()->addDays(2)->toDateString(),
        'passengers' => 1,
    ]));

    $response->assertOk();
    $response->assertSee('keberangkatan');
    $response->assertSee('Pilih kursi');
    $response->assertSee('Rp');
});

test('user searching unconnected route sees friendly notification and transit hub suggestion', function () {
    $sukabumi = Location::firstOrCreate(
        ['code' => 'SMI-TEST'],
        ['name' => 'Pool Cisaat (Sukabumi)', 'city' => 'Sukabumi', 'is_active' => true]
    );
    $cirebon = Location::firstOrCreate(
        ['code' => 'CBN-TEST'],
        ['name' => 'Pool Kejaksan (Cirebon)', 'city' => 'Cirebon', 'is_active' => true]
    );

    // Ensure Sukabumi <-> Bandung and Bandung <-> Cirebon exist for hub detection
    Route::firstOrCreate(['origin_id' => $sukabumi->id, 'destination_id' => $this->origin->id], ['price' => 70000, 'duration' => '2 Jam 30 Menit', 'is_active' => true]);
    Route::firstOrCreate(['origin_id' => $this->origin->id, 'destination_id' => $cirebon->id], ['price' => 110000, 'duration' => '3 Jam', 'is_active' => true]);

    $response = $this->actingAs($this->user)->get(route('dashboard.user.search', [
        'origin' => $sukabumi->id,
        'destination' => $cirebon->id,
        'departure_date' => Carbon::today()->addDays(2)->toDateString(),
        'passengers' => 1,
    ]));

    $response->assertOk();
    $response->assertSee('Belum ada rute langsung dari');
    $response->assertSee('dengan dua perjalanan');
    $response->assertSee('Perjalanan 1');
    $response->assertSee('Pool Dipatiukur (Bandung)');
});

test('concurrency control: user cannot lock seat that is already held by another user within 15 minutes', function () {
    $otherUser = User::factory()->create(['role' => 'user']);
    $seat = $this->vehicle->seats()->first();

    // 1. Other user holds the seat
    $this->actingAs($otherUser)->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $this->schedule->id,
        'passengers' => 1,
        'seat_ids' => [$seat->id],
    ]);

    $heldBooking = Booking::where('user_id', $otherUser->id)->latest()->first();
    expect($heldBooking)->not->toBeNull();
    expect($heldBooking->status)->toBe('pending');
    expect($heldBooking->expired_at->isFuture())->toBeTrue();

    // 2. Primary user attempts to lock the exact same seat concurrently
    $response = $this->actingAs($this->user)->from(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->schedule->id]))
        ->post(route('dashboard.user.booking.lock-seats'), [
            'schedule_id' => $this->schedule->id,
            'passengers' => 1,
            'seat_ids' => [$seat->id],
        ]);

    $response->assertRedirect(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->schedule->id]));
    $response->assertSessionHas('error');

    // Ensure primary user has no booking created for this seat
    $primaryUserBooking = Booking::where('user_id', $this->user->id)->where('schedule_id', $this->schedule->id)->first();
    expect($primaryUserBooking)->toBeNull();
});

test('concurrency control: user can lock seat after previous seat lock has expired', function () {
    $otherUser = User::factory()->create(['role' => 'user']);
    $seat = $this->vehicle->seats()->first();

    // Other user has expired booking
    $expiredBooking = Booking::create([
        'booking_code' => 'TRV-EXPIRED-TEST',
        'user_id' => $otherUser->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'pending',
        'source' => 'online',
        'expired_at' => Carbon::now()->subMinutes(5),
    ]);

    BookingPassenger::create([
        'booking_id' => $expiredBooking->id,
        'seat_id' => $seat->id,
        'name' => 'Old Expired Passenger',
    ]);

    // Primary user locks the seat
    $response = $this->actingAs($this->user)->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $this->schedule->id,
        'passengers' => 1,
        'seat_ids' => [$seat->id],
    ]);

    $response->assertRedirect();
    $response->assertSessionMissing('error');

    expect($expiredBooking->fresh()->status)->toBe('cancelled');

    $newBooking = Booking::where('user_id', $this->user->id)->latest()->first();
    expect($newBooking)->not->toBeNull();
    expect($newBooking->status)->toBe('pending');
    expect($newBooking->passengers()->first()->seat_id)->toBe($seat->id);
});
