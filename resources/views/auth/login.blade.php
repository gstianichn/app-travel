<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background: #f7f9f8;
            color: #17221d;
        }

        /* =====================================================
           PAGE
        ===================================================== */

        .login-page {
            min-height: 100vh;

            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* =====================================================
           LEFT BRANDING
        ===================================================== */

        .login-brand {
            position: relative;

            min-height: 100vh;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    #087f52 0%,
                    #0b8b5c 45%,
                    #076f49 100%
                );

            color: #ffffff;
        }

        .brand-content {
            position: relative;
            z-index: 2;

            min-height: 100vh;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            padding: 48px 56px;
        }

        .brand-logo {
            display: flex;
            align-items: center;

            gap: 14px;
        }

        .brand-logo-icon {
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 14px;

            background: rgba(255, 255, 255, 0.14);

            font-size: 23px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .brand-logo-title {
            margin: 0;

            font-size: 20px;
            font-weight: 700;

            letter-spacing: -0.02em;
        }

        .brand-logo-subtitle {
            margin: 3px 0 0;

            font-size: 12px;

            color: rgba(255, 255, 255, 0.7);
        }

        .brand-hero {
            max-width: 580px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;

            gap: 8px;

            padding: 8px 14px;

            margin-bottom: 22px;

            border: 1px solid rgba(255, 255, 255, 0.16);

            border-radius: 999px;

            background: rgba(255, 255, 255, 0.09);

            font-size: 12px;
            font-weight: 600;

            color: rgba(255, 255, 255, 0.9);
        }

        .brand-badge-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #ffffff;
        }

        .brand-title {
            margin: 0;

            font-size: clamp(42px, 4vw, 62px);

            line-height: 1.08;

            font-weight: 800;

            letter-spacing: -0.045em;
        }

        .brand-title-muted {
            display: block;

            color: rgba(255, 255, 255, 0.58);
        }

        .brand-description {
            max-width: 500px;

            margin: 24px 0 0;

            font-size: 16px;

            line-height: 1.7;

            color: rgba(255, 255, 255, 0.76);
        }

        .brand-features {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 12px;

            max-width: 540px;

            margin-top: 34px;
        }

        .brand-feature {
            padding: 16px;

            border: 1px solid rgba(255, 255, 255, 0.12);

            border-radius: 16px;

            background: rgba(255, 255, 255, 0.09);

            backdrop-filter: blur(10px);

            transition:
                transform 0.2s ease,
                background 0.2s ease;
        }

        .brand-feature:hover {
            transform: translateY(-3px);

            background: rgba(255, 255, 255, 0.13);
        }

        .brand-feature-icon {
            margin-bottom: 10px;

            font-size: 21px;
        }

        .brand-feature-title {
            margin: 0;

            font-size: 14px;

            font-weight: 700;
        }

        .brand-feature-text {
            margin: 4px 0 0;

            font-size: 11px;

            line-height: 1.5;

            color: rgba(255, 255, 255, 0.62);
        }

        .brand-footer {
            display: flex;

            justify-content: space-between;

            font-size: 11px;

            color: rgba(255, 255, 255, 0.5);
        }

        /* =====================================================
           DECORATION
        ===================================================== */

        .circle {
            position: absolute;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.07);

            pointer-events: none;
        }

        .circle-one {
            width: 330px;
            height: 330px;

            top: -150px;
            left: -130px;
        }

        .circle-two {
            width: 420px;
            height: 420px;

            right: -200px;
            bottom: -190px;
        }

        .circle-three {
            width: 110px;
            height: 110px;

            right: 100px;
            top: 36%;
        }

        .circle-four {
            width: 70px;
            height: 70px;

            left: 38%;
            bottom: 15%;
        }

        /* =====================================================
           RIGHT PANEL
        ===================================================== */

        .login-panel {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 48px;
        }

        .login-wrapper {
            width: 100%;

            max-width: 440px;
        }

        .mobile-logo {
            display: none;
        }

        /* =====================================================
           HEADING
        ===================================================== */

        .login-heading {
            margin-bottom: 28px;
        }

        .login-label {
            display: block;

            margin-bottom: 8px;

            font-size: 12px;

            font-weight: 700;

            letter-spacing: 0.18em;

            text-transform: uppercase;

            color: #078355;
        }

        .login-title {
            margin: 0;

            font-size: 36px;

            line-height: 1.15;

            font-weight: 800;

            letter-spacing: -0.04em;

            color: #17221d;
        }

        .login-description {
            margin: 10px 0 0;

            font-size: 14px;

            line-height: 1.6;

            color: #718078;
        }

        /* =====================================================
           CARD
        ===================================================== */

        .login-card {
            padding: 32px;

            border: 1px solid #e2e8e4;

            border-radius: 24px;

            background: #ffffff;

            box-shadow:
                0 20px 60px rgba(20, 50, 38, 0.08);
        }

        /* =====================================================
           ERROR
        ===================================================== */

        .error-box {
            margin-bottom: 22px;

            padding: 14px 16px;

            border: 1px solid #fecaca;

            border-radius: 14px;

            background: #fef2f2;

            color: #dc2626;

            font-size: 13px;

            line-height: 1.5;
        }

        .error-message {
            display: block;

            margin-top: 7px;

            font-size: 12px;

            color: #dc2626;
        }

        /* =====================================================
           FORM
        ===================================================== */

        .form-group {
            margin-bottom: 19px;
        }

        .form-label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 700;

            color: #26342d;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            width: 20px;
            height: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #8a9991;

            pointer-events: none;

            font-size: 16px;

            z-index: 2;
        }

        .form-input,
        .form-select {
            width: 100%;

            height: 50px;

            padding: 0 46px;

            border: 1px solid #dce4df;

            border-radius: 13px;

            outline: none;

            background: #f9fbfa;

            color: #17221d;

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        .form-input::placeholder {
            color: #a5afa9;
        }

        .form-input:hover,
        .form-select:hover {
            border-color: #b8c8bf;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #078355;

            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(7, 131, 85, 0.09);
        }

        .form-select {
            appearance: none;

            cursor: pointer;

            padding-right: 42px;

            background-image:
                linear-gradient(45deg, transparent 50%, #819088 50%),
                linear-gradient(135deg, #819088 50%, transparent 50%);

            background-position:
                calc(100% - 18px) 21px,
                calc(100% - 13px) 21px;

            background-size:
                5px 5px,
                5px 5px;

            background-repeat: no-repeat;
        }

        /* =====================================================
           PASSWORD
        ===================================================== */

        .password-toggle {
            position: absolute;

            right: 10px;
            top: 50%;

            transform: translateY(-50%);

            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 0;

            border-radius: 8px;

            background: transparent;

            color: #8a9991;

            cursor: pointer;

            transition:
                background 0.2s ease,
                color 0.2s ease;
        }

        .password-toggle:hover {
            background: #eef7f2;

            color: #078355;
        }

        /* =====================================================
           LOGIN OPTIONS
        ===================================================== */

        .login-options {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;

            margin: 2px 0 22px;
        }

        .remember {
            display: flex;

            align-items: center;

            gap: 9px;

            font-size: 13px;

            color: #718078;

            cursor: pointer;

            user-select: none;
        }

        .remember input {
            width: 16px;
            height: 16px;

            margin: 0;

            accent-color: #078355;

            cursor: pointer;
        }

        .forgot-password {
            font-size: 13px;

            font-weight: 600;

            color: #078355;

            text-decoration: none;

            transition: color 0.2s ease;
        }

        .forgot-password:hover {
            color: #066d47;

            text-decoration: underline;
        }

        /* =====================================================
           BUTTON
        ===================================================== */

        .login-button {
            width: 100%;

            height: 50px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            border: 0;

            border-radius: 13px;

            background: #078355;

            color: #ffffff;

            font-family: inherit;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 10px 22px rgba(7, 131, 85, 0.2);

            transition:
                transform 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);

            background: #066d47;

            box-shadow:
                0 14px 28px rgba(7, 131, 85, 0.25);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button span {
            font-size: 18px;

            line-height: 1;
        }

        /* =====================================================
           DIVIDER
        ===================================================== */

        .divider {
            display: flex;

            align-items: center;

            gap: 14px;

            margin: 24px 0;
        }

        .divider-line {
            flex: 1;

            height: 1px;

            background: #e6ebe8;
        }

        .divider-text {
            font-size: 10px;

            font-weight: 600;

            letter-spacing: 0.08em;

            color: #98a49e;

            white-space: nowrap;
        }

        /* =====================================================
           REGISTER
        ===================================================== */

        .register-button {
            width: 100%;

            height: 50px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 1px solid #dce4df;

            border-radius: 13px;

            background: #ffffff;

            color: #26342d;

            text-decoration: none;

            font-size: 14px;

            font-weight: 700;

            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .register-button:hover {
            border-color: #078355;

            background: #f2faf6;

            color: #078355;

            transform: translateY(-1px);
        }

        /* =====================================================
           SECURITY
        ===================================================== */

        .security-info {
            display: flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            margin-top: 20px;

            font-size: 11px;

            color: #89958f;
        }

        .security-info svg {
            flex-shrink: 0;
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {

            .brand-content {
                padding: 40px;
            }

            .login-panel {
                padding: 32px;
            }

            .brand-title {
                font-size: 46px;
            }

            .brand-description {
                font-size: 14px;
            }
        }

        @media (max-width: 850px) {

            .login-page {
                grid-template-columns: 42% 58%;
            }

            .brand-content {
                padding: 32px;
            }

            .brand-title {
                font-size: 38px;
            }

            .brand-description {
                font-size: 13px;
            }

            .brand-features {
                grid-template-columns: 1fr;
            }

            .brand-feature {
                padding: 13px;
            }

            .brand-feature-text {
                display: none;
            }

            .login-panel {
                padding: 24px;
            }
        }

        @media (max-width: 768px) {

            .login-page {
                display: block;
            }

            .login-brand {
                display: none;
            }

            .login-panel {
                min-height: 100vh;

                padding: 32px 20px;
            }

            .login-wrapper {
                max-width: 460px;
            }

            .mobile-logo {
                display: flex;

                align-items: center;

                justify-content: center;

                gap: 10px;

                margin-bottom: 30px;
            }

            .mobile-logo-icon {
                width: 44px;
                height: 44px;

                display: flex;

                align-items: center;

                justify-content: center;

                border-radius: 13px;

                background: #078355;

                font-size: 21px;

                box-shadow:
                    0 8px 20px rgba(7, 131, 85, 0.2);
            }

            .mobile-logo-title {
                font-size: 20px;

                font-weight: 800;

                color: #078355;
            }

            .login-heading {
                margin-bottom: 22px;
            }

            .login-title {
                font-size: 32px;
            }

            .login-card {
                padding: 24px;

                border-radius: 20px;
            }
        }

        @media (max-width: 480px) {

            .login-panel {
                padding: 24px 16px;
            }

            .mobile-logo {
                margin-bottom: 25px;
            }

            .login-title {
                font-size: 28px;
            }

            .login-description {
                font-size: 13px;
            }

            .login-card {
                padding: 20px;

                border-radius: 18px;
            }

            .login-options {
                align-items: flex-start;

                flex-direction: column;

                gap: 12px;
            }

            .forgot-password {
                margin-left: 25px;
            }
        }
    </style>
</head>


<body>

    <main class="login-page">

        {{-- =====================================================
             LEFT BRANDING
        ====================================================== --}}

        <section class="login-brand">

            <div class="circle circle-one"></div>
            <div class="circle circle-two"></div>
            <div class="circle circle-three"></div>
            <div class="circle circle-four"></div>

            <div class="brand-content">

                {{-- Logo --}}
                <div class="brand-logo">

                    <div class="brand-logo-icon">
                        🌿
                    </div>

                    <div>
                        <h2 class="brand-logo-title">
                            Travel
                        </h2>

                        <p class="brand-logo-subtitle">
                            Your Journey, Our Service
                        </p>
                    </div>

                </div>


                {{-- Hero --}}
                <div class="brand-hero">

                    <div class="brand-badge">
                        <span class="brand-badge-dot"></span>
                        Platform Travel Terpercaya
                    </div>


                    <h1 class="brand-title">

                        Perjalanan nyaman,

                        <span class="brand-title-muted">
                            dimulai dari sini.
                        </span>

                    </h1>


                    <p class="brand-description">
                        Temukan perjalanan yang sesuai dengan kebutuhan Anda.
                        Pesan dengan mudah dan nikmati pengalaman perjalanan
                        yang aman dan nyaman bersama Travel.
                    </p>


                    <div class="brand-features">

                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                🚌
                            </div>

                            <p class="brand-feature-title">
                                Mudah
                            </p>

                            <p class="brand-feature-text">
                                Pemesanan praktis
                            </p>

                        </div>


                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                🔒
                            </div>

                            <p class="brand-feature-title">
                                Aman
                            </p>

                            <p class="brand-feature-text">
                                Data terlindungi
                            </p>

                        </div>


                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                ✨
                            </div>

                            <p class="brand-feature-title">
                                Nyaman
                            </p>

                            <p class="brand-feature-text">
                                Layanan terbaik
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Footer --}}
                <div class="brand-footer">

                    <span>
                        © {{ date('Y') }} Travel
                    </span>

                    <span>
                        Semua hak dilindungi
                    </span>

                </div>

            </div>

        </section>


        {{-- =====================================================
             RIGHT LOGIN
        ====================================================== --}}

        <section class="login-panel">

            <div class="login-wrapper">

                {{-- Mobile Logo --}}
                <div class="mobile-logo">

                    <div class="mobile-logo-icon">
                        🌿
                    </div>

                    <div class="mobile-logo-title">
                        Travel
                    </div>

                </div>


                {{-- Heading --}}
                <div class="login-heading">

                    <span class="login-label">
                        Login
                    </span>

                    <h1 class="login-title">
                        Selamat Datang 👋
                    </h1>

                    <p class="login-description">
                        Masuk ke akun Anda untuk melanjutkan perjalanan.
                    </p>

                </div>


                {{-- Card --}}
                <div class="login-card">

                    {{-- Error --}}
                    @if ($errors->any())

                        <div class="error-box">

                            @foreach ($errors->all() as $error)

                                <div>
                                    {{ $error }}
                                </div>

                            @endforeach

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('login.process') }}"
                    >

                        @csrf


                        {{-- EMAIL --}}
                        <div class="form-group">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>

                            <div class="input-wrapper">

                                <span class="input-icon">
                                    ✉
                                </span>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-input"
                                    value="{{ old('email') }}"
                                    placeholder="nama@email.com"
                                    autocomplete="email"
                                    required
                                    autofocus
                                >

                            </div>

                            @error('email')

                                <small class="error-message">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        {{-- PASSWORD --}}
                        <div class="form-group">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Password
                            </label>

                            <div class="input-wrapper">

                                <span class="input-icon">
                                    🔒
                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="togglePassword"
                                    aria-label="Tampilkan password"
                                >

                                    <svg
                                        id="eyeIcon"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="3"
                                        />

                                    </svg>

                                </button>

                            </div>

                            @error('password')

                                <small class="error-message">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        {{-- ROLE --}}
                        <div class="form-group">

                            <label
                                for="role"
                                class="form-label"
                            >
                                Login sebagai
                            </label>

                            <div class="input-wrapper">

                                <span class="input-icon">
                                    👤
                                </span>

                                <select
                                    id="role"
                                    name="role"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Role --
                                    </option>

                                    <option
                                        value="admin_utama"
                                        {{ old('role') === 'admin_utama' ? 'selected' : '' }}
                                    >
                                        Admin Utama
                                    </option>

                                    <option
                                        value="staff"
                                        {{ old('role') === 'staff' ? 'selected' : '' }}
                                    >
                                        Staff
                                    </option>

                                    <option
                                        value="cs"
                                        {{ old('role') === 'cs' ? 'selected' : '' }}
                                    >
                                        Customer Service
                                    </option>

                                    <option
                                        value="driver"
                                        {{ old('role') === 'driver' ? 'selected' : '' }}
                                    >
                                        Driver
                                    </option>

                                </select>

                            </div>

                            @error('role')

                                <small class="error-message">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        {{-- KODE AKSES --}}
                        <div class="form-group">

                            <label
                                for="access_code"
                                class="form-label"
                            >
                                Kode Akses
                            </label>

                            <div class="input-wrapper">

                                <span class="input-icon">
                                    🔑
                                </span>

                                <input
                                    type="password"
                                    id="access_code"
                                    name="access_code"
                                    class="form-input"
                                    placeholder="Masukkan kode akses"
                                    autocomplete="off"
                                    required
                                >

                            </div>

                            @error('access_code')

                                <small class="error-message">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        {{-- REMEMBER --}}
                        <div class="login-options">

                            <label class="remember">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}
                                >

                                <span>
                                    Ingat saya
                                </span>

                            </label>


                            <a
                                href="{{ route('password.request') }}"
                                class="forgot-password"
                            >
                                Lupa password?
                            </a>

                        </div>


                        {{-- BUTTON --}}
                        <button
                            type="submit"
                            class="login-button"
                        >

                            <span>
                                Masuk ke Akun
                            </span>

                            <span>
                                →
                            </span>

                        </button>

                    </form>


                    {{-- Divider --}}
                    <div class="divider">

                        <div class="divider-line"></div>

                        <span class="divider-text">
                            AKUN BARU?
                        </span>

                        <div class="divider-line"></div>

                    </div>


                    {{-- Register --}}
                    <a
                        href="{{ route('register') }}"
                        class="register-button"
                    >
                        Buat Akun Baru
                    </a>

                </div>


                {{-- Security --}}
                <div class="security-info">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="15"
                        height="15"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12l2 2 4-4"
                        />

                    </svg>

                    <span>
                        Login aman dan data Anda terlindungi
                    </span>

                </div>

            </div>

        </section>

    </main>


    {{-- =====================================================
         PASSWORD TOGGLE
    ====================================================== --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const button =
                document.getElementById('togglePassword');

            const password =
                document.getElementById('password');

            const icon =
                document.getElementById('eyeIcon');


            if (!button || !password || !icon) {
                return;
            }


            button.addEventListener('click', function () {

                const hidden =
                    password.type === 'password';


                password.type =
                    hidden ? 'text' : 'password';


                button.setAttribute(
                    'aria-label',
                    hidden
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                );


                if (hidden) {

                    icon.innerHTML = `
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 001.458 12C2.732 16.057 6.523 19 11 19c1.61 0 3.13-.386 4.46-1.07"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.228 6.228A10.45 10.45 0 0111 5c4.477 0 8.268 2.943 9.542 7a10.47 10.47 0 01-2.328 4.332"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.228 6.228L3 3m3.228 3.228l3.07 3.07m0 0a3 3 0 104.243 4.243M9.298 9.298l4.243 4.243M21 21l-4.5-4.5"
                        />
                    `;

                } else {

                    icon.innerHTML = `
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />

                        <circle
                            cx="12"
                            cy="12"
                            r="3"
                        />
                    `;

                }

            });

        });

    </script>

</body>

</html>
