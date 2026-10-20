<style>
    /* ============================================================
       PALET WARNA & VARIABEL IDENTIK DASHBOARD ADMIN UTAMA
    ============================================================ */
    *, *::before, *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        /* Warna Brand Utama (Identik Layout Admin) */
        --brand: #087f55;
        --brand-dark: #066b47;
        --brand-deep: #045236;
        --brand-light: #e3f5ee;
        --brand-soft: #f0f8f5;

        /* Warna Latar & Surface */
        --page: #f5f8f7;
        --surface: #ffffff;
        --white: #ffffff;

        /* Tipografi & Teks */
        --text: #17221d;
        --text-secondary: #68766f;
        --text-muted: #98a39e;

        /* Border & Aksen Status */
        --border: #e2eae6;
        --border-subtle: #edf3f0;
        --danger: #dc3545;
        --danger-soft: #fff1f2;
        --warning: #f59e0b;
        --warning-soft: #fffbeb;
        --info: #0284c7;
        --info-soft: #f0f9ff;
        --purple: #7c3aed;
        --purple-soft: #faf5ff;

        /* Dimensi & Shadow */
        --sidebar-width: 260px;
        --topbar-height: 82px;
        --radius-sm: 8px;
        --radius-md: 11px;
        --radius-lg: 16px;
        --radius-xl: 18px;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html {
        scroll-behavior: smooth;
    }

    body, .cs-body {
        font-family: 'Overpass', "Segoe UI", Arial, Helvetica, sans-serif;
        background: var(--page);
        color: var(--text);
        min-height: 100vh;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    a, button {
        font-family: inherit;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button {
        cursor: pointer;
    }

    /* ============================================================
       APPLICATION MASTER LAYOUT
    ============================================================ */
    .cs-app-layout {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    .cs-main-wrapper {
        flex: 1;
        margin-left: var(--sidebar-width);
        min-width: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: var(--transition);
    }

    .cs-main-content, .content, .main {
        flex: 1;
        padding: 24px 35px 40px;
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
    }

    /* ============================================================
       SIDEBAR NAVIGATION (TEMA HIJAU IDENTIK ADMIN UTAMA)
    ============================================================ */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: var(--sidebar-width);
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--brand);
        color: white;
        z-index: 100;
        box-shadow: 8px 0 30px rgba(8, 127, 85, 0.08);
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-header {
        height: var(--topbar-height);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .brand-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #ffffff;
        padding: 5px;
        box-sizing: border-box;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .brand-title {
        color: white;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.2px;
    }

    .brand-subtitle {
        display: block;
        margin-top: 3px;
        color: rgba(255, 255, 255, 0.60);
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .sidebar-close-btn {
        display: none;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: white;
        padding: 6px;
        border-radius: 8px;
    }

    /* Identitas Agen CS di Sidebar */
    .sidebar-agent-card {
        padding: 12px 14px;
        margin: 16px 14px 0;
        background: rgba(0, 0, 0, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .agent-avatar-wrap {
        position: relative;
    }

    .agent-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.20);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
    }

    .agent-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid var(--brand);
        background: #22c55e;
    }

    .agent-status-dot.online { background: #22c55e; }
    .agent-status-dot.busy { background: #f59e0b; }
    .agent-status-dot.away { background: #98a39e; }

    .agent-meta {
        overflow: hidden;
    }

    .agent-name {
        font-size: 13px;
        font-weight: 700;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .agent-role-badge {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.70);
        font-weight: 500;
    }

    /* Navigasi Menu */
    .menu {
        flex: 1;
        padding: 20px 14px;
        overflow-y: auto;
    }

    .menu-title {
        padding: 0 12px;
        margin-bottom: 9px;
        color: rgba(255, 255, 255, 0.45);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }

    .menu-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 45px;
        padding: 10px 13px;
        border-radius: 11px;
        color: rgba(255, 255, 255, 0.72);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s ease;
    }

    .menu-icon {
        width: 22px;
        min-width: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.72);
        transition: 0.2s ease;
    }

    .menu-text {
        flex: 1;
    }

    .menu-badge {
        font-size: 10px;
        font-weight: 700;
        background: #ef4444;
        color: white;
        padding: 2px 7px;
        border-radius: 999px;
        line-height: 1.2;
    }

    .menu-link:hover {
        background: rgba(255, 255, 255, 0.09);
        color: white;
        transform: translateX(2px);
    }

    .menu-link:hover .menu-icon {
        color: white;
    }

    .menu-link.active {
        background: white;
        color: var(--brand);
        font-weight: 700;
        box-shadow: 0 7px 18px rgba(0, 0, 0, 0.10);
    }

    .menu-link.active .menu-icon {
        color: var(--brand);
    }

    /* Sidebar Footer Logout */
    .sidebar-footer {
        padding: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }

    .logout-button {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 13px;
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 11px;
        background: rgba(0, 0, 0, 0.10);
        color: rgba(255, 255, 255, 0.72);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .logout-button .menu-icon {
        color: rgba(255, 255, 255, 0.72);
    }

    .logout-button:hover {
        background: rgba(220, 53, 69, 0.95);
        border-color: rgba(220, 53, 69, 0.95);
        color: white;
    }

    .logout-button:hover .menu-icon {
        color: white;
    }

    /* ============================================================
       TOPBAR HEADER (IDENTIK DENGAN ADMIN UTAMA)
    ============================================================ */
    .topbar {
        height: var(--topbar-height);
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 0 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .topbar-mobile-toggle {
        display: none;
        background: none;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 6px;
        color: var(--text);
    }

    .topbar-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
        line-height: 1.2;
    }

    .topbar-subtitle {
        margin-top: 4px;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 500;
    }

    /* Quick Search Bar */
    .topbar-center {
        flex: 1;
        max-width: 420px;
        margin: 0 20px;
    }

    .topbar-search {
        position: relative;
        display: flex;
        align-items: center;
    }

    .topbar-search .search-icon {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        pointer-events: none;
    }

    .topbar-search input {
        width: 100%;
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 9px 70px 9px 36px;
        font-size: 13px;
        color: var(--text);
        outline: none;
        transition: var(--transition);
        font-family: inherit;
    }

    .topbar-search input:focus {
        background: var(--surface);
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .search-shortcut {
        position: absolute;
        right: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 11px;
        color: var(--text-muted);
        font-family: inherit;
        pointer-events: none;
    }

    /* Right Controls */
    .topbar-right {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    /* Status Pill CS */
    .agent-status-dropdown-wrapper {
        position: relative;
    }

    .status-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid transparent;
        transition: var(--transition);
    }

    .status-pill-online {
        background: var(--brand-light);
        color: var(--brand-dark);
        border-color: #b1eed1;
    }

    .status-pill-busy {
        background: var(--warning-soft);
        color: #92400e;
        border-color: #fde68a;
    }

    .status-pill-away {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    .status-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-online { background: #22c55e; box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25); }
    .dot-busy { background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25); }
    .dot-away { background: #98a39e; }

    .status-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        width: 220px;
        padding: 6px;
        display: none;
        flex-direction: column;
        z-index: 1000;
    }

    .status-menu.show {
        display: flex;
    }

    .status-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border: none;
        background: none;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
        text-align: left;
        width: 100%;
        transition: var(--transition);
    }

    .status-menu-item:hover {
        background: var(--page);
    }

    .topbar-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        position: relative;
        transition: var(--transition);
        background: var(--surface);
    }

    .topbar-icon-btn:hover {
        background: var(--brand-soft);
        color: var(--brand);
        border-color: var(--brand-light);
    }

    .topbar-badge-pulse {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 0 2px var(--surface);
    }

    /* Profil CS di Topbar */
    .profile {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 4px 8px;
        border-radius: var(--radius-md);
        cursor: pointer;
        position: relative;
        transition: var(--transition);
    }

    .profile:hover {
        background: var(--page);
    }

    .profile-avatar {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: var(--brand-light);
        color: var(--brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
    }

    .profile-info {
        text-align: right;
    }

    .profile-name {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        line-height: 1.2;
    }

    .profile-role {
        display: block;
        margin-top: 2px;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .profile-chevron {
        color: var(--text-muted);
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        width: 220px;
        padding: 6px;
        display: none;
        flex-direction: column;
        z-index: 1000;
    }

    .profile-dropdown.show {
        display: flex;
    }

    .profile-dropdown-header {
        padding: 10px 12px 6px;
        display: flex;
        flex-direction: column;
    }

    .profile-dropdown-header strong {
        font-size: 13px;
        color: var(--text);
    }

    .profile-dropdown-header small {
        font-size: 11px;
        color: var(--text-muted);
    }

    .profile-dropdown-divider {
        height: 1px;
        background: var(--border-subtle);
        margin: 6px 0;
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
        transition: var(--transition);
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .profile-dropdown-item:hover {
        background: var(--page);
    }

    .profile-dropdown-item.text-danger {
        color: var(--danger);
    }

    .profile-dropdown-item.text-danger:hover {
        background: var(--danger-soft);
    }

    /* ============================================================
       FLASH ALERTS
    ============================================================ */
    .cs-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border-radius: var(--radius-md);
        margin: 20px 35px 0;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .cs-alert-success {
        background: var(--brand-soft);
        border: 1px solid var(--brand-light);
        color: var(--brand-dark);
    }

    .cs-alert-danger {
        background: var(--danger-soft);
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .cs-alert-info {
        background: var(--info-soft);
        border: 1px solid #bae6fd;
        color: #0369a1;
    }

    .cs-alert-icon {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .cs-alert-text {
        flex: 1;
        font-weight: 600;
    }

    .cs-alert-close {
        background: none;
        border: none;
        font-size: 18px;
        line-height: 1;
        color: currentColor;
        opacity: 0.6;
    }

    .cs-alert-close:hover {
        opacity: 1;
    }

    /* ============================================================
       1. WELCOME BANNER (IDENTIK DENGAN ADMIN UTAMA)
    ============================================================ */
    .welcome {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 24px 28px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 60%, #045236 100%);
        border-radius: 18px;
        color: white;
        box-shadow: 0 8px 24px rgba(8, 127, 85, 0.16);
        margin-bottom: 26px;
    }

    .welcome::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-content {
        max-width: 680px;
        z-index: 1;
    }

    .welcome-label-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .welcome-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        background: rgba(255, 255, 255, 0.18);
        padding: 4px 10px;
        border-radius: 30px;
    }

    .welcome-shift-badge {
        font-size: 11px;
        font-weight: 600;
        background: rgba(0, 0, 0, 0.18);
        padding: 4px 10px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #bbf7d0;
    }

    .shift-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.35);
    }

    .welcome-heading, .welcome h1 {
        margin: 0 0 8px;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.2px;
        line-height: 1.25;
    }

    .welcome-description, .welcome p {
        margin: 0 0 20px;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.5;
        max-width: 620px;
    }

    .welcome-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cs-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 600;
        transition: 0.2s ease;
        border: none;
    }

    .cs-btn-light {
        background: white;
        color: var(--brand);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.10);
    }

    .cs-btn-light:hover {
        background: var(--brand-soft);
        transform: translateY(-1px);
    }

    .cs-btn-outline {
        background: rgba(255, 255, 255, 0.16);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .cs-btn-outline:hover {
        background: rgba(255, 255, 255, 0.24);
    }

    .welcome-illustration-wrap {
        z-index: 1;
    }

    .welcome-badge-float {
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        padding: 16px 20px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: white;
    }

    .float-icon {
        font-size: 26px;
    }

    .float-text strong {
        display: block;
        font-size: 16px;
        font-weight: 700;
    }

    .float-text small {
        font-size: 11px;
        opacity: 0.85;
    }

    /* ============================================================
       2. STATISTICS GRID (METRIK KARTU GAYA ADMIN)
    ============================================================ */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 26px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.07);
        border-color: #cbd5e1;
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-title {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
        letter-spacing: -0.2px;
    }

    .stat-value-alert {
        color: #d97706;
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

    /* Ikon dengan aksen warna brand admin utama */
    .icon-teal, .icon-emerald { background: var(--brand-light); color: var(--brand); }
    .icon-blue { background: var(--info-soft); color: var(--info); }
    .icon-amber { background: var(--warning-soft); color: #d97706; }
    .icon-purple { background: var(--purple-soft); color: var(--purple); }

    .stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: var(--text-muted);
        border-top: 1px solid var(--border-subtle);
        padding-top: 12px;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-weight: 700;
    }

    .trend-up { color: var(--brand); }
    .trend-neutral { color: var(--text-muted); }

    .stat-badge-urgency {
        background: #fef3c7;
        color: #b45309;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 10px;
    }

    /* ============================================================
       3. DASHBOARD MAIN GRID
    ============================================================ */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 20px;
        margin-bottom: 26px;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .card-header {
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-subtle);
    }

    .card-header-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-icon-bullet {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.2px;
    }

    .card-link {
        font-size: 12px;
        font-weight: 600;
        color: var(--brand);
        transition: var(--transition);
    }

    .card-link:hover {
        color: var(--brand-dark);
        text-decoration: underline;
    }

    .card-tag {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--page);
        padding: 3px 8px;
        border-radius: 4px;
    }

    .card-body {
        padding: 20px 22px;
    }

    /* ============================================================
       QUICK MENU ITEMS
    ============================================================ */
    .quick-menu {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .quick-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 16px;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        background: var(--page);
        transition: var(--transition);
    }

    .quick-item:hover {
        background: var(--surface);
        border-color: var(--brand);
        box-shadow: 0 4px 12px rgba(8, 127, 85, 0.08);
        transform: translateX(3px);
    }

    .quick-item-featured {
        background: var(--brand-soft);
        border-color: rgba(8, 127, 85, 0.18);
    }

    .quick-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quick-text {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .quick-header-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .quick-pill-active {
        font-size: 10px;
        font-weight: 700;
        background: var(--brand);
        color: white;
        padding: 2px 7px;
        border-radius: 999px;
    }

    .quick-desc {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    /* ============================================================
       ACTIVITY LIST
    ============================================================ */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
    }

    .activity-indicator {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .indicator-success { background: var(--brand-light); color: var(--brand); }
    .indicator-info { background: var(--info-soft); color: var(--info); }
    .indicator-purple { background: var(--purple-soft); color: var(--purple); }
    .indicator-amber { background: var(--warning-soft); color: #d97706; }

    .activity-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 12px;
    }

    .activity-item:last-child .activity-content {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-text {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        line-height: 1.35;
    }

    .activity-subtext {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .activity-time {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ============================================================
       CS FOOTER
    ============================================================ */
    .cs-footer {
        padding: 18px 35px;
        background: var(--surface);
        border-top: 1px solid var(--border);
        margin-top: auto;
    }

    .cs-footer-inner {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .cs-footer-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--page);
        padding: 4px 10px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 11px;
        color: var(--brand-dark);
        border: 1px solid var(--border);
    }

    .status-dot-pulse {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* ============================================================
       MOBILE BACKDROP OVERLAY
    ========================================= */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 99;
        display: none;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    /* ============================================================
       RESPONSIVE BREAKPOINTS
    ============================================================ */
    @media (max-width: 1200px) {
        .stats {
            grid-template-columns: repeat(2, 1fr);
        }
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar-close-btn {
            display: block;
        }
        .cs-main-wrapper {
            margin-left: 0;
        }
        .topbar-mobile-toggle {
            display: block;
        }
        .topbar-center {
            display: none;
        }
        .topbar {
            padding: 0 20px;
        }
        .cs-main-content, .content, .main {
            padding: 20px;
        }
        .welcome {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }
        .welcome-illustration-wrap {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .stats {
            grid-template-columns: 1fr;
        }
        .topbar-subtitle {
            display: none;
        }
        .profile-info {
            display: none;
        }
        .cs-footer-inner {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
    }
    /* ============================================================
       STYLES UNTUK OMNISEARCH, TRIP MONITOR, ESKALASI & NOTICE BOARD
    ============================================================ */
    .card-subtitle-small {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
    }

    /* 1. OMNISEARCH */
    .omnisearch-card {
        margin-bottom: 24px;
        border-color: #cbd5e1;
    }

    .omnisearch-chips-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .hint-tag {
        font-size: 10px;
        font-weight: 700;
        background: var(--page);
        color: var(--text-secondary);
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid var(--border);
    }

    .omnisearch-field-box {
        display: flex;
        align-items: center;
        position: relative;
        width: 100%;
    }

    .omnisearch-icon {
        position: absolute;
        left: 14px;
        color: var(--brand);
        display: flex;
        align-items: center;
        pointer-events: none;
    }

    .omnisearch-input {
        width: 100%;
        background: var(--page);
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 13px 130px 13px 44px;
        font-size: 14px;
        color: var(--text);
        outline: none;
        transition: 0.2s ease;
        font-family: inherit;
    }

    .omnisearch-input:focus {
        background: var(--surface);
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(8, 127, 85, 0.12);
    }

    .omnisearch-btn {
        position: absolute;
        right: 8px;
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-size: 12px;
    }

    .omnisearch-result-container {
        margin-top: 16px;
        animation: fadeIn 0.2s ease;
    }

    .omnisearch-card-result {
        background: var(--brand-soft);
        border: 1px solid var(--brand-light);
        border-radius: var(--radius-md);
        padding: 16px 20px;
    }

    .result-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(8, 127, 85, 0.15);
        margin-bottom: 12px;
    }

    .result-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .result-badge-pnr {
        font-size: 15px;
        font-weight: 800;
        color: var(--brand-dark);
        letter-spacing: 0.5px;
    }

    .result-grid-info {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .res-col {
        display: flex;
        flex-direction: column;
    }

    .res-label {
        font-size: 11px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    .res-val {
        font-size: 13px;
        color: var(--text);
        line-height: 1.3;
    }

    .res-subval {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    /* 2. TRIP MONITOR & DELAY ALERTS */
    .trip-monitor-card {
        margin-bottom: 24px;
    }

    .status-live-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .monitor-filter-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        background: var(--page);
        border-bottom: 1px solid var(--border-subtle);
        overflow-x: auto;
    }

    .filter-btn {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: 0.2s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .filter-btn:hover {
        background: var(--brand-soft);
        color: var(--brand);
        border-color: var(--brand);
    }

    .filter-btn.active {
        background: var(--brand);
        color: white;
        border-color: var(--brand);
    }

    .text-danger-btn {
        color: #dc2626;
    }

    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .cs-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .cs-data-table th {
        background: var(--page);
        padding: 12px 20px;
        color: var(--text-secondary);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid var(--border);
    }

    .cs-data-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-subtle);
        vertical-align: middle;
        color: var(--text);
    }

    .cs-data-table tbody tr:hover {
        background: #fafcfb;
    }

    .trip-row-delay {
        background: #fff8f8;
    }

    .trip-time-cell {
        display: flex;
        flex-direction: column;
    }

    .trip-clock {
        font-size: 14px;
        font-weight: 800;
    }

    .trip-route-name {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .trip-vehicle-cell {
        display: flex;
        flex-direction: column;
        line-height: 1.3;
    }

    .plate-badge {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
    }

    .trip-driver-cell {
        display: flex;
        flex-direction: column;
    }

    .trip-seat-occupancy {
        min-width: 140px;
    }

    .occupancy-labels {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .progress-bar-bg {
        width: 100%;
        height: 6px;
        background: var(--border);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .fill-high { background: #f59e0b; }
    .fill-full { background: #10b981; }

    .delay-alert-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .alert-danger {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-info {
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .trip-actions-cell {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-proactive-chat {
        background: var(--brand);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 11px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s ease;
        white-space: nowrap;
    }

    .btn-proactive-chat:hover {
        background: var(--brand-dark);
    }

    .btn-driver-call {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--page);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        text-decoration: none;
    }

    .btn-driver-call:hover {
        background: var(--brand-light);
    }

    /* 3. ESCALATION TRACKER */
    .escalation-card {
        height: 100%;
    }

    .text-purple { color: var(--purple); }

    .escalation-cat-cell {
        display: flex;
        flex-direction: column;
    }

    .cat-pill {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        width: fit-content;
        margin-bottom: 2px;
    }

    .pill-refund { background: #fee2e2; color: #b91c1c; }
    .pill-lostfound { background: #fef3c7; color: #b45309; }
    .pill-system { background: #e0f2fe; color: #0369a1; }

    .escalation-user-cell {
        display: flex;
        flex-direction: column;
    }

    .status-pill-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .badge-pending-admin { background: #fee2e2; color: #b91c1c; }
    .badge-investigating { background: #fef3c7; color: #b45309; }
    .badge-finance { background: #f3e8ff; color: #7e22ce; }

    .btn-followup {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .btn-followup:hover {
        background: var(--purple-soft);
        color: var(--purple);
        border-color: var(--purple);
    }

    /* 4. NOTICE BOARD (SHIFT BROADCAST) */
    .notice-board-card {
        height: 100%;
    }

    .notice-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .notice-item-card {
        background: var(--page);
        border-radius: var(--radius-md);
        padding: 14px 16px;
        border-left: 4px solid var(--brand);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .notice-border-danger { border-left-color: #ef4444; }
    .notice-border-amber { border-left-color: #f59e0b; }
    .notice-border-brand { border-left-color: var(--brand); }

    .notice-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notice-badge {
        font-size: 11px;
        font-weight: 700;
    }

    .badge-road { color: #dc2626; }
    .badge-fleet { color: #d97706; }
    .badge-promo { color: var(--brand); }

    .notice-time {
        font-size: 10px;
        color: var(--text-muted);
    }

    .notice-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        line-height: 1.3;
    }

    .notice-desc {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .notice-action-hint {
        font-size: 11px;
        background: rgba(0, 0, 0, 0.03);
        padding: 6px 10px;
        border-radius: 6px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

