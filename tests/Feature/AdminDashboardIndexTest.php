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
    $response->assertSee('Selamat Datang,');
    $response->assertSee('Sistem Operasional Aktif');
    // KPI Metrics
    $response->assertSee('Metrik Utama Hari Ini');
    $response->assertSee('Tiket Terjual Hari Ini');
    $response->assertSee('Pendapatan Hari Ini');
    $response->assertSee('Armada Sedang Jalan');
    $response->assertSee('Total Kursi Kosong');
    // Alerts & Notifications
    $response->assertSee('Notifikasi & Pengingat Penting', false);
    // Schedules Table
    $response->assertSee('Jadwal Perjalanan Terdekat');
    $response->assertSee('Daftar Jadwal Keberangkatan');
    // Quick Nav
    $response->assertSee('Akses Modul Administrasi');
    $response->assertSee('Kelola User');
    $response->assertSee('Rute Perjalanan');
});
