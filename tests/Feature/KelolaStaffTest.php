<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Pastikan user admin utama ada
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.test@travel.test'],
        [
            'name' => 'Admin Utama Travel',
            'phone' => '08123456789',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('admin utama can view kelola staff index page', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-staff.index'))
        ->assertOk()
        ->assertSee('Kelola Staf')
        ->assertSee('Total Staf Operasional');
});

test('admin utama can view create staff page', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-staff.create'))
        ->assertOk()
        ->assertSee('Tambah Staf Baru')
        ->assertSee('Penempatan Cabang / Loket')
        ->assertSee('Staf Loket Standar');
});

test('admin utama can store new staff with branch and permissions', function () {
    $staffData = [
        'name' => 'Doni Operator Loket',
        'email' => 'doni.loket@travel.test',
        'phone' => '081299887766',
        'branch' => 'Agen Bandung (Dipatiukur)',
        'position' => 'Staf Loket & Kasir',
        'access_code' => 'STF-BDG-01',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'permissions' => ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket'],
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('dashboard.admin.kelola-staff.store'), $staffData);

    $response->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'doni.loket@travel.test',
        'branch' => 'Agen Bandung (Dipatiukur)',
        'role' => 'staff',
        'status' => 'active',
    ]);

    $staff = User::where('email', 'doni.loket@travel.test')->first();
    expect($staff->hasPermission('create_booking'))->toBeTrue();
    expect($staff->hasPermission('approve_refund'))->toBeFalse();
    expect($staff->hasPermission('delete_data'))->toBeFalse();
});

test('admin utama can toggle staff status between active and inactive', function () {
    $staff = User::create([
        'name' => 'Rina Resigned',
        'email' => 'rina.resigned@travel.test',
        'phone' => '089988776655',
        'branch' => 'Pool Jakarta (Pusat)',
        'position' => 'Staf Loket',
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    // Deactivate staff (berhenti bekerja)
    $response = $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-staff.toggle-status', $staff->id));

    $response->assertSessionHas('success');
    expect($staff->fresh()->status)->toBe('inactive');

    // Reactivate staff
    $this->actingAs($this->admin)
        ->patch(route('dashboard.admin.kelola-staff.toggle-status', $staff->id));

    expect($staff->fresh()->status)->toBe('active');
});

test('admin utama can view staff details and permission matrix', function () {
    $staff = User::create([
        'name' => 'Hendra Detail Test',
        'email' => 'hendra.test@travel.test',
        'phone' => '081233445566',
        'branch' => 'Loket Gambir Jakarta',
        'position' => 'Petugas Boarding',
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
        'permissions' => ['create_booking', 'view_routes'],
    ]);

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-staff.show', $staff->id))
        ->assertOk()
        ->assertSee('Hendra Detail Test')
        ->assertSee('Matriks Hak Akses');
});
