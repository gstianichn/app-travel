@extends('layouts.driver')

@section('title', 'Beranda Driver - TravelKu')

@section('content')
<div class="ds">

    {{-- 1. HEADER SAMBUTAN & STATUS TUGAS OPERASIONAL --}}
    <header class="ds-head">
        <div>
            <h1>Halo, {{ \Illuminate\Support\Str::before($user->name ?? 'Pengemudi', ' ') }}.</h1>
            <p>
                {{ now()->translatedFormat('l, d F Y') }} &bull;
                @if($hasAssignedSchedule)
                    Armada: <strong>{{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}</strong>
                    ({{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }})
                @else
                    Status: <strong style="color: #087f55;">Standby (Siap Ditugaskan)</strong>
                @endif
            </p>
        </div>
        <div class="ds-head-actions">
            @if(!empty($nextAction['next_key']) && $activeSchedule->exists && $hasAssignedSchedule)
                <form action="{{ route('dashboard.driver.schedule.status', $activeSchedule) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="status" value="{{ $nextAction['next_key'] }}">
                    <button type="submit" class="ds-btn ds-btn--accent">
                        <span>{{ $nextAction['label'] ?? 'Lanjut Tahap Berikutnya' }}</span>
                    </button>
                </form>
            @endif

            @if($hasAssignedSchedule)
                <a href="{{ route('dashboard.driver.ubah-status') }}" class="ds-btn ds-btn--secondary">
                    Ubah Status Trip
                </a>
            @endif

            <button type="button" onclick="openIncidentModal()" class="ds-btn ds-btn--secondary">
                Lapor Kendala
            </button>

            <button type="button" onclick="openSosModal()" class="ds-btn ds-btn--danger">
                SOS Darurat
            </button>
        </div>
    </header>

    @if(!$hasAssignedSchedule)
        <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px; padding: 32px 24px; text-align: center; margin-bottom: 24px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 12px;">
                🚐
            </div>
            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 6px;">Tidak Ada Penugasan Jadwal Perjalanan Hari Ini</h3>
            <p style="font-size: 13px; color: #64748b; max-width: 500px; margin: 0 auto 16px;">
                Saat ini akun Anda dalam status <strong>Standby</strong>. Jika ada tugas perjalanan baru yang dijadwalkan oleh koordinator pool, data jadwal akan langsung muncul di sini.
            </p>
            <div style="display: inline-flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                <a href="{{ route('dashboard.driver.riwayat') }}" class="ds-btn ds-btn--secondary">Lihat Riwayat Perjalanan</a>
                <button type="button" onclick="openIncidentModal()" class="ds-btn ds-btn--secondary">Lapor Kendala Armada</button>
            </div>
        </div>
    @endif

    {{-- 2. KARTU ANGKA & STATISTIK KUNCI OPERASIONAL --}}
    <dl class="ds-stats" aria-label="Statistik operasional hari ini">
        <div class="ds-stat ds-stat--lead">
            <dt>Jadwal aktif</dt>
            <dd>
                {{ $activeSchedule->formattedDepartureTime() }}
                <small>{{ $activeSchedule->tripStatusLabel() }} &bull; {{ $activeSchedule->tripCode() }}</small>
            </dd>
        </div>
        <div class="ds-stat">
            <dt>Indikator Cepat Ketersediaan Kursi</dt>
            <dd>
                {{ $totalBookedPassengers }} / {{ $seatCapacity }}
                <small><span>Penumpang Terisi</span>: {{ $totalBookedPassengers }} &bull; <span>Kursi Kosong</span>: {{ $emptySeats }}</small>
            </dd>
        </div>
        <div class="ds-stat">
            <dt>Penumpang terabsen</dt>
            <dd>
                {{ $boardedCount }} / {{ $totalBookedPassengers }}
                <small>{{ $pendingBoardingCount }} belum naik kabin</small>
            </dd>
        </div>
        <div class="ds-stat">
            <dt>Tagihan COD / Tunai</dt>
            <dd>
                Rp {{ number_format($manifestPassengers->where('is_paid', false)->sum('amount_due'), 0, ',', '.') }}
                <small>{{ $manifestPassengers->where('is_paid', false)->count() }} penumpang bayar di armada</small>
            </dd>
        </div>
    </dl>

    {{-- 3. GRID 2 KOLOM: PERJALANAN AKTIF & ARMADA DITUGASKAN --}}
    <div class="ds-grid-2">
        <!-- Panel 1: Detail Perjalanan & Navigasi Rute Aktif -->
        <section class="ds-panel" id="section-jadwal" aria-labelledby="perjalanan-aktif">
            <div class="ds-panel-head">
                <div>
                    <h2 id="perjalanan-aktif">Ringkasan Operasional Harian Berjalan</h2>
                    <p><strong>Status Tugas Hari Ini</strong>: {{ $activeSchedule->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)' }} ke {{ $activeSchedule->route?->destination?->name ?? 'Pool Semanggi (Jakarta)' }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="ds-badge ds-badge--ok">{{ $activeSchedule->tripStatusLabel() }}</span>
                    <a href="{{ route('dashboard.driver.rute') }}" class="ds-link">Peta Rute</a>
                </div>
            </div>
            <div class="ds-panel-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 20px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--lp-muted); text-transform: uppercase;">Jam Keberangkatan</div>
                        <div style="font-size: 17px; font-weight: 800; color: var(--lp-forest); margin-top: 2px;">{{ $activeSchedule->formattedDepartureTime() }} WIB</div>
                        <div style="font-size: 12px; color: var(--lp-muted);">{{ $activeSchedule->formattedDepartureDate() }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--lp-muted); text-transform: uppercase;">Estimasi Waktu Tiba</div>
                        <div style="font-size: 17px; font-weight: 800; color: var(--lp-forest); margin-top: 2px;">{{ $activeSchedule->estimatedArrivalTime() }} WIB</div>
                        <div style="font-size: 12px; color: var(--lp-muted);">Panduan Ritme Waktu &bull; {{ $activeSchedule->tripStatusLabel() ?? 'Persiapan Armada' }} (± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }})</div>
                    </div>
                </div>

                <!-- Target Penjemputan / Titik Singgah Berikutnya -->
                <div style="padding: 16px; border-radius: 12px; background: var(--lp-soft, #EEF6F1); border: 1px solid var(--lp-line); margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--lp-green, #0C7950); letter-spacing: 0.5px;">
                            🎯 Pintasan Aksi Berikutnya (Next Action Card) &bull; {{ $nextTargetType }}
                        </span>
                        <span class="ds-badge ds-badge--muted">{{ $nextSeatNumber }}</span>
                    </div>
                    <div style="font-size: 15px; font-weight: 800; color: var(--lp-ink);">{{ $nextTargetTitle }}</div>
                    <div style="font-size: 12.5px; color: var(--lp-muted); margin-top: 4px;">📍 {{ $nextTargetAddress }}</div>
                    @if($nextTargetNotes)
                        <div style="font-size: 12px; color: var(--lp-forest); margin-top: 6px; font-style: italic;">
                            Catatan: "{{ $nextTargetNotes }}"
                        </div>
                    @endif
                    <div style="display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap;">
                        <a href="{{ $nextGmapsUrl }}" target="_blank" rel="noopener" class="ds-btn ds-btn--primary" style="font-size: 12px; min-height: 36px; padding: 0 14px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Buka Google Maps
                        </a>
                        <a href="{{ $nextWazeUrl }}" target="_blank" rel="noopener" class="ds-btn ds-btn--secondary" style="font-size: 12px; min-height: 36px; padding: 0 14px;">
                            Buka Waze
                        </a>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px;">
                    <span style="font-size: 12.5px; color: var(--lp-muted);">Status Keberangkatan: <strong>{{ $activeSchedule->tripStatusLabel() }}</strong></span>
                    <a href="{{ route('dashboard.driver.status') }}" class="ds-link">Lihat Log Status</a>
                </div>
            </div>
        </section>

        <!-- Panel 2: Detail Armada Ditugaskan -->
        <section class="ds-panel" id="section-armada" aria-labelledby="armada-unit">
            <div class="ds-panel-head">
                <div>
                    <h2 id="armada-unit">Persiapan Armada</h2>
                    <p>{{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}</p>
                </div>
                <a href="{{ route('dashboard.driver.kendaraan') }}" class="ds-link">Detail Dokumen</a>
            </div>
            <div class="ds-panel-body">
                <div style="display: flex; align-items: center; gap: 14px; padding-bottom: 16px; border-bottom: 1px solid var(--lp-line); margin-bottom: 16px;">
                    <div style="width: 52px; height: 52px; border-radius: 12px; background: var(--lp-soft, #EEF6F1); display: flex; align-items: center; justify-content: center; color: var(--lp-forest); font-size: 24px; flex-shrink: 0;">
                        🚐
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 16px; font-weight: 800; color: var(--lp-forest);">{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</span>
                            <span class="ds-badge ds-badge--muted">Nomor Lambung Armada: {{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}</span>
                        </div>
                        <div style="font-size: 12.5px; color: var(--lp-muted); margin-top: 3px;">
                            {{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }} &bull; Kapasitas {{ $activeSchedule->vehicle?->capacity ?? 14 }} Kursi
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 12.5px; margin-bottom: 16px;">
                    <div>
                        <span style="color: var(--lp-muted); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Lokasi Fisik Parkir</span>
                        <strong style="color: var(--lp-ink); font-size: 13px;">{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--lp-muted); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Status STNK & Pajak</span>
                        <strong style="color: var(--lp-forest); font-size: 13px;">{{ $activeSchedule->vehicle?->stnk_tax_due_date ? Carbon\Carbon::parse($activeSchedule->vehicle->stnk_tax_due_date)->translatedFormat('d M Y') : 'Berlaku Aktif' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--lp-muted); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Uji Berkala KIR</span>
                        <strong style="color: var(--lp-forest); font-size: 13px;">{{ $activeSchedule->vehicle?->kir_expiry_date ? Carbon\Carbon::parse($activeSchedule->vehicle->kir_expiry_date)->translatedFormat('d M Y') : 'Lolos Uji' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--lp-muted); display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;">Kesiapan Operasional</span>
                        <strong style="color: #0C7950; font-size: 13px;">Layak Jalan (Siap)</strong>
                    </div>
                </div>

                <div style="padding-top: 14px; border-top: 1px solid var(--lp-line); display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('dashboard.driver.seatmap') }}" class="ds-btn ds-btn--secondary" style="font-size: 12px; min-height: 36px; padding: 0 14px;">
                        Buka Seat Map Kabin
                    </a>
                    <a href="{{ route('dashboard.driver.jadwal') }}" class="ds-link">Lihat Jadwal Lain</a>
                </div>
            </div>
        </section>
    </div>

    {{-- 4. PANEL MANIFES PENUMPANG JADWAL AKTIF --}}
    <section class="ds-panel" id="section-manifest" aria-labelledby="manifes-penumpang">
        <div class="ds-panel-head">
            <div>
                <h2 id="manifes-penumpang">Manifes Penumpang Hari Ini</h2>
                <p>{{ $boardedCount }} dari {{ $totalBookedPassengers }} penumpang sudah naik kabin.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="{{ route('dashboard.driver.penumpang') }}" class="ds-btn ds-btn--primary" style="font-size: 12.5px; min-height: 36px; padding: 0 14px;">
                    Buka Manifes Lengkap
                </a>
            </div>
        </div>

        @if ($manifestPassengers->isEmpty())
            <p class="ds-empty"><b>Belum ada penumpang terdaftar untuk jadwal ini.</b> Daftar penumpang akan otomatis terisi saat tiket dipesan melalui sistem.</p>
        @else
            <div class="ds-table-wrap">
                <table class="ds-table">
                    <caption class="ds-visually-hidden">Daftar penumpang jadwal aktif</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kursi</th>
                            <th scope="col">Nama Penumpang</th>
                            <th scope="col">Titik Jemput & Antar</th>
                            <th scope="col">Status Bayar</th>
                            <th scope="col">Status Naik</th>
                            <th scope="col" style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($manifestPassengers->take(8) as $p)
                            <tr>
                                <td>
                                    <span class="ds-badge ds-badge--lead" style="font-size: 13px; font-weight: 800;">
                                        #{{ $p->seat_number }}
                                    </span>
                                </td>
                                <td>
                                    <b>{{ $p->name }}</b>
                                    @if($p->phone)
                                        <div class="ds-sub">{{ $p->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div><strong>Jemput:</strong> {{ $p->pickup_point ?? '-' }}</div>
                                    <div class="ds-sub"><strong>Turun:</strong> {{ $p->dropoff_point ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge--{{ $p->is_paid ? 'ok' : 'warn' }}">
                                        {{ $p->payment_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge--{{ $p->is_boarded ? 'ok' : 'muted' }}">
                                        {{ $p->is_boarded ? 'Sudah Naik ('.$p->boarded_at.')' : 'Belum Naik' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('dashboard.driver.passenger.checkin', $p->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="boarding_status" value="{{ $p->is_boarded ? 'belum_hadir' : 'boarded' }}">
                                        <button type="submit" class="ds-btn ds-btn--{{ $p->is_boarded ? 'secondary' : 'accent' }}" style="font-size: 12px; min-height: 32px; padding: 0 10px;">
                                            {{ $p->is_boarded ? 'Batal Naik' : 'Tandai Naik' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($manifestPassengers->count() > 8)
                <div style="padding: 14px 22px; text-align: center; border-top: 1px solid var(--lp-line);">
                    <a href="{{ route('dashboard.driver.penumpang') }}" class="ds-link">
                        Lihat {{ $manifestPassengers->count() - 8 }} penumpang lainnya &rarr;
                    </a>
                </div>
            @endif
        @endif
    </section>

    {{-- 5. PANEL PINTASAN & LOG OPERASIONAL DRIVER --}}
    <div class="ds-grid-2">
        <section class="ds-panel" id="section-aktivitas" aria-labelledby="aktivitas-operasional">
            <div class="ds-panel-head">
                <div>
                    <h2 id="aktivitas-operasional">Aktivitas & Catatan Operasional</h2>
                    <p>Status perjalanan dan log kendala perjalanan aktif.</p>
                </div>
            </div>
            @if ($activeIncidents->isNotEmpty())
                <ul class="ds-list">
                    @foreach ($activeIncidents as $inc)
                        <li>
                            <b>{{ $inc->categoryLabel() }} &bull; {{ $inc->statusLabel() }}</b>
                            <span class="ds-sub">{{ $inc->description }}</span>
                            <span class="ds-sub">Lokasi: {{ $inc->location_name }} &bull; Estimasi tertunda {{ $inc->estimated_delay_minutes ?? 15 }} menit</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="ds-list">
                    <li>
                        <b>Perjalanan Aktif &bull; {{ $activeSchedule->tripCode() }}</b>
                        <span class="ds-sub">Status: <strong>{{ $activeSchedule->tripStatusLabel() }}</strong></span>
                        <span class="ds-sub">{{ $activeSchedule->route?->origin?->name ?? 'Pool Asal' }} ke {{ $activeSchedule->route?->destination?->name ?? 'Pool Tujuan' }}</span>
                    </li>
                    <li>
                        <b>Armada Ditugaskan</b>
                        <span class="ds-sub">{{ $activeSchedule->vehicle?->name ?? 'HiAce' }} ({{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }})</span>
                        <span class="ds-sub">Kapasitas {{ $seatCapacity }} kursi &bull; {{ $totalBookedPassengers }} penumpang terdaftar</span>
                    </li>
                    <li>
                        <b>Kesiapan Operasional</b>
                        <span class="ds-sub">STNK & Uji KIR aktif. Dokumen perjalanan Surat Perintah Jalan (SPJ) siap.</span>
                    </li>
                </ul>
            @endif
        </section>

        <section class="ds-panel" id="section-pintasan" aria-labelledby="modul-driver">
            <div class="ds-panel-head">
                <div>
                    <h2 id="modul-driver">Akses Seluruh Fitur Pengemudi Per-Halaman</h2>
                    <p>Tombol Pintas Cepat (Quick Shortcuts) ke modul fungsional pengemudi per-halaman.</p>
                </div>
            </div>
            <div class="ds-panel-body">
                <nav aria-label="Menu pengemudi">
                    <ul class="ds-nav">
                        <li>
                            <a href="{{ route('dashboard.driver.jadwal') }}">
                                <b>Jadwal Perjalanan</b>
                                <span>Kalender tugas hari ini, besok & mendatang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.kendaraan') }}">
                                <b>Kendaraan Ditugaskan</b>
                                <span>Spesifikasi HiAce, dokumen STNK & KIR</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.penumpang') }}">
                                <b>Daftar Penumpang</b>
                                <span>Manifes absensi & tagihan tunai / COD</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.seatmap') }}">
                                <b>Seat Map Kabin</b>
                                <span>Denah tempat duduk & nomor kursi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.rute') }}">
                                <b>Rute Perjalanan</b>
                                <span>Peta jalur, waypoint & estimasi waktu</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.status') }}">
                                <b>Status Perjalanan</b>
                                <span>Update tahapan persiapan s/d selesai</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.info-penumpang') }}">
                                <b>Informasi Penumpang</b>
                                <span>Kontak WhatsApp dan titik antar</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="openIncidentModal()">
                                <b>Lapor Masalah</b>
                                <span>Laporan kendala armada / lalu lintas</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="openSosModal()" style="border-color: #fca5a5; background: #fff5f5;">
                                <b style="color: #991b1b;">Emergency SOS</b>
                                <span style="color: #b91c1c;">Pusat bantuan darurat lapangan & kontak</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.driver.riwayat') }}">
                                <b>Riwayat Perjalanan</b>
                                <span>Arsip penugasan & log ritase lampau</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
    </div>

</div>
@endsection