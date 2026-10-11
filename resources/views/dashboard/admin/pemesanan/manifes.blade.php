@extends('layouts.admin')

@section('title', 'Manifes Perjalanan - Jadwal #' . $schedule->id)

@section('page-title', 'Manifes Perjalanan')

@section('page-subtitle', 'Daftar resmi penumpang travel per armada, nomor kursi, kontak, dan catatan jemput siap cetak atau dikirim ke sopir.')

@section('content')

<style>
    .manifest-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* TOP ACTION TOOLBAR */
    .manifest-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 16px 22px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-back-link {
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

    .btn-back-link:hover {
        background: #f3f6f4;
    }

    .toolbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #1e293b;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-print:hover {
        background: #0f172a;
    }

    .btn-send-wa {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #16a34a;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .btn-send-wa:hover {
        background: #15803d;
    }

    .btn-copy-text {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #f1f5f3;
        color: #374151;
        border: 1px solid #d8e2de;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-copy-text:hover {
        background: #e2ece7;
    }

    /* =========================================
       PRINTABLE MANIFEST PAPER LAYOUT
    ========================================== */
    .manifest-paper {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        gap: 24px;
        color: #111827;
    }

    /* KOP SURAT MANIFES */
    .manifest-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding-bottom: 20px;
        border-bottom: 2px solid #1e293b;
        gap: 20px;
    }

    .manifest-brand h2 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 0.5px;
        color: #087f55;
    }

    .manifest-brand p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
    }

    .manifest-title-box {
        text-align: right;
    }

    .manifest-title-box h3 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #111827;
    }

    .manifest-title-box span {
        font-size: 12px;
        color: #64748b;
        font-family: monospace;
    }

    /* INFO TRIP GRID */
    .trip-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .trip-meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .trip-meta-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .trip-meta-val {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }

    /* MANIFEST ROSTER TABLE */
    .manifest-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .manifest-table th {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #334155;
        text-align: left;
    }

    .manifest-table td {
        border: 1px solid #cbd5e1;
        padding: 10px 12px;
        color: #1e293b;
        vertical-align: middle;
    }

    .seat-cell-tag {
        display: inline-block;
        background: #e2e8f0;
        color: #0f172a;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-family: monospace;
    }

    .boarding-checkbox-box {
        width: 22px;
        height: 22px;
        border: 1.5px solid #94a3b8;
        border-radius: 4px;
        display: inline-block;
    }

    /* SIGNATURE FOOTER */
    .manifest-footer-signatures {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        gap: 40px;
    }

    .signature-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 200px;
        text-align: center;
    }

    .signature-line {
        width: 100%;
        border-bottom: 1px solid #334155;
        height: 60px;
        margin-bottom: 6px;
    }

    .signature-name {
        font-size: 12px;
        font-weight: 700;
        color: #111827;
    }

    .signature-role {
        font-size: 11px;
        color: #64748b;
    }

    /* PRINT STYLES FOR PRINTER & PDF */
    @media print {
        body {
            background: white !important;
            color: black !important;
        }

        .admin-sidebar,
        .admin-header,
        .manifest-toolbar,
        .breadcrumb-trail,
        .btn-print,
        .btn-send-wa,
        .btn-copy-text {
            display: none !important;
        }

        .manifest-page {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .manifest-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>

<div class="manifest-page">

    {{-- TOOLBAR ACTION --}}
    <div class="manifest-toolbar">
        <div class="toolbar-left">
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="btn-back-link">
                &larr; Daftar Pemesanan
            </a>
            <a href="{{ route('dashboard.admin.pemesanan.seat-map', $schedule->id) }}" class="btn-back-link">
                Denah Kursi
            </a>
        </div>

        @php
            $routeLabel = $schedule->route 
                ? ($schedule->route->origin?->name . ' ke ' . $schedule->route->destination?->name) 
                : ($schedule->travelRoute?->name ?? 'Rute Travel');
            $depDate = $schedule->departure_date ? $schedule->departure_date->format('d M Y') : '-';
            $depTime = $schedule->departure_time ? \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') : '-';
            $vPlate = $schedule->vehicle?->plate_number ?? '-';
            $vName = $schedule->vehicle?->name ?? 'Minibus';
            $driverName = $schedule->driver?->user?->name ?? 'Sopir';
            $driverPhone = $schedule->driver?->user?->phone ?? '';

            // Generate WhatsApp Text for Driver
            $waLines = [];
            $waLines[] = "*MANIFES PENUMPANG TRAVEL*";
            $waLines[] = "Rute: " . $routeLabel;
            $waLines[] = "Jadwal: " . $depDate . ", Pukul " . $depTime . " WIB";
            $waLines[] = "Armada: " . $vName . " (" . $vPlate . ")";
            $waLines[] = "Driver: " . $driverName;
            $waLines[] = "Total Penumpang: " . $sortedPassengers->count() . " Orang";
            $waLines[] = "-----------------------------";
            foreach($sortedPassengers as $i => $p) {
                $num = $i + 1;
                $statusTxt = $p['is_paid'] ? 'LUNAS' : 'BAYAR DI LOKET';
                $noteTxt = ($p['special_request'] && $p['special_request'] !== '-') ? " (Ket: " . $p['special_request'] . ")" : "";
                $waLines[] = "{$num}. [Kursi {$p['seat_number']}] {$p['passenger_name']} ({$p['phone']}) - {$statusTxt}{$noteTxt}";
            }
            $waLines[] = "-----------------------------";
            $waLines[] = "Semoga perjalanan aman dan lancar sampai tujuan!";
            $waMessageFull = implode("\n", $waLines);

            $cleanDriverPhone = preg_replace('/[^0-9]/', '', $driverPhone);
            if (str_starts_with($cleanDriverPhone, '0')) {
                $cleanDriverPhone = '62' . substr($cleanDriverPhone, 1);
            }
            $waUrl = "https://wa.me/{$cleanDriverPhone}?text=" . urlencode($waMessageFull);
        @endphp

        <div class="toolbar-actions">
            <button type="button" class="btn-copy-text" onclick="copyManifestText()">
                Salin Teks
            </button>

            @if($driverPhone)
                <a href="{{ $waUrl }}" target="_blank" class="btn-send-wa">
                    Kirim WhatsApp ke Sopir
                </a>
            @endif

            <button type="button" class="btn-print" onclick="window.print()">
                Cetak Manifes (Print / PDF)
            </button>
        </div>
    </div>

    {{-- MANIFEST PAPER TO PRINT --}}
    <div class="manifest-paper" id="manifestPaper">
        
        {{-- KOP SURAT --}}
        <div class="manifest-header">
            <div class="manifest-brand">
                <h2>ANTIGRAVITY TRAVEL</h2>
                <p>Layanan Transportasi Antar Kota Eksekutif Minibus & Van</p>
                <p style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Jl. Raya Utama No. 123 &bull; Call Center / CS: (021) 555-1234</p>
            </div>
            <div class="manifest-title-box">
                <h3>MANIFES PERJALANAN</h3>
                <span>NOMOR JADWAL: #TRV-SCH-{{ $schedule->id }}</span>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>

        {{-- TRIP SUMMARY GRID --}}
        <div class="trip-meta-grid">
            <div class="trip-meta-item">
                <span class="trip-meta-label">Rute Perjalanan</span>
                <span class="trip-meta-val" style="color: #087f55;">{{ $routeLabel }}</span>
            </div>

            <div class="trip-meta-item">
                <span class="trip-meta-label">Waktu Keberangkatan</span>
                <span class="trip-meta-val">{{ $depDate }} &bull; {{ $depTime }} WIB</span>
            </div>

            <div class="trip-meta-item">
                <span class="trip-meta-label">Armada Minibus</span>
                <span class="trip-meta-val">{{ $vName }} ({{ $vPlate }})</span>
            </div>

            <div class="trip-meta-item">
                <span class="trip-meta-label">Pengemudi (Sopir)</span>
                <span class="trip-meta-val">{{ $driverName }} ({{ $driverPhone ?: 'No Kontak -' }})</span>
            </div>

            <div class="trip-meta-item">
                <span class="trip-meta-label">Total Penumpang Terisi</span>
                <span class="trip-meta-val">{{ $sortedPassengers->count() }} Orang / {{ $schedule->vehicle?->capacity ?? 10 }} Kursi</span>
            </div>

            <div class="trip-meta-item">
                <span class="trip-meta-label">Sisa Kursi Kosong</span>
                <span class="trip-meta-val" style="color: #0284c7;">{{ $schedule->available_seats }} Kursi</span>
            </div>
        </div>

        {{-- ROSTER TABLE --}}
        <table class="manifest-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th style="width: 70px; text-align: center;">Kursi</th>
                    <th>Nama Lengkap Penumpang</th>
                    <th>No. WhatsApp / HP</th>
                    <th>Kode Booking / Tiket</th>
                    <th>Titik Jemput / Catatan Khusus</th>
                    <th style="width: 110px; text-align: center;">Status Bayar</th>
                    <th style="width: 70px; text-align: center;">Boarding</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sortedPassengers as $index => $passenger)
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">
                            <span class="seat-cell-tag">{{ $passenger['seat_number'] }}</span>
                        </td>
                        <td>
                            <strong style="font-size: 13px;">{{ $passenger['passenger_name'] }}</strong>
                        </td>
                        <td>
                            {{ $passenger['phone'] }}
                        </td>
                        <td style="font-family: monospace; font-weight: 700; font-size: 11px;">
                            {{ $passenger['booking_code'] }}
                            @if($passenger['ticket_number'] !== '-')
                                <div style="color: #64748b; font-weight: normal;">{{ $passenger['ticket_number'] }}</div>
                            @endif
                        </td>
                        <td style="font-size: 11px; color: #475569;">
                            {{ $passenger['special_request'] }}
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 11px;">
                            @if($passenger['is_paid'])
                                <span style="color: #15803d;">LUNAS</span>
                            @else
                                <span style="color: #b45309;">BAYAR LOKET</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="boarding-checkbox-box"></span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                            Belum ada penumpang terdaftar untuk jadwal keberangkatan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN & PENGESAHAN --}}
        <div class="manifest-footer-signatures">
            <div class="signature-block">
                <div class="signature-line"></div>
                <span class="signature-name">{{ Auth::user()?->name ?? 'Petugas Loket' }}</span>
                <span class="signature-role">Petugas Operasional / Loket</span>
            </div>

            <div style="font-size: 11px; color: #64748b; text-align: center; max-width: 320px;">
                Pengemudi wajib memverifikasi nama penumpang dan mencentang kolom boarding sebelum kendaraan diberangkatkan dari pool/loket keberangkatan.
            </div>

            <div class="signature-block">
                <div class="signature-line"></div>
                <span class="signature-name">{{ $driverName }}</span>
                <span class="signature-role">Pengemudi (Sopir Armada)</span>
            </div>
        </div>

    </div>

</div>

{{-- TEXTAREA HIDDEN UNTUK COPY TEXT --}}
<textarea id="manifestRawText" style="display: none;">{{ $waMessageFull }}</textarea>

<script>
    function copyManifestText() {
        const text = document.getElementById('manifestRawText').value;
        navigator.clipboard.writeText(text).then(() => {
            alert('Teks manifes perjalanan berhasil disalin ke clipboard! Anda dapat langsung mem-paste ke WhatsApp atau pesan teks sopir.');
        }).catch(err => {
            alert('Gagal menyalin teks: ' + err);
        });
    }
</script>

@endsection
