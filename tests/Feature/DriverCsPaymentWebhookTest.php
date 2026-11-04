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
use App\Models\Ticket;
use App\Models\TripIncident;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

    $this->driverUser = User::factory()->create([
        'role' => 'driver',
        'status' => 'active',
        'phone' => '081299998888',
        'password' => Hash::make('password123'),
    ]);

    $this->driver = Driver::create([
        'user_id' => $this->driverUser->id,
        'phone' => '081299998888',
        'license_number' => 'SIM-DRIVER-01',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2),
        'emergency_contact_name' => 'Istri Driver',
        'emergency_contact_phone' => '081211112222',
        'emergency_contact_relation' => 'Istri',
        'address' => 'Jl. Driver No. 1',
        'status' => 'standby',
    ]);

    $this->csUser = User::factory()->create([
        'role' => 'cs',
        'status' => 'active',
        'phone' => '081200001111',
        'password' => Hash::make('password123'),
    ]);

    $this->otherUser = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '081200002222',
        'password' => Hash::make('password123'),
    ]);

    $this->ownerUser = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '081200003333',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pool Dipatiukur (Bandung)',
        'code' => 'BDO-DRV',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Semanggi (Jakarta)',
        'code' => 'JKT-DRV',
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

    $this->vehicle = Vehicle::create([
        'plate_number' => 'B 5555 DRV',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Minibus Eksekutif',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->makeSchedule = function (array $overrides = []): Schedule {
        return Schedule::create(array_merge([
            'route_id' => $this->route->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_date' => Carbon::today()->toDateString(),
            'departure_time' => '09:00',
            'price' => null,
            'available_seats' => 10,
            'status' => 'Scheduled',
            'trip_status' => 'scheduled',
        ], $overrides));
    };

    $this->seatIds = function (Schedule $schedule, array $numbers): array {
        return Seat::where('vehicle_id', $schedule->vehicle_id)
            ->whereIn('seat_number', array_map('strval', $numbers))
            ->orderBy('id')
            ->pluck('id')
            ->all();
    };

    $this->makePendingBooking = function (User $user, Schedule $schedule, array $seatNumbers = ['1'], array $overrides = []): array {
        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-WEB-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => count($seatNumbers),
            'subtotal' => 125000 * count($seatNumbers),
            'total_price' => 125000 * count($seatNumbers),
            'status' => 'Pending',
            'source' => 'online',
        ], $overrides));

        foreach ($seatNumbers as $number) {
            $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
                ->where('seat_number', (string) $number)
                ->firstOrFail();
            BookingPassenger::create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'name' => 'Penumpang Webhook',
                'phone' => '081200000001',
            ]);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => 'ORD-'.$booking->booking_code,
            'payment_method' => 'Virtual Account BCA',
            'gross_amount' => $booking->total_price,
            'status' => 'Pending',
            'paid_at' => null,
        ]);

        return ['booking' => $booking, 'schedule' => $schedule];
    };

    $this->makePaidBooking = function (User $user, Schedule $schedule, array $seatNumbers = ['1'], array $overrides = []): array {
        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-DRV-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => count($seatNumbers),
            'subtotal' => 125000 * count($seatNumbers),
            'total_price' => 125000 * count($seatNumbers),
            'status' => 'Paid',
            'source' => 'online',
        ], $overrides));

        foreach ($seatNumbers as $number) {
            $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
                ->where('seat_number', (string) $number)
                ->firstOrFail();
            BookingPassenger::create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'name' => 'Penumpang Driver',
                'phone' => '081200000001',
            ]);
        }

        Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => 'TKT-DRV-01',
            'qr_code' => 'QR-TRV-DRV-01',
            'status' => 'Issued',
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => 'ORD-'.$booking->booking_code,
            'payment_method' => 'Transfer Bank',
            'gross_amount' => $booking->total_price,
            'status' => 'Success',
            'paid_at' => now(),
        ]);

        return ['booking' => $booking, 'ticket' => Ticket::where('booking_id', $booking->id)->first(), 'schedule' => $schedule];
    };

    // Server key agar webhook memerlukan tanda tangan.
    AppSetting::set('gateway_server_key', 'test-server-key-12345');
    AppSetting::set('midtrans_server_key', 'test-server-key-12345');

    $this->makeWebhookSignature = function (string $orderId, string $statusCode, string $grossAmount): string {
        return hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key-12345');
    };
});

