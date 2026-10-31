<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->staff = User::factory()->create([
        'name' => 'Staf Loket',
        'email' => 'staf@travel.test',
        'phone' => '081200000001',
        'branch' => 'Pool Jakarta (Pusat)',
        'position' => 'Staf Loket & Kasir',
        'role' => 'staff',
        'status' => 'active',
        'access_code' => 'STF-BDG-01',
        'password' => Hash::make('password123'),
        'permissions' => [
            'create_booking',
            'view_routes',
            'view_passengers',
            'checkin_ticket',
        ],
    ]);
});

test('admin utama mengubah cabang staff', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-staff.update', $this->staff->id), [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'phone' => $this->staff->phone,
            'branch' => 'Pool Bandung (Pasteur)',
            'position' => $this->staff->position,
            'status' => 'active',
            'permissions' => ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket'],
        ]);

    $response->assertRedirect(route('dashboard.admin.kelola-staff.index'));
    $response->assertSessionHas('success');

    expect($this->staff->fresh()->branch)->toBe('Pool Bandung (Pasteur)');
});

test('admin utama menghapus hak checkin_ticket', function () {
    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-staff.update', $this->staff->id), [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'phone' => $this->staff->phone,
            'branch' => $this->staff->branch,
            'position' => $this->staff->position,
            'status' => 'active',
            'permissions' => ['create_booking', 'view_routes', 'view_passengers'],
        ])
        ->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $this->staff->refresh();

    expect($this->staff->permissions)->not->toContain('checkin_ticket');
    expect($this->staff->hasPermission('checkin_ticket'))->toBeFalse();
    expect($this->staff->hasPermission('create_booking'))->toBeTrue();
});

test('admin utama mengubah cabang sekaligus menghapus hak checkin_ticket', function () {
    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-staff.update', $this->staff->id), [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'phone' => $this->staff->phone,
            'branch' => 'Agen Bandung (Dipatiukur)',
            'position' => $this->staff->position,
            'status' => 'active',
            'permissions' => ['create_booking', 'view_routes'],
        ])
        ->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $this->staff->refresh();

    expect($this->staff->branch)->toBe('Agen Bandung (Dipatiukur)');
    expect($this->staff->hasPermission('checkin_ticket'))->toBeFalse();
});

test('form edit staff menyimpan cabang dan hak akses tanpa merusak access_code', function () {
    $storedAccessCode = $this->staff->access_code;

    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-staff.edit', $this->staff->id))
        ->assertOk()
        ->assertDontSee($storedAccessCode, false);

    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-staff.update', $this->staff->id), [
            'name' => $this->staff->name,
            'email' => $this->staff->email,
            'phone' => $this->staff->phone,
            'branch' => 'Pool Cianjur',
            'position' => $this->staff->position,
            'access_code' => '',
            'status' => 'active',
            'permissions' => ['create_booking', 'view_routes'],
        ])
        ->assertRedirect(route('dashboard.admin.kelola-staff.index'));

    $this->staff->refresh();

    expect($this->staff->branch)->toBe('Pool Cianjur');
    expect($this->staff->hasPermission('checkin_ticket'))->toBeFalse();
    expect(Hash::check('STF-BDG-01', $this->staff->access_code))->toBeTrue();
});

test('admin utama tidak bisa mengedit akun non-staff', function () {
    $customer = User::factory()->create([
        'name' => 'Pelanggan',
        'email' => 'pelanggan@travel.test',
        'role' => 'user',
        'status' => 'active',
    ]);

    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-staff.update', $customer->id), [
            'name' => 'Diubah',
            'email' => $customer->email,
            'phone' => '081211111111',
            'branch' => 'Pool Cianjur',
            'status' => 'active',
        ])
        ->assertNotFound();

    expect($customer->fresh()->name)->toBe('Pelanggan');
});
