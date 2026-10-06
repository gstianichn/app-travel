@extends('layouts.dashboard')

@section('page-title', 'Cari Jadwal Shuttle & Armada')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- ===== FORM PENCARIAN RUTE & PENJADWALAN ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                    <svg width="16" height="16" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">Pencarian Rute & Penjadwalan</h2>
                    <p class="text-[11px] text-slate-400">Pilih titik asal, tujuan, tanggal, dan jumlah kursi armada</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                Langkah 1 dari 4
            </span>
        </div>

        <form action="{{ route('dashboard.user.search') }}" method="GET" class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Asal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">DARI (TITIK ASAL)</label>
                    <select name="origin" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        <option value="">Pilih kota / pool asal</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(request('origin') == $location->id)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tujuan --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">KE (TITIK TUJUAN)</label>
                    <select name="destination" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        <option value="">Pilih kota / pool tujuan</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(request('destination') == $location->id)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">TANGGAL PERJALANAN</label>
                    <input type="date" name="departure_date" required
                        value="{{ request('departure_date', date('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                </div>

                {{-- Penumpang --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">JUMLAH KURSI</label>
                    <select name="passengers" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" @selected(request('passengers', 1) == $i)>
                                {{ $i }} Kursi Penumpang
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tombol Cari --}}
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm"
                        style="background:#087f55;"
                        onmouseover="this.style.background='#066b47'"
                        onmouseout="this.style.background='#087f55'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                        </svg>
                        Cari Tiket
                    </button>
                </div>

            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mt-4 p-3 rounded-xl text-sm text-rose-700 border border-rose-100 bg-rose-50">
                    <p class="font-semibold mb-1">Periksa kembali parameter pencarian Anda:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>

    {{-- ===== HASIL PENCARIAN JADWAL & ARMADA ===== --}}
    @if ($searchPerformed)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Jadwal &amp; Armada Tersedia</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Ditemukan <span class="font-bold text-slate-800">{{ $schedules->count() }} armada aktif</span> 
                    untuk {{ \Carbon\Carbon::parse(request('departure_date'))->translatedFormat('l, d F Y') }}
                    ({{ request('passengers', 1) }} Kursi)
                </p>
            </div>
            @if ($schedules->count() > 0)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full" style="background:#e3f5ee; color:#087f55;">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Armada Beroperasi
                </span>
            @endif
        </div>

        @if ($schedules->count() > 0)
            <div class="space-y-4">
                @foreach ($schedules as $schedule)
                    @php
                        $vehicle = $schedule->vehicle;
                        $facilities = $vehicle?->facilities ?? ['AC Dingin', 'Reclining Seat', 'Port USB Charger'];
                        $depTime = $schedule->departure_time ? \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') : '08:00';
                        $seatsLeft = $schedule->available_seats ?? 0;
                        $passengersReq = (int) request('passengers', 1);
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all p-5">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                            
                            {{-- Info Armada & Rute --}}
                            <div class="lg:col-span-4 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $vehicle?->type ?? 'Toyota HiAce Premio' }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">
                                        {{ $vehicle?->plate_number ?? 'B 1234 TAA' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-base">
                                    {{ $schedule->route?->origin?->name ?? 'Asal' }} 
                                    <span class="text-emerald-700 mx-1">&rarr;</span> 
                                    {{ $schedule->route?->destination?->name ?? 'Tujuan' }}
                                </h4>
                                <p class="text-xs text-slate-500">
                                    Durasi perjalanan: <span class="font-medium text-slate-700">{{ $schedule->route?->duration ?? '2 Jam 45 Menit' }}</span> via Jalan Tol
                                </p>
                            </div>

                            {{-- Jam & Fasilitas --}}
                            <div class="lg:col-span-5 space-y-2.5 border-y lg:border-y-0 lg:border-x border-slate-100 py-3 lg:py-0 lg:px-4">
                                <div class="flex items-center gap-4 text-sm">
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg leading-none">{{ $depTime }} <span class="text-xs font-normal text-slate-400">WIB</span></p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Keberangkatan</p>
                                    </div>
                                    <div class="flex-1 flex items-center justify-center">
                                        <div class="w-full h-0.5 bg-slate-200 relative">
                                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#087f55;"></div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-700 text-sm leading-none">Pool Tujuan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Langsung Sampai</p>
                                    </div>
                                </div>

                                {{-- Fasilitas Armada --}}
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach(array_slice($facilities, 0, 4) as $fac)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5 rounded-md bg-slate-50 text-slate-600 border border-slate-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            {{ $fac }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Harga & Tombol Aksi --}}
                            <div class="lg:col-span-3 flex flex-row lg:flex-col items-center lg:items-end justify-between gap-3">
                                <div class="text-left lg:text-right">
                                    <p class="text-[11px] text-slate-400">Tarif per Penumpang</p>
                                    <p class="font-extrabold text-xl" style="color:#087f55;">
                                        {{ $schedule->formattedEffectivePrice() }}
                                    </p>
                                    <div class="mt-0.5">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $seatsLeft <= 3 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                            Sisa {{ $seatsLeft }} Kursi
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    @if ($seatsLeft >= $passengersReq)
                                        <a href="{{ route('dashboard.user.booking.pilih-kursi', ['schedule' => $schedule->id, 'passengers' => $passengersReq]) }}"
                                           class="px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-2"
                                           style="background:#087f55;"
                                           onmouseover="this.style.background='#066b47'"
                                           onmouseout="this.style.background='#087f55'">
                                            Pilih Kursi
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path d="m9 18 6-6-6-6"/>
                                            </svg>
                                        </a>
                                    @else
                                        <button disabled class="px-5 py-2.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed">
                                            Kursi Penuh
                                        </button>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State: Rute Belum Terhubung vs Kursi Penuh --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 sm:p-12 text-center">
                @if(!($routeConnected ?? false))
                    {{-- 1. KOTA / RUTE BELUM TERHUBUNG KE JADWAL SHUTTLE --}}
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-amber-50 text-amber-600 border border-amber-200">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-3">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Rute Belum Terhubung Langsung
                    </span>
                    <h4 class="font-extrabold text-slate-900 text-lg mb-2">Jadwal Shuttle Belum Tersedia</h4>
                    <p class="text-sm text-slate-600 max-w-lg mx-auto leading-relaxed mb-6">
                        Mohon maaf, rute langsung dari <strong>{{ $originLoc?->name ?? 'Kota Asal' }}</strong> ke <strong>{{ $destLoc?->name ?? 'Kota Tujuan' }}</strong> saat ini belum tersedia. Silakan pilih rute transit atau hub terdekat.
                    </p>

                    @if($transitHub)
                        {{-- REKOMENDASI TRANSIT HUB OTOMATIS --}}
                        <div class="max-w-xl mx-auto p-4 mb-6 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white font-bold text-[10px] uppercase">Rekomendasi Transit</span>
                                <h5 class="text-xs font-bold text-emerald-950">Rekomendasi Rute Transit Terdekat via {{ $transitHub->name }}</h5>
                            </div>
                            <p class="text-xs text-emerald-800 mb-3 leading-relaxed">
                                Anda dapat memesan 2 perjalanan terhubung secara praktis melalui hub transit <strong>{{ $transitHub->city }}</strong>:
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                <a href="{{ route('dashboard.user.search', ['origin' => $originLoc->id, 'destination' => $transitHub->id, 'departure_date' => request('departure_date', date('Y-m-d')), 'passengers' => request('passengers', 1)]) }}"
                                   class="p-2.5 rounded-xl bg-white border border-emerald-200 hover:border-emerald-400 hover:shadow-xs transition flex items-center justify-between text-slate-800 font-semibold group">
                                    <span>1. {{ $originLoc->city }} &rarr; {{ $transitHub->city }}</span>
                                    <span class="text-emerald-600 group-hover:translate-x-0.5 transition inline-flex items-center gap-0.5">Cari Tiket &rarr;</span>
                                </a>
                                <a href="{{ route('dashboard.user.search', ['origin' => $transitHub->id, 'destination' => $destLoc->id, 'departure_date' => request('departure_date', date('Y-m-d')), 'passengers' => request('passengers', 1)]) }}"
                                   class="p-2.5 rounded-xl bg-white border border-emerald-200 hover:border-emerald-400 hover:shadow-xs transition flex items-center justify-between text-slate-800 font-semibold group">
                                    <span>2. {{ $transitHub->city }} &rarr; {{ $destLoc->city }}</span>
                                    <span class="text-emerald-600 group-hover:translate-x-0.5 transition inline-flex items-center gap-0.5">Cari Tiket &rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="max-w-xl mx-auto p-4 rounded-xl bg-slate-50 border border-slate-200 text-left">
                        <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Standar Koridor Shuttle Aktif (Jadwal Selalu Tersedia):
                        </p>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Jakarta (Semanggi, Tebet, Cikini)</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Bekasi (Summarecon)</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Bogor (Baranangsiang)</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Depok (Margonda)</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Bandara Soetta (CGK)</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Cianjur ⇄ Bogor</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Sukabumi ⇄ Bogor / Jakarta</span>
                            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Bandung ⇄ Cirebon &amp; Jakarta ⇄ Cirebon</span>
                        </div>
                    </div>
                @else
                    {{-- 2. RUTE TERHUBUNG TETAPI SEMUA KURSI PENUH --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e3f5ee;">
                        <svg width="28" height="28" fill="none" stroke="#087f55" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect x="1" y="3" width="15" height="13" rx="2"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-base mb-2">Seluruh Kursi Telah Terisi</h4>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto">
                        Kursi untuk {{ request('passengers', 1) }} penumpang pada tanggal {{ \Carbon\Carbon::parse(request('departure_date'))->translatedFormat('d F Y') }} sedang penuh. Silakan coba pilih jam atau tanggal keberangkatan lainnya.
                    </p>
                @endif
            </div>
        @endif
    @else
        {{-- Belum mencari --}}
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-8 sm:p-12 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#f5f8f7;">
                <svg width="28" height="28" fill="none" stroke="#087f55" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <h4 class="font-bold text-slate-800 text-base mb-1">Siap Bepergian dengan Shuttle TravelKu?</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Pilih pool keberangkatan dan tujuan Anda pada form di atas, lalu klik <strong>Cari Tiket</strong> untuk melihat puluhan jadwal armada HiAce &amp; Elf aktif setiap harinya.
            </p>
        </div>
    @endif

</div>
@endsection
