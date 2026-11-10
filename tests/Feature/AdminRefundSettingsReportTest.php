<?php

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\OperationalLocation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
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
        'name' => 'Pool Dipatiukur (Bandung)',
        'code' => 'BDO-REF',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'name' => 'Semanggi (Jakarta)',
        'code' => 'JKT-REF',
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
        'plate_number' => 'B 3333 REF',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Minibus Eksekutif',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '09:00',
        'price' => null,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $this->makePaidBooking = function (array $overrides = []): Booking {
        $booking = Booking::create(array_merge([
            'booking_code' => 'TRV-REF-'.strtoupper(Str::random(4)).'-'.rand(1000, 9999),
            'user_id' => $this->customer->id,
            'schedule_id' => $this->schedule->id,
            'total_passenger' => 1,
            'subtotal' => 125000,
            'total_price' => 125000,
            'status' => 'Paid',
            'source' => 'online',
        ], $overrides));
        $seat = Seat::where('vehicle_id', $this->vehicle->id)->where('seat_number', '1')->firstOrFail();
        BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'name' => 'Penumpang Refund',
            'phone' => '081200000001',
        ]);
        Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => 'TKT-REF-01',
            'qr_code' => 'QR-TRV-REF-01',
            'status' => 'Issued',
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => 'ORD-'.$booking->booking_code,
            'payment_method' => 'Transfer Bank',
            'gross_amount' => 125000,
            'status' => 'Success',
            'paid_at' => now(),
        ]);

        return $booking;
    };

    $this->refundPayload = function (Booking $booking, array $overrides = []): array {
        return array_merge([
            'booking_id' => $booking->id,
            'reason' => 'Keberadaan tidak mungkin.',
            'cancellation_fee_type' => 'percentage',
            'fee_percentage' => 10,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => $this->customer->name,
        ], $overrides);
    };
});

test('buat refund persentase: nominal bersih = 90% dari harga tiket', function () {
    $booking = ($this->makePaidBooking)();

    $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), ($this->refundPayload)($booking));

    $refund = Refund::where('booking_id', $booking->id)->first();

    expect($refund)->not->toBeNull()
        ->and($refund->status)->toBe('Menunggu')
        ->and((float) $refund->cancellation_fee)->toBe(12500.0)
        ->and((float) $refund->amount)->toBe(112500.0)
        ->and((float) $refund->refund_percentage)->toBe(90.0);
});

test('buat refund nominal: fee melebihi harga tiket dibatasi, bersih 0', function () {
    $booking = ($this->makePaidBooking)();

    $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), ($this->refundPayload)($booking, [
        'cancellation_fee_type' => 'nominal',
        'cancellation_fee' => 200000,
    ]));

    $refund = Refund::where('booking_id', $booking->id)->first();

    expect($refund)->not->toBeNull()
        ->and((float) $refund->cancellation_fee)->toBe(125000.0)
        ->and((float) $refund->amount)->toBe(0.0)
        ->and((float) $refund->refund_percentage)->toBe(0.0);
});

test('refund ganda ditolak ketika booking sudah punya pengajuan aktif', function () {
    $booking = ($this->makePaidBooking)();
    Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Pertama.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Menunggu',
    ]));

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), ($this->refundPayload)($booking));

    $response->assertRedirect();
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah memiliki permohonan refund');
    });

    expect(Refund::where('booking_id', $booking->id)->count())->toBe(1);
});

test('refund ganda dengan status Diajukan ditolak (BUG-09)', function () {
    $booking = ($this->makePaidBooking)();
    Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Pertama.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Diajukan',
    ]));

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), ($this->refundPayload)($booking));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Refund::where('booking_id', $booking->id)->count())->toBe(1);
});

test('approve refund: status Disetujui, booking & tiket batal, pembayaran Refunded, kursi lepas', function () {
    $booking = ($this->makePaidBooking)();
    $refund = Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Alasan.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Menunggu',
    ]));

    $proof = UploadedFile::fake()->createWithContent('bukti.png', $this->png);

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.approve', $refund->id), [
        'transfer_proof' => $proof,
        'cancellation_fee' => 12500,
    ]);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $refund->refresh();
    $booking->refresh();

    expect($refund->status)->toBe('Disetujui')
        ->and($refund->processed_by)->toBe($this->admin->id)
        ->and($refund->transfer_proof)->not->toBeNull()
        ->and($booking->status)->toBe('Cancelled')
        ->and(Ticket::where('booking_id', $booking->id)->first()->status)->toBe('Cancelled')
        ->and(Payment::where('booking_id', $booking->id)->first()->status)->toBe('Refunded')
        ->and($this->schedule->fresh()->available_seats)->toBe($this->vehicle->capacity);
});

