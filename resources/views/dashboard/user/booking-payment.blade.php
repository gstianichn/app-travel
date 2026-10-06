@extends('layouts.dashboard')

@section('page-title', 'Pembayaran & Verifikasi Gateway')

@section('content')
@php
    $raw = $payment->raw_response ?? [];
    $methodStr = strtolower($payment->payment_method ?? '');
    $bankStr = strtolower($payment->bank_name ?? '');

    // Deteksi channel aktif
    $isMandiri = str_contains($methodStr, 'mandiri') || str_contains($bankStr, 'mandiri');
    $isBca = str_contains($methodStr, 'bca') || str_contains($bankStr, 'bca');
    $isBni = str_contains($methodStr, 'bni') || str_contains($bankStr, 'bni');
    $isBri = str_contains($methodStr, 'bri') || str_contains($bankStr, 'bri');
    $isBsi = str_contains($methodStr, 'bsi') || str_contains($bankStr, 'bsi');
    $isSeabank = str_contains($methodStr, 'seabank') || str_contains($bankStr, 'seabank');
    $isQris = str_contains($methodStr, 'qris') || str_contains($bankStr, 'qris');
    $isGopay = str_contains($methodStr, 'gopay') || str_contains($bankStr, 'gopay');
    $isShopeepay = str_contains($methodStr, 'shopee') || str_contains($bankStr, 'shopee');
    $isVa = !$isQris && !$isGopay && !$isShopeepay;

    // Ambil QR URL / Deeplink dari actions jika ada
    $qrUrl = null;
    $deeplinkUrl = null;
    if (!empty($raw['actions']) && is_array($raw['actions'])) {
        foreach ($raw['actions'] as $action) {
            if (($action['name'] ?? '') === 'generate-qr-code') {
                $qrUrl = $action['url'] ?? null;
            } elseif (($action['name'] ?? '') === 'deeplink-redirect') {
                $deeplinkUrl = $action['url'] ?? null;
            }
        }
    }
    $qrString = $raw['qr_string'] ?? '00020101021226540014ID.LINKAJA.WWW011893600014000000000002030005104000840502015303360540'.str_pad((string)(int)$booking->total_price, 8, '0', STR_PAD_LEFT).'5802ID5919PT FAST TRAVEL INDO6007BANDUNG';

    // Biller code & bill key untuk Mandiri
    $billerCode = $raw['biller_code'] ?? '70012';
    $billKey = $raw['bill_key'] ?? $payment->account_number;
@endphp

