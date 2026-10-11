@extends('layouts.admin')

@section('title', 'Kelola Refund & Pembatalan Tiket - Admin Utama')

@section('page-title', 'Kelola Refund & Pembatalan')

@section('page-subtitle', 'Mengatur permohonan pembatalan tiket, transparansi pengembalian dana, potongan biaya admin, dan pelepasan kursi armada.')

@section('content')

<style>
    .refund-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* HEADER ROW */
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
        color: #72817a;
        margin-bottom: 6px;
    }

    .breadcrumb-nav a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .header-text h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 800;
        color: #17221d;
    }

    .header-text p {
        margin: 0;
        font-size: 13px;
        color: #6a7972;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-create-refund {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #087f55;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(8, 127, 85, 0.25);
    }

    .btn-create-refund:hover {
        background: #066b47;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(8, 127, 85, 0.35);
    }

    /* SUMMARY METRICS */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .metric-card {
        background: #ffffff;
        border: 1px solid #e6edea;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #087f55;
    }

    .metric-card.warning::before { background: #f59e0b; }
    .metric-card.danger::before { background: #ef4444; }
    .metric-card.info::before { background: #3b82f6; }

    .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metric-title {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: #e3f5ee;
        color: #087f55;
    }

    .metric-card.warning .metric-icon-wrap { background: #fef3c7; color: #d97706; }
    .metric-card.danger .metric-icon-wrap { background: #fee2e2; color: #dc2626; }
    .metric-card.info .metric-icon-wrap { background: #e0f2fe; color: #0284c7; }

    .metric-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .metric-sub {
        font-size: 11px;
        color: #64748b;
    }

    /* FILTER SECTION */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e6edea;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .filter-form {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 180px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
    }

    .form-input, .form-select {
        height: 40px;
        padding: 0 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background-color: #ffffff;
        transition: border-color 0.2s ease;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.1);
    }

    .btn-filter-submit {
        height: 40px;
        padding: 0 18px;
        background: #087f55;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .btn-filter-submit:hover {
        background: #066b47;
    }

    .btn-filter-reset {
        height: 40px;
        padding: 0 14px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .btn-filter-reset:hover {
        background: #e2e8f0;
    }

    /* TABLE SECTION */
    .table-container {
        background: #ffffff;
        border: 1px solid #e6edea;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 14px 16px;
        font-weight: 700;
        font-size: 11px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: top;
    }

    .custom-table tbody tr:hover {
        background-color: #fbfdfc;
    }

    /* CELL FORMATTING */
    .booking-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .booking-code {
        font-weight: 700;
        color: #087f55;
        font-family: monospace;
        font-size: 13px;
    }

    .booking-date {
        font-size: 11px;
        color: #64748b;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .customer-name {
        font-weight: 600;
        color: #1e293b;
    }

    .customer-phone {
        font-size: 11px;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .route-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 150px;
    }

    .route-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        color: #1e293b;
        font-size: 12px;
    }

    .route-schedule-time {
        font-size: 11px;
        color: #64748b;
    }

    .reason-box {
        max-width: 220px;
        font-size: 12px;
        color: #475569;
        background: #f8fafc;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        line-height: 1.4;
    }

    .finance-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 140px;
    }

    .original-amount {
        font-size: 11px;
        color: #64748b;
        text-decoration: line-through;
    }

    .fee-amount {
        font-size: 11px;
        color: #dc2626;
        font-weight: 600;
    }

    .net-refund-amount {
        font-size: 14px;
        font-weight: 800;
        color: #087f55;
    }

    .bank-account-box {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 150px;
    }

    .bank-name-badge {
        display: inline-block;
        padding: 2px 7px;
        background: #e2e8f0;
        color: #1e293b;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        width: fit-content;
    }

    .account-num {
        font-family: monospace;
        font-weight: 700;
        color: #0f172a;
        font-size: 13px;
    }

    .account-holder {
        font-size: 11px;
        color: #64748b;
    }

    /* BADGES */
    .badge-refund-pending {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #fef3c7;
        color: #b45309;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-refund-approved {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #dcfce7;
        color: #15803d;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-refund-rejected {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #fee2e2;
        color: #b91c1c;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    /* ACTION BUTTONS */
    .action-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-action-approve {
        padding: 6px 12px;
        background: #087f55;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action-approve:hover {
        background: #066b47;
    }

    .btn-action-reject {
        padding: 6px 12px;
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action-reject:hover {
        background: #dc2626;
        color: white;
    }

    .btn-action-proof {
        padding: 5px 10px;
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action-proof:hover {
        background: #0284c7;
        color: white;
    }

    /* EMPTY STATE */
    .empty-state-wrap {
        padding: 48px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .empty-state-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #64748b;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .empty-state-desc {
        font-size: 13px;
        color: #64748b;
        max-width: 380px;
        margin: 0;
    }

    /* MODAL STYLES */
    .custom-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .custom-modal.show {
        display: flex;
    }

    .modal-dialog {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-dialog-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fbfdfc;
    }

    .modal-dialog-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-modal-close {
        background: none;
        border: none;
        font-size: 20px;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .btn-modal-close:hover {
        color: #0f172a;
    }

    .modal-dialog-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-dialog-footer {
        padding: 16px 22px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }

    .info-callout {
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1.5;
    }

    .info-callout.warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }

    .info-callout.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .calc-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .calc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        color: #475569;
    }

    .calc-row.total {
        border-top: 1px dashed #cbd5e1;
        padding-top: 8px;
        font-weight: 800;
        font-size: 15px;
        color: #087f55;
    }

    .preset-btn-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .btn-preset {
        padding: 5px 10px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-preset:hover, .btn-preset.active {
        background: #087f55;
        color: white;
        border-color: #087f55;
    }
</style>

<div class="refund-page">

    {{-- BREADCRUMB & HEADER --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>/</span>
                <span>Kelola Refund</span>
            </div>
            <div class="header-text">
                <h1>Kelola Refund & Pembatalan Tiket</h1>
                <p>Mengatur arus keluar uang secara transparan, menerapkan potongan biaya administrasi, dan mengosongkan kembali kursi yang dibatalkan.</p>
            </div>
        </div>

        <div class="header-actions">
            <button type="button" class="btn-create-refund" onclick="openCreateRefundModal()">
                <span></span>
                <span>Ajukan Refund Baru</span>
            </button>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div style="padding: 12px 16px; border-radius: 10px; background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 12px 16px; border-radius: 10px; background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="padding: 12px 16px; border-radius: 10px; background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; font-size: 13px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 4 METRIC CARDS --}}
    <div class="metrics-grid">
        <div class="metric-card info">
            <div class="metric-header">
                <span class="metric-title">Total Pengajuan</span>
            </div>
            <div class="metric-val">{{ number_format($totalRefundRequests ?? 0) }}</div>
            <span class="metric-sub">Seluruh permohonan pembatalan</span>
        </div>

        <div class="metric-card warning">
            <div class="metric-header">
                <span class="metric-title">Menunggu Verifikasi</span>
                <div class="metric-icon-wrap">⏳</div>
            </div>
            <div class="metric-val">{{ number_format($pendingRefundCount ?? 0) }}</div>
            <span class="metric-sub">Perlu persetujuan admin</span>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">Dana Ditransfer Balik</span>
            </div>
            <div class="metric-val">Rp {{ number_format($totalRefundedAmount ?? 0, 0, ',', '.') }}</div>
            <span class="metric-sub">Akumulasi refund yang disetujui</span>
        </div>

        <div class="metric-card danger">
            <div class="metric-header">
                <span class="metric-title">Biaya Admin Ditahan</span>
            </div>
            <div class="metric-val">Rp {{ number_format($totalFeesRetained ?? 0, 0, ',', '.') }}</div>
            <span class="metric-sub">Total cancellation fee retained</span>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.admin.refund.index') }}" class="filter-form">
            <div class="form-group" style="flex: 2;">
                <label class="form-label" for="search">Cari Permohonan</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-input" placeholder="Kode booking, nama penumpang, bank, no. rekening...">
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="all" {{ request('status') === 'all' || !request()->has('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui / Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="date_from">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="date_to">Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-filter-submit">
                    <span></span> Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('dashboard.admin.refund.index') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLE PERMOHONAN REFUND --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Pemesanan & Tanggal</th>
                        <th>Pemesan / Penumpang</th>
                        <th>Rute & Jam Jalan</th>
                        <th>Alasan Pembatalan</th>
                        <th>Rincian Finansial</th>
                        <th>Rekening Tujuan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $rf)
                        @php
                            $st = strtolower($rf->status ?? 'menunggu');
                            $isPending = in_array($st, ['menunggu', 'pending']);
                            $isApproved = in_array($st, ['disetujui', 'approved', 'success']);
                            $isRejected = in_array($st, ['ditolak', 'rejected']);
                        @endphp
                        <tr>
                            {{-- PEMESANAN & TANGGAL --}}
                            <td>
                                <div class="booking-cell">
                                    <span class="booking-code">{{ $rf->booking?->booking_code ?? '#' . $rf->booking_id }}</span>
                                    <span class="booking-date">
                                        {{ $rf->created_at ? $rf->created_at->format('d/m/Y H:i') : '-' }}
                                    </span>
                                    @if($rf->booking?->source)
                                        <span style="font-size: 10px; color: #64748b; margin-top: 2px;">
                                            via {{ $rf->booking->sourceLabel() }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- PEMESAN --}}
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name">{{ $rf->booking?->user?->name ?? $rf->user?->name ?? 'Pelanggan' }}</span>
                                    <span class="customer-phone">
                                        {{ $rf->booking?->user?->phone ?? $rf->user?->phone ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- RUTE & JADWAL --}}
                            <td>
                                <div class="route-info">
                                    <div class="route-badge">
                                        <span>{{ $rf->booking?->schedule?->route?->origin?->city ?? 'Origin' }}</span>
                                        <span></span>
                                        <span>{{ $rf->booking?->schedule?->route?->destination?->city ?? 'Destination' }}</span>
                                    </div>
                                    <div class="route-schedule-time">
                                        {{ $rf->booking?->schedule?->departure_date ? $rf->booking->schedule->departure_date->format('d/m/Y') : '-' }}
                                        &bull; {{ $rf->booking?->schedule?->departure_time ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            {{-- ALASAN --}}
                            <td>
                                <div class="reason-box">
                                    {{ $rf->reason ?? 'Tidak ada alasan dicatat' }}
                                </div>
                                @if($rf->admin_note)
                                    <div style="margin-top: 4px; font-size: 11px; color: #475569; font-style: italic;">
                                        <strong>Catatan Admin:</strong> {{ $rf->admin_note }}
                                    </div>
                                @endif
                            </td>

                            {{-- RINCIAN FINANSIAL --}}
                            <td>
                                <div class="finance-cell">
                                    <span class="original-amount" title="Nominal Asli Tiket">
                                        {{ $rf->formattedOriginalAmount() }}
                                    </span>
                                    <span class="fee-amount" title="Potongan Biaya Admin">
                                        - {{ $rf->formattedCancellationFee() }} (Admin)
                                    </span>
                                    <span class="net-refund-amount" title="Wajib Ditransfer Balik">
                                        {{ $rf->formattedNetAmount() }}
                                    </span>
                                </div>
                            </td>

                            {{-- REKENING TUJUAN --}}
                            <td>
                                <div class="bank-account-box">
                                    <span class="bank-name-badge">{{ strtoupper($rf->bank_name ?? 'BANK') }}</span>
                                    <span class="account-num">{{ $rf->account_number ?? '-' }}</span>
                                    <span class="account-holder">a.n {{ $rf->account_holder ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                @if($isPending)
                                    <span class="badge-refund-pending">
                                        <span>⏳</span> Menunggu Persetujuan
                                    </span>
                                @elseif($isApproved)
                                    <span class="badge-refund-approved">
                                        <span></span> Disetujui
                                    </span>
                                    <div style="font-size: 10px; color: #166534; margin-top: 4px;">
                                        Oleh: {{ $rf->processor?->name ?? 'Admin' }}
                                    </div>
                                @else
                                    <span class="badge-refund-rejected">
                                        <span></span> Ditolak
                                    </span>
                                    <div style="font-size: 10px; color: #991b1b; margin-top: 4px;">
                                        Oleh: {{ $rf->processor?->name ?? 'Admin' }}
                                    </div>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td style="text-align: right;">
                                <div class="action-cell" style="justify-content: flex-end;">
                                    @if($isPending)
                                        <button
                                            type="button"
                                            class="btn-action-approve"
                                            onclick="openApproveModal({{ json_encode($rf) }}, {{ json_encode($rf->booking) }})"
                                            title="Setujui permohonan refund dan kosongkan kembali kursi"
                                        >
                                            <span></span> Setujui
                                        </button>

                                        <button
                                            type="button"
                                            class="btn-action-reject"
                                            onclick="openRejectModal({{ $rf->id }}, '{{ $rf->booking?->booking_code }}')"
                                            title="Tolak permohonan refund"
                                        >
                                            <span></span> Tolak
                                        </button>
                                    @else
                                        @if($rf->transfer_proof)
                                            <button
                                                type="button"
                                                class="btn-action-proof"
                                                onclick="openProofModal('{{ $rf->transferProofUrl() }}')"
                                            >
                                                <span></span> Bukti Transfer
                                            </button>
                                        @else
                                            <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                                                Telah diproses
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrap">
                                    <h4 class="empty-state-title">Belum Ada Permohonan Refund</h4>
                                    <p class="empty-state-desc">Saat ini tidak ada permohonan pembatalan tiket yang terdaftar atau sesuai dengan filter yang Anda tentukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($refunds->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0;">
                {{ $refunds->links() }}
            </div>
        @endif
    </div>

</div>


{{-- =========================================
     MODAL 1: PERSETUJUAN REFUND (APPROVE)
========================================== --}}
<div id="approveRefundModal" class="custom-modal">
    <div class="modal-dialog">
        <form id="approveRefundForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="modal-dialog-header">
                <h3>Setujui Pengembalian Dana (Refund)</h3>
                <button type="button" class="btn-modal-close" onclick="closeApproveModal()">&times;</button>
            </div>

            <div class="modal-dialog-body">
                <div class="info-callout success">
                    <strong>Pelepasan Kursi Otomatis:</strong> Setelah refund disetujui, pesanan tiket otomatis dibatalkan dan kursi di jadwal keberangkatan akan dikosongkan kembali sehingga langsung tersedia untuk dijual ke penumpang lain.
                </div>

                <div class="calc-box">
                    <div class="calc-row">
                        <span>Kode Booking:</span>
                        <strong id="approve_booking_code" style="font-family: monospace;">-</strong>
                    </div>
                    <div class="calc-row">
                        <span>Pemesan / Penumpang:</span>
                        <span id="approve_customer_name">-</span>
                    </div>
                    <div class="calc-row">
                        <span>Rekening Tujuan:</span>
                        <span id="approve_bank_account" style="font-weight: 600;">-</span>
                    </div>
                    <div class="calc-row" style="margin-top: 4px;">
                        <span>Nominal Asli Tiket:</span>
                        <span id="approve_original_price_text" style="font-weight: 700;">Rp 0</span>
                    </div>
                </div>

                <input type="hidden" id="approve_original_price" value="0">

                <div class="form-group">
                    <label class="form-label" for="approve_cancellation_fee">
                        Potongan Biaya Administrasi (Cancellation Fee) - Rp
                    </label>
                    <input
                        type="number"
                        name="cancellation_fee"
                        id="approve_cancellation_fee"
                        class="form-input"
                        min="0"
                        step="1000"
                        oninput="recalculateApproveNet()"
                    >
                    <div class="preset-btn-group">
                        <span style="font-size: 11px; color: #64748b; margin-right: 4px; align-self: center;">Preset:</span>
                        <button type="button" class="btn-preset" onclick="setApprovePresetFee(0)">0% (Bebas Biaya)</button>
                        <button type="button" class="btn-preset" onclick="setApprovePresetFee(10)">10%</button>
                        <button type="button" class="btn-preset" onclick="setApprovePresetFee(25)">25%</button>
                        <button type="button" class="btn-preset" onclick="setApprovePresetFee(50)">50%</button>
                    </div>
                </div>

                <div class="calc-box" style="background: #e3f5ee; border-color: #bbf7d0;">
                    <div class="calc-row total" style="border: none; padding: 0;">
                        <span>Nominal Bersih yang Wajib Ditransfer:</span>
                        <span id="approve_net_amount_text">Rp 0</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="approve_transfer_proof">
                        Unggah Bukti Struk Transfer Bank (Opsional)
                    </label>
                    <input type="file" name="transfer_proof" id="approve_transfer_proof" class="form-input" accept="image/*" style="padding-top: 7px;">
                    <small style="color: #64748b; font-size: 11px;">Format JPG, PNG, WEBP (Maksimal 4MB).</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="approve_admin_note">Catatan Tambahan Verifikasi (Opsional)</label>
                    <textarea name="admin_note" id="approve_admin_note" rows="2" class="form-input" style="height: auto; padding: 8px 12px;" placeholder="Contoh: Telah ditransfer via m-Banking BCA ke rekening penumpang..."></textarea>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-filter-reset" onclick="closeApproveModal()">Batal</button>
                <button type="submit" class="btn-create-refund">
                    <span></span> Konfirmasi & Setujui Refund
                </button>
            </div>
        </form>
    </div>
</div>


{{-- =========================================
     MODAL 2: PENOLAKAN REFUND (REJECT)
========================================== --}}
<div id="rejectRefundModal" class="custom-modal">
    <div class="modal-dialog">
        <form id="rejectRefundForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div class="modal-dialog-header">
                <h3>Tolak Permohonan Refund</h3>
                <button type="button" class="btn-modal-close" onclick="closeRejectModal()">&times;</button>
            </div>

            <div class="modal-dialog-body">
                <div class="info-callout warning">
                    <strong>Pemberitahuan:</strong> Menolak permohonan ini tidak akan membatalkan tiket perjalanan. Tiket dan kursi penumpang akan tetap berstatus aktif di jadwal keberangkatan.
                </div>

                <div class="calc-box">
                    <div class="calc-row">
                        <span>Kode Booking:</span>
                        <strong id="reject_booking_code" style="font-family: monospace;">-</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reject_admin_note">
                        Alasan Penolakan <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea
                        name="admin_note"
                        id="reject_admin_note"
                        rows="3"
                        class="form-input"
                        style="height: auto; padding: 8px 12px;"
                        placeholder="Contoh: Permohonan diajukan kurang dari 2 jam sebelum keberangkatan armada, tidak sesuai kebijakan refund..."
                        required
                    ></textarea>
                    <small style="color: #64748b; font-size: 11px;">Alasan akan tercatat dalam sistem audit dan riwayat pemesanan.</small>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-filter-reset" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-action-reject" style="padding: 10px 18px; font-size: 13px;">
                    <span></span> Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- =========================================
     MODAL 3: PENGAJUAN REFUND BARU (CREATE)
========================================== --}}
<div id="createRefundModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 620px;">
        <form method="POST" action="{{ route('dashboard.admin.refund.store') }}">
            @csrf

            <div class="modal-dialog-header">
                <h3>Ajukan Permohonan Refund Baru</h3>
                <button type="button" class="btn-modal-close" onclick="closeCreateRefundModal()">&times;</button>
            </div>

            <div class="modal-dialog-body">
                <div class="form-group">
                    <label class="form-label" for="create_booking_id">Pilih Pemesanan Lunas <span style="color: #dc2626;">*</span></label>
                    <select name="booking_id" id="create_booking_id" class="form-select" onchange="onBookingSelected()" required>
                        <option value="">-- Pilih Kode Booking --</option>
                        @foreach($activeBookings as $b)
                            @php
                                $price = (float) $b->total_price;
                                $routeText = ($b->schedule?->route?->origin?->city ?? 'Origin') . ' ke ' . ($b->schedule?->route?->destination?->city ?? 'Destination');
                                $custText = $b->user?->name ?? 'Pelanggan';
                            @endphp
                            <option
                                value="{{ $b->id }}"
                                data-price="{{ $price }}"
                                data-customer="{{ $custText }}"
                                data-phone="{{ $b->user?->phone }}"
                                data-route="{{ $routeText }}"
                            >
                                {{ $b->booking_code }} - {{ $custText }} ({{ $routeText }}) - Rp {{ number_format($price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="selected_booking_preview" class="calc-box" style="display: none;">
                    <div class="calc-row">
                        <span>Rute Perjalanan:</span>
                        <strong id="preview_route">-</strong>
                    </div>
                    <div class="calc-row">
                        <span>Nama Pemesan:</span>
                        <span id="preview_customer">-</span>
                    </div>
                    <div class="calc-row">
                        <span>Nominal Asli Tiket:</span>
                        <strong id="preview_price" style="color: #0f172a;">Rp 0</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="create_reason">Alasan Pembatalan <span style="color: #dc2626;">*</span></label>
                    <textarea name="reason" id="create_reason" rows="2" class="form-input" style="height: auto; padding: 8px 12px;" placeholder="Contoh: Penumpang sakit mendadak dan mengajukan pembatalan tiket..." required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="create_fee_type">Tipe Potongan Biaya Admin</label>
                        <select name="cancellation_fee_type" id="create_fee_type" class="form-select" onchange="toggleFeeType()">
                            <option value="percentage">Persentase (%)</option>
                            <option value="nominal">Nominal Langsung (Rp)</option>
                        </select>
                    </div>

                    <div class="form-group" id="group_percentage">
                        <label class="form-label" for="create_fee_percentage">Persentase Potongan (%)</label>
                        <input type="number" name="fee_percentage" id="create_fee_percentage" class="form-input" value="25" min="0" max="100" step="1" oninput="calculateCreateRefund()">
                        <div class="preset-btn-group">
                            <button type="button" class="btn-preset" onclick="setCreatePreset(0)">0%</button>
                            <button type="button" class="btn-preset" onclick="setCreatePreset(10)">10%</button>
                            <button type="button" class="btn-preset active" onclick="setCreatePreset(25)">25%</button>
                            <button type="button" class="btn-preset" onclick="setCreatePreset(50)">50%</button>
                        </div>
                    </div>

                    <div class="form-group" id="group_nominal" style="display: none;">
                        <label class="form-label" for="create_cancellation_fee">Nominal Potongan (Rp)</label>
                        <input type="number" name="cancellation_fee" id="create_cancellation_fee" class="form-input" value="0" min="0" step="1000" oninput="calculateCreateRefund()">
                    </div>
                </div>

                <div class="calc-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="calc-row">
                        <span>Potongan Biaya Admin (Perusahaan):</span>
                        <span id="create_calc_fee" style="color: #dc2626; font-weight: 600;">Rp 0</span>
                    </div>
                    <div class="calc-row total" style="margin-top: 4px;">
                        <span>Estimasi Dana Bersih Ditransfer Balik:</span>
                        <span id="create_calc_net">Rp 0</span>
                    </div>
                </div>

                <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-top: 6px;">
                    Rekening Pengembalian Dana Penumpang:
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="create_bank_name">Nama Bank <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="bank_name" id="create_bank_name" class="form-input" placeholder="BCA / Mandiri / BRI / BNI / Seabank" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="create_account_number">Nomor Rekening <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="account_number" id="create_account_number" class="form-input" placeholder="Contoh: 1234567890" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="create_account_holder">Nama Pemilik Rekening <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="account_holder" id="create_account_holder" class="form-input" placeholder="Sesuai buku tabungan atau akun bank penumpang" required>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-filter-reset" onclick="closeCreateRefundModal()">Batal</button>
                <button type="submit" class="btn-create-refund">
                    <span></span> Buat Permohonan Refund
                </button>
            </div>
        </form>
    </div>
</div>


{{-- =========================================
     MODAL 4: PRATINJAU BUKTI TRANSFER (PROOF)
========================================== --}}
<div id="proofModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-dialog-header">
            <h3>Bukti Transfer Pengembalian Dana</h3>
            <button type="button" class="btn-modal-close" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-dialog-body" style="align-items: center; justify-content: center; padding: 20px;">
            <img id="proof_modal_img" src="" alt="Bukti Transfer" style="max-width: 100%; max-height: 400px; border-radius: 8px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>
        <div class="modal-dialog-footer">
            <a id="proof_modal_link" href="" target="_blank" class="btn-action-proof">
                <span>↗</span> Buka Gambar Penuh
            </a>
            <button type="button" class="btn-filter-reset" onclick="closeProofModal()">Tutup</button>
        </div>
    </div>
</div>


<script>
    // MODAL APPROVE
    function openApproveModal(refund, booking) {
        document.getElementById('approveRefundForm').action = `/refund/${refund.id}/approve`;
        document.getElementById('approve_booking_code').innerText = booking ? booking.booking_code : ('#' + refund.booking_id);
        document.getElementById('approve_customer_name').innerText = (booking && booking.user) ? booking.user.name : 'Pelanggan';
        document.getElementById('approve_bank_account').innerText = `${refund.bank_name || 'BANK'} - ${refund.account_number || '-'} a.n ${refund.account_holder || '-'}`;

        const original = parseFloat(refund.original_amount || (booking ? booking.total_price : refund.amount) || 0);
        const fee = parseFloat(refund.cancellation_fee || 0);

        document.getElementById('approve_original_price').value = original;
        document.getElementById('approve_original_price_text').innerText = formatRupiah(original);
        document.getElementById('approve_cancellation_fee').value = fee;
        document.getElementById('approve_admin_note').value = refund.admin_note || '';

        recalculateApproveNet();
        document.getElementById('approveRefundModal').classList.add('show');
    }

    function closeApproveModal() {
        document.getElementById('approveRefundModal').classList.remove('show');
    }

    function recalculateApproveNet() {
        const original = parseFloat(document.getElementById('approve_original_price').value) || 0;
        let fee = parseFloat(document.getElementById('approve_cancellation_fee').value) || 0;
        fee = Math.min(original, Math.max(0, fee));
        const net = Math.max(0, original - fee);

        document.getElementById('approve_net_amount_text').innerText = formatRupiah(net);
    }

    function setApprovePresetFee(pct) {
        const original = parseFloat(document.getElementById('approve_original_price').value) || 0;
        const fee = Math.round(original * (pct / 100));
        document.getElementById('approve_cancellation_fee').value = fee;
        recalculateApproveNet();
    }

    // MODAL REJECT
    function openRejectModal(refundId, bookingCode) {
        document.getElementById('rejectRefundForm').action = `/refund/${refundId}/reject`;
        document.getElementById('reject_booking_code').innerText = bookingCode || ('#' + refundId);
        document.getElementById('reject_admin_note').value = '';
        document.getElementById('rejectRefundModal').classList.add('show');
    }

    function closeRejectModal() {
        document.getElementById('rejectRefundModal').classList.remove('show');
    }

    // MODAL CREATE
    function openCreateRefundModal() {
        document.getElementById('createRefundModal').classList.add('show');
    }

    function closeCreateRefundModal() {
        document.getElementById('createRefundModal').classList.remove('show');
    }

    function onBookingSelected() {
        const sel = document.getElementById('create_booking_id');
        const opt = sel.options[sel.selectedIndex];
        const preview = document.getElementById('selected_booking_preview');

        if (!opt || !opt.value) {
            preview.style.display = 'none';
            return;
        }

        const price = parseFloat(opt.getAttribute('data-price')) || 0;
        const customer = opt.getAttribute('data-customer') || '-';
        const route = opt.getAttribute('data-route') || '-';

        document.getElementById('preview_route').innerText = route;
        document.getElementById('preview_customer').innerText = customer;
        document.getElementById('preview_price').innerText = formatRupiah(price);
        preview.style.display = 'flex';

        calculateCreateRefund();
    }

    function toggleFeeType() {
        const type = document.getElementById('create_fee_type').value;
        if (type === 'percentage') {
            document.getElementById('group_percentage').style.display = 'flex';
            document.getElementById('group_nominal').style.display = 'none';
        } else {
            document.getElementById('group_percentage').style.display = 'none';
            document.getElementById('group_nominal').style.display = 'flex';
        }
        calculateCreateRefund();
    }

    function setCreatePreset(pct) {
        document.getElementById('create_fee_percentage').value = pct;
        document.querySelectorAll('#group_percentage .btn-preset').forEach(b => b.classList.remove('active'));
        if (event && event.target) {
            event.target.classList.add('active');
        }
        calculateCreateRefund();
    }

    function calculateCreateRefund() {
        const sel = document.getElementById('create_booking_id');
        const opt = sel.options[sel.selectedIndex];
        const original = opt ? (parseFloat(opt.getAttribute('data-price')) || 0) : 0;
        const type = document.getElementById('create_fee_type').value;

        let fee = 0;
        if (type === 'percentage') {
            const pct = parseFloat(document.getElementById('create_fee_percentage').value) || 0;
            fee = Math.round(original * (pct / 100));
        } else {
            fee = parseFloat(document.getElementById('create_cancellation_fee').value) || 0;
        }

        fee = Math.min(original, Math.max(0, fee));
        const net = Math.max(0, original - fee);

        document.getElementById('create_calc_fee').innerText = '- ' + formatRupiah(fee);
        document.getElementById('create_calc_net').innerText = formatRupiah(net);
    }

    // MODAL PROOF
    function openProofModal(url) {
        if (!url) return;
        document.getElementById('proof_modal_img').src = url;
        document.getElementById('proof_modal_link').href = url;
        document.getElementById('proofModal').classList.add('show');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.remove('show');
    }

    // HELPER RUPIAH
    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // CLOSE ON OUTSIDE CLICK
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal')) {
            e.target.classList.remove('show');
        }
    });
</script>

@endsection
