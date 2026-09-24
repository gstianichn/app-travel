        <header class="task-identity-header">
            <div class="task-vehicle-identity">
                <button type="button" class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Menu Navigasi">
                    ☰
                </button>
                <div class="plate-emblem">
                    <span>POL</span>
                    <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                </div>
                <div class="task-vehicle-meta">
                    <h3>{{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}</h3>
                    <p>
                        Tipe: <strong>{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }}</strong>
                        &nbsp;•&nbsp; No. Lambung: <strong>{{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}</strong>
                        &nbsp;•&nbsp; Lokasi Parkir: <strong>{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}</strong>
                    </p>
                </div>
            </div>

            <div class="system-status-group">
                <div class="system-pill gps-live" id="topbarGpsBadge">
                    <span style="font-size:13px;">📡</span>
                    <span id="gpsTextIndicator">GPS: Aktif</span>
                </div>

                <div class="system-pill conn-online">
                    <span style="font-size:13px;">📶</span>
                    <span>Koneksi: Online (4G LTE)</span>
                </div>

                <div class="system-pill">
                    <span>📅</span>
                    <span>{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Tombol SOS (Merah): Akses darurat instan yang selalu terlihat di pojok atas -->
                <button type="button" class="btn-header-sos" onclick="openSosModal()" title="Buka Akses Darurat Instan">
                    <span>🚨</span>
                    <span>SOS DARURAT</span>
                </button>
            </div>
        </header>
