<?php

use App\Mail\TicketIssuedMail;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::firstOrCreate(
        ['email' => 'user.midtrans@travel.test'],
        [
            'role' => 'user',
            'name' => 'Penumpang Midtrans',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
        ]
    );

    $this->origin = Location::firstOrCreate(
        ['code' => 'BDO-MID'],
        [
            'name' => 'Dipatiukur Bandung',
            'city' => 'Bandung',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-MID'],
        [
            'name' => 'Semanggi Jakarta',
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
            'price' => 150000,
            'duration' => '2 Jam 30 Menit',
            'is_active' => true,
        ]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 9999 MID'],
        [
            'name' => 'Toyota HiAce Premio Luxury',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'facilities' => ['AC Dingin', 'Reclining Seat'],
        ]
    );
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'departure_date' => Carbon::today()->toDateString(),
            'departure_time' => '10:00',
        ],
        [
            'vehicle_id' => $this->vehicle->id,
            'price' => 150000,
            'available_seats' => 10,
            'status' => 'Scheduled',
        ]
    );
});

function createWebhookBooking($test, string $bookingCode, string $orderId, int $totalPrice = 150000): Booking
{
    $booking = Booking::create([
        'booking_code' => $bookingCode,
        'user_id' => $test->user->id,
        'schedule_id' => $test->schedule->id,
        'total_passenger' => 1,
        'subtotal' => $totalPrice,
        'discount' => 0,
        'total_price' => $totalPrice,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => $totalPrice,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    return $booking;
}

test('user can see payment channels in passenger data page before checkout', function () {
    $seats = $this->vehicle->seats()->take(1)->get();
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-001',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    $booking->passengers()->create([
        'seat_id' => $seats->first()->id,
        'name' => 'Penumpang Temp',
        'phone' => '081234567891',
    ]);

    $response = $this->actingAs($this->user)->get(route('dashboard.user.booking.data-penumpang', $booking));
    $response->assertOk();
    $response->assertSee('Cara bayar');
    $response->assertSee('Transfer virtual account');
    $response->assertSee('value="bca"', false);
    $response->assertSee('value="mandiri"', false);
    $response->assertSee('value="bni"', false);
    $response->assertSee('BRI (BRIVA)');
    $response->assertSee('value="bsi"', false);
    $response->assertSee('SeaBank');
    $response->assertSee('QRIS');
    $response->assertSee('GoPay');
    $response->assertSee('ShopeePay');
    $response->assertSee('Lanjut ke pembayaran');
});

test('user can checkout with Mandiri bill payment and receives biller code & bill key', function () {
    $seats = $this->vehicle->seats()->take(1)->get();
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-MDR',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    $passenger = $booking->passengers()->create([
        'seat_id' => $seats->first()->id,
        'name' => 'Penumpang Mandiri',
        'phone' => '081234567891',
    ]);

    $response = $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking), [
        'booker_name' => 'Budi Mandiri',
        'booker_phone' => '081234567891',
        'booker_email' => 'budi.mandiri@test.com',
        'passengers' => [
            [
                'id' => $passenger->id,
                'name' => 'Budi Mandiri',
                'phone' => '081234567891',
            ],
        ],
        'payment_category' => 'va',
        'payment_channel' => 'mandiri',
    ]);

    $response->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $payment = $booking->payment()->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_method)->toBe('Mandiri Bill Payment');
    expect($payment->bank_name)->toBe('Mandiri');
    expect($payment->account_number)->not->toBeNull();
    expect($payment->notes)->toContain('Biller Code');

    // Cek halaman pembayaran menampilkan biller code dan bill key
    $payView = $this->actingAs($this->user)->get(route('dashboard.user.booking.pembayaran', $booking));
    $payView->assertOk();
    $payView->assertSee('Mandiri Bill Payment');
    $payView->assertSee('Biller Code');
    $payView->assertSee('Livin');
});

