            <!-- ================= 2. STATUS & PENGUBAH STATUS PERJALANAN ================= -->
            <div class="stepper-container" id="section-status">
                <div class="stepper-header">
                    <h3 style="display:flex; align-items:center; gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/></svg>
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
                        <div class="step-number">@if($curKey !== 'scheduled')<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 1 @endif</div>
                        <span class="step-title">Standby Pool</span>
                        <span class="step-time">Persiapan di Garasi</span>
                    </div>

                    <!-- Step 2: Mulai Jemput -->
                    <div class="step-item {{ $curKey === 'pickup' ? 'active' : (in_array($curKey, ['departed', 'arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">@if(in_array($curKey, ['departed', 'arrived', 'completed']))<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 2 @endif</div>
                        <span class="step-title">Mulai Jemput</span>
                        <span class="step-time">{{ $activeSchedule->pickup_started_at ? $activeSchedule->pickup_started_at->format('H:i').' WIB' : 'Titik Naik' }}</span>
                    </div>

                    <!-- Step 3: Dalam Perjalanan -->
                    <div class="step-item {{ $curKey === 'departed' ? 'active' : (in_array($curKey, ['arrived', 'completed']) ? 'passed' : '') }}">
                        <div class="step-number">@if(in_array($curKey, ['arrived', 'completed']))<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 3 @endif</div>
                        <span class="step-title">Berangkat / Jalan</span>
                        <span class="step-time">{{ $activeSchedule->departed_at ? $activeSchedule->departed_at->format('H:i').' WIB' : 'Jalur Tol' }}</span>
                    </div>

                    <!-- Step 4: Tiba di Tujuan -->
                    <div class="step-item {{ $curKey === 'arrived' ? 'active' : ($curKey === 'completed' ? 'passed' : '') }}">
                        <div class="step-number">@if($curKey === 'completed')<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@else 4 @endif</div>
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

