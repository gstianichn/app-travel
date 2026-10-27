@extends('layouts.admin')

@section('title', 'Kelola User')

@section('page-title', 'Kelola User')

@section('page-subtitle', 'Kelola akun pengguna, status aktivasi, dan data pelanggan TravelKu.')

@section('content')

<style>
    .kelola-user-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* =========================================
       PAGE HEADER & BREADCRUMB
    ========================================== */
    .page-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #7b8882;
        margin-bottom: 6px;
    }

    .breadcrumb-nav a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }

    .header-text h1 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 700;
        color: #17221d;
    }

    .header-text p {
        margin: 0;
        font-size: 13px;
        color: #6a7972;
    }

    .btn-create-user {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        border-radius: 11px;
        background: #087f55;
        color: white;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(8, 127, 85, 0.22);
        transition: all 0.2s ease;
    }

    .btn-create-user:hover {
        background: #066b47;
        box-shadow: 0 6px 18px rgba(8, 127, 85, 0.30);
        transform: translateY(-1px);
    }

    /* =========================================
       FLASH ALERTS
    ========================================== */
    .alert-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    /* =========================================
       STATISTICS CARDS
    ========================================== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .stat-box {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .stat-box-info .stat-box-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #72817a;
        margin-bottom: 5px;
    }

    .stat-box-info .stat-box-num {
        font-size: 24px;
        font-weight: 800;
        color: #17221d;
        line-height: 1.1;
    }

    .stat-box-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-box-icon.total {
        background: #e3f5ee;
        color: #087f55;
    }

    .stat-box-icon.active {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-box-icon.unverified {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-box-icon.inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    /* =========================================
       FILTER CARD
    ========================================== */
    .filter-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr auto;
        gap: 14px;
        align-items: center;
    }

    .filter-input-wrap {
        position: relative;
    }

    .filter-input-wrap .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #8c9993;
        font-size: 14px;
        pointer-events: none;
    }

    .filter-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        background: #fcfdfd;
        color: #1f2a24;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .filter-input:focus {
        border-color: #087f55;
        background: white;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        background: #fcfdfd;
        color: #1f2a24;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .filter-select:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .filter-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter-submit {
        padding: 10px 18px;
        border: none;
        border-radius: 10px;
        background: #087f55;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
        white-space: nowrap;
    }

    .btn-filter-submit:hover {
        background: #066b47;
    }

    .btn-filter-reset {
        padding: 10px 14px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        background: white;
        color: #66756e;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .btn-filter-reset:hover {
        background: #f4f7f5;
        color: #1f2a24;
    }

    /* =========================================
       USER TABLE CARD
    ========================================== */
    .table-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .table-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #edf2ef;
    }

    .table-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
    }

    .table-card-count {
        padding: 4px 10px;
        border-radius: 14px;
        background: #e3f5ee;
        color: #087f55;
        font-size: 11px;
        font-weight: 700;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .user-main-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .user-main-table th {
        padding: 13px 20px;
        background: #fbfdfc;
        color: #72817a;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf1ef;
    }

    .user-main-table td {
        padding: 15px 20px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .user-main-table tbody tr:last-child td {
        border-bottom: none;
    }

    .user-main-table tbody tr {
        transition: background 0.15s ease;
    }

    .user-main-table tbody tr:hover {
        background: #fbfdfc;
    }

    /* USER CELL */
    .user-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-badge {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        border: 1px solid #cce8dc;
    }

    .user-profile-meta .user-name {
        font-weight: 600;
        color: #17221d;
        margin-bottom: 2px;
    }

    .user-profile-meta .user-email {
        font-size: 11px;
        color: #76857e;
    }

    /* BADGES */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .verify-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
    }

    .verify-badge.verified {
        color: #087f55;
    }

    .verify-badge.unverified {
        color: #d97706;
    }

    /* ACTION BUTTONS */
    .action-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.15s ease;
    }

    .action-btn.view {
        background: #e3f5ee;
        color: #087f55;
    }

    .action-btn.view:hover {
        background: #087f55;
        color: white;
    }

    .action-btn.edit {
        background: #fef3c7;
        color: #b45309;
    }

    .action-btn.edit:hover {
        background: #d97706;
        color: white;
    }

    .action-btn.toggle {
        background: #e0f2fe;
        color: #0369a1;
    }

    .action-btn.toggle:hover {
        background: #0284c7;
        color: white;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .action-btn.delete:hover {
        background: #dc2626;
        color: white;
    }

    /* PAGINATION */
    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 22px;
        border-top: 1px solid #edf2ef;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-info {
        font-size: 12px;
        color: #72817a;
    }

    /* EMPTY STATE */
    .empty-state-box {
        padding: 60px 20px;
        text-align: center;
        color: #82918a;
    }

    .empty-state-icon {
        font-size: 42px;
        margin-bottom: 12px;
    }

    .empty-state-box h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 700;
        color: #17221d;
    }

    .empty-state-box p {
        margin: 0 0 16px;
        font-size: 13px;
        color: #76857e;
    }

    /* =========================================
       RESPONSIVE
    ========================================== */
    @media (max-width: 1050px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }

        .filter-btn-group {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 650px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .filter-form-grid {
            grid-template-columns: 1fr;
        }

        .page-header-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-create-user {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="kelola-user-page">

    {{-- =========================================
         PAGE HEADER
    ========================================== --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>/</span>
                <span>Kelola User</span>
            </div>
            <div class="header-text">
                <h1>Daftar Pengguna Sistem</h1>
                <p>Lihat dan kelola akun seluruh pelanggan yang terdaftar pada sistem TravelKu.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.admin.kelola-user.create') }}" class="btn-create-user">
            <span>+</span>
            <span>Tambah User Baru</span>
        </a>
    </div>


    {{-- =========================================
         FLASH NOTIFICATIONS
    ========================================== --}}
    @if(session('success'))
        <div class="alert-banner alert-success">
            <span></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-banner alert-error">
            <span></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif


    {{-- =========================================
         STATISTIK KELOLA USER
    ========================================== --}}
    <div class="stats-row">

        {{-- TOTAL USER --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total User</div>
                <div class="stat-box-num">{{ number_format($totalUsers ?? 0) }}</div>
            </div>
        </div>

        {{-- USER AKTIF --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">User Aktif</div>
                <div class="stat-box-num">{{ number_format($activeUsers ?? 0) }}</div>
            </div>
        </div>

        {{-- BELUM TERVERIFIKASI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Belum Verifikasi</div>
                <div class="stat-box-num">{{ number_format($unverifiedUsers ?? 0) }}</div>
            </div>
            <div class="stat-box-icon unverified">
                ⏳
            </div>
        </div>

        {{-- NONAKTIF --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">User Nonaktif</div>
                <div class="stat-box-num">{{ number_format($inactiveUsers ?? 0) }}</div>
            </div>
        </div>

    </div>


    {{-- =========================================
         FILTER & PENCARIAN
    ========================================== --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.kelola-user.index') }}" method="GET" class="filter-form-grid admin-filter-form">

            <div class="filter-input-wrap">
                <span class="input-icon"></span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    placeholder="Cari nama, email, atau nomor telepon..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <div>
                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div>
                <select name="verification" class="filter-select">
                    <option value="">Semua Verifikasi</option>
                    <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>Belum Verifikasi</option>
                </select>
            </div>

            <div class="filter-btn-group">
                <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="btn-filter-reset">
                    Reset
                </a>
            </div>

        </form>
    </div>


    {{-- =========================================
         TABEL USER
    ========================================== --}}
    <div class="table-card">

        <div class="table-card-top">
            <div class="table-card-title">
                Data Pengguna
            </div>
            <div class="table-card-count">
                {{ isset($users) && method_exists($users, 'total') ? $users->total() : (isset($users) ? $users->count() : 0) }} Pengguna
            </div>
        </div>

        <div class="table-responsive">
            @if(isset($users) && $users->count() > 0)
                <table class="user-main-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Pengguna</th>
                            <th>Kontak Telepon</th>
                            <th>Status Akun</th>
                            <th>Verifikasi Email</th>
                            <th>Terdaftar</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $u)
                            <tr>
                                <td>
                                    <span style="color: #92a099; font-size: 12px; font-weight: 600;">
                                        {{ (method_exists($users, 'firstItem') && $users->firstItem()) ? $users->firstItem() + $index : $index + 1 }}
                                    </span>
                                </td>

                                <td>
                                    <div class="user-profile-cell">
                                        <div class="user-avatar-badge">
                                            {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-profile-meta">
                                            <div class="user-name">{{ $u->name }}</div>
                                            <div class="user-email">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($u->phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $u->phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="color: #27342e; font-size: 13px; font-weight: 600;">
                                                {{ $u->phone }}
                                            </span>
                                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" title="Chat WhatsApp Pelanggan" style="display: inline-flex; align-items: center; padding: 2px 6px; border-radius: 6px; background: #e8f9ee; color: #16a34a; text-decoration: none; font-size: 10px; font-weight: 700;">
                                                WA
                                            </a>
                                        </div>
                                    @else
                                        <span style="color: #92a099; font-size: 12px; font-style: italic;">
                                            Belum ada
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="status-badge {{ ($u->status ?? 'active') === 'active' ? 'active' : 'inactive' }}">
                                        {{ ($u->status ?? 'active') === 'active' ? '● Aktif' : '● Diblokir' }}
                                    </span>
                                </td>

                                <td>
                                    @if($u->email_verified_at)
                                        <span class="verify-badge verified">
                                            <span></span>
                                            <span>Terverifikasi</span>
                                        </span>
                                    @else
                                        <span class="verify-badge unverified">
                                            <span>⏳</span>
                                            <span>Belum Verifikasi</span>
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span style="color: #6d7d76; font-size: 12px;">
                                        {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="action-group" style="justify-content: flex-end;">

                                        {{-- DETAIL & RIWAYAT PESANAN --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-user.show', $u->id) }}"
                                            class="action-btn view"
                                            title="Lihat Profil & Riwayat Pesanan"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-user.edit', $u->id) }}"
                                            class="action-btn edit"
                                            title="Edit Data User"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- BLOKIR / BUKA BLOKIR --}}
                                        <form
                                            action="{{ route('dashboard.admin.kelola-user.toggle-status', $u->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin {{ ($u->status ?? 'active') === 'active' ? 'MEMBLOKIR' : 'MENGAKTIFKAN KEMBALI' }} akun {{ $u->name }}?');"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="action-btn toggle"
                                                title="{{ ($u->status ?? 'active') === 'active' ? 'Blokir Akun Pengguna' : 'Buka Blokir Akun' }}"
                                            >
                                                @if(($u->status ?? 'active') === 'active')
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-width="2"/></svg>
                                                @else
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- DELETE --}}
                                        <form
                                            action="{{ route('dashboard.admin.kelola-user.destroy', $u->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Data yang dihapus tidak dapat dikembalikan.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="action-btn delete"
                                                title="Hapus User"
                                            >
                                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Menampilkan
                        <strong>{{ $users->firstItem() ?? 0 }}</strong>
                        -
                        <strong>{{ $users->lastItem() ?? 0 }}</strong>
                        dari
                        <strong>{{ $users->total() ?? 0 }}</strong>
                        pengguna
                    </div>

                    <div>
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>

            @else
                <div class="empty-state-box">
                    <h3>Data Pengguna Tidak Ditemukan</h3>
                    <p>
                        @if(request()->filled('search') || request()->filled('status') || request()->filled('verification'))
                            Tidak ada pengguna yang cocok dengan kriteria pencarian atau filter yang diterapkan.
                        @else
                            Belum ada akun pengguna yang terdaftar di sistem TravelKu.
                        @endif
                    </p>
                    @if(request()->filled('search') || request()->filled('status') || request()->filled('verification'))
                        <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="btn-filter-reset">
                            Reset Pencarian
                        </a>
                    @else
                        <a href="{{ route('dashboard.admin.kelola-user.create') }}" class="btn-create-user" style="display: inline-flex;">
                            Tambah User Baru
                        </a>
                    @endif
                </div>
            @endif
        </div>

    </div>

</div>

@endsection