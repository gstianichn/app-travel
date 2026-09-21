@extends('layouts.dashboard')

@section('page-title', 'Akun & Profil')

@section('content')
@php
    // Inisialisasi data tiket default untuk konteks WhatsApp CS
    $firstBooking = $recentBookings->first();
    $defaultBookingId = $firstBooking ? $firstBooking->id : '';
@endphp

<div x-data="accountPageHandler({
    selectedBookingId: '{{ $defaultBookingId }}',
    userName: '{{ addslashes(auth()->user()->name ?? 'Pengguna') }}',
    bookings: @js($recentBookings->map(fn($b) => [
        'id' => (string)$b->id,
        'code' => $b->booking_code,
        'ticket' => $b->ticket?->ticket_number ?? 'TKT-'.$b->booking_code,
        'route' => ($b->schedule?->route?->origin?->name ?? 'Asal') . ' ➔ ' . ($b->schedule?->route?->destination?->name ?? 'Tujuan'),
        'date' => \Carbon\Carbon::parse($b->schedule?->departure_date)->translatedFormat('d M Y'),
        'time' => \Carbon\Carbon::parse($b->schedule?->departure_time)->format('H:i') . ' WIB',
        'passenger' => $b->passengers->first()?->name ?? $user->name,
    ]))
})">

