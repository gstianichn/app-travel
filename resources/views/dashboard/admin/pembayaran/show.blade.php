@extends('layouts.admin')

@section('title', 'Faktur Pembayaran ' . $payment->order_id . ' - Admin Utama')

@section('page-title', 'Detail Tagihan & Pembayaran')

@section('page-subtitle', 'Rincian faktur resmi, verifikasi manual bukti transfer, dan log gateway gerbang pembayaran.')

@section('content')

<style>
    .payment-detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* TOP NAV ROW */
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
        color: #72817a;
    }

    .breadcrumb-trail a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-back {
        padding: 8px 16px;
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        color: #4b5852;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background: #f1f5f3;
        color: #17221d;
    }

    .btn-print {
        padding: 8px 18px;
        background: #087f55;
        border: none;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        color: white;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(8, 127, 85, 0.25);
        transition: background 0.2s ease;
    }

    .btn-print:hover {
        background: #066b47;
    }

    /* INVOICE CARD */
    .invoice-card {
        background: white;
        border: 1px solid #d4dfda;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .invoice-header {
        background: #17221d;
        color: white;
        padding: 24px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .invoice-header-brand h2 {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .invoice-header-brand p {
        margin: 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
    }

    .invoice-header-meta {
        text-align: right;
    }

    .invoice-code-tag {
        font-family: monospace;
        font-size: 16px;
        font-weight: 800;
        color: #34d399;
        background: rgba(52, 211, 153, 0.15);
        padding: 4px 12px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 4px;
    }

    .invoice-body {
        padding: 26px 30px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* GRID 2 COLUMNS */
    .grid-specs {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 24px;
    }

    .spec-card {
        background: #fbfdfc;
        border: 1px solid #e3eae6;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .spec-card-title {
        font-size: 12px;
        font-weight: 800;
        color: #17221d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
        border-bottom: 1px solid #edf2f0;
        padding-bottom: 8px;
    }

    .spec-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 13px;
        gap: 12px;
    }

    .spec-label {
        color: #64748b;
        font-size: 12px;
    }

    .spec-value {
        font-weight: 700;
        color: #1e293b;
        text-align: right;
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

    /* PASSENGER TABLE */
    .passenger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 6px;
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
    }

    .seat-badge {
        display: inline-flex;
        align-items: center;
        background: #e3f5ee;
        color: #087f55;
        font-weight: 800;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #c8ebd8;
    }

    /* MANUAL VERIFICATION PANEL */
    .verification-panel {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .verification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .verification-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .proof-preview-container {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .proof-image-box {
        width: 220px;
        height: 260px;
        border-radius: 10px;
        border: 1.5px solid #d4dfda;
        overflow: hidden;
        background: #f8faf9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .proof-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .proof-meta-box {
        flex: 1;
        min-width: 240px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        font-size: 13px;
    }

    .btn-approve {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        background: #087f55;
        color: white;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(8, 127, 85, 0.3);
        transition: background 0.2s ease;
    }

    .btn-approve:hover {
        background: #066b47;
    }

    .btn-reject {
        padding: 10px 18px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-reject:hover {
        background: #fca5a5;
    }

    /* GATEWAY LOG SECTION */
    .gateway-section {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .gateway-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .gateway-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .raw-json-box {
        background: #0f172a;
        color: #38bdf8;
        padding: 16px;
        border-radius: 10px;
        font-family: monospace;
        font-size: 12px;
        line-height: 1.5;
        max-height: 320px;
        overflow: auto;
        white-space: pre-wrap;
    }

    /* PRINT STYLES */
    @media print {
        .admin-sidebar,
        .admin-header,
        .detail-nav-row,
        .verification-panel,
        .gateway-section,
        .btn-print,
        .btn-back {
            display: none !important;
        }

        .payment-detail-page {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .invoice-card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="payment-detail-page">

    {{-- NAV ROW --}}
    <div class="detail-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('dashboard.admin.pembayaran.index') }}">Pembayaran</a>
            <span>&rsaquo;</span>
            <span>{{ $payment->order_id }}</span>
        </div>

        <div class="nav-actions">
            <a href="{{ route('dashboard.admin.pembayaran.index') }}" class="btn-back">
                &larr; Daftar Pembayaran
            </a>
            @if($payment->booking)
                <a href="{{ route('dashboard.admin.pemesanan.show', $payment->booking->id) }}" class="btn-back">
                    Lihat E-Ticket</a>
            @endif
            <button type="button" class="btn-print" onclick="window.print()">
                Cetak Faktur (Print / PDF)
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div style="padding: 12px 18px; border-radius: 10px; font-size: 13px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 12px 18px; border-radius: 10px; font-size: 13px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================
         1. INVOICE RESMI TRAVEL
    ========================================== --}}
    <div class="invoice-card">
        <div class="invoice-header">
            <div class="invoice-header-brand">
                <h2>ANTIGRAVITY TRAVEL</h2>
                <p>Faktur Resmi Pembayaran & Konfirmasi Tiket Travel Antar Kota</p>
            </div>
            <div class="invoice-header-meta">
                <span class="invoice-code-tag">{{ $payment->order_id }}</span>
                <div style="font-size: 11px; opacity: 0.8;">
                    Dibuat: {{ $payment->created_at ? $payment->created_at->format('d M Y, H:i') : '-' }} WIB
                </div>
            </div>
        </div>

        <div class="invoice-body">

            {{-- DUA KOLOM: PELANGGAN & PEMBAYARAN --}}
            <div class="grid-specs">
                {{-- Kiri: Info Perjalanan & Pelanggan --}}
                <div class="spec-card">
                    <span class="spec-card-title">Informasi Pemesan & Rute</span>

                    <div class="spec-row">
                        <span class="spec-label">Nama Lengkap</span>
                        <span class="spec-value">{{ $payment->booking?->customerName() ?? 'Pelanggan' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Nomor WhatsApp / HP</span>
                        <span class="spec-value">{{ $payment->booking?->customerPhone() ?? '-' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Kode Booking</span>
                        <span class="spec-value" style="color: #087f55; font-family: monospace;">
                            {{ $payment->booking?->booking_code ?? '-' }}
                        </span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Rute Perjalanan</span>
                        <span class="spec-value" style="color: #0284c7;">
                            {{ $payment->booking?->schedule?->route ? ($payment->booking->schedule->route->origin?->name . ' ke ' . $payment->booking->schedule->route->destination?->name) : 'Rute Travel' }}
                        </span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Jadwal Keberangkatan</span>
                        <span class="spec-value">
                            {{ $payment->booking?->schedule?->departure_date ? $payment->booking->schedule->departure_date->format('d/m/Y') : '-' }}
                            ({{ $payment->booking?->schedule?->departure_time ?? '-' }} WIB)
                        </span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Armada Minibus</span>
                        <span class="spec-value">
                            {{ $payment->booking?->schedule?->vehicle?->name ?? 'Minibus' }} 
                            [{{ $payment->booking?->schedule?->vehicle?->plate_number ?? '-' }}]
                        </span>
                    </div>
                </div>

                {{-- Kanan: Status & Rincian Pembayaran --}}
                <div class="spec-card">
                    <span class="spec-card-title">Rincian Tagihan & Status</span>

                    <div class="spec-row">
                        <span class="spec-label">Status Pembayaran</span>
                        <span class="spec-value">
                            <span class="badge-status {{ $payment->statusBadgeClass() }}">
                                {{ $payment->statusLabel() }}
                            </span>
                        </span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Metode Pembayaran</span>
                        <span class="spec-value">{{ $payment->methodLabel() }}</span>
                    </div>

                    @if($payment->paid_at)
                        <div class="spec-row">
                            <span class="spec-label">Waktu Pelunasan</span>
                            <span class="spec-value" style="color: #15803d;">
                                {{ $payment->paid_at->format('d/m/Y H:i') }} WIB
                            </span>
                        </div>
                    @endif

                    @if($payment->verified_by)
                        <div class="spec-row">
                            <span class="spec-label">Diverifikasi Oleh</span>
                            <span class="spec-value">
                                {{ $payment->verifiedBy?->name ?? 'Admin Loket' }}
                                ({{ $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i') : '' }})
                            </span>
                        </div>
                    @endif

                    <div class="spec-row" style="border-top: 1px solid #edf2f0; padding-top: 8px; margin-top: 4px;">
                        <span class="spec-label" style="font-weight: 700; color: #17221d;">Total Biaya Tagihan</span>
                        <span class="spec-value" style="font-size: 18px; color: #087f55;">
                            {{ $payment->formattedAmount() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- DAFTAR PENUMPANG & ALOKASI KURSI --}}
            <div>
                <span class="spec-card-title" style="margin-bottom: 8px;">Rincian Penumpang & Kursi Terdaftar</span>
                <table class="passenger-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 90px;">Kursi</th>
                            <th>Nama Penumpang</th>
                            <th>Nomor Telepon</th>
                            <th>Catatan / Titik Jemput</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($payment->booking && $payment->booking->passengers->count() > 0)
                            @foreach($payment->booking->passengers as $idx => $psg)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="seat-badge">
                                            {{ $psg->seat?->seat_number ?? '-' }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $psg->name }}</strong></td>
                                    <td>{{ $psg->phone ?? '-' }}</td>
                                    <td style="color: #64748b;">{{ $psg->special_request ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8;">
                                    Data penumpang tidak tersedia.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- =========================================
         2. VERIFIKASI MANUAL BUKTI TRANSFER
    ========================================== --}}
    @if($payment->isManualTransfer() || $payment->hasProof())
        <div class="verification-panel">
            <div class="verification-header">
                <h3>Bukti Pembayaran Transfer Bank Langsung</h3>
                <div>
                    @if(in_array(strtolower($payment->status), ['success', 'paid', 'lunas']))
                        <span style="font-size: 12px; font-weight: 700; color: #15803d; background: #dcfce7; padding: 4px 10px; border-radius: 6px;">
                            Pembayaran Telah Disetujui
                        </span>
                    @elseif(in_array(strtolower($payment->status), ['failed', 'gagal', 'ditolak']))
                        <span style="font-size: 12px; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 4px 10px; border-radius: 6px;">
                            Pembayaran Ditolak
                        </span>
                    @else
                        <span style="font-size: 12px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 4px 10px; border-radius: 6px;">
                            ⏳ Menunggu Tindakan Verifikasi
                        </span>
                    @endif
                </div>
            </div>

            <div class="proof-preview-container">
                {{-- Foto Bukti --}}
                <div class="proof-image-box">
                    @if($payment->hasProof())
                        <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti Transfer">
                    @else
                        <span style="font-size: 12px; color: #94a3b8; text-align: center; padding: 10px;">
                            Belum ada foto bukti transfer
                        </span>
                    @endif
                </div>

                {{-- Detail Rekening & Form Verifikasi --}}
                <div class="proof-meta-box">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; flex-direction: column; gap: 6px;">
                        <div>Bank Pengirim: <strong>{{ $payment->bank_name ?: '-' }}</strong></div>
                        <div>Rekening Pengirim: <strong>{{ $payment->account_number ?: '-' }}</strong></div>
                        <div>Atas Nama: <strong>{{ $payment->account_name ?: '-' }}</strong></div>
                        @if($payment->notes)
                            <div style="color: #475569; font-size: 12px; margin-top: 4px;">
                                Catatan: <em>{{ $payment->notes }}</em>
                            </div>
                        @endif
                    </div>

                    {{-- TOMBOL TINDAKAN VERIFIKASI (APPROVE / REJECT) --}}
                    @if(in_array(strtolower($payment->status), ['pending', 'menunggu']))
                        <div style="display: flex; gap: 10px; margin-top: 6px;">
                            {{-- Form Approve --}}
                            <form action="{{ route('dashboard.admin.pembayaran.verify', $payment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini dan menerbitkan tiket?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-approve">
                                    Setujui (Lunas & Terbitkan Tiket)
                                </button>
                            </form>

                            {{-- Form Reject Button triggers prompt --}}
                            <button type="button" class="btn-reject" onclick="showRejectInput()">
                                Tolak Bukti
                            </button>
                        </div>

                        {{-- Reject Form Hidden by default --}}
                        <form id="rejectFormBox" action="{{ route('dashboard.admin.pembayaran.verify', $payment->id) }}" method="POST" style="display: none; flex-direction: column; gap: 8px; margin-top: 10px; background: white; padding: 12px; border-radius: 8px; border: 1px solid #fca5a5;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="reject">
                            <label style="font-size: 12px; font-weight: 700; color: #b91c1c;">Alasan Penolakan Pembayaran:</label>
                            <input type="text" name="rejection_reason" class="filter-input" placeholder="Contoh: Bukti buram, mutasi rekening belum masuk.." required>
                            <div style="display: flex; gap: 8px;">
                                <button type="submit" class="btn-reject" style="width: auto;">Konfirmasi Penolakan</button>
                                <button type="button" class="btn-back" onclick="hideRejectInput()">Batal</button>
                            </div>
                        </form>
                    @endif

                    {{-- Form Upload Bukti Baru --}}
                    <div style="margin-top: 8px;">
                        <button type="button" class="btn-back" onclick="toggleUploadProofForm()" style="font-size: 11px;">
                            + Unggah / Ganti Bukti Transfer
                        </button>
                        <form id="uploadProofForm" action="{{ route('dashboard.admin.pembayaran.upload-proof', $payment->id) }}" method="POST" enctype="multipart/form-data" style="display: none; flex-direction: column; gap: 8px; margin-top: 10px; background: white; padding: 14px; border-radius: 10px; border: 1px solid #d4dfda;">
                            @csrf
                            <label style="font-size: 11px; font-weight: 700; color: #334155;">Pilih File Gambar Bukti (JPG/PNG):</label>
                            <input type="file" name="proof_image" accept="image/*,.pdf" class="filter-input">
                            <input type="text" name="bank_name" placeholder="Nama Bank (misal BCA)" value="{{ $payment->bank_name }}" class="filter-input">
                            <input type="text" name="account_name" placeholder="Atas Nama Pemilik Rekening" value="{{ $payment->account_name }}" class="filter-input">
                            <input type="text" name="account_number" placeholder="Nomor Rekening" value="{{ $payment->account_number }}" class="filter-input">
                            <button type="submit" class="btn-approve" style="align-self: flex-start; padding: 8px 16px;">
                                Simpan Bukti Transfer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================
         3. STATUS PAYMENT GATEWAY (MIDTRANS/XENDIT)
    ========================================== --}}
    <div class="gateway-section">
        <div class="gateway-header">
            <div>
                <h3>Status Payment Gateway (Midtrans / Xendit / Tripay)</h3>
                <span style="font-size: 12px; color: #64748b;">
                    Log pembaruan otomatis dari webhook penyedia gerbang pembayaran digital
                </span>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                {{-- Form Sinkronisasi / Simulasi Callback Gateway --}}
                <form action="{{ route('dashboard.admin.pembayaran.sync-gateway', $payment->id) }}" method="POST" style="display: flex; gap: 6px; align-items: center;">
                    @csrf
                    <select name="gateway_provider" style="height: 32px; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; background: white;">
                        <option value="midtrans" {{ $payment->gateway_provider === 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        <option value="xendit" {{ $payment->gateway_provider === 'xendit' ? 'selected' : '' }}>Xendit</option>
                    </select>

                    <select name="gateway_status" style="height: 32px; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; background: white;">
                        <option value="settlement">Settlement (Lunas)</option>
                        <option value="pending">Pending</option>
                        <option value="expire">Expire (Kedaluwarsa)</option>
                        <option value="deny">Deny (Ditolak)</option>
                    </select>

                    <button type="submit" class="btn-back" style="padding: 6px 12px; font-size: 11px; background: #0284c7; color: white; border: none; font-weight: 700;">
                        Sinkronkan Gateway
                    </button>
                </form>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; font-size: 13px;">
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                <span style="font-size: 11px; color: #64748b; display: block;">Gateway Provider</span>
                <strong style="font-size: 14px; color: #0284c7; text-transform: uppercase;">
                    {{ $payment->gateway_provider ?: 'Belum Terhubung' }}
                </strong>
            </div>

            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                <span style="font-size: 11px; color: #64748b; display: block;">Nomor Referensi Gateway</span>
                <strong style="font-size: 13px; font-family: monospace; color: #0f172a;">
                    {{ $payment->transaction_id ?: '-' }}
                </strong>
            </div>

            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                <span style="font-size: 11px; color: #64748b; display: block;">Status Notifikasi Gateway</span>
                <strong style="font-size: 13px; color: {{ in_array($payment->gateway_status, ['settlement', 'capture']) ? '#15803d' : '#d97706' }}; text-transform: uppercase;">
                    {{ $payment->gateway_status ?: 'Tidak ada respon' }}
                </strong>
            </div>
        </div>

        {{-- RAW RESPONSE JSON VIEWER --}}
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: #475569;">
                    Raw Webhook Callback Payload (JSON)
                </span>
                @if($payment->raw_response)
                    <button type="button" class="btn-back" onclick="copyDetailJson()" style="font-size: 11px;">
                        Salin JSON
                    </button>
                @endif
            </div>

            <pre id="detailJsonViewer" class="raw-json-box">{{ $payment->raw_response ? json_encode($payment->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '// Belum ada payload webhook yang diterima dari payment gateway.' }}</pre>
        </div>
    </div>

</div>

<script>
    function showRejectInput() {
        document.getElementById('rejectFormBox').style.display = 'flex';
    }

    function hideRejectInput() {
        document.getElementById('rejectFormBox').style.display = 'none';
    }

    function toggleUploadProofForm() {
        const f = document.getElementById('uploadProofForm');
        f.style.display = f.style.display === 'none' ? 'flex' : 'none';
    }

    function copyDetailJson() {
        const text = document.getElementById('detailJsonViewer').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Payload JSON webhook berhasil disalin!');
        });
    }
</script>

@endsection
