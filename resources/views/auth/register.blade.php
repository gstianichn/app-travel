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