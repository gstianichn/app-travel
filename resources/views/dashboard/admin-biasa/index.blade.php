<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff - Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Styling CSS Khusus Dashboard Staff Operasional --}}
    @include('dashboard.admin-biasa.partials.styles')
    @fonts('overpass')
    {{-- Brand skin and ds- components: loaded last so it wins over the role's own styles --}}
    @vite(['resources/css/dashboard-staff.css'])
</head>

<body data-staff="staff">

<div class="dashboard-wrapper">

    {{-- Sidebar Navigasi Menu Utama --}}
    @include('dashboard.admin-biasa.partials.sidebar')

    {{-- Konten Utama --}}
    <main class="main-content">

        {{-- Topbar Staff & Pool Lokasi --}}
        @include('dashboard.admin-biasa.partials.topbar')

        {{-- Section Isi Dashboard --}}
        <section class="content">
            <div class="ds">
                <header class="ds-head">
                    <div>
                        <h1>Halo, {{ \Illuminate\Support\Str::before($shiftInfo['staff_name'], ' ') }}.</h1>
                        <p>{{ $shiftInfo['active_date'] }}@if ($shiftInfo['pool_name']), {{ $shiftInfo['pool_name'] }}@endif</p>
                    </div>
                    <div class="ds-head-actions">
                        <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="ds-btn ds-btn--primary">+ Input Pesanan Offline (Loket)</a>
                        <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}" class="ds-btn ds-btn--accent">Pindai QR atau cari kode booking</a>
                    </div>
                </header>

                <div class="ds-flow-grid">
                    <section class="ds-flow-card ds-flow-card--online" aria-labelledby="flow-online-title">
                        <div class="ds-flow-head">
                            <h3 id="flow-online-title">Alur Customer Online (Website / Aplikasi)</h3>
                            <span class="ds-channel-badge ds-channel-badge--online">Online Web/App</span>
                        </div>
                        <ol class="ds-flow-steps">
                            <li>Customer memesan jadwal, memilih kursi, dan membayar lewat Website/Aplikasi.</li>
                            <li>Saat tiba di pool, customer menunjukkan QR E-Ticket atau Kode Booking (`TRV-...`).</li>
                            <li>Staf pindai/periksa di <strong>Validasi E-Ticket</strong>: jika status <em>Lunas</em>, langsung tercatat <strong>Boarded</strong>. Jika masih <em>Pending</em>, proses pelunasan di loket sebelum naik.</li>
                        </ol>
                        <div>
                            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}" class="ds-btn ds-btn--secondary">Buka Validasi E-Ticket</a>
                        </div>
                    </section>

                    <section class="ds-flow-card ds-flow-card--offline" aria-labelledby="flow-offline-title">
                        <div class="ds-flow-head">
                            <h3 id="flow-offline-title">Alur Customer Offline (Loket / WA / Telepon)</h3>
                            <span class="ds-channel-badge ds-channel-badge--offline">Non-Website</span>
                        </div>
                        <ol class="ds-flow-steps">
                            <li>Customer datang ke pool atau memesan via WhatsApp/telepon tanpa membuka website/app.</li>
                            <li>Staf buka <strong>Pemesanan Loket (Offline)</strong>, pilih jadwal & kursi kosong di denah real-time (sinkron dengan kursi Online agar tidak bentrok).</li>
                            <li>Terima pembayaran (<em>Cash / QRIS / Transfer</em>) atau tahan kursi sementara untuk bayar di pool, lalu tiket aktif & siap boarding.</li>
                        </ol>
                        <div>
                            <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="ds-btn ds-btn--primary">Input Pesanan Loket Sekarang</a>
                        </div>
                    </section>
                </div>

                <dl class="ds-stats">
                    <div class="ds-stat ds-stat--lead">
                        <dt>Boarding dibuka sekarang</dt>
                        <dd>{{ $stats['boarding_now'] }}<small>keberangkatan</small></dd>
                    </div>
                    <div class="ds-stat">
                        <dt>Jadwal hari ini</dt>
                        <dd>{{ $stats['active_schedules'] }}<small>belum selesai</small></dd>
                    </div>
                    <div class="ds-stat">
                        <dt>Masih persiapan</dt>
                        <dd>{{ $stats['ready_preparation'] }}<small>dari daftar di bawah</small></dd>
                    </div>
                    <div class="ds-stat">
                        <dt>Pesanan untuk hari ini</dt>
                        <dd>{{ $stats['today_bookings'] }}<small>tiket</small></dd>
                    </div>
                </dl>

                <section class="ds-panel" aria-labelledby="keberangkatan-terdekat">
                    <div class="ds-panel-head">
                        <div>
                            <h2 id="keberangkatan-terdekat">Keberangkatan terdekat</h2>
                            <p>Boarding dibuka 15 menit sebelum jam berangkat.</p>
                        </div>
                        <a href="{{ route('dashboard.admin-biasa.index') }}" class="ds-link">Muat ulang</a>
                    </div>

                    @if ($departures->isEmpty())
                        <p class="ds-empty"><b>Belum ada jadwal mendatang.</b> Jadwal muncul di sini setelah admin utama membuatnya, atau saat penumpang mencari rute untuk tanggal tertentu.</p>
                    @else
                        <ol class="ds-departures">
                            @foreach ($departures as $dep)
                                @php
                                    $badge = match ($dep->status) {
                                        'Boarding Dibuka' => 'ok',
                                        'Persiapan' => 'muted',
                                        'Sudah berangkat' => 'info',
                                        default => 'muted',
                                    };
                                @endphp
                                <li class="ds-departure">
                                    <p class="ds-time">{{ str_replace(' WIB', '', $dep->time) }}<small>{{ $dep->departure_note }}</small></p>
                                    <div class="ds-route">
                                        <b>{{ $dep->origin }}</b> <span>ke</span> <b>{{ $dep->destination }}</b>
                                        <p class="ds-sub">{{ $dep->vehicle_name }}, {{ $dep->plate_number }}. Sopir: {{ $dep->driver_name }}</p>
                                    </div>
                                    <div class="ds-seats">
                                        <span>{{ $dep->occupied_seats }} dari {{ $dep->total_seats }} kursi terisi</span>
                                        <span class="ds-meter" aria-hidden="true"><span style="width: {{ $dep->percentage }}%"></span></span>
                                    </div>
                                    <span class="ds-badge ds-badge--{{ $badge }}">{{ $dep->status }}</span>
                                    <div class="ds-actions">
                                        <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}?schedule_id={{ $dep->id }}" class="ds-btn ds-btn--primary">Validasi tiket</a>
                                        <a href="{{ route('dashboard.admin-biasa.booking') }}?schedule_id={{ $dep->id }}" class="ds-btn ds-btn--secondary">Manifes</a>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>

                <div class="ds-grid-2">
                    <section class="ds-panel" aria-labelledby="aktivitas">
                        <div class="ds-panel-head">
                            <h2 id="aktivitas">Aktivitas terakhir</h2>
                        </div>
                        @if ($recentActivities->isEmpty())
                            <p class="ds-empty"><b>Belum ada aktivitas.</b> Pesanan baru dan tiket yang divalidasi akan tercatat di sini.</p>
                        @else
                            <ul class="ds-list">
                                @foreach ($recentActivities as $activity)
                                    <li>
                                        <b>{{ $activity->title }}</b>
                                        <span class="ds-sub">{{ $activity->desc }}</span>
                                        <span class="ds-sub">{{ $activity->time_text }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>

                    <nav aria-label="Menu staf pool">
                        <ul class="ds-nav">
                            <li><a href="{{ route('dashboard.admin-biasa.offline-booking') }}"><b>Pemesanan loket (Offline)</b><span>Input pesanan walk-in, WA, atau telepon</span></a></li>
                            <li><a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}"><b>Validasi tiket</b><span>Pindai QR atau cari kode booking</span></a></li>
                            <li><a href="{{ route('dashboard.admin-biasa.booking') }}"><b>Manifes booking</b><span>Penumpang per keberangkatan</span></a></li>
                            <li><a href="{{ route('dashboard.admin-biasa.perjalanan') }}"><b>Informasi perjalanan</b><span>Jadwal, rute, dan status armada</span></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>

    </main>

</div>


</body>

</html>
