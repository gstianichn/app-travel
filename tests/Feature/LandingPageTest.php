<?php

use App\Models\Booking;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\TripReview;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

function landingLocation(string $code, string $name, string $city): Location
{
    return Location::create([
        'code' => $code,
        'name' => $name,
        'city' => $city,
        'address' => 'Jl. Uji No. 1',
        'type' => 'pool',
        'is_active' => true,
    ]);
}

test('landing page shows empty states when nothing is configured yet', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Rute sedang disiapkan');
    $response->assertSee('Data armada belum diisi.');
    $response->assertSee('Titik keberangkatan belum diisi oleh admin');
    $response->assertDontSee('Kata penumpang');
    $response->assertDontSee('lp-hero-facts', false);
});

test('landing page shows one sign per city pair with the cheapest active route', function () {
    $jakartaTebet = landingLocation('JKT-TBT', 'Tebet (Jakarta)', 'Jakarta');
    $jakartaCikini = landingLocation('JKT-CKN', 'Cikini (Jakarta)', 'Jakarta');
    $bandung = landingLocation('BDG-PST', 'Pool Pasteur (Bandung)', 'Bandung');
    $solo = landingLocation('SOC-TRT', 'Pool Tirtonadi (Solo)', 'Solo');

    TravelRoute::create(['origin_id' => $jakartaCikini->id, 'destination_id' => $bandung->id, 'price' => 130000, 'duration' => '2 Jam 45 Menit', 'is_active' => true]);
    $cheapest = TravelRoute::create(['origin_id' => $jakartaTebet->id, 'destination_id' => $bandung->id, 'price' => 125000, 'duration' => '2 Jam 30 Menit', 'distance_km' => 150, 'is_active' => true]);
    TravelRoute::create(['origin_id' => $bandung->id, 'destination_id' => $solo->id, 'price' => 210000, 'duration' => '8 Jam', 'is_active' => false]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('data-origin="'.$cheapest->origin_id.'"', false);
    $response->assertSee('mulai Rp 125.000');
    $response->assertSee('2 Jam 30 Menit, 150 km');
    $response->assertDontSee('mulai Rp 130.000');
    $response->assertDontSee('mulai Rp 210.000');
    $response->assertSee('2 rute aktif');
    $response->assertDontSee('Rute sedang disiapkan');
});

test('landing page summarises the fleet per vehicle type', function () {
    foreach (['B 1001 TK', 'B 1002 TK'] as $plate) {
        Vehicle::create([
            'plate_number' => $plate,
            'name' => 'HiAce '.$plate,
            'type' => 'Toyota HiAce Premio',
            'capacity' => 10,
            'status' => 'siap_jalan',
            'facilities' => ['AC Dingin', 'Port USB Charger'],
        ]);
    }

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Toyota HiAce Premio');
    $response->assertSee('10 kursi penumpang, 2 unit');
    $response->assertSee('AC Dingin, Port USB Charger');
});

test('landing page shows real passenger reviews with their average', function () {
    $origin = landingLocation('JKT-SMG', 'Semanggi (Jakarta)', 'Jakarta');
    $destination = landingLocation('BDG-DPU', 'Pool Dipatiukur (Bandung)', 'Bandung');
    $route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $destination->id, 'price' => 125000, 'duration' => '2 Jam 45 Menit', 'is_active' => true]);
    $schedule = Schedule::create([
        'route_id' => $route->id,
        'departure_date' => now()->subDay()->toDateString(),
        'departure_time' => '08:00',
        'available_seats' => 10,
        'status' => 'Completed',
    ]);
    $passenger = User::create([
        'name' => 'Rina Kartika',
        'email' => 'rina.landing@travel.test',
        'phone' => '081200000001',
        'role' => 'user',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);
    $booking = Booking::create([
        'booking_code' => 'TRV-LANDING-1',
        'user_id' => $passenger->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'completed',
    ]);

    TripReview::create([
        'booking_id' => $booking->id,
        'user_id' => $passenger->id,
        'schedule_id' => $schedule->id,
        'overall_rating' => 4.5,
        'comment' => 'Sopirnya sabar dan mobilnya bersih.',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Kata penumpang');
    $response->assertSee('4,5');
    $response->assertSee('Sopirnya sabar dan mobilnya bersih.');
    $response->assertSee('Rina');
    $response->assertDontSee('Rina Kartika');
    $response->assertSee('Jakarta ke Bandung');
});