test('approve ulang refund yang sudah disetujui ditolak', function () {
    $booking = ($this->makePaidBooking)();
    $refund = Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Alasan.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Disetujui',
    ]));

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.approve', $refund->id), [
        'cancellation_fee' => 12500,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', function (string $message): bool {
        return str_contains($message, 'sudah disetujui sebelumnya');
    });

    expect($refund->fresh()->status)->toBe('Disetujui');
});

test('reject refund tanpa alasan atau kurang dari 5 karakter gagal divalidasi', function () {
    $booking = ($this->makePaidBooking)();
    $refund = Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Alasan.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Menunggu',
    ]));

    $empty = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.reject', $refund->id), [
        'admin_note' => '',
    ]);
    $empty->assertRedirect();
    $empty->assertSessionHasErrors(['admin_note']);

    $short = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.reject', $refund->id), [
        'admin_note' => 'No.',
    ]);
    $short->assertRedirect();
    $short->assertSessionHasErrors(['admin_note']);

    expect($refund->fresh()->status)->toBe('Menunggu');
});

test('reject refund Diajukan: booking & tiket kembali aktif', function () {
    $booking = ($this->makePaidBooking)();
    $refund = Refund::create(array_merge([
        'booking_id' => $booking->id,
        'user_id' => $booking->user_id,
        'original_amount' => 125000,
        'amount' => 112500,
        'cancellation_fee' => 12500,
        'refund_percentage' => 90,
        'reason' => 'Alasan.',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Tes',
        'status' => 'Diajukan',
    ]));

    $response = $this->actingAs($this->admin)->patch(route('dashboard.admin.refund.reject', $refund->id), [
        'admin_note' => 'Alasan penolakan yang cukup panjang.',
    ]);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $refund->refresh();

    expect($refund->status)->toBe('Ditolak')
        ->and(Booking::find($booking->id)->status)->toBe('Paid')
        ->and(Ticket::where('booking_id', $booking->id)->first()->status)->toBe('Valid');
});

test('counter refund pending bertambah saat pengajuan dibuat', function () {
    ($this->makePaidBooking)();

    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.refund.index'));
    $before = (int) $response->viewData('pendingRefundCount');

    $this->actingAs($this->admin)->post(route('dashboard.admin.refund.store'), ($this->refundPayload)(Booking::latest('id')->first()));

    $after = (int) $this->actingAs($this->admin)->get(route('dashboard.admin.refund.index'))->viewData('pendingRefundCount');

    expect($after)->toBe($before + 1);
});

test('laporan menampilkan semua tab (rekonsiliasi, keterisian kursi, manifes, refund)', function () {
    foreach (['financial', 'occupancy', 'manifest', 'cancellation'] as $tab) {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.index', ['tab' => $tab]));
        $response->assertOk();
    }
});

test('laporan filter tanggal mengubah angka', function () {
    ($this->makePaidBooking)();

    $all = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.index', [
        'tab' => 'financial',
        'date_from' => Carbon::today()->subDays(30)->toDateString(),
        'date_to' => Carbon::today()->toDateString(),
    ]));
    $all->assertOk();

    $empty = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.index', [
        'tab' => 'financial',
        'date_from' => '2020-01-01',
        'date_to' => '2020-01-31',
    ]));
    $empty->assertOk();

    $allBookings = (float) $all->viewData('grossRevenue');
    $emptyBookings = (float) $empty->viewData('grossRevenue');

    expect($allBookings)->toBeGreaterThan($emptyBookings)
        ->and($emptyBookings)->toBe(0.0);
});

test('ekspor Excel dan PDF laporan dapat diunduh', function () {
    $excel = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.export.excel', [
        'tab' => 'financial',
    ]));
    $excel->assertStatus(200);

    $pdf = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.export.pdf', [
        'tab' => 'financial',
    ]));
    $pdf->assertStatus(200);
});

test('laporan tanpa data pada rentang tanggal tidak error dan tampil kosong', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.laporan.index', [
        'tab' => 'financial',
        'date_from' => '2020-01-01',
        'date_to' => '2020-01-31',
    ]));

    $response->assertOk();
    expect((float) $response->viewData('grossRevenue'))->toBe(0.0)
        ->and($response->viewData('paidBookings')->count())->toBe(0);
});

test('halaman pengaturan menampilkan 5 bagian utama', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard.admin.pengaturan.index'));
    $response->assertOk();

    foreach (['Profil Perusahaan', 'Kebijakan Tiket', 'Integrasi Pembayaran', 'Titik Operasional', 'Keamanan Sistem'] as $section) {
        $response->assertSee($section);
    }
});

test('profil perusahaan: nama wajib, logo maksimal 2MB tersimpan', function () {
    $missing = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.profil'), []);
    $missing->assertRedirect();
    $missing->assertSessionHasErrors(['company_name']);

    $logo = UploadedFile::fake()->createWithContent('logo.png', $this->png);

    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.profil'), [
        'company_name' => 'TravelKu Test',
        'company_email' => 'cs@travelku.test',
        'company_logo' => $logo,
    ]);

    $response->assertRedirect(route('dashboard.admin.pengaturan.index', ['tab' => 'profil']));
    $response->assertSessionHas('success');

    expect(AppSetting::get('company_name'))->toBe('TravelKu Test')
        ->and(AppSetting::get('company_logo'))->not->toBeNull();

    Storage::disk('public')->assertExists(AppSetting::get('company_logo'));
});

