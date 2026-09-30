<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Validasi Tiket & Boarding Pass - Travel</title>

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
            --accent: #0b7285;
            --accent-light: #e3fafc;

            --white: #ffffff;
            --background: #f4f8fb;
            --border: #e2e8f0;

            --text-dark: #0f172a;
            --text: #334155;
            --text-light: #64748b;

            --success: #087f55;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;

            --warning: #d97706;
            --warning-bg: #fffbeb;
            --warning-border: #fde68a;

            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--background);
            color: var(--text-dark);
            min-height: 100vh;
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
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            color: var(--primary);
            border-radius: 12px;
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
            color: rgba(255, 255, 255, 0.75);
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
            color: rgba(255, 255, 255, 0.85);
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
            background: rgba(220, 38, 38, 0.9);
            color: var(--white);
        }

        /* MAIN CONTENT */
        .main-content {
            width: calc(100% - 260px);
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR */
        .topbar {
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title h1 {
            color: var(--text-dark);
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title p {
            color: var(--text-light);
            font-size: 12px;
            margin-top: 2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .trip-select-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
        }

        .trip-select-box select {
            border: none;
            background: transparent;
            font-weight: 700;
            color: var(--primary-dark);
            outline: none;
            cursor: pointer;
            font-size: 13px;
        }

        .profile-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 14px;
            border-left: 1px solid var(--border);
        }

        .avatar {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            font-weight: 700;
            font-size: 14px;
        }

        /* CONTENT AREA */
        .content {
            padding: 24px 32px 40px;
            flex: 1;
        }

        /* TRIP SUMMARY HEADER CARD */
        .trip-header-card {
            background: linear-gradient(135deg, #0b4f35 0%, #087f55 100%);
            color: white;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(8, 127, 85, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .trip-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .trip-badge-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(8px);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(74, 222, 128, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
        }

        .trip-stats-strip {
            display: flex;
            align-items: center;
            gap: 18px;
            background: rgba(0, 0, 0, 0.18);
            padding: 10px 18px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .strip-stat-item {
            text-align: center;
        }

        .strip-stat-item span {
            display: block;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            font-weight: 600;
        }

        .strip-stat-item strong {
            font-size: 17px;
            font-weight: 800;
        }

        /* DUAL WORKSPACE GRID */
        .queue-grid {
            display: grid;
            grid-template-columns: 460px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .queue-grid {
                grid-template-columns: 1fr;
            }
        }

        /* CARD CONTAINERS */
        .panel-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-header h3 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-body {
            padding: 20px;
        }

        /* SCANNER CAMERA BOX */
        .scanner-container {
            position: relative;
            width: 100%;
            height: 270px;
            background: #090d16;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 25px rgba(0, 0, 0, 0.8);
            margin-bottom: 18px;
        }

        #scannerVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .camera-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* RETICLE & LASER SCANNER */
        .reticle-box {
            position: relative;
            width: 190px;
            height: 190px;
            border: 1px dashed rgba(255, 255, 255, 0.4);
            border-radius: 16px;
        }

        .corner {
            position: absolute;
            width: 22px;
            height: 22px;
            border-color: #10b981;
            border-style: solid;
        }

        .corner-tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 8px; }
        .corner-tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 8px; }
        .corner-bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 8px; }
        .corner-br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 8px; }

        .laser-line {
            position: absolute;
            left: 4px;
            right: 4px;
            height: 3px;
            background: linear-gradient(90deg, rgba(16, 185, 129, 0) 0%, #10b981 50%, rgba(16, 185, 129, 0) 100%);
            box-shadow: 0 0 12px #10b981, 0 0 20px #10b981;
            animation: scanLaser 2.2s ease-in-out infinite alternate;
        }

        @keyframes scanLaser {
            0% { top: 10px; opacity: 0.8; }
            100% { top: 175px; opacity: 1; }
        }

        .scanner-hud-text {
            position: absolute;
            bottom: 12px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 11px;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.6);
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.3px;
            backdrop-filter: blur(4px);
        }

        /* CAMERA CONTROLS BAR (Flashlight / Torch Toggle) */
        .camera-controls-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
        }

        .btn-camera-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid var(--border);
            background: #f8fafc;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-camera-action:hover {
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .btn-torch-active {
            background: #fef08a !important;
            border-color: #eab308 !important;
            color: #854d0e !important;
            box-shadow: 0 0 12px rgba(234, 179, 8, 0.4);
        }

        /* MANUAL FALLBACK INPUT */
        .manual-section {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .manual-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .manual-input-group {
            display: flex;
            gap: 8px;
        }

        .input-booking-code {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-dark);
            background: var(--white);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-booking-code:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.15);
        }

        .btn-validate-manual {
            padding: 0 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-validate-manual:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(8, 127, 85, 0.25);
        }

        /* QUICK TEST CHIPS */
        .simulation-chips {
            margin-top: 10px;
        }

        .chips-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 6px;
            display: block;
        }

        .chips-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip-btn {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--text);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .chip-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .chip-btn.fail {
            color: #b91c1c;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .chip-btn.fail:hover {
            background: #fee2e2;
        }

        /* INSTANT FEEDBACK CARD (HASIL VALIDASI) */
        .feedback-card {
            display: none;
            border-radius: 14px;
            padding: 18px;
            margin-top: 6px;
            animation: slideDown 0.25s ease forwards;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .feedback-card.active {
            display: block;
        }

        /* SUCCESS STATE */
        .feedback-success {
            background: #ecfdf5;
            border: 2px solid #34d399;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15);
        }

        .feedback-success .fb-header {
            color: #065f46;
        }

        /* ERROR STATE */
        .feedback-danger {
            background: #fef2f2;
            border: 2px solid #f87171;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.15);
        }

        .feedback-danger .fb-header {
            color: #991b1b;
        }

        .fb-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 800;
        }

        .fb-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fb-badge-success {
            background: #10b981;
            color: white;
        }

        .fb-badge-danger {
            background: #ef4444;
            color: white;
        }

        .fb-content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background: rgba(255, 255, 255, 0.75);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .fb-grid-item span {
            display: block;
            color: var(--text-light);
            font-size: 11px;
            margin-bottom: 2px;
        }

        .fb-grid-item strong {
            color: var(--text-dark);
            font-size: 13px;
        }

        .fb-grid-seat {
            font-size: 18px !important;
            font-weight: 900 !important;
            color: var(--primary) !important;
        }

        .fb-alert-box {
            background: white;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.4;
            color: #7f1d1d;
            border-left: 4px solid #ef4444;
            margin-bottom: 10px;
        }

        .fb-btn-dismiss {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-success-action {
            background: #087f55;
            color: white;
        }

        .btn-success-action:hover {
            background: #066b47;
        }

        .btn-danger-action {
            background: #dc2626;
            color: white;
        }

        .btn-danger-action:hover {
            background: #b91c1c;
        }

        /* RIGHT PANEL: CHECKLIST MANIFES */
        .checklist-toolbar {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: #fbfdff;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-box input {
            width: 100%;
            padding: 9px 14px 9px 36px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            outline: none;
            background: white;
            transition: border-color 0.2s ease;
        }

        .search-box input:focus {
            border-color: var(--primary);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 14px;
            pointer-events: none;
        }

        /* FILTER TABS */
        .filter-tabs {
            display: flex;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
        }

        .tab-btn {
            border: none;
            background: transparent;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn.active {
            background: white;
            color: var(--text-dark);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .tab-badge {
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 10px;
            background: #e2e8f0;
            color: var(--text-dark);
        }

        .tab-btn.active .tab-badge {
            background: var(--primary-light);
            color: var(--primary);
        }

        /* PASSENGER CHECKLIST TABLE */
        .checklist-table-wrapper {
            max-height: 520px;
            overflow-y: auto;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .checklist-table th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            z-index: 2;
        }

        .checklist-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .checklist-table tr:hover {
            background: #f8fafc;
        }

        .seat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: #f1f5f9;
            color: var(--text-dark);
            font-weight: 800;
            font-size: 14px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .passenger-info strong {
            display: block;
            font-size: 13px;
            color: var(--text-dark);
        }

        .passenger-info span {
            font-size: 11px;
            color: var(--text-light);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* STATUS BADGES */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-belum-hadir {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .status-boarded {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-no-show {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* MANUAL ACTION DROPDOWN / BUTTONS */
        .action-cell {
            text-align: right;
        }

        .status-select-btn {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            background: white;
            outline: none;
            transition: all 0.15s ease;
        }

        .status-select-btn:hover {
            border-color: var(--primary);
        }

        /* TOAST NOTIFICATION */
        .toast-notify {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 14px 20px;
            border-radius: 12px;
            color: white;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 999;
            display: none;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.2s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

<div class="dashboard-wrapper">

    <!-- =========================================
         SIDEBAR NAVIGATION
    ========================================== -->
    <aside class="sidebar">

        <div class="logo-area">
            <div class="logo-icon">🚐</div>
            <div class="logo-text">
                <h2>TravelKu</h2>
                <span>TERMINAL & LOKET SISTEM</span>
            </div>
        </div>

        <nav class="menu">
            <div class="menu-title">Menu Utama</div>

            <a href="{{ route('dashboard.admin-biasa.index') }}">
                <div class="menu-icon">⌂</div>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('dashboard.admin-biasa.booking') }}">
                <div class="menu-icon">🎫</div>
                <span>Manajemen Booking</span>
            </a>

            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}">
                <div class="menu-icon">🗺️</div>
                <span>Informasi Perjalanan</span>
            </a>

            <!-- MENU VALIDASI TIKET AKTIF -->
            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}" class="active">
                <div class="menu-icon">🎟️</div>
                <span>Validasi E-Ticket</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <div class="menu-icon">↪</div>
                    <span>Logout Petugas</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- =========================================
         MAIN CONTENT AREA
    ========================================== -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="page-title">
                <h1>
                    <span>🎟️</span>
                    <span>Validasi Tiket & Boarding Pass</span>
                </h1>
                <p>Verifikasi instan QR code, input fallback kode booking, dan status boarding penumpang kabin.</p>
            </div>

            <div class="topbar-right">
                <!-- SCHEDULE SELECTOR -->
                <form method="GET" action="{{ route('dashboard.admin-biasa.validasi-tiket') }}" id="scheduleSelectForm">
                    <div class="trip-select-box">
                        <span>🚌 Armada Trip:</span>
                        <select name="schedule_id" onchange="document.getElementById('scheduleSelectForm').submit()">
                            @foreach($scheduleList as $sched)
                                <option value="{{ $sched->id }}" {{ (isset($selectedSchedule) && $selectedSchedule->id == $sched->id) ? 'selected' : '' }}>
                                    {{ $sched->route_name ?? ($sched->route?->origin?->name . ' → ' . $sched->route?->destination?->name) }}
                                    ({{ $sched->vehicle?->plate_number ?? 'D 7812 AB' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- USER INFO -->
                <div class="profile-badge">
                    <div class="avatar">{{ substr($user->name ?? 'Staf', 0, 2) }}</div>
                    <div>
                        <strong style="font-size: 13px; display: block;">{{ $user->name ?? 'Petugas Boarding' }}</strong>
                        <small style="font-size: 11px; color: var(--text-light);">{{ $user->branch ?: 'Pool Dipatiukur' }}</small>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="content">

            <!-- TRIP SUMMARY HERO CARD -->
            <div class="trip-header-card">
                <div>
                    <div class="trip-badge-live">
                        <span class="pulse-dot"></span>
                        <span>Boarding Gate Terbuka</span>
                    </div>
                    <h2 style="font-size: 20px; font-weight: 800; margin-top: 8px;">
                        {{ $selectedSchedule->route_name }}
                    </h2>
                    <div class="trip-meta" style="margin-top: 6px; font-size: 13px; opacity: 0.9;">
                        <span>🚐 Unit: <strong>{{ $selectedSchedule->plate_number }}</strong> ({{ $selectedSchedule->vehicle_name }})</span>
                        <span>👤 Sopir: <strong>{{ $selectedSchedule->driver_name }}</strong></span>
                        <span>⏰ Berangkat: <strong>{{ $formattedDepartureTime }}</strong></span>
                    </div>
                </div>

                <!-- PROGRESS & STAT COUNTERS -->
                <div class="trip-stats-strip">
                    <div class="strip-stat-item">
                        <span>Total Kursi</span>
                        <strong id="statTotalCount">{{ $totalPassengers }}</strong>
                    </div>
                    <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                    <div class="strip-stat-item">
                        <span style="color: #86efac;">Boarded</span>
                        <strong id="statBoardedCount" style="color: #4ade80;">{{ $boardedCount }}</strong>
                    </div>
                    <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                    <div class="strip-stat-item">
                        <span style="color: #cbd5e1;">Belum Hadir</span>
                        <strong id="statPendingCount">{{ $pendingCount }}</strong>
                    </div>
                    <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                    <div class="strip-stat-item">
                        <span style="color: #fca5a5;">No-Show</span>
                        <strong id="statNoShowCount" style="color: #f87171;">{{ $noShowCount }}</strong>
                    </div>
                </div>
            </div>

            <!-- DUAL WORKSPACE: SCANNER & MANUAL FALLBACK (LEFT) + PASSENGER CHECKLIST (RIGHT) -->
            <div class="queue-grid">

                <!-- =========================================
                     PANEL KIRI: MODE PEMINDAI & MANUAL INPUT
                ========================================== -->
                <div class="panel-card">

                    <div class="panel-header">
                        <h3>
                            <span>📷</span>
                            <span>Pemindai QR & Validasi Manual</span>
                        </h3>
                        <span style="font-size: 11px; font-weight: 700; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px;">
                            Fast-Lane Antrean
                        </span>
                    </div>

                    <div class="panel-body">

                        <!-- 1. JENDELA KAMERA PEMINDAI (SCANNER) -->
                        <div class="scanner-container">
                            <video id="scannerVideo" autoplay playsinline muted></video>

                            <!-- Overlay Reticle HUD & Animasi Laser -->
                            <div class="camera-overlay">
                                <div class="reticle-box">
                                    <div class="corner corner-tl"></div>
                                    <div class="corner corner-tr"></div>
                                    <div class="corner corner-bl"></div>
                                    <div class="corner corner-br"></div>
                                    <div class="laser-line"></div>
                                </div>
                                <div class="scanner-hud-text" id="scannerStatusText">
                                    Arahkan QR E-Ticket Penumpang ke Garis Sensor
                                </div>
                            </div>
                        </div>

                        <!-- 2. KONTROL KAMERA: TOGGLE KAMERA & TOMBOL FLASHLIGHT / LAMPU KILAT -->
                        <div class="camera-controls-bar">
                            <button type="button" class="btn-camera-action" id="btnToggleCamera" onclick="toggleCamera()">
                                <span id="cameraBtnIcon">📹</span>
                                <span id="cameraBtnText">Mulai Kamera</span>
                            </button>

                            <button type="button" class="btn-camera-action" id="btnToggleTorch" onclick="toggleFlashlight()">
                                <span>⚡</span>
                                <span id="torchBtnText">Lampu Kilat (Off)</span>
                            </button>

                            <button type="button" class="btn-camera-action" onclick="switchCamera()">
                                <span>🔄</span>
                                <span>Balik Kamera</span>
                            </button>
                        </div>

                        <!-- 3. MODE MANUAL (FALLBACK KODE BOOKING 6-8 DIGIT) -->
                        <div class="manual-section">
                            <label class="manual-label">
                                <span>Ketik Kode Booking / Tiket (Fallback):</span>
                                <span style="font-weight: 600; color: var(--text-light); font-size: 11px;">6–8 Digit</span>
                            </label>

                            <div class="manual-input-group">
                                <input
                                    type="text"
                                    id="manualCodeInput"
                                    class="input-booking-code"
                                    placeholder="CONTOH: TKV-8920"
                                    maxlength="20"
                                    autocomplete="off"
                                    onkeydown="if(event.key==='Enter'){ handleManualSubmit(); }"
                                >
                                <button type="button" class="btn-validate-manual" onclick="handleManualSubmit()">
                                    <span>Periksa</span>
                                    <span>➔</span>
                                </button>
                            </div>

                            <!-- Quick simulation chips -->
                            <div class="simulation-chips">
                                <span class="chips-title">Simulasi Kasus Cepat (1-Klik):</span>
                                <div class="chips-list">
                                    <button type="button" class="chip-btn" onclick="runSimulatedCode('TKV-8920-JKT')">
                                        ⚡ Siti (1B - Sukses)
                                    </button>
                                    <button type="button" class="chip-btn" onclick="runSimulatedCode('TKV-9344-JKT')">
                                        ⚡ Andi (3A - Sukses)
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-ALREADY-SCAN')">
                                        ⚠️ Sudah Di-Scan
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-WRONG-TIME')">
                                        ⚠️ Salah Jam
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-UNPAID-123')">
                                        ⚠️ Belum Lunas
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-CANCELLED')">
                                        ⚠️ Tiket Batal
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-REFUNDED')">
                                        ⚠️ Di-Refund
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-EXPIRED')">
                                        ⚠️ Kadaluwarsa
                                    </button>
                                    <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-DEPARTED')">
                                        ⚠️ Sudah Berangkat
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 4. LAYAR KONFIRMASI INSTAN (UMPAN BALIK HASIL VALIDASI) -->
                        <div id="instantFeedbackCard" class="feedback-card">

                            <!-- SUCCESS TEMPLATE -->
                            <div id="feedbackSuccessBox" style="display: none;">
                                <div class="fb-header">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 20px;">✅</span>
                                        <span>TIKET VALID & TERVERIFIKASI</span>
                                    </div>
                                    <span class="fb-badge fb-badge-success">BOARDED</span>
                                </div>

                                <div class="fb-content-grid">
                                    <div class="fb-grid-item">
                                        <span>Nama Penumpang</span>
                                        <strong id="fbPassengerName">-</strong>
                                    </div>
                                    <div class="fb-grid-item">
                                        <span>Nomor Kursi</span>
                                        <strong id="fbSeatNumber" class="fb-grid-seat">-</strong>
                                    </div>
                                    <div class="fb-grid-item">
                                        <span>Kode Booking</span>
                                        <strong id="fbBookingCode" style="font-family: monospace;">-</strong>
                                    </div>
                                    <div class="fb-grid-item">
                                        <span>Waktu Validasi</span>
                                        <strong id="fbBoardedAt">-</strong>
                                    </div>
                                </div>

                                <button type="button" class="fb-btn-dismiss btn-success-action" onclick="dismissFeedback()">
                                    ✓ Penumpang Siap Masuk Kabin (Lanjut Scan Berikutnya)
                                </button>
                            </div>

                            <!-- FAILED TEMPLATE -->
                            <div id="feedbackFailedBox" style="display: none;">
                                <div class="fb-header">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 20px;">⛔</span>
                                        <span id="fbErrorTitle">TIKET DITOLAK</span>
                                    </div>
                                    <span class="fb-badge fb-badge-danger">GAGAL</span>
                                </div>

                                <div class="fb-alert-box" id="fbErrorMessage">
                                    Alasan penolakan spesifik.
                                </div>

                                <button type="button" class="fb-btn-dismiss btn-danger-action" onclick="dismissFeedback()">
                                    ✕ Tutup Peringatan & Cek Kembali
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- =========================================
                     PANEL KANAN: DAFTAR CENTANG MANUAL (CHECKLIST)
                ========================================== -->
                <div class="panel-card">

                    <div class="panel-header">
                        <h3>
                            <span>📋</span>
                            <span>Daftar Centang Penumpang (Checklist)</span>
                        </h3>
                        <div style="font-size: 12px; color: var(--text-light);">
                            Kapasitas: <strong>{{ $totalPassengers }}/{{ $selectedSchedule->capacity ?? 10 }} Kursi</strong>
                        </div>
                    </div>

                    <!-- TOOLBAR: SEARCH & TABS FILTER -->
                    <div class="checklist-toolbar">
                        <div class="search-box">
                            <span class="search-icon">🔍</span>
                            <input
                                type="text"
                                id="checklistSearchInput"
                                placeholder="Cari nama, kursi, kode booking..."
                                oninput="filterPassengerTable()"
                            >
                        </div>

                        <div class="filter-tabs">
                            <button type="button" class="tab-btn active" onclick="setChecklistFilter('all', this)">
                                <span>Semua</span>
                                <span class="tab-badge" id="badgeTabAll">{{ $totalPassengers }}</span>
                            </button>

                            <button type="button" class="tab-btn" onclick="setChecklistFilter('belum_hadir', this)">
                                <span>Belum Hadir</span>
                                <span class="tab-badge" id="badgeTabPending">{{ $pendingCount }}</span>
                            </button>

                            <button type="button" class="tab-btn" onclick="setChecklistFilter('boarded', this)">
                                <span>Boarded</span>
                                <span class="tab-badge" id="badgeTabBoarded">{{ $boardedCount }}</span>
                            </button>

                            <button type="button" class="tab-btn" onclick="setChecklistFilter('no_show', this)">
                                <span>No-Show</span>
                                <span class="tab-badge" id="badgeTabNoShow">{{ $noShowCount }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- PASSENGER TABLE -->
                    <div class="checklist-table-wrapper">
                        <table class="checklist-table" id="passengerTable">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Kursi</th>
                                    <th>Data Penumpang</th>
                                    <th>Kode Booking</th>
                                    <th style="width: 140px; text-align: center;">Status Saat Ini</th>
                                    <th style="width: 170px; text-align: right;">Ubah Status Manual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sortedPassengers as $p)
                                    <tr
                                        id="passengerRow-{{ $p->id }}"
                                        data-status="{{ $p->boarding_status }}"
                                        data-name="{{ strtolower($p->name) }}"
                                        data-seat="{{ strtolower($p->seat_number) }}"
                                        data-code="{{ strtolower($p->booking_code) }}"
                                    >
                                        <td style="text-align: center;">
                                            <span class="seat-badge" id="seatBadge-{{ $p->id }}">
                                                {{ $p->seat_number }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="passenger-info">
                                                <strong id="passengerName-{{ $p->id }}">{{ $p->name }}</strong>
                                                <span>
                                                    <span>📞 {{ $p->phone }}</span>
                                                    @if($p->special_request && $p->special_request !== '-')
                                                        <span>• 📝 {{ $p->special_request }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <span style="font-family: monospace; font-weight: 700; color: var(--text-dark);">
                                                {{ $p->booking_code }}
                                            </span>
                                            <small style="display: block; font-size: 11px; color: var(--primary);">
                                                {{ $p->payment_status }}
                                            </small>
                                        </td>

                                        <td style="text-align: center;" id="statusCell-{{ $p->id }}">
                                            @if($p->boarding_status === 'boarded')
                                                <span class="status-pill status-boarded">
                                                    ✓ Boarded
                                                </span>
                                                <small style="display: block; font-size: 10px; color: #047857; margin-top: 2px;" id="timeCell-{{ $p->id }}">
                                                    {{ $p->boarded_at ?: 'Terkonfirmasi' }}
                                                </small>
                                            @elseif($p->boarding_status === 'no_show')
                                                <span class="status-pill status-no-show">
                                                    ✕ No-Show
                                                </span>
                                            @else
                                                <span class="status-pill status-belum-hadir">
                                                    ⚪ Belum Hadir
                                                </span>
                                            @endif
                                        </td>

                                        <td class="action-cell">
                                            <select
                                                class="status-select-btn"
                                                id="statusDropdown-{{ $p->id }}"
                                                onchange="updatePassengerStatusManual('{{ $p->id }}', this.value)"
                                            >
                                                <option value="belum_hadir" {{ $p->boarding_status === 'belum_hadir' ? 'selected' : '' }}>
                                                    ⚪ Belum Hadir
                                                </option>
                                                <option value="boarded" {{ $p->boarding_status === 'boarded' ? 'selected' : '' }}>
                                                    🟢 Boarded
                                                </option>
                                                <option value="no_show" {{ $p->boarding_status === 'no_show' ? 'selected' : '' }}>
                                                    🔴 No-Show
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                                            Belum ada data penumpang terdaftar untuk jadwal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

            <!-- =========================================
                 RIWAYAT VALIDASI E-TICKET TERKINI (LIVE FEED STAF)
            ========================================== -->
            <div class="panel-card" style="margin-top: 24px;">
                <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>
                        <span>⚡</span>
                        <span>Riwayat Validasi E-Ticket Terkini (Semua Jadwal)</span>
                    </h3>
                    <span style="font-size: 12px; color: var(--text-light);">
                        Sinkronisasi otomatis saat penumpang scan tiket atau check-in mandiri
                    </span>
                </div>

                <div class="checklist-table-wrapper">
                    <table class="checklist-table">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Waktu Boarding</th>
                                <th>Penumpang & Kursi</th>
                                <th>Kode Booking / Tiket</th>
                                <th>Rute & Armada Trip</th>
                                <th style="width: 140px; text-align: center;">Status</th>
                                <th style="width: 150px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBoardedTickets ?? [] as $rt)
                                @php
                                    $pFirst = $rt->booking?->passengers?->first();
                                    $allSeats = $rt->booking?->passengers?->map(fn($p) => $p->seat?->seat_number)->filter()->join(', ') ?: ($pFirst?->seat?->seat_number ?? '-');
                                    $pName = $pFirst?->name ?? ($rt->booking?->user?->name ?? 'Penumpang');
                                    $routeName = ($rt->booking?->schedule?->route?->origin?->name ?? 'Asal') . ' → ' . ($rt->booking?->schedule?->route?->destination?->name ?? 'Tujuan');
                                    $plate = $rt->booking?->schedule?->vehicle?->plate_number ?? '-';
                                    $timeStr = $rt->boarded_at ? \Carbon\Carbon::parse($rt->boarded_at)->format('H:i') . ' WIB' : 'Baru saja';
                                    $schedId = $rt->booking?->schedule_id;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: #047857; font-size: 13px;">
                                            🕒 {{ $timeStr }}
                                        </div>
                                        <small style="color: var(--text-light); font-size: 11px;">
                                            {{ $rt->boarded_at ? \Carbon\Carbon::parse($rt->boarded_at)->diffForHumans() : 'Hari ini' }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong style="color: var(--text-dark); display: block;">{{ $pName }}</strong>
                                        <span style="font-size: 12px; color: var(--primary); font-weight: 700;">Kursi: {{ $allSeats }}</span>
                                    </td>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 700; color: var(--text-dark);">
                                            {{ $rt->booking?->booking_code ?? '-' }}
                                        </span>
                                        <small style="display: block; font-size: 11px; color: var(--text-light);">
                                            {{ $rt->ticket_number }}
                                        </small>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; font-weight: 600; color: var(--text-dark);">
                                            {{ $routeName }}
                                        </div>
                                        <small style="color: var(--text-light);">
                                            Unit: {{ $plate }}
                                        </small>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-pill status-boarded">
                                            ✓ Boarded
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($schedId)
                                            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}?schedule_id={{ $schedId }}"
                                               class="btn-camera-action"
                                               style="display: inline-block; padding: 6px 12px; font-size: 11px; text-decoration: none; text-align: center;">
                                                Lihat Manifes ➔
                                            </a>
                                        @else
                                            <span style="color: var(--text-light); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-light);">
                                        Belum ada riwayat validasi e-ticket. Tiket yang divalidasi staf atau di-check-in penumpang akan otomatis muncul di sini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

</div>

<!-- TOAST NOTIFICATION -->
<div id="toastNotification" class="toast-notify">
    <span id="toastIcon">🔔</span>
    <span id="toastMessage">Pesan pemberitahuan</span>
</div>

<!-- ==========================================================
     AUDIO SYNTHESIZER & REALTIME JAVASCRIPT LOGIC
=========================================================== -->
<script>
    const currentScheduleId = "{{ $selectedSchedule->id ?? 1 }}";
    let activeFilter = 'all';

    // ---------------------------------------------------------
    // 1. WEB AUDIO API SYNTHESIZER (ZERO LATENCY NATIVE CHIMES)
    // ---------------------------------------------------------
    let audioCtx = null;

    function getAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                audioCtx = new AudioContext();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Suara Bip Sukses (Two-Tone Positive High Chime: D5 587Hz -> A5 880Hz)
    function playSuccessSound() {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.setValueAtTime(880.00, now + 0.08); // A5

            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.3, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + 0.35);
        } catch (e) {
            console.warn('Audio playback error:', e);
        }
    }

    // Suara Bip Peringatan / Gagal (Dual Low Buzz Beep: 220Hz / 180Hz)
    function playErrorSound() {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, now);
            osc.frequency.setValueAtTime(160, now + 0.12);

            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.3, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.32);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + 0.32);
        } catch (e) {
            console.warn('Audio error playback error:', e);
        }
    }

    // ---------------------------------------------------------
    // 2. CAMERA SCANNER & FLASHLIGHT (TORCH) CONTROL
    // ---------------------------------------------------------
    let cameraStream = null;
    let videoTrack = null;
    let isTorchOn = false;
    let currentFacingMode = 'environment'; // Back camera by default
    let scanInterval = null;

    async function toggleCamera() {
        const videoElem = document.getElementById('scannerVideo');
        const btnText = document.getElementById('cameraBtnText');
        const btnIcon = document.getElementById('cameraBtnIcon');
        const statusText = document.getElementById('scannerStatusText');

        if (cameraStream) {
            // Stop Camera
            stopCameraStream();
            btnText.innerText = 'Mulai Kamera';
            btnIcon.innerText = '📹';
            statusText.innerText = 'Kamera Dihentikan (Mode Manual Aktif)';
        } else {
            // Start Camera
            try {
                const constraints = {
                    video: {
                        facingMode: { ideal: currentFacingMode },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                };

                cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
                videoElem.srcObject = cameraStream;
                videoTrack = cameraStream.getVideoTracks()[0];

                btnText.innerText = 'Hentikan Kamera';
                btnIcon.innerText = '⏹️';
                statusText.innerText = 'Kamera Aktif • Arahkan QR E-Ticket Penumpang';
                showToast('📷 Kamera pemindai QR aktif.', 'info');

                startBarcodeDetector(videoElem);
            } catch (err) {
                console.warn('Camera access issue:', err);
                statusText.innerText = 'Kamera tidak dapat diakses. Silakan gunakan Mode Manual';
                showToast('Gagal membuka kamera: ' + (err.message || 'Izin ditolak'), 'danger');
            }
        }
    }

    function stopCameraStream() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
            videoTrack = null;
        }
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
        document.getElementById('scannerVideo').srcObject = null;
        isTorchOn = false;
        updateTorchButtonUI();
    }

    // Toggle Flashlight (Torch) for Night / Dark conditions
    async function toggleFlashlight() {
        if (!videoTrack) {
            showToast('Nyalakan kamera terlebih dahulu untuk mengaktifkan lampu kilat.', 'danger');
            return;
        }

        try {
            const capabilities = videoTrack.getCapabilities ? videoTrack.getCapabilities() : {};
            if (!capabilities.torch) {
                // Torch not supported on this device/browser track
                isTorchOn = !isTorchOn;
                updateTorchButtonUI();
                showToast(isTorchOn ? '⚡ Lampu kilat disimulasikan (Perangkat tidak mendukung kontrol hardware)' : 'Lampu kilat dimatikan', 'info');
                return;
            }

            isTorchOn = !isTorchOn;
            await videoTrack.applyConstraints({
                advanced: [{ torch: isTorchOn }]
            });

            updateTorchButtonUI();
            showToast(isTorchOn ? '⚡ Lampu kilat (Torch) Menyala!' : 'Lampu kilat dimatikan', 'info');
        } catch (e) {
            console.warn('Torch control error:', e);
            isTorchOn = !isTorchOn;
            updateTorchButtonUI();
            showToast('Lampu kilat: ' + (isTorchOn ? 'Aktif' : 'Nonaktif'), 'info');
        }
    }

    function updateTorchButtonUI() {
        const torchBtn = document.getElementById('btnToggleTorch');
        const torchText = document.getElementById('torchBtnText');
        if (isTorchOn) {
            torchBtn.classList.add('btn-torch-active');
            torchText.innerText = 'Lampu Kilat (ON)';
        } else {
            torchBtn.classList.remove('btn-torch-active');
            torchText.innerText = 'Lampu Kilat (Off)';
        }
    }

    async function switchCamera() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        if (cameraStream) {
            stopCameraStream();
            toggleCamera();
        }
    }

    // Live Barcode/QR Detector Loop (menggunakan BarcodeDetector API jika browser mendukung)
    function startBarcodeDetector(videoElem) {
        if ('BarcodeDetector' in window) {
            const detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13'] });
            let isProcessing = false;

            scanInterval = setInterval(async () => {
                if (isProcessing || videoElem.readyState < 2) return;
                isProcessing = true;
                try {
                    const barcodes = await detector.detect(videoElem);
                    if (barcodes.length > 0) {
                        const rawValue = barcodes[0].rawValue;
                        if (rawValue) {
                            validateTicketCode(rawValue);
                        }
                    }
                } catch (err) {
                    // silent frame error
                } finally {
                    isProcessing = false;
                }
            }, 500);
        }
    }

    // ---------------------------------------------------------
    // 3. MANUAL FALLBACK & QUICK SIMULATION SUBMIT
    // ---------------------------------------------------------
    function handleManualSubmit() {
        const input = document.getElementById('manualCodeInput');
        const code = input.value.trim();
        if (!code) {
            showToast('Ketik kode booking atau e-ticket terlebih dahulu.', 'danger');
            input.focus();
            return;
        }
        validateTicketCode(code);
    }

    function runSimulatedCode(code) {
        document.getElementById('manualCodeInput').value = code;
        validateTicketCode(code);
    }

    // ---------------------------------------------------------
    // 4. AJAX TICKET VALIDATION & STATUS UPDATE
    // ---------------------------------------------------------
    let isSubmitting = false;

    async function validateTicketCode(code) {
        if (isSubmitting) return;
        isSubmitting = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const feedbackCard = document.getElementById('instantFeedbackCard');
        const successBox = document.getElementById('feedbackSuccessBox');
        const failedBox = document.getElementById('feedbackFailedBox');

        try {
            const response = await fetch("{{ route('dashboard.admin-biasa.validasi-tiket.validate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    schedule_id: currentScheduleId
                })
            });

            const data = await response.json();

            feedbackCard.classList.remove('feedback-success', 'feedback-danger');
            feedbackCard.classList.add('active');

            if (response.ok && data.success) {
                // HASIL VALIDASI: SUKSES 🟢
                playSuccessSound();

                feedbackCard.classList.add('feedback-success');
                successBox.style.display = 'block';
                failedBox.style.display = 'none';

                document.getElementById('fbPassengerName').innerText = data.passenger_name;
                document.getElementById('fbSeatNumber').innerText = 'Kursi ' + data.seat_number;
                document.getElementById('fbBookingCode').innerText = data.booking_code;
                document.getElementById('fbBoardedAt').innerText = data.boarded_at;

                showToast('✅ ' + data.message, 'success');

                // Otomatis ubah status penumpang di daftar checklist tabel
                if (data.passenger_id) {
                    markPassengerRowBoarded(data.passenger_id, data.boarded_at);
                } else {
                    // Cari row berdasarkan booking code
                    findAndMarkRowByCode(data.booking_code, data.boarded_at);
                }

                // Kosongkan kotak input untuk scan berikutnya
                document.getElementById('manualCodeInput').value = '';

            } else {
                // HASIL VALIDASI: GAGAL 🔴
                playErrorSound();

                feedbackCard.classList.add('feedback-danger');
                successBox.style.display = 'none';
                failedBox.style.display = 'block';

                document.getElementById('fbErrorTitle').innerText = data.title || 'TIKET DITOLAK';
                document.getElementById('fbErrorMessage').innerText = data.message || 'Tiket tidak memenuhi syarat boarding.';

                showToast('⚠️ ' + (data.title || 'Validasi Gagal'), 'danger');
            }

        } catch (err) {
            console.error('Validation request failed:', err);
            playErrorSound();

            feedbackCard.classList.remove('feedback-success');
            feedbackCard.classList.add('feedback-danger', 'active');
            successBox.style.display = 'none';
            failedBox.style.display = 'block';

            document.getElementById('fbErrorTitle').innerText = 'KONEKSI TERKENDALA';
            document.getElementById('fbErrorMessage').innerText = 'Gagal menghubungi server loket. Pastikan koneksi jaringan aktif.';
        } finally {
            isSubmitting = false;
        }
    }

    function dismissFeedback() {
        document.getElementById('instantFeedbackCard').classList.remove('active');
        const input = document.getElementById('manualCodeInput');
        input.value = '';
        input.focus();
    }

    // ---------------------------------------------------------
    // 5. CHECKLIST TABLE INTERACTIONS & MANUAL STATUS TOGGLE
    // ---------------------------------------------------------
    function markPassengerRowBoarded(passengerId, timeStr) {
        const row = document.getElementById(`passengerRow-${passengerId}`);
        if (!row) return;

        row.setAttribute('data-status', 'boarded');
        const statusCell = document.getElementById(`statusCell-${passengerId}`);
        if (statusCell) {
            statusCell.innerHTML = `
                <span class="status-pill status-boarded">✓ Boarded</span>
                <small style="display: block; font-size: 10px; color: #047857; margin-top: 2px;">${timeStr || 'Baru Saja'}</small>
            `;
        }

        const dropdown = document.getElementById(`statusDropdown-${passengerId}`);
        if (dropdown) {
            dropdown.value = 'boarded';
        }

        recalculateStats();
        filterPassengerTable();
    }

    function findAndMarkRowByCode(bookingCode, timeStr) {
        const rows = document.querySelectorAll('#passengerTable tbody tr');
        rows.forEach(r => {
            const code = r.getAttribute('data-code');
            if (code && code.includes(bookingCode.toLowerCase())) {
                const id = r.id.replace('passengerRow-', '');
                markPassengerRowBoarded(id, timeStr);
            }
        });
    }

    async function updatePassengerStatusManual(passengerId, newStatus) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const row = document.getElementById(`passengerRow-${passengerId}`);
        if (!row) return;

        try {
            const url = "{{ url('/admin/validasi-tiket/passenger') }}/" + passengerId + "/status";
            const response = await fetch(url, {
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
                row.setAttribute('data-status', newStatus);
                const statusCell = document.getElementById(`statusCell-${passengerId}`);

                if (newStatus === 'boarded') {
                    playSuccessSound();
                    statusCell.innerHTML = `
                        <span class="status-pill status-boarded">✓ Boarded</span>
                        <small style="display: block; font-size: 10px; color: #047857; margin-top: 2px;">${data.boarded_at || 'Baru Saja'}</small>
                    `;
                    showToast('Penumpang ' + document.getElementById(`passengerName-${passengerId}`).innerText + ' diubah menjadi Boarded', 'success');
                } else if (newStatus === 'no_show') {
                    statusCell.innerHTML = `<span class="status-pill status-no-show">✕ No-Show</span>`;
                    showToast('Status penumpang diubah menjadi No-Show', 'info');
                } else {
                    statusCell.innerHTML = `<span class="status-pill status-belum-hadir">⚪ Belum Hadir</span>`;
                    showToast('Status penumpang dikembalikan ke Belum Hadir', 'info');
                }

                recalculateStats();
                filterPassengerTable();
            } else {
                showToast('Gagal mengubah status: ' + (data.message || 'Terjadi kesalahan'), 'danger');
            }
        } catch (e) {
            console.error('Update status error:', e);
            showToast('Koneksi gagal saat mengubah status penumpang.', 'danger');
        }
    }

    // ---------------------------------------------------------
    // 6. FILTERING & SEARCHING PASSENGERS
    // ---------------------------------------------------------
    function setChecklistFilter(filter, btnElem) {
        activeFilter = filter;
        document.querySelectorAll('.filter-tabs .tab-btn').forEach(btn => btn.classList.remove('active'));
        if (btnElem) {
            btnElem.classList.add('active');
        }
        filterPassengerTable();
    }

    function filterPassengerTable() {
        const query = document.getElementById('checklistSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#passengerTable tbody tr');

        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            const name = row.getAttribute('data-name') || '';
            const seat = row.getAttribute('data-seat') || '';
            const code = row.getAttribute('data-code') || '';

            const matchesStatus = (activeFilter === 'all') || (status === activeFilter);
            const matchesQuery = !query || name.includes(query) || seat.includes(query) || code.includes(query);

            if (matchesStatus && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function recalculateStats() {
        const rows = document.querySelectorAll('#passengerTable tbody tr');
        let total = rows.length;
        let boarded = 0;
        let pending = 0;
        let noShow = 0;

        rows.forEach(r => {
            const s = r.getAttribute('data-status');
            if (s === 'boarded') boarded++;
            else if (s === 'no_show') noShow++;
            else pending++;
        });

        document.getElementById('statTotalCount').innerText = total;
        document.getElementById('statBoardedCount').innerText = boarded;
        document.getElementById('statPendingCount').innerText = pending;
        document.getElementById('statNoShowCount').innerText = noShow;

        document.getElementById('badgeTabAll').innerText = total;
        document.getElementById('badgeTabPending').innerText = pending;
        document.getElementById('badgeTabBoarded').innerText = boarded;
        document.getElementById('badgeTabNoShow').innerText = noShow;
    }

    // ---------------------------------------------------------
    // 7. TOAST NOTIFICATION UTILITY
    // ---------------------------------------------------------
    let toastTimeout = null;

    function showToast(msg, type = 'info') {
        const toast = document.getElementById('toastNotification');
        const icon = document.getElementById('toastIcon');
        const text = document.getElementById('toastMessage');

        if (type === 'success') {
            toast.style.background = '#087f55';
            icon.innerText = '✅';
        } else if (type === 'danger') {
            toast.style.background = '#dc2626';
            icon.innerText = '⚠️';
        } else {
            toast.style.background = '#1e293b';
            icon.innerText = 'ℹ️';
        }

        text.innerText = msg;
        toast.style.display = 'flex';

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3200);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('manualCodeInput');
        if (input) {
            input.focus();
        }
    });
</script>

</body>
</html>
