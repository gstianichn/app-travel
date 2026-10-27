{{-- Sidebar driver operasional, selaras dengan dashboard staff lainnya --}}
<aside class="sidebar" id="sidebar">

    <!-- Header Logo & Brand -->
    <div class="logo-area">
        <div class="logo-icon" aria-hidden="true">
            <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="logo-text">
            <h2>TravelKu</h2>
            <span>Driver armada</span>
        </div>
        <button type="button" class="sidebar-close-btn" onclick="toggleSidebar(false)" aria-label="Tutup Menu" style="display: none; margin-left: auto; background: none; border: none; color: white; cursor: pointer; padding: 4px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Navigasi Menu Driver -->
    <nav class="menu" id="driver-menu" aria-label="Menu driver">
        <div class="menu-title">Menu Utama</div>

        <!-- 1. Dashboard -->
        @php $isDashboard = request()->routeIs('dashboard.driver.index'); @endphp
        <a href="{{ route('dashboard.driver.index') }}" @class(['active' => $isDashboard]) @if ($isDashboard) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>
            </div>
            <span>Dashboard</span>
        </a>

        <div class="menu-title">Jadwal & Armada</div>

        <!-- 2. Melihat jadwal perjalanan -->
        @php $isJadwal = request()->routeIs('dashboard.driver.jadwal'); @endphp
        <a href="{{ route('dashboard.driver.jadwal') }}" @class(['active' => $isJadwal]) @if ($isJadwal) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <span>Melihat Jadwal Perjalanan</span>
        </a>

        <!-- 3. Melihat kendaraan yang ditugaskan -->
        @php $isKendaraan = request()->routeIs('dashboard.driver.kendaraan'); @endphp
        <a href="{{ route('dashboard.driver.kendaraan') }}" @class(['active' => $isKendaraan]) @if ($isKendaraan) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h13l4 4v7H3V6z"/>
                    <circle cx="7.5" cy="17.5" r="2"/>
                    <circle cx="16.5" cy="17.5" r="2"/>
                    <path d="M16 6v4h4"/>
                    <path d="M7 10h4"/>
                </svg>
            </div>
            <span>Melihat Kendaraan Ditugaskan</span>
        </a>

        <div class="menu-title">Penumpang & Kabin</div>

        <!-- 4. Melihat daftar penumpang -->
        @php $isPenumpang = request()->routeIs('dashboard.driver.penumpang'); @endphp
        <a href="{{ route('dashboard.driver.penumpang') }}" @class(['active' => $isPenumpang]) @if ($isPenumpang) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <span>Melihat Daftar Penumpang</span>
            @if(isset($totalBookedPassengers) && $totalBookedPassengers > 0)
                <span style="margin-left: auto; background: rgba(255,255,255,0.22); color: white; padding: 2px 7px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $totalBookedPassengers }}</span>
            @endif
        </a>

        <!-- 5. Melihat seat map -->
        @php $isSeatmap = request()->routeIs('dashboard.driver.seatmap'); @endphp
        <a href="{{ route('dashboard.driver.seatmap') }}" @class(['active' => $isSeatmap]) @if ($isSeatmap) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 19v2"/>
                    <path d="M17 19v2"/>
                    <path d="M5 8v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/>
                    <path d="M4 11h16"/>
                    <path d="M7 3h10a2 2 0 0 1 2 2v3H5V5a2 2 0 0 1 2-2z"/>
                </svg>
            </div>
            <span>Melihat Seat Map Kabin</span>
        </a>

        <!-- 9. Melihat informasi penumpang -->
        @php $isInfoPenumpang = request()->routeIs('dashboard.driver.info-penumpang'); @endphp
        <a href="{{ route('dashboard.driver.info-penumpang') }}" @class(['active' => $isInfoPenumpang]) @if ($isInfoPenumpang) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <span>Melihat Informasi Penumpang</span>
        </a>

        <div class="menu-title">Rute & Perjalanan</div>

        <!-- 6. Melihat rute perjalanan -->
        @php $isRute = request()->routeIs('dashboard.driver.rute'); @endphp
        <a href="{{ route('dashboard.driver.rute') }}" @class(['active' => $isRute]) @if ($isRute) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="19" r="3"/>
                    <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-7a3.5 3.5 0 0 1 0-7H18"/>
                    <circle cx="18" cy="5" r="3"/>
                </svg>
            </div>
            <span>Melihat Rute Perjalanan</span>
        </a>

        <!-- 7. Melihat status perjalanan -->
        @php $isStatus = request()->routeIs('dashboard.driver.status'); @endphp
        <a href="{{ route('dashboard.driver.status') }}" @class(['active' => $isStatus]) @if ($isStatus) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                </svg>
            </div>
            <span>Melihat Status Perjalanan</span>
        </a>

        <!-- 8. Mengubah status perjalanan -->
        @php $isUbahStatus = request()->routeIs('dashboard.driver.ubah-status'); @endphp
        <a href="{{ route('dashboard.driver.ubah-status') }}" @class(['active' => $isUbahStatus]) @if ($isUbahStatus) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <span>Mengubah Status Perjalanan</span>
        </a>

        <!-- 12. GPS Tracking saat perjalanan aktif -->
        @php $isGps = request()->routeIs('dashboard.driver.gps'); @endphp
        <a href="{{ route('dashboard.driver.gps') }}" @class(['active' => $isGps]) @if ($isGps) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <span>GPS Tracking Live</span>
            <span style="margin-left: auto; background: #0C7950; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">LIVE</span>
        </a>

        <div class="menu-title">Bantuan & Log Kendala</div>

        <!-- 10. Melaporkan masalah perjalanan -->
        @php $isLapor = request()->routeIs('dashboard.driver.lapor-masalah'); @endphp
        <a href="{{ route('dashboard.driver.lapor-masalah') }}" @class(['active' => $isLapor]) @if ($isLapor) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <span>Melaporkan Masalah Perjalanan</span>
        </a>

        <!-- 11. Emergency Center -->
        @php $isEmergency = request()->routeIs('dashboard.driver.emergency'); @endphp
        <a href="{{ route('dashboard.driver.emergency') }}" @class(['active' => $isEmergency]) @if ($isEmergency) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span>Emergency Center</span>
            <span style="margin-left: auto; background: #dc2626; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">SOS</span>
        </a>

        <div class="menu-title">Performa & Arsip</div>

        <!-- 13. Melihat performa -->
        @php $isPerforma = request()->routeIs('dashboard.driver.performa'); @endphp
        <a href="{{ route('dashboard.driver.performa') }}" @class(['active' => $isPerforma]) @if ($isPerforma) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <span>Melihat Performa</span>
        </a>

        <!-- 14. Melihat reward -->
        @php $isReward = request()->routeIs('dashboard.driver.reward'); @endphp
        <a href="{{ route('dashboard.driver.reward') }}" @class(['active' => $isReward]) @if ($isReward) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </div>
            <span>Melihat Reward</span>
        </a>

        <!-- 15. Melihat riwayat perjalanan -->
        @php $isRiwayat = request()->routeIs('dashboard.driver.riwayat'); @endphp
        <a href="{{ route('dashboard.driver.riwayat') }}" @class(['active' => $isRiwayat]) @if ($isRiwayat) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <span>Melihat Riwayat Perjalanan</span>
        </a>
    </nav>

    <script>
        (function() {
            try {
                var m = document.getElementById('driver-menu');
                var s = sessionStorage.getItem('driver_sidebar_scroll');
                if (m && s !== null) {
                    m.scrollTop = parseInt(s, 10);
                }
                if (m) {
                    m.addEventListener('scroll', function() {
                        sessionStorage.setItem('driver_sidebar_scroll', m.scrollTop);
                    }, { passive: true });
                }
            } catch(e) {}
        })();
    </script>

    <!-- Footer Sidebar: Status Driver & Logout -->
    <div class="sidebar-bottom">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding: 0 4px;">
            <div style="display: flex; align-items: center; gap: 7px;">
                <span class="live-dot" style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #0C7950; box-shadow: 0 0 6px rgba(12, 121, 80, 0.6);"></span>
                <span style="font-size: 11px; font-weight: 700; color: rgba(255, 255, 255, 0.85); text-transform: uppercase;">{{ $driver->statusLabel() }}</span>
            </div>
            <span style="font-size: 11px; color: rgba(255, 255, 255, 0.65); font-weight: 600;">{{ $driver->license_type ?? 'SIM B1' }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="menu-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                </div>
                <span>Keluar</span>
            </button>
        </form>
    </div>

</aside>
