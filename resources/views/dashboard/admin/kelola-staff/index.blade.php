@extends('layouts.admin')

@section('title', 'Kelola Staff')

@section('page-title', 'Kelola Staff')

@section('page-subtitle', 'Kelola profil staf, penempatan cabang/loket, pengaturan wewenang hak akses, dan status kepegawaian.')

@section('content')

<style>
    .kelola-staff-page {
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

    .btn-create-staff {
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

    .btn-create-staff:hover {
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

    .stat-box-icon.inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-box-icon.branch {
        background: #e0f2fe;
        color: #0284c7;
    }

    /* =========================================
       INFO BANNER: WEWENANG & PEMISAHAN ROLE
    ========================================== */
    .authority-info-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #e8f7ee 100%);
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .authority-icon {
        font-size: 24px;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .authority-content h4 {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 700;
        color: #14532d;
    }

    .authority-content p {
        margin: 0;
        font-size: 12px;
        color: #276749;
        line-height: 1.5;
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
        grid-template-columns: 2fr 1.3fr 1.2fr auto;
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
       STAFF TABLE CARD
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

    .staff-main-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 950px;
    }

    .staff-main-table th {
        padding: 13px 18px;
        background: #fbfdfc;
        color: #72817a;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf1ef;
    }

    .staff-main-table td {
        padding: 15px 18px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .staff-main-table tbody tr:last-child td {
        border-bottom: none;
    }

    .staff-main-table tbody tr {
        transition: background 0.15s ease;
    }

    .staff-main-table tbody tr:hover {
        background: #fbfdfc;
    }

    /* STAFF CELL */
    .staff-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .staff-avatar-badge {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
        border: 1px solid #cce8dc;
    }

    .staff-avatar-badge.inactive {
        background: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
    }

    .staff-profile-meta .staff-name {
        font-weight: 600;
        color: #17221d;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .staff-profile-meta .staff-email {
        font-size: 11px;
        color: #76857e;
    }

    .staff-position-tag {
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 6px;
        background: #f0f4f2;
        color: #4b5550;
        font-weight: 500;
    }

    /* BRANCH CELL */
    .branch-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 9px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0369a1;
        font-size: 12px;
        font-weight: 600;
    }

    /* CONTACT CELL */
    .contact-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .contact-phone {
        font-size: 12px;
        font-weight: 600;
        color: #1f2937;
    }

    .contact-wa-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #16a34a;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.15s ease;
    }

    .contact-wa-link:hover {
        color: #15803d;
        text-decoration: underline;
    }

    /* PERMISSIONS PILLS */
    .permissions-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        max-width: 250px;
    }

    .perm-pill {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        white-space: nowrap;
    }

    .perm-pill.restricted {
        background: #fff1f2;
        color: #e11d48;
        border-color: #fecdd3;
    }

    .perm-more-pill {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #6b7280;
    }

    /* STATUS BADGE */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.active .status-dot {
        background: #16a34a;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
    }

    .status-badge.inactive .status-dot {
        background: #dc2626;
    }

    /* ACTION BUTTONS */
    .action-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;
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

    .action-btn.toggle-off {
        background: #fee2e2;
        color: #b91c1c;
    }

    .action-btn.toggle-off:hover {
        background: #dc2626;
        color: white;
    }

    .action-btn.toggle-on {
        background: #dcfce7;
        color: #15803d;
    }

    .action-btn.toggle-on:hover {
        background: #16a34a;
        color: white;
    }

    .action-btn.delete {
        background: #f3f4f6;
        color: #6b7280;
    }

    .action-btn.delete:hover {
        background: #ef4444;
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

    /* RESPONSIVE */
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

        .btn-create-staff {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="kelola-staff-page">

    {{-- =========================================
         PAGE HEADER
    ========================================== --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>/</span>
                <span>Kelola Staff</span>
            </div>
            <div class="header-text">
                <h1>Kelola Staf & Hak Akses</h1>
                <p>Atur penempatan cabang/loket, pembagian wewenang operasional (role & permissions), serta kontrol status kepegawaian.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.admin.kelola-staff.create') }}" class="btn-create-staff">
            <span>+</span>
            <span>Tambah Staf Baru</span>
        </a>
    </div>

    {{-- =========================================
         FLASH NOTIFICATIONS
    ========================================== --}}
    @if(session('success'))
        <div class="alert-banner alert-success">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-banner alert-error">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================
         STATISTIK STAF
    ========================================== --}}
    <div class="stats-row">
        {{-- TOTAL STAF --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Staf Operasional</div>
                <div class="stat-box-num">{{ number_format($totalStaff ?? 0) }}</div>
            </div>
            <div class="stat-box-icon total">
                👥
            </div>
        </div>

        {{-- STAF AKTIF BEKERJA --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Aktif Bekerja</div>
                <div class="stat-box-num">{{ number_format($activeStaff ?? 0) }}</div>
            </div>
            <div class="stat-box-icon active">
                💼
            </div>
        </div>

        {{-- STAF NONAKTIF / BERHENTI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Berhenti / Nonaktif</div>
                <div class="stat-box-num">{{ number_format($inactiveStaff ?? 0) }}</div>
            </div>
            <div class="stat-box-icon inactive">
                ⛔
            </div>
        </div>

        {{-- CABANG / LOKET --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Cabang & Pool Tercover</div>
                <div class="stat-box-num">{{ number_format($uniqueBranchesCount ?? count($availableBranches ?? [])) }}</div>
            </div>
            <div class="stat-box-icon branch">
                🏢
            </div>
        </div>
    </div>

    {{-- =========================================
         INFO WEWENANG & PEMISAHAN ROLE
    ========================================== --}}
    <div class="authority-info-card">
        <div class="authority-icon">
            🛡️
        </div>
        <div class="authority-content">
            <h4>Kebijakan Pemisahan Hak Akses (Role & Permission)</h4>
            <p>
                Staf loket hanya diberikan hak untuk <strong>input tiket pesanan</strong>, melihat <strong>jadwal rute</strong>, dan <strong>check-in manifest penumpang</strong>. Tindakan berisiko tinggi seperti <strong>penyetujuan refund</strong> dan <strong>penghapusan data</strong> secara baku ditutup dan hanya dapat dilakukan oleh Admin Utama.
            </p>
        </div>
    </div>

    {{-- =========================================
         FILTER & PENCARIAN
    ========================================== --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.kelola-staff.index') }}" method="GET" class="filter-form-grid">

            <div class="filter-input-wrap">
                <span class="input-icon">🔍</span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    placeholder="Cari nama, email, nomor HP, atau cabang..."
                    value="{{ request('search') }}"
                >
            </div>

            <div>
                <select name="branch" class="filter-select">
                    <option value="">Semua Cabang / Pool / Loket</option>
                    @foreach($availableBranches as $branchOption)
                        <option value="{{ $branchOption }}" {{ request('branch') === $branchOption ? 'selected' : '' }}>
                            {{ $branchOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="filter-select">
                    <option value="">Semua Status Kepegawaian</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Bekerja</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Berhenti / Nonaktif</option>
                </select>
            </div>

            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">
                    Cari & Filter
                </button>

                @if(request()->filled('search') || request()->filled('branch') || request()->filled('status'))
                    <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- =========================================
         TABEL STAF
    ========================================== --}}
    <div class="table-card">

        <div class="table-card-top">
            <div class="table-card-title">
                Daftar Staf Loket & Operasional
            </div>
            <div class="table-card-count">
                {{ isset($staffMembers) && method_exists($staffMembers, 'total') ? $staffMembers->total() : (isset($staffMembers) ? $staffMembers->count() : 0) }} Staf
            </div>
        </div>

        <div class="table-responsive">
            @if(isset($staffMembers) && $staffMembers->count() > 0)
                <table class="staff-main-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Profil Staf</th>
                            <th>Penempatan Cabang / Loket</th>
                            <th>Kontak Staf</th>
                            <th>Hak Akses (Permissions)</th>
                            <th>Status Kepegawaian</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffMembers as $index => $staff)
                            <tr>
                                <td>
                                    <span style="color: #92a099; font-size: 12px; font-weight: 600;">
                                        {{ (method_exists($staffMembers, 'firstItem') && $staffMembers->firstItem()) ? ($staffMembers->firstItem() + $index) : ($index + 1) }}
                                    </span>
                                </td>

                                {{-- PROFIL STAF --}}
                                <td>
                                    <div class="staff-profile-cell">
                                        <div class="staff-avatar-badge {{ $staff->status === 'inactive' ? 'inactive' : '' }}">
                                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                                        </div>
                                        <div class="staff-profile-meta">
                                            <div class="staff-name">
                                                <span>{{ $staff->name }}</span>
                                                @if($staff->position)
                                                    <span class="staff-position-tag">{{ $staff->position }}</span>
                                                @endif
                                            </div>
                                            <div class="staff-email">{{ $staff->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- PENEMPATAN CABANG / LOKET --}}
                                <td>
                                    @if($staff->branch)
                                        <span class="branch-badge">
                                            <span>🏢</span>
                                            <span>{{ $staff->branch }}</span>
                                        </span>
                                    @else
                                        <span style="color: #9ca3af; font-size: 12px; font-style: italic;">Belum ditentukan</span>
                                    @endif
                                </td>

                                {{-- KONTAK --}}
                                <td>
                                    <div class="contact-cell">
                                        @if($staff->phone)
                                            <span class="contact-phone">{{ $staff->phone }}</span>
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $staff->phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="contact-wa-link" title="Kirim Pesan WhatsApp">
                                                <span>💬 Hubungi WA</span>
                                            </a>
                                        @else
                                            <span style="color: #9ca3af; font-size: 12px; font-style: italic;">Tidak ada nomor</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- HAK AKSES / PERMISSIONS --}}
                                <td>
                                    <div class="permissions-wrap">
                                        @php
                                            $perms = $staff->permissions ?? [];
                                            $labelMap = [
                                                'create_booking' => 'Input Pesanan',
                                                'view_routes' => 'Lihat Rute',
                                                'view_passengers' => 'Data Penumpang',
                                                'checkin_ticket' => 'Check-in Tiket',
                                                'view_reports' => 'Laporan Kasir',
                                                'approve_refund' => 'Setujui Refund ⚠️',
                                                'delete_data' => 'Hapus Data ⚠️',
                                            ];
                                            $showCount = 0;
                                        @endphp

                                        @if(empty($perms))
                                            <span style="color: #9ca3af; font-size: 11px; font-style: italic;">Belum ada hak akses</span>
                                        @else
                                            @foreach($perms as $p)
                                                @if($showCount < 3)
                                                    <span class="perm-pill {{ in_array($p, ['approve_refund', 'delete_data']) ? 'restricted' : '' }}">
                                                        {{ $labelMap[$p] ?? $p }}
                                                    </span>
                                                    @php $showCount++; @endphp
                                                @endif
                                            @endforeach

                                            @if(count($perms) > 3)
                                                <span class="perm-more-pill" title="{{ implode(', ', array_map(fn($k) => $labelMap[$k] ?? $k, $perms)) }}">
                                                    +{{ count($perms) - 3 }} lainnya
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                {{-- STATUS KEPEGAWAIAN --}}
                                <td>
                                    @if($staff->status === 'active')
                                        <span class="status-badge active" title="Akun aktif dan dapat bertugas">
                                            <span class="status-dot"></span>
                                            <span>Aktif Bekerja</span>
                                        </span>
                                    @else
                                        <span class="status-badge inactive" title="Akun dinonaktifkan / sudah berhenti bekerja">
                                            <span class="status-dot"></span>
                                            <span>Berhenti Bekerja</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    <div class="action-group">
                                        {{-- DETAIL --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-staff.show', $staff->id) }}"
                                            class="action-btn view"
                                            title="Lihat Detail Profil & Wewenang"
                                        >
                                            👁️
                                        </a>

                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-staff.edit', $staff->id) }}"
                                            class="action-btn edit"
                                            title="Edit Profil & Hak Akses"
                                        >
                                            ✏️
                                        </a>

                                        {{-- TOGGLE STATUS (AKTIF / NONAKTIFKAN STAF BERHENTI) --}}
                                        <form
                                            action="{{ route('dashboard.admin.kelola-staff.toggle-status', $staff->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('{{ $staff->status === 'active' ? "Nonaktifkan akun staf {$staff->name}? Staf yang berhenti bekerja tidak dapat lagi masuk ke sistem loket." : "Aktifkan kembali akun staf {$staff->name} agar dapat bertugas melayani loket?" }}');"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            @if($staff->status === 'active')
                                                <button type="submit" class="action-btn toggle-off" title="Nonaktifkan Akun (Staf Berhenti)">
                                                    ⛔
                                                </button>
                                            @else
                                                <button type="submit" class="action-btn toggle-on" title="Aktifkan Kembali Akun Staf">
                                                    🔄
                                                </button>
                                            @endif
                                        </form>

                                        {{-- DELETE --}}
                                        <form
                                            action="{{ route('dashboard.admin.kelola-staff.destroy', $staff->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Hapus permanen akun staf {{ $staff->name }}? Tindakan ini tidak dapat dibatalkan.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Hapus Akun Staf">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state-box">
                    <div class="empty-state-icon">👥</div>
                    <h3>Belum Ada Data Staf</h3>
                    <p>Tidak ditemukan staf operasional sesuai kriteria pencarian atau filter yang dipilih.</p>
                    <a href="{{ route('dashboard.admin.kelola-staff.create') }}" class="btn-create-staff" style="display: inline-flex;">
                        + Tambah Staf Baru
                    </a>
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if(isset($staffMembers) && method_exists($staffMembers, 'hasPages') && $staffMembers->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $staffMembers->firstItem() }}</strong> sampai <strong>{{ $staffMembers->lastItem() }}</strong> dari total <strong>{{ $staffMembers->total() }}</strong> staf
                </div>
                <div>
                    {{ $staffMembers->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

@endsection