test('user can checkout with SeaBank and view inter-bank transfer instructions to Permata VA', function () {
    $seats = $this->vehicle->seats()->take(1)->get();
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-SEA',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    $passenger = $booking->passengers()->create([
        'seat_id' => $seats->first()->id,
        'name' => 'Penumpang SeaBank',
        'phone' => '081234567891',
    ]);

    $response = $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking), [
        'booker_name' => 'SeaBank User',
        'booker_phone' => '081234567891',
        'booker_email' => 'seabank@test.com',
        'passengers' => [
            [
                'id' => $passenger->id,
                'name' => 'SeaBank User',
                'phone' => '081234567891',
            ],
        ],
        'payment_category' => 'va',
        'payment_channel' => 'seabank',
    ]);

    $response->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $payment = $booking->payment()->first();
    expect($payment)->not->toBeNull();
    expect($payment->bank_name)->toBe('SeaBank');
    expect($payment->account_number)->not->toBeNull();

    // Halaman pembayaran menampilkan panduan SeaBank
    $payView = $this->actingAs($this->user)->get(route('dashboard.user.booking.pembayaran', $booking));
    $payView->assertOk();
    $payView->assertSee('SeaBank');
    $payView->assertSee('Bank Permata');
    $payView->assertSee('013');
    $payView->assertSee('BI-FAST');
});

test('user can checkout with QRIS Universal and view QR string & instructions', function () {
    $seats = $this->vehicle->seats()->take(1)->get();
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-QRS',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    $passenger = $booking->passengers()->create([
        'seat_id' => $seats->first()->id,
        'name' => 'Penumpang QRIS',
        'phone' => '081234567891',
    ]);

    $response = $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking), [
        'booker_name' => 'QRIS User',
        'booker_phone' => '081234567891',
        'booker_email' => 'qris@test.com',
        'passengers' => [
            [
                'id' => $passenger->id,
                'name' => 'QRIS User',
                'phone' => '081234567891',
            ],
        ],
        'payment_category' => 'qris',
        'payment_channel' => 'qris',
    ]);

    $response->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $payment = $booking->payment()->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_method)->toBe('QRIS Dinamis Universal');
    expect($payment->bank_name)->toBe('QRIS');

    // Halaman pembayaran menampilkan kode QRIS dan dukungan e-wallet
    $payView = $this->actingAs($this->user)->get(route('dashboard.user.booking.pembayaran', $booking));
    $payView->assertOk();
    $payView->assertSee('QRIS Dinamis');
    $payView->assertSee('GoPay');
    $payView->assertSee('OVO');
    $payView->assertSee('DANA');
    $payView->assertSee('ShopeePay');
    $payView->assertSee('Salin Kode QR');
});

test('user can switch payment method from payment page', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-SWT',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-20261001-0001',
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'bank_name' => 'BCA',
        'account_number' => '82710001234',
        'status' => 'pending',
    ]);

    // Switch ke BNI
    $switchResponse = $this->actingAs($this->user)->post(route('dashboard.user.booking.switch-payment', $booking), [
        'payment_channel' => 'bni',
    ]);

    $switchResponse->assertRedirect(route('dashboard.user.booking.pembayaran', $booking));

    $payment = $booking->payment()->first();
    expect($payment->bank_name)->toBe('BNI');
    expect($payment->payment_method)->toBe('Virtual Account BNI');
    expect($payment->account_number)->toStartWith('9881');
});

test('user can check realtime payment status via polling endpoint', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-POL',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-20261001-POL1',
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    $res = $this->actingAs($this->user)->getJson(route('dashboard.user.booking.payment-status', $booking));
    $res->assertOk();
    $res->assertJson([
        'booking_id' => $booking->id,
        'booking_code' => 'TRV-MID-POL',
        'booking_status' => 'pending',
        'is_paid' => false,
    ]);
});

