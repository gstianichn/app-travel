@extends('layouts.admin')

@section('title', 'Detail Unit Armada - ' . $vehicle->plate_number)

@section('page-title', 'Detail Unit Armada')

@section('page-subtitle', 'Informasi identitas unit, legalitas uji KIR & pajak STNK, status operasional, dan riwayat penugasan travel.')

@section('content')

<style>
    .fleet-detail-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* BREADCRUMB & TOP NAV */
    .detail-nav-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-trail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #7b8882;
    }

    .breadcrumb-trail a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-trail a:hover {
        text-decoration: underline;
    }

    .detail-top-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        background: white;
        color: #55665e;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-back:hover {
        background: #f3f6f4;
        color: #17221d;
    }

    .btn-action-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        background: #087f55;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-edit:hover {
        background: #066b47;
    }

    /* FLASH NOTIFICATIONS */
    .flash-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .flash-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .flash-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    /* =========================================
       HERO CARD UNIT
    ========================================== */
    .vehicle-hero-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .hero-main {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .hero-thumb-img {
        width: 80px;
        height: 80px;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid #e2eae6;
        background: #f1f5f3;
    }

    .hero-thumb-fallback {
        width: 80px;
        height: 80px;
        border-radius: 14px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        border: 2px solid #c9ebd9;
    }

    /* Plat Nomor Badge Style Indonesia */
    .plate-number-hero {
        display: inline-flex;
        background: #111827;
        color: #f9fafb;
        padding: 5px 14px;
        border-radius: 8px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        font-size: 16px;
        letter-spacing: 1.5px;
        border: 2px solid #374151;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
        margin-bottom: 6px;
    }

    .hero-text h1 {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 800;
        color: #17221d;
    }

    .hero-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #6a7972;
        flex-wrap: wrap;
    }

    .hero-badge-type {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
    }

    .hero-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    /* Status Badge */
    .badge-unit-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
    }

    .badge-ready {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-ready::before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 8px;
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
        width: 8px;
        height: 8px;
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
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #dc2626;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
    }

    /* =========================================
       MAIN GRID LAYOUT (2 COLUMNS)
    ========================================== */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 22px;
    }

    .info-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .info-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .info-card-header h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* KEY-VALUE LIST */
    .spec-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .spec-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 10px;
        border-bottom: 1px solid #f2f5f3;
        font-size: 13px;
    }

    .spec-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .spec-label {
        color: #6a7972;
        font-weight: 500;
    }

    .spec-value {
        color: #17221d;
        font-weight: 600;
        text-align: right;
    }

    /* FACILITIES PILLS */
    .facilities-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .facility-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 7px;
        background: #f1f8f5;
        color: #087f55;
        border: 1px solid #d2ebd9;
        font-size: 11px;
        font-weight: 600;
    }

    /* QUICK STATUS CARD */
    .quick-status-card {
        background: #f8faf9;
        border: 1px solid #e2eae6;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .quick-status-card h4 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .quick-status-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .quick-select {
        flex: 1;
        height: 38px;
        padding: 0 12px;
        border: 1px solid #d4dfda;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        background: white;
        outline: none;
    }

    .btn-quick-save {
        height: 38px;
        padding: 0 16px;
        background: #087f55;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-quick-save:hover {
        background: #066b47;
    }

    /* =========================================
       LEGALITY STATUS CARDS (KIR & STNK)
    ========================================== */
    .legality-dual-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .legality-box {
        border: 1px solid #e2eae6;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #fafcfb;
    }

    .legality-box.valid {
        border-color: #bbf7d0;
        background: #fcfdfd;
    }

    .legality-box.warning {
        border-color: #fde68a;
        background: #fffdfa;
    }

    .legality-box.expired {
        border-color: #fecaca;
        background: #fffcfc;
    }

    .legality-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .legality-title {
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-legal {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 5px;
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

    .legality-date-display {
        font-size: 18px;
        font-weight: 800;
        color: #17221d;
    }

    .legality-subtext {
        font-size: 12px;
        color: #6a7972;
    }

    /* =========================================
       SCHEDULES / TRIPS HISTORY
    ========================================== */
    .schedule-history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .schedule-history-table th {
        background: #f7faf8;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #55655e;
        border-bottom: 1px solid #e2eae6;
        text-align: left;
    }

    .schedule-history-table td {
        padding: 12px;
        border-bottom: 1px solid #eef3f0;
        color: #213129;
        vertical-align: middle;
    }

    .schedule-history-table tr:hover {
        background: #fafcfb;
    }

    .badge-sch-status {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 5px;
    }

    .badge-sch-scheduled {
        background: #e0f2fe;
        color: #0284c7;
    }

    .badge-sch-departed {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-sch-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-sch-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 860px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .legality-dual-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="fleet-detail-container">

    {{-- BREADCRUMB & ACTIONS --}}
    <div class="detail-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}">Memantau Kendaraan</a>
            <span>&rsaquo;</span>
            <span>{{ $vehicle->plate_number }}</span>
        </div>

        <div class="detail-top-actions">
            <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="btn-action-back">
                &larr; Kembali ke Daftar
            </a>
            <a href="{{ route('dashboard.admin.memantau-kendaraan.edit', $vehicle->id) }}" class="btn-action-edit">
                Edit Data Unit
            </a>
        </div>
    </div>

    {{-- FLASH ALERTS --}}
    @if(session('success'))
        <div class="flash-alert flash-success">
            <span></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-alert flash-error">
            <span></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- HERO PROFIL ARMADA --}}
    <div class="vehicle-hero-card">
        <div class="hero-main">
            @if($vehicle->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($vehicle->photo))
                <img src="{{ asset('storage/' . $vehicle->photo) }}" alt="{{ $vehicle->name }}" class="hero-thumb-img">
            @else
                <div class="hero-thumb-fallback">Belum ada foto</div>
            @endif

            <div class="hero-text">
                <div>
                    <span class="plate-number-hero">{{ $vehicle->plate_number }}</span>
                </div>
                <h1>{{ $vehicle->name }}</h1>
                <div class="hero-meta">
                    <span class="hero-badge-type">{{ $vehicle->type }}</span>
                    <span>&bull;</span>
                    <span><strong>{{ $vehicle->capacity }} Kursi Penumpang</strong></span>
                    <span>&bull;</span>
                    <span>Tahun: {{ $vehicle->manufacture_year ?? '-' }}</span>
                    <span>&bull;</span>
                    <span>{{ $vehicle->schedules->count() }} Penugasan Jadwal</span>
                </div>
            </div>
        </div>

        <div class="hero-right">
            <span class="badge-unit-status {{ $vehicle->statusBadgeClass() }}">
                {{ $vehicle->statusLabel() }}
            </span>
            <span style="font-size: 11px; color: #72817a;">
                ID Armada: #{{ $vehicle->id }} &bull; Terdaftar {{ $vehicle->created_at ? $vehicle->created_at->format('d M Y') : '-' }}
            </span>
        </div>
    </div>

    {{-- 2 COLUMNS DETAIL GRID --}}
    <div class="detail-grid">

        {{-- LEFT COLUMN: IDENTITAS & STATUS --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- SPESIFIKASI & IDENTITAS UNIT --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h3>Spesifikasi & Identitas Unit</h3>
                </div>
                <div class="info-card-body">
                    <div class="spec-list">
                        <div class="spec-row">
                            <span class="spec-label">Nomor Polisi (Pelat)</span>
                            <span class="spec-value" style="font-family: monospace; font-size: 14px;">{{ $vehicle->plate_number }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Tipe Minibus</span>
                            <span class="spec-value">{{ $vehicle->type }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Nama Armada</span>
                            <span class="spec-value">{{ $vehicle->name }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Total Kapasitas Kursi</span>
                            <span class="spec-value" style="color: #087f55;">{{ $vehicle->capacity }} Penumpang</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Tahun Pembuatan</span>
                            <span class="spec-value">{{ $vehicle->manufacture_year ?? 'Tidak tercatat' }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Nomor Rangka (VIN)</span>
                            <span class="spec-value" style="font-family: monospace;">{{ $vehicle->chassis_number ?? '-' }}</span>
                        </div>
                        <div class="spec-row">
                            <span class="spec-label">Nomor Mesin</span>
                            <span class="spec-value" style="font-family: monospace;">{{ $vehicle->engine_number ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- FASILITAS ARMADA --}}
                    <div style="margin-top: 6px;">
                        <span class="spec-label" style="display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600;">
                            Fasilitas Armada yang Tersedia:
                        </span>
                        @if(is_array($vehicle->facilities) && count($vehicle->facilities) > 0)
                            <div class="facilities-wrap">
                                @foreach($vehicle->facilities as $fac)
                                    <span class="facility-pill">
                                        {{ $fac }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size: 12px; color: #9ca3af;">Belum ada fasilitas khusus dicatat.</span>
                        @endif
                    </div>

                    {{-- CATATAN KONDISI --}}
                    @if($vehicle->notes)
                        <div style="margin-top: 6px; padding: 12px; background: #fbfdfc; border: 1px solid #eef3f0; border-radius: 10px;">
                            <span class="spec-label" style="display: block; margin-bottom: 4px; font-size: 11px; font-weight: 600;">
                                Catatan Pemeliharaan / Riwayat Servis:
                            </span>
                            <p style="margin: 0; font-size: 12px; color: #2a3b33; line-height: 1.5;">
                                {{ $vehicle->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- WIDGET GANTI STATUS CEPAT --}}
            <div class="quick-status-card">
                <h4>Perbarui Status Unit Operasional</h4>
                <p style="margin: 0; font-size: 12px; color: #6a7972;">
                    Ubah status kesiapan armada langsung saat mobil selesai servis atau hendak ditugaskan ke perjalanan.
                </p>
                <form action="{{ route('dashboard.admin.memantau-kendaraan.update-status', $vehicle->id) }}" method="POST" class="quick-status-form">
                    @csrf
                    @method('PATCH')
                    @php
                        $vStatus = strtolower($vehicle->status);
                        if (in_array($vStatus, ['maintenance', 'perbaikan', 'bengkel'])) {
                            $norm = 'maintenance';
                        } elseif (in_array($vStatus, ['beroperasi', 'on duty', 'operating', 'in_trip'])) {
                            $norm = 'beroperasi';
                        } else {
                            $norm = 'siap_jalan';
                        }
                    @endphp
                    <select name="status" class="quick-select">
                        <option value="siap_jalan" {{ $norm === 'siap_jalan' ? 'selected' : '' }}>Siap Jalan (Standby)</option>
                        <option value="beroperasi" {{ $norm === 'beroperasi' ? 'selected' : '' }}>Sedang Beroperasi (Jalan)</option>
                        <option value="maintenance" {{ $norm === 'maintenance' ? 'selected' : '' }}>Masuk Bengkel (Servis)</option>
                    </select>
                    <button type="submit" class="btn-quick-save">
                        Simpan
                    </button>
                </form>
            </div>

        </div>

        {{-- RIGHT COLUMN: LEGALITAS KELAIKAN & RIWAYAT JADWAL --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- CARD LEGALITAS & KELAIKAN JALAN (KIR & PAJAK STNK) --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h3>Legalitas & Kelaikan Dokumen Armada</h3>
                    <span style="font-size: 11px; color: #72817a;">Wajib diperpanjang berkala</span>
                </div>
                <div class="info-card-body">
                    <div class="legality-dual-grid">

                        {{-- WIDGET UJI KIR DISHUB --}}
                        @php
                            $kir = $vehicle->kirStatus();
                            $kirBoxClass = match($kir['status']) {
                                'valid' => 'valid',
                                'expiring_soon' => 'warning',
                                'expired' => 'expired',
                                default => ''
                            };
                        @endphp
                        <div class="legality-box {{ $kirBoxClass }}">
                            <div class="legality-box-header">
                                <span class="legality-title">
                                    Masa Berlaku KIR
                                </span>
                                <span class="badge-legal {{ $kir['badge_class'] }}">
                                    {{ $kir['label'] }}
                                </span>
                            </div>

                            <div class="legality-date-display">
                                {{ $vehicle->kir_expiry_date ? $vehicle->kir_expiry_date->format('d M Y') : 'Belum Ditentukan' }}
                            </div>

                            <div class="legality-subtext">
                                @if($kir['status'] === 'expired')
                                    <span style="color: #b91c1c; font-weight: 600;">Uji KIR telah kedaluwarsa. Armada dilarang beroperasi sebelum lulus uji berkala Dishub.</span>
                                @elseif($kir['status'] === 'expiring_soon')
                                    <span style="color: #d97706; font-weight: 600;">⏳ Segera jadwalkan uji berkala Dishub dalam {{ $kir['days_left'] }} hari ke depan.</span>
                                @elseif($kir['status'] === 'valid')
                                    <span style="color: #15803d; font-weight: 600;">Kondisi kelaikan teknis diakui resmi Dishub (sisa {{ $kir['days_left'] }} hari).</span>
                                @else
                                    <span>Mohon masukkan tanggal masa berlaku uji KIR pada formulir edit armada.</span>
                                @endif
                            </div>
                        </div>

                        {{-- WIDGET PAJAK STNK --}}
                        @php
                            $stnk = $vehicle->stnkStatus();
                            $stnkBoxClass = match($stnk['status']) {
                                'valid' => 'valid',
                                'expiring_soon' => 'warning',
                                'expired' => 'expired',
                                default => ''
                            };
                        @endphp
                        <div class="legality-box {{ $stnkBoxClass }}">
                            <div class="legality-box-header">
                                <span class="legality-title">
                                    Pajak STNK
                                </span>
                                <span class="badge-legal {{ $stnk['badge_class'] }}">
                                    {{ $stnk['label'] }}
                                </span>
                            </div>

                            <div class="legality-date-display">
                                {{ $vehicle->stnk_tax_due_date ? $vehicle->stnk_tax_due_date->format('d M Y') : 'Belum Ditentukan' }}
                            </div>

                            <div class="legality-subtext">
                                @if($stnk['status'] === 'expired')
                                    <span style="color: #b91c1c; font-weight: 600;">Pajak STNK telah jatuh tempo. Segera lakukan perpanjangan di Samsat terdekat.</span>
                                @elseif($stnk['status'] === 'expiring_soon')
                                    <span style="color: #d97706; font-weight: 600;">⏳ Batas pembayaran pajak tersisa {{ $stnk['days_left'] }} hari lagi.</span>
                                @elseif($stnk['status'] === 'valid')
                                    <span style="color: #15803d; font-weight: 600;">Pajak kendaraan aktif dan legalitas jalan berlaku aman (sisa {{ $stnk['days_left'] }} hari).</span>
                                @else
                                    <span>Mohon masukkan tanggal jatuh tempo pajak pada formulir edit armada.</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RIWAYAT PENUGASAN JADWAL PERJALANAN --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h3>Penugasan Jadwal Perjalanan Terakhir</h3>
                    <span style="font-size: 11px; color: #72817a;">Maksimal 10 jadwal terakhir</span>
                </div>
                <div class="info-card-body" style="padding: 0;">
                    @if($vehicle->schedules->count() > 0)
                        <table class="schedule-history-table">
                            <thead>
                                <tr>
                                    <th>Rute Perjalanan</th>
                                    <th>Waktu Keberangkatan</th>
                                    <th>Driver Bertugas</th>
                                    <th>Status Jadwal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->schedules as $sch)
                                    @php
                                        $routeLabel = $sch->route 
                                            ? ($sch->route->origin?->name . ' ke ' . $sch->route->destination?->name) 
                                            : ($sch->travelRoute?->name ?? 'Rute Travel');
                                        
                                        $schStatus = strtolower($sch->status ?? 'scheduled');
                                        $badgeClass = match($schStatus) {
                                            'departed', 'operating', 'in_trip' => 'badge-sch-departed',
                                            'completed' => 'badge-sch-completed',
                                            'cancelled' => 'badge-sch-cancelled',
                                            default => 'badge-sch-scheduled',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $routeLabel }}</strong>
                                        </td>
                                        <td>
                                            <div>{{ $sch->departure_date ? $sch->departure_date->format('d M Y') : '-' }}</div>
                                            <div style="font-size: 11px; color: #72817a;">Pukul {{ $sch->departure_time ? \Carbon\Carbon::parse($sch->departure_time)->format('H:i') : '-' }} WIB</div>
                                        </td>
                                        <td>
                                            @if($sch->driver && $sch->driver->user)
                                                <span>{{ $sch->driver->user->name }}</span>
                                            @else
                                                <span style="color: #9ca3af;">Belum ditentukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-sch-status {{ $badgeClass }}">
                                                {{ ucfirst($sch->status ?? 'Terjadwal') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="padding: 32px 20px; text-align: center; color: #72817a; font-size: 13px;">
                            <div style="font-size: 28px; margin-bottom: 6px;"></div>
                            Belum ada jadwal perjalanan yang ditugaskan untuk unit armada ini.
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
