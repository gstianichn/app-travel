<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserVoucher;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
        'access_code' => Hash::make('ADMIN123'),
    ]);

    $this->csUser = User::factory()->create([
        'role' => 'customer_service',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'name' => 'Pasteur',
        'code' => 'BDO-PST',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Pancoran',
        'code' => 'JKT-PCR',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->otherLocation = Location::create([
        'name' => 'Arjosari',
        'code' => 'MLG-ARJ',
        'city' => 'Malang',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 150000,
        'weekend_price' => 150000,
        'duration' => '3 Jam',
        'is_active' => true,
    ]);

    $this->otherRoute = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->otherLocation->id,
        'price' => 200000,
        'weekend_price' => 200000,
        'duration' => '10 Jam',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 7788 PRE',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 150000,
        'status' => 'Scheduled',
        'total_seats' => 10,
        'available_seats' => 10,
    ]);
});

/**
 * Helper to build a pending booking
 */
function createPendingBooking($user, $schedule, $seatIds = [1], $subtotal = 150000): Booking
{
    $booking = Booking::create([
        'booking_code' => 'TRV-'.strtoupper(bin2hex(random_bytes(4))),
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => count($seatIds),
        'subtotal' => $subtotal,
        'discount' => 0,
        'total_price' => $subtotal,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    foreach ($seatIds as $idx => $seatId) {
        BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seatId,
            'name' => 'Penumpang '.($idx + 1),
            'phone' => '081234567890',
            'boarding_status' => 'belum_hadir',
        ]);
    }

    return $booking;
}

/**
 * Helper to build a paid booking with ticket
 */
function createPaidBookingWithTicket($user, $schedule, $seatIds = [1], $subtotal = 150000): array
{
    $booking = Booking::create([
        'booking_code' => 'TRV-'.strtoupper(bin2hex(random_bytes(4))),
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => count($seatIds),
        'subtotal' => $subtotal,
        'discount' => 0,
        'total_price' => $subtotal,
        'status' => 'Paid',
    ]);

    foreach ($seatIds as $idx => $seatId) {
        BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seatId,
            'name' => 'Penumpang '.($idx + 1),
            'phone' => '081234567890',
            'boarding_status' => 'belum_hadir',
        ]);
    }

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-'.strtoupper(bin2hex(random_bytes(4))),
        'qr_code' => 'QR-'.$booking->booking_code,
        'status' => 'Valid',
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'PAY-'.$booking->booking_code,
        'gross_amount' => $subtotal,
        'payment_method' => 'manual_transfer',
        'status' => 'Success',
        'paid_at' => Carbon::now(),
    ]);

    return [$booking, $ticket, $payment];
}

