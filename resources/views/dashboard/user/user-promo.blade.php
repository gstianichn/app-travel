@extends('layouts.dashboard')

@section('page-title', 'Voucher saya')

@php
    $filters = [
        'all' => ['Semua', $totalCount],
        'active' => ['Masih Berlaku', $activeCount],
        'used' => ['Sudah dipakai', $usedCount],
        'expired' => ['Kedaluwarsa', $expiredCount],
    ];
    $sourceLabels = [
        'compensation' => 'Kompensasi keterlambatan',
        'welcome_gift' => 'Hadiah akun baru',
    ];
@endphp

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    @include('dashboard.user.partials.flash')

    {{-- HEADER + KLAIM KODE --}}
    <header class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-7 space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-brand-900">Voucher saya</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Pakai kodenya di kolom <b>Kode promo</b> saat mengisi data pemesan. Promo umum tersedia di
                    <a href="{{ route('dashboard.user.index') }}" class="font-semibold text-brand-700 underline underline-offset-2">Beranda</a>.
                </p>
            </div>
            <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-900">
                {{ $activeCount }} voucher aktif
            </span>
        </div>

        <form action="{{ route('dashboard.user.voucher.claim') }}" method="POST" class="border-t border-slate-100 pt-5 space-y-2">
            @csrf
            <label for="voucher-code" class="block text-sm font-semibold text-slate-800">Punya kode voucher atau kompensasi?</label>
            <div class="flex flex-col sm:flex-row gap-2.5">
                <input id="voucher-code"
                       type="text"
                       name="code"
                       required
                       maxlength="50"
                       autocomplete="off"
                       value="{{ old('code') }}"
                       placeholder="Masukkan kode, misal HEMATLIBURAN"
                       class="flex-1 min-h-11 rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold uppercase tracking-wide text-slate-900 placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700/15">
                <button type="submit" class="lp-btn lp-btn--green shrink-0 min-h-11 px-5 text-sm cursor-pointer">Simpan</button>
            </div>
        </form>
    </header>

    {{-- FILTER TABS --}}
    <nav class="flex flex-wrap gap-1 border-b border-slate-200" aria-label="Saring voucher">
        @foreach ($filters as $key => [$label, $count])
            <a href="{{ route('dashboard.user.user-promo', ['status' => $key]) }}"
               @if ($filterStatus === $key) aria-current="page" @endif
               @class([
                   '-mb-px inline-flex items-center gap-2 min-h-11 px-4 border-b-2 text-sm font-bold transition-colors',
                   'border-brand-800 text-brand-900' => $filterStatus === $key,
                   'border-transparent text-slate-500 hover:text-brand-800' => $filterStatus !== $key,
               ])>
                <span>{{ $label }}</span>
                <span class="text-xs font-semibold text-slate-400">{{ $count }}</span>
            </a>
        @endforeach
    </nav>

    {{-- DAFTAR VOUCHER --}}
    @if ($vouchers->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <p class="font-bold text-slate-900">{{ $filterStatus === 'all' ? 'Belum ada voucher tersimpan.' : 'Tidak ada voucher di kelompok ini.' }}</p>
            <p class="mt-1 text-sm text-slate-600">Voucher kompensasi dari CS dan kode promo yang kamu simpan akan muncul di sini.</p>
        </div>
    @else
        <ul class="grid sm:grid-cols-2 gap-4">
            @foreach ($vouchers as $voucher)
                @php
                    $status = $voucher->statusInfo();
                    $isActive = $voucher->isActive();
                    $isUsed = $voucher->isUsed();
                @endphp
                <li @class([
                    'flex flex-col justify-between rounded-2xl border overflow-hidden transition-colors',
                    'border-brand-700/35 bg-white hover:border-brand-700' => $isActive,
                    'border-slate-200 bg-slate-50 text-slate-500' => ! $isActive,
                ])>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <span @class([
                                'font-mono text-base font-extrabold tracking-wide',
                                'text-brand-900' => $isActive,
                                'text-slate-500' => ! $isActive,
                            ])>{{ $voucher->code }}</span>
                            <span @class([
                                'shrink-0 px-2.5 py-0.5 rounded-full text-xs font-bold',
                                'bg-brand-100 text-brand-900' => $isActive,
                                'bg-slate-200 text-slate-600' => $isUsed,
                                'bg-rose-50 text-rose-700' => ! $isActive && ! $isUsed,
                            ])>{{ $status['label'] }}</span>
                        </div>

                        <div>
                            <p @class(['font-bold', 'text-slate-900' => $isActive, 'text-slate-600' => ! $isActive])>{{ $voucher->title }}</p>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $voucher->discountText() }} &bull; {{ $voucher->minOrderText() }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                Rute: {{ $voucher->applicable_route ?: 'Semua rute' }}
                                @isset($sourceLabels[$voucher->source])
                                    &bull; {{ $sourceLabels[$voucher->source] }}
                                @endisset
                            </p>
                        </div>
                    </div>

                    <div @class([
                        'flex items-center justify-between gap-3 border-t border-dashed px-5 py-3',
                        'border-slate-200 bg-slate-50/70' => $isActive,
                        'border-slate-200 bg-slate-100/60' => ! $isActive,
                    ])>
                        <p class="text-xs text-slate-500">
                            @if ($isUsed)
                                Dipakai {{ $voucher->used_at?->locale('id')->isoFormat('D MMM YYYY') }}
                            @elseif ($voucher->expires_at)
                                {{ $isActive ? 'Berlaku s/d' : 'Berakhir' }} {{ $voucher->expires_at->locale('id')->isoFormat('D MMM YYYY') }}
                            @else
                                Tanpa batas waktu
                            @endif
                        </p>

                        @if ($isActive)
                            <button type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-brand-700 bg-white text-xs font-bold text-brand-800 hover:bg-brand-50 transition-colors cursor-pointer"
                                    data-copy-code="{{ $voucher->code }}">
                                <span data-copy-label>Salin kode</span>
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        <p class="sr-only" role="status" aria-live="polite" data-copy-status></p>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-copy-code]').forEach((button) => {
            button.addEventListener('click', async () => {
                const status = document.querySelector('[data-copy-status]');
                const labelEl = button.querySelector('[data-copy-label]') || button;

                try {
                    await navigator.clipboard.writeText(button.dataset.copyCode);
                    labelEl.textContent = 'Tersalin';
                    status.textContent = `Kode ${button.dataset.copyCode} tersalin.`;
                } catch {
                    labelEl.textContent = 'Salin manual';
                    status.textContent = 'Browser menolak menyalin. Pilih kodenya lalu salin manual.';
                }

                window.setTimeout(() => { labelEl.textContent = 'Salin kode'; }, 2000);
            });
        });
    </script>
@endpush
