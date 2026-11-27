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
        <form class="topbar-search" method="GET" action="{{ route('dashboard.customer-service.cek-tiket') }}" role="search">
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
                   value="{{ request()->routeIs('dashboard.customer-service.cek-tiket') ? request('q') : '' }}"
                   placeholder="Kode booking, nama penumpang, atau nomor HP"
                   autocomplete="off">
            <kbd class="search-shortcut">Ctrl + K</kbd>
        </form>
    </div>

    <!-- Right Controls: Quick Jump & Agent Profile -->
    <div class="topbar-right">
        <a href="{{ route('dashboard.customer-service.pantauan-armada') }}" class="topbar-icon-btn" title="Pantauan Armada Hari Ini" aria-label="Pantauan Armada Hari Ini">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </a>

        @php
            $rawCsName = trim((string) (auth()->user()->name ?? 'Customer Service'));
            $cleanCsName = trim((string) preg_replace('/\s*\([^)]*\)\s*/', ' ', $rawCsName)) ?: $rawCsName;
            $nameParts = preg_split('/\s+/', $cleanCsName) ?: [];
            $csInitials = count($nameParts) >= 2
                ? strtoupper(mb_substr($nameParts[0], 0, 1).mb_substr($nameParts[1], 0, 1))
                : strtoupper(mb_substr($cleanCsName, 0, 2));
        @endphp

        <!-- Profile Card -->
        <div class="profile" onclick="toggleProfileDropdown()">
            <div class="profile-avatar">
                {{ $csInitials }}
            </div>

            <div class="profile-info">
                <span class="profile-name">
                    {{ $cleanCsName }}
                </span>
                <span class="profile-role">
                    Customer Service
                </span>
            </div>

            <div class="profile-chevron">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <!-- Profile Dropdown Menu -->
            <div class="profile-dropdown" id="profile-dropdown">
                <a href="{{ route('dashboard.customer-service.profil') }}" class="profile-dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Profil Saya</span>
                </a>
            </div>
        </div>
    </div>

</header>
