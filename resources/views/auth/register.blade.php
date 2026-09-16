<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Daftar - Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        /* =========================================================
           REGISTER PAGE
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        body.register-page {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f8f7;
            color: #17221d;
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* =========================================================
           LEFT SIDE
        ========================================================= */

        .register-left {
            width: 45%;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            background: #087f55;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 42px 55px;
        }

        .register-left::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            top: -100px;
            right: -100px;
        }

        .register-left::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            bottom: -140px;
            left: -100px;
        }

        .register-logo {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .register-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .register-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .register-left-content {
            position: relative;
            z-index: 2;
            max-width: 520px;
        }

        .register-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .register-left-title {
            margin: 0;
            font-size: clamp(36px, 4vw, 54px);
            line-height: 1.08;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .register-left-description {
            margin: 22px 0 0;
            max-width: 480px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 15px;
            line-height: 1.7;
        }

        .register-features {
            margin-top: 34px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .register-feature {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .register-feature-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .register-feature-title {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 700;
            color: white;
        }

        .register-feature-text {
            margin: 0;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
        }

        .register-copyright {
            position: relative;
            z-index: 2;
            margin: 0;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* =========================================================
           RIGHT SIDE
        ========================================================= */

        .register-right {
            width: 55%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 70px;
            background: #f7faf9;
        }

        .register-container {
            width: 100%;
            max-width: 560px;
        }

        .register-mobile-logo {
            display: none;
        }

        .register-header {
            margin-bottom: 26px;
        }

        .register-header-badge {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 11px;
            border-radius: 999px;
            background: #e3f5ee;
            color: #087f55;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .register-header h1 {
            margin: 0;
            color: #17221d;
            font-size: 34px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.6px;
        }

        .register-header p {
            margin: 9px 0 0;
            color: #718079;
            font-size: 14px;
            line-height: 1.6;
        }

        /* =========================================================
           FORM CARD
        ========================================================= */

        .register-card {
            width: 100%;
            background: white;
            border: 1px solid #e1e9e5;
            border-radius: 22px;
            padding: 30px;
            box-shadow:
                0 12px 35px rgba(19, 48, 37, 0.07),
                0 2px 6px rgba(19, 48, 37, 0.03);
        }

        /* =========================================================
           ERROR
        ========================================================= */

        .register-error {
            display: flex;
            gap: 12px;
            padding: 14px;
            margin-bottom: 22px;
            border: 1px solid #fecaca;
            border-radius: 14px;
            background: #fff5f5;
        }

        .register-error-icon {
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #fee2e2;
            color: #dc2626;
            font-weight: 700;
        }

        .register-error-title {
            margin: 0 0 5px;
            font-size: 13px;
            font-weight: 700;
            color: #b91c1c;
        }

        .register-error-list {
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 12px;
            line-height: 1.7;
            color: #dc2626;
        }

        /* =========================================================
           FORM
        ========================================================= */

        .register-form {
            display: flex;
            flex-direction: column;
            gap: 17px;
        }

        .register-field {
            width: 100%;
        }

        .register-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 600;
            color: #25332d;
        }

        .register-optional {
            color: #84918b;
            font-weight: 400;
        }

        .register-input-wrapper {
            position: relative;
            width: 100%;
        }

        .register-input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            width: 20px;
            text-align: center;
            font-size: 15px;
            pointer-events: none;
            z-index: 1;
        }

        .register-input {
            display: block;
            width: 100%;
            height: 48px !important;
            min-height: 48px !important;
            padding: 0 15px 0 45px !important;
            border: 1px solid #dce5e0 !important;
            border-radius: 12px !important;
            outline: none !important;
            background: #f9fbfa !important;
            color: #17221d !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 13px !important;
            line-height: 48px !important;
            box-shadow: none !important;
            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }

        .register-input::placeholder {
            color: #a4b0aa !important;
            opacity: 1;
        }

        .register-input:hover {
            border-color: #a9c7ba !important;
        }

        .register-input:focus {
            border-color: #087f55 !important;
            background: white !important;
            box-shadow: 0 0 0 4px rgba(8, 127, 85, 0.10) !important;
        }

        /* =========================================================
           TERMS
        ========================================================= */

        .register-terms {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 14px;
            border-radius: 12px;
            background: #f7faf9;
            border: 1px solid #edf2ef;
        }

        .register-checkbox {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px;
            margin: 2px 0 0 !important;
            accent-color: #087f55;
            cursor: pointer;
        }

        .register-terms label {
            cursor: pointer;
            color: #718079;
            font-size: 11px;
            line-height: 1.6;
        }

        /* =========================================================
           BUTTON
        ========================================================= */

        .register-button {
            width: 100%;
            height: 48px !important;
            min-height: 48px !important;
            border: none !important;
            border-radius: 12px !important;
            background: #087f55 !important;
            color: white !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 5px 14px rgba(8, 127, 85, 0.18);
            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .register-button:hover {
            background: #066b47 !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(8, 127, 85, 0.22);
        }

        .register-button:active {
            transform: translateY(0);
        }

        .register-button-arrow {
            transition: transform 0.2s ease;
        }

        .register-button:hover .register-button-arrow {
            transform: translateX(3px);
        }

        /* =========================================================
           BOTTOM LINKS
        ========================================================= */

        .register-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
        }

        .register-divider-line {
            flex: 1;
            height: 1px;
            background: #e5ebe8;
        }

        .register-divider-text {
            color: #9aa6a0;
            font-size: 11px;
        }

        .register-login {
            margin: 0;
            text-align: center;
            color: #718079;
            font-size: 13px;
        }

        .register-login a {
            margin-left: 4px;
            color: #087f55;
            font-weight: 700;
            text-decoration: none;
        }

        .register-login a:hover {
            color: #066b47;
        }

        .register-back {
            margin: 17px 0 0;
            text-align: center;
        }

        .register-back a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 9px;
            color: #718079;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .register-back a:hover {
            background: #f1f7f4;
            color: #087f55;
        }

        .register-mobile-copyright {
            display: none;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1000px) {

            .register-left {
                width: 42%;
                padding: 35px;
            }

            .register-right {
                width: 58%;
                padding: 40px;
            }

            .register-left-title {
                font-size: 38px;
            }

        }

        @media (max-width: 768px) {

            .register-wrapper {
                display: block;
            }

            .register-left {
                display: none;
            }

            .register-right {
                width: 100%;
                min-height: 100vh;
                padding: 30px 18px;
                align-items: center;
            }

            .register-container {
                max-width: 520px;
            }

            .register-mobile-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                margin-bottom: 28px;
            }

            .register-mobile-logo-icon {
                width: 40px;
                height: 40px;
                border-radius: 11px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #087f55;
                font-size: 20px;
            }

            .register-mobile-logo-text {
                font-size: 22px;
                font-weight: 700;
                color: #087f55;
            }

            .register-header {
                text-align: center;
            }

            .register-header h1 {
                font-size: 30px;
            }

            .register-card {
                padding: 24px 20px;
                border-radius: 18px;
            }

            .register-mobile-copyright {
                display: block;
                margin-top: 22px;
                text-align: center;
                font-size: 11px;
                color: #9aa6a0;
            }
        }

        @media (max-width: 400px) {

            .register-right {
                padding: 25px 14px;
            }

            .register-card {
                padding: 20px 16px;
            }

            .register-header h1 {
                font-size: 27px;
            }

            .register-input {
                height: 46px !important;
                min-height: 46px !important;
                line-height: 46px !important;
            }

            .register-button {
                height: 46px !important;
                min-height: 46px !important;
            }
        }
    </style>
</head>


<body class="register-page">

    <div class="register-wrapper">

        {{-- =====================================================
             LEFT PANEL
        ====================================================== --}}
        <section class="register-left">

            {{-- Logo --}}
            <div class="register-logo">

                <div class="register-logo-icon">
                    🌿
                </div>

                <div class="register-logo-text">
                    Travel
                </div>

            </div>


            {{-- Content --}}
            <div class="register-left-content">

                <div class="register-badge">
                    ✨ Mulai perjalanan Anda
                </div>

                <h2 class="register-left-title">
                    Buat akun dan<br>
                    nikmati perjalanan<br>
                    Anda.
                </h2>

                <p class="register-left-description">
                    Daftarkan akun Anda untuk mendapatkan pengalaman
                    pemesanan perjalanan yang mudah, cepat, dan nyaman.
                </p>


                {{-- Features --}}
                <div class="register-features">

                    <div class="register-feature">

                        <div class="register-feature-icon">
                            ✓
                        </div>

                        <div>
                            <p class="register-feature-title">
                                Pemesanan mudah
                            </p>

                            <p class="register-feature-text">
                                Pesan perjalanan dengan cepat.
                            </p>
                        </div>

                    </div>


                    <div class="register-feature">

                        <div class="register-feature-icon">
                            🚌
                        </div>

                        <div>
                            <p class="register-feature-title">
                                Perjalanan nyaman
                            </p>

                            <p class="register-feature-text">
                                Pilihan perjalanan untuk kebutuhan Anda.
                            </p>
                        </div>

                    </div>


                    <div class="register-feature">

                        <div class="register-feature-icon">
                            🔒
                        </div>

                        <div>
                            <p class="register-feature-title">
                                Data terlindungi
                            </p>

                            <p class="register-feature-text">
                                Informasi akun Anda tetap aman.
                            </p>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Copyright --}}
            <p class="register-copyright">
                © {{ date('Y') }} Travel. Semua hak dilindungi.
            </p>

        </section>


        {{-- =====================================================
             RIGHT PANEL
        ====================================================== --}}
        <main class="register-right">

            <div class="register-container">

                {{-- Mobile Logo --}}
                <div class="register-mobile-logo">

                    <div class="register-mobile-logo-icon">
                        🌿
                    </div>

                    <div class="register-mobile-logo-text">
                        Travel
                    </div>

                </div>


                {{-- Header --}}
                <div class="register-header">

                    <span class="register-header-badge">
                        BUAT AKUN
                    </span>

                    <h1>
                        Daftar Akun
                    </h1>

                    <p>
                        Daftar untuk mulai memesan perjalanan.
                    </p>

                </div>


                {{-- =================================================
                     CARD
                ================================================== --}}
                <div class="register-card">

                    {{-- Error --}}
                    @if ($errors->any())

                        <div class="register-error">

                            <div class="register-error-icon">
                                !
                            </div>

                            <div>

                                <p class="register-error-title">
                                    Periksa kembali data Anda:
                                </p>

                                <ul class="register-error-list">

                                    @foreach ($errors->all() as $error)

                                        <li>
                                            • {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    @endif


                    {{-- Form --}}
                    <form
                        method="POST"
                        action="{{ route('register.store') }}"
                        class="register-form"
                    >

                        @csrf


                        {{-- Nama Lengkap --}}
                        <div class="register-field">

                            <label
                                for="name"
                                class="register-label"
                            >
                                Nama Lengkap
                            </label>

                            <div class="register-input-wrapper">

                                <span class="register-input-icon">
                                    👤
                                </span>

                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap"
                                    autocomplete="name"
                                    required
                                    autofocus
                                    class="register-input"
                                >

                            </div>

                        </div>


                        {{-- Email --}}
                        <div class="register-field">

                            <label
                                for="email"
                                class="register-label"
                            >
                                Email
                            </label>

                            <div class="register-input-wrapper">

                                <span class="register-input-icon">
                                    ✉
                                </span>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email"
                                    autocomplete="email"
                                    required
                                    class="register-input"
                                >

                            </div>

                        </div>


                        {{-- Nomor Telepon --}}
                        <div class="register-field">

                            <label
                                for="phone"
                                class="register-label"
                            >
                                Nomor Telepon
                                <span class="register-optional">
                                    (opsional)
                                </span>
                            </label>

                            <div class="register-input-wrapper">

                                <span class="register-input-icon">
                                    ☎
                                </span>

                                <input
                                    id="phone"
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="08xxxxxxxxxx"
                                    autocomplete="tel"
                                    class="register-input"
                                >

                            </div>

                        </div>


                        {{-- Password --}}
                        <div class="register-field">

                            <label
                                for="password"
                                class="register-label"
                            >
                                Password
                            </label>

                            <div class="register-input-wrapper">

                                <span class="register-input-icon">
                                    🔒
                                </span>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    autocomplete="new-password"
                                    required
                                    class="register-input"
                                >

                            </div>

                        </div>


                        {{-- Konfirmasi Password --}}
                        <div class="register-field">

                            <label
                                for="password_confirmation"
                                class="register-label"
                            >
                                Konfirmasi Password
                            </label>

                            <div class="register-input-wrapper">

                                <span class="register-input-icon">
                                    🔐
                                </span>

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Ulangi password"
                                    autocomplete="new-password"
                                    required
                                    class="register-input"
                                >

                            </div>

                        </div>


                        {{-- Terms --}}
                        <div class="register-terms">

                            <input
                                id="terms"
                                type="checkbox"
                                name="terms"
                                required
                                class="register-checkbox"
                            >

                            <label for="terms">
                                Saya menyetujui syarat dan ketentuan
                                penggunaan layanan Travel.
                            </label>

                        </div>


                        {{-- Button --}}
                        <button
                            type="submit"
                            class="register-button"
                        >

                            <span>
                                Daftar
                            </span>

                            <span class="register-button-arrow">
                                →
                            </span>

                        </button>

                    </form>


                    {{-- Divider --}}
                    <div class="register-divider">

                        <span class="register-divider-line"></span>

                        <span class="register-divider-text">
                            atau
                        </span>

                        <span class="register-divider-line"></span>

                    </div>


                    {{-- Login --}}
                    <p class="register-login">

                        Sudah punya akun?

                        <a href="{{ route('login') }}">
                            Login
                        </a>

                    </p>


                    {{-- Back --}}
                    <div class="register-back">

                        <a href="{{ url('/') }}">

                            <span>
                                ←
                            </span>

                            <span>
                                Kembali ke Beranda
                            </span>

                        </a>

                    </div>

                </div>


                {{-- Mobile Copyright --}}
                <p class="register-mobile-copyright">
                    © {{ date('Y') }} Travel. Semua hak dilindungi.
                </p>

            </div>

        </main>

    </div>

</body>

</html>