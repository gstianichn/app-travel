@extends('layouts.dashboard')

@section('page-title', 'Cari jadwal')

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name;
    $passengerCount = (int) ($passengerCount ?? request('passengers', 1));
    $searchDate = $searchPerformed && ! $errors->any() && request('departure_date')
        ? \Illuminate\Support\Carbon::parse(request('departure_date'))->locale('id')
        : null;
    $hasRoute = $searchDate && $originLoc && $destLoc;
@endphp

@section('content')
<div class="space-y-10">

    @include('dashboard.user.partials.flash')

    @include('components.search-ticket', [
        'heading' => $hasRoute ? 'Ubah pencarian' : 'Cari jadwal',
        'action' => route('dashboard.user.search'),
        'selected' => [
            'origin' => request('origin'),
            'destination' => request('destination'),
            'departure_date' => request('departure_date'),
            'passengers' => $passengerCount,
        ],
    ])

    @if ($hasRoute)
        <section aria-labelledby="hasil" class="space-y-4">
            @if ($schedules->isNotEmpty())
                <div>
                    <h2 id="hasil" class="text-2xl font-extrabold text-brand-900">
                        {{ $schedules->count() }} keberangkatan {{ $cityOf($originLoc) }} ke {{ $cityOf($destLoc) }}
                    </h2>
                    <p class="text-slate-600">{{ $searchDate->isoFormat('dddd, D MMMM YYYY') }}, {{ $passengerCount }} orang. Jam tiba adalah perkiraan.</p>
                </div>

                <ol class="divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($schedules as $schedule)
                        @php
                            $seatsLeft = (int) $schedule->available_seats;
                            $facilities = array_slice((array) ($schedule->vehicle?->facilities ?? []), 0, 3);
                        @endphp
                        <li class="grid gap-4 py-5 sm:grid-cols-[120px_minmax(0,1fr)_auto] sm:items-center">
                            <p class="text-2xl font-extrabold text-slate-900 tabular-nums">
                                {{ $schedule->departureCarbon()->format('H:i') }}
                                <span class="block text-base font-semibold text-slate-500">tiba {{ str_replace(' WIB', '', $schedule->estimatedArrivalTime()) }}</span>
                            </p>

                            <div class="min-w-0">
                                <p class="font-bold text-slate-900">
                                    {{ $schedule->route?->origin?->name }} <span class="font-normal text-slate-500">ke</span> {{ $schedule->route?->destination?->name }}
                                </p>
                                <p class="text-slate-600">
                                    {{ $schedule->route?->duration }}@if ($schedule->vehicle), {{ $schedule->vehicle->type ?: $schedule->vehicle->name }}@endif
                                    @if ($facilities)
                                        <span class="text-slate-500">({{ implode(', ', $facilities) }})</span>
                                    @endif
                                </p>
                                <p @class(['mt-1 text-sm font-semibold', 'text-amber-900' => $seatsLeft <= 3, 'text-slate-600' => $seatsLeft > 3])>
                                    {{ $seatsLeft }} kursi tersisa
                                </p>
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                <p class="text-xl font-extrabold text-slate-900">
                                    {{ $schedule->formattedEffectivePrice() }}
                                    <span class="block text-sm font-normal text-slate-500 sm:text-right">per orang</span>
                                </p>
                                <a href="{{ route('dashboard.user.booking.pilih-kursi', ['schedule' => $schedule->id, 'passengers' => $passengerCount]) }}" class="lp-btn lp-btn--green">Pilih kursi</a>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @elseif ($routeConnected)
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8">
                    <h2 id="hasil" class="text-xl font-extrabold text-slate-900">Tidak ada keberangkatan dengan {{ $passengerCount }} kursi kosong</h2>
                    <p class="mt-1 text-slate-600">
                        Jadwal {{ $searchDate->isoFormat('D MMMM') }} sudah penuh atau sudah berangkat. Coba tanggal lain, atau kurangi jumlah penumpang.
                    </p>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8 space-y-4">
                    <div>
                        <h2 id="hasil" class="text-xl font-extrabold text-slate-900">Belum ada rute langsung dari {{ $cityOf($originLoc) }} ke {{ $cityOf($destLoc) }}</h2>
                        @unless ($transitHub)
                            <p class="mt-1 text-slate-600">Rute yang sudah jalan ada di <a href="{{ route('home') }}#rute" class="font-semibold text-brand-700 underline underline-offset-2">halaman utama</a>.</p>
                        @endunless
                    </div>

                    @if ($transitHub)
                        <div>
                            <p class="text-slate-700">Kamu bisa sampai lewat {{ $cityOf($transitHub) }} dengan dua perjalanan:</p>
                            <ol class="mt-3 grid sm:grid-cols-2 gap-3">
                                @foreach ([[$originLoc, $transitHub], [$transitHub, $destLoc]] as $legIndex => [$legFrom, $legTo])
                                    <li>
                                        <a href="{{ route('dashboard.user.search', ['origin' => $legFrom->id, 'destination' => $legTo->id, 'departure_date' => $searchDate->toDateString(), 'passengers' => $passengerCount]) }}"
                                           class="block rounded-lg border-2 border-brand-700 px-4 py-3 hover:bg-brand-50">
                                            <span class="block text-sm font-semibold text-slate-600">Perjalanan {{ $legIndex + 1 }}</span>
                                            <span class="font-bold text-brand-900">{{ $cityOf($legFrom) }} ke {{ $cityOf($legTo) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
