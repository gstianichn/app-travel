<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TravelKu - Beranda</title>

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
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f6f8fc;
            color: #1f2937;
        }

        /* =========================
           NAVBAR
        ========================== */

        .navbar {
            height: 72px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-menu a:hover {
            color: var(--primary);
        }

        .notification {
            position: relative;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f3f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #374151;
        }

        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            min-width: 17px;
            height: 17px;
            padding: 0 4px;
            border-radius: 20px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 18px;
            border-left: 1px solid #e5e7eb;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dbeafe;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
        }

        .user-label {
            font-size: 11px;
            color: #9ca3af;
        }

        /* =========================
           HERO
        ========================== */

        .hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 65px 7% 115px;
            color: white;
        }

        .hero-content {
            max-width: 1200px;
            margin: auto;
        }

        .hero h1 {
            font-size: 38px;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .hero p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        /* =========================
           SEARCH BOX
        ========================== */

        .search-card {
            max-width: 1200px;
            margin: -65px auto 0;
            position: relative;
            z-index: 10;
            background: white;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
        }

        .search-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 14px;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            background: #fff;
            color: #374151;
            outline: none;
            font-size: 13px;
        }

        .form-control:focus {
            border-color: var(--primary-dark);
        }

        .search-btn {
            height: 48px;
            padding: 0 23px;
            border: none;
            border-radius: 10px;
            background: var(--primary);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .search-btn:hover {
            background: var(--primary-dark);
        }

        /* =========================
           MAIN
        ========================== */

        .main-container {
            max-width: 1200px;
            margin: 45px auto;
        }

        .section {
            margin-bottom: 45px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 21px;
            font-weight: 700;
            color: #111827;
        }

        .section-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        /* =========================
           QUICK MENU
        ========================== */

        .quick-menu {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
        }

        .quick-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 20px 12px;
            text-align: center;
            text-decoration: none;
            color: #374151;
            transition: 0.2s;
        }

        .quick-card:hover {
            transform: translateY(-3px);
            border-color: #bfdbfe;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.08);
        }

        .quick-icon {
            width: 44px;
            height: 44px;
            margin: auto auto 10px;
            border-radius: 12px;
            background: #eff6ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .quick-card span {
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           PROMO
        ========================== */

        .promo-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .promo-card {
            border-radius: 16px;
            padding: 24px;
            min-height: 150px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1px solid #bfdbfe;
            position: relative;
            overflow: hidden;
        }

        .promo-card:nth-child(2) {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-color: #bbf7d0;
        }

        .promo-card:nth-child(3) {
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border-color: #fed7aa;
        }

        .promo-card h3 {
            font-size: 17px;
            margin-bottom: 7px;
        }

        .promo-card p {
            font-size: 12px;
            color: #6b7280;
            max-width: 220px;
        }

        .promo-code {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 10px;
            border-radius: 6px;
            background: white;
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
        }

        /* =========================
           BOOKING
        ========================== */

        .booking-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 16px;
            padding: 23px;
        }

        .booking-empty {
            text-align: center;
            padding: 25px;
            color: #9ca3af;
            font-size: 13px;
        }

        .booking-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           FEATURES
        ========================== */

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .feature-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 20px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eff6ff;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 13px;
        }

        .feature-card h3 {
            font-size: 14px;
            margin-bottom: 7px;
        }

        .feature-card p {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* =========================
           FOOTER
        ========================== */

        footer {
            margin-top: 60px;
            background: var(--primary-dark);
            color: white;
            padding: 40px 7%;
        }

        .footer-container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            font-size: 18px;
            font-weight: 700;
        }

        .footer-text {
            color: #9ca3af;
            font-size: 12px;
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 1000px) {
            .nav-menu {
                display: none;
            }

            .search-grid {
                grid-template-columns: 1fr 1fr;
            }

            .search-btn {
                width: 100%;
            }

            .quick-menu {
                grid-template-columns: repeat(3, 1fr);
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .navbar {
                padding: 0 5%;
            }

            .user-info {
                display: none;
            }

            .hero {
                padding: 45px 5% 100px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .main-container {
                margin: 35px 5%;
            }

            .search-card {
                margin-left: 5%;
                margin-right: 5%;
                padding: 18px;
            }

            .search-grid {
                grid-template-columns: 1fr;
            }

            .quick-menu {
                grid-template-columns: repeat(2, 1fr);
            }

            .promo-grid {
                grid-template-columns: 1fr;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .footer-container {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    {{-- =========================
         NAVBAR
    ========================== --}}

    <nav class="navbar">

        <a href="{{ route('dashboard.user.index') }}" class="logo">
            <div class="logo-icon">✈</div>
            TravelKu
        </a>

        <div class="nav-menu">

            <a href="{{ route('dashboard.user.index') }}">
                Beranda
            </a>

            <a href="{{ route('dashboard.user.user-booking') }}">
                Booking Saya
            </a>

            <a href="{{ route('dashboard.user.user-history') }}">
                Riwayat
            </a>

            <a href="{{ route('dashboard.user.user-refund') }}">
                Refund
            </a>

            <a href="{{ route('dashboard.user.user-promo') }}">
                Promo
            </a>

        </div>

        <div class="user-menu">

            <a href="#" class="notification">
                🔔
                <span class="notification-badge">3</span>
            </a>

            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>

            <div class="user-info">
                <span class="user-name">
                    {{ auth()->user()->name ?? 'User' }}
                </span>

                <span class="user-label">
                    Pelanggan
                </span>
            </div>

            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="background: red; color: white; padding: 4px 2px;">
                        Logout
                    </button>
                </form>
            </div>

        </div>

    </nav>


    {{-- =========================
         HERO
    ========================== --}}

    <section class="hero">

        <div class="hero-content">

            <h1>
                Mau pergi ke mana hari ini?
            </h1>

            <p>
                Cari rute travel, pilih jadwal, kursi favorit,
                dan pesan perjalananmu dengan mudah.
            </p>

        </div>

    </section>


    {{-- =========================
         SEARCH
    ========================== --}}

    <div class="search-card">

        <div class="search-title">
            🔎 Cari Perjalanan
        </div>

        <form action="#" method="GET">

            <div class="search-grid">

                <div class="form-group">

                    <label>
                        Kota Asal
                    </label>

                    <select
                        name="origin"
                        class="form-control"
                        required
                    >
                        <option value="">
                            Pilih kota asal
                        </option>

                        <option value="Jakarta">
                            Jakarta
                        </option>

                        <option value="Bandung">
                            Bandung
                        </option>

                        <option value="Cianjur">
                            Cianjur
                        </option>

                        <option value="Sukabumi">
                            Sukabumi
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Kota Tujuan
                    </label>

                    <select
                        name="destination"
                        class="form-control"
                        required
                    >
                        <option value="">
                            Pilih kota tujuan
                        </option>

                        <option value="Jakarta">
                            Jakarta
                        </option>

                        <option value="Bandung">
                            Bandung
                        </option>

                        <option value="Cianjur">
                            Cianjur
                        </option>

                        <option value="Sukabumi">
                            Sukabumi
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Tanggal Berangkat
                    </label>

                    <input
                        type="date"
                        name="date"
                        class="form-control"
                        min="{{ date('Y-m-d') }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Jumlah Penumpang
                    </label>

                    <select
                        name="passengers"
                        class="form-control"
                        required
                    >
                        <option value="1">
                            1 Penumpang
                        </option>

                        <option value="2">
                            2 Penumpang
                        </option>

                        <option value="3">
                            3 Penumpang
                        </option>

                        <option value="4">
                            4 Penumpang
                        </option>

                        <option value="5">
                            5 Penumpang
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="search-btn"
                >
                    Cari Travel
                </button>

            </div>

        </form>

    </div>


    {{-- =========================
         MAIN CONTENT
    ========================== --}}

    <main class="main-container">


        {{-- QUICK MENU --}}

        <section class="section">

            <div class="section-header">

                <h2 class="section-title">
                    Menu Utama
                </h2>

            </div>

            <div class="quick-menu">

                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        🔎
                    </div>

                    <span>
                        Cari Rute
                    </span>
                </a>


                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        📅
                    </div>

                    <span>
                        Jadwal
                    </span>
                </a>


                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        🚐
                    </div>

                    <span>
                        Kendaraan
                    </span>
                </a>


                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        🎫
                    </div>

                    <span>
                        Booking Saya
                    </span>
                </a>


                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        🎟️
                    </div>

                    <span>
                        E-Ticket
                    </span>
                </a>


                <a
                    href="#"
                    class="quick-card"
                >
                    <div class="quick-icon">
                        📋
                    </div>

                    <span>
                        Riwayat
                    </span>
                </a>

            </div>

        </section>


        {{-- BOOKING AKTIF --}}

        <section class="section">

            <div class="section-header">

                <h2 class="section-title">
                    Booking Saya
                </h2>

                <a
                    href="#"
                    class="section-link"
                >
                    Lihat Semua
                </a>

            </div>

            <div class="booking-card">

                @if(isset($activeBookings) && $activeBookings->count())

                    @foreach($activeBookings as $booking)

                        <div>

                            <strong>
                                {{ $booking->origin }}
                                →
                                {{ $booking->destination }}
                            </strong>

                            <p>
                                {{ $booking->travel_date }}
                                ·
                                {{ $booking->departure_time }}
                            </p>

                        </div>

                    @endforeach

                @else

                    <div class="booking-empty">

                        <div style="font-size: 35px; margin-bottom: 10px;">
                            🎫
                        </div>

                        <p>
                            Belum ada booking aktif.
                        </p>

                        <a
                            href="#"
                            class="booking-button"
                        >
                            Cari Perjalanan
                        </a>

                    </div>

                @endif

            </div>

        </section>


        {{-- PROMO --}}

        <section class="section">

            <div class="section-header">

                <h2 class="section-title">
                    Promo Untuk Kamu
                </h2>

                <a
                    href="#"
                    class="section-link"
                >
                    Lihat Semua
                </a>

            </div>


            <div class="promo-grid">

                <div class="promo-card">

                    <h3>
                        Diskon Perjalanan
                    </h3>

                    <p>
                        Dapatkan potongan harga untuk perjalanan pilihanmu.
                    </p>

                    <span class="promo-code">
                        TRAVEL10
                    </span>

                </div>


                <div class="promo-card">

                    <h3>
                        Promo Pelanggan
                    </h3>

                    <p>
                        Gunakan promo khusus pelanggan untuk perjalanan berikutnya.
                    </p>

                    <span class="promo-code">
                        USER20
                    </span>

                </div>


                <div class="promo-card">

                    <h3>
                        Hemat Lebih Banyak
                    </h3>

                    <p>
                        Nikmati harga perjalanan yang lebih hemat dengan kode promo.
                    </p>

                    <span class="promo-code">
                        HEMAT15
                    </span>

                </div>

            </div>

        </section>


        {{-- FITUR --}}

        <section class="section">

            <div class="section-header">

                <h2 class="section-title">
                    Fitur TravelKu
                </h2>

            </div>


            <div class="feature-grid">

                <div class="feature-card">

                    <div class="feature-icon">
                        🪑
                    </div>

                    <h3>
                        Pilih Kursi
                    </h3>

                    <p>
                        Pilih kursi yang tersedia sesuai dengan posisi yang kamu inginkan.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        💳
                    </div>

                    <h3>
                        Pembayaran
                    </h3>

                    <p>
                        Lakukan pembayaran booking melalui metode pembayaran yang tersedia.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        🎟️
                    </div>

                    <h3>
                        E-Ticket
                    </h3>

                    <p>
                        Dapatkan e-ticket setelah pembayaran berhasil dilakukan.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        ⭐
                    </div>

                    <h3>
                        Rating & Review
                    </h3>

                    <p>
                        Berikan rating dan review terhadap perjalanan yang telah kamu lakukan.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        ❌
                    </div>

                    <h3>
                        Pembatalan Booking
                    </h3>

                    <p>
                        Batalkan booking sesuai dengan ketentuan pembatalan yang berlaku.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        💰
                    </div>

                    <h3>
                        Refund
                    </h3>

                    <p>
                        Ajukan refund dan pantau status pengajuan refund perjalananmu.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        📢
                    </div>

                    <h3>
                        Notifikasi
                    </h3>

                    <p>
                        Dapatkan informasi terbaru mengenai booking, pembayaran, dan perjalanan.
                    </p>

                </div>


                <div class="feature-card">

                    <div class="feature-icon">
                        🔐
                    </div>

                    <h3>
                        Akun Aman
                    </h3>

                    <p>
                        Kelola akun, verifikasi OTP, dan gunakan fitur lupa password dengan mudah.
                    </p>

                </div>

            </div>

        </section>

    </main>


    {{-- =========================
         FOOTER
    ========================== --}}

    <footer>

        <div class="footer-container">

            <div class="footer-logo">
                ✈ TravelKu
            </div>

            <div class="footer-text">
                © {{ date('Y') }} TravelKu. Semua hak dilindungi.
            </div>

        </div>

    </footer>

</body>

</html>