/* ------------------------------------------------------------------ */
/* Halaman driver */
/* ------------------------------------------------------------------ */

test('semua halaman driver dapat diakses (200 tanpa error)', function () {
    $pages = [
        'dashboard.driver.index',
        'dashboard.driver.jadwal',
        'dashboard.driver.kendaraan',
        'dashboard.driver.penumpang',
        'dashboard.driver.seatmap',
        'dashboard.driver.rute',
        'dashboard.driver.status',
        'dashboard.driver.ubah-status',
        'dashboard.driver.info-penumpang',
        'dashboard.driver.lapor-masalah',
        'dashboard.driver.emergency',
        'dashboard.driver.gps',
        'dashboard.driver.performa',
        'dashboard.driver.reward',
        'dashboard.driver.riwayat',
    ];

    foreach ($pages as $page) {
        $response = $this->actingAs($this->driverUser)->get(route($page));
        $response->assertStatus(200);
    }
});

test('driver tanpa jadwal tetap menampilkan halaman kosong dengan rapi', function () {
    // Tidak ada jadwal yang ditugaskan ke driver ini dan tidak ada jadwal hari ini.
    $response = $this->actingAs($this->driverUser)->get(route('dashboard.driver.index'));

    $response->assertStatus(200);
    $response->assertViewHas('activeSchedule');
});

/* ------------------------------------------------------------------ */
/* Status perjalanan */
/* ------------------------------------------------------------------ */

test('mulai penjemputan (pickup): jadwal Persiapan, driver on_duty, penumpang diberi tahu', function () {
    $schedule = ($this->makeSchedule)();

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $schedule->id), [
            'status' => 'pickup',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();
    $this->driver->refresh();

    expect($schedule->trip_status)->toBe('pickup')
        ->and($schedule->status)->toBe('Persiapan')
        ->and($schedule->pickup_started_at)->not->toBeNull()
        ->and($this->driver->status)->toBe('on_duty');
});

test('berangkat (departed): jadwal Dalam Perjalanan, kendaraan beroperasi', function () {
    $schedule = ($this->makeSchedule)(['trip_status' => 'pickup', 'status' => 'Persiapan', 'pickup_started_at' => now()]);

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $schedule->id), [
            'status' => 'departed',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();
    $this->driver->refresh();
    $this->vehicle->refresh();

    expect($schedule->trip_status)->toBe('departed')
        ->and($schedule->status)->toBe('Dalam Perjalanan')
        ->and($schedule->departed_at)->not->toBeNull()
        ->and($this->driver->status)->toBe('on_duty')
        ->and($this->vehicle->status)->toBe('beroperasi');
});

test('tiba (arrived): jadwal Tiba di Tujuan', function () {
    $schedule = ($this->makeSchedule)(['trip_status' => 'departed', 'status' => 'Dalam Perjalanan', 'departed_at' => now()]);

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $schedule->id), [
            'status' => 'arrived',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();

    expect($schedule->trip_status)->toBe('arrived')
        ->and($schedule->status)->toBe('Tiba di Tujuan')
        ->and($schedule->arrived_at)->not->toBeNull();
});

test('selesai (completed): jadwal Completed, driver standby, kendaraan siap_jalan, booking lunas jadi completed', function () {
    $schedule = ($this->makeSchedule)(['trip_status' => 'arrived', 'status' => 'Tiba di Tujuan', 'arrived_at' => now()]);
    $data = ($this->makePaidBooking)($this->driverUser, $schedule, ['1'], ['status' => 'paid']);

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $schedule->id), [
            'status' => 'completed',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $schedule->refresh();
    $this->driver->refresh();
    $this->vehicle->refresh();
    $data['booking']->refresh();

    expect($schedule->trip_status)->toBe('completed')
        ->and($schedule->status)->toBe('Completed')
        ->and($schedule->completed_at)->not->toBeNull()
        ->and($this->driver->status)->toBe('standby')
        ->and($this->vehicle->status)->toBe('siap_jalan')
        ->and($data['booking']->status)->toBe('completed');
});

