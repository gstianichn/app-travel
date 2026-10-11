<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.dashboardtest@travel.test'],
        [
            'name' => 'Admin Utama Dashboard Test',
            'phone' => '081234567899',
            'role' => 'admin_utama',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('admin can access dashboard index with all modular components loaded', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard.admin.index'));

    $response->assertOk();
    // Welcome Banner
    $response->assertSee('Halo,');
    $response->assertDontSee('Sistem Operasional Aktif');
    // KPI Metrics
    $response->assertSee('Angka hari ini', false);
    $response->assertSee('Tiket terjual');
    $response->assertSee('Pendapatan hari ini');
    $response->assertSee('Armada terjadwal');
    $response->assertSee('Kursi kosong');
    // Alerts & Notifications
    $response->assertSee('Penumpang baru');
    // Schedules Table
    $response->assertSee('Jadwal terdekat');
    $response->assertSee('Belum ada jadwal mendatang.');
    // Quick Nav
    $response->assertSee('Menu admin', false);
    $response->assertSee('Kelola User');
    $response->assertSee('Rute Perjalanan');
});
