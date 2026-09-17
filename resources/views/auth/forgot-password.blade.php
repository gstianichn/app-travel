<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lupa Password - TravelKu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
        }

        .forgot-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .forgot-container {
            width: 100%;
            max-width: 1050px;
            min-height: 600px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }

        /* ================================
           LEFT SIDE
        ================================= */

        .forgot-left {
            background: linear-gradient(
                135deg,
                #16a34a,
                #15803d
            );

            color: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 30px;
        }

        .forgot-left h1 {
            font-size: 42px;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .forgot-left p {
            font-size: 16px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.9);
            max-width: 430px;
        }

        .forgot-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 30px;
        }

        /* ================================
           RIGHT SIDE
        ================================= */

        .forgot-right {
            padding: 60px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forgot-card {
            width: 100%;
            max-width: 420px;
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            background: #dcfce7;
            color: #15803d;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .forgot-card h2 {
            font-size: 30px;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .forgot-description {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            height: 52px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 15px 0 45px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }

        .forgot-button {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 12px;
            background: #16a34a;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .forgot-button:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .back-login {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }

        .back-login a {
            color: #16a34a;
            font-weight: 700;
            text-decoration: none;
        }

        .back-login a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 7px;
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 800px) {
            .forgot-container {
                grid-template-columns: 1fr;
            }

            .forgot-left {
                padding: 40px 30px;
                min-height: 300px;
            }

            .forgot-left h1 {
                font-size: 32px;
            }

            .forgot-right {
                padding: 40px 30px;
            }
        }

        @media (max-width: 500px) {
            .forgot-wrapper {
                padding: 15px;
            }

            .forgot-container {
                border-radius: 18px;
            }

            .forgot-left {
                padding: 35px 25px;
            }

            .forgot-right {
                padding: 35px 25px;
            }

            .forgot-left h1 {
                font-size: 28px;
            }

            .forgot-card h2 {
                font-size: 25px;
            }
        }
    </style>
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