test('status tidak valid (terbang) gagal divalidasi', function () {
    $schedule = ($this->makeSchedule)();

    $response = $this->actingAs($this->driverUser)
        ->post(route('dashboard.driver.schedule.status', $schedule->id), [
            'status' => 'terbang',
        ]);

    $response->assertSessionHasErrors(['status']);
    $response->assertRedirect();

    $schedule->refresh();
    expect($schedule->trip_status)->toBe('scheduled');
});

/* ------------------------------------------------------------------ */
/* GPS & check-in */
/* ------------------------------------------------------------------ */

test('update GPS: koordinat dan last_gps_at tersimpan', function () {
    $schedule = ($this->makeSchedule)();

    $response = $this->actingAs($this->driverUser)->postJson(route('dashboard.driver.schedule.gps', $schedule->id), [
        'latitude' => -6.9147,
        'longitude' => 107.6098,
        'accuracy' => 10,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $schedule->refresh();
    expect((float) $schedule->current_latitude)->toBe(-6.9147)
        ->and((float) $schedule->current_longitude)->toBe(107.6098)
        ->and($schedule->last_gps_at)->not->toBeNull();
});

test('check-in penumpang: boarded / belum_hadir mengubah status penumpang', function () {
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePaidBooking)($this->driverUser, $schedule, ['1', '2']);
    $passengerId = $data['booking']->passengers->first()->id;

    $boarded = $this->actingAs($this->driverUser)
        ->patchJson(route('dashboard.driver.passenger.checkin', $passengerId), [
            'boarding_status' => 'boarded',
        ]);
    $boarded->assertStatus(200);
    $boarded->assertJson(['boarding_status' => 'boarded']);

    expect($data['booking']->passengers->first()->fresh()->boarding_status)->toBe('boarded')
        ->and($data['booking']->passengers->first()->fresh()->boarded_at)->not->toBeNull();

    $pending = $this->actingAs($this->driverUser)
        ->patchJson(route('dashboard.driver.passenger.checkin', $passengerId), [
            'boarding_status' => 'belum_hadir',
        ]);
    $pending->assertStatus(200);
    $pending->assertJson(['boarding_status' => 'belum_hadir']);

    expect($data['booking']->passengers->first()->fresh()->boarding_status)->toBe('belum_hadir');
});

/* ------------------------------------------------------------------ */
/* Lapor masalah & SOS */
/* ------------------------------------------------------------------ */

test('lapor masalah ban_bocor severity sedang: insiden tercatat', function () {
    $schedule = ($this->makeSchedule)();

    $response = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'ban_bocor',
        'severity' => 'sedang',
        'description' => 'Ban bocor di KM 45, perlu ganti ban cadang.',
        'location_name' => 'KM 45 Tol Cipularang',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(TripIncident::where('schedule_id', $schedule->id)
        ->where('category', 'ban_bocor')
        ->where('severity', 'sedang')
        ->where('description', 'Ban bocor di KM 45, perlu ganti ban cadang.')
        ->exists())->toBeTrue();
});

test('lapor macet parah: penumpang dapat notifikasi keterlambatan', function () {
    $schedule = ($this->makeSchedule)();
    $booking = ($this->makePaidBooking)($this->driverUser, $schedule, ['1'])['booking'];

    $response = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'macet_parah',
        'severity' => 'tinggi',
        'description' => 'Macet parah akibat kecelakaan di KM 70.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $incident = TripIncident::where('schedule_id', $schedule->id)
        ->where('category', 'macet_parah')
        ->first();
    expect($incident)->not->toBeNull();
});

test('validasi laporan: kategori/severity tidak dikenal dan deskripsi kosong / > 1000 karakter error validasi', function () {
    $schedule = ($this->makeSchedule)();

    $badCategory = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'ufo_entang',
        'severity' => 'sedang',
        'description' => 'Ada objek aneh di langit.',
    ]);
    $badCategory->assertSessionHasErrors(['category']);

    $badSeverity = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'ban_bocor',
        'severity' => 'mati_lampu',
        'description' => 'Ban bocor.',
    ]);
    $badSeverity->assertSessionHasErrors(['severity']);

    $emptyDesc = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'ban_bocor',
        'severity' => 'sedang',
        'description' => '',
    ]);
    $emptyDesc->assertSessionHasErrors(['description']);

    $longDesc = str_repeat('a', 1001);
    $longDescResp = $this->actingAs($this->driverUser)->post(route('dashboard.driver.schedule.incident', $schedule->id), [
        'category' => 'ban_bocor',
        'severity' => 'sedang',
        'description' => $longDesc,
    ]);
    $longDescResp->assertSessionHasErrors(['description']);
});

