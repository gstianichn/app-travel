@extends('layouts.dashboard')

@section('page-title', 'Pemesanan Berhasil & E-Ticket')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Alert Success --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center space-y-3">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto text-white text-2xl shadow-sm" style="background:#087f55;">
            ✓
        </div>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800">
            Pembayaran Terverifikasi Otomatis
        </span>
        <h2 class="text-2xl font-black text-slate-900">E-Ticket Perjalanan Anda Terbit!</h2>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Webhook payment gateway telah memverifikasi pembayaran Anda. Status pesanan telah diubah menjadi <strong>Confirmed</strong>. Tunjukkan kode QR di bawah ini kepada supir saat boarding.
        </p>
    </div>

    {{-- E-TICKET DIGITAL BOARDING PASS CARD --}}
    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-md overflow-hidden relative">
        
        {{-- Header Tiket Hijau Brand --}}
        <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest bg-white/20 px-2.5 py-0.5 rounded-full">
                    BOARDING PASS ELEKTRONIK
                </span>
                <h3 class="text-xl font-black mt-1">
                    {{ $booking->schedule->route?->origin?->name }} &rarr; {{ $booking->schedule->route?->destination?->name }}
                </h3>
                <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.85);">
                    {{ $booking->schedule->vehicle?->type ?? 'Toyota HiAce Premio' }} • {{ $booking->schedule->vehicle?->plate_number ?? '-' }}
                </p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] text-emerald-200 block uppercase tracking-wider">No. Tiket Resmi</span>
                <span class="font-mono font-bold text-base text-white">{{ $booking->ticket?->ticket_number ?? 'TKT-2609-AUTO' }}</span>
            </div>
        </div>

        {{-- Detail Perjalanan & Waktu --}}
        <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs border-b border-dashed border-slate-200">
            <div>
                <p class="text-slate-400 font-medium">Tanggal Berangkat</p>
                <p class="font-bold text-slate-800 text-sm mt-0.5">
                    {{ \Carbon\Carbon::parse($booking->schedule->departure_date)->translatedFormat('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Jam Keberangkatan</p>
                <p class="font-bold text-slate-800 text-sm mt-0.5">
                    {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') }} WIB
                </p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Nomor Kursi</p>
                <p class="font-extrabold text-sm mt-0.5" style="color:#087f55;">
                    {{ $booking->passengerSeatNumbers() }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Total Penumpang</p>
                <p class="font-bold text-slate-800 text-sm mt-0.5">
                    {{ $booking->total_passenger }} Orang
                </p>
            </div>
        </div>

        {{-- QR Code & Data Penumpang --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            
            {{-- QR Code Boarding --}}
            <div class="md:col-span-5 flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                <div class="w-36 h-36 bg-slate-900 rounded-xl p-2.5 flex items-center justify-center text-white shadow-inner">
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
                </div>
                <p class="font-mono font-extrabold text-slate-900 text-sm tracking-wider mt-3">
                    {{ $booking->booking_code }}
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5">Tunjukkan saat naik kendaraan</span>
            </div>

            {{-- Manifest Penumpang & Rincian Lunas --}}
            <div class="md:col-span-7 space-y-4">
                <div>
                    <h5 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-2">Daftar Penumpang:</h5>
                    <div class="space-y-1.5">
                        @foreach($booking->passengers as $p)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white" style="background:#087f55;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="font-semibold text-slate-800">{{ $p->name }}</span>
                                </div>
                                <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    Kursi {{ $p->seat?->seat_number ?? '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Metode Pembayaran:</span>
                        <span class="font-medium text-slate-700">{{ $booking->payment?->payment_method ?? 'Virtual Account BCA' }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Status Pembayaran:</span>
                        <span class="font-bold text-emerald-700">Lunas (Confirmed)</span>
                    </div>
                    <div class="flex justify-between text-sm font-extrabold pt-1">
                        <span>Total Dibayar:</span>
                        <span style="color:#087f55;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Tiket Card --}}
        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-400">
            <span>Pool Keberangkatan: {{ $booking->schedule->route?->origin?->address ?? 'Pool Utama' }}</span>
            <span>Waktu Cetak: {{ date('d M Y, H:i') }} WIB</span>
        </div>

    </div>

    {{-- Tombol Aksi Navigasi --}}
    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
        <a href="{{ route('dashboard.user.user-tiket') }}"
           class="px-6 py-3 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center gap-2"
           style="background:#087f55;"
           onmouseover="this.style.background='#066b47'"
           onmouseout="this.style.background='#087f55'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
            <span>Buka Tab Tiket Saya</span>
        </a>

        <button type="button" onclick="window.print();"
                class="px-5 py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition shadow-2xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            <span>Cetak Tiket</span>
        </button>

        <a href="{{ route('dashboard.user.index') }}"
           class="px-5 py-3 text-slate-600 hover:text-slate-800 text-sm font-semibold transition">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