test('BUG-07 (USR-BK-14, USR-BK-15, USR-PR-05): voucher dompet diterapkan saat simpan, voucher ditandai terpakai, dan voucher kedaluwarsa/terpakai tidak bisa diklaim ulang', function () {
    // Berikan voucher awal agar ensureUserPresets tidak meng-auto-populate KOMPENSASI50
    $this->user->vouchers()->create([
        'code' => 'INITIAL',
        'title' => 'Initial',
        'discount_type' => 'fixed',
        'discount_value' => 1000,
        'min_transaction' => 10000,
        'source' => 'claim_promo',
        'status' => 'active',
        'expires_at' => Carbon::now()->addDays(5),
    ]);

    // 1. Klaim voucher KOMPENSASI50 ke dompet pengguna
    $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'KOMPENSASI50',
    ])->assertRedirect()->assertSessionHas('success');

    $voucher = $this->user->vouchers()->where('code', 'KOMPENSASI50')->first();
    expect($voucher)->not->toBeNull();
    expect($voucher->status)->toBe('active');

    // 2. USR-BK-14: Buat booking pending dengan subtotal Rp 150.000, lalu simpan data dengan voucher KOMPENSASI50
    $seats = $this->vehicle->seats()->take(1)->get();
    $booking = createPendingBooking($this->user, $this->schedule, [$seats[0]->id], 150000);
    $passenger = $booking->passengers()->first();

    $saveResponse = $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking->id), [
        'booker_name' => 'Budi User',
        'booker_phone' => '08123456789',
        'booker_email' => 'budi@test.com',
        'passengers' => [
            [
                'id' => $passenger->id,
                'name' => 'Budi User',
                'phone' => '08123456789',
            ],
        ],
        'promo_code' => 'KOMPENSASI50',
    ]);

    $saveResponse->assertRedirect(route('dashboard.user.booking.pembayaran', $booking->id));

    // Verifikasi total bayar berkurang Rp 50.000 dan voucher ditandai 'used'
    $booking->refresh();
    expect((float) $booking->discount)->toBe(50000.0);
    expect((float) $booking->total_price)->toBe(100000.0);

    $voucher->refresh();
    expect($voucher->status)->toBe('used');
    expect($voucher->used_at)->not->toBeNull();

    // 3. USR-BK-15: Coba pakai lagi voucher KOMPENSASI50 yang sudah terpakai di booking baru -> Ditolak di validatePromo
    $booking2 = createPendingBooking($this->user, $this->schedule, [$this->vehicle->seats()->skip(1)->first()->id], 150000);
    $validateResponse = $this->actingAs($this->user)->postJson(route('dashboard.user.booking.validate-promo'), [
        'promo_code' => 'KOMPENSASI50',
        'booking_id' => $booking2->id,
    ]);
    $validateResponse->assertStatus(422);
    $validateResponse->assertJsonFragment(['valid' => false]);
    expect($validateResponse->json('message'))->toContain('sudah pernah digunakan');

    // 4. USR-PR-05: Klaim ulang voucher yang sudah kedaluwarsa atau sudah terpakai -> Ditolak
    $claimAgainResponse = $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'KOMPENSASI50',
    ]);
    $claimAgainResponse->assertSessionHas('error');
    expect(session('error'))->toContain('sudah pernah digunakan');

    // Buat voucher kedaluwarsa di akun user
    $expiredVoucher = UserVoucher::create([
        'user_id' => $this->user->id,
        'code' => 'SUPERDISCOUNT',
        'title' => 'Promo Super Hemat 30%',
        'discount_type' => 'percent',
        'discount_value' => 30,
        'min_transaction' => 150000,
        'source' => 'claim_promo',
        'status' => 'expired',
        'expires_at' => Carbon::now()->subDays(2),
    ]);

    $claimExpiredResponse = $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'SUPERDISCOUNT',
    ]);
    $claimExpiredResponse->assertSessionHas('error');
    expect(session('error'))->toContain('sudah kedaluwarsa dan tidak dapat diklaim ulang');

    // 5. HEMATLIBURAN: Cek rute berlaku (Bandung - Jakarta) vs rute lain (Bandung - Malang)
    $otherSchedule = Schedule::create([
        'route_id' => $this->otherRoute->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '12:00:00',
        'price' => 200000,
        'status' => 'Scheduled',
        'total_seats' => 10,
        'available_seats' => 10,
    ]);
    $bookingMalang = createPendingBooking($this->user, $otherSchedule, [$seats[0]->id], 200000);

    $promoCheckOtherRoute = $this->actingAs($this->user)->postJson(route('dashboard.user.booking.validate-promo'), [
        'promo_code' => 'HEMATLIBURAN',
        'booking_id' => $bookingMalang->id,
    ]);
    $promoCheckOtherRoute->assertStatus(422);
    expect($promoCheckOtherRoute->json('message'))->toContain('hanya berlaku untuk rute Bandung ke Jakarta');
});

