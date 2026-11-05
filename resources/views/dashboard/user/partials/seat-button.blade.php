{{--
    One seat in the cabin map. Status is carried by the label and a mark, not by colour alone.
    Expects: $seat = ['seat_id', 'seat_number', 'occupancy' => ['status']].
--}}
@php
    $status = $seat['occupancy']['status'] ?? 'available';
    $seatId = $seat['seat_id'];
    $seatNumber = $seat['seat_number'];
@endphp

@if ($status === 'paid' || $status === 'locked')
    <button type="button"
            aria-disabled="true"
            data-seat-disabled="true"
            data-seat-status="{{ $status }}"
            @click="warnOccupiedSeat({{ $seatId }}, @js($seatNumber), @js($status))"
            :class="rejectedSeatId === {{ $seatId }}
                ? 'bg-rose-100 border-rose-600 text-rose-900 ring-2 ring-rose-500/40'
                : 'bg-slate-300 border-slate-400 text-slate-600 hover:bg-slate-400/70'"
            class="w-12 h-12 rounded-lg border-2 flex flex-col items-center justify-center leading-none select-none cursor-not-allowed transition-all duration-150"
            title="Kursi {{ $seatNumber }} {{ $status === 'locked' ? 'sedang ditahan penumpang lain' : 'sudah dipesan orang lain' }}">
        <span class="text-xs font-bold">{{ $seatNumber }}</span>
        <span class="text-[10px]" aria-hidden="true">{{ $status === 'locked' ? 'ditahan' : 'terisi' }}</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, {{ $status === 'locked' ? 'sedang ditahan penumpang lain' : 'sudah terisi' }} (tidak tersedia)</span>
    </button>
@else
    <button type="button"
            data-seat-available="true"
            @click="toggleSeat({{ $seatId }}, @js($seatNumber))"
            :aria-pressed="isSelected({{ $seatId }}).toString()"
            :class="isSelected({{ $seatId }})
                ? 'bg-brand-800 border-brand-900 text-white shadow-sm hover:bg-brand-900'
                : 'bg-white border-brand-600 text-brand-900 hover:bg-brand-100 hover:border-brand-800 hover:text-brand-950 hover:shadow-sm'"
            class="w-12 h-12 rounded-lg border-2 flex flex-col items-center justify-center leading-none font-bold transition-all duration-150">
        <span class="text-sm">{{ $seatNumber }}</span>
        <svg x-show="isSelected({{ $seatId }})" x-cloak class="w-3.5 h-3.5 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 13l4 4L19 7" /></svg>
        <span class="sr-only">Kursi {{ $seatNumber }}</span>
    </button>
@endif
