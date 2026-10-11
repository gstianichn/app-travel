@extends('layouts.dashboard')

@section('page-title', 'Voucher saya')

@php
    $filters = [
        'all' => ['Semua', $totalCount],
        'active' => ['Masih Berlaku', $activeCount],
        'used' => ['Sudah dipakai', $usedCount],
        'expired' => ['Kedaluwarsa', $expiredCount],
    ];
    $sourceLabels = ['compensation' => 'Kompensasi keterlambatan', 'welcome_gift' => 'Hadiah akun baru'];
@endphp

@section('content')
<div class="space-y-8 max-w-4xl">

    @include('dashboard.user.partials.flash')

    <header class="space-y-2">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Voucher saya</h1>
        <p class="text-slate-600">Pakai kodenya di kolom <b>Kode promo</b> saat mengisi data pemesan. Kode yang sedang berlaku untuk semua orang ada di <a href="{{ route('dashboard.user.index') }}" class="font-semibold text-brand-700 underline underline-offset-2">Beranda</a>.</p>
    </header>

    <form action="{{ route('dashboard.user.voucher.claim') }}" method="POST" class="max-w-xl space-y-2">
        @csrf
        <label for="voucher-code" class="block font-semibold text-slate-900">Punya kode voucher atau kompensasi?</label>
        <div class="flex gap-2">
            <input id="voucher-code" type="text" name="code" required maxlength="50" autocomplete="off" value="{{ old('code') }}"
                   class="w-full min-h-12 rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 font-bold uppercase text-slate-900">
            <button type="submit" class="shrink-0 min-h-12 px-5 rounded-lg bg-brand-800 font-bold text-white hover:bg-brand-900">Simpan</button>
        </div>
    </form>

    <nav class="flex flex-wrap gap-1 border-b border-slate-300" aria-label="Saring voucher">
        @foreach ($filters as $key => [$label, $count])
            <a href="{{ route('dashboard.user.user-promo', ['status' => $key]) }}"
               @if ($filterStatus === $key) aria-current="page" @endif
               @class([
                   '-mb-px inline-flex items-center gap-2 min-h-11 px-4 border-b-[3px] font-bold',
                   'border-brand-800 text-brand-900' => $filterStatus === $key,
                   'border-transparent text-slate-600 hover:text-brand-800' => $filterStatus !== $key,
               ])>
                {{ $label }} <span class="text-sm font-semibold text-slate-500">{{ $count }}</span>
            </a>
        @endforeach
    </nav>

    @if ($vouchers->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8">
            <p class="font-bold text-slate-900">{{ $filterStatus === 'all' ? 'Belum ada voucher tersimpan.' : 'Tidak ada voucher di kelompok ini.' }}</p>
            <p class="mt-1 text-slate-600">Voucher kompensasi dari CS dan kode promo yang kamu simpan akan muncul di sini.</p>
        </div>
    @else
        <ul class="grid sm:grid-cols-2 gap-4">
            @foreach ($vouchers as $voucher)
                @php
                    $status = $voucher->statusInfo();
                    $isActive = $voucher->isActive();
                @endphp
                {{-- Coupon shape: the dashed edge is the tear line, the same motif as the ticket stub --}}
                <li @class([
                    'rounded-xl border-2 border-dashed p-5 space-y-3',
                    'border-brand-600 bg-white' => $isActive,
                    'border-slate-300 bg-slate-50 text-slate-600' => ! $isActive,
                ])>
                    <div class="flex items-start justify-between gap-3">
                        <p @class(['text-xl font-extrabold tracking-wide', 'text-brand-900' => $isActive])>{{ $voucher->code }}</p>
                        <span @class([
                            'shrink-0 px-2.5 py-1 rounded-md text-sm font-semibold',
                            'bg-brand-100 text-brand-800' => $isActive,
                            'bg-slate-200 text-slate-700' => ! $isActive,
                        ])>{{ $status['label'] }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">{{ $voucher->title }}</p>
                        <p>{{ $voucher->discountText() }}. {{ $voucher->minOrderText() }}. Rute: {{ $voucher->applicable_route ?: 'semua rute' }}.</p>
                    </div>
                    <p class="text-sm">
                        @if ($voucher->isUsed())
                            Dipakai {{ $voucher->used_at?->locale('id')->isoFormat('D MMM YYYY') }}.
                        @elseif ($voucher->expires_at)
                            {{ $isActive ? 'Berlaku sampai' : 'Berakhir' }} {{ $voucher->expires_at->locale('id')->isoFormat('D MMMM YYYY') }}.
                        @else
                            Tanpa batas waktu.
                        @endif
                        @isset($sourceLabels[$voucher->source])
                            {{ $sourceLabels[$voucher->source] }}.
                        @endisset
                    </p>
                    @if ($isActive)
                        <button type="button" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2" data-copy-code="{{ $voucher->code }}">Salin kode</button>
                    @endif
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

                try {
                    await navigator.clipboard.writeText(button.dataset.copyCode);
                    button.textContent = 'Tersalin';
                    status.textContent = `Kode ${button.dataset.copyCode} tersalin.`;
                } catch {
                    button.textContent = 'Salin manual';
                    status.textContent = 'Browser menolak menyalin. Pilih kodenya lalu salin manual.';
                }

                window.setTimeout(() => { button.textContent = 'Salin kode'; }, 2000);
            });
        });
    </script>
@endpush
