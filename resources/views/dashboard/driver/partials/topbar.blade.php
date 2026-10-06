        <header class="task-identity-header">
            <div class="task-vehicle-identity">
                <button type="button" class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Menu Navigasi" style="display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
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
                <div class="system-pill gps-live" id="topbarGpsBadge" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/></svg>
                    <span id="gpsTextIndicator">GPS: Aktif</span>
                </div>

                <div class="system-pill conn-online" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h.01M8.5 16.5a5 5 0 017 0M5 13a10 10 0 0114 0M1.5 9.5a15 15 0 0121 0"/></svg>
                    <span>Koneksi: Online (4G LTE)</span>
                </div>

                <div class="system-pill" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/></svg>
                    <span>{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Tombol SOS (Merah): Akses darurat instan yang selalu terlihat di pojok atas -->
                <button type="button" class="btn-header-sos" onclick="openSosModal()" title="Buka Akses Darurat Instan" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>SOS DARURAT</span>
                </button>
            </div>
        </header>
