<?php

use App\Models\Booking;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'user',
        'password' => Hash::make('password'),
    ]);
});

function createUserNotificationFor(
    User $user,
    string $category,
    string $title,
    ?string $deepLink = null,
    ?Carbon $readAt = null,
    ?string $message = null,
): DatabaseNotification {
    return $user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'App\\Notifications\\BookingStatusNotification',
        'data' => [
            'title' => $title,
            'message' => $message ?? 'Isi notifikasi '.$title,
            'category' => $category,
            'deep_link_url' => $deepLink ?? route('dashboard.user.user-tiket'),
        ],
        'read_at' => $readAt,
    ]);
}

test('daftar notifikasi menampilkan daftar dan jumlah per kategori dengan benar', function () {
    $transaksiUnread = createUserNotificationFor($this->user, 'transaksi', 'Transaksi Baru');
    $transaksiRead = createUserNotificationFor($this->user, 'transaksi', 'Transaksi Lama', readAt: Carbon::now()->subHour());
    $pengingat = createUserNotificationFor($this->user, 'pengingat', 'Pengingat Berangkat');
    $operasional = createUserNotificationFor($this->user, 'operasional', 'Info Armada', readAt: Carbon::now()->subHour());

    $this->actingAs($this->user)->get(route('dashboard.user.user-notifikasi'))
        ->assertOk()
        ->assertViewHas('unreadCount', 2)
        ->assertViewHas('countTransaksi', 2)
        ->assertViewHas('countPengingat', 1)
        ->assertViewHas('countOperasional', 1)
        ->assertViewHas('notifications', fn ($notifications) => $notifications->total() === 4);

    $cases = [
        'transaksi' => [$transaksiUnread->id, $transaksiRead->id],
        'pengingat' => [$pengingat->id],
        'operasional' => [$operasional->id],
    ];

    foreach ($cases as $category => $ids) {
        $response = $this->actingAs($this->user)->get(route('dashboard.user.user-notifikasi', [
            'category' => $category,
        ]));

        $response->assertOk();
        $response->assertViewHas('selectedCategory', $category);
        $response->assertViewHas('notifications', function ($notifications) use ($ids) {
            return collect($notifications->items())->pluck('id')->sort()->values()->all()
                === collect($ids)->sort()->values()->all();
        });
    }
});

test('membuka notifikasi menandainya dibaca dan mengarahkan ke deep link', function () {
    $deepLink = route('dashboard.user.user-tiket');
    $notification = createUserNotificationFor($this->user, 'transaksi', 'Buka Tiket', deepLink: $deepLink);

    expect($notification->read_at)->toBeNull();

    $this->actingAs($this->user)
        ->get(route('dashboard.user.notifikasi.open', $notification->id))
        ->assertRedirect($deepLink);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('tandai semua dibaca membuat jumlah belum dibaca menjadi nol', function () {
    createUserNotificationFor($this->user, 'transaksi', 'Satu');
    createUserNotificationFor($this->user, 'pengingat', 'Dua');
    createUserNotificationFor($this->user, 'operasional', 'Tiga');

    $this->actingAs($this->user)
        ->post(route('dashboard.user.notifikasi.mark-all-read'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->user->unreadNotifications()->count())->toBe(0);
    expect($this->user->notifications()->whereNull('read_at')->count())->toBe(0);

    $this->actingAs($this->user)->get(route('dashboard.user.user-notifikasi'))
        ->assertOk()
        ->assertViewHas('unreadCount', 0);
});

test('menghapus notifikasi menghilangkan dari daftar', function () {
    createUserNotificationFor($this->user, 'transaksi', 'Notifikasi Disimpan');
    $deleted = createUserNotificationFor($this->user, 'pengingat', 'Notifikasi Dihapus');

    $this->actingAs($this->user)
        ->delete(route('dashboard.user.notifikasi.destroy', $deleted->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->user->notifications()->whereKey($deleted->id)->exists())->toBeFalse();

    $this->actingAs($this->user)->get(route('dashboard.user.user-notifikasi'))
        ->assertOk()
        ->assertSee('Notifikasi Disimpan')
        ->assertDontSee('Notifikasi Dihapus');
});

test('membuka notifikasi milik user lain menghasilkan 404', function () {
    $otherUser = User::factory()->create(['role' => 'user']);
    $notification = createUserNotificationFor($otherUser, 'transaksi', 'Milik Orang Lain');

    $this->actingAs($this->user)
        ->get(route('dashboard.user.notifikasi.open', $notification->id))
        ->assertNotFound();

    expect($notification->fresh()->read_at)->toBeNull();
});

test('simulasi notifikasi menghasilkan isi yang sesuai untuk setiap tipe', function () {
    $origin = Location::create([
        'code' => 'BDO-NTF',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $destination = Location::create([
        'code' => 'JKT-NTF',
        'name' => 'Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $route = Route::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 125000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $vehicle = Vehicle::create([
        'plate_number' => 'B 6262 NTF',
        'name' => 'HiAce Notifikasi',
        'type' => 'Toyota HiAce Commuter',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);

    $schedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '14:30',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    Booking::create([
        'booking_code' => 'TRV-NTF-0001',
        'user_id' => $this->user->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'pending',
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);

    $cases = [
        'pending' => [
            'status_type' => 'pending',
            'category' => 'transaksi',
            'title' => 'Menunggu Pembayaran',
            'message' => ['menunggu pembayaran'],
        ],
        'confirmed' => [
            'status_type' => 'confirmed',
            'category' => 'transaksi',
            'title' => 'Pembayaran Berhasil',
            'message' => ['telah terverifikasi'],
        ],
        'expired' => [
            'status_type' => 'expired',
            'category' => 'transaksi',
            'title' => 'Transaksi Dibatalkan',
            'message' => ['batas waktu pembayaran telah habis'],
        ],
        'reminder_2h' => [
            'status_type' => 'reminder_2h',
            'category' => 'pengingat',
            'title' => 'Pengingat Perjalanan',
            'message' => ['14:30', '1A, 1B', '14:15'],
        ],
        'operational_vehicle' => [
            'status_type' => 'vehicle_assigned',
            'category' => 'operasional',
            'title' => 'Info Armada',
            'message' => ['B 1234 XYZ', 'Pak Budi Santoso', 'Pool Dipatiukur (Gate 2)'],
        ],
        'operational_delay' => [
            'status_type' => 'delay_traffic',
            'category' => 'operasional',
            'title' => 'Update Operasional',
            'message' => ['Kepadatan arus di Tol Cipularang KM 90', '20 menit'],
        ],
    ];

    foreach ($cases as $type => $expected) {
        $this->user->notifications()->delete();

        $this->actingAs($this->user)
            ->post(route('dashboard.user.notifikasi.simulate'), ['type' => $type])
            ->assertRedirect()
            ->assertSessionHas('success');

        $notification = $this->user->notifications()->firstOrFail();
        $data = $notification->data;

        expect($data['status_type'])->toBe($expected['status_type']);
        expect($data['category'])->toBe($expected['category']);
        expect(strtolower($data['title']))->toContain(strtolower($expected['title']));

        foreach ($expected['message'] as $needle) {
            expect(strtolower($data['message']))->toContain(strtolower($needle));
        }
    }
});
