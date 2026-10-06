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
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6a2 2 0 0 1 2-2h9.5a2 2 0 0 1 1.6.8l3.4 4.5A2 2 0 0 1 20 10.5V16a2 2 0 0 1-2 2h-1"/>
                            <path d="M3 6v10a2 2 0 0 0 2 2h1"/>
                            <circle cx="8" cy="18" r="2"/>
                            <path d="M10 18h4"/>
                            <circle cx="16" cy="18" r="2"/>
                            <path d="M15 6v5h5"/>
                            <path d="M3 11h12"/>
                            <path d="M7 6v5"/>
                            <path d="M11 6v5"/>
                        </svg>
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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/>
                                </svg>
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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                                </svg>
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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6a2 2 0 0 1 2-2h9.5a2 2 0 0 1 1.6.8l3.4 4.5A2 2 0 0 1 20 10.5V16a2 2 0 0 1-2 2h-1"/>
                            <path d="M3 6v10a2 2 0 0 0 2 2h1"/>
                            <circle cx="8" cy="18" r="2"/>
                            <path d="M10 18h4"/>
                            <circle cx="16" cy="18" r="2"/>
                            <path d="M15 6v5h5"/>
                            <path d="M3 11h12"/>
                            <path d="M7 6v5"/>
                            <path d="M11 6v5"/>
                        </svg>
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
                        Selamat Datang
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="7.5" cy="15.5" r="5.5"/>
                                        <path d="m21 2-9.6 9.6"/>
                                        <path d="m15.5 7.5 3 3L22 7l-2-2"/>
                                    </svg>
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
