<?php

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Route;
use App\Models\SavedPassenger;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserVoucher;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi@travel.test',
        'phone' => '081234567890',
        'password' => Hash::make('password123'),
        'role' => 'user',
    ]);
});

test('A1: user can view akun dashboard with initialized presets of saved passengers and vouchers', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-akun'));

    $response->assertOk();
    $response->assertSee('Daftar Penumpang Tersimpan');
    $response->assertSee('Dompet Voucher');
    $response->assertSee('Nomor WhatsApp CS belum diisi');

    // Presets should be created
    expect($this->user->savedPassengers()->count())->toBeGreaterThanOrEqual(1);
    expect($this->user->vouchers()->count())->toBeGreaterThanOrEqual(1);
});

test('A2: user can add a new saved passenger with 16 digit NIK and phone', function () {
    $response = $this->actingAs($this->user)->post(route('dashboard.user.saved-passenger.store'), [
        'name' => 'Ahmad Dahlan',
        'nik' => '3273012345678901',
        'phone' => '081399887766',
        'is_primary' => '1',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $passenger = $this->user->savedPassengers()->where('name', 'Ahmad Dahlan')->first();
    expect($passenger)->not->toBeNull();
    expect($passenger->nik)->toBe('3273012345678901');
    expect($passenger->phone)->toBe('081399887766');
    expect($passenger->is_primary)->toBeTrue();
    expect($passenger->maskedNik())->toContain('••••');
});

test('A3: user can update and delete a saved passenger', function () {
    $passenger = SavedPassenger::create([
        'user_id' => $this->user->id,
        'name' => 'Rina Nose',
        'nik' => '3273112233445566',
        'phone' => '08122334455',
        'is_primary' => false,
    ]);

    // Update
    $responseUpdate = $this->actingAs($this->user)->put(route('dashboard.user.saved-passenger.update', $passenger), [
        'name' => 'Rina Nose Update',
        'nik' => '3273112233445566',
        'phone' => '08122334455',
        'is_primary' => '1',
    ]);

    $responseUpdate->assertRedirect();
    $responseUpdate->assertSessionHas('success');
    expect($passenger->fresh()->name)->toBe('Rina Nose Update');
    expect($passenger->fresh()->is_primary)->toBeTrue();

    // Delete
    $responseDelete = $this->actingAs($this->user)->delete(route('dashboard.user.saved-passenger.destroy', $passenger));
    $responseDelete->assertRedirect();
    $responseDelete->assertSessionHas('success');
    expect(SavedPassenger::find($passenger->id))->toBeNull();
});

test('B1: user can view my vouchers wallet with status filter and tags', function () {
    // Seed vouchers with different statuses
    UserVoucher::create([
        'user_id' => $this->user->id,
        'code' => 'ACTIVEPROMO',
        'title' => 'Diskon Liburan 20%',
        'discount_type' => 'percent',
        'discount_value' => 20,
        'min_transaction' => 100000,
        'source' => 'claim_promo',
        'status' => 'active',
        'expires_at' => Carbon::now()->addMonth(),
    ]);

    UserVoucher::create([
        'user_id' => $this->user->id,
        'code' => 'USEDVOUCHER',
        'title' => 'Voucher Terpakai',
        'discount_type' => 'fixed',
        'discount_value' => 25000,
        'min_transaction' => 50000,
        'source' => 'welcome_gift',
        'status' => 'used',
        'used_at' => Carbon::now()->subDays(2),
    ]);

    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-promo', ['status' => 'active']));
    $response->assertOk();
    $response->assertSee('ACTIVEPROMO');
    $response->assertSee('Masih Berlaku');
});

test('B2: user can claim a promo or compensation voucher into my vouchers wallet', function () {
    $response = $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'SUPERDISCOUNT',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $voucher = $this->user->vouchers()->where('code', 'SUPERDISCOUNT')->first();
    expect($voucher)->not->toBeNull();
    expect($voucher->discount_value)->toBe('30.00');
    expect($voucher->isActive())->toBeTrue();
});

test('B3: voucher validation API rejects invalid route or under min order', function () {
    $locA = Location::create(['name' => 'Pool Dipatiukur Bandung', 'code' => 'BDG-01', 'city' => 'Bandung', 'address' => 'Jl Dipatiukur', 'is_active' => true]);
    $locB = Location::create(['name' => 'Semanggi Jakarta', 'code' => 'JKT-01', 'city' => 'Jakarta', 'address' => 'Jl Semanggi', 'is_active' => true]);
    $route = Route::create(['origin_id' => $locA->id, 'destination_id' => $locB->id, 'distance' => 150, 'duration' => 180, 'price' => 50000]);
    $vehicle = Vehicle::create(['name' => 'HiAce 01', 'type' => 'Toyota HiAce Premio', 'plate_number' => 'D 1234 ABC', 'capacity' => 10, 'status' => 'ready']);
    $schedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => Carbon::now()->addDay()->toDateString(),
        'departure_time' => '08:00:00',
        'price' => 50000,
        'available_seats' => 10,
        'status' => 'ready',
    ]);

    $booking = Booking::create([
        'user_id' => $this->user->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'TRV-PROMOTEST-01',
        'subtotal' => 50000, // Di bawah min transaksi Rp 100.000
        'discount' => 0,
        'total_price' => 50000,
        'total_passenger' => 1,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'expired_at' => Carbon::now()->addMinutes(10),
    ]);

    // Test dengan kode HEMATLIBURAN (min transaksi Rp 100.000)
    $response = $this->actingAs($this->user)->postJson(route('dashboard.user.booking.validate-promo'), [
        'booking_id' => $booking->id,
        'promo_code' => 'HEMATLIBURAN',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['valid' => false]);
    expect($response->json('message'))->toContain('Minimum transaksi');
});

test('C1: customer support WhatsApp URL includes ticket number and route context automatically', function () {
    AppSetting::set('company_whatsapp', '0812-1111-2222');

    $locA = Location::create(['name' => 'Pool Pasteur', 'code' => 'PST-01', 'city' => 'Bandung', 'address' => 'Jl Pasteur', 'is_active' => true]);
    $locB = Location::create(['name' => 'Gambir', 'code' => 'GMB-01', 'city' => 'Jakarta', 'address' => 'Stasiun Gambir', 'is_active' => true]);
    $route = Route::create(['origin_id' => $locA->id, 'destination_id' => $locB->id, 'distance' => 150, 'duration' => 180, 'price' => 120000]);
    $vehicle = Vehicle::create(['name' => 'HiAce 02', 'type' => 'Toyota HiAce Premio', 'plate_number' => 'B 9988 XYZ', 'capacity' => 10, 'status' => 'ready']);
    $schedule = Schedule::create([
        'route_id' => $route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => Carbon::now()->addDay()->toDateString(),
        'departure_time' => '10:00:00',
        'price' => 120000,
        'available_seats' => 9,
        'status' => 'ready',
    ]);

    $booking = Booking::create([
        'user_id' => $this->user->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BK-CS-889900',
        'subtotal' => 120000,
        'discount' => 0,
        'total_price' => 120000,
        'total_passenger' => 1,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $ticket = Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => 'TKT-CS-889900',
        'qr_code' => 'QR-CS-889900',
        'status' => 'issued',
    ]);

    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-tiket'));
    $response->assertOk();
    $response->assertSee('Bantuan CS');
    $response->assertSee('TKT-CS-889900');
    $response->assertSee('BK-CS-889900');
    $response->assertSee('Pool Pasteur');
    $response->assertSee('Gambir');
    $response->assertSee('https://wa.me/6281211112222?text=', false);

    // Cek juga di halaman akun bahwa selector tiket menyertakan booking ini
    $responseAkun = $this->actingAs($this->user)->get(route('dashboard.user.user-akun'));
    $responseAkun->assertOk();
    $responseAkun->assertSee('BK-CS-889900');
});

test('D1: user can update account profile information and password', function () {
    // Update profile
    $resProfile = $this->actingAs($this->user)->post(route('dashboard.user.profil.update'), [
        'name' => 'Budi Santoso Baru',
        'email' => 'budi.baru@travel.test',
        'phone' => '089988776655',
    ]);
    $resProfile->assertRedirect();
    $resProfile->assertSessionHas('success');
    expect($this->user->fresh()->name)->toBe('Budi Santoso Baru');
    expect($this->user->fresh()->email)->toBe('budi.baru@travel.test');

    // Update password
    $resPassword = $this->actingAs($this->user)->post(route('dashboard.user.profil.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    $resPassword->assertRedirect();
    $resPassword->assertSessionHas('success');
    expect(Hash::check('newpassword123', $this->user->fresh()->password))->toBeTrue();
});

test('C2: WhatsApp help button is hidden until the admin sets a company number', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-akun'));

    $response->assertOk();
    $response->assertSee('Nomor WhatsApp CS belum diisi');
    $response->assertDontSee('wa.me/', false);
});
