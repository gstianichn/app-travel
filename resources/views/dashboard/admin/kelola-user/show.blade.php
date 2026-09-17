<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail User - Admin Utama</title>

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
            max-width: 900px;
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

        .profile-header {
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #edf0f5;
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: 700;
        }

        .name {
            font-size: 21px;
            font-weight: 700;
            color: #111827;
        }

        .email {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 4px;
        }

        .body {
            padding: 30px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 17px;
        }

        .information {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid #edf0f5;
            border-radius: 12px;
            overflow: hidden;
        }

        .item {
            padding: 17px;
            border-bottom: 1px solid #edf0f5;
        }

        .item:nth-child(odd) {
            border-right: 1px solid #edf0f5;
        }

        .label {
            color: #9ca3af;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .value {
            color: #374151;
            font-size: 13px;
            font-weight: 600;
        }

        .status {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .active {
            background: #dcfce7;
            color: #15803d;
        }

        .inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .verified {
            color: #16a34a;
        }

        .unverified {
            color: #ea580c;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 25px;
        }

        .button {
            height: 40px;
            padding: 0 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .back {
            background: #f3f4f6;
            color: #4b5563;
        }

        .edit {
            background: #2563eb;
            color: white;
        }

        @media(max-width:600px) {

            .main {
                padding: 20px;
            }

            .profile-header {
                padding: 22px;
            }

            .body {
                padding: 22px;
            }

            .information {
                grid-template-columns: 1fr;
            }

            .item:nth-child(odd) {
                border-right: none;
            }

            .actions {
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

            Detail User

        </div>


        <div class="card">


            {{-- PROFILE HEADER --}}

            <div class="profile-header">

                <div class="avatar">

                    {{
                        strtoupper(
                            substr(
                                $user->name,
                                0,
                                1
                            )
                        )
                    }}

                </div>


                <div>

                    <div class="name">
                        {{ $user->name }}
                    </div>

                    <div class="email">
                        {{ $user->email }}
                    </div>

                </div>

            </div>


            {{-- BODY --}}

            <div class="body">

                <div class="section-title">
                    Informasi User
                </div>


                <div class="information">


                    <div class="item">

                        <div class="label">
                            Nama Lengkap
                        </div>

                        <div class="value">
                            {{ $user->name }}
                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Email
                        </div>

                        <div class="value">
                            {{ $user->email }}
                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Nomor Telepon
                        </div>

                        <div class="value">
                            {{ $user->phone ?? '-' }}
                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Role
                        </div>

                        <div class="value">
                            User / Pelanggan
                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Status Akun
                        </div>

                        <div class="value">

                            @if($user->status === 'active')

                                <span class="status active">
                                    Aktif
                                </span>

                            @else

                                <span class="status inactive">
                                    Nonaktif
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Status Verifikasi
                        </div>

                        <div class="value">

                            @if($user->email_verified_at)

                                <span class="verified">
                                    ✓ Terverifikasi
                                </span>

                            @else

                                <span class="unverified">
                                    ! Belum Terverifikasi
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Terdaftar Pada
                        </div>

                        <div class="value">

                            {{
                                $user->created_at
                                    ? $user->created_at->format('d F Y, H:i')
                                    : '-'
                            }}

                        </div>

                    </div>


                    <div class="item">

                        <div class="label">
                            Terakhir Diperbarui
                        </div>

                        <div class="value">

                            {{
                                $user->updated_at
                                    ? $user->updated_at->format('d F Y, H:i')
                                    : '-'
                            }}

                        </div>

                    </div>

                </div>


                {{-- ACTION --}}

                <div class="actions">

                    <a
                        href="{{ route('dashboard.admin.kelola-user.index') }}"
                        class="button back"
                    >
                        ← Kembali
                    </a>


                    <a
                        href="{{ route('dashboard.admin.kelola-user.edit', $user->id) }}"
                        class="button edit"
                    >
                        ✏ Edit User
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

</body>

</html>