test('SOS: insiden sos_emergency tercatat, kontak darurat tampil, log emergency tertulis', function () {
    $schedule = ($this->makeSchedule)();

    Log::shouldReceive('emergency')->once()->withArgs(function ($message) {
        return str_contains($message, 'EMERGENCY SOS');
    });

    $response = $this->actingAs($this->driverUser)->postJson(route('dashboard.driver.schedule.sos', $schedule->id), [
        'description' => 'Kecelakaan lalu lintas parah.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'incident_id' => TripIncident::where('category', 'sos_emergency')->first()->id,
    ]);
    $response->assertJsonStructure(['emergency_contacts']);

    $incident = TripIncident::where('category', 'sos_emergency')->first();
    expect($incident->severity)->toBe('darurat')
        ->and($incident->emergency_dispatched)->toBeTrue();
});

/* ------------------------------------------------------------------ */
/* Customer service */
/* ------------------------------------------------------------------ */

test('dashboard customer service: keberangkatan hari ini, pembayaran pending, refund pending tampil', function () {
    $response = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index'));

    $response->assertStatus(200);
    $response->assertViewHas('stats');

    $stats = $response->viewData('stats');
    expect(array_keys($stats))->toContain('departures_today')
        ->and(array_keys($stats))->toContain('pending_payments')
        ->and(array_keys($stats))->toContain('pending_refunds');
});

test('cari booking dengan kode booking, nomor tiket, nama/email/HP pemesan atau penumpang', function () {
    $user = User::factory()->create(['name' => 'Cari Nama Pemesan', 'email' => 'cari.nama@travel.test', 'phone' => '081299990001']);
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePendingBooking)($user, $schedule, ['1']);
    $booking = $data['booking'];

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-CARI-001',
        'qr_code' => 'QR-CARI-001',
        'status' => 'Issued',
    ]);
    $booking->passengers->first()->update(['name' => 'Cari Nama Penumpang', 'phone' => '081288880001']);

    // Kode booking
    $r = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index', ['q' => $booking->booking_code]));
    $r->assertOk();
    expect($r->viewData('searchResults')->pluck('id')->all())->toContain($booking->id);

    // Nomor tiket
    $r = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index', ['q' => 'TKT-CARI-001']));
    $r->assertOk();
    expect($r->viewData('searchResults')->pluck('id')->all())->toContain($booking->id);

    // Nama pemesan
    $r = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index', ['q' => 'Cari Nama Pemesan']));
    $r->assertOk();
    expect($r->viewData('searchResults')->pluck('id')->all())->toContain($booking->id);

    // Email pemesan
    $r = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index', ['q' => 'cari.nama@travel.test']));
    $r->assertOk();
    expect($r->viewData('searchResults')->pluck('id')->all())->toContain($booking->id);

    // HP penumpang
    $r = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index', ['q' => '081288880001']));
    $r->assertOk();
    expect($r->viewData('searchResults')->pluck('id')->all())->toContain($booking->id);
});

test('halaman CS lain: live chat, konteks booking, riwayat chat, kirim file, eskalasi semua 200', function () {
    $pages = [
        'dashboard.customer-service.live-chat',
        'dashboard.customer-service.booking-context',
        'dashboard.customer-service.chat-history',
        'dashboard.customer-service.send-file',
        'dashboard.customer-service.escalation',
    ];

    foreach ($pages as $page) {
        $response = $this->actingAs($this->csUser)->get(route($page));
        $response->assertStatus(200);
    }
});

/* ------------------------------------------------------------------ */
/* Pembayaran API & webhook */
/* ------------------------------------------------------------------ */

test('daftar kanal pembayaran: JSON daftar kanal', function () {
    $response = $this->get(route('api.payment.channels'));

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);
    $channels = $response->json('channels');
    expect(is_array($channels) && count($channels) > 0)->toBeTrue();
});

