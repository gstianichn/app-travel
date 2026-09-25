@extends('layouts.customer-service')

@section('title', 'Dashboard Customer Service - TravelKu FastShuttle')

@section('content')

    {{-- Banner Selamat Datang & Status Shift CS --}}
    @include('dashboard.customer-service.partials.welcome-banner')

    {{-- 1. Kartu Metrik Kerja Harian (Real-Time Queue Counters: Chat Menunggu Dibalas, Batas SLA, Eskalasi, Keberangkatan Terdekat) --}}
    @include('dashboard.customer-service.partials.stats-grid')

    {{-- 3. Pintasan Pencarian Cepat (Omnisearch Bar: PNR, WhatsApp, Nama, Plat Nomor) --}}
    @include('dashboard.customer-service.partials.omnisearch')

    {{-- 2. Panel Pantauan Keberangkatan Hari Ini (Trip Monitor & Incident Alert) --}}
    @include('dashboard.customer-service.partials.trip-monitor')

    {{-- 4 & 5. Papan Kasus Eskalasi Berjalan (Escalation Tracker) & Papan Pengumuman Operasional (Notice Board) --}}
    <div class="dashboard-grid" style="margin-bottom: 24px;">
        @include('dashboard.customer-service.partials.escalation-tracker')
        @include('dashboard.customer-service.partials.notice-board')
    </div>

    {{-- 6. Menu Akses Cepat & Log Aktivitas Pelayanan Terbaru --}}
    <div class="dashboard-grid">
        @include('dashboard.customer-service.partials.quick-menu')
        @include('dashboard.customer-service.partials.activity-list')
    </div>

@endsection