test('api endpoints can charge transaction and list channels', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-API',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    // 1. GET /api/payment/channels
    $channelsRes = $this->getJson(route('api.payment.channels'));
    $channelsRes->assertOk();
    $channelsRes->assertJsonStructure(['status', 'channels' => ['bca', 'mandiri', 'bni', 'bri', 'bsi', 'seabank', 'qris']]);

    // 2. POST /api/payment/charge
    $chargeRes = $this->actingAs($this->user)->postJson(route('api.payment.charge'), [
        'booking_id' => $booking->id,
        'payment_channel' => 'bri',
    ]);
    $chargeRes->assertOk();
    $chargeRes->assertJson([
        'status' => 'success',
        'payment_method' => 'Virtual Account BRI',
        'bank_name' => 'BRI',
    ]);

    // 3. POST /api/payment/create-token
    $tokenRes = $this->actingAs($this->user)->postJson(route('api.payment.create-token'), [
        'booking_id' => $booking->id,
    ]);
    $tokenRes->assertOk();
    $tokenRes->assertJsonStructure(['status', 'token', 'redirect_url']);
});

test('midtrans webhook successfully confirms booking and generates e-ticket', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-WH1',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $orderId = 'INV-20261001-WH001';
    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    // Simulasi Webhook Midtrans Settlement
    $response = $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'va_numbers' => [
            ['bank' => 'bca', 'va_number' => '827100018899'],
        ],
        'gross_amount' => '150000.00',
    ]);

    $response->assertOk();
    $response->assertJson([
        'status' => 'success',
        'booking_code' => 'TRV-MID-WH1',
    ]);

    $booking->refresh();
    expect($booking->status)->toBe('confirmed');

    $payment = $booking->payment->fresh();
    expect($payment->status)->toBe('paid');
    expect($payment->paid_at)->not->toBeNull();

    // Pastikan E-Ticket QR diterbitkan
    $ticket = Ticket::where('booking_id', $booking->id)->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->qr_code)->toBe('QR-TRV-MID-WH1');
    expect($ticket->status)->toBe('active');
});

test('midtrans webhook cancels booking and marks payment expired on expire status', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-MID-WH2',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $orderId = 'INV-20261001-WH002';
    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    // Simulasi Webhook Midtrans Expire
    $response = $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'expire',
    ]);

    $response->assertOk();
    $response->assertJson([
        'status' => 'cancelled',
    ]);

    $booking->refresh();
    expect($booking->status)->toBe('cancelled');

    $payment = $booking->payment->fresh();
    expect($payment->status)->toBe('expired');
});

test('midtrans webhook requires a valid signature when server key is configured', function () {
    config(['midtrans.server_key' => 'server-key-rahasia']);

    $booking = Booking::create([
        'booking_code' => 'TRV-MID-WH3',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $orderId = 'INV-20261001-WH003';
    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
    ])->assertForbidden();

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
        'signature_key' => str_repeat('0', 128),
    ])->assertForbidden();

    expect($booking->fresh()->status)->toBe('pending');
    expect($booking->payment->fresh()->status)->toBe('pending');
});

test('midtrans webhook accepts a valid signature when server key is configured', function () {
    config(['midtrans.server_key' => 'server-key-rahasia']);

    $booking = Booking::create([
        'booking_code' => 'TRV-MID-WH4',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $orderId = 'INV-20261001-WH004';
    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    $payload = [
        'order_id' => $orderId,
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'gross_amount' => '150000.00',
        'va_numbers' => [['bank' => 'bca', 'va_number' => '827100018899']],
    ];
    $payload['signature_key'] = hash('sha512', $orderId.'200'.'150000.00'.'server-key-rahasia');

    $this->postJson(route('payment.webhook'), $payload)
        ->assertOk()
        ->assertJson(['status' => 'success']);

    expect($booking->fresh()->status)->toBe('confirmed');
    expect($booking->payment->fresh()->status)->toBe('paid');
});

test('midtrans webhook allows unsigned payloads for the local simulator', function () {
    config([
        'midtrans.server_key' => 'server-key-rahasia',
        'midtrans.allow_unsigned_webhook' => true,
    ]);

    $booking = Booking::create([
        'booking_code' => 'TRV-MID-WH5',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $orderId = 'INV-20261001-WH005';
    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => $orderId,
        'gross_amount' => 150000,
        'payment_method' => 'Virtual Account BCA',
        'status' => 'pending',
    ]);

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
    ])->assertOk()->assertJson(['status' => 'success']);

    expect($booking->fresh()->status)->toBe('confirmed');
});

