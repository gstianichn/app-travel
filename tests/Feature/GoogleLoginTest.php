<?php

use App\Models\Booking;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

beforeEach(function () {
    config([
        'services.google.client_id' => 'test-client-id',
        'services.google.client_secret' => 'test-client-secret',
    ]);
});

function fakeGoogleUser(string $email, string $id = 'google-123', string $name = 'Rina Google'): void
{
    $googleUser = (new GoogleUser)->map(['id' => $id, 'name' => $name, 'email' => $email]);

    Socialite::shouldReceive('driver->user')->andReturn($googleUser);
}

test('the passenger login and register pages offer Google', function () {
    $this->get(route('user.login'))
        ->assertOk()
        ->assertSee(route('auth.google.redirect'), false)
        ->assertSee('Masuk dengan Google');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Daftar dengan Google');
});

test('without Google credentials the button explains itself instead of failing', function () {
    config(['services.google.client_id' => null]);

    $this->get(route('auth.google.redirect'))
        ->assertRedirect(route('user.login'))
        ->assertSessionHasErrors('google');
});

test('Google login creates a passenger account and signs it in', function () {
    fakeGoogleUser('rina.google@example.com');

    $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard.user.index'));

    $user = User::where('email', 'rina.google@example.com')->firstOrFail();
    expect($user->role)->toBe('user');
    expect($user->google_id)->toBe('google-123');
    expect($user->email_verified_at)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

test('Google login links to an existing passenger with the same email', function () {
    $existing = User::create([
        'name' => 'Rina Lama',
        'email' => 'rina.google@example.com',
        'password' => Hash::make('rahasia123'),
        'role' => 'user',
    ]);

    fakeGoogleUser('rina.google@example.com');

    $this->get(route('auth.google.callback'))->assertRedirect();

    expect(User::where('email', 'rina.google@example.com')->count())->toBe(1);
    expect($existing->fresh()->google_id)->toBe('google-123');
    $this->assertAuthenticatedAs($existing);
});

test('Google login is refused for staff accounts', function () {
    User::create([
        'name' => 'Petugas',
        'email' => 'petugas@example.com',
        'password' => Hash::make('rahasia123'),
        'role' => 'staff',
    ]);

    fakeGoogleUser('petugas@example.com');

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('user.login'))
        ->assertSessionHasErrors('google');

    $this->assertGuest();
});

test('Google login picks up earlier guest bookings made with the same email', function () {
    $origin = Location::create(['code' => 'A-GGL', 'name' => 'Pool A', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $destination = Location::create(['code' => 'B-GGL', 'name' => 'Pool B', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);
    $route = Route::create(['origin_id' => $origin->id, 'destination_id' => $destination->id, 'price' => 100000, 'duration' => '3 Jam', 'is_active' => true]);
    $schedule = Schedule::create([
        'route_id' => $route->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '08:00',
        'price' => 100000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $guestBooking = Booking::create([
        'booking_code' => 'TRV-GGL-001',
        'user_id' => null,
        'contact_email' => 'rina.google@example.com',
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 100000,
        'total_price' => 100000,
        'status' => 'confirmed',
    ]);

    fakeGoogleUser('rina.google@example.com');

    $this->get(route('auth.google.callback'))->assertRedirect();

    expect($guestBooking->fresh()->user_id)->toBe(User::where('email', 'rina.google@example.com')->value('id'));
});
