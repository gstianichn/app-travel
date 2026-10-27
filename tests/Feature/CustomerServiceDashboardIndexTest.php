<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Vehicle;
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

    // Sidebar and topbar
    $response->assertSee('TravelKu');
    $response->assertSee('Dashboard');
    $response->assertSee('Logout');
    $response->assertSee('Customer service');

    // Home built from database numbers, with honest empty states
    $response->assertSee('Halo, Customer.');
    $response->assertSee('Cari pesanan');
    $response->assertSee('Keberangkatan tersisa hari ini');
    $response->assertSee('Pembayaran belum selesai');
    $response->assertSee('Refund menunggu admin');
    $response->assertSee('Tidak ada keberangkatan lagi hari ini.');
    $response->assertSee('Tidak ada pembayaran tertunda.');

    // The invented feed, satisfaction score and escalation cases are gone
    $response->assertDontSee('98.4%');
    $response->assertDontSee('Pertanyaan pelanggan berhasil ditangani');
});

test('customer service can look up a booking by code or passenger phone', function () {
    $origin = Location::create(['code' => 'CS-A', 'name' => 'Pool Pasteur (Bandung)', 'city' => 'Bandung', 'is_active' => true]);
    $destination = Location::create(['code' => 'CS-B', 'name' => 'Tebet (Jakarta)', 'city' => 'Jakarta', 'is_active' => true]);
    $route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $destination->id, 'price' => 125000, 'duration' => '2 Jam 30 Menit', 'is_active' => true]);
    $vehicle = Vehicle::create(['name' => 'HiAce CS', 'type' => 'HiAce Premio', 'plate_number' => 'D 9999 CS', 'capacity' => 10, 'status' => 'Available']);
    $seat1 = Seat::create(['vehicle_id' => $vehicle->id, 'seat_number' => '1A', 'seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']);
    $seat2 = Seat::create(['vehicle_id' => $vehicle->id, 'seat_number' => '1B', 'seat_row' => '1', 'seat_column' => 2, 'status' => 'Available']);
    $schedule = Schedule::create(['route_id' => $route->id, 'vehicle_id' => $vehicle->id, 'departure_date' => now()->addDay()->toDateString(), 'departure_time' => '08:00', 'available_seats' => 9, 'status' => 'Scheduled']);
    $passenger = User::create(['name' => 'Dewi Lestari', 'email' => 'dewi.cs@travel.test', 'phone' => '081299990000', 'role' => 'user', 'status' => 'active', 'password' => Hash::make('password')]);
    $booking = Booking::create([
        'booking_code' => 'TRV-CS-LOOKUP',
        'user_id' => $passenger->id,
        'contact_name' => 'Dewi Lestari',
        'contact_phone' => '081299990000',
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'paid',
    ]);
    BookingPassenger::create([
        'booking_id' => $booking->id,
        'seat_id' => $seat1->id,
        'name' => 'Dewi Lestari',
        'phone' => '081299990000',
    ]);

    // Lookup by booking code
    $byCode = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => 'CS-LOOKUP']));
    $byCode->assertOk();
    $byCode->assertSee('TRV-CS-LOOKUP');
    $byCode->assertSee('Dewi Lestari');
    $byCode->assertSee('Bandung ke Jakarta');
    $byCode->assertSee('Lunas');

    // Lookup by phone
    $byPhone = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => '081299990000']));
    $byPhone->assertSee('TRV-CS-LOOKUP');

    // Lookup by user name
    $byName = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => 'Dewi Lestari']));
    $byName->assertOk();
    $byName->assertSee('TRV-CS-LOOKUP');
    $byName->assertSee('Dewi Lestari');

    // Lookup by passenger name when different from booker
    $guestBooking = Booking::create([
        'booking_code' => 'TRV-GUEST-NAME',
        'contact_name' => 'Pemesan Walkin',
        'contact_phone' => '081311112222',
        'schedule_id' => $schedule->id,
        'total_passenger' => 1,
        'subtotal' => 125000,
        'discount' => 0,
        'total_price' => 125000,
        'status' => 'paid',
    ]);
    BookingPassenger::create([
        'booking_id' => $guestBooking->id,
        'seat_id' => $seat2->id,
        'name' => 'Siti Khadijah',
        'phone' => '081311112222',
    ]);

    $byPassengerName = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => 'Siti Khadijah']));
    $byPassengerName->assertOk();
    $byPassengerName->assertSee('TRV-GUEST-NAME');
    $byPassengerName->assertSee('Siti Khadijah');

    // Lookup by contact name
    $byContactName = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => 'Pemesan Walkin']));
    $byContactName->assertOk();
    $byContactName->assertSee('TRV-GUEST-NAME');
    $byContactName->assertSee('Pemesan Walkin');

    $noMatch = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index', ['q' => 'TIDAK-ADA']));
    $noMatch->assertSee('Tidak ada pesanan yang cocok');
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
