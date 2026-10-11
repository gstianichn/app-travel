<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingStatusNotification;
use App\Notifications\OperationalInfoNotification;
use App\Notifications\TripReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::firstOrCreate(
        ['email' => 'penumpang.notif@travel.test'],
        [
            'role' => 'user',
            'name' => 'Aditya Pratama Notif',
            'phone' => '081299887766',
            'password' => Hash::make('password'),
        ]
    );

    $this->origin = Location::firstOrCreate(
        ['code' => 'BDG-NOTIF'],
        [
            'name' => 'Pool Dipatiukur (Bandung)',
            'city' => 'Bandung',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->destination = Location::firstOrCreate(
        ['code' => 'JKT-NOTIF'],
        [
            'name' => 'Semanggi (Jakarta)',
            'city' => 'Jakarta',
            'type' => 'pool',
            'is_active' => true,
        ]
    );

    $this->route = Route::firstOrCreate(
        [
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
        ],
        [
            'price' => 135000,
            'duration' => '2 Jam 45 Menit',
            'is_active' => true,
        ]
    );

    $this->vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 9999 NTF'],
        [
            'name' => 'Toyota HiAce Premio Luxury',
            'type' => 'HiAce Premio',
            'capacity' => 10,
            'status' => 'available',
        ]
    );

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::today()->toDateString(),
        'departure_time' => Carbon::now()->addHours(2)->format('H:i:s'),
        'total_seats' => 10,
        'available_seats' => 10,
        'price' => 135000,
        'status' => 'scheduled',
    ]);

    $this->seat = Seat::firstOrCreate(
        [
            'vehicle_id' => $this->vehicle->id,
            'seat_number' => '1A',
        ],
        [
            'seat_row' => 1,
            'seat_column' => 1,
            'is_active' => true,
        ]
    );

    $this->booking = Booking::create([
        'booking_code' => 'TRV-NOTIF-01',
        'user_id' => $this->user->id,
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 135000,
        'total_price' => 135000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'seat_id' => $this->seat->id,
        'name' => 'Aditya Pratama',
        'phone' => '081299887766',
        'seat_number' => '1A',
    ]);
});

test('tab notifikasi dapat diakses dan menampilkan unread count serta filter kategori', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-notifikasi'));

    $response->assertStatus(200);
    $response->assertSee('Pemberitahuan');
    $response->assertSee('Izinkan notifikasi');
    $response->assertSee('Status Transaksi');
    $response->assertSee('Pengingat (H-2 Jam)');
    $response->assertSee('Info Operasional');
});

test('event trigger status transaksi pending mencatat batas waktu dan deep link pembayaran', function () {
    $this->user->notify(new BookingStatusNotification($this->booking, 'pending'));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('transaksi');
    expect($notification->data['status_type'])->toBe('pending');
    expect($notification->data['deep_link_url'])->toContain('/user/booking/pembayaran/'.$this->booking->id);
    expect($notification->data['action_label'])->toBe('Bayar Sekarang');
    expect($notification->data['message'])->toContain('menunggu pembayaran');
});

test('event trigger status transaksi confirmed mencatat tiket QR dan deep link ke e-ticket', function () {
    $ticket = Ticket::create([
        'booking_id' => $this->booking->id,
        'ticket_number' => 'TKT-TEST-99',
        'qr_code' => 'QR-TRV-NOTIF-01',
        'status' => 'active',
    ]);

    $this->user->notify(new BookingStatusNotification($this->booking, 'confirmed'));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('transaksi');
    expect($notification->data['status_type'])->toBe('confirmed');
    expect($notification->data['deep_link_url'])->toContain('/user/booking/sukses/'.$this->booking->id);
    expect($notification->data['action_label'])->toBe('Buka E-Ticket');
});

test('event trigger status transaksi expired mencatat pembatalan dan deep link pencarian jadwal', function () {
    $this->user->notify(new BookingStatusNotification($this->booking, 'expired'));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('transaksi');
    expect($notification->data['status_type'])->toBe('expired');
    expect($notification->data['deep_link_url'])->toBe(route('dashboard.user.search'));
    expect($notification->data['action_label'])->toBe('Cari Jadwal Baru');
});

