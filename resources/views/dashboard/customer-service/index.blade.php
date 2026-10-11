@extends('layouts.customer-service')

@section('title', 'Beranda Customer Service - TravelKu')

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name ?: '-';
    $statusBadge = fn (string $status) => match (strtolower($status)) {
        'paid', 'confirmed', 'success', 'lunas' => ['Lunas', 'ok'],
        'pending' => ['Menunggu bayar', 'warn'],
        'completed' => ['Selesai', 'muted'],
        'cancelled', 'expired', 'batal' => ['Batal', 'danger'],
        default => [ucfirst($status), 'muted'],
    };
@endphp

@section('content')
<div class="ds">
    <header class="ds-head">
        <div>
            <h1>Halo, {{ \Illuminate\Support\Str::before(auth()->user()->name ?? 'CS', ' ') }}.</h1>
            <p>{{ now()->translatedFormat('l, d F Y') }}. Cari pesanan penumpang dari kode booking, nomor tiket, nama, email, atau nomor HP.</p>
        </div>
    </header>

    {{-- Lookup: the first thing a CS agent needs when a passenger calls --}}
    <section class="ds-panel" aria-labelledby="cari-pesanan">
        <div class="ds-panel-head">
            <h2 id="cari-pesanan">Cari pesanan</h2>
        </div>
        <div class="ds-panel-body">
            <form method="GET" action="{{ route('dashboard.customer-service.index') }}" class="ds-search" role="search">
                <label for="cs-lookup" class="ds-visually-hidden">Kode booking, nomor tiket, nama, email, atau nomor HP</label>
                <input type="search" id="cs-lookup" name="q" value="{{ $query }}" placeholder="TRV-20261011-ABCDE, nama, atau 0812..." autocomplete="off">
                <button type="submit" class="ds-btn ds-btn--primary">Cari</button>
                @if ($query !== '')
                    <a href="{{ route('dashboard.customer-service.index') }}" class="ds-btn ds-btn--secondary">Hapus pencarian</a>
                @endif
            </form>
        </div>

        @if ($query !== '')
            @if ($searchResults->isEmpty())
                <p class="ds-empty"><b>Tidak ada pesanan yang cocok dengan "{{ $query }}".</b> Coba kode booking lengkap, atau nomor HP tanpa spasi.</p>
            @else
                <div class="ds-table-wrap">
                    <table class="ds-table">
                        <caption class="ds-visually-hidden">Hasil pencarian pesanan</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Penumpang</th>
                                <th scope="col">Perjalanan</th>
                                <th scope="col">Kursi</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($searchResults as $booking)
                                @php [$label, $tone] = $statusBadge((string) $booking->status); @endphp
                                <tr>
                                    <td>
                                        <b>{{ $booking->booking_code }}</b>
                                        @if ($booking->ticket)
                                            <div class="ds-sub">{{ $booking->ticket->ticket_number }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $booking->user?->name ?? $booking->passengers->first()?->name ?? '-' }}
                                        <div class="ds-sub">{{ $booking->user?->phone ?: $booking->user?->email }}</div>
                                    </td>
                                    <td>
                                        {{ $cityOf($booking->schedule?->route?->origin) }} ke {{ $cityOf($booking->schedule?->route?->destination) }}
                                        @if ($booking->schedule)
                                            <div class="ds-sub">{{ $booking->schedule->departureCarbon()->translatedFormat('d M Y, H:i') }} WIB</div>
                                        @endif
                                    </td>
                                    <td>{{ $booking->passengerSeatNumbers() ?: $booking->total_passenger.' orang' }}</td>
                                    <td><span class="ds-badge ds-badge--{{ $tone }}">{{ $label }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </section>

    <dl class="ds-stats">
        <div class="ds-stat ds-stat--lead">
            <dt>Keberangkatan tersisa hari ini</dt>
            <dd>{{ $stats['departures_left'] }}<small>dari {{ $stats['departures_today'] }} jadwal</small></dd>
        </div>
        <div class="ds-stat">
            <dt>Pembayaran belum selesai</dt>
            <dd>{{ $stats['pending_payments'] }}<small>masih dalam batas waktu</small></dd>
        </div>
        <div class="ds-stat">
            <dt>Refund menunggu admin</dt>
            <dd>{{ $stats['pending_refunds'] }}<small>diproses admin utama</small></dd>
        </div>
    </dl>

    <div class="ds-grid-2">
        <section class="ds-panel" aria-labelledby="berangkat-berikutnya">
            <div class="ds-panel-head">
                <h2 id="berangkat-berikutnya">Berangkat berikutnya</h2>
                <p>Hari ini, termasuk yang baru berangkat 30 menit terakhir.</p>
            </div>
            @if ($nextDepartures->isEmpty())
                <p class="ds-empty"><b>Tidak ada keberangkatan lagi hari ini.</b> Jadwal besok muncul di sini mulai tengah malam.</p>
            @else
                <ol class="ds-list">
                    @foreach ($nextDepartures as $schedule)
                        <li>
                            <b>{{ $schedule->departureCarbon()->format('H:i') }}, {{ $schedule->route?->origin?->name ?? '-' }} ke {{ $schedule->route?->destination?->name ?? '-' }}</b>
                            <span class="ds-sub">
                                {{ $schedule->vehicle?->name ?? 'Kendaraan belum ditugaskan' }}@if ($schedule->vehicle), {{ $schedule->vehicle->plate_number }}@endif.
                                Sopir {{ $schedule->driver?->user?->name ?? 'belum ditugaskan' }}.
                                Sisa {{ $schedule->available_seats }} kursi.
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <section class="ds-panel" aria-labelledby="menunggu-bayar">
            <div class="ds-panel-head">
                <h2 id="menunggu-bayar">Menunggu pembayaran</h2>
                <p>Kursi dilepas otomatis saat batas waktu habis.</p>
            </div>
            @if ($pendingBookings->isEmpty())
                <p class="ds-empty"><b>Tidak ada pembayaran tertunda.</b></p>
            @else
                <ul class="ds-list">
                    @foreach ($pendingBookings as $booking)
                        <li>
                            <b>{{ $booking->booking_code }}, {{ $booking->user?->name ?? 'Penumpang' }}</b>
                            <span class="ds-sub">
                                {{ $cityOf($booking->schedule?->route?->origin) }} ke {{ $cityOf($booking->schedule?->route?->destination) }}.
                                Batas bayar {{ $booking->expired_at->format('H:i') }} WIB ({{ $booking->expired_at->diffForHumans() }}).
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</div>
@endsection
