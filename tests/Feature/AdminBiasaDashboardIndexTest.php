<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->staff = User::firstOrCreate(
        ['email' => 'staff.dashboardtest@travel.test'],
        [
            'name' => 'Staf Operasional Test',
            'phone' => '081234567888',
            'role' => 'staff',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('staff can access admin-biasa dashboard index with all modular components loaded', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.index'));

    $response->assertOk();

    // Sidebar
    $response->assertSee('Menu Utama');
    $response->assertSee('Manajemen Booking');
    $response->assertSee('Melihat Informasi Perjalanan');
    $response->assertSee('Validasi E-Ticket');

    // Greeting, numbers and departures
    $response->assertSee('Halo, Staf.');
    $response->assertSee('Boarding dibuka sekarang');
    $response->assertSee('Jadwal hari ini');
    $response->assertSee('Keberangkatan terdekat');
    $response->assertSee('Aktivitas terakhir');

    // Scanning goes to the real validation page; the old simulated scanner is gone
    $response->assertSee(route('dashboard.admin-biasa.validasi-tiket'), false);
    $response->assertDontSee('simulateScan');
    $response->assertDontSee('Ahmad Subarjo');
});

test('staff dashboard shows an honest empty state when no schedules exist', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.index'));

    $response->assertOk();
    $response->assertSee('Belum ada jadwal mendatang.');
});

test('staff can access informasi perjalanan page safely without memory issues', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.perjalanan'));

    $response->assertOk();
    $response->assertSee('Informasi Perjalanan Armada');
    $response->assertSee('Semua Status');
    $response->assertSee('Waktu Berangkat');
});

test('staff can access manajemen booking page safely', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('dashboard.admin-biasa.booking'));

    $response->assertOk();
    $response->assertSee('Manajemen Booking');
});