test('BUG-08 (USR-TK-04, USR-TK-05): check-in tiket dibatalkan ditolak dan check-in booking pending belum bayar ditolak tanpa menerbitkan tiket', function () {
    $seat = $this->vehicle->seats()->first();

    // 1. USR-TK-05: Check-in booking pending (belum bayar)
    $pendingBooking = createPendingBooking($this->user, $this->schedule, [$seat->id], 150000);
    expect(Ticket::where('booking_id', $pendingBooking->id)->exists())->toBeFalse();

    $checkInPendingResponse = $this->actingAs($this->user)
        ->post(route('dashboard.user.tiket.check-in', $pendingBooking->id));

    $checkInPendingResponse->assertRedirect();
    $checkInPendingResponse->assertSessionHas('error');
    expect(session('error'))->toContain('Check-in mandiri hanya dapat dilakukan untuk tiket yang sudah lunas');

    // Pastikan TIDAK ADA tiket yang terbit
    expect(Ticket::where('booking_id', $pendingBooking->id)->exists())->toBeFalse();
    expect($pendingBooking->fresh()->status)->toBe('pending');

    // 2. USR-TK-04: Check-in tiket yang sudah dibatalkan/refund
    [$paidBooking, $ticket, $payment] = createPaidBookingWithTicket($this->user, $this->schedule, [$seat->id]);

    // Batalkan tiket & booking
    $paidBooking->update(['status' => 'cancelled']);
    $ticket->update(['status' => 'Cancelled']);

    $checkInCancelledResponse = $this->actingAs($this->user)
        ->post(route('dashboard.user.tiket.check-in', $ticket->id));

    $checkInCancelledResponse->assertRedirect();
    $checkInCancelledResponse->assertSessionHas('error');
    expect(session('error'))->toContain('Tiket yang telah dibatalkan tidak dapat di-check-in');
    expect($ticket->fresh()->status)->toBe('Cancelled');
});

test('BUG-09 (ADM-RF-03, ADM-RF-07, CS-03): counter refund pending menghitung Diajukan, refund ganda ditolak, dan reject refund mengembalikan tiket & booking aktif', function () {
    $seat = $this->vehicle->seats()->first();
    [$booking, $ticket, $payment] = createPaidBookingWithTicket($this->user, $this->schedule, [$seat->id]);

    // 1. Penumpang mengajukan refund -> status Diajukan & status booking refund_pending
    $refundSubmitResponse = $this->actingAs($this->user)->post(route('dashboard.user.tiket.refund', $booking->id), [
        'reason' => 'Ada keperluan mendadak di kantor.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi User',
    ]);
    $refundSubmitResponse->assertRedirect()->assertSessionHas('success');

    $refund = Refund::where('booking_id', $booking->id)->first();
    expect($refund)->not->toBeNull();
    expect($refund->status)->toBe('Diajukan');
    expect($booking->fresh()->status)->toBe('refund_pending');

    // 2. CS-03: Counter refund pending di Customer Service & Admin menghitung refund berstatus 'Diajukan'
    $csPage = $this->actingAs($this->csUser)->get(route('dashboard.customer-service.index'));
    $csPage->assertOk();
    $csStats = $csPage->viewData('stats');
    expect($csStats['pending_refunds'])->toBeGreaterThanOrEqual(1);

    $adminRefundPage = $this->actingAs($this->admin)->get(route('dashboard.admin.refund.index'));
    $adminRefundPage->assertOk();
    expect($adminRefundPage->viewData('pendingRefundCount'))->toBeGreaterThanOrEqual(1);

    // 3. ADM-RF-03: Admin mencoba membuat refund ganda untuk booking yang sama -> Ditolak
    $duplicateRefundResponse = $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), [
        'booking_id' => $booking->id,
        'reason' => 'Admin ingin refund lagi',
        'cancellation_fee_type' => 'percentage',
        'fee_percentage' => 10,
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Budi User',
    ]);
    $duplicateRefundResponse->assertRedirect();
    $duplicateRefundResponse->assertSessionHas('error');
    expect(Refund::where('booking_id', $booking->id)->count())->toBe(1);

    // 4. ADM-RF-07: Admin menolak (reject) permohonan refund penumpang
    $rejectResponse = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.reject', $refund->id), [
        'admin_note' => 'Permohonan refund ditolak karena melanggar syarat dan ketentuan.',
    ]);
    $rejectResponse->assertRedirect();
    $rejectResponse->assertSessionHas('success');

    // Booking dan tiket harus kembali aktif (Paid / Valid) dan kursi tidak dilepas
    expect($refund->fresh()->status)->toBe('Ditolak');
    expect($booking->fresh()->status)->toBe('Paid');
    expect($ticket->fresh()->status)->toBe('Valid');
});

test('BUG-10 (ADM-PAY-05, ADM-PAY-06): approve pembayaran bentrok kursi pada booking batal ditolak, dan reject pada pembayaran lunas ditolak', function () {
    $seat = $this->vehicle->seats()->first();

    // 1. ADM-PAY-05: Booking A sempat batal, kursinya sudah dibeli Booking B (Paid)
    // Booking A (Cancelled)
    $bookingA = Booking::create([
        'booking_code' => 'TRV-BOOKING-A',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Cancelled',
    ]);
    BookingPassenger::create([
        'booking_id' => $bookingA->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang A',
        'phone' => '081234567890',
    ]);
    $paymentA = Payment::create([
        'booking_id' => $bookingA->id,
        'order_id' => 'PAY-BOOKING-A',
        'gross_amount' => 150000,
        'payment_method' => 'manual_transfer',
        'status' => 'Pending',
    ]);

    // Booking B mengambil kursi yang sama dan sudah Paid
    $userB = User::factory()->create(['role' => 'user']);
    $bookingB = Booking::create([
        'booking_code' => 'TRV-BOOKING-B',
        'user_id' => $userB->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Paid',
    ]);
    BookingPassenger::create([
        'booking_id' => $bookingB->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang B',
        'phone' => '081298765432',
    ]);

    // Admin mencoba approve pembayaran manual Booking A padahal kursinya sudah dipakai Booking B
    $approveAConflict = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $paymentA->id), [
        'action' => 'approve',
    ]);
    $approveAConflict->assertRedirect();
    $approveAConflict->assertSessionHas('error');
    expect(session('error'))->toContain('telah ditempati oleh pemesanan lain');
    expect($paymentA->fresh()->status)->toBe('Pending');
    expect($bookingA->fresh()->status)->toBe('Cancelled');

    // 2. ADM-PAY-06: Reject pada pembayaran yang sudah lunas (Success)
    [$paidBooking, $ticket, $successPayment] = createPaidBookingWithTicket($this->user, $this->schedule, [$this->vehicle->seats()->skip(1)->first()->id]);
    expect($successPayment->status)->toBe('Success');
    expect($paidBooking->status)->toBe('Paid');

    $rejectPaidPayment = $this->actingAs($this->admin)->patch(route('dashboard.admin.pembayaran.verify', $successPayment->id), [
        'action' => 'reject',
        'rejection_reason' => 'Ingin batalkan pembayaran lunas',
    ]);
    $rejectPaidPayment->assertRedirect();
    $rejectPaidPayment->assertSessionHas('error');
    expect(session('error'))->toContain('Pembayaran yang sudah lunas tidak dapat ditolak');

    // Pastikan pembayaran tetap Success dan booking tetap Paid
    expect($successPayment->fresh()->status)->toBe('Success');
    expect($paidBooking->fresh()->status)->toBe('Paid');
    expect($ticket->fresh()->status)->toBe('Valid');
});

