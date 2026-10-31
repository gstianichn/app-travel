<?php

use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
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

    $this->vehiclePayload = function (array $overrides = []): array {
        return array_merge([
            'plate_number' => 'B 1001 VHC',
            'name' => 'Toyota HiAce Premio',
            'type' => 'Minibus Eksekutif',
            'capacity' => 10,
            'manufacture_year' => 2021,
            'chassis_number' => 'MHFXW42G0M1234567',
            'engine_number' => '2KDFTV123456',
            'stnk_tax_due_date' => now()->addMonths(6)->format('Y-m-d'),
            'kir_expiry_date' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'siap_jalan',
            'facilities' => ['AC', 'WiFi'],
            'notes' => 'Unit armada uji.',
        ], $overrides);
    };

    $this->makeVehicle = function (array $overrides = []): Vehicle {
        return Vehicle::create(array_merge([
            'plate_number' => 'B 2002 VHC',
            'name' => 'Toyota HiAce Commuter',
            'type' => 'Minibus Eksekutif',
            'capacity' => 10,
            'status' => 'siap_jalan',
        ], $overrides));
    };
});

test('daftar kendaraan menampilkan peringatan KIR/STNK hampir habis', function () {
    ($this->makeVehicle)([
        'plate_number' => 'B 3003 KIR',
        'kir_expiry_date' => now()->addDays(5)->format('Y-m-d'),
        'stnk_tax_due_date' => now()->addDays(10)->format('Y-m-d'),
    ]);

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.memantau-kendaraan.index'))
        ->assertOk()
        ->assertSee('Perhatian Dokumen Kelaikan')
        ->assertSee('KIR/STNK Hampir Habis', false);
});

test('tambah kendaraan membuat data dan kursi otomatis', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)());

    $vehicle = Vehicle::where('plate_number', 'B 1001 VHC')->first();

    $response->assertRedirect(route('dashboard.admin.memantau-kendaraan.show', $vehicle->id));
    expect($vehicle)->not->toBeNull();
    expect($vehicle->seats()->count())->toBe($vehicle->capacity);
});

test('jumlah kursi mengikuti kapasitas kendaraan', function () {
    foreach ([['B 1234 C04', 4], ['B 5678 C20', 20]] as [$plate, $capacity]) {
        $this->actingAs($this->admin)
            ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)([
                'plate_number' => $plate,
                'capacity' => $capacity,
            ]))
            ->assertRedirect();

        $vehicle = Vehicle::where('plate_number', $plate)->first();

        expect($vehicle->seats()->count())->toBe($capacity);
        expect($vehicle->seats()->pluck('seat_number')->unique()->count())->toBe($capacity);
    }
});

test('validasi kendaraan menolak plat duplikat, kapasitas di luar 4-60, dan tahun 1989', function () {
    ($this->makeVehicle)(['plate_number' => 'B 4004 DUP']);

    $duplicate = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)([
            'plate_number' => 'B 4004 DUP',
        ]));

    $duplicate->assertSessionHasErrors('plate_number');

    $tooSmall = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)([
            'plate_number' => 'B 5005 SMALL',
            'capacity' => 3,
        ]));

    $tooSmall->assertSessionHasErrors('capacity');

    $tooBig = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)([
            'plate_number' => 'B 6006 BIG',
            'capacity' => 61,
        ]));

    $tooBig->assertSessionHasErrors('capacity');

    $oldYear = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.memantau-kendaraan.store'), ($this->vehiclePayload)([
            'plate_number' => 'B 7007 OLD',
            'manufacture_year' => 1989,
        ]));

    $oldYear->assertSessionHasErrors('manufacture_year');

    expect(Vehicle::whereIn('plate_number', ['B 5005 SMALL', 'B 6006 BIG', 'B 7007 OLD'])->count())->toBe(0);
});

test('edit kendaraan menyimpan fasilitas dan foto', function () {
    $vehicle = ($this->makeVehicle)();

    $photo = UploadedFile::fake()->createWithContent('vehicle.png', $this->png);

    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.memantau-kendaraan.update', $vehicle->id), ($this->vehiclePayload)([
            'plate_number' => $vehicle->plate_number,
            'facilities' => ['AC', 'WiFi', 'USB Charger'],
            'photo' => $photo,
        ]));

    $response->assertRedirect(route('dashboard.admin.memantau-kendaraan.show', $vehicle->id));

    $vehicle->refresh();

    expect($vehicle->facilities)->toBe(['AC', 'WiFi', 'USB Charger']);
    expect($vehicle->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($vehicle->photo);
});

test('ubah status siap_jalan ke maintenance menghilangkan jadwal dari pencarian', function () {
    $origin = Location::create([
        'name' => 'Pool Jakarta GST',
        'code' => 'JKT-GST',
        'city' => 'Jakarta',
        'address' => 'Jl. Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $destination = Location::create([
        'name' => 'Pool Bandung GST',
        'code' => 'BDG-GST',
        'city' => 'Bandung',
        'address' => 'Jl. Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $route = TravelRoute::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 150000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $vehicle = ($this->makeVehicle)([
        'plate_number' => 'B 8008 GST',
        'name' => 'Armada GST',
        'type' => 'Minibus GST-08',
    ]);

    $departureDate = now()->addDays(2)->format('Y-m-d');

    Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => $departureDate,
        'departure_time' => '08:00',
        'available_seats' => 10,
        'price' => 150000,
        'status' => 'Scheduled',
    ]);

    $searchUrl = route('search', [
        'origin' => $origin->id,
        'destination' => $destination->id,
        'departure_date' => $departureDate,
    ]);

    $this->get($searchUrl)
        ->assertOk()
        ->assertSee('Minibus GST-08');

    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.memantau-kendaraan.update-status', $vehicle->id), [
            'status' => 'maintenance',
        ])
        ->assertSessionHas('success');

    expect($vehicle->fresh()->status)->toBe('maintenance');

    $this->get($searchUrl)
        ->assertOk()
        ->assertDontSee('Minibus GST-08');
});

test('hapus kendaraan dengan jadwal mendatang ditolak dengan pesan', function () {
    $vehicle = ($this->makeVehicle)(['plate_number' => 'B 9009 FUT']);

    Schedule::create([
        'vehicle_id' => $vehicle->id,
        'departure_date' => now()->addDays(3)->format('Y-m-d'),
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.memantau-kendaraan.destroy', $vehicle->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Vehicle::find($vehicle->id))->not->toBeNull();
});

test('hapus kendaraan yang hanya punya jadwal lampau ditolak dengan pesan rapi', function () {
    $vehicle = ($this->makeVehicle)(['plate_number' => 'B 1111 PAST']);

    Schedule::create([
        'vehicle_id' => $vehicle->id,
        'departure_date' => now()->subDays(10)->format('Y-m-d'),
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.memantau-kendaraan.destroy', $vehicle->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Vehicle::find($vehicle->id))->not->toBeNull();
});

test('hapus kendaraan tanpa jadwal menghapus data beserta kursinya', function () {
    $vehicle = ($this->makeVehicle)(['plate_number' => 'B 2222 IDLE']);

    $vehicle->ensureSeatsExist();
    expect($vehicle->seats()->count())->toBeGreaterThan(0);

    $response = $this->actingAs($this->admin)
        ->delete(route('dashboard.admin.memantau-kendaraan.destroy', $vehicle->id));

    $response->assertRedirect(route('dashboard.admin.memantau-kendaraan.index'));
    $response->assertSessionHas('success');

    expect(Vehicle::find($vehicle->id))->toBeNull();
    expect(Seat::where('vehicle_id', $vehicle->id)->count())->toBe(0);
});
