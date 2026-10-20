<!-- ========================================
     TOPBAR DRIVER OPERASIONAL (TRAVELKU DESIGN SYSTEM)
========================================= -->
<header class="topbar">
    <div class="topbar-left" style="display: flex; align-items: center; gap: 14px; min-width: 0;">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka Menu Navigasi" style="display: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="topbar-heading" style="min-width: 0;">
            <h2 style="font-size: 17px; font-weight: 800; color: var(--lp-forest); margin: 0; line-height: 1.2; letter-spacing: -0.01em;">
                Driver Operasional
            </h2>
            <p style="font-size: 12.5px; color: var(--lp-muted); margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}
                &bull; <strong style="color: var(--lp-ink);">{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                @if($activeSchedule->vehicle?->hullNumber())
                    &bull; No. {{ $activeSchedule->vehicle->hullNumber() }}
                @endif
            </p>
        </div>
    </div>

    <!-- Status Operasional, Tombol SOS & Profil Driver -->
    <div class="topbar-right" style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
        <!-- Status GPS Live -->
        <div class="ds-badge ds-badge--ok" id="topbarGpsBadge" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 12px; padding: 4px 10px;">
            <span class="live-dot" style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #0C7950; box-shadow: 0 0 6px rgba(12, 121, 80, 0.6);"></span>
            <span id="gpsTextIndicator">GPS Aktif</span>
        </div>

        <!-- Tanggal Hari Ini -->
        <div class="ds-badge ds-badge--muted" style="display: none; font-size: 12px; padding: 4px 10px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/></svg>
            <span>{{ Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>

        <!-- Tombol SOS Darurat -->
        <button type="button" class="ds-btn ds-btn--danger" onclick="openSosModal()" title="Buka Akses Darurat Instan" style="min-height: 38px; padding: 0 14px; font-size: 12px; font-weight: 800; border-radius: 8px; box-shadow: 0 2px 6px rgba(163, 52, 31, 0.25);">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>SOS DARURAT</span>
        </button>

        <!-- Driver Profile Component -->
        <div class="profile" style="display: flex; align-items: center; gap: 10px; padding-left: 10px; border-left: 1px solid var(--lp-line);">
            <div class="avatar" style="width: 38px; height: 38px; border-radius: 50%; background: var(--lp-forest); color: white; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 13px; box-shadow: 0 2px 6px rgba(12, 57, 47, 0.15);">
                {{ strtoupper(substr($user->name ?? 'DR', 0, 2)) }}
            </div>
            <div class="profile-info" style="display: flex; flex-direction: column;">
                <strong style="font-size: 13px; font-weight: 700; color: var(--lp-ink); line-height: 1.2;">
                    {{ \Illuminate\Support\Str::before($user->name ?? 'Pengemudi', ' ') }}
                </strong>
                <span style="font-size: 11px; color: var(--lp-muted); line-height: 1.2; margin-top: 2px;">
                    {{ $driver->license_type ?? 'SIM B1' }} &bull; <span style="color: var(--lp-green); font-weight: 700;">{{ strtoupper($driver->statusLabel()) }}</span>
                </span>
            </div>
        </div>
    </div>
</header>
