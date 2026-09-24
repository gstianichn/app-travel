<!-- 3. DAFTAR KEBERANGKATAN TERDEKAT (1-2 JAM KE DEPAN) -->

            <div class="section-header">
                <div class="section-header-left">
                    <h3>
                        🚍 Daftar Keberangkatan Terdekat (1–2 Jam Ke Depan)
                        <span class="badge-live-monitor">
                            <span class="pulse-green" style="width: 6px; height: 6px; background: #087f55; border-radius: 50%; display: inline-block;"></span>
                            Live Monitor
                        </span>
                    </h3>
                    <p>
                        Pantau jam keberangkatan, rute tujuan, kesiapan armada, driver, dan rasio keterisian kursi penumpang.
                    </p>
                </div>

                <div>
                    <button type="button" class="btn-action-secondary" onclick="window.location.reload()" title="Muat Ulang Data">
                        🔄 Segarkan Jadwal
                    </button>
                </div>
            </div>

            <div class="departures-grid">

                @forelse($departures as $dep)
                    <div class="dep-card">

                        <div>
                            <!-- Header Kartu: Jam Berangkat & Indikator Status Trip -->
                            <div class="dep-card-top">
                                <div class="dep-time-box">
                                    <span class="dep-time">{{ $dep->time }}</span>
                                    <span class="dep-countdown">{{ $dep->departure_note }}</span>
                                </div>

                                <!-- INDIKATOR STATUS TRIP: Tag jelas seperti Persiapan, Boarding Dibuka, atau Selesai -->
                                @if($dep->status === 'Boarding Dibuka')
                                    <span class="trip-tag trip-tag-boarding">
                                        <span class="tag-dot"></span>
                                        Boarding Dibuka
                                    </span>
                                @elseif($dep->status === 'Persiapan')
                                    <span class="trip-tag trip-tag-prep">
                                        ⏳ Persiapan
                                    </span>
                                @else
                                    <span class="trip-tag trip-tag-done">
                                        ✅ Selesai
                                    </span>
                                @endif
                            </div>

                            <!-- Rute Perjalanan -->
                            <div class="dep-route">
                                <div class="dep-route-name">
                                    <span>🛣️</span>
                                    <span>{{ $dep->route_name }}</span>
                                </div>
                            </div>

                            <!-- Info Armada, Pelat Nomor & Supir -->
                            <div class="dep-info-grid">
                                <div class="dep-info-item">
                                    <span>Armada & Pelat Nomor</span>
                                    <strong>🚐 {{ $dep->plate_number }}</strong>
                                    <small style="font-size: 10px; color: #64748b;">{{ $dep->vehicle_name }}</small>
                                </div>

                                <div class="dep-info-item">
                                    <span>Driver / Supir</span>
                                    <strong>👤 {{ $dep->driver_name }}</strong>
                                    <small style="font-size: 10px; color: #087f55;">Standby di Pool</small>
                                </div>
                            </div>

                            <!-- RASIO KETERISIAN KURSI (Misal: 8/10 kursi) -->
                            <div class="occupancy-section">
                                <div class="occupancy-header">
                                    <span>Rasio Keterisian Kursi</span>
                                    <strong>{{ $dep->ratio }} ({{ $dep->percentage }}%)</strong>
                                </div>
                                <div class="occupancy-bar-bg">
                                    <div
                                        class="occupancy-bar-fill {{ $dep->percentage >= 100 ? 'full' : ($dep->percentage >= 80 ? 'warning' : '') }}"
                                        style="width: {{ $dep->percentage }}%;"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Cepat Kartu -->
                        <div class="dep-card-actions">
                            <a
                                href="{{ route('dashboard.admin-biasa.validasi-tiket') }}?schedule_id={{ $dep->id }}"
                                class="btn-action-primary"
                                style="text-align: center; text-decoration: none;"
                            >
                                📷 Scan Boarding Pass
                            </a>

                            <a
                                href="{{ route('dashboard.admin-biasa.booking') }}?schedule_id={{ $dep->id }}"
                                class="btn-action-secondary"
                            >
                                📋 Manifes
                            </a>
                        </div>

                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: white; border-radius: 16px; border: 1px dashed var(--border);">
                        <p style="color: var(--text-light); font-size: 14px;">Belum ada jadwal keberangkatan untuk 1–2 jam ke depan.</p>
                    </div>
                @endforelse

            </div>
