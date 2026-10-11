        <!-- ================= 10. RIWAYAT PERJALANAN (RITASE SELESAI) ================= -->
        <section class="card-panel" id="section-riwayat">
            <div class="card-panel-header">
                <div>
                    <h3 style="display:flex; align-items:center; gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
                        <span>Riwayat Perjalanan (Ritase Selesai &amp; Rekap Harian)</span>
                    </h3>
                    <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Daftar ritase operasional yang telah selesai untuk rekapitulasi harian.</p>
                </div>

                <div class="pill-badge" style="background:#ecfdf5; color:#065f46; font-weight:800;">
                    Total Selesai: {{ $totalCompletedTrips }} Perjalanan
                </div>
            </div>

            <div class="card-panel-body" style="padding:0;">
                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Armada</th>
                                <th>Rute Perjalanan</th>
                                <th>Penumpang</th>
                                <th>Status Ritase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedTrips as $index => $cTrip)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $cTrip->departure_date ? $cTrip->departure_date->format('d/m/Y') : date('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">{{ Carbon\Carbon::parse($cTrip->departure_time)->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <strong>{{ $cTrip->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">{{ $cTrip->vehicle?->name ?? 'Toyota HiAce' }}</div>
                                    </td>
                                    <td>
                                        {{ $cTrip->route?->origin?->name ?? 'Bandung' }} &nbsp; ke &nbsp; {{ $cTrip->route?->destination?->name ?? 'Jakarta' }}
                                    </td>
                                    <td>
                                        <span class="seat-badge-pill" style="background:#059669; border-color:#059669;">
                                            {{ $cTrip->bookings?->sum('total_passenger') ?? 8 }} Orang
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">
                                            Ditutup Selesai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <!-- Contoh ritase dummy bila belum ada di DB -->
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <strong>{{ Carbon\Carbon::yesterday()->format('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">08:00 WIB</div>
                                    </td>
                                    <td>
                                        <strong>D 7812 AB</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">Toyota HiAce Premio Eksekutif</div>
                                    </td>
                                    <td>Bandung (Dipatiukur) ke Jakarta (Semanggi)</td>
                                    <td><span class="seat-badge-pill" style="background:#059669;">10 Orang</span></td>
                                    <td><span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">Selesai</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <strong>{{ Carbon\Carbon::yesterday()->format('d/m/Y') }}</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">14:30 WIB</div>
                                    </td>
                                    <td>
                                        <strong>D 7812 AB</strong>
                                        <div style="font-size:11px; color:var(--text-muted);">Toyota HiAce Premio Eksekutif</div>
                                    </td>
                                    <td>Jakarta (Semanggi) ke Bandung (Dipatiukur)</td>
                                    <td><span class="seat-badge-pill" style="background:#059669;">9 Orang</span></td>
                                    <td><span class="badge-status-completed" style="padding:4px 8px; border-radius:6px; font-weight:700; font-size:11px;">Selesai</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

