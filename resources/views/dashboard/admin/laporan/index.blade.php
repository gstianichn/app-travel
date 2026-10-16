@extends('layouts.admin')

@section('title', 'Laporan & Rekonsiliasi Pembukuan - Admin Utama')

@section('page-title', 'Laporan & Rekonsiliasi Pembukuan')

@section('page-subtitle', 'Mencocokkan penerimaan di rekening dengan tiket fisik/digital terjual, keterisian kursi, manifes penumpang, dan rekap pembatalan.')

@section('content')

<style>
    .report-page {
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

    .btn-export-excel {
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

    .btn-export-excel:hover {
        background: #066b47;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(8, 127, 85, 0.35);
    }

    .btn-export-pdf {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        color: #1e293b;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-export-pdf:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    /* SUMMARY CARDS */
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
        gap: 10px;
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

    .metric-card.outflow::before { background: #ef4444; }
    .metric-card.net::before { background: #059669; }
    .metric-card.occupancy::before { background: #3b82f6; }

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

    .metric-card.outflow .metric-icon-wrap { background: #fee2e2; color: #dc2626; }
    .metric-card.net .metric-icon-wrap { background: #d1fae5; color: #059669; }
    .metric-card.occupancy .metric-icon-wrap { background: #e0f2fe; color: #0284c7; }

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
        min-width: 170px;
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

    .preset-btn-row {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .btn-preset-filter {
        padding: 4px 9px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-preset-filter:hover, .btn-preset-filter.active {
        background: #087f55;
        color: white;
        border-color: #087f55;
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

    /* TABS BAR */
    .tabs-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0;
        overflow-x: auto;
    }

    .tab-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .tab-link:hover {
        color: #087f55;
    }

    .tab-link.active {
        color: #087f55;
        border-bottom-color: #087f55;
        background: #f8fafc;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    /* PAYMENT METHOD PILLS */
    .breakdown-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .breakdown-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .breakdown-pill-title {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }

    .breakdown-pill-val {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
    }

    /* TABLE SECTION */
    .table-container {
        background: #ffffff;
        border: 1px solid #e6edea;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .table-header-box {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fbfdfc;
    }

    .table-header-box h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
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
        padding: 12px 16px;
        font-weight: 700;
        font-size: 11px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background-color: #fbfdfc;
    }

    .custom-table tfoot td {
        background: #f8fafc;
        font-weight: 800;
        color: #0f172a;
        border-top: 2px solid #cbd5e1;
        padding: 14px 16px;
    }

    /* PROGRESS BAR */
    .progress-bar-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 120px;
    }

    .progress-bar-bg {
        width: 100%;
        height: 7px;
        border-radius: 4px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
    }

    .progress-bar-fill.green { background: #10b981; }
    .progress-bar-fill.orange { background: #f59e0b; }
    .progress-bar-fill.red { background: #ef4444; }

    /* BADGES */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-status.paid { background: #dcfce7; color: #15803d; }
    .badge-status.scheduled { background: #e0f2fe; color: #0369a1; }
    .badge-status.active { background: #fef3c7; color: #b45309; }
    .badge-status.completed { background: #f1f5f9; color: #475569; }

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
</style>

<div class="report-page">

    {{-- BREADCRUMB & HEADER --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>/</span>
                <span>Laporan & Rekonsiliasi</span>
            </div>
            <div class="header-text">
                <h1>Laporan & Rekonsiliasi Pembukuan</h1>
                <p>Mencocokkan penerimaan di rekening dengan tiket fisik/digital terjual, keterisian kursi, manifes penumpang, dan rekap pembatalan.</p>
            </div>
        </div>

        <div class="header-actions">
            <a
                href="{{ route('dashboard.admin.laporan.export.excel', request()->query()) }}"
                class="btn-export-excel"
                title="Unduh data tabel ke file Excel / CSV"
            >
                <span></span>
                <span>Unduh Excel</span>
            </a>

            <a
                href="{{ route('dashboard.admin.laporan.export.pdf', request()->query()) }}"
                target="_blank"
                class="btn-export-pdf"
                title="Buka format dokumen siap cetak atau simpan ke PDF"
            >
                <span></span>
                <span>Cetak / Simpan PDF</span>
            </a>
        </div>
    </div>

    {{-- 4 KARTU METRIK EKSEKUTIF --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">Pendapatan Kotor</span>
            </div>
            <div class="metric-val">Rp {{ number_format($grossRevenue ?? 0, 0, ',', '.') }}</div>
            <span class="metric-sub">{{ number_format($totalTicketsSold ?? 0) }} tiket terjual terbayar</span>
        </div>

        <div class="metric-card outflow">
            <div class="metric-header">
                <span class="metric-title">Pengembalian Refund</span>
            </div>
            <div class="metric-val">- Rp {{ number_format($totalRefundOutflow ?? 0, 0, ',', '.') }}</div>
            <span class="metric-sub">Fee admin ditahan: Rp {{ number_format($totalFeesRetained ?? 0, 0, ',', '.') }}</span>
        </div>

        <div class="metric-card net">
            <div class="metric-header">
                <span class="metric-title">Pendapatan Bersih</span>
            </div>
            <div class="metric-val">Rp {{ number_format($netRevenue ?? 0, 0, ',', '.') }}</div>
            <span class="metric-sub">Dana riil masuk ke kas perusahaan</span>
        </div>

        <div class="metric-card occupancy">
            <div class="metric-header">
                <span class="metric-title">Keterisian Kursi</span>
            </div>
            <div class="metric-val">{{ $overallLoadFactor ?? 0 }}%</div>
            <span class="metric-sub">{{ number_format($totalOccupiedAll ?? 0) }} / {{ number_format($totalCapacityAll ?? 0) }} kursi terisi</span>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.admin.laporan.index') }}" class="filter-form admin-filter-form">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="form-group" style="flex: 2;">
                <label class="form-label" for="route_id">Filter Rute Perjalanan</label>
                <select name="route_id" id="route_id" class="form-select">
                    <option value="all" {{ empty($routeId) ? 'selected' : '' }}>Semua Rute Travel</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" {{ (string)$routeId === (string)$r->id ? 'selected' : '' }}>
                            {{ $r->routeTitle() }} ({{ $r->origin?->city }} - {{ $r->destination?->city }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="date_from">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ $startDate }}" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="date_to">Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" value="{{ $endDate }}" class="form-input">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-filter-submit">
                    <span></span> Terapkan Filter
                </button>
                <a href="{{ route('dashboard.admin.laporan.index', ['tab' => $tab]) }}" class="btn-filter-reset">
                    Reset
                </a>
            </div>
        </form>

        {{-- PRESET FILTER ROW --}}
        <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 11px; font-weight: 600; color: #64748b;">Preset Periode:</span>
            <a href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['preset' => 'today', 'tab' => $tab])) }}" class="btn-preset-filter {{ $preset === 'today' ? 'active' : '' }}">Hari Ini</a>
            <a href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['preset' => '7days', 'tab' => $tab])) }}" class="btn-preset-filter {{ $preset === '7days' ? 'active' : '' }}">7 Hari Terakhir</a>
            <a href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['preset' => 'this_month', 'tab' => $tab])) }}" class="btn-preset-filter {{ $preset === 'this_month' || empty($preset) ? 'active' : '' }}">Bulan Ini</a>
            <a href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['preset' => 'this_year', 'tab' => $tab])) }}" class="btn-preset-filter {{ $preset === 'this_year' ? 'active' : '' }}">Tahun Ini</a>
        </div>
    </div>

    {{-- TABS BAR NAVIGATION --}}
    <div class="tabs-bar">
        <a
            href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['tab' => 'financial'])) }}"
            class="tab-link {{ $tab === 'financial' ? 'active' : '' }}"
        >
            <span></span>
            <span>Rekonsiliasi Keuangan</span>
        </a>

        <a
            href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['tab' => 'occupancy'])) }}"
            class="tab-link {{ $tab === 'occupancy' ? 'active' : '' }}"
        >
            <span></span>
            <span>Keterisian Kursi (Load Factor)</span>
        </a>

        <a
            href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['tab' => 'manifest'])) }}"
            class="tab-link {{ $tab === 'manifest' ? 'active' : '' }}"
        >
            <span></span>
            <span>Manifes Penumpang</span>
        </a>

        <a
            href="{{ route('dashboard.admin.laporan.index', array_merge(request()->query(), ['tab' => 'cancellation'])) }}"
            class="tab-link {{ $tab === 'cancellation' ? 'active' : '' }}"
        >
            <span></span>
            <span>Rekap Pembatalan & Refund</span>
        </a>
    </div>

    {{-- TAB CONTENT 1: REKONSILIASI KEUANGAN --}}
    @if($tab === 'financial')
        {{-- BREAKDOWN PAYMENT METHOD --}}
        <div class="breakdown-row">
            <div class="breakdown-pill">
                <span class="breakdown-pill-title">Transfer Bank Langsung</span>
                <span class="breakdown-pill-val">Rp {{ number_format($breakdownBank, 0, ',', '.') }}</span>
            </div>
            <div class="breakdown-pill">
                <span class="breakdown-pill-title">QRIS Instan</span>
                <span class="breakdown-pill-val">Rp {{ number_format($breakdownQris, 0, ',', '.') }}</span>
            </div>
            <div class="breakdown-pill">
                <span class="breakdown-pill-title">Tunai di Loket / Agen</span>
                <span class="breakdown-pill-val">Rp {{ number_format($breakdownCash, 0, ',', '.') }}</span>
            </div>
            <div class="breakdown-pill">
                <span class="breakdown-pill-title">Payment Gateway</span>
                <span class="breakdown-pill-val">Rp {{ number_format($breakdownGateway, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header-box">
                <div>
                    <h3>Rincian Transaksi Tiket Terjual (Lunas)</h3>
                    <small style="color: #64748b;">Mencocokkan nomor tagihan dan mutasi rekening kantor</small>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #087f55;">
                    {{ $paidBookings->count() }} Transaksi
                </span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>No. Invoice</th>
                            <th>Tanggal & Jam</th>
                            <th>Nama Pemesan</th>
                            <th>Rute Perjalanan</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                            <th style="text-align: right;">Nominal Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paidBookings as $idx => $b)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-family: monospace; font-weight: 700; color: #087f55;">
                                    {{ $b->booking_code }}
                                </td>
                                <td style="font-family: monospace;">
                                    {{ $b->payment?->order_id ?? '-' }}
                                </td>
                                <td>
                                    {{ $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">{{ $b->user?->name ?? 'Pelanggan' }}</div>
                                    <small style="color: #64748b;">{{ $b->user?->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $b->schedule?->route?->origin?->city ?? 'Origin' }} ke {{ $b->schedule?->route?->destination?->city ?? 'Destination' }}
                                </td>
                                <td>
                                    <span style="display: inline-block; padding: 2px 7px; background: #f1f5f9; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        {{ $b->payment?->payment_method ?? ($b->source === 'walk_in' ? 'Tunai Loket' : 'Online') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status paid">
                                        Lunas
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                    Rp {{ number_format($b->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state-wrap">
                                        <h4 class="empty-state-title">Tidak Ada Transaksi Terbayar</h4>
                                        <p class="empty-state-desc">Tidak ditemukan transaksi tiket lunas pada rentang periode dan filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" style="text-align: right;">TOTAL PENDAPATAN KOTOR:</td>
                            <td style="text-align: right; color: #0f172a;">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align: right; color: #dc2626;">TOTAL PENGELUARAN REFUND KELUAR:</td>
                            <td style="text-align: right; color: #dc2626;">- Rp {{ number_format($totalRefundOutflow, 0, ',', '.') }}</td>
                        </tr>
                        <tr style="background: #e3f5ee;">
                            <td colspan="8" style="text-align: right; color: #087f55; font-size: 14px;">PENDAPATAN BERSIH REALISTIS (NET):</td>
                            <td style="text-align: right; color: #087f55; font-size: 15px;">Rp {{ number_format($netRevenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    {{-- TAB CONTENT 2: KETERISIAN KURSI (OCCUPANCY) --}}
    @elseif($tab === 'occupancy')
        <div class="table-container">
            <div class="table-header-box">
                <div>
                    <h3>Analisis Tingkat Keterisian Kursi (Load Factor)</h3>
                    <small style="color: #64748b;">Memantau utilisasi armada dan performa keterisian kursi per jadwal keberangkatan</small>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #087f55;">
                    {{ $schedules->count() }} Jadwal Terjadwal
                </span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal & Jam</th>
                            <th>Rute Perjalanan</th>
                            <th>Armada & Nopol</th>
                            <th>Sopir Bertugas</th>
                            <th style="text-align: center;">Kapasitas</th>
                            <th style="text-align: center;">Terisi</th>
                            <th style="text-align: center;">Kosong</th>
                            <th>Load Factor (%)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $idx => $s)
                            @php
                                $cap = $s->vehicle?->capacity ?? 10;
                                $occ = 0;
                                foreach ($s->bookings as $sb) {
                                    $occ += $sb->passengers->count() > 0 ? $sb->passengers->count() : ($sb->total_passenger ?? 1);
                                }
                                $empty = max(0, $cap - $occ);
                                $lf = $cap > 0 ? round(($occ / $cap) * 100, 1) : 0;
                                $colorClass = $lf >= 80 ? 'green' : ($lf >= 40 ? 'orange' : 'red');
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div style="font-weight: 700; color: #1e293b;">
                                        {{ $s->departure_date ? $s->departure_date->format('d/m/Y') : '-' }}
                                    </div>
                                    <small style="color: #64748b;">Jam {{ $s->departure_time }}</small>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $s->route?->origin?->city ?? 'Origin' }} ke {{ $s->route?->destination?->city ?? 'Destination' }}
                                    </div>
                                    <small style="color: #64748b;">{{ $s->route?->origin?->name }}</small>
                                </td>
                                <td>
                                    <div>{{ $s->vehicle?->name ?? 'Minibus' }}</div>
                                    <small style="font-family: monospace; color: #64748b;">{{ $s->vehicle?->plate_number ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $s->driver?->user?->name ?? 'Belum Ditugaskan' }}
                                </td>
                                <td style="text-align: center; font-weight: 600;">{{ $cap }}</td>
                                <td style="text-align: center; font-weight: 700; color: #087f55;">{{ $occ }}</td>
                                <td style="text-align: center; font-weight: 600; color: #64748b;">{{ $empty }}</td>
                                <td>
                                    <div class="progress-bar-wrap">
                                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700;">
                                            <span>{{ $lf }}%</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill {{ $colorClass }}" style="width: {{ min(100, $lf) }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status {{ strtolower($s->status) }}">
                                        {{ $s->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state-wrap">
                                        <h4 class="empty-state-title">Tidak Ada Jadwal Perjalanan</h4>
                                        <p class="empty-state-desc">Tidak ada jadwal perjalanan pada periode dan filter yang Anda pilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right;">TOTAL OPERASIONAL:</td>
                            <td style="text-align: center;">{{ $totalCapacityAll }}</td>
                            <td style="text-align: center; color: #087f55;">{{ $totalOccupiedAll }}</td>
                            <td style="text-align: center; color: #64748b;">{{ $totalEmptySeatsAll }}</td>
                            <td colspan="2" style="font-weight: 800; color: #087f55;">
                                Rata-rata: {{ $overallLoadFactor }}%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    {{-- TAB CONTENT 3: MANIFES PENUMPANG --}}
    @elseif($tab === 'manifest')
        <div class="table-container">
            <div class="table-header-box">
                <div>
                    <h3>Manifes Lengkap Penumpang Terverifikasi</h3>
                    <small style="color: #64748b;">Daftar identitas penumpang aktif yang berangkat pada periode yang dipilih</small>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #087f55;">
                    {{ $manifestPassengers->count() }} Penumpang
                </span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Jalan</th>
                            <th>No. Tiket</th>
                            <th>Kode Booking</th>
                            <th>Nama Penumpang</th>
                            <th style="text-align: center;">Kursi</th>
                            <th>Kontak WhatsApp</th>
                            <th>Rute Perjalanan</th>
                            <th>Titik Naik (Pool)</th>
                            <th>Titik Turun</th>
                            <th>Armada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manifestPassengers as $idx => $p)
                            @php
                                $sch = $p->booking?->schedule;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $sch?->departure_date ? $sch->departure_date->format('d/m/Y') : '-' }}
                                    </div>
                                    <small style="color: #64748b;">Jam {{ $sch?->departure_time ?? '-' }}</small>
                                </td>
                                <td style="font-family: monospace; font-weight: 700; color: #0f172a;">
                                    {{ $p->booking?->ticket?->ticket_number ?? '-' }}
                                </td>
                                <td style="font-family: monospace; color: #087f55;">
                                    {{ $p->booking?->booking_code ?? '-' }}
                                </td>
                                <td style="font-weight: 700; color: #1e293b;">
                                    {{ $p->name }}
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; padding: 3px 8px; background: #e3f5ee; color: #087f55; border-radius: 6px; font-weight: 800; font-size: 12px;">
                                        {{ $p->seat?->seat_number ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $p->phone ?? $p->booking?->user?->phone ?? '-' }}
                                </td>
                                <td>
                                    {{ $sch?->route?->origin?->city ?? 'Origin' }} ke {{ $sch?->route?->destination?->city ?? 'Destination' }}
                                </td>
                                <td>{{ $sch?->route?->origin?->name ?? '-' }}</td>
                                <td>{{ $sch?->route?->destination?->name ?? '-' }}</td>
                                <td>
                                    <small style="font-family: monospace;">{{ $sch?->vehicle?->plate_number ?? '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state-wrap">
                                        <h4 class="empty-state-title">Tidak Ada Data Penumpang</h4>
                                        <p class="empty-state-desc">Tidak ditemukan penumpang yang berangkat pada rentang tanggal dan rute yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- TAB CONTENT 4: REKAP PEMBATALAN & REFUND --}}
    @else
        <div class="table-container">
            <div class="table-header-box">
                <div>
                    <h3>Rekapitulasi Pembatalan Tiket & Pengembalian Dana</h3>
                    <small style="color: #64748b;">Mencatat arus keluar pengembalian dana, potongan biaya admin, dan alasan pembatalan</small>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #dc2626;">
                    {{ $cancellations->count() }} Pengajuan
                </span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Nama Pemesan</th>
                            <th>Alasan Pembatalan</th>
                            <th style="text-align: right;">Nominal Asli</th>
                            <th style="text-align: right;">Potongan Admin</th>
                            <th style="text-align: right;">Dana Ditransfer</th>
                            <th>Rekening Tujuan</th>
                            <th>Status</th>
                            <th>Diverifikasi Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cancellations as $idx => $rf)
                            @php
                                $orig = (float) ($rf->original_amount ?? ($rf->booking ? $rf->booking->total_price : $rf->amount));
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-family: monospace; font-weight: 700; color: #087f55;">
                                    {{ $rf->booking?->booking_code ?? '#'.$rf->booking_id }}
                                </td>
                                <td>
                                    {{ $rf->created_at ? $rf->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">{{ $rf->booking?->user?->name ?? $rf->user?->name ?? 'Pelanggan' }}</div>
                                    <small style="color: #64748b;">{{ $rf->booking?->user?->phone ?? $rf->user?->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    <div style="max-width: 200px; font-size: 12px; color: #475569;">
                                        {{ $rf->reason ?? '-' }}
                                    </div>
                                </td>
                                <td style="text-align: right; text-decoration: line-through; color: #64748b;">
                                    Rp {{ number_format($orig, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; color: #dc2626; font-weight: 600;">
                                    Rp {{ number_format($rf->cancellation_fee, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-weight: 800; color: #087f55;">
                                    Rp {{ number_format($rf->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div style="font-size: 11px;">
                                        <strong>{{ strtoupper($rf->bank_name ?? 'BANK') }}</strong> - {{ $rf->account_number ?? '-' }}
                                        <div style="color: #64748b;">a.n {{ $rf->account_holder ?? '-' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status {{ strtolower($rf->status) === 'disetujui' ? 'paid' : (strtolower($rf->status) === 'ditolak' ? 'completed' : 'active') }}">
                                        {{ $rf->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $rf->processor?->name ?? '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state-wrap">
                                        <h4 class="empty-state-title">Tidak Ada Riwayat Pembatalan</h4>
                                        <p class="empty-state-desc">Tidak ditemukan permohonan pembatalan tiket pada rentang tanggal dan filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
