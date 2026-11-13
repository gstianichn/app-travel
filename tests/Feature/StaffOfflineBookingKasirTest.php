<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->staff = User::factory()->create([
        'name' => 'Staf Loket Pasteur',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Pool Pasteur Bandung',
        'password' => Hash::make('password123'),
    ]);

    $this->origin = Location::create([
        'code' => 'BDG-KASIR',
        'name' => 'Pool Pasteur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->destination = Location::create([
        'code' => 'JKT-KASIR',
        'name' => 'Pool Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = TravelRoute::create([
        'origin_id' => $this->origin->id,
        'destination_id' => $this->destination->id,
        'price' => 125000,
        'weekend_price' => 150000,
        'duration' => '2 Jam 30 Menit',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'D 1234 KSR',
        'name' => 'Toyota HiAce Premio Eksekutif',
        'type' => 'Minibus Eksekutif',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '09:00:00',
        'price' => 125000,
        'status' => 'Scheduled',
        'available_seats' => 10,
    ]);
});

test('1. staff loket dapat mengakses halaman buat pesanan baru dan navigasi sidebar memuat menu buat pesanan baru', function () {
    $response = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.offline-booking'));

    $response->assertOk()
        ->assertSee('Buat Pesanan Baru')
        ->assertSee('Pilih Jadwal &amp; Denah Kursi', false)
        ->assertSee('Data Pelanggan &amp; Pembayaran Kasir', false)
        ->assertSee('Konfirmasi Lunas &amp; Cetak Tiket', false);

    // Cek pada sidebar staf
    $sidebarCheck = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.index'));
    $sidebarCheck->assertOk()
        ->assertSee('Buat Pesanan Baru');
});

test('2. cek ketersediaan menghitung total kursi yang dipesan online dan offline untuk mencegah double-booking', function () {
    $seats = $this->vehicle->seats()->orderBy('id')->take(3)->get();
    $onlineUser = User::factory()->create(['role' => 'user']);

    // Kursi 1 dipesan oleh Customer Online (Lunas)
    $onlineBooking = Booking::create([
        'booking_code' => 'TRV-ONL-TEST',
        'user_id' => $onlineUser->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $onlineBooking->id,
        'seat_id' => $seats[0]->id,
        'name' => 'Penumpang Online App',
        'phone' => '081211112222',
    ]);

    // Kursi 2 dipesan oleh Customer Offline Loket sebelumnya (Pending reservasi)
    $offlineBooking = Booking::create([
        'booking_code' => 'TRV-LKT-TEST',
        'schedule_id' => $this->schedule->id,
        'contact_name' => 'Pemesan Offline Telp',
        'contact_phone' => '081233334444',
        'total_passenger' => 1,
        'subtotal' => 125000,
        'total_price' => 125000,
        'status' => 'Pending',
        'source' => 'phone',
        'expired_at' => Carbon::now()->addMinutes(20),
    ]);
    BookingPassenger::create([
        'booking_id' => $offlineBooking->id,
        'seat_id' => $seats[1]->id,
        'name' => 'Pemesan Offline Telp',
        'phone' => '081233334444',
    ]);

    // AJAX cek ketersediaan kursi
    $res = $this->actingAs($this->staff)->getJson(route('dashboard.admin-biasa.offline-booking.seats', $this->schedule->id));
    $res->assertOk();

    $data = $res->json();
    expect($data['success'])->toBeTrue()
        ->and($data['schedule']['capacity'])->toBe(10)
        ->and($data['schedule']['occupied_seats'])->toBe(2)
        ->and($data['schedule']['available_seats'])->toBe(8);

    $seatList = collect($data['seats']);
    $seat1 = $seatList->firstWhere('id', $seats[0]->id);
    $seat2 = $seatList->firstWhere('id', $seats[1]->id);
    $seat3 = $seatList->firstWhere('id', $seats[2]->id);

    expect($seat1['status'])->toBe('paid')
        ->and($seat2['status'])->toBe('pending')
        ->and($seat3['status'])->toBe('available');

    // Anti double-booking: Staff mencoba memesan kursi 1 yang sudah terisi online -> ditolak
    $this->actingAs($this->staff)->post(route('dashboard.admin-biasa.offline-booking.store'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => [$seats[0]->id],
        'customer_name' => 'Penumpang Walk In Gagal',
        'customer_phone' => '081299990000',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
        'source' => 'offline',
    ])->assertSessionHas('error');
});

