<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login User | Travel</title>

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

        .login-page {
            min-height: 100vh;

            display: grid;

            grid-template-columns: 1fr 1fr;
        }

        /* =====================================================
           LEFT SIDE
        ====================================================== */

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

            color: white;
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
        }

        .brand-logo-title {
            margin: 0;

            font-size: 20px;

            font-weight: 700;
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

            background: white;
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

            grid-template-columns:
                repeat(3, 1fr);

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
        ====================================================== */

        .circle {
            position: absolute;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.07);
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

            right: -220px;
            bottom: -200px;
        }

        /* =====================================================
           RIGHT SIDE
        ====================================================== */

        .login-form-side {
            min-height: 100vh;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 40px 30px;

            background: #f7f9f8;
        }

        .login-card {
            width: 100%;

            max-width: 440px;

            background: white;

            padding: 42px;

            border-radius: 22px;

            box-shadow:
                0 20px 60px rgba(15, 23, 42, 0.08);

            border: 1px solid #edf1ef;
        }

        .login-heading {
            margin-bottom: 30px;
        }

        .login-heading h1 {
            margin: 0;

            font-size: 30px;

            line-height: 1.2;

            font-weight: 800;

            color: #17221d;
        }

        .login-heading p {
            margin: 9px 0 0;

            color: #718078;

            font-size: 14px;
        }

        .user-badge {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 16px;

            padding: 7px 12px;

            border-radius: 999px;

            background: #e5f4ed;

            color: #087f52;

            font-size: 12px;

            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 700;

            color: #29362f;
        }

        .form-input {
            width: 100%;

            height: 48px;

            padding: 0 15px;

            border: 1px solid #dce6e1;

            border-radius: 11px;

            outline: none;

            font-size: 14px;

            color: #17221d;

            background: white;

            transition: 0.2s ease;
        }

        .form-input:focus {
            border-color: #0c7950;

            box-shadow:
                0 0 0 4px rgba(12, 121, 80, 0.08);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-input {
            padding-right: 50px;
        }

        .password-toggle {
            position: absolute;

            top: 50%;
            right: 14px;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            cursor: pointer;

            color: #7a8881;
        }

        .form-footer {
            display: flex;

            align-items: center;
            justify-content: space-between;

            margin-top: 10px;
            margin-bottom: 22px;
        }

        .remember-me {
            display: flex;

            align-items: center;

            gap: 8px;

            cursor: pointer;

            font-size: 13px;

            color: #29362f;

            font-weight: 600;

            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;

            accent-color: #0c7950;

            cursor: pointer;
        }

        .forgot-password {
            font-size: 13px;

            color: #087f52;

            font-weight: 600;

            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;

            height: 50px;

            display: flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            border: none;

            border-radius: 11px;

            background: #0c7950;

            color: white;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .login-button:hover {
            background: #096b47;

            transform: translateY(-1px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .error-message {
            margin-bottom: 20px;

            padding: 12px 14px;

            border-radius: 10px;

            background: #fff1f1;

            border: 1px solid #ffd6d6;

            color: #c0392b;

            font-size: 13px;
        }

        .divider {
            display: flex;

            align-items: center;

            gap: 12px;

            margin: 26px 0 18px;
        }

        .divider-line {
            flex: 1;

            height: 1px;

            background: #e8eeeb;
        }

        .divider-text {
            font-size: 10px;

            font-weight: 700;

            color: #a0aaa5;

            letter-spacing: 0.08em;
        }

        .register-button {
            width: 100%;

            height: 48px;

            display: flex;

            align-items: center;
            justify-content: center;

            border: 1px solid #d9e4df;

            border-radius: 11px;

            background: white;

            color: #0c7950;

            font-size: 14px;

            font-weight: 700;

            transition: 0.2s ease;
        }

        .register-button:hover {
            background: #f2f8f5;
        }

        .security-info {
            display: flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            margin-top: 22px;

            color: #8b9691;

            font-size: 11px;
        }

        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 900px) {

            .login-page {
                grid-template-columns: 1fr;
            }

            .login-brand {
                display: none;
            }

            .login-form-side {
                min-height: 100vh;

                padding: 25px 18px;
            }

            .login-card {
                padding: 30px 24px;
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

            <div class="brand-content">

                {{-- Logo --}}
                <div class="brand-logo">

                    <div class="brand-logo-icon">
                        ✈
                    </div>

                    <div>
                        <p class="brand-logo-title">
                            Travel
                        </p>

                        <p class="brand-logo-subtitle">
                            Travel Management System
                        </p>
                    </div>

                </div>


                {{-- Hero --}}
                <div class="brand-hero">

                    <div class="brand-badge">

                        <span class="brand-badge-dot"></span>

                        LOGIN USER

                    </div>

                    <h2 class="brand-title">

                        Nikmati perjalanan
                        <span class="brand-title-muted">
                            dengan lebih mudah.
                        </span>

                    </h2>

                    <p class="brand-description">

                        Masuk sebagai User untuk melakukan
                        pemesanan perjalanan, melihat informasi
                        booking, dan mengelola aktivitas perjalanan
                        Anda.

                    </p>


                    <div class="brand-features">

                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                🔐
                            </div>

                            <p class="brand-feature-title">
                                Aman
                            </p>

                            <p class="brand-feature-text">
                                Data akun terlindungi.
                            </p>

                        </div>


                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                🚌
                            </div>

                            <p class="brand-feature-title">
                                Perjalanan
                            </p>

                            <p class="brand-feature-text">
                                Kelola perjalanan Anda.
                            </p>

                        </div>


                        <div class="brand-feature">

                            <div class="brand-feature-icon">
                                📋
                            </div>

                            <p class="brand-feature-title">
                                Booking
                            </p>

                            <p class="brand-feature-text">
                                Kelola pemesanan dengan mudah.
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
                        User Portal
                    </span>

                </div>

            </div>

        </section>


        {{-- =====================================================
             LOGIN FORM
        ====================================================== --}}

        <section class="login-form-side">

            <div class="login-card">

                {{-- Heading --}}
                <div class="login-heading">

                    <h1 class="login-title">
                        Selamat Datang 👋
                    </h1>

                    <p class="login-description">
                        Masuk ke akun Anda untuk melanjutkan perjalanan.
                    </p>

                </div>


                {{-- Error --}}
                @if ($errors->any())

                    <div class="error-message">

                        {{ $errors->first() }}

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('user.login.store') }}"
                >

                    @csrf


                    {{-- Email --}}
                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-input"
                            placeholder="Masukkan email Anda"
                            autocomplete="email"
                            required
                            autofocus
                        >

                    </div>


                    {{-- Password --}}
                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                        </label>

                        <div class="password-wrapper">

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
                                id="togglePassword"
                                class="password-toggle"
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
                                    stroke-width="2"
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

                    </div>


                    {{-- Ingat Saya + Lupa Password --}}
                    <div class="form-footer">

                        <label class="remember-me">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                            >
                            Ingat saya
                        </label>

                        <a
                            href="{{ route('password.request') }}"
                            class="forgot-password"
                        >
                            Lupa password?
                        </a>

                    </div>


                    {{-- Login --}}
                    <button
                        type="submit"
                        class="login-button"
                    >

                        Masuk sebagai User

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="17"
                            height="17"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                            />

                        </svg>

                    </button>

                </form>


                {{-- Register --}}
                <div class="divider">

                    <div class="divider-line"></div>

                    <span class="divider-text">
                        BELUM MEMILIKI AKUN?
                    </span>

                    <div class="divider-line"></div>

                </div>


                <a
                    href="{{ route('register') }}"
                    class="register-button"
                >
                    Buat Akun User
                </a>


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
