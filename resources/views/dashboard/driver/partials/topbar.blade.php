<!-- ========================================
     TOPBAR DRIVER OPERASIONAL (TRAVELKU STAFF SYSTEM)
========================================= -->
<header class="topbar">
    <div class="topbar-left" style="display: flex; align-items: center; gap: 14px; min-width: 0;">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka Menu Navigasi" style="display: none; background: none; border: none; cursor: pointer; color: var(--lp-ink); padding: 4px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="page-title" style="min-width: 0;">
            <p><strong>TravelKu</strong>, driver</p>
        </div>
    </div>

    <!-- Status Operasional, Tombol SOS & Profil Driver -->
    <div class="topbar-right" style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
        <!-- Status GPS Live -->
        <div class="ds-badge ds-badge--ok" id="topbarGpsBadge" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; font-size: 12px; padding: 4px 10px;">
            <span class="live-dot" style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #0C7950; box-shadow: 0 0 6px rgba(12, 121, 80, 0.6);"></span>
            <span id="gpsTextIndicator">GPS Aktif</span>
        </div>

        <!-- Tombol SOS Darurat -->
        <button type="button" class="ds-btn ds-btn--danger" onclick="openSosModal()" title="Buka Akses Darurat Instan" style="min-height: 38px; padding: 0 14px; font-size: 12px; font-weight: 800; border-radius: 8px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>SOS DARURAT</span>
        </button>

        <!-- Driver Profile Component -->
        <div class="profile">
            <div class="avatar">
                {{ strtoupper(substr($user->name ?? 'D', 0, 1)) }}
            </div>
            <div class="profile-info">
                <strong>
                    {{ \Illuminate\Support\Str::before($user->name ?? 'Pengemudi', ' ') }}
                </strong>
                <span>
                    {{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}
                </span>
            </div>
        </div>
    </div>
</header>
