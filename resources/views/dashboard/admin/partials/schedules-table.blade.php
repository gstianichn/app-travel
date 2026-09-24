{{-- =========================================
         2. JADWAL PERJALANAN TERDEKAT
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Jadwal Perjalanan Terdekat</h2>
                <p>Tabel ringkas rute keberangkatan, jam jalan, nama sopir, dan status keterisian kursi (load factor)</p>
            </div>
        </div>

        <div class="schedule-panel-card" style="margin-top: 14px;">
            <div class="schedule-panel-header">
                <div class="schedule-panel-title">
                    <span>Daftar Jadwal Keberangkatan</span>
                    <span class="schedule-badge-total">
                        {{ isset($upcomingSchedules) ? $upcomingSchedules->count() : 0 }} Jadwal
                    </span>
                </div>
                <a href="{{ route('dashboard.admin.rute.jadwal') }}" class="alert-footer-link">
                    Kelola Semua Rute & Jadwal &rarr;
                </a>
            </div>

            <div class="table-scroll-wrap">
                @if(isset($upcomingSchedules) && $upcomingSchedules->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Rute Perjalanan</th>
                                <th>Jam Jalan</th>
                                <th>Nama Sopir</th>
                                <th>Armada</th>
                                <th>Keterisian Kursi (Load Factor)</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingSchedules as $sch)
                                @php
                                    // 1. Rute
                                    $origin = $sch->route?->origin?->name ?? ($sch->travelRoute?->origin ?? 'Asal');
                                    $destination = $sch->route?->destination?->name ?? ($sch->travelRoute?->destination ?? 'Tujuan');

                                    // 2. Jam & Tanggal
                                    $timeStr = '-';
                                    if ($sch->departure_time) {
                                        $timeStr = date('H:i', strtotime($sch->departure_time)) . ' WIB';
                                    } elseif ($sch->departure_at) {
                                        $timeStr = $sch->departure_at->format('H:i') . ' WIB';
                                    }

                                    $dateStr = '-';
                                    if ($sch->departure_date) {
                                        $dateStr = $sch->departure_date->format('d M Y');
                                    } elseif ($sch->departure_at) {
                                        $dateStr = $sch->departure_at->format('d M Y');
                                    }

                                    // 3. Driver
                                    $driverName = $sch->driver?->user?->name;

                                    // 4. Armada
                                    $vehicleName = $sch->vehicle?->name ?? 'Minibus';
                                    $plateNumber = $sch->vehicle?->plate_number ?? '';

                                    // 5. Load Factor Calculation
                                    $capacity = (int) ($sch->vehicle?->capacity ?? 12);
                                    if ($capacity <= 0) $capacity = 12;

                                    // Terisi
                                    $available = $sch->available_seats;
                                    if ($available !== null) {
                                        $booked = max(0, $capacity - (int)$available);
                                    } else {
                                        $booked = (int) $sch->bookings->whereIn('status', ['Paid', 'Confirmed', 'paid', 'confirmed', 'success'])->sum('total_passenger');
                                    }

                                    $loadPercent = min(100, round(($booked / $capacity) * 100));

                                    $colorClass = 'safe';
                                    if ($loadPercent >= 90) {
                                        $colorClass = 'full';
                                    } elseif ($loadPercent >= 70) {
                                        $colorClass = 'medium';
                                    }
                                @endphp
                                <tr>
                                    {{-- RUTE --}}
                                    <td>
                                        <div class="route-cell">
                                            <span>{{ $origin }}</span>
                                            <span class="route-arrow">&rarr;</span>
                                            <span>{{ $destination }}</span>
                                        </div>
                                    </td>

                                    {{-- JAM JALAN --}}
                                    <td>
                                        <div class="time-cell">
                                            <span class="time-val">{{ $timeStr }}</span>
                                            <span class="time-date">{{ $dateStr }}</span>
                                        </div>
                                    </td>

                                    {{-- NAMA SOPIR --}}
                                    <td>
                                        <div class="driver-cell">
                                            @if($driverName)
                                                <div class="driver-avatar-mini">
                                                    {{ strtoupper(substr($driverName, 0, 1)) }}
                                                </div>
                                                <span class="driver-name-text">{{ $driverName }}</span>
                                            @else
                                                <span class="driver-unassigned">Belum Ditugaskan</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ARMADA --}}
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 2px;">
                                            <span style="font-weight: 600; color: #17221d;">{{ $vehicleName }}</span>
                                            @if($plateNumber)
                                                <span style="font-size: 11px; color: #72817a;">{{ $plateNumber }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- LOAD FACTOR --}}
                                    <td>
                                        <div class="load-factor-wrap">
                                            <div class="load-factor-meta">
                                                <span style="color: #26332c;">{{ $booked }} / {{ $capacity }} Kursi</span>
                                                <span style="font-weight: 700; color: {{ $loadPercent >= 90 ? '#dc2626' : ($loadPercent >= 70 ? '#d97706' : '#15803d') }};">
                                                    {{ $loadPercent }}%
                                                </span>
                                            </div>
                                            <div class="progress-bar-bg">
                                                <div class="progress-bar-fill {{ $colorClass }}" style="width: {{ $loadPercent }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- STATUS --}}
                                    <td style="text-align: right;">
                                        @if(in_array(strtolower($sch->status ?? ''), ['in_trip', 'on_trip', 'beroperasi', 'departed']))
                                            <span class="status-pill active-trip">
                                                ● Beroperasi
                                            </span>
                                        @else
                                            <span class="status-pill scheduled">
                                                Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-schedules-notice">
                        Belum ada jadwal perjalanan terdaftar untuk ditampilkan.
                    </div>
                @endif
            </div>
        </div>
    </div>
