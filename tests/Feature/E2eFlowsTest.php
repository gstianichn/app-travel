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
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->staff = User::factory()->create([
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->driverUser = User::factory()->create([
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->driver = Driver::create([
        'user_id' => $this->driverUser->id,
        'phone' => '081299998888',
        'license_number' => 'SIM-E2E-01',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYears(2),
        'emergency_contact_name' => 'Istri',
        'emergency_contact_phone' => '081211112222',
        'emergency_contact_relation' => 'Istri',
        'address' => 'Jl. E2E No. 1',
        'status' => 'standby',
    ]);

    $this->passenger = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '081200001111',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pool Dipatiukur (Bandung)',
        'code' => 'BDO-E2E',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Semanggi (Jakarta)',
        'code' => 'JKT-E2E',
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
            'plate_number' => 'B '.rand(1000, 9999).' E2E',
            'name' => 'Toyota HiAce Premio',
            'type' => 'Minibus Eksekutif',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ], $overrides));

        $vehicle->ensureSeatsExist();

        return $vehicle;
    };

    $this->makeSchedule = function (array $overrides = []): Schedule {
        $vehicle = $overrides['vehicle'] ?? ($this->makeVehicle)();
        unset($overrides['vehicle']);
        $driver = $overrides['driver'] ?? $this->driver;
        unset($overrides['driver']);

        return Schedule::create(array_merge([
            'route_id' => $this->route->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'departure_date' => Carbon::tomorrow()->toDateString(),
            'departure_time' => '09:00',
            'price' => null,
            'available_seats' => $vehicle->capacity,
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

    // Penumpang membayar lunas (simulasi hasil webhook settlement): booking Paid,
    // pembayaran Success, tiket Issued, kursi terisi.
    $this->makePaidBooking = function (User $user, Schedule $schedule, array $seatNumbers = ['1']): array {
        $booking = Booking::create([
            'booking_code' => 'TRV-E2E-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => count($seatNumbers),
            'subtotal' => 125000 * count($seatNumbers),
            'total_price' => 125000 * count($seatNumbers),
            'status' => 'confirmed',
            'source' => 'online',
        ]);

        foreach ($seatNumbers as $number) {
            $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
                ->where('seat_number', (string) $number)
                ->firstOrFail();
            BookingPassenger::create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'name' => 'Penumpang E2E',
                'phone' => '081200000001',
            ]);
        }

        Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => 'TKT-E2E-'.strtoupper(Str::random(4)),
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

        $schedule->recalculateAvailableSeats();

        return ['booking' => $booking, 'schedule' => $schedule];
    };

    $this->staffScan = function (string $code, ?Schedule $schedule = null) {
        return $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
            'code' => $code,
            'schedule_id' => $schedule?->id,
        ]);
    };

    $this->driverTransition = function (Schedule $schedule, string $status) {
        return $this->actingAs($this->driverUser)
            ->post(route('dashboard.driver.schedule.status', $schedule->id), [
                'status' => $status,
            ]);
    };
});

/* =================================================================== */
/* 1. ALUR NORMAL */
/* =================================================================== */

test('alur normal: pesan & bayar → staf scan → driver pickup/departed/arrived/completed → ulasan tampil di landing', function () {
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePaidBooking)($this->passenger, $schedule, ['1']);
    $booking = $data['booking'];

    // Staf scan tiket lunas → berhasil, status Boarded.
    $scan = ($this->staffScan)('QR-'.$booking->booking_code, $schedule);
    $scan->assertStatus(200);
    $scan->assertJson(['success' => true]);

    expect($booking->refresh()->passengers->first()->boarding_status)->toBe('boarded')
        ->and($booking->ticket->fresh()->status)->toBe('Boarded');

    // Driver melintasi tahapan perjalanan secara berurutan.
    ($this->driverTransition)($schedule, 'pickup')->assertSessionHas('success');
    ($this->driverTransition)($schedule, 'departed')->assertSessionHas('success');
    ($this->driverTransition)($schedule, 'arrived')->assertSessionHas('success');
    ($this->driverTransition)($schedule, 'completed')->assertSessionHas('success');

    $schedule->refresh();
    $booking->refresh();

    expect($schedule->trip_status)->toBe('completed')
        ->and($schedule->status)->toBe('Completed')
        ->and($booking->status)->toBe('completed');

    // Penumpang mengirim ulasan setelah perjalanan selesai.
    $review = $this->actingAs($this->passenger)
        ->post(route('dashboard.user.tiket.review', $booking->id), [
            'cleanliness_rating' => 5,
            'driver_rating' => 4,
            'punctuality_rating' => 5,
            'comment' => 'Perjalanan nyaman dan tepat waktu.',
        ]);
    $review->assertSessionHas('success');

    expect(TripReview::where('booking_id', $booking->id)->where('comment', 'Perjalanan nyaman dan tepat waktu.')->exists())->toBeTrue();

    // Ulasan tampil di halaman landing (home).
    $home = $this->get(route('home'));
    $home->assertOk();
    $home->assertViewHas('reviews');
    $homeReviews = $home->viewData('reviews');
    expect($homeReviews->where('booking_id', $booking->id)->first()->comment)->toBe('Perjalanan nyaman dan tepat waktu.');
});

/* =================================================================== */
/* 2. ALUR REFUND */
/* =================================================================== */

