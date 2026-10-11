@extends('layouts.admin')

@section('title', 'Beranda admin')

@section('page-title', 'Halo, '.\Illuminate\Support\Str::before($user->name ?? 'Admin', ' ').'.')

@section('page-subtitle', now()->translatedFormat('l, d F Y'))

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name ?: '-';
    $documentAlerts = $expiringDrivers->count() + $expiringVehicles->count() + $maintenanceVehicles->count();
@endphp

@section('content')
<div class="ds">

    {{-- Today's numbers. Revenue leads because it is the one figure the admin reports upward. --}}
    <dl class="ds-stats" aria-label="Angka hari ini">
        <div class="ds-stat ds-stat--lead">
            <dt>Pendapatan hari ini</dt>
            <dd>Rp {{ number_format($revenueToday, 0, ',', '.') }}<small>dari pesanan lunas</small></dd>
        </div>
        <div class="ds-stat">
            <dt>Tiket terjual</dt>
            <dd>{{ number_format($ticketsSoldToday, 0, ',', '.') }}<small>kursi, hari ini</small></dd>
        </div>
        <div class="ds-stat">
            <dt>Armada terjadwal</dt>
            <dd>{{ $activeFleetCount }}<small>dari {{ $totalVehicles }} unit</small></dd>
        </div>
        <div class="ds-stat">
            <dt>Kursi kosong</dt>
            <dd>{{ number_format($emptySeatsToday, 0, ',', '.') }}<small>di jadwal hari ini</small></dd>
        </div>
    </dl>

    {{-- Things that need a decision come before things that are only informative --}}
    @if ($totalPendingRefunds > 0 || $documentAlerts > 0)
        <div class="ds-grid-2">
            <section class="ds-panel" aria-labelledby="refund-menunggu">
                <div class="ds-panel-head">
                    <h2 id="refund-menunggu">Refund menunggu keputusan</h2>
                    <a href="{{ route('dashboard.admin.refund.index') }}" class="ds-link">Buka refund</a>
                </div>
                @if ($pendingRefunds->isEmpty())
                    <p class="ds-empty"><b>Tidak ada refund yang menunggu.</b></p>
                @else
                    <ul class="ds-list">
                        @foreach ($pendingRefunds as $refund)
                            <li>
                                <b>{{ $refund->booking?->booking_code ?? 'Booking #'.$refund->booking_id }}, {{ $refund->user?->name ?? 'Penumpang' }}</b>
                                <span class="ds-sub">
                                    Rp {{ number_format((float) ($refund->amount ?? $refund->original_amount ?? 0), 0, ',', '.') }}.
                                    Diajukan {{ $refund->created_at?->diffForHumans() }}.
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="ds-panel" aria-labelledby="dokumen">
                <div class="ds-panel-head">
                    <h2 id="dokumen">Dokumen dan armada</h2>
                    <p>SIM, KIR, dan STNK yang habis dalam 30 hari.</p>
                </div>
                @if ($documentAlerts === 0)
                    <p class="ds-empty"><b>Semua dokumen masih berlaku.</b></p>
                @else
                    <ul class="ds-list">
                        @foreach ($expiringDrivers as $driver)
                            <li>
                                <b>SIM {{ $driver->user?->name ?? 'Driver #'.$driver->id }}</b>
                                <span class="ds-sub">Berlaku sampai {{ $driver->license_expired_at ? \Illuminate\Support\Carbon::parse($driver->license_expired_at)->translatedFormat('d M Y') : '-' }}</span>
                            </li>
                        @endforeach
                        @foreach ($expiringVehicles as $vehicle)
                            <li>
                                <b>{{ $vehicle->name }}, {{ $vehicle->plate_number }}</b>
                                <span class="ds-sub">
                                    KIR {{ $vehicle->kir_expiry_date?->translatedFormat('d M Y') ?? '-' }},
                                    STNK {{ $vehicle->stnk_tax_due_date?->translatedFormat('d M Y') ?? '-' }}
                                </span>
                            </li>
                        @endforeach
                        @foreach ($maintenanceVehicles as $vehicle)
                            <li>
                                <b>{{ $vehicle->name }} masuk bengkel</b>
                                <span class="ds-sub">{{ $vehicle->plate_number }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    @endif

    <section class="ds-panel" aria-labelledby="jadwal-terdekat">
        <div class="ds-panel-head">
            <div>
                <h2 id="jadwal-terdekat">Jadwal terdekat</h2>
                <p>Termasuk yang baru berangkat 30 menit terakhir.</p>
            </div>
            <a href="{{ route('dashboard.admin.rute.jadwal') }}" class="ds-link">Kelola jadwal</a>
        </div>

        @if ($upcomingSchedules->isEmpty())
            <p class="ds-empty"><b>Belum ada jadwal mendatang.</b> Buat jadwal di menu Rute Perjalanan.</p>
        @else
            <ol class="ds-departures">
                @foreach ($upcomingSchedules as $schedule)
                    @php
                        $capacity = $schedule->vehicle?->capacity ?: 10;
                        $filled = max(0, $capacity - (int) $schedule->available_seats);
                        $percent = (int) min(100, round($filled / max(1, $capacity) * 100));
                        $departureAt = $schedule->departureCarbon();
                    @endphp
                    <li class="ds-departure">
                        <p class="ds-time">{{ $departureAt->format('H:i') }}<small>{{ $departureAt->isToday() ? 'hari ini' : $departureAt->translatedFormat('d M') }}</small></p>
                        <div class="ds-route">
                            <b>{{ $schedule->route?->origin?->name ?? '-' }}</b> <span>ke</span> <b>{{ $schedule->route?->destination?->name ?? '-' }}</b>
                            <p class="ds-sub">{{ $schedule->vehicle?->name ?? 'Kendaraan belum ditugaskan' }}{{ $schedule->vehicle ? ', '.$schedule->vehicle->plate_number : '' }}. Sopir: {{ $schedule->driver?->user?->name ?? 'belum ditugaskan' }}</p>
                        </div>
                        <div class="ds-seats">
                            <span>{{ $filled }} dari {{ $capacity }} kursi terisi</span>
                            <span class="ds-meter" aria-hidden="true"><span style="width: {{ $percent }}%"></span></span>
                        </div>
                        <span class="ds-badge ds-badge--{{ $departureAt->isPast() ? 'info' : 'muted' }}">{{ $departureAt->isPast() ? 'Sudah berangkat' : 'Terjadwal' }}</span>
                        <div class="ds-actions">
                            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="ds-btn ds-btn--secondary">Pesanan</a>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>

    <div class="ds-grid-2">
        <section class="ds-panel" aria-labelledby="penumpang-baru">
            <div class="ds-panel-head">
                <h2 id="penumpang-baru">Penumpang baru</h2>
                <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="ds-link">Semua {{ $totalUsers }} akun</a>
            </div>
            @if ($recentUsers->isEmpty())
                <p class="ds-empty"><b>Belum ada akun penumpang.</b></p>
            @else
                <ul class="ds-list">
                    @foreach ($recentUsers as $recentUser)
                        <li>
                            <b>{{ $recentUser->name }}</b>
                            <span class="ds-sub">{{ $recentUser->email }}, daftar {{ $recentUser->created_at?->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <nav aria-label="Menu admin">
            <ul class="ds-nav">
                <li><a href="{{ route('dashboard.admin.pemesanan.index') }}"><b>Pemesanan</b><span>{{ number_format($totalBookings, 0, ',', '.') }} pesanan tercatat</span></a></li>
                <li><a href="{{ route('dashboard.admin.pembayaran.index') }}"><b>Pembayaran</b><span>Verifikasi transfer</span></a></li>
                <li><a href="{{ route('dashboard.admin.rute.index') }}"><b>Rute perjalanan</b><span>Tarif dan jadwal</span></a></li>
                <li><a href="{{ route('dashboard.admin.kelola-driver.index') }}"><b>Kelola driver</b><span>{{ $totalDrivers }} driver</span></a></li>
                <li><a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}"><b>Armada</b><span>{{ $totalVehicles }} unit</span></a></li>
                <li><a href="{{ route('dashboard.admin.laporan.index') }}"><b>Laporan</b><span>Ekspor Excel atau PDF</span></a></li>
            </ul>
        </nav>
    </div>
</div>
@endsection
