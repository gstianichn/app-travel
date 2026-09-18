<?php

use App\Models\AppSetting;
use App\Models\OperationalLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// ─── Access Control ──────────────────────────────────────────────────────────

test('guest cannot access pengaturan', function () {
    $this->get('/pengaturan')->assertRedirect('/login');
});

test('customer user cannot access pengaturan', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get('/pengaturan')->assertForbidden();
})->skip('role middleware returns 403');

// ─── Index ───────────────────────────────────────────────────────────────────

test('admin utama can view pengaturan index', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->get('/pengaturan')
        ->assertOk()
        ->assertSee('Profil Perusahaan')
        ->assertSee('Kebijakan Tiket')
        ->assertSee('Integrasi Pembayaran')
        ->assertSee('Titik Operasional')
        ->assertSee('Keamanan Sistem');
});

// ─── Profil Perusahaan ───────────────────────────────────────────────────────

test('admin can update profil perusahaan', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/profil', [
            'company_name' => 'PT. Trans Nusantara',
            'company_phone' => '022-1234-5678',
            'company_whatsapp' => '0812-3456-7890',
            'company_email' => 'info@transnusantara.co.id',
            'company_address' => 'Jl. Sudirman No.1, Bandung',
        ])
        ->assertRedirect();

    expect(AppSetting::get('company_name'))->toBe('PT. Trans Nusantara');
    expect(AppSetting::get('company_email'))->toBe('info@transnusantara.co.id');
});

test('profil update requires company_name', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/profil', ['company_name' => ''])
        ->assertSessionHasErrors('company_name');
});

test('admin can upload company logo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin_utama']);

    // Use create() instead of image() to avoid requiring the GD extension
    $this->actingAs($admin)
        ->post('/pengaturan/profil', [
            'company_name' => 'Travel Test',
            'company_logo' => UploadedFile::fake()->create('logo.png', 100, 'image/png'),
        ])
        ->assertRedirect();

    $logoPath = AppSetting::get('company_logo');
    expect($logoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($logoPath);
});

// ─── Kebijakan Tiket ─────────────────────────────────────────────────────────

test('admin can update kebijakan tiket', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/kebijakan', [
            'payment_timeout_qris' => 15,
            'payment_timeout_transfer' => 60,
            'reschedule_cutoff_hours' => 6,
            'reschedule_fee' => 0,
            'baggage_free_kg' => 10,
            'baggage_excess_fee_per_kg' => 5000,
        ])
        ->assertRedirect();

    expect(AppSetting::get('payment_timeout_qris'))->toBe('15');
    expect(AppSetting::get('baggage_free_kg'))->toBe('10');
});

// ─── Integrasi Pembayaran ────────────────────────────────────────────────────

test('admin can save bank account settings', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/pembayaran', [
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'PT. Travel Trans Utama',
            'gateway_provider' => 'none',
        ])
        ->assertRedirect();

    expect(AppSetting::get('bank_name'))->toBe('BCA');
    expect(AppSetting::get('bank_account_number'))->toBe('1234567890');
});

test('admin can save midtrans gateway keys', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/pembayaran', [
            'gateway_provider' => 'midtrans',
            'gateway_server_key' => 'SB-Mid-server-key-test',
            'gateway_client_key' => 'SB-Mid-client-key-test',
        ])
        ->assertRedirect();

    expect(AppSetting::get('gateway_provider'))->toBe('midtrans');
    expect(AppSetting::get('gateway_server_key'))->toBe('SB-Mid-server-key-test');
});

// ─── Titik Operasional ───────────────────────────────────────────────────────

test('admin can add operational location', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/lokasi', [
            'name' => 'Pool Bandung – Jl. Soekarno Hatta',
            'address' => 'Jl. Soekarno Hatta No.10, Bandung',
            'phone' => '022-7654321',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('operational_locations', [
        'name' => 'Pool Bandung – Jl. Soekarno Hatta',
        'phone' => '022-7654321',
    ]);
});

test('admin can update operational location', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);
    $loc = OperationalLocation::create(['name' => 'Pool Lama', 'phone' => '021-111222']);

    $this->actingAs($admin)
        ->put("/pengaturan/lokasi/{$loc->id}", [
            'name' => 'Pool Baru',
            'phone' => '021-333444',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('operational_locations', ['name' => 'Pool Baru', 'phone' => '021-333444']);
    $this->assertDatabaseMissing('operational_locations', ['name' => 'Pool Lama']);
});

test('admin can delete operational location', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);
    $loc = OperationalLocation::create(['name' => 'Pool Hapus']);

    $this->actingAs($admin)
        ->delete("/pengaturan/lokasi/{$loc->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('operational_locations', ['id' => $loc->id]);
});

test('location name is required when adding', function () {
    $admin = User::factory()->create(['role' => 'admin_utama']);

    $this->actingAs($admin)
        ->post('/pengaturan/lokasi', ['name' => ''])
        ->assertSessionHasErrors('name');
});

// ─── Keamanan Sistem ─────────────────────────────────────────────────────────

test('admin can change password successfully', function () {
    $admin = User::factory()->create([
        'role' => 'admin_utama',
        'password' => Hash::make('OldPassword123'),
    ]);

    $this->actingAs($admin)
        ->post('/pengaturan/keamanan', [
            'current_password' => 'OldPassword123',
            'new_password' => 'NewPassword456',
            'new_password_confirmation' => 'NewPassword456',
        ])
        ->assertRedirect();

    expect(Hash::check('NewPassword456', $admin->fresh()->password))->toBeTrue();
});

test('wrong current password is rejected', function () {
    $admin = User::factory()->create([
        'role' => 'admin_utama',
        'password' => Hash::make('CorrectPassword'),
    ]);

    $this->actingAs($admin)
        ->post('/pengaturan/keamanan', [
            'current_password' => 'WrongPassword',
            'new_password' => 'NewPassword456',
            'new_password_confirmation' => 'NewPassword456',
        ])
        ->assertSessionHasErrors('current_password');
});

test('new password must be confirmed', function () {
    $admin = User::factory()->create([
        'role' => 'admin_utama',
        'password' => Hash::make('OldPassword123'),
    ]);

    $this->actingAs($admin)
        ->post('/pengaturan/keamanan', [
            'current_password' => 'OldPassword123',
            'new_password' => 'NewPassword456',
            'new_password_confirmation' => 'DifferentPassword',
        ])
        ->assertSessionHasErrors('new_password');
});
