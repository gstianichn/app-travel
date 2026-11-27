<!-- ========================================
     SIDEBAR CUSTOMER SERVICE
========================================= -->

<aside id="cs-sidebar" class="sidebar">

    <!-- Header Logo & Brand -->
    <div class="sidebar-header">
        <a href="{{ route('dashboard.customer-service.index') }}" class="brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="brand-text">
                <span class="brand-title">TravelKu</span>
                <span class="brand-subtitle">CS Helpdesk Hub</span>
            </div>
        </a>
        <button type="button" class="sidebar-close-btn" onclick="toggleCsSidebar(false)" aria-label="Tutup Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Navigasi Menu Inti Customer Service -->
    <nav class="menu">
        <div class="menu-title">Menu Utama CS</div>

        <ul class="menu-list">
            <!-- 1. Dashboard -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.index') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.index') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- 2. Live Chat & SLA -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.live-chat') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.live-chat*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span>
                    <span class="menu-text">Live Chat &amp; SLA</span>
                </a>
            </li>

            <!-- 2. Cek Tiket Penumpang (Read-Only) -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.cek-tiket') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.cek-tiket*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <span class="menu-text">Cek Tiket Penumpang</span>
                </a>
            </li>

            <!-- 3. Laporan & Tiket Kendala -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.laporan-kendala') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.laporan-kendala*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </span>
                    <span class="menu-text">Laporan Kendala</span>
                </a>
            </li>

            <!-- 4. Pantauan Armada Hari Ini -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.pantauan-armada') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.pantauan-armada*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </span>
                    <span class="menu-text">Pantauan Armada</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout Footer -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" id="cs-logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <span class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </span>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
