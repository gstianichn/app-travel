<!-- ========================================
     SIDEBAR CUSTOMER SERVICE (SESUAI PRD 4.5)
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

    <!-- Agent Quick Status Card -->
    <div class="sidebar-agent-card">
        <div class="agent-avatar-wrap">
            <div class="agent-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'CS', 0, 2)) }}
            </div>
            
        </div>
        <div class="agent-meta">
            <div class="agent-name">{{ auth()->user()->name ?? 'Customer Service' }}</div>
            <div class="agent-role-badge">Agent ID #{{ str_pad(auth()->id() ?? 1, 4, '0', STR_PAD_LEFT) }} &bull; CS Specialist</div>
        </div>
    </div>

    <!-- Navigasi Menu Sesuai PRD 4.5 -->
    <nav class="menu">
        <div class="menu-title">Fitur Layanan CS</div>

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

            <!-- 2. Layanan Pelanggan -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.live-chat') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.live-chat*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span>
                    <span class="menu-text">Layanan Pelanggan</span>
                    <span class="menu-badge">Contoh</span>
                </a>
            </li>

            <!-- 3. Melihat Konteks Booking -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.booking-context') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.booking-context*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <span class="menu-text">Melihat Konteks Booking</span>
                </a>
            </li>

            <!-- 4. Melihat Riwayat Chat -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.chat-history') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.chat-history*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                        </svg>
                    </span>
                    <span class="menu-text">Melihat Riwayat Chat</span>
                </a>
            </li>


            <!-- 5. Mengirim Gambar/File -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.send-file') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.send-file*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                        </svg>
                    </span>
                    <span class="menu-text">Mengirim Gambar/File</span>
                </a>
            </li>

            <!-- 7. Melakukan Eskalasi ke Admin -->
            <li class="menu-item">
                <a href="{{ route('dashboard.customer-service.escalation') }}"
                   class="menu-link {{ request()->routeIs('dashboard.customer-service.escalation*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </span>
                    <span class="menu-text">Melakukan Eskalasi ke Admin</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout & Status Footer -->
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
