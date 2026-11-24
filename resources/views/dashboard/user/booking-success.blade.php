@extends('layouts.dashboard')

@section('page-title', 'E-ticket '.$booking->booking_code)

@php
    $schedule = $booking->schedule;
    $tripRoute = $schedule?->route;
    $departure = $schedule?->departureCarbon()->locale('id');
    $boardingOpens = $departure?->copy()->subMinutes(15);
    $qrValue = $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code;
    $returnUrl = url()->full();
    $baseTicketNumber = $booking->ticket?->ticket_number ?? ('TKT-'.$booking->booking_code);
    $passengersList = $booking->passengers->values();
    $ticketItems = $passengersList->isNotEmpty()
        ? $passengersList->map(function ($passenger, $idx) use ($passengersList, $baseTicketNumber, $tripRoute, $departure, $schedule, $booking, $boardingOpens, $qrValue) {
            $seatNo = $passenger->seat?->seat_number ?? '-';
            $totalCount = $passengersList->count();
            $ticketNo = $totalCount > 1 ? "{$baseTicketNumber}-".($idx + 1) : $baseTicketNumber;
            $boardingNote = 'Boarding dibuka '.($boardingOpens?->format('H:i') ?? '-').' WIB di '.($tripRoute?->origin?->name ?? '-')
                .($tripRoute?->origin?->address ? ', '.$tripRoute->origin->address : '')
                .'. Dibayar lewat '.($booking->payment?->payment_method ?? 'Midtrans').'.';

            return [
                'ticketNumber' => $ticketNo,
                'ticketIndexLabel' => $totalCount > 1 ? 'Tiket '.($idx + 1).' dari '.$totalCount : '',
                'origin' => $tripRoute?->origin?->name ?? '-',
                'destination' => $tripRoute?->destination?->name ?? '-',
                'date' => $departure?->isoFormat('D MMM YYYY') ?? '-',
                'time' => ($departure?->format('H:i') ?? '-').' WIB',
                'seatNumber' => (string) $seatNo,
                'vehiclePlate' => $schedule?->vehicle?->plate_number ?? 'Diumumkan hari-H',
                'passengerName' => $passenger->name ?: $booking->customerName(),
                'boardingNote' => $boardingNote,
                'qrValue' => $qrValue,
                'bookingCode' => $booking->booking_code,
            ];
        })
        : collect([[
            'ticketNumber' => $baseTicketNumber,
            'ticketIndexLabel' => '',
            'origin' => $tripRoute?->origin?->name ?? '-',
            'destination' => $tripRoute?->destination?->name ?? '-',
            'date' => $departure?->isoFormat('D MMM YYYY') ?? '-',
            'time' => ($departure?->format('H:i') ?? '-').' WIB',
            'seatNumber' => $booking->passengerSeatNumbers(),
            'vehiclePlate' => $schedule?->vehicle?->plate_number ?? 'Diumumkan hari-H',
            'passengerName' => $booking->customerName(),
            'boardingNote' => 'Boarding dibuka '.($boardingOpens?->format('H:i') ?? '-').' WIB di '.($tripRoute?->origin?->name ?? '-')
                .($tripRoute?->origin?->address ? ', '.$tripRoute->origin->address : '')
                .'. Dibayar lewat '.($booking->payment?->payment_method ?? 'Midtrans').'.',
            'qrValue' => $qrValue,
            'bookingCode' => $booking->booking_code,
        ]]);
@endphp

