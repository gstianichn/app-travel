@extends('layouts.dashboard')

@section('page-title', 'Pilih kursi')

@php
    $frontRow = $occupancyMap['1'] ?? [];
    $cabinRows = collect($occupancyMap)->except('1');
@endphp

@section('content')
<div class="space-y-8" x-data="seatPicker({ maxSeats: {{ $schedule->available_seats }}, unitPrice: {{ $schedule->effectivePrice() }} })">

    @include('dashboard.user.partials.booking-steps', ['current' => 1])

    @include('dashboard.user.partials.flash')

    <header class="space-y-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900"
            x-text="selectedSeats.length > 0
                ? 'Pilih kursi (' + selectedSeats.length + ' dipilih)'
                : 'Pilih kursi'"
        >Pilih kursi</h1>
        @include('dashboard.user.partials.trip-summary', ['schedule' => $schedule])
    </header>

    <div class="grid lg:grid-cols-[minmax(0,1fr)_340px] gap-8 items-start">

        {{-- Cabin: drawn front to back, driver on the right like Indonesian minibuses --}}
        <section aria-labelledby="denah" class="space-y-5">
            <div class="flex flex-wrap items-baseline justify-between gap-3">
                <h2 id="denah" class="text-xl font-extrabold text-brand-900">Denah kursi</h2>
                <ul class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-700">
                    <li class="flex items-center gap-2"><span class="w-5 h-5 rounded border-2 border-brand-600 bg-white" aria-hidden="true"></span> Kosong</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 rounded bg-brand-800" aria-hidden="true"></span> Pilihanmu</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 rounded border-2 border-dashed border-amber-700 bg-amber-50" aria-hidden="true"></span> Ditahan orang lain</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 rounded bg-slate-300" aria-hidden="true"></span> Terisi</li>
                </ul>
            </div>

            @if (empty($occupancyMap))
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8">
                    <p class="font-bold text-slate-900">Denah kursi untuk jadwal ini belum tersedia.</p>
                    <p class="mt-1 text-slate-600">Kendaraannya belum ditentukan admin. Pilih jam lain atau coba lagi nanti.</p>
                    <a href="{{ route('search') }}" class="mt-4 inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Kembali ke jadwal</a>
                </div>
            @else
                <div class="max-w-sm rounded-[2.5rem_2.5rem_1.25rem_1.25rem] border-4 border-slate-400 bg-white px-6 pt-6 pb-5">
                    <div class="flex items-end justify-between border-b-2 border-dashed border-slate-300 pb-4">
                        <div class="flex flex-col items-center gap-1">
                            <div class="flex gap-2">
                                @foreach ($frontRow as $seat)
                                    @include('dashboard.user.partials.seat-button', ['seat' => $seat])
                                @endforeach
                            </div>
                            <span class="text-xs text-slate-500">Depan</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <span class="w-12 h-12 rounded-lg bg-slate-800 text-white flex items-center justify-center" title="Sopir">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="2" /><path d="M12 14v7M10.2 11 3.5 9.5M13.8 11l6.7-1.5" /></svg>
                            </span>
                            <span class="text-xs text-slate-500">Sopir</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-4">
                        @foreach ($cabinRows as $rowNumber => $rowSeats)
                            <div class="grid grid-cols-4 gap-2 items-center justify-items-center">
                                @foreach ($rowSeats as $seat)
                                    <div style="grid-column: {{ $seat['seat_column'] }};" class="w-full flex justify-center">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $seat])
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- Area Bagasi Belakang Kabin Armada --}}
                        <div class="mt-4 rounded-xl bg-amber-400 text-amber-950 font-black text-xs text-center py-3.5 tracking-widest uppercase border-2 border-amber-500 shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>FULL BAGASI</span>
                        </div>
                    </div>

                    <p class="mt-4 border-t-2 border-dashed border-slate-300 pt-3 text-center text-xs text-slate-500">Belakang</p>
                </div>
            @endif
        </section>

        {{-- Summary sits beside the map on desktop and under it on a phone --}}
        <aside class="lg:sticky lg:top-24 rounded-xl border border-slate-300 bg-white p-6 space-y-5" aria-labelledby="kursi-pilihan">
            <h2 id="kursi-pilihan" class="text-xl font-extrabold text-brand-900">Kursi pilihanmu</h2>

            <p class="text-slate-700" x-show="selectedSeats.length === 0">Ketuk kursi kosong di denah. Kamu bisa memilih satu atau lebih kursi.</p>
            <ul class="flex flex-wrap gap-2" x-show="selectedSeats.length > 0" x-cloak>
                <template x-for="seat in selectedSeats" :key="seat.id">
                    <li>
                        <button type="button" @click="toggleSeat(seat.id, seat.number)"
                                class="inline-flex items-center gap-2 min-h-11 pl-3 pr-2 rounded-lg bg-brand-800 text-white font-bold"
                                :aria-label="'Batalkan kursi ' + seat.number">
                            <span x-text="seat.number"></span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12" /></svg>
                        </button>
                    </li>
                </template>
            </ul>
            <p class="text-sm font-semibold text-amber-900" role="status" aria-live="polite" x-text="limitMessage"></p>

            <dl class="space-y-2 border-t border-slate-200 pt-4">
                <div class="flex justify-between gap-4 text-slate-600">
                    <dt>Harga per kursi</dt>
                    <dd>{{ $schedule->formattedEffectivePrice() }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-slate-600">
                    <dt>Kursi dipilih</dt>
                    <dd x-text="selectedSeats.length + ' kursi'"></dd>
                </div>
                <div class="flex justify-between gap-4 text-lg font-extrabold text-slate-900">
                    <dt>Total</dt>
                    <dd x-text="formatRupiah(selectedSeats.length * unitPrice)"></dd>
                </div>
            </dl>

            <form action="{{ route('dashboard.user.booking.lock-seats') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                {{-- passengers dihitung dari jumlah kursi yang dipilih, diisi dinamis oleh Alpine --}}
                <input type="hidden" name="passengers" :value="selectedSeats.length">
                <template x-for="seat in selectedSeats" :key="seat.id">
                    <input type="hidden" name="seat_ids[]" :value="seat.id">
                </template>

                <button type="submit" class="lp-btn lp-btn--primary w-full" :disabled="selectedSeats.length === 0">
                    <span x-text="selectedSeats.length > 0
                        ? 'Tahan ' + selectedSeats.length + ' kursi dan isi data'
                        : 'Pilih minimal 1 kursi'"
                    >Tahan kursi dan isi data</span>
                </button>
                <p class="text-sm text-slate-600">Kursinya kami tahan 15 menit untuk kamu, jadi tidak bisa diambil penumpang lain selama kamu mengisi data dan membayar.</p>
            </form>
        </aside>
    </div>
</div>

<script>
function seatPicker(config) {
    return {
        maxSeats: config.maxSeats,
        unitPrice: config.unitPrice,
        selectedSeats: [],
        limitMessage: '',

        toggleSeat(id, number) {
            const index = this.selectedSeats.findIndex((seat) => seat.id === id);
            this.limitMessage = '';

            if (index > -1) {
                this.selectedSeats.splice(index, 1);
                return;
            }

            if (this.selectedSeats.length >= this.maxSeats) {
                this.limitMessage = `Semua ${this.maxSeats} kursi yang tersedia sudah dipilih.`;
                return;
            }

            this.selectedSeats.push({ id, number });
        },

        isSelected(id) {
            return this.selectedSeats.some((seat) => seat.id === id);
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },
    };
}
</script>
@endsection