test('3. alur pemesanan offline loket: input data, pilih kursi, konfirmasi lunas, generate tiket dan struk fisik otomatis masuk ke manajemen booking', function () {
    $vacantSeats = $this->vehicle->seats()->orderBy('id')->skip(4)->take(2)->get();

    // Staff membuat pesanan tiket walk-in untuk 2 penumpang, bayar tunai (Konfirmasi Lunas)
    $response = $this->actingAs($this->staff)->post(route('dashboard.admin-biasa.offline-booking.store'), [
        'schedule_id' => $this->schedule->id,
        'selected_seats' => [$vacantSeats[0]->id, $vacantSeats[1]->id],
        'customer_name' => 'Bambang Sudirman',
        'customer_phone' => '081298765432',
        'customer_email' => 'bambang.sudirman@gmail.com',
        'source' => 'offline',
        'payment_method' => 'Cash',
        'payment_status' => 'paid',
        'notes' => 'Bawa 1 tas jinjing',
        'passengers' => [
            $vacantSeats[0]->id => ['name' => 'Bambang Sudirman', 'phone' => '081298765432'],
            $vacantSeats[1]->id => ['name' => 'Siti Nurhaliza', 'phone' => '081298765433'],
        ],
    ]);

    // Redirect ke Manajemen Booking dengan session pesan dan print_url
    $response->assertRedirect(route('dashboard.admin-biasa.booking', ['schedule_id' => $this->schedule->id]))
        ->assertSessionHas('success')
        ->assertSessionHas('print_booking_id')
        ->assertSessionHas('print_url');

    // Cek di Database: status source = 'offline' dan payment_status = 'paid' / status = 'Paid'
    $booking = Booking::where('contact_phone', '081298765432')->firstOrFail();

    expect($booking->source)->toBe('offline')
        ->and($booking->status)->toBe('Paid')
        ->and($booking->total_passenger)->toBe(2)
        ->and((float) $booking->total_price)->toBe(250000.0);

    // Cek relasi penumpang
    expect($booking->passengers)->toHaveCount(2);

    // Cek pembayaran lunas
    $payment = Payment::where('booking_id', $booking->id)->firstOrFail();
    expect($payment->status)->toBe('Success')
        ->and($payment->payment_method)->toBe('Cash')
        ->and($payment->verified_by)->toBe($this->staff->id);

    // Cek generate tiket
    $ticket = Ticket::where('booking_id', $booking->id)->firstOrFail();
    expect($ticket->status)->toBe('Valid')
        ->and($ticket->ticket_number)->toStartWith('TKT-')
        ->and($ticket->qr_code)->toBe('QR-'.$booking->booking_code);

    // Cek halaman cetak struk / tiket fisik untuk pelanggan
    $printPage = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.booking.print-ticket', $booking->id));
    $printPage->assertOk()
        ->assertSee($booking->booking_code)
        ->assertSee($ticket->ticket_number)
        ->assertSee('LUNAS')
        ->assertSee('Bambang Sudirman')
        ->assertSee('Siti Nurhaliza')
        ->assertSee('Pool Pasteur (Bandung)')
        ->assertSee('Pool Semanggi (Jakarta)');

    // Cek otomatis masuk ke Manajemen Booking
    $manifestPage = $this->actingAs($this->staff)->get(route('dashboard.admin-biasa.booking', ['schedule_id' => $this->schedule->id]));
    $manifestPage->assertOk()
        ->assertSee($booking->booking_code)
        ->assertSee('Bambang Sudirman')
        ->assertSee('Siti Nurhaliza')
        ->assertSee('Lunas');
});
