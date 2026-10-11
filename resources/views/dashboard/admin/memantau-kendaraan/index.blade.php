@extends('layouts.admin')

@section('title', 'Memantau Kendaraan')

@section('page-title', 'Memantau Kendaraan')

@section('page-subtitle', 'Pantau identitas unit armada, kelaikan legalitas (Uji KIR & Pajak STNK), status operasional, dan riwayat penugasan travel.')

@section('content')

<style>
    .fleet-page {
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

    .btn-create-vehicle {
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

    .btn-create-vehicle:hover {
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

    .stat-box-icon.ready {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-box-icon.operating {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-box-icon.maintenance {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* =========================================
       LEGALITY NOTICE BANNER
    ========================================== */
    .legality-warning-card {
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
        line-height: 1;
    }

    .warning-action a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #92400e;
        text-decoration: underline;
    }

    /* =========================================
       FILTER & SEARCH CARD
    ========================================== */
    .filter-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.2fr auto;
        gap: 14px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #55655e;
    }

    .filter-input,
    .filter-select {
        height: 42px;
        padding: 0 14px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        color: #17221d;
        background: #fbfdfc;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        background: white;
    }

    .filter-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-filter-submit {
        height: 42px;
        padding: 0 18px;
        background: #087f55;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-filter-submit:hover {
        background: #066b47;
    }

    .btn-filter-reset {
        height: 42px;
        padding: 0 14px;
        background: #f1f5f3;
        color: #52635c;
        border: 1px solid #d8e2de;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .btn-filter-reset:hover {
        background: #e5ece8;
    }

    /* =========================================
       TABLE CARD
    ========================================== */
    .table-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .fleet-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .fleet-table thead th {
        background: #f7faf8;
        padding: 14px 18px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #55655e;
        border-bottom: 1px solid #e2eae6;
        white-space: nowrap;
    }

    .fleet-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        color: #213129;
        border-bottom: 1px solid #eef3f0;
        vertical-align: middle;
    }

    .fleet-table tbody tr:hover {
        background: #fafcfb;
    }

    /* Plat Nomor Badge Style Indonesia */
    .plate-number-box {
        display: inline-flex;
        flex-direction: column;
        background: #111827;
        color: #f9fafb;
        padding: 4px 10px;
        border-radius: 6px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: 1px;
        border: 1.5px solid #374151;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.4);
    }

    .vehicle-unit-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .vehicle-thumb-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #e2eae6;
        background: #f1f5f3;
        flex-shrink: 0;
    }

    .vehicle-thumb-fallback {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        border: 1px solid #c9ebd9;
    }

    .unit-meta h4 {
        margin: 0 0 3px;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
    }

    .unit-meta .unit-year {
        font-size: 11px;
        color: #72817a;
    }

    /* Tipe & Kapasitas */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        background: #f3f4f6;
        color: #374151;
        margin-bottom: 4px;
    }

    .capacity-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #087f55;
    }

    /* Legality Badges (KIR & STNK) */
    .legality-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .legality-date {
        font-size: 12px;
        font-weight: 600;
        color: #17221d;
    }

    .badge-legal {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 5px;
        width: fit-content;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-legal-valid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-legal-warning {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .badge-legal-expired {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-legal-unknown {
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }

    /* Operational Status Badges */
    .badge-unit-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-ready {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-ready::before {
        content: '';
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #16a34a;
    }

    .badge-operating {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .badge-operating::before {
        content: '';
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #0284c7;
        animation: pulse 1.8s infinite;
    }

    .badge-maintenance {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-maintenance::before {
        content: '';
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #dc2626;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
    }

    /* Actions */
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #d4dfda;
        background: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action-icon:hover {
        border-color: #087f55;
        color: #087f55;
        background: #e3f5ee;
    }

    .btn-action-icon.delete:hover {
        border-color: #b91c1c;
        color: #b91c1c;
        background: #fee2e2;
    }

    /* Quick status button */
    .btn-quick-status {
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 7px;
        border: 1px solid #d4dfda;
        background: white;
        color: #374151;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .btn-quick-status:hover {
        background: #f3f7f5;
        border-color: #087f55;
        color: #087f55;
    }

    /* =========================================
       EMPTY STATE & PAGINATION
    ========================================== */
    .empty-state-box {
        padding: 60px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .empty-state-icon {
        font-size: 48px;
        line-height: 1;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 700;
        color: #17221d;
        margin: 0;
    }

    .empty-state-desc {
        font-size: 13px;
        color: #72817a;
        max-width: 420px;
        margin: 0;
    }

    .pagination-bar {
        padding: 16px 20px;
        border-top: 1px solid #eef3f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Modal Backdrop & Dialog */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        backdrop-filter: blur(3px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-dialog {
        background: white;
        width: 100%;
        max-width: 440px;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #17221d;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 20px;
        color: #9ca3af;
        cursor: pointer;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    @media (max-width: 1024px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="fleet-page">

    {{-- BREADCRUMB & TITLE --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>&rsaquo;</span>
                <span>Memantau Kendaraan</span>
            </div>
            <div class="header-text">
                <h1>Memantau Kendaraan (Fleet Monitoring)</h1>
                <p>Identitas armada, kelaikan dokumen (Uji KIR & Pajak STNK), status unit, dan riwayat penugasan travel.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.admin.memantau-kendaraan.create') }}" class="btn-create-vehicle">
            <span>+</span>
            <span>Tambah Unit Armada</span>
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
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

    {{-- STATS CARDS --}}
    <div class="stats-row">
        {{-- TOTAL ARMADA --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Unit Armada</div>
                <div class="stat-box-num">{{ number_format($totalVehicles) }}</div>
            </div>
        </div>

        {{-- SIAP JALAN --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Siap Jalan (Standby)</div>
                <div class="stat-box-num" style="color: #15803d;">{{ number_format($readyVehicles) }}</div>
            </div>
        </div>

        {{-- SEDANG BEROPERASI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Sedang Beroperasi</div>
                <div class="stat-box-num" style="color: #0284c7;">{{ number_format($operatingVehicles) }}</div>
            </div>
        </div>

        {{-- MASUK BENGKEL --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Masuk Bengkel (Servis)</div>
                <div class="stat-box-num" style="color: #b91c1c;">{{ number_format($maintenanceVehicles) }}</div>
            </div>
        </div>
    </div>

    {{-- WARNING DOKUMEN / LEGALITAS JATUH TEMPO --}}
    @if($legalityAlertCount > 0)
        <div class="legality-warning-card">
            <div class="warning-text">
                <div>
                    <strong>Perhatian Dokumen Kelaikan:</strong> Terdapat <strong>{{ $legalityAlertCount }} unit armada</strong> dengan uji KIR atau Pajak STNK yang telah kedaluwarsa atau jatuh tempo dalam 30 hari ke depan.
                </div>
            </div>
            <div class="warning-action">
                <a href="{{ route('dashboard.admin.memantau-kendaraan.index', ['legality' => 'expired']) }}">
                    Tampilkan Unit Kritis</a>
            </div>
        </div>
    @endif

    {{-- FILTER & SEARCH --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.memantau-kendaraan.index') }}" method="GET" class="filter-form">
            {{-- SEARCH --}}
            <div class="filter-group">
                <label class="filter-label" for="search">Cari Unit / Pelat</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="filter-input"
                    placeholder="Contoh: B 1234 TAA, HiAce Premio..."
                    value="{{ request('search') }}"
                >
            </div>

            {{-- STATUS OPERASIONAL --}}
            <div class="filter-group">
                <label class="filter-label" for="status">Status Unit</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="siap_jalan" {{ request('status') === 'siap_jalan' ? 'selected' : '' }}>Siap Jalan</option>
                    <option value="beroperasi" {{ request('status') === 'beroperasi' ? 'selected' : '' }}>Sedang Beroperasi</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Masuk Bengkel</option>
                </select>
            </div>

            {{-- TIPE KENDARAAN --}}
            <div class="filter-group">
                <label class="filter-label" for="type">Tipe Minibus</label>
                <select name="type" id="type" class="filter-select">
                    <option value="all">Semua Tipe</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- LEGALITAS & KELAIKAN --}}
            <div class="filter-group">
                <label class="filter-label" for="legality">Kelaikan Dokumen</label>
                <select name="legality" id="legality" class="filter-select">
                    <option value="all">Semua Dokumen</option>
                    <option value="kir_expiring" {{ request('legality') === 'kir_expiring' ? 'selected' : '' }}>Uji KIR Akan Berakhir (&le; 30 Hari)</option>
                    <option value="stnk_expiring" {{ request('legality') === 'stnk_expiring' ? 'selected' : '' }}>Pajak STNK Akan Berakhir (&le; 30 Hari)</option>
                    <option value="expired" {{ request('legality') === 'expired' ? 'selected' : '' }}>Dokumen Lewat / Kedaluwarsa</option>
                </select>
            </div>

            {{-- BUTTONS --}}
            <div class="filter-buttons">
                <button type="submit" class="btn-filter-submit">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'status', 'type', 'legality']))
                    <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLE DATA ARMADA --}}
    <div class="table-card">
        <div class="table-container">
            <table class="fleet-table">
                <thead>
                    <tr>
                        <th>Identitas Unit & Pelat Nomor</th>
                        <th>Tipe & Kapasitas</th>
                        <th>Masa Berlaku Uji KIR</th>
                        <th>Jatuh Tempo Pajak STNK</th>
                        <th>Status Operasional</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        @php
                            $kir = $vehicle->kirStatus();
                            $stnk = $vehicle->stnkStatus();
                        @endphp
                        <tr>
                            {{-- IDENTITAS & PELAT --}}
                            <td>
                                <div class="vehicle-unit-cell">
                                    @if($vehicle->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($vehicle->photo))
                                        <img src="{{ asset('storage/' . $vehicle->photo) }}" alt="{{ $vehicle->name }}" class="vehicle-thumb-img">
                                    @else
                                        <div class="vehicle-thumb-fallback">Belum ada foto</div>
                                    @endif

                                    <div class="unit-meta">
                                        <div style="margin-bottom: 4px;">
                                            <span class="plate-number-box">{{ $vehicle->plate_number }}</span>
                                        </div>
                                        <h4>{{ $vehicle->name }}</h4>
                                        <span class="unit-year">Tahun: {{ $vehicle->manufacture_year ?? '-' }} &bull; Total Perjalanan: {{ $vehicle->schedules_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- TIPE & KAPASITAS --}}
                            <td>
                                <span class="type-badge">{{ $vehicle->type }}</span>
                                <div>
                                    <span class="capacity-tag">
                                        {{ $vehicle->capacity }} Total Kursi
                                    </span>
                                </div>
                            </td>

                            {{-- UJI KIR --}}
                            <td>
                                <div class="legality-cell">
                                    <span class="legality-date">
                                        {{ $vehicle->kir_expiry_date ? $vehicle->kir_expiry_date->format('d M Y') : 'Belum diisi' }}
                                    </span>
                                    <span class="badge-legal {{ $kir['badge_class'] }}">
                                        {{ $kir['label'] }}
                                    </span>
                                </div>
                            </td>

                            {{-- PAJAK STNK --}}
                            <td>
                                <div class="legality-cell">
                                    <span class="legality-date">
                                        {{ $vehicle->stnk_tax_due_date ? $vehicle->stnk_tax_due_date->format('d M Y') : 'Belum diisi' }}
                                    </span>
                                    <span class="badge-legal {{ $stnk['badge_class'] }}">
                                        {{ $stnk['label'] }}
                                    </span>
                                </div>
                            </td>

                            {{-- STATUS UNIT --}}
                            <td>
                                <div>
                                    <span class="badge-unit-status {{ $vehicle->statusBadgeClass() }}">
                                        {{ $vehicle->statusLabel() }}
                                    </span>
                                </div>
                                <div style="margin-top: 6px;">
                                    <button
                                        type="button"
                                        class="btn-quick-status"
                                        onclick="openStatusModal('{{ $vehicle->id }}', '{{ $vehicle->plate_number }}', '{{ strtolower($vehicle->status) }}')"
                                    >
                                        Ganti Status
                                    </button>
                                </div>
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- DETAIL --}}
                                    <a
                                        href="{{ route('dashboard.admin.memantau-kendaraan.show', $vehicle->id) }}"
                                        class="btn-action-icon"
                                        title="Detail Unit & Riwayat Jadwal"
                                    >Detail
                                                                            </a>

                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('dashboard.admin.memantau-kendaraan.edit', $vehicle->id) }}"
                                        class="btn-action-icon"
                                        title="Edit Informasi Armada"
                                    >Ubah
                                                                            </a>

                                    {{-- DELETE --}}
                                    <form
                                        action="{{ route('dashboard.admin.memantau-kendaraan.destroy', $vehicle->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit {{ $vehicle->plate_number }}? Tindakan ini tidak dapat dibatalkan.');"
                                        style="display: inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-action-icon delete"
                                            title="Hapus Unit Armada"
                                        >Hapus
                                                                                    </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state-box">
                                    <h4 class="empty-state-title">Tidak Ada Data Armada Kendaraan</h4>
                                    <p class="empty-state-desc">
                                        @if(request()->hasAny(['search', 'status', 'type', 'legality']))
                                            Tidak ditemukan kendaraan yang sesuai dengan kriteria filter pencarian Anda. Coba atur ulang filter.
                                        @else
                                            Belum ada unit kendaraan terdaftar di sistem. Mulai tambahkan armada minibus travel untuk penugasan rute.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'status', 'type', 'legality']))
                                        <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="btn-filter-reset">
                                            Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('dashboard.admin.memantau-kendaraan.create') }}" class="btn-create-vehicle" style="margin-top: 6px;">
                                            + Daftarkan Unit Baru
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="pagination-bar">
                <span style="font-size: 12px; color: #6a7972;">
                    Menampilkan {{ $vehicles->firstItem() }} - {{ $vehicles->lastItem() }} dari {{ $vehicles->total() }} unit armada
                </span>
                <div>
                    {{ $vehicles->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- MODAL UBAH STATUS INSTAN --}}
<div id="statusModal" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Perbarui Status Unit</h3>
            <button type="button" class="modal-close-btn" onclick="closeStatusModal()">&times;</button>
        </div>
        <form id="quickStatusForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <p id="modalVehicleText" style="font-size: 13px; color: #52635c; margin-top: 0; margin-bottom: 14px;"></p>
            
            <div class="filter-group">
                <label class="filter-label" for="modal_status">Pilih Status Baru</label>
                <select name="status" id="modal_status" class="filter-select" style="width: 100%;">
                    <option value="siap_jalan">Siap Jalan (Standby di Pool / Prima)</option>
                    <option value="beroperasi">Sedang Beroperasi (Aktif Mengangkut Penumpang)</option>
                    <option value="maintenance">Masuk Bengkel (Servis / Perbaikan / Rusak)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-filter-reset" onclick="closeStatusModal()">Batal</button>
                <button type="submit" class="btn-filter-submit">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStatusModal(vehicleId, plateNumber, currentStatus) {
        const modal = document.getElementById('statusModal');
        const form = document.getElementById('quickStatusForm');
        const text = document.getElementById('modalVehicleText');
        const select = document.getElementById('modal_status');

        form.action = `/memantau-kendaraan/${vehicleId}/status`;
        text.innerHTML = `Mengubah status operasional untuk armada pelat nomor <strong>${plateNumber}</strong>:`;

        if (currentStatus === 'maintenance' || currentStatus === 'perbaikan' || currentStatus === 'bengkel') {
            select.value = 'maintenance';
        } else if (currentStatus === 'beroperasi' || currentStatus === 'on duty' || currentStatus === 'operating') {
            select.value = 'beroperasi';
        } else {
            select.value = 'siap_jalan';
        }

        modal.classList.add('show');
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        modal.classList.remove('show');
    }

    // Close when clicking backdrop
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('statusModal');
        if (e.target === modal) {
            closeStatusModal();
        }
    });
</script>

@endsection
