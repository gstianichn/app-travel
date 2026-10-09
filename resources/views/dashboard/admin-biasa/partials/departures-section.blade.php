<!-- 3. DAFTAR KEBERANGKATAN TERDEKAT (1-2 JAM KE DEPAN) -->

            <div class="section-header">
                <div class="section-header-left">
                    <h3 style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                            <rect x="2" y="5" width="20" height="13" rx="2" />
                            <circle cx="7" cy="18" r="2" />
                            <circle cx="17" cy="18" r="2" />
                            <path d="M2 10h20" />
                        </svg>
                        <span>Daftar Keberangkatan Terdekat (1–2 Jam Ke Depan)</span>
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
                    <button type="button" class="btn-action-secondary" onclick="window.location.reload()" title="Muat Ulang Data" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                            <path d="M21 3v5h-5" />
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                            <path d="M8 16H3v5" />
                        </svg>
                        Segarkan Jadwal
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
                                    <span class="trip-tag trip-tag-prep" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Persiapan
                                    </span>
                                @else
                                    <span class="trip-tag trip-tag-done" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                        Selesai
                                    </span>
                                @endif
                            </div>

                            <!-- Rute Perjalanan -->
                            <div class="dep-route">
                                <div class="dep-route-name" style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;">
                                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                                        <line x1="9" x2="9" y1="3" y2="18" />
                                        <line x1="15" x2="15" y1="6" y2="21" />
                                    </svg>
                                    <span>{{ $dep->route_name }}</span>
                                </div>
                            </div>

                            <!-- Info Armada, Pelat Nomor & Supir -->
                            <div class="dep-info-grid">
                                <div class="dep-info-item">
                                    <span>Armada & Pelat Nomor</span>
                                    <strong style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;">
                                            <rect x="2" y="5" width="20" height="13" rx="2" />
                                            <circle cx="7" cy="18" r="2" />
                                            <circle cx="17" cy="18" r="2" />
                                        </svg>
                                        {{ $dep->plate_number }}
                                    </strong>
                                    <small style="font-size: 10px; color: #64748b;">{{ $dep->vehicle_name }}</small>
                                </div>

                                <div class="dep-info-item">
                                    <span>Driver / Supir</span>
                                    <strong style="display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        {{ $dep->driver_name }}
                                    </strong>
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
                                style="text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
                            >
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                    <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                    <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                    <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                    <rect x="7" y="7" width="10" height="10" rx="1" />
                                </svg>
                                <span>Scan Boarding Pass</span>
                            </a>

                            <a
                                href="{{ route('dashboard.admin-biasa.booking') }}?schedule_id={{ $dep->id }}"
                                class="btn-action-secondary"
                                style="display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
                            >
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                                    <path d="M9 12h6" />
                                    <path d="M9 16h6" />
                                </svg>
                                <span>Manifes</span>
                            </a>
                        </div>

                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: white; border-radius: 16px; border: 1px dashed var(--border);">
                        <p style="color: var(--text-light); font-size: 14px;">Belum ada jadwal keberangkatan untuk 1–2 jam ke depan.</p>
                    </div>
                @endforelse

            </div>
