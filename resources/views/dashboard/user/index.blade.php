@extends('layouts.dashboard')

@section('page-title', 'Beranda Penumpang')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- 1. BANNER STATUS PERJALANAN AKTIF (JIKA ADA TIKET TERKONFIRMASI) -->
    @if ($activeBooking && $activeBooking->schedule)
        <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-block px-2.5 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wider">Perjalanan Mendatang</span>
                    <span class="text-xs bg-emerald-400 text-emerald-950 font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Terkonfirmasi</span>
                </div>
                <h3 class="text-xl font-bold">
                    {{ $activeBooking->schedule->route?->origin?->name ?? 'Asal' }} ➔ {{ $activeBooking->schedule->route?->destination?->name ?? 'Tujuan' }}
                </h3>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,.85);">
                    {{ $activeBooking->schedule->vehicle?->type ?? 'Toyota HiAce Premio' }} ({{ $activeBooking->schedule->vehicle?->plate_number ?? '-' }}) • 
                    {{ \Carbon\Carbon::parse($activeBooking->schedule->departure_date)->translatedFormat('d M Y') }}, 
                    {{ \Carbon\Carbon::parse($activeBooking->schedule->departure_time)->format('H:i') }} WIB • 
                    Kursi: <span class="font-bold text-white">{{ $activeBooking->passengerSeatNumbers() }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.user.user-tiket') }}" style="background:white; color:#087f55;" class="px-5 py-2.5 font-bold text-sm rounded-xl hover:opacity-90 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zm12 0h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1zM5 20h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1z"/></svg>
                    Lihat E-Ticket QR
                </a>
            </div>
        </div>
    @else
        <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="inline-block px-2.5 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">Pemesanan Shuttle Cepat</span>
                <h3 class="text-xl font-bold">Siap Berangkat Hari Ini?</h3>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,.80);">Nikmati perjalanan nyaman minibus eksekutif Toyota HiAce Premio dengan fasilitas lengkap & aman.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="#widget-pencarian" style="background:white; color:#087f55;" class="px-5 py-2.5 font-bold text-sm rounded-xl hover:opacity-90 transition shadow-sm">
                    Pesan Tiket Sekarang ↓
                </a>
            </div>
        </div>
    @endif

    <!-- 2. WIDGET PENCARIAN RUTE & PENJADWALAN -->
    <div id="widget-pencarian" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 border-b border-slate-100 pb-3 gap-2">
            <div>
                <h4 class="font-bold text-slate-800 text-base">Cari Jadwal Shuttle & Armada</h4>
                <p class="text-xs text-slate-400 mt-0.5">Tentukan rute asal, tujuan, tanggal keberangkatan, dan jumlah kursi</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#e3f5ee; color:#087f55;">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Armada Aktif Siap Jalan
                </span>
            </div>
        </div>

        <form action="{{ route('dashboard.user.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Titik Asal --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">DARI (TITIK ASAL)</label>
                <div class="relative">
                    <select name="origin" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        <option value="">Pilih Titik Asal</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ (request('origin') == $location->id || (empty(request('origin')) && str_contains(strtolower($location->name), 'bandung'))) ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Titik Tujuan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">KE (TITIK TUJUAN)</label>
                <div class="relative">
                    <select name="destination" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        <option value="">Pilih Titik Tujuan</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ (request('destination') == $location->id || (empty(request('destination')) && str_contains(strtolower($location->name), 'jakarta'))) ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Perjalanan --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">TANGGAL PERJALANAN</label>
                <input type="date" name="departure_date" required min="{{ date('Y-m-d') }}" value="{{ request('departure_date', date('Y-m-d')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>

            {{-- Jumlah Kursi --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">JUMLAH KURSI</label>
                <select name="passengers" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('passengers', 1) == $i ? 'selected' : '' }}>{{ $i }} Kursi Penumpang</option>
                    @endfor
                </select>
            </div>

            {{-- Tombol Cari --}}
            <div class="flex items-end">
                <button type="submit" class="w-full text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm" style="background:#087f55;" onmouseover="this.style.background='#066b47'" onmouseout="this.style.background='#087f55'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    Cari Tiket
                </button>
            </div>
        </form>

        {{-- Quick Rute Populer --}}
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-400 font-medium">Rute Cepat:</span>
            @php
                $bandungLoc = $locations->first(fn($l) => str_contains(strtolower($l->name), 'bandung'));
                $jakartaLoc = $locations->first(fn($l) => str_contains(strtolower($l->name), 'jakarta'));
            @endphp
            @if ($bandungLoc && $jakartaLoc)
                <a href="{{ route('dashboard.user.search', ['origin' => $bandungLoc->id, 'destination' => $jakartaLoc->id, 'departure_date' => date('Y-m-d'), 'passengers' => 1]) }}" class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                    ⚡ Bandung (Dipatiukur) ➔ Jakarta (Semanggi)
                </a>
                <a href="{{ route('dashboard.user.search', ['origin' => $jakartaLoc->id, 'destination' => $bandungLoc->id, 'departure_date' => date('Y-m-d'), 'passengers' => 1]) }}" class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                    ⚡ Jakarta (Semanggi) ➔ Bandung (Dipatiukur)
                </a>
            @endif
        </div>
    </div>

    <!-- 3. KARTU VOUCHER PROMO & RIWAYAT TERAKHIR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Voucher Promo Aktif -->
        <div class="space-y-4">
            @foreach($promos as $promo)
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md">{{ $promo['discount_label'] }}</span>
                            <span class="text-xs text-slate-400">s/d {{ $promo['valid_until'] }}</span>
                        </div>
                        <h5 class="font-bold text-slate-800 text-base">{{ $promo['title'] }}</h5>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $promo['description'] }}</p>
                        
                        <div class="mt-3.5 p-2.5 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-between bg-slate-50">
                            <span class="font-mono font-bold tracking-wider text-sm" style="color:#087f55;">
                                {{ $promo['code'] }}
                            </span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $promo['code'] }}'); alert('Kode promo {{ $promo['code'] }} berhasil disalin!');" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                                Salin Kode
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Ringkasan Tiket & Transaksi Terakhir -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Aktivitas Pemesanan Terakhir</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Pantau status transaksi tiket perjalanan Anda</p>
                    </div>
                    <a href="{{ route('dashboard.user.user-tiket') }}" class="text-xs font-semibold hover:underline" style="color:#087f55;">
                        Lihat Semua Tiket →
                    </a>
                </div>

                @if ($recentBookings->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($recentBookings as $recent)
                            @php
                                $isPaid = in_array(strtolower($recent->status), ['paid', 'confirmed', 'success', 'lunas']);
                                $isPending = strtolower($recent->status) === 'pending';
                            @endphp
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs hover:border-slate-200 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-800">{{ $recent->booking_code }}</span>
                                        @if ($recent->schedule?->vehicle)
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-200 text-slate-700 font-medium">
                                                {{ $recent->schedule->vehicle->type }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-slate-600 font-medium">
                                        {{ $recent->schedule?->route?->origin?->name ?? '-' }} ➔ {{ $recent->schedule?->route?->destination?->name ?? '-' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $recent->schedule?->departure_date ? \Carbon\Carbon::parse($recent->schedule->departure_date)->translatedFormat('d M Y') : '-' }} • 
                                        {{ $recent->total_passenger }} Penumpang (Kursi: {{ $recent->passengerSeatNumbers() }})
                                    </p>
                                </div>
                                <div class="text-right space-y-1.5">
                                    @if ($isPaid)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas / Terkonfirmasi
                                        </span>
                                    @elseif ($isPending)
                                        <a href="{{ route('dashboard.user.booking.pembayaran', $recent) }}" class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition">
                                            Menunggu Pembayaran →
                                        </a>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ ucfirst($recent->status) }}
                                        </span>
                                    @endif
                                    <p class="font-bold text-slate-800 text-sm">
                                        Rp {{ number_format($recent->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background:#e3f5ee;">
                            <svg class="w-6 h-6" fill="none" stroke="#087f55" stroke-width="1.8" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                        </div>
                        <p class="font-semibold text-slate-700 text-sm">Belum Ada Transaksi</p>
                        <p class="text-xs text-slate-400 mt-1">Cari jadwal shuttle di atas untuk memulai pemesanan perjalanan Anda.</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Butuh bantuan customer support?</span>
                <a href="{{ route('dashboard.user.user-akun') }}" class="font-semibold hover:underline" style="color:#087f55;">
                    Pusat Bantuan & WhatsApp CS
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
