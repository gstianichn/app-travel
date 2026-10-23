<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        @yield('title', 'Dashboard Admin Utama') - TravelKu
    </title>


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
            --brand: #087f55;
            --brand-dark: #066b47;
            --brand-light: #e3f5ee;
            --brand-soft: #f0f8f5;
            --page: #f5f8f7;
            --surface: #ffffff;
            --text: #17221d;
            --text-secondary: #68766f;
            --text-muted: #98a39e;
            --border: #e2eae6;
            --danger: #dc3545;
            --danger-soft: #fff1f2;
        }
        html {
            scroll-behavior: smooth;
        }
        body {
            min-height: 100vh;
            font-family: 'Overpass', "Segoe UI", system-ui, sans-serif;
            background: var(--page);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }
        
        a,
        button {
            font-family: inherit;
        }

        /* =========================================
           DASHBOARD
        ========================================== */

        .admin-dashboard {
            min-height: 100vh;
        }

        /* =========================================
           SIDEBAR (ORIGINAL GREEN BRAND)
        ========================================== */

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #087f55 0%, #066b47 100%);
            color: white;
            z-index: 100;
            box-shadow: 6px 0 25px rgba(8, 127, 85, 0.12);
        }

        /* =========================================
           LOGO
        ========================================== */

        .admin-logo {
            height: 68px;
            display: flex;
            align-items: center;
            padding: 0 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            text-decoration: none;
            color: white;
        }

        .admin-logo-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #ffffff;
            padding: 5px;
            box-sizing: border-box;
            margin-right: 11px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: transform 0.2s ease;
        }

        .admin-logo:hover .admin-logo-icon {
            transform: scale(1.05);
        }

        .admin-logo-text {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.01em;
            color: #FFFFFF;
        }

        .admin-logo-subtitle {
            display: block;
            margin-top: 2px;
            color: rgba(255, 255, 255, 0.70);
            font-size: 11px;
            font-weight: 500;
        }

        /* =========================================
           MENU
        ========================================== */

        .admin-menu {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .admin-menu::-webkit-scrollbar {
            width: 4px;
        }
        .admin-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .admin-menu-title {
            padding: 0 10px;
            margin-bottom: 6px;
            color: rgba(255, 255, 255, 0.55);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.1px;
        }

        .admin-menu-section {
            margin-top: 14px;
        }

        .admin-menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 38px;
            padding: 8px 11px;
            margin-bottom: 2px;
            border-radius: 9px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .admin-menu-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
            transform: translateX(2px);
        }

        .admin-menu-link.active {
            background: #FFFFFF;
            color: var(--brand);
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10);
            transform: none;
        }

        .admin-menu-icon {
            width: 20px;
            min-width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        /* =========================================
           SIDEBAR BOTTOM
        ========================================== */

        .admin-sidebar-bottom {
            padding: 10px 12px;
            border-top: 1px solid rgba(255,255,255,.12);
        }

        .admin-logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            min-height: 38px;
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 9px;
            background: rgba(0,0,0,.10);
            color: rgba(255,255,255,.72);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: .2s ease;
        }

        .admin-logout-button:hover {
            background: rgba(220,53,69,.95);
            border-color: rgba(220,53,69,.95);
            color: white;
        }

        /* =========================================
           MAIN
        ========================================== */

        .admin-main {
            width: calc(100% - 260px);
            min-height: 100vh;
            margin-left: 260px;
        }

        /* =========================================
           TOPBAR
        ========================================== */

        .admin-topbar {
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(17, 32, 25, 0.02);
        }

        .admin-page-title h2 {
            font-size: 19px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.3px;
        }

        .admin-page-title p {
            margin-top: 3px;
            color: var(--text-secondary);
            font-size: 12px;
        }

        /* =========================================
           PROFILE
        ========================================== */

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 8px 6px 14px;
            border-radius: 40px;
            background: #F7FAF8;
            border: 1px solid #E1E8E4;
            transition: all 0.2s ease;
        }

        .admin-profile:hover {
            background: #F0F5F2;
            border-color: #D3DFD8;
        }

        .admin-profile-info {
            text-align: right;
        }

        .admin-profile-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .admin-profile-role {
            margin-top: 1px;
            color: var(--brand);
            font-size: 11px;
            font-weight: 600;
        }

        .admin-profile-avatar {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #087f55 0%, #066b47 100%);
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(8, 127, 85, 0.25);
        }

        /* =========================================
           CONTENT
        ========================================== */

        .admin-content {
            padding: 32px 35px 40px;
        }

        /* =========================================
           MOBILE
        ========================================== */

        @media (max-width: 900px) {

            .admin-sidebar {
                width: 220px;
            }

            .admin-main {
                width: calc(100% - 220px);
                margin-left: 220px;
            }

            .admin-topbar {
                padding: 0 24px;
            }

            .admin-content {
                padding: 25px 24px;
            }

        }

        @media (max-width: 700px) {

            .admin-sidebar {
                width: 70px;
            }

            .admin-logo {
                justify-content: center;
                padding: 0;
            }

            .admin-logo-icon {
                margin-right: 0;
            }

            .admin-logo-text,
            .admin-logo-subtitle,
            .admin-menu-title,
            .admin-menu-link span:not(.admin-menu-icon),
            .admin-profile-info {
                display: none;
            }

            .admin-menu {
                padding: 20px 10px;
            }

            .admin-menu-link {
                justify-content: center;
                padding: 11px;
            }

            .admin-menu-icon {
                margin: 0;
            }

            .admin-sidebar-bottom {
                padding: 10px;
            }

            .admin-logout-button {
                justify-content: center;
                padding: 12px;
            }

            .admin-main {
                width: calc(100% - 70px);
                margin-left: 70px;
            }

            .admin-topbar {
                height: 72px;
                padding: 0 18px;
            }

            .admin-page-title h2 {
                font-size: 15px;
            }

            .admin-page-title p {
                display: none;
            }

            .admin-content {
                padding: 20px 16px;
            }

        }

        /* =========================================
           SIDEBAR TOGGLE & TRANSITION ENHANCEMENTS
        ========================================== */
        .sidebar-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #e2eae6;
            background: #ffffff;
            color: #17221d;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: #e3f5ee;
            color: #087f55;
            border-color: #a3d9c3;
            transform: scale(1.05);
        }

        .admin-sidebar {
            transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), width 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .admin-main {
            transition: margin-left 0.32s cubic-bezier(0.16, 1, 0.3, 1), width 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Desktop Collapsed */
        .sidebar-collapsed {
            transform: translateX(-260px) !important;
        }

        .sidebar-collapsed-main {
            margin-left: 0 !important;
            width: 100% !important;
        }

        /* Mobile Drawer Slide */
        .admin-sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
            z-index: 99;
            transition: opacity 0.3s ease;
        }

        .admin-sidebar-backdrop.active {
            display: block;
        }

        @media (max-width: 900px) {
            .admin-sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2);
            }
            .admin-sidebar.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* =========================================
           GLOBAL MODAL STYLES (CUSTOM-MODAL & MODAL-BACKDROP)
        ========================================== */
        .custom-modal, .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .custom-modal.show, .modal-backdrop.show, .modal-backdrop.modal-visible, .custom-modal.modal-visible {
            display: flex !important;
            opacity: 1 !important;
        }

        .modal-dialog {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            transform: translateY(14px) scale(0.96);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .custom-modal.show .modal-dialog,
        .modal-backdrop.show .modal-dialog,
        .modal-visible .modal-dialog {
            transform: translateY(0) scale(1) !important;
        }

        .modal-dialog-header, .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-dialog-header h3, .modal-header h3 {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .btn-close-modal, .modal-close-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.15s ease;
        }

        .btn-close-modal:hover, .modal-close-btn:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .modal-dialog-body {
            padding: 20px 24px;
        }

        .modal-dialog-footer, .modal-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }
    </style>

    @stack('styles')
    @fonts('overpass')
    {{-- Brand skin and ds- components: loaded last so it wins over the role's own styles --}}
    @vite(['resources/css/dashboard-staff.css'])
    {{-- Admin Panel enhancements (live search, auto-filter, sidebar animation) --}}
    @vite(['resources/js/admin-panel.js'])
</head>


<body data-staff="admin">

<div class="admin-dashboard">


    {{-- BACKDROP UNTUK MOBILE SLIDE --}}
    <div class="admin-sidebar-backdrop" id="sidebar-backdrop"></div>

    <aside class="admin-sidebar">


        {{-- LOGO --}}

        <a href="{{ route('dashboard.admin.index') }}" class="admin-logo">
            <div class="admin-logo-icon">
                <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div>
                <div class="admin-logo-text">
                    TravelKu
                </div>
                <span class="admin-logo-subtitle">
                    Admin Management
                </span>
            </div>
        </a>

        {{-- MENU --}}

        <nav class="admin-menu" id="admin-menu">
            <div class="admin-menu-title">
                Menu Utama
            </div>

            {{-- DASHBOARD --}}

            <a
                href="{{ route('dashboard.admin.index') }}"
                class="admin-menu-link {{ request()->routeIs('dashboard.admin.index') ? 'active' : '' }}"
            >

                <span class="admin-menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9" rx="1"/>
                        <rect x="14" y="3" width="7" height="5" rx="1"/>
                        <rect x="14" y="12" width="7" height="9" rx="1"/>
                        <rect x="3" y="16" width="7" height="5" rx="1"/>
                    </svg>
                </span>

                <span>
                    Dashboard
                </span>
            </a>
            <div class="admin-menu-section">
                <div class="admin-menu-title">
                    Manajemen
                </div>

                {{-- KELOLA USER --}}

                <a
                    href="{{ route('dashboard.admin.kelola-user.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.kelola-user.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <span>
                        Kelola User
                    </span>
                </a>

                {{-- MENU LAIN --}}

                <a
                    href="{{ route('dashboard.admin.kelola-staff.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.kelola-staff.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                            <path d="m14 11-2 4-2-4"/>
                        </svg>
                    </span>
                    <span>
                        Kelola Staff
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.kelola-driver.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.kelola-driver.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <circle cx="9" cy="10" r="2"/>
                            <path d="M15 8h2"/>
                            <path d="M15 12h2"/>
                            <path d="M7 16h10"/>
                        </svg>
                    </span>
                    <span>
                        Kelola Driver
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.memantau-kendaraan.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.memantau-kendaraan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h13l4 4v7H3V6z"/>
                            <circle cx="7.5" cy="17.5" r="2"/>
                            <circle cx="16.5" cy="17.5" r="2"/>
                            <path d="M16 6v4h4"/>
                            <path d="M7 10h4"/>
                        </svg>
                    </span>
                    <span>
                        Memantau Kendaraan
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.pemesanan.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.pemesanan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                            <path d="M13 5v2"/>
                            <path d="M13 11v2"/>
                            <path d="M13 17v2"/>
                        </svg>
                    </span>
                    <span>
                        Pemesanan
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.pembayaran.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.pembayaran.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </span>
                    <span>
                        Pembayaran
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.rute.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.rute.index') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="19" r="3"/>
                            <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-7a3.5 3.5 0 0 1 0-7H18"/>
                            <circle cx="18" cy="5" r="3"/>
                        </svg>
                    </span>
                    <span>
                        Rute Perjalanan
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.rute.jadwal') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.rute.jadwal*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    <span>
                        Jadwal Keberangkatan
                    </span>
                </a>
                
                <a
                    href="{{ route('dashboard.admin.refund.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.refund.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                    </span>
                    <span>
                        Kelola Refund
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.laporan.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.laporan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </span>
                    <span>
                        Laporan
                    </span>
                </a>
            </div>

            <div class="admin-menu-section">
                <div class="admin-menu-title">
                    Sistem
                </div>

                <a
                    href="{{ route('dashboard.admin.pengaturan.index') }}"
                    class="admin-menu-link {{ request()->routeIs('dashboard.admin.pengaturan.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </span>
                    <span>
                        Pengaturan
                    </span>
                </a>
            </div>
        </nav>

        <script>
            (function() {
                try {
                    var m = document.getElementById('admin-menu');
                    var s = sessionStorage.getItem('admin_sidebar_scroll');
                    if (m && s !== null) {
                        m.scrollTop = parseInt(s, 10);
                    }
                } catch(e) {}
            })();
        </script>

        {{-- LOGOUT --}}

        <div class="admin-sidebar-bottom">
            <form
                action="{{ route('logout') }}"
                method="POST"
            >
                @csrf
                <button
                    type="submit"
                    class="admin-logout-button"
                >
                    <span class="admin-menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </span>
                    <span>
                        Logout
                    </span>
                </button>
            </form>
        </div>
    </aside>

    {{-- =========================================
         MAIN
    ========================================== --}}

    <div class="admin-main">

        {{-- TOPBAR --}}

        <header class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Toggle Sidebar" title="Buka/Tutup Sidebar (Slide)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="admin-page-title">
                    <h2>
                        @yield('page-title', 'Dashboard Admin Utama')
                    </h2>
                    <p>
                        @yield(
                            'page-subtitle',
                            'Kelola sistem Travel dengan mudah dan terintegrasi.'
                        )
                    </p>
                </div>
            </div>

            {{-- PROFILE --}}

            <div class="admin-profile">
                <div class="admin-profile-info">
                    <div class="admin-profile-name">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </div>
                    <div class="admin-profile-role">
                        Admin Utama
                    </div>
                </div>

                <div class="admin-profile-avatar">
                    {{
                        strtoupper(
                            substr(
                                Auth::user()->name ?? 'A',
                                0,
                                1
                            )
                        )
                    }}
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}

        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>