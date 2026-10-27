@php
    $frontRow = $seatOccupancyRows['1'] ?? [];
    $cabinRows = collect($seatOccupancyRows)->except('1');

    // Buat index penumpang berdasarkan seat_number untuk lookup cepat
    $passengerBySeat = [];
    foreach ($manifestPassengers as $mp) {
        $passengerBySeat[$mp->seat_number] = $mp;
    }
@endphp

<!-- ================= 5. MELIHAT SEAT MAP (DENAH KURSI KABIN) ================= -->
<div class="card-panel" id="section-seatmap" style="margin-bottom: 24px;">
    <div class="card-panel-header">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                <span>FITUR 5</span>
                <span>•</span>
                <span>DENAH KURSI KABIN</span>
            </div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h14a1 1 0 011 1v9H4V5a1 1 0 011-1zM4 14v4a2 2 0 002 2h12a2 2 0 002-2v-4M7 20v2M17 20v2"/></svg>
                <span>Melihat Seat Map</span>
            </h3>
            <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                Fokus: Denah visual keterisian bangku kabin penumpang, posisi driver, dan status kursi (Terisi / Kosong).
            </p>
        </div>
        <span class="pill-badge" style="font-size:11px;">{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }} ({{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }})</span>
    </div>

    <div class="card-panel-body">
        <div class="grid lg:grid-cols-[minmax(0,1fr)_340px] gap-8 items-start">

            {{-- Kolom Kiri: Denah Kabin Minibus (Sama Persis dengan Akun User) --}}
            <section aria-labelledby="denah-kabin" class="space-y-5">
                <div class="flex flex-wrap items-baseline justify-between gap-3">
                    <h4 id="denah-kabin" class="text-lg font-extrabold text-slate-900 m-0">Denah Kursi Kabin</h4>
                    <ul class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-700 m-0 p-0" style="list-style: none;">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded border-2 border-brand-600 bg-white inline-block" aria-hidden="true"></span>
                            <span style="font-size: 12px; font-weight: 600;">Kosong</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded border-2 border-dashed border-amber-700 bg-amber-50 inline-block" aria-hidden="true"></span>
                            <span style="font-size: 12px; font-weight: 600;">Dipesan (Belum Naik)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded bg-emerald-600 text-white flex items-center justify-center text-xs font-bold" aria-hidden="true">✓</span>
                            <span style="font-size: 12px; font-weight: 600;">Sudah Naik (Boarded)</span>
                        </li>
                    </ul>
                </div>

                @if (empty($seatOccupancyRows))
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8 text-center">
                        <p class="font-bold text-slate-900 m-0">Denah kursi untuk jadwal ini belum tersedia.</p>
                        <p class="mt-1 text-slate-600 text-sm">Armada kendaraan belum ditentukan.</p>
                    </div>
                @else
                    {{-- Kapsul Minibus: Bentuk identik dengan akun user --}}
                    <div class="max-w-sm rounded-[2.5rem_2.5rem_1.25rem_1.25rem] border-4 border-slate-400 bg-white px-6 pt-6 pb-5 mx-auto shadow-sm" style="box-sizing: border-box;">

                        {{-- Baris Depan: Penumpang Depan (Kiri) & Kemudi Sopir (Kanan) --}}
                        <div class="flex items-end justify-around border-b-2 border-dashed border-slate-300 py-2">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex gap-2">
                                    @foreach ($frontRow as $seat)
                                        @include('dashboard.driver.partials.seat-item', [
                                            'seat' => $seat,
                                            'passenger' => $passengerBySeat[$seat['seat_number']] ?? null
                                        ])
                                    @endforeach
                                </div>
                                <span class="text-xs text-slate-500 font-medium">Depan</span>
                            </div>

                            <div class="flex flex-col items-center gap-1">
                                <span class="w-12 h-12 rounded-lg bg-slate-800 text-white flex items-center justify-center shadow-sm" title="Posisi Kemudi Sopir (Setir Kanan)">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="2" /><path d="M12 14v7M10.2 11 3.5 9.5M13.8 11l6.7-1.5" /></svg>
                                </span>
                                <span class="text-xs text-slate-500 font-medium">Sopir</span>
                            </div>
                        </div>

                        {{-- Baris Kabin Penumpang (Kabin Belakang dengan Lorong Tengah) --}}
                        <div class="space-y-3 pt-4">
                            @foreach ($cabinRows as $rowSeats)
                                <div @class([
                                    'flex items-center gap-2',
                                    'justify-between' => count($rowSeats) !== 3,
                                    'justify-around' => count($rowSeats) === 3,
                                ])>
                                    @if (count($rowSeats) >= 4)
                                        <div class="flex gap-2">
                                            @foreach (array_slice($rowSeats, 0, 2) as $seat)
                                                @include('dashboard.driver.partials.seat-item', [
                                                    'seat' => $seat,
                                                    'passenger' => $passengerBySeat[$seat['seat_number']] ?? null
                                                ])
                                            @endforeach
                                        </div>
                                        <div class="flex gap-2">
                                            @foreach (array_slice($rowSeats, 2) as $seat)
                                                @include('dashboard.driver.partials.seat-item', [
                                                    'seat' => $seat,
                                                    'passenger' => $passengerBySeat[$seat['seat_number']] ?? null
                                                ])
                                            @endforeach
                                        </div>
                                    @else
                                        @foreach ($rowSeats as $seat)
                                            @include('dashboard.driver.partials.seat-item', [
                                                'seat' => $seat,
                                                'passenger' => $passengerBySeat[$seat['seat_number']] ?? null
                                            ])
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-4 border-t-2 border-dashed border-slate-300 pt-3 text-center text-xs text-slate-500 font-medium">Belakang</p>
                    </div>
                @endif
            </section>

            {{-- Kolom Kanan: Inspector Kursi & Detail Penumpang Terpilih --}}
            <aside class="rounded-xl border border-slate-300 bg-white p-5 space-y-4 shadow-sm" id="driverSeatInspector" style="min-height: 280px;">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <h4 class="text-base font-extrabold text-slate-900 m-0 flex items-center gap-2">
                        <span id="inspectSeatTitle">Detail Kursi</span>
                    </h4>
                    <span id="inspectSeatBadge" class="ds-badge ds-badge--muted" style="font-size: 11px;">Pilih Kursi</span>
                </div>

                {{-- Konten Dinamis yang diisi via Javascript saat kursi diklik --}}
                <div id="inspectContentDefault" class="text-center py-6 text-slate-500">
                    <div style="font-size: 32px; margin-bottom: 8px;">💺</div>
                    <p class="font-bold text-slate-700 m-0 text-sm">Ketuk salah satu kursi di denah</p>
                    <p class="text-xs text-slate-500 mt-1 m-0">Untuk memeriksa nama penumpang, status absensi, titik jemput, dan status bayar.</p>
                </div>

                <div id="inspectContentDetails" style="display: none;" class="space-y-3">
                    <div style="background: #f8fafc; border-radius: 8px; padding: 12px; border: 1px solid #e2e8f0;">
                        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block;">Nama Penumpang</span>
                        <strong id="inspectPassengerName" style="font-size: 15px; color: #0f172a; display: block; margin-top: 2px;">-</strong>
                        <span id="inspectTicketCode" style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">Kode: -</span>
                    </div>

                    <div style="font-size: 12.5px;" class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-slate-500">Titik Jemput:</span>
                            <strong id="inspectPickup" class="text-right text-slate-800">-</strong>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-slate-500">Titik Turun:</span>
                            <strong id="inspectDropoff" class="text-right text-slate-800">-</strong>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-slate-500">Pembayaran:</span>
                            <span id="inspectPayment" class="font-bold text-slate-800">-</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-slate-500">Bawaan:</span>
                            <span id="inspectBaggage" class="text-right text-slate-700">-</span>
                        </div>
                    </div>

                    {{-- Tombol Hubungi WhatsApp & Telepon --}}
                    <div class="flex gap-2 pt-2 border-t border-slate-100" id="inspectContactBtns">
                        <a id="inspectWaBtn" href="#" target="_blank" class="btn-contact btn-contact-wa" style="flex: 1; justify-content: center; text-decoration: none;">
                            <span>WhatsApp</span>
                        </a>
                        <a id="inspectTelBtn" href="#" class="btn-contact btn-contact-tel" style="flex: 1; justify-content: center; text-decoration: none;">
                            <span>Telepon</span>
                        </a>
                    </div>

                    {{-- Tombol Aksi Check-in --}}
                    <div class="pt-2 border-t border-slate-100" id="inspectActionContainer">
                        <form id="inspectCheckinForm" method="POST" action="" style="display: block;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="boarding_status" id="inspectFormStatus" value="boarded">
                            <input type="hidden" name="mark_paid" id="inspectFormMarkPaid" value="0">
                            <button type="submit" id="inspectSubmitBtn" class="ds-btn ds-btn--primary" style="width: 100%; justify-content: center; font-size: 12.5px;">
                                Konfirmasi Naik (Boarded)
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Ringkasan Cepat Okupansi --}}
                <div style="background: #f1f5f9; border-radius: 8px; padding: 10px 14px; font-size: 11.5px; color: #475569;" class="mt-4">
                    <div class="flex justify-between items-center mb-1">
                        <span>Total Penumpang:</span>
                        <strong class="text-slate-900">{{ $totalBookedPassengers }} Orang</strong>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span>Sudah Naik Kabin:</span>
                        <strong class="text-emerald-700">{{ $boardedCount }} Penumpang</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Kursi Kosong:</span>
                        <strong class="text-slate-700">{{ $emptySeats }} Kursi</strong>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
