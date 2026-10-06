<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lupa Password - TravelKu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
</head>

<body>

<div class="forgot-wrapper">

    <div class="forgot-container">

        {{-- ================================
             LEFT SIDE
        ================================= --}}
        <div class="forgot-left">

            <div class="brand" style="display: flex; align-items: center; gap: 10px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h13l4 4v7H3V6z"/>
                    <circle cx="7.5" cy="17.5" r="2"/>
                    <circle cx="16.5" cy="17.5" r="2"/>
                    <path d="M16 6v4h4"/>
                    <path d="M7 10h4"/>
                </svg>
                <span>TravelKu</span>
            </div>

            <div class="forgot-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h1>
                Lupa Password?
            </h1>

            <p>
                Jangan khawatir. Masukkan email yang terdaftar
                pada akun Anda dan kami akan membantu Anda
                untuk mengatur ulang password.
            </p>

        </div>


        {{-- ================================
             RIGHT SIDE
        ================================= --}}
        <div class="forgot-right">

            <div class="forgot-card">

                <span class="badge" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21 2-2 2m-1.5 1.5L14 9a5 5 0 1 0-5 5l8-8m0 0 2-2"/>
                        <circle cx="7.5" cy="16.5" r="2.5"/>
                    </svg>
                    <span>Reset Password</span>
                </span>

                <h2>
                    Lupa Password
                </h2>

                <p class="forgot-description">
                    Masukkan alamat email yang Anda gunakan
                    saat mendaftar. Kami akan mengirimkan
                    instruksi untuk membuat password baru.
                </p>


                {{-- Success Message --}}
                @if (session('status'))
                    <div class="success-message">
                        {{ session('status') }}
                    </div>
                @endif


                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                >

                    @csrf

                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <div class="input-wrapper">

                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control"
                                placeholder="Masukkan email Anda"
                                required
                                autofocus
                            >

                        </div>

                        @error('email')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="forgot-button"
                    >
                        Kirim Link Reset Password
                    </button>

                </form>


                {{-- Back to Login --}}
                <div class="back-login">

                    Ingat password Anda?

                    <a href="{{ route('user.login') }}">
                        Kembali ke Login
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>