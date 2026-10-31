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
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'code' => 'BDO-MANUAL',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-MANUAL',
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
            'plate_number' => 'B 7007 MAN',
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

    $this->schedule = ($this->makeSchedule)();

    $this->seatIds = function (Schedule $schedule, array $numbers): array {
        return Seat::where('vehicle_id', $schedule->vehicle_id)
            ->whereIn('seat_number', array_map('strval', $numbers))
            ->orderBy('id')
            ->pluck('id')
            ->all();
    };

    $this->makeBooking = function (Schedule $schedule, array $seatNumbers = ['1'], array $overrides = []): Booking {
        $user = $overrides['user'] ?? User::factory()->create(['role' => 'user']);
        unset($overrides['user']);

        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => count($seatNumbers),
            'subtotal' => 125000 * count($seatNumbers),
            'total_price' => 125000 * count($seatNumbers),
            'status' => 'Paid',
            'source' => 'walk_in',
        ], $overrides));

        foreach ($seatNumbers as $number) {
            $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
                ->where('seat_number', (string) $number)
                ->firstOrFail();

            BookingPassenger::create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'name' => 'Penumpang Kursi '.$seat->seat_number,
                'phone' => '0812000000'.str_pad((string) $seat->seat_number, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $schedule->recalculateAvailableSeats();

        return $booking;
    };
});

test('daftar pemesanan menampilkan statistik dan filter status serta sumber', function () {
    $todaySchedule = $this->schedule;
    $yesterdaySchedule = ($this->makeSchedule)(['departure_date' => Carbon::yesterday()->toDateString()]);

    $paidToday = ($this->makeBooking)($todaySchedule, ['1'], [
        'booking_code' => 'TRV-STAT-PAID',
        'status' => 'Paid',
        'source' => 'walk_in',
    ]);
    $pending = ($this->makeBooking)($todaySchedule, ['2'], [
        'booking_code' => 'TRV-STAT-PENDING',
        'status' => 'Pending',
        'source' => 'online',
    ]);
    $paidYesterday = ($this->makeBooking)($yesterdaySchedule, ['1'], [
        'booking_code' => 'TRV-STAT-YEST',
        'status' => 'Paid',
        'source' => 'whatsapp',
    ]);
    $paidYesterday->forceFill(['created_at' => now()->subDay()])->save();

    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.pemesanan.index'));

    $response->assertOk();
    $response->assertSee('Pemesanan Tiket Travel');
    $response->assertSee('TRV-STAT-PAID');
    $response->assertSee('TRV-STAT-PENDING');
    $response->assertSee('TRV-STAT-YEST');

    expect($response->viewData('totalBookings'))->toBe(3)
        ->and($response->viewData('todayPaidBookings'))->toBe(1)
        ->and($response->viewData('pendingBookings'))->toBe(1)
        ->and($response->viewData('walkInBookings'))->toBe(2);

    $pendingResponse = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.index', ['status' => 'pending']));
    expect($pendingResponse->viewData('bookings')->pluck('booking_code')->all())
        ->toBe(['TRV-STAT-PENDING']);

    $paidResponse = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.index', ['status' => 'paid']));
    $paidCodes = $paidResponse->viewData('bookings')->pluck('booking_code')->all();
    expect($paidCodes)->toContain('TRV-STAT-PAID')
        ->toContain('TRV-STAT-YEST')
        ->not->toContain('TRV-STAT-PENDING');

    $sourceResponse = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.index', ['source' => 'walk_in']));
    $walkInCodes = $sourceResponse->viewData('bookings')->pluck('booking_code')->all();
    expect($walkInCodes)->toContain('TRV-STAT-PAID')
        ->not->toContain('TRV-STAT-PENDING')
        ->not->toContain('TRV-STAT-YEST');
});

