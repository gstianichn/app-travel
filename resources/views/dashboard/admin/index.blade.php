@extends('layouts.admin')

@section('title', 'Dashboard Admin Utama')

@section('page-title', 'Dashboard Admin Utama')

@section('page-subtitle', 'Pantau metrik operasional harian, jadwal perjalanan, dan notifikasi armada TravelKu.')

@section('content')

<style>
    .admin-dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 26px;
        width: 100%;
    }

    /* =========================================
       WELCOME BANNER
    ========================================== */
    .welcome-banner {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 24px 28px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 60%, #045236 100%);
        border-radius: 18px;
        color: white;
        box-shadow: 0 8px 24px rgba(8, 127, 85, 0.16);
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-text h1 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.2px;
    }

    .welcome-text p {
        margin: 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.88);
        max-width: 620px;
        line-height: 1.5;
    }

    .welcome-chips {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }

    .chip-date {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 12px;
        font-weight: 600;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .chip-status-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #bbf7d0;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.35);
    }

    /* =========================================
       NOTIFIKASI PENTING (ALERTS SECTION)
    ========================================== */
    .alerts-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .alert-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        gap: 14px;
        border-left: 5px solid;
    }

    .alert-card.refund-alert {
        border-color: #f59e0b;
        background: linear-gradient(to right, #fffdf8, #ffffff);
    }

    .alert-card.maintenance-alert {
        border-color: #ef4444;
        background: linear-gradient(to right, #fffaf9, #ffffff);
    }

    .alert-card.clean-alert {
        border-color: #10b981;
        background: linear-gradient(to right, #f7fdf9, #ffffff);
    }

    .alert-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .alert-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-icon-pill {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .alert-card.refund-alert .alert-icon-pill {
        background: #fef3c7;
        color: #d97706;
    }

    .alert-card.maintenance-alert .alert-icon-pill {
        background: #fee2e2;
        color: #dc2626;
    }

    .alert-card.clean-alert .alert-icon-pill {
        background: #dcfce7;
        color: #15803d;
    }

    .alert-card-title h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
    }

    .alert-card-title p {
        margin: 2px 0 0;
        font-size: 11px;
        color: #7b8882;
    }

    .alert-badge-count {
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .refund-alert .alert-badge-count {
        background: #fef3c7;
        color: #b45309;
    }

    .maintenance-alert .alert-badge-count {
        background: #fee2e2;
        color: #b91c1c;
    }

    .alert-item-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: #fbfdfc;
        border: 1px solid #edf2ef;
        font-size: 12px;
    }

    .alert-item-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .alert-item-code {
        font-weight: 700;
        color: #17221d;
    }

    .alert-item-sub {
        font-size: 11px;
        color: #6e7d76;
    }

    .alert-item-action {
        font-weight: 700;
        font-size: 11px;
        color: #087f55;
        text-decoration: none;
    }

    .alert-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-top: 4px;
    }

    .alert-footer-link {
        font-size: 12px;
        font-weight: 600;
        color: #087f55;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .alert-footer-link:hover {
        text-decoration: underline;
    }

    /* =========================================
       1. METRIK UTAMA (SUMMARY CARDS)
    ========================================== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .metric-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 22px 20px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(8, 127, 85, 0.08);
    }

    .metric-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .metric-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #72817a;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 26px;
        font-weight: 800;
        color: #17221d;
        line-height: 1.1;
    }

    .metric-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .metric-icon-box.tickets {
        background: #e3f5ee;
        color: #087f55;
    }

    .metric-icon-box.revenue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .metric-icon-box.fleet {
        background: #fef3c7;
        color: #d97706;
    }

    .metric-icon-box.seats {
        background: #f3e8ff;
        color: #9333ea;
    }

    .metric-bottom {
        padding-top: 12px;
        border-top: 1px solid #f1f5f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #87948e;
    }

    .metric-badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 10px;
    }

    .metric-badge-status.green {
        background: #dcfce7;
        color: #15803d;
    }

    .metric-badge-status.blue {
        background: #e0f2fe;
        color: #0369a1;
    }

    .metric-badge-status.orange {
        background: #fef3c7;
        color: #b45309;
    }

    .metric-badge-status.purple {
        background: #f3e8ff;
        color: #7e22ce;
    }

    /* =========================================
       SECTION TITLES
    ========================================== */
    .section-header-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .section-header-wrap h2 {
        font-size: 17px;
        font-weight: 700;
        color: #17221d;
        margin: 0;
    }

    .section-header-wrap p {
        margin: 3px 0 0;
        font-size: 12px;
        color: #7b8882;
    }

    /* =========================================
       2. JADWAL PERJALANAN TERDEKAT (TABLE)
    ========================================== */
    .schedule-panel-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .schedule-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #edf2ef;
    }

    .schedule-panel-title {
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .schedule-badge-total {
        padding: 3px 9px;
        border-radius: 12px;
        background: #e3f5ee;
        color: #087f55;
        font-size: 11px;
        font-weight: 700;
    }

    .table-scroll-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .schedule-table th {
        padding: 13px 20px;
        background: #fbfdfc;
        color: #72817a;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf1ef;
    }

    .schedule-table td {
        padding: 15px 20px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .schedule-table tbody tr:hover {
        background: #fbfdfc;
    }

    /* ROUTE CELL */
    .route-cell {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 700;
        color: #17221d;
    }

    .route-arrow {
        color: #087f55;
        font-weight: 900;
    }

    /* TIME CELL */
    .time-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .time-val {
        font-weight: 700;
        color: #17221d;
        font-size: 13px;
    }

    .time-date {
        font-size: 11px;
        color: #7b8882;
    }

    /* DRIVER CELL */
    .driver-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .driver-avatar-mini {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 11px;
        flex-shrink: 0;
    }

    .driver-name-text {
        font-weight: 600;
        color: #27342e;
    }

    .driver-unassigned {
        color: #92a099;
        font-style: italic;
        font-size: 12px;
    }

    /* LOAD FACTOR BAR CELL */
    .load-factor-wrap {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 140px;
    }

    .load-factor-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 600;
    }

    .progress-bar-bg {
        width: 100%;
        height: 7px;
        border-radius: 6px;
        background: #e9eeeb;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.3s ease;
    }

    .progress-bar-fill.safe {
        background: #10b981;
    }

    .progress-bar-fill.medium {
        background: #f59e0b;
    }

    .progress-bar-fill.full {
        background: #ef4444;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-pill.scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-pill.active-trip {
        background: #dcfce7;
        color: #15803d;
    }

    .empty-schedules-notice {
        padding: 45px 20px;
        text-align: center;
        color: #8a9791;
        font-size: 13px;
    }

    /* =========================================
       MENU CEPAT GRID
    ========================================== */
    .quick-nav-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .quick-nav-item {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 14px;
        padding: 16px;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .quick-nav-item:hover {
        border-color: #087f55;
        box-shadow: 0 6px 18px rgba(8, 127, 85, 0.10);
        transform: translateY(-2px);
    }

    .quick-nav-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f0f7f4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .quick-nav-text h4 {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .quick-nav-text p {
        margin: 0;
        font-size: 11px;
        color: #7b8882;
    }

    /* =========================================
       RESPONSIVE
    ========================================== */
    @media (max-width: 1150px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .alerts-section {
            grid-template-columns: 1fr;
        }

        .quick-nav-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 650px) {
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .welcome-chips {
            align-items: flex-start;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .quick-nav-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard-container">

    {{-- =========================================
         WELCOME BANNER
    ========================================== --}}
    <div class="welcome-banner">
        <div class="welcome-text">
            <h1>Selamat Datang, {{ $user->name ?? auth()->user()->name ?? 'Admin Utama' }} 👋</h1>
            <p>
                Pantau performa tiket, pendapatan hari ini, keterisian kursi armada, dan tindak lanjuti notifikasi operasional TravelKu secara real-time.
            </p>
        </div>

        <div class="welcome-chips">
            <div class="chip-date">
                <span>📅</span>
                <span>{{ date('d F Y') }}</span>
            </div>
            <div class="chip-status-live">
                <span class="pulse-dot"></span>
                <span>Sistem Operasional Aktif</span>
            </div>
        </div>
    </div>


    {{-- =========================================
         1. METRIK UTAMA (SUMMARY CARDS)
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Metrik Utama Hari Ini</h2>
                <p>Ringkasan penjualan tiket, arus kas pendapatan, armada jalan, dan kursi kosong</p>
            </div>
        </div>

        <div class="metrics-grid" style="margin-top: 14px;">

            {{-- 1. TOTAL TIKET TERJUAL HARI INI --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Tiket Terjual Hari Ini</div>
                        <div class="metric-value">{{ number_format($ticketsSoldToday ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box tickets">
                        🎟️
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Tiket penumpang terbit</span>
                    <span class="metric-badge-status green">Hari Ini</span>
                </div>
            </div>

            {{-- 2. PENDAPATAN HARI INI --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Pendapatan Hari Ini</div>
                        <div class="metric-value">Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="metric-icon-box revenue">
                        💰
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Transaksi terbayar</span>
                    <span class="metric-badge-status blue">Terverifikasi</span>
                </div>
            </div>

            {{-- 3. JUMLAH ARMADA SEDANG JALAN --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Armada Sedang Jalan</div>
                        <div class="metric-value">{{ number_format($activeFleetCount ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box fleet">
                        🚐
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Minibus & Van di jalan</span>
                    <span class="metric-badge-status orange">Aktif</span>
                </div>
            </div>

            {{-- 4. TOTAL KURSI KOSONG --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Total Kursi Kosong</div>
                        <div class="metric-value">{{ number_format($emptySeatsToday ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box seats">
                        💺
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Siap untuk dipesan</span>
                    <span class="metric-badge-status purple">Tersedia</span>
                </div>
            </div>

        </div>
    </div>


    {{-- =========================================
         3. NOTIFIKASI PENTING
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Notifikasi & Pengingat Penting</h2>
                <p>Tindak lanjut pengajuan refund dan pengingat kelayakan armada serta pengemudi</p>
            </div>
        </div>

        <div class="alerts-section" style="margin-top: 14px;">

            {{-- PENGINGAT REFUND BELUM DIPROSES --}}
            @if(isset($pendingRefunds) && $pendingRefunds->count() > 0)
                <div class="alert-card refund-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                🔄
                            </div>
                            <div>
                                <h3>Persetujuan Refund Menunggu</h3>
                                <p>Terdapat permintaan refund yang memerlukan tindakan verifikasi admin</p>
                            </div>
                        </div>
                        <span class="alert-badge-count">
                            {{ $totalPendingRefunds ?? $pendingRefunds->count() }} Menunggu
                        </span>
                    </div>

                    <div class="alert-item-list">
                        @foreach($pendingRefunds as $rf)
                            <div class="alert-item">
                                <div class="alert-item-main">
                                    <div class="alert-item-code">
                                        {{ $rf->booking?->booking_code ?? 'Booking #' . $rf->booking_id }}
                                        &bull; Rp {{ number_format($rf->amount, 0, ',', '.') }}
                                    </div>
                                    <div class="alert-item-sub">
                                        Pemohon: {{ $rf->user?->name ?? 'Pelanggan' }} &bull; {{ $rf->reason ? Str::limit($rf->reason, 35) : 'Pengajuan refund' }}
                                    </div>
                                </div>
                                <div>
                                    <span style="font-size: 11px; color: #b45309; font-weight: 700;">
                                        {{ $rf->created_at ? $rf->created_at->diffForHumans() : 'Baru' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert-card-footer">
                        <a href="{{ route('dashboard.admin.refund.index') }}" class="alert-footer-link">
                            Buka Modul Refund &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="alert-card clean-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                ✓
                            </div>
                            <div>
                                <h3>Tidak Ada Refund Tertunda</h3>
                                <p>Seluruh permohonan pengembalian dana telah diproses dengan tuntas.</p>
                            </div>
                        </div>
                        <span class="metric-badge-status green">Terkendali</span>
                    </div>
                    <div class="alert-item" style="border: none; background: transparent; padding-left: 0;">
                        <span style="color: #61736b; font-size: 12px;">
                            Sistem akan otomatis memberi tahu Anda jika ada pelanggan yang mengajukan refund baru.
                        </span>
                    </div>
                </div>
            @endif

            {{-- PENGINGAT UJI KIR / PAJAK ARMADA & SIM DRIVER --}}
            @php
                $hasMaintenance = isset($maintenanceVehicles) && $maintenanceVehicles->count() > 0;
                $hasExpiringDrivers = isset($expiringDrivers) && $expiringDrivers->count() > 0;
                $hasExpiringVehicles = isset($expiringVehicles) && $expiringVehicles->count() > 0;
            @endphp

            @if($hasMaintenance || $hasExpiringDrivers || $hasExpiringVehicles)
                <div class="alert-card maintenance-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                ⚠️
                            </div>
                            <div>
                                <h3>Perhatian Kelayakan Armada & Driver</h3>
                                <p>Pengingat uji KIR armada, jatuh tempo pajak STNK, dan lisensi SIM driver</p>
                            </div>
                        </div>
                        <span class="alert-badge-count">
                            Perlu Cek
                        </span>
                    </div>

                    <div class="alert-item-list">
                        {{-- VEHICLES EXPIRING KIR / STNK --}}
                        @if($hasExpiringVehicles)
                            @foreach($expiringVehicles as $vExp)
                                @php
                                    $kirInfo = $vExp->kirStatus();
                                    $stnkInfo = $vExp->stnkStatus();
                                @endphp
                                @if($kirInfo['status'] === 'expired' || $kirInfo['status'] === 'expiring_soon')
                                    <div class="alert-item">
                                        <div class="alert-item-main">
                                            <div class="alert-item-code">
                                                🚐 Uji KIR: {{ $vExp->name }} ({{ $vExp->plate_number }})
                                            </div>
                                            <div class="alert-item-sub">
                                                Masa Berlaku: {{ $vExp->kir_expiry_date ? $vExp->kir_expiry_date->format('d/m/Y') : '-' }} &bull; Tipe: {{ $vExp->type }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($kirInfo['status'] === 'expired')
                                                <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                    KIR Kedaluwarsa
                                                </span>
                                            @else
                                                <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                    KIR Sisa {{ $kirInfo['days_left'] }} Hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($stnkInfo['status'] === 'expired' || $stnkInfo['status'] === 'expiring_soon')
                                    <div class="alert-item">
                                        <div class="alert-item-main">
                                            <div class="alert-item-code">
                                                📄 Pajak STNK: {{ $vExp->name }} ({{ $vExp->plate_number }})
                                            </div>
                                            <div class="alert-item-sub">
                                                Jatuh Tempo: {{ $vExp->stnk_tax_due_date ? $vExp->stnk_tax_due_date->format('d/m/Y') : '-' }} &bull; Tipe: {{ $vExp->type }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($stnkInfo['status'] === 'expired')
                                                <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                    Pajak Lewat
                                                </span>
                                            @else
                                                <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                    Pajak Sisa {{ $stnkInfo['days_left'] }} Hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- DRIVERS EXPIRING --}}
                        @if($hasExpiringDrivers)
                            @foreach($expiringDrivers as $drv)
                                @php
                                    $daysRemaining = now()->diffInDays($drv->license_expired_at, false);
                                @endphp
                                <div class="alert-item">
                                    <div class="alert-item-main">
                                        <div class="alert-item-code">
                                            🪪 SIM Driver: {{ $drv->user?->name ?? 'Driver' }}
                                        </div>
                                        <div class="alert-item-sub">
                                            No. Lisensi: {{ $drv->license_number }} &bull; Jatuh tempo: {{ $drv->license_expired_at ? $drv->license_expired_at->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                    <div>
                                        @if($daysRemaining < 0)
                                            <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                Kedaluwarsa
                                            </span>
                                        @else
                                            <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                Tersisa {{ $daysRemaining }} Hari
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- VEHICLES MAINTENANCE --}}
                        @if($hasMaintenance)
                            @foreach($maintenanceVehicles as $vh)
                                <div class="alert-item">
                                    <div class="alert-item-main">
                                        <div class="alert-item-code">
                                            🚐 {{ $vh->name }} ({{ $vh->plate_number }})
                                        </div>
                                        <div class="alert-item-sub">
                                            Status: {{ $vh->status }} &bull; Kapasitas {{ $vh->capacity }} Kursi
                                        </div>
                                    </div>
                                    <div>
                                        <span style="font-size: 10px; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                            Masuk Bengkel
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="alert-card-footer">
                        <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="alert-footer-link">
                            Pantau Kelayakan Armada &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="alert-card clean-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                🚐
                            </div>
                            <div>
                                <h3>Armada & Driver Dalam Kondisi Siap</h3>
                                <p>Semua lisensi SIM driver aktif dan seluruh armada dalam status layak operasi.</p>
                            </div>
                        </div>
                        <span class="metric-badge-status green">KIR & SIM Aman</span>
                    </div>
                    <div class="alert-item" style="border: none; background: transparent; padding-left: 0;">
                        <span style="color: #61736b; font-size: 12px;">
                            Tidak ada pengingat kedaluwarsa dokumen kendaraan atau driver dalam 30 hari ke depan.
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </div>


    {{-- =========================================
         2. JADWAL PERJALANAN TERDEKAT
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Jadwal Perjalanan Terdekat</h2>
                <p>Tabel ringkas rute keberangkatan, jam jalan, nama sopir, dan status keterisian kursi (load factor)</p>
            </div>
        </div>

        <div class="schedule-panel-card" style="margin-top: 14px;">
            <div class="schedule-panel-header">
                <div class="schedule-panel-title">
                    <span>Daftar Jadwal Keberangkatan</span>
                    <span class="schedule-badge-total">
                        {{ isset($upcomingSchedules) ? $upcomingSchedules->count() : 0 }} Jadwal
                    </span>
                </div>
                <a href="{{ route('dashboard.admin.rute.jadwal') }}" class="alert-footer-link">
                    Kelola Semua Rute & Jadwal &rarr;
                </a>
            </div>

            <div class="table-scroll-wrap">
                @if(isset($upcomingSchedules) && $upcomingSchedules->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Rute Perjalanan</th>
                                <th>Jam Jalan</th>
                                <th>Nama Sopir</th>
                                <th>Armada</th>
                                <th>Keterisian Kursi (Load Factor)</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingSchedules as $sch)
                                @php
                                    // 1. Rute
                                    $origin = $sch->route?->origin?->name ?? ($sch->travelRoute?->origin ?? 'Asal');
                                    $destination = $sch->route?->destination?->name ?? ($sch->travelRoute?->destination ?? 'Tujuan');

                                    // 2. Jam & Tanggal
                                    $timeStr = '-';
                                    if ($sch->departure_time) {
                                        $timeStr = date('H:i', strtotime($sch->departure_time)) . ' WIB';
                                    } elseif ($sch->departure_at) {
                                        $timeStr = $sch->departure_at->format('H:i') . ' WIB';
                                    }

                                    $dateStr = '-';
                                    if ($sch->departure_date) {
                                        $dateStr = $sch->departure_date->format('d M Y');
                                    } elseif ($sch->departure_at) {
                                        $dateStr = $sch->departure_at->format('d M Y');
                                    }

                                    // 3. Driver
                                    $driverName = $sch->driver?->user?->name;

                                    // 4. Armada
                                    $vehicleName = $sch->vehicle?->name ?? 'Minibus';
                                    $plateNumber = $sch->vehicle?->plate_number ?? '';

                                    // 5. Load Factor Calculation
                                    $capacity = (int) ($sch->vehicle?->capacity ?? 12);
                                    if ($capacity <= 0) $capacity = 12;

                                    // Terisi
                                    $available = $sch->available_seats;
                                    if ($available !== null) {
                                        $booked = max(0, $capacity - (int)$available);
                                    } else {
                                        $booked = (int) $sch->bookings->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])->sum('total_passenger');
                                    }

                                    $loadPercent = min(100, round(($booked / $capacity) * 100));

                                    $colorClass = 'safe';
                                    if ($loadPercent >= 90) {
                                        $colorClass = 'full';
                                    } elseif ($loadPercent >= 70) {
                                        $colorClass = 'medium';
                                    }
                                @endphp
                                <tr>
                                    {{-- RUTE --}}
                                    <td>
                                        <div class="route-cell">
                                            <span>{{ $origin }}</span>
                                            <span class="route-arrow">&rarr;</span>
                                            <span>{{ $destination }}</span>
                                        </div>
                                    </td>

                                    {{-- JAM JALAN --}}
                                    <td>
                                        <div class="time-cell">
                                            <span class="time-val">{{ $timeStr }}</span>
                                            <span class="time-date">{{ $dateStr }}</span>
                                        </div>
                                    </td>

                                    {{-- NAMA SOPIR --}}
                                    <td>
                                        <div class="driver-cell">
                                            @if($driverName)
                                                <div class="driver-avatar-mini">
                                                    {{ strtoupper(substr($driverName, 0, 1)) }}
                                                </div>
                                                <span class="driver-name-text">{{ $driverName }}</span>
                                            @else
                                                <span class="driver-unassigned">Belum Ditugaskan</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ARMADA --}}
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 2px;">
                                            <span style="font-weight: 600; color: #17221d;">{{ $vehicleName }}</span>
                                            @if($plateNumber)
                                                <span style="font-size: 11px; color: #72817a;">{{ $plateNumber }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- LOAD FACTOR --}}
                                    <td>
                                        <div class="load-factor-wrap">
                                            <div class="load-factor-meta">
                                                <span style="color: #26332c;">{{ $booked }} / {{ $capacity }} Kursi</span>
                                                <span style="font-weight: 700; color: {{ $loadPercent >= 90 ? '#dc2626' : ($loadPercent >= 70 ? '#d97706' : '#15803d') }};">
                                                    {{ $loadPercent }}%
                                                </span>
                                            </div>
                                            <div class="progress-bar-bg">
                                                <div class="progress-bar-fill {{ $colorClass }}" style="width: {{ $loadPercent }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- STATUS --}}
                                    <td style="text-align: right;">
                                        @if(in_array(strtolower($sch->status ?? ''), ['in_trip', 'on_trip', 'beroperasi', 'departed']))
                                            <span class="status-pill active-trip">
                                                ● Beroperasi
                                            </span>
                                        @else
                                            <span class="status-pill scheduled">
                                                Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-schedules-notice">
                        Belum ada jadwal perjalanan terdaftar untuk ditampilkan.
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- =========================================
         MENU CEPAT MANAJEMEN OPERASIONAL
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Akses Modul Administrasi</h2>
                <p>Navigasi cepat ke seluruh fungsi manajemen TravelKu</p>
            </div>
        </div>

        <div class="quick-nav-grid" style="margin-top: 14px;">

            {{-- KELOLA USER --}}
            <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">👥</div>
                <div class="quick-nav-text">
                    <h4>Kelola User</h4>
                    <p>{{ number_format($totalUsers ?? 0) }} akun pengguna</p>
                </div>
            </a>

            {{-- KELOLA STAFF --}}
            <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🧑‍💼</div>
                <div class="quick-nav-text">
                    <h4>Kelola Staff</h4>
                    <p>Hak akses & tiket kasir</p>
                </div>
            </a>

            {{-- KELOLA DRIVER --}}
            <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🪪</div>
                <div class="quick-nav-text">
                    <h4>Kelola Driver</h4>
                    <p>{{ number_format($totalDrivers ?? 0) }} sopir terdaftar</p>
                </div>
            </a>

            {{-- MEMANTAU KENDARAAN --}}
            <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🚐</div>
                <div class="quick-nav-text">
                    <h4>Memantau Kendaraan</h4>
                    <p>{{ number_format($totalVehicles ?? 0) }} unit armada</p>
                </div>
            </a>

            {{-- PEMESANAN --}}
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">💺</div>
                <div class="quick-nav-text">
                    <h4>Pemesanan</h4>
                    <p>{{ number_format($totalBookings ?? 0) }} total tiket</p>
                </div>
            </a>

            {{-- PEMBAYARAN --}}
            <a href="{{ route('dashboard.admin.pembayaran.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">💳</div>
                <div class="quick-nav-text">
                    <h4>Pembayaran</h4>
                    <p>Verifikasi & arus kas</p>
                </div>
            </a>

            {{-- RUTE PERJALANAN --}}
            <a href="{{ route('dashboard.admin.rute.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🕒</div>
                <div class="quick-nav-text">
                    <h4>Rute Perjalanan</h4>
                    <p>Jalur & tarif travel</p>
                </div>
            </a>

            {{-- KELOLA REFUND --}}
            <a href="#" class="quick-nav-item">
                <div class="quick-nav-icon">🔄</div>
                <div class="quick-nav-text">
                    <h4>Kelola Refund</h4>
                    <p>{{ $totalPendingRefunds ?? 0 }} perlu tindakan</p>
                </div>
            </a>

        </div>
    </div>

</div>

@endsection