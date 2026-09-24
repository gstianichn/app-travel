            <!-- ================= 5. MELIHAT SEAT MAP ================= -->
            <div class="card-panel" id="section-seatmap" style="margin-bottom: 24px;">
                <div class="card-panel-header">
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                            <span>FITUR 5</span>
                            <span>•</span>
                            <span>DENAH KURSI KABIN</span>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <span>💺</span>
                            <span>Melihat Seat Map</span>
                        </h3>
                        <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                            Fokus: Denah visual keterisian bangku kabin penumpang, posisi driver, dan status kursi (Terisi / Kosong).
                        </p>
                    </div>
                    <span class="pill-badge" style="font-size:11px;">{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }}</span>
                </div>

                <div class="card-panel-body">
                    <div class="seatmap-container">
                        <div class="cabin-layout">
                            <!-- DEPAN / WINDSHIELD -->
                            <div class="cockpit-front">
                                <div class="driver-seat">
                                    <span>💺</span>
                                    <span>SOPIR</span>
                                </div>
                                <span class="windshield-label">Kaca Depan</span>
                            </div>

                            <!-- ROWS KURSI -->
                            @if (!empty($seatOccupancyRows))
                                @foreach ($seatOccupancyRows as $rowKey => $seatsInRow)
                                    <div class="seat-row">
                                        @foreach ($seatsInRow as $seatItem)
                                            @php
                                                $occ = $seatItem['occupancy']['status'] ?? 'available';
                                                $pName = $seatItem['occupancy']['passenger_name'] ?? 'Kosong';
                                                $isBoarded = false;

                                                // Cari apakah penumpang di kursi ini sudah naik
                                                foreach ($manifestPassengers as $mp) {
                                                    if ($mp->seat_number == $seatItem['seat_number'] && $mp->is_boarded) {
                                                        $isBoarded = true;
                                                        break;
                                                    }
                                                }

                                                $slotClass = $isBoarded ? 'boarded' : ($occ !== 'available' ? 'pending' : 'empty');
                                            @endphp

                                            <div class="seat-slot {{ $slotClass }}"
                                                 onclick="showSeatInfo('{{ $seatItem['seat_number'] }}', '{{ addslashes($pName) }}', '{{ $slotClass }}')"
                                                 title="Kursi {{ $seatItem['seat_number'] }}: {{ $pName }}">
                                                <strong>{{ $seatItem['seat_number'] }}</strong>
                                                <small>{{ $isBoarded ? 'NAIK' : ($occ !== 'available' ? 'TERISI' : 'KOSONG') }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback Default Minibus 10 Seats -->
                                <div class="seat-row">
                                    <div class="seat-slot boarded" onclick="showSeatInfo('1A', 'Budi Santoso', 'boarded')">
                                        <strong>1A</strong><small>NAIK</small>
                                    </div>
                                    <div style="flex:1;"></div>
                                </div>
                                <div class="seat-row">
                                    <div class="seat-slot pending" onclick="showSeatInfo('2A', 'Siti Rahmawati', 'pending')">
                                        <strong>2A</strong><small>TERISI</small>
                                    </div>
                                    <div class="seat-slot pending" onclick="showSeatInfo('2B', 'Hendra Gunawan', 'pending')">
                                        <strong>2B</strong><small>TERISI</small>
                                    </div>
                                </div>
                                <div class="seat-row">
                                    <div class="seat-slot empty" onclick="showSeatInfo('3A', 'Kosong', 'empty')">
                                        <strong>3A</strong><small>KOSONG</small>
                                    </div>
                                    <div class="seat-slot empty" onclick="showSeatInfo('3B', 'Kosong', 'empty')">
                                        <strong>3B</strong><small>KOSONG</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="seatmap-legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background:#10b981;"></div>
                                <span>Sudah Naik</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#f59e0b;"></div>
                                <span>Dipesan (Belum Naik)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#cbd5e1;"></div>
                                <span>Kosong</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

