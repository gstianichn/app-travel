<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Perjalanan Armada - Travel</title>

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

        /* STATS OVERVIEW CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 22px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-info span {
            display: block;
            color: var(--text-light);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 20px;
        }

        /* STATUS FILTER TABS */
        .filter-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }

        .filter-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 6px;
        }

        .filter-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(8, 127, 85, 0.25);
        }

        /* TRIP CARDS LIST */
        .trips-container {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .trip-detail-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .trip-detail-card:hover {
            border-color: #b1eed1;
            box-shadow: 0 10px 30px rgba(8, 127, 85, 0.08);
        }

        /* CARD HEADER */
        .trip-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 20px 26px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }

        .trip-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .trip-departure-badge {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #087f55;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 800;
        }

        .trip-departure-badge .time-text {
            font-size: 18px;
            letter-spacing: -0.5px;
        }

        .trip-departure-badge .label-text {
            font-size: 9px;
            text-transform: uppercase;
            opacity: 0.85;
            letter-spacing: 0.5px;
        }

        .trip-route-title h3 {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trip-route-title span {
            font-size: 12px;
            color: #64748b;
        }

        /* STATUS PERJALANAN BADGES */
        .trip-status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .status-bay {
            background: #eaf8f2;
            color: #087f55;
            border: 1.5px solid #b1eed1;
        }

        .status-bay .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #087f55;
            animation: pulseDot 1.5s infinite;
        }

        .status-garasi {
            background: #fff8eb;
            color: #b45309;
            border: 1.5px solid #fde68a;
        }

        .status-berangkat {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #bfdbfe;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }

        /* CARD BODY GRID */
        .trip-card-body {
            padding: 26px;
            display: grid;
            grid-template-columns: 1.2fr 1.8fr 1fr;
            gap: 24px;
        }

        /* SECTION 1: ARMADA & KRU */
        .body-section {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .section-sub-title {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .fleet-box {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 14px;
            padding: 16px;
        }

        .plate-number-display {
            font-size: 18px;
            font-weight: 900;
            color: var(--text-dark);
            font-family: monospace;
            display: inline-block;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            padding: 4px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .vehicle-model-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .vehicle-meta-tags {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #64748b;
        }

        .driver-box {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 14px;
            padding: 16px;
        }

        .driver-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .driver-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eefbf5;
            color: #087f55;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            border: 1px solid #b1eed1;
        }

        .driver-details strong {
            display: block;
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 700;
        }

        .driver-details span {
            font-size: 11px;
            color: #64748b;
        }

        .driver-contact-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-driver-contact {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-driver-wa {
            background: #25d366;
            color: #ffffff;
        }

        .btn-driver-wa:hover {
            background: #1ebc59;
        }

        .btn-driver-tel {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-driver-tel:hover {
            background: #e2e8f0;
        }

        /* SECTION 2: RUTE PERJALANAN & TITIK SINGGAH */
        .route-timeline {
            position: relative;
            padding-left: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .route-timeline::before {
            content: "";
            position: absolute;
            left: 7px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: #cbd5e1;
        }

        .timeline-step {
            position: relative;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #94a3b8;
        }

        .timeline-dot.origin {
            border-color: #087f55;
            background: #087f55;
            box-shadow: 0 0 0 3px #eefbf5;
        }

        .timeline-dot.transit {
            border-color: #b45309;
            background: #ffffff;
        }

        .timeline-dot.destination {
            border-color: #2563eb;
            background: #2563eb;
            box-shadow: 0 0 0 3px #eff6ff;
        }

        .timeline-content strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .timeline-content span {
            display: inline-block;
            font-size: 11px;
            color: #64748b;
        }

        .timeline-tag {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            margin-top: 3px;
        }

        .tag-origin { background: #eefbf5; color: #087f55; border: 1px solid #b1eed1; }
        .tag-transit { background: #fff8eb; color: #b45309; border: 1px solid #fde68a; }
        .tag-destination { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

        /* SECTION 3: JADWAL WAKTU & STATUS AKSI */
        .schedule-time-box {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .time-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .time-row-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .icon-gathering { background: #fff8eb; color: #b45309; }
        .icon-boarding { background: #eefbf5; color: #087f55; }
        .icon-arrival { background: #eff6ff; color: #1d4ed8; }

        .time-row-content span {
            display: block;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        .time-row-content strong {
            display: block;
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* STATUS BAY LOCATION & CARD FOOTER */
        .trip-card-footer {
            padding: 16px 26px;
            background: #fafcfb;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .bay-status-text {
            font-size: 13px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bay-badge-pill {
            padding: 4px 10px;
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            color: var(--primary);
        }

        .trip-actions-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-trip-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .btn-to-bay {
            background: #eefbf5;
            color: #087f55;
            border-color: #b1eed1;
        }

        .btn-to-bay:hover {
            background: #087f55;
            color: #ffffff;
        }

        .btn-mark-departed {
            background: #1e293b;
            color: #ffffff;
        }

        .btn-mark-departed:hover {
            background: #0f172a;
        }

        .btn-view-manifest {
            background: #ffffff;
            color: var(--text);
            border-color: var(--border);
        }

        .btn-view-manifest:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* RESPONSIVE */
        @media (max-width: 1150px) {
            .trip-card-body {
                grid-template-columns: 1fr 1fr;
            }

            .body-section:nth-child(3) {
                grid-column: span 2;
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .trip-card-body {
                grid-template-columns: 1fr;
            }

            .body-section:nth-child(3) {
                grid-column: span 1;
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

            <!-- MANAJEMEN BOOKING -->
            <a href="{{ route('dashboard.admin-biasa.booking') }}">
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

            <!-- MELIHAT INFORMASI PERJALANAN (ACTIVE) -->
            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}" class="active">
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
                <h1>Melihat Informasi Perjalanan Armada</h1>
                <p>Pantau detail armada & kru, rute titik singgah, jadwal kumpul & boarding, serta status lokasi unit.</p>
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

            <!-- 1. STATS OVERVIEW -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Total Jadwal Hari Ini</span>
                        <h3>{{ $stats['total_trips'] }} Trip</h3>
                    </div>
                    <div class="stat-icon" style="background: #eefbf5; color: #087f55;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="13" rx="2" />
                            <circle cx="7" cy="18" r="2" />
                            <circle cx="17" cy="18" r="2" />
                            <path d="M2 10h20" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Sandar di Area Muat (Bay)</span>
                        <h3 style="color: #087f55;">{{ $stats['in_bay'] }} Unit</h3>
                    </div>
                    <div class="stat-icon" style="background: #eaf8f2; color: #087f55;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="4" width="16" height="16" rx="3" />
                            <path d="M9 16V8h4a2 2 0 1 1 0 4H9" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Masih di Garasi (Prep)</span>
                        <h3 style="color: #b45309;">{{ $stats['in_garage'] }} Unit</h3>
                    </div>
                    <div class="stat-icon" style="background: #fff8eb; color: #b45309;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <path d="M9 22V12h6v10" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Sudah Berangkat</span>
                        <h3 style="color: #2563eb;">{{ $stats['departed'] }} Trip</h3>
                    </div>
                    <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- 2. FILTER STATUS PERJALANAN -->
            <div class="filter-header-bar">
                <div class="filter-tabs">
                    <button type="button" class="filter-tab-btn active" onclick="filterTrips('all', this)">
                        Semua Status ({{ $stats['total_trips'] }})
                    </button>
                    <button type="button" class="filter-tab-btn" onclick="filterTrips('bay', this)" style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <path d="M9 16V8h4a2 2 0 1 1 0 4H9" />
                        </svg>
                        <span>Sandar di Bay ({{ $stats['in_bay'] }})</span>
                    </button>
                    <button type="button" class="filter-tab-btn" onclick="filterTrips('garasi', this)" style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        </svg>
                        <span>Masih di Garasi ({{ $stats['in_garage'] }})</span>
                    </button>
                    <button type="button" class="filter-tab-btn" onclick="filterTrips('berangkat', this)" style="display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                        </svg>
                        <span>Sudah Berangkat ({{ $stats['departed'] }})</span>
                    </button>
                </div>

                <div>
                    <button type="button" class="btn-trip-action btn-view-manifest" onclick="window.location.reload()" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                            <path d="M21 3v5h-5" />
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                            <path d="M8 16H3v5" />
                        </svg>
                        <span>Segarkan Informasi Real-Time</span>
                    </button>
                </div>
            </div>


            <!-- 3. LIST INFORMASI PERJALANAN DETAIL -->
            <div class="trips-container" id="tripsContainer">

                @forelse($trips as $trip)
                    <div class="trip-detail-card" data-status="{{ $trip->status_key }}" id="trip-card-{{ $trip->id }}">

                        <!-- HEADER KARTU PERJALANAN -->
                        <div class="trip-card-header">
                            <div class="trip-header-left">
                                <div class="trip-departure-badge">
                                    <span class="time-text">{{ $trip->departure_time }}</span>
                                    <span class="label-text">Waktu Berangkat</span>
                                </div>

                                <div class="trip-route-title">
                                    <h3 style="display: flex; align-items: center; gap: 6px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                                            <line x1="9" x2="9" y1="3" y2="18" />
                                            <line x1="15" x2="15" y1="6" y2="21" />
                                        </svg>
                                        <span>{{ $trip->origin_pool }} ➔ {{ $trip->destination_pool }}</span>
                                    </h3>
                                    <span>Kode Trip: <strong>{{ $trip->trip_code }}</strong> • Keterisian: <strong>{{ $trip->occupancy_ratio }} ({{ $trip->occupancy_percentage }}%)</strong></span>
                                </div>
                            </div>

                            <!-- 4. INDIKATOR STATUS PERJALANAN -->
                            <div>
                                @if($trip->status_key === 'bay')
                                    <span class="trip-status-indicator status-bay" id="status-badge-{{ $trip->id }}" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <span class="pulse-dot"></span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="4" width="16" height="16" rx="2" />
                                            <path d="M9 16V8h4a2 2 0 1 1 0 4H9" />
                                        </svg>
                                        <span>{{ $trip->status_label }}</span>
                                    </span>
                                @elseif($trip->status_key === 'garasi')
                                    <span class="trip-status-indicator status-garasi" id="status-badge-{{ $trip->id }}" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        </svg>
                                        <span>{{ $trip->status_label }}</span>
                                    </span>
                                @else
                                    <span class="trip-status-indicator status-berangkat" id="status-badge-{{ $trip->id }}" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m22 2-7 20-4-9-9-4Z" />
                                        </svg>
                                        <span>{{ $trip->status_label }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <!-- BODY KARTU: 3 KOLOM DETAIL -->
                        <div class="trip-card-body">

                            <!-- KOLOM 1: DETAIL ARMADA & KRU -->
                            <div class="body-section">
                                <div class="section-sub-title" style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                        <rect x="2" y="5" width="20" height="13" rx="2" />
                                        <circle cx="7" cy="18" r="2" />
                                        <circle cx="17" cy="18" r="2" />
                                        <path d="M2 10h20" />
                                    </svg>
                                    <span>Detail Armada & Kru</span>
                                </div>

                                <div class="fleet-box">
                                    <span class="plate-number-display">{{ $trip->plate_number }}</span>
                                    <div class="vehicle-model-name">{{ $trip->vehicle_type }}</div>
                                    <div class="vehicle-meta-tags">
                                        <span>Kapasitas: <strong>{{ $trip->vehicle_capacity }} Kursi</strong></span>
                                        <span>•</span>
                                        <span>AC & USB Port Aktif</span>
                                    </div>
                                </div>

                                <div class="driver-box">
                                    <div class="driver-header">
                                        <div class="driver-avatar">
                                            {{ strtoupper(substr($trip->driver_name, 0, 1)) }}
                                        </div>
                                        <div class="driver-details">
                                            <strong>{{ $trip->driver_name }}</strong>
                                            <span>Sopir Utama Bertugas</span>
                                        </div>
                                    </div>

                                    <div class="driver-contact-actions">
                                        <a href="{{ $trip->driver_wa_url }}" target="_blank" class="btn-driver-contact btn-driver-wa" title="WhatsApp Sopir" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                            </svg>
                                            <span>WhatsApp</span>
                                        </a>
                                        <a href="{{ $trip->driver_tel_url }}" class="btn-driver-contact btn-driver-tel" title="Telepon Sopir" style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            <span>{{ $trip->driver_phone }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>


                            <!-- KOLOM 2: RUTE PERJALANAN & TITIK SINGGAH -->
                            <div class="body-section">
                                <div class="section-sub-title" style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span>Rute, Titik Singgah & Transfer</span>
                                </div>

                                <div class="route-timeline">
                                    <!-- Pool Asal -->
                                    <div class="timeline-step">
                                        <div class="timeline-dot origin"></div>
                                        <div class="timeline-content">
                                            <strong>{{ $trip->origin_pool }}</strong>
                                            <span>Titik Keberangkatan Utama Penumpang</span>
                                            <div>
                                                <span class="timeline-tag tag-origin" style="display: inline-flex; align-items: center; gap: 4px;">
                                                    <span style="width: 6px; height: 6px; background: #087f55; border-radius: 50%; display: inline-block;"></span>
                                                    <span>Pool Asal</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Titik Singgah / Rest Area -->
                                    @foreach($trip->transit_points as $transit)
                                        <div class="timeline-step">
                                            <div class="timeline-dot transit"></div>
                                            <div class="timeline-content">
                                                <strong>{{ $transit->name }}</strong>
                                                <span>{{ $transit->type }} ({{ $transit->time }})</span>
                                                <div>
                                                    <span class="timeline-tag tag-transit" style="display: inline-flex; align-items: center; gap: 4px;">
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                            <circle cx="12" cy="10" r="3" />
                                                        </svg>
                                                        <span>Titik Singgah / Transfer</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Pool Tujuan Akhir -->
                                    <div class="timeline-step">
                                        <div class="timeline-dot destination"></div>
                                        <div class="timeline-content">
                                            <strong>{{ $trip->destination_pool }}</strong>
                                            <span>Titik Akhir Penurunan Seluruh Penumpang</span>
                                            <div>
                                                <span class="timeline-tag tag-destination" style="display: inline-flex; align-items: center; gap: 4px;">
                                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                        <line x1="4" x2="4" y1="22" y2="15" />
                                                    </svg>
                                                    <span>Pool Tujuan Akhir</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- KOLOM 3: JADWAL WAKTU OPERASIONAL -->
                            <div class="body-section">
                                <div class="section-sub-title" style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    <span>Jadwal Waktu & Boarding</span>
                                </div>

                                <div class="schedule-time-box">
                                    <!-- 1. Jam Kumpul -->
                                    <div class="time-row">
                                        <div class="time-row-icon icon-gathering">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                        </div>
                                        <div class="time-row-content">
                                            <span>Jam Kumpul Penumpang:</span>
                                            <strong>{{ $trip->gathering_time }}</strong>
                                            <small style="font-size: 10px; color: #b45309;">(Wajib check-in di loket)</small>
                                        </div>
                                    </div>

                                    <!-- 2. Jam Buka Gerbang Boarding -->
                                    <div class="time-row">
                                        <div class="time-row-icon icon-boarding">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 20V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v14" />
                                                <path d="M2 20h20" />
                                                <path d="M14 12v.01" />
                                            </svg>
                                        </div>
                                        <div class="time-row-content">
                                            <span>Buka Gerbang Boarding:</span>
                                            <strong style="color: #087f55;">{{ $trip->boarding_gate_time }}</strong>
                                            <small style="font-size: 10px; color: #087f55;">(Pintu armada dibuka di Bay)</small>
                                        </div>
                                    </div>

                                    <!-- 3. Estimasi Jam Tiba di Tujuan -->
                                    <div class="time-row">
                                        <div class="time-row-icon icon-arrival">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                <line x1="4" x2="4" y1="22" y2="15" />
                                            </svg>
                                        </div>
                                        <div class="time-row-content">
                                            <span>Estimasi Tiba di Tujuan:</span>
                                            <strong style="color: #1d4ed8;">{{ $trip->estimated_arrival_time }}</strong>
                                            <small style="font-size: 10px; color: #64748b;">(Via Tol Trans Jawa)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- FOOTER KARTU: LOKASI DOCK & TOMBOL AKSI CEPAT STAF -->
                        <div class="trip-card-footer">
                            <div class="bay-status-text">
                                <span>Lokasi Saat Ini:</span>
                                <span class="bay-badge-pill" id="bay-text-{{ $trip->id }}">{{ $trip->status_bay }}</span>
                                <span style="font-size: 12px; color: #64748b;">• {{ $trip->status_desc }}</span>
                            </div>

                            <div class="trip-actions-row">
                                <!-- Tombol Aksi Status Cepat -->
                                @if($trip->status_key === 'garasi')
                                    <button
                                        type="button"
                                        class="btn-trip-action btn-to-bay"
                                        onclick="updateTripStatus({{ $trip->id }}, 'bay', 'Bay 01 (Gate Utama)')"
                                        style="display: inline-flex; align-items: center; gap: 5px;"
                                    >
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="4" width="16" height="16" rx="2" />
                                            <path d="M9 16V8h4a2 2 0 1 1 0 4H9" />
                                        </svg>
                                        <span>Arahkan Sandar ke Bay</span>
                                    </button>
                                @elseif($trip->status_key === 'bay')
                                    <button
                                        type="button"
                                        class="btn-trip-action btn-mark-departed"
                                        onclick="updateTripStatus({{ $trip->id }}, 'berangkat', 'Sudah Berangkat (Tol)')"
                                        style="display: inline-flex; align-items: center; gap: 5px;"
                                    >
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m22 2-7 20-4-9-9-4Z" />
                                        </svg>
                                        <span>Berangkatkan Armada</span>
                                    </button>
                                @endif

                                <a
                                    href="{{ route('dashboard.admin-biasa.booking') }}?schedule_id={{ $trip->id }}"
                                    class="btn-trip-action btn-view-manifest"
                                    style="display: inline-flex; align-items: center; gap: 5px;"
                                >
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                                        <path d="M9 12h6" />
                                        <path d="M9 16h6" />
                                    </svg>
                                    <span>Buka Manifes Kursi</span>
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <div style="padding: 50px; text-align: center; background: white; border-radius: 20px; border: 1px dashed var(--border);">
                        <p style="color: var(--text-light); font-size: 15px;">Belum ada jadwal informasi perjalanan yang terdaftar.</p>
                    </div>
                @endforelse

            </div>

        </section>

    </main>

</div>


<!-- =========================================
     JAVASCRIPT LOGIC UNTUK FILTER & STATUS
========================================== -->
<script>
    function filterTrips(statusKey, buttonElement) {
        document.querySelectorAll('.filter-tab-btn').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');

        const cards = document.querySelectorAll('.trip-detail-card');
        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (statusKey === 'all' || cardStatus === statusKey) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function updateTripStatus(tripId, newStatus, newBayText) {
        const card = document.getElementById('trip-card-' + tripId);
        const badge = document.getElementById('status-badge-' + tripId);
        const bayText = document.getElementById('bay-text-' + tripId);

        card.setAttribute('data-status', newStatus);

        if (newStatus === 'bay') {
            badge.className = 'trip-status-indicator status-bay';
            badge.innerHTML = '<span class="pulse-dot"></span> Sudah Sandar di Area Muat (Bay)';
            bayText.innerText = newBayText;
            alert('Sukses! Armada trip #' + tripId + ' berhasil dicatat SANDAR DI AREA MUAT (BAY). Penumpang dapat mulai boarding.');
        } else if (newStatus === 'berangkat') {
            badge.className = 'trip-status-indicator status-berangkat';
            badge.innerHTML = 'Sudah Berangkat';
            bayText.innerText = newBayText;
            alert('Armada trip #' + tripId + ' telah resmi DIBERANGKATKAN menuju pool tujuan.');
        }

        setTimeout(() => {
            window.location.reload();
        }, 1200);
    }
</script>

</body>

</html>
