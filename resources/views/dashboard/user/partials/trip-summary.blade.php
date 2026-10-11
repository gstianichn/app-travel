{{--
    The trip being booked, shown at the top of every checkout step.
    Expects: $schedule (with route.origin, route.destination, vehicle).
--}}
@php
    $tripRoute = $schedule->route;
    $departure = $schedule->departureCarbon()->locale('id');
@endphp

<div>
    <p class="flex flex-wrap items-baseline gap-x-3 text-2xl sm:text-3xl font-extrabold text-brand-900">
        <span>{{ $tripRoute?->origin?->name }}</span>
        <span class="text-lg font-semibold text-brand-600">ke</span>
        <span>{{ $tripRoute?->destination?->name }}</span>
    </p>
    <p class="mt-2 text-slate-600">
        {{ $departure->isoFormat('dddd, D MMMM YYYY') }}, berangkat <b class="text-slate-900">{{ $departure->format('H:i') }} WIB</b>,
        perkiraan tiba {{ $schedule->estimatedArrivalTime() }}.
        @if ($schedule->vehicle)
            {{ $schedule->vehicle->type ?: $schedule->vehicle->name }}, pelat {{ $schedule->vehicle->plate_number }}.
        @endif
    </p>
</div>
