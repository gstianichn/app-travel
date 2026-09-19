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

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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

</body>

</html>
