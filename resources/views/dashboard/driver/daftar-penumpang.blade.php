@extends('layouts.driver')

@section('title', 'Daftar Penumpang - Driver TravelKu')

@section('content')
<div class="space-y-6">
    {{-- Header & Pemilih Jadwal --}}
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Daftar Penumpang</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                {{ $activeSchedule->route?->origin?->name ?? 'Loket Asal' }} &rarr; {{ $activeSchedule->route?->destination?->name ?? 'Loket Tujuan' }}
                &middot; {{ $activeSchedule->vehicle?->name ?? 'Minibus Shuttle' }} ({{ $activeSchedule->vehicle?->plate_number ?? '-' }})
                &middot; Terisi <strong class="text-[#0C392F]">{{ $totalBookedPassengers }}/{{ $seatCapacity }}</strong> kursi
            </p>
        </div>

        @if (isset($availableDriverSchedules) && $availableDriverSchedules->count() > 1)
            <div class="flex items-center gap-2">
                <label for="manifestScheduleSelect" class="text-xs font-bold text-[#5C6862]">Ganti Jadwal:</label>
                <select id="manifestScheduleSelect"
                        onchange="if(this.value) window.location.href = '{{ route('dashboard.driver.penumpang') }}?schedule_id=' + this.value"
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

    {{-- Tabel Daftar Penumpang --}}
    <section class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-[#ECE8DA] flex items-center justify-between gap-2">
            <h2 class="text-base font-extrabold text-[#0C392F]">Manifest Penumpang</h2>
            <span class="px-3 py-1 rounded-full bg-[#EEF6F1] text-[#0C5A3E] text-xs font-bold">
                {{ $manifestPassengers->count() }} Penumpang
            </span>
        </div>

        @if ($manifestPassengers->isEmpty())
            <div class="p-8 text-center text-sm text-[#5C6862]">
                Belum ada penumpang terdaftar pada jadwal ini.
            </div>
        @else
            {{-- Tampilan Tabel (Desktop & Tablet) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#ECE8DA] bg-[#F6F4EC] text-[11px] font-extrabold uppercase tracking-wider text-[#5C6862]">
                            <th class="py-3.5 px-5">Kode QR</th>
                            <th class="py-3.5 px-5">Nama</th>
                            <th class="py-3.5 px-5">Kursi</th>
                            <th class="py-3.5 px-5">Tujuan</th>
                            <th class="py-3.5 px-5">Berangkat Jam</th>
                            <th class="py-3.5 px-5">Sampai Jam</th>
                            <th class="py-3.5 px-5">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECE8DA] text-sm">
                        @foreach ($manifestPassengers as $p)
                            <tr class="hover:bg-[#F6F4EC]/50">
                                <td class="py-4 px-5 font-mono text-xs font-bold text-[#0C392F] whitespace-nowrap">
                                    {{ $p->qr_code }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-[#13201B]">{{ $p->name }}</div>
                                    @if (! empty($p->phone) && $p->phone !== '-')
                                        <div class="mt-0.5 font-mono text-xs font-semibold text-[#5C6862]">
                                            {{ $p->phone }}
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1 text-xs">
                                        @if (! empty($p->phone) && $p->phone !== '-')
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $p->phone) }}" class="text-[#5C6862] hover:text-[#0C7950] font-semibold">
                                                Telepon
                                            </a>
                                        @endif
                                        @if (! empty($p->wa_phone))
                                            @if (! empty($p->phone) && $p->phone !== '-')
                                                <span class="text-[#DEDAC9]">&middot;</span>
                                            @endif
                                            <a href="https://wa.me/{{ $p->wa_phone }}?text={{ urlencode('Halo '.$p->name.', saya driver TravelKu untuk jadwal '.$activeSchedule->formattedDepartureTime().'.') }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="text-[#0C7950] hover:underline font-semibold">
                                                WhatsApp
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#0C392F] text-white text-xs font-extrabold">
                                        Kursi {{ $p->seat_number }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-[#13201B]">
                                    {{ $p->dropoff_point }}
                                </td>
                                <td class="py-4 px-5 font-bold text-[#0C392F] whitespace-nowrap tabular-nums">
                                    {{ $activeSchedule->formattedDepartureTime() }}
                                </td>
                                <td class="py-4 px-5 font-semibold text-[#5C6862] whitespace-nowrap tabular-nums">
                                    {{ $activeSchedule->estimatedArrivalTime() }}
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    @if ($p->is_paid)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#DFF0E8] text-[#0C5A3E] text-xs font-bold">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Ringkas (Mobile) --}}
            <div class="md:hidden divide-y divide-[#ECE8DA]">
                @foreach ($manifestPassengers as $p)
                    <div class="p-4 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono font-bold text-[#0C392F]">{{ $p->qr_code }}</span>
                            @if ($p->is_paid)
                                <span class="px-2.5 py-0.5 rounded-full bg-[#DFF0E8] text-[#0C5A3E] font-bold">Lunas</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">Belum Lunas</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <strong class="text-sm font-extrabold text-[#13201B]">{{ $p->name }}</strong>
                            <span class="px-2 py-0.5 rounded-md bg-[#0C392F] text-white font-bold">Kursi {{ $p->seat_number }}</span>
                        </div>

                        <div class="text-[#5C6862] space-y-1">
                            @if (! empty($p->phone) && $p->phone !== '-')
                                <p><strong class="text-[#13201B]">No. HP / WA:</strong> <span class="font-mono font-semibold">{{ $p->phone }}</span></p>
                            @endif
                            <p><strong class="text-[#13201B]">Tujuan:</strong> {{ $p->dropoff_point }}</p>
                            <p>
                                <strong class="text-[#13201B]">Berangkat:</strong> {{ $activeSchedule->formattedDepartureTime() }}
                                &middot;
                                <strong class="text-[#13201B]">Sampai:</strong> {{ $activeSchedule->estimatedArrivalTime() }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            @if (! empty($p->phone) && $p->phone !== '-')
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $p->phone) }}"
                                   class="px-3 py-1.5 rounded-lg border border-[#DEDAC9] bg-white font-bold text-[#13201B]">
                                    Telepon
                                </a>
                            @endif
                            @if (! empty($p->wa_phone))
                                <a href="https://wa.me/{{ $p->wa_phone }}?text={{ urlencode('Halo '.$p->name.', saya driver TravelKu untuk jadwal '.$activeSchedule->formattedDepartureTime().'.') }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="px-3 py-1.5 rounded-lg bg-[#0C7950] text-white font-bold">
                                    WhatsApp
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