<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs">✕</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <p class="font-bold">Terjadi kesalahan pengisian data:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ============================================================
         1. HEADER PROFIL (IDENTITAS PENGGUNA)
    ============================================================ -->
    <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="rounded-3xl p-6 sm:p-7 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-5 shadow-lg relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <!-- Avatar -->
            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl flex items-center justify-center text-2xl font-black text-white flex-shrink-0 shadow-inner"
                 style="background: rgba(255,255,255,.22); border: 2px solid rgba(255,255,255,.3);">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="font-black text-xl sm:text-2xl leading-tight truncate">{{ $user->name ?? 'Nama Pengguna' }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/20 text-emerald-100 border border-white/20">
                        Member
                    </span>
                </div>
                <p class="text-xs sm:text-sm mt-1 text-emerald-100/90 truncate flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>{{ $user->email ?? 'user@email.com' }}</span>
                </p>
                <p class="text-xs mt-0.5 text-emerald-200/80 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $user->phone ?? '+62 812-xxxx-xxxx' }}</span>
                </p>
            </div>
        </div>

        <button id="btn-edit-profile" @click="showEditProfile = true"
            class="self-start sm:self-center px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm relative z-10"
            style="background: rgba(255,255,255,.20);"
            onmouseover="this.style.background='rgba(255,255,255,.30)'"
            onmouseout="this.style.background='rgba(255,255,255,.20)'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
            </svg>
            <span>Edit Profil</span>
        </button>
    </div>

    <!-- ============================================================
         A. ALUR PENUMPANG TERSIMPAN (SAVED PASSENGER LIST)
    ============================================================ -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                    <svg width="20" height="20" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Daftar Penumpang Tersimpan (Saved Passengers)</h3>
                    <p class="text-xs text-slate-400">Input sekali pakai ulang: NIK/ID Asuransi & No HP otomatis terintegrasi di Checkout</p>
                </div>
            </div>
            <button id="btn-tambah-penumpang" @click="openAddPassengerModal()" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm transition flex items-center gap-1.5"
                    style="background:#087f55;"
                    onmouseover="this.style.background='#066b47'"
                    onmouseout="this.style.background='#087f55'">
                <span class="text-sm">+</span>
                <span>Tambah Data</span>
            </button>
        </div>

        {{-- Daftar Penumpang --}}
        <div class="divide-y divide-slate-100">
            @forelse($savedPassengers as $sp)
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 transition" id="saved-passenger-{{ $sp->id }}">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black text-slate-600 bg-slate-100 flex-shrink-0">
                            {{ strtoupper(substr($sp->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-slate-800">{{ $sp->name }}</p>
                                @if($sp->is_primary)
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full" style="background:#e3f5ee; color:#087f55;">
                                        ★ Utama
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-400 mt-0.5 font-mono">
                                <span>NIK: <strong class="text-slate-600">{{ $sp->maskedNik() }}</strong></span>
                                <span>•</span>
                                <span>HP: <strong class="text-slate-600">{{ $sp->phone ?: '-' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi: Edit & Hapus --}}
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <button type="button" 
                                @click="openEditPassengerModal({{ $sp->id }}, '{{ addslashes($sp->name) }}', '{{ $sp->nik }}', '{{ $sp->phone }}', {{ $sp->is_primary ? 'true' : 'false' }})"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 text-xs font-semibold transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('dashboard.user.saved-passenger.destroy', $sp) }}" method="POST" onsubmit="return confirm('Hapus penumpang {{ addslashes($sp->name) }} dari daftar tersimpan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Penumpang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400">
                    <p class="text-sm font-medium">Belum ada penumpang tersimpan.</p>
                    <p class="text-xs mt-1">Klik tombol "+ Tambah Data" untuk menyimpan data penumpang sekali saja.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================
         B. ALUR MANAJEMEN PROMO & VOUCHER (DOMPET VOUCHER)
    ============================================================ -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m9 9 6 6m0-6-6 6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Dompet Voucher & Promo Saya (My Vouchers)</h3>
                    <p class="text-xs text-slate-400">Voucher klaim promo & kompensasi tersimpan otomatis dengan validasi syarat order</p>
                </div>
            </div>
            <a href="{{ route('dashboard.user.user-promo') }}" class="text-xs font-bold hover:underline flex items-center gap-1" style="color:#087f55;">
                <span>Lihat Semua</span>
                <span class="text-sm">➔</span>
            </a>
        </div>

        {{-- Preview Voucher Terbaru --}}
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @forelse($vouchers->take(2) as $vc)
                    @php
                        $st = $vc->statusInfo();
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 hover:border-emerald-300 transition space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-black text-sm text-slate-900 tracking-wider">
                                {{ $vc->code }}
                            </span>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full border {{ $st['badge_class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $vc->title }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $vc->discountText() }} • {{ $vc->minOrderText() }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Berlaku s/d: {{ $vc->expires_at ? $vc->expires_at->translatedFormat('d M Y') : 'Selamanya' }}</span>
                            @if($vc->isActive())
                                <a href="{{ route('dashboard.user.search') }}" class="text-emerald-700 font-bold hover:underline">Gunakan ➔</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-4 text-xs text-slate-400">
                        Belum ada voucher tersimpan di dompet Anda.
                    </div>
                @endforelse
            </div>

            {{-- Form Cepat Klaim Kode Voucher --}}
            <form action="{{ route('dashboard.user.voucher.claim') }}" method="POST" class="pt-2 flex flex-col sm:flex-row gap-2">
                @csrf
                <div class="relative flex-1">
                    <input type="text" name="code" required placeholder="Punya kode promo / kompensasi? Masukkan di sini (misal: KOMPENSASI50)"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs uppercase font-mono font-bold tracking-wider outline-none transition focus:border-emerald-600">
                </div>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5"
                        style="background:#087f55;"
                        onmouseover="this.style.background='#066b47'"
                        onmouseout="this.style.background='#087f55'">
                    <span>Klaim ke Dompet</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ============================================================
         C. ALUR PUSAT BANTUAN (LIVE CHAT & WHATSAPP DENGAN KONTEKS TIKET)
    ============================================================ -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm" id="section-helpdesk">
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                    <svg width="20" height="20" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pusat Bantuan CS (Live Chat & WhatsApp)</h3>
                    <p class="text-xs text-slate-400">Pilih tiket bermasalah untuk menyertakan nomor tiket & rute secara otomatis</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">
            {{-- SELECTOR KONTEKS TIKET OTOMATIS --}}
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Pilih Tiket Perjalanan yang Membutuhkan Bantuan:
                </label>
                <div class="relative">
                    <select x-model="selectedBookingId" @change="updateContext()"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none transition focus:border-emerald-600 shadow-2xs">
                        <option value="">-- Pertanyaan Umum / Tanpa Tiket Spesifik --</option>
                        @foreach($recentBookings as $b)
                            <option value="{{ $b->id }}" {{ (string)$b->id === (string)$defaultBookingId ? 'selected' : '' }}>
                                #{{ $b->booking_code }} ({{ $b->ticket?->ticket_number ?? 'TKT' }}) • {{ $b->schedule?->route?->origin?->name }} ➔ {{ $b->schedule?->route?->destination?->name }} ({{ \Carbon\Carbon::parse($b->schedule?->departure_date)->format('d M') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Preview Pesan Pre-filled WhatsApp --}}
                <div class="pt-2 text-[11px] text-slate-500">
                    <span class="font-semibold text-slate-700">Pratinjau Pesan CS Otomatis:</span>
                    <div class="mt-1 p-2.5 rounded-xl bg-white border border-slate-200/80 font-mono text-[11px] text-slate-600 whitespace-pre-line" x-text="previewMessage"></div>
                </div>
            </div>

            {{-- TOMBOL WHATSAPP CS & LIVE CHAT --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                
                {{-- Tombol WhatsApp CS dengan Pre-filled Message --}}
                <a :href="waUrl" target="_blank" id="btn-whatsapp-cs"
                   class="px-5 py-4 rounded-2xl border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100/70 transition flex items-center gap-3.5 group shadow-2xs">
                    <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900">Chat WhatsApp CS</p>
                        <p class="text-[11px] text-emerald-800 font-medium">Bawa Nomor Tiket & Rute ➔</p>
                    </div>
                    <svg class="w-4 h-4 text-emerald-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                {{-- Tombol Live Chat App --}}
                <button type="button" @click="showLiveChat = true" id="btn-live-chat"
                   class="px-5 py-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center gap-3.5 group text-left shadow-2xs">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition" style="background:#e3f5ee;">
                        <svg width="20" height="20" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold text-slate-900">Live Chat Bantuan</p>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                        <p class="text-[11px] text-slate-400">Obrolan langsung dalam aplikasi</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </button>

            </div>
        </div>
    </div>

    <!-- ============================================================
         4. PENGATURAN KEAMANAN AKUN
    ============================================================ -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center">
                    <svg width="20" height="20" fill="none" stroke="#475569" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Keamanan & Pengaturan Akun</h3>
            </div>
        </div>
        <div class="divide-y divide-slate-100 text-xs sm:text-sm font-semibold">
            <button id="btn-ganti-password" @click="showChangePassword = true"
               class="w-full px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition text-left group">
                <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-slate-800">Ganti Password</p>
                    <p class="text-xs text-slate-400 font-normal">Perbarui kata sandi akun secara berkala</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>

    <!-- ============================================================
         5. LOGOUT
    ============================================================ -->
    <div class="bg-white border border-rose-100 rounded-3xl overflow-hidden shadow-sm">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button id="btn-logout" type="submit"
                class="w-full px-6 py-4 flex items-center gap-4 hover:bg-rose-50/80 transition text-left group">
                <div class="w-9 h-9 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-rose-600">Keluar dari Akun</p>
                    <p class="text-xs text-slate-400">Akhiri sesi portal penumpang Anda</p>
                </div>
                <svg class="w-4 h-4 text-rose-300 group-hover:text-rose-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </form>
    </div>

    <!-- Versi App -->
    <p class="text-center text-[11px] text-slate-400 pb-4">Fast Travel v2.5.0 • Portal Penumpang & Client Area</p>

</div>

{{-- ============================================================
     MODAL 1: TAMBAH PENUMPANG TERSIMPAN
============================================================ --}}
<div x-show="showAddPassenger" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);"
     @click.self="showAddPassenger = false">

    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Tambah Penumpang Tersimpan</h3>
            <button @click="showAddPassenger = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('dashboard.user.saved-passenger.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nama Lengkap Penumpang *</label>
                <input type="text" name="name" required placeholder="Sesuai KTP / SIM / Paspor"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">NIK / No. Identitas Kependudukan (16 Digit)</label>
                <input type="text" name="nik" maxlength="16" placeholder="3273xxxxxxxxxxxx (wajib untuk asuransi perjalanan)"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono outline-none transition focus:border-emerald-600">
                <p class="text-[10px] text-slate-400 mt-1">Data NIK digunakan untuk keperluan manifes dan klaim asuransi Jasa Raharja.</p>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" placeholder="08xxxxxxxxxx"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700">
                    <input type="checkbox" name="is_primary" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300">
                    <span class="font-medium">Jadikan Penumpang Utama</span>
                </label>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" @click="showAddPassenger = false" class="flex-1 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 text-white font-bold rounded-xl transition shadow-sm" style="background:#087f55;">
                    Simpan Penumpang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     MODAL 2: EDIT PENUMPANG TERSIMPAN
============================================================ --}}
<div x-show="showEditPassenger" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);"
     @click.self="showEditPassenger = false">

    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Edit Penumpang Tersimpan</h3>
            <button @click="showEditPassenger = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form :action="'{{ url('user/penumpang-tersimpan') }}/' + editData.id" method="POST" class="space-y-3.5 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nama Lengkap Penumpang *</label>
                <input type="text" name="name" required x-model="editData.name"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">NIK / No. Identitas Kependudukan (16 Digit)</label>
                <input type="text" name="nik" maxlength="16" x-model="editData.nik"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" x-model="editData.phone"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700">
                    <input type="checkbox" name="is_primary" value="1" :checked="editData.is_primary" class="w-4 h-4 text-emerald-600 rounded border-slate-300">
                    <span class="font-medium">Jadikan Penumpang Utama</span>
                </label>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" @click="showEditPassenger = false" class="flex-1 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 text-white font-bold rounded-xl transition shadow-sm" style="background:#087f55;">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     MODAL 3: EDIT PROFIL AKUN
============================================================ --}}
<div x-show="showEditProfile" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);"
     @click.self="showEditProfile = false">

    <div class="bg-white rounded-3xl w-full max-w-sm p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Edit Profil Akun</h3>
            <button @click="showEditProfile = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('dashboard.user.profil.update') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="{{ $user->name }}"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Email *</label>
                <input type="email" name="email" required value="{{ $user->email }}"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" value="{{ $user->phone }}" placeholder="08xxxxxxxxxx"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" @click="showEditProfile = false" class="flex-1 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 text-white font-bold rounded-xl transition shadow-sm" style="background:#087f55;">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     MODAL 4: GANTI PASSWORD
============================================================ --}}
<div x-show="showChangePassword" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);"
     @click.self="showChangePassword = false">

    <div class="bg-white rounded-3xl w-full max-w-sm p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Ganti Password</h3>
            <button @click="showChangePassword = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('dashboard.user.profil.password') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Password Saat Ini *</label>
                <input type="password" name="current_password" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Password Baru (min. 8 karakter) *</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Konfirmasi Password Baru *</label>
                <input type="password" name="password_confirmation" required minlength="8"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="button" @click="showChangePassword = false" class="flex-1 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 text-white font-bold rounded-xl transition shadow-sm" style="background:#087f55;">
                    Update Sandi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     MODAL 5: LIVE CHAT INTERAKTIF DENGAN KONTEKS TIKET
