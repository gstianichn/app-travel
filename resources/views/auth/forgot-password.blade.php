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

            <div class="brand">
                TravelKu
            </div>

            <div class="forgot-icon">
                🔐
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

                <span class="badge">
                    🔑 Reset Password
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
                                ✉
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