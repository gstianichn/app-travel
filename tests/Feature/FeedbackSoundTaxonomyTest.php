<?php

use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'user', 'status' => 'active']);

    $origin = Location::create(['name' => 'Pasteur (Bandung)', 'code' => 'BDO-SND', 'city' => 'Bandung', 'type' => 'pool', 'is_active' => true]);
    $destination = Location::create(['name' => 'Cikini (Jakarta)', 'code' => 'JKT-SND', 'city' => 'Jakarta', 'type' => 'pool', 'is_active' => true]);

    $this->route = TravelRoute::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 150000,
        'duration' => '3 Jam',
        'is_active' => true,
    ]);

    $vehicle = Vehicle::create([
        'plate_number' => 'D 7001 SND',
        'name' => 'Toyota HiAce Premio',
        'type' => 'Toyota HiAce Premio',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $vehicle->ensureSeatsExist();

    $makeSchedule = fn (string $time, int $seats): Schedule => Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => $time,
        'price' => 150000,
        'available_seats' => $seats,
        'status' => 'Scheduled',
    ]);

    $this->openSchedule = $makeSchedule('08:00', 6);
    $this->fullSchedule = $makeSchedule('10:00', 0);

    $this->searchParams = [
        'origin' => $origin->id,
        'destination' => $destination->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'passengers' => 1,
    ];
});

test('tombol Pilih kursi memutar Action Sound dan jadwal penuh tampil nonaktif dengan Warning Sound', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.search', $this->searchParams));

    $response->assertOk()
        ->assertSee('window.TravelFeedback?.playAction(false)', false)
        ->assertSee('data-schedule-full', false)
        ->assertSee('Jadwal Penuh')
        ->assertSee('window.TravelFeedback?.playWarning()', false);

    expect($response->viewData('schedules')->pluck('id')->all())->toBe([$this->openSchedule->id])
        ->and($response->viewData('fullSchedules')->pluck('id')->all())->toBe([$this->fullSchedule->id]);
});

test('halaman pencarian publik juga menampilkan jadwal penuh sebagai tombol peringatan', function () {
    $response = $this->get(route('search', $this->searchParams));

    $response->assertOk()->assertSee('data-schedule-full', false);
    expect($response->viewData('fullSchedules')->pluck('id')->all())->toBe([$this->fullSchedule->id]);
});

test('membuka jadwal penuh lewat URL ditolak dan halaman tujuan memutar Warning Sound dengan toast merah', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->fullSchedule->id]));

    $response->assertRedirect()->assertSessionHas('error', 'Jadwal ini sudah penuh. Silakan pilih jam atau tanggal lain.');

    $this->actingAs($this->user)
        ->get($response->headers->get('Location'))
        ->assertOk()
        ->assertSee('window.TravelFeedback?.playWarning();', false)
        ->assertSee('Peringatan Sistem');
});

test('halaman pilih kursi memasang Action, Warning, dan Success Sound', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard.user.booking.pilih-kursi', ['schedule' => $this->openSchedule->id]))
        ->assertOk()
        ->assertSee('playAction(false)', false)
        ->assertSee('playWarning()', false)
        ->assertSee('playSuccess()', false);
});

