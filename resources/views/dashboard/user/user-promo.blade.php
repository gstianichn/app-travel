@extends('layouts.dashboard')

@section('page-title', 'Dompet Voucher & Promo Saya')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- Breadcrumb Navigation --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('dashboard.user.user-akun') }}" class="hover:text-emerald-700 transition">Tab Akun</a>
            <span>/</span>
            <span class="text-slate-800 font-bold">Dompet Voucher (My Vouchers)</span>
        </div>
        <a href="{{ route('dashboard.user.user-akun') }}" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
            <span>&larr; Kembali ke Akun</span>
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs font-bold">✕</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ===== HEADER DOMPET VOUCHER & KLAIM PROMO ===== --}}
    <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-extrabold uppercase tracking-wider backdrop-blur-xs">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                    <span>Dompet Voucher Pribadi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Voucher & Kupon Perjalanan Anda
                </h1>
                <p class="text-xs sm:text-sm text-amber-100 font-medium">
                    Semua kupon klaim promo, voucher kompensasi keterlambatan, dan hadiah sambutan disimpan di sini dan siap digunakan saat checkout tiket.
                </p>
            </div>

            {{-- Form Klaim Kode Promo --}}
            <div class="bg-white p-5 rounded-2xl shadow-lg text-slate-800 w-full md:w-80 flex-shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-2">Klaim Kode Voucher Baru</span>
                <form action="{{ route('dashboard.user.voucher.claim') }}" method="POST" class="space-y-2.5">
                    @csrf
                    <input type="text" name="code" required placeholder="Contoh: KOMPENSASI50"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold uppercase tracking-wider outline-none transition focus:border-amber-500">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                        <span>Klaim ke Dompet</span>
                    </button>
                </form>
                <div class="mt-2 text-[10px] text-slate-400 text-center">
                    Coba kode: <strong>HEMATLIBURAN</strong> atau <strong>KOMPENSASI50</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TAB FILTER & STATISTIK VOUCHER ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('dashboard.user.user-promo', ['status' => 'all']) }}"
           class="p-4 rounded-2xl border transition {{ $filterStatus === 'all' ? 'bg-white border-emerald-500 ring-2 ring-emerald-100 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Semua Voucher</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</p>
        </a>

        <a href="{{ route('dashboard.user.user-promo', ['status' => 'active']) }}"
           class="p-4 rounded-2xl border transition {{ $filterStatus === 'active' ? 'bg-white border-emerald-500 ring-2 ring-emerald-100 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Masih Berlaku</span>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ $activeCount }}</p>
        </a>

        <a href="{{ route('dashboard.user.user-promo', ['status' => 'used']) }}"
           class="p-4 rounded-2xl border transition {{ $filterStatus === 'used' ? 'bg-white border-emerald-500 ring-2 ring-emerald-100 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sudah Terpakai</span>
            <p class="text-2xl font-black text-slate-700 mt-1">{{ $usedCount }}</p>
        </a>

        <a href="{{ route('dashboard.user.user-promo', ['status' => 'expired']) }}"
           class="p-4 rounded-2xl border transition {{ $filterStatus === 'expired' ? 'bg-white border-emerald-500 ring-2 ring-emerald-100 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Kedaluwarsa</span>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ $expiredCount }}</p>
        </a>
    </div>

    {{-- ===== DAFTAR KARTU VOUCHER ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($vouchers as $vc)
            @php
                $st = $vc->statusInfo();
                $isComp = $vc->source === 'compensation';
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden">
                
                {{-- Watermark / Dekorasi --}}
                <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-slate-50 border border-slate-100/50 flex items-center justify-center text-slate-300 opacity-40 pointer-events-none">
                    @if($isComp)
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    @else
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    @endif
                </div>

                <div class="space-y-4 relative z-10">
                    {{-- Header Voucher: Kode & Status Badge --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-slate-100 font-mono font-black text-sm text-slate-900 border border-slate-200/80 tracking-wider">
                                {{ $vc->code }}
                            </span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $vc->code }}'); alert('Kode voucher {{ $vc->code }} disalin!');" class="text-slate-400 hover:text-slate-600 transition" title="Salin Kode Voucher">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                        
                        {{-- Tag Status Otomatis --}}
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full border {{ $st['badge_class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    {{-- Detail Diskon & Syarat Order --}}
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base text-slate-900">{{ $vc->title }}</h3>
                            @if($isComp)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">Kompensasi</span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-emerald-700">{{ $vc->discountText() }}</p>
                    </div>

                    {{-- Rincian Syarat Minimum Order & Rute --}}
                    <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-medium">Syarat Order:</span>
                            <strong class="text-slate-800">{{ $vc->minOrderText() }}</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-medium">Rute Berlaku:</span>
                            <strong class="text-slate-800">{{ $vc->applicable_route ?: 'Semua Rute Shuttle' }}</strong>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-1 border-t border-slate-200/60">
                            <span>Masa Berlaku:</span>
                            <span>{{ $vc->expires_at ? $vc->expires_at->translatedFormat('d F Y') : 'Tanpa batas waktu' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Voucher --}}
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs relative z-10">
                    <span class="text-[11px] text-slate-400">
                        Sumber: {{ $vc->source === 'compensation' ? 'Kompensasi Tiket' : ($vc->source === 'welcome_gift' ? 'Hadiah Sambutan' : 'Klaim Promo') }}
                    </span>

                    @if($vc->isActive())
                        <a href="{{ route('dashboard.user.search') }}"
                           class="px-4 py-2 rounded-xl text-white font-bold text-xs shadow-sm transition flex items-center gap-1"
                           style="background:#087f55;"
                           onmouseover="this.style.background='#066b47'"
                           onmouseout="this.style.background='#087f55'">
                            <span>Pakai Sekarang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    @elseif($vc->isUsed())
                        <span class="text-xs font-semibold text-slate-400">
                            Digunakan {{ $vc->used_at ? $vc->used_at->translatedFormat('d M Y') : '' }}
                        </span>
                    @else
                        <span class="text-xs font-semibold text-rose-400">
                            Telah Kedaluwarsa
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-2 bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 flex items-center justify-center text-amber-700">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Tidak Ada Voucher Pada Kategori Ini</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Masukkan kode promo pada form klaim di atas untuk menambahkan voucher baru ke dompet Anda.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
