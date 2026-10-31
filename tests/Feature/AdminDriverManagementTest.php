<?php

use App\Models\Driver;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->driverPayload = function (array $overrides = []): array {
        return array_merge([
            'name' => 'Bambang Pamungkas',
            'email' => 'bambang.driver@travel.test',
            'phone' => '081234567899',
            'password' => 'password123',
            'license_number' => 'SIM-9988-7766-55',
            'license_type' => 'SIM B1 Umum',
            'license_expired_at' => now()->addYears(3)->format('Y-m-d'),
            'emergency_contact_name' => 'Tri Hastuti',
            'emergency_contact_relation' => 'Istri',
            'emergency_contact_phone' => '085299887766',
            'address' => 'Jl. Soekarno Hatta No. 45, Bandung',
            'status' => 'standby',
        ], $overrides);
    };

    $this->makeDriver = function (array $overrides = []): Driver {
        $user = User::create([
            'name' => 'Agus Supardi',
            'email' => 'agus.driver@travel.test',
            'phone' => '087711223344',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'status' => 'active',
            'access_code' => 'DRV-2026-TEST',
        ]);

        return Driver::create(array_merge([
            'user_id' => $user->id,
            'phone' => '087711223344',
            'license_number' => 'SIM-1122-3344-55',
            'license_type' => 'SIM B1 Umum',
            'license_expired_at' => now()->addMonths(6),
            'emergency_contact_name' => 'Wati',
            'emergency_contact_phone' => '087799881122',
            'emergency_contact_relation' => 'Istri',
            'status' => 'standby',
        ], $overrides));
    };
});

test('tambah driver lengkap dengan SIM unik dan foto maksimal 2MB', function () {
    $photo = UploadedFile::fake()->createWithContent('driver.png', $this->png);

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), ($this->driverPayload)([
            'photo' => $photo,
        ]));

    $response->assertRedirect(route('dashboard.admin.kelola-driver.index'));

    $user = User::where('email', 'bambang.driver@travel.test')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('driver');
    expect($user->status)->toBe('active');

    $driver = Driver::where('license_number', 'SIM-9988-7766-55')->first();
    expect($driver)->not->toBeNull();
    expect($driver->user_id)->toBe($user->id);
    expect($driver->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($driver->photo);
});

test('driver baru dapat login di halaman login', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), ($this->driverPayload)());

    $message = $response->getSession()->get('success');
    preg_match('/kode akses login: (DRV-\d{4}-[A-Z0-9]{4})/', (string) $message, $matches);

    expect($matches[1] ?? null)->not->toBeNull();

    Auth::logout();
    $this->flushSession();

    $login = $this->post(route('login'), [
        'email' => 'bambang.driver@travel.test',
        'password' => 'password123',
        'role' => 'driver',
        'access_code' => $matches[1],
    ]);

    $login->assertRedirect(route('dashboard.driver.index'));

    $driverUser = User::where('email', 'bambang.driver@travel.test')->first();
    $this->assertAuthenticatedAs($driverUser);
});

test('driver tanpa kode akses tidak dapat login (BUG-04)', function () {
    User::create([
        'name' => 'Driver Seeder',
        'email' => 'driver.seeder@travel.test',
        'phone' => '081200000009',
        'password' => 'password',
        'role' => 'driver',
        'status' => 'active',
    ]);

    $response = $this->post(route('login'), [
        'email' => 'driver.seeder@travel.test',
        'password' => 'password',
        'role' => 'driver',
        'access_code' => 'DRV-2026-TRAVEL',
    ]);

    $response->assertSessionHasErrors('access_code');
    $this->assertGuest();
});

test('SIM duplikat ditolak dengan error validasi', function () {
    ($this->makeDriver)();

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), ($this->driverPayload)([
            'license_number' => 'SIM-1122-3344-55',
        ]));

    $response->assertSessionHasErrors(['license_number' => 'Nomor SIM ini sudah terdaftar pada sistem.']);

    expect(Driver::where('license_number', 'SIM-1122-3344-55')->count())->toBe(1);
});

test('foto lebih dari 2MB ditolak dengan error validasi', function () {
    $photo = UploadedFile::fake()->createWithContent(
        'driver.png',
        $this->png.str_repeat("\0", 3 * 1024 * 1024)
    );

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), ($this->driverPayload)([
            'photo' => $photo,
        ]));

    $response->assertSessionHasErrors(['photo' => 'Ukuran foto maksimal 2MB.']);

    expect(User::where('email', 'bambang.driver@travel.test')->exists())->toBeFalse();
});

test('foto bukan gambar ditolak dengan error validasi', function () {
    $photo = UploadedFile::fake()->create('driver.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), ($this->driverPayload)([
            'photo' => $photo,
        ]));

    $response->assertSessionHasErrors(['photo' => 'File foto harus berupa gambar valid.']);

    expect(User::where('email', 'bambang.driver@travel.test')->exists())->toBeFalse();
});

test('edit driver menyimpan perubahan SIM dan status', function () {
    $driver = ($this->makeDriver)();

    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-driver.update', $driver->id), [
            'name' => $driver->user->name,
            'email' => $driver->user->email,
            'phone' => $driver->phone,
            'license_number' => 'SIM-7788-9900-11',
            'license_type' => $driver->license_type,
            'license_expired_at' => now()->addYears(2)->format('Y-m-d'),
            'emergency_contact_name' => $driver->emergency_contact_name,
            'emergency_contact_phone' => $driver->emergency_contact_phone,
            'emergency_contact_relation' => $driver->emergency_contact_relation,
            'address' => $driver->address,
            'notes' => $driver->notes,
            'status' => 'on_duty',
        ]);

    $response->assertRedirect(route('dashboard.admin.kelola-driver.show', $driver->id));

    $driver->refresh();

    expect($driver->license_number)->toBe('SIM-7788-9900-11');
    expect($driver->status)->toBe('on_duty');
});

test('ubah status cepat driver standby ke on_duty lalu on_leave', function () {
    $driver = ($this->makeDriver)();

    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-driver.update-status', $driver->id), [
            'status' => 'on_duty',
        ])
        ->assertSessionHas('success');

    expect($driver->fresh()->status)->toBe('on_duty');

    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-driver.update-status', $driver->id), [
            'status' => 'on_leave',
        ])
        ->assertSessionHas('success');

    expect($driver->fresh()->status)->toBe('on_leave');
});

test('hapus driver yang masih punya jadwal mendatang dicegah', function () {
    $driver = ($this->makeDriver)();

    $vehicle = Vehicle::create([
        'name' => 'Toyota HiAce Premio',
        'plate_number' => 'D 1234 ABC',
        'type' => 'Minibus Eksekutif',
        'capacity' => 12,
        'status' => 'Available',
    ]);

    $schedule = Schedule::create([
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => now()->addDays(3)->format('Y-m-d'),
        'departure_time' => '08:00:00',
        'available_seats' => 10,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.kelola-driver.destroy', $driver->id));

    $response->assertSessionHas('error');

    expect(Driver::find($driver->id))->not->toBeNull();
    expect($schedule->fresh()->driver_id)->toBe($driver->id);
});
