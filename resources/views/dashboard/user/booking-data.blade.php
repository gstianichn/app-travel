@extends('layouts.dashboard')

@section('page-title', 'Pengisian Data & Penerapan Promo')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="bookingDataHandler({
    remainingSeconds: {{ $remainingSeconds }},
    bookingId: {{ $booking->id }},
    subtotal: {{ (float) $booking->subtotal }},
    csrfToken: '{{ csrf_token() }}'
})">

    {{-- ===== STEPPER FLOW & COUNTDOWN SEAT LOCK ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</span>
                Pilih Kursi
            </span>
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white font-bold" style="background:#087f55;">
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">2</span>
                Data Penumpang & Promo
            </span>
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500">
                <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px]">3</span>
                Pembayaran
            </span>
        </div>

        {{-- Seat Lock Countdown Timer --}}
        <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-bold shadow-2xs">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
            <span>Sisa Waktu Seat Lock:</span>
            <span class="font-mono text-sm text-amber-700 bg-white px-2 py-0.5 rounded-md border border-amber-200" x-text="formatTimer(remainingSeconds)"></span>
        </div>
    </div>

    {{-- Form Data Penumpang & Pembayaran --}}
    <form action="{{ route('dashboard.user.booking.save-passenger', $booking) }}" method="POST" id="form-booking-data" @submit="validateBeforeSubmit($event)">
        @csrf
        <input type="hidden" name="payment_channel" :value="selectedChannel">
        <input type="hidden" name="payment_category" :value="activeCategory">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- KOLOM KIRI: DATA PEMESAN & DATA PENUMPANG --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- 1. DATA PEMESAN (KONTAK UTAMA) --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#e3f5ee;">
                                <svg width="15" height="15" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">Data Pemesan (Kontak Utama)</h3>
                        </div>
                        <span class="text-[11px] text-slate-400">E-Ticket QR akan dikirim ke kontak ini</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Nama Lengkap Pemesan *</label>
                            <input type="text" name="booker_name" required value="{{ old('booker_name', auth()->user()->name) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Nomor WhatsApp / HP *</label>
                            <input type="tel" name="booker_phone" required value="{{ old('booker_phone', auth()->user()->phone ?? '081234567894') }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1.5">Alamat Email *</label>
                            <input type="email" name="booker_email" required value="{{ old('booker_email', auth()->user()->email) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none transition focus:border-emerald-600">
                        </div>
                    </div>
                </div>

                {{-- 2. DATA PENUMPANG (SESUAI KURSI YANG DIKUNCI) --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-slate-100">
                                <svg width="15" height="15" fill="none" stroke="#475569" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Detail Penumpang</h3>
                                <p class="text-[11px] text-slate-400">Isi nama sesuai kartu identitas untuk verifikasi boarding</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                            {{ $booking->passengers->count() }} Penumpang Terkunci
                        </span>
                    </div>

                    <div class="space-y-5">
                        @foreach($booking->passengers as $idx => $passenger)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3" id="card-passenger-{{ $passenger->id }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#087f55;">
                                            {{ $idx + 1 }}
                                        </span>
                                        <span class="font-bold text-slate-800 text-sm">Penumpang {{ $idx + 1 }}</span>
                                        <span class="text-xs px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 font-extrabold border border-emerald-200">
                                            Nomor Kursi: {{ $passenger->seat?->seat_number ?? '-' }}
                                        </span>
                                    </div>

                                    {{-- FITUR QUICK PASSENGER (DITARIK DARI TAB AKUN) --}}
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-slate-400">Isi Cepat:</span>
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button type="button" @click="open = !open" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-50 transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                                <span>Pilih Penumpang Tersimpan</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" class="origin-top-right absolute right-0 mt-1 w-72 rounded-2xl shadow-xl bg-white ring-1 ring-black/5 divide-y divide-slate-100 z-30 p-1">
                                                <div class="p-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>Data Tersimpan (Tab Akun):</span>
                                                    <a href="{{ route('dashboard.user.user-akun') }}" target="_blank" class="text-[10px] text-emerald-600 hover:underline inline-flex items-center gap-0.5">Kelola &rarr;</a>
                                                </div>
                                                <div class="py-1 space-y-1">
                                                    @foreach($quickPassengers as $qp)
                                                        <button type="button" 
                                                                @click="fillPassenger({{ $passenger->id }}, '{{ addslashes($qp['name']) }}', '{{ $qp['phone'] }}', '{{ $qp['nik'] }}'); open = false;"
                                                                class="w-full text-left px-3 py-2 rounded-xl text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-900 transition flex items-center justify-between group">
                                                            <div class="min-w-0 pr-2">
                                                                <span class="font-bold block truncate text-slate-800 group-hover:text-emerald-900">{{ $qp['name'] }}</span>
                                                                <span class="text-[10px] text-slate-400 block truncate">
                                                                    NIK: {{ $qp['nik'] ? substr($qp['nik'], 0, 4) . '••••' . substr($qp['nik'], -4) : '-' }}
                                                                </span>
                                                            </div>
                                                            <span class="text-[10px] text-emerald-700 font-mono bg-emerald-100/70 px-2 py-0.5 rounded-md flex-shrink-0">Pilih</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Chips Horizontal untuk opsi klik centang cepat --}}
                                @if(count($quickPassengers) > 0)
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px]">
                                        <span class="text-slate-400 text-[10px] flex-shrink-0 font-medium">Gunakan:</span>
                                        @foreach($quickPassengers as $qp)
                                            <button type="button"
                                                    @click="fillPassenger({{ $passenger->id }}, '{{ addslashes($qp['name']) }}', '{{ $qp['phone'] }}', '{{ $qp['nik'] }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 transition flex-shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="font-semibold">{{ Str::limit($qp['name'], 15) }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <input type="hidden" name="passengers[{{ $idx }}][id]" value="{{ $passenger->id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="block font-semibold text-slate-600 mb-1">Nama Lengkap Penumpang *</label>
                                        <input type="text" name="passengers[{{ $idx }}][name]" id="p_name_{{ $passenger->id }}" required
                                               value="{{ old('passengers.'.$idx.'.name', $passenger->name) }}"
                                               placeholder="Contoh: Budi Santoso"
                                               class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none transition focus:border-emerald-600">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-600 mb-1">NIK / ID Asuransi (16 Digit)</label>
                                        <input type="text" name="passengers[{{ $idx }}][nik]" id="p_nik_{{ $passenger->id }}" maxlength="16"
                                               value="{{ old('passengers.'.$idx.'.nik', $passenger->nik ?? '') }}"
                                               placeholder="3273xxxxxxxxxxxx"
                                               class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none transition focus:border-emerald-600 font-mono">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-600 mb-1">Nomor WhatsApp / HP</label>
                                        <input type="tel" name="passengers[{{ $idx }}][phone]" id="p_phone_{{ $passenger->id }}"
                                               value="{{ old('passengers.'.$idx.'.phone', $passenger->phone) }}"
                                               placeholder="08xxxxxxxxxx"
                                               class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none transition focus:border-emerald-600">
                                    </div>
                                </div>

                                <div class="text-xs">
                                    <label class="block font-medium text-slate-500 mb-1">Permintaan Khusus (Opsional)</label>
                                    <input type="text" name="passengers[{{ $idx }}][special_request]"
                                           value="{{ old('passengers.'.$idx.'.special_request', $passenger->special_request) }}"
                                           placeholder="Contoh: Bawa koper 20 inch, lansia butuh kursi dekat pintu"
                                           class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs outline-none transition focus:border-emerald-600">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. PILIHAN METODE PEMBAYARAN MIDTRANS --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5" id="payment-method-section">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shadow-xs" style="background:#e3f5ee;">
                                <svg width="18" height="18" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Metode Pembayaran (Midtrans)</h3>
                                <p class="text-[11px] text-slate-400">Pilih salah satu metode pembayaran sebelum menekan tombol checkout</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5 self-start sm:self-auto">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Payment Gateway Aktif
                        </span>
                    </div>

                    {{-- Validation Error Alert --}}
                    <div x-show="channelError" x-transition class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="channelError"></span>
                    </div>

                    {{-- CATEGORY SWITCHER TABS --}}
                    <div class="flex bg-slate-100 p-1.5 rounded-xl gap-1.5 text-xs font-bold">
                        <button type="button" @click="activeCategory = 'va'"
                                :class="activeCategory === 'va' ? 'bg-white text-emerald-800 shadow-sm border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
                                class="flex-1 py-2.5 px-3 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            <span>Bank Transfer (Virtual Account)</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800">6 Bank</span>
                        </button>
                        <button type="button" @click="activeCategory = 'qris'"
                                :class="activeCategory === 'qris' ? 'bg-white text-emerald-800 shadow-sm border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
                                class="flex-1 py-2.5 px-3 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            <span>E-Wallet & QRIS</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-rose-100 text-rose-800">Universal</span>
                        </button>
                    </div>

                    {{-- SECTION 1: VIRTUAL ACCOUNT CHANNELS --}}
                    <div x-show="activeCategory === 'va'" class="space-y-3">
                        <p class="text-xs font-semibold text-slate-500">Pilih Bank Virtual Account:</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- BCA --}}
                            <div @click="selectChannel('bca', 'va')"
                                 :class="selectedChannel === 'bca' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                            BCA
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">BCA Virtual Account</h4>
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">Otomatis 24 Jam</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'bca' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">m-BCA, KlikBCA, myBCA, atau ATM BCA dengan verifikasi instan.</p>
                            </div>

                            {{-- Mandiri --}}
                            <div @click="selectChannel('mandiri', 'va')"
                                 :class="selectedChannel === 'mandiri' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-600 text-white font-black text-[10px] tracking-tight flex items-center justify-center shadow-xs">
                                            MANDIRI
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">Bank Mandiri</h4>
                                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">Bill Payment</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'mandiri' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Livin by Mandiri & ATM Mandiri via Biller Code & Bill Key resmi.</p>
                            </div>

                            {{-- BNI --}}
                            <div @click="selectChannel('bni', 'va')"
                                 :class="selectedChannel === 'bni' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                            BNI
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">BNI Virtual Account</h4>
                                            <span class="text-[10px] font-semibold text-teal-700 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">Otomatis 24 Jam</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'bni' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">BNI Mobile Banking, Internet Banking, atau ATM BNI.</p>
                            </div>

                            {{-- BRI --}}
                            <div @click="selectChannel('bri', 'va')"
                                 :class="selectedChannel === 'bri' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-sky-700 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                            BRI
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">BRI Virtual Account (BRIVA)</h4>
                                            <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 px-1.5 py-0.5 rounded">BRIVA Otomatis</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'bri' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">BRImo, ATM BRI, & Mini ATM EDC dengan verifikasi waktu-nyata.</p>
                            </div>

                            {{-- BSI --}}
                            <div @click="selectChannel('bsi', 'va')"
                                 :class="selectedChannel === 'bsi' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                            BSI
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">BSI Virtual Account</h4>
                                            <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">Syariah Otomatis</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'bsi' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">BSI Mobile & ATM BSI via Virtual Account Bank Syariah.</p>
                            </div>

                            {{-- SeaBank --}}
                            <div @click="selectChannel('seabank', 'va')"
                                 :class="selectedChannel === 'seabank' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-orange-600 text-white font-black text-[9px] tracking-tight flex items-center justify-center shadow-xs">
                                            SEABANK
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">SeaBank</h4>
                                            <span class="text-[10px] font-semibold text-orange-700 bg-orange-50 border border-orange-200 px-1.5 py-0.5 rounded">Bebas Biaya Transfer</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'seabank' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Transfer gratis dari SeaBank via BI-FAST / Virtual Account Permata.</p>
                            </div>
                        </div>

                        {{-- SEABANK HELPER BANNER --}}
                        <div x-show="selectedChannel === 'seabank'" x-transition class="p-3.5 rounded-xl bg-orange-50 border border-orange-200 text-xs text-orange-950 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-orange-900">
                                <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                <span>Panduan Khusus SeaBank:</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-orange-800">
                                Midtrans terintegrasi menggunakan Virtual Account Permata (Kode Bank: <strong>013</strong>). Di aplikasi SeaBank, pilih menu <strong>Transfer &rarr; Virtual Account / Bank Lain (BI-FAST)</strong>, masukkan nomor VA yang diterbitkan, bebas biaya admin!
                            </p>
                        </div>
                    </div>

                    {{-- SECTION 2: QRIS & E-WALLET CHANNELS --}}
                    <div x-show="activeCategory === 'qris'" class="space-y-3" style="display: none;">
                        <p class="text-xs font-semibold text-slate-500">Pilih Layanan E-Wallet / QRIS:</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- QRIS --}}
                            <div @click="selectChannel('qris', 'qris')"
                                 :class="selectedChannel === 'qris' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                            QRIS
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">QRIS Dinamis</h4>
                                            <span class="text-[9px] font-semibold text-rose-700 bg-rose-50 border border-rose-200 px-1 py-0.5 rounded">Universal</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'qris' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">GoPay, OVO, DANA, ShopeePay, LinkAja, BCA, Livin, BRImo, dll.</p>
                            </div>

                            {{-- GoPay --}}
                            <div @click="selectChannel('gopay', 'qris')"
                                 :class="selectedChannel === 'gopay' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-sky-600 text-white font-black text-[10px] flex items-center justify-center shadow-xs">
                                            GOPAY
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">GoPay</h4>
                                            <span class="text-[9px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 px-1 py-0.5 rounded">Direct App</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'gopay' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Otomatis membuka aplikasi GoPay / Gojek di smartphone Anda.</p>
                            </div>

                            {{-- ShopeePay --}}
                            <div @click="selectChannel('shopeepay', 'qris')"
                                 :class="selectedChannel === 'shopeepay' ? 'border-emerald-600 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition relative flex flex-col justify-between gap-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-orange-500 text-white font-black text-[9px] flex items-center justify-center shadow-xs">
                                            SHOPEE
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">ShopeePay</h4>
                                            <span class="text-[9px] font-semibold text-orange-700 bg-orange-50 border border-orange-200 px-1 py-0.5 rounded">Deep Link</span>
                                        </div>
                                    </div>
                                    <span :class="selectedChannel === 'shopeepay' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white text-transparent'"
                                          class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-bold transition">✓</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Direct verifikasi pin via aplikasi Shopee secara instan.</p>
                            </div>
                        </div>

                        {{-- QRIS UNIVERSAL HELPER BANNER --}}
                        <div x-show="selectedChannel === 'qris'" x-transition class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-950 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-rose-900">
                                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                <span>QRIS Bersifat Universal:</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-rose-800">
                                Satu kode QR dinamis Midtrans dapat di-scan dari aplikasi apapun: <strong>GoPay, OVO, DANA, ShopeePay, LinkAja</strong> serta seluruh aplikasi perbankan (BCA, Mandiri, BRI, BNI, dll).
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: KODE VOUCHER PROMO & RINGKASAN BIAYA --}}
            <div class="lg:col-span-4 space-y-4">

                {{-- CARD PENERAPAN VOUCHER PROMO --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                        <div class="w-6 h-6 rounded-md bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Voucer & Promo Diskon</h4>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Masukkan Kode Voucer</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="promoInput" placeholder="HEMATLIBURAN"
                                   class="flex-1 uppercase font-mono font-bold text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 outline-none transition focus:border-emerald-600">
                            <button type="button" @click="applyPromo()"
                                     class="px-4 py-2 text-xs font-bold text-white rounded-xl shadow-2xs transition"
                                     style="background:#087f55;"
                                     onmouseover="this.style.background='#066b47'"
                                     onmouseout="this.style.background='#087f55'">
                                Pasang
                            </button>
                        </div>
                    </div>

                    {{-- Feedback status promo --}}
                    <div x-show="promoMessage" x-transition>
                        <div :class="promoSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'"
                             class="p-2.5 rounded-xl border text-xs leading-relaxed flex items-start gap-2">
                            <template x-if="promoSuccess">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </template>
                            <template x-if="!promoSuccess">
                                <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </template>
                            <span x-text="promoMessage"></span>
                        </div>
                    </div>

                    {{-- Quick promo chips --}}
                    <div class="pt-2 border-t border-slate-100 space-y-1.5">
                        <p class="text-[11px] text-slate-400 font-medium">Voucer Tersedia:</p>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="promoInput = 'HEMATLIBURAN'; applyPromo()"
                                    class="text-[11px] font-mono font-semibold px-2 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition">
                                HEMATLIBURAN (-25%)
                            </button>
                            <button type="button" @click="promoInput = 'TRAVELHEMAT'; applyPromo()"
                                    class="text-[11px] font-mono font-semibold px-2 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition">
                                TRAVELHEMAT (-20rb)
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="promo_code" :value="appliedPromoCode">
                </div>

                {{-- CARD RINCIAN PEMBAYARAN --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                    <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2.5">Rincian Pembayaran</h4>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal ({{ $booking->total_passenger }} Tiket):</span>
                            <span class="font-semibold text-slate-800" x-text="formatRupiah(subtotal)"></span>
                        </div>

                        <div class="flex justify-between text-emerald-700" x-show="discount > 0">
                            <span class="flex items-center gap-1">
                                <span>Potongan Diskon:</span>
                                <span class="text-[10px] font-mono font-bold bg-emerald-100 px-1 rounded" x-text="appliedPromoCode"></span>
                            </span>
                            <span class="font-bold" x-text="'- ' + formatRupiah(discount)"></span>
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span>Biaya Layanan & Asuransi:</span>
                            <span class="font-semibold text-emerald-700">GRATIS (Rp 0)</span>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-sm font-extrabold">
                            <span class="text-slate-900">Total Tagihan:</span>
                            <span class="text-lg" style="color:#087f55;" x-text="formatRupiah(totalPrice)"></span>
                        </div>

                        {{-- Ringkasan Metode Bayar Terpilih --}}
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Metode Pembayaran Dipilih:</span>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800" x-text="getChannelName(selectedChannel)"></span>
                                <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">MIDTRANS</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full text-white font-bold py-3.5 px-4 rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2"
                            style="background:#087f55;"
                            onmouseover="this.style.background='#066b47'"
                            onmouseout="this.style.background='#087f55'">
                        <span>Checkout & Bayar Tiket</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                    </button>

                    <p class="text-[11px] text-center text-slate-400 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Transaksi aman & terverifikasi otomatis via Payment Gateway
                    </p>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
function bookingDataHandler(config) {
    return {
        remainingSeconds: config.remainingSeconds,
        bookingId: config.bookingId,
        subtotal: config.subtotal,
        discount: 0,
        totalPrice: config.subtotal,
        promoInput: '',
        appliedPromoCode: '',
        promoMessage: '',
        promoSuccess: false,
        csrfToken: config.csrfToken,
        activeCategory: 'va',
        selectedChannel: 'bca',
        channelError: '',
        channelNames: {
            'bca': 'Virtual Account BCA',
            'mandiri': 'Mandiri Bill Payment',
            'bni': 'Virtual Account BNI',
            'bri': 'Virtual Account BRI (BRIVA)',
            'bsi': 'Virtual Account BSI',
            'seabank': 'SeaBank (Transfer VA Permata)',
            'qris': 'QRIS Dinamis Universal',
            'gopay': 'GoPay Instant',
            'shopeepay': 'ShopeePay'
        },

        selectChannel(code, category) {
            this.selectedChannel = code;
            if (category) {
                this.activeCategory = category;
            }
            this.channelError = '';
        },

        getChannelName(code) {
            return this.channelNames[code] || 'Virtual Account BCA';
        },

        validateBeforeSubmit(e) {
            if (!this.selectedChannel) {
                e.preventDefault();
                this.channelError = 'Silakan pilih salah satu metode pembayaran terlebih dahulu sebelum checkout.';
                document.getElementById('payment-method-section')?.scrollIntoView({ behavior: 'smooth' });
                return false;
            }
            return true;
        },

        init() {
            // Ticker countdown
            setInterval(() => {
                if (this.remainingSeconds > 0) {
                    this.remainingSeconds--;
                } else {
                    alert('Waktu penguncian kursi Anda telah habis. Halaman akan dialihkan kembali ke pencarian.');
                    window.location.href = "{{ route('dashboard.user.search') }}";
                }
            }, 1000);
        },

        formatTimer(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },

        fillPassenger(id, name, phone, nik = '') {
            const nameInput = document.getElementById('p_name_' + id);
            const phoneInput = document.getElementById('p_phone_' + id);
            const nikInput = document.getElementById('p_nik_' + id);
            if (nameInput) nameInput.value = name;
            if (phoneInput) phoneInput.value = phone;
            if (nikInput && nik) nikInput.value = nik;
        },

        async applyPromo() {
            if (!this.promoInput.trim()) {
                this.promoMessage = 'Masukkan kode voucer terlebih dahulu.';
                this.promoSuccess = false;
                return;
            }

            try {
                const response = await fetch("{{ route('dashboard.user.booking.validate-promo') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        booking_id: this.bookingId,
                        promo_code: this.promoInput.trim()
                    })
                });

                const data = await response.json();

                if (response.ok && data.valid) {
                    this.promoSuccess = true;
                    this.promoMessage = data.message;
                    this.appliedPromoCode = data.code;
                    this.discount = data.discount;
                    this.totalPrice = data.new_total;
                } else {
                    this.promoSuccess = false;
                    this.promoMessage = data.message || 'Kode voucer tidak dapat digunakan.';
                    this.appliedPromoCode = '';
                    this.discount = 0;
                    this.totalPrice = this.subtotal;
                }
            } catch (err) {
                this.promoSuccess = false;
                this.promoMessage = 'Gagal memvalidasi promo. Periksa koneksi Anda.';
            }
        }
    };
}
</script>
@endsection