test('alur refund: bayar → ajukan refund → admin approve → staf scan ditolak, kursi lepas', function () {
    $schedule = ($this->makeSchedule)();
    $data = ($this->makePaidBooking)($this->passenger, $schedule, ['1']);
    $booking = $data['booking'];
    $payment = $booking->payment;

    // Penumpang mengajukan refund (masih dalam cut-off H-1 jam).
    $apply = $this->actingAs($this->passenger)
        ->post(route('dashboard.user.tiket.refund', $booking->id), [
            'reason' => 'Keberadaan tidak mungkin.',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => $this->passenger->name,
        ]);
    $apply->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('refund_pending');

    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund->status)->toBe('Diajukan');

    // Admin menyetujui refund melalui route verifikasi refund.
    $approve = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.refund.approve', $refund->id), [
            'cancellation_fee' => 0,
            'admin_note' => 'Disetujui.',
        ]);
    $approve->assertSessionHas('success');

    $refund->refresh();
    $booking->refresh();
    $payment->refresh();
    $ticket = Ticket::where('booking_id', $booking->id)->first();

    expect($refund->status)->toBe('Disetujui')
        ->and($booking->status)->toBe('Cancelled')
        ->and($ticket->status)->toBe('Cancelled')
        ->and($payment->status)->toBe('Refunded')
        ->and($schedule->fresh()->available_seats)->toBe($schedule->vehicle->capacity);

    // Staf mencoba scan tiket yang sudah di-refund → ditolak (dibatalkan/di-refund).
    $scan = ($this->staffScan)('QR-'.$booking->booking_code, $schedule);
    $scan->assertStatus(422);
    $scan->assertJson(['success' => false]);
    $title = $scan->json('title');
    expect(in_array($title, ['Tiket Telah Dibatalkan', 'Tiket Telah Di-refund']))->toBeTrue();

    // Kursi telah dibebaskan (bisa dipesan ulang).
    expect($schedule->fresh()->available_seats)->toBe($schedule->vehicle->capacity);
});

/* =================================================================== */
/* 3. RESCHEDULE LALU SCAN */
/* =================================================================== */

test('reschedule lalu scan: jadwal lama salah jadwal, jadwal baru valid', function () {
    $scheduleA = ($this->makeSchedule)(['departure_time' => '09:00']);
    $scheduleB = ($this->makeSchedule)([
        'vehicle' => $scheduleA->vehicle,
        'driver' => $scheduleA->driver,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '14:00',
    ]);
    $data = ($this->makePaidBooking)($this->passenger, $scheduleA, ['1']);
    $booking = $data['booking'];

    // Admin reschedule ke jadwal B dengan kursi yang sama (jadwal B belum ada booking).
    $newSeats = ($this->seatIds)($scheduleB, ['1']);

    $reschedule = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.pemesanan.reschedule.update', $booking->id), [
            'new_schedule_id' => $scheduleB->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Pindah ke jam sore.',
        ]);
    $reschedule->assertSessionHas('success');

    $booking->refresh();
    expect($booking->schedule_id)->toBe($scheduleB->id);

    // Staf scan tiket di jadwal LAMA → salah jadwal.
    $scanOld = ($this->staffScan)('QR-'.$booking->booking_code, $scheduleA);
    $scanOld->assertStatus(422);
    $scanOld->assertJson(['title' => 'Salah Jam Keberangkatan']);

    // Staf scan tiket di jadwal BARU → valid.
    $scanNew = ($this->staffScan)('QR-'.$booking->booking_code, $scheduleB);
    $scanNew->assertStatus(200);
    $scanNew->assertJson(['success' => true]);

    expect($booking->refresh()->passengers->first()->boarding_status)->toBe('boarded');
});

/* =================================================================== */
/* 4. PENYESUAIAN KAPASITAS MOBIL (HALAMAN BERANDA) */
/* =================================================================== */

test('halaman beranda hanya menampilkan mobil kapasitas sedang dan besar, kecil dihapus', function () {
    $small = ($this->makeVehicle)(['capacity' => 4, 'plate_number' => 'B 0001 KECIL', 'name' => 'Mobil Kecil', 'type' => 'Mobil Kecil']);
    $medium = ($this->makeVehicle)(['capacity' => 10, 'plate_number' => 'B 0010 SEDANG', 'name' => 'Mobil Sedang', 'type' => 'Mobil Sedang']);
    $large = ($this->makeVehicle)(['capacity' => 14, 'plate_number' => 'B 0014 BESAR', 'name' => 'Mobil Besar', 'type' => 'Mobil Besar']);

    $home = $this->get(route('home'));
    $home->assertOk();
    $home->assertViewHas('fleetTypes');

    $fleetTypes = $home->viewData('fleetTypes');
    $capacities = $fleetTypes->pluck('capacity')->all();
    $categories = $fleetTypes->pluck('category')->all();

    // Mobil kapasitas kecil (< 10) tidak boleh ada.
    foreach ($capacities as $cap) {
        expect($cap)->toBeGreaterThanOrEqual(10);
    }

    // Hanya kapasitas sedang (<= 10) dan besar (> 10) yang tersedia.
    expect($capacities)->toContain(10);
    expect($capacities)->toContain(14);
    expect($categories)->toContain('Kapasitas Sedang');
    expect($categories)->toContain('Kapasitas Besar');
});
