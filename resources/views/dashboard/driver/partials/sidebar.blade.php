<!-- ========================================
     SIDEBAR DRIVER OPERASIONAL (TRAVELKU DESIGN SYSTEM)
========================================= -->
<aside class="sidebar" id="sidebar">

    <!-- Header Logo & Brand -->
    <div class="brand-zone" style="height: 74px; display: flex; align-items: center; padding: 0 22px; border-bottom: 1px solid rgba(255, 255, 255, 0.12);">
        <div class="brand-logo-icon" style="width: 40px; height: 40px; background: #ffffff; border-radius: 11px; display: flex; align-items: center; justify-content: center; padding: 5px; box-sizing: border-box; margin-right: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); flex-shrink: 0;">
            <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="brand-text">
            <h2 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0; line-height: 1.2; letter-spacing: -0.2px;">TravelKu</h2>
            <span style="font-size: 11px; color: #a7f3d0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Driver Cockpit</span>
        </div>
    </div>

    <!-- Navigasi Menu Driver -->
    <nav class="nav-menu" style="flex: 1; padding: 12px 14px; overflow-y: auto;">
        <!-- 1. Dashboard -->
        <a href="{{ route('dashboard.driver.index') }}" class="nav-link {{ request()->routeIs('dashboard.driver.index') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        <div class="nav-label" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #6ee7b7; padding: 14px 12px 6px;">Jadwal & Armada</div>

        <!-- 2. Melihat jadwal perjalanan -->
        <a href="{{ route('dashboard.driver.jadwal') }}" class="nav-link {{ request()->routeIs('dashboard.driver.jadwal') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </span>
            <span>Melihat Jadwal Perjalanan</span>
        </a>

        <!-- 3. Melihat kendaraan yang ditugaskan -->
        <a href="{{ route('dashboard.driver.kendaraan') }}" class="nav-link {{ request()->routeIs('dashboard.driver.kendaraan') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h13l4 4v7H3V6z"/>
                    <circle cx="7.5" cy="17.5" r="2"/>
                    <circle cx="16.5" cy="17.5" r="2"/>
                    <path d="M16 6v4h4"/>
                    <path d="M7 10h4"/>
                </svg>
            </span>
            <span>Melihat Kendaraan Ditugaskan</span>
        </a>

        <div class="nav-label" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #6ee7b7; padding: 14px 12px 6px;">Penumpang & Kabin</div>

        <!-- 4. Melihat daftar penumpang -->
        <a href="{{ route('dashboard.driver.penumpang') }}" class="nav-link {{ request()->routeIs('dashboard.driver.penumpang') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span>Melihat Daftar Penumpang</span>
            <span class="menu-badge-count" style="margin-left: auto; background: rgba(255,255,255,0.2); color: white; padding: 2px 7px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $totalBookedPassengers ?? 0 }}</span>
        </a>

        <!-- 5. Melihat seat map -->
        <a href="{{ route('dashboard.driver.seatmap') }}" class="nav-link {{ request()->routeIs('dashboard.driver.seatmap') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 19v2"/>
                    <path d="M17 19v2"/>
                    <path d="M5 8v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/>
                    <path d="M4 11h16"/>
                    <path d="M7 3h10a2 2 0 0 1 2 2v3H5V5a2 2 0 0 1 2-2z"/>
                </svg>
            </span>
            <span>Melihat Seat Map Kabin</span>
        </a>

        <div class="nav-label" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #6ee7b7; padding: 14px 12px 6px;">Rute & Status Perjalanan</div>

        <!-- 6. Melihat rute perjalanan -->
        <a href="{{ route('dashboard.driver.rute') }}" class="nav-link {{ request()->routeIs('dashboard.driver.rute') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="19" r="3"/>
                    <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-7a3.5 3.5 0 0 1 0-7H18"/>
                    <circle cx="18" cy="5" r="3"/>
                </svg>
            </span>
            <span>Melihat Rute Perjalanan</span>
        </a>

        <!-- 7. Melihat status perjalanan -->
        <a href="{{ route('dashboard.driver.status') }}" class="nav-link {{ request()->routeIs('dashboard.driver.status') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                </svg>
            </span>
            <span>Melihat Status Perjalanan</span>
        </a>

        <!-- 8. Mengubah status perjalanan -->
        <a href="{{ route('dashboard.driver.ubah-status') }}" class="nav-link {{ request()->routeIs('dashboard.driver.ubah-status') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </span>
            <span>Mengubah Status Perjalanan</span>
        </a>

        <!-- 9. Melihat informasi penumpang -->
        <a href="{{ route('dashboard.driver.info-penumpang') }}" class="nav-link {{ request()->routeIs('dashboard.driver.info-penumpang') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </span>
            <span>Melihat Informasi Penumpang</span>
        </a>

        <div class="nav-label" style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #6ee7b7; padding: 14px 12px 6px;">Bantuan, Pelacakan & Arsip</div>

        <!-- 10. Melaporkan masalah perjalanan -->
        <a href="{{ route('dashboard.driver.lapor-masalah') }}" class="nav-link {{ request()->routeIs('dashboard.driver.lapor-masalah') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </span>
            <span>Melaporkan Masalah Perjalanan</span>
        </a>

        <!-- 11. Emergency Center -->
        <a href="{{ route('dashboard.driver.emergency') }}" class="nav-link nav-link-emergency {{ request()->routeIs('dashboard.driver.emergency') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </span>
            <span>Emergency Center</span>
            <span class="menu-badge-sos" style="margin-left: auto;">SOS</span>
        </a>

        <!-- 12. GPS Tracking saat perjalanan aktif -->
        <a href="{{ route('dashboard.driver.gps') }}" class="nav-link {{ request()->routeIs('dashboard.driver.gps') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </span>
            <span>GPS Tracking Live</span>
            <span class="menu-badge-live" style="margin-left: auto;">LIVE</span>
        </a>

        <!-- 13. Melihat performa -->
        <a href="{{ route('dashboard.driver.performa') }}" class="nav-link {{ request()->routeIs('dashboard.driver.performa') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </span>
            <span>Melihat Performa</span>
        </a>

        <!-- 14. Melihat reward -->
        <a href="{{ route('dashboard.driver.reward') }}" class="nav-link {{ request()->routeIs('dashboard.driver.reward') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </span>
            <span>Melihat Reward</span>
        </a>

        <!-- 15. Melihat riwayat perjalanan -->
        <a href="{{ route('dashboard.driver.riwayat') }}" class="nav-link {{ request()->routeIs('dashboard.driver.riwayat') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span>Melihat Riwayat Perjalanan</span>
        </a>
    </nav>

    <!-- Footer Sidebar: Status Driver & Logout -->
    <div class="sidebar-foot" style="padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.12); margin-top: auto; background: rgba(0,0,0,0.12);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="live-dot" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #34d399; box-shadow: 0 0 6px #34d399;"></span>
                <span style="font-size: 11px; font-weight: 700; color: #a7f3d0; text-transform: uppercase;">{{ $driver->statusLabel() }}</span>
            </div>
            <span style="font-size: 11px; color: #6ee7b7; font-weight: 600;">{{ $driver->license_type ?? 'SIM B1' }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); color: white; border: none; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                <span>Keluar / Logout</span>
            </button>
        </form>
    </div>

</aside>
