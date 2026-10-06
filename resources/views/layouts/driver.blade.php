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
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:currentColor;display:flex;align-items:center;" aria-label="Tutup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-banner error">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:currentColor;display:flex;align-items:center;" aria-label="Tutup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
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
