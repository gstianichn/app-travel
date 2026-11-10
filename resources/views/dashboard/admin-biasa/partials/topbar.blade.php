<!-- TOPBAR -->
<header class="topbar">
    <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
        <button type="button" class="staff-menu-toggle" onclick="toggleStaffSidebar()" aria-label="Buka menu navigasi">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>

        <div class="page-title">
            @if(!empty($topbarTitle))
                <h1 style="font-size: 1.05rem; margin: 0;">{{ $topbarTitle }}</h1>
                @if(!empty($topbarSubtitle))
                    <p style="margin: 0; font-size: 0.8rem;">{{ $topbarSubtitle }}</p>
                @endif
            @else
                <p style="margin: 0;"><strong>TravelKu</strong>, staf pool</p>
            @endif
        </div>
    </div>

    <div class="profile">
        <div class="avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
        </div>
        <div class="profile-info">
            <strong>{{ auth()->user()->name ?? 'Staff Operasional' }}</strong>
            <span>{{ ($shiftInfo['pool_name'] ?? auth()->user()->branch) ?: 'Staf pool' }}</span>
        </div>
    </div>
</header>
