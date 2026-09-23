<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cockpit Driver Dashboard - Operasional Travel Minibus</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --brand-primary: #059669;
            --brand-primary-dark: #047857;
            --brand-primary-light: #ecfdf5;
            --brand-accent: #0284c7;
            --brand-dark: #0f172a;
            --brand-surface: #ffffff;
            --brand-bg: #f1f5f9;
            --brand-border: #e2e8f0;
            --danger: #ef4444;
            --danger-dark: #b91c1c;
            --warning: #f59e0b;
            --warning-dark: #b45309;
            --success: #10b981;
            --info: #3b82f6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --text-sub: #94a3b8;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 10px 25px -3px rgba(15, 23, 42, 0.1);
        }

        html {
            scroll-behavior: smooth;
        }

        [id^="section-"] {
            scroll-margin-top: 24px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--brand-bg);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
        }

        /* LAYOUT SHELL */
        .driver-shell {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR (Desktop) */
        .sidebar {
            width: 270px;
            background: #064e3b;
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            transition: all 0.25s ease;
        }

        .brand-zone {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #10b981, #047857);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .brand-text h2 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 11px;
            color: #6ee7b7;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .driver-mini-card {
            margin: 16px 14px;
            padding: 14px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-box {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #0284c7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 16px;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .driver-details {
            flex: 1;
            min-width: 0;
        }

        .driver-details h4 {
            font-size: 13.5px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .driver-details p {
            font-size: 11px;
            color: #a7f3d0;
            margin-top: 1px;
        }

        .live-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.25);
            color: #a7f3d0;
            margin-top: 4px;
        }

        .live-status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: pulse-dot 1.8s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }

        .nav-menu {
            flex: 1;
            padding: 8px 12px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6ee7b7;
            padding: 12px 12px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: #d1fae5;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: white;
            transform: translateX(3px);
        }

        .nav-link.active {
            background: #10b981;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .nav-icon {
            font-size: 16px;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link span:nth-child(2) {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-badge-pill {
            background: rgba(255, 255, 255, 0.16);
            color: #a7f3d0;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 0.3px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .menu-badge-live {
            background: #10b981;
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
            animation: pulse-dot 1.6s infinite;
        }

        .menu-badge-sos {
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            animation: pulse-ring 2s infinite;
        }

        .menu-badge-count {
            background: rgba(255, 255, 255, 0.22);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 10px;
        }

        .nav-link-emergency {
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.08);
            margin-top: 2px;
        }

        .nav-link-emergency:hover {
            background: #ef4444 !important;
            color: white !important;
            border-color: #ef4444 !important;
        }

        .sidebar-foot {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .btn-logout {
            width: 100%;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
        }

        /* MAIN CONTENT AREA */
        .main-canvas {
            margin-left: 270px;
            flex: 1;
            min-width: 0;
            padding: 24px 32px 60px;
        }

        /* EMERGENCY TOP BANNER */
        .emergency-quick-bar {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            color: white;
            border-radius: var(--radius-lg);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 20px rgba(185, 28, 28, 0.28);
            position: relative;
            overflow: hidden;
        }

        .emergency-quick-bar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: #fca5a5;
        }

        .emergency-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .emergency-pulsing-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        .emergency-text h3 {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.2px;
        }

        .emergency-text p {
            font-size: 12px;
            opacity: 0.9;
        }

        .emergency-actions-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-sos-trigger {
            background: white;
            color: #b91c1c;
            border: none;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }

        .btn-sos-trigger:hover {
            background: #fee2e2;
            transform: translateY(-2px);
        }

        .btn-sos-trigger:active {
            transform: translateY(0);
        }

        .btn-phone-quick {
            background: rgba(255,255,255,0.18);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-phone-quick:hover {
            background: rgba(255,255,255,0.3);
        }

        /* ================= 1. HEADER & IDENTITAS TUGAS ================= */
        .task-identity-header {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--brand-border);
            box-shadow: var(--shadow-sm);
            padding: 16px 24px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .task-vehicle-identity {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .task-vehicle-meta h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-dark);
            line-height: 1.2;
        }

        .task-vehicle-meta p {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .system-status-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .system-pill {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            padding: 7px 13px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .system-pill.gps-live {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .system-pill.conn-online {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .btn-header-sos {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
            transition: all 0.2s;
            animation: pulse-ring 2s infinite;
        }

        .btn-header-sos:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        /* ================= 2. STATUS & JADWAL PERJALANAN AKTIF ================= */
        .trip-summary-hero-card {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--brand-border);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .trip-hero-accent-bar {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: white;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .trip-route-display h2 {
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .trip-route-display p {
            font-size: 13px;
            color: #a7f3d0;
            margin-top: 3px;
        }

        .trip-stats-overview-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 20px 28px;
            background: #ffffff;
            border-bottom: 1px solid var(--brand-border);
        }

        .trip-stat-box {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .trip-stat-box .stat-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .trip-stat-box .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .trip-stat-box .stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .load-bar-wrap {
            width: 100%;
            height: 7px;
            background: #e2e8f0;
            border-radius: 10px;
            margin-top: 8px;
            overflow: hidden;
        }

        .load-bar-fill {
            height: 100%;
            background: #10b981;
            border-radius: 10px;
        }

        /* ================= 3. KARTU AKSI UTAMA (NEXT ACTION CARD) ================= */
        .next-action-card {
            background: #ffffff;
            border: 2px solid #059669;
            border-radius: var(--radius-xl);
            box-shadow: 0 8px 24px -4px rgba(5, 150, 105, 0.18);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .next-action-header {
            background: #ecfdf5;
            border-bottom: 1px solid #a7f3d0;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .next-action-tag {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #065f46;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .next-action-body {
            padding: 22px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .next-target-details {
            flex: 1;
            min-width: 280px;
        }

        .next-target-details h3 {
            font-size: 19px;
            font-weight: 800;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .next-address-box {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 8px;
            font-size: 13.5px;
            color: var(--brand-dark);
            line-height: 1.45;
        }

        .next-notes-box {
            font-size: 12.5px;
            color: #b45309;
            background: #fffbeb;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .next-action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 250px;
        }

        .nav-buttons-row {
            display: flex;
            gap: 10px;
        }

        .btn-nav-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-nav-action.gmaps {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .btn-nav-action.waze {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .btn-nav-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        /* SLIDER STATUS CONFIRMATION */
        .status-slider-wrap {
            position: relative;
            background: #f1f5f9;
            border: 2px solid #059669;
            border-radius: 40px;
            height: 52px;
            display: flex;
            align-items: center;
            padding: 3px;
            user-select: none;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
            touch-action: none;
            cursor: pointer;
        }

        .status-slider-track-text {
            position: absolute;
            width: 100%;
            text-align: center;
            font-size: 13px;
            font-weight: 800;
            color: #065f46;
            pointer-events: none;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            z-index: 1;
        }

        .status-slider-thumb {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            cursor: grab;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
            z-index: 2;
            transition: background 0.2s;
        }

        .status-slider-thumb:active {
            cursor: grabbing;
        }

        .status-slider-progress {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: rgba(16, 185, 129, 0.25);
            border-radius: 40px;
            pointer-events: none;
            z-index: 0;
            width: 0%;
        }

        .btn-main-status-trigger {
            background: #059669;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px 20px;
            font-size: 13.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
            transition: all 0.2s;
            width: 100%;
        }

        .btn-main-status-trigger:hover {
            background: #047857;
            transform: translateY(-2px);
        }

        /* ================= 5. QUICK ACTIONS STRIP ================= */
        .quick-action-strip {
            background: white;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 14px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .quick-action-strip-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .quick-action-buttons-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-quick-shortcut {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 9px 16px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--brand-dark);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-quick-shortcut:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .btn-quick-shortcut.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }
        .btn-quick-shortcut.warning:hover {
            background: #fef3c7;
        }

        .btn-quick-shortcut.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .btn-quick-shortcut.danger:hover {
            background: #fee2e2;
        }

        /* TOPBAR (Legacy fallback) */
        .topbar-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .topbar-titles h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--brand-dark);
            letter-spacing: -0.5px;
        }

        .topbar-titles p {
            color: var(--text-muted);
            font-size: 13.5px;
            margin-top: 2px;
        }

        .topbar-badges {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pill-badge {
            background: white;
            border: 1px solid var(--brand-border);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: var(--shadow-sm);
        }

        .gps-status-pill {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        /* ================= 1. JADWAL PERJALANAN & METRIK WAKTU SOPIR ================= */
        .trip-hero-card {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--brand-border);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .schedule-hero-header {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: white;
            padding: 22px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .schedule-header-info h2 {
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .schedule-header-info p {
            font-size: 13px;
            color: #a7f3d0;
            margin-top: 4px;
        }

        .trip-code-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trip-code-badge {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 8px 16px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .trip-code-badge .trip-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .trip-code-badge .trip-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 17px;
            font-weight: 800;
            color: #6ee7b7;
            letter-spacing: 0.5px;
        }

        /* 4 Core Schedule Metric Cards */
        .schedule-metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 22px 28px;
            background: #ffffff;
            border-bottom: 1px solid var(--brand-border);
        }

        .schedule-metric-card {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.2s ease;
        }

        .schedule-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
            border-color: #cbd5e1;
        }

        .schedule-metric-card.highlight {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .schedule-metric-card.highlight-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .schedule-metric-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .schedule-metric-icon {
            font-size: 20px;
        }

        .schedule-metric-tag {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .schedule-metric-val {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .schedule-metric-card.highlight .schedule-metric-val {
            color: #065f46;
        }

        .schedule-metric-card.highlight-blue .schedule-metric-val {
            color: #1e40af;
        }

        .schedule-metric-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Rhythm & Prep Time Guide */
        .schedule-rhythm-guide {
            padding: 22px 28px;
            background: #f8fafc;
            border-bottom: 1px solid var(--brand-border);
        }

        .rhythm-guide-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .rhythm-guide-title h4 {
            font-size: 14px;
            font-weight: 800;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rhythm-guide-title span.badge-pill {
            background: #dbeafe;
            color: #1e40af;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .rhythm-timeline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .rhythm-card {
            background: white;
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            padding: 14px 16px;
            position: relative;
            border-left: 4px solid #cbd5e1;
            transition: all 0.2s;
        }

        .rhythm-card:hover {
            box-shadow: var(--shadow-sm);
        }

        .rhythm-card.stage-prep { border-left-color: #f59e0b; }
        .rhythm-card.stage-pool { border-left-color: #3b82f6; }
        .rhythm-card.stage-boarding { border-left-color: #8b5cf6; }
        .rhythm-card.stage-depart { border-left-color: #10b981; }

        .rhythm-time-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            padding: 2px 8px;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .stage-prep .rhythm-time-badge { background: #fef3c7; color: #b45309; }
        .stage-pool .rhythm-time-badge { background: #dbeafe; color: #1e40af; }
        .stage-boarding .rhythm-time-badge { background: #ede9fe; color: #6d28d9; }
        .stage-depart .rhythm-time-badge { background: #d1fae5; color: #065f46; }

        .rhythm-card h5 {
            font-size: 13px;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 4px;
        }

        .rhythm-card p {
            font-size: 11.5px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        /* ================= 2. KENDARAAN YANG DITUGASKAN ================= */
        .assigned-vehicle-card {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--brand-border);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .assigned-vehicle-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .assigned-vehicle-header-info h3 {
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .assigned-vehicle-header-info p {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 3px;
        }

        .spj-badge-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .spj-pill {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 6px 14px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .spj-pill .spj-label {
            font-size: 9.5px;
            font-weight: 700;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .spj-pill .spj-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 800;
            color: #38bdf8;
        }

        /* 4 Vehicle Metrics: Pelat Nomor, Jenis/Tipe Mobil, Nomor Lambung, Lokasi Parkir */
        .vehicle-metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 22px 28px;
            background: #ffffff;
            border-bottom: 1px solid var(--brand-border);
        }

        .vehicle-metric-card {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .vehicle-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
            border-color: #cbd5e1;
        }

        .vehicle-metric-card.highlight-amber {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .vehicle-metric-card.highlight-emerald {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .vehicle-metric-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .vehicle-metric-icon {
            font-size: 20px;
        }

        .vehicle-metric-tag {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .vehicle-metric-val {
            font-size: 17px;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .vehicle-metric-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* SPJ Verification & Anti-Swap Banner */
        .vehicle-verification-banner {
            padding: 18px 28px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border-bottom: 1px solid #d1fae5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .verification-notice {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            max-width: 620px;
        }

        .verification-icon-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #059669;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .verification-text h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: #065f46;
        }

        .verification-text p {
            font-size: 12px;
            color: #047857;
            margin-top: 2px;
            line-height: 1.45;
        }

        .verification-checklist {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .check-item-chip {
            background: white;
            border: 1px solid #a7f3d0;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 700;
            color: #065f46;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 1px 2px rgba(5, 150, 105, 0.08);
        }

        .plate-emblem {
            background: #0f172a;
            color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .plate-emblem span {
            font-size: 10px;
            color: #94a3b8;
        }

        /* STEPPER PROGRESS BAR (STATUS PERJALANAN) */
        .stepper-container {
            padding: 24px 28px;
            border-bottom: 1px solid var(--brand-border);
            background: #fafaf9;
        }

        .stepper-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .stepper-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trip-status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-status-scheduled { background: #fef3c7; color: #b45309; }
        .badge-status-pickup { background: #dbeafe; color: #1d4ed8; }
        .badge-status-departed { background: #dcfce7; color: #15803d; }
        .badge-status-arrived { background: #f3e8ff; color: #7e22ce; }
        .badge-status-completed { background: #e2e8f0; color: #475569; }

        .stepper-track {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            position: relative;
            gap: 8px;
        }

        .step-item {
            position: relative;
            text-align: center;
            padding: 10px 6px;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--brand-border);
            transition: all 0.2s;
        }

        .step-item.active {
            border-color: #059669;
            background: #ecfdf5;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.15);
        }

        .step-item.passed {
            border-color: #cbd5e1;
            background: #f8fafc;
            opacity: 0.85;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            background: #e2e8f0;
            color: var(--text-muted);
        }

        .step-item.active .step-number {
            background: #059669;
            color: white;
            animation: bounce 1.4s infinite;
        }

        .step-item.passed .step-number {
            background: #10b981;
            color: white;
        }

        .step-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            display: block;
        }

        .step-time {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 2px;
            display: block;
        }

        /* ACTION BAR TRIGGER (BESAR, TACTILE) */
        .trip-action-bar {
            padding: 18px 28px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .action-instruction {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-instruction .info-icon {
            font-size: 22px;
        }

        .action-instruction p {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .btn-status-step {
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }

        .btn-status-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .btn-status-step:active {
            transform: translateY(0);
        }

        .btn-action-pickup { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .btn-action-departed { background: linear-gradient(135deg, #059669, #047857); }
        .btn-action-arrived { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
        .btn-action-complete { background: linear-gradient(135deg, #0f172a, #334155); }

        /* GRID DUA KOLOM OPERASIONAL */
        .cockpit-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        /* CARD UMUM */
        .card-panel {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--brand-border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .card-panel-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--brand-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }

        .card-panel-header h3 {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-panel-body {
            padding: 20px 22px;
            flex: 1;
        }

        /* SECTION: NAVIGASI & RUTE LANGSUNG */
        .waypoint-timeline {
            position: relative;
            padding-left: 28px;
        }

        .waypoint-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 10px;
            bottom: 20px;
            width: 2px;
            background: #cbd5e1;
        }

        .waypoint-node {
            position: relative;
            margin-bottom: 22px;
        }

        .waypoint-node:last-child {
            margin-bottom: 0;
        }

        .waypoint-marker {
            position: absolute;
            left: -28px;
            top: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: white;
            border: 3px solid #64748b;
            box-shadow: 0 0 0 3px white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .waypoint-node.passed .waypoint-marker {
            border-color: #10b981;
            background: #10b981;
            color: white;
        }

        .waypoint-node.active .waypoint-marker {
            border-color: #0284c7;
            background: #38bdf8;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.25);
        }

        .waypoint-box {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .waypoint-info h4 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .waypoint-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .waypoint-tag {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 4px;
            background: #e2e8f0;
            color: #475569;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .waypoint-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .btn-nav {
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .btn-nav-gmaps {
            background: #ea4335;
            color: white;
        }

        .btn-nav-gmaps:hover {
            background: #d93025;
            transform: translateY(-1px);
        }

        .btn-nav-waze {
            background: #33ccff;
            color: #003366;
        }

        .btn-nav-waze:hover {
            background: #00bfff;
            color: #002244;
            transform: translateY(-1px);
        }

        /* SECTION: DAFTAR & DETAIL MANIFEST PENUMPANG */
        .manifest-stats-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .stat-pill {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .stat-pill span {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
        }

        .stat-pill strong {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .manifest-search {
            margin-bottom: 14px;
            position: relative;
        }

        .manifest-search input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .manifest-search input:focus {
            border-color: #059669;
        }

        .manifest-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-sub);
            font-size: 15px;
        }

        .manifest-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 480px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .passenger-item {
            background: white;
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.2s;
        }

        .passenger-item:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .passenger-item.is-boarded {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .passenger-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .passenger-avatar-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seat-badge-pill {
            background: #0f172a;
            color: #ffffff;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #334155;
        }

        .passenger-name-wrap h4 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .passenger-name-wrap p {
            font-size: 11px;
            color: var(--text-muted);
        }

        .passenger-badges {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
            font-size: 10.5px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .badge-cod {
            background: #fef3c7;
            color: #b45309;
            font-size: 10.5px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .badge-boarded {
            background: #10b981;
            color: white;
            font-size: 10.5px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-waiting {
            background: #f1f5f9;
            color: #64748b;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .passenger-details-strip {
            background: #f8fafc;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 11.5px;
            color: #334155;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .passenger-details-strip span strong {
            color: var(--brand-dark);
        }

        .passenger-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-top: 6px;
            border-top: 1px dashed var(--brand-border);
        }

        .contact-group {
            display: flex;
            gap: 8px;
        }

        .btn-contact {
            padding: 6px 11px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-contact-wa {
            background: #25d366;
            color: white;
        }

        .btn-contact-tel {
            background: #0284c7;
            color: white;
        }

        .btn-checkin-action {
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-checkin-action.btn-board {
            background: #059669;
            color: white;
        }

        .btn-checkin-action.btn-board:hover {
            background: #047857;
        }

        .btn-checkin-action.btn-unboard {
            background: #e2e8f0;
            color: #475569;
        }

        /* SECTION: DENAH KURSI (SEAT MAP) */
        .seatmap-container {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-lg);
            padding: 18px;
            position: relative;
        }

        .cabin-layout {
            max-width: 260px;
            margin: 0 auto;
            background: white;
            border: 2px solid #cbd5e1;
            border-radius: 24px 24px 14px 14px;
            padding: 20px 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }

        .cockpit-front {
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .driver-seat {
            background: #0f172a;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .windshield-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .seat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 8px;
        }

        .seat-slot {
            flex: 1;
            height: 48px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
            border: 1.5px solid transparent;
        }

        .seat-slot:hover {
            transform: scale(1.05);
            z-index: 2;
        }

        .seat-slot.empty {
            background: #f1f5f9;
            color: #94a3b8;
            border-color: #cbd5e1;
        }

        .seat-slot.pending {
            background: #fef3c7;
            color: #b45309;
            border-color: #fcd34d;
        }

        .seat-slot.boarded {
            background: #dcfce7;
            color: #15803d;
            border-color: #86efac;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.2);
        }

        .seat-slot small {
            font-size: 8px;
            font-weight: 700;
            line-height: 1;
        }

        .seatmap-legend {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 14px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* SECTION: GPS TRACKING REAL-TIME PANEL */
        .gps-panel {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            border-radius: var(--radius-xl);
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: var(--shadow-md);
            flex-wrap: wrap;
        }

        .gps-status-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .radar-box {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            position: relative;
        }

        .radar-box::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 1px solid #10b981;
            animation: radar-wave 2s infinite linear;
        }

        @keyframes radar-wave {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .gps-info h3 {
            font-size: 15.5px;
            font-weight: 800;
            color: white;
        }

        .gps-info p {
            font-size: 12px;
            color: #94a3b8;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
        }

        .gps-action-btn {
            background: #10b981;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .gps-action-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        /* SECTION: RIWAYAT PERJALANAN (RITASE) */
        .history-table-wrap {
            overflow-x: auto;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            text-align: left;
        }

        .history-table th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            padding: 12px 14px;
            border-bottom: 1px solid var(--brand-border);
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.5px;
        }

        .history-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--brand-dark);
        }

        .history-table tr:hover td {
            background: #f8fafc;
        }

        /* MODAL DIALOGS */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 520px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: modal-enter 0.2s ease-out;
        }

        @keyframes modal-enter {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--brand-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
        }

        .modal-body {
            padding: 22px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--brand-dark);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #059669;
        }

        .modal-footer {
            padding: 16px 22px;
            background: #f8fafc;
            border-top: 1px solid var(--brand-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-secondary {
            background: white;
            border: 1px solid var(--brand-border);
            color: var(--text-main);
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #059669;
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-danger-sos {
            background: #dc2626;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* ALERT TOAST */
        .toast-banner {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s;
        }

        .toast-banner.error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* RESPONSIVE */
        @media (max-width: 1080px) {
            .cockpit-grid {
                grid-template-columns: 1fr;
            }
            .schedule-metric-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .rhythm-timeline {
                grid-template-columns: repeat(2, 1fr);
            }
            .vehicle-metric-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-canvas {
                margin-left: 0;
                padding: 16px;
            }
            .schedule-hero-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .trip-code-badge {
                align-items: flex-start;
            }
            .schedule-metric-grid {
                grid-template-columns: 1fr;
            }
            .rhythm-timeline {
                grid-template-columns: 1fr;
            }
            .assigned-vehicle-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .spj-pill {
                align-items: flex-start;
            }
            .vehicle-metric-grid {
                grid-template-columns: 1fr;
            }
            .vehicle-verification-banner {
                flex-direction: column;
                align-items: flex-start;
            }
            .stepper-track {
                grid-template-columns: repeat(2, 1fr);
            }
            .emergency-quick-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }
            .emergency-actions-row {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

<div class="driver-shell">

    <!-- ================= SIDEBAR OPERASIONAL ================= -->
    <aside class="sidebar" id="sidebar">

        <div class="brand-zone">
            <div class="brand-logo-icon">
                🚐
            </div>
            <div class="brand-text">
                <h2>TRAVEL MINIBUS</h2>
                <span>Cockpit Driver v2.0</span>
            </div>
        </div>

        <div class="driver-mini-card">
            <div class="avatar-box">
                {{ strtoupper(substr($user->name ?? 'DR', 0, 2)) }}
            </div>
            <div class="driver-details">
                <h4>{{ $user->name ?? 'Pengemudi' }}</h4>
                <p>{{ $driver->license_type ?? 'SIM B1 Umum' }}</p>
                <div class="live-status-pill">
                    <span class="dot"></span>
                    <span>{{ strtoupper($driver->statusLabel()) }}</span>
                </div>
            </div>
        </div>

        <nav class="nav-menu">
            <!-- 1. Dashboard -->
            <a href="#section-dashboard" class="nav-link active">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <div class="nav-label">Jadwal & Armada</div>

            <!-- 2. Melihat jadwal perjalanan -->
            <a href="#section-jadwal" class="nav-link">
                <span class="nav-icon">📅</span>
                <span>Melihat Jadwal Perjalanan</span>
            </a>

            <!-- 3. Melihat kendaraan yang ditugaskan -->
            <a href="#section-kendaraan" class="nav-link">
                <span class="nav-icon">🚐</span>
                <span>Melihat Kendaraan Ditugaskan</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Penumpang & Kabin</div>

            <!-- 4. Melihat daftar penumpang -->
            <a href="#section-manifest" class="nav-link">
                <span class="nav-icon">👥</span>
                <span>Melihat Daftar Penumpang</span>
                <span class="menu-badge-count">{{ $totalBookedPassengers ?? 0 }}</span>
            </a>

            <!-- 5. Melihat seat map (cukup sebagai menu pelengkap) -->
            <a href="#section-seatmap" class="nav-link">
                <span class="nav-icon">💺</span>
                <span>Melihat Seat Map</span>
                <span class="menu-badge-pill">Pelengkap</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Rute & Status Perjalanan</div>

            <!-- 6. Melihat rute perjalanan -->
            <a href="#section-rute" class="nav-link">
                <span class="nav-icon">🗺️</span>
                <span>Melihat Rute Perjalanan</span>
            </a>

            <!-- 7. Melihat status perjalanan -->
            <a href="#section-status" class="nav-link">
                <span class="nav-icon">🧭</span>
                <span>Melihat Status Perjalanan</span>
            </a>

            <!-- 8. Mengubah status perjalanan -->
            <a href="#section-ubah-status" class="nav-link">
                <span class="nav-icon">⚡</span>
                <span>Mengubah Status Perjalanan</span>
            </a>

            <!-- 9. Melihat informasi penumpang -->
            <a href="#section-info-penumpang" class="nav-link">
                <span class="nav-icon">👤</span>
                <span>Melihat Informasi Penumpang</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Bantuan, Pelacakan & Arsip</div>

            <!-- 10. Melaporkan masalah perjalanan -->
            <a href="javascript:void(0)" onclick="openIncidentModal()" class="nav-link">
                <span class="nav-icon">⚠️</span>
                <span>Melaporkan Masalah Perjalanan</span>
            </a>

            <!-- 11. Emergency Center -->
            <a href="javascript:void(0)" onclick="openSosModal()" class="nav-link nav-link-emergency">
                <span class="nav-icon">🚨</span>
                <span>Emergency Center</span>
                <span class="menu-badge-sos">SOS</span>
            </a>

            <!-- 12. GPS Tracking saat perjalanan aktif -->
            <a href="#section-gps" class="nav-link">
                <span class="nav-icon">📍</span>
                <span>GPS Tracking Saat Perjalanan Aktif</span>
                <span class="menu-badge-live">LIVE</span>
            </a>

            <!-- 13. Melihat riwayat perjalanan (cukup di tab riwayat/arsip) -->
            <a href="#section-riwayat" class="nav-link">
                <span class="nav-icon">🕘</span>
                <span>Melihat Riwayat Perjalanan</span>
                <span class="menu-badge-pill">Arsip</span>
            </a>
        </nav>

        <div class="sidebar-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <span>↪</span>
                    <span>Keluar / Ganti Akun</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-canvas" id="section-dashboard">

        <!-- FLASH NOTIFICATION -->
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

        <!-- ================= 1. HEADER & IDENTITAS TUGAS ================= -->
        <header class="task-identity-header">
            <div class="task-vehicle-identity">
                <div class="plate-emblem">
                    <span>POL</span>
                    <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                </div>
                <div class="task-vehicle-meta">
                    <h3>{{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}</h3>
                    <p>
                        Tipe: <strong>{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }}</strong>
                        &nbsp;•&nbsp; No. Lambung: <strong>{{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}</strong>
                        &nbsp;•&nbsp; Lokasi Parkir: <strong>{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}</strong>
                    </p>
                </div>
            </div>

            <div class="system-status-group">
                <div class="system-pill gps-live" id="topbarGpsBadge">
                    <span style="font-size:13px;">📡</span>
                    <span id="gpsTextIndicator">GPS: Aktif</span>
                </div>

                <div class="system-pill conn-online">
                    <span style="font-size:13px;">📶</span>
                    <span>Koneksi: Online (4G LTE)</span>
                </div>

                <div class="system-pill">
                    <span>📅</span>
                    <span>{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Tombol SOS (Merah): Akses darurat instan yang selalu terlihat di pojok atas -->
                <button type="button" class="btn-header-sos" onclick="openSosModal()" title="Buka Akses Darurat Instan">
                    <span>🚨</span>
                    <span>SOS DARURAT</span>
                </button>
            </div>
        </header>

        <!-- ================= 2. STATUS & JADWAL PERJALANAN AKTIF ================= -->
        <section class="trip-hero-card" id="section-jadwal">
            <!-- Header Jadwal Perjalanan & Rangkuman Trip Hari Itu -->
            <div class="schedule-hero-header">
                <div class="schedule-header-info">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                        <span class="badge-pill" style="background:rgba(255,255,255,0.18); color:#ffffff; font-size:11px; font-weight:800; border:1px solid rgba(255,255,255,0.25);">
                            BAGIAN 2: STATUS & JADWAL PERJALANAN AKTIF
                        </span>
                    </div>
                    <h2>
                        <span>📅</span>
                        <span>Melihat Jadwal Perjalanan</span>
                    </h2>
                    <p style="font-size:14.5px; margin-top:5px; color:#ffffff;">
                        Jadwal & Rute Utama: <strong>{{ $activeSchedule->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}</strong>
                        &nbsp;➔&nbsp;
                        <strong>{{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</strong>,
                        <strong>{{ $activeSchedule->formattedDepartureTime() }} WIB</strong>
                    </p>
                    <p style="font-size:12px; color:#a7f3d0; margin-top:2px;">
                        Jarak Tempuh: {{ $activeSchedule->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Estimasi Waktu Tiba: {{ $activeSchedule->estimatedArrivalTime() }} WIB
                    </p>
                </div>

                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                    <!-- Badge Status Saat Ini: Menunggu Keberangkatan, Proses Penjemputan, atau Dalam Perjalanan -->
                    <div class="trip-status-badge {{ $activeSchedule->tripStatusBadgeClass() }}" style="box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <span>Status Saat Ini: </span>
                        <strong>{{ $activeSchedule->tripStatusLabel() }}</strong>
                    </div>

                    <!-- Ringkasan Muatan: Kapasitas penumpang terisi (misal: 8 / 10 Kursi Terisi) -->
                    <div style="background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.2); border-radius:10px; padding:6px 14px; color:white; text-align:right;">
                        <span style="color:#94a3b8; font-size:10.5px; display:block; font-weight:700;">Ringkasan Muatan Kabin</span>
                        <strong style="color:#34d399; font-size:13.5px;">{{ $totalBookedPassengers }} / {{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi Terisi</strong>
                    </div>

                    <div class="trip-code-box" style="margin-top:2px;">
                        <div class="trip-code-badge" style="padding:4px 12px;">
                            <span class="trip-label" style="font-size:10px;">Nomor Trip / Perjalanan</span>
                            <span class="trip-value" style="font-size:13px;">{{ $activeSchedule->tripCode() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Metrik Inti Jadwal: Nomor Trip, Tanggal, Jam Keberangkatan, Estimasi Tiba -->
            <div class="schedule-metric-grid">
                <!-- 1. Nomor Trip / Perjalanan -->
                <div class="schedule-metric-card">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">🎫</span>
                        <span class="schedule-metric-tag">Nomor Trip</span>
                    </div>
                    <div class="schedule-metric-val" style="font-family:'JetBrains Mono', monospace; font-size:16px;">
                        {{ $activeSchedule->tripCode() }}
                    </div>
                    <div class="schedule-metric-sub">
                        Shuttle Reguler Eksekutif
                    </div>
                </div>

                <!-- 2. Tanggal Perjalanan -->
                <div class="schedule-metric-card">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">🗓️</span>
                        <span class="schedule-metric-tag">Tanggal Perjalanan</span>
                    </div>
                    <div class="schedule-metric-val" style="font-size:15px;">
                        {{ $activeSchedule->formattedDepartureDate() }}
                    </div>
                    <div class="schedule-metric-sub">
                        {{ $activeSchedule->isWeekendDeparture() ? 'Akhir Pekan (Padat Lalu Lintas)' : 'Hari Kerja (Jadwal Normal)' }}
                    </div>
                </div>

                <!-- 3. Jam Keberangkatan -->
                <div class="schedule-metric-card highlight">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">⏰</span>
                        <span class="schedule-metric-tag" style="color:#065f46;">Jam Keberangkatan</span>
                    </div>
                    <div class="schedule-metric-val">
                        {{ $activeSchedule->formattedDepartureTime() }}
                    </div>
                    <div class="schedule-metric-sub">
                        Titik Awal: {{ $activeSchedule->route?->origin?->name ?? 'Pool Dipatiukur' }}
                    </div>
                </div>

                <!-- 4. Estimasi Waktu Tiba -->
                <div class="schedule-metric-card highlight-blue">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">🏁</span>
                        <span class="schedule-metric-tag" style="color:#1e40af;">Estimasi Waktu Tiba</span>
                    </div>
                    <div class="schedule-metric-val">
                        {{ $activeSchedule->estimatedArrivalTime() }}
                    </div>
                    <div class="schedule-metric-sub">
                        Durasi Tempuh ± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }}
                    </div>
                </div>
            </div>

            <!-- Panduan Ritme Waktu & Persiapan Sopir (Kegunaan) -->
            <div class="schedule-rhythm-guide">
                <div class="rhythm-guide-title">
                    <h4>
                        <span>⏱️</span>
                        <span>Panduan Ritme Waktu & Persiapan Armada Sopir</span>
                    </h4>
                    <span class="badge-pill">Manajemen Waktu Sopir</span>
                </div>

                <div class="rhythm-timeline">
                    <!-- Fase 1: H-45 Menit Persiapan Armada -->
                    <div class="rhythm-card stage-prep">
                        <div class="rhythm-time-badge">
                            <span>🔧</span>
                            <span>{{ $activeSchedule->prepTime() }} (H-45)</span>
                        </div>
                        <h5>Persiapan Armada (Garasi)</h5>
                        <p>Cek kondisi fisik unit: periksa BBM, oli mesin, tekanan ban, kebersihan kabin, dan nyalakan AC agar dingin saat penumpang naik.</p>
                    </div>

                    <!-- Fase 2: H-30 Menit Tiba di Pool -->
                    <div class="rhythm-card stage-pool">
                        <div class="rhythm-time-badge">
                            <span>🅿️</span>
                            <span>{{ $activeSchedule->gatheringTime() }} (H-30)</span>
                        </div>
                        <h5>Tiba di Pool / Titik Awal</h5>
                        <p>Tiba tepat waktu di <strong>{{ $activeSchedule->route?->origin?->name ?? 'Pool Dipatiukur' }}</strong>, sandar rapi di bay keberangkatan, dan koordinasi dengan tim operasional pool.</p>
                    </div>

                    <!-- Fase 3: H-15 Menit Boarding -->
                    <div class="rhythm-card stage-boarding">
                        <div class="rhythm-time-badge">
                            <span>🧳</span>
                            <span>{{ $activeSchedule->boardingTime() }} (H-15)</span>
                        </div>
                        <h5>Boarding & Muat Bagasi</h5>
                        <p>Buka pintu kabin, sambut ramah penumpang, bantu muat bagasi ke kompartemen belakang, dan lakukan check-in kehadiran penumpang.</p>
                    </div>

                    <!-- Fase 4: H-0 Menit Meluncur -->
                    <div class="rhythm-card stage-depart">
                        <div class="rhythm-time-badge">
                            <span>🚀</span>
                            <span>{{ $activeSchedule->formattedDepartureTime() }} (H-0)</span>
                        </div>
                        <h5>Meluncur & Prediksi Ritme</h5>
                        <p>Berangkat tepat waktu menuju <strong>{{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</strong>. Jaga kecepatan aman, estimasi tiba pukul <strong>{{ $activeSchedule->estimatedArrivalTime() }}</strong>.</p>
                    </div>
                </div>
            </div>

            <!-- ================= 2. KENDARAAN YANG DITUGASKAN ================= -->
            <div class="assigned-vehicle-segment" id="section-kendaraan">
                <!-- Header Kartu Kendaraan -->
                <div class="assigned-vehicle-header">
                    <div class="assigned-vehicle-header-info">
                        <h3>
                            <span>🚐</span>
                            <span>Melihat Kendaraan yang Ditugaskan</span>
                        </h3>
                        <p>Armada operasional resmi terverifikasi sistem sesuai Surat Perintah Jalan (SPJ) dinas.</p>
                    </div>

                    <div class="spj-badge-box">
                        <div class="spj-pill">
                            <span class="spj-label">Surat Perintah Jalan (SPJ)</span>
                            <span class="spj-code">{{ $activeSchedule->vehicle?->spjNumber($activeSchedule) ?? 'SPJ/20260923/HCE-01' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 4 Metrik Inti: Pelat Nomor, Jenis/Tipe Mobil, Nomor Lambung, Lokasi Parkir -->
                <div class="vehicle-metric-grid">
                    <!-- 1. Nomor Polisi (Pelat Nomor) -->
                    <div class="vehicle-metric-card">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon">🔢</span>
                            <span class="vehicle-metric-tag">Nomor Polisi (Pelat)</span>
                        </div>
                        <div class="plate-emblem" style="margin-top: 4px; margin-bottom: 6px; width: fit-content;">
                            <span>POL</span>
                            <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                        </div>
                        <div class="vehicle-metric-sub">
                            Masa Berlaku STNK: {{ $activeSchedule->vehicle?->stnk_tax_due_date ? $activeSchedule->vehicle?->stnk_tax_due_date->format('d/m/Y') : 'Pajak & STNK Aktif' }}
                        </div>
                    </div>

                    <!-- 2. Jenis / Tipe Mobil -->
                    <div class="vehicle-metric-card">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon">🚐</span>
                            <span class="vehicle-metric-tag">Jenis / Tipe Mobil</span>
                        </div>
                        <div class="vehicle-metric-val">
                            {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Tipe: <strong>{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }}</strong> • Kapasitas: <strong>{{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi</strong>
                        </div>
                    </div>

                    <!-- 3. Nomor Lambung Armada -->
                    <div class="vehicle-metric-card highlight-amber">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon">🏷️</span>
                            <span class="vehicle-metric-tag" style="color: #b45309;">Nomor Lambung Armada</span>
                        </div>
                        <div class="vehicle-metric-val" style="font-family:'JetBrains Mono', monospace; font-size:20px; color:#b45309;">
                            {{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-01' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Tercetak pada Kaca Depan & Bodi Samping
                        </div>
                    </div>

                    <!-- 4. Lokasi Fisik Kendaraan Diparkir -->
                    <div class="vehicle-metric-card highlight-emerald">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon">🅿️</span>
                            <span class="vehicle-metric-tag" style="color: #065f46;">Lokasi Fisik Parkir</span>
                        </div>
                        <div class="vehicle-metric-val" style="color: #065f46; font-size:16px;">
                            {{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Timur Pool' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Jalur Standby Keberangkatan Siap Jalan
                        </div>
                    </div>
                </div>

                <!-- Banner Verifikasi SPJ: Memastikan Unit Tepat & Tanpa Tertukar (Kegunaan) -->
                <div class="vehicle-verification-banner">
                    <div class="verification-notice">
                        <div class="verification-icon-badge">
                            ✓
                        </div>
                        <div class="verification-text">
                            <h4>Pencocokan Fisik Unit & Verifikasi SPJ (Cegah Tertukar)</h4>
                            <p>Memastikan sopir membawa unit yang tepat sesuai surat perintah jalan tanpa tertukar dengan armada rekan lainnya di area pool atau garasi.</p>
                        </div>
                    </div>

                    <div class="verification-checklist">
                        <div class="check-item-chip">
                            <span>🏷️</span>
                            <span>Lambung: <strong>{{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-01' }}</strong></span>
                        </div>
                        <div class="check-item-chip">
                            <span>🛡️</span>
                            <span>Pelat: <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong></span>
                        </div>
                        <div class="check-item-chip">
                            <span>🅿️</span>
                            <span>Posisi: <strong>{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3' }}</strong></span>
                        </div>
                        <div class="check-item-chip" style="background:#f1f5f9; border-color:#cbd5e1; color:#334155;">
                            <span>📑</span>
                            <span>Status: <strong>{{ $activeSchedule->vehicle?->statusLabel() ?? 'Siap Jalan' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= 2. STATUS & PENGUBAH STATUS PERJALANAN ================= -->
            <div class="stepper-container" id="section-status">
                <div class="stepper-header">
                    <h3>
                        <span>📍</span>
                        <span>Alur Status Perjalanan Bertahap</span>
                    </h3>

                    <div class="trip-status-badge {{ $activeSchedule->tripStatusBadgeClass() }}">
                        {{ $activeSchedule->tripStatusLabel() }}
                    </div>
                </div>

                @php
                    $curKey = $activeSchedule->tripStatusKey();
                @endphp

                <div class="stepper-track">
                    <!-- Step 1: Belum Berangkat / Standby -->
                    <div class="step-item {{ $curKey === 'scheduled' ? 'active' : 'passed' }}">
                        <div class="step-number">{{ $curKey !== 'scheduled' ? '✓' : '1' }}</div>
                        <span class="step-title">Standby Pool</span>
                        <span class="step-time">Persiapan di Garasi</span>
                    </div>

                    <!-- Step 2: Mulai Jemput -->
                    <div class="step-item {{ $curKey === 'pickup' ? 'active' : (in_array($curKey, ['departed', 'arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">{{ in_array($curKey, ['departed', 'arrived', 'completed']) ? '✓' : '2' }}</div>
                        <span class="step-title">Mulai Jemput</span>
                        <span class="step-time">{{ $activeSchedule->pickup_started_at ? $activeSchedule->pickup_started_at->format('H:i').' WIB' : 'Titik Naik' }}</span>
                    </div>

                    <!-- Step 3: Dalam Perjalanan -->
                    <div class="step-item {{ $curKey === 'departed' ? 'active' : (in_array($curKey, ['arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">{{ in_array($curKey, ['arrived', 'completed']) ? '✓' : '3' }}</div>
                        <span class="step-title">Berangkat / Jalan</span>
                        <span class="step-time">{{ $activeSchedule->departed_at ? $activeSchedule->departed_at->format('H:i').' WIB' : 'Jalur Tol' }}</span>
                    </div>

                    <!-- Step 4: Tiba di Tujuan -->
                    <div class="step-item {{ $curKey === 'arrived' ? 'active' : ($curKey === 'completed' ? 'passed' : '') }}">
                        <div class="step-number">{{ $curKey === 'completed' ? '✓' : '4' }}</div>
                        <span class="step-title">Tiba di Tujuan</span>
                        <span class="step-time">{{ $activeSchedule->arrived_at ? $activeSchedule->arrived_at->format('H:i').' WIB' : 'Pool Kedatangan' }}</span>
                    </div>
                </div>
            </div>

            </div>
        </section>

        @php
            $unboardedPassenger = $manifestPassengers->firstWhere('is_boarded', false);
            $nextPassenger = $unboardedPassenger ?? $manifestPassengers->first();

            if ($unboardedPassenger) {
                $nextTargetTitle = $unboardedPassenger->name;
                $nextTargetType = 'Jemput Penumpang Berikutnya';
                $nextTargetAddress = $unboardedPassenger->pickup_point;
                $nextTargetNotes = !empty($unboardedPassenger->special_request) && $unboardedPassenger->special_request !== '-' 
                    ? $unboardedPassenger->special_request 
                    : $unboardedPassenger->baggage_notes;
                $nextSeatNumber = 'Seat ' . $unboardedPassenger->seat_number;
            } else {
                $nextTargetTitle = $activeSchedule->route?->destination?->name ?? 'Pool Kedatangan';
                $nextTargetType = 'Menuju Titik Turun Akhir';
                $nextTargetAddress = $activeSchedule->route?->destination?->address ?? ($activeSchedule->route?->destination?->name ?? 'Pool Semanggi Plaza');
                $nextTargetNotes = 'Semua penumpang telah naik kabin. Meluncur aman menuju destinasi akhir.';
                $nextSeatNumber = 'Semua Naik';
            }

            $targetQuery = urlencode($nextTargetAddress);
            $nextGmapsUrl = "https://www.google.com/maps/search/?api=1&query={$targetQuery}";
            $nextWazeUrl = "https://waze.com/ul?q={$targetQuery}&navigate=yes";
        @endphp

        <!-- ================= 3. KARTU AKSI UTAMA (NEXT ACTION CARD) ================= -->
        <section class="next-action-card" id="section-ubah-status">
            <div class="next-action-header">
                <div class="next-action-tag">
                    <span>⚡</span>
                    <span>3. Kartu Aksi Utama (Next Action Card)</span>
                    <span style="background:#059669; color:white; font-size:10px; padding:2px 8px; border-radius:12px; margin-left:6px; font-weight:800;">
                        Aksi Cepat Driver Saat Bergerak
                    </span>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="pill-badge" style="background:#ffffff; border-color:#86efac; color:#15803d; font-size:11px; font-weight:800;">
                        💺 {{ $nextSeatNumber }}
                    </span>
                    <span class="pill-badge" style="background:#f0fdf4; border-color:#86efac; color:#065f46; font-size:11px; font-weight:700;">
                        {{ $nextTargetType }}
                    </span>
                </div>
            </div>

            <div class="next-action-body">
                <!-- Titik Tugas Berikutnya -->
                <div class="next-target-details">
                    <span style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">
                        Titik Tugas Berikutnya
                    </span>
                    <h3>
                        <span>👤</span>
                        <span>{{ $nextTargetTitle }}</span>
                        @if ($unboardedPassenger && !$unboardedPassenger->is_paid)
                            <span class="badge-cod" style="font-size:11px;">Tagihan Tunai COD: Rp {{ number_format($unboardedPassenger->amount_due, 0, ',', '.') }}</span>
                        @elseif ($unboardedPassenger && $unboardedPassenger->is_paid)
                            <span class="badge-paid" style="font-size:11px;">Tiket Lunas</span>
                        @endif
                    </h3>

                    <div class="next-address-box">
                        <div style="font-weight:700; color:var(--brand-dark); display:flex; align-items:center; gap:6px;">
                            <span>📍</span>
                            <span>Alamat Lengkap Titik Jemput / Turun:</span>
                        </div>
                        <div style="margin-top:4px; font-size:13.5px; color:#1e293b; font-weight:700;">
                            {{ $nextTargetAddress }}
                        </div>
                    </div>

                    <div class="next-notes-box">
                        <span>🏡</span>
                        <span><strong>Catatan Patokan Rumah & Bawaan:</strong> {{ $nextTargetNotes }}</span>
                    </div>
                </div>

                <!-- Tombol Navigasi 1-Klik & Tombol Pengubah Status -->
                <div class="next-action-buttons">
                    <!-- Tombol Navigasi 1-Klik: Google Maps & Waze -->
                    <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                        Tombol Navigasi 1-Klik Langsung
                    </div>
                    <div class="nav-buttons-row">
                        <a href="{{ $nextGmapsUrl }}" target="_blank" class="btn-nav-action gmaps" id="btnNextActionGmaps" title="Buka rute titik tugas di Google Maps">
                            <span>🗺️</span>
                            <span>Google Maps</span>
                        </a>
                        <a href="{{ $nextWazeUrl }}" target="_blank" class="btn-nav-action waze" id="btnNextActionWaze" title="Buka rute titik tugas di Waze">
                            <span>🚙</span>
                            <span>Waze</span>
                        </a>
                    </div>

                    <!-- Tombol Pengubah Status: Slider konfirmasi atau tombol aksi bertahap -->
                    <div style="margin-top:6px;">
                        <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                            Tombol Pengubah Status
                        </div>

                        @if ($nextAction)
                            <form method="POST" id="mainStatusForm" action="{{ route('dashboard.driver.schedule.status', $activeSchedule->id ?? 1) }}" onsubmit="return confirm('{{ $nextAction['prompt'] }}');">
                                @csrf
                                <input type="hidden" name="status" value="{{ $nextAction['next_key'] }}">
                                <input type="hidden" name="latitude" id="sliderLat" value="">
                                <input type="hidden" name="longitude" id="sliderLng" value="">

                                <!-- Interactive Slider Confirmation -->
                                <div class="status-slider-wrap" id="statusSliderWrap" title="Geser ke kanan untuk konfirmasi status">
                                    <div class="status-slider-progress" id="statusSliderProgress"></div>
                                    <div class="status-slider-track-text" id="statusSliderText">
                                        <span>👉 Geser untuk Konfirmasi: {{ $nextAction['label'] }}</span>
                                    </div>
                                    <div class="status-slider-thumb" id="statusSliderThumb">
                                        ➔
                                    </div>
                                </div>

                                <!-- Direct Click Trigger -->
                                <button type="submit" class="btn-main-status-trigger" style="margin-top:8px;">
                                    <span>{{ $nextAction['icon'] }}</span>
                                    <span>{{ $nextAction['label'] }}</span>
                                </button>
                            </form>
                        @elseif ($unboardedPassenger && $unboardedPassenger->passenger_model)
                            <form method="POST" action="{{ route('dashboard.driver.passenger.checkin', $unboardedPassenger->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="boarding_status" value="boarded">
                                @if (!$unboardedPassenger->is_paid)
                                    <input type="hidden" name="mark_paid" value="1">
                                @endif
                                <button type="submit" class="btn-main-status-trigger">
                                    <span>✓</span>
                                    <span>Penumpang Sudah Naik ({{ $unboardedPassenger->name }})</span>
                                </button>
                            </form>
                        @else
                            <div class="pill-badge" style="background:#f1f5f9; color:#475569; font-weight:700; width:100%; justify-content:center; padding:12px;">
                                ✓ Perjalanan Ini Telah Ditutup & Selesai
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= 4. MANIFES & INFORMASI CEPAT PENUMPANG ================= -->
        <section class="card-panel" id="section-manifest" style="margin-bottom: 24px;">
            <div class="card-panel-header">
                <div>
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                        BAGIAN 4: INFORMASI KABIN
                    </div>
                    <h3>
                        <span>📋</span>
                        <span>Manifes & Informasi Cepat Penumpang</span>
                    </h3>
                    <p style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
                        Daftar ringkas penumpang dengan tombol telepon & WhatsApp 1-klik tanpa simpan nomor, alokasi kursi kabin, status pembayaran (Lunas / COD), dan catatan bawaan khusus.
                    </p>
                </div>

                <div style="display:flex; gap:8px;">
                    <a href="#section-seatmap" class="btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <span>💺</span>
                        <span>Lihat Denah Kursi</span>
                    </a>
                </div>
            </div>

            <div class="card-panel-body">
                <!-- STATISTIK MANIFEST -->
                <div class="manifest-stats-strip">
                    <div class="stat-pill">
                        <span>Total Dipesan</span>
                        <strong>{{ $totalBookedPassengers }} Orang</strong>
                    </div>
                    <div class="stat-pill" style="background:#f0fdf4; border-color:#bbf7d0;">
                        <span style="color:#15803d;">Sudah Naik Kabin</span>
                        <strong style="color:#15803d;">{{ $boardedCount }}</strong>
                    </div>
                    <div class="stat-pill" style="background:#fef2f2; border-color:#fecaca;">
                        <span style="color:#991b1b;">Belum Naik</span>
                        <strong style="color:#991b1b;">{{ $pendingBoardingCount }}</strong>
                    </div>
                </div>

                <!-- PENCARIAN PENUMPANG -->
                <div class="manifest-search">
                    <span class="manifest-search-icon">🔍</span>
                    <input type="text" id="manifestSearchInput" placeholder="Ketik nama penumpang atau nomor kursi untuk menyaring..." onkeyup="filterManifest()">
                </div>

                <!-- LIST MANIFEST PENUMPANG -->
                <div class="manifest-list" id="section-info-penumpang">
                    @forelse ($manifestPassengers as $p)
                        <div class="passenger-item {{ $p->is_boarded ? 'is-boarded' : '' }}" data-name="{{ strtolower($p->name) }}" data-seat="{{ strtolower($p->seat_number) }}">
                            <div class="passenger-top">
                                <div class="passenger-avatar-box">
                                    <div class="seat-badge-pill">
                                        Seat {{ $p->seat_number }}
                                    </div>
                                    <div class="passenger-name-wrap">
                                        <h4>{{ $p->name }}</h4>
                                        <p>{{ $p->booking_code }}</p>
                                    </div>
                                </div>

                                <div class="passenger-badges">
                                    <span class="{{ $p->payment_badge_class }}">
                                        {{ $p->payment_label }}
                                    </span>

                                    @if ($p->is_boarded)
                                        <span class="badge-boarded">
                                            ✓ NAIK ({{ $p->boarded_at }})
                                        </span>
                                    @else
                                        <span class="badge-waiting">
                                            Belum Naik
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- DETAIL JEMPUT & CATATAN BAWAAN -->
                            <div class="passenger-details-strip">
                                <div>📍 Titik Jemput: <strong>{{ $p->pickup_point }}</strong> &nbsp;➔&nbsp; Turun: <strong>{{ $p->dropoff_point }}</strong></div>
                                <div>🧳 Catatan Bawaan: <strong>{{ $p->baggage_notes }}</strong></div>
                                @if (!empty($p->special_request) && $p->special_request !== '-')
                                    <div style="color:#b45309;">💬 Catatan / Patokan Rumah: {{ $p->special_request }}</div>
                                @endif
                                @if (!$p->is_paid && $p->amount_due > 0)
                                    <div style="color:#dc2626; font-weight:700;">💵 Tagihan Tunai (COD): Rp {{ number_format($p->amount_due, 0, ',', '.') }}</div>
                                @endif
                            </div>

                            <!-- ACTION BAR: KONTAK CEPAT & CHECK-IN -->
                            <div class="passenger-actions">
                                <div class="contact-group">
                                    <!-- Tombol WhatsApp langsung tanpa simpan nomor -->
                                    <a href="https://wa.me/{{ $p->wa_phone }}?text={{ urlencode('Halo Kak ' . $p->name . ', saya pengemudi Travel Minibus untuk jadwal hari ini. Saya ingin mengonfirmasi penjemputan di ' . $p->pickup_point . '.') }}"
                                       target="_blank"
                                       class="btn-contact btn-contact-wa"
                                       title="Kirim Pesan WhatsApp Langsung Tanpa Simpan Nomor">
                                        <span>💬</span>
                                        <span>WhatsApp</span>
                                    </a>

                                    <!-- Tombol Panggil Telepon langsung -->
                                    <a href="tel:{{ $p->phone }}"
                                       class="btn-contact btn-contact-tel"
                                       title="Panggil Telepon Langsung">
                                        <span>📞</span>
                                        <span>Telepon</span>
                                    </a>
                                </div>

                                <div>
                                    @if ($p->passenger_model)
                                        <form method="POST" action="{{ route('dashboard.driver.passenger.checkin', $p->id) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')

                                            @if (!$p->is_boarded)
                                                <input type="hidden" name="boarding_status" value="boarded">
                                                @if (!$p->is_paid)
                                                    <input type="hidden" name="mark_paid" value="1">
                                                @endif
                                                <button type="submit" class="btn-checkin-action btn-board">
                                                    <span>✓</span>
                                                    <span>{{ !$p->is_paid ? 'Terima COD & Naik' : 'Konfirmasi Naik' }}</span>
                                                </button>
                                            @else
                                                <input type="hidden" name="boarding_status" value="belum_hadir">
                                                <button type="submit" class="btn-checkin-action btn-unboard">
                                                    <span>↩ Batalkan Naik</span>
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <!-- Demo fallback action -->
                                        <button type="button" class="btn-checkin-action {{ $p->is_boarded ? 'btn-unboard' : 'btn-board' }}" onclick="alert('Simulasi: Status check-in untuk {{ $p->name }} berhasil diperbarui!')">
                                            <span>✓</span>
                                            <span>{{ $p->is_boarded ? 'Batalkan Naik' : 'Konfirmasi Naik' }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:30px; color:var(--text-muted);">
                            Belum ada manifes penumpang untuk jadwal ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ================= 5. TOMBOL KENDALA OPERASIONAL (QUICK ACTIONS) ================= -->
        <section class="quick-action-strip" id="section-quick-actions" style="margin-bottom: 24px;">
            <div class="quick-action-strip-title">
                <span style="font-size:22px;">⚡</span>
                <div>
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.5px;">BAGIAN 5: OPERASIONAL LAPANGAN</div>
                    <strong style="font-size:15px; color:var(--brand-dark);">Tombol Kendala Operasional (Quick Actions)</strong>
                </div>
            </div>

            <div class="quick-action-buttons-group">
                <!-- Lapor Kendala: Tombol lapis kedua untuk memilih masalah lapangan (ban bocor, macet parah, mogok, penumpang tidak bisa dihubungi) -->
                <button type="button" class="btn-quick-shortcut warning" onclick="openIncidentModal()" title="Buka menu lapis kedua memilih kendala lapangan">
                    <span>⚠️</span>
                    <span>Lapor Kendala Lapangan</span>
                </button>

                <!-- Denah Kursi (Seat Map): Tombol pintasan untuk melihat denah visual penataan bangku kabin -->
                <a href="#section-seatmap" class="btn-quick-shortcut" title="Buka denah visual tata letak kursi kabin">
                    <span>💺</span>
                    <span>Denah Kursi (Seat Map)</span>
                </a>

                <!-- Emergency Center SOS -->
                <button type="button" class="btn-quick-shortcut danger" onclick="openSosModal()" title="Akses darurat instan">
                    <span>🚨</span>
                    <span>Emergency Center SOS</span>
                </button>

                <!-- Hubungi Dispatcher Pool -->
                <a href="tel:081122334455" class="btn-quick-shortcut" title="Hubungi Dispatcher Pool Resmi">
                    <span>📞</span>
                    <span>Hubungi Dispatcher Pool</span>
                </a>
            </div>
        </section>

        <!-- ================= 6. GPS TRACKING AKTIF (BACKGROUND & LIVE) ================= -->
        <section class="gps-panel" id="section-gps">
            <div class="gps-status-group">
                <div class="radar-box">
                    🛰️
                </div>
                <div class="gps-info">
                    <h3>GPS Tracking Latar Belakang Aktif</h3>
                    <p id="gpsCoordsDisplay">
                        @if ($activeSchedule->current_latitude && $activeSchedule->current_longitude)
                            Lat: {{ $activeSchedule->current_latitude }}, Lng: {{ $activeSchedule->current_longitude }}
                            (Diperbarui {{ $activeSchedule->last_gps_at ? $activeSchedule->last_gps_at->diffForHumans() : 'baru saja' }})
                        @else
                            Menunggu izin browser & mendeteksi sinyal satelit GPS...
                        @endif
                    </p>
                </div>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="gps-action-btn" onclick="triggerManualGpsSync()">
                    <span>🔄</span>
                    <span>Perbarui Posisi Sekarang</span>
                </button>
            </div>
        </section>

        <!-- COCKPIT DUA KOLOM: NAVIGASI WAYPOINTS & DENAH KURSI -->
        <div class="cockpit-grid">

            <!-- ================= NAVIGASI & RUTE LANGSUNG ================= -->
            <div class="card-panel" id="section-rute">
                <div class="card-panel-header">
                    <h3>
                        <span>🗺️</span>
                        <span>Navigasi & Rute Langsung (Waypoints)</span>
                    </h3>
                    <span class="pill-badge" style="font-size:11px;">1-Klik Maps & Waze</span>
                </div>

                <div class="card-panel-body">
                    <div class="waypoint-timeline">
                        @foreach ($waypoints as $wp)
                            <div class="waypoint-node {{ $wp['status'] }}">
                                <div class="waypoint-marker">
                                    {{ $loop->iteration }}
                                </div>

                                <div class="waypoint-box">
                                    <div class="waypoint-info">
                                        <span class="waypoint-tag">{{ $wp['badge'] }}</span>
                                        <h4>{{ $wp['title'] }}</h4>
                                        <p>{{ $wp['subtitle'] }}</p>
                                    </div>

                                    <div class="waypoint-actions">
                                        <a href="{{ $wp['gmaps_url'] }}" target="_blank" class="btn-nav btn-nav-gmaps" title="Buka Google Maps">
                                            <span>🗺️</span>
                                            <span>Maps</span>
                                        </a>

                                        <a href="{{ $wp['waze_url'] }}" target="_blank" class="btn-nav btn-nav-waze" title="Buka Waze">
                                            <span>🚙</span>
                                            <span>Waze</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ================= DENAH KURSI (SEAT MAP) ================= -->
            <div class="card-panel" id="section-seatmap">
                <div class="card-panel-header">
                    <h3>
                        <span>💺</span>
                        <span>Denah Kursi Kabin Minibus</span>
                    </h3>
                    <span class="pill-badge" style="font-size:11px;">{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce' }}</span>
                </div>

                <div class="card-panel-body">
                    <div class="seatmap-container">
                        <div class="cabin-layout">
                            <!-- DEPAN / WINDSHIELD -->
                            <div class="cockpit-front">
                                <div class="driver-seat">
                                    <span>💺</span>
                                    <span>SOPIR</span>
                                </div>
                                <span class="windshield-label">Kaca Depan</span>
                            </div>

                            <!-- ROWS KURSI -->
                            @if (!empty($seatOccupancyRows))
                                @foreach ($seatOccupancyRows as $rowKey => $seatsInRow)
                                    <div class="seat-row">
                                        @foreach ($seatsInRow as $seatItem)
                                            @php
                                                $occ = $seatItem['occupancy']['status'] ?? 'available';
                                                $pName = $seatItem['occupancy']['passenger_name'] ?? 'Kosong';
                                                $isBoarded = false;

                                                // Cari apakah penumpang di kursi ini sudah naik
                                                foreach ($manifestPassengers as $mp) {
                                                    if ($mp->seat_number == $seatItem['seat_number'] && $mp->is_boarded) {
                                                        $isBoarded = true;
                                                        break;
                                                    }
                                                }

                                                $slotClass = $isBoarded ? 'boarded' : ($occ !== 'available' ? 'pending' : 'empty');
                                            @endphp

                                            <div class="seat-slot {{ $slotClass }}"
                                                 onclick="showSeatInfo('{{ $seatItem['seat_number'] }}', '{{ addslashes($pName) }}', '{{ $slotClass }}')"
                                                 title="Kursi {{ $seatItem['seat_number'] }}: {{ $pName }}">
                                                <strong>{{ $seatItem['seat_number'] }}</strong>
                                                <small>{{ $isBoarded ? 'NAIK' : ($occ !== 'available' ? 'TERISI' : 'KOSONG') }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback Default Minibus 10 Seats -->
                                <div class="seat-row">
                                    <div class="seat-slot boarded" onclick="showSeatInfo('1A', 'Budi Santoso', 'boarded')">
                                        <strong>1A</strong><small>NAIK</small>
                                    </div>
                                    <div style="flex:1;"></div>
                                </div>
                                <div class="seat-row">
                                    <div class="seat-slot pending" onclick="showSeatInfo('2A', 'Siti Rahmawati', 'pending')">
                                        <strong>2A</strong><small>TERISI</small>
                                    </div>
                                    <div class="seat-slot pending" onclick="showSeatInfo('2B', 'Hendra Gunawan', 'pending')">
                                        <strong>2B</strong><small>TERISI</small>
                                    </div>
                                </div>
                                <div class="seat-row">
                                    <div class="seat-slot empty" onclick="showSeatInfo('3A', 'Kosong', 'empty')">
                                        <strong>3A</strong><small>KOSONG</small>
                                    </div>
                                    <div class="seat-slot empty" onclick="showSeatInfo('3B', 'Kosong', 'empty')">
                                        <strong>3B</strong><small>KOSONG</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="seatmap-legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background:#10b981;"></div>
                                <span>Sudah Naik</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#f59e0b;"></div>
                                <span>Dipesan (Belum Naik)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#cbd5e1;"></div>
                                <span>Kosong</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= 7. LAPOR KENDALA AKTIF ================= -->
        @if ($activeIncidents->isNotEmpty())
            <section class="card-panel" style="margin-bottom: 24px; border-color: #fcd34d;">
                <div class="card-panel-header" style="background:#fffbeb;">
                    <h3 style="color:#b45309;">
                        <span>⚠️</span>
                        <span>Kendala Jalan Terlaporkan pada Perjalanan Ini</span>
                    </h3>
                </div>
                <div class="card-panel-body">
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($activeIncidents as $inc)
                            <div style="background:#fef3c7; padding:12px 16px; border-radius:10px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <strong style="color:#92400e;">{{ $inc->categoryIcon() }} {{ $inc->categoryLabel() }}</strong>
                                    <p style="font-size:12px; color:#78350f; margin-top:2px;">{{ $inc->description }}</p>
                                    <small style="font-size:10.5px; color:#b45309;">Lokasi: {{ $inc->location_name }} • Dilaporkan: {{ $inc->reported_at?->format('H:i') }} WIB</small>
                                </div>
                                <span class="pill-badge" style="background:white; font-size:11px; font-weight:700;">
                                    {{ $inc->statusLabel() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ================= 10. RIWAYAT PERJALANAN (RITASE SELESAI) ================= -->
        <section class="card-panel" id="section-riwayat">
            <div class="card-panel-header">
                <div>
                    <h3>
                        <span>🕘</span>
                        <span>Riwayat Perjalanan (Ritase Selesai & Rekap Harian)</span>
                    </h3>
                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Daftar ritase operasional yang telah selesai untuk rekapitulasi harian.</p>
                </div>

                <div class="pill-badge" style="background:#ecfdf5; color:#065f46; font-weight:800;">
                    Total Selesai: {{ $totalCompletedTrips }} Perjalanan
                </div>
            </div>

            <div class="card-panel-body" style="padding:0;">
                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Armada</th>
                                <th>Rute Perjalanan</th>
                                <th>Penumpang</th>
                                <th>Status Ritase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedTrips as $index => $cTrip)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $cTrip->departure_date ? $cTrip->departure_date->format('d/m/Y') : date('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">{{ Carbon\Carbon::parse($cTrip->departure_time)->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <strong>{{ $cTrip->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">{{ $cTrip->vehicle?->name ?? 'Toyota HiAce' }}</div>
                                    </td>
                                    <td>
                                        {{ $cTrip->route?->origin?->name ?? 'Bandung' }} &nbsp;→&nbsp; {{ $cTrip->route?->destination?->name ?? 'Jakarta' }}
                                    </td>
                                    <td>
                                        <span class="seat-badge-pill" style="background:#059669; border-color:#059669;">
                                            {{ $cTrip->bookings?->sum('total_passenger') ?? 8 }} Orang
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">
                                            ✓ Ditutup Selesai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <!-- Contoh ritase dummy bila belum ada di DB -->
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <strong>{{ Carbon\Carbon::yesterday()->format('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">08:00 WIB</div>
                                    </td>
                                    <td>
                                        <strong>D 7812 AB</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">Toyota HiAce Premio Eksekutif</div>
                                    </td>
                                    <td>Bandung (Dipatiukur) → Jakarta (Semanggi)</td>
                                    <td><span class="seat-badge-pill" style="background:#059669;">10 Orang</span></td>
                                    <td><span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">✓ Selesai</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <strong>{{ Carbon\Carbon::yesterday()->format('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">14:30 WIB</div>
                                    </td>
                                    <td>
                                        <strong>D 7812 AB</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">Toyota HiAce Premio Eksekutif</div>
                                    </td>
                                    <td>Jakarta (Semanggi) → Bandung (Dipatiukur)</td>
                                    <td><span class="seat-badge-pill" style="background:#059669;">9 Orang</span></td>
                                    <td><span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">✓ Selesai</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

</div>

<!-- ================= MODAL: 7. LAPOR KENDALA JALAN ================= -->
<div class="modal-backdrop" id="modalIncident">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <span>⚠️</span>
                <span>Laporkan Kendala di Perjalanan</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeIncidentModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('dashboard.driver.schedule.incident', $activeSchedule->id ?? 1) }}">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="latitude" id="incidentLat" value="">
                <input type="hidden" name="longitude" id="incidentLng" value="">

                <div class="form-group">
                    <label>Jenis Kendala Jalan</label>
                    <select name="category" class="form-control" required>
                        <option value="ban_bocor">🛞 Ban Bocor / Kempes</option>
                        <option value="macet_parah">🚦 Macet Parah / Total (Keterlambatan)</option>
                        <option value="mesin_mogok">⚙️ Kendala Mesin / Radiator Panas</option>
                        <option value="penumpang_batal">👤 Penumpang Batal Mendadak / Tidak Datang</option>
                        <option value="kecelakaan">💥 Insiden Senggolan / Kecelakaan Jalan</option>
                        <option value="darurat_medis">🩺 Darurat Medis Penumpang</option>
                        <option value="lainnya">⚠️ Kendala Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tingkat Urgensi</label>
                    <select name="severity" class="form-control" required>
                        <option value="sedang">Sedang (Perjalanan terhambat sementara)</option>
                        <option value="rendah">Rendah (Pemberitahuan operasional)</option>
                        <option value="tinggi">Tinggi (Memerlukan armada cadangan / mekanik)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lokasi Terkini Armada</label>
                    <input type="text" name="location_name" id="incidentLocationInput" class="form-control" placeholder="Contoh: Tol Cipularang KM 88 Jalur A">
                </div>

                <div class="form-group">
                    <label>Keterangan Tambahan & Estimasi Solusi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Ceritakan kondisi kendala di lapangan secara singkat..." required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeIncidentModal()">Batal</button>
                <button type="submit" class="btn-primary">Kirim Laporan ke Pusat Kendali</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: 8. EMERGENCY SOS CENTER ================= -->
<div class="modal-backdrop" id="modalSos">
    <div class="modal-box" style="border: 2px solid #ef4444;">
        <div class="modal-header" style="background:#fee2e2;">
            <h3 style="color:#b91c1c;">
                <span>🚨</span>
                <span>KONFIRMASI AKTIVASI EMERGENCY SOS</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeSosModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('dashboard.driver.schedule.sos', $activeSchedule->id ?? 1) }}" id="formSos">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="latitude" id="sosLat" value="">
                <input type="hidden" name="longitude" id="sosLng" value="">

                <p style="font-size:13.5px; color:#7f1d1d; line-height:1.6; margin-bottom:16px;">
                    <strong>PERHATIAN!</strong> Fitur ini memancarkan sinyal darurat langsung ke ruang kendali operasional travel beserta koordinat GPS live Anda.
                </p>

                <div class="form-group">
                    <label>Keterangan Darurat (Opsional)</label>
                    <input type="text" name="description" class="form-control" placeholder="Contoh: Tabrakan beruntun di Tol KM 92, butuh ambulans">
                </div>

                <div style="background:#fef2f2; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-bottom:14px;">
                    <div style="font-weight:700; font-size:12px; color:#991b1b; margin-bottom:6px;">Nomor Panggilan Darurat Cepat:</div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="tel:081122334455" class="btn-contact btn-contact-tel">HQ: 0811-2233-4455</a>
                        <a href="tel:118" class="btn-contact btn-nav-gmaps">Ambulans (118)</a>
                        <a href="tel:110" class="btn-contact" style="background:#1e293b; color:white;">Polisi (110)</a>
                    </div>
                </div>

                <button type="submit" class="btn-danger-sos">
                    <span>🚨</span>
                    <span>PANCARKAN SINYAL SOS SEKARANG</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT LOGIC: GPS TRACKING REAL-TIME, MANIFEST FILTER, MODALS -->
<script>
    // 1. INSIALISASI GEOLOCATION API & BACKGROUND GPS SYNC
    let currentLat = null;
    let currentLng = null;
    const scheduleId = {{ $activeSchedule->id ?? 1 }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function initGeolocation() {
        if (!navigator.geolocation) {
            updateGpsUI(false, "Browser Anda tidak mendukung Geolocation API.");
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        // Watch Position live
        navigator.geolocation.watchPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                updateGpsUI(true, `Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)} (Akurasi: ±${Math.round(accuracy)}m)`);
                syncGpsWithServer(currentLat, currentLng, accuracy, pos.coords.speed, pos.coords.heading);

                // Set form hidden values
                const fLat = document.getElementById('formCurrentLat');
                const fLng = document.getElementById('formCurrentLng');
                const iLat = document.getElementById('incidentLat');
                const iLng = document.getElementById('incidentLng');
                const sLat = document.getElementById('sosLat');
                const sLng = document.getElementById('sosLng');

                const slLat = document.getElementById('sliderLat');
                const slLng = document.getElementById('sliderLng');

                if (fLat) fLat.value = currentLat;
                if (fLng) fLng.value = currentLng;
                if (slLat) slLat.value = currentLat;
                if (slLng) slLng.value = currentLng;
                if (iLat) iLat.value = currentLat;
                if (iLng) iLng.value = currentLng;
                if (sLat) sLat.value = currentLat;
                if (sLng) sLng.value = currentLng;
            },
            (err) => {
                console.warn('GPS watch error:', err.message);
                updateGpsUI(false, "Sinyal GPS terputus atau izin akses lokasi belum aktif.");
            },
            options
        );

        // Auto sync interval setiap 45 detik selama di jalan
        setInterval(() => {
            if (currentLat && currentLng) {
                syncGpsWithServer(currentLat, currentLng);
            }
        }, 45000);
    }

    function updateGpsUI(isActive, message) {
        const topbarGpsBadge = document.getElementById('topbarGpsBadge');
        const gpsTextIndicator = document.getElementById('gpsTextIndicator');
        const gpsCoordsDisplay = document.getElementById('gpsCoordsDisplay');

        if (isActive) {
            if (topbarGpsBadge) {
                topbarGpsBadge.style.background = '#ecfdf5';
                topbarGpsBadge.style.borderColor = '#a7f3d0';
                topbarGpsBadge.style.color = '#065f46';
            }
            if (gpsTextIndicator) gpsTextIndicator.textContent = "GPS: Terhubung & Aktif";
            if (gpsCoordsDisplay) gpsCoordsDisplay.textContent = message;
        } else {
            if (topbarGpsBadge) {
                topbarGpsBadge.style.background = '#fef3c7';
                topbarGpsBadge.style.borderColor = '#fcd34d';
                topbarGpsBadge.style.color = '#92400e';
            }
            if (gpsTextIndicator) gpsTextIndicator.textContent = "GPS: Standby";
            if (gpsCoordsDisplay) gpsCoordsDisplay.textContent = message;
        }
    }

    function syncGpsWithServer(lat, lng, accuracy, speed, heading) {
        if (!scheduleId || !csrfToken) return;

        fetch(`/driver/schedule/${scheduleId}/gps`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                accuracy: accuracy || null,
                speed: speed || null,
                heading: heading || null
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log('GPS synced with server:', data);
        })
        .catch(err => {
            console.error('Error syncing GPS:', err);
        });
    }

    function triggerManualGpsSync() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                syncGpsWithServer(currentLat, currentLng, pos.coords.accuracy);
                alert(`Posisi berhasil diperbarui: Lat ${currentLat.toFixed(5)}, Lng ${currentLng.toFixed(5)}`);
            }, (err) => {
                alert('Gagal membaca koordinat GPS: ' + err.message);
            });
        }
    }

    // 2. FILTER MANIFEST PENUMPANG
    function filterManifest() {
        const input = document.getElementById('manifestSearchInput');
        const filter = input.value.toLowerCase();
        const items = document.querySelectorAll('.passenger-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const seat = item.getAttribute('data-seat') || '';
            if (name.includes(filter) || seat.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // 3. SEAT MAP INFO POPUP
    function showSeatInfo(seatNumber, passengerName, statusClass) {
        let statusText = 'Kosong';
        if (statusClass === 'boarded') statusText = 'Sudah Naik (Boarded)';
        else if (statusClass === 'pending') statusText = 'Dipesan (Belum Naik)';

        alert(`Nomor Kursi: ${seatNumber}\nPenumpang: ${passengerName}\nStatus: ${statusText}`);
    }

    // 4. MODALS CONTROL
    function openIncidentModal() {
        document.getElementById('modalIncident').classList.add('show');
    }
    function closeIncidentModal() {
        document.getElementById('modalIncident').classList.remove('show');
    }

    function openSosModal() {
        document.getElementById('modalSos').classList.add('show');
    }
    function closeSosModal() {
        document.getElementById('modalSos').classList.remove('show');
    }

    // Close on backdrop click
    window.addEventListener('click', (e) => {
        const modalInc = document.getElementById('modalIncident');
        const modalSos = document.getElementById('modalSos');
        if (e.target === modalInc) closeIncidentModal();
        if (e.target === modalSos) closeSosModal();
    });

    // 5. SIDEBAR ACTIVE CLICK HANDLER
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // 6. INTERACTIVE STATUS SLIDER CONFIRMATION
    function initStatusSlider() {
        const sliderWrap = document.getElementById('statusSliderWrap');
        const sliderThumb = document.getElementById('statusSliderThumb');
        const sliderProgress = document.getElementById('statusSliderProgress');
        const sliderForm = document.getElementById('mainStatusForm');

        if (!sliderWrap || !sliderThumb || !sliderForm) return;

        let isDragging = false;
        let startX = 0;
        let maxSlide = 0;

        function updateDimensions() {
            maxSlide = sliderWrap.clientWidth - sliderThumb.clientWidth - 6;
        }
        updateDimensions();
        window.addEventListener('resize', updateDimensions);

        function startDrag(clientX) {
            isDragging = true;
            startX = clientX;
            sliderThumb.style.transition = 'none';
            if (sliderProgress) sliderProgress.style.transition = 'none';
        }

        function onDrag(clientX) {
            if (!isDragging) return;
            let delta = clientX - startX;
            if (delta < 0) delta = 0;
            if (delta > maxSlide) delta = maxSlide;

            sliderThumb.style.transform = `translateX(${delta}px)`;
            if (sliderProgress) {
                const pct = (delta / maxSlide) * 100;
                sliderProgress.style.width = `${pct}%`;
            }

            // If dragged >= 88%, trigger confirmation and submit
            if (delta >= maxSlide * 0.88) {
                isDragging = false;
                sliderThumb.style.transform = `translateX(${maxSlide}px)`;
                if (sliderProgress) sliderProgress.style.width = '100%';

                const slLat = document.getElementById('sliderLat');
                const slLng = document.getElementById('sliderLng');
                if (slLat && typeof currentLat !== 'undefined' && currentLat) slLat.value = currentLat;
                if (slLng && typeof currentLng !== 'undefined' && currentLng) slLng.value = currentLng;

                setTimeout(() => {
                    sliderForm.submit();
                }, 160);
            }
        }

        function stopDrag() {
            if (!isDragging) return;
            isDragging = false;
            sliderThumb.style.transition = 'transform 0.25s ease-out';
            sliderThumb.style.transform = 'translateX(0px)';
            if (sliderProgress) {
                sliderProgress.style.transition = 'width 0.25s ease-out';
                sliderProgress.style.width = '0%';
            }
        }

        // Mouse Listeners
        sliderThumb.addEventListener('mousedown', (e) => {
            e.preventDefault();
            startDrag(e.clientX);
        });
        window.addEventListener('mousemove', (e) => onDrag(e.clientX));
        window.addEventListener('mouseup', stopDrag);

        // Touch Listeners for mobile/tablet drivers
        sliderThumb.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches[0]) {
                startDrag(e.touches[0].clientX);
            }
        }, { passive: true });
        window.addEventListener('touchmove', (e) => {
            if (e.touches && e.touches[0]) {
                onDrag(e.touches[0].clientX);
            }
        }, { passive: true });
        window.addEventListener('touchend', stopDrag);
    }

    // Start GPS & Slider on load
    document.addEventListener('DOMContentLoaded', () => {
        initGeolocation();
        initStatusSlider();
    });
</script>

</body>
</html>