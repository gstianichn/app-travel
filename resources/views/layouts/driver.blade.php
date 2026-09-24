<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Cockpit Driver Dashboard - Operasional Travel Minibus')
    </title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    {{-- Vite Asset Bundler --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Stylesheet Khusus Driver Cockpit --}}
    @include('dashboard.driver.partials.styles')

    {{-- Custom Page Styles --}}
    @stack('styles')
</head>

<body>

<div class="driver-shell">

    {{-- Sidebar Navigasi Menu Driver --}}
    @include('dashboard.driver.partials.sidebar')

    {{-- Konten Utama Dashboard Driver --}}
    <main class="main-canvas" id="section-dashboard">

        {{-- Flash Notification Toast --}}
        @if (session('success'))
            <div class="toast-banner">
                <div>✓ &nbsp; {{ session('success') }}</div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:bold;">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-banner error">
                <div>⚠️ &nbsp; {{ session('error') }}</div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:bold;">✕</button>
            </div>
        @endif

        {{-- Header Identitas Tugas & Status Sistem --}}
        @include('dashboard.driver.partials.topbar')

        {{-- Dynamic Content Area --}}
        @yield('content')

    </main>

</div>

{{-- Global Modals: Lapor Kendala & Emergency SOS --}}
@include('dashboard.driver.partials.lapor-masalah')
@include('dashboard.driver.partials.emergency')

{{-- Extra Modals Stack --}}
@stack('modals')

{{-- Script Logika GPS, Slider, Filter Manifes & Interaksi --}}
@include('dashboard.driver.partials.scripts')

{{-- Extra Scripts Stack --}}
@stack('scripts')

</body>
</html>
