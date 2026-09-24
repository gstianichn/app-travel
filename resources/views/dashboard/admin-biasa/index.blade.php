<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff - Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Styling CSS Khusus Dashboard Staff Operasional --}}
    @include('dashboard.admin-biasa.partials.styles')
</head>

<body>

<div class="dashboard-wrapper">

    {{-- Sidebar Navigasi Menu Utama --}}
    @include('dashboard.admin-biasa.partials.sidebar')

    {{-- Konten Utama --}}
    <main class="main-content">

        {{-- Topbar Staff & Pool Lokasi --}}
        @include('dashboard.admin-biasa.partials.topbar')

        {{-- Section Isi Dashboard --}}
        <section class="content">

            {{-- 1. Informasi Shift & Lokasi + Tombol Pintas Scanner --}}
            @include('dashboard.admin-biasa.partials.shift-banner')

            {{-- 2. Metrik Operasional Hari Ini --}}
            @include('dashboard.admin-biasa.partials.stats-grid')

            {{-- 3. Daftar Keberangkatan Terdekat (1-2 Jam Ke Depan) --}}
            @include('dashboard.admin-biasa.partials.departures-section')

            {{-- 4. Menu Akses Cepat Operasional & Aktivitas Terkini --}}
            @include('dashboard.admin-biasa.partials.quick-actions-activity')

        </section>

    </main>

</div>

{{-- Modal Scanner Kamera Pemindai QR E-Ticket --}}
@include('dashboard.admin-biasa.partials.scanner-modal')

{{-- Javascript Logika Scanner QR & Aksi Boarding --}}
@include('dashboard.admin-biasa.partials.scripts')

</body>

</html>
