<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'user',
        'password' => Hash::make('password'),
    ]);
});

test('menambah user baru', function () {
    $response = $this->post(route('user.register'), [
        'name' => 'Budi Baru',
        'email' => 'baru@test.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $response->assertRedirect();
    expect(User::where('email', 'baru@test.com')->exists())->toBeTrue();
});

test('penambahan user dengan email duplikat', function () {
    $user = User::factory()->create(['email' => 'duplikat@test.com']);

    $response = $this->post(route('user.register'), [
        'name' => 'Budi Duplikat',
        'email' => 'duplikat@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'Email sudah terdaftar.']);
    expect(User::where('email', 'duplikat@test.com')->exists())->toBeTrue();
});

test('detail user | profil', function () {
    $this->actingAs($this->user)->get(route('dashboard.user.profil'))
        ->assertOk()
        ->assertSee('Profil Saya');
});

test('detail petugas lewat URL user | 404', function () {
    $other = User::factory()->create(['role' => 'admin_utama']);
    $this->actingAs($this->user)->get(route('detail.petugas', $other->id))->assertNotFound();
});

test('edit user | ubah nama/HP', function () {
    $this->actingAs($this->user)->put(route('user.profil.update'), [
        'name' => 'Budi Ubah',
        'phone' => '081234567899',
    ])->assertRedirect();

    $this->user->refresh();
    expect($this->user->name)->toBe('Budi Ubah');
    expect($this->user->phone)->toBe('081234567899');
});