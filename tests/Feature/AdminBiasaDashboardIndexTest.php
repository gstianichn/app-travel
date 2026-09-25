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

    // 1. Sidebar
    $response->assertSee('Menu Utama');
    $response->assertSee('Manajemen Booking');
    $response->assertSee('Melihat Informasi Perjalanan');
    $response->assertSee('Validasi E-Ticket');

    // 2. Topbar
    $response->assertSee('Dashboard Operasional (Layar Utama)');

    // 3. Shift Banner
    $response->assertSee('Buka Kamera Pemindai');
    $response->assertSee('Tombol Pintas (Scan QR)');

    // 4. Stats Grid
    $response->assertSee('Jadwal Aktif Hari Ini');
    $response->assertSee('Boarding Dibuka');
    $response->assertSee('Tahap Persiapan');
    $response->assertSee('Pemesanan Hari Ini');

    // 5. Departures Section
    $response->assertSee('Daftar Keberangkatan Terdekat', false);
    $response->assertSee('Live Monitor');

    // 6. Quick Actions & Activity
    $response->assertSee('Akses Cepat Operasional');
    $response->assertSee('Aktivitas Terkini');

    // 7. Scanner Modal
    $response->assertSee('Kamera Pemindai QR Code E-Ticket');
    $response->assertSee('scannerModal');

    // 8. Scripts
    $response->assertSee('openScannerModal');
    $response->assertSee('confirmBoarding');
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
