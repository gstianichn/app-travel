@extends('layouts.admin')

@section('title', 'Detail Pemesanan - ' . $booking->booking_code)

@section('page-title', 'Detail Pemesanan & E-Ticket')

@section('page-subtitle', 'Rincian data tiket, alokasi kursi penumpang, pembayaran, dan informasi penugasan armada travel.')

@section('content')

<style>
    .booking-show-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* TOP NAV */
    .show-nav-row {
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

    .show-nav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
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
    }

    .btn-action-reschedule {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 10px;
        background: #0284c7;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-action-reschedule:hover {
        background: #0369a1;
    }

    .btn-action-print {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 10px;
        background: #1e293b;
        color: white;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    /* FLASH ALERTS */
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

    /* E-TICKET HERO CARD */
    .eticket-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .eticket-header {
        background: linear-gradient(135deg, #087f55 0%, #066b47 100%);
        color: white;
        padding: 24px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .eticket-header-main h1 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .eticket-header-main p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
    }

    .eticket-code-pill {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        padding: 8px 18px;
        border-radius: 10px;
        font-family: monospace;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 1px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .eticket-body {
        padding: 30px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* 2 COLUMNS SUMMARY */
    .ticket-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .ticket-section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .spec-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f3;
        font-size: 13px;
    }

    .spec-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .spec-lbl {
        color: #64748b;
    }

    .spec-val {
        font-weight: 700;
        color: #1e293b;
        text-align: right;
    }

    /* PASSENGER TABLE */
    .passenger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .passenger-table th {
        background: #f8fafc;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .passenger-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .seat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e3f5ee;
        color: #087f55;
        font-weight: 800;
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #c8ebd8;
    }

    /* PAYMENT SUMMARY BOX */
    .payment-summary-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
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

    @media print {
        .admin-sidebar, .admin-header, .show-nav-row, .btn-action-print, .btn-action-reschedule {
            display: none !important;
        }

        .booking-show-container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .eticket-card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }
    }

    @media (max-width: 768px) {
        .ticket-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="booking-show-container">

    {{-- TOP NAV & ACTIONS --}}
    <div class="show-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('dashboard.admin.pemesanan.index') }}">Pemesanan</a>
            <span>&rsaquo;</span>
            <span>{{ $booking->booking_code }}</span>
        </div>

        <div class="show-nav-actions">
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="btn-action-back">
                &larr; Daftar Pemesanan
            </a>

            @if($booking->schedule_id)
                <a href="{{ route('dashboard.admin.pemesanan.seat-map', $booking->schedule_id) }}" class="btn-action-back">
                    Denah Kursi
                </a>
                <a href="{{ route('dashboard.admin.pemesanan.manifes', $booking->schedule_id) }}" class="btn-action-back">
                    Manifes Jadwal
                </a>
            @endif

            @if(strtolower($booking->status) !== 'cancelled')
                <a href="{{ route('dashboard.admin.pemesanan.reschedule', $booking->id) }}" class="btn-action-reschedule">
                    Reschedule / Geser Kursi
                </a>
            @endif

            <button type="button" class="btn-action-print" onclick="window.print()">
                Cetak E-Ticket
            </button>
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

    {{-- E-TICKET CARD --}}
    <div class="eticket-card">
        @php
            $routeLabel = $booking->schedule?->route 
                ? ($booking->schedule->route->origin?->name . ' ke ' . $booking->schedule->route->destination?->name) 
                : ($booking->schedule?->travelRoute?->name ?? 'Rute Travel');
        @endphp
        <div class="eticket-header">
            <div class="eticket-header-main">
                <p>E-TICKET RESMI PERJALANAN TRAVEL</p>
                <h1>{{ $routeLabel }}</h1>
                <span style="font-size: 12px; opacity: 0.85;">
                    Saluran: {{ $booking->sourceLabel() }} &bull; Dipesan pada {{ $booking->created_at ? $booking->created_at->format('d M Y, H:i') : '-' }} WIB
                </span>
            </div>

            <div class="eticket-code-pill">
                {{ $booking->booking_code }}
            </div>
        </div>

        <div class="eticket-body">

            {{-- 2 COLUMNS: JADWAL & PELANGGAN --}}
            <div class="ticket-grid">
                {{-- JADWAL PERJALANAN --}}
                <div>
                    <div class="ticket-section-title">Informasi Perjalanan</div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="spec-item">
                            <span class="spec-lbl">Rute</span>
                            <span class="spec-val" style="color: #087f55;">{{ $routeLabel }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Tanggal Keberangkatan</span>
                            <span class="spec-val">{{ $booking->schedule?->departure_date ? $booking->schedule->departure_date->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Jam Berangkat</span>
                            <span class="spec-val">{{ $booking->schedule?->departure_time ? \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') : '-' }} WIB</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Armada Minibus</span>
                            <span class="spec-val">{{ $booking->schedule?->vehicle?->name ?? 'Minibus' }} ({{ $booking->schedule?->vehicle?->plate_number ?? '-' }})</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Pengemudi (Sopir)</span>
                            <span class="spec-val">{{ $booking->schedule?->driver?->user?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                    </div>
                </div>

                {{-- PEMESAN & PEMBAYARAN --}}
                <div>
                    <div class="ticket-section-title">Informasi Pemesan & Pembayaran</div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="spec-item">
                            <span class="spec-lbl">Nama Pemesan</span>
                            <span class="spec-val">{{ $booking->customerName() }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Nomor WhatsApp / HP</span>
                            <span class="spec-val">{{ $booking->customerPhone() }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Status Pembayaran</span>
                            <span class="spec-val">
                                <span class="badge-status {{ $booking->statusBadgeClass() }}">
                                    {{ $booking->statusLabel() }}
                                </span>
                            </span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Metode Pembayaran</span>
                            <span class="spec-val">{{ $booking->payment?->payment_method ?? 'Loket' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-lbl">Total Biaya</span>
                            <span class="spec-val" style="color: #087f55; font-size: 15px;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CATATAN / TITIK JEMPUT --}}
            @if($booking->notes)
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                        Titik Jemput / Catatan Khusus:
                    </span>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #1e293b; white-space: pre-line;">
                        {{ $booking->notes }}
                    </p>
                </div>
            @endif

            {{-- DAFTAR PENUMPANG & NOMOR KURSI --}}
            <div>
                <div class="ticket-section-title">Daftar Penumpang & Alokasi Kursi</div>
                <table class="passenger-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 100px;">Nomor Kursi</th>
                            <th>Nama Penumpang</th>
                            <th>Nomor Telepon</th>
                            <th>Nomor Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passengers as $idx => $passenger)
                            <tr>
                                <td style="font-weight: 700;">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="seat-badge">
                                        {{ $passenger->seat?->seat_number ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $passenger->name }}</strong>
                                </td>
                                <td>
                                    {{ $passenger->phone ?: $booking->customerPhone() }}
                                </td>
                                <td style="font-family: monospace; font-size: 12px; color: #64748b;">
                                    {{ $booking->ticket?->ticket_number ?? 'TKT-' . $booking->booking_code }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- FOOTER NOTICE --}}
            <div class="payment-summary-box">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Ketentuan Keberangkatan:</span>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #334155;">
                        Penumpang dimohon tiba di pool/titik jemput 15 menit sebelum waktu keberangkatan yang tercantum pada e-ticket.
                    </p>
                </div>

                @if(strtolower($booking->status) !== 'cancelled')
                    <form
                        action="{{ route('dashboard.admin.pemesanan.cancel', $booking->id) }}"
                        method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini? Kursi akan dilepaskan kembali.');"
                    >
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="padding: 8px 16px; border: 1px solid #fecaca; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                            Batalkan Pemesanan
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

</div>

@endsection
