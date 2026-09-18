<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Admin Utama User
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.refundtest@travel.test'],
        [
            'name' => 'Admin Utama Refund Test',
            'phone' => '08123456701',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Regular Customer User
    $this->customer = User::firstOrCreate(
        ['email' => 'customer.refundtest@travel.test'],
        [
            'name' => 'Customer Refund Test',
            'phone' => '08987654301',
            'role' => 'penumpang',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    // Lokasi & Rute
    $origin = Location::firstOrCreate(
        ['code' => 'BDG-REF'],
        ['name' => 'Bandung Pasteur Pool', 'city' => 'Bandung', 'address' => 'Jl. Pasteur No. 10', 'type' => 'pool']
    );

    $destination = Location::firstOrCreate(
        ['code' => 'JKT-REF'],
        ['name' => 'Jakarta Tebet Pool', 'city' => 'Jakarta', 'address' => 'Jl. Tebet No. 20', 'type' => 'drop_point']
    );

    $this->route = TravelRoute::firstOrCreate(
        ['origin_id' => $origin->id, 'destination_id' => $destination->id],
        ['price' => 150000, 'duration' => '3 Jam']
    );

    // Armada & Kursi
    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 7777 REF'],
        [
            'name' => 'Toyota HiAce Refund Unit',
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
        ['email' => 'driver.refundtest@travel.test'],
        [
            'name' => 'Sopir Refund Test',
            'phone' => '087711223301',
            'role' => 'driver',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );

    $this->driver = Driver::firstOrCreate(
        ['user_id' => $driverUser->id],
        [
            'license_number' => 'SIM-REFUND-01',
            'license_type' => 'B1',
            'license_expired_at' => now()->addYears(2),
            'emergency_contact_name' => 'Keluarga Sopir',
            'emergency_contact_phone' => '087799887701',
            'status' => 'standby',
        ]
    );

    // Jadwal Perjalanan
    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '08:00',
        'available_seats' => 10,
        'price' => 150000,
        'status' => 'Scheduled',
    ]);

    // Booking & Kursi yang dipesan
    $this->seat = $this->vehicle->seats()->first();

    $this->booking = Booking::create([
        'booking_code' => 'TRV-REF-101',
        'user_id' => $this->customer->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 150000,
        'discount' => 0,
        'total_price' => 150000,
        'status' => 'Paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'seat_id' => $this->seat->id,
        'name' => 'Penumpang Refund Test',
        'phone' => '08987654301',
    ]);

    $this->ticket = Ticket::create([
        'booking_id' => $this->booking->id,
        'ticket_number' => 'TKT-REF-101',
        'qr_code' => 'QR-REF-101',
        'status' => 'Issued',
        'issued_at' => now(),
    ]);

    $this->payment = Payment::create([
        'booking_id' => $this->booking->id,
        'order_id' => 'INV-REF-101',
        'gross_amount' => 150000,
        'payment_method' => 'Transfer Bank BCA',
        'status' => 'Success',
        'paid_at' => now(),
    ]);

    // Hitung ulang kuota kursi: sisa 9 kursi
    $this->schedule->recalculateAvailableSeats();
    expect($this->schedule->available_seats)->toBe(9);
});

// 1. Hak Akses
test('guest and customer cannot access refund management pages', function () {
    $this->get(route('dashboard.admin.refund.index'))
        ->assertRedirect(route('login'));

    $this->actingAs($this->customer)
        ->get(route('dashboard.admin.refund.index'))
        ->assertStatus(403);
});

// 2. Tampilan Index & Metrik Ringkasan
test('admin utama can view refund list and summary metrics', function () {
    Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 37500, // 25% fee
        'amount' => 112500, // 75% net
        'refund_percentage' => 75,
        'reason' => 'Ada keperluan keluarga mendadak di luar kota',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Refund Test',
        'status' => 'Menunggu',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.refund.index'));

    $response->assertStatus(200);
    $response->assertSee('Kelola Refund & Pembatalan Tiket');
    $response->assertSee('TRV-REF-101');
    $response->assertSee('Customer Refund Test');
    $response->assertSee('Rp 150.000');
    $response->assertSee('Rp 112.500');
    $response->assertSee('BCA');
    $response->assertSee('Menunggu Persetujuan');
});