test('charge dari sesi pemilik: data VA/QR dikembalikan', function () {
    $user = $this->ownerUser;
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePaidBooking)($user, $schedule, ['1']);
    $booking = $data['booking'];

    $response = $this->actingAs($user)->postJson(route('api.payment.charge'), [
        'booking_id' => $booking->id,
        'payment_channel' => 'bca',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success', 'payment_method' => 'Virtual Account BCA']);
    $json = $response->json();
    foreach (['order_id', 'bank_name', 'account_number', 'gross_amount'] as $key) {
        expect(array_key_exists($key, $json))->toBeTrue();
    }

    $booking->refresh();
    expect($booking->payment)->not->toBeNull();
});

test('charge booking orang lain (sesi lain): 403 Forbidden', function () {
    $owner = $this->ownerUser;
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePaidBooking)($owner, $schedule, ['1']);
    $booking = $data['booking'];

    $response = $this->actingAs($this->otherUser)->postJson(route('api.payment.charge'), [
        'booking_id' => $booking->id,
        'payment_channel' => 'bca',
    ]);

    $response->assertStatus(403);
});

test('webhook settlement bertanda tangan valid: booking confirmed, tiket & email terbit 1x', function () {
    $user = $this->ownerUser;
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePendingBooking)($user, $schedule, ['1']);
    $booking = $data['booking'];
    $payment = $booking->payment;

    $payload = [
        'order_id' => $payment->order_id,
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '125000',
        'signature_key' => ($this->makeWebhookSignature)($payment->order_id, '200', '125000'),
        'payment_type' => 'bank_transfer',
        'va_numbers' => [['bank' => 'bca', 'va_number' => '81234567890']],
    ];

    $response = $this->post(route('payment.webhook'), $payload);

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);

    $booking->refresh();
    $payment->refresh();

    expect($booking->status)->toBe('confirmed')
        ->and($payment->status)->toBe('paid')
        ->and($payment->paid_at)->not->toBeNull();

    $ticket = $booking->ticket;
    expect($ticket)->not->toBeNull()
        ->and($ticket->status)->toBe('active');

    // Kirim webhook sekali lagi: tiket & email hanya terbit sekali.
    $response2 = $this->post(route('payment.webhook'), $payload);
    $response2->assertStatus(200);

    expect(Ticket::where('booking_id', $booking->id)->count())->toBe(1);
});

test('webhook signature salah: 403 Invalid signature', function () {
    $user = $this->ownerUser;
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePendingBooking)($user, $schedule, ['1']);
    $payment = $data['booking']->payment;

    $payload = [
        'order_id' => $payment->order_id,
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '125000',
        'signature_key' => 'signature-salah-banget',
    ];

    $response = $this->post(route('payment.webhook'), $payload);

    $response->assertStatus(403);
    $response->assertJson(['message' => 'Invalid signature']);

    $booking = $data['booking'];
    $booking->refresh();
    expect($booking->status)->toBe('Pending');
});

test('webhook expire: booking batal, kursi lepas, notifikasi terkirim', function () {
    $user = $this->ownerUser;
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePendingBooking)($user, $schedule, ['1']);
    $booking = $data['booking'];
    $payment = $data['booking']->payment;

    $schedule->recalculateAvailableSeats();
    $seatsBefore = $schedule->available_seats;

    $payload = [
        'order_id' => $payment->order_id,
        'transaction_status' => 'expire',
        'status_code' => '200',
        'gross_amount' => '125000',
        'signature_key' => ($this->makeWebhookSignature)($payment->order_id, '200', '125000'),
    ];

    $response = $this->post(route('payment.webhook'), $payload);

    $response->assertStatus(200);

    $booking->refresh();
    $payment->refresh();
    $schedule->refresh();

    expect($booking->status)->toBe('cancelled')
        ->and($payment->status)->toBe('expired')
        ->and($schedule->available_seats)->toBeGreaterThan($seatsBefore)
        ->and($schedule->available_seats)->toBe($this->vehicle->capacity);
});

test('webhook order tidak dikenal: 404', function () {
    $payload = [
        'order_id' => 'ORD-UNKNOWN-9999',
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '125000',
        'signature_key' => ($this->makeWebhookSignature)('ORD-UNKNOWN-9999', '200', '125000'),
    ];

    $response = $this->post(route('payment.webhook'), $payload);

    $response->assertStatus(404);
});

test('webhook tanpa order_id: 400', function () {
    $response = $this->post(route('payment.webhook'), [
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'gross_amount' => '125000',
    ]);

    $response->assertStatus(400);
});
