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
                <div class="ds-alert ds-alert--success" role="status" style="margin-bottom: 20px;">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="ds-alert ds-alert--error" role="alert" style="margin-bottom: 20px;">
                    <span>{{ session('error') }}</span>
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

                    <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="btn-filter-apply" style="text-decoration: none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Buat Pesanan Baru (Kasir)</span>
                    </a>
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
</script>

@include('components.numeric-input-enforcer')
</body>

</html>
