<?php

use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Pastikan user admin utama ada
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.vehicletest@travel.test'],
        [
            'name' => 'Admin Utama Vehicle Test',
            'phone' => '08123456788',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('admin utama can view memantau kendaraan index page and counters', function () {
    $vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 1111 TST'],
        [
            'name' => 'HiAce Test Unit',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addMonths(6),
            'kir_expiry_date' => now()->addMonths(6),
        ]
    );

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.memantau-kendaraan.index'))
        ->assertOk()
        ->assertSee('Memantau Kendaraan')
        ->assertSee('Total Unit Armada')
        ->assertSee('Siap Jalan')
        ->assertSee('B 1111 TST')
        ->assertSee('Toyota HiAce Premio');
});

test('admin utama can search and filter vehicles by status and type', function () {
    Vehicle::firstOrCreate(
        ['plate_number' => 'D 9999 ELF'],
        [
            'name' => 'Elf Pariwisata Long',
            'type' => 'Isuzu Elf Long',
            'capacity' => 19,
            'status' => 'maintenance',
            'stnk_tax_due_date' => now()->subDays(5),
            'kir_expiry_date' => now()->addDays(10),
        ]
    );

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.memantau-kendaraan.index', [
            'search' => 'ELF',
            'status' => 'maintenance',
            'type' => 'Isuzu Elf Long',
        ]))
        ->assertOk()
        ->assertSee('D 9999 ELF')
        ->assertSee('Isuzu Elf Long');
});

test('admin utama can view create vehicle page', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.memantau-kendaraan.create'))
        ->assertOk()
        ->assertSee('Tambah Unit Armada')
        ->assertSee('Nomor Polisi (Pelat Nomor)')
        ->assertSee('Tanggal Jatuh Tempo Pajak STNK')
        ->assertSee('Masa Berlaku Uji KIR');
});

test('admin utama can store a new vehicle with legality dates and facilities', function () {
    $vehicleData = [
        'plate_number' => 'B 2345 ABC',
        'name' => 'HiAce Premio Luxury Test',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'manufacture_year' => 2024,
        'chassis_number' => 'MHF21TEST12345',
        'engine_number' => '1GD-TEST9876',
        'stnk_tax_due_date' => now()->addMonths(8)->format('Y-m-d'),
        'kir_expiry_date' => now()->addMonths(5)->format('Y-m-d'),
        'status' => 'siap_jalan',
        'facilities' => ['AC Dingin', 'Port USB Charger', 'Wi-Fi Cepat'],
        'notes' => 'Unit baru tiba dari karoseri dalam kondisi prima.',
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), $vehicleData);

    $createdVehicle = Vehicle::where('plate_number', 'B 2345 ABC')->first();
    expect($createdVehicle)->not->toBeNull();

    $response->assertRedirect(route('dashboard.admin.memantau-kendaraan.show', $createdVehicle->id));
    $response->assertSessionHas('success');

    expect($createdVehicle->name)->toBe('HiAce Premio Luxury Test');
    expect($createdVehicle->capacity)->toBe(10);
    expect($createdVehicle->status)->toBe('siap_jalan');
    expect($createdVehicle->facilities)->toContain('AC Dingin');
});

test('admin utama can view vehicle show page with legality evaluation', function () {
    $vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 7777 SHW'],
        [
            'name' => 'Unit Show Test',
            'type' => 'Toyota HiAce Commuter',
            'capacity' => 14,
            'status' => 'siap_jalan',
            'stnk_tax_due_date' => now()->addDays(15), // expiring soon
            'kir_expiry_date' => now()->subDays(2), // expired
            'facilities' => ['AC Dingin', 'Audio & Musik'],
        ]
    );

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.memantau-kendaraan.show', $vehicle->id))
        ->assertOk()
        ->assertSee('B 7777 SHW')
        ->assertSee('Unit Show Test')
        ->assertSee('14 Kursi Penumpang')
        ->assertSee('Masa Berlaku KIR')
        ->assertSee('Pajak STNK');
});

test('admin utama can update vehicle data', function () {
    $vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 8888 UPD'],
        [
            'name' => 'Old Name Vehicle',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ]
    );

    $updateData = [
        'plate_number' => 'B 8888 UPD',
        'name' => 'Updated Name Vehicle',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 12,
        'manufacture_year' => 2023,
        'status' => 'maintenance',
        'stnk_tax_due_date' => now()->addMonths(4)->format('Y-m-d'),
        'kir_expiry_date' => now()->addMonths(4)->format('Y-m-d'),
        'notes' => 'Masuk bengkel servis rutin.',
    ];

    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.memantau-kendaraan.update', $vehicle->id), $updateData);

    $response->assertRedirect(route('dashboard.admin.memantau-kendaraan.show', $vehicle->id));
    $response->assertSessionHas('success');

    $vehicle->refresh();
    expect($vehicle->name)->toBe('Updated Name Vehicle');
    expect($vehicle->capacity)->toBe(12);
    expect($vehicle->status)->toBe('maintenance');
});

test('admin utama can quick update vehicle operational status', function () {
    $vehicle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 3333 QCK'],
        [
            'name' => 'Quick Status Vehicle',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ]
    );

    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.memantau-kendaraan.update-status', $vehicle->id), [
            'status' => 'beroperasi',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $vehicle->refresh();
    expect($vehicle->status)->toBe('beroperasi');
    expect($vehicle->statusLabel())->toBe('Sedang Beroperasi');
});

test('admin utama cannot delete vehicle with active upcoming schedules but can delete idle vehicle', function () {
    $vehicleActive = Vehicle::firstOrCreate(
        ['plate_number' => 'B 4444 ACT'],
        [
            'name' => 'Active Vehicle',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ]
    );

    // Buat jadwal mendatang aktif
    Schedule::create([
        'vehicle_id' => $vehicleActive->id,
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    // Percobaan hapus unit yang memiliki jadwal aktif
    $responseActive = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.memantau-kendaraan.destroy', $vehicleActive->id));

    $responseActive->assertRedirect();
    $responseActive->assertSessionHas('error');
    expect(Vehicle::find($vehicleActive->id))->not->toBeNull();

    // Hapus unit tanpa jadwal (idle)
    $vehicleIdle = Vehicle::firstOrCreate(
        ['plate_number' => 'B 5555 IDL'],
        [
            'name' => 'Idle Vehicle',
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ]
    );

    $responseIdle = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.memantau-kendaraan.destroy', $vehicleIdle->id));

    $responseIdle->assertRedirect(route('dashboard.admin.memantau-kendaraan.index'));
    $responseIdle->assertSessionHas('success');
    expect(Vehicle::find($vehicleIdle->id))->toBeNull();
});