test('event trigger pengingat perjalanan H-2 jam memuat rute, jam tiba pool 15 menit sebelum jalan, dan nomor kursi', function () {
    $this->user->notify(new TripReminderNotification(
        $this->booking,
        departureTime: '14:30',
        requiredArrivalTime: '14:15 WIB',
        seatNumbers: '1A'
    ));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('pengingat');
    expect($notification->data['status_type'])->toBe('reminder_2h');
    expect($notification->data['message'])->toContain('14:30');
    expect($notification->data['message'])->toContain('14:15 WIB');
    expect($notification->data['message'])->toContain('Kursi 1A');
    expect($notification->data['deep_link_url'])->toContain('/tiket/dashboard');
    expect($notification->data['meta']['pool_arrival_time'])->toBe('14:15 WIB');
});

test('event trigger info operasional hari-H memuat pelat minibus dan posisi penjemputan gate pool', function () {
    $this->user->notify(new OperationalInfoNotification(
        $this->booking,
        infoType: 'vehicle_assigned',
        plateNumber: 'D 7890 AB',
        driverName: 'Pak Budi Hartono',
        pickupLocation: 'Pool Dipatiukur (Gate 2)'
    ));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('operasional');
    expect($notification->data['status_type'])->toBe('vehicle_assigned');
    expect($notification->data['message'])->toContain('D 7890 AB');
    expect($notification->data['message'])->toContain('Pak Budi Hartono');
    expect($notification->data['message'])->toContain('Gate 2');
    expect($notification->data['meta']['plate_number'])->toBe('D 7890 AB');
});

test('event trigger info operasional keterlambatan jalan memuat penyesuaian waktu dan alasan lalu lintas', function () {
    $this->user->notify(new OperationalInfoNotification(
        $this->booking,
        infoType: 'delay_traffic',
        plateNumber: 'D 7890 AB',
        driverName: 'Pak Budi Hartono',
        delayReason: 'Kepadatan arus di Tol Cipularang KM 90',
        delayMinutes: 20
    ));

    $notification = $this->user->notifications()->first();
    expect($notification)->not->toBeNull();
    expect($notification->data['category'])->toBe('operasional');
    expect($notification->data['status_type'])->toBe('delay_traffic');
    expect($notification->data['message'])->toContain('20 menit');
    expect($notification->data['message'])->toContain('Tol Cipularang');
});

test('deep linking openNotification menandai dibaca dan me-redirect ke target deep link URL', function () {
    $this->user->notify(new BookingStatusNotification($this->booking, 'pending'));
    $notification = $this->user->notifications()->first();

    expect($notification->unread())->toBeTrue();

    $response = $this->actingAs($this->user)->get(route('dashboard.user.notifikasi.open', $notification->id));

    $response->assertRedirect(route('dashboard.user.booking.pembayaran', $this->booking));
    $notification->refresh();
    expect($notification->read())->toBeTrue();
});

test('markAllAsRead menandai semua notifikasi pengguna sebagai sudah dibaca', function () {
    $this->user->notify(new BookingStatusNotification($this->booking, 'pending'));
    $this->user->notify(new TripReminderNotification($this->booking));

    expect($this->user->unreadNotifications()->count())->toBe(2);

    $response = $this->actingAs($this->user)->post(route('dashboard.user.notifikasi.mark-all-read'));
    $response->assertRedirect();

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

test('simulateNotification endpoint berhasil memicu event trigger dan mencatat ke database riwayat', function () {
    $response = $this->actingAs($this->user)->post(route('dashboard.user.notifikasi.simulate'), [
        'type' => 'reminder_2h',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $notif = $this->user->notifications()->latest()->first();
    expect($notif)->not->toBeNull();
    expect($notif->data['category'])->toBe('pengingat');
});

test('artisan command travel:send-reminders memproses pengingat H-2 jam untuk booking yang ditentukan', function () {
    $this->booking->update(['status' => 'confirmed']);

    $this->artisan('travel:send-reminders', ['--booking' => $this->booking->id])
        ->assertExitCode(0);

    $notif = $this->user->notifications()->where('data->status_type', 'reminder_2h')->first();
    expect($notif)->not->toBeNull();
});
