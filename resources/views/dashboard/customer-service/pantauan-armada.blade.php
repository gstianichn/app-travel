@extends('layouts.customer-service')

@section('title', 'Pantauan Armada Hari Ini - Customer Service TravelKu')

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name ?: '-';

    $tripStatusBadge = fn (string $tone): string => match ($tone) {
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'blue' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-[#DFF0E8] text-[#0C5A3E] border-[#B8DFC9]',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

@section('content')
<div class="space-y-6 text-[#13201B]">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Pantauan Armada Hari Ini</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                {{ now()->translatedFormat('l, d F Y') }} &middot; Pantau status perjalanan armada (view-only), indikator kendala sopir, dan kontak cepat sopir.
            </p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full border border-[#DEDAC9] bg-[#FFFDF8] px-3 py-1 text-xs font-bold text-[#0C392F]">
            {{ $fleetSummary['total'] }} Jadwal Hari Ini
        </span>
    </header>

    {{-- Ringkasan Status Armada --}}
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-4 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#5C6862]">Belum Berangkat</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-700 tabular-nums">{{ $fleetSummary['belum_berangkat'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Penjemputan</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-700 tabular-nums">{{ $fleetSummary['proses_penjemputan'] }}</p>
        </div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50/50 p-4 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-800">Dalam Perjalanan</p>
            <p class="mt-1 text-2xl font-extrabold text-sky-700 tabular-nums">{{ $fleetSummary['dalam_perjalanan'] }}</p>
        </div>
        <div class="rounded-2xl border border-[#B8DFC9] bg-[#EEF6F1] p-4 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#0C5A3E]">Selesai</p>
            <p class="mt-1 text-2xl font-extrabold text-[#0C7950] tabular-nums">{{ $fleetSummary['selesai'] }}</p>
        </div>
        <div class="col-span-2 sm:col-span-1 rounded-2xl border {{ $fleetSummary['active_alerts'] > 0 ? 'border-rose-300 bg-rose-50' : 'border-[#DEDAC9] bg-[#FFFDF8]' }} p-4 shadow-xs">
            <p class="text-[11px] font-bold uppercase tracking-wider {{ $fleetSummary['active_alerts'] > 0 ? 'text-rose-700' : 'text-[#5C6862]' }}">Peringatan Sopir</p>
            <p class="mt-1 text-2xl font-extrabold {{ $fleetSummary['active_alerts'] > 0 ? 'text-rose-700' : 'text-[#0C392F]' }} tabular-nums">{{ $fleetSummary['active_alerts'] }}</p>
        </div>
    </section>

    {{-- Daftar Jadwal Armada Hari Ini --}}
    <section class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] overflow-hidden shadow-xs">
        @if ($fleetSchedules->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm font-bold text-[#0C392F]">Tidak ada jadwal keberangkatan hari ini.</p>
                <p class="text-xs text-[#5C6862] mt-1">Jadwal operasional armada hari ini akan muncul otomatis di sini.</p>
            </div>
        @else
            {{-- Tampilan Tabel (Desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#ECE8DA] bg-[#F6F4EC] text-[11px] font-extrabold uppercase tracking-wider text-[#5C6862]">
                            <th class="py-3.5 px-5">Jam &amp; Rute</th>
                            <th class="py-3.5 px-5">Armada</th>
                            <th class="py-3.5 px-5">Status Perjalanan</th>
                            <th class="py-3.5 px-5">Peringatan Lapangan</th>
                            <th class="py-3.5 px-5">Sopir &amp; Kontak Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECE8DA] text-sm">
                        @foreach ($fleetSchedules as $item)
                            @php
                                $sched = $item->schedule;
                                $latestIncident = $item->active_incidents->first();
                            @endphp
                            <tr class="{{ $item->has_emergency ? 'bg-rose-50/50' : 'hover:bg-[#F6F4EC]/50' }}">
                                <td class="py-4 px-5 align-top">
                                    <div class="font-extrabold text-[#0C392F] tabular-nums">{{ $sched->formattedDepartureTime() }}</div>
                                    <div class="text-xs font-bold text-[#13201B] mt-0.5">
                                        {{ $cityOf($sched->route?->origin) }} &rarr; {{ $cityOf($sched->route?->destination) }}
                                    </div>
                                    <div class="text-[11px] text-[#5C6862]">
                                        {{ $sched->route?->origin?->name ?? '-' }} ke {{ $sched->route?->destination?->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-4 px-5 align-top">
                                    <div class="font-bold text-[#13201B]">{{ $sched->vehicle?->plate_number ?? 'Belum Ditugaskan' }}</div>
                                    <div class="text-xs text-[#5C6862]">{{ $sched->vehicle?->name ?? '-' }}</div>
                                    <div class="text-[11px] text-[#5C6862] mt-0.5">Sisa {{ $sched->available_seats ?? 0 }} kursi</div>
                                </td>

                                <td class="py-4 px-5 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $tripStatusBadge($item->trip_tone) }}">
                                        {{ $item->trip_label }}
                                    </span>
                                </td>

                                <td class="py-4 px-5 align-top">
                                    @if ($latestIncident)
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 rounded-full border {{ $item->has_emergency ? 'border-rose-300 bg-rose-100 text-rose-800' : 'border-amber-300 bg-amber-100 text-amber-900' }} px-2.5 py-0.5 text-[11px] font-extrabold">
                                                &#9888; {{ $latestIncident->categoryLabel() }}
                                            </span>
                                            <p class="text-xs text-[#4B5D55] max-w-xs line-clamp-2">{{ $latestIncident->description }}</p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-[#B8DFC9] bg-[#EEF6F1] px-2.5 py-0.5 text-[11px] font-bold text-[#0C5A3E]">
                                            Normal
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-5 align-top">
                                    <div class="font-bold text-[#13201B]">{{ $item->driver_name }}</div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        @if ($item->wa_url)
                                            <a href="{{ $item->wa_url }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="inline-flex items-center gap-1 rounded-lg bg-[#0C7950] px-3 py-1.5 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                                                WhatsApp
                                            </a>
                                        @endif
                                        @if ($item->tel_phone !== '')
                                            <a href="tel:{{ $item->tel_phone }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-[#DEDAC9] bg-white px-3 py-1.5 text-xs font-bold text-[#13201B] hover:bg-[#F6F4EC] transition">
                                                Telepon
                                            </a>
                                        @endif
                                        @if (! $item->wa_url && $item->tel_phone === '')
                                            <span class="text-xs text-[#5C6862]">Kontak belum tersedia</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Kartu (Mobile) --}}
            <div class="md:hidden divide-y divide-[#ECE8DA]">
                @foreach ($fleetSchedules as $item)
                    @php
                        $sched = $item->schedule;
                        $latestIncident = $item->active_incidents->first();
                    @endphp
                    <div class="p-4 space-y-3 {{ $item->has_emergency ? 'bg-rose-50/50' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-sm font-extrabold text-[#0C392F] tabular-nums">{{ $sched->formattedDepartureTime() }}</div>
                                <div class="text-xs font-bold text-[#13201B]">
                                    {{ $cityOf($sched->route?->origin) }} &rarr; {{ $cityOf($sched->route?->destination) }}
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $tripStatusBadge($item->trip_tone) }}">
                                {{ $item->trip_label }}
                            </span>
                        </div>

                        <div class="text-xs text-[#5C6862] space-y-1">
                            <p><strong class="text-[#13201B]">Armada:</strong> {{ $sched->vehicle?->plate_number ?? '-' }} ({{ $sched->vehicle?->name ?? '-' }})</p>
                            <p><strong class="text-[#13201B]">Sopir:</strong> {{ $item->driver_name }}</p>
                        </div>

                        @if ($latestIncident)
                            <div class="rounded-xl border {{ $item->has_emergency ? 'border-rose-300 bg-rose-100/80 text-rose-900' : 'border-amber-300 bg-amber-50 text-amber-900' }} p-2.5 text-xs">
                                <p class="font-extrabold">&#9888; {{ $latestIncident->categoryLabel() }}</p>
                                <p class="mt-0.5">{{ $latestIncident->description }}</p>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            @if ($item->wa_url)
                                <a href="{{ $item->wa_url }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="inline-flex items-center gap-1 rounded-lg bg-[#0C7950] px-3 py-1.5 text-xs font-bold text-white">
                                    WhatsApp Sopir
                                </a>
                            @endif
                            @if ($item->tel_phone !== '')
                                <a href="tel:{{ $item->tel_phone }}"
                                   class="inline-flex items-center gap-1 rounded-lg border border-[#DEDAC9] bg-white px-3 py-1.5 text-xs font-bold text-[#13201B]">
                                    Telepon
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
