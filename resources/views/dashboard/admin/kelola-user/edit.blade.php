<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit User - Admin Utama</title>

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
            color: #2563eb;
            text-decoration: none;
        }

        .card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 16px;
            overflow: hidden;
        }

        .header {
            padding: 24px 28px;
            border-bottom: 1px solid #edf0f5;
        }

        .title {
            font-size: 20px;
            font-weight: 700;
        }

        .description {
            margin-top: 5px;
            font-size: 12px;
            color: #9ca3af;
        }

        .form {
            padding: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .input,
        .select {
            width: 100%;
            height: 45px;
            border: 1px solid #dfe3eb;
            border-radius: 8px;
            padding: 0 13px;
            font-size: 13px;
            outline: none;
            background: white;
        }

        .input:focus,
        .select:focus {
            border-color: #2563eb;
        }

        .help {
            margin-top: 6px;
            font-size: 10px;
            color: #9ca3af;
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 11px;
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
            border: none;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cancel {
            background: #f3f4f6;
            color: #4b5563;
        }

        .save {
            background: #2563eb;
            color: white;
        }

        .save:hover {
            background: #1d4ed8;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin: 28px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #edf0f5;
        }

        @media(max-width:600px) {

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

            Edit User

        </div>


        <div class="card">

            <div class="header">

                <div class="title">
                    Edit User
                </div>

                <div class="description">
                    Perbarui informasi akun pelanggan.
                </div>

            </div>


            <form
                action="{{ route('dashboard.admin.kelola-user.update', $user->id) }}"
                method="POST"
                class="form"
            >

                @csrf
                @method('PUT')


                {{-- DATA AKUN --}}

                <div class="section-title">
                    Informasi Akun
                </div>


                <div class="form-group">

                    <label>
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="input"
                        value="{{ old('name', $user->name) }}"
                        required
                    >

                    @error('name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="input"
                        value="{{ old('email', $user->email) }}"
                        required
                    >

                    @error('email')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="form-group">

                    <label>
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="input"
                        value="{{ old('phone', $user->phone) }}"
                    >

                    @error('phone')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- STATUS --}}

                <div class="form-group">

                    <label>
                        Status Akun
                    </label>

                    <select
                        name="status"
                        class="select"
                        required
                    >

                        <option
                            value="active"
                            {{ old('status', $user->status) === 'active' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}
                        >
                            Nonaktif
                        </option>

                    </select>

                    @error('status')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- PASSWORD --}}

                <div class="section-title">
                    Ubah Password
                </div>


                <div class="form-group">

                    <label>
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        placeholder="Kosongkan jika tidak ingin mengubah"
                    >

                    <div class="help">
                        Password minimal 8 karakter.
                    </div>

                    @error('password')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="form-group">

                    <label>
                        Konfirmasi Password Baru
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="input"
                        placeholder="Ulangi password baru"
                    >

                </div>


                <div class="buttons">

                    <a
                        href="{{ route('dashboard.admin.kelola-user.index') }}"
                        class="button cancel"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="button save"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

</body>

</html>