test('midtrans webhook rejects payloads without transaction_status', function () {
    $booking = createWebhookBooking($this, 'TRV-MID-WH6', 'INV-20261001-WH006');

    $this->postJson(route('payment.webhook'), [
        'order_id' => 'INV-20261001-WH006',
        'gross_amount' => '150000.00',
    ])->assertStatus(400)->assertJson([
        'status' => 'error',
        'message' => 'Missing transaction_status',
    ]);

    expect($booking->fresh()->status)->toBe('pending');
    expect($booking->payment->fresh()->status)->toBe('pending');
});

test('midtrans webhook rejects settlement with a mismatched gross amount', function () {
    $booking = createWebhookBooking($this, 'TRV-MID-WH7', 'INV-20261001-WH007');

    $this->postJson(route('payment.webhook'), [
        'order_id' => 'INV-20261001-WH007',
        'transaction_status' => 'settlement',
        'gross_amount' => '1000.00',
    ])->assertStatus(422)->assertJson([
        'status' => 'error',
        'message' => 'Gross amount mismatch',
    ]);

    expect($booking->fresh()->status)->toBe('pending');
    expect($booking->payment->fresh()->status)->toBe('pending');
    expect($booking->ticket()->exists())->toBeFalse();
});

test('midtrans webhook ignores expiry for a booking that is already paid', function () {
    $booking = createWebhookBooking($this, 'TRV-MID-WH8', 'INV-20261001-WH008');
    $orderId = 'INV-20261001-WH008';

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
    ])->assertOk();

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'expire',
    ])->assertOk()->assertJson(['status' => 'ignored']);

    expect($booking->fresh()->status)->toBe('confirmed');
    expect($booking->payment->fresh()->status)->toBe('paid');
    expect($booking->ticket()->exists())->toBeTrue();
});

test('midtrans webhook marks a denied payment as failed', function () {
    $booking = createWebhookBooking($this, 'TRV-MID-WH9', 'INV-20261001-WH009');

    $this->postJson(route('payment.webhook'), [
        'order_id' => 'INV-20261001-WH009',
        'transaction_status' => 'deny',
    ])->assertOk()->assertJson(['status' => 'cancelled']);

    expect($booking->fresh()->status)->toBe('cancelled');
    expect($booking->payment->fresh()->status)->toBe('failed');
});

test('midtrans webhook records refunds without cancelling the booking', function () {
    $booking = createWebhookBooking($this, 'TRV-MID-WH10', 'INV-20261001-WH010');
    $orderId = 'INV-20261001-WH010';

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
    ])->assertOk();

    $this->postJson(route('payment.webhook'), [
        'order_id' => $orderId,
        'transaction_status' => 'refund',
    ])->assertOk()->assertJson(['status' => 'refunded']);

    expect($booking->fresh()->status)->toBe('confirmed');
    expect($booking->payment->fresh()->status)->toBe('refund');
});

test('repeated settlement webhooks issue one ticket and one email', function () {
    Mail::fake();

    $booking = createWebhookBooking($this, 'TRV-MID-WH11', 'INV-20261001-WH011');
    $payload = [
        'order_id' => 'INV-20261001-WH011',
        'transaction_status' => 'settlement',
        'gross_amount' => '150000.00',
        'payment_type' => 'bank_transfer',
    ];

    $this->postJson(route('payment.webhook'), $payload)->assertOk();
    $ticketNumber = $booking->fresh()->ticket->ticket_number;

    $this->postJson(route('payment.webhook'), $payload)->assertOk();

    expect($booking->fresh()->ticket->ticket_number)->toBe($ticketNumber);
    Mail::assertSent(TicketIssuedMail::class, 1);
});
