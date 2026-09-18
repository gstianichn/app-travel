@extends('layouts.admin')

@section('title', 'Detail Driver - ' . ($driver->user?->name ?? 'Pengemudi'))

@section('page-title', 'Detail Driver & Riwayat Penugasan')

@section('page-subtitle', 'Informasi profil legalitas SIM, kontak darurat keluarga, status kesiapan kerja, dan catatan armada minibus.')

@section('content')

<style>
    .driver-detail-container {
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
       1. HERO PROFIL PENGEMUDI
    ========================================== */
    .profile-hero-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .profile-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 26px 30px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 100%);
        color: white;
        gap: 20px;
        flex-wrap: wrap;
    }

    .hero-driver-main {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .hero-avatar {
        width: 76px;
        height: 76px;
        border-radius: 20px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.18);
        border: 2px solid rgba(255, 255, 255, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-driver-info h2 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 800;
        color: white;
    }

    .hero-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.18);
        color: white;
        backdrop-filter: blur(4px);
    }

    .hero-tag.tag-standby {
        background: #10b981;
    }

    .hero-tag.tag-onduty {
        background: #0284c7;
    }

    .hero-tag.tag-onleave {
        background: #f59e0b;
    }

    /* HERO DETAILS GRID */
    .profile-hero-details {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 22px 30px;
        gap: 20px;
        background: #fafcfb;
        border-bottom: 1px solid #edf1ef;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 600;
        color: #7d8e86;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .detail-wa-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #16a34a;
        font-weight: 600;
        text-decoration: none;
        margin-top: 2px;
    }

    .detail-wa-btn:hover {
        text-decoration: underline;
    }

    /* =========================================
       TWO-COLUMN DETAIL GRID
    ========================================== */
    .detail-grid-layout {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 24px;
    }

    .detail-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* CONTENT CARDS */
    .content-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-head-title {
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 22px;
    }

    /* LEGALITY CARDS */
    .legality-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
    }

    .legality-item.warning-box {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .legality-item.danger-box {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .legality-item:last-child {
        margin-bottom: 0;
    }

    .legality-info h4 {
        margin: 0 0 2px;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }

    .legality-info p {
        margin: 0;
        font-size: 11px;
        color: #64748b;
    }

    /* BADGES */
    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .badge-valid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .badge-expiring {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-expired {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* EMERGENCY CONTACT HIGHLIGHT */
    .emergency-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border-radius: 12px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
    }

    .emergency-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #0284c7;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .emergency-details h4 {
        margin: 0 0 2px;
        font-size: 14px;
        font-weight: 700;
        color: #0369a1;
    }

    .emergency-details p {
        margin: 0 0 4px;
        font-size: 11px;
        color: #0284c7;
    }

    .emergency-phone-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 700;
        color: #0369a1;
        text-decoration: none;
    }

    .emergency-phone-btn:hover {
        text-decoration: underline;
    }

    /* STATUS CHANGER FORM */
    .status-quick-changer {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .status-radio-option {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .status-radio-option:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .status-radio-option.selected-option {
        border-color: #087f55;
        background: #f0fdf4;
    }

    .btn-update-status {
        margin-top: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        background: #087f55;
        color: white;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-update-status:hover {
        background: #066b47;
    }

    /* =========================================
       3. RIWAYAT PENUGASAN (MINIBUS & RUTE)
    ========================================== */
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }

    .schedule-table th {
        padding: 12px 16px;
        background: #fbfdfc;
        color: #72817a;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf1ef;
    }

    .schedule-table td {
        padding: 14px 16px;
        color: #27342e;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .minibus-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
        font-size: 11px;
    }

    .trip-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
    }

    .trip-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .trip-scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .trip-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 900px) {
        .detail-grid-layout {
            grid-template-columns: 1fr;
        }

        .profile-hero-details {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .profile-hero-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="driver-detail-container">

    {{-- BREADCRUMB & NAV --}}
    <div class="detail-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>/</span>
            <a href="{{ route('dashboard.admin.kelola-driver.index') }}">Kelola Driver</a>
            <span>/</span>
            <span>Detail: {{ $driver->user?->name ?? 'Pengemudi' }}</span>
        </div>

        <div class="detail-top-actions">
            <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="btn-action-back">
                ← Daftar Pengemudi
            </a>
            <a href="{{ route('dashboard.admin.kelola-driver.edit', $driver->id) }}" class="btn-action-edit">
                ✏️ Edit Profil & Dokumen
            </a>
        </div>
    </div>

    {{-- FLASH NOTIFICATIONS --}}
    @if(session('success'))
        <div class="flash-alert flash-success">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-alert flash-error">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================
         1. HERO PROFIL PENGEMUDI
    ========================================== --}}
    @php
        $statusClass = $driver->statusBadgeClass();
        $lic = $driver->licenseStatus();
    @endphp

    <div class="profile-hero-card">
        <div class="profile-hero-top">
            <div class="hero-driver-main">
                <div class="hero-avatar">
                    @if($driver->photo)
                        <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->user?->name }}">
                    @else
                        {{ strtoupper(substr($driver->user?->name ?? 'DR', 0, 2)) }}
                    @endif
                </div>
                <div class="hero-driver-info">
                    <h2>{{ $driver->user?->name ?? 'Pengemudi #' . $driver->id }}</h2>
                    <div class="hero-meta-tags">
                        <span class="hero-tag">
                            🪪 {{ $driver->license_type ?? 'SIM' }} - {{ $driver->license_number }}
                        </span>
                        <span class="hero-tag tag-{{ $statusClass }}">
                            ● {{ $driver->statusLabel() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-hero-details">
            <div class="detail-item">
                <span class="detail-label">Nomor WhatsApp Pengemudi</span>
                <span class="detail-value">{{ $driver->phone ?? $driver->user?->phone ?? '-' }}</span>
                @php
                    $phoneNum = $driver->phone ?? $driver->user?->phone;
                @endphp
                @if($phoneNum)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNum);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="detail-wa-btn">
                        💬 Hubungi WhatsApp
                    </a>
                @endif
            </div>

            <div class="detail-item">
                <span class="detail-label">Masa Berlaku SIM</span>
                <span class="detail-value">{{ $driver->license_expired_at ? $driver->license_expired_at->format('d M Y') : 'Belum ditentukan' }}</span>
                <span style="font-size: 11px; margin-top: 2px;">
                    <span class="badge-pill {{ $lic['badge_class'] }}">{{ $lic['label'] }}</span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Kontak Darurat Keluarga</span>
                <span class="detail-value">{{ $driver->emergency_contact_name ?? '-' }}</span>
                <span style="font-size: 11px; color: #64748b;">
                    Hubungan: {{ $driver->emergency_contact_relation ?? 'Keluarga' }}
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Total Penugasan Selesai</span>
                <span class="detail-value">{{ $driver->completedTripsCount() }} Perjalanan</span>
                <span style="font-size: 11px; color: #64748b;">
                    Dari {{ $driver->totalTripsCount() }} penugasan total
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================
         2. DETAIL GRID (LEGALITAS & STATUS KERJA)
    ========================================== --}}
    <div class="detail-grid-layout">

        {{-- KOLOM KIRI: LEGALITAS SIM & KONTAK DARURAT --}}
        <div class="detail-column">

            {{-- LEGALITAS DOKUMEN SIM --}}
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <span>🪪</span>
                        <span>Legalitas Dokumen Mengemudi (SIM)</span>
                    </div>
                    <span class="badge-pill {{ $lic['badge_class'] }}">
                        {{ $lic['label'] }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="legality-item {{ $lic['status'] === 'expired' ? 'danger-box' : ($lic['status'] === 'expiring_soon' ? 'warning-box' : '') }}">
                        <div class="legality-info">
                            <h4>Surat Izin Mengemudi: {{ $driver->license_number }}</h4>
                            <p>Golongan: <strong>{{ $driver->license_type ?? 'SIM Standar' }}</strong></p>
                            <p style="margin-top: 4px;">Masa Berlaku Hingga: <strong>{{ $driver->license_expired_at ? $driver->license_expired_at->translatedFormat('d F Y') : '-' }}</strong></p>
                        </div>
                        <div>
                            @if($lic['status'] === 'expired')
                                <span style="font-size: 11px; font-weight: 700; color: #dc2626;">⛔ Dilarang Jalan</span>
                            @elseif($lic['status'] === 'expiring_soon')
                                <span style="font-size: 11px; font-weight: 700; color: #d97706;">⚠️ Perpanjang Segera</span>
                            @else
                                <span style="font-size: 11px; font-weight: 700; color: #16a34a;">✓ Kelaikan Terverifikasi</span>
                            @endif
                        </div>
                    </div>

                    @if($driver->address)
                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2eae6;">
                            <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Alamat Domisili Pengemudi:</span>
                            <p style="margin: 4px 0 0; font-size: 12px; color: #1e293b; line-height: 1.4;">{{ $driver->address }}</p>
                        </div>
                    @endif

                    @if($driver->notes)
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e2eae6;">
                            <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Catatan Pengemudi:</span>
                            <p style="margin: 4px 0 0; font-size: 12px; color: #475569; font-style: italic;">{{ $driver->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KONTAK DARURAT KELUARGA --}}
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <span>🚨</span>
                        <span>Kontak Darurat Keluarga</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="emergency-box">
                        <div class="emergency-icon">
                            📞
                        </div>
                        <div class="emergency-details">
                            <h4>{{ $driver->emergency_contact_name ?? 'Belum diisi' }}</h4>
                            <p>Hubungan Keluarga: <strong>{{ $driver->emergency_contact_relation ?? 'Keluarga' }}</strong></p>
                            @if($driver->emergency_contact_phone)
                                @php
                                    $emPhone = preg_replace('/[^0-9]/', '', $driver->emergency_contact_phone);
                                    if (str_starts_with($emPhone, '0')) {
                                        $emPhone = '62' . substr($emPhone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $emPhone }}" target="_blank" class="emergency-phone-btn">
                                    <span>💬 Hubungi Kontak Darurat: {{ $driver->emergency_contact_phone }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: STATUS KESIAPAN KERJA --}}
        <div class="detail-column">
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <span>🚦</span>
                        <span>Status Kesiapan Kerja Saat Ini</span>
                    </div>
                    <span class="badge-pill tag-{{ $statusClass }}">
                        {{ $driver->statusLabel() }}
                    </span>
                </div>
                <div class="card-body">
                    <p style="margin: 0 0 14px; font-size: 12px; color: #64748b;">
                        Pilih status operasional pengemudi untuk mengontrol ketersediaan penugasan pada jadwal perjalanan armada travel:
                    </p>

                    <form action="{{ route('dashboard.admin.kelola-driver.update-status', $driver->id) }}" method="POST" class="status-quick-changer">
                        @csrf
                        @method('PATCH')

                        @php
                            $curr = strtolower($driver->status ?? 'standby');
                            if ($curr === 'available') $curr = 'standby';
                        @endphp

                        <label class="status-radio-option {{ $curr === 'standby' ? 'selected-option' : '' }}">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>🟢</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; color: #15803d;">Siap Jalan (Standby)</div>
                                    <div style="font-size: 11px; color: #64748b;">Pengemudi siap di pool untuk keberangkatan armada.</div>
                                </div>
                            </div>
                            <input type="radio" name="status" value="standby" {{ $curr === 'standby' ? 'checked' : '' }}>
                        </label>

                        <label class="status-radio-option {{ $curr === 'on_duty' ? 'selected-option' : '' }}">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>🔵</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; color: #0369a1;">Sedang Bertugas di Jalan</div>
                                    <div style="font-size: 11px; color: #64748b;">Pengemudi sedang mengemudikan perjalanan aktif.</div>
                                </div>
                            </div>
                            <input type="radio" name="status" value="on_duty" {{ $curr === 'on_duty' ? 'checked' : '' }}>
                        </label>

                        <label class="status-radio-option {{ $curr === 'on_leave' ? 'selected-option' : '' }}">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>🟡</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; color: #b45309;">Sedang Libur / Cuti</div>
                                    <div style="font-size: 11px; color: #64748b;">Pengemudi sedang izin istirahat atau cuti kerja.</div>
                                </div>
                            </div>
                            <input type="radio" name="status" value="on_leave" {{ $curr === 'on_leave' ? 'checked' : '' }}>
                        </label>

                        <button type="submit" class="btn-update-status">
                            Simpan Perubahan Status Kerja
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================
         3. RIWAYAT PENUGASAN (RUTE & UNIT MINIBUS)
    ========================================== --}}
    <div class="content-card">
        <div class="card-head">
            <div class="card-head-title">
                <span>🚐</span>
                <span>Riwayat Penugasan: Catatan Rute & Unit Minibus yang Pernah Dikemudikan</span>
            </div>
            <span style="font-size: 11px; font-weight: 700; color: #087f55; background: #e3f5ee; padding: 3px 9px; border-radius: 6px;">
                {{ $schedules->total() }} Catatan Penugasan
            </span>
        </div>

        <div class="table-responsive">
            @if($schedules->count() > 0)
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Jadwal Keberangkatan</th>
                            <th>Rute Perjalanan</th>
                            <th>Unit Minibus / Armada</th>
                            <th>Kapasitas Kursi</th>
                            <th>Status Penugasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $index => $sch)
                            @php
                                $originName = $sch->route?->origin?->name ?? $sch->travelRoute?->origin ?? 'Asal';
                                $destName = $sch->route?->destination?->name ?? $sch->travelRoute?->destination ?? 'Tujuan';
                                $vehicle = $sch->vehicle;
                            @endphp
                            <tr>
                                <td>
                                    <span style="color: #94a3b8; font-weight: 600;">
                                        {{ $schedules->firstItem() + $index }}
                                    </span>
                                </td>

                                {{-- JADWAL --}}
                                <td>
                                    <div style="font-weight: 700; color: #1e293b;">
                                        {{ $sch->departure_date ? $sch->departure_date->format('d M Y') : ($sch->departure_at ? $sch->departure_at->format('d M Y') : '-') }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">
                                        Pukul {{ $sch->departure_time ? \Carbon\Carbon::parse($sch->departure_time)->format('H:i') : ($sch->departure_at ? $sch->departure_at->format('H:i') : '-') }} WIB
                                    </div>
                                </td>

                                {{-- RUTE --}}
                                <td>
                                    <div style="font-weight: 700; color: #087f55; display: flex; align-items: center; gap: 6px;">
                                        <span>{{ $originName }}</span>
                                        <span>&rarr;</span>
                                        <span>{{ $destName }}</span>
                                    </div>
                                    <div style="font-size: 10px; color: #94a3b8;">
                                        Jadwal ID #{{ $sch->id }}
                                    </div>
                                </td>

                                {{-- UNIT MINIBUS --}}
                                <td>
                                    @if($vehicle)
                                        <div>
                                            <span class="minibus-badge">
                                                <span>🚐</span>
                                                <span>{{ $vehicle->name ?? 'Minibus' }} ({{ $vehicle->plate_number }})</span>
                                            </span>
                                        </div>
                                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                                            Tipe: {{ $vehicle->type ?? 'Minivan' }}
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Minibus belum dialokasikan</span>
                                    @endif
                                </td>

                                {{-- KAPASITAS --}}
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $sch->available_seats ?? 0 }} Kursi Kosong
                                    </div>
                                    <div style="font-size: 10px; color: #64748b;">
                                        Kapasitas: {{ $vehicle->capacity ?? 14 }} seat
                                    </div>
                                </td>

                                {{-- STATUS PERJALANAN --}}
                                <td>
                                    @php
                                        $tripStatus = strtolower($sch->status ?? 'scheduled');
                                    @endphp
                                    @if(in_array($tripStatus, ['completed', 'selesai']))
                                        <span class="trip-status-badge trip-completed">
                                            ✓ Selesai Jalan
                                        </span>
                                    @elseif(in_array($tripStatus, ['cancelled', 'batal']))
                                        <span class="trip-status-badge trip-cancelled">
                                            ✕ Dibatalkan
                                        </span>
                                    @else
                                        <span class="trip-status-badge trip-scheduled">
                                            ● Terjadwal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($schedules->hasPages())
                    <div style="padding: 16px 20px; border-top: 1px solid #edf1ef;">
                        {{ $schedules->links() }}
                    </div>
                @endif
            @else
                <div style="padding: 40px 20px; text-align: center; color: #82918a;">
                    <div style="font-size: 36px; margin-bottom: 8px;">🚐</div>
                    <h4 style="margin: 0 0 4px; font-size: 14px; font-weight: 700; color: #17221d;">Belum Ada Riwayat Penugasan Minibus</h4>
                    <p style="margin: 0; font-size: 12px; color: #76857e;">Pengemudi ini belum pernah ditugaskan membawa unit armada pada jadwal perjalanan travel.</p>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
