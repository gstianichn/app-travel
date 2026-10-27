<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Admin Utama User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.pembayarantest@travel.test'],
        [
            'name' => 'Admin Utama Pembayaran Test',
            'phone' => '08123456798',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Lokasi & Rute
    $origin = Location::firstOrCreate(
        ['code' => 'BDG-PAY'],
        ['name' => 'Bandung Payment Pool', 'city' => 'Bandung', 'address' => 'Jl. Dipatiukur No. 10']
    );

    $destination = Location::firstOrCreate(
        ['code' => 'JKT-PAY'],
        ['name' => 'Jakarta Payment Pool', 'city' => 'Jakarta', 'address' => 'Jl. Tebet No. 20']
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $origin->id, 'destination_id' => $destination->id],
        ['price' => 150000, 'duration' => '3 jam']
    );

    // Armada & Kursi
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 8888 PAY'],
        [
            'name' => 'Toyota HiAce Payment Unit',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );
    $this->vehicle->ensureSeatsExist();

    // Driver
    $driverUser = User::firstOrCreate(
        ['email' => 'driver.paytest@travel.test'],
        [
            'name' => 'Sopir Budi Payment',
            'phone' => '087812345677',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $driverUser->id],
        [
            'license_number' => 'SIM-B1-PAYTEST',
            'license_type' => 'SIM B1 Umum',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Siti',
            'emergency_contact_phone' => '087899887755',
            'status' => 'standby',
        ]
    );

    // Jadwal
    $this->schedule = Schedule::firstOrCreate(
        [
            'route_id' => $this->route->id,
            'vehicle_id' => $this->vehicle->id,
            'departure_date' => now()->addDays(2)->toDateString(),
            'departure_time' => '09:00',
        ],
        [
            'driver_id' => $this->driver->id,
            'available_seats' => 10,
            'status' => 'Scheduled',
        ]
    );
});

test('admin utama can view payment index page with metrics and transactions table', function () {
    $customer = User::factory()->create(['name' => 'Pelanggan Uji Bayar']);
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-PAY-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Pelanggan Uji Bayar',
        'phone' => '081299990001',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-2026-PAY01',
        'transaction_id' => 'TX-MIDTRANS-12345',
        'payment_method' => 'Transfer Bank BCA',
        'gross_amount' => 150000,
        'status' => 'Pending',
        'bank_name' => 'BCA',
        'account_name' => 'Pelanggan Uji Bayar',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pembayaran.index'));

    $response->assertOk()
        ->assertSee('Daftar Transaksi Pembayaran')
        ->assertSee('Total Transaksi')
        ->assertSee('Omzet Lunas')
        ->assertSee('Verifikasi Manual')
        ->assertSee('INV-2026-PAY01')
        ->assertSee('TRV-PAY-01')
        ->assertSee('Pelanggan Uji Bayar');
});

test('admin utama can filter payments by search keyword and status', function () {
    $customer = User::factory()->create(['name' => 'Anisa Rahmawati']);
    $booking = Booking::create([
        'booking_code' => 'TRV-FILTER-99',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-FILTER-SUCCESS',
        'payment_method' => 'QRIS',
        'gross_amount' => 150000,
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pembayaran.index', [
            'search' => 'INV-FILTER-SUCCESS',
            'status' => 'paid',
            'method' => 'qris',
        ]));

    $response->assertOk()
        ->assertSee('INV-FILTER-SUCCESS')
        ->assertSee('TRV-FILTER-99')
        ->assertSee('QRIS Dinamis');
});

test('admin utama can view payment show page with invoice, bank details, and gateway log', function () {
    $customer = User::factory()->create(['name' => 'Dewi Lestari']);
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-SHOW-PAY',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Dewi Lestari',
        'phone' => '081233334444',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-SHOW-999',
        'transaction_id' => 'TX-XENDIT-999',
        'payment_method' => 'Transfer Bank Mandiri',
        'bank_name' => 'Bank Mandiri',
        'account_name' => 'Dewi Lestari',
        'account_number' => '1370012345678',
        'gross_amount' => 150000,
        'status' => 'Pending',
        'gateway_provider' => 'xendit',
        'gateway_status' => 'pending',
        'raw_response' => [
            'id' => 'xnd_123',
            'status' => 'PENDING',
            'amount' => 150000,
        ],
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pembayaran.show', $payment->id));

    $response->assertOk()
        ->assertSee('INV-SHOW-999')
        ->assertSee('TRV-SHOW-PAY')
        ->assertSee('Dewi Lestari')
        ->assertSee('Bank Mandiri')
        ->assertSee('1370012345678')
        ->assertSee('Status Payment Gateway')
        ->assertSee('xnd_123');
});