// 3. Tambah Permohonan Refund Baru oleh Admin
test('admin utama can create a new refund request with cancellation fee calculation', function () {
    $data = [
        'booking_id' => $this->booking->id,
        'reason' => 'Penumpang meminta pembatalan lewat WhatsApp CS',
        'cancellation_fee_type' => 'percentage',
        'fee_percentage' => 20, // Potongan 20%
        'bank_name' => 'Mandiri',
        'account_number' => '9876543210',
        'account_holder' => 'Customer Refund Test',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.refund.store'), $data);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('refunds', [
        'booking_id' => $this->booking->id,
        'original_amount' => 150000,
        'cancellation_fee' => 30000, // 20% dari 150.000
        'amount' => 120000, // Net 120.000
        'bank_name' => 'Mandiri',
        'account_number' => '9876543210',
        'status' => 'Menunggu',
    ]);
});

// 4. Setujui Permohonan Refund & Kosongkan Kursi Otomatis
test('admin utama can approve refund which cancels booking and releases seat back to schedule', function () {
    $refund = Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 25000,
        'amount' => 125000,
        'refund_percentage' => 83.33,
        'reason' => 'Permohonan pembatalan resmi',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Refund Test',
        'status' => 'Menunggu',
    ]);

    // Kursi sebelum refund disetujui adalah 9
    expect($this->schedule->available_seats)->toBe(9);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.refund.approve', $refund), [
            'cancellation_fee' => 25000,
            'admin_note' => 'Refund disetujui, dana telah ditransfer via transfer bank BCA.',
        ]);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $refund->refresh();
    expect($refund->status)->toBe('Disetujui');
    expect((int) $refund->amount)->toBe(125000);
    expect((int) $refund->cancellation_fee)->toBe(25000);
    expect($refund->processed_by)->toBe($this->admin->id);
    expect($refund->processed_at)->not->toBeNull();

    // Pemesanan & Tiket dibatalkan
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Cancelled');
    $this->ticket->refresh();
    expect($this->ticket->status)->toBe('Cancelled');

    // Kursi di jadwal keberangkatan DIKOSONGKAN KEMBALI (dari 9 menjadi 10)
    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(10);
});

// 5. Tolak Permohonan Refund
test('admin utama can reject refund request with reason and seat remains occupied', function () {
    $refund = Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 0,
        'amount' => 150000,
        'refund_percentage' => 100,
        'reason' => 'Ingin batalkan tiket 10 menit sebelum jam jalan',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Refund Test',
        'status' => 'Menunggu',
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.refund.reject', $refund), [
            'admin_note' => 'Permohonan ditolak karena diajukan mendadak saat minibus sudah siap berangkat.',
        ]);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $refund->refresh();
    expect($refund->status)->toBe('Ditolak');
    expect($refund->admin_note)->toContain('Permohonan ditolak karena diajukan mendadak');

    // Pemesanan & tiket tetap aktif
    $this->booking->refresh();
    expect($this->booking->status)->toBe('Paid');

    // Kursi tetap terisi (tetap 9)
    $this->schedule->refresh();
    expect($this->schedule->available_seats)->toBe(9);
});

// 6. Setujui Refund dengan Unggah Bukti Struk Transfer Bank
test('admin utama can approve refund and upload transfer proof image', function () {
    Storage::fake('public');

    $refund = Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 30000,
        'amount' => 120000,
        'refund_percentage' => 80,
        'reason' => 'Ubah rencana perjalanan',
        'bank_name' => 'BRI',
        'account_number' => '5555666677',
        'account_holder' => 'Customer Refund Test',
        'status' => 'Menunggu',
    ]);

    $file = UploadedFile::fake()->create('struk_refund.png', 100, 'image/png');

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.refund.approve', $refund), [
            'cancellation_fee' => 30000,
            'transfer_proof' => $file,
            'admin_note' => 'Bukti struk transfer terlampir.',
        ]);

    $response->assertRedirect(route('dashboard.admin.refund.index'));
    $response->assertSessionHas('success');

    $refund->refresh();
    expect($refund->transfer_proof)->not->toBeNull();
    Storage::disk('public')->assertExists($refund->transfer_proof);
});

// 7. Cegah Pengajuan Refund Duplikat untuk Booking yang Sama
test('validation rejects duplicate active refund request for same booking', function () {
    Refund::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->customer->id,
        'original_amount' => 150000,
        'cancellation_fee' => 25000,
        'amount' => 125000,
        'refund_percentage' => 83.33,
        'reason' => 'Pengajuan refund pertama',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Refund Test',
        'status' => 'Menunggu',
    ]);

    $data = [
        'booking_id' => $this->booking->id,
        'reason' => 'Pengajuan refund kedua yang duplikat',
        'cancellation_fee_type' => 'percentage',
        'fee_percentage' => 25,
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Customer Refund Test',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.refund.store'), $data);

    $response->assertSessionHas('error');
});
