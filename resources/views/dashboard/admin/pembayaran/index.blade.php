@extends('layouts.admin')

@section('title', 'Kelola Pembayaran & Transaksi - Admin Utama')

@section('page-title', 'Pembayaran & Transaksi')

@section('page-subtitle', 'Monitoring riwayat transaksi, verifikasi manual bukti transfer, dan status pembaruan payment gateway.')

@section('content')

<style>
    .payment-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* HEADER & BREADCRUMB */
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

    /* STATS SUMMARY CARDS */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .stat-box {
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .stat-box-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-box-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #72817a;
    }

    .stat-box-num {
        font-size: 22px;
        font-weight: 800;
        color: #17221d;
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

    .stat-box-icon.revenue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-box-icon.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-box-icon.failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ALERTS */
    .alert-banner {
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .alert-banner.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-banner.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* FILTER CARD */
    .filter-card {
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.2fr 1fr auto;
        gap: 12px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: #4b5852;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .filter-input,
    .filter-select {
        height: 40px;
        padding: 0 12px;
        border: 1px solid #d4dfda;
        border-radius: 8px;
        font-size: 13px;
        color: #17221d;
        background: #fbfdfc;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #087f55;
        background: white;
    }

    .filter-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-filter-submit {
        height: 40px;
        padding: 0 18px;
        border: none;
        border-radius: 8px;
        background: #087f55;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-filter-submit:hover {
        background: #066b47;
    }

    .btn-filter-reset {
        height: 40px;
        padding: 0 14px;
        border: 1px solid #d4dfda;
        border-radius: 8px;
        background: white;
        color: #61736b;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-filter-reset:hover {
        background: #f1f5f3;
        color: #17221d;
    }

    /* TABLE CARD */
    .table-card {
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .payment-table th {
        background: #f8faf9;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #61736b;
        border-bottom: 1px solid #edf1ef;
        white-space: nowrap;
    }

    .payment-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .payment-table tr:hover {
        background: #fbfdfc;
    }

    /* INVOICE CELL */
    .invoice-cell h4 {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 800;
        font-family: monospace;
        color: #0f172a;
    }

    .invoice-date {
        font-size: 11px;
        color: #64748b;
    }

    /* ROUTE CELL */
    .route-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .booking-link {
        font-family: monospace;
        font-weight: 700;
        font-size: 12px;
        color: #087f55;
        text-decoration: none;
    }

    .booking-link:hover {
        text-decoration: underline;
    }

    .route-sub {
        font-size: 12px;
        color: #334155;
        font-weight: 500;
    }

    /* CUSTOMER CELL */
    .customer-cell h4 {
        margin: 0 0 2px;
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .customer-phone {
        font-size: 11px;
        color: #64748b;
    }

    /* METHOD BADGES */
    .badge-method {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-method-transfer {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-method-qris {
        background: #faf5ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .badge-method-cash {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-method-gateway {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    /* STATUS BADGES */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
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

    .badge-status-expired {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .badge-status-failed {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* PROOF THUMBNAIL */
    .proof-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: white;
        font-size: 11px;
        font-weight: 600;
        color: #0369a1;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .proof-btn:hover {
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    /* ACTION BUTTONS */
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .btn-action-view {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
    }

    .btn-action-view:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-action-verify {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        background: #087f55;
        color: white;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 6px rgba(8, 127, 85, 0.25);
        transition: background 0.2s ease;
    }

    .btn-action-verify:hover {
        background: #066b47;
    }

    .btn-action-log {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-action-log:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    /* EMPTY STATE */
    .empty-state-box {
        padding: 60px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .empty-state-icon {
        font-size: 44px;
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
        margin: 0;
        max-width: 400px;
    }

    /* PAGINATION */
    .pagination-bar {
        padding: 16px 20px;
        border-top: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* MODAL STYLES */
    .custom-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(3px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .custom-modal.show {
        display: flex;
    }

    .modal-dialog {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
    }

    .modal-dialog-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-dialog-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 20px;
        color: #94a3b8;
        cursor: pointer;
    }

    .modal-dialog-body {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .modal-dialog-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    /* RESPONSIVE */
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

<div class="payment-page">

    {{-- HEADER ROW --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>&rsaquo;</span>
                <span>Pembayaran</span>
            </div>
            <div class="header-text">
                <h1>Daftar Transaksi Pembayaran</h1>
                <p>Kelola verifikasi transfer manual, pelacakan nomor tagihan (invoice), dan status payment gateway.</p>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert-banner success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-banner error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- SUMMARY METRICS ROW --}}
    <div class="stats-row">
        {{-- Total Transaksi --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Transaksi</div>
                <div class="stat-box-num">{{ number_format($totalTransactions) }}</div>
            </div>
        </div>

        {{-- Total Omzet Lunas --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Omzet Lunas</div>
                <div class="stat-box-num" style="color: #0284c7; font-size: 19px;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Menunggu Verifikasi Manual --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Verifikasi Manual</div>
                <div class="stat-box-num" style="color: #d97706;">{{ number_format($pendingVerificationCount) }}</div>
            </div>
            <div class="stat-box-icon pending">
                ⏳
            </div>
        </div>

        {{-- Gagal / Expired --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Gagal & Kedaluwarsa</div>
                <div class="stat-box-num" style="color: #b91c1c;">{{ number_format($failedOrExpiredCount) }}</div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.pembayaran.index') }}" method="GET" class="filter-form">
            {{-- Search input --}}
            <div class="filter-group">
                <label class="filter-label" for="search">Cari Invoice / Booking / Nama / HP</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="filter-input"
                    placeholder="Contoh: ORD-TRV, TRV-2026, Ahmad, BCA.."
                    value="{{ request('search') }}"
                >
            </div>

            {{-- Status Filter --}}
            <div class="filter-group">
                <label class="filter-label" for="status">Status Pembayaran</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Lunas</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal / Ditolak</option>
                </select>
            </div>

            {{-- Payment Method Filter --}}
            <div class="filter-group">
                <label class="filter-label" for="method">Metode Pembayaran</label>
                <select name="method" id="method" class="filter-select">
                    <option value="all">Semua Metode</option>
                    <option value="transfer" {{ request('method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ request('method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Tunai Loket</option>
                    <option value="gateway" {{ request('method') === 'gateway' ? 'selected' : '' }}>Payment Gateway</option>
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="filter-group">
                <label class="filter-label" for="date_from">Dari Tanggal</label>
                <input
                    type="date"
                    name="date_from"
                    id="date_from"
                    class="filter-input"
                    value="{{ request('date_from') }}"
                >
            </div>

            <div class="filter-buttons">
                <button type="submit" class="btn-filter-submit">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'status', 'method', 'date_from', 'date_to']))
                    <a href="{{ route('dashboard.admin.pembayaran.index') }}" class="btn-filter-reset" title="Reset Filter">Reset
                                            </a>
                @endif
            </div>
        </form>
    </div>

    {{-- DATA TABLE CARD --}}
    <div class="table-card">
        <div class="table-container">
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Nomor Invoice & Waktu</th>
                        <th>Kode Booking & Rute</th>
                        <th>Nama Pemesan / Kontak</th>
                        <th>Metode Pembayaran</th>
                        <th>Nominal Tagihan</th>
                        <th>Status</th>
                        <th>Bukti Transfer</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            {{-- INVOICE CELL --}}
                            <td>
                                <div class="invoice-cell">
                                    <h4>{{ $payment->order_id }}</h4>
                                    <span class="invoice-date">
                                        {{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- BOOKING & ROUTE --}}
                            <td>
                                <div class="route-cell">
                                    @if($payment->booking)
                                        <a href="{{ route('dashboard.admin.pemesanan.show', $payment->booking->id) }}" class="booking-link">
                                            {{ $payment->booking->booking_code }}
                                        </a>
                                        @php
                                            $sch = $payment->booking->schedule;
                                            $rText = $sch && $sch->route 
                                                ? ($sch->route->origin?->city . ' ke ' . $sch->route->destination?->city)
                                                : 'Rute Travel';
                                        @endphp
                                        <span class="route-sub">
                                            {{ $rText }}
                                        </span>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </div>
                            </td>

                            {{-- CUSTOMER CELL --}}
                            <td>
                                <div class="customer-cell">
                                    <h4>{{ $payment->booking?->customerName() ?? 'Pelanggan' }}</h4>
                                    <span class="customer-phone">
                                        {{ $payment->booking?->customerPhone() ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- PAYMENT METHOD --}}
                            <td>
                                <span class="badge-method {{ $payment->methodBadgeClass() }}">
                                    @if(stripos($payment->payment_method, 'qris') !== false)
                                                                            @elseif(stripos($payment->payment_method, 'cash') !== false || stripos($payment->payment_method, 'tunai') !== false)
                                                                            @elseif(stripos($payment->payment_method, 'transfer') !== false || stripos($payment->payment_method, 'bank') !== false)
                                                                            @else
                                                                            @endif
                                    {{ $payment->methodLabel() }}
                                </span>
                                @if($payment->gateway_provider && $payment->gateway_provider !== 'manual')
                                    <div style="font-size: 10px; color: #64748b; font-weight: 600; margin-top: 2px;">
                                        via {{ strtoupper($payment->gateway_provider) }}
                                    </div>
                                @endif
                            </td>

                            {{-- NOMINAL --}}
                            <td>
                                <strong style="font-size: 14px; color: #087f55;">
                                    {{ $payment->formattedAmount() }}
                                </strong>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="badge-status {{ $payment->statusBadgeClass() }}">
                                    @if($payment->statusBadgeClass() === 'badge-status-paid')
                                        {{ $payment->statusLabel() }}
                                    @elseif($payment->statusBadgeClass() === 'badge-status-pending')
                                        ⏳ {{ $payment->statusLabel() }}
                                    @else
                                        {{ $payment->statusLabel() }}
                                    @endif
                                </span>
                            </td>

                            {{-- BUKTI TRANSFER --}}
                            <td>
                                @if($payment->hasProof())
                                    <button
                                        type="button"
                                        class="proof-btn"
                                        onclick="openProofModal('{{ asset('storage/' . $payment->proof_image) }}', '{{ $payment->order_id }}', '{{ $payment->bank_name }}', '{{ $payment->account_name }}')"
                                    >
                                        Ada Bukti
                                    </button>
                                @elseif($payment->isManualTransfer())
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                                        Belum diunggah
                                    </span>
                                @else
                                    <span style="font-size: 11px; color: #94a3b8;">
                                        Otomatis
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI CELL --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Quick Verify Button for pending manual payments --}}
                                    @if(in_array(strtolower($payment->status), ['pending', 'menunggu']))
                                        <button
                                            type="button"
                                            class="btn-action-verify"
                                            onclick="openVerifyModal({{ $payment->id }}, '{{ $payment->order_id }}', '{{ $payment->formattedAmount() }}', '{{ $payment->hasProof() ? asset('storage/' . $payment->proof_image) : '' }}', '{{ $payment->bank_name ?? 'Transfer Bank' }}', '{{ $payment->account_name ?? '-' }}')"
                                            title="Verifikasi Manual Bukti Transfer"
                                        >
                                            Verifikasi
                                        </button>
                                    @endif

                                    {{-- Detail Link --}}
                                    <a
                                        href="{{ route('dashboard.admin.pembayaran.show', $payment->id) }}"
                                        class="btn-action-view"
                                        title="Buka Faktur & Log Gateway"
                                    >
                                        Faktur</a>

                                    {{-- Gateway Log Quick Icon --}}
                                    @if($payment->hasGatewayLog())
                                        <button
                                            type="button"
                                            class="btn-action-log"
                                            onclick='openGatewayLogModal({{ json_encode($payment->raw_response) }}, "{{ $payment->gateway_provider ?? "gateway" }}", "{{ $payment->transaction_id ?? "-" }}")'
                                            title="Lihat Raw JSON Log Gateway"
                                        >Log
                                                                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-box">
                                    <h4 class="empty-state-title">Tidak Ada Data Transaksi</h4>
                                    <p class="empty-state-desc">
                                        Tidak ditemukan transaksi pembayaran yang sesuai dengan kriteria filter Anda.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($payments->hasPages())
            <div class="pagination-bar">
                <span style="font-size: 12px; color: #64748b;">
                    Menampilkan {{ $payments->firstItem() }} - {{ $payments->lastItem() }} dari total {{ $payments->total() }} transaksi
                </span>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- =========================================
     MODAL 1: PRATINJAU BUKTI TRANSFER
========================================== --}}
<div id="proofModal" class="custom-modal">
    <div class="modal-dialog">
        <div class="modal-dialog-header">
            <h3 id="proofModalTitle">Bukti Transfer Pembayaran</h3>
            <button type="button" class="btn-close-modal" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-dialog-body" style="text-align: center;">
            <div id="proofBankInfo" style="font-size: 13px; color: #334155; margin-bottom: 8px; font-weight: 600;"></div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; max-height: 420px; overflow: auto;">
                <img id="proofImageElement" src="" alt="Bukti Transfer" style="max-width: 100%; border-radius: 8px; object-fit: contain;">
            </div>
        </div>
        <div class="modal-dialog-footer">
            <button type="button" class="btn-action-view" onclick="closeProofModal()">Tutup</button>
        </div>
    </div>
</div>

{{-- =========================================
     MODAL 2: VERIFIKASI MANUAL PEMBAYARAN
========================================== --}}
<div id="verifyModal" class="custom-modal">
    <div class="modal-dialog">
        <div class="modal-dialog-header">
            <h3 id="verifyModalTitle">Verifikasi Manual Bukti Transfer</h3>
            <button type="button" class="btn-close-modal" onclick="closeVerifyModal()">&times;</button>
        </div>
        <form id="verifyForm" method="POST">
            @csrf
            @method('PATCH')

            <div class="modal-dialog-body">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                    <div>Nomor Tagihan: <strong id="verifyOrderIdText" style="font-family: monospace;"></strong></div>
                    <div>Nominal Tagihan: <strong id="verifyAmountText" style="color: #087f55;"></strong></div>
                    <div>Bank Pengirim: <strong id="verifyBankText"></strong></div>
                    <div>Atas Nama: <strong id="verifyAccountText"></strong></div>
                </div>

                <div id="verifyProofPreviewBox" style="display: none; text-align: center;">
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 6px;">
                        Foto Bukti Transfer
                    </span>
                    <img id="verifyProofImg" src="" alt="Bukti Transfer" style="max-height: 200px; max-width: 100%; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>

                {{-- Action Selector --}}
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Pilih Tindakan Verifikasi <span style="color: #dc2626;">*</span></label>
                    <div style="display: flex; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600; color: #15803d; background: #f0fdf4; padding: 10px 14px; border-radius: 8px; border: 1.5px solid #86efac; flex: 1;">
                            <input type="radio" name="action" value="approve" checked onchange="toggleRejectionInput(false)">
                            <span>Setujui (Lunas & Terbitkan Tiket)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600; color: #b91c1c; background: #fef2f2; padding: 10px 14px; border-radius: 8px; border: 1.5px solid #fca5a5; flex: 1;">
                            <input type="radio" name="action" value="reject" onchange="toggleRejectionInput(true)">
                            <span>Tolak Pembayaran</span>
                        </label>
                    </div>
                </div>

                <div id="rejectionReasonBox" style="display: none; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #b91c1c;">Alasan Penolakan <span style="color: #dc2626;">*</span></label>
                    <input
                        type="text"
                        name="rejection_reason"
                        id="rejection_reason"
                        class="filter-input"
                        placeholder="Contoh: Bukti transfer tidak valid, mutasi rekening tidak masuk, nominal kurang.."
                    >
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; color: #64748b;">Catatan Verifikasi Tambahan (Opsional)</label>
                    <input
                        type="text"
                        name="notes"
                        class="filter-input"
                        placeholder="Catatan internal verifikasi admin loket.."
                    >
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-action-view" onclick="closeVerifyModal()">Batal</button>
                <button type="submit" class="btn-action-verify" id="submitVerifyBtn">
                    Simpan Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================
     MODAL 3: GATEWAY RAW LOG VIEWER
========================================== --}}
<div id="gatewayLogModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-dialog-header">
            <div>
                <h3 style="margin: 0;">Log Respons Payment Gateway</h3>
                <span id="gatewayProviderBadge" style="font-size: 11px; font-weight: 700; color: #0284c7;"></span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeGatewayLogModal()">&times;</button>
        </div>
        <div class="modal-dialog-body">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 12px; color: #64748b;">Raw Webhook Payload (JSON format)</span>
                <button type="button" class="proof-btn" onclick="copyGatewayJson()">Salin JSON</button>
            </div>
            <pre id="gatewayJsonViewer" style="background: #0f172a; color: #38bdf8; padding: 16px; border-radius: 10px; font-family: monospace; font-size: 12px; max-height: 380px; overflow: auto; white-space: pre-wrap; margin: 0;"></pre>
        </div>
        <div class="modal-dialog-footer">
            <button type="button" class="btn-action-view" onclick="closeGatewayLogModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    // 1. Proof Modal
    function openProofModal(imgUrl, orderId, bankName, accName) {
        document.getElementById('proofImageElement').src = imgUrl;
        document.getElementById('proofModalTitle').innerText = `Bukti Transfer - ${orderId}`;
        document.getElementById('proofBankInfo').innerText = (bankName ? `${bankName} ` : '') + (accName ? `a.n ${accName}` : '');
        document.getElementById('proofModal').classList.add('show');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.remove('show');
    }

    // 2. Verify Modal
    function openVerifyModal(paymentId, orderId, amount, proofUrl, bankName, accName) {
        const form = document.getElementById('verifyForm');
        form.action = `/pembayaran/${paymentId}/verify`;

        document.getElementById('verifyOrderIdText').innerText = orderId;
        document.getElementById('verifyAmountText').innerText = amount;
        document.getElementById('verifyBankText').innerText = bankName;
        document.getElementById('verifyAccountText').innerText = accName;

        const previewBox = document.getElementById('verifyProofPreviewBox');
        const img = document.getElementById('verifyProofImg');
        if (proofUrl) {
            img.src = proofUrl;
            previewBox.style.display = 'block';
        } else {
            previewBox.style.display = 'none';
        }

        toggleRejectionInput(false);
        document.getElementById('verifyModal').classList.add('show');
    }

    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.remove('show');
    }

    function toggleRejectionInput(isReject) {
        const box = document.getElementById('rejectionReasonBox');
        const input = document.getElementById('rejection_reason');
        const submitBtn = document.getElementById('submitVerifyBtn');

        if (isReject) {
            box.style.display = 'flex';
            input.required = true;
            submitBtn.style.background = '#dc2626';
            submitBtn.innerText = 'Tolak Pembayaran Ini';
        } else {
            box.style.display = 'none';
            input.required = false;
            submitBtn.style.background = '#087f55';
            submitBtn.innerText = 'Setujui & Terbitkan Tiket';
        }
    }

    // 3. Gateway Log Modal
    let activeJsonText = '';
    function openGatewayLogModal(rawResponse, provider, txId) {
        const viewer = document.getElementById('gatewayJsonViewer');
        document.getElementById('gatewayProviderBadge').innerText = `Provider: ${provider.toUpperCase()} | Ref: ${txId}`;
        
        activeJsonText = JSON.stringify(rawResponse, null, 2);
        viewer.innerText = activeJsonText || '// Tidak ada payload webhook tercatat';

        document.getElementById('gatewayLogModal').classList.add('show');
    }

    function closeGatewayLogModal() {
        document.getElementById('gatewayLogModal').classList.remove('show');
    }

    function copyGatewayJson() {
        if (!activeJsonText) return;
        navigator.clipboard.writeText(activeJsonText).then(() => {
            alert('JSON respons gateway berhasil disalin ke clipboard!');
        });
    }

    // Close modal on background click
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal')) {
            e.target.classList.remove('show');
        }
    });
</script>

@endsection
