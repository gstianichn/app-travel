<?php

use App\Mail\TicketIssuedMail;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $origin = Location::create(['code' => 'BDO-GST', 'name' => 'Pool Dipatiukur (Bandung)', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $destination = Location::create(['code' => 'JKT-GST', 'name' => 'Semanggi (Jakarta)', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);

    $route = Route::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 125000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'B 7777 GST',
        'name' => 'Toyota HiAce Commuter',
        'type' => 'Toyota HiAce Commuter',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '09:30',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);
});

/**
 * Lock one seat as a guest and return the pending booking.
 */
function lockSeatAsGuest($test): Booking
{
    $seat = $test->vehicle->seats()->first();

    $test->post(route('dashboard.user.booking.lock-seats'), [
        'schedule_id' => $test->schedule->id,
        'passengers' => 1,
        'seat_ids' => [$seat->id],
    ])->assertRedirect();

    return Booking::latest('id')->firstOrFail();
}

test('public search lets guests go straight to the seat map', function () {
    $this->get(route('search', [
        'origin' => $this->schedule->route->origin_id,
        'destination' => $this->schedule->route->destination_id,
        'departure_date' => $this->schedule->departure_date->toDateString(),
        'passengers' => 1,
    ]))
        ->assertOk()
        ->assertSee(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->schedule->id, 'passengers' => 1]), false)
        ->assertDontSee('Masuk untuk pesan');

    $this->get(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->schedule->id, 'passengers' => 1]))
        ->assertOk()
        ->assertSee('Tahan kursi dan isi data');
});

test('guest can book with name, phone and email and reach the payment page', function () {
    $booking = lockSeatAsGuest($this);

    expect($booking->user_id)->toBeNull();
    expect($booking->status)->toBe('pending');

    $this->get(route('dashboard.user.booking.data-penumpang', $booking))
        ->assertOk()
        ->assertSee('Tidak wajib punya akun')
        ->assertSee('Masuk dengan Google')
        ->assertSee('e-ticket kami kirim ke email itu');

    $passenger = $booking->passengers()->first();

    $this->post(route('dashboard.user.booking.save-passenger', $booking), [
        'booker_name' => 'Rina Tamu',
        'booker_phone' => '081211112222',
        'booker_email' => 'rina.tamu@example.com',
        'passengers' => [['id' => $passenger->id, 'name' => 'Rina Tamu']],
        'payment_channel' => 'bca',
    ])->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $booking->refresh();
    expect($booking->contact_name)->toBe('Rina Tamu');
    expect($booking->contact_phone)->toBe('081211112222');
    expect($booking->contact_email)->toBe('rina.tamu@example.com');
    expect($booking->user_id)->toBeNull();

    $this->get(route('dashboard.user.booking.pembayaran', $booking))
        ->assertOk()
        ->assertSee($booking->booking_code)
        ->assertSee('rina.tamu@example.com');
});

test('a guest booking cannot be opened from another browser session', function () {
    $booking = lockSeatAsGuest($this);

    $this->flushSession();

    $this->get(route('dashboard.user.booking.data-penumpang', $booking))->assertForbidden();
    $this->get(route('dashboard.user.booking.pembayaran', $booking))->assertForbidden();
    $this->getJson(route('dashboard.user.booking.payment-status', $booking))->assertForbidden();
});

test('a logged-in passenger cannot open someone else\'s booking', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $intruder = User::factory()->create(['role' => 'user']);

    $this->actingAs($owner);
    $booking = lockSeatAsGuest($this);
    expect($booking->user_id)->toBe($owner->id);

    $this->actingAs($intruder)
        ->get(route('dashboard.user.booking.data-penumpang', $booking))
        ->assertForbidden();
});

test('paying a guest booking emails the e-ticket exactly once', function () {
    Mail::fake();

    $booking = lockSeatAsGuest($this);
    $booking->update(['contact_name' => 'Rina Tamu', 'contact_email' => 'rina.tamu@example.com']);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-GUEST-0001',
        'gross_amount' => 125000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    $settlement = [
        'order_id' => 'INV-GUEST-0001',
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'va_numbers' => [['bank' => 'bca', 'va_number' => '827100018899']],
    ];

    $this->postJson(route('payment.webhook'), $settlement)->assertOk();
    $ticketNumber = $booking->fresh()->ticket->ticket_number;

    // Midtrans retries webhooks; a repeat must not re-issue the ticket or email it again.
    $this->postJson(route('payment.webhook'), $settlement)->assertOk();

    expect($booking->fresh()->status)->toBe('confirmed');
    expect($booking->fresh()->ticket->ticket_number)->toBe($ticketNumber);

    Mail::assertSent(TicketIssuedMail::class, 1);
    Mail::assertSent(TicketIssuedMail::class, fn (TicketIssuedMail $mail) => $mail->hasTo('rina.tamu@example.com')
        && $mail->booking->is($booking));
});

test('the emailed e-ticket link opens without logging in, but only when signed', function () {
    $booking = lockSeatAsGuest($this);
    $booking->update(['status' => 'confirmed', 'contact_email' => 'rina.tamu@example.com']);
    $booking->ticket()->create(['ticket_number' => 'TKT-GST-0001', 'qr_code' => 'QR-'.$booking->booking_code, 'status' => 'active']);

    $this->flushSession();

    $this->get(URL::signedRoute('booking.e-ticket', $booking))
        ->assertOk()
        ->assertSee('Tiketmu sudah terbit')
        ->assertSee('TKT-GST-0001')
        ->assertSee('data-qr="QR-'.$booking->booking_code.'"', false);

    $this->get(route('booking.e-ticket', $booking))->assertForbidden();
});

test('logging in during checkout moves the guest booking to the account', function () {
    $booking = lockSeatAsGuest($this);
    $user = User::create([
        'name' => 'Rina Penumpang',
        'email' => 'rina@example.com',
        'password' => Hash::make('rahasia123'),
        'role' => 'user',
    ]);

    $returnTo = route('dashboard.user.booking.data-penumpang', $booking);

    $this->get(route('user.login', ['redirect' => $returnTo]))->assertOk();
    $this->post(route('user.login.store'), ['email' => 'rina@example.com', 'password' => 'rahasia123'])
        ->assertRedirect($returnTo);

    expect($booking->fresh()->user_id)->toBe($user->id);

    $this->get($returnTo)->assertOk()->assertDontSee('Tidak wajib punya akun');
});

test('the payment test shortcut is not available in production', function () {
    $booking = lockSeatAsGuest($this);

    app()->detectEnvironment(fn () => 'production');

    $this->withoutMiddleware(PreventRequestForgery::class)
        ->post(route('dashboard.user.booking.simulate-payment', $booking))
        ->assertNotFound();
    expect($booking->fresh()->status)->toBe('pending');
});