test('kebijakan tiket: timeout 1-1440 menit dan cutoff reschedule 0-168 jam tersimpan; luar batas ditolak', function () {
    $out = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.kebijakan'), [
        'payment_timeout_qris' => 0,
        'payment_timeout_transfer' => 30,
        'reschedule_cutoff_hours' => 200,
        'reschedule_fee' => 0,
        'baggage_free_kg' => 7,
        'baggage_excess_fee_per_kg' => 50000,
    ]);
    $out->assertRedirect();
    $out->assertSessionHasErrors(['payment_timeout_qris', 'reschedule_cutoff_hours']);

    $ok = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.kebijakan'), [
        'payment_timeout_qris' => 15,
        'payment_timeout_transfer' => 1440,
        'reschedule_cutoff_hours' => 0,
        'reschedule_fee' => 25000,
        'baggage_free_kg' => 7,
        'baggage_excess_fee_per_kg' => 50000,
    ]);
    $ok->assertRedirect(route('dashboard.admin.pengaturan.index', ['tab' => 'kebijakan']));
    $ok->assertSessionHas('success');

    expect(AppSetting::get('payment_timeout_qris'))->toBe('15')
        ->and(AppSetting::get('reschedule_cutoff_hours'))->toBe('0');
});

test('integrasi pembayaran: provider dan key tersimpan', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.pembayaran'), [
        'gateway_provider' => 'midtrans',
        'gateway_server_key' => 'SB-Mid-server-12345',
        'gateway_client_key' => 'SB-Mid-client-67890',
        'bank_name' => 'BCA',
        'bank_account_number' => '1234567890',
        'bank_account_holder' => 'TravelKu',
    ]);

    $response->assertRedirect(route('dashboard.admin.pengaturan.index', ['tab' => 'pembayaran']));
    $response->assertSessionHas('success');

    expect(AppSetting::get('gateway_provider'))->toBe('midtrans')
        ->and(AppSetting::get('gateway_server_key'))->toBe('SB-Mid-server-12345');
});

test('titik operasional: CRUD jalan, maps_url tidak valid ditolak', function () {
    $add = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.lokasi.store'), [
        'name' => 'Garasi Pusat',
        'address' => 'Jl. Raya No. 1',
        'maps_url' => 'https://maps.google.com/?q=1',
        'coordinates' => '-6.91,107.61',
        'phone' => '081211112222',
    ]);
    $add->assertRedirect();
    $add->assertSessionHas('success');

    $location = OperationalLocation::latest()->first();
    expect($location)->not->toBeNull()
        ->and($location->name)->toBe('Garasi Pusat');

    $badUrl = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.lokasi.store'), [
        'name' => 'Salah',
        'maps_url' => 'not-a-valid-url',
    ]);
    $badUrl->assertRedirect();
    $badUrl->assertSessionHasErrors(['maps_url']);

    $edit = $this->actingAs($this->admin)->put(route('dashboard.admin.pengaturan.lokasi.update', $location->id), [
        'name' => 'Garasi Pusat Baru',
        'maps_url' => 'https://maps.google.com/?q=2',
    ]);
    $edit->assertRedirect();
    $edit->assertSessionHas('success');
    expect($location->fresh()->name)->toBe('Garasi Pusat Baru');

    $delete = $this->actingAs($this->admin)->delete(route('dashboard.admin.pengaturan.lokasi.destroy', $location->id));
    $delete->assertRedirect();
    $delete->assertSessionHas('success');
    expect(OperationalLocation::find($location->id))->toBeNull();
});

test('ganti password admin: lama benar + baru 8+ + konfirmasi berhasil', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.keamanan'), [
        'current_password' => 'password123',
        'new_password' => 'newpass12345',
        'new_password_confirmation' => 'newpass12345',
    ]);

    $response->assertRedirect(route('dashboard.admin.pengaturan.index', ['tab' => 'keamanan']));
    $response->assertSessionHas('success');

    $this->admin->refresh();
    expect(Hash::check('newpass12345', $this->admin->password))->toBeTrue();
});

test('password lama salah gagal dengan pesan yang jelas', function () {
    $response = $this->actingAs($this->admin)->post(route('dashboard.admin.pengaturan.keamanan'), [
        'current_password' => 'salah123',
        'new_password' => 'newpass12345',
        'new_password_confirmation' => 'newpass12345',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['current_password'], function ($errors) {
        foreach ($errors as $error) {
            if (str_contains((string) $error, 'Kata sandi saat ini tidak cocok')) {
                return true;
            }
        }

        return false;
    });
});