test('admin utama can manually approve bank transfer and ticket is automatically issued', function () {
    $customer = User::factory()->create(['name' => 'Hendra Setiawan']);
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-APPROVE-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Hendra Setiawan',
        'phone' => '081277778888',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-APPROVE-01',
        'payment_method' => 'Transfer Bank BCA',
        'gross_amount' => 150000,
        'status' => 'Pending',
        'bank_name' => 'BCA',
        'account_name' => 'Hendra Setiawan',
    ]);

    // Lakukan approve verifikasi manual
    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pembayaran.verify', $payment->id), [
            'action' => 'approve',
            'notes' => 'Mutasi rekening BCA Rp 150.000 terverifikasi valid.',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    $booking->refresh();

    expect($payment->status)->toBe('Success');
    expect($payment->paid_at)->not->toBeNull();
    expect($payment->verified_by)->toBe($this->admin->id);
    expect($payment->verified_at)->not->toBeNull();
    expect($booking->status)->toBe('Paid');

    // Pastikan E-Ticket otomatis terbit
    $ticket = Ticket::where('booking_id', $booking->id)->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->status)->toBe('Issued');
});

test('admin utama can manually reject bank transfer with reason and booking is cancelled', function () {
    $customer = User::factory()->create(['name' => 'Rian Hidayat']);
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-REJECT-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Rian Hidayat',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-REJECT-01',
        'payment_method' => 'Transfer Bank BNI',
        'gross_amount' => 150000,
        'status' => 'Pending',
        'bank_name' => 'BNI',
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pembayaran.verify', $payment->id), [
            'action' => 'reject',
            'rejection_reason' => 'Nominal transfer tidak sesuai dengan jumlah tagihan.',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    $booking->refresh();

    expect($payment->status)->toBe('Failed');
    expect($payment->notes)->toBe('Nominal transfer tidak sesuai dengan jumlah tagihan.');
    expect($booking->status)->toBe('Cancelled');
});

test('admin utama can upload or update manual payment proof image and bank details', function () {
    Storage::fake('public');

    $customer = User::factory()->create();
    $booking = Booking::create([
        'booking_code' => 'TRV-UPLOAD-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'whatsapp',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-UPLOAD-01',
        'payment_method' => 'Transfer Bank BRI',
        'gross_amount' => 150000,
        'status' => 'Pending',
    ]);

    $file = UploadedFile::fake()->create('bukti_transfer.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.pembayaran.upload-proof', $payment->id), [
            'proof_image' => $file,
            'bank_name' => 'Bank BRI',
            'account_name' => 'Siti Aisyah',
            'account_number' => '0123-01-000456-50-1',
            'notes' => 'Dikirim via WhatsApp CS loket',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    expect($payment->proof_image)->not->toBeNull();
    expect($payment->bank_name)->toBe('Bank BRI');
    expect($payment->account_name)->toBe('Siti Aisyah');
    expect($payment->account_number)->toBe('0123-01-000456-50-1');
});

test('admin utama can sync or simulate gateway webhook status update', function () {
    $customer = User::factory()->create();
    $seat1A = Seat::where('vehicle_id', $this->vehicle->id)->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-GATEWAY-01',
        'user_id' => $customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Pending',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1A->id,
        'name' => 'Tamu Midtrans',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'INV-MIDTRANS-TEST',
        'payment_method' => 'Midtrans Payment Gateway',
        'gross_amount' => 150000,
        'status' => 'Pending',
        'gateway_provider' => 'midtrans',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.pembayaran.sync-gateway', $payment->id), [
            'gateway_provider' => 'midtrans',
            'gateway_status' => 'settlement',
        ]);

    $response->assertRedirect();

    $payment->refresh();
    $booking->refresh();

    expect($payment->status)->toBe('Success');
    expect($payment->gateway_status)->toBe('settlement');
    expect($payment->raw_response)->not->toBeNull();
    expect($payment->raw_response['transaction_status'])->toBe('settlement');
    expect($booking->status)->toBe('Paid');

    // Tiket otomatis terbit
    $ticket = Ticket::where('booking_id', $booking->id)->first();
    expect($ticket)->not->toBeNull();
    expect($ticket->status)->toBe('Issued');
});
