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
                <p style="margin: 0;"><strong>TravelKu</strong>, Staff Operasional</p>
            @endif
        </div>
    </div>

    <div class="staff-profile-wrap" id="staffProfileWrap">
        <button type="button"
                class="profile {{ request()->routeIs('dashboard.admin-biasa.profil') ? 'is-active' : '' }}"
                id="staffProfileTrigger"
                onclick="toggleStaffProfileDropdown(event)"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="staffProfileDropdown"
                aria-label="Menu profil {{ auth()->user()->name ?? 'Staff Operasional' }}">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
            </div>
            <div class="profile-info">
                <strong>{{ auth()->user()->name ?? 'Staff Operasional' }}</strong>
                <span>{{ ($shiftInfo['pool_name'] ?? auth()->user()->branch) ?: 'Staff Operasional' }}</span>
            </div>
            <span class="profile-chevron" aria-hidden="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </span>
        </button>

        <div class="staff-profile-dropdown" id="staffProfileDropdown" role="menu">
            <a href="{{ route('dashboard.admin-biasa.profil') }}"
               class="staff-profile-dropdown-item {{ request()->routeIs('dashboard.admin-biasa.profil') ? 'is-active' : '' }}"
               role="menuitem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Profil Saya</span>
            </a>
        </div>
    </div>
</header>

<script>
    function toggleStaffProfileDropdown(event) {
        if (event) event.stopPropagation();
        var dropdown = document.getElementById('staffProfileDropdown');
        var trigger = document.getElementById('staffProfileTrigger');
        if (!dropdown || !trigger) return;
        var isOpen = dropdown.classList.toggle('show');
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    document.addEventListener('click', function (event) {
        var wrap = document.getElementById('staffProfileWrap');
        var dropdown = document.getElementById('staffProfileDropdown');
        var trigger = document.getElementById('staffProfileTrigger');
        if (wrap && dropdown && !wrap.contains(event.target)) {
            dropdown.classList.remove('show');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            var dropdown = document.getElementById('staffProfileDropdown');
            var trigger = document.getElementById('staffProfileTrigger');
            if (dropdown) dropdown.classList.remove('show');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    });
</script>
