<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola User - Admin Utama</title>

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

        /* =========================
           SIDEBAR
        ========================== */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 25px 16px;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 10px 25px;
            border-bottom: 1px solid #edf0f5;
            margin-bottom: 22px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: #2563eb;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .brand-text {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        .admin-label {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .menu-title {
            font-size: 10px;
            color: #9ca3af;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 0 12px;
            margin-bottom: 9px;
        }

        .menu {
            list-style: none;
        }

        .menu li {
            margin-bottom: 5px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 9px;
            color: #4b5563;
            text-decoration: none;
            font-size: 13px;
            transition: .2s;
        }

        .menu a:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .menu a.active {
            background: #2563eb;
            color: white;
            font-weight: 600;
        }

        .menu-icon {
            width: 21px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-bottom {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 20px;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            border: none;
            border-radius: 9px;
            background: #fef2f2;
            color: #dc2626;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: #fee2e2;
        }

        /* =========================
           MAIN
        ========================== */

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* =========================
           TOPBAR
        ========================== */

        .topbar {
            height: 72px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .page-subtitle {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .notification {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 9px;
            padding-left: 14px;
            border-left: 1px solid #e5e7eb;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .profile-name {
            font-size: 12px;
            font-weight: 600;
        }

        .profile-role {
            font-size: 10px;
            color: #9ca3af;
        }

        /* =========================
           CONTENT
        ========================== */

        .content {
            padding: 30px 35px 45px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #9ca3af;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .breadcrumb span:last-child {
            color: #2563eb;
            font-weight: 600;
        }

        /* =========================
           STATISTICS
        ========================== */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-label {
            font-size: 11px;
            color: #9ca3af;
            margin-bottom: 7px;
        }

        .stat-number {
            font-size: 25px;
            font-weight: 700;
            color: #111827;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .red {
            background: #fef2f2;
            color: #dc2626;
        }

        /* =========================
           USER CARD
        ========================== */

        .user-container {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 16px;
            overflow: hidden;
        }

        .user-header {
            padding: 22px 24px;
            border-bottom: 1px solid #edf0f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .header-title {
            font-size: 17px;
            font-weight: 700;
        }

        .header-description {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .add-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .add-button:hover {
            background: #1d4ed8;
        }

        /* =========================
           FILTER
        ========================== */

        .filter-area {
            padding: 20px 24px;
            background: #fafbfc;
            border-bottom: 1px solid #edf0f5;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 180px 180px auto;
            gap: 12px;
        }

        .search-input,
        .filter-select {
            height: 42px;
            border: 1px solid #dfe3eb;
            border-radius: 8px;
            padding: 0 12px;
            font-size: 12px;
            outline: none;
            background: white;
        }

        .search-input:focus,
        .filter-select:focus {
            border-color: #2563eb;
        }

        .filter-button {
            height: 42px;
            padding: 0 18px;
            border: none;
            border-radius: 8px;
            background: #374151;
            color: white;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .reset-button {
            height: 42px;
            padding: 0 15px;
            border-radius: 8px;
            border: 1px solid #dfe3eb;
            background: white;
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 12px;
        }

        /* =========================
           TABLE
        ========================== */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #fafbfc;
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-align: left;
            padding: 13px 20px;
            border-bottom: 1px solid #edf0f5;
            text-transform: uppercase;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f2f5;
            font-size: 12px;
            color: #374151;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-table-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .user-table-name {
            font-weight: 600;
            color: #111827;
        }

        .user-table-email {
            color: #9ca3af;
            font-size: 10px;
            margin-top: 2px;
        }

        .status {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .verified {
            color: #16a34a;
            font-size: 11px;
            font-weight: 600;
        }

        .not-verified {
            color: #ea580c;
            font-size: 11px;
            font-weight: 600;
        }

        /* =========================
           ACTION
        ========================== */

        .actions {
            display: flex;
            gap: 6px;
        }

        .action-button {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .action-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .action-edit {
            background: #f3f4f6;
            color: #374151;
        }

        .action-toggle {
            background: #fff7ed;
            color: #ea580c;
        }

        .action-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .action-button:hover {
            opacity: .75;
        }

        /* =========================
           EMPTY
        ========================== */

        .empty {
            text-align: center;
            padding: 55px 20px;
            color: #9ca3af;
        }

        .empty-icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .empty-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .empty-text {
            font-size: 11px;
        }

        /* =========================
           PAGINATION
        ========================== */

        .pagination-area {
            padding: 17px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pagination-info {
            color: #9ca3af;
            font-size: 11px;
        }

        .pagination {
            display: flex;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 11px;
            color: #6b7280;
            background: white;
        }

        .pagination .active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 800px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .brand-text,
            .admin-label,
            .menu-title,
            .menu a span:not(.menu-icon),
            .logout-btn span {
                display: none;
            }

            .brand {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            .menu a {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 25px 20px;
            }

            .user-header {
                align-items: flex-start;
                flex-direction: column;
            }

        }

        @media (max-width: 600px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .pagination-area {
                flex-direction: column;
                gap: 15px;
            }

            .profile-name,
            .profile-role {
                display: none;
            }

        }
    </style>
</head>

<body>

    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                ✈
            </div>

            <div>
                <div class="brand-text">
                    TravelKu
                </div>

                <div class="admin-label">
                    ADMIN UTAMA
                </div>
            </div>

        </div>


        <div class="menu-title">
            Menu Utama
        </div>

        <ul class="menu">

            <li>
                <a href="{{ route('dashboard.admin.index') }}">
                    <span class="menu-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="active">
                    <span class="menu-icon">👥</span>
                    <span>Kelola User</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.staff.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">👨‍💼</span>
                    <span>Kelola Staff</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.driver.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">🚐</span>
                    <span>Kelola Driver</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.vehicle.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">🚙</span>
                    <span>Kendaraan</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.route.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">🛣️</span>
                    <span>Rute & Jadwal</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.booking.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">🎫</span>
                    <span>Booking</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.payment.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">💳</span>
                    <span>Pembayaran</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.refund.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">💰</span>
                    <span>Refund</span>
                </a>
            </li>

            <li>
                {{-- <a href="{{ route('admin.promo.index') }}"> --}}
                <a href="#">
                    <span class="menu-icon">🎁</span>
                    <span>Promo</span>
                </a>
            </li>

        </ul>


        <div class="sidebar-bottom">

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit" class="logout-btn">
                    <span>🚪</span>
                    <span>Logout</span>
                </button>

            </form>

        </div>

    </aside>


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="main">


        {{-- TOPBAR --}}

        <header class="topbar">

            <div>

                <div class="page-title">
                    Kelola User
                </div>

                <div class="page-subtitle">
                    Kelola seluruh akun pelanggan TravelKu
                </div>

            </div>


            <div class="topbar-right">

                <a
                    {{-- href="{{ route('admin.notifications') }}" --}}
                    href="#"
                    class="notification"
                >
                    🔔

                    <span class="notification-badge">
                        3
                    </span>
                </a>


                <div class="profile">

                    <div class="profile-avatar">

                        {{
                            strtoupper(
                                substr(
                                    auth()->user()->name ?? 'A',
                                    0,
                                    1
                                )
                            )
                        }}

                    </div>

                    <div>

                        <div class="profile-name">
                            {{ auth()->user()->name ?? 'Admin Utama' }}
                        </div>

                        <div class="profile-role">
                            Administrator
                        </div>

                    </div>

                </div>

            </div>

        </header>


        {{-- CONTENT --}}

        <div class="content">


            {{-- BREADCRUMB --}}

            <div class="breadcrumb">

                <span>
                    Dashboard
                </span>

                <span>
                    /
                </span>

                <span>
                    Kelola User
                </span>

            </div>


            {{-- STATISTICS --}}

            <div class="stats">

                <div class="stat-card">

                    <div>

                        <div class="stat-label">
                            Total User
                        </div>

                        <div class="stat-number">
                            {{ $totalUsers ?? 0 }}
                        </div>

                    </div>

                    <div class="stat-icon blue">
                        👥
                    </div>

                </div>


                <div class="stat-card">

                    <div>

                        <div class="stat-label">
                            User Aktif
                        </div>

                        <div class="stat-number">
                            {{ $activeUsers ?? 0 }}
                        </div>

                    </div>

                    <div class="stat-icon green">
                        ✓
                    </div>

                </div>


                <div class="stat-card">

                    <div>

                        <div class="stat-label">
                            Belum Terverifikasi
                        </div>

                        <div class="stat-number">
                            {{ $unverifiedUsers ?? 0 }}
                        </div>

                    </div>

                    <div class="stat-icon orange">
                        ⏳
                    </div>

                </div>


                <div class="stat-card">

                    <div>

                        <div class="stat-label">
                            User Nonaktif
                        </div>

                        <div class="stat-number">
                            {{ $inactiveUsers ?? 0 }}
                        </div>

                    </div>

                    <div class="stat-icon red">
                        !
                    </div>

                </div>

            </div>


            {{-- USER MANAGEMENT --}}

            <div class="user-container">


                {{-- HEADER --}}

                <div class="user-header">

                    <div>

                        <div class="header-title">
                            Daftar User
                        </div>

                        <div class="header-description">
                            Lihat dan kelola akun pelanggan
                        </div>

                    </div>


                    <a
                        href="{{ route('dashboard.admin.kelola-user.create') }}"
                        class="add-button"
                    >
                        <span>+</span>
                        Tambah User
                    </a>

                </div>


                {{-- FILTER --}}

                <div class="filter-area">

                    <form
                        action="{{ route('dashboard.admin.kelola-user.index') }}"
                        method="GET"
                        class="filter-form"
                    >

                        <input
                            type="text"
                            name="search"
                            class="search-input"
                            placeholder="Cari nama, email, atau nomor telepon..."
                            value="{{ request('search') }}"
                        >


                        <select
                            name="status"
                            class="filter-select"
                        >

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="active"
                                {{ request('status') === 'active' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="inactive"
                                {{ request('status') === 'inactive' ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>

                        </select>


                        <select
                            name="verification"
                            class="filter-select"
                        >

                            <option value="">
                                Semua Verifikasi
                            </option>

                            <option
                                value="verified"
                                {{ request('verification') === 'verified' ? 'selected' : '' }}
                            >
                                Terverifikasi
                            </option>

                            <option
                                value="unverified"
                                {{ request('verification') === 'unverified' ? 'selected' : '' }}
                            >
                                Belum Terverifikasi
                            </option>

                        </select>


                        <div style="display:flex; gap:7px;">

                            <button
                                type="submit"
                                class="filter-button"
                            >
                                Cari
                            </button>

                            <a
                                href="{{ route('dashboard.admin.kelola-user.index') }}"
                                class="reset-button"
                            >
                                Reset
                            </a>

                        </div>

                    </form>

                </div>


                {{-- TABLE --}}

                <div class="table-wrapper">

                    @if(isset($users) && $users->count())

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        User
                                    </th>

                                    <th>
                                        No. Telepon
                                    </th>

                                    <th>
                                        Verifikasi
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Bergabung
                                    </th>

                                    <th>
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($users as $user)

                                    <tr>

                                        {{-- USER --}}

                                        <td>

                                            <div class="user-cell">

                                                <div class="user-table-avatar">

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

                                                    <div class="user-table-name">
                                                        {{ $user->name }}
                                                    </div>

                                                    <div class="user-table-email">
                                                        {{ $user->email }}
                                                    </div>

                                                </div>

                                            </div>

                                        </td>


                                        {{-- PHONE --}}

                                        <td>

                                            {{ $user->phone ?? '-' }}

                                        </td>


                                        {{-- VERIFICATION --}}

                                        <td>

                                            @if(
                                                isset($user->email_verified_at)
                                                && $user->email_verified_at
                                            )

                                                <span class="verified">
                                                    ✓ Terverifikasi
                                                </span>

                                            @elseif(
                                                isset($user->otp_verified)
                                                && $user->otp_verified
                                            )

                                                <span class="verified">
                                                    ✓ Terverifikasi
                                                </span>

                                            @else

                                                <span class="not-verified">
                                                    ! Belum Verifikasi
                                                </span>

                                            @endif

                                        </td>


                                        {{-- STATUS --}}

                                        <td>

                                            @if(
                                                isset($user->status)
                                                && $user->status === 'inactive'
                                            )

                                                <span class="status status-inactive">
                                                    Nonaktif
                                                </span>

                                            @else

                                                <span class="status status-active">
                                                    Aktif
                                                </span>

                                            @endif

                                        </td>


                                        {{-- DATE --}}

                                        <td>

                                            {{
                                                $user->created_at
                                                    ? $user->created_at->format('d M Y')
                                                    : '-'
                                            }}

                                        </td>


                                        {{-- ACTION --}}

                                        <td>

                                            <div class="actions">


                                                {{-- VIEW --}}

                                                <a
                                                    href="{{ route('dashboard.admin.kelola-user.show', $user->id) }}"
                                                    class="action-button action-view"
                                                    title="Lihat Detail"
                                                >
                                                    👁
                                                </a>


                                                {{-- EDIT --}}

                                                <a
                                                    href="{{ route('dashboard.admin.kelola-user.edit', $user->id) }}"
                                                    class="action-button action-edit"
                                                    title="Edit User"
                                                >
                                                    ✏
                                                </a>


                                                {{-- ACTIVATE / DEACTIVATE --}}

                                                <form
                                                    action="{{ route('dashboard.admin.kelola-user.toggle-status', $user->id) }}"
                                                    method="POST"
                                                    style="display:inline;"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="action-button action-toggle"
                                                        title="Ubah Status"
                                                    >
                                                        {{ isset($user->status) && $user->status === 'inactive' ? '✓' : '⏸' }}
                                                    </button>

                                                </form>


                                                {{-- DELETE --}}

                                                <form
                                                    action="{{ route('dashboard.admin.kelola-user.destroy', $user->id) }}"
                                                    method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="action-button action-delete"
                                                        title="Hapus User"
                                                    >
                                                        🗑
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>


                        {{-- PAGINATION --}}

                        <div class="pagination-area">

                            <div class="pagination-info">

                                Menampilkan
                                {{ $users->firstItem() ?? 0 }}
                                -
                                {{ $users->lastItem() ?? 0 }}
                                dari
                                {{ $users->total() ?? 0 }}
                                user

                            </div>


                            <div class="pagination">

                                {{ $users->withQueryString()->links() }}

                            </div>

                        </div>


                    @else

                        <div class="empty">

                            <div class="empty-icon">
                                👥
                            </div>

                            <div class="empty-title">
                                Belum ada user
                            </div>

                            <div class="empty-text">
                                Data user yang terdaftar akan muncul di sini.
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </main>


</body>

</html>