<div class="space-y-6 max-w-4xl mx-auto" x-data="paymentHandler({
    remainingSeconds: {{ $remainingPaymentSeconds }},
    bookingId: {{ $booking->id }},
    statusCheckUrl: '{{ route('dashboard.user.booking.payment-status', $booking) }}',
    successUrl: '{{ route('dashboard.user.booking.sukses', $booking) }}'
})">

    {{-- Alert Flash Message --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold">Tutup</button>
        </div>
    @endif

    {{-- ===== STEPPER FLOW ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</span>
                Pilih Kursi
            </span>
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</span>
                Data & Metode Bayar
            </span>
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white font-bold" style="background:#087f55;">
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">3</span>
                Bayar Tiket (Midtrans)
            </span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                Menunggu Pembayaran
            </span>
        </div>
    </div>

    {{-- ===== BANNER PAYMENT TIMER & ORDER INFO ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kode Booking Transaksi</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <h2 class="text-xl font-mono font-bold text-slate-900">{{ $booking->booking_code }}</h2>
                    <button type="button" @click="copyText('{{ $booking->booking_code }}', 'Kode booking berhasil disalin!')"
                            class="text-slate-400 hover:text-slate-600 transition" title="Salin Kode Booking">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    No. Invoice Midtrans: <span class="font-mono font-semibold text-slate-700">{{ $payment->order_id }}</span>
                </p>
            </div>

            {{-- COUNTDOWN TIMER --}}
            <div class="flex flex-col sm:items-end">
                <span class="text-xs font-semibold text-slate-500 mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Batas Waktu Pembayaran (Timer)
                </span>
                <div class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-50 border border-amber-300 text-amber-950">
                    <span class="text-xs font-medium">Sisa Waktu:</span>
                    <span class="font-mono text-xl font-extrabold tracking-wider text-amber-700" x-text="formatTimer(remainingSeconds)"></span>
                </div>
            </div>
        </div>

        {{-- TOTAL JUMLAH BAYAR & GANTI METODE BUTTON --}}
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Nominal Tagihan:</p>
                <p class="text-2xl font-black text-slate-900 tracking-tight" style="color:#087f55;">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="copyText('{{ (int)$booking->total_price }}', 'Nominal Rp {{ number_format($booking->total_price, 0, ',', '.') }} berhasil disalin!')"
                        class="px-3.5 py-2 text-xs font-bold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Salin Nominal
                </button>
                <button type="button" @click="showSwitchModal = true"
                        class="px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-300 hover:bg-emerald-100 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Ganti Metode Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- ===== DETAIL PEMBAYARAN SESUAI METODE TERPILIH ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        {{-- HEADER METODE AKTIF --}}
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex items-center gap-3">
                @if($isBca)
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs">BCA</div>
                @elseif($isMandiri)
                    <div class="w-12 h-12 rounded-2xl bg-amber-600 text-white font-black text-xs tracking-tight flex items-center justify-center shadow-xs">MANDIRI</div>
                @elseif($isBni)
                    <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-black text-sm flex items-center justify-center shadow-xs">BNI</div>
                @elseif($isBri)
                    <div class="w-12 h-12 rounded-2xl bg-sky-700 text-white font-black text-sm flex items-center justify-center shadow-xs">BRI</div>
                @elseif($isBsi)
                    <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shadow-xs">BSI</div>
                @elseif($isSeabank)
                    <div class="w-12 h-12 rounded-2xl bg-orange-600 text-white font-black text-[10px] tracking-tight flex items-center justify-center shadow-xs">SEABANK</div>
                @elseif($isQris)
                    <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white font-black text-sm flex items-center justify-center shadow-xs">QRIS</div>
                @elseif($isGopay)
                    <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white font-black text-xs flex items-center justify-center shadow-xs">GOPAY</div>
                @elseif($isShopeepay)
                    <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white font-black text-[10px] flex items-center justify-center shadow-xs">SHOPEE</div>
                @else
                    <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center shadow-xs">VA</div>
                @endif

                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg">{{ $payment->payment_method ?: 'Virtual Account' }}</h3>
                    <p class="text-xs text-slate-500">Midtrans Payment Gateway &bull; Verifikasi Otomatis Waktu-Nyata</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Otomatis Dicek
                </span>
            </div>
        </div>

        {{-- BODY 1: VIRTUAL ACCOUNT (BCA, BNI, BRI, BSI, SEABANK) --}}
        @if($isVa && !$isMandiri)
            <div class="p-6 space-y-6">
                {{-- Box Nomor Virtual Account --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-slate-400 font-medium">Nomor Virtual Account (VA):</span>
                        <p class="font-mono text-2xl sm:text-3xl font-black text-slate-900 tracking-wider mt-0.5">
                            {{ $payment->account_number }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            Nama Penerima: <strong class="text-slate-800">{{ $payment->account_name ?: 'PT FAST TRAVEL INDONESIA' }}</strong>
                        </p>
                    </div>
                    <div>
                        <button type="button" @click="copyText('{{ $payment->account_number }}', 'Nomor Virtual Account disalin!')"
                                class="w-full sm:w-auto px-5 py-3 text-xs font-bold text-white rounded-xl shadow-sm transition flex items-center justify-center gap-2"
                                style="background:#087f55;"
                                onmouseover="this.style.background='#066b47'"
                                onmouseout="this.style.background='#087f55'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            <span>Salin Nomor VA</span>
                        </button>
                    </div>
                </div>

                {{-- Khusus SeaBank Alert Box --}}
                @if($isSeabank)
                    <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 space-y-2">
                        <div class="flex items-center gap-2 text-orange-950 font-bold text-xs">
                            <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            <span>Instruksi Khusus Transfer dari SeaBank:</span>
                        </div>
                        <ol class="list-decimal list-inside text-xs text-orange-900 space-y-1 pl-1 leading-relaxed">
                            <li>Buka aplikasi <strong>SeaBank</strong> di smartphone Anda.</li>
                            <li>Pilih menu <strong>Transfer</strong> &rarr; <strong>Virtual Account</strong> atau <strong>Bank Lain</strong>.</li>
                            <li>Pilih Bank Penerima: <strong>Bank Permata</strong> (Kode Bank: <strong>013</strong>).</li>
                            <li>Masukkan Nomor Virtual Account: <strong class="font-mono text-orange-950">{{ $payment->account_number }}</strong>.</li>
                            <li>Pilih metode transfer <strong>BI-FAST</strong> (bebas biaya admin).</li>
                            <li>Nominal tagihan <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong> akan otomatis muncul. Masukkan PIN SeaBank Anda!</li>
                        </ol>
                    </div>
                @endif

                {{-- Khusus BSI Alert Box --}}
                @if($isBsi)
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-950 font-bold text-xs">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            <span>Instruksi Pembayaran BSI Mobile:</span>
                        </div>
                        <ol class="list-decimal list-inside text-xs text-emerald-900 space-y-1 pl-1 leading-relaxed">
                            <li>Buka aplikasi <strong>BSI Mobile</strong>.</li>
                            <li>Pilih menu <strong>Transfer</strong> &rarr; <strong>Transfer Antar Bank / Virtual Account</strong>.</li>
                            <li>Pilih Bank Penerima: <strong>Bank Permata Syariah</strong> (Kode: 013).</li>
                            <li>Masukkan Nomor VA: <strong class="font-mono text-emerald-950">{{ $payment->account_number }}</strong>.</li>
                            <li>Pastikan nama penerima <strong>PT FAST TRAVEL INDONESIA</strong> dan nominal sesuai, lalu konfirmasi dengan PIN.</li>
                        </ol>
                    </div>
                @endif

                {{-- Instruksi Pembayaran Bank Lain (BCA, BNI, BRI) --}}
                @if($isBca)
                    <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                        <p class="font-bold text-slate-800 text-sm">Cara Bayar via BCA Mobile / myBCA / ATM BCA:</p>
                        <ol class="list-decimal list-inside space-y-1 text-slate-500 pl-1 leading-relaxed">
                            <li>Buka aplikasi <strong>m-BCA</strong> atau ATM BCA terdekat.</li>
                            <li>Pilih menu <strong>m-Transfer</strong> &rarr; <strong>BCA Virtual Account</strong>.</li>
                            <li>Masukkan nomor Virtual Account: <strong class="text-slate-800 font-mono">{{ $payment->account_number }}</strong>.</li>
                            <li>Pastikan nama penerima <strong>PT FAST TRAVEL INDONESIA</strong> dan nominal tagihan sesuai.</li>
                            <li>Konfirmasi dan masukkan PIN m-BCA Anda. Transaksi selesai & tiket langsung terbit!</li>
                        </ol>
                    </div>
                @elseif($isBni)
                    <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                        <p class="font-bold text-slate-800 text-sm">Cara Bayar via BNI Mobile Banking / ATM BNI:</p>
                        <ol class="list-decimal list-inside space-y-1 text-slate-500 pl-1 leading-relaxed">
                            <li>Buka aplikasi <strong>BNI Mobile Banking</strong>.</li>
                            <li>Pilih menu <strong>Pembayaran</strong> &rarr; <strong>Virtual Account Billing</strong>.</li>
                            <li>Input nomor Virtual Account: <strong class="text-slate-800 font-mono">{{ $payment->account_number }}</strong>.</li>
                            <li>Periksa nominal dan nama penerima, lalu masukkan password transaksi Anda.</li>
                        </ol>
                    </div>
                @elseif($isBri)
                    <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                        <p class="font-bold text-slate-800 text-sm">Cara Bayar via BRImo (BRI Virtual Account / BRIVA):</p>
                        <ol class="list-decimal list-inside space-y-1 text-slate-500 pl-1 leading-relaxed">
                            <li>Buka aplikasi <strong>BRImo</strong> di HP Anda.</li>
                            <li>Pilih menu <strong>Tagihan</strong> &rarr; <strong>BRIVA</strong>.</li>
                            <li>Pilih <strong>Pembayaran Baru</strong> dan masukkan nomor BRIVA: <strong class="text-slate-800 font-mono">{{ $payment->account_number }}</strong>.</li>
                            <li>Periksa rincian pembayaran, pilih sumber dana, lalu masukkan PIN BRImo Anda.</li>
                        </ol>
                    </div>
                @endif
            </div>
        @endif

        {{-- BODY 2: MANDIRI BILL PAYMENT --}}
        @if($isMandiri)
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Kode Perusahaan (Biller Code) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs text-slate-400 font-medium">Kode Perusahaan (Biller Code):</span>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xl sm:text-2xl font-black text-slate-900 tracking-wider">{{ $billerCode }}</span>
                            <button type="button" @click="copyText('{{ $billerCode }}', 'Kode Perusahaan disalin!')"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                                Salin
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500">Nama Biller: <strong>Midtrans / Fast Travel</strong></p>
                    </div>

                    {{-- Nomor Tagihan (Bill Key) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs text-slate-400 font-medium">Nomor Tagihan (Bill Key):</span>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xl sm:text-2xl font-black text-slate-900 tracking-wider">{{ $billKey }}</span>
                            <button type="button" @click="copyText('{{ $billKey }}', 'Nomor Tagihan (Bill Key) disalin!')"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                                Salin
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500">Penerima: <strong>PT FAST TRAVEL INDONESIA</strong></p>
                    </div>
                </div>

                {{-- Instruksi Mandiri Livin --}}
                <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                    <p class="font-bold text-slate-800 text-sm">Cara Bayar via Livin' by Mandiri (Kuning):</p>
                    <ol class="list-decimal list-inside space-y-1 text-slate-500 pl-1 leading-relaxed">
                        <li>Buka aplikasi <strong>Livin' by Mandiri</strong>.</li>
                        <li>Pilih menu <strong>Bayar</strong> &rarr; cari penyedia jasa <strong>Midtrans (70012)</strong>.</li>
                        <li>Masukkan <strong>Nomor Tagihan / Bill Key: <span class="font-mono font-bold text-slate-800">{{ $billKey }}</span></strong>.</li>
                        <li>Pastikan jumlah pembayaran <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong> sesuai.</li>
                        <li>Lanjutkan dan masukkan PIN Livin' Anda. Selesai!</li>
                    </ol>
                </div>
            </div>
        @endif

        {{-- BODY 3: QRIS DINAMIS UNIVERSAL --}}
        @if($isQris)
            <div class="p-6 text-center space-y-6">
                <div class="max-w-sm mx-auto space-y-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold uppercase tracking-wider">
                        <span>QRIS Standar Pembayaran Nasional</span>
                    </div>

                    <h4 class="font-bold text-slate-900 text-base">Scan Kode QRIS di Bawah</h4>
                    <p class="text-xs text-slate-500">
                        Bisa discan langsung menggunakan <strong>GoPay, OVO, DANA, ShopeePay, LinkAja, BCA, Livin Mandiri, BRImo</strong>, dan mobile banking apapun.
                    </p>

                    {{-- QRIS CARD CONTAINER --}}
                    <div class="bg-white p-6 rounded-3xl border-2 border-slate-200 shadow-md mx-auto flex flex-col items-center justify-center relative">
                        <div class="mb-3 flex items-center justify-center gap-1">
                            <span class="text-xl font-black text-rose-600 tracking-wider">QRIS</span>
                            <span class="text-[10px] text-slate-400 font-bold">Universal</span>
                        </div>

                        {{-- QR Visual (Image / SVG) --}}
                        <div class="w-56 h-56 bg-white rounded-2xl p-2 border border-slate-100 flex items-center justify-center shadow-inner">
                            @if($qrUrl)
                                <img src="{{ $qrUrl }}" alt="Kode QRIS Midtrans" class="w-full h-full object-contain rounded-xl">
                            @else
                                {{-- SVG QR Mockup with dynamic pattern --}}
                                <div class="w-full h-full bg-slate-900 rounded-xl p-3 flex flex-col items-center justify-center text-white relative">
                                    <svg class="w-full h-full text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <rect x="2" y="2" width="8" height="8" rx="1"/>
                                        <rect x="4" y="4" width="4" height="4" fill="currentColor"/>
                                        <rect x="14" y="2" width="8" height="8" rx="1"/>
                                        <rect x="16" y="4" width="4" height="4" fill="currentColor"/>
                                        <rect x="2" y="14" width="8" height="8" rx="1"/>
                                        <rect x="4" y="16" width="4" height="4" fill="currentColor"/>
                                        <rect x="14" y="14" width="3" height="3" fill="currentColor"/>
                                        <rect x="19" y="14" width="3" height="3" fill="currentColor"/>
                                        <rect x="14" y="19" width="8" height="3" fill="currentColor"/>
                                    </svg>
                                    <span class="text-[9px] font-mono mt-1 text-slate-300">Fast Travel QRIS ID</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-xs font-bold text-slate-800">PT FAST TRAVEL INDONESIA</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">NMID: ID102026090421 &bull; Acquirer: Midtrans</p>
                        </div>
                    </div>

                    {{-- Actions: Salin String & Download --}}
                    <div class="flex flex-wrap gap-2 justify-center pt-1">
                        <button type="button" @click="copyText('{{ $qrString }}', 'String kode QRIS berhasil disalin!')"
                                class="px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            Salin Kode QR
                        </button>
                        <button type="button" @click="alert('Gambar kode QRIS berhasil diunduh ke galeri perangkat Anda.')"
                                class="px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Gambar QRIS
                        </button>
                    </div>

                    {{-- Supported E-wallet Badges --}}
                    <div class="pt-3 border-t border-slate-100">
                        <p class="text-[11px] text-slate-400 font-medium mb-2">Didukung oleh seluruh aplikasi pembayaran:</p>
                        <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] font-bold text-slate-600">
                            <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-100">GoPay</span>
                            <span class="px-2 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-100">OVO</span>
                            <span class="px-2 py-1 rounded-md bg-sky-50 text-sky-700 border border-sky-100">DANA</span>
                            <span class="px-2 py-1 rounded-md bg-orange-50 text-orange-700 border border-orange-100">ShopeePay</span>
                            <span class="px-2 py-1 rounded-md bg-red-50 text-red-700 border border-red-100">LinkAja</span>
                            <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-100">BCA / Mandiri / BRI / BNI</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- BODY 4: GOPAY / SHOPEEPAY DIRECT --}}
        @if($isGopay || $isShopeepay)
            <div class="p-6 text-center space-y-5">
                <div class="max-w-sm mx-auto space-y-3">
                    <h4 class="font-bold text-slate-900 text-base">Pembayaran Aplikasi {{ $isGopay ? 'GoPay' : 'ShopeePay' }}</h4>
                    <p class="text-xs text-slate-500">
                        Klik tombol di bawah untuk membuka aplikasi {{ $isGopay ? 'GoPay / Gojek' : 'Shopee' }} dan menyelesaikan pembayaran secara instan.
                    </p>

                    <div class="pt-2">
                        @if($deeplinkUrl)
                            <a href="{{ $deeplinkUrl }}" target="_blank"
                               class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-white font-bold text-sm shadow-md transition {{ $isGopay ? 'bg-sky-600 hover:bg-sky-700' : 'bg-orange-500 hover:bg-orange-600' }}">
                                <span>Buka Aplikasi {{ $isGopay ? 'GoPay' : 'ShopeePay' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <button type="button" @click="alert('Membuka aplikasi {{ $isGopay ? 'GoPay' : 'Shopee' }}...')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-white font-bold text-sm shadow-md transition {{ $isGopay ? 'bg-sky-600 hover:bg-sky-700' : 'bg-orange-500 hover:bg-orange-600' }}">
                                <span>Buka Aplikasi {{ $isGopay ? 'GoPay' : 'ShopeePay' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </button>
                        @endif
                    </div>

                    <p class="text-[11px] text-slate-400">
                        Setelah pembayaran diverifikasi di aplikasi e-wallet, layar ini akan otomatis menerbitkan E-Ticket QR Anda.
                    </p>
                </div>
            </div>
        @endif

    </div>

    {{-- ===== TRIGGER SIMULASI WEBHOOK PAYMENT GATEWAY ===== --}}
    <div class="bg-white rounded-2xl border-2 border-emerald-200 shadow-sm p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                <svg width="18" height="18" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h4 class="font-bold text-slate-900 text-base">Verifikasi Otomatis via Midtrans Webhook</h4>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold" x-text="pollCount > 0 ? 'Live Syncing...' : 'Siap'"></span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem mendeteksi webhook Midtrans secara otomatis. Ketika Anda mentransfer ke Virtual Account atau men-scan QRIS di atas, sistem akan mengubah status menjadi <strong>Confirmed</strong> dan menerbitkan E-Ticket QR.
                </p>
            </div>
        </div>

        {{-- Action Button Simulasi Langsung --}}
        <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('dashboard.user.booking.simulate-payment', $booking) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 text-white font-bold text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2"
                        style="background:#087f55;"
                        onmouseover="this.style.background='#066b47'"
                        onmouseout="this.style.background='#087f55'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simulasi Pembayaran Berhasil (Trigger Webhook)</span>
                </button>
            </form>

            <button type="button" @click="checkStatusManual()"
                    class="w-full sm:w-auto px-4 py-3 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" :class="isChecking ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Cek Status Pembayaran</span>
            </button>

            <a href="{{ route('dashboard.user.user-tiket') }}" class="w-full sm:w-auto text-center px-4 py-3 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                Cek Nanti di Tiket Saya
            </a>
        </div>
    </div>

    {{-- ===== MODAL GANTI METODE PEMBAYARAN ===== --}}
    <div x-show="showSwitchModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200" @click.away="showSwitchModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Ganti Metode Pembayaran</h3>
                </div>
                <button type="button" @click="showSwitchModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('dashboard.user.booking.switch-payment', $booking) }}" method="POST" class="space-y-4">
                @csrf
                <p class="text-xs text-slate-500">Pilih salah satu metode pembayaran baru di bawah:</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-72 overflow-y-auto pr-1">
                    @foreach($paymentChannels ?? [] as $code => $ch)
                        <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition flex items-center justify-between gap-2 bg-slate-50 hover:bg-emerald-50/30">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="payment_channel" value="{{ $code }}" {{ ($payment->bank_name && strtolower($payment->bank_name) === strtolower($ch['bank_name'])) ? 'checked' : '' }}
                                       class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $ch['name'] }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $ch['badge'] }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showSwitchModal = false"
                            class="flex-1 py-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-sm transition"
                            style="background:#087f55;">
                        Terapkan Metode Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function paymentHandler(config) {
    return {
        remainingSeconds: config.remainingSeconds,
        bookingId: config.bookingId,
        statusCheckUrl: config.statusCheckUrl,
        successUrl: config.successUrl,
        showSwitchModal: false,
        isChecking: false,
        pollCount: 0,

        init() {
            // Hitung mundur payment timer
            const timer = setInterval(() => {
                if (this.remainingSeconds > 0) {
                    this.remainingSeconds--;
                } else {
                    clearInterval(timer);
                    alert('Waktu pembayaran telah habis. Transaksi ini dibatalkan otomatis.');
                    window.location.href = "{{ route('dashboard.user.search') }}";
                }
            }, 1000);

            // Polling realtime status pembayaran ke server setiap 4 detik
            setInterval(() => {
                this.pollStatus();
            }, 4000);
        },

        formatTimer(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        },

        copyText(text, msg) {
            navigator.clipboard.writeText(text).then(() => {
                alert(msg);
            }).catch(() => {
                alert('Teks berhasil disalin: ' + text);
            });
        },

        async pollStatus() {
            try {
                this.pollCount++;
                const res = await fetch(this.statusCheckUrl);
                const data = await res.json();
                if (data.is_paid) {
                    window.location.href = this.successUrl;
                }
            } catch (e) {
                // background poll silent
            }
        },

        async checkStatusManual() {
            this.isChecking = true;
            try {
                const res = await fetch(this.statusCheckUrl);
                const data = await res.json();
                if (data.is_paid) {
                    alert('Pembayaran berhasil dikonfirmasi! Mengalihkan ke tiket Anda...');
                    window.location.href = this.successUrl;
                } else {
                    alert('Status transaksi saat ini: ' + (data.payment_status || 'Menunggu Pembayaran') + '. Silakan selesaikan pembayaran sesuai instruksi.');
                }
            } catch (e) {
                alert('Gagal memeriksa status pembayaran. Pastikan koneksi internet stabil.');
            } finally {
                this.isChecking = false;
            }
        }
    };
}
</script>
@endsection
