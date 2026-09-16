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
            background: rgba(255, 255, 255, 0.14);
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


            <a href="#">
                <div class="menu-icon">
                    🎫
                </div>

                <span>
                    Pemesanan
                </span>
            </a>


            <a href="#">
                <div class="menu-icon">
                    🚐
                </div>

                <span>
                    Jadwal Travel
                </span>
            </a>


            <a href="#">
                <div class="menu-icon">
                    👥
                </div>

                <span>
                    Pelanggan
                </span>
            </a>


            <a href="#">
                <div class="menu-icon">
                    💳
                </div>

                <span>
                    Pembayaran
                </span>
            </a>


            <a href="#">
                <div class="menu-icon">
                    📋
                </div>

                <span>
                    Laporan
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
                    Dashboard Staff
                </h1>

                <p>
                    Kelola operasional travel dengan mudah.
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
                        {{ auth()->user()->name ?? 'Staff' }}
                    </strong>

                    <span>
                        Admin Biasa / Staff
                    </span>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">


            <!-- WELCOME -->

            <div class="welcome">

                <div class="welcome-content">

                    <h2>
                        Selamat datang,
                        {{ auth()->user()->name ?? 'Staff' }} 👋
                    </h2>

                    <p>
                        Pantau aktivitas dan kelola operasional
                        travel melalui dashboard staff.
                    </p>

                </div>

            </div>


            <!-- STATISTICS -->

            <div class="stats-grid">


                <div class="stat-card">

                    <div class="stat-info">

                        <span>
                            Pemesanan Hari Ini
                        </span>

                        <h3>
                            24
                        </h3>

                    </div>

                    <div class="stat-icon blue">
                        🎫
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span>
                            Jadwal Aktif
                        </span>

                        <h3>
                            12
                        </h3>

                    </div>

                    <div class="stat-icon cyan">
                        🚐
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span>
                            Pelanggan
                        </span>

                        <h3>
                            156
                        </h3>

                    </div>

                    <div class="stat-icon green">
                        👥
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-info">

                        <span>
                            Menunggu Pembayaran
                        </span>

                        <h3>
                            8
                        </h3>

                    </div>

                    <div class="stat-icon orange">
                        💳
                    </div>

                </div>

            </div>


            <!-- LOWER CONTENT -->

            <div class="dashboard-grid">


                <!-- QUICK MENU -->

                <div class="card">

                    <div class="card-header">

                        <h3>
                            Akses Cepat
                        </h3>

                    </div>


                    <div class="quick-menu">


                        <a
                            href="#"
                            class="quick-item"
                        >

                            <div class="quick-icon">
                                🎫
                            </div>

                            <strong>
                                Kelola Pemesanan
                            </strong>

                            <span>
                                Lihat dan kelola pesanan pelanggan
                            </span>

                        </a>


                        <a
                            href="#"
                            class="quick-item"
                        >

                            <div class="quick-icon">
                                🚐
                            </div>

                            <strong>
                                Jadwal Travel
                            </strong>

                            <span>
                                Kelola jadwal keberangkatan
                            </span>

                        </a>


                        <a
                            href="#"
                            class="quick-item"
                        >

                            <div class="quick-icon">
                                👥
                            </div>

                            <strong>
                                Data Pelanggan
                            </strong>

                            <span>
                                Lihat data pelanggan travel
                            </span>

                        </a>


                        <a
                            href="#"
                            class="quick-item"
                        >

                            <div class="quick-icon">
                                📋
                            </div>

                            <strong>
                                Laporan
                            </strong>

                            <span>
                                Lihat laporan operasional
                            </span>

                        </a>

                    </div>

                </div>


                <!-- AKTIVITAS -->

                <div class="card">

                    <div class="card-header">

                        <h3>
                            Aktivitas Terbaru
                        </h3>

                        <a href="#">
                            Lihat semua
                        </a>

                    </div>


                    <div class="activity">


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <strong>
                                    Pesanan baru diterima
                                </strong>

                                <span>
                                    5 menit yang lalu
                                </span>

                            </div>

                        </div>


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <strong>
                                    Pembayaran dikonfirmasi
                                </strong>

                                <span>
                                    15 menit yang lalu
                                </span>

                            </div>

                        </div>


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <strong>
                                    Jadwal travel diperbarui
                                </strong>

                                <span>
                                    1 jam yang lalu
                                </span>

                            </div>

                        </div>


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <strong>
                                    Data pelanggan ditambahkan
                                </strong>

                                <span>
                                    2 jam yang lalu
                                </span>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>
