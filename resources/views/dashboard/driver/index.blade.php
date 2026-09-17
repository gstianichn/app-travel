<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Driver - Travel</title>

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
            --danger: #ff0000;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: 260px;
            background: var(--primary);
            border-right: 1px solid #e5e7eb;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand {
            padding: 25px 22px;
            border-bottom: 1px solid var(--primary-dark);
        }

        .brand h2 {
            color: white;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .brand p {
            color: white;
            font-size: 12px;
        }

        .driver-profile {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--primary-dark);
        }

        .profile-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .profile-info h4 {
            color: white;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .profile-info span {
            font-size: 11px;
            color: greenyellow;
            font-weight: 600;
        }

        .menu {
            padding: 18px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .menu-title {
            font-size: 10px;
            color: white;
            font-weight: 800;
            padding: 0 12px 10px;
            text-transform: uppercase;
            letter-spacing: .7px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 13px;
            margin-bottom: 4px;
            text-decoration: none;
            color: white;
            border-radius: 9px;
            font-size: 13px;
            transition: .2s;
        }

        .menu a:hover,
        .menu a.active {
            background: var(--primary-dark);
            color: greenyellow);
            font-weight: 600;
        }

        .menu-icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .logout {
            padding: 15px;
            border-top: 1px solid #eef0f4;
        }

        .logout button {
            width: 100%;
            border: none;
            background: #fff1f2;
            color: var(--danger);
            padding: 11px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        /* ================= MAIN ================= */

        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 25px 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 25px;
            margin-bottom: 5px;
        }

        .topbar p {
            color: #6b7280;
            font-size: 13px;
        }

        .date-box {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 10px 16px;
            border-radius: 9px;
            font-size: 13px;
            color: #4b5563;
        }

        /* ================= WELCOME ================= */

        .welcome {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 25px rgba(37, 99, 235, .15);
        }

        .welcome h2 {
            font-size: 21px;
            margin-bottom: 7px;
        }

        .welcome p {
            font-size: 13px;
            opacity: .9;
        }

        .status-online {
            background: rgba(255,255,255,.15);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
        }

        .status-online::before {
            content: "";
            width: 8px;
            height: 8px;
            display: inline-block;
            border-radius: 50%;
            background: #4ade80;
            margin-right: 7px;
        }

        /* ================= STAT ================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: #eff6ff;
        }

        .stat-card h3 {
            font-size: 22px;
            margin-bottom: 3px;
        }

        .stat-card p {
            font-size: 11px;
            color: #9ca3af;
        }

        /* ================= GRID ================= */

        .content-grid {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 17px 20px;
            border-bottom: 1px solid #eef0f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 15px;
        }

        .card-header a {
            color: var(--primary);
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        /* ================= TRIP ================= */

        .trip-info {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
        }

        .location small {
            color: #9ca3af;
            font-size: 10px;
            display: block;
            margin-bottom: 4px;
        }

        .location strong {
            font-size: 16px;
        }

        .route-line {
            text-align: center;
            color: var(--primary);
        }

        .route-line div {
            height: 2px;
            width: 80px;
            background: #bfdbfe;
            margin: 5px auto;
            position: relative;
        }

        .route-line div::after {
            content: "→";
            position: absolute;
            right: -5px;
            top: -9px;
            color: var(--primary);
        }

        .trip-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .meta-box {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
        }

        .meta-box span {
            display: block;
            color: #9ca3af;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .meta-box strong {
            font-size: 12px;
        }

        /* ================= STATUS ================= */

        .status-box {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 20px;
            background: #fef3c7;
            color: #b45309;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .status-select {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            margin-bottom: 10px;
        }

        .btn {
            width: 100%;
            border: none;
            padding: 11px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        /* ================= VEHICLE ================= */

        .vehicle {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .vehicle-icon {
            width: 60px;
            height: 60px;
            background: #eff6ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .vehicle-info h4 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .vehicle-info p {
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .vehicle-status {
            color: #059669;
            font-size: 11px;
            font-weight: 600;
        }

        /* ================= PASSENGER ================= */

        .passenger-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .passenger {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .passenger-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .passenger-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #dbeafe;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .passenger h4 {
            font-size: 12px;
        }

        .passenger p {
            color: #9ca3af;
            font-size: 10px;
        }

        .seat {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
        }

        /* ================= QUICK MENU ================= */

        .quick-menu {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .quick-item {
            text-decoration: none;
            background: white;
            border: 1px solid #e5e7eb;
            padding: 17px 10px;
            border-radius: 10px;
            text-align: center;
            color: #374151;
            transition: .2s;
        }

        .quick-item:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
        }

        .quick-item .icon {
            font-size: 23px;
            margin-bottom: 8px;
        }

        .quick-item span {
            display: block;
            font-size: 11px;
            font-weight: 600;
        }

        /* ================= PERFORMANCE ================= */

        .performance {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .performance-box {
            padding: 15px;
            background: #f8fafc;
            border-radius: 9px;
        }

        .performance-box span {
            color: #9ca3af;
            font-size: 10px;
            display: block;
            margin-bottom: 7px;
        }

        .performance-box strong {
            font-size: 20px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 1100px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .quick-menu {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .brand h2,
            .brand p,
            .profile-info,
            .menu-title,
            .menu a span,
            .logout button span {
                display: none;
            }

            .brand {
                text-align: center;
                padding: 20px 5px;
            }

            .driver-profile {
                justify-content: center;
                padding: 15px 5px;
            }

            .menu a {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 18px;
            }

            .welcome {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .trip-info {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .route-line div {
                width: 100px;
            }

            .trip-meta,
            .performance {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <h2>TRAVEL</h2>
            <p>Driver Management</p>
        </div>

        <div class="driver-profile">
            <div class="profile-avatar">
                DR
            </div>

            <div class="profile-info">
                <h4>{{ auth()->user()->name ?? 'Driver' }}</h4>
                <span>● ONLINE</span>
            </div>
        </div>

        <nav class="menu">

            <div class="menu-title">
                Menu Driver
            </div>

            <a href="#" class="active">
                <span class="menu-icon">⌂</span>
                <span>Dashboard</span>
            </a>

            <a href="#">
                <span class="menu-icon">📅</span>
                <span>Jadwal Perjalanan</span>
            </a>

            <a href="#">
                <span class="menu-icon">🚐</span>
                <span>Kendaraan Saya</span>
            </a>

            <a href="#">
                <span class="menu-icon">👥</span>
                <span>Daftar Penumpang</span>
            </a>

            <a href="#">
                <span class="menu-icon">💺</span>
                <span>Seat Map</span>
            </a>

            <a href="#">
                <span class="menu-icon">🗺️</span>
                <span>Rute Perjalanan</span>
            </a>

            <a href="#">
                <span class="menu-icon">📍</span>
                <span>GPS Tracking</span>
            </a>

            <a href="#">
                <span class="menu-icon">⚠️</span>
                <span>Laporan Masalah</span>
            </a>

            <a href="#">
                <span class="menu-icon">🚨</span>
                <span>Emergency Center</span>
            </a>

            <div class="menu-title" style="margin-top: 15px;">
                Evaluasi
            </div>

            <a href="#">
                <span class="menu-icon">📊</span>
                <span>Performa</span>
            </a>

            <a href="#">
                <span class="menu-icon">🏆</span>
                <span>Reward</span>
            </a>

            <a href="#">
                <span class="menu-icon">🕘</span>
                <span>Riwayat Perjalanan</span>
            </a>

        </nav>

        <div class="logout">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit">
                    <span>↪ &nbsp; Logout</span>
                    <span style="display:none;">↪</span>
                </button>
            </form>

        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">

            <div>
                <h1>Dashboard Driver</h1>
                <p>Kelola perjalanan dan aktivitas Anda hari ini.</p>
            </div>

            <div class="date-box">
                📅 {{ date('d F Y') }}
            </div>

        </div>


        <!-- WELCOME -->
        <section class="welcome">

            <div>
                <h2>Selamat datang, {{ auth()->user()->name ?? 'Driver' }} 👋</h2>

                <p>
                    Pastikan perjalanan berjalan aman,
                    nyaman, dan tepat waktu.
                </p>
            </div>

            <div class="status-online">
                Status Driver Aktif
            </div>

        </section>


        <!-- STATISTICS -->
        <section class="stats">

            <div class="stat-card">

                <div class="stat-icon">
                    📅
                </div>

                <div>
                    <h3>2</h3>
                    <p>Jadwal Hari Ini</p>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    👥
                </div>

                <div>
                    <h3>18</h3>
                    <p>Total Penumpang</p>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🚐
                </div>

                <div>
                    <h3>1</h3>
                    <p>Kendaraan Ditugaskan</p>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    ⭐
                </div>

                <div>
                    <h3>4.9</h3>
                    <p>Rating Performa</p>
                </div>

            </div>

        </section>


        <!-- MAIN GRID -->
        <section class="content-grid">

            <!-- PERJALANAN -->
            <div class="card">

                <div class="card-header">

                    <h3>Perjalanan Berikutnya</h3>

                    <a href="#">
                        Lihat Semua
                    </a>

                </div>

                <div class="card-body">

                    <div class="trip-info">

                        <div class="location">
                            <small>ASAL</small>
                            <strong>Bandung</strong>
                        </div>

                        <div class="route-line">
                            🚌
                            <div></div>
                        </div>

                        <div class="location">
                            <small>TUJUAN</small>
                            <strong>Jakarta</strong>
                        </div>

                    </div>


                    <div class="trip-meta">

                        <div class="meta-box">
                            <span>Jam Berangkat</span>
                            <strong>08:00 WIB</strong>
                        </div>

                        <div class="meta-box">
                            <span>Kendaraan</span>
                            <strong>BM 1234 XX</strong>
                        </div>

                        <div class="meta-box">
                            <span>Penumpang</span>
                            <strong>12 Orang</strong>
                        </div>

                    </div>

                </div>

            </div>


            <!-- STATUS -->
            <div class="card">

                <div class="card-header">

                    <h3>Status Perjalanan</h3>

                </div>

                <div class="card-body status-box">

                    <div class="status-badge">
                        BELUM BERANGKAT
                    </div>

                    <select class="status-select">
                        <option>Belum Berangkat</option>
                        <option>Persiapan</option>
                        <option>Dalam Perjalanan</option>
                        <option>Tiba di Tujuan</option>
                        <option>Perjalanan Selesai</option>
                        <option>Perjalanan Bermasalah</option>
                    </select>

                    <button class="btn btn-primary">
                        Perbarui Status
                    </button>

                </div>

            </div>

        </section>


        <!-- VEHICLE + PASSENGER -->
        <section class="content-grid">

            <!-- VEHICLE -->
            <div class="card">

                <div class="card-header">

                    <h3>Kendaraan yang Ditugaskan</h3>

                    <a href="#">
                        Detail
                    </a>

                </div>

                <div class="card-body">

                    <div class="vehicle">

                        <div class="vehicle-icon">
                            🚐
                        </div>

                        <div class="vehicle-info">

                            <h4>Toyota Hiace</h4>

                            <p>
                                Nomor Polisi: <strong>BM 1234 XX</strong>
                            </p>

                            <p>
                                Kapasitas: <strong>14 Seat</strong>
                            </p>

                            <span class="vehicle-status">
                                ● Kendaraan Siap Digunakan
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PASSENGER -->
            <div class="card">

                <div class="card-header">

                    <h3>Daftar Penumpang</h3>

                    <a href="#">
                        Lihat Semua
                    </a>

                </div>

                <div class="card-body">

                    <div class="passenger-list">

                        <div class="passenger">

                            <div class="passenger-left">

                                <div class="passenger-avatar">
                                    AS
                                </div>

                                <div>
                                    <h4>Andi Saputra</h4>
                                    <p>Jakarta</p>
                                </div>

                            </div>

                            <span class="seat">
                                Seat 01
                            </span>

                        </div>


                        <div class="passenger">

                            <div class="passenger-left">

                                <div class="passenger-avatar">
                                    DP
                                </div>

                                <div>
                                    <h4>Dina Putri</h4>
                                    <p>Jakarta</p>
                                </div>

                            </div>

                            <span class="seat">
                                Seat 02
                            </span>

                        </div>


                        <div class="passenger">

                            <div class="passenger-left">

                                <div class="passenger-avatar">
                                    RF
                                </div>

                                <div>
                                    <h4>Rizky Fajar</h4>
                                    <p>Jakarta</p>
                                </div>

                            </div>

                            <span class="seat">
                                Seat 03
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- QUICK ACCESS -->
        <div class="card" style="margin-bottom:20px;">

            <div class="card-header">
                <h3>Akses Cepat</h3>
            </div>

            <div class="card-body">

                <div class="quick-menu">

                    <a href="#" class="quick-item">
                        <div class="icon">💺</div>
                        <span>Seat Map</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">🗺️</div>
                        <span>Rute Perjalanan</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">📍</div>
                        <span>GPS Tracking</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">👤</div>
                        <span>Informasi Penumpang</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">⚠️</div>
                        <span>Laporkan Masalah</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">🚨</div>
                        <span>Emergency Center</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">🏆</div>
                        <span>Reward</span>
                    </a>

                    <a href="#" class="quick-item">
                        <div class="icon">🕘</div>
                        <span>Riwayat Perjalanan</span>
                    </a>

                </div>

            </div>

        </div>


        <!-- PERFORMANCE -->
        <div class="card">

            <div class="card-header">

                <h3>Performa Driver</h3>

                <a href="#">
                    Lihat Detail
                </a>

            </div>

            <div class="card-body">

                <div class="performance">

                    <div class="performance-box">

                        <span>Rating Penumpang</span>

                        <strong>
                            ⭐ 4.9
                        </strong>

                    </div>


                    <div class="performance-box">

                        <span>Perjalanan Selesai</span>

                        <strong>
                            128
                        </strong>

                    </div>


                    <div class="performance-box">

                        <span>Reward Points</span>

                        <strong>
                            2.450
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>