test('booking manual lunas membuat pembayaran dan menerbitkan tiket', function () {
    $seats = ($this->seatIds)($this->schedule, [1, 2]);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => $seats,
        'customer_name' => 'Siti Walk In',
        'customer_phone' => '081277778888',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);

    $booking = Booking::latest('id')->first();

    $response->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));
    $response->assertSessionHas('success');

    expect($booking->status)->toBe('Paid')
        ->and($booking->source)->toBe('walk_in')
        ->and((float) $booking->total_price)->toBe(250000.0)
        ->and($booking->passengers()->count())->toBe(2)
        ->and($booking->user)->not->toBeNull()
        ->and($booking->user->role)->toBe('user');

    expect($booking->payment)->not->toBeNull()
        ->and($booking->payment->status)->toBe('Success')
        ->and($booking->payment->payment_method)->toBe('Cash')
        ->and((float) $booking->payment->gross_amount)->toBe(250000.0)
        ->and($booking->payment->paid_at)->not->toBeNull();

    expect($booking->ticket)->not->toBeNull()
        ->and($booking->ticket->status)->toBe('Issued')
        ->and($booking->ticket->ticket_number)->toStartWith('TKT-');

    expect($this->schedule->fresh()->available_seats)->toBe($this->vehicle->capacity - 2);
});

test('booking manual pending tidak menerbitkan tiket', function () {
    $seats = ($this->seatIds)($this->schedule, [3]);

    $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => $seats,
        'customer_name' => 'Joko Bayar Nanti',
        'customer_phone' => '081266667777',
        'source' => 'whatsapp',
        'payment_method' => 'Transfer Bank',
        'payment_status' => 'pending',
    ]);

    $booking = Booking::latest('id')->first();

    expect($booking->status)->toBe('Pending')
        ->and($booking->payment)->not->toBeNull()
        ->and($booking->payment->status)->toBe('Pending')
        ->and($booking->ticket)->toBeNull()
        ->and($this->schedule->fresh()->available_seats)->toBe($this->vehicle->capacity - 1);
});

test('booking manual dengan kursi yang sudah terisi ditolak', function () {
    $seat = ($this->seatIds)($this->schedule, [4]);

    $payload = [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => $seat,
        'customer_name' => 'Pemesan Pertama',
        'customer_phone' => '081255554444',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ];

    $this->actingAs($this->admin)
        ->post(route('dashboard.admin.pemesanan.store-manual'), $payload)
        ->assertRedirect();

    $second = $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), array_merge($payload, [
        'customer_name' => 'Pemesan Kedua',
        'customer_phone' => '081233332222',
    ]));

    $second->assertRedirect();
    $second->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'telah dipesan oleh penumpang lain');
    });

    expect(Booking::count())->toBe(1);
});

test('total booking manual mengikuti harga khusus jadwal seperti pemesanan online', function () {
    $schedule = ($this->makeSchedule)(['price' => 175000]);
    $seat = ($this->seatIds)($schedule, [1]);

    $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $schedule->id,
        'selected_seats' => $seat,
        'customer_name' => 'Harga Khusus',
        'customer_phone' => '081299990000',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);

    $booking = Booking::latest('id')->first();

    expect((float) $booking->total_price)->toBe((float) $schedule->effectivePrice())
        ->and((float) $booking->payment->gross_amount)->toBe((float) $schedule->effectivePrice());
});

test('total booking manual mengikuti tarif akhir pekan seperti pemesanan online', function () {
    $saturday = Carbon::now()->next(Carbon::SATURDAY)->toDateString();
    $schedule = ($this->makeSchedule)(['departure_date' => $saturday, 'price' => null]);
    $seat = ($this->seatIds)($schedule, [1]);

    $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $schedule->id,
        'selected_seats' => $seat,
        'customer_name' => 'Harga Weekend',
        'customer_phone' => '081288880000',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);

    $booking = Booking::latest('id')->first();

    expect((float) $booking->total_price)->toBe((float) $schedule->effectivePrice());
});

test('booking manual tidak menempel ke akun staf saat nomor HP sama', function () {
    $staff = User::factory()->create([
        'name' => 'Staf Loket',
        'role' => 'staff',
        'status' => 'active',
        'phone' => '081211112222',
        'access_code' => 'STF-TEST-01',
    ]);

    $seat = ($this->seatIds)($this->schedule, [5]);

    $this->actingAs($this->admin)->post(route('dashboard.admin.pemesanan.store-manual'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => $seat,
        'customer_name' => 'Budi Pelanggan',
        'customer_phone' => '081211112222',
        'source' => 'walk_in',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ]);

    $booking = Booking::latest('id')->first();

    expect($booking->user_id)->not->toBe($staff->id)
        ->and($booking->user?->role)->not->toBe('staff');
});

