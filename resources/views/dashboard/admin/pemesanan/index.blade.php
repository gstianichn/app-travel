@extends('layouts.admin')

@section('title', 'Pemesanan Tiket Travel')

@section('page-title', 'Pemesanan Tiket')

@section('page-subtitle', 'Kelola data pemesanan, tiket loket walk-in/WhatsApp, denah kursi visual, manifes perjalanan, dan opsi reschedule.')

@section('content')

<style>
    .booking-page {
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

    .btn-create-booking {
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

    .btn-create-booking:hover {
        background: #066b47;
        box-shadow: 0 6px 18px rgba(8, 127, 85, 0.30);
        transform: translateY(-1px);
    }

    /* FLASH NOTIFICATIONS */
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

    .stat-box-icon.paid {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-box-icon.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-box-icon.walkin {
        background: #e0f2fe;
        color: #0284c7;
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
        grid-template-columns: 2fr 1fr 1.2fr auto;
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

    .booking-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .booking-table thead th {
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

    .booking-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        color: #213129;
        border-bottom: 1px solid #eef3f0;
        vertical-align: middle;
    }

    .booking-table tbody tr:hover {
        background: #fafcfb;
    }

    /* Kode Booking & Source */
    .code-badge {
        display: inline-flex;
        align-items: center;
        font-family: monospace;
        font-weight: 800;
        font-size: 13px;
        color: #087f55;
        letter-spacing: 0.5px;
    }

    .badge-source {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 5px;
        margin-top: 4px;
        width: fit-content;
    }

    .badge-source-online {
        background: #f3f4f6;
        color: #4b5563;
    }

    .badge-source-walkin {
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .badge-source-wa {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-source-phone {
        background: #fef3c7;
        color: #d97706;
    }

    /* Passenger info */
    .passenger-cell h4 {
        margin: 0 0 3px;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
    }

    .passenger-phone {
        font-size: 12px;
        color: #6a7972;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .link-wa {
        color: #15803d;
        text-decoration: none;
        font-weight: 600;
    }

    .link-wa:hover {
        text-decoration: underline;
    }

    /* Seat Badges */
    .seat-badge-list {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .seat-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e3f5ee;
        color: #087f55;
        font-weight: 700;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #c8ebd8;
    }

    /* Status Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-status-paid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-status-pending {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .badge-status-cancelled {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Action buttons */
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

    .btn-action-icon.cancel:hover {
        border-color: #b91c1c;
        color: #b91c1c;
        background: #fee2e2;
    }

    /* Empty state */
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

<div class="booking-page">

    {{-- BREADCRUMB & HEADER --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>&rsaquo;</span>
                <span>Pemesanan</span>
            </div>
            <div class="header-text">
                <h1>Pemesanan Tiket Travel</h1>
                <p>Kelola daftar tiket, denah kursi visual, input loket walk-in/WhatsApp, manifes sopir, dan reschedule.</p>
            </div>
        </div>

        <a href="{{ route('dashboard.admin.pemesanan.create-manual') }}" class="btn-create-booking">
            <span>+</span>
            <span>Input Tiket Loket (Walk-in / WA)</span>
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

    {{-- STATS SUMMARY --}}
    <div class="stats-row">
        {{-- TOTAL PEMESANAN --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Pemesanan</div>
                <div class="stat-box-num">{{ number_format($totalBookings) }}</div>
            </div>
        </div>

        {{-- LUNAS HARI INI --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Lunas Hari Ini</div>
                <div class="stat-box-num" style="color: #15803d;">{{ number_format($todayPaidBookings) }}</div>
            </div>
        </div>

        {{-- PENDING / BAYAR LOKET --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Menunggu Pembayaran</div>
                <div class="stat-box-num" style="color: #d97706;">{{ number_format($pendingBookings) }}</div>
            </div>
            <div class="stat-box-icon pending">
                ⏳
            </div>
        </div>

        {{-- TIKET LOKET / WALK-IN --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Tiket Loket & WA</div>
                <div class="stat-box-num" style="color: #0284c7;">{{ number_format($walkInBookings) }}</div>
            </div>
        </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.pemesanan.index') }}" method="GET" class="filter-form admin-filter-form">
            {{-- SEARCH --}}
            <div class="filter-group">
                <label class="filter-label" for="search">Cari Kode / Pemesan / HP</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="filter-input"
                    placeholder="Contoh: TRV-2026, Ahmad, 0812..."
                    value="{{ request('search') }}"
                >
            </div>

            {{-- STATUS BAYAR --}}
            <div class="filter-group">
                <label class="filter-label" for="status">Status Pembayaran</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Lunas</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Bayar (Pending)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- SUMBER ASAL PESANAN --}}
            <div class="filter-group">
                <label class="filter-label" for="source">Saluran Pemesanan</label>
                <select name="source" id="source" class="filter-select">
                    <option value="all">Semua Saluran</option>
                    <option value="walk_in" {{ request('source') === 'walk_in' ? 'selected' : '' }}>Loket / Walk-in</option>
                    <option value="whatsapp" {{ request('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp CS</option>
                    <option value="online" {{ request('source') === 'online' ? 'selected' : '' }}>Website Online</option>
                </select>
            </div>

            {{-- BUTTONS --}}
            <div class="filter-buttons">
                <button type="submit" class="btn-filter-submit">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'status', 'source', 'schedule_id']))
                    <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL PEMESANAN --}}
    <div class="table-card">
        <div class="table-container">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Kode & Tanggal Pesan</th>
                        <th>Pemesan & Kontak</th>
                        <th>Rute & Jadwal Keberangkatan</th>
                        <th>Kursi Terpilih</th>
                        <th>Total & Status</th>
                        <th style="text-align: right;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        @php
                            $routeLabel = $b->schedule?->route 
                                ? ($b->schedule->route->origin?->name . ' ke ' . $b->schedule->route->destination?->name)
                                : ($b->schedule?->travelRoute?->name ?? 'Rute Travel');
                        @endphp
                        <tr>
                            {{-- KODE & WAKTU PESAN --}}
                            <td>
                                <div>
                                    <span class="code-badge">{{ $b->booking_code }}</span>
                                </div>
                                <div style="font-size: 11px; color: #72817a; margin-top: 2px;">
                                    {{ $b->created_at ? $b->created_at->format('d M Y, H:i') : '-' }} WIB
                                </div>
                                <div>
                                    <span class="badge-source {{ $b->sourceBadgeClass() }}">
                                        {{ $b->sourceLabel() }}
                                    </span>
                                </div>
                            </td>

                            {{-- PEMESAN & KONTAK --}}
                            <td>
                                <div class="passenger-cell">
                                    <h4>{{ $b->customerName() }}</h4>
                                    <div class="passenger-phone">
                                        <span>{{ $b->customerPhone() }}</span>
                                        @if($b->customerPhone() && $b->customerPhone() !== '-')
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $b->customerPhone());
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($b->customerName()) }},%20konfirmasi%20tiket%20travel%20{{ $b->booking_code }}" target="_blank" class="link-wa" title="Chat WhatsApp">
                                                (Chat WA)
                                            </a>
                                        @endif
                                    </div>
                                    <span style="font-size: 11px; color: #72817a;">
                                        Total {{ $b->total_passenger }} Penumpang
                                    </span>
                                </div>
                            </td>

                            {{-- RUTE & JADWAL JALAN --}}
                            <td>
                                <div>
                                    <strong style="color: #17221d; font-size: 13px;">{{ $routeLabel }}</strong>
                                </div>
                                <div style="font-size: 12px; color: #495750; margin-top: 2px;">
                                    {{ $b->schedule?->departure_date ? $b->schedule->departure_date->format('d M Y') : '-' }}
                                    &bull; Pukul {{ $b->schedule?->departure_time ? \Carbon\Carbon::parse($b->schedule->departure_time)->format('H:i') : '-' }} WIB
                                </div>
                                <div style="font-size: 11px; color: #72817a; margin-top: 2px;">
                                    {{ $b->schedule?->vehicle?->name ?? 'Minibus' }} ({{ $b->schedule?->vehicle?->plate_number ?? '-' }})
                                </div>
                            </td>

                            {{-- KURSI TERPILIH --}}
                            <td>
                                <div class="seat-badge-list">
                                    @forelse($b->passengers as $p)
                                        <span class="seat-pill" title="{{ $p->name }}">
                                            {{ $p->seat?->seat_number ?? '-' }}
                                        </span>
                                    @empty
                                        <span style="color: #9ca3af; font-size: 12px;">Belum dialokasikan</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- TOTAL & STATUS --}}
                            <td>
                                <div style="font-weight: 700; color: #17221d; font-size: 13px;">
                                    Rp {{ number_format($b->total_price, 0, ',', '.') }}
                                </div>
                                <div style="margin-top: 3px;">
                                    <span class="badge-status {{ $b->statusBadgeClass() }}">
                                        {{ $b->statusLabel() }}
                                    </span>
                                </div>
                                <div style="font-size: 11px; color: #72817a; margin-top: 2px;">
                                    {{ $b->payment?->payment_method ?? 'Metode Loket' }}
                                </div>
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- DETAIL TIKET --}}
                                    <a
                                        href="{{ route('dashboard.admin.pemesanan.show', $b->id) }}"
                                        class="btn-action-icon"
                                        title="Detail E-Ticket & Transaksi"
                                    >
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    {{-- SEAT MAP JADWAL --}}
                                    @if($b->schedule_id)
                                        <a
                                            href="{{ route('dashboard.admin.pemesanan.seat-map', $b->schedule_id) }}"
                                            class="btn-action-icon"
                                            title="Denah Kursi Visual Jadwal Ini"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/></svg>
                                        </a>

                                        {{-- MANIFES --}}
                                        <a
                                            href="{{ route('dashboard.admin.pemesanan.manifes', $b->schedule_id) }}"
                                            class="btn-action-icon"
                                            title="Manifes Perjalanan Sopir"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                        </a>
                                    @endif

                                    {{-- RESCHEDULE --}}
                                    @if(strtolower($b->status) !== 'cancelled')
                                        <a
                                            href="{{ route('dashboard.admin.pemesanan.reschedule', $b->id) }}"
                                            class="btn-action-icon"
                                            title="Ubah Jadwal (Reschedule) & Geser Kursi"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </a>

                                        {{-- BATALKAN (Custom Popup Modal, tanpa confirm localhost) --}}
                                        <button
                                            type="button"
                                            class="btn-action-icon cancel"
                                            title="Batalkan Pemesanan"
                                            data-open-cancel-modal
                                            data-cancel-action="{{ route('dashboard.admin.pemesanan.cancel', $b->id) }}"
                                            data-booking-code="{{ $b->booking_code }}"
                                            data-customer-name="{{ $b->contact_name ?? $b->user?->name ?? 'Penumpang' }}"
                                            data-total-amount="Rp {{ number_format((float) $b->total_amount, 0, ',', '.') }}"
                                            data-is-paid="{{ in_array(strtolower((string) $b->status), ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed', 'refund_pending'], true) ? '1' : '0' }}"
                                        >
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-width="2"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state-box">
                                    <h4 class="empty-state-title">Belum Ada Pemesanan Tiket</h4>
                                    <p class="empty-state-desc">
                                        @if(request()->hasAny(['search', 'status', 'source', 'schedule_id']))
                                            Tidak ditemukan data pemesanan yang cocok dengan kriteria pencarian Anda. Coba atur ulang filter.
                                        @else
                                            Belum ada transaksi pemesanan tiket yang tercatat di sistem. Anda dapat menginput tiket walk-in untuk penumpang loket sekarang.
                                        @endif
                                    </p>
                                    <a href="{{ route('dashboard.admin.pemesanan.create-manual') }}" class="btn-create-booking" style="margin-top: 6px;">
                                        + Input Tiket Loket (Walk-in)
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="pagination-bar">
                <span style="font-size: 12px; color: #6a7972;">
                    Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }} pesanan tiket
                </span>
                <div>
                    {{ $bookings->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- CUSTOM POPUP MODAL PEMBATALAN TIKET (Tanpa Alert Localhost) --}}
<div id="adminCancelBookingModal" role="dialog" aria-modal="true" aria-labelledby="adminCancelModalTitle" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px; background: rgba(15, 29, 24, 0.62); backdrop-filter: blur(4px);">
    <div style="width: 100%; max-width: 480px; background: #ffffff; border-radius: 18px; border: 1px solid #e2ebe6; box-shadow: 0 24px 60px rgba(15, 29, 24, 0.25); overflow: hidden; animation: modalPopIn 0.2s ease-out;">
        <div style="padding: 20px 24px; background: #fef2f2; border-bottom: 1px solid #fecaca; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 id="adminCancelModalTitle" style="margin: 0; font-size: 16px; font-weight: 800; color: #991b1b;">Konfirmasi Pembatalan Tiket</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #b91c1c;">Tindakan ini akan membatalkan pesanan & mengosongkan kursi</p>
                </div>
            </div>
            <button type="button" data-close-cancel-modal style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #fecaca; background: #ffffff; color: #991b1b; font-weight: 800; cursor: pointer;">✕</button>
        </div>

        <form id="adminCancelBookingForm" method="POST" action="" style="padding: 20px 24px;">
            @csrf
            @method('PATCH')

            <div style="padding: 12px 14px; border-radius: 12px; background: #f8faf9; border: 1px solid #e2ebe6; margin-bottom: 14px; font-size: 13px; color: #16251f;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: #64748b;">Kode Booking:</span>
                    <strong id="cancelModalCode" style="font-family: monospace; color: #0f172a;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: #64748b;">Nama Penumpang:</span>
                    <strong id="cancelModalCustomer">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Total Transaksi:</span>
                    <strong id="cancelModalAmount" style="color: #0f766e;">-</strong>
                </div>
            </div>

            <div id="cancelModalRefundNotice" style="display: none; padding: 10px 12px; border-radius: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 12px; margin-bottom: 14px; line-height: 1.45;">
                <strong>Refund & Notifikasi Otomatis:</strong> Karena tiket ini sudah terbayar, sistem akan otomatis mengembalikan dana (Refund 100%) dan mengirimkan notifikasi pembatalan ke akun & email penumpang.
            </div>

            <div style="margin-bottom: 18px;">
                <label for="cancelReasonInput" style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px;">Alasan Pembatalan (Dikirim ke Notifikasi Penumpang)</label>
                <input
                    type="text"
                    id="cancelReasonInput"
                    name="cancel_reason"
                    value="Dibatalkan oleh Admin Operasional TravelKu"
                    placeholder="Contoh: Perubahan operasional armada / jadwal"
                    style="width: 100%; height: 40px; padding: 0 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 13px; color: #0f172a;"
                >
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                <button type="button" data-close-cancel-modal style="height: 40px; padding: 0 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; font-size: 13px; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 18px; border-radius: 10px; border: none; background: #dc2626; color: #ffffff; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                    Ya, Batalkan Tiket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('adminCancelBookingModal');
    const form = document.getElementById('adminCancelBookingForm');
    if (!modal || !form) return;

    const codeEl = document.getElementById('cancelModalCode');
    const customerEl = document.getElementById('cancelModalCustomer');
    const amountEl = document.getElementById('cancelModalAmount');
    const refundNoticeEl = document.getElementById('cancelModalRefundNotice');

    document.querySelectorAll('[data-open-cancel-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            form.action = btn.dataset.cancelAction || '';
            if (codeEl) codeEl.textContent = btn.dataset.bookingCode || '-';
            if (customerEl) customerEl.textContent = btn.dataset.customerName || '-';
            if (amountEl) amountEl.textContent = btn.dataset.totalAmount || '-';
            if (refundNoticeEl) {
                refundNoticeEl.style.display = btn.dataset.isPaid === '1' ? 'block' : 'none';
            }
            modal.style.display = 'flex';
        });
    });

    modal.querySelectorAll('[data-close-cancel-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

@endsection