test('BUG-11 (ADM-PAY-09): sinkron gateway status expire mengubah booking menjadi Expired dan melepaskan kembali sisa kursi serta menghapus kursi dari seat occupancy map', function () {
    $seats = $this->vehicle->seats()->take(2)->get();
    $booking = createPendingBooking($this->user, $this->schedule, [$seats[0]->id, $seats[1]->id], 300000);
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'order_id' => 'PAY-EXPIRE-TEST',
        'gross_amount' => 300000,
        'payment_method' => 'midtrans_va',
        'status' => 'Pending',
    ]);

    // Hitung ulang awal -> tersedia 8 dari 10 kursi, dan kursi 1 & 2 berstatus pending di seat map
    $this->schedule->recalculateAvailableSeats();
    expect($this->schedule->fresh()->available_seats)->toBe(8);
    $flatSeatsBefore = collect($this->schedule->fresh()->getSeatOccupancyMap())->flatten(1);
    expect($flatSeatsBefore->firstWhere('seat_id', $seats[0]->id)['occupancy']['status'])->toBe('pending');
    expect($flatSeatsBefore->firstWhere('seat_id', $seats[1]->id)['occupancy']['status'])->toBe('pending');

    // Admin melakukan sinkron gateway status -> expire
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pembayaran.sync-gateway', $payment->id), [
        'gateway_provider' => 'midtrans',
        'gateway_status' => 'expire',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Booking dan payment menjadi Expired, seluruh kursi (10) kembali tersedia dan occupancy map berstatus available
    expect($payment->fresh()->status)->toBe('Expired');
    expect($booking->fresh()->status)->toBe('Expired');
    expect($this->schedule->fresh()->available_seats)->toBe(10);
    $flatSeatsAfter = collect($this->schedule->fresh()->getSeatOccupancyMap())->flatten(1);
    expect($flatSeatsAfter->firstWhere('seat_id', $seats[0]->id)['occupancy']['status'])->toBe('available');
    expect($flatSeatsAfter->firstWhere('seat_id', $seats[1]->id)['occupancy']['status'])->toBe('available');
});

test('BUG-12 (ADM-VH-07): menghapus kendaraan dengan jadwal lampau/selesai atau kursi yang masih terhubung dengan penumpang ditolak dengan pesan rapi (tidak error 500)', function () {
    // 1. Kendaraan dengan jadwal lampau berstatus completed
    $pastVehicle = Vehicle::create([
        'plate_number' => 'D 1212 PST',
        'name' => 'HiAce Lampau',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $pastVehicle->ensureSeatsExist();

    Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $pastVehicle->id,
        'departure_date' => Carbon::now()->subDays(5)->toDateString(),
        'departure_time' => '08:00:00',
        'price' => 150000,
        'status' => 'completed',
        'total_seats' => 10,
        'available_seats' => 10,
    ]);

    $destroyPastResponse = $this->actingAs($this->admin)->delete(route('dashboard.admin.memantau-kendaraan.destroy', $pastVehicle->id));
    $destroyPastResponse->assertRedirect();
    $destroyPastResponse->assertSessionHas('error');
    expect(session('error'))->toContain('tidak dapat dihapus');
    expect(Vehicle::find($pastVehicle->id))->not->toBeNull();

    // 2. Kendaraan tanpa jadwal tetapi kursinya masih terhubung dengan booking_passengers (misal setelah ganti armada jadwal)
    $orphanSeatVehicle = Vehicle::create([
        'plate_number' => 'D 3434 ORP',
        'name' => 'HiAce Orphan Seat',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $orphanSeatVehicle->ensureSeatsExist();
    $orphanSeat = $orphanSeatVehicle->seats()->first();

    $booking = Booking::create([
        'booking_code' => 'TRV-ORPHAN-SEAT',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Paid',
    ]);
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $orphanSeat->id,
        'name' => 'Penumpang Riwayat Kursi',
        'phone' => '081234567890',
    ]);

    $destroyOrphanResponse = $this->actingAs($this->admin)->delete(route('dashboard.admin.memantau-kendaraan.destroy', $orphanSeatVehicle->id));
    $destroyOrphanResponse->assertRedirect();
    $destroyOrphanResponse->assertSessionHas('error');
    expect(session('error'))->toContain('tidak dapat dihapus');
    expect(Vehicle::find($orphanSeatVehicle->id))->not->toBeNull();
});
