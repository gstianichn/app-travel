@extends('layouts.admin')

@section('title', 'Kelola Driver')

@section('page-title', 'Kelola Driver')

@section('page-subtitle', 'Pantau profil pengemudi, legalitas SIM & masa berlaku, status kesiapan kerja, dan riwayat penugasan armada.')

@section('content')

<style>
    .kelola-driver-page {
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

    .btn-create-driver {
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

    .btn-create-driver:hover {
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

    .stat-box-icon.standby {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-box-icon.onduty {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-box-icon.leave {
        background: #fef3c7;
        color: #d97706;
    }

    /* =========================================
       LEGALITY NOTICE BANNER
    ========================================== */
    .license-warning-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .warning-text {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #92400e;
        font-size: 13px;
        font-weight: 500;
    }

    .warning-icon {
        font-size: 22px;
    }

    .btn-filter-warning {
        padding: 6px 14px;
        border-radius: 8px;
        background: #f59e0b;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: background 0.15s ease;
    }

    .btn-filter-warning:hover {
        background: #d97706;
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
        grid-template-columns: 2fr 1.3fr 1.3fr auto;
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
       DRIVER TABLE CARD
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

    .driver-main-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .driver-main-table th {
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

    .driver-main-table td {
        padding: 15px 18px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .driver-main-table tbody tr:last-child td {
        border-bottom: none;
    }

    .driver-main-table tbody tr {
        transition: background 0.15s ease;
    }

    .driver-main-table tbody tr:hover {
        background: #fbfdfc;
    }

    /* DRIVER PROFILE CELL */
    .driver-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .driver-avatar-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        overflow: hidden;
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

    .driver-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .driver-profile-meta .driver-name {
        font-weight: 600;
        color: #17221d;
        margin-bottom: 2px;
    }

    .driver-profile-meta .driver-sub {
        font-size: 11px;
        color: #76857e;
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

    /* LICENSE CELL */
    .license-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .license-num {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        font-family: monospace;
    }

    .license-type-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        width: fit-content;
    }

    .license-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        width: fit-content;
    }

    .badge-valid {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .badge-expiring {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-expired {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .badge-unknown {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* EMERGENCY CONTACT CELL */
    .emergency-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .emergency-name {
        font-size: 12px;
        font-weight: 600;
        color: #1f2937;
    }

    .emergency-rel {
        font-size: 10px;
        color: #64748b;
    }

    .emergency-phone {
        font-size: 11px;
        color: #0284c7;
        font-weight: 500;
    }

    /* STATUS KERJA BADGES */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.standby {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .status-badge.onduty {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .status-badge.onleave {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.standby .status-dot {
        background: #16a34a;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
    }

    .status-badge.onduty .status-dot {
        background: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
    }

    .status-badge.onleave .status-dot {
        background: #d97706;
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

    .action-btn.status-toggle {
        background: #e0f2fe;
        color: #0369a1;
    }

    .action-btn.status-toggle:hover {
        background: #0284c7;
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

        .btn-create-driver {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="kelola-driver-page">

    {{-- =========================================
         PAGE HEADER
    ========================================== --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>/</span>
                <span>Kelola Driver</span>
            </div>
            <div class="header-text">
                <h1>Kelola Driver & Pengemudi</h1>
                <p>Pantau profil pengemudi, legalitas SIM & masa berlaku, status kesiapan kerja, serta riwayat penugasan unit minibus.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.admin.kelola-driver.create') }}" class="btn-create-driver">
            <span>+</span>
            <span>Tambah Driver Baru</span>
        </a>
    </div>

    {{-- =========================================
         FLASH NOTIFICATIONS
    ========================================== --}}
    @if(session('success'))
        <div class="alert-banner alert-success" style="display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-banner alert-error" style="display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================
         PERINGATAN SIM KEDALUWARSA (JIKA ADA)
    ========================================== --}}
    @if(($expiringLicenseDrivers ?? 0) > 0 || ($expiredLicenseDrivers ?? 0) > 0)
        <div class="license-warning-card">
            <div class="warning-text">
                <span class="warning-icon" style="display:inline-flex; align-items:center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <strong>Peringatan Legalitas Pengemudi:</strong>
                    Terdapat <strong>{{ $expiredLicenseDrivers ?? 0 }} SIM kedaluwarsa</strong> dan <strong>{{ $expiringLicenseDrivers ?? 0 }} SIM hampir habis masa berlaku</strong> dalam 30 hari ke depan. Pastikan pengemudi memperpanjang lisensi sebelum ditugaskan jalan.
                </div>
            </div>
            <a href="{{ route('dashboard.admin.kelola-driver.index', ['license_status' => 'expiring']) }}" class="btn-filter-warning">
                Tinjau Pengemudi
            </a>
        </div>
    @endif

    {{-- =========================================
         STATISTIK STATUS KERJA DRIVER
    ========================================== --}}
    <div class="stats-row">
        {{-- TOTAL PENGEMUDI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Pengemudi</div>
                <div class="stat-box-num">{{ number_format($totalDrivers ?? 0) }}</div>
            </div>
            <div class="stat-box-icon total">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3" stroke-width="2"/><circle cx="9" cy="10" r="2" stroke-width="2"/><line x1="15" y1="9" x2="18" y2="9" stroke-width="2"/><line x1="15" y1="13" x2="18" y2="13" stroke-width="2"/><line x1="6" y1="16" x2="18" y2="16" stroke-width="2"/></svg>
            </div>
        </div>

        {{-- SIAP JALAN (STANDBY) --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Siap Jalan (Standby)</div>
                <div class="stat-box-num">{{ number_format($standbyDrivers ?? 0) }}</div>
            </div>
            <div class="stat-box-icon standby">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- SEDANG BERTUGAS --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Sedang Bertugas</div>
                <div class="stat-box-num">{{ number_format($onDutyDrivers ?? 0) }}</div>
            </div>
            <div class="stat-box-icon onduty">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="3" stroke-width="2"/><circle cx="7" cy="18" r="2" fill="currentColor"/><circle cx="17" cy="18" r="2" fill="currentColor"/><path stroke-linecap="round" stroke-width="2" d="M2 11h20M14 6v5M8 6v5"/></svg>
            </div>
        </div>

        {{-- LIBUR / CUTI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Sedang Libur / Cuti</div>
                <div class="stat-box-num">{{ number_format($onLeaveDrivers ?? 0) }}</div>
            </div>
            <div class="stat-box-icon leave">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="10" y1="15" x2="10" y2="9" stroke-width="2"/><line x1="14" y1="15" x2="14" y2="9" stroke-width="2"/></svg>
            </div>
        </div>
    </div>

    {{-- =========================================
         FILTER & PENCARIAN
    ========================================== --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.kelola-driver.index') }}" method="GET" class="filter-form-grid admin-filter-form">

            <div class="filter-input-wrap">
                <span class="input-icon" style="display:inline-flex; align-items:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    placeholder="Cari nama, nomor SIM, WhatsApp, atau kontak darurat..."
                    value="{{ request('search') }}"
                >
            </div>

            <div>
                <select name="status" class="filter-select">
                    <option value="">Semua Status Kerja</option>
                    <option value="standby" {{ request('status') === 'standby' ? 'selected' : '' }}>Siap Jalan (Standby)</option>
                    <option value="on_duty" {{ request('status') === 'on_duty' ? 'selected' : '' }}>Sedang Bertugas di Jalan</option>
                    <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>Sedang Libur / Cuti</option>
                </select>
            </div>

            <div>
                <select name="license_status" class="filter-select">
                    <option value="">Semua Status Legalitas SIM</option>
                    <option value="valid" {{ request('license_status') === 'valid' ? 'selected' : '' }}>Berlaku Aktif</option>
                    <option value="expiring" {{ request('license_status') === 'expiring' ? 'selected' : '' }}>Hampir Kedaluwarsa (&le; 30 Hari)</option>
                    <option value="expired" {{ request('license_status') === 'expired' ? 'selected' : '' }}>Sudah Kedaluwarsa</option>
                </select>
            </div>

            <div class="filter-btn-group">
                <button type="submit" class="btn-filter-submit">
                    Cari & Filter
                </button>

                @if(request()->filled('search') || request()->filled('status') || request()->filled('license_status'))
                    <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- =========================================
         TABEL DRIVER
    ========================================== --}}
    <div class="table-card">

        <div class="table-card-top">
            <div class="table-card-title">
                Daftar Pengemudi Travel
            </div>
            <div class="table-card-count">
                {{ isset($drivers) && method_exists($drivers, 'total') ? $drivers->total() : (isset($drivers) ? $drivers->count() : 0) }} Driver
            </div>
        </div>

        <div class="table-responsive">
            @if(isset($drivers) && $drivers->count() > 0)
                <table class="driver-main-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Pengemudi</th>
                            <th>Kontak WhatsApp</th>
                            <th>Legalitas SIM</th>
                            <th>Kontak Darurat Keluarga</th>
                            <th>Status Kerja</th>
                            <th>Riwayat Tugas</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $index => $driver)
                            @php
                                $lic = $driver->licenseStatus();
                            @endphp
                            <tr>
                                <td>
                                    <span style="color: #92a099; font-size: 12px; font-weight: 600;">
                                        {{ (method_exists($drivers, 'firstItem') && $drivers->firstItem()) ? ($drivers->firstItem() + $index) : ($index + 1) }}
                                    </span>
                                </td>

                                {{-- PROFIL & FOTO --}}
                                <td>
                                    <div class="driver-profile-cell">
                                        <div class="driver-avatar-box">
                                            @if($driver->photo)
                                                <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->user?->name }}" class="driver-avatar-img">
                                            @else
                                                {{ strtoupper(substr($driver->user?->name ?? 'DR', 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="driver-profile-meta">
                                            <div class="driver-name">{{ $driver->user?->name ?? 'Pengemudi #' . $driver->id }}</div>
                                            <div class="driver-sub">{{ $driver->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- KONTAK WHATSAPP --}}
                                <td>
                                    <div class="contact-cell">
                                        @php
                                            $phoneNum = $driver->phone ?? $driver->user?->phone;
                                        @endphp
                                        @if($phoneNum)
                                            <span class="contact-phone">{{ $phoneNum }}</span>
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNum);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="contact-wa-link" title="Kirim Pesan WhatsApp" style="display:inline-flex; align-items:center; gap:4px;">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                <span>Chat WhatsApp</span>
                                            </a>
                                        @else
                                            <span style="color: #9ca3af; font-size: 12px; font-style: italic;">Tidak ada kontak</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- LEGALITAS SIM --}}
                                <td>
                                    <div class="license-cell">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span class="license-num">{{ $driver->license_number }}</span>
                                            @if($driver->license_type)
                                                <span class="license-type-badge">{{ $driver->license_type }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="license-status-pill {{ $lic['badge_class'] }}" title="Masa berlaku: {{ $driver->license_expired_at ? $driver->license_expired_at->format('d M Y') : '-' }}">
                                                <span>●</span>
                                                <span>{{ $lic['label'] }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- KONTAK DARURAT KELUARGA --}}
                                <td>
                                    <div class="emergency-cell">
                                        @if($driver->emergency_contact_name)
                                            <span class="emergency-name">{{ $driver->emergency_contact_name }}</span>
                                            <span class="emergency-rel">Hubungan: {{ $driver->emergency_contact_relation ?? 'Keluarga' }}</span>
                                            @if($driver->emergency_contact_phone)
                                                <span class="emergency-phone" style="display:inline-flex; align-items:center; gap:4px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $driver->emergency_contact_phone }}</span>
                                            @endif
                                        @else
                                            <span style="color: #9ca3af; font-size: 12px; font-style: italic;">Belum diisi</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- STATUS KERJA --}}
                                <td>
                                    <span class="status-badge {{ $driver->statusBadgeClass() }}">
                                        <span class="status-dot"></span>
                                        <span>{{ $driver->statusLabel() }}</span>
                                    </span>
                                </td>

                                {{-- RIWAYAT PENUGASAN --}}
                                <td>
                                    <span style="font-size: 12px; font-weight: 700; color: #17221d;">
                                        {{ $driver->totalTripsCount() }} Penugasan
                                    </span>
                                    <div style="font-size: 11px; color: #76857e;">
                                        {{ $driver->completedTripsCount() }} selesai
                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    <div class="action-group">
                                    {{-- DETAIL & RIWAYAT PENUGASAN --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-driver.show', $driver->id) }}"
                                            class="action-btn view"
                                            title="Lihat Detail & Riwayat Minibus"
                                            style="display:inline-flex; align-items:center; justify-content:center;"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('dashboard.admin.kelola-driver.edit', $driver->id) }}"
                                            class="action-btn edit"
                                            title="Edit Profil & Legalitas SIM"
                                            style="display:inline-flex; align-items:center; justify-content:center;"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        {{-- GANTI STATUS KERJA CEPAT --}}
                                        <div style="position: relative; display: inline-block;">
                                            <button
                                                type="button"
                                                class="action-btn status-toggle"
                                                title="Ubah Status Kerja Cepat"
                                                onclick="toggleStatusDropdown({{ $driver->id }})"
                                                style="display:inline-flex; align-items:center; justify-content:center;"
                                            >
                                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>

                                            <div id="status-dropdown-{{ $driver->id }}" style="display: none; position: absolute; right: 0; top: 38px; background: white; border: 1px solid #dce5e1; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 50; width: 190px; padding: 6px 0;">
                                                <form action="{{ route('dashboard.admin.kelola-driver.update-status', $driver->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="standby">
                                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 14px; background: none; border: none; font-size: 12px; font-weight: 600; color: #15803d; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                                        <span style="width:8px; height:8px; border-radius:50%; background:#16a34a; display:inline-block;"></span> <span>Siap Jalan (Standby)</span>
                                                    </button>
                                                </form>

                                                <form action="{{ route('dashboard.admin.kelola-driver.update-status', $driver->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="on_duty">
                                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 14px; background: none; border: none; font-size: 12px; font-weight: 600; color: #0369a1; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                                        <span style="width:8px; height:8px; border-radius:50%; background:#0284c7; display:inline-block;"></span> <span>Sedang Bertugas</span>
                                                    </button>
                                                </form>

                                                <form action="{{ route('dashboard.admin.kelola-driver.update-status', $driver->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="on_leave">
                                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 14px; background: none; border: none; font-size: 12px; font-weight: 600; color: #b45309; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                                        <span style="width:8px; height:8px; border-radius:50%; background:#f59e0b; display:inline-block;"></span> <span>Sedang Libur / Cuti</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- HAPUS --}}
                                        <form
                                            action="{{ route('dashboard.admin.kelola-driver.destroy', $driver->id) }}"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Hapus permanen data pengemudi {{ $driver->user?->name }}? Seluruh riwayat akan dihapus.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Hapus Driver" style="display:inline-flex; align-items:center; justify-content:center;">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                    <div class="empty-state-icon" style="display:inline-flex; align-items:center; justify-content:center; color:#087f55;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3" stroke-width="2"/><circle cx="9" cy="10" r="2" stroke-width="2"/><line x1="15" y1="9" x2="18" y2="9" stroke-width="2"/><line x1="15" y1="13" x2="18" y2="13" stroke-width="2"/><line x1="6" y1="16" x2="18" y2="16" stroke-width="2"/></svg>
                    </div>
                    <h3>Belum Ada Data Driver</h3>
                    <p>Tidak ditemukan data pengemudi sesuai kriteria pencarian atau filter yang dipilih.</p>
                    <a href="{{ route('dashboard.admin.kelola-driver.create') }}" class="btn-create-driver" style="display: inline-flex;">
                        + Tambah Driver Baru
                    </a>
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if(isset($drivers) && method_exists($drivers, 'hasPages') && $drivers->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $drivers->firstItem() }}</strong> sampai <strong>{{ $drivers->lastItem() }}</strong> dari total <strong>{{ $drivers->total() }}</strong> driver
                </div>
                <div>
                    {{ $drivers->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

<script>
    function toggleStatusDropdown(driverId) {
        const dd = document.getElementById('status-dropdown-' + driverId);
        if (dd) {
            const isShown = dd.style.display === 'block';
            document.querySelectorAll('[id^="status-dropdown-"]').forEach(el => el.style.display = 'none');
            dd.style.display = isShown ? 'none' : 'block';
        }
    }

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-btn.status-toggle') && !e.target.closest('[id^="status-dropdown-"]')) {
            document.querySelectorAll('[id^="status-dropdown-"]').forEach(el => el.style.display = 'none');
        }
    });
</script>

@endsection
