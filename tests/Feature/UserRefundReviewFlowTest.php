<?php

use App\Models\Booking;
use App\Models\Location;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'user',
        'password' => Hash::make('password'),
    ]);

    $origin = Location::create([
        'code' => 'BDO-RFR',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $destination = Location::create([
        'code' => 'JKT-RFR',
        'name' => 'Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = Route::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 125000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'B 5151 RFR',
        'name' => 'HiAce Refund Review',
        'type' => 'Toyota HiAce Commuter',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();
});

function createRefundReviewSchedule(Route $route, Vehicle $vehicle, Carbon $departure, int $availableSeats = 10): Schedule
{
    return Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => $departure->toDateString(),
        'departure_time' => $departure->format('H:i'),
        'price' => 125000,
        'available_seats' => $availableSeats,
        'status' => 'Scheduled',
    ]);
}

function createRefundReviewBooking(User $user, Schedule $schedule, string $status, string $bookingCode): Booking
{
    return Booking::create([
        'booking_code' => $bookingCode,
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => $status,
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);
}

test('ajukan refund minimal satu jam sebelum berangkat lalu disetujui admin memotong 25 persen, membatalkan booking, dan melepas kursi', function () {
    $departure = Carbon::tomorrow()->setTime(14, 30);
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, $departure, 9);
    $booking = createRefundReviewBooking($this->user, $schedule, 'confirmed', 'TRV-RFR-PASS');

    $booking->passengers()->create([
        'seat_id' => $this->vehicle->seats()->first()->id,
        'name' => 'Budi Refund',
    ]);

    expect($booking->canRefund())->toBeTrue();

    $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
        'reason' => 'Perubahan rencana perjalanan mendadak',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi Santoso',
    ])->assertRedirect();

    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull();
    expect((float) $refund->cancellation_fee)->toBe(31250.0);
    expect((float) $refund->amount)->toBe(93750.0);
    expect((float) $refund->refund_percentage)->toBe(75.0);

    // Sebelum disetujui admin, booking masih menunggu dan kursi masih ditahan.
    expect($booking->fresh()->status)->toBe('refund_pending');
    expect($schedule->fresh()->available_seats)->toBe(9);

    $admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
    ]);

    $this->actingAs($admin)->patch(route('dashboard.admin.refund.approve', $refund), [
        'cancellation_fee' => 31250,
        'admin_note' => 'ACC admin',
    ])->assertRedirect(route('dashboard.admin.refund.index'));

    $refund->refresh();
    $booking->refresh();
    $schedule->refresh();

    expect([
        'cancellation_fee' => (float) $refund->cancellation_fee,
        'refund_amount' => (float) $refund->amount,
        'refund_percentage' => (float) $refund->refund_percentage,
        'refund_status' => strtolower((string) $refund->status),
        'booking_status' => strtolower((string) $booking->status),
        'available_seats' => $schedule->available_seats,
    ])->toBe([
        'cancellation_fee' => 31250.0,
        'refund_amount' => 93750.0,
        'refund_percentage' => 75.0,
        'refund_status' => 'disetujui',
        'booking_status' => 'cancelled',
        'available_seats' => 10,
    ]);
});

test('ajukan refund kurang dari satu jam sebelum berangkat ditolak karena batas waktu', function () {
    $departure = Carbon::now()->addMinutes(30);
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, $departure, 9);
    $booking = createRefundReviewBooking($this->user, $schedule, 'confirmed', 'TRV-RFR-LATE');

    expect($booking->canRefund())->toBeFalse();

    $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
        'reason' => 'Perubahan rencana mendadak',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi Santoso',
    ])->assertSessionHas('error');

    expect(session('error'))->toContain('Batas waktu pembatalan telah berakhir');
    expect($booking->fresh()->status)->toBe('confirmed');
    expect(Refund::where('booking_id', $booking->id)->exists())->toBeFalse();
});

test('ajukan refund booking belum bayar atau sudah batal ditolak', function () {
    $departure = Carbon::tomorrow()->setTime(14, 30);
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, $departure);

    foreach (['pending', 'cancelled'] as $index => $status) {
        $booking = createRefundReviewBooking($this->user, $schedule, $status, 'TRV-RFR-'.$index);

        $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
            'reason' => 'Perubahan rencana mendadak',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Budi Santoso',
        ])->assertSessionHas('error');

        expect($booking->fresh()->status)->toBe($status);
        expect(Refund::where('booking_id', $booking->id)->exists())->toBeFalse();
    }
});

test('ajukan refund dengan field bank atau rekening kosong menghasilkan error validasi', function () {
    $departure = Carbon::tomorrow()->setTime(14, 30);
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, $departure);
    $booking = createRefundReviewBooking($this->user, $schedule, 'confirmed', 'TRV-RFR-EMPTY');

    $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking), [
        'reason' => 'Perubahan rencana mendadak',
        'bank_name' => '',
        'account_number' => '',
        'account_holder' => 'Budi Santoso',
    ])->assertSessionHasErrors(['bank_name', 'account_number']);

    expect($booking->fresh()->status)->toBe('confirmed');
    expect(Refund::where('booking_id', $booking->id)->exists())->toBeFalse();
});

test('kirim ulasan menyimpan penilaian tiga aspek dan menghitung rata-rata', function () {
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, Carbon::tomorrow()->setTime(9, 30));
    $booking = createRefundReviewBooking($this->user, $schedule, 'completed', 'TRV-REV-OK');

    $this->actingAs($this->user)->post(route('dashboard.user.tiket.review', $booking), [
        'cleanliness_rating' => 4,
        'driver_rating' => 5,
        'punctuality_rating' => 3,
        'comment' => 'Kabin bersih, sopir ramah, tetapi keberangkatan sedikit terlambat.',
    ])->assertRedirect()->assertSessionHas('success');

    $review = TripReview::where('booking_id', $booking->id)->first();

    expect($review)->not->toBeNull();
    expect($review->cleanliness_rating)->toBe(4);
    expect($review->driver_rating)->toBe(5);
    expect($review->punctuality_rating)->toBe(3);
    expect((float) $review->overall_rating)->toBe(4.0);
    expect($review->comment)->toBe('Kabin bersih, sopir ramah, tetapi keberangkatan sedikit terlambat.');
});

test('kirim ulasan dengan nilai di luar batas dan komentar terlalu panjang menghasilkan error validasi', function () {
    $schedule = createRefundReviewSchedule($this->route, $this->vehicle, Carbon::tomorrow()->setTime(9, 30));
    $booking = createRefundReviewBooking($this->user, $schedule, 'completed', 'TRV-REV-INVALID');

    foreach ([0, 6] as $rating) {
        $this->actingAs($this->user)->post(route('dashboard.user.tiket.review', $booking), [
            'cleanliness_rating' => $rating,
            'driver_rating' => $rating,
            'punctuality_rating' => $rating,
            'comment' => str_repeat('a', 1001),
        ])->assertSessionHasErrors([
            'cleanliness_rating',
            'driver_rating',
            'punctuality_rating',
            'comment',
        ]);
    }

    expect(TripReview::where('booking_id', $booking->id)->exists())->toBeFalse();
});
