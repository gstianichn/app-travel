@extends('layouts.admin')

@section('title', 'Detail User - ' . $user->name)

@section('page-title', 'Detail Pengguna')

@section('page-subtitle', 'Informasi profil, kontrol status akun, dan riwayat perjalanan pelanggan TravelKu.')

@section('content')

<style>
    .user-detail-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* =========================================
       BREADCRUMB & TOP NAV
    ========================================== */
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

    /* =========================================
       FLASH MESSAGES
    ========================================== */
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
       1. DATA PROFIL CARD
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
        padding: 28px 30px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 100%);
        color: white;
        gap: 20px;
        flex-wrap: wrap;
    }

    .hero-user-main {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .hero-avatar {
        width: 72px;
        height: 72px;
        border-radius: 20px;
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

    .hero-meta h1 {
        margin: 0 0 5px;
        font-size: 22px;
        font-weight: 700;
    }

    .hero-meta p {
        margin: 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
    }

    .hero-badge-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
    }

    .hero-badge-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .hero-badge-status.blocked {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* PROFILE DETAILS GRID */
    .profile-info-grid {
        padding: 26px 30px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        background: white;
    }

    .info-tile {
        padding: 14px 16px;
        border-radius: 12px;
        background: #fbfdfc;
        border: 1px solid #edf2ef;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-tile-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #788780;
    }

    .info-tile-value {
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
        word-break: break-word;
    }

    .whatsapp-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 6px;
        background: #25d366;
        color: white;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        transition: opacity 0.15s ease;
        width: fit-content;
    }

    .whatsapp-chip:hover {
        opacity: 0.88;
    }

    /* =========================================
       3. KONTROL AKUN SECTION
    ========================================== */
    .account-control-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 20px;
    }

    .control-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
    }

    .control-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .control-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .control-icon.block {
        background: #fee2e2;
        color: #dc2626;
    }

    .control-icon.unblock {
        background: #dcfce7;
        color: #15803d;
    }

    .control-icon.password {
        background: #fef3c7;
        color: #d97706;
    }

    .control-card-header h3 {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
    }

    .control-card-header p {
        margin: 0;
        font-size: 12px;
        color: #72817a;
        line-height: 1.4;
    }

    .control-desc-box {
        padding: 12px 14px;
        border-radius: 10px;
        background: #f8faf9;
        font-size: 12px;
        color: #55665e;
        line-height: 1.5;
        border: 1px solid #edf1ef;
    }

    .btn-toggle-block {
        width: 100%;
        padding: 11px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-toggle-block.danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .btn-toggle-block.danger:hover {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
    }

    .btn-toggle-block.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .btn-toggle-block.success:hover {
        background: #16a34a;
        color: white;
        border-color: #16a34a;
    }

    /* RESET PASSWORD FORM */
    .reset-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .form-group-reset {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group-reset label {
        font-size: 11px;
        font-weight: 700;
        color: #55665e;
    }

    .input-reset {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #dce5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        background: #fcfdfd;
        transition: border-color 0.2s ease;
    }

    .input-reset:focus {
        border-color: #087f55;
        background: white;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .reset-action-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 4px;
    }

    .btn-generate-rand {
        padding: 9px 12px;
        border: 1px solid #dce5e1;
        border-radius: 8px;
        background: white;
        color: #4b5a52;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-generate-rand:hover {
        background: #f3f6f4;
    }

    .btn-submit-reset {
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        background: #087f55;
        color: white;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-submit-reset:hover {
        background: #066b47;
    }

    /* =========================================
       2. RIWAYAT PESANAN TABLE
    ========================================== */
    .history-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .history-header {
        padding: 20px 24px;
        border-bottom: 1px solid #edf2ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .history-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .history-title h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #17221d;
    }

    .history-badge-count {
        padding: 3px 9px;
        border-radius: 12px;
        background: #e3f5ee;
        color: #087f55;
        font-size: 11px;
        font-weight: 700;
    }

    .history-stat-chips {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
    }

    .chip-stat-item {
        padding: 5px 11px;
        border-radius: 8px;
        background: #f7faf8;
        border: 1px solid #e5ede8;
        color: #4b5b53;
        font-weight: 600;
    }

    .history-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .history-table th {
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

    .history-table td {
        padding: 15px 20px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    .history-table tbody tr:hover {
        background: #fbfdfc;
    }

    .route-display {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: #17221d;
    }

    .arrow-green {
        color: #087f55;
    }

    /* TICKET STATUS BADGES */
    .ticket-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .ticket-status.completed {
        background: #dcfce7;
        color: #15803d;
    }

    .ticket-status.cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .ticket-status.expired {
        background: #fef3c7;
        color: #b45309;
    }

    .ticket-status.active {
        background: #e0f2fe;
        color: #0369a1;
    }

    .ticket-status.pending {
        background: #f3f4f6;
        color: #4b5563;
    }

    .empty-orders {
        padding: 50px 20px;
        text-align: center;
        color: #82928a;
    }

    .empty-orders-icon {
        font-size: 38px;
        margin-bottom: 8px;
    }

    /* =========================================
       RESPONSIVE
    ========================================== */
    @media (max-width: 900px) {
        .profile-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .account-control-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .profile-info-grid {
            grid-template-columns: 1fr;
            padding: 20px;
        }

        .profile-hero-top {
            padding: 20px;
        }

        .hero-user-main {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="user-detail-container">

    {{-- =========================================
         TOP NAVIGATION & BREADCRUMB
    ========================================== --}}
    <div class="detail-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>/</span>
            <a href="{{ route('dashboard.admin.kelola-user.index') }}">Kelola User</a>
            <span>/</span>
            <span>Detail Pengguna</span>
        </div>

        <div class="detail-top-actions">
            <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="btn-action-back">
                &larr; Kembali
            </a>
            <a href="{{ route('dashboard.admin.kelola-user.edit', $user->id) }}" class="btn-action-edit">
                Edit Data
            </a>
        </div>
    </div>


    {{-- =========================================
         FLASH NOTIFICATIONS
    ========================================== --}}
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

    @if($errors->any())
        <div class="flash-alert flash-error">
            <span></span>
            <span>
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </span>
        </div>
    @endif


    {{-- =========================================
         1. DATA PROFIL LENGKAP
    ========================================== --}}
    <div class="profile-hero-card">

        {{-- HERO BANNER --}}
        <div class="profile-hero-top">
            <div class="hero-user-main">
                <div class="hero-avatar">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="hero-meta">
                    <h1>{{ $user->name }}</h1>
                    <p>{{ $user->email }} &bull; ID Akun: #{{ $user->id }}</p>
                </div>
            </div>

            <div>
                @if(($user->status ?? 'active') === 'active')
                    <span class="hero-badge-status active">
                        ● Akun Aktif
                    </span>
                @else
                    <span class="hero-badge-status blocked">
                        ● Akun Diblokir / Nonaktif
                    </span>
                @endif
            </div>
        </div>

        {{-- INFO TILES GRID --}}
        <div class="profile-info-grid">

            {{-- NAMA LENGKAP --}}
            <div class="info-tile">
                <span class="info-tile-label">Nama Lengkap</span>
                <span class="info-tile-value">{{ $user->name }}</span>
            </div>

            {{-- NOMOR TELEPON / WHATSAPP --}}
            <div class="info-tile">
                <span class="info-tile-label">Nomor Telepon / WhatsApp</span>
                <span class="info-tile-value">{{ $user->phone ?? 'Belum Ditambahkan' }}</span>
                @if(!empty($user->phone))
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="whatsapp-chip">
                        <span></span>
                        <span>Chat WhatsApp</span>
                    </a>
                @endif
            </div>

            {{-- EMAIL & VERIFIKASI --}}
            <div class="info-tile">
                <span class="info-tile-label">Email Akun</span>
                <span class="info-tile-value">{{ $user->email }}</span>
                <div style="margin-top: 4px;">
                    @if($user->email_verified_at)
                        <span style="font-size: 11px; font-weight: 700; color: #16a34a;">Terverifikasi</span>
                    @else
                        <span style="font-size: 11px; font-weight: 700; color: #d97706;">⏳ Belum Verifikasi</span>
                    @endif
                </div>
            </div>

            {{-- TANGGAL BERGABUNG --}}
            <div class="info-tile">
                <span class="info-tile-label">Tanggal Bergabung</span>
                <span class="info-tile-value">
                    {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                </span>
                <span style="font-size: 11px; color: #7b8882;">
                    {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}
                </span>
            </div>

        </div>

    </div>


    {{-- =========================================
         3. KONTROL AKUN
    ========================================== --}}
    <div class="account-control-grid">

        {{-- BLOKIR / AKTIFKAN AKUN --}}
        <div class="control-card">
            <div>
                <div class="control-card-header">
                    @if(($user->status ?? 'active') === 'active')
                        <div>
                            <h3>Blokir Akun Pengguna</h3>
                            <p>Nonaktifkan akun pengguna bermasalah atau nomor fiktif</p>
                        </div>
                    @else
                        <div>
                            <h3>Buka Blokir Akun</h3>
                            <p>Pulihkan akses akun agar dapat kembali memesan tiket travel</p>
                        </div>
                    @endif
                </div>

                <div class="control-desc-box" style="margin-top: 14px;">
                    @if(($user->status ?? 'active') === 'active')
                        Jika akun diblokir, pengguna tidak akan dapat melakukan login, pemesanan tiket, ataupun mengakses fitur aplikasi.
                    @else
                        Akun saat ini sedang diblokir. Klik tombol di bawah untuk mengaktifkan kembali hak akses pengguna.
                    @endif
                </div>
            </div>

            <form
                action="{{ route('dashboard.admin.kelola-user.toggle-status', $user->id) }}"
                method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin {{ ($user->status ?? 'active') === 'active' ? 'MEMBLOKIR' : 'MENGAKTIFKAN KEMBALI' }} akun {{ $user->name }}?');"
            >
                @csrf
                @method('PATCH')

                @if(($user->status ?? 'active') === 'active')
                    <button type="submit" class="btn-toggle-block danger">
                        <span></span>
                        <span>Blokir Akun Ini</span>
                    </button>
                @else
                    <button type="submit" class="btn-toggle-block success">
                        <span></span>
                        <span>Buka Blokir & Aktifkan Kembali</span>
                    </button>
                @endif
            </form>
        </div>


        {{-- RESET KATA SANDI MANUAL --}}
        <div class="control-card">
            <div>
                <div class="control-card-header">
                    <div>
                        <h3>Reset Kata Sandi Manual</h3>
                        <p>Tetapkan kata sandi baru jika pelanggan kesulitan login</p>
                    </div>
                </div>

                <form
                    action="{{ route('dashboard.admin.kelola-user.reset-password', $user->id) }}"
                    method="POST"
                    class="reset-form"
                    style="margin-top: 14px;"
                    onsubmit="return confirm('Apakah Anda yakin ingin mengatur ulang kata sandi pengguna ini?');"
                >
                    @csrf
                    @method('PATCH')

                    <div class="form-group-reset">
                        <label for="new_password">Kata Sandi Baru (Min. 8 Karakter)</label>
                        <input
                            type="text"
                            name="password"
                            id="new_password"
                            class="input-reset"
                            placeholder="Masukkan kata sandi baru..."
                            required
                        >
                    </div>

                    <div class="form-group-reset">
                        <label for="new_password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input
                            type="text"
                            name="password_confirmation"
                            id="new_password_confirmation"
                            class="input-reset"
                            placeholder="Ulangi kata sandi baru..."
                            required
                        >
                    </div>

                    <div class="reset-action-row">
                        <button type="button" class="btn-generate-rand" onclick="generateRandomPassword()">
                            Sandi Acak
                        </button>

                        <button type="submit" class="btn-submit-reset">
                            Terapkan Sandi Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>


    {{-- =========================================
         2. RIWAYAT PESANAN PERJALANAN
    ========================================== --}}
    <div class="history-card">

        <div class="history-header">
            <div class="history-title">
                <h2>Riwayat Pesanan Perjalanan</h2>
                <span class="history-badge-count">{{ $totalBookings ?? 0 }} Pesanan</span>
            </div>

            <div class="history-stat-chips">
                <div class="chip-stat-item">
                    Total Pengeluaran: <strong style="color: #087f55;">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="history-table-wrap">
            @if(isset($bookings) && $bookings->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Rute Perjalanan</th>
                            <th>Jadwal Keberangkatan</th>
                            <th>Penumpang / Armada</th>
                            <th>Total Tarif</th>
                            <th style="text-align: right;">Status Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                            @php
                                // Rute
                                $sch = $b->schedule;
                                $origin = $sch?->route?->origin?->name ?? ($sch?->travelRoute?->origin ?? 'Asal');
                                $destination = $sch?->route?->destination?->name ?? ($sch?->travelRoute?->destination ?? 'Tujuan');

                                // Waktu keberangkatan
                                $depTime = '-';
                                if ($sch?->departure_time) {
                                    $depTime = date('H:i', strtotime($sch->departure_time)) . ' WIB';
                                } elseif ($sch?->departure_at) {
                                    $depTime = $sch->departure_at->format('H:i') . ' WIB';
                                }

                                $depDate = '-';
                                if ($sch?->departure_date) {
                                    $depDate = $sch->departure_date->format('d M Y');
                                } elseif ($sch?->departure_at) {
                                    $depDate = $sch->departure_at->format('d M Y');
                                }

                                // Status Tiket / Perjalanan
                                $rawStatus = strtolower($b->ticket?->status ?? $b->status ?? '');
                                if (in_array($rawStatus, ['completed', 'selesai', 'used', 'boarded'])) {
                                    $statusLabel = 'Selesai';
                                    $statusClass = 'completed';
                                } elseif (in_array($rawStatus, ['cancelled', 'batal', 'rejected'])) {
                                    $statusLabel = 'Batal';
                                    $statusClass = 'cancelled';
                                } elseif (in_array($rawStatus, ['expired', 'kedaluwarsa'])) {
                                    $statusLabel = '⏳ Kedaluwarsa';
                                    $statusClass = 'expired';
                                } elseif (in_array($rawStatus, ['paid', 'confirmed', 'lunas', 'success', 'scheduled'])) {
                                    $statusLabel = '● Siap Berangkat';
                                    $statusClass = 'active';
                                } else {
                                    $statusLabel = ucfirst($b->status ?? 'Menunggu');
                                    $statusClass = 'pending';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #17221d;">
                                        {{ $b->booking_code }}
                                    </div>
                                    <div style="font-size: 11px; color: #72817a;">
                                        {{ $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="route-display">
                                        <span>{{ $origin }}</span>
                                        <span class="arrow-green"></span>
                                        <span>{{ $destination }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 600; color: #17221d;">
                                        {{ $depTime }}
                                    </div>
                                    <div style="font-size: 11px; color: #72817a;">
                                        {{ $depDate }}
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 600;">
                                        {{ $b->total_passenger }} Penumpang
                                    </div>
                                    <div style="font-size: 11px; color: #72817a;">
                                        {{ $sch?->vehicle?->name ?? 'Minibus Travel' }}
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 700; color: #087f55;">
                                        Rp {{ number_format($b->total_price, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: 11px; color: #72817a;">
                                        {{ $b->payment?->payment_method ?? 'Digital' }}
                                    </div>
                                </td>

                                <td style="text-align: right;">
                                    <span class="ticket-status {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-orders">
                    <div style="font-weight: 700; color: #17221d; margin-bottom: 4px;">Belum Ada Riwayat Perjalanan</div>
                    <p style="font-size: 12px; margin: 0;">Pengguna ini belum pernah melakukan pemesanan tiket pada sistem TravelKu.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script>
    function generateRandomPassword() {
        const chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789#@!";
        let password = "Travel#";
        for (let i = 0; i < 4; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('new_password').value = password;
        document.getElementById('new_password_confirmation').value = password;
    }
</script>
@endpush

@endsection