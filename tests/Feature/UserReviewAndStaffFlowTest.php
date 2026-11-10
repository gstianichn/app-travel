<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->staff = User::factory()->create([
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Pool Pasteur Bandung',
        'password' => Hash::make('password123'),
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'email' => 'penumpang.google@gmail.com',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'code' => 'BDG-REV',
        'name' => 'Pool Pasteur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-REV',
        'name' => 'Pool Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 150000,
        'weekend_price' => 175000,
        'duration' => '3 Jam',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 8899 REV',
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
        'available_seats' => 10,
    ]);
});

test('1. validasi nomor HP hanya menerima angka saja dan menolak huruf atau simbol pada seluruh form utama', function () {
    // Register dengan huruf pada phone -> ditolak
    $this->post(route('register.store'), [
        'name' => 'Tes Angka',
        'email' => 'angka.test@gmail.com',
        'phone' => '0812abcd5678',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('phone');

    // Register dengan simbol (+ / -) -> ditolak
    $this->post(route('register.store'), [
        'name' => 'Tes Simbol',
        'email' => 'simbol.test@gmail.com',
        'phone' => '+62-812-3456',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('phone');

    // Update profile user dengan huruf -> ditolak
    $this->actingAs($this->user)->post(route('dashboard.user.profil.update'), [
        'name' => 'Penumpang Google',
        'email' => $this->user->email,
        'phone' => '0812huruf90',
    ])->assertSessionHasErrors('phone');

    // Simpan data pemesan saat checkout dengan huruf -> ditolak
    $seat = $this->vehicle->seats()->first();
    $booking = Booking::create([
        'booking_code' => 'TRV-HP-001',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);
    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat->id,
        'name' => 'Penumpang',
    ]);

    $this->actingAs($this->user)->post(route('dashboard.user.booking.save-passenger', $booking->id), [
        'booker_name' => 'Penumpang Google',
        'booker_phone' => '0812abc1234',
        'booker_email' => $this->user->email,
        'passengers' => [
            ['id' => $passenger->id, 'name' => 'Penumpang Google', 'phone' => '0812xyz'],
        ],
    ])->assertSessionHasErrors(['booker_phone', 'passengers.0.phone']);
});

test('2. halaman lupa password menampilkan opsi masuk Google dan menangani status driver email lokal dengan aman', function () {
    $page = $this->get(route('password.request'));
    $page->assertOk()
        ->assertSee('Masuk dengan Akun Google')
        ->assertSee('Kirim Tautan ke Gmail');

    // User nonaktif ditolak reset password
    $inactive = User::factory()->create([
        'email' => 'nonaktif@gmail.com',
        'role' => 'user',
        'status' => 'inactive',
    ]);

    $this->post(route('password.email'), ['email' => 'nonaktif@gmail.com'])
        ->assertSessionHasErrors('email');

    // User aktif berhasil membuat token reset + informasi status mailer & preview URL
    $response = $this->post(route('password.email'), ['email' => $this->user->email]);
    $response->assertRedirect();
    $response->assertSessionHas('status');
    $response->assertSessionHas('reset_email_sent_to', $this->user->email);
    $response->assertSessionHas('reset_preview_url');
});

test('3. modal persetujuan refund admin memiliki tombol kirim persetujuan yang terlihat jelas dan dapat menyetujui refund', function () {
    $booking = Booking::create([
        'booking_code' => 'TRV-RF-BTN',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'refund_pending',
    ]);
    $refund = Refund::create([
        'booking_id' => $booking->id,
        'user_id' => $this->user->id,
        'original_amount' => 150000,
        'amount' => 112500,
        'cancellation_fee' => 37500,
        'refund_percentage' => 75,
        'reason' => 'Ganti jadwal',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Penumpang Google',
        'status' => 'Diajukan',
    ]);

    $refundIndex = $this->actingAs($this->admin)->get(route('dashboard.admin.refund.index'));
    $refundIndex->assertOk()
        ->assertSee('Kirim Persetujuan &amp; Setujui Refund', false);

    $bookingShow = $this->actingAs($this->admin)->get(route('dashboard.admin.pemesanan.show', $booking->id));
    $bookingShow->assertOk()
        ->assertSee('Kirim Persetujuan &amp; Setujui Refund', false);
});

test('4 & 5. landing page tidak menampilkan section Jadwal & Rute Shuttle dan membatasi maksimal 3 ulasan', function () {
    for ($i = 1; $i <= 5; $i++) {
        $b = Booking::create([
            'booking_code' => "TRV-LAND-{$i}",
            'user_id' => $this->user->id,
            'schedule_id' => $this->schedule->id,
            'total_passenger' => 1,
            'subtotal' => 150000,
            'total_price' => 150000,
            'status' => 'completed',
        ]);
        TripReview::create([
            'booking_id' => $b->id,
            'user_id' => $this->user->id,
            'schedule_id' => $this->schedule->id,
            'overall_rating' => 5,
            'comment' => "Komentar Uji Ke-{$i} untuk verifikasi batas maksimal tiga ulasan.",
        ]);
    }

    $home = $this->get(route('home'));
    $home->assertOk();

    // Item 4: Jadwal & Rute Shuttle dihapus dari landing page
    $home->assertDontSee('Jadwal &amp; Rute Shuttle Antar Kota', false);
    $home->assertDontSee('Daftar Jadwal &amp; Titik Point Keberangkatan', false);

    // Item 5: Hanya 3 ulasan terbaru yang ditampilkan meskipun ada 5 ulasan di DB
    $reviews = $home->viewData('reviews');
    expect($reviews)->toHaveCount(3);
});

test('6. alur staff pool: pemesanan offline loket, sinkron kursi real-time, dan pelunasan customer sebelum boarding', function () {
    // 1. Beranda & halaman pemesanan loket staff pool dapat diakses
    $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.index'))
        ->assertOk()
        ->assertSee('Alur Customer Online (Website / Aplikasi)')
        ->assertSee('Alur Customer Offline (Loket / WA / Telepon)')
        ->assertSee('Input Pesanan Offline (Loket)');

    $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.offline-booking'))
        ->assertOk()
        ->assertSee('Pemesanan Loket Pool (Non-Website)');

    $seats = $this->vehicle->seats()->take(2)->get();

    // 2. Simulasikan Customer Online sudah memesan Kursi 1 (Paid)
    $onlineBooking = Booking::create([
        'booking_code' => 'TRV-ONL-100',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $onlineBooking->id,
        'seat_id' => $seats[0]->id,
        'name' => 'Penumpang Online',
        'phone' => '081200000001',
    ]);

    // Endpoint kursi loket menampilkan kursi 1 sebagai 'paid' (terisi) dan kursi 2 'available'
    $seatMapJson = $this->actingAs($this->staff)
        ->getJson(route('dashboard.admin-biasa.offline-booking.seats', $this->schedule->id))
        ->assertOk()
        ->json('seats');
    $seat1 = collect($seatMapJson)->firstWhere('id', $seats[0]->id);
    $seat2 = collect($seatMapJson)->firstWhere('id', $seats[1]->id);
    expect($seat1['status'])->toBe('paid')
        ->and($seat2['status'])->toBe('available');

    // 3. Staff mencoba memesan kursi 1 (yang sudah dibeli Customer Online) untuk Customer Offline -> ditolak
    $this->actingAs($this->staff)->post(route('dashboard.admin-biasa.offline-booking.store'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => [$seats[0]->id],
        'source' => 'walk_in',
        'customer_name' => 'Budi Walk-In',
        'customer_phone' => '081234567890',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
    ])->assertSessionHas('error');

    // 4. Staff memesan kursi 2 untuk Customer Offline WA (status Pending / bayar saat tiba di pool)
    $this->actingAs($this->staff)->post(route('dashboard.admin-biasa.offline-booking.store'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => [$seats[1]->id],
        'source' => 'whatsapp',
        'customer_name' => 'Sari WhatsApp',
        'customer_phone' => '081399998888',
        'payment_method' => 'Cash',
        'payment_status' => 'pending',
        'passengers' => [
            $seats[1]->id => ['name' => 'Sari WhatsApp', 'phone' => '081399998888'],
        ],
    ])->assertRedirect(route('dashboard.admin-biasa.booking', ['schedule_id' => $this->schedule->id]))
        ->assertSessionHas('success');

    $offlineBooking = Booking::where('contact_phone', '081399998888')->firstOrFail();
    expect($offlineBooking->status)->toBe('Pending')
        ->and($offlineBooking->source)->toBe('whatsapp');

    // 5. Sebelum dilunasi, validasi tiket atau manual checklist boarding harus menolak (unpaid + can_settle)
    $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.validasi-tiket.validate'), [
        'code' => $offlineBooking->booking_code,
        'schedule_id' => $this->schedule->id,
    ])->assertStatus(422)->assertJson([
        'success' => false,
        'reason' => 'unpaid',
        'can_settle' => true,
        'booking_id' => $offlineBooking->id,
    ]);

    $offlinePassenger = $offlineBooking->passengers()->first();
    $this->actingAs($this->staff)->patchJson(route('dashboard.admin-biasa.validasi-tiket.update-status', $offlinePassenger->id), [
        'status' => 'boarded',
    ])->assertStatus(422);

    // 6. Staff memproses pelunasan di loket + langsung boarding
    $this->actingAs($this->staff)->postJson(route('dashboard.admin-biasa.booking.settle', $offlineBooking->id), [
        'payment_method' => 'Cash',
        'mark_boarded' => true,
    ])->assertOk()->assertJson(['success' => true]);

    expect($offlineBooking->fresh()->status)->toBe('checked_in')
        ->and($offlinePassenger->fresh()->boarding_status)->toBe('boarded')
        ->and(Payment::where('booking_id', $offlineBooking->id)->first()->status)->toBe('Success')
        ->and(Ticket::where('booking_id', $offlineBooking->id)->first()->status)->toBe('Boarded');
});
