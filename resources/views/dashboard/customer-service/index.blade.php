<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Customer Service - TravelKu</title>

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
            --green: #087F5B;
            --green-dark: #07543F;
            --green-hover: #066A4C;
            --green-light: #E8F5F0;

            --white: #FFFFFF;
            --page: #F5F7F6;

            --text: #111827;
            --text-secondary: #64748B;

            --border: #DDE5E2;
            --danger: #DC2626;

            --sidebar-width: 260px;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--page);
            color: var(--text);
            min-height: 100vh;
        }

        /* ========================================
           SIDEBAR
        ======================================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: var(--sidebar-width);
            height: 100vh;

            background: var(--white);

            border-right: 1px solid var(--border);

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        /* BRAND */

        .sidebar-header {
            height: 86px;

            padding: 0 25px;

            display: flex;
            align-items: center;

            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;

            gap: 12px;

            text-decoration: none;
        }

        .brand-icon {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            background: var(--green);

            color: var(--white);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: 700;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            color: var(--green);

            font-size: 20px;
            font-weight: 700;
        }

        .brand-subtitle {
            color: var(--text-secondary);

            font-size: 11px;

            margin-top: 2px;
        }

        /* MENU */

        .menu {
            flex: 1;

            padding: 25px 15px;

            overflow-y: auto;
        }

        .menu-title {
            padding: 0 12px 12px;

            color: #94A3B8;

            font-size: 11px;
            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;
        }

        .menu-list {
            list-style: none;
        }

        .menu-item {
            margin-bottom: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;

            gap: 12px;

            padding: 12px 13px;

            border-radius: 9px;

            color: var(--text-secondary);

            text-decoration: none;

            font-size: 14px;
            font-weight: 500;

            transition: 0.2s ease;
        }

        .menu-link:hover {
            background: var(--green-light);

            color: var(--green);
        }

        .menu-link.active {
            background: var(--green);

            color: var(--white);
        }

        .menu-icon {
            width: 20px;

            display: flex;
            justify-content: center;

            font-size: 16px;
        }

        /* LOGOUT */

        .sidebar-footer {
            padding: 15px;

            border-top: 1px solid var(--border);
        }

        .logout-button {
            width: 100%;

            border: none;

            background: transparent;

            color: var(--danger);

            display: flex;
            align-items: center;

            gap: 12px;

            padding: 12px 13px;

            border-radius: 9px;

            font-size: 14px;
            font-weight: 500;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .logout-button:hover {
            background: #ff5050;
            color: white;
        }

        /* ========================================
           MAIN
        ======================================== */

        .main {
            margin-left: var(--sidebar-width);

            min-height: 100vh;
        }

        /* ========================================
           TOPBAR
        ======================================== */

        .topbar {
            height: 86px;

            padding: 0 35px;

            background: var(--white);

            border-bottom: 1px solid var(--border);

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-title {
            font-size: 22px;

            font-weight: 700;

            color: var(--text);
        }

        .topbar-subtitle {
            margin-top: 3px;

            color: var(--text-secondary);

            font-size: 13px;
        }

        /* PROFILE */

        .profile {
            display: flex;
            align-items: center;

            gap: 12px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: var(--green-light);

            color: var(--green);

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 700;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 14px;

            font-weight: 600;
        }

        .profile-role {
            margin-top: 2px;

            color: var(--text-secondary);

            font-size: 11px;
        }

        /* ========================================
           CONTENT
        ======================================== */

        .content {
            padding: 32px 35px;
        }

        /* ========================================
           WELCOME BANNER
        ======================================== */

        .welcome {
            position: relative;

            overflow: hidden;

            margin-bottom: 28px;

            padding: 30px;

            background: var(--green);

            color: var(--white);

            border-radius: 14px;
        }

        .welcome::before {
            content: "";

            position: absolute;

            width: 170px;
            height: 170px;

            right: -45px;
            top: -80px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.08);
        }

        .welcome::after {
            content: "";

            position: absolute;

            width: 110px;
            height: 110px;

            right: 110px;
            bottom: -70px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.05);
        }

        .welcome-content {
            position: relative;

            z-index: 2;
        }

        .welcome-label {
            margin-bottom: 8px;

            font-size: 11px;

            font-weight: 700;

            letter-spacing: 1px;

            text-transform: uppercase;

            opacity: 0.8;
        }

        .welcome h1 {
            margin-bottom: 7px;

            font-size: 27px;

            font-weight: 600;
        }

        .welcome p {
            font-size: 14px;

            opacity: 0.9;
        }

        /* ========================================
           STATISTICS
        ======================================== */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 18px;

            margin-bottom: 28px;
        }

        .stat-card {
            padding: 20px;

            background: var(--white);

            border: 1px solid var(--border);

            border-radius: 13px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            transition: 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);

            border-color: rgba(8, 127, 91, 0.35);

            box-shadow:
                0 8px 25px rgba(8, 127, 91, 0.08);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-title {
            margin-bottom: 7px;

            color: var(--text-secondary);

            font-size: 12px;
        }

        .stat-value {
            color: var(--text);

            font-size: 27px;

            font-weight: 700;
        }

        .stat-icon {
            width: 46px;
            height: 46px;

            border-radius: 11px;

            background: var(--green-light);

            color: var(--green);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 19px;
        }

        /* ========================================
           DASHBOARD GRID
        ======================================== */

        .dashboard-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(300px, 1fr);

            gap: 20px;
        }

        .card {
            background: var(--white);

            border: 1px solid var(--border);

            border-radius: 13px;

            overflow: hidden;
        }

        .card-header {
            padding: 20px 22px;

            border-bottom: 1px solid var(--border);

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 16px;

            font-weight: 700;
        }

        .card-link {
            color: var(--green);

            font-size: 12px;

            font-weight: 600;

            text-decoration: none;
        }

        .card-link:hover {
            color: var(--green-hover);
        }

        .card-body {
            padding: 20px 22px;
        }

        /* ========================================
           QUICK MENU
        ======================================== */

        .quick-menu {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 12px;
        }

        .quick-item {
            padding: 15px;

            border: 1px solid var(--border);

            border-radius: 10px;

            color: var(--text);

            text-decoration: none;

            display: flex;
            align-items: center;

            gap: 12px;

            transition: 0.2s ease;
        }

        .quick-item:hover {
            background: var(--green-light);

            border-color: var(--green);

            transform: translateY(-1px);
        }

        .quick-icon {
            width: 40px;
            height: 40px;

            flex-shrink: 0;

            border-radius: 9px;

            background: var(--green-light);

            color: var(--green);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }

        .quick-text {
            display: flex;
            flex-direction: column;

            gap: 2px;
        }

        .quick-title {
            font-size: 13px;

            font-weight: 600;
        }

        .quick-desc {
            color: var(--text-secondary);

            font-size: 11px;
        }

        /* ========================================
           ACTIVITY
        ======================================== */

        .activity-list {
            display: flex;
            flex-direction: column;
        }

        .activity-item {
            padding: 14px 0;

            border-bottom: 1px solid #EEF2F1;

            display: flex;
            align-items: flex-start;

            gap: 12px;
        }

        .activity-item:first-child {
            padding-top: 0;
        }

        .activity-item:last-child {
            padding-bottom: 0;

            border-bottom: none;
        }

        .activity-dot {
            width: 9px;
            height: 9px;

            flex-shrink: 0;

            margin-top: 5px;

            border-radius: 50%;

            background: var(--green);
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: var(--text);

            font-size: 13px;

            line-height: 1.5;
        }

        .activity-time {
            margin-top: 3px;

            color: var(--text-secondary);

            font-size: 11px;
        }

        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 800px) {

            :root {
                --sidebar-width: 0px;
            }

            .sidebar {
                width: 0;

                overflow: hidden;

                border: none;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 25px 20px;
            }

            .topbar-title {
                font-size: 19px;
            }

            .profile-info {
                display: none;
            }
        }

        @media (max-width: 600px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .quick-menu {
                grid-template-columns: 1fr;
            }

            .welcome {
                padding: 24px;
            }

            .welcome h1 {
                font-size: 22px;
            }

            .content {
                padding: 20px 15px;
            }

            .topbar {
                height: 75px;
            }

            .topbar-subtitle {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- ========================================
         SIDEBAR
    ========================================= -->

    <aside class="sidebar">

        <div class="sidebar-header">

            <a
                href="{{ route('dashboard.customer-service.index') }}"
                class="brand"
            >

                <div class="brand-icon">
                    T
                </div>

                <div class="brand-text">

                    <span class="brand-title">
                        TravelKu
                    </span>

                    <span class="brand-subtitle">
                        Management System
                    </span>

                </div>

            </a>

        </div>


        <nav class="menu">

            <div class="menu-title">
                Menu Utama
            </div>


            <ul class="menu-list">

                <li class="menu-item">

                    <a
                        href="{{ route('dashboard.customer-service.index') }}"
                        class="menu-link active"
                    >

                        <span class="menu-icon">
                            ⌂
                        </span>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="{{ route('dashboard.customer-service.live-chat') }}"
                        class="quick-item"
                    >
                        <div class="quick-icon">
                            💬
                        </div>

                        <div class="quick-text">
                            <span class="quick-title">
                                Layanan Pelanggan
                            </span>

                            <span class="quick-desc">
                                Tangani pertanyaan pelanggan
                            </span>
                        </div>
                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ♙
                        </span>

                        <span>
                            Pelanggan
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ◷
                        </span>

                        <span>
                            Jadwal Travel
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            💬
                        </span>

                        <span>
                            Layanan Pelanggan
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ▤
                        </span>

                        <span>
                            Laporan
                        </span>

                    </a>

                </li>

            </ul>

        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-footer">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <span class="menu-icon">
                        ⇥
                    </span>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>


    <!-- ========================================
         MAIN CONTENT
    ========================================= -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">

            <div>

                <div class="topbar-title">
                    Dashboard Customer Service
                </div>

                <div class="topbar-subtitle">
                    Kelola pelayanan dan kebutuhan pelanggan TravelKu
                </div>

            </div>


            <div class="profile">

                <div class="profile-avatar">

                    {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}

                </div>


                <div class="profile-info">

                    <span class="profile-name">
                        {{ auth()->user()->name ?? 'Customer Service' }}
                    </span>

                    <span class="profile-role">
                        Customer Service
                    </span>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">


            <!-- WELCOME -->

            <div class="welcome">

                <div class="welcome-content">

                    <div class="welcome-label">
                        TravelKu
                    </div>

                    <h1>
                        Selamat datang,
                        {{ auth()->user()->name ?? 'Customer Service' }} 👋
                    </h1>

                    <p>
                        Bantu pelanggan mendapatkan pengalaman perjalanan
                        yang nyaman dan menyenangkan.
                    </p>

                </div>

            </div>


            <!-- STATISTICS -->

            <div class="stats">


                <div class="stat-card">

                    <div class="stat-info">

                        <span class="stat-title">
                            Pertanyaan Hari Ini
                        </span>

                        <span class="stat-value">
                            18
                        </span>

                    </div>

                    <div class="stat-icon">
                        💬
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span class="stat-title">
                            Pesanan Hari Ini
                        </span>

                        <span class="stat-value">
                            24
                        </span>

                    </div>

                    <div class="stat-icon">
                        ▣
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span class="stat-title">
                            Pelanggan Aktif
                        </span>

                        <span class="stat-value">
                            156
                        </span>

                    </div>

                    <div class="stat-icon">
                        ♙
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span class="stat-title">
                            Menunggu Respons
                        </span>

                        <span class="stat-value">
                            5
                        </span>

                    </div>

                    <div class="stat-icon">
                        ◷
                    </div>

                </div>

            </div>


            <!-- GRID -->

            <div class="dashboard-grid">


                <!-- MENU CEPAT -->

                <div class="card">

                    <div class="card-header">

                        <span class="card-title">
                            Menu Cepat
                        </span>

                        <a
                            href="#"
                            class="card-link"
                        >
                            Lihat Semua
                        </a>

                    </div>


                    <div class="card-body">

                        <div class="quick-menu">


                            <a
                                href="#"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    💬
                                </div>

                                <div class="quick-text">

                                    <span class="quick-title">
                                        Layanan Pelanggan
                                    </span>

                                    <span class="quick-desc">
                                        Tangani pertanyaan pelanggan
                                    </span>

                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    ▣
                                </div>

                                <div class="quick-text">

                                    <span class="quick-title">
                                        Pemesanan
                                    </span>

                                    <span class="quick-desc">
                                        Cek dan kelola pemesanan
                                    </span>

                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    ♙
                                </div>

                                <div class="quick-text">

                                    <span class="quick-title">
                                        Data Pelanggan
                                    </span>

                                    <span class="quick-desc">
                                        Lihat informasi pelanggan
                                    </span>

                                </div>

                            </a>


                            <a
                                href="#"
                                class="quick-item"
                            >

                                <div class="quick-icon">
                                    ◷
                                </div>

                                <div class="quick-text">

                                    <span class="quick-title">
                                        Jadwal Travel
                                    </span>

                                    <span class="quick-desc">
                                        Lihat jadwal perjalanan
                                    </span>

                                </div>

                            </a>

                        </div>

                    </div>

                </div>


                <!-- AKTIVITAS -->

                <div class="card">

                    <div class="card-header">

                        <span class="card-title">
                            Aktivitas Terbaru
                        </span>

                        <a
                            href="#"
                            class="card-link"
                        >
                            Semua
                        </a>

                    </div>


                    <div class="card-body">

                        <div class="activity-list">


                            <div class="activity-item">

                                <div class="activity-dot"></div>

                                <div class="activity-content">

                                    <div class="activity-text">
                                        Pertanyaan pelanggan berhasil ditangani
                                    </div>

                                    <div class="activity-time">
                                        5 menit yang lalu
                                    </div>

                                </div>

                            </div>


                            <div class="activity-item">

                                <div class="activity-dot"></div>

                                <div class="activity-content">

                                    <div class="activity-text">
                                        Pemesanan baru berhasil dibuat
                                    </div>

                                    <div class="activity-time">
                                        20 menit yang lalu
                                    </div>

                                </div>

                            </div>


                            <div class="activity-item">

                                <div class="activity-dot"></div>

                                <div class="activity-content">

                                    <div class="activity-text">
                                        Data pelanggan diperbarui
                                    </div>

                                    <div class="activity-time">
                                        1 jam yang lalu
                                    </div>

                                </div>

                            </div>


                            <div class="activity-item">

                                <div class="activity-dot"></div>

                                <div class="activity-content">

                                    <div class="activity-text">
                                        Jadwal perjalanan diperbarui
                                    </div>

                                    <div class="activity-time">
                                        2 jam yang lalu
                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</body>

</html>