============================================================ --}}
<div x-show="showLiveChat" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,.5); backdrop-filter: blur(4px);"
     @click.self="showLiveChat = false">

    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl flex flex-col overflow-hidden h-[540px]">
        {{-- Header Live Chat --}}
        <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="px-5 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center font-bold">
                    CS
                </div>
                <div>
                    <h4 class="font-bold text-sm">Customer Support Fast Travel</h4>
                    <p class="text-[11px] text-emerald-100 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Online • Respon Rata-rata < 2 Menit</span>
                    </p>
                </div>
            </div>
            <button @click="showLiveChat = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Badge Konteks Tiket di Header Chat --}}
        <div class="px-5 py-2.5 bg-emerald-50 border-b border-emerald-100 text-xs text-emerald-900 flex items-center justify-between">
            <div class="flex items-center gap-1.5 truncate">
                <span class="font-bold">Konteks:</span>
                <span class="truncate" x-text="selectedBooking ? selectedBooking.ticket + ' (' + selectedBooking.route + ')' : 'Pertanyaan Umum Akun'"></span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-emerald-200">ID Terverifikasi</span>
        </div>

        {{-- Area Pesan Chat --}}
        <div class="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50 text-xs">
            {{-- Pesan Otomatis Sistem --}}
            <div class="flex gap-2.5 max-w-[85%]">
                <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                    CS
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none border border-slate-200 shadow-2xs space-y-1">
                    <p class="font-bold text-slate-800">Halo, Kak <span x-text="userName"></span>!</p>
                    <p class="text-slate-600 leading-relaxed">
                        Terima kasih telah menghubungi Customer Support Fast Travel. Sistem kami telah menerima identitas Anda beserta konteks tiket perjalanan terkait. Ada yang bisa kami bantu hari ini?
                    </p>
                    <span class="text-[9px] text-slate-400 block pt-1">Baru saja</span>
                </div>
            </div>

            {{-- Pesan Pengguna Berisi Konteks Tiket Otomatis --}}
            <template x-if="chatSentMessage">
                <div class="flex justify-end">
                    <div class="bg-emerald-600 text-white p-3 rounded-2xl rounded-tr-none shadow-2xs max-w-[85%] space-y-1">
                        <p class="whitespace-pre-line text-xs font-mono" x-text="chatSentMessage"></p>
                        <span class="text-[9px] text-emerald-200 block text-right">Terkirim</span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input Pesan --}}
        <div class="p-3 bg-white border-t border-slate-200 flex items-center gap-2">
            <input type="text" x-model="chatInput" @keyup.enter="sendChatMessage()"
                   placeholder="Ketik pesan atau kendala Anda di sini..."
                   class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-emerald-600 transition">
            <button type="button" @click="sendChatMessage()"
                    class="p-2.5 rounded-xl text-white shadow-sm transition"
                    style="background:#087f55;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}

