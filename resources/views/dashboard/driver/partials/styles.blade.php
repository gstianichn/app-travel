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

        /* ================= 1. FITUR DASHBOARD (KARTU KENDALI UTAMA OPERASIONAL) ================= */
        .cockpit-master-card {
            background: #ffffff;
            border: 2px solid #059669;
            border-radius: var(--radius-xl);
            box-shadow: 0 10px 30px -5px rgba(5, 150, 105, 0.15), 0 4px 12px rgba(15, 23, 42, 0.05);
            margin-bottom: 28px;
            overflow: hidden;
            position: relative;
        }

        .cockpit-hero-header {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 45%, #047857 100%);
            color: #ffffff;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .cockpit-title-group h2 {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.25;
            color: #ffffff;
        }

        .cockpit-title-group p {
            font-size: 13px;
            color: #a7f3d0;
            margin-top: 4px;
        }

        .cockpit-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            color: #ecfdf5;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 6px;
        }

        .cockpit-status-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cockpit-body-grid {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 20px;
            padding: 24px;
            background: #f8fafc;
        }

        @media (max-width: 1024px) {
            .cockpit-body-grid {
                grid-template-columns: 1fr;
                padding: 16px;
                gap: 16px;
            }
        }

        .cockpit-subcard {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: all 0.2s ease;
        }

        .cockpit-subcard:hover {
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .cockpit-subcard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .cockpit-subcard-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            color: var(--brand-dark);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* 1. Kartu Status Tugas */
        .cockpit-task-card {
            border-left: 4px solid #059669;
        }

        .cockpit-trip-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .cockpit-trip-code-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cockpit-trip-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 15px;
            font-weight: 800;
            color: #064e3b;
            background: #d1fae5;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
        }

        .cockpit-route-display {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .cockpit-route-path {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-dark);
            flex-wrap: wrap;
        }

        .cockpit-route-arrow {
            color: #059669;
            font-size: 18px;
        }

        .cockpit-route-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cockpit-task-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .cockpit-meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .cockpit-meta-box .meta-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 2px;
        }

        .cockpit-meta-box .meta-val {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        /* 2. Indikator Cepat Ketersediaan Kursi */
        .cockpit-seat-card {
            border-left: 4px solid #0284c7;
        }

        .cockpit-seat-headline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px 18px;
        }

        .cockpit-seat-counter {
            font-size: 18px;
            font-weight: 800;
            color: #065f46;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .cockpit-seat-counter strong {
            font-size: 22px;
            color: #047857;
        }

        .cockpit-seat-pct-badge {
            background: #059669;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .cockpit-seat-progress-track {
            width: 100%;
            height: 10px;
            background: #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        .cockpit-seat-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 20px;
            transition: width 0.5s ease;
        }

        .cockpit-seat-pills-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        @media (max-width: 640px) {
            .cockpit-seat-pills-row {
                grid-template-columns: 1fr;
            }
        }

        .cockpit-seat-pill {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 8px;
            padding: 8px 10px;
            text-align: center;
        }

        .cockpit-seat-pill span {
            font-size: 10.5px;
            color: var(--text-muted);
            font-weight: 700;
            display: block;
        }

        .cockpit-seat-pill strong {
            font-size: 13.5px;
            color: var(--brand-dark);
            font-weight: 800;
        }

        /* 3. Pintasan Aksi Berikutnya (Next Action Card) */
        .cockpit-next-card {
            border: 2px solid #059669;
            background: #ffffff;
            box-shadow: 0 4px 16px -2px rgba(5, 150, 105, 0.12);
        }

        .cockpit-next-target-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .cockpit-next-target-name {
            font-size: 16.5px;
            font-weight: 800;
            color: #064e3b;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cockpit-next-address {
            margin-top: 6px;
            font-size: 13px;
            color: #1e293b;
            font-weight: 700;
            line-height: 1.4;
        }

        .cockpit-next-notes {
            margin-top: 6px;
            font-size: 12px;
            color: #b45309;
            background: #fffbeb;
            padding: 6px 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .cockpit-gps-nav-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-cockpit-gps {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-cockpit-gps.gmaps {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .btn-cockpit-gps.gmaps:hover {
            background: #dbeafe;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.15);
        }

        .btn-cockpit-gps.waze {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .btn-cockpit-gps.waze:hover {
            background: #dcfce7;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(22, 101, 52, 0.15);
        }

        /* 4. Tombol Pintas Cepat (Quick Shortcuts) */
        .cockpit-shortcuts-card {
            border-left: 4px solid #f59e0b;
        }

        .cockpit-quick-btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 640px) {
            .cockpit-quick-btn-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn-cockpit-shortcut {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-cockpit-shortcut.warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fcd34d;
            color: #92400e;
        }

        .btn-cockpit-shortcut.warning:hover {
            background: #fde68a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        }

        .btn-cockpit-shortcut.danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            color: #991b1b;
            position: relative;
        }

        .btn-cockpit-shortcut.danger:hover {
            background: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
        }

        .btn-cockpit-shortcut-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .btn-cockpit-shortcut.warning .btn-cockpit-shortcut-icon {
            background: #f59e0b;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);
        }

        .btn-cockpit-shortcut.danger .btn-cockpit-shortcut-icon {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.4);
            animation: pulse-ring 2s infinite;
        }

        .btn-cockpit-shortcut-text strong {
            display: block;
            font-size: 13.5px;
            font-weight: 800;
            line-height: 1.25;
        }

        .btn-cockpit-shortcut-text span {
            display: block;
            font-size: 11px;
            opacity: 0.85;
            margin-top: 2px;
        }

        .cockpit-aux-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-cockpit-aux {
            flex: 1;
            min-width: 140px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 12px;
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--brand-dark);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-cockpit-aux:hover {
            background: #e2e8f0;
            color: var(--brand-dark);
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

        /* ================= 2. KALENDER KERJA & AGENDA PENUGASAN ================= */
        .calendar-agenda-wrapper {
            background: #ffffff;
            border-top: 1px solid var(--brand-border);
            padding: 24px 28px;
        }

        .calendar-agenda-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 18px;
        }

        .calendar-agenda-title h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .calendar-agenda-title p {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* 7-Day Weekly Calendar Strip */
        .weekly-calendar-strip {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }

        @media (max-width: 768px) {
            .weekly-calendar-strip {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 480px) {
            .weekly-calendar-strip {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .calendar-day-card {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 12px 8px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .calendar-day-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .calendar-day-card.is-today {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
        }

        .day-name {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .calendar-day-card.is-today .day-name {
            color: #065f46;
        }

        .day-number {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 6px;
        }

        .calendar-day-card.is-today .day-number {
            color: #047857;
        }

        .day-status-pill {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 12px;
            display: inline-block;
            white-space: nowrap;
        }

        .day-status-pill.active-duty {
            background: #059669;
            color: white;
        }

        .day-status-pill.planned-duty {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .day-status-pill.standby-duty {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .day-status-pill.off-duty {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Agenda Tabs Nav */
        .agenda-tab-nav {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .agenda-tab-btn {
            background: none;
            border: none;
            padding: 10px 18px;
            border-radius: 10px 10px 0 0;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            position: relative;
            bottom: -2px;
            border-bottom: 3px solid transparent;
        }

        .agenda-tab-btn:hover {
            color: var(--brand-primary);
            background: #f8fafc;
        }

        .agenda-tab-btn.active {
            color: #059669;
            font-weight: 800;
            border-bottom-color: #059669;
            background: #ecfdf5;
        }

        .agenda-tab-badge {
            background: #e2e8f0;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .agenda-tab-btn.active .agenda-tab-badge {
            background: #059669;
            color: white;
        }

        /* Agenda Panels & Schedule Cards */
        .agenda-tab-panel {
            display: none;
        }

        .agenda-tab-panel.active {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .agenda-schedule-card {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
            position: relative;
        }

        .agenda-schedule-card:hover {
            border-color: #94a3b8;
            box-shadow: var(--shadow-md);
        }

        .agenda-schedule-card.is-active-trip {
            border: 2px solid #059669;
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 40%);
            box-shadow: 0 4px 18px rgba(5, 150, 105, 0.12);
        }

        .agenda-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .agenda-trip-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 800;
            color: #064e3b;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .agenda-route-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        /* Highlight Box: Waktu Berkumpul, Waktu Keberangkatan, Estimasi Durasi */
        .agenda-operational-times-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 14px;
        }

        @media (max-width: 768px) {
            .agenda-operational-times-grid {
                grid-template-columns: 1fr;
            }
        }

        .agenda-time-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .agenda-time-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }

        .agenda-time-val {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--brand-dark);
            line-height: 1.25;
        }

        .agenda-time-val.standby-highlight {
            color: #b45309;
        }

        .agenda-time-val.depart-highlight {
            color: #047857;
        }

        .agenda-time-val.duration-highlight {
            color: #1e40af;
        }

        .agenda-time-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .agenda-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .agenda-vehicle-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .btn-select-schedule {
            background: #059669;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-select-schedule:hover {
            background: #047857;
            transform: translateY(-1px);
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

        /* Vehicle Legal Documents Grid (Pajak STNK & KIR) */
        .vehicle-docs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 20px 28px;
            background: #ffffff;
            border-bottom: 1px solid var(--brand-border);
        }

        @media (max-width: 768px) {
            .vehicle-docs-grid {
                grid-template-columns: 1fr;
                padding: 16px;
            }
        }

        .doc-status-card {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
            transition: all 0.2s ease;
        }

        .doc-status-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .doc-status-card.valid {
            border-left: 4px solid #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .doc-status-card.warning {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
        }

        .doc-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .doc-details {
            flex: 1;
            min-width: 0;
        }

        .doc-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .doc-title-row strong {
            font-size: 13.5px;
            color: var(--brand-dark);
        }

        .doc-date-val {
            font-size: 15px;
            font-weight: 800;
            color: #065f46;
            margin-top: 2px;
        }

        .doc-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 4px;
            line-height: 1.4;
        }

        /* Technical Specifications & Physical Strip */
        .vehicle-specs-container {
            padding: 20px 28px;
            background: #f8fafc;
            border-bottom: 1px solid var(--brand-border);
        }

        .vehicle-specs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        @media (max-width: 992px) {
            .vehicle-specs-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .vehicle-specs-grid {
                grid-template-columns: 1fr;
            }
        }

        .spec-item-box {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 12px 14px;
        }

        .spec-item-box .spec-label {
            font-size: 10.5px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .spec-item-box .spec-val {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--brand-dark);
        }

        .spec-item-box .spec-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
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

        /* SECTION: DAFTAR & DETAIL MANIFEST PENUMPANG (FITUR 4) */
        .rekapitulasi-penumpang-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        @media (max-width: 640px) {
            .rekapitulasi-penumpang-grid {
                grid-template-columns: 1fr;
            }
        }

        .rekap-card {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            transition: all 0.2s ease;
        }

        .rekap-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-sm);
        }

        .rekap-card.rekap-total {
            border-left: 4px solid #3b82f6;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .rekap-card.rekap-boarded {
            border-left: 4px solid #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .rekap-card.rekap-pending {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
        }

        .rekap-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .rekap-icon {
            font-size: 16px;
        }

        .rekap-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rekap-val {
            font-size: 22px;
            font-weight: 800;
            color: var(--brand-dark);
            line-height: 1.2;
        }

        .rekap-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .attendance-progress-wrap {
            background: #f8fafc;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }

        .attendance-progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #334155;
            margin-bottom: 6px;
        }

        .attendance-progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
        }

        .attendance-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

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

        .manifest-search,
        .manifest-search-box {
            margin-bottom: 14px;
            position: relative;
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
        }

        .manifest-search input,
        .manifest-search-box input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: none;
            background: transparent;
            font-size: 13px;
            outline: none;
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
            .btn-sidebar-toggle {
                display: inline-flex !important;
            }
        }

        .btn-sidebar-toggle {
            display: none;
            background: #ffffff;
            border: 1px solid var(--brand-border);
            color: var(--brand-dark);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-sidebar-toggle:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
    </style>
