<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Staff - Travel</title>

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

        button {
            font-family: inherit;
        }

        /* =========================================
           LAYOUT
        ========================================= */

        .dashboard-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {
            width: 260px;
            min-height: 100vh;

            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;

            display: flex;
            flex-direction: column;

            background: linear-gradient(
                180deg,
                var(--primary-dark) 0%,
                var(--primary) 100%
            );

            color: var(--white);

            z-index: 100;

            box-shadow: 4px 0 20px rgba(10, 53, 93, 0.10);
        }

        /* =========================================
           LOGO
        ========================================= */

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

        /* =========================================
           MENU
        ========================================= */

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

            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .menu-icon {
            width: 21px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }

        /* =========================================
           SIDEBAR BOTTOM
        ========================================= */

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

        /* =========================================
           MAIN CONTENT
        ========================================= */

        .main-content {
            width: calc(100% - 260px);

            margin-left: 260px;

            min-height: 100vh;
        }

        /* =========================================
           TOPBAR
        ========================================= */

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
            font-weight: 700;

            margin-bottom: 4px;
        }

        .page-title p {
            color: var(--text-light);

            font-size: 13px;
        }

        /* =========================================
           PROFILE
        ========================================= */

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

        /* =========================================
           CONTENT
        ========================================= */

        .content {
            padding: 32px 36px 40px;
        }

        /* =========================================
           WELCOME
        ========================================= */

        .welcome {
            position: relative;

            overflow: hidden;

            padding: 30px 32px;

            margin-bottom: 25px;

            border-radius: 18px;

            background:
                linear-gradient(
                    120deg,
                    var(--primary-dark),
                    var(--primary),
                    #066b47
                );

            color: var(--white);

            box-shadow:
                0 10px 25px rgba(15, 76, 129, 0.15);
        }

        .welcome::before {
            content: "";

            position: absolute;

            width: 240px;
            height: 240px;

            right: -80px;
            top: -110px;

            border-radius: 50%;

            border: 35px solid rgba(255, 255, 255, 0.07);
        }

        .welcome::after {
            content: "";

            position: absolute;

            width: 150px;
            height: 150px;

            right: 90px;
            bottom: -110px;

            border-radius: 50%;

            background: rgba(25, 167, 206, 0.18);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome h2 {
            font-size: 24px;

            margin-bottom: 8px;
        }

        .welcome p {
            max-width: 650px;

            color: rgba(255, 255, 255, 0.82);

            font-size: 14px;

            line-height: 1.6;
        }

        /* =========================================
           STATISTICS
        ========================================= */

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

            padding: 21px;

            background: var(--white);

            border: 1px solid var(--border);

            border-radius: 15px;

            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);

            box-shadow:
                0 8px 20px rgba(15, 76, 129, 0.08);
        }

        .stat-info span {
            display: block;

            color: var(--text-light);

            font-size: 12px;

            margin-bottom: 7px;
        }

        .stat-info h3 {
            color: var(--text-dark);

            font-size: 25px;
            font-weight: 700;
        }

        .stat-icon {
            width: 45px;
            height: 45px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            font-size: 19px;
        }

        .blue {
            background: var(--primary-light);
            color: var(--primary);
        }

        .cyan {
            background: var(--accent-light);
            color: var(--accent);
        }

        .green {
            background: var(--success-bg);
            color: var(--success);
        }

        .orange {
            background: var(--warning-bg);
            color: var(--warning);
        }

        /* =========================================
           LOWER GRID
        ========================================= */

        .dashboard-grid {
            display: grid;

            grid-template-columns: 1.45fr 1fr;

            gap: 20px;
        }

        .card {
            background: var(--white);

            border: 1px solid var(--border);

            border-radius: 16px;

            padding: 23px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 20px;
        }

        .card-header h3 {
            color: var(--text-dark);

            font-size: 16px;
            font-weight: 700;
        }

        .card-header a {
            color: var(--primary);

            font-size: 12px;
            font-weight: 600;
        }

        .card-header a:hover {
            color: var(--accent);
        }

        /* =========================================
           QUICK MENU
        ========================================= */

        .quick-menu {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 12px;
        }

        .quick-item {
            padding: 17px;

            border: 1px solid var(--border);

            border-radius: 12px;

            color: var(--text);

            transition: all 0.2s ease;
        }

        .quick-item:hover {
            border-color: #b9d8e9;

            background: #f8fcfe;

            transform: translateY(-2px);

            box-shadow:
                0 5px 15px rgba(15, 76, 129, 0.06);
        }

        .quick-icon {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 11px;

            border-radius: 10px;

            background: var(--primary-light);

            color: var(--primary);

            font-size: 16px;
        }

        .quick-item strong {
            display: block;

            color: var(--text-dark);

            font-size: 13px;

            margin-bottom: 4px;
        }

        .quick-item span {
            display: block;

            color: var(--text-light);

            font-size: 11px;

            line-height: 1.5;
        }

        /* =========================================
           ACTIVITY
        ========================================= */

        .activity {
            display: flex;

            flex-direction: column;
        }

        .activity-item {
            display: flex;

            gap: 12px;

            padding: 13px 0;

            border-bottom: 1px solid #edf2f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 9px;
            height: 9px;

            flex-shrink: 0;

            margin-top: 5px;

            border-radius: 50%;

            background: var(--accent);

            box-shadow:
                0 0 0 4px var(--accent-light);
        }

        .activity-item strong {
            display: block;

            color: var(--text);

            font-size: 13px;

            margin-bottom: 4px;
        }

        .activity-item span {
            color: var(--text-light);

            font-size: 11px;
        }

        /* =========================================
           OPERASIONAL: SHIFT & SHORTCUT
        ========================================= */

        .shift-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            padding: 26px 30px;
            margin-bottom: 25px;
            border-radius: 18px;
            background: linear-gradient(135deg, #064932 0%, #087f55 60%, #0a9966 100%);
            color: var(--white);
            box-shadow: 0 10px 25px rgba(8, 127, 85, 0.18);
            position: relative;
            overflow: hidden;
        }

        .shift-banner::after {
            content: "";
            position: absolute;
            right: -40px;
            bottom: -50px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
            pointer-events: none;
        }

        .shift-details {
            position: relative;
            z-index: 2;
        }

        .shift-badge-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .badge-duty {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-duty .pulse-green {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ecc71;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.4);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
        }

        .shift-title {
            font-size: 23px;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .shift-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.90);
        }

        .shift-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .shift-actions {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-shortcut-scanner {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 22px;
            background: #ffffff;
            color: #064932;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .btn-shortcut-scanner:hover {
            background: #e8f8f0;
            color: #053b28;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.20);
        }

        .btn-shortcut-scanner .scan-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #e3f5ee;
            border-radius: 8px;
            font-size: 16px;
        }

        /* =========================================
           OPERASIONAL: DEPARTURE CARDS & TRIP STATUS
        ========================================= */

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .section-header-left h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header-left p {
            font-size: 13px;
            color: var(--text-light);
        }

        .badge-live-monitor {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            background: #eaf8f2;
            color: #087f55;
            border: 1px solid #b1eed1;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .departures-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .dep-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            position: relative;
        }

        .dep-card:hover {
            border-color: #b1eed1;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(8, 127, 85, 0.08);
        }

        .dep-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 1px dashed var(--border);
            margin-bottom: 16px;
        }

        .dep-time-box {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .dep-time {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .dep-countdown {
            font-size: 11px;
            font-weight: 600;
            color: #087f55;
            background: #eefbf5;
            padding: 2px 8px;
            border-radius: 8px;
        }

        /* INDIKATOR STATUS TRIP */
        .trip-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .trip-tag-boarding {
            background: #eaf8f2;
            color: #087f55;
            border: 1px solid #b1eed1;
        }

        .trip-tag-boarding .tag-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #087f55;
            animation: pulseDot 1.5s infinite;
        }

        .trip-tag-prep {
            background: #fff8eb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .trip-tag-done {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .dep-route {
            margin-bottom: 14px;
        }

        .dep-route-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dep-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .dep-info-item span {
            display: block;
            font-size: 11px;
            color: var(--text-light);
            margin-bottom: 2px;
        }

        .dep-info-item strong {
            display: block;
            font-size: 13px;
            color: var(--text-dark);
            font-weight: 600;
        }

        /* RASIO KETERISIAN KURSI */
        .occupancy-section {
            margin-bottom: 16px;
        }

        .occupancy-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .occupancy-header span {
            color: var(--text-light);
            font-weight: 500;
        }

        .occupancy-header strong {
            color: var(--text-dark);
            font-weight: 700;
        }

        .occupancy-bar-bg {
            height: 9px;
            background: #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .occupancy-bar-fill {
            height: 100%;
            border-radius: 6px;
            background: linear-gradient(90deg, #3ecc91, #087f55);
            transition: width 0.4s ease;
        }

        .occupancy-bar-fill.warning {
            background: linear-gradient(90deg, #fbbf24, #d97706);
        }

        .occupancy-bar-fill.full {
            background: linear-gradient(90deg, #f87171, #dc2626);
        }

        .dep-card-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-action-primary {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 14px;
            background: #eefbf5;
            color: #087f55;
            border: 1px solid #b1eed1;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action-primary:hover {
            background: #087f55;
            color: #ffffff;
            border-color: #087f55;
        }

        .btn-action-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 14px;
            background: #ffffff;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* =========================================
           MODAL: PEMINDAI KAMERA SCAN QR
        ========================================= */

        .scanner-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }

        .scanner-modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .scanner-modal-content {
            background: #ffffff;
            border-radius: 20px;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            transform: translateY(20px) scale(0.96);
            transition: all 0.25s ease;
        }

        .scanner-modal-backdrop.active .scanner-modal-content {
            transform: translateY(0) scale(1);
        }

        .scanner-header {
            padding: 18px 24px;
            background: #064932;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .scanner-header h4 {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .scanner-close-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .scanner-close-btn:hover {
            background: rgba(255, 255, 255, 0.30);
        }

        .scanner-body {
            padding: 24px;
        }

        .scanner-camera-box {
            position: relative;
            width: 100%;
            height: 240px;
            background: #0f172a;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .scanner-hud-frame {
            position: relative;
            width: 170px;
            height: 170px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.45);
        }

        .scanner-hud-frame::before,
        .scanner-hud-frame::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #3ecc91;
            border-style: solid;
        }

        .scanner-hud-frame::before {
            top: -2px;
            left: -2px;
            border-width: 3px 0 0 3px;
            border-radius: 6px 0 0 0;
        }

        .scanner-hud-frame::after {
            bottom: -2px;
            right: -2px;
            border-width: 0 3px 3px 0;
            border-radius: 0 0 6px 0;
        }

        .scanner-laser {
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3ecc91, transparent);
            box-shadow: 0 0 12px #3ecc91;
            animation: scanLaser 2s infinite ease-in-out;
        }

        @keyframes scanLaser {
            0% { top: 10%; }
            50% { top: 90%; }
            100% { top: 10%; }
        }

        .scanner-instruction {
            position: absolute;
            bottom: 12px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            text-align: center;
        }

        .scanner-manual-form {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .scanner-manual-input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-family: monospace;
            outline: none;
            transition: border-color 0.2s;
        }

        .scanner-manual-input:focus {
            border-color: #087f55;
            box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.10);
        }

        .scanner-manual-btn {
            padding: 10px 18px;
            background: #087f55;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .scanner-manual-btn:hover {
            background: #066b47;
        }

        .scanner-test-chips {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .scanner-test-chips span {
            font-size: 11px;
            color: var(--text-light);
        }

        .test-chip-btn {
            padding: 4px 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: monospace;
        }

        .test-chip-btn:hover {
            background: #e2e8f0;
            color: #087f55;
        }

        .scan-result-card {
            display: none;
            background: #eefbf5;
            border: 1px solid #b1eed1;
            border-radius: 12px;
            padding: 16px;
            margin-top: 14px;
        }

        .scan-result-card.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .scan-result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .scan-result-header strong {
            color: #087f55;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .scan-result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .scan-result-grid span {
            color: #68766f;
        }

        .scan-result-grid b {
            color: #17221d;
        }

        .btn-confirm-boarding {
            width: 100%;
            padding: 10px;
            background: #087f55;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-confirm-boarding:hover {
            background: #066b47;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1150px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
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

            .logo-text,
            .menu-title,
            .menu a span,
            .sidebar-bottom span {
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
                padding: 0 22px;
            }

            .content {
                padding: 24px 22px;
            }

            .profile-info {
                display: none;
            }
        }

        @media (max-width: 550px) {

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-menu {
                grid-template-columns: 1fr;
            }

            .welcome {
                padding: 25px;
            }

            .welcome h2 {
                font-size: 20px;
            }

            .page-title h1 {
                font-size: 18px;
            }

            .page-title p {
                display: none;
            }

            .content {
                padding: 20px 15px;
            }

            .topbar {
                padding: 0 15px;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-wrapper">

    <!-- =========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="logo-area">

            <div class="logo-icon">
                T
            </div>

            <div class="logo-text">

                <h2>Travel</h2>

                <span>
                    Management System
                </span>

            </div>

        </div>


        <nav class="menu">

            <div class="menu-title">
                Menu Utama
            </div>


            <a
                href="{{ route('dashboard.admin-biasa.index') }}"
                class="active"
            >
                <div class="menu-icon">
                    ⌂
                </div>

                <span>
                    Dashboard
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.booking') }}">
                <div class="menu-icon">
                    🎫
                </div>

                <span>
                    Manajemen Booking
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}">
                <div class="menu-icon">
                    🗺️
                </div>

                <span>
                    Melihat Informasi Perjalanan
                </span>
            </a>


            <a href="#">
                <div class="menu-icon">
                    🎟️
                </div>

                <span>
                    Validasi E-Ticket
                </span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                >

                    <div class="menu-icon">
                        ↪
                    </div>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>


    <!-- =========================================
         MAIN
    ========================================== -->

    <main class="main-content">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="page-title">

                <h1>
                    Dashboard Operasional (Layar Utama)
                </h1>

                <p>
                    Pusat pemantauan keberangkatan armada, validasi tiket, dan operasional loket.
                </p>

            </div>


            <div class="profile">

                <div class="avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'S',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="profile-info">

                    <strong>
                        {{ auth()->user()->name ?? 'Staff Operasional' }}
                    </strong>

                    <span>
                        {{ $shiftInfo['pool_name'] }}
                    </span>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <!-- 1. INFORMASI SHIFT & LOKASI + TOMBOL PINTAS (SHORTCUT) -->

            <div class="shift-banner">

                <div class="shift-details">

                    <div class="shift-badge-row">
                        <span class="badge-duty">
                            <span class="pulse-green"></span>
                            {{ $shiftInfo['status'] }}
                        </span>
                        <span class="badge-duty" style="background: rgba(255,255,255,0.12);">
                            🕒 {{ $shiftInfo['shift_name'] }}
                        </span>
                    </div>

                    <h2 class="shift-title">
                        Halo, {{ $shiftInfo['staff_name'] }} 👋
                    </h2>

                    <div class="shift-meta">
                        <div class="shift-meta-item">
                            <span>📍</span>
                            <strong>{{ $shiftInfo['pool_name'] }}</strong>
                        </div>
                        <div class="shift-meta-item">
                            <span>📅</span>
                            <span>{{ $shiftInfo['active_date'] }}</span>
                        </div>
                        <div class="shift-meta-item">
                            <span>🏢</span>
                            <span>Loket Keberangkatan Utama</span>
                        </div>
                    </div>

                </div>

                <!-- TOMBOL PINTAS (SHORTCUT): SCAN QR -->
                <div class="shift-actions">
                    <button type="button" class="btn-shortcut-scanner" onclick="openScannerModal()">
                        <div class="scan-icon-box">
                            📷
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; line-height: 1.2;">Buka Kamera Pemindai</div>
                            <div style="font-size: 11px; font-weight: 500; opacity: 0.8;">Tombol Pintas (Scan QR)</div>
                        </div>
                    </button>
                </div>

            </div>


            <!-- 2. METRIK OPERASIONAL HARI INI -->

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Jadwal Aktif Hari Ini</span>
                        <h3>{{ $stats['active_schedules'] }} Trip</h3>
                    </div>
                    <div class="stat-icon blue">
                        🚐
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Boarding Dibuka</span>
                        <h3 style="color: #087f55;">{{ $stats['boarding_now'] }} Armada</h3>
                    </div>
                    <div class="stat-icon green">
                        🟢
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Tahap Persiapan</span>
                        <h3 style="color: #b45309;">{{ $stats['ready_preparation'] }} Armada</h3>
                    </div>
                    <div class="stat-icon orange">
                        ⏳
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Pemesanan Hari Ini</span>
                        <h3>{{ $stats['today_bookings'] }} Tiket</h3>
                    </div>
                    <div class="stat-icon cyan">
                        🎫
                    </div>
                </div>

            </div>


            <!-- 3. DAFTAR KEBERANGKATAN TERDEKAT (1-2 JAM KE DEPAN) -->

            <div class="section-header">
                <div class="section-header-left">
                    <h3>
                        🚍 Daftar Keberangkatan Terdekat (1–2 Jam Ke Depan)
                        <span class="badge-live-monitor">
                            <span class="pulse-green" style="width: 6px; height: 6px; background: #087f55; border-radius: 50%; display: inline-block;"></span>
                            Live Monitor
                        </span>
                    </h3>
                    <p>
                        Pantau jam keberangkatan, rute tujuan, kesiapan armada, driver, dan rasio keterisian kursi penumpang.
                    </p>
                </div>

                <div>
                    <button type="button" class="btn-action-secondary" onclick="window.location.reload()" title="Muat Ulang Data">
                        🔄 Segarkan Jadwal
                    </button>
                </div>
            </div>

            <div class="departures-grid">

                @forelse($departures as $dep)
                    <div class="dep-card">

                        <div>
                            <!-- Header Kartu: Jam Berangkat & Indikator Status Trip -->
                            <div class="dep-card-top">
                                <div class="dep-time-box">
                                    <span class="dep-time">{{ $dep->time }}</span>
                                    <span class="dep-countdown">{{ $dep->departure_note }}</span>
                                </div>

                                <!-- INDIKATOR STATUS TRIP: Tag jelas seperti Persiapan, Boarding Dibuka, atau Selesai -->
                                @if($dep->status === 'Boarding Dibuka')
                                    <span class="trip-tag trip-tag-boarding">
                                        <span class="tag-dot"></span>
                                        Boarding Dibuka
                                    </span>
                                @elseif($dep->status === 'Persiapan')
                                    <span class="trip-tag trip-tag-prep">
                                        ⏳ Persiapan
                                    </span>
                                @else
                                    <span class="trip-tag trip-tag-done">
                                        ✅ Selesai
                                    </span>
                                @endif
                            </div>

                            <!-- Rute Perjalanan -->
                            <div class="dep-route">
                                <div class="dep-route-name">
                                    <span>🛣️</span>
                                    <span>{{ $dep->route_name }}</span>
                                </div>
                            </div>

                            <!-- Info Armada, Pelat Nomor & Supir -->
                            <div class="dep-info-grid">
                                <div class="dep-info-item">
                                    <span>Armada & Pelat Nomor</span>
                                    <strong>🚐 {{ $dep->plate_number }}</strong>
                                    <small style="font-size: 10px; color: #64748b;">{{ $dep->vehicle_name }}</small>
                                </div>

                                <div class="dep-info-item">
                                    <span>Driver / Supir</span>
                                    <strong>👤 {{ $dep->driver_name }}</strong>
                                    <small style="font-size: 10px; color: #087f55;">Standby di Pool</small>
                                </div>
                            </div>

                            <!-- RASIO KETERISIAN KURSI (Misal: 8/10 kursi) -->
                            <div class="occupancy-section">
                                <div class="occupancy-header">
                                    <span>Rasio Keterisian Kursi</span>
                                    <strong>{{ $dep->ratio }} ({{ $dep->percentage }}%)</strong>
                                </div>
                                <div class="occupancy-bar-bg">
                                    <div
                                        class="occupancy-bar-fill {{ $dep->percentage >= 100 ? 'full' : ($dep->percentage >= 80 ? 'warning' : '') }}"
                                        style="width: {{ $dep->percentage }}%;"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Cepat Kartu -->
                        <div class="dep-card-actions">
                            <button
                                type="button"
                                class="btn-action-primary"
                                onclick="openScannerForTrip('{{ $dep->id }}', '{{ $dep->route_name }}', '{{ $dep->plate_number }}')"
                            >
                                📷 Scan Boarding Pass
                            </button>

                            <a
                                href="{{ route('dashboard.admin-biasa.booking') }}?schedule_id={{ $dep->id }}"
                                class="btn-action-secondary"
                            >
                                📋 Manifes
                            </a>
                        </div>

                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: white; border-radius: 16px; border: 1px dashed var(--border);">
                        <p style="color: var(--text-light); font-size: 14px;">Belum ada jadwal keberangkatan untuk 1–2 jam ke depan.</p>
                    </div>
                @endforelse

            </div>


            <!-- 4. MENU AKSES CEPAT & AKTIVITAS -->

            <div class="dashboard-grid">

                <!-- AKSES CEPAT -->
                <div class="card">

                    <div class="card-header">
                        <h3>Akses Cepat Operasional</h3>
                    </div>

                    <div class="quick-menu">

                        <a href="javascript:void(0)" onclick="openScannerModal()" class="quick-item">
                            <div class="quick-icon" style="background: #eefbf5; color: #087f55;">
                                📷
                            </div>
                            <strong>Validasi E-Ticket (Scan QR)</strong>
                            <span>Pindai barcode & verifikasi tiket penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="quick-item">
                            <div class="quick-icon">
                                🎫
                            </div>
                            <strong>Manajemen Booking</strong>
                            <span>Lihat dan kelola pesanan penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.perjalanan') }}" class="quick-item">
                            <div class="quick-icon">
                                🗺️
                            </div>
                            <strong>Informasi Perjalanan</strong>
                            <span>Cek jadwal rute, jam, dan pool transit</span>
                        </a>

                        <a href="javascript:void(0)" onclick="alert('Membuka Laporan Boarding Harian')" class="quick-item">
                            <div class="quick-icon">
                                📋
                            </div>
                            <strong>Laporan Harian</strong>
                            <span>Rekapitulasi boarding pass loket</span>
                        </a>

                    </div>

                </div>


                <!-- AKTIVITAS TERBARU -->
                <div class="card">

                    <div class="card-header">
                        <h3>Aktivitas Terkini</h3>
                        <a href="javascript:void(0)" onclick="alert('Memuat seluruh riwayat aktivitas...')">
                            Lihat semua
                        </a>
                    </div>

                    <div class="activity">

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #087f55; box-shadow: 0 0 0 4px #eefbf5;"></div>
                            <div>
                                <strong>Boarding Pass Divalidasi (Kursi 03A)</strong>
                                <span>Rute Bandung ➔ Jakarta (Semanggi) • 2 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <strong>Status Armada D 7812 AB: Boarding Dibuka</strong>
                                <span>Pemberitahuan gate keberangkatan • 10 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #b45309; box-shadow: 0 0 0 4px #fff8eb;"></div>
                            <div>
                                <strong>Armada B 1945 TKL Memasuki Tahap Persiapan</strong>
                                <span>Pengecekan unit HiAce dan supir standby • 35 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #64748b; box-shadow: 0 0 0 4px #f1f5f9;"></div>
                            <div>
                                <strong>Trip D 7551 GH Telah Diberangkatkan Tepat Waktu</strong>
                                <span>10/10 Penumpang boarding penuh • 55 menit yang lalu</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- =========================================
     MODAL: KAMERA PEMINDAI (SCAN QR E-TICKET)
========================================== -->

<div id="scannerModal" class="scanner-modal-backdrop" onclick="handleBackdropClick(event)">

    <div class="scanner-modal-content">

        <div class="scanner-header">
            <h4>
                <span>📷</span>
                <span id="modalTitle">Kamera Pemindai QR Code E-Ticket</span>
            </h4>
            <button type="button" class="scanner-close-btn" onclick="closeScannerModal()" title="Tutup Pemindai">
                ✕
            </button>
        </div>

        <div class="scanner-body">

            <!-- Viewfinder Kamera dengan Animasi Laser -->
            <div class="scanner-camera-box">
                <div class="scanner-hud-frame">
                    <div class="scanner-laser"></div>
                </div>
                <div class="scanner-instruction">
                    Arahkan kamera ke QR Code pada E-Ticket / Boarding Pass penumpang
                </div>
            </div>

            <!-- Form Manual Kode Booking -->
            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                    Atau Masukkan Kode Booking / E-Ticket:
                </label>
                <div class="scanner-manual-form">
                    <input
                        type="text"
                        id="bookingCodeInput"
                        class="scanner-manual-input"
                        placeholder="Contoh: TKV-882190-JKT"
                        onkeydown="if(event.key==='Enter'){ validateTicketCode(); }"
                    >
                    <button type="button" class="scanner-manual-btn" onclick="validateTicketCode()">
                        Periksa
                    </button>
                </div>
            </div>

            <!-- Quick Test Chips -->
            <div class="scanner-test-chips">
                <span>Simulasi Cepat:</span>
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-7821-JKT', 'Budi Santoso', '03A', 'Bandung → Jakarta', 'D 7812 AB')">
                    ⚡ TKV-7821-JKT (Budi - 03A)
                </button>
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-9904-CGK', 'Siti Rahma', '01B', 'Bandung → Bandara Soetta', 'B 1945 TKL')">
                    ⚡ TKV-9904-CGK (Siti - 01B)
                </button>
            </div>

            <!-- Kartu Hasil Pemindaian / Validasi -->
            <div id="scanResultCard" class="scan-result-card">
                <div class="scan-result-header">
                    <strong>
                        <span>✅</span>
                        <span>E-TICKET VALID & SIAP BOARDING</span>
                    </strong>
                    <span style="font-size: 11px; font-weight: 700; background: #b1eed1; color: #064932; padding: 2px 8px; border-radius: 6px;" id="resultTripStatus">
                        Lunas
                    </span>
                </div>

                <div class="scan-result-grid">
                    <div>
                        <span>Nama Penumpang</span>
                        <b id="resultPassengerName">-</b>
                    </div>
                    <div>
                        <span>Nomor Kursi</span>
                        <b id="resultSeatNumber" style="color: #087f55; font-size: 14px;">-</b>
                    </div>
                    <div>
                        <span>Kode Booking</span>
                        <b id="resultBookingCode" style="font-family: monospace;">-</b>
                    </div>
                    <div>
                        <span>Rute & Armada</span>
                        <b id="resultRoute">-</b>
                    </div>
                </div>

                <button type="button" class="btn-confirm-boarding" onclick="confirmBoarding()">
                    Konfirmasi Masuk Armada (Boarding Pass Valid)
                </button>
            </div>

        </div>

    </div>

</div>


<!-- =========================================
     JAVASCRIPT SCANNER LOGIC
========================================== -->

<script>
    function openScannerModal() {
        document.getElementById('modalTitle').innerText = 'Kamera Pemindai QR Code E-Ticket';
        document.getElementById('bookingCodeInput').value = '';
        document.getElementById('scanResultCard').classList.remove('active');
        document.getElementById('scannerModal').classList.add('active');
        setTimeout(() => {
            document.getElementById('bookingCodeInput').focus();
        }, 300);
    }

    function openScannerForTrip(tripId, routeName, plateNumber) {
        openScannerModal();
        document.getElementById('modalTitle').innerText = 'Scan Boarding: ' + plateNumber + ' (' + routeName + ')';
    }

    function closeScannerModal() {
        document.getElementById('scannerModal').classList.remove('active');
    }

    function handleBackdropClick(event) {
        if (event.target === document.getElementById('scannerModal')) {
            closeScannerModal();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeScannerModal();
        }
    });

    function simulateScan(code, name, seat, route, plate) {
        document.getElementById('bookingCodeInput').value = code;
        displayScanResult(code, name, seat, route + ' (' + plate + ')');
    }

    function validateTicketCode() {
        const code = document.getElementById('bookingCodeInput').value.trim();
        if (!code) {
            alert('Silakan ketik atau tempelkan kode booking e-ticket terlebih dahulu.');
            return;
        }

        displayScanResult(code.toUpperCase(), 'Rian Pratama', '04A', 'Bandung (Dipatiukur) → Jakarta (D 7812 AB)');
    }

    function displayScanResult(code, name, seat, route) {
        document.getElementById('resultBookingCode').innerText = code;
        document.getElementById('resultPassengerName').innerText = name;
        document.getElementById('resultSeatNumber').innerText = 'Kursi ' + seat;
        document.getElementById('resultRoute').innerText = route;
        document.getElementById('scanResultCard').classList.add('active');
    }

    function confirmBoarding() {
        const name = document.getElementById('resultPassengerName').innerText;
        const seat = document.getElementById('resultSeatNumber').innerText;
        alert('🎉 Sukses! Penumpang ' + name + ' (' + seat + ') telah berhasil divalidasi dan tercatat Masuk Armada.');
        closeScannerModal();
    }
</script>

</body>

</html>

