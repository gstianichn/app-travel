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
            <p>{{ now()->translatedFormat('l, d F Y') }}.</p>
        </div>
    </header>

    @if ($query !== '')
        <section class="ds-panel" aria-labelledby="hasil-pencarian">
            <div class="ds-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 id="hasil-pencarian">Hasil pencarian "{{ $query }}"</h2>
                <a href="{{ route('dashboard.customer-service.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; padding: 6px 14px;">Hapus pencarian</a>
            </div>

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
                                        @php
                                            $bookerName = $booking->contact_name ?: ($booking->user?->name ?: null);
                                            $passengersList = $booking->passengers->pluck('name')->filter()->unique()->values();
                                            $primaryName = $bookerName ?: ($passengersList->first() ?: '-');
                                            $phone = $booking->contact_phone ?: ($booking->user?->phone ?: ($booking->passengers->first()?->phone ?: ''));
                                            $email = $booking->contact_email ?: ($booking->user?->email ?: '');
                                        @endphp
                                        <b>{{ $primaryName }}</b>
                                        @if ($passengersList->isNotEmpty() && ($passengersList->count() > 1 || ($bookerName && ! $passengersList->contains($bookerName))))
                                            <div class="ds-sub" style="font-size: 11px;">
                                                Penumpang: {{ $passengersList->implode(', ') }}
                                            </div>
                                        @endif
                                        <div class="ds-sub">{{ $phone ?: $email }}</div>
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
        </section>
    @endif

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
