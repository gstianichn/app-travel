<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer Service - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Styling CSS Khusus Dashboard Customer Service --}}
    @include('dashboard.customer-service.partials.styles')
</head>

<body>

    {{-- Sidebar Navigasi Customer Service --}}
    @include('dashboard.customer-service.partials.sidebar')

    {{-- Konten Utama Dashboard --}}
    <main class="main">

        {{-- Topbar Header CS --}}
        @include('dashboard.customer-service.partials.topbar')

        {{-- Isi Konten Halaman --}}
        <section class="content">

            {{-- 1. Banner Selamat Datang CS --}}
            @include('dashboard.customer-service.partials.welcome-banner')

            {{-- 2. Grid Statistik & Indikator CS (Chat Aktif, Tiket Batal, dsb) --}}
            @include('dashboard.customer-service.partials.stats-grid')

            {{-- 3. Grid Menu Akses Cepat & Log Aktivitas CS --}}
            <div class="dashboard-grid">
                @include('dashboard.customer-service.partials.quick-menu')
                @include('dashboard.customer-service.partials.activity-list')
            </div>

        </section>

    </main>

    {{-- Script Pendukung Interaktivitas CS --}}
    @include('dashboard.customer-service.partials.scripts')

</body>

</html>
