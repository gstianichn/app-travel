<!-- ============================================================
     2. PANEL PANTAUAN KEBERANGKATAN HARI INI
     (Trip Monitor & Incident Alert)
============================================================ -->

<div class="card trip-monitor-card" id="jadwal-travel">
    <div class="card-header">
        <div class="card-header-left">
            <span class="card-icon-bullet" style="background: #10b981;"></span>
            <div>
                <h2 class="card-title">Pantauan Keberangkatan Hari Ini &amp; Peringatan Armada</h2>
                <p class="card-subtitle-small">Pantau status armada live, delay keterlambatan, dan rasio keterisian kursi penumpang.</p>
            </div>
        </div>
        <div class="card-header-actions">
            <span class="status-live-pill">
                <span class="pulse-dot"></span> Live Monitoring
            </span>
            <button type="button" class="cs-btn cs-btn-secondary" style="padding: 6px 12px; font-size: 11px;" onclick="refreshTripMonitor()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                <span>Perbarui Data</span>
            </button>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">

        <!-- Filter Tab Cepat Pantauan Armada -->
        <div class="monitor-filter-bar">
            <button type="button" class="filter-btn active" onclick="filterMonitorTable('all', this)">Semua Armada (4)</button>
            <button type="button" class="filter-btn text-danger-btn" onclick="filterMonitorTable('delay', this)">
                <span class="kpi-critical-dot"></span> Terlambat / Delay (1)
            </button>
            <button type="button" class="filter-btn" onclick="filterMonitorTable('boarding', this)">Boarding di Pool (2)</button>
            <button type="button" class="filter-btn" onclick="filterMonitorTable('ontrip', this)">Dalam Perjalanan (1)</button>
        </div>

        <div class="table-responsive-wrapper">
            <table class="cs-data-table">
                <thead>
                    <tr>
                        <th>Jadwal &amp; Rute</th>
                        <th>Armada &amp; Plat Nomor</th>
                        <th>Pengemudi (Driver)</th>
                        <th>Kapasitas Kursi</th>
                        <th>Status &amp; Delay Alert</th>
                        <th>Aksi Proaktif CS</th>
                    </tr>
                </thead>
                <tbody id="trip-monitor-tbody">

                    <!-- Trip 1: Kritis / Terlambat > 15 Menit -->
                    <tr class="trip-row-delay" data-status="delay">
                        <td>
                            <div class="trip-time-cell">
                                <span class="trip-clock text-danger">09:00 WIB</span>
                                <span class="trip-route-name">Bandung (Pasteur) &rarr; Jakarta (Semanggi)</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-vehicle-cell">
                                <strong>Toyota HiAce Premio</strong>
                                <span class="plate-badge">D 7182 AB</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-driver-cell">
                                <span>Pak Suryanto</span>
                                <small class="text-muted">0812-7788-9900</small>
                            </div>
                        </td>
                        <td>
                            <div class="trip-seat-occupancy">
                                <div class="occupancy-labels">
                                    <strong class="text-slate-800">11 / 12 Kursi</strong>
                                    <span class="occupancy-pct">92%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill fill-high" style="width: 92%;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="delay-alert-tag alert-danger">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                <span>Terlambat +25 Menit (Macet Tol)</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-actions-cell">
                                <button type="button" class="btn-proactive-chat" onclick="triggerProactiveBroadcast('TRV-2026-0925', 'Bandung - Jakarta 09:00', 'Delay 25 Menit karena insiden tol Pasteur')">
                                    <span>Chat Penumpang</span>
                                </button>
                                <a href="tel:081277889900" class="btn-driver-call" title="Hubungi Driver">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Trip 2: Boarding di Pool (Tepat Waktu) -->
                    <tr data-status="boarding">
                        <td>
                            <div class="trip-time-cell">
                                <span class="trip-clock text-brand">09:30 WIB</span>
                                <span class="trip-route-name">Bandung (Pasteur) &rarr; Bandara Soetta (T3)</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-vehicle-cell">
                                <strong>Toyota HiAce Premio</strong>
                                <span class="plate-badge">D 9021 CG</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-driver-cell">
                                <span>Pak Dadang</span>
                                <small class="text-muted">0821-4455-6677</small>
                            </div>
                        </td>
                        <td>
                            <div class="trip-seat-occupancy">
                                <div class="occupancy-labels">
                                    <strong class="text-slate-800">12 / 12 Penuh</strong>
                                    <span class="occupancy-pct text-emerald-700">100%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill fill-full" style="width: 100%;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="delay-alert-tag alert-success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Tepat Waktu &bull; Boarding di Pool</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-actions-cell">
                                <button type="button" class="btn-proactive-chat" onclick="triggerProactiveBroadcast('TRV-2026-0774', 'Bandung - Soetta 09:30', 'Boarding loket 2 dibuka')">
                                    <span>Pemberitahuan Boarding</span>
                                </button>
                                <a href="tel:082144556677" class="btn-driver-call" title="Hubungi Driver">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Trip 3: Sedang Dalam Perjalanan -->
                    <tr data-status="ontrip">
                        <td>
                            <div class="trip-time-cell">
                                <span class="trip-clock">08:00 WIB</span>
                                <span class="trip-route-name">Jakarta (Semanggi) &rarr; Bandung (Dipatiukur)</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-vehicle-cell">
                                <strong>Toyota HiAce Luxury</strong>
                                <span class="plate-badge">B 8911 JKT</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-driver-cell">
                                <span>Pak Joko</span>
                                <small class="text-muted">0811-3322-1144</small>
                            </div>
                        </td>
                        <td>
                            <div class="trip-seat-occupancy">
                                <div class="occupancy-labels">
                                    <strong class="text-slate-800">10 / 10 Penuh</strong>
                                    <span class="occupancy-pct">100%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill fill-full" style="width: 100%;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="delay-alert-tag alert-info">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>On Trip &bull; Posisi KM 72 Tol Cipularang</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-actions-cell">
                                <button type="button" class="btn-proactive-chat" onclick="triggerProactiveBroadcast('TRV-2026-0811', 'Jakarta - Bandung 08:00', 'Estimasi tiba di Dipatiukur 10:45 WIB')">
                                    <span>Estimasi Sampai</span>
                                </button>
                                <a href="tel:081133221144" class="btn-driver-call" title="Hubungi Driver">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Trip 4: Boarding di Pool -->
                    <tr data-status="boarding">
                        <td>
                            <div class="trip-time-cell">
                                <span class="trip-clock text-brand">10:15 WIB</span>
                                <span class="trip-route-name">Bekasi Barat &rarr; Bandung (Dipatiukur)</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-vehicle-cell">
                                <strong>Toyota HiAce Commuter</strong>
                                <span class="plate-badge">B 1204 KLS</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-driver-cell">
                                <span>Pak Ridwan</span>
                                <small class="text-muted">0812-9911-2233</small>
                            </div>
                        </td>
                        <td>
                            <div class="trip-seat-occupancy">
                                <div class="occupancy-labels">
                                    <strong class="text-slate-800">8 / 12 Kursi</strong>
                                    <span class="occupancy-pct">67%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: 67%; background: var(--brand);"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="delay-alert-tag alert-success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Persiapan Pool &bull; On Schedule</span>
                            </div>
                        </td>
                        <td>
                            <div class="trip-actions-cell">
                                <button type="button" class="btn-proactive-chat" onclick="triggerProactiveBroadcast('TRV-2026-0612', 'Bekasi - Bandung 10:15', 'Boarding pool dibuka pukul 10:00')">
                                    <span>Pemberitahuan Boarding</span>
                                </button>
                                <a href="tel:081299112233" class="btn-driver-call" title="Hubungi Driver">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>
