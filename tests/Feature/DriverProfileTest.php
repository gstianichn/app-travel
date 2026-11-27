<?php

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('driver topbar dropdown links to Profil Saya and driver can view profile page', function () {
    $driverUser = User::factory()->create([
        'name' => 'Pak Yayan Supriatna',
        'email' => 'yayan.driver@travel.test',
        'phone' => '081234567890',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    Driver::create([
        'user_id' => $driverUser->id,
        'phone' => '081234567890',
        'license_number' => 'SIM-B1-0099',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $response = $this->actingAs($driverUser)->get(route('dashboard.driver.profil'));

    $response->assertOk();
    $response->assertSee('Profil Saya');
    $response->assertSee('Pak Yayan Supriatna');
    $response->assertSee('SIM-B1-0099');
    $response->assertSee('driverProfileDropdown', false);
    $response->assertSee(route('dashboard.driver.profil'), false);
});

test('driver can update profile information and password', function () {
    $driverUser = User::factory()->create([
        'name' => 'Yayan Lama',
        'email' => 'yayan.lama@travel.test',
        'phone' => '081200000001',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password123'),
    ]);

    $driver = Driver::create([
        'user_id' => $driverUser->id,
        'phone' => '081200000001',
        'license_number' => 'SIM-B1-0100',
        'license_expired_at' => now()->addYear(),
        'status' => 'standby',
    ]);

    $update = $this->actingAs($driverUser)->post(route('dashboard.driver.profil.update'), [
        'name' => 'Pak Yayan Baru',
        'email' => 'yayan.baru@travel.test',
        'phone' => '081299998888',
        'address' => 'Jl. Pasteur No. 12, Bandung',
        'emergency_contact_name' => 'Ibu Yayan',
        'emergency_contact_phone' => '081211112222',
    ]);

    $update->assertRedirect(route('dashboard.driver.profil'));
    $update->assertSessionHas('success');

    $driverUser->refresh();
    $driver->refresh();
    expect($driverUser->name)->toBe('Pak Yayan Baru')
        ->and($driverUser->email)->toBe('yayan.baru@travel.test')
        ->and($driverUser->phone)->toBe('081299998888')
        ->and($driver->phone)->toBe('081299998888')
        ->and($driver->address)->toBe('Jl. Pasteur No. 12, Bandung')
        ->and($driver->emergency_contact_name)->toBe('Ibu Yayan')
        ->and($driver->emergency_contact_phone)->toBe('081211112222');

    $badPw = $this->actingAs($driverUser)
        ->from(route('dashboard.driver.profil'))
        ->post(route('dashboard.driver.profil.password'), [
            'current_password' => 'salah',
            'password' => 'passwordbaru999',
            'password_confirmation' => 'passwordbaru999',
        ]);
    $badPw->assertSessionHasErrors('current_password');

    $goodPw = $this->actingAs($driverUser)->post(route('dashboard.driver.profil.password'), [
        'current_password' => 'password123',
        'password' => 'passwordbaru999',
        'password_confirmation' => 'passwordbaru999',
    ]);
    $goodPw->assertRedirect(route('dashboard.driver.profil'));
    $goodPw->assertSessionHas('success');

    expect(Hash::check('passwordbaru999', $driverUser->fresh()->password))->toBeTrue();
});

test('non-driver role cannot access driver profile page', function () {
    $user = User::factory()->create(['role' => 'user', 'status' => 'active']);

    $this->actingAs($user)
        ->get(route('dashboard.driver.profil'))
        ->assertForbidden();
});
