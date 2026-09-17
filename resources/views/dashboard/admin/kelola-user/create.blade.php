<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah User - Admin Utama</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #087f55;
            --primary-dark: #066b47;
            --primary-light: #e3f5ee;
            --accent: #066b47;
            --accent-light: #e8f8fc;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .main {
            min-height: 100vh;
            padding: 35px;
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        .breadcrumb {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
        }

        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--accent-light);
            overflow: hidden; 
        }

        .card-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--primary-dark);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .card-description {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 12px;
        }

        .form {
            padding: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .required {
            color: #dc2626;
        }

        .input {
            width: 100%;
            height: 45px;
            padding: 0 13px;
            border: 1px solid #dfe3eb;
            border-radius: 8px;
            outline: none;
            font-size: 13px;
        }

        .input:focus {
            border-color: var(--accent);
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 11px;
        }

        .info-box {
            padding: 14px;
            border-radius: 9px;
            background: #eff6ff;
            color: var(--accent);
            font-size: 11px;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .button {
            height: 42px;
            padding: 0 18px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .button-cancel {
            background: #f3f4f6;
            color: #4b5563;
        }

        .button-save {
            background: var(--primary);
            color: white;
        }

        .button-save:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 600px) {

            .main {
                padding: 20px;
            }

            .form {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }

        }

    </style>

</head>

<body>

<main class="main">

    <div class="container">

        <div class="breadcrumb">

            <a href="{{ route('dashboard.admin.kelola-user.index') }}">
                Kelola User
            </a>

            &nbsp;/&nbsp;

            Tambah User

        </div>


        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Tambah User
                </div>

                <div class="card-description">
                    Tambahkan akun pelanggan baru ke sistem TravelKu.
                </div>

            </div>


            <form
                action="{{ route('dashboard.admin.kelola-user.store') }}"
                method="POST"
                class="form"
            >

                @csrf


                <div class="info-box">

                    Akun yang dibuat melalui halaman ini akan otomatis
                    memiliki role <strong>User</strong> dan status
                    <strong>Aktif</strong>.

                </div>


                {{-- NAMA --}}

                <div class="form-group">

                    <label class="form-label">
                        Nama Lengkap
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="input"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                    @error('name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- EMAIL --}}

                <div class="form-group">

                    <label class="form-label">
                        Email
                        <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="input"
                        value="{{ old('email') }}"
                        placeholder="contoh@email.com"
                        required
                    >

                    @error('email')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- TELEPON --}}

                <div class="form-group">

                    <label class="form-label">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="input"
                        value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                    >

                    @error('phone')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- PASSWORD --}}

                <div class="form-group">

                    <label class="form-label">
                        Password
                        <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        placeholder="Minimal 8 karakter"
                        required
                    >

                    @error('password')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- CONFIRM PASSWORD --}}

                <div class="form-group">

                    <label class="form-label">
                        Konfirmasi Password
                        <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="input"
                        placeholder="Ulangi password"
                        required
                    >

                </div>


                <div class="buttons">

                    <a
                        href="{{ route('dashboard.admin.kelola-user.index') }}"
                        class="button button-cancel"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="button button-save"
                    >
                        Simpan User
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

</body>

</html>