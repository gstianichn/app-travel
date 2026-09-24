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
