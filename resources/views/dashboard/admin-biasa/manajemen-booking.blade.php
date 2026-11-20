<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Semua Booking (Manajemen Booking) - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @include('dashboard.admin-biasa.partials.styles')

    <style>
        :root {
            /* Type Badges */
            --online-bg: #e6fcf5;
            --online-text: #087f55;
            --online-border: #96f2d7;

            --offline-bg: #e7f5ff;
            --offline-text: #1971c2;
            --offline-border: #a5d8ff;
        }

        .content {
            padding: 24px 28px 48px;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.total { background: #f1f5f9; color: #475569; }
        .stat-icon.online { background: var(--online-bg); color: var(--online-text); border: 1px solid var(--online-border); }
        .stat-icon.offline { background: var(--offline-bg); color: var(--offline-text); border: 1px solid var(--offline-border); }
        .stat-icon.paid { background: #ecfdf5; color: #059669; }
        .stat-icon.cancelled { background: #fef2f2; color: #dc2626; }

        .stat-info span {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .stat-info strong {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
        }

        /* FILTER & TOOLBAR CARD */
        .filter-panel {
            background: var(--white);
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
        }

        .filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        /* TYPE PILL TABS */
        .type-pills {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }

        .type-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .type-pill:hover {
            color: var(--text-dark);
        }

        .type-pill.active {
            background: var(--white);
            color: var(--text-dark);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .type-pill.active-online {
            background: var(--online-bg);
            color: var(--online-text);
            box-shadow: 0 2px 6px rgba(8, 127, 85, 0.15);
        }

        .type-pill.active-offline {
            background: var(--offline-bg);
            color: var(--offline-text);
            box-shadow: 0 2px 6px rgba(25, 113, 194, 0.15);
        }

        .pill-counter {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.06);
            font-weight: 700;
        }

        /* FILTER FORM GRID */
        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr 1fr auto;
            gap: 12px;
            align-items: center;
        }

        @media (max-width: 1024px) {
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .search-box {
            position: relative;
        }

        .search-box svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 42px;
            padding: 0 14px 0 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            color: var(--text-dark);
            background: #ffffff;
            transition: all 0.2s;
        }

        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        }

        .filter-select {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            color: var(--text-dark);
            background: #ffffff;
            transition: all 0.2s;
        }

        .btn-filter-apply {
            height: 42px;
            padding: 0 18px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-filter-apply:hover {
            background: var(--primary-dark);
        }

        .btn-filter-reset {
            height: 42px;
            padding: 0 14px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-filter-reset:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* BIG TABLE CARD */
        .table-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .table-card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafcfb;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-card-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-card-badge {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 3px 10px;
            border-radius: 999px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .booking-table th {
            background: #f8fafc;
            padding: 13px 16px;
            font-weight: 800;
            color: #475569;
            border-bottom: 1.5px solid var(--border);
            white-space: nowrap;
            letter-spacing: 0.3px;
        }

        .booking-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
            color: var(--text-dark);
        }

        .booking-table tr:hover td {
            background: #fafcfb;
        }

        /* SPECIAL COLUMN BADGES: TIPE PESANAN */
        .badge-type-online {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: var(--online-bg);
            color: var(--online-text);
            border: 1.5px solid var(--online-border);
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .badge-type-offline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: var(--offline-bg);
            color: var(--offline-text);
            border: 1.5px solid var(--offline-border);
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        /* STATUS BADGES */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-status-paid {
            background: #e6fcf5;
            color: #087f55;
            border: 1px solid #96f2d7;
        }

        .badge-status-pending {
            background: #fff9db;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .badge-status-cancelled {
            background: #ffe3e3;
            color: #e03131;
            border: 1px solid #ffc9c9;
        }

        .badge-status-completed {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* SEAT PILLS */
        .seat-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 7px;
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-weight: 800;
            font-size: 11px;
            font-family: monospace;
            margin: 1px;
        }

        /* ACTION BUTTONS */
        .action-group {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .btn-tbl-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-tbl-action:hover {
            transform: translateY(-1px);
        }

        .btn-tbl-action.detail:hover {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #7dd3fc;
        }

        .btn-tbl-action.print:hover {
            background: #eefbf5;
            color: #087f55;
            border-color: #8fd3b4;
        }

        .btn-tbl-action.reschedule:hover {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fed7aa;
        }

        .btn-tbl-action.cancel:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        /* MODAL STYLES */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-dialog {
            background: #ffffff;
            border-radius: 18px;
            width: 100%;
            max-width: 620px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: scale(0.96);
            transition: transform 0.2s ease;
            position: relative;
        }

        .modal-overlay.active .modal-dialog {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 10;
        }

        .modal-header h3 {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #e2e8f0;
            color: var(--text-dark);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #fafcfb;
            position: sticky;
            bottom: 0;
        }

        .modal-section-title {
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-section-title:first-child {
            margin-top: 0;
        }

        .detail-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #f8fafc;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .detail-item span {
            display: block;
            font-size: 11.5px;
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 2px;
        }

        .detail-item strong {
            font-size: 13.5px;
            color: var(--text-dark);
            font-weight: 700;
        }

        /* PAGINATION */
        .pagination-container {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: #fafcfb;
        }

        .pagination-container svg {
            width: 16px;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
        }

        .pagination-container nav {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 72px;
            }
            .main-content {
                margin-left: 72px;
            }
            .content {
                padding: 16px;
            }
        }
    </style>
    @fonts('overpass')
    @vite(['resources/css/dashboard-staff.css'])
</head>

<body data-staff="staff">

<div class="dashboard-wrapper">

    <!-- SIDEBAR STAF -->
    @include('dashboard.admin-biasa.partials.sidebar')

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        @include('dashboard.admin-biasa.partials.topbar', [
            'topbarTitle' => 'Semua Booking (Manajemen Booking)',
            'topbarSubtitle' => 'Pusat pemantauan seluruh data transaksi tanpa membedakan asal usul pesanan (Online & Offline).',
        ])

        <!-- CONTENT SECTION -->
        <section class="content">

            <!-- FLASH ALERTS -->
            @if (session('success'))
                <div class="ds-alert ds-alert--success" role="status" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
                    <span>{{ session('success') }}</span>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @if (session('print_url'))
                            <a href="{{ session('print_url') }}" target="_blank" class="btn-filter-apply" style="text-decoration: none; height: 36px; font-size: 12.5px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 18 2 18 9" />
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                    <rect width="12" height="8" x="6" y="14" />
                                </svg>
                                <span>Cetak Struk / Tiket Fisik ({{ session('print_booking_code') }})</span>
                            </a>
                        @endif
                        @if ($scheduleId)
                            <a href="{{ route('dashboard.admin-biasa.booking') }}" class="btn-filter-reset" style="height: 36px; font-size: 12.5px; background: #ffffff;">
                                Tampilkan Semua Booking
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="ds-alert ds-alert--error" role="alert" style="margin-bottom: 20px;">
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="ds-alert ds-alert--error" role="alert" style="margin-bottom: 20px; display: grid; gap: 4px;">
                    <strong>Mohon periksa kembali isian formulir pesanan manual:</strong>
                    <ul style="padding-left: 18px; margin: 0;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. KARTU STATISTIK RINGKASAN -->
            <div class="stats-grid">
                <!-- Total Booking -->
                <div class="stat-card">
                    <div class="stat-icon total">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span>Semua Booking</span>
                        <strong>{{ number_format($stats['total']) }}</strong>
                    </div>
                </div>

                <!-- Booking Online (Hijau) -->
                <div class="stat-card">
                    <div class="stat-icon online">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: var(--online-text);">Tipe Online</span>
                        <strong style="color: var(--online-text);">{{ number_format($stats['online']) }}</strong>
                    </div>
                </div>

                <!-- Booking Offline / Loket (Biru) -->
                <div class="stat-card">
                    <div class="stat-icon offline">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: var(--offline-text);">Tipe Offline / Loket</span>
                        <strong style="color: var(--offline-text);">{{ number_format($stats['offline']) }}</strong>
                    </div>
                </div>

                <!-- Lunas / Berbayar -->
                <div class="stat-card">
                    <div class="stat-icon paid">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span>Sudah Lunas</span>
                        <strong style="color: #059669;">{{ number_format($stats['paid']) }}</strong>
                    </div>
                </div>

                <!-- Dibatalkan -->
                <div class="stat-card">
                    <div class="stat-icon cancelled">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span>Dibatalkan</span>
                        <strong style="color: #dc2626;">{{ number_format($stats['cancelled']) }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. FILTER & PENCARIAN -->
            <div class="filter-panel">
                <div class="filter-header">
                    <!-- Tab Pill Tipe Pesanan: Semua, Online, Offline -->
                    <div class="type-pills" role="tablist">
                        <a
                            href="{{ route('dashboard.admin-biasa.booking', array_merge(request()->except('page'), ['type' => 'all'])) }}"
                            class="type-pill {{ $type === 'all' ? 'active' : '' }}"
                        >
                            <span>Semua Tipe</span>
                            <span class="pill-counter">{{ $stats['total'] }}</span>
                        </a>

                        <a
                            href="{{ route('dashboard.admin-biasa.booking', array_merge(request()->except('page'), ['type' => 'online'])) }}"
                            class="type-pill {{ $type === 'online' ? 'active-online' : '' }}"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/></svg>
                            <span>Online (App / Web)</span>
                            <span class="pill-counter">{{ $stats['online'] }}</span>
                        </a>

                        <a
                            href="{{ route('dashboard.admin-biasa.booking', array_merge(request()->except('page'), ['type' => 'offline'])) }}"
                            class="type-pill {{ $type === 'offline' ? 'active-offline' : '' }}"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <span>Offline (Loket / Kasir)</span>
                            <span class="pill-counter">{{ $stats['offline'] }}</span>
                        </a>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                        <button type="button" onclick="openManualBookingModal()" class="btn-filter-apply">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Buat Pesanan Baru (Kasir)</span>
                        </button>
                        <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="btn-filter-reset" style="text-decoration: none;" title="Buka Halaman Kasir Loket Penuh">
                            <span>Halaman Kasir Penuh</span>
                        </a>
                    </div>
                </div>

                <!-- Form Search & Dropdown Filter -->
                <form method="GET" action="{{ route('dashboard.admin-biasa.booking') }}" class="filter-grid">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <!-- Kolom Pencarian Cepat -->
                    <div class="search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari kode booking, nama pemesan, no HP, rute..."
                            class="search-input"
                        >
                    </div>

                    <!-- Filter Status Transaksi -->
                    <div>
                        <select name="status" class="filter-select">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Sudah Lunas</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Bayar)</option>
                            <option value="checked_in" {{ in_array($status, ['checked_in', 'boarded']) ? 'selected' : '' }}>Sudah Boarding / Check-in</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Filter Tanggal Keberangkatan -->
                    <div>
                        <input
                            type="date"
                            name="date"
                            value="{{ $date }}"
                            class="filter-select"
                            title="Filter Tanggal Keberangkatan"
                        >
                    </div>

                    <!-- Filter Jadwal / Rute Spesifik -->
                    <div>
                        <select name="schedule_id" class="filter-select">
                            <option value="">Semua Jadwal Trip</option>
                            @foreach($scheduleList as $sched)
                                @php
                                    $sDate = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->format('d/m') : '';
                                    $sTime = $sched->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i') : '';
                                    $sOrigin = $sched->route?->origin?->name ?? 'Asal';
                                    $sDest = $sched->route?->destination?->name ?? 'Tujuan';
                                @endphp
                                <option value="{{ $sched->id }}" {{ ($scheduleId == $sched->id) ? 'selected' : '' }}>
                                    {{ $sDate }} {{ $sTime }} — {{ $sOrigin }} ke {{ $sDest }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi Filter -->
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn-filter-apply">
                            <span>Filter</span>
                        </button>
                        @if ($search || $type !== 'all' || $status !== 'all' || $scheduleId || $date)
                            <a href="{{ route('dashboard.admin-biasa.booking') }}" class="btn-filter-reset" title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 3. TABEL BESAR: SEMUA BOOKING -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18" />
                            <path d="M9 21V9" />
                        </svg>
                        <span>Daftar Seluruh Transaksi Booking</span>
                        <span class="table-card-badge">{{ $bookings->total() }} Data Ditemukan</span>
                    </div>

                    <div style="font-size: 12.5px; color: var(--text-light);">
                        Menampilkan halaman {{ $bookings->currentPage() }} dari {{ $bookings->lastPage() }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="booking-table" id="allBookingsTable">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Kode Booking</th>
                                <th style="min-width: 150px; text-align: center;">Tipe Pesanan</th>
                                <th style="min-width: 180px;">Pemesan &amp; Kontak</th>
                                <th style="min-width: 220px;">Rute &amp; Jadwal</th>
                                <th style="min-width: 160px;">Penumpang &amp; Kursi</th>
                                <th style="min-width: 150px;">Total &amp; Status</th>
                                <th style="min-width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($bookings as $b)
                                @php
                                    $isOffline = $b->isOffline();
                                    $sched = $b->schedule;
                                    $originName = $sched?->route?->origin?->name ?? 'Asal';
                                    $destName = $sched?->route?->destination?->name ?? 'Tujuan';
                                    $depDate = $sched?->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->translatedFormat('d M Y') : '-';
                                    $depTime = $sched?->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i') . ' WIB' : '-';
                                    $phoneRaw = $b->customerPhone();
                                    $cleanPhone = preg_replace('/[^0-9]/', '', (string)$phoneRaw);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $statusLower = strtolower((string)$b->status);
                                    $isCancelled = in_array($statusLower, ['cancelled', 'batal']);
                                    $isPaid = in_array($statusLower, ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed']);
                                @endphp

                                <tr id="row-booking-{{ $b->id }}">
                                    <!-- 1. KODE BOOKING & TANGGAL TRANSAKSI -->
                                    <td>
                                        <div style="font-family: monospace; font-weight: 800; font-size: 13.5px; color: var(--text-dark);">
                                            {{ $b->booking_code }}
                                        </div>
                                        <small style="color: var(--text-light); font-size: 11px; display: block; margin-top: 2px;">
                                            {{ $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-' }} WIB
                                        </small>
                                        @if($b->ticket)
                                            <span style="font-family: monospace; font-size: 10px; color: #0C5A3E; background: #eefbf5; padding: 1px 5px; border-radius: 4px; display: inline-block; margin-top: 3px;">
                                                {{ $b->ticket->ticket_number }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 2. INDIKATOR KOLOM KHUSUS: TIPE PESANAN (HIJAU = ONLINE, BIRU = OFFLINE/LOKET) -->
                                    <td style="text-align: center;">
                                        @if($isOffline)
                                            <span class="badge-type-offline" title="Pemesanan langsung di Loket / Kasir Fisik">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                    <polyline points="9 22 9 12 15 12 15 22" />
                                                </svg>
                                                <span>Offline (Loket)</span>
                                            </span>
                                        @else
                                            <span class="badge-type-online" title="Pemesanan online melalui Aplikasi Mobile atau Web">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20" />
                                                </svg>
                                                <span>Online (App)</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 3. PEMESAN & KONTAK -->
                                    <td>
                                        <strong style="display: block; font-size: 13.5px; color: var(--text-dark);">
                                            {{ $b->customerName() }}
                                        </strong>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                            <span style="font-size: 12px; color: var(--text-light); font-family: monospace;">{{ $phoneRaw }}</span>
                                            @if($cleanPhone !== '')
                                                <a
                                                    href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode("Halo Kak {$b->customerName()}, konfirmasi pesanan tiket TravelKu #{$b->booking_code} rute {$originName} - {$destName}.") }}"
                                                    target="_blank"
                                                    style="display: inline-flex; align-items: center; padding: 2px 6px; border-radius: 4px; background: #25d366; color: #ffffff; font-size: 10px; font-weight: 700;"
                                                    title="Hubungi WhatsApp"
                                                >
                                                    WA
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 4. RUTE & JADWAL PERJALANAN -->
                                    <td>
                                        <div style="font-weight: 800; color: #0C5A3E; font-size: 13px;">
                                            {{ $originName }} &rarr; {{ $destName }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text); margin-top: 2px;">
                                            {{ $depDate }} &bull; <strong>{{ $depTime }}</strong>
                                        </div>
                                        <small style="color: var(--text-light); font-size: 11px;">
                                            {{ $sched?->vehicle?->name ?? 'Armada' }} ({{ $sched?->vehicle?->plate_number ?? '-' }})
                                        </small>
                                    </td>

                                    <!-- 5. PENUMPANG & NOMOR KURSI -->
                                    <td>
                                        <div style="font-weight: 700; font-size: 12.5px; color: var(--text-dark); margin-bottom: 3px;">
                                            {{ $b->total_passenger }} Penumpang
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 3px;">
                                            @foreach($b->passengers as $p)
                                                <span class="seat-pill" title="{{ $p->name }} (Kursi {{ $p->seat?->seat_number ?? '-' }})">
                                                    {{ $p->seat?->seat_number ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @if($b->passengers->first())
                                            <small style="display: block; font-size: 11px; color: var(--text-light); margin-top: 3px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $b->passengers->first()->name }}
                                                @if($b->passengers->count() > 1)
                                                    +{{ $b->passengers->count() - 1 }} lainnya
                                                @endif
                                            </small>
                                        @endif
                                    </td>

                                    <!-- 6. TOTAL HARGA & STATUS PEMBAYARAN -->
                                    <td>
                                        <div style="font-weight: 800; font-size: 14px; color: var(--text-dark);">
                                            Rp {{ number_format((float)$b->total_price, 0, ',', '.') }}
                                        </div>
                                        <div style="margin-top: 4px;">
                                            @if($isCancelled)
                                                <span class="status-badge badge-status-cancelled">
                                                    Dibatalkan
                                                </span>
                                            @elseif($isPaid)
                                                <span class="status-badge badge-status-paid">
                                                    &check; Lunas
                                                </span>
                                            @else
                                                <span class="status-badge badge-status-pending">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 7. AKSI: DETAIL, CETAK ULANG TIKET, RESCHEDULE, BATALKAN -->
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- 1. LIHAT DETAIL PESANAN -->
                                            <button
                                                type="button"
                                                class="btn-tbl-action detail"
                                                onclick="openDetailModal({{ $b->id }})"
                                                title="Lihat Detail Lengkap Transaksi"
                                            >
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </button>

                                            <!-- 2. CETAK ULANG TIKET / STRUK FISIK -->
                                            <a
                                                href="{{ route('dashboard.admin-biasa.booking.print-ticket', $b->id) }}"
                                                target="_blank"
                                                class="btn-tbl-action print"
                                                title="Cetak Ulang Tiket / Struk Fisik"
                                            >
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="6 9 6 2 18 2 18 9" />
                                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                                    <rect width="12" height="8" x="6" y="14" />
                                                </svg>
                                            </a>

                                            <!-- 2.5 KONFIRMASI LUNAS CEPAT (UNTUK PESANAN PENDING) -->
                                            @if(!$isPaid && !$isCancelled)
                                                <form method="POST" action="{{ route('dashboard.admin-biasa.booking.settle', $b->id) }}" style="display: inline-flex; margin: 0;">
                                                    @csrf
                                                    <input type="hidden" name="payment_method" value="Cash">
                                                    <button
                                                        type="submit"
                                                        class="btn-tbl-action print"
                                                        style="background: #ecfdf5; color: #059669; border-color: #6ee7b7;"
                                                        title="Konfirmasi Lunas (Tunai / Loket)"
                                                    >
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="20 6 9 17 4 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- 3. UBAH JADWAL (RESCHEDULE) -->
                                            @if(!$isCancelled && $sched && $sched->departureCarbon()->isFuture())
                                                <button
                                                    type="button"
                                                    class="btn-tbl-action reschedule"
                                                    onclick="openRescheduleModal({{ $b->id }}, '{{ $b->booking_code }}')"
                                                    title="Ubah Jadwal Keberangkatan (Reschedule)"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                                        <path d="M3 3v5h5" />
                                                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                                        <path d="M16 21h5v-5" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- 4. MEMBATALKAN PESANAN -->
                                            @if(!$isCancelled)
                                                <button
                                                    type="button"
                                                    class="btn-tbl-action cancel"
                                                    onclick="openCancelModal({{ $b->id }}, '{{ $b->booking_code }}', '{{ $b->customerName() }}')"
                                                    title="Batalkan Pesanan Ini"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10" />
                                                        <line x1="15" y1="9" x2="9" y2="15" />
                                                        <line x1="9" y1="9" x2="15" y2="15" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 50px 20px; text-align: center; color: var(--text-light);">
                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="11" cy="11" r="8" />
                                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                            </svg>
                                            <strong style="font-size: 15px; color: var(--text-dark);">Tidak ada data transaksi booking ditemukan</strong>
                                            <span style="font-size: 13px;">Silakan sesuaikan kata kunci pencarian atau ganti filter tipe pesanan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION CONTROLS -->
                @if($bookings->hasPages())
                    <div class="pagination-container">
                        <div style="font-size: 13px; color: var(--text-light);">
                            Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} pesanan
                        </div>
                        <div>
                            {{ $bookings->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </section>

    </main>

</div>

<!-- =====================================================================================
     MODAL 1: LIHAT DETAIL PESANAN
====================================================================================== -->
<div class="modal-overlay" id="detailModalOverlay" onclick="if (event.target === this) closeModal('detailModalOverlay')">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                <span id="detailModalTitle">Detail Pesanan #TRV-</span>
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('detailModalOverlay')">&times;</button>
        </div>

        <div class="modal-body" id="detailModalBody">
            <div style="text-align: center; padding: 40px; color: var(--text-light);">
                Memuat rincian transaksi...
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-filter-reset" onclick="closeModal('detailModalOverlay')">Tutup</button>
            <a href="#" id="detailPrintBtn" target="_blank" class="btn-filter-apply" style="text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak Tiket / Struk</span>
            </a>
        </div>
    </div>
</div>

<!-- =====================================================================================
     MODAL 2: UBAH JADWAL (RESCHEDULE)
====================================================================================== -->
<div class="modal-overlay" id="rescheduleModalOverlay" onclick="if (event.target === this) closeModal('rescheduleModalOverlay')">
    <div class="modal-dialog">
        <form id="rescheduleForm" method="POST" onsubmit="submitReschedule(event)">
            @csrf
            <div class="modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #ea580c;">
                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                        <path d="M16 21h5v-5" />
                    </svg>
                    <span>Ubah Jadwal Tiket (Reschedule)</span>
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('rescheduleModalOverlay')">&times;</button>
            </div>

            <div class="modal-body">
                <div id="rescheduleCurrentInfo" style="background: #fff7ed; border: 1px solid #fed7aa; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 13px;">
                    <!-- Diisi via JS -->
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="newScheduleSelect" style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Pilih Jadwal Keberangkatan Baru:
                    </label>
                    <select id="newScheduleSelect" name="new_schedule_id" class="filter-select" required>
                        <option value="">-- Memuat Jadwal Alternatif... --</option>
                    </select>
                    <small style="color: var(--text-light); font-size: 11px; display: block; margin-top: 4px;">
                        Hanya jadwal aktif mendatang dengan rute yang sama dan kursi mencukupi yang ditampilkan.
                    </small>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="rescheduleReasonInput" style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Alasan Reschedule:
                    </label>
                    <input
                        type="text"
                        id="rescheduleReasonInput"
                        name="reschedule_reason"
                        class="search-input"
                        placeholder="Contoh: Permintaan pelanggan via telepon karena ada urusan mendadak"
                        required
                    >
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('rescheduleModalOverlay')">Batal</button>
                <button type="submit" id="rescheduleSubmitBtn" class="btn-filter-apply" style="background: #ea580c;">
                    <span>Konfirmasi Ubah Jadwal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================================
     MODAL 3: MEMBATALKAN PESANAN
====================================================================================== -->
<div class="modal-overlay" id="cancelModalOverlay" onclick="if (event.target === this) closeModal('cancelModalOverlay')">
    <div class="modal-dialog" style="max-width: 480px;">
        <form id="cancelBookingForm" method="POST" onsubmit="submitCancelBooking(event)">
            @csrf
            <div class="modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #dc2626;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="15" y1="9" x2="9" y2="15" />
                        <line x1="9" y1="9" x2="15" y2="15" />
                    </svg>
                    <span>Batalkan Pemesanan</span>
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('cancelModalOverlay')">&times;</button>
            </div>

            <div class="modal-body">
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; padding: 14px 16px; border-radius: 12px; margin-bottom: 16px;">
                    <p style="font-weight: 700; color: #991b1b; font-size: 13.5px; margin-bottom: 4px;">
                        Peringatan Pembatalan Tiket
                    </p>
                    <p style="font-size: 12.5px; color: #b91c1c; line-height: 1.4;">
                        Pesanan <strong id="cancelBookingCodeText">#TRV-</strong> atas nama <strong id="cancelCustomerNameText">-</strong> akan dibatalkan.
                        Kursi yang dipesan akan otomatis dilepaskan kembali ke sistem kuota armada.
                    </p>
                </div>

                <div style="margin-bottom: 12px;">
                    <label for="cancelReasonInput" style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Alasan Pembatalan:
                    </label>
                    <textarea
                        id="cancelReasonInput"
                        name="reason"
                        rows="3"
                        class="filter-select"
                        style="height: auto; padding: 10px; font-size: 13px;"
                        placeholder="Contoh: Penumpang membatalkan perjalanan secara langsung di loket pool..."
                        required
                    ></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('cancelModalOverlay')">Kembali</button>
                <button type="submit" id="cancelSubmitBtn" class="btn-filter-apply" style="background: #dc2626;">
                    <span>Ya, Batalkan Pesanan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================================
     MODAL 4: BUAT PESANAN MANUAL / BARU (KASIR LOKET) LANGSUNG DARI SEMUA BOOKING
====================================================================================== -->
<div class="modal-overlay" id="manualBookingModalOverlay" onclick="if (event.target === this) closeModal('manualBookingModalOverlay')">
    <div class="modal-dialog" style="max-width: 960px;">
        <form method="POST" action="{{ route('dashboard.admin-biasa.offline-booking.store') }}" id="manualBookingModalForm">
            @csrf
            <div class="modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>Buat Pesanan Manual / Baru (Kasir Loket)</span>
                </h3>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="btn-filter-reset" style="height: 32px; font-size: 12px; text-decoration: none;">
                        Buka Layar Kasir Penuh &nearr;
                    </a>
                    <button type="button" class="modal-close" onclick="closeModal('manualBookingModalOverlay')">&times;</button>
                </div>
            </div>

            <div class="modal-body">
                @php
                    $modalSchedules = $activeSchedules ?? collect();
                    $modalRoutes = $availableRoutes ?? collect();
                    $modalDefaultDate = $defaultBookingDate ?? now()->toDateString();
                @endphp

                @if ($modalSchedules->isEmpty())
                    <div style="padding: 28px; text-align: center; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                        <strong style="display: block; font-size: 15px; color: var(--text-dark); margin-bottom: 6px;">Belum ada jadwal keberangkatan aktif.</strong>
                        <p style="font-size: 13px; color: var(--text-light); margin-bottom: 14px;">Jadwal keberangkatan harus tersedia terlebih dahulu sebelum loket dapat melayani pemesanan tiket manual.</p>
                        <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="btn-filter-apply" style="text-decoration: none;">Buka Halaman Buat Pesanan Baru</a>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 20px; align-items: start;">
                        <!-- KOLOM KIRI: PILIH JADWAL & DENAH KURSI REAL-TIME -->
                        <div style="background: #f8faf9; border: 1.5px solid #d5e5de; border-radius: 14px; padding: 16px; display: grid; gap: 14px;">
                            <div style="font-weight: 800; font-size: 14px; color: #0C392F; border-bottom: 1px solid #d5e5de; padding-bottom: 8px;">
                                1. Pilih Jadwal &amp; Cek Ketersediaan Kursi
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label for="mb_filter_date" style="display: block; font-size: 12px; font-weight: 700; color: #0C392F; margin-bottom: 4px;">Tanggal Berangkat</label>
                                    <input type="date" id="mb_filter_date" class="filter-select" value="{{ $modalDefaultDate }}" onchange="mbFilterSchedules(true)">
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 5px;">
                                        <button type="button" onclick="mbSetDate('{{ now()->toDateString() }}')" style="font-size: 11px; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; font-weight: 700;">Hari Ini</button>
                                        <button type="button" onclick="mbSetDate('{{ now()->addDay()->toDateString() }}')" style="font-size: 11px; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; font-weight: 700;">Besok</button>
                                        <button type="button" onclick="mbSetDate('')" style="font-size: 11px; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; font-weight: 700;">Semua</button>
                                    </div>
                                </div>
                                <div>
                                    <label for="mb_filter_route" style="display: block; font-size: 12px; font-weight: 700; color: #0C392F; margin-bottom: 4px;">Rute / Tujuan</label>
                                    <select id="mb_filter_route" class="filter-select" onchange="mbFilterSchedules(true)">
                                        <option value="">Semua Rute / Tujuan</option>
                                        @foreach ($modalRoutes as $r)
                                            <option value="{{ $r->id }}">{{ $r->origin?->name ?? 'Asal' }} &rarr; {{ $r->destination?->name ?? 'Tujuan' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="mb_schedule_id" style="display: block; font-size: 12px; font-weight: 700; color: #0C392F; margin-bottom: 4px;">Pilih Jam Keberangkatan &amp; Armada <span style="color: #dc2626;">*</span></label>
                                <select name="schedule_id" id="mb_schedule_id" class="filter-select" onchange="mbLoadScheduleSeats(this.value)" required>
                                    @foreach ($modalSchedules as $sched)
                                        @php
                                            $sDateVal = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->toDateString() : '';
                                            $sDateText = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->translatedFormat('d M Y') : '-';
                                            $sTimeText = $sched->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i').' WIB' : '-';
                                            $sRouteText = ($sched->route?->origin?->name ?? 'Asal').' → '.($sched->route?->destination?->name ?? 'Tujuan');
                                            $sPriceText = 'Rp '.number_format((float) $sched->effectivePrice(), 0, ',', '.');
                                            $sAvail = (int) ($sched->available_seats ?? 10);
                                        @endphp
                                        <option
                                            value="{{ $sched->id }}"
                                            data-date="{{ $sDateVal }}"
                                            data-route-id="{{ $sched->route_id }}"
                                        >
                                            {{ $sTimeText }} ({{ $sDateText }}) — {{ $sRouteText }} • {{ $sched->vehicle?->name ?? 'Armada' }} (Sisa {{ $sAvail }} kursi) • {{ $sPriceText }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="mbNoScheduleText" style="display: none; color: #dc2626; font-size: 11.5px; margin-top: 4px;">
                                    Tidak ada jadwal untuk filter ini.
                                </small>
                            </div>

                            <!-- Bar Ketersediaan Kursi -->
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #eefbf5; border: 1px solid #8fd3b4; border-radius: 8px; font-size: 12px; color: #0C5A3E;">
                                <span>Sisa Kursi: <strong id="mbLiveAvailableCount">Memuat...</strong></span>
                                <span style="font-weight: 700; font-size: 11px;">Sinkron Online &amp; Loket</span>
                            </div>

                            <!-- Denah Kursi Interaktif -->
                            <div style="background: #ffffff; border: 2px solid #cbd5e1; border-radius: 20px 20px 12px 12px; padding: 14px; max-width: 320px; width: 100%; margin: 0 auto;">
                                <div style="padding: 6px; border-radius: 10px 10px 4px 4px; background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 800; text-align: center; text-transform: uppercase; margin-bottom: 10px;">
                                    Kaca Depan Armada (Pilih Kursi Kosong)
                                </div>
                                <div id="mbSeatGridBody" style="display: grid; gap: 8px;">
                                    <div style="text-align: center; padding: 20px; color: #64748b; font-size: 12px;">Memuat denah kursi...</div>
                                </div>
                            </div>

                            <!-- Ringkasan Kursi & Tarif -->
                            <div style="padding: 12px 14px; border-radius: 10px; background: #eef6f1; border: 1px solid #c6e2d2; display: grid; gap: 4px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Kursi dipilih:</span>
                                    <strong id="mbSummarySeatsText">Belum ada kursi dipilih</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 14.5px; color: #0C392F;">
                                    <span>Total Tarif:</span>
                                    <strong id="mbSummaryTotalText">Rp 0</strong>
                                </div>
                            </div>

                            <div id="mbSelectedSeatInputs"></div>
                        </div>

                        <!-- KOLOM KANAN: INPUT DATA PELANGGAN & PEMBAYARAN -->
                        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px; display: grid; gap: 12px;">
                            <div style="font-weight: 800; font-size: 14px; color: #0C392F; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                                2. Data Pelanggan &amp; Pembayaran Kasir
                            </div>

                            <div>
                                <label for="mb_source" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Saluran Pemesanan Loket</label>
                                <select name="source" id="mb_source" class="filter-select" required>
                                    <option value="offline">Datang Langsung di Loket / Go-Show (Offline)</option>
                                    <option value="whatsapp">Pesan via WhatsApp Pool / CS</option>
                                    <option value="phone">Pesan via Telepon Pool</option>
                                </select>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label for="mb_customer_name" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Nama Pelanggan <span style="color: #dc2626;">*</span></label>
                                    <input type="text" id="mb_customer_name" name="customer_name" class="filter-select" placeholder="Contoh: Budi Santoso" required maxlength="100" oninput="mbSyncPassengerNames()">
                                </div>
                                <div>
                                    <label for="mb_customer_phone" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Nomor HP / WA <span style="color: #dc2626;">*</span></label>
                                    <input type="tel" id="mb_customer_phone" name="customer_phone" class="filter-select" placeholder="081234567890" required maxlength="20" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, ''); mbSyncPassengerPhones();">
                                </div>
                            </div>

                            <div>
                                <label for="mb_customer_email" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Email Pelanggan (Opsional)</label>
                                <input type="email" id="mb_customer_email" name="customer_email" class="filter-select" placeholder="email@contoh.com" maxlength="100">
                            </div>

                            <div id="mbPassengersDetailSection" style="display: none; gap: 8px;">
                                <span style="font-size: 12px; font-weight: 700; color: #0C392F;">Rincian Penumpang per Kursi Terpilih:</span>
                                <div id="mbPassengerCardsList" style="display: grid; gap: 8px; max-height: 160px; overflow-y: auto;"></div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label for="mb_payment_method" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Metode Pembayaran</label>
                                    <select name="payment_method" id="mb_payment_method" class="filter-select" required>
                                        <option value="Cash">Tunai (Cash di Loket)</option>
                                        <option value="Transfer Bank">Transfer Langsung</option>
                                        <option value="QRIS">QRIS Loket Pool</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="mb_payment_status" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Status Pembayaran</label>
                                    <select name="payment_status" id="mb_payment_status" class="filter-select" onchange="mbTogglePaymentStatus()" required>
                                        <option value="paid">Konfirmasi Lunas (Terbitkan Tiket)</option>
                                        <option value="pending">Bayar Nanti (Pending 30 Menit)</option>
                                    </select>
                                </div>
                            </div>

                            <label id="mbAutoBoardWrap" style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 8px; background: #f8faf9; border: 1px solid #d5e5de; cursor: pointer; font-size: 12.5px; font-weight: 700; color: #0C392F;">
                                <input type="checkbox" name="auto_board" value="1">
                                <span>Langsung tandai <em>Boarded</em> (penumpang langsung naik armada)</span>
                            </label>

                            <div>
                                <label for="mb_notes" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Catatan / Titik Turun (Opsional)</label>
                                <input type="text" id="mb_notes" name="notes" class="filter-select" placeholder="Contoh: Turun dekat pintu tol / bawa koper" maxlength="500">
                            </div>

                            <div id="mbSeatHint" style="padding: 8px 12px; border-radius: 8px; background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; font-size: 12px; font-weight: 700;">
                                Klik minimal 1 nomor kursi kosong pada denah di sebelah kiri untuk mengaktifkan tombol Konfirmasi Lunas.
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('manualBookingModalOverlay')">Batal</button>
                @if ($modalSchedules->isNotEmpty())
                    <button type="submit" id="mbSubmitBtn" class="btn-filter-apply" disabled>
                        <span>Konfirmasi Lunas &amp; Buat Tiket</span>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================================
     SCRIPTS
====================================================================================== -->
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function closeModal(modalId) {
        document.getElementById(modalId)?.classList.remove('active');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal('detailModalOverlay');
            closeModal('rescheduleModalOverlay');
            closeModal('cancelModalOverlay');
            closeModal('manualBookingModalOverlay');
        }
    });

    // 1. DETAIL PESANAN
    async function openDetailModal(bookingId) {
        const overlay = document.getElementById('detailModalOverlay');
        const body = document.getElementById('detailModalBody');
        const title = document.getElementById('detailModalTitle');
        const printBtn = document.getElementById('detailPrintBtn');

        overlay.classList.add('active');
        body.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--text-light);">Memuat rincian transaksi...</div>';

        try {
            const res = await fetch("{{ url('/admin/booking') }}/" + bookingId + "/detail", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                body.innerHTML = '<div style="color: #dc2626; padding: 20px; text-align: center;">Gagal memuat rincian pesanan.</div>';
                return;
            }

            const b = data.booking;
            const sched = data.schedule;
            const pay = data.payment;
            const tkt = data.ticket;
            const passengers = data.passengers || [];

            title.innerText = `Detail Pesanan #${b.booking_code}`;
            printBtn.href = b.print_url;

            const badgeHtml = b.is_offline
                ? '<span class="badge-type-offline"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg> Offline (Loket)</span>'
                : '<span class="badge-type-online"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg> Online (App / Web)</span>';

            let passHtml = passengers.map((p, i) => `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 6px; font-size: 13px;">
                    <div>
                        <strong>${escapeHtml(p.name)}</strong>
                        <small style="color: #64748b; display: block;">Telp: ${escapeHtml(p.phone)}</small>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="seat-pill" style="font-size: 13px; padding: 3px 10px;">Kursi ${escapeHtml(p.seat_number)}</span>
                        <span style="font-size: 11px; font-weight: 700; color: ${p.is_boarded ? '#087f55' : '#b45309'};">
                            ${p.is_boarded ? '&check; Sudah Naik' : 'Belum Naik'}
                        </span>
                    </div>
                </div>
            `).join('');

            body.innerHTML = `
                <div class="modal-section-title">Informasi Pemesanan &amp; Asal Saluran</div>
                <div class="detail-info-grid">
                    <div class="detail-item">
                        <span>Kode Booking:</span>
                        <strong style="font-family: monospace;">${escapeHtml(b.booking_code)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Tipe Pesanan:</span>
                        <div>${badgeHtml}</div>
                    </div>
                    <div class="detail-item">
                        <span>Nama Pemesan:</span>
                        <strong>${escapeHtml(b.customer_name)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Nomor Telepon:</span>
                        <strong>${escapeHtml(b.customer_phone)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Status Transaksi:</span>
                        <strong style="color: ${b.status === 'Cancelled' ? '#dc2626' : '#087f55'};">${escapeHtml(b.status_label)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Waktu Transaksi:</span>
                        <strong style="font-size: 12px;">${escapeHtml(b.created_at)}</strong>
                    </div>
                </div>

                <div class="modal-section-title">Rute &amp; Jadwal Perjalanan</div>
                <div class="detail-info-grid">
                    <div class="detail-item">
                        <span>Rute:</span>
                        <strong style="color: #0C5A3E;">${escapeHtml(sched.route_name)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Waktu Keberangkatan:</span>
                        <strong>${escapeHtml(sched.departure_date)} &bull; ${escapeHtml(sched.departure_time)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Armada &amp; Plat:</span>
                        <strong>${escapeHtml(sched.vehicle_name)} (${escapeHtml(sched.plate_number)})</strong>
                    </div>
                    <div class="detail-item">
                        <span>Driver Bertugas:</span>
                        <strong>${escapeHtml(sched.driver_name)} (${escapeHtml(sched.driver_phone)})</strong>
                    </div>
                </div>

                <div class="modal-section-title">Manifes Penumpang (${passengers.length} Orang)</div>
                <div>${passHtml || '<p style="color: #64748b;">Belum ada manifes penumpang.</p>'}</div>

                <div class="modal-section-title">Rincian Pembayaran &amp; Tiket</div>
                <div class="detail-info-grid">
                    <div class="detail-item">
                        <span>Nomor Tiket Fisik:</span>
                        <strong style="font-family: monospace; color: #0C5A3E;">${escapeHtml(tkt.ticket_number)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Metode Pembayaran:</span>
                        <strong>${escapeHtml(pay.method)} (${escapeHtml(pay.status)})</strong>
                    </div>
                    <div class="detail-item">
                        <span>Total Pembayaran:</span>
                        <strong style="font-size: 16px; color: #087f55;">${escapeHtml(b.formatted_total)}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Catatan Khusus:</span>
                        <strong style="font-size: 12px; font-weight: normal;">${escapeHtml(b.notes)}</strong>
                    </div>
                </div>
            `;
        } catch (err) {
            console.error(err);
            body.innerHTML = '<div style="color: #dc2626; padding: 20px; text-align: center;">Gagal menghubungi server.</div>';
        }
    }

    // 2. RESCHEDULE MODAL
    let currentRescheduleBookingId = null;

    async function openRescheduleModal(bookingId, bookingCode) {
        currentRescheduleBookingId = bookingId;
        const overlay = document.getElementById('rescheduleModalOverlay');
        const form = document.getElementById('rescheduleForm');
        const currentInfo = document.getElementById('rescheduleCurrentInfo');
        const select = document.getElementById('newScheduleSelect');

        overlay.classList.add('active');
        form.action = "{{ url('/admin/booking') }}/" + bookingId + "/reschedule";
        currentInfo.innerHTML = `Memuat informasi jadwal tiket <strong>#${escapeHtml(bookingCode)}</strong>...`;
        select.innerHTML = '<option value="">-- Memuat Jadwal Alternatif... --</option>';

        try {
            const res = await fetch("{{ url('/admin/booking') }}/" + bookingId + "/reschedule-data", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                currentInfo.innerHTML = '<span style="color: #dc2626;">Gagal memuat opsi reschedule.</span>';
                return;
            }

            const b = data.booking;
            const schedules = data.available_schedules || [];

            currentInfo.innerHTML = `
                <div><strong>Kode Booking:</strong> #${escapeHtml(b.booking_code)} (${b.total_passenger} Penumpang)</div>
                <div><strong>Jadwal Saat Ini:</strong> ${escapeHtml(b.current_schedule)}</div>
                <div><strong>Kursi Saat Ini:</strong> ${escapeHtml(b.current_seats)}</div>
            `;

            if (schedules.length === 0) {
                select.innerHTML = '<option value="">-- Tidak ada jadwal alternatif rute yang sama di masa mendatang --</option>';
                select.disabled = true;
            } else {
                select.disabled = false;
                select.innerHTML = '<option value="">-- Pilih Jadwal Baru --</option>' + schedules.map(s => `
                    <option value="${s.id}">
                        ${escapeHtml(s.date)} ${escapeHtml(s.time)} — ${escapeHtml(s.route_name)} (${escapeHtml(s.vehicle_name)} - Sisa ${s.available_seats} Kursi)
                    </option>
                `).join('');
            }
        } catch (err) {
            console.error(err);
            currentInfo.innerHTML = '<span style="color: #dc2626;">Gagal menghubungi server.</span>';
        }
    }

    async function submitReschedule(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('rescheduleSubmitBtn');
        const select = document.getElementById('newScheduleSelect');
        const reason = document.getElementById('rescheduleReasonInput').value;

        if (!select.value) {
            alert('Silakan pilih jadwal keberangkatan baru terlebih dahulu.');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    new_schedule_id: select.value,
                    reschedule_reason: reason
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Jadwal tiket berhasil diubah!');
                window.location.reload();
            } else {
                alert(data.message || 'Gagal mengubah jadwal tiket.');
                btn.disabled = false;
                btn.innerText = 'Konfirmasi Ubah Jadwal';
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerText = 'Konfirmasi Ubah Jadwal';
        }
    }

    // 3. CANCEL MODAL
    let currentCancelBookingId = null;

    function openCancelModal(bookingId, bookingCode, customerName) {
        currentCancelBookingId = bookingId;
        const overlay = document.getElementById('cancelModalOverlay');
        const form = document.getElementById('cancelBookingForm');
        document.getElementById('cancelBookingCodeText').innerText = `#${bookingCode}`;
        document.getElementById('cancelCustomerNameText').innerText = customerName;
        document.getElementById('cancelReasonInput').value = '';
        form.action = "{{ url('/admin/booking') }}/" + bookingId + "/batal";
        overlay.classList.add('active');
    }

    async function submitCancelBooking(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('cancelSubmitBtn');
        const reason = document.getElementById('cancelReasonInput').value;

        btn.disabled = true;
        btn.innerText = 'Membatalkan...';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ reason: reason })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Pesanan berhasil dibatalkan.');
                window.location.reload();
            } else {
                alert(data.message || 'Gagal membatalkan pesanan.');
                btn.disabled = false;
                btn.innerText = 'Ya, Batalkan Pesanan';
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerText = 'Ya, Batalkan Pesanan';
        }
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // 4. MODAL BUAT PESANAN MANUAL / BARU (KASIR LOKET)
    const mbSeatsUrlTemplate = @json(route('dashboard.admin-biasa.offline-booking.seats', ['schedule' => '__SCHED_ID__']));
    const mbSchedulesUrl = @json(route('dashboard.admin-biasa.offline-booking'));
    let mbSelectedSeats = new Map();
    let mbUnitPrice = 0;
    let mbInitialized = false;

    function openManualBookingModal() {
        const overlay = document.getElementById('manualBookingModalOverlay');
        if (!overlay) return;
        overlay.classList.add('active');

        if (!mbInitialized) {
            mbInitialized = true;
            mbFilterSchedules(false);
        }
    }

    function mbSetDate(dateStr) {
        const dateEl = document.getElementById('mb_filter_date');
        if (dateEl) {
            dateEl.value = dateStr;
            mbFilterSchedules(true);
        }
    }

    async function mbFilterSchedules(fetchIfEmpty = false) {
        const dateVal = document.getElementById('mb_filter_date')?.value || '';
        const routeVal = document.getElementById('mb_filter_route')?.value || '';
        const selectEl = document.getElementById('mb_schedule_id');
        if (!selectEl) return;

        const options = Array.from(selectEl.options);
        let firstVisibleValue = null;
        let visibleCount = 0;

        options.forEach(opt => {
            const oDate = opt.dataset.date || '';
            const oRoute = opt.dataset.routeId || '';
            const matchDate = !dateVal || oDate === dateVal;
            const matchRoute = !routeVal || oRoute === routeVal;

            if (matchDate && matchRoute) {
                opt.hidden = false;
                opt.disabled = false;
                visibleCount++;
                if (!firstVisibleValue) firstVisibleValue = opt.value;
            } else {
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        if (visibleCount === 0 && !fetchIfEmpty && dateVal && options.length > 0) {
            const dateEl = document.getElementById('mb_filter_date');
            if (dateEl) {
                dateEl.value = '';
                return mbFilterSchedules(false);
            }
        }

        if (visibleCount === 0 && fetchIfEmpty && dateVal) {
            try {
                const params = new URLSearchParams();
                if (dateVal) params.set('date', dateVal);
                if (routeVal) params.set('route_id', routeVal);

                const res = await fetch(`${mbSchedulesUrl}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (res.ok && data.success && Array.isArray(data.schedules) && data.schedules.length > 0) {
                    data.schedules.forEach(sched => {
                        if (!selectEl.querySelector(`option[value="${sched.id}"]`)) {
                            const opt = document.createElement('option');
                            opt.value = sched.id;
                            opt.dataset.date = sched.date_val;
                            opt.dataset.routeId = String(sched.route_id);
                            opt.textContent = `${sched.time_text} (${sched.date_text}) — ${sched.route_text} • ${sched.vehicle_name} (Sisa ${sched.available_seats} kursi) • ${sched.price_text}`;
                            selectEl.appendChild(opt);
                        }
                    });
                    return mbFilterSchedules(false);
                }
            } catch (e) {
                console.error(e);
            }
        }

        const noText = document.getElementById('mbNoScheduleText');
        if (noText) {
            noText.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        if (firstVisibleValue) {
            selectEl.value = firstVisibleValue;
            mbLoadScheduleSeats(firstVisibleValue);
        }
    }

    async function mbLoadScheduleSeats(scheduleId) {
        if (!scheduleId) return;

        mbSelectedSeats.clear();
        mbRenderSelectionState();

        const container = document.getElementById('mbSeatGridBody');
        if (!container) return;
        container.innerHTML = '<div style="text-align: center; padding: 20px; color: #64748b; font-size: 12px;">Memuat denah kursi...</div>';

        try {
            const url = mbSeatsUrlTemplate.replace('__SCHED_ID__', scheduleId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            if (!res.ok || !data.success) {
                container.innerHTML = `<div style="color: #dc2626; text-align: center; font-size: 12px;">${escapeHtml(data.message || 'Gagal memuat kursi.')}</div>`;
                return;
            }

            mbUnitPrice = Number(data.schedule.price || 0);
            const availEl = document.getElementById('mbLiveAvailableCount');
            if (availEl) {
                availEl.textContent = `${data.schedule.available_seats} dari ${data.schedule.capacity} Kursi (${data.schedule.occupied_seats} Terisi Online & Loket)`;
            }

            mbRenderSeatGrid(data.seats);
        } catch (err) {
            console.error(err);
            container.innerHTML = '<div style="color: #dc2626; text-align: center; font-size: 12px;">Gagal memuat data kursi armada.</div>';
        }
    }

    function mbRenderSeatGrid(seats) {
        const container = document.getElementById('mbSeatGridBody');
        if (!container) return;
        container.innerHTML = '';

        const rows = {};
        seats.forEach(s => {
            const r = String(s.seat_row || '1');
            if (!rows[r]) rows[r] = [];
            rows[r].push(s);
        });

        Object.keys(rows).sort((a, b) => Number(a) - Number(b)).forEach(rowNum => {
            const rowDiv = document.createElement('div');
            rowDiv.style.display = 'grid';
            rowDiv.style.gridTemplateColumns = 'repeat(4, 1fr)';
            rowDiv.style.gap = '6px';
            rowDiv.style.alignItems = 'center';

            const rowSeats = rows[rowNum].sort((a, b) => a.seat_column - b.seat_column);

            for (let col = 1; col <= 4; col++) {
                if (rowNum === '1' && col === 4) {
                    const driverEl = document.createElement('div');
                    driverEl.style.minHeight = '44px';
                    driverEl.style.borderRadius = '8px';
                    driverEl.style.background = '#22403A';
                    driverEl.style.color = '#ffffff';
                    driverEl.style.fontSize = '10px';
                    driverEl.style.fontWeight = '800';
                    driverEl.style.display = 'grid';
                    driverEl.style.placeItems = 'center';
                    driverEl.textContent = 'SOPIR';
                    rowDiv.appendChild(driverEl);
                    continue;
                }

                const seat = rowSeats.find(item => Number(item.seat_column) === col);
                if (!seat) {
                    rowDiv.appendChild(document.createElement('div'));
                    continue;
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.seatId = String(seat.id);
                btn.dataset.seatNum = seat.seat_number;
                btn.style.minHeight = '44px';
                btn.style.borderRadius = '8px';
                btn.style.fontWeight = '800';
                btn.style.fontSize = '12.5px';
                btn.style.display = 'flex';
                btn.style.flexDirection = 'column';
                btn.style.alignItems = 'center';
                btn.style.justifyContent = 'center';

                if (seat.status === 'available') {
                    btn.style.background = '#eefbf5';
                    btn.style.border = '2px solid #8fd3b4';
                    btn.style.color = '#0C5A3E';
                    btn.style.cursor = 'pointer';
                    btn.innerHTML = `<span>${escapeHtml(seat.seat_number)}</span><small style="font-size: 9px; font-weight: 600;">Kosong</small>`;
                    btn.onclick = () => mbToggleSeat(btn);
                } else {
                    btn.disabled = true;
                    btn.style.background = '#f1f5f9';
                    btn.style.border = '2px solid #cbd5e1';
                    btn.style.color = '#64748b';
                    btn.style.cursor = 'not-allowed';
                    const tag = seat.status === 'paid' ? 'Terisi' : 'Ditahan';
                    btn.title = `${seat.passenger_name || 'Terisi'} (${seat.channel || 'Booking'})`;
                    btn.innerHTML = `<span>${escapeHtml(seat.seat_number)}</span><small style="font-size: 9px; font-weight: 600;">${tag}</small>`;
                }

                rowDiv.appendChild(btn);
            }

            container.appendChild(rowDiv);
        });
    }

    function mbToggleSeat(btn) {
        const id = btn.dataset.seatId;
        const num = btn.dataset.seatNum;

        if (mbSelectedSeats.has(id)) {
            mbSelectedSeats.delete(id);
            btn.style.background = '#eefbf5';
            btn.style.borderColor = '#8fd3b4';
            btn.style.color = '#0C5A3E';
        } else {
            mbSelectedSeats.set(id, num);
            btn.style.background = '#0C7950';
            btn.style.borderColor = '#0C392F';
            btn.style.color = '#ffffff';
        }

        mbRenderSelectionState();
    }

    function mbRenderSelectionState() {
        const summarySeats = document.getElementById('mbSummarySeatsText');
        const summaryTotal = document.getElementById('mbSummaryTotalText');
        const inputsWrap = document.getElementById('mbSelectedSeatInputs');
        const cardsSection = document.getElementById('mbPassengersDetailSection');
        const cardsList = document.getElementById('mbPassengerCardsList');
        const submitBtn = document.getElementById('mbSubmitBtn');
        const hintEl = document.getElementById('mbSeatHint');

        if (!summarySeats || !inputsWrap) return;

        inputsWrap.innerHTML = '';
        if (cardsList) cardsList.innerHTML = '';

        if (mbSelectedSeats.size === 0) {
            summarySeats.textContent = 'Belum ada kursi dipilih';
            summaryTotal.textContent = 'Rp 0';
            if (cardsSection) cardsSection.style.display = 'none';
            if (submitBtn) submitBtn.disabled = true;
            if (hintEl) hintEl.style.display = 'block';
            return;
        }

        const seatNumbers = Array.from(mbSelectedSeats.values());
        const totalTariff = mbSelectedSeats.size * mbUnitPrice;

        summarySeats.textContent = seatNumbers.join(', ') + ` (${mbSelectedSeats.size} kursi)`;
        summaryTotal.textContent = 'Rp ' + totalTariff.toLocaleString('id-ID');
        if (cardsSection) cardsSection.style.display = 'grid';
        if (submitBtn) submitBtn.disabled = false;
        if (hintEl) hintEl.style.display = 'none';

        const defaultName = document.getElementById('mb_customer_name')?.value.trim() || '';
        const defaultPhone = document.getElementById('mb_customer_phone')?.value.trim() || '';

        let idx = 0;
        mbSelectedSeats.forEach((seatNum, seatId) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'selected_seats[]';
            hidden.value = seatId;
            inputsWrap.appendChild(hidden);

            if (cardsList) {
                const card = document.createElement('div');
                card.style.padding = '10px';
                card.style.borderRadius = '8px';
                card.style.border = '1px solid #cbd5e1';
                card.style.background = '#f8fafc';
                card.style.display = 'grid';
                card.style.gap = '6px';
                const suggestedName = idx === 0 ? defaultName : (defaultName ? `${defaultName} (${idx + 1})` : '');
                card.innerHTML = `
                    <strong style="font-size: 11.5px; color: #0C392F;">Penumpang Kursi ${escapeHtml(seatNum)}</strong>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                        <input type="text" name="passengers[${seatId}][name]" class="filter-select" style="height: 36px; font-size: 12.5px;" data-auto-synced="true" oninput="this.dataset.autoSynced = 'false'" value="${escapeHtml(suggestedName)}" placeholder="Nama penumpang kursi ${escapeHtml(seatNum)}" required maxlength="100">
                        <input type="tel" name="passengers[${seatId}][phone]" class="filter-select" style="height: 36px; font-size: 12.5px;" data-auto-synced="true" value="${escapeHtml(defaultPhone)}" placeholder="No. HP (opsional)" maxlength="20" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.dataset.autoSynced = 'false'">
                    </div>
                `;
                cardsList.appendChild(card);
            }
            idx++;
        });
    }

    function mbSyncPassengerNames() {
        const defaultName = document.getElementById('mb_customer_name')?.value.trim() || '';
        const inputs = document.querySelectorAll('#mbPassengerCardsList input[name$="[name]"]');
        inputs.forEach((input, idx) => {
            if (input.dataset.autoSynced === 'true' || !input.value.trim()) {
                input.value = idx === 0 ? defaultName : (defaultName ? `${defaultName} (${idx + 1})` : '');
                input.dataset.autoSynced = 'true';
            }
        });
    }

    function mbSyncPassengerPhones() {
        const defaultPhone = document.getElementById('mb_customer_phone')?.value.trim() || '';
        const inputs = document.querySelectorAll('#mbPassengerCardsList input[name$="[phone]"]');
        inputs.forEach(input => {
            if (input.dataset.autoSynced === 'true' || !input.value.trim()) {
                input.value = defaultPhone;
                input.dataset.autoSynced = 'true';
            }
        });
    }

    function mbTogglePaymentStatus() {
        const status = document.getElementById('mb_payment_status')?.value;
        const wrap = document.getElementById('mbAutoBoardWrap');
        const submitBtn = document.getElementById('mbSubmitBtn');
        if (submitBtn) {
            submitBtn.innerHTML = status === 'paid'
                ? '<span>Konfirmasi Lunas &amp; Buat Tiket</span>'
                : '<span>Simpan Pesanan Pending (30 Menit)</span>';
        }
        if (wrap) {
            wrap.style.display = status === 'paid' ? 'flex' : 'none';
            if (status !== 'paid') {
                const cb = wrap.querySelector('input[type="checkbox"]');
                if (cb) cb.checked = false;
            }
        }
    }
</script>

@include('components.numeric-input-enforcer')
</body>

</html>
