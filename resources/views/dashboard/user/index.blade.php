@extends('layouts.dashboard')

@section('page-title', 'Client Area & Beranda Penumpang')

@section('content')
<div class="space-y-8">

    <!-- ============================================================
         1. WELCOME HERO & CLIENT AREA GREETING
    ============================================================ -->
    <div class="relative rounded-3xl p-6 sm:p-8 text-white overflow-hidden shadow-lg"
         style="background: linear-gradient(135deg, #055c3d 0%, #087f55 50%, #0a9e6a 100%);">
        
        <!-- Decorative Background Patterns -->
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 -mb-12 w-80 h-80 rounded-full bg-emerald-400/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold uppercase tracking-wider text-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Pelanggan Resmi TravelKu</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Halo, {{ auth()->user()->name ?? 'Penumpang' }}! 👋
                </h1>
                <p class="text-sm sm:text-base text-emerald-50/90 leading-relaxed font-normal">
                    Mau bepergian ke mana hari ini? Nikmati perjalanan antar kota minibus Toyota HiAce Premio yang aman, tepat waktu, dan nyaman.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="#form-cari-tiket"
                   class="px-5 py-3 rounded-2xl bg-white text-brand-700 hover:bg-emerald-50 font-extrabold text-sm shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    <span>Pesan Tiket Sekarang</span>
                </a>
                <a href="{{ route('dashboard.user.user-tiket') }}"
                   class="px-5 py-3 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-bold text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                    <span>Tiket Saya ({{ $activeTicketsCount ?? 0 }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================
         2. CLIENT AREA QUICK STATS (METRIK PENUMPANG)
    ============================================================ -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Stat 1: Tiket Aktif -->
        <a href="{{ route('dashboard.user.user-tiket') }}"
           class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 hover:border-brand-300 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tiket Aktif</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition">
                    🎫
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $activeTicketsCount ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">perjalanan</span>
            </div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1">Siap berangkat ➔</p>
        </a>

        <!-- Stat 2: Menunggu Bayar -->
        <a href="{{ route('dashboard.user.user-tiket') }}"
           class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 hover:border-amber-300 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Bayar</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition">
                    ⏳
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $pendingPaymentsCount ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">transaksi</span>
            </div>
            <p class="text-[11px] text-amber-600 font-semibold mt-1">Selesaikan sebelum batas waktu ➔</p>
        </a>

        <!-- Stat 3: Riwayat Selesai -->
        <a href="{{ route('dashboard.user.user-tiket') }}"
           class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Riwayat Selesai</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition">
                    🛣️
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $completedTripsCount ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">perjalanan</span>
            </div>
            <p class="text-[11px] text-blue-600 font-semibold mt-1">Lihat riwayat & invoice ➔</p>
        </a>

        <!-- Stat 4: Pemberitahuan Sistem -->
        <a href="{{ route('dashboard.user.user-notifikasi') }}"
           class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Notifikasi</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold group-hover:scale-110 transition">
                    🔔
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $unreadNotifCount ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">pesan baru</span>
            </div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-1">Cek pengingat & armada ➔</p>
        </a>

    </div>

    <!-- ============================================================
         3. PERJALANAN AKTIF MENDATANG (E-TICKET BOARDING PASS)
    ============================================================ -->
    @if ($activeBooking && $activeBooking->schedule)
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden border border-slate-700">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            ● Tiket Aktif Siap Pakai
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-300">
                            #{{ $activeBooking->booking_code }}
                        </span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white">
                        {{ $activeBooking->schedule->route?->origin?->name ?? 'Titik Asal' }} ➔ {{ $activeBooking->schedule->route?->destination?->name ?? 'Titik Tujuan' }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300 font-medium pt-1">
                        <span class="flex items-center gap-1.5">
                            📅 {{ \Carbon\Carbon::parse($activeBooking->schedule->departure_date)->translatedFormat('d F Y') }}
                        </span>
                        <span class="flex items-center gap-1.5 font-bold text-emerald-300">
                            ⏰ Pukul {{ \Carbon\Carbon::parse($activeBooking->schedule->departure_time)->format('H:i') }} WIB
                        </span>
                        <span class="flex items-center gap-1.5">
                            🚐 {{ $activeBooking->schedule->vehicle?->name ?? 'Toyota HiAce Premio' }} ({{ $activeBooking->schedule->vehicle?->plate_number ?? '-' }})
                        </span>
                        <span class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white/10 text-white font-bold">
                            💺 Kursi: {{ $activeBooking->passengerSeatNumbers() }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <a href="{{ route('dashboard.user.user-tiket', ['booking_id' => $activeBooking->id, 'action' => 'ticket']) }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-sm shadow-md transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zm12 0h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zM5 20h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1z"/></svg>
                        <span>Buka E-Ticket & QR</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================
         4. WIDGET PENCARIAN TIKET FRONT-END CONSUMER GRADE
    ============================================================ -->
    <div id="form-cari-tiket" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900">Cari Jadwal Shuttle & Booking Tiket</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Pilih pool keberangkatan, kota tujuan, tanggal perjalanan, dan jumlah kursi minibus
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-ping"></span>
                Jadwal Real-Time
            </span>
        </div>

        <form action="{{ route('dashboard.user.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            {{-- Titik Asal --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Dari (Pool Keberangkatan)</label>
                <div class="relative">
                    <select name="origin" required class="w-full bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-brand-500 focus:bg-white rounded-2xl px-3.5 py-3 text-sm font-semibold text-slate-800 outline-none transition shadow-2xs">
                        <option value="">Pilih Pool Asal</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ (request('origin') == $location->id || (empty(request('origin')) && str_contains(strtolower($location->name), 'bandung'))) ? 'selected' : '' }}>
                                📍 {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Titik Tujuan --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Ke (Pool Kedatangan)</label>
                <div class="relative">
                    <select name="destination" required class="w-full bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-brand-500 focus:bg-white rounded-2xl px-3.5 py-3 text-sm font-semibold text-slate-800 outline-none transition shadow-2xs">
                        <option value="">Pilih Pool Tujuan</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ (request('destination') == $location->id || (empty(request('destination')) && str_contains(strtolower($location->name), 'jakarta'))) ? 'selected' : '' }}>
                                🏁 {{ $location->name }} ({{ $location->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Perjalanan --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tanggal Keberangkatan</label>
                <input type="date" name="departure_date" required min="{{ date('Y-m-d') }}" value="{{ request('departure_date', date('Y-m-d')) }}"
                       class="w-full bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-brand-500 focus:bg-white rounded-2xl px-3.5 py-3 text-sm font-semibold text-slate-800 outline-none transition shadow-2xs">
            </div>

            {{-- Jumlah Kursi --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Jumlah Penumpang</label>
                <select name="passengers" required class="w-full bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-brand-500 focus:bg-white rounded-2xl px-3.5 py-3 text-sm font-semibold text-slate-800 outline-none transition shadow-2xs">
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('passengers', 1) == $i ? 'selected' : '' }}>
                            👤 {{ $i }} Kursi Penumpang
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Tombol Cari --}}
            <div class="flex items-end">
                <button type="submit"
                        class="w-full py-3 px-5 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    <span>Cari Tiket</span>
                </button>
            </div>

        </form>

        {{-- Quick Rute Populer --}}
        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-400 font-semibold">Rute Terpopuler:</span>
            @php
                $bandungLoc = $locations->first(fn($l) => str_contains(strtolower($l->name), 'bandung'));
                $jakartaLoc = $locations->first(fn($l) => str_contains(strtolower($l->name), 'jakarta'));
            @endphp
            @if ($bandungLoc && $jakartaLoc)
                <a href="{{ route('dashboard.user.search', ['origin' => $bandungLoc->id, 'destination' => $jakartaLoc->id, 'departure_date' => date('Y-m-d'), 'passengers' => 1]) }}"
                   class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-semibold transition">
                    ⚡ Bandung (Dipatiukur) ➔ Jakarta (Semanggi)
                </a>
                <a href="{{ route('dashboard.user.search', ['origin' => $jakartaLoc->id, 'destination' => $bandungLoc->id, 'departure_date' => date('Y-m-d'), 'passengers' => 1]) }}"
                   class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-semibold transition">
                    ⚡ Jakarta (Semanggi) ➔ Bandung (Dipatiukur)
                </a>
            @endif
        </div>
    </div>

    <!-- ============================================================
         5. KEUNGGULAN MINIBUS EKSEKUTIF (TRUST BADGES CONSUMER)
    ============================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                🚐
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Armada Toyota HiAce</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Kabin bersih, AC dingin, dan kursi captain seat yang lega.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                ⏱️
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Jaminan Tepat Waktu</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Berangkat sesuai jadwal pasti tanpa menunggu kuota penuh.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                🔌
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Charging Port & Reclining</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Colokan USB di setiap kursi, free air mineral, dan bagasi aman.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-2xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                🛡️
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Asuransi Jasa Raharja</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Perlindungan resmi penumpang dan sopir bersertifikasi handal.</p>
            </div>
        </div>

    </div>

    <!-- ============================================================
         6. VOUCHER PROMO & RIWAYAT PEMESANAN KONSUMEN
    ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Voucher Promo Spesial -->
        <div id="promo-section" class="space-y-4">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Kupon & Promo Spesial</h3>
                    <p class="text-xs text-slate-400">Gunakan saat pengisian data penumpang</p>
                </div>
            </div>

            @foreach($promos as $promo)
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-emerald-300 transition">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-lg">{{ $promo['discount_label'] }}</span>
                            <span class="text-[11px] text-slate-400">s/d {{ $promo['valid_until'] }}</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $promo['title'] }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $promo['description'] }}</p>
                        
                        <div class="mt-4 p-2.5 border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-between bg-slate-50">
                            <span class="font-mono font-bold tracking-wider text-sm text-brand-600">
                                {{ $promo['code'] }}
                            </span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $promo['code'] }}'); alert('Kode promo {{ $promo['code'] }} berhasil disalin!');"
                                    class="text-xs font-bold px-3 py-1 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                                Salin Kode
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Aktivitas Pemesanan Terakhir -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Aktivitas Pemesanan Terakhir</h3>
                        <p class="text-xs text-slate-400">Pantau transaksi dan tiket perjalanan Anda</p>
                    </div>
                    <a href="{{ route('dashboard.user.user-tiket') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 hover:underline">
                        Lihat Semua Tiket →
                    </a>
                </div>

                @if ($recentBookings->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($recentBookings as $recent)
                            @php
                                $isPaid = in_array(strtolower($recent->status), ['paid', 'confirmed', 'success', 'lunas']);
                                $isPending = strtolower($recent->status) === 'pending';
                                $isCompleted = strtolower($recent->status) === 'completed';
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition gap-3 text-xs">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900">{{ $recent->booking_code }}</span>
                                        @if ($recent->schedule?->vehicle)
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-200 text-slate-700 font-semibold">
                                                {{ $recent->schedule->vehicle->type }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-slate-800 font-bold text-sm">
                                        {{ $recent->schedule?->route?->origin?->name ?? '-' }} ➔ {{ $recent->schedule?->route?->destination?->name ?? '-' }}
                                    </p>
                                    <p class="text-slate-400">
                                        {{ $recent->schedule?->departure_date ? \Carbon\Carbon::parse($recent->schedule->departure_date)->translatedFormat('d F Y') : '-' }} • 
                                        {{ $recent->total_passenger }} Penumpang (Kursi: {{ $recent->passengerSeatNumbers() }})
                                    </p>
                                </div>
                                <div class="text-left sm:text-right space-y-1.5 flex-shrink-0">
                                    @if ($isPaid)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Lunas / E-Ticket Aktif
                                        </span>
                                    @elseif ($isPending)
                                        <a href="{{ route('dashboard.user.booking.pembayaran', $recent) }}" class="inline-block px-3 py-1 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition">
                                            Bayar Sekarang →
                                        </a>
                                    @elseif ($isCompleted)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                            {{ ucfirst($recent->status) }}
                                        </span>
                                    @endif
                                    <p class="font-extrabold text-slate-900 text-sm">
                                        Rp {{ number_format($recent->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3 bg-brand-50 text-brand-600 text-2xl">
                            🎫
                        </div>
                        <p class="font-bold text-slate-800 text-base">Belum Ada Transaksi Tiket</p>
                        <p class="text-xs text-slate-400 mt-1">Cari jadwal shuttle di atas untuk memulai pemesanan perjalanan Anda.</p>
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Punya pertanyaan seputar jadwal atau tiket?</span>
                <a href="https://wa.me/62812345678" target="_blank" class="font-bold text-brand-600 hover:underline">
                    Hubungi Customer Service WhatsApp ➔
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