test('seat map dan manifes menampilkan data booking aktif', function () {
    $booking = ($this->makeBooking)($this->schedule, ['3'], [
        'booking_code' => 'TRV-MAP-0001',
    ]);

    $booking->passengers()->first()->update([
        'name' => 'Rina Penumpang',
        'phone' => '081233334444',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-MAP-0001',
        'qr_code' => 'QR-TRV-MAP-0001',
        'status' => 'Issued',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'ORD-TRV-MAP-0001',
        'payment_method' => 'Cash',
        'gross_amount' => $booking->total_price,
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $this->schedule->recalculateAvailableSeats();

    $seatMap = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.seat-map', $this->schedule->id));

    $seatMap->assertOk();
    $seatMap->assertSee('Denah Kursi Visual');
    $seatMap->assertSee('Rina Penumpang');
    $seatMap->assertSee('Lunas: 1 Kursi');

    $manifest = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.manifes', $this->schedule->id));

    $manifest->assertOk();
    $manifest->assertSee('Manifes Perjalanan');
    $manifest->assertSee('Rina Penumpang');
    $manifest->assertSee('081233334444');
    $manifest->assertSee('TRV-MAP-0001');
    $manifest->assertSee($ticket->ticket_number);
});

test('detail pemesanan menampilkan detail dan e-tiket', function () {
    $booking = ($this->makeBooking)($this->schedule, ['2'], [
        'booking_code' => 'TRV-DETAIL-01',
        'source' => 'walk_in',
    ]);

    $booking->passengers()->first()->update(['name' => 'Dewi Detail', 'phone' => '081277776666']);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-DETAIL-01',
        'qr_code' => 'QR-TRV-DETAIL-01',
        'status' => 'Issued',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'ORD-TRV-DETAIL-01',
        'payment_method' => 'Cash',
        'gross_amount' => $booking->total_price,
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.show', $booking->id));

    $response->assertOk();
    $response->assertSee('TRV-DETAIL-01');
    $response->assertSee('Dewi Detail');
    $response->assertSee('Sudah Lunas');
    $response->assertSee($ticket->ticket_number);
    $response->assertSee('Cash');
});

test('reschedule valid memindahkan jadwal dan kursi serta menghitung ulang sisa kursi', function () {
    $oldSchedule = ($this->makeSchedule)(['departure_time' => '08:00']);
    $newSchedule = ($this->makeSchedule)(['departure_date' => Carbon::tomorrow()->addDay()->toDateString()]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    $newSeats = ($this->seatIds)($newSchedule, [5, 6]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $newSchedule->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Permintaan pelanggan pindah jam',
        ]
    );

    $response->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));
    $response->assertSessionHas('success');

    $booking->refresh();

    expect($booking->schedule_id)->toBe($newSchedule->id)
        ->and($booking->passengers()->pluck('seat_id')->sort()->values()->all())
        ->toBe(collect($newSeats)->sort()->values()->all())
        ->and($booking->rescheduled_at)->not->toBeNull()
        ->and($booking->reschedule_reason)->toBe('Permintaan pelanggan pindah jam')
        ->and($booking->notes)->toContain('[Reschedule');

    expect($oldSchedule->fresh()->available_seats)->toBe($this->vehicle->capacity)
        ->and($newSchedule->fresh()->available_seats)->toBe($this->vehicle->capacity - 2);
});

test('reschedule dengan jumlah kursi tidak sesuai ditolak', function () {
    $oldSchedule = $this->schedule;
    $newSchedule = ($this->makeSchedule)(['departure_date' => Carbon::tomorrow()->addDay()->toDateString()]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    $newSeats = ($this->seatIds)($newSchedule, [5]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $newSchedule->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Kursi kurang',
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'harus sama dengan jumlah penumpang');
    });

    expect($booking->fresh()->schedule_id)->toBe($oldSchedule->id);
});

