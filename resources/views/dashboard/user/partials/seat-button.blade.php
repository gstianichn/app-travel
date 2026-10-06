@php
    $status = $seat['occupancy']['status'] ?? 'available';
    $seatId = $seat['seat_id'];
    $seatNum = $seat['seat_number'];
@endphp

@if ($status === 'paid')
    {{-- Kursi Terisi / Lunas --}}
    <button type="button" disabled
            title="Kursi {{ $seatNum }} (Sudah Terisi)"
            class="w-12 h-12 rounded-xl bg-slate-200 border border-slate-300 flex flex-col items-center justify-center text-slate-400 cursor-not-allowed select-none shadow-2xs">
        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        <span class="text-[10px] font-bold mt-0.5">{{ $seatNum }}</span>
    </button>
@elseif ($status === 'locked')
    {{-- Kursi Terkunci Sementara (Seat Lock Pengguna Lain) --}}
    <button type="button" disabled
            title="Kursi {{ $seatNum }} (Sedang Dikunci Sementara oleh Pengguna Lain)"
            class="w-12 h-12 rounded-xl bg-amber-100 border-2 border-amber-400 flex flex-col items-center justify-center text-amber-800 cursor-not-allowed select-none shadow-2xs">
        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span class="text-[10px] font-bold mt-0.5">{{ $seatNum }}</span>
    </button>
@else
    {{-- Kursi Tersedia / Bebas Dipilih --}}
    <button type="button"
            @click="toggleSeat({{ $seatId }}, '{{ $seatNum }}')"
            :class="isSelected({{ $seatId }})
                ? 'text-white border-2 border-emerald-700 shadow-md scale-105'
                : 'bg-emerald-50/80 border-2 border-emerald-500 text-emerald-900 hover:bg-emerald-100 hover:border-emerald-600 shadow-2xs'"
            :style="isSelected({{ $seatId }}) ? 'background:#087f55;' : ''"
            title="Kursi {{ $seatNum }} (Tersedia)"
            class="w-12 h-12 rounded-xl flex flex-col items-center justify-center transition-all duration-150 cursor-pointer select-none">
        <template x-if="isSelected({{ $seatId }})">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        </template>
        <template x-if="!isSelected({{ $seatId }})">
            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </template>
        <span class="text-[10px] font-extrabold mt-0.5">{{ $seatNum }}</span>
    </button>
@endif
