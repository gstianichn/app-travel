        <!-- ================= 2. STATUS & JADWAL PERJALANAN AKTIF ================= -->
        <section class="trip-hero-card" id="section-jadwal">
            <!-- Header Jadwal Perjalanan & Rangkuman Trip Hari Itu -->
            <div class="schedule-hero-header">
                <div class="schedule-header-info">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                        <span class="badge-pill" style="background:rgba(255,255,255,0.18); color:#ffffff; font-size:11px; font-weight:800; border:1px solid rgba(255,255,255,0.25);">
                            BAGIAN 2: STATUS & JADWAL PERJALANAN AKTIF
                        </span>
                    </div>
                    <h2>
                        <span>📅</span>
                        <span>Melihat Jadwal Perjalanan</span>
                    </h2>
                    <p style="font-size:14.5px; margin-top:5px; color:#ffffff;">
                        Jadwal & Rute Utama: <strong>{{ $activeSchedule->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}</strong>
                        &nbsp;➔&nbsp;
                        <strong>{{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</strong>,
                        <strong>{{ $activeSchedule->formattedDepartureTime() }} WIB</strong>
                    </p>
                    <p style="font-size:12px; color:#a7f3d0; margin-top:2px;">
                        Jarak Tempuh: {{ $activeSchedule->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Estimasi Waktu Tiba: {{ $activeSchedule->estimatedArrivalTime() }} WIB
                    </p>
                </div>

                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                    <!-- Badge Status Saat Ini: Menunggu Keberangkatan, Proses Penjemputan, atau Dalam Perjalanan -->
                    <div class="trip-status-badge {{ $activeSchedule->tripStatusBadgeClass() }}" style="box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <span>Status Saat Ini: </span>
                        <strong>{{ $activeSchedule->tripStatusLabel() }}</strong>
                    </div>

                    <!-- Ringkasan Muatan: Kapasitas penumpang terisi (misal: 8 / 10 Kursi Terisi) -->
                    <div style="background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.2); border-radius:10px; padding:6px 14px; color:white; text-align:right;">
                        <span style="color:#94a3b8; font-size:10.5px; display:block; font-weight:700;">Ringkasan Muatan Kabin</span>
                        <strong style="color:#34d399; font-size:13.5px;">{{ $totalBookedPassengers }} / {{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi Terisi</strong>
                    </div>

                    <div class="trip-code-box" style="margin-top:2px;">
                        <div class="trip-code-badge" style="padding:4px 12px;">
                            <span class="trip-label" style="font-size:10px;">Nomor Trip / Perjalanan</span>
                            <span class="trip-value" style="font-size:13px;">{{ $activeSchedule->tripCode() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Metrik Inti Jadwal: Nomor Trip, Tanggal, Jam Keberangkatan, Estimasi Tiba -->
            <div class="schedule-metric-grid">
                <!-- 1. Nomor Trip / Perjalanan -->
                <div class="schedule-metric-card">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">🎫</span>
                        <span class="schedule-metric-tag">Nomor Trip</span>
                    </div>
                    <div class="schedule-metric-val" style="font-family:'JetBrains Mono', monospace; font-size:16px;">
                        {{ $activeSchedule->tripCode() }}
                    </div>
                    <div class="schedule-metric-sub">
                        Shuttle Reguler Eksekutif
                    </div>
                </div>

                <!-- 2. Tanggal Perjalanan -->
                <div class="schedule-metric-card">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">🗓️</span>
                        <span class="schedule-metric-tag">Tanggal Perjalanan</span>
                    </div>
                    <div class="schedule-metric-val" style="font-size:15px;">
                        {{ $activeSchedule->formattedDepartureDate() }}
                    </div>
                    <div class="schedule-metric-sub">
                        {{ $activeSchedule->isWeekendDeparture() ? 'Akhir Pekan (Padat Lalu Lintas)' : 'Hari Kerja (Jadwal Normal)' }}
                    </div>
                </div>

                <!-- 3. Jam Keberangkatan & Waktu Berkumpul di Pool -->
                <div class="schedule-metric-card highlight">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">⏰</span>
                        <span class="schedule-metric-tag" style="color:#065f46;">Waktu Keberangkatan & Berkumpul</span>
                    </div>
                    <div class="schedule-metric-val">
                        {{ $activeSchedule->formattedDepartureTime() }}
                    </div>
                    <div class="schedule-metric-sub">
                        Waktu Berkumpul (Standby): <strong>{{ $activeSchedule->gatheringTime() }}</strong> (H-30 di Pool)
                    </div>
                </div>

                <!-- 4. Estimasi Total Durasi & Waktu Tiba -->
                <div class="schedule-metric-card highlight-blue">
                    <div class="schedule-metric-top">
                        <span class="schedule-metric-icon">⏱️</span>
                        <span class="schedule-metric-tag" style="color:#1e40af;">Estimasi Total Durasi</span>
                    </div>
                    <div class="schedule-metric-val">
                        ± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }}
                    </div>
                    <div class="schedule-metric-sub">
                        Jarak {{ $activeSchedule->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Estimasi Waktu Tiba: <strong>{{ $activeSchedule->estimatedArrivalTime() }}</strong>
                    </div>
                </div>
            </div>

            <!-- ================= KALENDER KERJA & AGENDA PENUGASAN ================= -->
            <div class="calendar-agenda-wrapper">
                <div class="calendar-agenda-header">
                    <div class="calendar-agenda-title">
                        <h4>
                            <span>🗓️</span>
                            <span>Kalender Kerja & Agenda Penugasan Driver</span>
                        </h4>
                        <p>Manajemen kalender kerja dan penugasan perjalanan driver: jadwal hari ini, besok, serta jadwal mendatang lengkap dengan waktu berkumpul di pool (standby time), waktu keberangkatan (departure time), dan estimasi total durasi perjalanan.</p>
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="badge-pill" style="background:#eff6ff; color:#1e40af; border-color:#bfdbfe;">
                            📅 {{ Carbon\Carbon::today()->translatedFormat('F Y') }}
                        </span>
                    </div>
                </div>

                <!-- 1. Kalender Kerja Mingguan (7-Day Strip) -->
                @php
                    $startOfWeek = Carbon\Carbon::today()->startOfWeek();
                    $weekDays = [];
                    for ($i = 0; $i < 7; $i++) {
                        $dayDate = $startOfWeek->copy()->addDays($i);
                        $isToday = $dayDate->isToday();
                        $isTomorrow = $dayDate->isTomorrow();
                        $dutyLabel = $isToday ? 'Hari Ini' : ($isTomorrow ? 'Besok' : ($dayDate->isPast() ? 'Selesai' : 'Standby'));
                        $dutyClass = $isToday ? 'active-duty' : ($isTomorrow ? 'planned-duty' : ($dayDate->isPast() ? 'off-duty' : 'standby-duty'));
                        $weekDays[] = [
                            'name' => $dayDate->translatedFormat('D'),
                            'date_num' => $dayDate->format('d'),
                            'is_today' => $isToday,
                            'label' => $dutyLabel,
                            'class' => $dutyClass,
                            'full_date' => $dayDate->translatedFormat('d M'),
                        ];
                    }
                @endphp

                <div class="weekly-calendar-strip">
                    @foreach ($weekDays as $wDay)
                        <div class="calendar-day-card {{ $wDay['is_today'] ? 'is-today' : '' }}">
                            <div class="day-name">{{ $wDay['name'] }}</div>
                            <div class="day-number">{{ $wDay['date_num'] }}</div>
                            <span class="day-status-pill {{ $wDay['class'] }}">{{ $wDay['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- 2. Tab Navigasi Agenda: Hari Ini, Besok, dan Jadwal Mendatang -->
                <div class="agenda-tab-nav">
                    <button type="button" class="agenda-tab-btn active" id="tab-btn-today" onclick="switchScheduleTab('today')">
                        <span>📌 Hari Ini</span>
                        <span class="agenda-tab-badge">{{ $todaySchedules->count() }}</span>
                    </button>
                    <button type="button" class="agenda-tab-btn" id="tab-btn-tomorrow" onclick="switchScheduleTab('tomorrow')">
                        <span>🗓️ Besok</span>
                        <span class="agenda-tab-badge">{{ $tomorrowSchedules->count() }}</span>
                    </button>
                    <button type="button" class="agenda-tab-btn" id="tab-btn-upcoming" onclick="switchScheduleTab('upcoming')">
                        <span>🚀 Jadwal Mendatang</span>
                        <span class="agenda-tab-badge">{{ $upcomingSchedules->count() }}</span>
                    </button>
                </div>

                <!-- 3. Konten Panel Agenda -->
                <!-- Panel 1: Hari Ini -->
                <div class="agenda-tab-panel active" id="tab-panel-today">
                    @forelse ($todaySchedules as $sched)
                        <div class="agenda-schedule-card {{ ($activeSchedule && $activeSchedule->id == $sched->id) ? 'is-active-trip' : '' }}">
                            <div class="agenda-card-header">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <span class="agenda-trip-code">{{ $sched->tripCode() }}</span>
                                    <span class="trip-status-badge {{ $sched->tripStatusBadgeClass() }}" style="font-size:11px; padding:3px 10px;">
                                        {{ $sched->tripStatusLabel() }}
                                    </span>
                                    <span style="font-size:12px; color:var(--text-muted); font-weight:600;">
                                        🗓️ Hari Ini, {{ $sched->formattedDepartureDate() }}
                                    </span>
                                </div>

                                @if ($activeSchedule && $activeSchedule->id == $sched->id)
                                    <span class="pill-badge" style="background:#ecfdf5; border-color:#a7f3d0; color:#065f46; font-size:11px; font-weight:800;">
                                        ✓ Jadwal Utama Aktif
                                    </span>
                                @else
                                    <a href="{{ route('dashboard.driver.index', ['schedule_id' => $sched->id]) }}" class="btn-select-schedule">
                                        <span>Aktifkan Jadwal Ini</span>
                                        <span>➔</span>
                                    </a>
                                @endif
                            </div>

                            <div class="agenda-route-row">
                                <span>📍 {{ $sched->route?->origin?->name ?? 'Pool Dipatiukur' }}</span>
                                <span style="color:#059669;">➔</span>
                                <span>🏁 {{ $sched->route?->destination?->name ?? 'Pool Semanggi' }}</span>
                            </div>

                            <!-- 3 Highlight Waktu: Standby, Departure, Estimasi Durasi -->
                            <div class="agenda-operational-times-grid">
                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>🅿️</span>
                                        <span>Waktu Berkumpul (Standby)</span>
                                    </span>
                                    <div class="agenda-time-val standby-highlight">
                                        {{ $sched->gatheringTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        H-30 Standby di Pool / Titik Awal
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏰</span>
                                        <span>Waktu Keberangkatan</span>
                                    </span>
                                    <div class="agenda-time-val depart-highlight">
                                        {{ $sched->formattedDepartureTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Waktu armada meluncur keluar pool
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏱️</span>
                                        <span>Estimasi Total Durasi</span>
                                    </span>
                                    <div class="agenda-time-val duration-highlight">
                                        ± {{ $sched->route?->duration ?? '2 Jam 45 Menit' }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Jarak {{ $sched->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Tiba: <strong>{{ $sched->estimatedArrivalTime() }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="agenda-card-footer">
                                <div class="agenda-vehicle-info">
                                    <span>🚐</span>
                                    <span>Armada: {{ $sched->vehicle?->plate_number ?? 'D 7812 AB' }} ({{ $sched->vehicle?->type ?? 'Toyota HiAce Premio' }})</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-weight:700; color:#059669;">
                                        💺 Sisa {{ $sched->available_seats ?? 8 }} / {{ $sched->vehicle?->capacity ?? 10 }} Kursi Kosong
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1; color:var(--text-muted);">
                            Tidak ada jadwal penugasan tambahan untuk hari ini.
                        </div>
                    @endforelse
                </div>

                <!-- Panel 2: Besok -->
                <div class="agenda-tab-panel" id="tab-panel-tomorrow">
                    @forelse ($tomorrowSchedules as $sched)
                        <div class="agenda-schedule-card">
                            <div class="agenda-card-header">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <span class="agenda-trip-code">{{ $sched->tripCode() }}</span>
                                    <span class="trip-status-badge {{ $sched->tripStatusBadgeClass() }}" style="font-size:11px; padding:3px 10px;">
                                        {{ $sched->tripStatusLabel() }}
                                    </span>
                                    <span style="font-size:12px; color:var(--text-muted); font-weight:600;">
                                        🗓️ Besok, {{ $sched->formattedDepartureDate() }}
                                    </span>
                                </div>
                                <span class="badge-pill" style="background:#eff6ff; border-color:#bfdbfe; color:#1e40af; font-size:11px; font-weight:800;">
                                    Penugasan Besok
                                </span>
                            </div>

                            <div class="agenda-route-row">
                                <span>📍 {{ $sched->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}</span>
                                <span style="color:#059669;">➔</span>
                                <span>🏁 {{ $sched->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</span>
                            </div>

                            <!-- 3 Highlight Waktu: Standby, Departure, Estimasi Durasi -->
                            <div class="agenda-operational-times-grid">
                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>🅿️</span>
                                        <span>Waktu Berkumpul (Standby)</span>
                                    </span>
                                    <div class="agenda-time-val standby-highlight">
                                        {{ $sched->gatheringTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        H-30 Standby di Pool / Titik Awal
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏰</span>
                                        <span>Waktu Keberangkatan</span>
                                    </span>
                                    <div class="agenda-time-val depart-highlight">
                                        {{ $sched->formattedDepartureTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Waktu armada meluncur keluar pool
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏱️</span>
                                        <span>Estimasi Total Durasi</span>
                                    </span>
                                    <div class="agenda-time-val duration-highlight">
                                        ± {{ $sched->route?->duration ?? '2 Jam 45 Menit' }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Jarak {{ $sched->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Tiba: <strong>{{ $sched->estimatedArrivalTime() }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="agenda-card-footer">
                                <div class="agenda-vehicle-info">
                                    <span>🚐</span>
                                    <span>Armada: {{ $sched->vehicle?->plate_number ?? 'D 7812 AB' }} ({{ $sched->vehicle?->type ?? 'Toyota HiAce Premio' }})</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-weight:700; color:#059669;">
                                        💺 Sisa {{ $sched->available_seats ?? 9 }} / {{ $sched->vehicle?->capacity ?? 10 }} Kursi Kosong
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1; color:var(--text-muted);">
                            Belum ada jadwal penugasan untuk besok.
                        </div>
                    @endforelse
                </div>

                <!-- Panel 3: Jadwal Mendatang -->
                <div class="agenda-tab-panel" id="tab-panel-upcoming">
                    @forelse ($upcomingSchedules as $sched)
                        <div class="agenda-schedule-card">
                            <div class="agenda-card-header">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <span class="agenda-trip-code">{{ $sched->tripCode() }}</span>
                                    <span class="trip-status-badge {{ $sched->tripStatusBadgeClass() }}" style="font-size:11px; padding:3px 10px;">
                                        {{ $sched->tripStatusLabel() }}
                                    </span>
                                    <span style="font-size:12px; color:var(--text-muted); font-weight:600;">
                                        🗓️ {{ $sched->formattedDepartureDate() }}
                                    </span>
                                </div>
                                <span class="badge-pill" style="background:#f8fafc; border-color:#cbd5e1; color:#475569; font-size:11px; font-weight:700;">
                                    Jadwal Mendatang
                                </span>
                            </div>

                            <div class="agenda-route-row">
                                <span>📍 {{ $sched->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}</span>
                                <span style="color:#059669;">➔</span>
                                <span>🏁 {{ $sched->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</span>
                            </div>

                            <!-- 3 Highlight Waktu: Standby, Departure, Estimasi Durasi -->
                            <div class="agenda-operational-times-grid">
                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>🅿️</span>
                                        <span>Waktu Berkumpul (Standby)</span>
                                    </span>
                                    <div class="agenda-time-val standby-highlight">
                                        {{ $sched->gatheringTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        H-30 Standby di Pool / Titik Awal
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏰</span>
                                        <span>Waktu Keberangkatan</span>
                                    </span>
                                    <div class="agenda-time-val depart-highlight">
                                        {{ $sched->formattedDepartureTime() }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Waktu armada meluncur keluar pool
                                    </span>
                                </div>

                                <div class="agenda-time-box">
                                    <span class="agenda-time-label">
                                        <span>⏱️</span>
                                        <span>Estimasi Total Durasi</span>
                                    </span>
                                    <div class="agenda-time-val duration-highlight">
                                        ± {{ $sched->route?->duration ?? '2 Jam 45 Menit' }}
                                    </div>
                                    <span class="agenda-time-sub">
                                        Jarak {{ $sched->route?->distance_km ?? 150 }} km &nbsp;•&nbsp; Tiba: <strong>{{ $sched->estimatedArrivalTime() }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="agenda-card-footer">
                                <div class="agenda-vehicle-info">
                                    <span>🚐</span>
                                    <span>Armada: {{ $sched->vehicle?->plate_number ?? 'D 7812 AB' }} ({{ $sched->vehicle?->type ?? 'Toyota HiAce Premio' }})</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-weight:700; color:#059669;">
                                        💺 Sisa {{ $sched->available_seats ?? 10 }} / {{ $sched->vehicle?->capacity ?? 10 }} Kursi Kosong
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:30px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1; color:var(--text-muted);">
                            Belum ada jadwal mendatang berikutnya.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Panduan Ritme Waktu & Persiapan Sopir (Kegunaan) -->
            <div class="schedule-rhythm-guide">
                <div class="rhythm-guide-title">
                    <h4>
                        <span>⏱️</span>
                        <span>Panduan Ritme Waktu & Persiapan Armada Sopir</span>
                    </h4>
                    <span class="badge-pill">Manajemen Waktu Sopir</span>
                </div>

                <div class="rhythm-timeline">
                    <!-- Fase 1: H-45 Menit Persiapan Armada -->
                    <div class="rhythm-card stage-prep">
                        <div class="rhythm-time-badge">
                            <span>🔧</span>
                            <span>{{ $activeSchedule->prepTime() }} (H-45)</span>
                        </div>
                        <h5>Persiapan Armada (Garasi)</h5>
                        <p>Cek kondisi fisik unit: periksa BBM, oli mesin, tekanan ban, kebersihan kabin, dan nyalakan AC agar dingin saat penumpang naik.</p>
                    </div>

                    <!-- Fase 2: H-30 Menit Tiba di Pool -->
                    <div class="rhythm-card stage-pool">
                        <div class="rhythm-time-badge">
                            <span>🅿️</span>
                            <span>{{ $activeSchedule->gatheringTime() }} (H-30)</span>
                        </div>
                        <h5>Tiba di Pool / Titik Awal</h5>
                        <p>Tiba tepat waktu di <strong>{{ $activeSchedule->route?->origin?->name ?? 'Pool Dipatiukur' }}</strong>, sandar rapi di bay keberangkatan, dan koordinasi dengan tim operasional pool.</p>
                    </div>

                    <!-- Fase 3: H-15 Menit Boarding -->
                    <div class="rhythm-card stage-boarding">
                        <div class="rhythm-time-badge">
                            <span>🧳</span>
                            <span>{{ $activeSchedule->boardingTime() }} (H-15)</span>
                        </div>
                        <h5>Boarding & Muat Bagasi</h5>
                        <p>Buka pintu kabin, sambut ramah penumpang, bantu muat bagasi ke kompartemen belakang, dan lakukan check-in kehadiran penumpang.</p>
                    </div>

                    <!-- Fase 4: H-0 Menit Meluncur -->
                    <div class="rhythm-card stage-depart">
                        <div class="rhythm-time-badge">
                            <span>🚀</span>
                            <span>{{ $activeSchedule->formattedDepartureTime() }} (H-0)</span>
                        </div>
                        <h5>Meluncur & Prediksi Ritme</h5>
                        <p>Berangkat tepat waktu menuju <strong>{{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</strong>. Jaga kecepatan aman, estimasi tiba pukul <strong>{{ $activeSchedule->estimatedArrivalTime() }}</strong>.</p>
                    </div>
                </div>
            </div>

