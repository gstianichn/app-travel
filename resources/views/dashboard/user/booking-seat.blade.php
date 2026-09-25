@extends('layouts.dashboard')

@section('page-title', 'Pilih Kursi Armada (Seat Map)')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="seatPicker({
    maxSeats: {{ $passengers }},
    unitPrice: {{ $schedule->effectivePrice() }}
})">

    {{-- ===== BREADCRUMB / STEPPER FLOW ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white font-bold" style="background:#087f55;">
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">1</span>
                Pilih Kursi
            </span>
            <span class="text-slate-300">➔</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500">
                <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px]">2</span>
                Data Penumpang & Promo
            </span>
            <span class="text-slate-300">➔</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500">
                <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px]">3</span>
                Pembayaran
            </span>
        </div>

        {{-- Lock timer badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
            <svg class="w-4 h-4 text-amber-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            <span>Reservasi Kursi: <strong>10 Menit</strong> Seat Lock</span>
        </div>
    </div>

    {{-- Error banner jika ada tabrakan lock --}}
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- ===== KONTEN UTAMA: DENAH KURSI & SIDEBAR RINGKASAN ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- KOLOM DENAH KURSI (SEAT MAP) --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                
                {{-- Header Denah --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Denah Kursi Penumpang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Silakan pilih <span class="font-bold text-emerald-800">{{ $passengers }} kursi</span> di denah bawah ini</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                        {{ $schedule->vehicle?->type ?? 'Toyota HiAce Premio' }}
                    </span>
                </div>

                {{-- Legenda Warna Kursi --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md border-2 border-emerald-500 bg-emerald-50"></div>
                        <span class="text-slate-600">Tersedia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md text-white flex items-center justify-center text-[10px]" style="background:#087f55;">✓</div>
                        <span class="text-slate-600 font-semibold">Dipilih</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md bg-amber-200 border border-amber-400 flex items-center justify-center text-[10px]">🔒</div>
                        <span class="text-slate-600">Terkunci</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md bg-slate-300 border border-slate-400 flex items-center justify-center text-[10px]">✕</div>
                        <span class="text-slate-600">Terisi</span>
                    </div>
                </div>

                {{-- Layout Fisik Kabin Minibus HiAce / Van (Standar Kemudi Kanan Indonesia) --}}
                <div class="max-w-xs mx-auto bg-slate-50 rounded-3xl p-6 border-2 border-slate-200 shadow-inner relative">
                    
                    {{-- Bagian Depan Minibus (Kaca Depan & Kabin Supir Setir Kanan) --}}
                    <div class="border-b-2 border-dashed border-slate-300 pb-4 mb-5 text-center">
                        <div class="flex items-center justify-between px-2 mb-2.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                <span>🚪</span> Pintu Kiri
                            </span>
                            <div class="h-1.5 w-24 rounded-full bg-slate-300" title="Kaca Depan Minibus"></div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                                <span>🇮🇩</span> Setir Kanan
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-2">
                            {{-- Kursi 1A (Baris Depan Kiri - Samping Pintu Penumpang) --}}
                            @php
                                $seat1A = null;
                                foreach($occupancyMap as $row) {
                                    foreach($row as $s) {
                                        if ($s['seat_number'] === '1A') {
                                            $seat1A = $s;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            <div class="flex flex-col items-center">
                                @if($seat1A)
                                    @include('dashboard.user.partials.seat-button', ['seat' => $seat1A])
                                @else
                                    <div class="w-14 h-14 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-300 text-xs">
                                        Kosong
                                    </div>
                                @endif
                                <span class="text-[9px] font-semibold text-slate-400 mt-1">Depan Kiri</span>
                            </div>

                            {{-- Lorong Tangga / Pintu Masuk Depan --}}
                            <div class="flex flex-col items-center justify-center px-1">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pintu / Depan</span>
                            </div>

                            {{-- Kursi Kemudi Sopir (Kanan - Standar Kendaraan Indonesia) --}}
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border-2 border-slate-700 flex flex-col items-center justify-center text-amber-400 shadow-sm" title="Kursi Kemudi Sopir (Setir Kanan Indonesia)">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="m12 2v20m10-10H2"/>
                                    </svg>
                                    <span class="text-[9px] font-bold uppercase mt-0.5 text-white">Sopir</span>
                                </div>
                                <span class="text-[9px] font-bold text-slate-500 mt-1">Setir Kanan</span>
                            </div>
                        </div>
                    </div>

                    {{-- Baris Kursi Penumpang (Row 2 dst - Realistis Minibus Indonesia) --}}
                    <div class="space-y-3.5">
                        @foreach($occupancyMap as $rowIndex => $rowSeats)
                            {{-- Lewatkan baris 1 jika sudah ditaruh di depan --}}
                            @if((string) $rowIndex === '1')
                                @continue
                            @endif

                            <div class="flex items-center justify-between px-2">
                                @php
                                    $seatCount = count($rowSeats);
                                @endphp

                                @if($seatCount === 2)
                                    {{-- Kursi Kiri (Sisi Jendela Kiri / Pintu Geser) --}}
                                    <div class="flex items-center gap-2 w-14 justify-center">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[0]])
                                    </div>

                                    {{-- Lorong Tengah --}}
                                    <div class="text-[10px] text-slate-300 font-mono">
                                        R{{ $rowIndex }}
                                    </div>

                                    {{-- Kursi Kanan (Belakang Kemudi Sopir) --}}
                                    <div class="flex items-center gap-2 w-14 justify-center">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[1]])
                                    </div>
                                @elseif($seatCount === 3)
                                    <div class="flex items-center gap-2">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[0]])
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[1]])
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[2]])
                                    </div>
                                @elseif($seatCount >= 4)
                                    <div class="flex items-center gap-1.5">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[0]])
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[1]])
                                    </div>
                                    <div class="text-[10px] text-slate-300 font-mono">
                                        R{{ $rowIndex }}
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[2]])
                                        @include('dashboard.user.partials.seat-button', ['seat' => $rowSeats[3]])
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        @foreach($rowSeats as $st)
                                            @include('dashboard.user.partials.seat-button', ['seat' => $st])
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Bagian Belakang Kabin / Bagasi --}}
                    <div class="mt-6 pt-3 border-t-2 border-dashed border-slate-300 text-center">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Bagasi Luas Belakang</span>
                    </div>

                </div>

            </div>
        </div>

        {{-- KOLOM RINGKASAN PEMESANAN & AKSI LOCK --}}
        <div class="lg:col-span-5 space-y-4">
            
            {{-- Detail Perjalanan Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="font-bold text-slate-900 text-sm">Ringkasan Jadwal</h4>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md" style="background:#e3f5ee; color:#087f55;">
                        Konfirmasi Instan
                    </span>
                </div>

                {{-- Rute & Waktu --}}
                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                            <svg width="14" height="14" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Titik Keberangkatan</p>
                            <p class="font-bold text-slate-800 text-sm">{{ $schedule->route?->origin?->name }}</p>
                            <p class="text-slate-500 text-[11px]">{{ $schedule->route?->origin?->address ?? 'Pool Utama' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-100">
                            <svg width="14" height="14" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <p class="text-slate-400 font-medium">Titik Tujuan</p>
                            <p class="font-bold text-slate-800 text-sm">{{ $schedule->route?->destination?->name }}</p>
                            <p class="text-slate-500 text-[11px]">{{ $schedule->route?->destination?->address ?? 'Pool Tujuan' }}</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-slate-400">Tanggal</p>
                            <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($schedule->departure_date)->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Jam Berangkat</p>
                            <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }} WIB</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Tipe Armada</p>
                            <p class="font-semibold text-slate-800">{{ $schedule->vehicle?->type }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Plat Nomor</p>
                            <p class="font-semibold text-slate-800 font-mono">{{ $schedule->vehicle?->plate_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Konfirmasi Kunci Kursi (Seat Lock) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h4 class="font-bold text-slate-900 text-sm">Kursi Pilihan Anda</h4>

                {{-- Display Tag Kursi Terpilih --}}
                <div>
                    <template x-if="selectedSeats.length > 0">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="seat in selectedSeats" :key="seat.id">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-white font-bold text-xs" style="background:#087f55;">
                                    Kursi <span x-text="seat.number"></span>
                                    <button type="button" @click="toggleSeat(seat.id, seat.number)" class="hover:text-rose-200">✕</button>
                                </span>
                            </template>
                        </div>
                    </template>
                    <template x-if="selectedSeats.length === 0">
                        <p class="text-xs text-amber-600 bg-amber-50 p-2.5 rounded-xl border border-amber-200">
                            Silakan klik kursi di denah sebelah kiri untuk memilih.
                        </p>
                    </template>
                </div>

                {{-- Kalkulasi Harga --}}
                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Tarif per kursi:</span>
                        <span class="font-semibold">{{ $schedule->formattedEffectivePrice() }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Jumlah kursi dipilih:</span>
                        <span class="font-bold" x-text="selectedSeats.length + ' dari ' + maxSeats + ' kursi'"></span>
                    </div>
                    <div class="flex justify-between text-base font-extrabold pt-2 border-t border-slate-100">
                        <span>Estimasi Total:</span>
                        <span style="color:#087f55;" x-text="formatRupiah(selectedSeats.length * unitPrice)"></span>
                    </div>
                </div>

                {{-- Mekanisme Seat Lock Alert --}}
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-[11px] space-y-1">
                    <p class="font-bold text-slate-800">🛡️ Mekanisme Reservasi Sementara (Seat Lock)</p>
                    <p>Setelah menekan tombol di bawah, kursi pilihan Anda akan <strong>dikunci otomatis selama 10 menit</strong> agar tidak diserobot pengguna lain selagi Anda mengisi data penumpang.</p>
                </div>

                {{-- Form submit lock --}}
                <form action="{{ route('dashboard.user.booking.lock-seats') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="passengers" value="{{ $passengers }}">
                    
                    {{-- Hidden input array untuk kursi yang dipilih --}}
                    <template x-for="seat in selectedSeats" :key="seat.id">
                        <input type="hidden" name="seat_ids[]" :value="seat.id">
                    </template>

                    <button type="submit"
                            :disabled="selectedSeats.length !== maxSeats"
                            class="w-full text-white font-bold py-3 px-4 rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2"
                            :style="selectedSeats.length === maxSeats ? 'background:#087f55;' : 'background:#cbd5e1; cursor:not-allowed;'"
                            onmouseover="if(this.getAttribute('disabled') === null) this.style.background='#066b47'"
                            onmouseout="if(this.getAttribute('disabled') === null) this.style.background='#087f55'">
                        <span>Kunci Kursi & Lanjut Isi Data</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </form>

            </div>

        </div>

    </div>

</div>

<script>
function seatPicker(config) {
    return {
        maxSeats: config.maxSeats,
        unitPrice: config.unitPrice,
        selectedSeats: [],

        toggleSeat(id, number) {
            const index = this.selectedSeats.findIndex(s => s.id === id);
            if (index > -1) {
                this.selectedSeats.splice(index, 1);
            } else {
                if (this.selectedSeats.length >= this.maxSeats) {
                    alert('Anda telah memilih maksimal ' + this.maxSeats + ' kursi sesuai pencarian.');
                    return;
                }
                this.selectedSeats.push({ id: id, number: number });
            }
        },

        isSelected(id) {
            return this.selectedSeats.some(s => s.id === id);
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    };
}
</script>
@endsection
