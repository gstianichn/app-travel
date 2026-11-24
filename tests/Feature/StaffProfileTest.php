<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('staff can view their profile page and topbar links to it', function () {
    $staff = User::factory()->create([
        'name' => 'Rian Hidayat (Staff Semanggi)',
        'email' => 'rian.staff@travel.test',
        'phone' => '081234567890',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Loket Jakarta (Pusat)',
        'position' => 'Staff Kasir Loket',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($staff)->get(route('dashboard.admin-biasa.profil'));

    $response->assertOk();
    $response->assertSee('Profil Saya');
    $response->assertSee('Rian Hidayat (Staff Semanggi)');
    $response->assertSee('Loket Jakarta (Pusat)');
    $response->assertSee('Staff Kasir Loket');
    $response->assertSee('Hak Akses Operasional');
    $response->assertSee('Ubah Password');
    $response->assertSee(route('dashboard.admin-biasa.profil'), false);
});

test('staff can update their profile information', function () {
    $staff = User::factory()->create([
        'name' => 'Rian Lama',
        'email' => 'rian.lama@travel.test',
        'phone' => '081200000001',
        'role' => 'staff',
        'status' => 'active',
        'branch' => 'Loket Jakarta (Pusat)',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($staff)->post(route('dashboard.admin-biasa.profil.update'), [
        'name' => 'Rian Hidayat Baru',
        'email' => 'rian.baru@travel.test',
        'phone' => '081299998888',
    ]);

    $response->assertRedirect(route('dashboard.admin-biasa.profil'));
    $response->assertSessionHas('success');

    $staff->refresh();
    expect($staff->name)->toBe('Rian Hidayat Baru');
    expect($staff->email)->toBe('rian.baru@travel.test');
    expect($staff->phone)->toBe('081299998888');
    expect($staff->branch)->toBe('Loket Jakarta (Pusat)');
});

test('staff can update their password with valid current password', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($staff)->post(route('dashboard.admin-biasa.profil.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword456',
        'password_confirmation' => 'newpassword456',
    ]);

    $response->assertRedirect(route('dashboard.admin-biasa.profil'));
    $response->assertSessionHas('success');

    $staff->refresh();
    expect(Hash::check('newpassword456', $staff->password))->toBeTrue();
});

test('staff password update fails when current password is wrong', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($staff)
        ->from(route('dashboard.admin-biasa.profil'))
        ->post(route('dashboard.admin-biasa.profil.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

    $response->assertRedirect(route('dashboard.admin-biasa.profil'));
    $response->assertSessionHasErrors('current_password');

    $staff->refresh();
    expect(Hash::check('password123', $staff->password))->toBeTrue();
});

test('regular user cannot access staff profile page', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.admin-biasa.profil'))
        ->assertForbidden();
});
