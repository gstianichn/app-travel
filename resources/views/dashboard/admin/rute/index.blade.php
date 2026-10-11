@extends('layouts.admin')

@section('title', 'Rute Perjalanan & Tarif - Admin Utama')

@section('page-title', 'Rute Perjalanan & Tarif')

@section('page-subtitle', 'Manajemen titik awal pool, titik tujuan drop point, tarif dasar & penyesuaian akhir pekan, serta jadwal keberangkatan.')

@section('content')

<style>
    .route-page {
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

    .btn-create-route {
        padding: 10px 18px;
        background: #087f55;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(8, 127, 85, 0.25);
        transition: background 0.2s ease;
    }

    .btn-create-route:hover {
        background: #066b47;
    }

    .btn-create-location {
        padding: 10px 16px;
        background: white;
        color: #087f55;
        border: 1.5px solid #087f55;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-create-location:hover {
        background: #e3f5ee;
    }

    .btn-schedule-nav {
        padding: 10px 16px;
        background: #0284c7;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        transition: background 0.2s ease;
    }

    .btn-schedule-nav:hover {
        background: #0369a1;
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

    .stat-box-icon.routes {
        background: #e3f5ee;
        color: #087f55;
    }

    .stat-box-icon.pools {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-box-icon.schedules {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-box-icon.pricing {
        background: #f3e8ff;
        color: #9333ea;
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
        grid-template-columns: 2fr 1.5fr auto;
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

    .route-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .route-table th {
        background: #f8faf9;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #61736b;
        border-bottom: 1px solid #edf1ef;
        white-space: nowrap;
    }

    .route-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .route-table tr:hover {
        background: #fbfdfc;
    }

    /* LOCATION POINT CELLS */
    .point-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .point-title {
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .point-city {
        font-size: 11px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .point-tag {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        display: inline-block;
        font-family: monospace;
    }

    .route-arrow {
        font-size: 16px;
        font-weight: 800;
        color: #087f55;
        padding: 0 8px;
    }

    /* PRICING CELL */
    .price-box {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .price-base {
        font-size: 14px;
        font-weight: 800;
        color: #087f55;
    }

    .price-weekend {
        font-size: 11px;
        font-weight: 700;
        color: #d97706;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* BADGES */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-status.active {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-status.inactive {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-schedules {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border-radius: 7px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .badge-schedules:hover {
        background: #bae6fd;
    }

    /* ACTIONS */
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
        border: 1px solid #d4dfda;
        background: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
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

    /* MODAL */
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
        max-width: 650px;
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

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fbfdfc;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #087f55;
        background: white;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        .filter-form {
            grid-template-columns: 1fr;
        }
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="route-page">

    {{-- HEADER ROW --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>&rsaquo;</span>
                <span>Rute Perjalanan</span>
            </div>
            <div class="header-text">
                <h1>Rute Perjalanan & Tarif Travel</h1>
                <p>Kelola jalur antar kota, titik pool keberangkatan/tujuan, penetapan tarif tiket weekday/weekend, dan jam jalan armada.</p>
            </div>
        </div>

        <div class="header-actions">
            <button type="button" class="btn-create-location" onclick="openLocationModal()">
                + Titik Pool / Drop Point
            </button>
            <button type="button" class="btn-create-route" onclick="openCreateRouteModal()">
                + Tambah Rute Baru
            </button>
            <a href="{{ route('dashboard.admin.rute.jadwal') }}" class="btn-schedule-nav">
                Kelola Jadwal Harian</a>
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

    {{-- METRIC SUMMARY CARDS --}}
    <div class="stats-row">
        {{-- Total Rute --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Rute Jalur</div>
                <div class="stat-box-num">{{ number_format($totalRoutes) }}</div>
            </div>
        </div>

        {{-- Titik Pool / Drop Point --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Titik Pool & Shuttle</div>
                <div class="stat-box-num" style="color: #0284c7;">{{ number_format($totalLocations) }}</div>
            </div>
        </div>

        {{-- Jadwal Keberangkatan Aktif --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Jadwal Aktif</div>
                <div class="stat-box-num" style="color: #d97706;">{{ number_format($activeSchedulesCount) }}</div>
            </div>
        </div>

        {{-- Rute Aktif Beroperasi --}}
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Rute Status Aktif</div>
                <div class="stat-box-num" style="color: #15803d;">{{ number_format($activeRoutesCount) }}</div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.rute.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label" for="search">Cari Nama Pool, Kota, atau Kode</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="filter-input"
                    placeholder="Contoh: Bandung, Pasteur, Jakarta, Tebet.."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group">
                <label class="filter-label" for="status">Status Rute</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit">
                Terapkan Filter
            </button>
        </form>
    </div>

    {{-- ROUTES TABLE CARD --}}
    <div class="table-card">
        <div class="table-container">
            <table class="route-table">
                <thead>
                    <tr>
                        <th>Titik Keberangkatan & Tujuan (Pool ke Drop Point)</th>
                        <th>Estimasi Perjalanan</th>
                        <th>Tarif Weekday & Weekend</th>
                        <th>Jadwal Aktif Terdaftar</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr>
                            {{-- ORIGIN & DESTINATION --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="point-cell">
                                        <div class="point-title">{{ $route->origin?->name ?? 'Asal' }}</div>
                                        <div class="point-city">
                                            <span class="point-tag">{{ $route->origin?->code ?? '-' }}</span>
                                            <span>{{ $route->origin?->city ?? 'Origin Pool' }}</span>
                                        </div>
                                    </div>

                                    <div class="route-arrow"></div>

                                    <div class="point-cell">
                                        <div class="point-title">{{ $route->destination?->name ?? 'Tujuan' }}</div>
                                        <div class="point-city">
                                            <span class="point-tag">{{ $route->destination?->code ?? '-' }}</span>
                                            <span>{{ $route->destination?->city ?? 'Drop Point' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- DURATION & DISTANCE --}}
                            <td>
                                <div style="font-weight: 600; color: #17221d;">
                                    ⏱{{ $route->duration }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    {{ $route->distance_km ? $route->distance_km . ' km' : 'Jalur Reguler' }}
                                </div>
                            </td>

                            {{-- PRICING (WEEKDAY / WEEKEND) --}}
                            <td>
                                <div class="price-box">
                                    <div class="price-base">
                                        {{ $route->formattedPrice() }}
                                        <span style="font-size: 10px; font-weight: 600; color: #64748b;">(Hari Biasa)</span>
                                    </div>
                                    @if($route->weekend_price)
                                        <div class="price-weekend">
                                            {{ $route->formattedWeekendPrice() }} (Weekend)
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- JADWAL TERDAFTAR --}}
                            <td>
                                <a href="{{ route('dashboard.admin.rute.jadwal', ['route_id' => $route->id]) }}" class="badge-schedules">
                                    {{ $route->schedules->count() }} Jadwal Tersedia</a>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <form action="{{ route('dashboard.admin.rute.toggle-status', $route->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="badge-status {{ $route->is_active ? 'active' : 'inactive' }}"
                                        style="border: none; cursor: pointer;"
                                        title="Klik untuk mengubah status aktif/nonaktif"
                                    >
                                        {{ $route->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Edit Route Button --}}
                                    <button
                                        type="button"
                                        class="btn-action-icon"
                                        onclick='openEditRouteModal({{ json_encode($route) }})'
                                        title="Ubah Rute & Tarif"
                                    >Ubah
                                                                            </button>

                                    {{-- Create Schedule for this Route --}}
                                    <a
                                        href="{{ route('dashboard.admin.rute.jadwal', ['route_id' => $route->id]) }}"
                                        class="btn-action-icon"
                                        title="Buka Jadwal Rute"
                                    >Jadwal
                                                                            </a>

                                    {{-- Delete Route Form --}}
                                    <form
                                        action="{{ route('dashboard.admin.rute.destroy', $route->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini? Rute hanya dapat dihapus jika tidak ada tiket aktif.');"
                                        style="display: inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus Rute">Hapus
                                                                                    </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px 20px; color: #64748b;">
                                <div style="font-size: 36px; margin-bottom: 8px;"></div>
                                <div style="font-weight: 700; font-size: 15px; color: #17221d;">Belum Ada Rute Perjalanan</div>
                                <p style="margin: 4px 0 12px; font-size: 13px;">Tambahkan titik pool asal dan tujuan untuk mulai membuka rute baru.</p>
                                <button type="button" class="btn-create-route" onclick="openCreateRouteModal()">
                                    + Tambah Rute Pertama
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($routes->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #edf1ef;">
                {{ $routes->links() }}
            </div>
        @endif
    </div>

</div>

{{-- =========================================
     MODAL 1: TAMBAH RUTE BARU
========================================== --}}
<div id="createRouteModal" class="custom-modal">
    <div class="modal-dialog">
        <div class="modal-dialog-header">
            <h3>Pendaftaran Rute Perjalanan Baru</h3>
            <button type="button" class="btn-close-modal" onclick="closeCreateRouteModal()">&times;</button>
        </div>

        <form action="{{ route('dashboard.admin.rute.store') }}" method="POST">
            @csrf
            <div class="modal-dialog-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="new_origin_id">
                            Titik Awal Keberangkatan (Origin) <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="origin_id" id="new_origin_id" class="form-select" required>
                            <option value="">-- Pilih Pool / Shuttle Asal --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">
                                    {{ $loc->name }} ({{ $loc->code }}) &bull; {{ $loc->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_destination_id">
                            Titik Tujuan Akhir (Drop Point) <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="destination_id" id="new_destination_id" class="form-select" required>
                            <option value="">-- Pilih Titik Tujuan --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">
                                    {{ $loc->name }} ({{ $loc->code }}) &bull; {{ $loc->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="new_price">
                            Tarif Dasar Tiket (Weekday) <span style="color: #dc2626;">*</span>
                        </label>
                        <input
                            type="number"
                            name="price"
                            id="new_price"
                            class="form-input"
                            placeholder="Contoh: 150000"
                            min="10000"
                            step="1000"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_weekend_price">
                            Tarif Akhir Pekan (Weekend / Libur)
                        </label>
                        <input
                            type="number"
                            name="weekend_price"
                            id="new_weekend_price"
                            class="form-input"
                            placeholder="Contoh: 175000 (Opsional)"
                            min="10000"
                            step="1000"
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="new_duration">
                            Estimasi Waktu Tempuh <span style="color: #dc2626;">*</span>
                        </label>
                        <input
                            type="text"
                            name="duration"
                            id="new_duration"
                            class="form-input"
                            placeholder="Contoh: 3 Jam, 3.5 Jam.."
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_distance_km">
                            Jarak Tempuh (km)
                        </label>
                        <input
                            type="number"
                            name="distance_km"
                            id="new_distance_km"
                            class="form-input"
                            placeholder="Contoh: 150"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_notes">Catatan Rute / Titik Singgah Tambahan (Opsional)</label>
                    <textarea
                        name="notes"
                        id="new_notes"
                        rows="2"
                        class="form-textarea"
                        placeholder="Contoh: Melewati Tol Cipularang, singgah di rest area KM 72 bila dibutuhkan.."
                    ></textarea>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-location" onclick="closeCreateRouteModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-create-route">
                    Simpan Rute Baru
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================
     MODAL 2: EDIT RUTE
========================================== --}}
<div id="editRouteModal" class="custom-modal">
    <div class="modal-dialog">
        <div class="modal-dialog-header">
            <h3>Perbarui Data Rute & Penyesuaian Tarif</h3>
            <button type="button" class="btn-close-modal" onclick="closeEditRouteModal()">&times;</button>
        </div>

        <form id="editRouteForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-dialog-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="edit_origin_id">
                            Titik Asal (Origin) <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="origin_id" id="edit_origin_id" class="form-select" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_destination_id">
                            Titik Tujuan (Drop Point) <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="destination_id" id="edit_destination_id" class="form-select" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="edit_price">
                            Tarif Dasar (Weekday) <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" name="price" id="edit_price" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_weekend_price">
                            Tarif Akhir Pekan (Weekend)
                        </label>
                        <input type="number" name="weekend_price" id="edit_weekend_price" class="form-input">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="edit_duration">
                            Estimasi Waktu Tempuh <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="duration" id="edit_duration" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_distance_km">
                            Jarak Tempuh (km)
                        </label>
                        <input type="number" name="distance_km" id="edit_distance_km" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_notes">Catatan Rute</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="form-textarea"></textarea>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-location" onclick="closeEditRouteModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-create-route">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================
     MODAL 3: TAMBAH TITIK POOL / DROP POINT
========================================== --}}
<div id="locationModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 550px;">
        <div class="modal-dialog-header">
            <h3>Daftarkan Titik Pool / Shuttle Baru</h3>
            <button type="button" class="btn-close-modal" onclick="closeLocationModal()">&times;</button>
        </div>

        <form action="{{ route('dashboard.admin.rute.lokasi.store') }}" method="POST">
            @csrf
            <div class="modal-dialog-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="loc_name">Nama Pool / Shuttle <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="name" id="loc_name" class="form-input" placeholder="Contoh: Pool Pasteur" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="loc_code">Kode Singkat (Max 10) <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="code" id="loc_code" class="form-input" placeholder="Contoh: BDG-PST" maxlength="10" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="loc_city">Kota / Wilayah</label>
                        <input type="text" name="city" id="loc_city" class="form-input" placeholder="Contoh: Bandung">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="loc_type">Tipe Titik <span style="color: #dc2626;">*</span></label>
                        <select name="type" id="loc_type" class="form-select" required>
                            <option value="pool">Pool Keberangkatan Utama</option>
                            <option value="shuttle">Shuttle Point / Agen Resmi</option>
                            <option value="drop_point">Drop Point Penurunan Saja</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="loc_address">Alamat Lengkap Pool</label>
                    <textarea name="address" id="loc_address" rows="2" class="form-textarea" placeholder="Contoh: Jl. Dr. Djunjunan No. 120, Pasteur, Bandung.."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="loc_phone">Nomor Telepon / WhatsApp Loket</label>
                    <input type="text" name="phone" id="loc_phone" class="form-input" placeholder="Contoh: 081234567890">
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-location" onclick="closeLocationModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-create-route">
                    Simpan Titik Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateRouteModal() {
        document.getElementById('createRouteModal').classList.add('show');
    }
    function closeCreateRouteModal() {
        document.getElementById('createRouteModal').classList.remove('show');
    }

    function openEditRouteModal(route) {
        document.getElementById('editRouteForm').action = `/rute/${route.id}`;
        document.getElementById('edit_origin_id').value = route.origin_id;
        document.getElementById('edit_destination_id').value = route.destination_id;
        document.getElementById('edit_price').value = parseInt(route.price);
        document.getElementById('edit_weekend_price').value = route.weekend_price ? parseInt(route.weekend_price) : '';
        document.getElementById('edit_duration').value = route.duration;
        document.getElementById('edit_distance_km').value = route.distance_km || '';
        document.getElementById('edit_notes').value = route.notes || '';
        document.getElementById('editRouteModal').classList.add('show');
    }
    function closeEditRouteModal() {
        document.getElementById('editRouteModal').classList.remove('show');
    }

    function openLocationModal() {
        document.getElementById('locationModal').classList.add('show');
    }
    function closeLocationModal() {
        document.getElementById('locationModal').classList.remove('show');
    }

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal')) {
            e.target.classList.remove('show');
        }
    });
</script>

@endsection
