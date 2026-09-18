<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        @yield('title', 'Dashboard Admin Utama') - Travel
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
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            background: var(--page);
            color: var(--text);
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
           SIDEBAR
        ========================================== */

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            display: flex;
            flex-direction: column;
            background: var(--brand);
            color: white;
            z-index: 100;
            box-shadow: 8px 0 30px rgba(8, 127, 85, 0.08);
        }

        /* =========================================
           LOGO
        ========================================== */

        .admin-logo {
            height: 82px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid rgba(255,255,255,.12);
        }

        .admin-logo-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: rgba(255,255,255,.15);
            font-size: 21px;
            margin-right: 12px;
        }

        .admin-logo-text {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .admin-logo-subtitle {
            display: block;
            margin-top: 3px;
            color: rgba(255,255,255,.60);
            font-size: 10px;
        }

        /* =========================================
           MENU
        ========================================== */

        .admin-menu {
            flex: 1;
            padding: 24px 14px;
            overflow-y: auto;
        }

        .admin-menu-title {
            padding: 0 12px;
            margin-bottom: 9px;
            color: rgba(255,255,255,.45);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .admin-menu-section {
            margin-top: 25px;
        }

        .admin-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 45px;
            padding: 10px 13px;
            margin-bottom: 4px;
            border-radius: 11px;
            color: rgba(255,255,255,.72);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: .2s ease;
        }

        .admin-menu-link:hover {
            background: rgba(255,255,255,.09);
            color: white;
            transform: translateX(2px);
        }

        .admin-menu-link.active {
            background: white;
            color: var(--brand);
            box-shadow:
                0 7px 18px rgba(0,0,0,.10);
        }

        .admin-menu-icon {
            width: 23px;
            min-width: 23px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* =========================================
           SIDEBAR BOTTOM
        ========================================== */

        .admin-sidebar-bottom {
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,.12);
        }

        .admin-logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 13px;
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 11px;
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
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .admin-page-title h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .admin-page-title p {
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* =========================================
           PROFILE
        ========================================== */

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 11px;
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
            margin-top: 2px;
            color: var(--text-muted);
            font-size: 11px;
        }

        .admin-profile-avatar {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: var(--brand-light);
            color: var(--brand);
            font-size: 15px;
            font-weight: 700;
            border: 1px solid #cdeadd;
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

    </style>

    @stack('styles')
</head>


<body>

<div class="admin-dashboard">


    {{-- =========================================
         SIDEBAR
    ========================================== --}}

    <aside class="admin-sidebar">


        {{-- LOGO --}}

        <div class="admin-logo">
            <div class="admin-logo-icon">
                🚍
            </div>
            <div>
                <div class="admin-logo-text">
                    Travel
                </div>
                <span class="admin-logo-subtitle">
                    Admin Management
                </span>
            </div>
        </div>

        {{-- MENU --}}

        <nav class="admin-menu">
            <div class="admin-menu-title">
                Menu Utama
            </div>

            {{-- DASHBOARD --}}

            <a
                href="{{ route('dashboard.admin.index') }}"
                class="admin-menu-link
                    {{ request()->routeIs('dashboard.admin.index') ? 'active' : '' }}"
            >

                <span class="admin-menu-icon">
                    📊
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
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.kelola-user.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        👥
                    </span>
                    <span>
                        Kelola User
                    </span>
                </a>

                {{-- MENU LAIN --}}

                <a
                    href="{{ route('dashboard.admin.kelola-staff.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.kelola-staff.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        🧑‍💼
                    </span>
                    <span>
                        Kelola Staff
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.kelola-driver.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.kelola-driver.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        🪪
                    </span>
                    <span>
                        Kelola Driver
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.memantau-kendaraan.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.memantau-kendaraan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        🚐
                    </span>
                    <span>
                        Memantau Kendaraan
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.pemesanan.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.pemesanan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        💺
                    </span>
                    <span>
                        Pemesanan
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.pembayaran.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.pembayaran.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        💳
                    </span>
                    <span>
                        Pembayaran
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.rute.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.rute.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        🕒
                    </span>
                    <span>
                        Rute Perjalanan
                    </span>
                </a>
                
                <a
                    href="{{ route('dashboard.admin.refund.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.refund.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        🔄
                    </span>
                    <span>
                        Kelola Refund
                    </span>
                </a>

                <a
                    href="{{ route('dashboard.admin.laporan.index') }}"
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.laporan.*') ? 'active' : '' }}"
                >
                    <span class="admin-menu-icon">
                        📑
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
                    class="admin-menu-link
                        {{ request()->routeIs('dashboard.admin.pengaturan.*') ? 'active' : '' }}"
                >

                    <span class="admin-menu-icon">
                        ⚙
                    </span>
                    <span>
                        Pengaturan
                    </span>
                </a>
            </div>
        </nav>

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
                        ↪
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