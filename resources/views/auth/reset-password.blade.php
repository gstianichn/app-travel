<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password | Travel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
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
            min-height: 100vh;
            color: #17221d;
        }

        .reset-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .reset-container {
            width: 100%;
            max-width: 1050px;
            min-height: 600px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10);
            border: 1px solid #edf1ef;
        }

        /* ================================
           LEFT SIDE
        ================================= */

        .reset-left {
            position: relative;
            background: linear-gradient(
                145deg,
                #087f52 0%,
                #0b8b5c 45%,
                #076f49 100%
            );
            color: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

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

        .brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 23px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 700;
        }

        .brand-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 2px;
        }

        .reset-left-body {
            position: relative;
            z-index: 2;
        }

        .reset-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 28px;
        }

        .reset-left h1 {
            font-size: clamp(32px, 3.5vw, 46px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }

        .reset-left p {
            font-size: 16px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            max-width: 400px;
        }

        .reset-left-footer {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* ================================
           RIGHT SIDE
        ================================= */

        .reset-right {
            padding: 60px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-card {
            width: 100%;
            max-width: 420px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #e5f4ed;
            color: #087f52;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .reset-card h2 {
            font-size: 28px;
            font-weight: 800;
            color: #17221d;
            letter-spacing: -0.025em;
            margin-bottom: 8px;
        }

        .reset-description {
            color: #718078;
            line-height: 1.65;
            margin-bottom: 28px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #29362f;
            margin-bottom: 8px;
        }

        .password-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            height: 50px;
            border: 1px solid #dce6e1;
            border-radius: 11px;
            padding: 0 48px 0 15px;
            font-size: 14px;
            outline: none;
            color: #17221d;
            background: white;
            transition: 0.2s ease;
        }

        .form-input:focus {
            border-color: #0c7950;
            box-shadow: 0 0 0 4px rgba(12, 121, 80, 0.08);
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
            padding: 0;
            display: flex;
            align-items: center;
        }

        .error-message {
            color: #c0392b;
            font-size: 12px;
            margin-top: 6px;
        }

        .form-error-box {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fff1f1;
            border: 1px solid #ffd6d6;
            color: #c0392b;
            font-size: 13px;
        }

        .success-message {
            margin-bottom: 20px;
            background: #e5f4ed;
            color: #166534;
            border: 1px solid #a7d7b8;
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 14px;
        }

        .reset-button {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 11px;
            background: #0c7950;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
        }

        .reset-button:hover {
            background: #096b47;
            transform: translateY(-1px);
        }

        .reset-button:active {
            transform: translateY(0);
        }

        .back-login {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: #718078;
        }

        .back-login a {
            color: #0c7950;
            font-weight: 700;
            text-decoration: none;
        }

        .back-login a:hover {
            text-decoration: underline;
        }

        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 800px) {
            .reset-container {
                grid-template-columns: 1fr;
            }

            .reset-left {
                display: none;
            }
        }

        @media (max-width: 500px) {
            .reset-wrapper {
                padding: 15px;
            }

            .reset-container {
                border-radius: 18px;
            }

            .reset-right {
                padding: 35px 25px;
            }
        }
    </style>
</head>

<body>

<div class="reset-wrapper">

    <div class="reset-container">

        {{-- ================================
             LEFT SIDE
        ================================= --}}
        <div class="reset-left">

            <div class="circle circle-one"></div>
            <div class="circle circle-two"></div>

            <div class="brand">
                <div class="brand-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h13l4 4v7H3V6z"/>
                        <circle cx="7.5" cy="17.5" r="2"/>
                        <circle cx="16.5" cy="17.5" r="2"/>
                        <path d="M16 6v4h4"/>
                        <path d="M7 10h4"/>
                    </svg>
                </div>
                <div>
                    <p class="brand-name">Travel</p>
                    <p class="brand-sub">Travel Management System</p>
                </div>
            </div>

            <div class="reset-left-body">

                <div class="reset-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>

                <h1>
                    Buat Password<br>Baru Anda
                </h1>

                <p>
                    Masukkan password baru Anda. Pastikan
                    password cukup kuat dan mudah diingat,
                    namun sulit ditebak oleh orang lain.
                </p>

            </div>

            <div class="reset-left-footer">
                <span>© {{ date('Y') }} Travel</span>
                <span>User Portal</span>
            </div>

        </div>


        {{-- ================================
             RIGHT SIDE
        ================================= --}}
        <div class="reset-right">

            <div class="reset-card">

                <span class="badge" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21 2-2 2m-1.5 1.5L14 9a5 5 0 1 0-5 5l8-8m0 0 2-2"/>
                        <circle cx="7.5" cy="16.5" r="2.5"/>
                    </svg>
                    <span>Reset Password</span>
                </span>

                <h2>Password Baru</h2>

                <p class="reset-description">
                    Masukkan password baru Anda di bawah ini.
                    Password harus minimal 8 karakter.
                </p>


                {{-- Status --}}
                @if (session('status'))
                    <div class="success-message">
                        {{ session('status') }}
                    </div>
                @endif


                {{-- Error --}}
                @if ($errors->any())
                    <div class="form-error-box">
                        {{ $errors->first() }}
                    </div>
                @endif


                {{-- Form --}}
                <form method="POST" action="{{ route('password.update') }}">

                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="form-group">

                        <label for="email" class="form-label">Email</label>

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
                            style="padding-right: 15px;"
                        >

                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror

                    </div>


                    {{-- Password --}}
                    <div class="form-group">

                        <label for="password" class="form-label">Password Baru</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
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
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>

                        </div>

                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror

                    </div>


                    {{-- Konfirmasi Password --}}
                    <div class="form-group">

                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Ulangi password baru"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                id="togglePasswordConfirm"
                                class="password-toggle"
                                aria-label="Tampilkan konfirmasi password"
                            >
                                <svg
                                    id="eyeIconConfirm"
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
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>

                        </div>

                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror

                    </div>


                    <button type="submit" class="reset-button">

                        Simpan Password Baru

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


                <div class="back-login">
                    Ingat password Anda?
                    <a href="{{ route('user.login') }}">Kembali ke Login</a>
                </div>

            </div>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        function setupToggle(buttonId, inputId, iconId) {
            const button = document.getElementById(buttonId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (!button || !input || !icon) {
                return;
            }

            button.addEventListener('click', function () {
                const hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';

                button.setAttribute(
                    'aria-label',
                    hidden ? 'Sembunyikan password' : 'Tampilkan password'
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
                        <circle cx="12" cy="12" r="3" />
                    `;
                }
            });
        }

        setupToggle('togglePassword', 'password', 'eyeIcon');
        setupToggle('togglePasswordConfirm', 'password_confirmation', 'eyeIconConfirm');

    });
</script>

</body>

</html>
