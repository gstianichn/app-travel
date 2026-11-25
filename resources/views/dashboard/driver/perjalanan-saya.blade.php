@extends('layouts.driver')

@section('title', 'Perjalanan Saya - Driver TravelKu')

@section('content')
@php
    $originName = $activeSchedule->route?->origin?->name ?? 'Loket Keberangkatan';
    $destName = $activeSchedule->route?->destination?->name ?? 'Loket Tujuan';
    $tripKey = $activeSchedule->tripStatusKey();
    $isCancelled = $tripKey === 'cancelled';
    $isDepartingOrLater = in_array($tripKey, ['departing', 'departed', 'arrived', 'completed'], true);
@endphp

<div class="space-y-6">
    {{-- Header Halaman --}}
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Perjalanan Saya</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                Kelola jadwal aktif, tinjau rute jemput &amp; turun penumpang, dan konfirmasi keberangkatan langsung tanpa approval admin.
            </p>
        </div>

        @if (isset($availableDriverSchedules) && $availableDriverSchedules->count() > 1)
            <div class="flex items-center gap-2">
                <label for="tripScheduleSelect" class="text-xs font-bold text-[#5C6862]">Pilih Jadwal:</label>
                <select id="tripScheduleSelect"
                        onchange="if(this.value) window.location.href = '{{ route('dashboard.driver.perjalanan') }}?schedule_id=' + this.value"
                        class="h-10 px-3 rounded-lg border border-[#DEDAC9] bg-[#FFFDF8] text-sm font-semibold text-[#13201B]">
                    @foreach ($availableDriverSchedules as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $activeSchedule->id ? 'selected' : '' }}>
                            {{ $s->departure_date ? $s->departure_date->format('d M') : 'Hari ini' }} {{ \Carbon\Carbon::parse($s->departure_time)->format('H:i') }} WIB &middot; {{ $s->route?->origin?->city ?? 'Asal' }} &rarr; {{ $s->route?->destination?->city ?? 'Tujuan' }}@if (($s->active_bookings_count ?? 0) > 0) ({{ $s->active_bookings_count }} pesanan)@endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </header>

    @if (! $hasAssignedSchedule || ! $activeSchedule->exists)
        <div class="bg-[#FFFDF8] border border-dashed border-[#DEDAC9] rounded-2xl p-8 text-center">
            <h2 class="text-lg font-extrabold text-[#0C392F]">Belum Ada Jadwal Aktif</h2>
            <p class="text-sm text-[#5C6862] mt-1">Anda belum memiliki jadwal keberangkatan aktif hari ini.</p>
        </div>
    @else
        {{-- Kartu Detail Armada & Perjalanan Aktif --}}
        <section class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl p-5 sm:p-6 space-y-5">
            <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-[#ECE8DA]">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isCancelled ? 'bg-red-100 text-red-800' : 'bg-[#DFF0E8] text-[#0C5A3E]' }}">
                            {{ $activeSchedule->tripStatusLabel() }}
                        </span>
                        <span class="text-xs font-mono text-[#5C6862]">{{ $activeSchedule->tripCode() }}</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-[#0C392F]">
                        {{ $originName }} &rarr; {{ $destName }}
                    </h2>
                    <p class="text-sm text-[#5C6862] mt-1">
                        {{ $activeSchedule->formattedDepartureDate() }} &middot; Berangkat <strong class="text-[#13201B]">{{ $activeSchedule->formattedDepartureTime() }}</strong>
                    </p>
                </div>

                <div class="bg-[#EEF6F1] border border-[#cde3d7] rounded-xl px-4 py-3 text-right">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-[#0C5A3E]">Kapasitas Kursi Real-Time</span>
                    <strong class="text-lg font-extrabold text-[#0C392F]">
                        Terisi {{ $totalBookedPassengers }} dari {{ $seatCapacity }} kursi
                    </strong>
                    <span class="block text-xs text-[#5C6862]">Tersisa {{ $emptySeats }} kursi kosong</span>
                </div>
            </div>

            {{-- Info Armada --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-[#F6F4EC] border border-[#ECE8DA]">
                    <span class="block text-xs font-semibold text-[#5C6862]">Nama / Tipe Mobil</span>
                    <strong class="block text-sm font-extrabold text-[#13201B] mt-1">
                        {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce' }}
                    </strong>
                    <span class="text-xs text-[#5C6862]">{{ $activeSchedule->vehicle?->type ?? 'Minibus Shuttle' }}</span>
                </div>
                <div class="p-4 rounded-xl bg-[#F6F4EC] border border-[#ECE8DA]">
                    <span class="block text-xs font-semibold text-[#5C6862]">Nomor Polisi</span>
                    <strong class="block text-sm font-extrabold text-[#13201B] mt-1 font-mono">
                        {{ $activeSchedule->vehicle?->plate_number ?? '-' }}
                    </strong>
                    <span class="text-xs text-[#5C6862]">Status unit: {{ ucfirst((string) ($activeSchedule->vehicle?->status ?? 'siap_jalan')) }}</span>
                </div>
                <div class="p-4 rounded-xl bg-[#F6F4EC] border border-[#ECE8DA]">
                    <span class="block text-xs font-semibold text-[#5C6862]">Jam Keberangkatan &amp; Estimasi</span>
                    <strong class="block text-sm font-extrabold text-[#13201B] mt-1">
                        {{ $activeSchedule->formattedDepartureTime() }} &rarr; {{ $activeSchedule->estimatedArrivalTime() }}
                    </strong>
                    <span class="text-xs text-[#5C6862]">Durasi {{ $activeSchedule->route?->duration ?? '2-3 Jam' }}</span>
                </div>
            </div>

            {{-- Tombol Aksi Langsung (Tanpa Approval Admin) --}}
            @if (! $isCancelled)
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('dashboard.driver.schedule.departing', $activeSchedule) }}" class="inline">
                        @csrf
                        <button type="submit"
                                @disabled($isDepartingOrLater)
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-extrabold transition {{ $isDepartingOrLater ? 'bg-[#DFF0E8] text-[#0C5A3E] cursor-not-allowed' : 'bg-[#0C7950] text-white hover:bg-[#0C5A3E] cursor-pointer' }}">
                            @if ($tripKey === 'departing')
                                Sudah Dikonfirmasi Segera Berangkat
                            @elseif ($isDepartingOrLater)
                                Status: {{ $activeSchedule->tripStatusLabel() }}
                            @else
                                Konfirmasi Segera Berangkat
                            @endif
                        </button>
                    </form>

                    @if ($tripKey === 'departing' || $tripKey === 'pickup')
                        <form method="POST" action="{{ route('dashboard.driver.schedule.status', $activeSchedule) }}" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="departed">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-[#0C7950] bg-[#FFFDF8] text-[#0C5A3E] text-sm font-bold hover:bg-[#EEF6F1] transition cursor-pointer">
                                Mulai Jalan (Dalam Perjalanan)
                            </button>
                        </form>
                    @endif

                    @if (! in_array($tripKey, ['departed', 'arrived', 'completed'], true))
                        <button type="button"
                                onclick="document.getElementById('cancelTripBox').classList.toggle('hidden')"
                                class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-red-300 bg-red-50 text-red-700 text-sm font-bold hover:bg-red-100 transition cursor-pointer">
                            Batalkan Perjalanan
                        </button>
                    @endif
                </div>

                {{-- Form Pembatalan Perjalanan (Langsung tanpa approval admin) --}}
                <div id="cancelTripBox" class="{{ $errors->has('cancellation_reason') ? '' : 'hidden' }} mt-4 p-4 sm:p-5 rounded-xl border border-red-200 bg-red-50/70">
                    <h3 class="text-sm font-extrabold text-red-900">Batalkan Perjalanan Sebelum Berangkat</h3>
                    <p class="text-xs text-red-700 mt-1">
                        Pembatalan langsung aktif tanpa validasi admin dan otomatis mengirim notifikasi ke seluruh penumpang terdaftar serta Admin.
                    </p>
                    <form method="POST" action="{{ route('dashboard.driver.schedule.cancel', $activeSchedule) }}" class="mt-3 space-y-3">
                        @csrf
                        <div>
                            <label for="cancellation_reason" class="block text-xs font-bold text-red-900 mb-1">
                                Alasan Kendala Fatal Sebelum Berangkat
                            </label>
                            <textarea id="cancellation_reason"
                                      name="cancellation_reason"
                                      rows="3"
                                      required
                                      minlength="5"
                                      placeholder="Contoh: Mesin mobil gagal starter di garasi dan tidak bisa jalan tepat waktu."
                                      class="w-full rounded-lg border border-red-300 bg-white px-3 py-2 text-sm text-[#13201B] focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('cancellation_reason') }}</textarea>
                            @error('cancellation_reason')
                                <p class="text-xs font-semibold text-red-700 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2.5 rounded-lg bg-red-600 text-white text-xs font-extrabold hover:bg-red-700 transition cursor-pointer">
                                Ya, Batalkan Perjalanan &amp; Kirim Notifikasi
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('cancelTripBox').classList.add('hidden')"
                                    class="px-4 py-2.5 rounded-lg border border-[#DEDAC9] bg-white text-xs font-bold text-[#5C6862] cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
                    <strong>Perjalanan ini telah dibatalkan.</strong>
                    @if ($activeSchedule->cancellation_reason)
                        <p class="mt-1 text-xs">Alasan: {{ $activeSchedule->cancellation_reason }}</p>
                    @endif
                </div>
            @endif
        </section>

        {{-- Rincian Rute Perjalanan: Urutan Titik Jemput & Titik Turun Penumpang --}}
        <section class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[#ECE8DA] flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-extrabold text-[#0C392F]">Rincian Rute, Titik Jemput &amp; Titik Turun</h2>
                    <p class="text-xs text-[#5C6862]">Urutan titik keberangkatan, penjemputan, hingga penurunan masing-masing penumpang.</p>
                </div>
                <a href="{{ route('dashboard.driver.penumpang', ['schedule_id' => $activeSchedule->id]) }}" class="text-xs font-bold text-[#0C7950] underline">
                    Lihat Manifest Penumpang
                </a>
            </div>

            <div class="p-5 sm:p-6 space-y-6">
                {{-- Alur Waypoints --}}
                <ol class="relative border-l-2 border-[#cde3d7] ml-3 space-y-5">
                    @foreach ($waypoints as $wp)
                        <li class="pl-6 relative">
                            <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-[#0C7950] {{ $wp['status'] === 'passed' ? 'bg-[#0C7950]' : 'bg-[#FFFDF8]' }}"></span>
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#0C7950]">{{ $wp['badge'] }}</span>
                                    <h3 class="text-sm font-extrabold text-[#13201B]">{{ $wp['title'] }}</h3>
                                    <p class="text-xs text-[#5C6862]">{{ $wp['subtitle'] }} &middot; {{ $wp['address'] }}</p>
                                </div>
                                <a href="{{ $wp['gmaps_url'] }}" target="_blank" rel="noopener" class="text-xs font-bold text-[#0C7950] hover:underline">
                                    Buka Maps &rarr;
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ol>

                {{-- Daftar Titik Jemput & Turun per Penumpang --}}
                @if ($manifestPassengers->isNotEmpty())
                    <div class="pt-4 border-t border-[#ECE8DA]">
                        <h3 class="text-sm font-extrabold text-[#0C392F] mb-3">Titik Jemput &amp; Turun Penumpang ({{ $manifestPassengers->count() }} orang)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($manifestPassengers as $p)
                                <div class="p-3.5 rounded-xl bg-[#F6F4EC] border border-[#ECE8DA] text-xs space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <strong class="text-sm text-[#13201B]">{{ $p->name }}</strong>
                                        <span class="px-2 py-0.5 rounded-md bg-[#DFF0E8] text-[#0C5A3E] font-bold">Kursi {{ $p->seat_number }}</span>
                                    </div>
                                    <p class="text-[#5C6862]"><strong class="text-[#13201B]">Jemput:</strong> {{ $p->pickup_point }}</p>
                                    <p class="text-[#5C6862]"><strong class="text-[#13201B]">Turun:</strong> {{ $p->dropoff_point }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection
