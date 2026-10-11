{{--
    One seat in the cabin map. Status is carried by the label and a mark, not by colour alone.
    Expects: $seat = ['seat_id', 'seat_number', 'occupancy' => ['status']].
--}}
@php
    $status = $seat['occupancy']['status'] ?? 'available';
    $seatId = $seat['seat_id'];
    $seatNumber = $seat['seat_number'];
@endphp

@if ($status === 'paid')
    <span class="w-12 h-12 rounded-lg bg-slate-300 text-slate-700 flex flex-col items-center justify-center leading-none select-none"
          title="Kursi {{ $seatNumber }} sudah terisi">
        <span class="text-xs font-bold">{{ $seatNumber }}</span>
        <span class="text-[10px]" aria-hidden="true">terisi</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, sudah terisi</span>
    </span>
@elseif ($status === 'locked')
    <span class="w-12 h-12 rounded-lg border-2 border-dashed border-amber-700 bg-amber-50 text-amber-900 flex flex-col items-center justify-center leading-none select-none"
          title="Kursi {{ $seatNumber }} sedang ditahan penumpang lain">
        <span class="text-xs font-bold">{{ $seatNumber }}</span>
        <span class="text-[10px]" aria-hidden="true">ditahan</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, sedang ditahan penumpang lain</span>
    </span>
@else
    <button type="button"
            @click="toggleSeat({{ $seatId }}, '{{ $seatNumber }}')"
            :aria-pressed="isSelected({{ $seatId }}).toString()"
            :class="isSelected({{ $seatId }})
                ? 'bg-brand-800 border-brand-800 text-white'
                : 'bg-white border-brand-600 text-brand-900 hover:bg-brand-50'"
            class="w-12 h-12 rounded-lg border-2 flex flex-col items-center justify-center leading-none font-bold transition-colors">
        <span class="text-sm">{{ $seatNumber }}</span>
        <svg x-show="isSelected({{ $seatId }})" x-cloak class="w-3.5 h-3.5 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 13l4 4L19 7" /></svg>
        <span class="sr-only">Kursi {{ $seatNumber }}</span>
    </button>
@endif
