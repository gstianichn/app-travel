            <!-- ================= 2. STATUS & PENGUBAH STATUS PERJALANAN ================= -->
            <div class="stepper-container" id="section-status">
                <div class="stepper-header">
                    <h3>
                        <span>📍</span>
                        <span>Alur Status Perjalanan Bertahap</span>
                    </h3>

                    <div class="trip-status-badge {{ $activeSchedule->tripStatusBadgeClass() }}">
                        {{ $activeSchedule->tripStatusLabel() }}
                    </div>
                </div>

                @php
                    $curKey = $activeSchedule->tripStatusKey();
                @endphp

                <div class="stepper-track">
                    <!-- Step 1: Belum Berangkat / Standby -->
                    <div class="step-item {{ $curKey === 'scheduled' ? 'active' : 'passed' }}">
                        <div class="step-number">{{ $curKey !== 'scheduled' ? '✓' : '1' }}</div>
                        <span class="step-title">Standby Pool</span>
                        <span class="step-time">Persiapan di Garasi</span>
                    </div>

                    <!-- Step 2: Mulai Jemput -->
                    <div class="step-item {{ $curKey === 'pickup' ? 'active' : (in_array($curKey, ['departed', 'arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">{{ in_array($curKey, ['departed', 'arrived', 'completed']) ? '✓' : '2' }}</div>
                        <span class="step-title">Mulai Jemput</span>
                        <span class="step-time">{{ $activeSchedule->pickup_started_at ? $activeSchedule->pickup_started_at->format('H:i').' WIB' : 'Titik Naik' }}</span>
                    </div>

                    <!-- Step 3: Dalam Perjalanan -->
                    <div class="step-item {{ $curKey === 'departed' ? 'active' : (in_array($curKey, ['arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">{{ in_array($curKey, ['arrived', 'completed']) ? '✓' : '3' }}</div>
                        <span class="step-title">Berangkat / Jalan</span>
                        <span class="step-time">{{ $activeSchedule->departed_at ? $activeSchedule->departed_at->format('H:i').' WIB' : 'Jalur Tol' }}</span>
                    </div>

                    <!-- Step 4: Tiba di Tujuan -->
                    <div class="step-item {{ $curKey === 'arrived' ? 'active' : ($curKey === 'completed' ? 'passed' : '') }}">
                        <div class="step-number">{{ $curKey === 'completed' ? '✓' : '4' }}</div>
                        <span class="step-title">Tiba di Tujuan</span>
                        <span class="step-time">{{ $activeSchedule->arrived_at ? $activeSchedule->arrived_at->format('H:i').' WIB' : 'Pool Kedatangan' }}</span>
                    </div>
                </div>
            </div>

            </div>
        </section>

        @php
            $unboardedPassenger = $manifestPassengers->firstWhere('is_boarded', false);
            $nextPassenger = $unboardedPassenger ?? $manifestPassengers->first();

            if ($unboardedPassenger) {
                $nextTargetTitle = $unboardedPassenger->name;
                $nextTargetType = 'Jemput Penumpang Berikutnya';
                $nextTargetAddress = $unboardedPassenger->pickup_point;
                $nextTargetNotes = !empty($unboardedPassenger->special_request) && $unboardedPassenger->special_request !== '-' 
                    ? $unboardedPassenger->special_request 
                    : $unboardedPassenger->baggage_notes;
                $nextSeatNumber = 'Seat ' . $unboardedPassenger->seat_number;
            } else {
                $nextTargetTitle = $activeSchedule->route?->destination?->name ?? 'Pool Kedatangan';
                $nextTargetType = 'Menuju Titik Turun Akhir';
                $nextTargetAddress = $activeSchedule->route?->destination?->address ?? ($activeSchedule->route?->destination?->name ?? 'Pool Semanggi Plaza');
                $nextTargetNotes = 'Semua penumpang telah naik kabin. Meluncur aman menuju destinasi akhir.';
                $nextSeatNumber = 'Semua Naik';
            }

            $targetQuery = urlencode($nextTargetAddress);
            $nextGmapsUrl = "https://www.google.com/maps/search/?api=1&query={$targetQuery}";
            $nextWazeUrl = "https://waze.com/ul?q={$targetQuery}&navigate=yes";
        @endphp

