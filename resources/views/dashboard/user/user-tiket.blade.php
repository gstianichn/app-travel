@extends('layouts.dashboard')

@section('page-title', 'Tiket Saya & Manajemen Perjalanan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{
    tab: 'aktif',
    activeModalTicket: null,
    activeRefundBooking: null,
    showReviewModal: {{ $pendingReviewBooking ? 'true' : 'false' }}
}">

    <!-- Flash Message -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs">!</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold">✕</button>
        </div>
    @endif

    {{-- ===== EVALUASI / REVIEW POP-UP BANNER (JIKA ADA PERJALANAN SELESAI BELUM DIULAS) ===== --}}
    @if ($pendingReviewBooking)
        <div class="bg-white rounded-3xl border-2 border-amber-300 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-200 text-amber-900">Perjalanan Selesai</span>
                    <span class="text-xs text-amber-700 font-semibold">{{ $pendingReviewBooking->schedule?->departure_date ? \Carbon\Carbon::parse($pendingReviewBooking->schedule->departure_date)->translatedFormat('d M Y') : '' }}</span>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">
                    Bagaimana perjalanan Anda: {{ $pendingReviewBooking->schedule?->route?->origin?->name }} ➔ {{ $pendingReviewBooking->schedule?->route?->destination?->name }}?
                </h3>
                <p class="text-xs text-slate-600">
                    Armada: <strong>{{ $pendingReviewBooking->schedule?->vehicle?->type ?? 'HiAce Premio' }}</strong> • 
                    Pengemudi: <strong>{{ $pendingReviewBooking->schedule?->driver?->user?->name ?? 'Pengemudi Fast Travel' }}</strong>
                </p>
            </div>
            <button type="button" @click="showReviewModal = true"
                    class="px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 flex-shrink-0"
                    style="background:#087f55;"
                    onmouseover="this.style.background='#066b47'"
                    onmouseout="this.style.background='#087f55'">
                <svg class="w-4 h-4 fill-amber-300" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                <span>Beri Penilaian (Review)</span>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tiket Saya & Manajemen Perjalanan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Akses e-ticket boarding, check-in pool, pengajuan refund, dan riwayat perjalanan.</p>
        </div>
        <a href="{{ route('dashboard.user.search') }}"
           class="px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-2 self-start"
           style="background:#087f55;"
           onmouseover="this.style.background='#066b47'"
           onmouseout="this.style.background='#087f55'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
            <span>Pesan Tiket Baru</span>
        </a>
    </div>

    <!-- Tab Switcher (3 Tab: Aktif, Riwayat, Refund) -->
    <div class="flex bg-slate-100 rounded-xl p-1 gap-1 text-xs">
        <button @click="tab = 'aktif'" id="tab-btn-aktif"
            :class="tab === 'aktif' ? 'bg-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
            :style="tab === 'aktif' ? 'color:#087f55;' : ''"
            class="flex-1 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
            <span>Tiket Aktif ({{ $activeBookings->count() }})</span>
        </button>

        <button @click="tab = 'riwayat'" id="tab-btn-riwayat"
            :class="tab === 'riwayat' ? 'bg-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
            :style="tab === 'riwayat' ? 'color:#087f55;' : ''"
            class="flex-1 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            <span>Riwayat Perjalanan ({{ $historyBookings->count() }})</span>
        </button>

        <button @click="tab = 'refund'" id="tab-btn-refund"
            :class="tab === 'refund' ? 'bg-white shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700'"
            :style="tab === 'refund' ? 'color:#087f55;' : ''"
            class="flex-1 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6 6-6"/></svg>
            <span>Status Refund ({{ $refunds->count() }})</span>
        </button>
    </div>

    <!-- ===== TAB 1: TIKET MENDATANG / AKTIF (ALUR A & B) ===== -->
    <div x-show="tab === 'aktif'" x-transition>
        @if ($activeBookings->isNotEmpty())
            <div class="space-y-5">
                @foreach ($activeBookings as $booking)
                    @php
                        $isCheckedIn = strtolower($booking->ticket?->status ?? '') === 'checked_in' || strtolower($booking->status) === 'checked_in';
                        $canRefund = $booking->canRefund();
                        $hoursLeft = $booking->hoursUntilDeparture();
                    @endphp

                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition">
                        
                        <!-- Header Tiket -->
                        <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-white/20 px-2.5 py-0.5 rounded-full">
                                        Perjalanan Terjadwal
                                    </span>
                                    @if ($isCheckedIn)
                                        <span class="bg-emerald-400 text-emerald-950 text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-2xs">
                                            ✓ Checked-In / On-Board
                                        </span>
                                    @else
                                        <span class="bg-white text-emerald-900 text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-2xs">
                                            Terkonfirmasi (Lunas)
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-black text-lg mt-1">
                                    {{ $booking->schedule?->route?->origin?->name ?? 'Asal' }} ➔ {{ $booking->schedule?->route?->destination?->name ?? 'Tujuan' }}
                                </h3>
                                <p class="text-xs text-white/85 mt-0.5">
                                    {{ $booking->schedule?->vehicle?->type ?? 'Toyota HiAce Premio' }} ({{ $booking->schedule?->vehicle?->plate_number ?? '-' }}) • 
                                    Supir: {{ $booking->schedule?->driver?->user?->name ?? 'Pengemudi Fast Travel' }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] text-emerald-200 block uppercase tracking-wider">No. Tiket Resmi</span>
                                <span class="font-mono font-bold text-base text-white">
                                    {{ $booking->ticket?->ticket_number ?? 'TKT-'.$booking->booking_code }}
                                </span>
                            </div>
                        </div>

                        <!-- Detail Perjalanan -->
                        <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-xs border-b border-dashed border-slate-200">
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Tanggal</p>
                                <p class="font-bold text-slate-800 mt-0.5">
                                    {{ $booking->schedule?->departure_date ? \Carbon\Carbon::parse($booking->schedule->departure_date)->translatedFormat('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Jam Berangkat</p>
                                <p class="font-bold text-slate-800 mt-0.5">
                                    {{ $booking->schedule?->departure_time ? \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') : '-' }} WIB
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Nomor Kursi</p>
                                <p class="font-black text-sm mt-0.5" style="color:#087f55;">
                                    {{ $booking->passengerSeatNumbers() }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Penumpang</p>
                                <p class="font-semibold text-slate-800 mt-0.5">
                                    {{ $booking->total_passenger }} Orang
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Pool Naik</p>
                                <p class="font-semibold text-slate-800 mt-0.5 truncate">
                                    {{ $booking->schedule?->route?->origin?->name ?? 'Pool Asal' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-medium uppercase tracking-wide">Total Bayar</p>
                                <p class="font-extrabold text-slate-900 mt-0.5">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Baris QR Code & Aksi Boarding -->
                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-slate-900 rounded-2xl p-1.5 flex items-center justify-center text-white flex-shrink-0 shadow-inner">
                                    <svg class="w-full h-full text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <rect x="2" y="2" width="8" height="8" rx="1"/>
                                        <rect x="4" y="4" width="4" height="4" fill="currentColor"/>
                                        <rect x="14" y="2" width="8" height="8" rx="1"/>
                                        <rect x="16" y="4" width="4" height="4" fill="currentColor"/>
                                        <rect x="2" y="14" width="8" height="8" rx="1"/>
                                        <rect x="4" y="16" width="4" height="4" fill="currentColor"/>
                                        <rect x="14" y="14" width="8" height="8" rx="1"/>
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Kode Booking & Boarding Token</span>
                                    <p class="font-mono font-black text-lg text-slate-900 tracking-wider">{{ $booking->booking_code }}</p>
                                    <p class="text-xs text-slate-500">
                                        @if ($isCheckedIn)
                                            <span class="text-emerald-700 font-semibold">Telah Check-in pada: {{ $booking->ticket?->boarded_at ? \Carbon\Carbon::parse($booking->ticket->boarded_at)->format('H:i') . ' WIB' : 'Hari Ini' }}</span>
                                        @else
                                            <span>Tunjukkan QR code ini ke petugas pool / sopir untuk check-in.</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Aksi Tiket -->
                            <div class="flex items-center gap-2">
                                {{-- Tombol Lihat E-Ticket QR Modal --}}
                                <button type="button"
                                        @click="activeModalTicket = {
                                            id: {{ $booking->ticket?->id ?? 0 }},
                                            code: '{{ $booking->booking_code }}',
                                            number: '{{ $booking->ticket?->ticket_number ?? 'TKT-'.$booking->booking_code }}',
                                            route: '{{ $booking->schedule?->route?->origin?->name }} ➔ {{ $booking->schedule?->route?->destination?->name }}',
                                            date: '{{ \Carbon\Carbon::parse($booking->schedule?->departure_date)->translatedFormat('d M Y') }}',
                                            time: '{{ \Carbon\Carbon::parse($booking->schedule?->departure_time)->format('H:i') }} WIB',
                                            seats: '{{ $booking->passengerSeatNumbers() }}',
                                            isCheckedIn: {{ $isCheckedIn ? 'true' : 'false' }}
                                        }"
                                        class="px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5"
                                        style="background:#087f55;"
                                        onmouseover="this.style.background='#066b47'"
                                        onmouseout="this.style.background='#087f55'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat E-Ticket (QR)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Footer Tiket: Syarat Waktu & Tombol Batalkan / Refund -->
                        <div class="px-6 py-3.5 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                @if ($canRefund)
                                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Dapat dibatalkan (Sisa waktu: <strong>{{ $hoursLeft }} jam lagi</strong> sebelum cut-off H-1 jam).
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                        ⚠️ Batas waktu pembatalan telah lewat (Maksimal H-1 jam sebelum keberangkatan).
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($canRefund)
                                    <button type="button"
                                            @click="activeRefundBooking = {
                                                id: {{ $booking->id }},
                                                code: '{{ $booking->booking_code }}',
                                                route: '{{ $booking->schedule?->route?->origin?->name }} ➔ {{ $booking->schedule?->route?->destination?->name }}',
                                                totalPrice: {{ (float)$booking->total_price }},
                                                cancellationFee: {{ round((float)$booking->total_price * 0.25, 2) }},
                                                refundAmount: {{ round((float)$booking->total_price * 0.75, 2) }}
                                            }"
                                            class="px-4 py-2 border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        <span>Batalkan & Ajukan Refund</span>
                                    </button>
                                @else
                                    <button disabled title="Batas waktu H-1 jam telah terlewati"
                                            class="px-4 py-2 bg-slate-200 text-slate-400 rounded-xl font-semibold text-xs cursor-not-allowed">
                                        Batalkan (Terkunci)
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-12 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto" style="background:#e3f5ee;">
                    <svg class="w-7 h-7" fill="none" stroke="#087f55" stroke-width="1.8" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Belum Ada Tiket Aktif</h4>
                <p class="text-xs text-slate-500 max-w-xs mx-auto">
                    Anda belum memiliki tiket terkonfirmasi untuk jadwal perjalanan mendatang.
                </p>
                <div class="pt-2">
                    <a href="{{ route('dashboard.user.search') }}"
                       class="inline-block px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition"
                       style="background:#087f55;"
                       onmouseover="this.style.background='#066b47'"
                       onmouseout="this.style.background='#087f55'">
                        Cari Tiket Sekarang →
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ===== TAB 2: RIWAYAT PERJALANAN (SELESAI & DIBATALKAN) ===== -->
    <div x-show="tab === 'riwayat'" x-transition style="display:none;">
        <div class="space-y-3">
            @forelse($historyBookings as $history)
                @php
                    $isCompleted = in_array(strtolower($history->status), ['completed', 'selesai']);
                    $hasReview = $history->review !== null;
                @endphp
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-900 text-sm">{{ $history->booking_code }}</span>
                            @if($isCompleted)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Perjalanan Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">
                            {{ $history->schedule?->route?->origin?->name ?? 'Asal' }} ➔ {{ $history->schedule?->route?->destination?->name ?? 'Tujuan' }}
                        </h4>
                        <p class="text-xs text-slate-500">
                            {{ $history->schedule?->departure_date ? \Carbon\Carbon::parse($history->schedule->departure_date)->translatedFormat('d M Y') : '-' }} • 
                            {{ $history->schedule?->vehicle?->type ?? 'HiAce' }} • Kursi: {{ $history->passengerSeatNumbers() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 self-end sm:self-center">
                        <div class="text-right">
                            <p class="text-xs text-slate-400">Total Transaksi</p>
                            <p class="font-bold text-slate-800 text-sm">Rp {{ number_format($history->total_price, 0, ',', '.') }}</p>
                        </div>

                        {{-- Status Review jika completed --}}
                        @if($isCompleted)
                            @if($hasReview)
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200">
                                    ⭐ {{ number_format($history->review->overall_rating, 1) }} (Sudah Diulas)
                                </span>
                            @else
                                <button type="button" @click="showReviewModal = true"
                                        class="px-3.5 py-1.5 text-xs font-bold rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 transition flex items-center gap-1">
                                    ⭐ Beri Rating
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400">
                    Belum ada riwayat perjalanan sebelumnya.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ===== TAB 3: STATUS & PROGRES REFUND (ALUR B) ===== -->
    <div x-show="tab === 'refund'" x-transition style="display:none;">
        <div class="space-y-4">
            @forelse($refunds as $ref)
                @php
                    $st = strtolower($ref->status);
                    // Progres 3 Tahap: 1. Diajukan -> 2. Diverifikasi -> 3. Dana Ditransfer
                    $step = 1;
                    if (in_array($st, ['diverifikasi', 'diproses', 'pending_approval'])) {
                        $step = 2;
                    } elseif (in_array($st, ['disetujui', 'approved', 'dana ditransfer', 'selesai'])) {
                        $step = 3;
                    }
                @endphp

                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">ID Refund #{{ $ref->id }}</span>
                            <h4 class="font-bold text-slate-900 text-base">
                                {{ $ref->booking?->schedule?->route?->origin?->name }} ➔ {{ $ref->booking?->schedule?->route?->destination?->name }}
                            </h4>
                            <p class="text-xs text-slate-500 font-mono">Kode Booking: {{ $ref->booking?->booking_code }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-400">Pengembalian Bersih (75%):</span>
                            <p class="text-lg font-black" style="color:#087f55;">
                                Rp {{ number_format($ref->amount, 0, ',', '.') }}
                            </p>
                            <span class="text-[11px] text-slate-400">(Potongan admin 25%: Rp {{ number_format($ref->cancellation_fee, 0, ',', '.') }})</span>
                        </div>
                    </div>

                    {{-- PROGRESS TRACKER 3 TAHAP: Diajukan ➔ Diverifikasi ➔ Dana Ditransfer --}}
                    <div>
                        <p class="text-xs font-bold text-slate-700 mb-3">Progres Pengembalian Dana:</p>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            {{-- Tahap 1 --}}
                            <div class="p-3 rounded-2xl border {{ $step >= 1 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                <span class="block text-lg mb-0.5">📝</span>
                                <span>1. Diajukan</span>
                                <span class="block text-[10px] font-normal text-slate-500 mt-0.5">{{ $ref->created_at ? $ref->created_at->format('d M, H:i') : '' }}</span>
                            </div>

                            {{-- Tahap 2 --}}
                            <div class="p-3 rounded-2xl border {{ $step >= 2 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                <span class="block text-lg mb-0.5">🔍</span>
                                <span>2. Diverifikasi</span>
                                <span class="block text-[10px] font-normal text-slate-500 mt-0.5">{{ $step >= 2 ? 'Disetujui Admin' : 'Menunggu antrean' }}</span>
                            </div>

                            {{-- Tahap 3 --}}
                            <div class="p-3 rounded-2xl border {{ $step >= 3 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                <span class="block text-lg mb-0.5">💸</span>
                                <span>3. Dana Ditransfer</span>
                                <span class="block text-[10px] font-normal text-slate-500 mt-0.5">{{ $step >= 3 ? 'Transfer Selesai' : 'Sedang dikirim' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl">
                        <div>
                            <span class="text-slate-400">Rekening / E-Wallet Tujuan:</span>
                            <p class="font-bold text-slate-800">{{ $ref->bank_name }} - {{ $ref->account_number }}</p>
                            <p class="text-slate-500">a/n {{ $ref->account_holder }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400">Alasan Pembatalan:</span>
                            <p class="text-slate-700 italic">"{{ $ref->reason }}"</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-12 text-center text-xs text-slate-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto bg-slate-100 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6 6-6"/></svg>
                    </div>
                    <p class="font-bold text-slate-700 text-sm">Tidak Ada Pengajuan Refund</p>
                    <p>Semua pesanan Anda aktif dan tidak ada pembatalan perjalanan.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ===== MODAL 1: LIHAT E-TICKET (QR) & CHECK-IN POOL (ALUR A) ===== --}}
    <div x-show="activeModalTicket !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
         x-transition>
        <div @click.outside="activeModalTicket = null" class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl space-y-4">
            
            {{-- Header Modal --}}
            <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="p-5 text-white flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider bg-white/20 px-2.5 py-0.5 rounded-full">E-Ticket Boarding Pass</span>
                    <h3 class="text-base font-bold mt-1" x-text="activeModalTicket?.route"></h3>
                </div>
                <button type="button" @click="activeModalTicket = null" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 text-white font-bold">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-5 text-center">
                {{-- QR Code Visual Generator --}}
                <div class="bg-slate-50 p-6 rounded-3xl border-2 border-slate-200 shadow-inner flex flex-col items-center justify-center space-y-3">
                    <div class="w-44 h-44 bg-slate-950 rounded-2xl p-3 flex items-center justify-center text-white shadow-md">
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
                    <div>
                        <p class="font-mono text-xl font-black text-slate-900 tracking-widest" x-text="activeModalTicket?.code"></p>
                        <p class="text-xs text-slate-400 mt-0.5">No. Tiket: <span class="font-mono font-bold text-slate-700" x-text="activeModalTicket?.number"></span></p>
                    </div>
                </div>

                {{-- Info Ringkas --}}
                <div class="grid grid-cols-3 gap-2 text-xs py-2 border-y border-slate-100 text-left">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Tanggal</span>
                        <strong class="text-slate-800" x-text="activeModalTicket?.date"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Jam Berangkat</span>
                        <strong class="text-slate-800" x-text="activeModalTicket?.time"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nomor Kursi</span>
                        <strong style="color:#087f55;" x-text="activeModalTicket?.seats"></strong>
                    </div>
                </div>

                {{-- Status & Simulasi Check-in di Pool --}}
                <div class="space-y-2 pt-1">
                    <template x-if="activeModalTicket?.isCheckedIn">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold text-xs flex items-center justify-center gap-2">
                            <span>✓ Penumpang Telah Check-In / On-Board</span>
                        </div>
                    </template>

                    <template x-if="!activeModalTicket?.isCheckedIn">
                        <form :action="'{{ url('/user/tiket') }}/' + activeModalTicket?.id + '/check-in'" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 px-4 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2"
                                    style="background:#087f55;"
                                    onmouseover="this.style.background='#066b47'"
                                    onmouseout="this.style.background='#087f55'">
                                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zm12 0h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zM5 20h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1z"/></svg>
                                <span>⚡ Simulasi Petugas Scan QR (Check-in Pool)</span>
                            </button>
                        </form>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== MODAL 2: FORMULIR PEMBATALAN & AJUKAN REFUND (ALUR B) ===== --}}
    <div x-show="activeRefundBooking !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
         x-transition>
        <div @click.outside="activeRefundBooking = null" class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl space-y-4">
            
            <div class="bg-rose-50 border-b border-rose-100 p-5 flex items-center justify-between text-rose-900">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-200 flex items-center justify-center text-rose-700 font-bold">
                        ✕
                    </div>
                    <div>
                        <h3 class="font-bold text-base">Batalkan & Ajukan Refund</h3>
                        <p class="text-xs text-rose-600 font-mono" x-text="activeRefundBooking?.code"></p>
                    </div>
                </div>
                <button type="button" @click="activeRefundBooking = null" class="text-slate-400 hover:text-slate-600 font-bold text-lg">
                    ✕
                </button>
            </div>

            <form :action="'{{ url('/user/tiket') }}/' + activeRefundBooking?.id + '/refund'" method="POST" class="p-6 space-y-4 text-xs">
                @csrf

                {{-- Simulasi Kalkulasi Potongan 25% Sesuai Ketentuan --}}
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                    <p class="font-bold text-slate-800 text-xs">Simulasi Pengembalian Dana (Refund):</p>
                    <div class="flex justify-between text-slate-500">
                        <span>Total Nilai Pembayaran:</span>
                        <span class="font-semibold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeRefundBooking?.totalPrice || 0)"></span>
                    </div>
                    <div class="flex justify-between text-rose-600">
                        <span>Biaya Administrasi (25%):</span>
                        <span class="font-semibold" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(activeRefundBooking?.cancellationFee || 0)"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between font-extrabold text-sm text-slate-900">
                        <span>Dana Dikembalikan (75%):</span>
                        <span style="color:#087f55;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(activeRefundBooking?.refundAmount || 0)"></span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alasan Pembatalan *</label>
                    <select name="reason" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs outline-none focus:border-emerald-600">
                        <option value="">Pilih alasan pembatalan</option>
                        <option value="Perubahan rencana perjalanan mendadak">Perubahan rencana perjalanan mendadak</option>
                        <option value="Kondisi darurat / sakit">Kondisi darurat / sakit</option>
                        <option value="Salah memilih tanggal atau armada">Salah memilih tanggal atau armada</option>
                        <option value="Keperluan keluarga">Keperluan keluarga</option>
                        <option value="Lainnya">Alasan lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bank / E-Wallet *</label>
                        <select name="bank_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs outline-none focus:border-emerald-600">
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="GoPay">GoPay</option>
                            <option value="OVO">OVO</option>
                            <option value="DANA">DANA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Rekening / HP *</label>
                        <input type="text" name="account_number" required placeholder="Contoh: 1234567890" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-emerald-600">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik Rekening / Akun *</label>
                    <input type="text" name="account_holder" required placeholder="Nama sesuai buku tabungan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-emerald-600">
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-[11px] leading-relaxed">
                    ℹ️ <strong>Pembebasan Kursi Otomatis:</strong> Setelah permohonan dikirim, tiket Anda dibatalkan dan kursi langsung dibuka kembali ke sistem agar bisa dipesan penumpang lain.
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="activeRefundBooking = null" class="flex-1 py-2.5 border border-slate-200 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition shadow-sm">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ===== MODAL 3: EVALUASI POP-UP RATING & REVIEW (ALUR C) ===== --}}
    @if ($pendingReviewBooking)
        <div x-show="showReviewModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
             x-transition>
            <div @click.outside="showReviewModal = false" class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl space-y-4">
                
                <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="p-5 text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider bg-white/20 px-2.5 py-0.5 rounded-full">Evaluasi Pascaperjalanan</span>
                        <h3 class="text-base font-bold mt-1">Ulasan & Rating Perjalanan</h3>
                    </div>
                    <button type="button" @click="showReviewModal = false" class="text-white/80 hover:text-white font-bold text-lg">✕</button>
                </div>

                <form action="{{ route('dashboard.user.tiket.review', $pendingReviewBooking) }}" method="POST"
                      x-data="{ cleanliness: 5, driver: 5, punctuality: 5 }"
                      class="p-6 space-y-5 text-xs">
                    @csrf

                    <div class="text-center pb-2 border-b border-slate-100">
                        <p class="font-bold text-slate-800 text-sm">
                            {{ $pendingReviewBooking->schedule?->route?->origin?->name }} ➔ {{ $pendingReviewBooking->schedule?->route?->destination?->name }}
                        </p>
                        <p class="text-slate-400 mt-0.5 font-mono">{{ $pendingReviewBooking->booking_code }}</p>
                    </div>

                    {{-- Metric 1: Kebersihan Armada --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-700">1. Kebersihan Armada & Kursi</span>
                            <span class="font-bold text-amber-600" x-text="cleanliness + ' / 5 ⭐'"></span>
                        </div>
                        <input type="range" name="cleanliness_rating" min="1" max="5" x-model="cleanliness" class="w-full accent-emerald-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400">
                            <span>Kurang Bersih</span>
                            <span>Sangat Bersih & Wangi</span>
                        </div>
                    </div>

                    {{-- Metric 2: Keramahan Pengemudi --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-700">2. Keramahan & Keamanan Pengemudi</span>
                            <span class="font-bold text-amber-600" x-text="driver + ' / 5 ⭐'"></span>
                        </div>
                        <input type="range" name="driver_rating" min="1" max="5" x-model="driver" class="w-full accent-emerald-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400">
                            <span>Perlu Ditingkatkan</span>
                            <span>Ramah & Sopan</span>
                        </div>
                    </div>

                    {{-- Metric 3: Ketepatan Waktu --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-700">3. Ketepatan Waktu Keberangkatan</span>
                            <span class="font-bold text-amber-600" x-text="punctuality + ' / 5 ⭐'"></span>
                        </div>
                        <input type="range" name="punctuality_rating" min="1" max="5" x-model="punctuality" class="w-full accent-emerald-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400">
                            <span>Terlambat</span>
                            <span>Tepat Waktu (On-Time)</span>
                        </div>
                    </div>

                    {{-- Kotak Saran / Komentar --}}
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kotak Saran & Ulasan Anda (Opsional)</label>
                        <textarea name="comment" rows="3" placeholder="Ceritakan kepuasan Anda selama perjalanan dengan minibus kami..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-emerald-600 resize-none"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showReviewModal = false" class="flex-1 py-2.5 border border-slate-200 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition">
                            Nanti Saja
                        </button>
                        <button type="submit" class="flex-1 py-2.5 text-white font-bold rounded-xl transition shadow-sm"
                                style="background:#087f55;"
                                onmouseover="this.style.background='#066b47'"
                                onmouseout="this.style.background='#087f55'">
                            Kirim Evaluasi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>
@endsection
