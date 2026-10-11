<!-- ========================================
     TOPBAR CUSTOMER SERVICE
========================================= -->

<header class="topbar">

    <div class="topbar-left">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" class="topbar-mobile-toggle" onclick="toggleCsSidebar(true)" aria-label="Buka Menu Navigasi">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="topbar-heading">
            <p class="topbar-title">Customer service</p>
        </div>
    </div>

    <!-- Quick Search Lookup Bar -->
    <div class="topbar-center">
        <form class="topbar-search" method="GET" action="{{ route('dashboard.customer-service.index') }}" role="search">
            <span class="search-icon" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <label for="global-booking-search" class="ds-visually-hidden">Cari pesanan</label>
            <input type="search"
                   id="global-booking-search"
                   name="q"
                   value="{{ request()->routeIs('dashboard.customer-service.index') ? request('q') : '' }}"
                   placeholder="Kode booking, tiket, nama, atau HP"
                   autocomplete="off">
            <kbd class="search-shortcut">Ctrl + K</kbd>
        </form>
    </div>

    <!-- Right Controls: Status, Notifications, Agent Profile -->
    <div class="topbar-right">

        <!-- Notification / Live Chat Incoming Indicator -->
        <a href="{{ route('dashboard.customer-service.live-chat') }}" class="topbar-icon-btn" title="Live Chat Masuk" aria-label="Notifikasi Chat Masuk">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </a>

        <!-- Profile Card -->
        <div class="profile" onclick="toggleProfileDropdown()">
            <div class="profile-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'CS', 0, 2)) }}
            </div>

            <div class="profile-info">
                <span class="profile-name">
                    {{ auth()->user()->name ?? 'Customer Service' }}
                </span>
                <span class="profile-role">
                    Customer Service Specialist
                </span>
            </div>

            <div class="profile-chevron">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <!-- Profile Dropdown Menu -->
            <div class="profile-dropdown" id="profile-dropdown">
                <div class="profile-dropdown-header">
                    <strong>{{ auth()->user()->name ?? 'Customer Service' }}</strong>
                    <small>{{ auth()->user()->email ?? 'cs@travelku.com' }}</small>
                </div>
                <div class="profile-dropdown-divider"></div>
                <a href="{{ route('dashboard.customer-service.live-chat') }}" class="profile-dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Ruang Live Chat</span>
                </a>
                <a href="#quick-lookup" class="profile-dropdown-item" onclick="focusQuickSearch(event)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span>Cari Tiket Pelanggan</span>
                </a>
                <div class="profile-dropdown-divider"></div>
                <button type="button" class="profile-dropdown-item text-danger" onclick="document.getElementById('cs-logout-form').submit()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Keluar (Logout)</span>
                </button>
            </div>
        </div>

    </div>

</header>
