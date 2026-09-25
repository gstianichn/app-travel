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

test('customer service can access live chat queue with 3 tabs and departure urgency features', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.live-chat'));

    $response->assertOk();
    $response->assertSee('Pusat Antrean Live Chat');
    $response->assertSee('Unassigned');
    $response->assertSee('Assigned to Me');
    $response->assertSee('Resolved');
    $response->assertSee('Konteks Pemesanan');
    $response->assertSee('Eskalasi ke Admin');
});

test('customer service live chat displays complete Melihat Konteks Booking panel and quick actions', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.live-chat'));

    $response->assertOk();

    // 1. Panel Header & Identitas Tiket
    $response->assertSee('Melihat Konteks Booking');
    $response->assertSee('Identitas Tiket');
    $response->assertSee('Nama Pemesan:');
    $response->assertSee('Rute Perjalanan:');
    $response->assertSee('Tanggal Keberangkatan:');
    $response->assertSee('Jam Keberangkatan:');

    // 2. Detail Kursi & Armada
    $response->assertSee('Detail Kursi &amp; Armada', false);
    $response->assertSee('Nomor Kursi Dipilih:');
    $response->assertSee('Nomor Polisi Armada:');
    $response->assertSee('Sopir Bertugas:');
    $response->assertSee('WhatsApp');

    // 3. Titik Jemput & Turun
    $response->assertSee('Titik Jemput &amp; Turun', false);
    $response->assertSee('Alamat Penjemputan:');
    $response->assertSee('Instruksi Penjemputan:');
    $response->assertSee('Google Maps');
    $response->assertSee('Alamat Penurunan:');

    // 4. Status Pembayaran
    $response->assertSee('Status Pembayaran');
    $response->assertSee('Metode Bayar:');
    $response->assertSee('Total Tagihan:');
    $response->assertSee('Waktu Pembayaran:');

    // 5. Tombol Aksi Cepat (Quick Action) & Modals
    $response->assertSee('Tombol Aksi Cepat');
    $response->assertSee('Ubah Kursi');
    $response->assertSee('Reschedule');
    $response->assertSee('Ajukan Pembatalan');
    $response->assertSee('modal-change-seat');
    $response->assertSee('modal-reschedule');
    $response->assertSee('modal-cancellation');
});

test('customer service can open dedicated Melihat Konteks Booking page directly from sidebar', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.booking-context'));

    $response->assertOk();
    $response->assertSee('Melihat Konteks Booking');
    $response->assertSee('Antrean Booking Pelanggan');
    $response->assertSee('Identitas Tiket');
    $response->assertSee('Detail Kursi &amp; Armada', false);
    $response->assertSee('Titik Jemput &amp; Turun', false);
    $response->assertSee('Status Pembayaran');
    $response->assertSee('Tombol Aksi Cepat (Quick Action)');
    $response->assertSee('Ubah Kursi');
    $response->assertSee('Reschedule');
    $response->assertSee('Ajukan Pembatalan');
});

test('customer service can open dedicated Melihat Riwayat Chat page with keyword search, internal notes, and agent handover logs', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.chat-history'));

    $response->assertOk();
    $response->assertSee('Melihat Riwayat Chat');
    $response->assertSee('Rekam jejak komunikasi agar CS tidak perlu menanyakan hal yang sama berulang kali');
    $response->assertSee('Pencarian Kata Kunci (Search in Chat)');
    $response->assertSee('Catatan Internal (Warna Kuning Khusus Antar-CS)');
    $response->assertSee('Log Pergantian Agen');
    $response->assertSee('bawa lansia');
    $response->assertSee('nomor rekening');
    $response->assertSee('Total Riwayat Percakapan');
    $response->assertSee('Catatan Internal Antar-CS');
});

test('customer service live chat includes search in chat and internal notes switch features', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.live-chat'));

    $response->assertOk();
    $response->assertSee('search-in-chat-input');
    $response->assertSee('Catatan Internal Antar-CS (Kuning)');
    $response->assertSee('Balas Penumpang');
    $response->assertSee('bawa lansia');
    $response->assertSee('Log Pergantian Agen');
    $response->assertSee('accept=".jpg,.jpeg,.png,.pdf"', false);
    $response->assertSee('Generator Tiket Instan (PDF)');
    $response->assertSee('Kompresi Otomatis');
    $response->assertSee('E-Tiket-Resmi-TRV-2026-0925.pdf');
});

test('customer service can open dedicated Mengirim Gambar / File page with JPG PNG PDF support, compression, and ticket generator', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.send-file'));

    $response->assertOk();
    $response->assertSee('Mengirim Gambar / File');
    $response->assertSee('Format yang didukung: Gambar (JPG, PNG) dan dokumen (PDF)', false);
    $response->assertSee('Kompresi Otomatis');
    $response->assertSee('Generator Tiket Instan');
    $response->assertSee('generator tiket instan');
    $response->assertSee('Bukti Transfer / Rekening');
    $response->assertSee('TRV-2026-0925');
    $response->assertSee('Official Boarding Pass &amp; E-Ticket', false);
    $response->assertSee('Kirim E-Tiket Instan (PDF) ke Chat');
});

test('customer service can open dedicated Melakukan Eskalasi ke Admin page with 3 PRD categories, severity levels, mandatory root cause form, and status tracker', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.escalation'));

    $response->assertOk();
    $response->assertSee('Melakukan Eskalasi ke Admin');
    $response->assertSee('Jalur pengalihan masalah yang melampaui wewenang CS garis depan');
    $response->assertSee('Pengajuan Refund / Keuangan');
    $response->assertSee('Armada Rusak / Ganti Mobil (Operasional)');
    $response->assertSee('Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)');
    $response->assertSee('Prioritas Kendala (Severity Level)');
    $response->assertSee('Low');
    $response->assertSee('Medium');
    $response->assertSee('High/Emergency');
    $response->assertSee('Form Ringkasan Masalah');
    $response->assertSee('Akar Masalah (Root Cause)');
    $response->assertSee('Data Pendukung');
    $response->assertSee('Pelacakan Status Eskalasi');
    $response->assertSee('Pending di Admin');
    $response->assertSee('Diproses');
    $response->assertSee('Selesai');
});

test('customer service live chat escalation modal includes 3 PRD categories, severity level, and mandatory root cause fields', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.live-chat'));

    $response->assertOk();
    $response->assertSee('Pengajuan Refund / Keuangan');
    $response->assertSee('Armada Rusak / Ganti Mobil (Operasional)');
    $response->assertSee('Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)');
    $response->assertSee('High/Emergency');
    $response->assertSee('Form Ringkasan Masalah (Akar Masalah - Wajib)');
    $response->assertSee('Data Pendukung (Wajib)');
});
