<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Booking (Manifes Penumpang) - Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #087f55;
            --primary-dark: #066b47;
            --primary-light: #e3f5ee;
            --accent: #066b47;
            --accent-light: #e8f8fc;

            --white: #ffffff;
            --background: #f4f8fb;
            --border: #e3edf4;

            --text-dark: #102a43;
            --text: #334e68;
            --text-light: #829ab1;

            --success: #16825d;
            --success-bg: #eaf8f2;

            --warning: #c77b00;
            --warning-bg: #fff7e6;

            --danger: #c0392b;
            --danger-bg: #fff0ee;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--background);
            color: var(--text-dark);
        }

        a {
            text-decoration: none;
        }

        button, input, select {
            font-family: inherit;
        }

        /* LAYOUT */
        .dashboard-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: var(--white);
            z-index: 100;
            box-shadow: 4px 0 20px rgba(10, 53, 93, 0.10);
        }

        .logo-area {
            height: 82px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        }

        .logo-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            color: var(--primary);
            border-radius: 13px;
            font-size: 20px;
            font-weight: 800;
            margin-right: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.10);
        }

        .logo-text h2 {
            color: var(--white);
            font-size: 19px;
            font-weight: 700;
            line-height: 1.2;
        }

        .logo-text span {
            color: rgba(255, 255, 255, 0.65);
            font-size: 10px;
        }

        .menu {
            flex: 1;
            padding: 25px 15px;
        }

        .menu-title {
            padding: 0 12px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.45);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            margin-bottom: 6px;
            color: rgba(255, 255, 255, 0.72);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.10);
            color: var(--white);
        }

        .menu a.active {
            background: var(--white);
            color: var(--primary);
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .menu-icon {
            width: 21px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .sidebar-bottom {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.10);
        }

        .logout-btn {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.80);
            padding: 11px 14px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 1);
            color: var(--white);
        }

        /* MAIN CONTENT */
        .main-content {
            width: calc(100% - 260px);
            margin-left: 260px;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 36px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title h1 {
            color: var(--text-dark);
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .page-title p {
            color: var(--text-light);
            font-size: 13px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            color: var(--primary);
            border: 2px solid #d7eaf7;
            border-radius: 50%;
            font-weight: 700;
        }

        .profile-info strong {
            display: block;
            color: var(--text-dark);
            font-size: 14px;
            margin-bottom: 2px;
        }

        .profile-info span {
            color: var(--text-light);
            font-size: 11px;
        }

        /* CONTENT */
        .content {
            padding: 30px 36px 50px;
        }

        /* TRIP SELECTOR & SUMMARY BANNER */
        .trip-header-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px 28px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .trip-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 18px;
        }

        .trip-select-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .trip-select-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .trip-dropdown {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            color: var(--text-dark);
            background: #f8fafc;
            outline: none;
            cursor: pointer;
            min-width: 280px;
            transition: border-color 0.2s;
        }

        .trip-dropdown:focus {
            border-color: var(--primary);
        }

        .trip-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-print {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-print:hover {
            background: #e2e8f0;
        }

        .btn-driver-wa {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-driver-wa:hover {
            background: #15803d;
        }

        .trip-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .trip-summary-box {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .trip-summary-box span {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .trip-summary-box strong {
            display: block;
            font-size: 15px;
            color: var(--text-dark);
            font-weight: 800;
        }

        .trip-summary-box small {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* MANIFEST TOOLBAR & SEARCH BOX */
        .manifest-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .search-container {
            position: relative;
            flex: 1;
            max-width: 480px;
            min-width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: var(--text-light);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 11px 16px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 13px;
            outline: none;
            background: #ffffff;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        }

        .search-count-badge {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
        }

        .status-filter-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--text);
            transition: all 0.2s;
        }

        .filter-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* MANIFEST PASSENGERS TABLE CARD */
        .manifest-table-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        .manifest-table th {
            background: #f8fafc;
            padding: 14px 18px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            border-bottom: 1.5px solid var(--border);
        }

        .manifest-table td {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--text-dark);
        }

        .manifest-table tr:hover td {
            background: #fafcfb;
        }

        /* KURSI BADGE */
        .seat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #eefbf5;
            color: #087f55;
            border: 1.5px solid #b1eed1;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            font-family: monospace;
            box-shadow: 0 2px 6px rgba(8, 127, 85, 0.08);
        }

        /* NAMA & TIKET */
        .passenger-info strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .passenger-code {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-family: monospace;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* KONTAK & TOMBOL WHATSAPP / TELEPON LANGSUNG */
        .contact-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .contact-number {
            font-weight: 700;
            color: #1e293b;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .contact-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-wa-direct {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            background: #25d366;
            color: #ffffff;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-wa-direct:hover {
            background: #1ebc59;
            transform: translateY(-1px);
        }

        .btn-call-direct {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-call-direct:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* TITIK NAIK & TURUN (DROP POINT) */
        .route-stops {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-width: 250px;
        }

        .stop-item {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            font-size: 12px;
            line-height: 1.35;
        }

        .stop-icon {
            font-size: 10px;
            margin-top: 2px;
        }

        .stop-item.pickup {
            color: #087f55;
        }

        .stop-item.dropoff {
            color: #b45309;
        }

        .stop-item strong {
            font-weight: 700;
        }

        /* CATATAN KHUSUS PENUMPANG */
        .special-note-box {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.4;
            max-width: 260px;
        }

        .note-luggage {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .note-child {
            background: #fdf4ff;
            color: #a21caf;
            border: 1px solid #f0abfc;
        }

        .note-elderly {
            background: #fff8eb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .note-standard {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* STATUS BOARDING TOGGLE */
        .status-badge-boarded {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            background: #eaf8f2;
            color: #087f55;
            border: 1px solid #b1eed1;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-badge-pending {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            background: #fff8eb;
            color: #b45309;
            border: 1px solid #fde68a;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .btn-toggle-boarding {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            margin-top: 4px;
            display: inline-block;
        }

        .btn-toggle-boarded {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-toggle-boarded:hover {
            background: #cbd5e1;
        }

        .btn-toggle-not-yet {
            background: #087f55;
            color: #ffffff;
        }

        .btn-toggle-not-yet:hover {
            background: #066b47;
        }

        /* PRINT STYLES */
        @media print {
            .sidebar, .topbar, .manifest-toolbar, .btn-action, .contact-actions, .btn-toggle-boarding {
                display: none !important;
            }

            .main-content {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .content {
                padding: 0 !important;
            }

            .manifest-table-card, .trip-header-card {
                border: 1px solid #94a3b8 !important;
                box-shadow: none !important;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .trip-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 72px;
            }

            .logo-area {
                justify-content: center;
                padding: 0;
            }

            .logo-icon {
                margin-right: 0;
            }

            .logo-text, .menu-title, .menu a span, .sidebar-bottom span, .profile-info {
                display: none;
            }

            .menu a {
                justify-content: center;
                padding: 13px;
            }

            .logout-btn {
                justify-content: center;
            }

            .main-content {
                width: calc(100% - 72px);
                margin-left: 72px;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 20px 16px;
            }

            .trip-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-wrapper">

    <!-- =========================================
         SIDEBAR STAF
    ========================================== -->
    <aside class="sidebar">

        <div class="logo-area">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="13" rx="2" />
                    <circle cx="7" cy="18" r="2" />
                    <circle cx="17" cy="18" r="2" />
                    <path d="M2 10h20" />
                    <path d="M7 5v5" />
                    <path d="M12 5v5" />
                    <path d="M17 5v5" />
                </svg>
            </div>
            <div class="logo-text">
                <h2>Travel</h2>
                <span>Management System</span>
            </div>
        </div>

        <nav class="menu">
            <div class="menu-title">
                Menu Utama
            </div>

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard.admin-biasa.index') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>
                <span>Dashboard</span>
            </a>

            <!-- MANAJEMEN BOOKING (ACTIVE) -->
            <a href="{{ route('dashboard.admin-biasa.booking') }}" class="active">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                        <path d="M13 5v2" />
                        <path d="M13 17v2" />
                        <path d="M13 11v2" />
                    </svg>
                </div>
                <span>Manajemen Booking</span>
            </a>

            <!-- MELIHAT INFORMASI PERJALANAN -->
            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                        <line x1="9" x2="9" y1="3" y2="18" />
                        <line x1="15" x2="15" y1="6" y2="21" />
                    </svg>
                </div>
                <span>Melihat Informasi Perjalanan</span>
            </a>

            <!-- VALIDASI E-TICKET -->
            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="5" height="5" x="3" y="3" rx="1" />
                        <rect width="5" height="5" x="16" y="3" rx="1" />
                        <rect width="5" height="5" x="3" y="16" rx="1" />
                        <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                        <path d="M21 21v.01" />
                        <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                        <path d="M12 12v4" />
                        <path d="M12 20v1" />
                        <path d="M17 12h4" />
                    </svg>
                </div>
                <span>Validasi E-Ticket</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <div class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" x2="9" y1="12" y2="12" />
                        </svg>
                    </div>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </aside>


    <!-- =========================================
         MAIN CONTENT
    ========================================== -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="page-title">
                <h1>Manajemen Booking (Manifes Penumpang)</h1>
                <p>Daftar manifes penumpang per perjalanan, nomor kursi, kontak langsung, titik singgah & catatan khusus.</p>
            </div>

            <div class="profile">
                <div class="avatar">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
                <div class="profile-info">
                    <strong>{{ $user->name ?? 'Staff Loket' }}</strong>
                    <span>{{ $user->branch ?? 'Pool Bandung Pusat' }}</span>
                </div>
            </div>
        </header>


        <!-- CONTENT SECTION -->
        <section class="content">

            <!-- 1. HEADER PERJALANAN & FILTER TRIP -->
            <div class="trip-header-card">

                <div class="trip-header-top">
                    <!-- Dropdown Pemilihan Jadwal Perjalanan -->
                    <form method="GET" action="{{ route('dashboard.admin-biasa.booking') }}" class="trip-select-group">
                        <label for="scheduleSelect" class="trip-select-label">
                            Pilih Perjalanan:
                        </label>

                        <select id="scheduleSelect" name="schedule_id" class="trip-dropdown" onchange="this.form.submit()">
                            @if($scheduleList->isNotEmpty())
                                @foreach($scheduleList as $sched)
                                    @php
                                        $bCount = $sched->bookings ? $sched->bookings->whereNotIn('status', ['cancelled', 'batal', 'Cancelled'])->sum(fn($b) => $b->passengers->count() ?: $b->total_passenger) : 0;
                                        $starTag = $bCount > 0 ? " • ({$bCount} Terdaftar)" : "";
                                        $dateText = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->translatedFormat('d M') . ' | ' : '';
                                    @endphp
                                    <option value="{{ $sched->id }}" {{ ($selectedSchedule && $selectedSchedule->id == $sched->id) ? 'selected' : '' }}>
                                        {{ $dateText }}{{ $sched->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i') : '13:00' }} WIB — {{ $sched->route?->origin?->name ?? 'Bandung' }} ➔ {{ $sched->route?->destination?->name ?? 'Jakarta' }} ({{ $sched->vehicle?->plate_number ?? 'D 7812 AB' }}){{ $starTag }}
                                    </option>
                                @endforeach
                            @else
                                <option value="1" selected>
                                    13:00 WIB — Bandung (Dipatiukur) ➔ Jakarta (Semanggi) (D 7812 AB)
                                </option>
                                <option value="2">
                                    14:15 WIB — Bandung (Pasteur) ➔ Bandara Soetta T3 (B 1945 TKL)
                                </option>
                                <option value="3">
                                    15:30 WIB — Bandung (Dipatiukur) ➔ Bekasi Timur (D 7023 XY)
                                </option>
                            @endif
                        </select>
                    </form>

                    <!-- Tombol Aksi: Cetak & WhatsApp Driver -->
                    <div class="trip-header-actions">
                        <button type="button" class="btn-action btn-print" onclick="window.print()" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                <rect width="12" height="8" x="6" y="14" />
                            </svg>
                            <span>Cetak Manifes</span>
                        </button>

                        @php
                            $waDriverText = urlencode("Halo Pak {$activeTripInfo->driver_name}, berikut ringkasan manifes trip {$activeTripInfo->time} ({$activeTripInfo->route_name}) armada {$activeTripInfo->plate_number}: Terisi {$totalBooked}/{$activeTripInfo->capacity} kursi, Sudah Boarding: {$boardedCount} orang. Mohon standby di pool.");
                        @endphp
                        <a
                            href="https://wa.me/6281288889999?text={{ $waDriverText }}"
                            target="_blank"
                            class="btn-action btn-driver-wa"
                            style="display: inline-flex; align-items: center; gap: 6px;"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m22 2-7 20-4-9-9-4Z" />
                                <path d="M22 2 11 13" />
                            </svg>
                            <span>Kirim Manifes ke Driver</span>
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Info Perjalanan -->
                <div class="trip-summary-grid">
                    <div class="trip-summary-box">
                        <span>Rute & Waktu</span>
                        <strong>{{ $activeTripInfo->route_name }}</strong>
                        <small style="display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ $activeTripInfo->time }} • {{ $activeTripInfo->date }}
                        </small>
                    </div>

                    <div class="trip-summary-box">
                        <span>Armada & Pelat</span>
                        <strong style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="13" rx="2" />
                                <circle cx="7" cy="18" r="2" />
                                <circle cx="17" cy="18" r="2" />
                                <path d="M2 10h20" />
                            </svg>
                            {{ $activeTripInfo->plate_number }}
                        </strong>
                        <small>{{ $activeTripInfo->vehicle_name }}</small>
                    </div>

                    <div class="trip-summary-box">
                        <span>Driver Bertugas</span>
                        <strong style="display: inline-flex; align-items: center; gap: 5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            {{ $activeTripInfo->driver_name }}
                        </strong>
                        <small style="display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            {{ $activeTripInfo->driver_phone }} (Standby)
                        </small>
                    </div>

                    <div class="trip-summary-box">
                        <span>Status Boarding</span>
                        <strong style="color: #087f55;">
                            {{ $boardedCount }}/{{ $totalBooked }} Naik
                        </strong>
                        <small>{{ $pendingCount }} Penumpang belum tiba</small>
                    </div>
                </div>

            </div>


            <!-- 2. TOOLBAR & KOLOM PENCARIAN CEPAT -->
            <div class="manifest-toolbar">

                <!-- KOLOM PENCARIAN (Berdasarkan Nama atau Nomor Kursi) -->
                <div class="search-container">
                    <span class="search-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="passengerSearch"
                        class="search-input"
                        placeholder="Cari cepat nama penumpang atau nomor kursi (misal: Budi, Siti, atau 1A)..."
                        onkeyup="filterPassengers()"
                    >
                </div>

                <!-- Filter Tombol Status -->
                <div class="status-filter-buttons">
                    <button type="button" class="filter-btn active" onclick="setFilter('all', this)">
                        Semua (<span id="countAll">{{ $totalBooked }}</span>)
                    </button>
                    <button type="button" class="filter-btn" onclick="setFilter('boarded', this)">
                        Sudah Boarding (<span id="countBoarded">{{ $boardedCount }}</span>)
                    </button>
                    <button type="button" class="filter-btn" onclick="setFilter('pending', this)">
                        Belum Naik (<span id="countPending">{{ $pendingCount }}</span>)
                    </button>
                </div>

                <div class="search-count-badge" id="searchCountIndicator">
                    Menampilkan <span id="visibleCount">{{ $totalBooked }}</span> dari {{ $totalBooked }} penumpang
                </div>

            </div>


            <!-- 3. TABEL MANIFES PENUMPANG -->
            <div class="manifest-table-card">

                <div class="table-responsive">
                    <table class="manifest-table" id="manifestTable">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Kursi</th>
                                <th style="min-width: 170px;">Nama Penumpang</th>
                                <th style="min-width: 190px;">Kontak & Panggilan</th>
                                <th style="min-width: 240px;">Titik Naik & Turun (Drop Point)</th>
                                <th style="min-width: 230px;">Catatan Khusus Penumpang</th>
                                <th style="width: 130px; text-align: center;">Status Boarding</th>
                            </tr>
                        </thead>

                        <tbody id="passengerTableBody">
                            @forelse($sortedPassengers as $p)
                                @php
                                    $isBoarded = $p->boarded;
                                    $specialClass = 'note-standard';
                                    if (str_contains(strtolower($p->special_request), 'koper') || str_contains(strtolower($p->special_request), 'bagasi')) {
                                        $specialClass = 'note-luggage';
                                    } elseif (str_contains(strtolower($p->special_request), 'anak') || str_contains(strtolower($p->special_request), 'balita')) {
                                        $specialClass = 'note-child';
                                    } elseif (str_contains(strtolower($p->special_request), 'lansia') || str_contains(strtolower($p->special_request), 'tua')) {
                                        $specialClass = 'note-elderly';
                                    }
                                @endphp

                                <tr
                                    class="passenger-row"
                                    data-name="{{ strtolower($p->name) }}"
                                    data-seat="{{ strtolower($p->seat_number) }}"
                                    data-boarded="{{ $isBoarded ? 'true' : 'false' }}"
                                    id="row-passenger-{{ $p->id }}"
                                >
                                    <!-- 1. NOMOR KURSI -->
                                    <td style="text-align: center;">
                                        <div class="seat-badge">
                                            {{ $p->seat_number }}
                                        </div>
                                    </td>

                                    <!-- 2. NAMA PENUMPANG -->
                                    <td>
                                        <div class="passenger-info">
                                            <strong class="passenger-name-text">{{ $p->name }}</strong>
                                            <span class="passenger-code" style="display: inline-flex; align-items: center; gap: 4px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                                                </svg>
                                                {{ $p->booking_code }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 3. NOMOR TELEPON AKTIF + TOMBOL PANGGIL / WHATSAPP LANGSUNG -->
                                    <td>
                                        <div class="contact-group">
                                            <div class="contact-number" style="display: flex; align-items: center; gap: 5px;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-light);">
                                                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2" />
                                                    <path d="M12 18h.01" />
                                                </svg>
                                                <span>{{ $p->phone }}</span>
                                            </div>

                                            <div class="contact-actions">
                                                <!-- Tombol WhatsApp Langsung -->
                                                <a
                                                    href="{{ $p->phone_wa_url }}"
                                                    target="_blank"
                                                    class="btn-wa-direct"
                                                    title="Kirim Pesan WhatsApp Langsung"
                                                    style="display: inline-flex; align-items: center; gap: 4px;"
                                                >
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                                    </svg>
                                                    <span>WhatsApp</span>
                                                </a>

                                                <!-- Tombol Panggil Langsung -->
                                                <a
                                                    href="{{ $p->phone_tel_url }}"
                                                    class="btn-call-direct"
                                                    title="Hubungi Nomor Telepon"
                                                    style="display: inline-flex; align-items: center; gap: 4px;"
                                                >
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                                    </svg>
                                                    <span>Panggil</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 4. TITIK NAIK & TURUN (KETERANGAN DROP POINT) -->
                                    <td>
                                        <div class="route-stops">
                                            <div class="stop-item pickup">
                                                <span class="stop-icon" style="color: #087f55;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                        <circle cx="12" cy="12" r="8" />
                                                    </svg>
                                                </span>
                                                <div>
                                                    <strong>Naik:</strong> {{ $p->pickup_point }}
                                                </div>
                                            </div>

                                            <div class="stop-item dropoff">
                                                <span class="stop-icon" style="color: #d97706;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                        <circle cx="12" cy="10" r="3" />
                                                    </svg>
                                                </span>
                                                <div>
                                                    <strong>Turun:</strong> {{ $p->drop_point }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 5. CATATAN KHUSUS PENUMPANG -->
                                    <td>
                                        <div class="special-note-box {{ $specialClass }}">
                                            <span>{{ $p->special_request }}</span>
                                        </div>
                                    </td>

                                    <!-- 6. STATUS BOARDING & AKSI TOGGLE -->
                                    <td style="text-align: center;">
                                        <div id="badge-status-{{ $p->id }}">
                                            @if($isBoarded)
                                                <span class="status-badge-boarded" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                    <span>Sudah Boarding</span>
                                                </span>
                                            @else
                                                <span class="status-badge-pending" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10" />
                                                        <polyline points="12 6 12 12 16 14" />
                                                    </svg>
                                                    <span>Belum Naik</span>
                                                </span>
                                            @endif
                                        </div>

                                        <button
                                            type="button"
                                            class="btn-toggle-boarding {{ $isBoarded ? 'btn-toggle-boarded' : 'btn-toggle-not-yet' }}"
                                            onclick="togglePassengerBoarding({{ $p->id }}, '{{ $p->name }}', '{{ $p->seat_number }}')"
                                            id="btn-toggle-{{ $p->id }}"
                                        >
                                            {{ $isBoarded ? 'Batalkan' : 'Masuk Armada' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-light);">
                                        Belum ada penumpang terdaftar pada perjalanan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pesan jika hasil pencarian nihil -->
                <div id="noResultsMessage" style="display: none; padding: 40px; text-align: center;">
                    <p style="font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <span>Penumpang Tidak Ditemukan</span>
                    </p>
                    <p style="font-size: 13px; color: var(--text-light);">
                        Tidak ada penumpang atau nomor kursi yang cocok dengan kata kunci tersebut.
                    </p>
                </div>

            </div>

        </section>

    </main>

</div>


<!-- =========================================
     INTERACTIVE JAVASCRIPT LOGIC
========================================== -->
<script>
    let currentFilter = 'all';

    // 1. KOLOM PENCARIAN CEPAT (Berdasarkan Nama atau Nomor Kursi)
    function filterPassengers() {
        const query = document.getElementById('passengerSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.passenger-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const seat = row.getAttribute('data-seat') || '';
            const isBoarded = row.getAttribute('data-boarded') === 'true';

            // Cek kondisi pencarian
            const matchesSearch = name.includes(query) || seat.includes(query);

            // Cek kondisi filter status
            let matchesFilter = true;
            if (currentFilter === 'boarded' && !isBoarded) matchesFilter = false;
            if (currentFilter === 'pending' && isBoarded) matchesFilter = false;

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('visibleCount').innerText = visibleCount;

        const noResults = document.getElementById('noResultsMessage');
        const table = document.getElementById('manifestTable');

        if (visibleCount === 0 && rows.length > 0) {
            noResults.style.display = 'block';
            table.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            table.style.display = 'table';
        }
    }

    // 2. FILTER TAB (Semua, Sudah Boarding, Belum Naik)
    function setFilter(status, buttonElement) {
        currentFilter = status;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');
        filterPassengers();
    }

    // 3. TOGGLE STATUS BOARDING DENGAN SINKRONISASI DATABASE BACKEND
    async function togglePassengerBoarding(id, name, seat) {
        const row = document.getElementById('row-passenger-' + id);
        const badgeContainer = document.getElementById('badge-status-' + id);
        const toggleBtn = document.getElementById('btn-toggle-' + id);
        const currentStatus = row.getAttribute('data-boarded') === 'true';
        const newStatus = currentStatus ? 'belum_hadir' : 'boarded';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        toggleBtn.disabled = true;
        toggleBtn.style.opacity = '0.6';

        try {
            const response = await fetch("{{ url('/admin/validasi-tiket/passenger') }}/" + id + "/status", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                if (newStatus === 'boarded') {
                    // Ubah menjadi Sudah Boarding
                    row.setAttribute('data-boarded', 'true');
                    badgeContainer.innerHTML = '<span class="status-badge-boarded" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span>Sudah Boarding</span></span>';
                    toggleBtn.className = 'btn-toggle-boarding btn-toggle-boarded';
                    toggleBtn.innerText = 'Batalkan';
                } else {
                    // Ubah menjadi Belum Naik
                    row.setAttribute('data-boarded', 'false');
                    badgeContainer.innerHTML = '<span class="status-badge-pending" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Belum Naik</span></span>';
                    toggleBtn.className = 'btn-toggle-boarding btn-toggle-not-yet';
                    toggleBtn.innerText = 'Masuk Armada';
                }

                updateCounters();
                filterPassengers();
            } else {
                alert('Gagal memperbarui status boarding: ' + (data.message || 'Terjadi kesalahan server'));
            }
        } catch (err) {
            console.error('Error toggle boarding:', err);
            alert('Gagal menghubungi server. Pastikan koneksi aktif.');
        } finally {
            toggleBtn.disabled = false;
            toggleBtn.style.opacity = '1';
        }
    }

    function updateCounters() {
        const rows = document.querySelectorAll('.passenger-row');
        let boarded = 0;
        let pending = 0;

        rows.forEach(row => {
            if (row.getAttribute('data-boarded') === 'true') {
                boarded++;
            } else {
                pending++;
            }
        });

        document.getElementById('countAll').innerText = rows.length;
        document.getElementById('countBoarded').innerText = boarded;
        document.getElementById('countPending').innerText = pending;
    }
</script>

</body>

</html>