test('reschedule ke kursi yang sudah terisi ditolak', function () {
    $oldSchedule = $this->schedule;
    $newSchedule = ($this->makeSchedule)(['departure_date' => Carbon::tomorrow()->addDay()->toDateString()]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    ($this->makeBooking)($newSchedule, ['5']);

    $newSeats = ($this->seatIds)($newSchedule, [5, 6]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $newSchedule->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Pindah jadwal',
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah terisi');
    });

    expect($booking->fresh()->schedule_id)->toBe($oldSchedule->id);
});

test('reschedule ke jadwal lampau ditolak', function () {
    $oldSchedule = $this->schedule;
    $pastSchedule = ($this->makeSchedule)(['departure_date' => Carbon::yesterday()->toDateString()]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    $newSeats = ($this->seatIds)($pastSchedule, [5, 6]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $pastSchedule->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Uji jadwal lampau',
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect($booking->fresh()->schedule_id)->toBe($oldSchedule->id);
});

test('reschedule ke jadwal yang dibatalkan ditolak', function () {
    $oldSchedule = $this->schedule;
    $cancelledSchedule = ($this->makeSchedule)([
        'departure_date' => Carbon::tomorrow()->addDay()->toDateString(),
        'status' => 'Cancelled',
    ]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    $newSeats = ($this->seatIds)($cancelledSchedule, [5, 6]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $cancelledSchedule->id,
            'new_seats' => $newSeats,
            'reschedule_reason' => 'Uji jadwal batal',
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect($booking->fresh()->schedule_id)->toBe($oldSchedule->id);
});

test('reschedule dengan kursi milik armada lain ditolak', function () {
    $oldSchedule = $this->schedule;
    $otherVehicle = ($this->makeVehicle)([
        'plate_number' => 'B 8008 LAIN',
        'name' => 'HiAce Armada Lain',
    ]);
    $otherSchedule = ($this->makeSchedule)(['vehicle' => $otherVehicle]);
    $booking = ($this->makeBooking)($oldSchedule, ['1', '2']);

    $foreignSeats = ($this->seatIds)($oldSchedule, [5, 6]);

    $response = $this->actingAs($this->admin)->put(
        route('dashboard.admin.pemesanan.reschedule.update', $booking->id),
        [
            'new_schedule_id' => $otherSchedule->id,
            'new_seats' => $foreignSeats,
            'reschedule_reason' => 'Uji kursi armada lain',
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect($booking->fresh()->schedule_id)->toBe($oldSchedule->id);
});

test('form reschedule untuk booking yang dibatalkan ditolak', function () {
    $booking = ($this->makeBooking)($this->schedule, ['1'], ['status' => 'Cancelled']);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.pemesanan.reschedule', $booking->id));

    $response->assertRedirect(route('dashboard.admin.pemesanan.show', $booking->id));
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'tidak dapat diubah jadwalnya');
    });
});

test('pembatalan booking membatalkan tiket dan melepaskan kursi', function () {
    $booking = ($this->makeBooking)($this->schedule, ['1', '2'], [
        'booking_code' => 'TRV-CANCEL-01',
    ]);

    Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-CANCEL-01',
        'qr_code' => 'QR-TRV-CANCEL-01',
        'status' => 'Issued',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'ORD-TRV-CANCEL-01',
        'payment_method' => 'Cash',
        'gross_amount' => $booking->total_price,
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    $this->schedule->recalculateAvailableSeats();

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pemesanan.cancel', $booking->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking->refresh();

    expect($booking->status)->toBe('Cancelled')
        ->and($booking->ticket->fresh()->status)->toBe('Cancelled')
        ->and($booking->payment->fresh()->status)->toBe('Refunded')
        ->and($this->schedule->fresh()->available_seats)->toBe($this->vehicle->capacity);

    $refund = Refund::where('booking_id', $booking->id)->first();

    expect($refund)->not->toBeNull()
        ->and($refund->status)->toBe('Disetujui')
        ->and((float) $refund->amount)->toBe((float) $booking->total_price);
});

test('pembatalan booking yang sudah batal ditolak dengan pesan yang jelas', function () {
    $booking = ($this->makeBooking)($this->schedule, ['1'], ['status' => 'Cancelled']);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.pemesanan.cancel', $booking->id));

    $response->assertRedirect();
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah dalam status dibatalkan sebelumnya');
    });

    expect($booking->fresh()->status)->toBe('Cancelled');
});
