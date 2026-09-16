<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin Utama - Travel</title>

    <style>
        /* =========================================================
           RESET
        ========================================================== */

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

            --warning: #d97706;
            --warning-soft: #fff7ed;

            --blue: #2563eb;
            --blue-soft: #eff6ff;

            --purple: #7c3aed;
            --purple-soft: #f5f3ff;
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

        button,
        a {
            font-family: inherit;
        }


        /* =========================================================
           DASHBOARD
        ========================================================== */

        .dashboard {
            min-height: 100vh;
        }


        /* =========================================================
           SIDEBAR
        ========================================================== */

        .sidebar {
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


        /* =========================================================
           LOGO
        ========================================================== */

        .logo {
            height: 82px;

            display: flex;
            align-items: center;

            padding: 0 24px;

            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .logo-icon {
            width: 43px;
            height: 43px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;

            background: rgba(255, 255, 255, 0.15);

            font-size: 21px;

            margin-right: 12px;
        }

        .logo-text {
            font-size: 21px;
            font-weight: 700;
            line-height: 1.2;
        }

        .logo-subtitle {
            display: block;

            margin-top: 3px;

            color: rgba(255, 255, 255, 0.60);

            font-size: 10px;
            letter-spacing: 0.2px;
        }


        /* =========================================================
           MENU
        ========================================================== */

        .menu {
            flex: 1;

            padding: 24px 14px;

            overflow-y: auto;
        }

        .menu::-webkit-scrollbar {
            width: 4px;
        }

        .menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
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

        .menu-section {
            margin-top: 25px;
        }

        .menu a {
            display: flex;
            align-items: center;

            gap: 12px;

            min-height: 45px;

            padding: 10px 13px;

            margin-bottom: 4px;

            border-radius: 11px;

            color: rgba(255, 255, 255, 0.72);

            text-decoration: none;

            font-size: 13px;
            font-weight: 500;

            transition:
                background 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.09);
            color: white;

            transform: translateX(2px);
        }

        .menu a.active {
            background: white;
            color: var(--brand);

            box-shadow:
                0 7px 18px rgba(0, 0, 0, 0.10);
        }

        .menu-icon {
            width: 23px;
            min-width: 23px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }


        /* =========================================================
           LOGOUT
        ========================================================== */

        .logout-area {
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

        .logout-button:hover {
            background: rgba(220, 53, 69, 0.95);
            border-color: rgba(220, 53, 69, 0.95);

            color: white;
        }


        /* =========================================================
           MAIN
        ========================================================== */

        .main {
            width: calc(100% - 260px);

            min-height: 100vh;

            margin-left: 260px;
        }


        /* =========================================================
           HEADER
        ========================================================== */

        .header {
            height: 82px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 35px;

            background: var(--surface);

            border-bottom: 1px solid var(--border);
        }

        .header-title h2 {
            font-size: 18px;
            font-weight: 700;

            color: var(--text);
        }

        .header-title p {
            margin-top: 4px;

            color: var(--text-muted);

            font-size: 12px;
        }


        /* =========================================================
           PROFILE
        ========================================================== */

        .profile {
            display: flex;
            align-items: center;

            gap: 11px;
        }

        .profile-info {
            text-align: right;
        }

        .profile-name {
            font-size: 13px;
            font-weight: 700;

            color: var(--text);
        }

        .profile-role {
            margin-top: 2px;

            color: var(--text-muted);

            font-size: 11px;
        }

        .profile-avatar {
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


        /* =========================================================
           CONTENT
        ========================================================== */

        .content {
            padding: 32px 35px 40px;
        }


        /* =========================================================
           WELCOME
        ========================================================== */

        .welcome {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            margin-bottom: 27px;
        }

        .welcome h1 {
            font-size: 27px;
            line-height: 1.25;

            letter-spacing: -0.5px;

            color: var(--text);
        }

        .welcome p {
            margin-top: 7px;

            color: var(--text-secondary);

            font-size: 13px;
        }

        .welcome-date {
            display: flex;
            align-items: center;
            gap: 8px;

            padding: 9px 13px;

            border: 1px solid var(--border);
            border-radius: 10px;

            background: white;

            color: var(--text-secondary);

            font-size: 11px;
        }


        /* =========================================================
           STATISTICS
        ========================================================== */

        .stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 17px;

            margin-bottom: 32px;
        }

        .stat-card {
            position: relative;

            min-height: 135px;

            padding: 20px;

            overflow: hidden;

            background: white;

            border: 1px solid var(--border);
            border-radius: 16px;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .stat-card::after {
            content: "";

            position: absolute;

            width: 90px;
            height: 90px;

            right: -35px;
            bottom: -40px;

            border-radius: 50%;

            background: var(--brand-light);

            opacity: 0.55;
        }

        .stat-card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 12px 28px rgba(22, 34, 29, 0.07);
        }

        .stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .stat-label {
            margin-bottom: 8px;

            color: var(--text-secondary);

            font-size: 12px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 29px;
            line-height: 1;

            color: var(--text);

            font-weight: 700;
        }

        .stat-icon {
            width: 43px;
            height: 43px;

            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            font-size: 18px;
        }

        .stat-footer {
            position: absolute;

            left: 20px;
            bottom: 17px;

            color: var(--text-muted);

            font-size: 10px;
        }

        .icon-green {
            background: var(--brand-light);
        }

        .icon-blue {
            background: var(--blue-soft);
        }

        .icon-orange {
            background: var(--warning-soft);
        }

        .icon-purple {
            background: var(--purple-soft);
        }


        /* =========================================================
           SECTION
        ========================================================== */

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 15px;
        }

        .section-header h2 {
            font-size: 17px;
            font-weight: 700;

            color: var(--text);
        }

        .section-header p {
            margin-top: 3px;

            color: var(--text-muted);

            font-size: 11px;
        }


        /* =========================================================
           QUICK MENU
        ========================================================== */

        .quick-menu {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 17px;

            margin-bottom: 30px;
        }

        .quick-card {
            position: relative;

            display: block;

            min-height: 155px;

            padding: 20px;

            background: white;

            border: 1px solid var(--border);
            border-radius: 16px;

            color: inherit;
            text-decoration: none;

            transition:
                transform 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .quick-card:hover {
            transform: translateY(-3px);

            border-color: #b8dcca;

            box-shadow:
                0 12px 28px rgba(22, 34, 29, 0.07);
        }

        .quick-icon {
            width: 43px;
            height: 43px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 16px;

            border-radius: 12px;

            background: var(--brand-light);

            font-size: 18px;
        }

        .quick-card h3 {
            margin-bottom: 6px;

            color: var(--text);

            font-size: 14px;
            font-weight: 700;
        }

        .quick-card p {
            max-width: 210px;

            color: var(--text-secondary);

            font-size: 11px;
            line-height: 1.6;
        }

        .quick-arrow {
            position: absolute;

            right: 18px;
            bottom: 17px;

            color: var(--brand);

            font-size: 16px;

            transition: transform 0.2s ease;
        }

        .quick-card:hover .quick-arrow {
            transform: translateX(3px);
        }


        /* =========================================================
           INFORMATION
        ========================================================== */

        .info-box {
            display: flex;
            align-items: flex-start;

            gap: 14px;

            padding: 19px 20px;

            border: 1px solid #cdeadd;
            border-radius: 15px;

            background: var(--brand-light);
        }

        .info-icon {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 11px;

            background: white;

            font-size: 17px;
        }

        .info-box h3 {
            margin-bottom: 4px;

            color: var(--brand-dark);

            font-size: 13px;
        }

        .info-box p {
            color: #34775e;

            font-size: 11px;
            line-height: 1.6;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            .stats,
            .quick-menu {
                grid-template-columns: repeat(2, 1fr);
            }

            .register-left {
                padding: 35px;
            }

        }


        @media (max-width: 900px) {

            .sidebar {
                width: 76px;
            }

            .logo {
                justify-content: center;
                padding: 0;
            }

            .logo-icon {
                margin: 0;
            }

            .logo-text,
            .logo-subtitle,
            .menu-title,
            .menu a span:not(.menu-icon),
            .logout-button span:not(.menu-icon) {
                display: none;
            }

            .menu {
                padding: 20px 10px;
            }

            .menu a {
                justify-content: center;
                padding: 12px;
            }

            .menu-icon {
                width: auto;
            }

            .logout-area {
                padding: 10px;
            }

            .logout-button {
                justify-content: center;
                padding: 12px;
            }

            .main {
                width: calc(100% - 76px);
                margin-left: 76px;
            }

            .header {
                padding: 0 22px;
            }

            .content {
                padding: 25px 22px 35px;
            }

            .welcome-date {
                display: none;
            }

        }


        @media (max-width: 600px) {

            .header {
                height: 70px;
            }

            .header-title h2 {
                font-size: 15px;
            }

            .header-title p {
                display: none;
            }

            .profile-info {
                display: none;
            }

            .profile-avatar {
                width: 39px;
                height: 39px;
            }

            .content {
                padding: 22px 16px 30px;
            }

            .welcome {
                margin-bottom: 22px;
            }

            .welcome h1 {
                font-size: 22px;
            }

            .welcome p {
                font-size: 12px;
                line-height: 1.6;
            }

            .stats,
            .quick-menu {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .stat-card {
                min-height: 125px;
            }

            .quick-card {
                min-height: 140px;
            }

            .info-box {
                padding: 16px;
            }

        }


        @media (max-width: 420px) {

            .sidebar {
                width: 62px;
            }

            .main {
                width: calc(100% - 62px);
                margin-left: 62px;
            }

            .menu {
                padding-left: 7px;
                padding-right: 7px;
            }

            .menu a {
                padding: 11px 8px;
            }

            .content {
                padding-left: 13px;
                padding-right: 13px;
            }

        }

    </style>
</head>


<body>

<div class="dashboard">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

    <aside class="sidebar">

        {{-- LOGO --}}
        <div class="logo">

            <div class="logo-icon">
                🚐
            </div>

            <div>
                <div class="logo-text">
                    Travel
                </div>

                <span class="logo-subtitle">
                    Management System
                </span>
            </div>

        </div>


        {{-- MENU --}}
        <nav class="menu">

            <div class="menu-title">
                Menu Utama
            </div>


            {{-- Dashboard --}}
            <a
                href="{{ url('/dashboard') }}"
                class="active"
            >
                <span class="menu-icon">
                    📊
                </span>

                <span>
                    Dashboard
                </span>
            </a>


            {{-- Management --}}
            <div class="menu-section">

                <div class="menu-title">
                    Manajemen
                </div>


                <a href="#">
                    <span class="menu-icon">
                        👤
                    </span>

                    <span>
                        Kelola User
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        🧑‍✈️
                    </span>

                    <span>
                        Kelola Driver
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        👥
                    </span>

                    <span>
                        Customer Service
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        🚐
                    </span>

                    <span>
                        Kendaraan
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        📍
                    </span>

                    <span>
                        Rute
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        🗓️
                    </span>

                    <span>
                        Jadwal
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        🎫
                    </span>

                    <span>
                        Pemesanan
                    </span>
                </a>


                <a href="#">
                    <span class="menu-icon">
                        📈
                    </span>

                    <span>
                        Laporan
                    </span>
                </a>

            </div>

        </nav>


        {{-- LOGOUT --}}
        <div class="logout-area">

            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <span class="menu-icon">
                        🚪
                    </span>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>



    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main class="main">


        {{-- HEADER --}}
        <header class="header">

            <div class="header-title">

                <h2>
                    Dashboard Admin Utama
                </h2>

                <p>
                    Panel administrasi sistem travel
                </p>

            </div>


            {{-- PROFILE --}}
            <div class="profile">

                <div class="profile-info">

                    <div class="profile-name">
                        {{ auth()->user()->name ?? 'Admin Utama' }}
                    </div>

                    <div class="profile-role">
                        Administrator
                    </div>

                </div>


                <div class="profile-avatar">

                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}

                </div>

            </div>

        </header>



        {{-- =====================================================
             CONTENT
        ====================================================== --}}

        <section class="content">


            {{-- WELCOME --}}
            <div class="welcome">

                <div>

                    <h1>
                        Selamat Datang,
                        {{ auth()->user()->name ?? 'Admin Utama' }}
                        👋
                    </h1>

                    <p>
                        Kelola seluruh aktivitas operasional travel melalui dashboard ini.
                    </p>

                </div>


                <div class="welcome-date">
                    📅
                    <span>
                        {{ date('d F Y') }}
                    </span>
                </div>

            </div>



            {{-- =================================================
                 STATISTICS
            ================================================== --}}

            <div class="stats">


                {{-- USER --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>

                            <div class="stat-label">
                                Total User
                            </div>

                            <div class="stat-value">
                                {{ $totalUsers ?? 0 }}
                            </div>

                        </div>

                        <div class="stat-icon icon-blue">
                            👤
                        </div>

                    </div>

                    <div class="stat-footer">
                        Pengguna terdaftar
                    </div>

                </div>


                {{-- DRIVER --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>

                            <div class="stat-label">
                                Total Driver
                            </div>

                            <div class="stat-value">
                                {{ $totalDrivers ?? 0 }}
                            </div>

                        </div>

                        <div class="stat-icon icon-green">
                            🧑‍✈️
                        </div>

                    </div>

                    <div class="stat-footer">
                        Driver terdaftar
                    </div>

                </div>


                {{-- VEHICLE --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>

                            <div class="stat-label">
                                Kendaraan
                            </div>

                            <div class="stat-value">
                                {{ $totalVehicles ?? 0 }}
                            </div>

                        </div>

                        <div class="stat-icon icon-orange">
                            🚐
                        </div>

                    </div>

                    <div class="stat-footer">
                        Armada tersedia
                    </div>

                </div>


                {{-- BOOKING --}}
                <div class="stat-card">

                    <div class="stat-top">

                        <div>

                            <div class="stat-label">
                                Total Booking
                            </div>

                            <div class="stat-value">
                                {{ $totalBookings ?? 0 }}
                            </div>

                        </div>

                        <div class="stat-icon icon-purple">
                            🎫
                        </div>

                    </div>

                    <div class="stat-footer">
                        Pemesanan perjalanan
                    </div>

                </div>

            </div>



            {{-- =================================================
                 QUICK MENU
            ================================================== --}}

            <div class="section-header">

                <div>

                    <h2>
                        Menu Cepat
                    </h2>

                    <p>
                        Akses fitur administrasi utama
                    </p>

                </div>

            </div>


            <div class="quick-menu">


                {{-- USER --}}
                <a
                    href="#"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        👤
                    </div>

                    <h3>
                        Kelola User
                    </h3>

                    <p>
                        Kelola akun pengguna sistem travel.
                    </p>

                    <span class="quick-arrow">
                        →
                    </span>

                </a>


                {{-- DRIVER --}}
                <a
                    href="#"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        🧑‍✈️
                    </div>

                    <h3>
                        Kelola Driver
                    </h3>

                    <p>
                        Kelola data driver travel.
                    </p>

                    <span class="quick-arrow">
                        →
                    </span>

                </a>


                {{-- VEHICLE --}}
                <a
                    href="#"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        🚐
                    </div>

                    <h3>
                        Kendaraan
                    </h3>

                    <p>
                        Kelola armada kendaraan travel.
                    </p>

                    <span class="quick-arrow">
                        →
                    </span>

                </a>


                {{-- SCHEDULE --}}
                <a
                    href="#"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        🗓️
                    </div>

                    <h3>
                        Jadwal Travel
                    </h3>

                    <p>
                        Kelola jadwal keberangkatan travel.
                    </p>

                    <span class="quick-arrow">
                        →
                    </span>

                </a>

            </div>



            {{-- =================================================
                 INFORMATION
            ================================================== --}}

            <div class="info-box">

                <div class="info-icon">
                    ℹ️
                </div>

                <div>

                    <h3>
                        Informasi Dashboard
                    </h3>

                    <p>
                        Dashboard Admin Utama digunakan untuk mengelola
                        seluruh data dan aktivitas aplikasi travel.
                    </p>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>