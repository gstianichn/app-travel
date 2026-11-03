<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('public');

    $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->customer = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '081212341234',
    ]);

    $this->origin = Location::create([
        'code' => 'BDO-PAY',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-PAY',
        'name' => 'Semanggi (Jakarta)',
        'city' => 'Jakarta',
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

    $this->makeVehicle = function (array $overrides = []): Vehicle {
        $vehicle = Vehicle::create(array_merge([
            'plate_number' => 'B 9009 PAY',
            'name' => 'Toyota HiAce Premio',
            'type' => 'Minibus Eksekutif',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ], $overrides));
        $vehicle->ensureSeatsExist();

        return $vehicle;
    };

    $this->vehicle = ($this->makeVehicle)();

    $this->makeSchedule = function (array $overrides = []): Schedule {
        $vehicle = $overrides['vehicle'] ?? $this->vehicle;
        unset($overrides['vehicle']);

        return Schedule::create(array_merge([
            'route_id' => $this->route->id,
            'vehicle_id' => $vehicle->id,
            'departure_date' => Carbon::tomorrow()->toDateString(),
            'departure_time' => '09:00',
            'price' => null,
            'available_seats' => $vehicle->capacity,
            'status' => 'Scheduled',
        ], $overrides));
    };

    $this->seatIds = function (Schedule $schedule, array $numbers): array {
        return Seat::where('vehicle_id', $schedule->vehicle_id)
            ->whereIn('seat_number', array_map('strval', $numbers))
            ->orderBy('id')
            ->pluck('id')
            ->all();
    };

    $this->makePayment = function (array $bookingOverrides = [], array $paymentOverrides = []): array {
        $schedule = ($this->makeSchedule)();
        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-PAY-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $this->customer->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 1,
            'subtotal' => 125000,
            'total_price' => 125000,
            'status' => 'Pending',
            'source' => 'online',
        ], $bookingOverrides));

        $seat = Seat::where('vehicle_id', $schedule->vehicle_id)->where('seat_number', '1')->firstOrFail();
        BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'name' => 'Penumpang Pembayaran',
            'phone' => '081200000001',
        ]);

        $payment = Payment::create(array_merge([
            'booking_id' => $booking->id,
            'order_id' => 'ORD-'.$booking->booking_code,
            'payment_method' => 'Transfer Bank BCA',
            'gross_amount' => 125000,
            'status' => 'Pending',
            'proof_image' => null,
        ], $paymentOverrides));

        return ['booking' => $booking, 'payment' => $payment, 'schedule' => $schedule];
    };
});

test('daftar pembayaran menampilkan data dan metrik omzet', function () {
    $p1 = ($this->makePayment)([], ['status' => 'Success', 'gross_amount' => 125000]);
    ($this->makePayment)([], ['status' => 'Pending', 'gross_amount' => 125000]);
    ($this->makePayment)([], ['status' => 'Failed', 'gross_amount' => 125000]);

    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.pembayaran.index'));

    $response->assertOk();
    $response->assertSee('Monitoring riwayat transaksi');
    $response->assertSee($p1['payment']->order_id);

    expect($response->viewData('totalTransactions'))->toBe(3)
        ->and($response->viewData('totalRevenue'))->toBe(125000)
        ->and($response->viewData('pendingVerificationCount'))->toBe(1)
        ->and($response->viewData('failedOrExpiredCount'))->toBe(1);

    $response->assertSee($p1['payment']->order_id);
    expect($response->viewData('payments')->pluck('order_id')->all())->toContain($p1['payment']->order_id);
});

test('detail pembayaran dapat dilihat', function () {
    $data = ($this->makePayment)([], ['status' => 'Success']);

    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.pembayaran.show', $data['payment']->id));

    $response->assertOk();
    $response->assertSee($data['payment']->order_id);
    $response->assertSee('Faktur Pembayaran');
    $response->assertSee($data['booking']->booking_code);
});

