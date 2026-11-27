@extends('layouts.customer-service')

@section('title', 'Dashboard Customer Service - TravelKu')

@section('content')
<div class="space-y-8 text-[#13201B]">
    {{-- Sapaan --}}
    <header class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 sm:p-8 shadow-xs">
        <div class="inline-flex items-center gap-2 rounded-full border border-[#DEDAC9] bg-[#EEF6F1] px-3 py-1 text-xs font-bold text-[#0C5A3E] mb-3">
            <span class="h-2 w-2 rounded-full bg-[#0C7950]"></span>
            Customer Service Helpdesk &middot; {{ $monthLabel }}
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0C392F]">
            Halo, {{ \Illuminate\Support\Str::before(auth()->user()->name ?? 'CS', ' ') }}.
        </h1>
        <p class="text-sm text-[#5C6862] mt-1.5">
            {{ now()->translatedFormat('l, d F Y') }} &middot; Berikut ringkasan statistik laporan kendala pelanggan pada bulan <strong class="text-[#0C392F]">{{ $monthLabel }}</strong>.
        </p>
    </header>

    {{-- Statistik Kendala Bulan Ini --}}
    <section aria-labelledby="heading-statistik-bulan-ini" class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 id="heading-statistik-bulan-ini" class="text-lg font-extrabold text-[#0C392F]">
                Statistik Kendala Bulan {{ $monthLabel }}
            </h2>
            <span class="text-xs font-semibold text-[#5C6862]">
                Periode {{ now()->startOfMonth()->translatedFormat('d M') }} &ndash; {{ now()->endOfMonth()->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-5 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-[#5C6862]">Total Kendala</p>
                <p class="mt-2 text-3xl font-extrabold text-[#0C392F] tabular-nums">{{ $monthlyStats['total'] }}</p>
                <p class="mt-1 text-xs text-[#5C6862]">laporan bulan ini</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Baru Masuk</p>
                <p class="mt-2 text-3xl font-extrabold text-amber-700 tabular-nums">{{ $monthlyStats['baru_masuk'] }}</p>
                <p class="mt-1 text-xs text-amber-800/80">menunggu pengecekan</p>
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50/50 p-5 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-sky-800">Sedang Dicek</p>
                <p class="mt-2 text-3xl font-extrabold text-sky-700 tabular-nums">{{ $monthlyStats['sedang_dicek'] }}</p>
                <p class="mt-1 text-xs text-sky-800/80">dalam proses penanganan</p>
            </div>

            <div class="rounded-2xl border border-[#B8DFC9] bg-[#EEF6F1] p-5 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-[#0C5A3E]">Selesai</p>
                <p class="mt-2 text-3xl font-extrabold text-[#0C7950] tabular-nums">{{ $monthlyStats['selesai'] }}</p>
                <p class="mt-1 text-xs text-[#0C5A3E]/80">kendala tertangani</p>
            </div>

            <div class="rounded-2xl border border-purple-200 bg-purple-50/50 p-5 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-purple-800">Dieskalasi</p>
                <p class="mt-2 text-3xl font-extrabold text-purple-800 tabular-nums">{{ $monthlyStats['escalated'] }}</p>
                <p class="mt-1 text-xs text-purple-800/80">diteruskan ke Admin</p>
            </div>
        </div>
    </section>

    {{-- Sebaran Kategori Kendala Bulan Ini --}}
    <section aria-labelledby="heading-kategori-bulan-ini" class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 id="heading-kategori-bulan-ini" class="text-base font-extrabold text-[#0C392F]">
                    Sebaran Kategori Kendala ({{ $monthLabel }})
                </h2>
                <p class="text-xs text-[#5C6862] mt-0.5">
                    Jumlah laporan yang masuk berdasarkan jenis kendala pelanggan selama bulan ini.
                </p>
            </div>
            <a href="{{ route('dashboard.customer-service.laporan-kendala') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-[#0C7950] px-4 py-2 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                Kelola Laporan Kendala &rarr;
            </a>
        </div>

        <div class="space-y-3">
            @foreach ($monthlyByCategory as $cat)
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-[#13201B]">{{ $cat->label }}</span>
                        <span class="font-semibold text-[#5C6862] tabular-nums">{{ $cat->count }} laporan ({{ $cat->percent }}%)</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-[#ECE8DA]">
                        <div class="h-full rounded-full bg-[#0C7950] transition-all" style="width: {{ $cat->percent }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
