<?php

use App\Models\Driver;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Pastikan user admin utama ada
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.drivertest@travel.test'],
        [
            'name' => 'Admin Utama Driver Test',
            'phone' => '08123456780',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('admin utama can view kelola driver index page and status counters', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-driver.index'))
        ->assertOk()
        ->assertSee('Kelola Driver')
        ->assertSee('Total Pengemudi')
        ->assertSee('Siap Jalan (Standby)');
});

test('admin utama can view create driver page', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-driver.create'))
        ->assertOk()
        ->assertSee('Pendaftaran Pengemudi Baru')
        ->assertSee('Nomor Surat Izin Mengemudi (SIM)')
        ->assertSee('Kontak Darurat Keluarga');
});

test('admin utama can store a new driver with legalities and emergency contact', function () {
    $driverData = [
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
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-driver.store'), $driverData);

    $response->assertRedirect(route('dashboard.admin.kelola-driver.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'bambang.driver@travel.test',
        'role' => 'driver',
    ]);

    $this->assertDatabaseHas('drivers', [
        'license_number' => 'SIM-9988-7766-55',
        'license_type' => 'SIM B1 Umum',
        'emergency_contact_name' => 'Tri Hastuti',
        'emergency_contact_relation' => 'Istri',
        'status' => 'standby',
    ]);

    $driver = Driver::where('license_number', 'SIM-9988-7766-55')->first();
    expect($driver->statusLabel())->toBe('Siap Jalan (Standby)');
    expect($driver->licenseStatus()['status'])->toBe('valid');
});

test('admin utama can toggle driver work status between standby, on duty, and on leave', function () {
    $user = User::create([
        'name' => 'Agus Supardi',
        'email' => 'agus.driver@travel.test',
        'phone' => '087711223344',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    $driver = Driver::create([
        'user_id' => $user->id,
        'phone' => '087711223344',
        'license_number' => 'SIM-1122-3344-55',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addMonths(6),
        'emergency_contact_name' => 'Wati',
        'emergency_contact_phone' => '087799881122',
        'emergency_contact_relation' => 'Istri',
        'status' => 'standby',
    ]);

    // Update status to on_duty
    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-driver.update-status', $driver->id), [
            'status' => 'on_duty',
        ])
        ->assertSessionHas('success');

    expect($driver->fresh()->status)->toBe('on_duty');
    expect($driver->fresh()->statusLabel())->toBe('Sedang Bertugas di Jalan');

    // Update status to on_leave
    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-driver.update-status', $driver->id), [
            'status' => 'on_leave',
        ]);

    expect($driver->fresh()->status)->toBe('on_leave');
    expect($driver->fresh()->statusLabel())->toBe('Sedang Libur / Cuti');
});

test('admin utama can view driver details with vehicle assignment history', function () {
    $user = User::create([
        'name' => 'Cecep Solihin',
        'email' => 'cecep.driver@travel.test',
        'phone' => '089812345678',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    $driver = Driver::create([
        'user_id' => $user->id,
        'phone' => '089812345678',
        'license_number' => 'SIM-5566-7788-99',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addDays(20), // expiring soon
        'emergency_contact_name' => 'Enok Rohaeti',
        'emergency_contact_phone' => '089876543210',
        'emergency_contact_relation' => 'Istri',
        'status' => 'on_duty',
    ]);

    // Buat unit minibus & jadwal penugasan
    $vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'D 1234 ABC'],
        [
            'name' => 'Toyota HiAce Premio',
            'type' => 'Minibus Eksekutif',
            'capacity' => 12,
            'status' => 'Available',
        ]
    );

    Schedule::create([
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => now()->format('Y-m-d'),
        'departure_time' => '08:00:00',
        'available_seats' => 4,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-driver.show', $driver->id));

    $response->assertOk()
        ->assertSee('Cecep Solihin')
        ->assertSee('SIM-5566-7788-99')
        ->assertSee('Toyota HiAce Premio')
        ->assertSee('D 1234 ABC');

    expect($driver->totalTripsCount())->toBe(1);
    expect($driver->completedTripsCount())->toBe(1);
    expect($driver->licenseStatus()['status'])->toBe('expiring_soon');
});