test('approve pembayaran pending: pembayaran sukses, booking lunas, tiket terbit', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $data['payment']->id), [
        'action' => 'approve',
        'notes' => 'Bukti transfer valid',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($data['payment']->fresh()->status)->toBe('Success')
        ->and($data['payment']->fresh()->paid_at)->not->toBeNull()
        ->and($data['payment']->fresh()->verified_by)->toBe($this->admin->id)
        ->and($data['booking']->fresh()->status)->toBe('Paid');

    expect($data['booking']->fresh()->ticket)->not->toBeNull()
        ->and($data['booking']->fresh()->ticket->status)->toBe('Issued');
});

test('reject pembayaran tanpa alasan ditolak dengan error rejection_reason wajib', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $data['payment']->id), [
        'action' => 'reject',
        'rejection_reason' => '',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['rejection_reason']);

    expect($data['payment']->fresh()->status)->toBe('Pending');
});

test('approve pembayaran booking yang sudah batal (BUG-10)', function () {
    $data = ($this->makePayment)(['status' => 'Cancelled'], ['status' => 'Pending']);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $data['payment']->id), [
        'action' => 'approve',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($data['payment']->fresh()->status)->toBe('Success')
        ->and($data['booking']->fresh()->status)->toBe('Paid');
});

test('reject pembayaran yang sudah lunas (BUG-10)', function () {
    $data = ($this->makePayment)([], ['status' => 'Success']);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $data['payment']->id), [
        'action' => 'reject',
        'rejection_reason' => 'Duplikasi bukti transfer',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect($data['payment']->fresh()->status)->toBe('Failed')
        ->and($data['booking']->fresh()->status)->toBe('Cancelled');
});

test('upload bukti transfer valid (jpg/png/webp/pdf, maksimal 5MB)', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $proof = UploadedFile::fake()->createWithContent('bukti.png', $this->png);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.upload-proof', $data['payment']->id), [
        'proof_image' => $proof,
        'bank_name' => 'BCA',
        'account_name' => 'Budi',
        'account_number' => '1234567890',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($data['payment']->fresh()->proof_image)->not->toBeNull();
    Storage::disk('public')->assertExists($data['payment']->fresh()->proof_image);
});

test('upload bukti transfer dengan file tidak valid ditolak', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $exe = UploadedFile::fake()->createWithContent('virus.exe', 'MZ'.str_repeat('A', 1000));
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.upload-proof', $data['payment']->id), [
        'proof_image' => $exe,
    ]);
    $response->assertRedirect();
    $response->assertSessionHasErrors(['proof_image']);
    expect($data['payment']->fresh()->proof_image)->toBeNull();

    $big = UploadedFile::fake()->createWithContent('big.png', str_repeat('A', 5 * 1024 * 1024 + 1));
    $response2 = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.upload-proof', $data['payment']->id), [
        'proof_image' => $big,
    ]);
    $response2->assertRedirect();
    $response2->assertSessionHasErrors(['proof_image']);
    expect($data['payment']->fresh()->proof_image)->toBeNull();
});

test('sinkron gateway expire: booking kadaluarsa dan kursi kembali', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.sync-gateway', $data['payment']->id), [
        'gateway_provider' => 'midtrans',
        'gateway_status' => 'expire',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($data['payment']->fresh()->status)->toBe('Expired')
        ->and($data['payment']->fresh()->gateway_status)->toBe('expire')
        ->and($data['booking']->fresh()->status)->toBe('Expired')
        ->and($data['schedule']->fresh()->available_seats)->toBe($this->vehicle->capacity);
});

test('sinkron gateway settlement: booking lunas dan tiket terbit', function () {
    $data = ($this->makePayment)([], ['status' => 'Pending']);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.sync-gateway', $data['payment']->id), [
        'gateway_provider' => 'midtrans',
        'gateway_status' => 'settlement',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($data['payment']->fresh()->status)->toBe('Success')
        ->and($data['payment']->fresh()->paid_at)->not->toBeNull()
        ->and($data['booking']->fresh()->status)->toBe('Paid')
        ->and($data['booking']->fresh()->ticket)->not->toBeNull()
        ->and($data['booking']->fresh()->ticket->status)->toBe('Issued');
});
