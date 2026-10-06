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

    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h13l4 4v7H3V6z"/>
                        <circle cx="7.5" cy="17.5" r="2"/>
                        <circle cx="16.5" cy="17.5" r="2"/>
                        <path d="M16 6v4h4"/>
                        <path d="M7 10h4"/>
                    </svg>
                </div>

                <div class="register-logo-text">
                    Travel
                </div>

            </div>


            {{-- Content --}}
            <div class="register-left-content">

                <div class="register-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                        <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                    </svg>
                    <span>Mulai perjalanan Anda</span>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h13l4 4v7H3V6z"/>
                                <circle cx="7.5" cy="17.5" r="2"/>
                                <circle cx="16.5" cy="17.5" r="2"/>
                                <path d="M16 6v4h4"/>
                                <path d="M7 10h4"/>
                            </svg>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h13l4 4v7H3V6z"/>
                            <circle cx="7.5" cy="17.5" r="2"/>
                            <circle cx="16.5" cy="17.5" r="2"/>
                            <path d="M16 6v4h4"/>
                            <path d="M7 10h4"/>
                        </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
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
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        <path d="m9 16 2 2 4-4"/>
                                    </svg>
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