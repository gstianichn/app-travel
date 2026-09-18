@extends('layouts.admin')

@section('title', 'Jadwal Keberangkatan Harian - Admin Utama')

@section('page-title', 'Jadwal Keberangkatan Harian')

@section('page-subtitle', 'Pengaturan jam jalan harian (06:00, 10:00, 14:00, 18:00), penetapan armada minibus & sopir, serta monitoring kuota kursi.')

@section('content')

<style>
    .schedule-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* HEADER */
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

    .btn-create-single {
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

    .btn-create-single:hover {
        background: #e3f5ee;
    }

    .btn-batch-generator {
        padding: 10px 18px;
        background: #0284c7;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        transition: background 0.2s ease;
    }

    .btn-batch-generator:hover {
        background: #0369a1;
    }

    .btn-back-route {
        padding: 10px 14px;
        background: #f1f5f3;
        color: #4b5852;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-back-route:hover {
        background: #e2eae6;
        color: #17221d;
    }

    /* STATS SUMMARY CARDS */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
    }

    /* FILTER CARD */
    .filter-card {
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        padding: 16px 20px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 1.2fr 2fr 1.2fr 1fr auto;
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
    }

    .filter-input,
    .filter-select {
        height: 40px;
        padding: 0 12px;
        border: 1px solid #d4dfda;
        border-radius: 8px;
        font-size: 13px;
        background: #fbfdfc;
        outline: none;
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
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .schedule-table th {
        background: #f8faf9;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #61736b;
        border-bottom: 1px solid #edf1ef;
        white-space: nowrap;
    }

    .schedule-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .schedule-table tr:hover {
        background: #fbfdfc;
    }

    /* TIME PILL */
    .time-badge {
        font-size: 15px;
        font-weight: 800;
        font-family: monospace;
        color: #0f172a;
    }

    .date-sub {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
    }

    /* VEHICLE & DRIVER ASSIGNED PILLS */
    .assigned-box {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .assigned-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }

    .assigned-sub {
        font-size: 11px;
        color: #64748b;
    }

    .driver-name {
        font-weight: 600;
        color: #087f55;
    }

    /* LOAD FACTOR PROGRESS */
    .load-factor-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 110px;
    }

    .load-factor-bar {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .load-factor-fill {
        height: 100%;
        border-radius: 99px;
    }

    .load-factor-fill.low { background: #10b981; }
    .load-factor-fill.mid { background: #f59e0b; }
    .load-factor-fill.high { background: #ef4444; }

    /* STATUS PILLS */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge.completed {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-badge.cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ACTIONS CELL */
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

    .btn-action-assign {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action-assign:hover {
        background: #bae6fd;
    }

    /* TIME CHECKBOXES GRID IN BATCH MODAL */
    .time-slots-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .time-slot-label {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .time-slot-label:hover {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .time-slot-label input[type="checkbox"] {
        accent-color: #0284c7;
        width: 16px;
        height: 16px;
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
    .form-select {
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: #fbfdfc;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: #087f55;
        background: white;
    }

    @media (max-width: 1024px) {
        .filter-form {
            grid-template-columns: 1fr 1fr;
        }
        .stats-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="schedule-page">

    {{-- HEADER ROW --}}
    <div class="page-header-row">
        <div>
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('dashboard.admin.rute.index') }}">Rute Perjalanan</a>
                <span>&rsaquo;</span>
                <span>Jadwal Harian</span>
            </div>
            <div class="header-text">
                <h1>Jadwal Keberangkatan Harian</h1>
                <p>Pengaturan jam jalan (06:00, 10:00, 14:00, 18:00), penetapan armada minibus & sopir, serta sisa kuota kursi.</p>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('dashboard.admin.rute.index') }}" class="btn-back-route">
                &larr; Daftar Rute
            </a>
            <button type="button" class="btn-create-single" onclick="openSingleScheduleModal()">
                + Buat Jadwal Tunggal
            </button>
            <button type="button" class="btn-batch-generator" onclick="openBatchModal()">
                ⚡ Batch Generator Jam Jalan
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div style="padding: 12px 18px; border-radius: 10px; font-size: 13px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 12px 18px; border-radius: 10px; font-size: 13px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
            <span>⚠️ {{ session('error') }}</span>
        </div>
    @endif

    {{-- METRICS SUMMARY --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Total Jadwal Mendatang</div>
                <div class="stat-box-num">{{ number_format($totalSchedulesCount) }}</div>
            </div>
            <div class="stat-box-icon" style="background: #e0f2fe; color: #0369a1;">
                📅
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Sedang di Jalan (Active)</div>
                <div class="stat-box-num" style="color: #15803d;">{{ number_format($activeTripsCount) }}</div>
            </div>
            <div class="stat-box-icon" style="background: #dcfce7; color: #15803d;">
                🚐
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-info">
                <div class="stat-box-label">Terjadwal Siap Berangkat</div>
                <div class="stat-box-num" style="color: #d97706;">{{ number_format($scheduledCount) }}</div>
            </div>
            <div class="stat-box-icon" style="background: #fef3c7; color: #d97706;">
                ⏳
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form action="{{ route('dashboard.admin.rute.jadwal') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label" for="date">Tanggal Keberangkatan</label>
                <input type="date" name="date" id="date" class="filter-input" value="{{ request('date', now()->toDateString()) }}">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="route_id">Pilih Rute</label>
                <select name="route_id" id="route_id" class="filter-select">
                    <option value="all">Semua Rute</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" {{ request('route_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->routeTitle() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="vehicle_id">Armada</label>
                <select name="vehicle_id" id="vehicle_id" class="filter-select">
                    <option value="all">Semua Unit</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }} [{{ $v->plate_number }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="status">Status</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active (Jalan)</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-submit">
                Terapkan Filter
            </button>
        </form>
    </div>

    {{-- SCHEDULES TABLE CARD --}}
    <div class="table-card">
        <div style="overflow-x: auto;">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Jam Jalan & Tanggal</th>
                        <th>Rute Perjalanan</th>
                        <th>Armada Minibus Terpasang</th>
                        <th>Pengemudi (Sopir)</th>
                        <th>Keterisian Kursi</th>
                        <th>Tarif Tiket</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sch)
                        @php
                            $loadPct = $sch->loadFactorPercentage();
                            $colorClass = $loadPct > 80 ? 'high' : ($loadPct > 40 ? 'mid' : 'low');
                            $dTime = $sch->departure_time ? \Carbon\Carbon::parse($sch->departure_time)->format('H:i') : '-';
                            $dDate = $sch->departure_date ? $sch->departure_date->format('d M Y') : '-';
                        @endphp
                        <tr>
                            {{-- JAM JALAN --}}
                            <td>
                                <div class="time-badge">🕒 {{ $dTime }} WIB</div>
                                <div class="date-sub">{{ $dDate }}</div>
                            </td>

                            {{-- RUTE --}}
                            <td>
                                <div style="font-weight: 700; color: #17221d;">
                                    {{ $sch->route ? $sch->route->routeTitle() : 'Rute Travel' }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    Estimasi: {{ $sch->route?->duration ?? '-' }}
                                </div>
                            </td>

                            {{-- ARMADA --}}
                            <td>
                                <div class="assigned-box">
                                    <span class="assigned-title">
                                        🚐 {{ $sch->vehicle?->name ?? 'Belum ada armada' }}
                                    </span>
                                    <span class="assigned-sub">
                                        {{ $sch->vehicle ? '[' . $sch->vehicle->plate_number . '] • ' . $sch->vehicle->capacity . ' Kursi' : 'Silakan pasangkan unit' }}
                                    </span>
                                </div>
                            </td>

                            {{-- SOPIR --}}
                            <td>
                                <div class="assigned-box">
                                    <span class="driver-name">
                                        🧑‍✈️ {{ $sch->driver?->user?->name ?? 'Belum ada sopir' }}
                                    </span>
                                    <span class="assigned-sub">
                                        {{ $sch->driver?->user?->phone ? '📱 ' . $sch->driver->user->phone : 'Standby' }}
                                    </span>
                                </div>
                            </td>

                            {{-- KETERISIAN KURSI --}}
                            <td>
                                <div class="load-factor-wrap">
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700;">
                                        <span>Sisa: {{ $sch->available_seats }}</span>
                                        <span style="color: #64748b;">{{ $loadPct }}%</span>
                                    </div>
                                    <div class="load-factor-bar">
                                        <div class="load-factor-fill {{ $colorClass }}" style="width: {{ $loadPct }}%;"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- TARIF TIKET --}}
                            <td>
                                <div style="font-weight: 800; color: #087f55;">
                                    {{ $sch->formattedEffectivePrice() }}
                                </div>
                                @if($sch->isWeekendDeparture())
                                    <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 2px 6px; border-radius: 4px;">
                                        Weekend
                                    </span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="status-badge {{ strtolower($sch->status) }}">
                                    {{ $sch->status }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Quick Assign Vehicle & Driver --}}
                                    <button
                                        type="button"
                                        class="btn-action-assign"
                                        onclick="openAssignModal({{ $sch->id }}, '{{ $sch->vehicle_id }}', '{{ $sch->driver_id }}', '{{ $dDate }} {{ $dTime }}', '{{ $sch->route?->routeTitle() }}')"
                                        title="Tetapkan atau Ganti Armada & Sopir"
                                    >
                                        🚐 Pasang Sopir/Armada
                                    </button>

                                    {{-- Seat Map Link --}}
                                    <a
                                        href="{{ route('dashboard.admin.pemesanan.seat-map', $sch->id) }}"
                                        class="btn-action-icon"
                                        title="Denah Kursi Visual"
                                    >
                                        💺
                                    </a>

                                    {{-- Manifest Link --}}
                                    <a
                                        href="{{ route('dashboard.admin.pemesanan.manifes', $sch->id) }}"
                                        class="btn-action-icon"
                                        title="Manifes Perjalanan"
                                    >
                                        📋
                                    </a>

                                    {{-- Delete Schedule --}}
                                    <form
                                        action="{{ route('dashboard.admin.rute.jadwal.destroy', $sch->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Hanya jadwal tanpa pemesanan yang dapat dihapus.');"
                                        style="display: inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon" style="color: #b91c1c;" title="Hapus Jadwal">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px 20px; color: #64748b;">
                                <div style="font-size: 36px; margin-bottom: 8px;">📅</div>
                                <div style="font-weight: 700; font-size: 15px; color: #17221d;">Tidak Ada Jadwal Keberangkatan</div>
                                <p style="margin: 4px 0 12px; font-size: 13px;">Gunakan Batch Generator untuk membuat jadwal harian secara otomatis.</p>
                                <button type="button" class="btn-batch-generator" onclick="openBatchModal()">
                                    ⚡ Buka Batch Generator Jam Jalan
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #edf1ef;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

{{-- =========================================
     MODAL 1: BATCH SCHEDULE GENERATOR
========================================== --}}
<div id="batchModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 680px;">
        <div class="modal-dialog-header">
            <div>
                <h3>⚡ Batch Generator Jam Jalan Harian</h3>
                <span style="font-size: 12px; color: #64748b;">Buat banyak slot jadwal sekaligus dalam rentang tanggal</span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeBatchModal()">&times;</button>
        </div>

        <form action="{{ route('dashboard.admin.rute.jadwal.batch-generate') }}" method="POST">
            @csrf
            <div class="modal-dialog-body">
                <div class="form-group">
                    <label class="form-label" for="batch_route_id">Pilih Rute Perjalanan <span style="color: #dc2626;">*</span></label>
                    <select name="route_id" id="batch_route_id" class="form-select" required>
                        <option value="">-- Pilih Rute --</option>
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}">{{ $r->routeTitle() }} &bull; Dasar: {{ $r->formattedPrice() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="batch_vehicle_id">Armada Default <span style="color: #dc2626;">*</span></label>
                        <select name="vehicle_id" id="batch_vehicle_id" class="form-select" required>
                            <option value="">-- Pilih Unit Minibus --</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">
                                    {{ $v->name }} [{{ $v->plate_number }}] &bull; {{ $v->capacity }} Kursi
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="batch_driver_id">Pengemudi (Sopir) Default</label>
                        <select name="driver_id" id="batch_driver_id" class="form-select">
                            <option value="">-- Tetapkan Nanti / Standby --</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->user?->name ?? 'Driver' }} ({{ $d->license_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="batch_start_date">Dari Tanggal <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="start_date" id="batch_start_date" class="form-input" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="batch_end_date">Sampai Tanggal <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="end_date" id="batch_end_date" class="form-input" value="{{ now()->addDays(7)->toDateString() }}" required>
                    </div>
                </div>

                {{-- JAM JALAN HARIAN SELECTION --}}
                <div class="form-group">
                    <label class="form-label">
                        Centang Jam Jalan Keberangkatan Harian <span style="color: #dc2626;">*</span>
                    </label>
                    <div class="time-slots-grid">
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="06:00" checked>
                            <span>06:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="10:00" checked>
                            <span>10:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="14:00" checked>
                            <span>14:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="18:00" checked>
                            <span>18:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="08:00">
                            <span>08:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="12:00">
                            <span>12:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="16:00">
                            <span>16:00 WIB</span>
                        </label>
                        <label class="time-slot-label">
                            <input type="checkbox" name="departure_times[]" value="20:00">
                            <span>20:00 WIB</span>
                        </label>
                    </div>
                    <span style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        Jadwal yang sudah ada pada kombinasi rute, armada, tanggal & jam yang sama akan dilewati secara otomatis untuk mencegah duplikasi.
                    </span>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-single" onclick="closeBatchModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-batch-generator">
                    ⚡ Generate Semua Jadwal Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================
     MODAL 2: ASSIGN ARMADA & SOPIR
========================================== --}}
<div id="assignModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 520px;">
        <div class="modal-dialog-header">
            <div>
                <h3 style="margin: 0;">Penetapan Armada & Sopir</h3>
                <span id="assignSubtitle" style="font-size: 12px; color: #087f55; font-weight: 700;"></span>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeAssignModal()">&times;</button>
        </div>

        <form id="assignForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-dialog-body">
                <div class="form-group">
                    <label class="form-label" for="assign_vehicle_id">
                        Pilih Unit Minibus <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="vehicle_id" id="assign_vehicle_id" class="form-select" required>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->name }} [{{ $v->plate_number }}] &bull; {{ $v->capacity }} Kursi
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="assign_driver_id">
                        Pilih Pengemudi (Sopir)
                    </label>
                    <select name="driver_id" id="assign_driver_id" class="form-select">
                        <option value="">-- Belum Ditugaskan / Kosong --</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->user?->name ?? 'Driver' }} ({{ $d->license_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-single" onclick="closeAssignModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-filter-submit">
                    Simpan Penetapan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================
     MODAL 3: BUAT JADWAL TUNGGAL
========================================== --}}
<div id="singleScheduleModal" class="custom-modal">
    <div class="modal-dialog" style="max-width: 580px;">
        <div class="modal-dialog-header">
            <h3>Tambah 1 Slot Jadwal Keberangkatan</h3>
            <button type="button" class="btn-close-modal" onclick="closeSingleScheduleModal()">&times;</button>
        </div>

        <form action="{{ route('dashboard.admin.rute.jadwal.store') }}" method="POST">
            @csrf
            <div class="modal-dialog-body">
                <div class="form-group">
                    <label class="form-label" for="single_route_id">Pilih Rute Perjalanan <span style="color: #dc2626;">*</span></label>
                    <select name="route_id" id="single_route_id" class="form-select" required>
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}">{{ $r->routeTitle() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="single_date">Tanggal Jalan <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="departure_date" id="single_date" class="form-input" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="single_time">Jam Keberangkatan <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="departure_time" id="single_time" class="form-input" placeholder="06:00" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="single_vehicle_id">Armada Minibus <span style="color: #dc2626;">*</span></label>
                        <select name="vehicle_id" id="single_vehicle_id" class="form-select" required>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} [{{ $v->plate_number }}]</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="single_driver_id">Sopir</label>
                        <select name="driver_id" id="single_driver_id" class="form-select">
                            <option value="">-- Pilih Sopir (Opsional) --</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->user?->name ?? 'Driver' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-dialog-footer">
                <button type="button" class="btn-create-single" onclick="closeSingleScheduleModal()" style="border-color: #cbd5e1; color: #475569;">
                    Batal
                </button>
                <button type="submit" class="btn-filter-submit">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBatchModal() {
        document.getElementById('batchModal').classList.add('show');
    }
    function closeBatchModal() {
        document.getElementById('batchModal').classList.remove('show');
    }

    function openSingleScheduleModal() {
        document.getElementById('singleScheduleModal').classList.add('show');
    }
    function closeSingleScheduleModal() {
        document.getElementById('singleScheduleModal').classList.remove('show');
    }

    function openAssignModal(scheduleId, vehicleId, driverId, datetimeStr, routeTitle) {
        document.getElementById('assignForm').action = `/rute/jadwal/${scheduleId}/assign`;
        document.getElementById('assignSubtitle').innerText = `${routeTitle} • ${datetimeStr}`;
        if (vehicleId) document.getElementById('assign_vehicle_id').value = vehicleId;
        if (driverId) document.getElementById('assign_driver_id').value = driverId;
        document.getElementById('assignModal').classList.add('show');
    }
    function closeAssignModal() {
        document.getElementById('assignModal').classList.remove('show');
    }

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal')) {
            e.target.classList.remove('show');
        }
    });
</script>

@endsection
