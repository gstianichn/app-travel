<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->cs = User::firstOrCreate(
        ['email' => 'cs.dashboardtest@travel.test'],
        [
            'name' => 'Customer Service Test',
            'phone' => '081234567877',
            'role' => 'cs',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]
    );
});

test('customer service can access dashboard index with all modular components loaded', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.index'));

    $response->assertOk();

    // 1. Sidebar & Brand
    $response->assertSee('TravelKu');
    $response->assertSee('Dashboard');
    $response->assertSee('Logout');

    // 2. Topbar
    $response->assertSee('Dashboard Customer Service');

    // 3. Welcome Banner
    $response->assertSee('Selamat datang,');
    $response->assertSee('Customer Service Test');

    // 4. Statistics
    $response->assertSee('Pertanyaan Hari Ini');
    $response->assertSee('Pesanan Hari Ini');
    $response->assertSee('Pelanggan Aktif');
    $response->assertSee('Menunggu Respons');

    // 5. Quick Menu
    $response->assertSee('Menu Cepat');
    $response->assertSee('Layanan Pelanggan');
    $response->assertSee('Jadwal Travel');

    // 6. Activity List
    $response->assertSee('Aktivitas Terbaru');
    $response->assertSee('Pertanyaan pelanggan berhasil ditangani');
});