<script>
function accountPageHandler(config) {
    return {
        selectedBookingId: config.selectedBookingId || '',
        userName: config.userName || 'Pengguna',
        bookings: config.bookings || [],
        showAddPassenger: false,
        showEditPassenger: false,
        showEditProfile: false,
        showChangePassword: false,
        showLiveChat: false,
        chatInput: '',
        chatSentMessage: '',
        editData: {
            id: null,
            name: '',
            nik: '',
            phone: '',
            is_primary: false
        },
        waUrl: 'https://wa.me/62812345678',
        previewMessage: '',

        init() {
            this.updateContext();
        },

        get selectedBooking() {
            if (!this.selectedBookingId) return null;
            return this.bookings.find(b => String(b.id) === String(this.selectedBookingId)) || null;
        },

        updateContext() {
            const b = this.selectedBooking;
            let msg = '';
            if (b) {
                msg = `Halo CS Fast Travel, saya butuh bantuan untuk tiket perjalanan saya:\n• No. Tiket: ${b.ticket}\n• Kode Booking: #${b.code}\n• Rute: ${b.route}\n• Jadwal: ${b.date}, ${b.time}\n• Penumpang: ${b.passenger}\n\nKendala yang saya alami: [Mohon dibantu terkait kendala tiket ini]`;
            } else {
                msg = `Halo CS Fast Travel, saya atas nama ${this.userName} membutuhkan bantuan customer support terkait layanan dan akun Fast Travel.`;
            }

            this.previewMessage = msg;
            this.waUrl = 'https://wa.me/62812345678?text=' + encodeURIComponent(msg);
        },

        openAddPassengerModal() {
            this.showAddPassenger = true;
        },

        openEditPassengerModal(id, name, nik, phone, isPrimary) {
            this.editData = {
                id: id,
                name: name,
                nik: nik || '',
                phone: phone || '',
                is_primary: isPrimary
            };
            this.showEditPassenger = true;
        },

        sendChatMessage() {
            if (!this.chatInput.trim()) return;
            const b = this.selectedBooking;
            let fullMsg = this.chatInput.trim();
            if (b && !this.chatSentMessage) {
                fullMsg = `[Konteks Tiket #${b.code} - ${b.route} (${b.date})]\n` + fullMsg;
            }
            this.chatSentMessage = fullMsg;
            this.chatInput = '';
        }
    };
}
</script>
@endsection
