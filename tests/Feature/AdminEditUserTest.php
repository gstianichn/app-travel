<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $this->target = User::factory()->create([
        'name' => 'Nama Awal',
        'email' => 'nama@travel.test',
        'role' => 'user',
        'status' => 'active',
        'phone' => '081200000000',
        'password' => Hash::make('password123'),
    ]);
});

test('admin utama membuka form edit user', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard.admin.kelola-user.edit', $this->target->id))
        ->assertOk()
        ->assertSee($this->target->name)
        ->assertSee($this->target->email)
        ->assertSee('name="phone"', false)
        ->assertSee('name="status"', false);
});

test('status wajib diisi saat memperbarui user', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-user.update', $this->target->id), [
            'name' => 'Nama Baru',
            'email' => $this->target->email,
            'role' => 'user',
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response->assertSessionHasErrors([
        'status' => 'Status user wajib dipilih.',
    ]);

    expect($this->target->fresh()->name)->toBe('Nama Awal');
});

test('admin utama mengganti nama dan nomor HP user', function () {
    $response = $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-user.update', $this->target->id), [
            'name' => 'Nama Baru',
            'email' => $this->target->email,
            'phone' => '081234567890',
            'status' => 'active',
            'role' => 'user',
        ]);

    $response->assertRedirect(route('dashboard.admin.kelola-user.index'));
    $response->assertSessionHas('success', 'Data user berhasil diperbarui.');

    $this->target->refresh();

    expect($this->target->name)->toBe('Nama Baru');
    expect($this->target->phone)->toBe('081234567890');
});

test('admin utama dapat mengubah role user', function () {
    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-user.update', $this->target->id), [
            'name' => $this->target->name,
            'email' => $this->target->email,
            'phone' => $this->target->phone,
            'status' => 'active',
            'role' => 'staff',
        ])
        ->assertRedirect(route('dashboard.admin.kelola-user.index'));

    expect($this->target->fresh()->role)->toBe('staff');
});

test('admin utama tidak bisa mengubah akun selain role user', function () {
    $staff = User::factory()->create([
        'name' => 'Petugas',
        'email' => 'petugas@travel.test',
        'role' => 'staff',
        'status' => 'active',
    ]);

    $this->actingAs($this->admin)
        ->put(route('dashboard.admin.kelola-user.update', $staff->id), [
            'name' => 'Diubah',
            'email' => $staff->email,
            'phone' => '081211111111',
            'status' => 'active',
        ])
        ->assertNotFound();

    expect($staff->fresh()->name)->toBe('Petugas');
});