@section('content')
<div class="space-y-8 max-w-4xl">

    @include('dashboard.user.partials.flash')

    <header class="space-y-2">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">
            {{ $ticketItems->count() > 1 ? $ticketItems->count().' Tiketmu sudah terbit' : 'Tiketmu sudah terbit' }}
        </h1>
        <p class="text-lg text-slate-700">
            Pembayaran {{ 'Rp '.number_format($booking->total_price, 0, ',', '.') }} sudah masuk untuk {{ $ticketItems->count() }} kursi.
            @if ($booking->customerEmail())
                Salinan e-ticket dikirim ke <b>{{ $booking->customerEmail() }}</b>.
            @endif
        </p>
    </header>

    {{-- Boarding pass per kursi / penumpang --}}
    <div class="space-y-6">
        @foreach ($ticketItems as $ticketItem)
            <article class="grid md:grid-cols-[minmax(0,1fr)_260px] rounded-2xl overflow-hidden border border-brand-700 bg-white break-inside-avoid" aria-label="E-ticket {{ $ticketItem['ticketNumber'] }}">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-500">No. tiket {{ $ticketItem['ticketNumber'] }}</p>
                        @if ($ticketItem['ticketIndexLabel'])
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-brand-100 text-brand-900">
                                {{ $ticketItem['ticketIndexLabel'] }}
                            </span>
                        @endif
                    </div>

                    <h2 class="flex flex-wrap items-baseline gap-x-3 text-2xl sm:text-3xl font-extrabold text-brand-900">
                        <span>{{ $ticketItem['origin'] }}</span>
                        <span class="text-lg font-semibold text-brand-600">ke</span>
                        <span>{{ $ticketItem['destination'] }}</span>
                    </h2>

                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm text-slate-500">Tanggal</dt>
                            <dd class="font-bold text-slate-900">{{ $ticketItem['date'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-500">Berangkat</dt>
                            <dd class="font-bold text-slate-900">{{ $ticketItem['time'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-500">Kursi</dt>
                            <dd class="font-bold text-slate-900">Kursi {{ $ticketItem['seatNumber'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-500">Kendaraan</dt>
                            <dd class="font-bold text-slate-900">{{ $ticketItem['vehiclePlate'] }}</dd>
                        </div>
                    </dl>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500">Penumpang</h3>
                        <div class="mt-2 flex items-center justify-between gap-4 py-2.5 border-y border-slate-200">
                            <span class="font-bold text-slate-900 text-lg">{{ $ticketItem['passengerName'] }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-brand-50 border border-brand-200 text-sm font-bold text-brand-900">
                                Kursi {{ $ticketItem['seatNumber'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-slate-700 text-sm flex-1 min-w-[220px]">
                            Boarding dibuka <b>{{ $boardingOpens?->format('H:i') }} WIB</b> di {{ $tripRoute?->origin?->name }}{{ $tripRoute?->origin?->address ? ', '.$tripRoute->origin->address : '' }}.
                            Dibayar lewat {{ $booking->payment?->payment_method ?? 'Midtrans' }}.
                        </p>
                        <button
                            type="button"
                            onclick='window.downloadTicketPng(@json($ticketItem))'
                            class="inline-flex items-center gap-2 min-h-10 px-4 rounded-lg border-2 border-brand-700 bg-brand-50 text-brand-900 text-sm font-bold hover:bg-brand-100 transition print:hidden cursor-pointer"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            <span>Download tiket{{ $ticketItems->count() > 1 ? ' (Kursi '.$ticketItem['seatNumber'].')' : '' }}</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-4 p-6 sm:p-8 bg-brand-800 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/50">
                    <div class="w-44 h-44 rounded-lg bg-white p-3" data-qr="{{ $ticketItem['qrValue'] }}" role="img" aria-label="QR e-ticket {{ $booking->booking_code }}"></div>
                    <div class="text-center">
                        <p class="text-sm text-brand-100">Kode booking</p>
                        <p class="text-2xl font-extrabold tracking-wide">{{ $booking->booking_code }}</p>
                    </div>
                    <p class="text-center text-sm text-brand-100">Tunjukkan QR ini ke petugas. Kalau tidak terbaca, sebutkan kode booking.</p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-3 print:hidden">
        <button type="button" onclick='window.downloadAllTicketsPng(@json($ticketItems->values()))' class="lp-btn lp-btn--green inline-flex items-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            <span>{{ $ticketItems->count() > 1 ? 'Download semua tiket ('.$ticketItems->count().')' : 'Download tiket' }}</span>
        </button>
        <button type="button" onclick="window.print()" class="lp-btn lp-btn--ghost">Cetak tiket</button>
        @auth
            <a href="{{ route('dashboard.user.user-tiket') }}" class="lp-btn lp-btn--ghost">Lihat semua tiketku</a>
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
@include('dashboard.user.partials.ticket-download-script')
@endsection
