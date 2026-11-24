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

        /* STATS CARDS (5 KARTU SEJAJAR & INTERAKTIF) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 14px;
            margin-bottom: 22px;
        }

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .stat-card {
            background: var(--white);
            border-radius: 14px;
            padding: 16px 18px;
            border: 1.5px solid var(--border);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: center;
            justify-content: flex-start !important;
            gap: 14px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            text-decoration: none;
            cursor: pointer;
            position: relative;
            min-width: 0;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
            border-color: #94a3b8;
        }

        .stat-card.is-active-total {
            border-color: #334155;
            background: #f8fafc;
            box-shadow: 0 0 0 3px rgba(51, 65, 85, 0.12);
        }

        .stat-card.is-active-online {
            border-color: #087f55;
            background: #f2fdf9;
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.14);
        }

        .stat-card.is-active-offline {
            border-color: #1971c2;
            background: #f0f8ff;
            box-shadow: 0 0 0 3px rgba(25, 113, 194, 0.14);
        }

        .stat-card.is-active-paid {
            border-color: #059669;
            background: #f0fdf4;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.14);
        }

        .stat-card.is-active-cancelled {
            border-color: #dc2626;
            background: #fff5f5;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.14);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.total { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .stat-icon.online { background: var(--online-bg); color: var(--online-text); border: 1px solid var(--online-border); }
        .stat-icon.offline { background: var(--offline-bg); color: var(--offline-text); border: 1px solid var(--offline-border); }
        .stat-icon.paid { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .stat-icon.cancelled { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        .stat-info {
            min-width: 0;
            flex: 1;
        }

        .stat-info span {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-info strong {
            display: block;
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
            grid-template-columns: minmax(195px, 1.3fr) minmax(175px, 1.05fr) minmax(152px, 0.92fr) minmax(205px, 1.3fr) auto;
            gap: 10px;
            align-items: end;
        }

        @media (max-width: 1180px) {
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
        }

        .filter-field-label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            padding-left: 2px;
            white-space: nowrap;
        }

        .filter-control-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .filter-control-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            flex-shrink: 0;
        }

        .search-box {
            position: relative;
        }

        .search-box svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 42px;
            padding: 0 12px 0 36px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            background: #ffffff;
            transition: all 0.2s;
            text-overflow: ellipsis;
        }

        .search-input::placeholder {
            font-weight: 500;
            color: #64748b;
        }

        .search-input:focus,
        .filter-select:focus,
        .filter-date:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        }

        .filter-select {
            width: 100%;
            height: 42px;
            padding: 0 30px 0 12px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px 14px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-select.has-icon {
            padding-left: 34px;
        }

        .filter-date {
            width: 100%;
            height: 42px;
            padding: 0 10px 0 34px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            color: var(--text-dark);
            background-color: #ffffff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-filter-apply {
            height: 42px;
            padding: 0 16px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .btn-filter-apply:hover {
            background: var(--primary-dark);
        }

        .btn-filter-reset {
            height: 42px;
            padding: 0 13px;
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
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

            <!-- 1. KARTU STATISTIK RINGKASAN (INTERAKTIF & SINKRON DENGAN DATA TABEL) -->
            @php
                $baseFilterParams = array_filter([
                    'search' => $search,
                    'date' => $date,
                    'schedule_id' => $scheduleId,
                    'destination_id' => $destinationId ?? null,
                ], fn ($val) => $val !== null && $val !== '');

                $isAllActive = $type === 'all' && $status === 'all';
                $isOnlineActive = $type === 'online' && $status === 'all';
                $isOfflineActive = $type === 'offline' && $status === 'all';
                $isPaidActive = $status === 'paid';
                $isCancelledActive = $status === 'cancelled';

                $pCounts = $pillCounts ?? [
                    'total' => $stats['total'],
                    'online' => $stats['online'],
                    'offline' => $stats['offline'],
                ];
            @endphp

            <div class="stats-grid">
                <!-- Total Booking -->
                <a
                    href="{{ route('dashboard.admin-biasa.booking', array_merge($baseFilterParams, ['type' => 'all', 'status' => 'all'])) }}"
                    class="stat-card {{ $isAllActive ? 'is-active-total' : '' }}"
                    title="Tampilkan Semua Booking ({{ number_format($stats['total']) }} data)"
                >
                    <div class="stat-icon total">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span>Semua Booking</span>
                        <strong>{{ number_format($stats['total']) }}</strong>
                    </div>
                </a>

                <!-- Booking Online (Hijau) -->
                <a
                    href="{{ route('dashboard.admin-biasa.booking', array_merge($baseFilterParams, ['type' => 'online', 'status' => 'all'])) }}"
                    class="stat-card {{ $isOnlineActive ? 'is-active-online' : '' }}"
                    title="Filter Tipe Online ({{ number_format($stats['online']) }} data)"
                >
                    <div class="stat-icon online">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: var(--online-text);">Tipe Online</span>
                        <strong style="color: var(--online-text);">{{ number_format($stats['online']) }}</strong>
                    </div>
                </a>

                <!-- Booking Offline / Loket (Biru) -->
                <a
                    href="{{ route('dashboard.admin-biasa.booking', array_merge($baseFilterParams, ['type' => 'offline', 'status' => 'all'])) }}"
                    class="stat-card {{ $isOfflineActive ? 'is-active-offline' : '' }}"
                    title="Filter Tipe Offline / Loket ({{ number_format($stats['offline']) }} data)"
                >
                    <div class="stat-icon offline">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: var(--offline-text);">Tipe Offline / Loket</span>
                        <strong style="color: var(--offline-text);">{{ number_format($stats['offline']) }}</strong>
                    </div>
                </a>

                <!-- Lunas / Berbayar -->
                <a
                    href="{{ route('dashboard.admin-biasa.booking', array_merge($baseFilterParams, ['type' => 'all', 'status' => 'paid'])) }}"
                    class="stat-card {{ $isPaidActive ? 'is-active-paid' : '' }}"
                    title="Filter Sudah Lunas ({{ number_format($stats['paid']) }} data)"
                >
                    <div class="stat-icon paid">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: #059669;">Sudah Lunas</span>
                        <strong style="color: #059669;">{{ number_format($stats['paid']) }}</strong>
                    </div>
                </a>

                <!-- Dibatalkan -->
                <a
                    href="{{ route('dashboard.admin-biasa.booking', array_merge($baseFilterParams, ['type' => 'all', 'status' => 'cancelled'])) }}"
                    class="stat-card {{ $isCancelledActive ? 'is-active-cancelled' : '' }}"
                    title="Filter Dibatalkan ({{ number_format($stats['cancelled']) }} data)"
                >
                    <div class="stat-icon cancelled">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span style="color: #dc2626;">Dibatalkan</span>
                        <strong style="color: #dc2626;">{{ number_format($stats['cancelled']) }}</strong>
                    </div>
                </a>
            </div>

            <!-- 2. FILTER & PENCARIAN -->
            <div class="filter-panel">
                <!-- Form Search & Dropdown Filter -->
                <form method="GET" action="{{ route('dashboard.admin-biasa.booking') }}" class="filter-grid" autocomplete="off">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <!-- 1. Kolom Pencarian Cepat -->
                    <div class="filter-field">
                        <label class="filter-field-label" for="booking_search_input">Pencarian Cepat</label>
                        <div class="search-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input
                                type="text"
                                id="booking_search_input"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari kode, nama, no HP, rute..."
                                class="search-input"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- 2. Filter Status Transaksi -->
                    @php
                        $sCounts = $statusCounts ?? [
                            'all' => $stats['total'],
                            'paid' => $stats['paid'],
                            'pending' => $stats['pending'],
                            'checked_in' => $stats['checked_in'] ?? 0,
                            'completed' => $stats['completed'] ?? 0,
                            'cancelled' => $stats['cancelled'],
                        ];
                    @endphp
                    <div class="filter-field">
                        <label class="filter-field-label" for="booking_status_select">Status Transaksi</label>
                        <div class="filter-control-wrap">
                            <svg class="filter-control-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            <select id="booking_status_select" name="status" class="filter-select has-icon" onchange="this.form.submit()">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status ({{ $sCounts['all'] }})</option>
                                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Sudah Lunas ({{ $sCounts['paid'] }})</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending ({{ $sCounts['pending'] }})</option>
                                <option value="checked_in" {{ in_array($status, ['checked_in', 'boarded']) ? 'selected' : '' }}>Sudah Boarding ({{ $sCounts['checked_in'] }})</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai ({{ $sCounts['completed'] }})</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan ({{ $sCounts['cancelled'] }})</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. Filter Tanggal Keberangkatan -->
                    <div class="filter-field">
                        <label class="filter-field-label" for="booking_date_input">Tanggal Keberangkatan</label>
                        <div class="filter-control-wrap">
                            <svg class="filter-control-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <input
                                type="date"
                                id="booking_date_input"
                                name="date"
                                value="{{ $date }}"
                                class="filter-date"
                                title="Filter Tanggal Keberangkatan"
                                onchange="const schedSel = document.getElementById('booking_schedule_select'); if (schedSel) schedSel.value = ''; this.form.submit();"
                            >
                        </div>
                    </div>

                    <!-- 4. Filter Jadwal / Rute Spesifik -->
                    <div class="filter-field">
                        <label class="filter-field-label" for="booking_schedule_select">Jadwal Trip Armada</label>
                        <div class="filter-control-wrap">
                            <svg class="filter-control-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="16" height="16" x="4" y="3" rx="2" />
                                <path d="M4 11h16" />
                                <path d="M12 3v8" />
                                <path d="m8 19-2 3" />
                                <path d="m18 22-2-3" />
                                <path d="M8 15h0" />
                                <path d="M16 15h0" />
                            </svg>
                            <select id="booking_schedule_select" name="schedule_id" class="filter-select has-icon" onchange="const opt = this.options[this.selectedIndex]; const dateInp = document.getElementById('booking_date_input'); if (dateInp && opt && opt.dataset.date) { dateInp.value = opt.dataset.date; } this.form.submit();">
                                <option value="">Semua Jadwal Trip ({{ $scheduleList->count() }})</option>
                                @foreach($scheduleList as $sched)
                                    @php
                                        $sDateRaw = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->toDateString() : '';
                                        $sDate = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->format('d/m/Y') : '';
                                        $sTime = $sched->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i') : '';
                                        $sOrigin = $sched->route?->origin?->name ?? 'Asal';
                                        $sDest = $sched->route?->destination?->name ?? 'Tujuan';
                                        $sCount = (int) ($sched->bookings_count ?? 0);
                                    @endphp
                                    <option value="{{ $sched->id }}" data-date="{{ $sDateRaw }}" {{ ((string) $scheduleId === (string) $sched->id) ? 'selected' : '' }}>
                                        {{ $sDate }} {{ $sTime }} • {{ $sOrigin }} &rarr; {{ $sDest }} ({{ $sCount }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 5. Tombol Aksi Filter -->
                    <div style="display: flex; gap: 6px; align-items: center;">
                        <button type="submit" class="btn-filter-apply">
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="btn-filter-reset" title="Reset Semua Filter">
                            Reset
                        </a>
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
                                    @php
                                        $isCompleted = $statusLower === 'completed';
                                        $isBoarded = in_array($statusLower, ['checked_in', 'boarded'], true);
                                        $isFutureTrip = $sched && $sched->departureCarbon()->isFuture();
                                    @endphp
                                    <td>
                                        <div style="font-weight: 800; font-size: 14px; color: var(--text-dark);">
                                            Rp {{ number_format((float)$b->total_price, 0, ',', '.') }}
                                        </div>
                                        <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                            @if($isCancelled)
                                                <span class="status-badge badge-status-cancelled">
                                                    Dibatalkan
                                                </span>
                                            @elseif($isCompleted)
                                                <span class="status-badge badge-status-completed" title="Pembayaran Lunas & Perjalanan Selesai">
                                                    &check; Selesai
                                                </span>
                                            @elseif($isBoarded)
                                                <span class="status-badge badge-status-paid" title="Pembayaran Lunas & Sudah Boarding">
                                                    &check; Boarding
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

                                    <!-- 7. AKSI (DISESUAIKAN DENGAN STATUS MASING-MASING PESANAN) -->
                                    <td style="text-align: center;">
                                        <div class="action-group" style="justify-content: center;">
                                            <!-- 1. LIHAT DETAIL PESANAN (SELALU TERSEDIA) -->
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

                                            <!-- 2. CETAK TIKET / STRUK FISIK (HANYA UNTUK PESANAN SUDAH LUNAS / SELESAI) -->
                                            @if($isPaid && !$isCancelled)
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
                                            @endif

                                            <!-- 3. KONFIRMASI LUNAS CEPAT (KHUSUS PESANAN PENDING) -->
                                            @if(!$isPaid && !$isCancelled)
                                                <button
                                                    type="button"
                                                    class="btn-tbl-action print"
                                                    style="background: #ecfdf5; color: #059669; border-color: #6ee7b7;"
                                                    title="Konfirmasi Lunas (Tunai / QRIS / Transfer)"
                                                    onclick="openSettleModal({{ $b->id }}, @js($b->booking_code), @js($b->customerName()), @js('Rp '.number_format((float) $b->total_price, 0, ',', '.')))"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- 4. MEMBATALKAN PESANAN (KHUSUS PESANAN BELUM DIBATALKAN & BELUM SELESAI) -->
                                            @if(!$isCancelled && !$isCompleted)
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
        <form method="POST" action="{{ route('dashboard.admin-biasa.offline-booking.store') }}" id="manualBookingModalForm" autocomplete="off">
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
                                <span style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Saluran Pemesanan Loket</span>
                                <input type="hidden" name="source" id="mb_source" value="offline">
                                <div style="height: 42px; padding: 0 12px; border: 1.5px solid var(--border); border-radius: 10px; background: #f8faf9; color: #0C392F; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; cursor: default;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: #0C7950;">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                    <span>Datang Langsung di Loket / Go-Show (Offline)</span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label for="mb_customer_name" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Nama Pelanggan <span style="color: #dc2626;">*</span></label>
                                    <input type="text" id="mb_customer_name" name="customer_name" class="filter-select" style="background-image: none; padding-right: 12px;" placeholder="Contoh: Budi Santoso" required maxlength="100" autocomplete="off" oninput="mbSyncPassengerNames()">
                                </div>
                                <div>
                                    <label for="mb_customer_phone" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Nomor HP / WA <span style="color: #dc2626;">*</span></label>
                                    <input type="tel" id="mb_customer_phone" name="customer_phone" class="filter-select" style="background-image: none; padding-right: 12px;" placeholder="081234567890" required maxlength="20" inputmode="numeric" pattern="[0-9]*" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, ''); mbSyncPassengerPhones();">
                                </div>
                            </div>

                            <div>
                                <label for="mb_customer_email" style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">Email Pelanggan (Opsional)</label>
                                <input type="email" id="mb_customer_email" name="customer_email" class="filter-select" style="background-image: none; padding-right: 12px;" placeholder="email@contoh.com" maxlength="100" autocomplete="off">
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
     MODAL 5: KONFIRMASI PELUNASAN CEPAT (SETTLE PAYMENT)
====================================================================================== -->
<div class="modal-overlay" id="settleModalOverlay" onclick="if (event.target === this) closeModal('settleModalOverlay')">
    <div class="modal-dialog" style="max-width: 460px;">
        <form id="settleBookingForm" method="POST" onsubmit="submitSettleBooking(event)" autocomplete="off">
            @csrf
            <div class="modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <span>Konfirmasi Pelunasan Tiket</span>
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('settleModalOverlay')">&times;</button>
            </div>

            <div class="modal-body" style="display: grid; gap: 14px;">
                <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; padding: 14px 16px; border-radius: 12px;">
                    <div style="font-size: 12px; color: #047857; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">Tagihan Pesanan Pending</div>
                    <div style="font-size: 15px; font-weight: 800; color: #064e3b; margin-top: 2px;">
                        <span id="settleBookingCodeText">#TRV-</span> &bull; <span id="settleCustomerNameText">-</span>
                    </div>
                    <div style="font-size: 18px; font-weight: 800; color: #059669; margin-top: 6px;" id="settleTotalText">Rp 0</div>
                </div>

                <div>
                    <label for="settlePaymentMethod" style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                        Pilih Metode Pembayaran Diterima:
                    </label>
                    <select id="settlePaymentMethod" name="payment_method" class="filter-select" required>
                        <option value="Cash">Tunai (Cash di Loket)</option>
                        <option value="QRIS">QRIS Loket Pool</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                    </select>
                </div>

                <label style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #cbd5e1; cursor: pointer; font-size: 12.5px; font-weight: 700; color: #0f172a;">
                    <input type="checkbox" id="settleMarkBoarded" name="mark_boarded" value="1">
                    <span>Langsung tandai penumpang <em>Boarded</em> (Sudah Naik)</span>
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('settleModalOverlay')">Batal</button>
                <button type="submit" id="settleSubmitBtn" class="btn-filter-apply" style="background: #059669;">
                    <span>Konfirmasi Lunas Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================================================
     MODAL 6: POPUP NOTIFIKASI CUSTOM (PENGGANTI BROWSER ALERT LOCALHOST)
====================================================================================== -->
<div class="modal-overlay" id="appPopupModalOverlay" style="z-index: 1200;" onclick="if (event.target === this) closeAppPopup()">
    <div class="modal-dialog" style="max-width: 440px; text-align: center; padding: 28px 24px 22px;">
        <div id="appPopupIconWrap" style="width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; background: #ecfdf5; color: #059669; border: 2px solid #a7f3d0;">
            <!-- SVG Icon diisi via JS -->
        </div>
        <h3 id="appPopupTitle" style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Notifikasi Sistem</h3>
        <p id="appPopupMessage" style="font-size: 13.5px; color: #475569; line-height: 1.55; margin-bottom: 20px;"></p>

        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
            <a href="#" id="appPopupActionLink" target="_blank" class="btn-filter-apply" style="display: none; text-decoration: none; background: #0C7950;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect width="12" height="8" x="6" y="14" />
                </svg>
                <span id="appPopupActionLabel">Cetak Struk / Tiket Fisik</span>
            </a>
            <button type="button" id="appPopupConfirmBtn" class="btn-filter-apply" onclick="closeAppPopup()" style="min-width: 120px; justify-content: center;">
                <span>Mengerti</span>
            </button>
        </div>
    </div>
</div>

<!-- =====================================================================================
     SCRIPTS
====================================================================================== -->
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let appPopupCloseCallback = null;

    function closeModal(modalId) {
        document.getElementById(modalId)?.classList.remove('active');
    }

    function showAppPopup({ type = 'success', title = 'Informasi', message = '', buttonText = 'Tutup', actionUrl = '', actionLabel = '', onClose = null }) {
        const overlay = document.getElementById('appPopupModalOverlay');
        const iconWrap = document.getElementById('appPopupIconWrap');
        const titleEl = document.getElementById('appPopupTitle');
        const msgEl = document.getElementById('appPopupMessage');
        const btnEl = document.getElementById('appPopupConfirmBtn');
        const linkEl = document.getElementById('appPopupActionLink');
        const linkLabelEl = document.getElementById('appPopupActionLabel');

        if (!overlay) return;
        appPopupCloseCallback = typeof onClose === 'function' ? onClose : null;

        if (type === 'error') {
            iconWrap.style.background = '#fef2f2';
            iconWrap.style.color = '#dc2626';
            iconWrap.style.borderColor = '#fecaca';
            iconWrap.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            btnEl.style.background = '#dc2626';
        } else if (type === 'warning') {
            iconWrap.style.background = '#fffbeb';
            iconWrap.style.color = '#d97706';
            iconWrap.style.borderColor = '#fde68a';
            iconWrap.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            btnEl.style.background = '#d97706';
        } else {
            iconWrap.style.background = '#ecfdf5';
            iconWrap.style.color = '#059669';
            iconWrap.style.borderColor = '#a7f3d0';
            iconWrap.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
            btnEl.style.background = '#087f55';
        }

        titleEl.textContent = title;
        msgEl.textContent = message;
        btnEl.querySelector('span').textContent = buttonText;

        if (actionUrl && linkEl) {
            linkEl.href = actionUrl;
            if (linkLabelEl) linkLabelEl.textContent = actionLabel || 'Cetak Struk / Tiket Fisik';
            linkEl.style.display = 'inline-flex';
        } else if (linkEl) {
            linkEl.style.display = 'none';
        }

        overlay.classList.add('active');
    }

    function closeAppPopup() {
        const overlay = document.getElementById('appPopupModalOverlay');
        overlay?.classList.remove('active');
        if (appPopupCloseCallback) {
            const cb = appPopupCloseCallback;
            appPopupCloseCallback = null;
            cb();
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal('detailModalOverlay');
            closeModal('rescheduleModalOverlay');
            closeModal('cancelModalOverlay');
            closeModal('manualBookingModalOverlay');
            closeModal('settleModalOverlay');
            closeAppPopup();
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
            const bStatusLower = String(b.status || '').toLowerCase();
            const canPrintTicket = ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed'].includes(bStatusLower);
            printBtn.href = b.print_url;
            printBtn.style.display = canPrintTicket ? 'inline-flex' : 'none';

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
            showAppPopup({
                type: 'warning',
                title: 'Jadwal Belum Dipilih',
                message: 'Silakan pilih jadwal keberangkatan baru terlebih dahulu sebelum menyimpan perubahan.',
                buttonText: 'Pilih Jadwal'
            });
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
                closeModal('rescheduleModalOverlay');
                showAppPopup({
                    type: 'success',
                    title: 'Jadwal Berhasil Diubah',
                    message: data.message || 'Jadwal keberangkatan tiket berhasil diperbarui.',
                    buttonText: 'Muat Ulang Data',
                    onClose: () => window.location.reload()
                });
            } else {
                showAppPopup({
                    type: 'error',
                    title: 'Gagal Mengubah Jadwal',
                    message: data.message || 'Gagal mengubah jadwal tiket.',
                    buttonText: 'Tutup'
                });
                btn.disabled = false;
                btn.innerText = 'Konfirmasi Ubah Jadwal';
            }
        } catch (err) {
            console.error(err);
            showAppPopup({
                type: 'error',
                title: 'Gangguan Koneksi',
                message: 'Terjadi kesalahan koneksi saat menghubungi server. Silakan coba lagi.',
                buttonText: 'Tutup'
            });
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
                closeModal('cancelModalOverlay');
                showAppPopup({
                    type: 'success',
                    title: 'Pesanan Dibatalkan',
                    message: data.message || 'Pesanan berhasil dibatalkan dan kursi telah dikembalikan ke kuota armada.',
                    buttonText: 'Muat Ulang Data',
                    onClose: () => window.location.reload()
                });
            } else {
                showAppPopup({
                    type: 'error',
                    title: 'Gagal Membatalkan',
                    message: data.message || 'Gagal membatalkan pesanan.',
                    buttonText: 'Tutup'
                });
                btn.disabled = false;
                btn.innerText = 'Ya, Batalkan Pesanan';
            }
        } catch (err) {
            console.error(err);
            showAppPopup({
                type: 'error',
                title: 'Gangguan Koneksi',
                message: 'Terjadi kesalahan koneksi saat membatalkan pesanan.',
                buttonText: 'Tutup'
            });
            btn.disabled = false;
            btn.innerText = 'Ya, Batalkan Pesanan';
        }
    }

    // 3.5 SETTLE PAYMENT MODAL (KONFIRMASI LUNAS CEPAT)
    const settleBookingUrlTemplate = @json(route('dashboard.admin-biasa.booking.settle', ['booking' => '__BOOKING_ID__']));

    function openSettleModal(bookingId, bookingCode, customerName, formattedTotal) {
        const overlay = document.getElementById('settleModalOverlay');
        const form = document.getElementById('settleBookingForm');
        if (!overlay || !form) return;

        document.getElementById('settleBookingCodeText').textContent = `#${bookingCode}`;
        document.getElementById('settleCustomerNameText').textContent = customerName;
        document.getElementById('settleTotalText').textContent = formattedTotal;
        document.getElementById('settlePaymentMethod').value = 'Cash';
        document.getElementById('settleMarkBoarded').checked = false;
        form.action = settleBookingUrlTemplate.replace('__BOOKING_ID__', String(bookingId));
        overlay.classList.add('active');
    }

    async function submitSettleBooking(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('settleSubmitBtn');
        const paymentMethod = document.getElementById('settlePaymentMethod').value;
        const markBoarded = document.getElementById('settleMarkBoarded').checked;

        btn.disabled = true;
        btn.innerHTML = '<span>Memproses...</span>';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    mark_boarded: markBoarded
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeModal('settleModalOverlay');
                showAppPopup({
                    type: 'success',
                    title: 'Pelunasan Berhasil',
                    message: data.message || 'Status pesanan telah diperbarui menjadi Lunas.',
                    buttonText: 'Muat Ulang Data',
                    onClose: () => window.location.reload()
                });
            } else {
                showAppPopup({
                    type: 'error',
                    title: 'Pelunasan Gagal',
                    message: data.message || 'Tidak dapat memproses pelunasan tiket.',
                    buttonText: 'Tutup'
                });
                btn.disabled = false;
                btn.innerHTML = '<span>Konfirmasi Lunas Sekarang</span>';
            }
        } catch (err) {
            console.error(err);
            showAppPopup({
                type: 'error',
                title: 'Gangguan Koneksi',
                message: 'Terjadi kesalahan jaringan saat memproses pelunasan.',
                buttonText: 'Tutup'
            });
            btn.disabled = false;
            btn.innerHTML = '<span>Konfirmasi Lunas Sekarang</span>';
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
        const statusLabel = btn.querySelector('small');

        if (mbSelectedSeats.has(id)) {
            mbSelectedSeats.delete(id);
            btn.style.background = '#eefbf5';
            btn.style.borderColor = '#8fd3b4';
            btn.style.color = '#0C5A3E';
            if (statusLabel) {
                statusLabel.textContent = 'Kosong';
            }
        } else {
            mbSelectedSeats.set(id, num);
            btn.style.background = '#0C7950';
            btn.style.borderColor = '#0C392F';
            btn.style.color = '#ffffff';
            if (statusLabel) {
                statusLabel.textContent = 'Terpilih';
            }
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
                        <input type="text" name="passengers[${seatId}][name]" class="filter-select" style="height: 36px; font-size: 12.5px; background-image: none; padding-right: 10px;" autocomplete="off" data-auto-synced="true" oninput="this.dataset.autoSynced = 'false'" value="${escapeHtml(suggestedName)}" placeholder="Nama penumpang kursi ${escapeHtml(seatNum)}" required maxlength="100">
                        <input type="tel" name="passengers[${seatId}][phone]" class="filter-select" style="height: 36px; font-size: 12.5px; background-image: none; padding-right: 10px;" autocomplete="off" data-auto-synced="true" value="${escapeHtml(defaultPhone)}" placeholder="No. HP (opsional)" maxlength="20" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.dataset.autoSynced = 'false'">
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
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            showAppPopup({
                type: 'success',
                title: 'Transaksi Berhasil',
                message: @json(session('success')),
                buttonText: 'Tutup',
                actionUrl: @json(session('print_url') ?? ''),
                actionLabel: @json(session('print_booking_code') ? 'Cetak Struk (' . session('print_booking_code') . ')' : 'Cetak Struk / Tiket Fisik')
            });
        });
    @elseif (session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            showAppPopup({
                type: 'error',
                title: 'Transaksi Gagal',
                message: @json(session('error')),
                buttonText: 'Mengerti'
            });
        });
    @endif
</script>

@include('components.numeric-input-enforcer')
</body>

</html>
