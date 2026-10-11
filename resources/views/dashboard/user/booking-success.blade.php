@extends('layouts.dashboard')

@section('page-title', 'E-ticket '.$booking->booking_code)

@php
    $schedule = $booking->schedule;
    $tripRoute = $schedule?->route;
    $departure = $schedule?->departureCarbon()->locale('id');
    $boardingOpens = $departure?->copy()->subMinutes(15);
    $qrValue = $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code;
    $returnUrl = url()->full();
@endphp

@section('content')
<div class="space-y-8 max-w-4xl">

    @include('dashboard.user.partials.flash')

    <header class="space-y-2">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Tiketmu sudah terbit</h1>
        <p class="text-lg text-slate-700">
            Pembayaran {{ 'Rp '.number_format($booking->total_price, 0, ',', '.') }} sudah masuk.
            @if ($booking->customerEmail())
                Salinan e-ticket dikirim ke <b>{{ $booking->customerEmail() }}</b>.
            @endif
        </p>
    </header>

    {{-- Boarding pass: same ticket shape as the search form and the Beranda card --}}
    <article class="grid md:grid-cols-[minmax(0,1fr)_260px] rounded-2xl overflow-hidden border border-brand-700 bg-white" aria-labelledby="tiket-rute">
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <p class="text-sm font-semibold text-slate-500">No. tiket {{ $booking->ticket?->ticket_number ?? '-' }}</p>
                <h2 id="tiket-rute" class="mt-2 flex flex-wrap items-baseline gap-x-3 text-2xl sm:text-3xl font-extrabold text-brand-900">
                    <span>{{ $tripRoute?->origin?->name }}</span>
                    <span class="text-lg font-semibold text-brand-600">ke</span>
                    <span>{{ $tripRoute?->destination?->name }}</span>
                </h2>
            </div>

            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm text-slate-500">Tanggal</dt>
                    <dd class="font-bold text-slate-900">{{ $departure?->isoFormat('D MMM YYYY') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Berangkat</dt>
                    <dd class="font-bold text-slate-900">{{ $departure?->format('H:i') }} WIB</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Kursi</dt>
                    <dd class="font-bold text-slate-900">{{ $booking->passengerSeatNumbers() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Kendaraan</dt>
                    <dd class="font-bold text-slate-900">{{ $schedule?->vehicle?->plate_number ?? 'Diumumkan hari-H' }}</dd>
                </div>
            </dl>

            <div>
                <h3 class="text-sm font-semibold text-slate-500">Penumpang</h3>
                <ul class="mt-2 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($booking->passengers as $passenger)
                        <li class="flex items-center justify-between gap-4 py-2">
                            <span class="font-semibold text-slate-900">{{ $passenger->name }}</span>
                            <span class="text-slate-600">Kursi {{ $passenger->seat?->seat_number ?? '-' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="text-slate-700">
                Boarding dibuka <b>{{ $boardingOpens?->format('H:i') }} WIB</b> di {{ $tripRoute?->origin?->name }}{{ $tripRoute?->origin?->address ? ', '.$tripRoute->origin->address : '' }}.
                Dibayar lewat {{ $booking->payment?->payment_method ?? 'Midtrans' }}.
            </p>
        </div>

        <div class="flex flex-col items-center justify-center gap-4 p-6 sm:p-8 bg-brand-800 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/50">
            <div class="w-44 h-44 rounded-lg bg-white p-3" data-qr="{{ $qrValue }}" role="img" aria-label="QR e-ticket {{ $booking->booking_code }}"></div>
            <div class="text-center">
                <p class="text-sm text-brand-100">Kode booking</p>
                <p class="text-2xl font-extrabold tracking-wide">{{ $booking->booking_code }}</p>
            </div>
            <p class="text-center text-sm text-brand-100">Tunjukkan QR ini ke petugas. Kalau tidak terbaca, sebutkan kode booking.</p>
        </div>
    </article>

    <div class="flex flex-wrap items-center gap-3 print:hidden">
        <button type="button" onclick="window.print()" class="lp-btn lp-btn--ghost">Cetak tiket</button>
        @auth
            <a href="{{ route('dashboard.user.user-tiket') }}" class="lp-btn lp-btn--green">Lihat semua tiketku</a>
        @else
            <a href="{{ route('home') }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Kembali ke halaman utama</a>
        @endauth
    </div>

    @guest
        <section class="rounded-xl border border-brand-300 bg-brand-50 p-6 print:hidden" aria-labelledby="simpan-tiket">
            <h2 id="simpan-tiket" class="text-lg font-extrabold text-brand-900">Simpan tiket ini di akun</h2>
            <p class="mt-2 text-slate-700">
                Tanpa akun, tiket ini hanya ada di email tadi. Masuk sekarang supaya tiket tersimpan di akunmu dan pembatalan bisa diajukan sendiri.
            </p>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <a href="{{ route('auth.google.redirect', ['redirect' => $returnUrl]) }}"
                   class="inline-flex items-center gap-3 min-h-11 px-4 rounded-lg border border-slate-300 bg-white font-semibold text-slate-900 hover:border-slate-500">
                    @include('auth.partials.google-mark')
                    Masuk dengan Google
                </a>
                <a href="{{ route('user.login', ['redirect' => $returnUrl]) }}" class="inline-flex items-center min-h-11 px-4 rounded-lg border-2 border-brand-800 font-semibold text-brand-800 hover:bg-white">
                    Masuk dengan email
                </a>
            </div>
        </section>
    @endguest
</div>

@include('dashboard.user.partials.qr-script')
@endsection
