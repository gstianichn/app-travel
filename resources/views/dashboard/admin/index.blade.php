@extends('layouts.admin')

@section('title', 'Dashboard Admin Utama')

@section('page-title', 'Dashboard Admin Utama')

@section('page-subtitle', 'Pantau metrik operasional harian, jadwal perjalanan, dan notifikasi armada TravelKu.')

@push('styles')
    @include('dashboard.admin.partials.styles')
@endpush

@section('content')
<div class="admin-dashboard-container">
    {{-- 1. Banner Selamat Datang & Status Sistem --}}
    @include('dashboard.admin.partials.welcome-banner')

    {{-- 2. Metrik Utama Hari Ini (Tiket, Pendapatan, Armada, Kursi) --}}
    @include('dashboard.admin.partials.kpi-metrics')

    {{-- 3. Notifikasi & Pengingat Penting (Refund, Driver, Dokumen Armada) --}}
    @include('dashboard.admin.partials.alerts')

    {{-- 4. Jadwal Perjalanan Terdekat & Pemantauan Live --}}
    @include('dashboard.admin.partials.schedules-table')

    {{-- 5. Pintasan Akses Cepat Modul Administrasi --}}
    @include('dashboard.admin.partials.quick-nav')
</div>
@endsection

@push('scripts')
    @include('dashboard.admin.partials.scripts')
@endpush