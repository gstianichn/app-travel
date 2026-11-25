@extends('layouts.driver')

@section('title', 'Jadwal Hari Ini (Semua Armada) - Driver TravelKu')

@section('content')
@php
    $todayIndo = \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<div class="space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-[#0C7950]">{{ $todayIndo }}</p>
            <h1 class="text-2xl font-extrabold text-[#0C392F] mt-0.5">Jadwal Hari Ini (Semua Armada)</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                Daftar seluruh keberangkatan armada hari ini beserta rekan driver yang bertugas untuk koordinasi cepat di lapangan.
            </p>
        </div>

        <span class="px-3 py-1.5 rounded-full bg-[#EEF6F1] border border-[#cde3d7] text-xs font-bold text-[#0C5A3E]">
            {{ $allTodaySchedules->count() }} Jadwal Aktif Hari Ini
        </span>
    </header>

    <section class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
        @if ($allTodaySchedules->isEmpty())
            <div class="p-8 text-center text-sm text-[#5C6862]">
                Belum ada jadwal keberangkatan armada yang tercatat untuk hari ini.
            </div>
        @else
            {{-- Tampilan Tabel (Desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#ECE8DA] bg-[#F6F4EC] text-[11px] font-extrabold uppercase tracking-wider text-[#5C6862]">
                            <th class="py-3.5 px-5">Jam Berangkat</th>
                            <th class="py-3.5 px-5">Armada &amp; Pelat</th>
                            <th class="py-3.5 px-5">Rute Perjalanan</th>
                            <th class="py-3.5 px-5">Driver Bertugas</th>
                            <th class="py-3.5 px-5 text-right">Koordinasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECE8DA] text-sm">
                        @foreach ($allTodaySchedules as $sched)
                            @php
                                $driverName = $sched->driver?->user?->name ?? 'Driver #'.$sched->driver_id;
                                $origin = $sched->route?->origin?->name ?? 'Asal';
                                $dest = $sched->route?->destination?->name ?? 'Tujuan';
                                $phoneClean = $sched->driver_phone_clean ?? '';
                                $waPhone = $sched->driver_wa_phone ?? '';
                            @endphp
                            <tr class="{{ $sched->is_current_driver ? 'bg-[#EEF6F1]/60' : 'hover:bg-[#F6F4EC]/50' }}">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="font-extrabold text-[#0C392F] tabular-nums">{{ $sched->formattedDepartureTime() }}</div>
                                    <span class="inline-block mt-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#DFF0E8] text-[#0C5A3E]">
                                        {{ $sched->tripStatusLabel() }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-[#13201B]">{{ $sched->vehicle?->name ?? 'Minibus Shuttle' }}</div>
                                    <div class="text-xs font-mono text-[#5C6862]">{{ $sched->vehicle?->plate_number ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-[#13201B]">{{ $origin }} &rarr; {{ $dest }}</div>
                                    @if (($sched->active_bookings_count ?? 0) > 0)
                                        <div class="text-xs text-[#0C7950] font-semibold mt-0.5">{{ $sched->active_bookings_count }} pesanan aktif</div>
                                    @endif
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-[#13201B] flex items-center gap-2">
                                        <span>{{ $driverName }}</span>
                                        @if ($sched->is_current_driver)
                                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-[#0C7950] text-white">Anda</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-[#5C6862]">{{ $phoneClean ?: 'Nomor belum diisi' }}</div>
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    @if ($sched->is_current_driver)
                                        <a href="{{ route('dashboard.driver.perjalanan', ['schedule_id' => $sched->id]) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg border border-[#0C7950] text-xs font-bold text-[#0C5A3E] hover:bg-[#EEF6F1] transition">
                                            Kelola Trip
                                        </a>
                                    @else
                                        <div class="inline-flex items-center gap-2">
                                            @if ($phoneClean !== '')
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phoneClean) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-[#DEDAC9] bg-white text-xs font-bold text-[#13201B] hover:bg-[#F6F4EC] transition">
                                                    Telepon
                                                </a>
                                            @endif
                                            @if ($waPhone !== '')
                                                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo '.$driverName.', saya '.$user->name.' (rekan driver TravelKu) ingin koordinasi jadwal hari ini.') }}"
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#0C7950] text-white text-xs font-bold hover:bg-[#0C5A3E] transition">
                                                    WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Kartu (Mobile) --}}
            <div class="md:hidden divide-y divide-[#ECE8DA]">
                @foreach ($allTodaySchedules as $sched)
                    @php
                        $driverName = $sched->driver?->user?->name ?? 'Driver #'.$sched->driver_id;
                        $origin = $sched->route?->origin?->name ?? 'Asal';
                        $dest = $sched->route?->destination?->name ?? 'Tujuan';
                        $phoneClean = $sched->driver_phone_clean ?? '';
                        $waPhone = $sched->driver_wa_phone ?? '';
                    @endphp
                    <div class="p-4 space-y-2.5 {{ $sched->is_current_driver ? 'bg-[#EEF6F1]/60' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <strong class="text-base font-extrabold text-[#0C392F] tabular-nums">{{ $sched->formattedDepartureTime() }}</strong>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-[#DFF0E8] text-[#0C5A3E]">
                                {{ $sched->tripStatusLabel() }}
                            </span>
                        </div>
                        <div class="text-sm font-bold text-[#13201B]">{{ $origin }} &rarr; {{ $dest }}</div>
                        <div class="text-xs text-[#5C6862]">
                            <span>{{ $sched->vehicle?->name ?? 'Minibus Shuttle' }}</span>
                            &middot;
                            <span class="font-mono font-semibold text-[#13201B]">{{ $sched->vehicle?->plate_number ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#ECE8DA]">
                            <div class="text-xs">
                                <span class="text-[#5C6862]">Driver:</span>
                                <strong class="text-[#13201B]">{{ $driverName }}</strong>
                                @if ($sched->is_current_driver)
                                    <span class="ml-1 text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-[#0C7950] text-white">Anda</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if (! $sched->is_current_driver)
                                    @if ($phoneClean !== '')
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phoneClean) }}"
                                           class="px-2.5 py-1.5 rounded-lg border border-[#DEDAC9] bg-white text-xs font-bold text-[#13201B]">
                                            Telepon
                                        </a>
                                    @endif
                                    @if ($waPhone !== '')
                                        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo '.$driverName.', saya '.$user->name.' (rekan driver TravelKu) ingin koordinasi jadwal hari ini.') }}"
                                           target="_blank"
                                           rel="noopener"
                                           class="px-2.5 py-1.5 rounded-lg bg-[#0C7950] text-white text-xs font-bold">
                                            WA
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('dashboard.driver.perjalanan', ['schedule_id' => $sched->id]) }}"
                                       class="px-2.5 py-1.5 rounded-lg border border-[#0C7950] text-xs font-bold text-[#0C5A3E]">
                                        Kelola
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
