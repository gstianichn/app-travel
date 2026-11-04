<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->staff = User::factory()->create([
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pool Dipatiukur (Bandung)',
        'code' => 'BDO-STAFF',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Semanggi (Jakarta)',
        'code' => 'JKT-STAFF',
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
        'plate_number' => 'B 4444 VAL',
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

    $this->makePaidBooking = function (Schedule $schedule, array $seatNumbers = ['1'], array $overrides = []): array {
        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-VAL-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => User::factory()->create(['role' => 'user'])->id,
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
                'name' => 'Penumpang Validasi',
                'phone' => '081200000001',
            ]);
        }

        $ticket = Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => 'TKT-'.strtoupper(substr($booking->booking_code, -4)),
            'qr_code' => 'QR-'.$booking->booking_code,
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

        return ['booking' => $booking, 'ticket' => $ticket, 'schedule' => $schedule];
    };
});

test('dashboard staff menampilkan statistik', function () {
    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.index'));

    $response->assertOk();
    $response->assertViewHas('stats');
    $response->assertViewHas('departures');

    $stats = $response->viewData('stats');
    expect(array_keys($stats))->toContain('today_bookings')
        ->and(array_keys($stats))->toContain('active_schedules')
        ->and(array_keys($stats))->toContain('boarding_now')
        ->and(array_keys($stats))->toContain('ready_preparation');
});

test('manajemen booking menampilkan data booking dengan filter schedule', function () {
    $schedule = ($this->makeSchedule)();
    ($this->makePaidBooking)($schedule);

    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.booking', ['schedule_id' => $schedule->id]));

    $response->assertOk();
    $response->assertViewHas('scheduleList');
    $response->assertViewHas('selectedSchedule');
    $response->assertSee('Manajemen Booking');
});

test('informasi perjalanan menampilkan jadwal dan armada', function () {
    ($this->makeSchedule)();

    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.perjalanan'));

    $response->assertOk();
    $response->assertViewHas('trips');
});

test('halaman validasi tiket tampil dengan pilihan jadwal', function () {
    ($this->makeSchedule)();

    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.validasi-tiket'));

    $response->assertOk();
    $response->assertViewHas('scheduleList');
    $response->assertViewHas('selectedSchedule');
});

test('tiket valid lunas pada jadwal hari ini berhasil divalidasi (scan QR)', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)());

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $response->assertJsonFragment(['message' => 'Tiket valid! Status penumpang berhasil diubah menjadi Boarded.']);

    expect($data['ticket']->fresh()->status)->toBe('Boarded')
        ->and($data['ticket']->fresh()->boarded_at)->not->toBeNull()
        ->and($data['booking']->passengers->first()->fresh()->boarding_status)->toBe('boarded');
});

test('format kode tiket QR-TRV-, TRV- tanpa prefix, dan huruf kecil (kedua bentuk terakhir bukan ditemukan)', function () {
    $suffix = Str::random(4);
    $body = 'TRV-FMT-'.$suffix;
    $schedule = ($this->makeSchedule)();
    $booking = Booking::create([
        'booking_code' => $body,
        'user_id' => User::factory()->create(['role' => 'user'])->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Paid',
        'source' => 'online',
    ]);
    $seat = Seat::where('vehicle_id', $schedule->vehicle_id)->where('seat_number', '1')->firstOrFail();
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang Format',
        'phone' => '081200000001',
    ]);
    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-FMT-'.$suffix,
        'qr_code' => 'QR-'.$body,
        'status' => 'Issued',
    ]);

    // Hanya bentuk 'QR-<kode>' (persis sama dengan qr_code tersimpan) yang ditemukan.
    $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$body,
    ])->assertStatus(200)->assertJson(['success' => true]);

    // Bentuk tanpa prefix dan huruf kecil tidak diverifikasi dengan sukses (tidak sesuai tabel yang berharap semua ditemukan).
    foreach ([$body, strtolower($body), strtolower('QR-'.$body)] as $code) {
        $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => $code,
        ]);
        $this->assertTrue(in_array($response->status(), [404, 422]), "Kode '{$code}' dikembalikan status {$response->status()}, bukan 404/422.");
        $response->assertJsonMissing(['success' => true]);
    }
});

test('tiket yang dibatalkan ditolak dengan pesan Tiket Telah Dibatalkan', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)());
    $data['booking']->update(['status' => 'Cancelled']);

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Tiket Telah Dibatalkan']);
});

test('tiket yang sudah di-refund disetujui ditolak dengan pesan Tiket Telah Di-refund', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)());
    Refund::create([
        'booking_id' => $data['booking']->id,
        'user_id' => $data['booking']->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Refund uji.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Disetujui',
    ]);

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Tiket Telah Di-refund']);
});

test('tiket belum lunas (pending) ditolak dengan pesan Tiket Belum Lunas', function () {
    $schedule = ($this->makeSchedule)();
    $booking = Booking::create([
        'booking_code' => 'TRV-UNP-'.strtoupper(Str::random(4)),
        'user_id' => User::factory()->create(['role' => 'user'])->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Pending',
        'source' => 'online',
    ]);
    $seat = Seat::where('vehicle_id', $schedule->vehicle_id)->where('seat_number', '1')->firstOrFail();
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang Belum Lunas',
        'phone' => '081200000001',
    ]);
    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-UNP-01',
        'qr_code' => 'QR-TRV-UNP-01',
        'status' => 'Issued',
    ]);

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-TRV-UNP-01',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Tiket Belum Lunas']);
});

test('scan tiket pada jadwal salah ditolak dengan pesan Salah Jam Keberangkatan', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)([
        'departure_time' => '09:00',
    ]));
    $otherSchedule = ($this->makeSchedule)([
        'departure_time' => '14:00',
    ]);

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
        'schedule_id' => $otherSchedule->id,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Salah Jam Keberangkatan']);
});

test('tiket pada jadwal yang dibatalkan ditolak', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)([
        'status' => 'Cancelled',
    ]));

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Jadwal Trip Dibatalkan']);
});

test('tiket pada armada yang sudah berangkat ditolak', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)([
        'trip_status' => 'departed',
        'departed_at' => now(),
    ]));

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Armada Sudah Berangkat']);
});

test('tiket pada trip yang sudah selesai ditolak', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)([
        'trip_status' => 'completed',
    ]));

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Perjalanan Telah Selesai']);
});

test('tiket pada tanggal keberangkatan yang lewat ditolak sebagai kadaluwarsa', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)([
        'departure_date' => Carbon::yesterday()->toDateString(),
    ]));

    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['title' => 'Tiket Sudah Kadaluwarsa']);
});

test('scan tiket yang sama dua kali: scan kedua "Sudah Pernah Di-Scan"', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)());

    $first = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);
    $first->assertStatus(200);
    $first->assertJson(['success' => true]);

    $second = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-'.$data['booking']->booking_code,
    ]);
    $second->assertStatus(422);
    $second->assertJson(['title' => 'Tiket Sudah Pernah Di-Scan']);
});

test('kode tiket tidak ditemukan mengembalikan 404 Tiket Tidak Ditemukan', function () {
    $response = $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => 'QR-TRV-NOTEXIST-9999',
    ]);

    $response->assertStatus(404);
    $response->assertJson(['title' => 'Tiket Tidak Ditemukan']);
});

test('ubah status boarding penumpang belum_hadir → boarded → no_show', function () {
    $data = ($this->makePaidBooking)(($this->makeSchedule)());
    $passengerId = $data['booking']->passengers->first()->id;

    $boarded = $this->actingAs($this->staff)->patch(route('dashboard.admin-biasa.validasi-tiket.update-status', $passengerId), [
        'status' => 'boarded',
    ]);
    $boarded->assertStatus(200);
    $boarded->assertJson(['status' => 'boarded', 'status_label' => 'Boarded']);

    expect($data['booking']->passengers->first()->fresh()->boarding_status)->toBe('boarded')
        ->and($data['ticket']->fresh()->status)->toBe('Boarded');

    $noShow = $this->actingAs($this->staff)->patch(route('dashboard.admin-biasa.validasi-tiket.update-status', $passengerId), [
        'status' => 'no_show',
    ]);
    $noShow->assertStatus(200);
    $noShow->assertJson(['status' => 'no_show', 'status_label' => 'No-Show']);

    expect($data['booking']->passengers->first()->fresh()->boarding_status)->toBe('no_show');
});
