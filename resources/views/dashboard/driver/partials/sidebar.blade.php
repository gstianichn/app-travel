    <!-- ================= SIDEBAR OPERASIONAL ================= -->
    <aside class="sidebar" id="sidebar">

        <div class="brand-zone">
            <div class="brand-logo-icon">
                🚐
            </div>
            <div class="brand-text">
                <h2>TRAVEL MINIBUS</h2>
                <span>Cockpit Driver v2.0</span>
            </div>
        </div>

        <div class="driver-mini-card">
            <div class="avatar-box">
                {{ strtoupper(substr($user->name ?? 'DR', 0, 2)) }}
            </div>
            <div class="driver-details">
                <h4>{{ $user->name ?? 'Pengemudi' }}</h4>
                <p>{{ $driver->license_type ?? 'SIM B1 Umum' }}</p>
                <div class="live-status-pill">
                    <span class="dot"></span>
                    <span>{{ strtoupper($driver->statusLabel()) }}</span>
                </div>
            </div>
        </div>

        <nav class="nav-menu">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard.driver.index') }}" class="nav-link {{ request()->routeIs('dashboard.driver.index') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <div class="nav-label">Jadwal & Armada</div>

            <!-- 2. Melihat jadwal perjalanan -->
            <a href="{{ route('dashboard.driver.jadwal') }}" class="nav-link {{ request()->routeIs('dashboard.driver.jadwal') ? 'active' : '' }}">
                <span class="nav-icon">📅</span>
                <span>Melihat Jadwal Perjalanan</span>
            </a>

            <!-- 3. Melihat kendaraan yang ditugaskan -->
            <a href="{{ route('dashboard.driver.kendaraan') }}" class="nav-link {{ request()->routeIs('dashboard.driver.kendaraan') ? 'active' : '' }}">
                <span class="nav-icon">🚐</span>
                <span>Melihat Kendaraan Ditugaskan</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Penumpang & Kabin</div>

            <!-- 4. Melihat daftar penumpang -->
            <a href="{{ route('dashboard.driver.penumpang') }}" class="nav-link {{ request()->routeIs('dashboard.driver.penumpang') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span>Melihat Daftar Penumpang</span>
                <span class="menu-badge-count">{{ $totalBookedPassengers ?? 0 }}</span>
            </a>

            <!-- 5. Melihat seat map (cukup sebagai menu pelengkap) -->
            <a href="{{ route('dashboard.driver.seatmap') }}" class="nav-link {{ request()->routeIs('dashboard.driver.seatmap') ? 'active' : '' }}">
                <span class="nav-icon">💺</span>
                <span>Melihat Seat Map</span>
                <span class="menu-badge-pill">Pelengkap</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Rute & Status Perjalanan</div>

            <!-- 6. Melihat rute perjalanan -->
            <a href="{{ route('dashboard.driver.rute') }}" class="nav-link {{ request()->routeIs('dashboard.driver.rute') ? 'active' : '' }}">
                <span class="nav-icon">🗺️</span>
                <span>Melihat Rute Perjalanan</span>
            </a>

            <!-- 7. Melihat status perjalanan -->
            <a href="{{ route('dashboard.driver.status') }}" class="nav-link {{ request()->routeIs('dashboard.driver.status') ? 'active' : '' }}">
                <span class="nav-icon">🧭</span>
                <span>Melihat Status Perjalanan</span>
            </a>

            <!-- 8. Mengubah status perjalanan -->
            <a href="{{ route('dashboard.driver.ubah-status') }}" class="nav-link {{ request()->routeIs('dashboard.driver.ubah-status') ? 'active' : '' }}">
                <span class="nav-icon">⚡</span>
                <span>Mengubah Status Perjalanan</span>
            </a>

            <!-- 9. Melihat informasi penumpang -->
            <a href="{{ route('dashboard.driver.info-penumpang') }}" class="nav-link {{ request()->routeIs('dashboard.driver.info-penumpang') ? 'active' : '' }}">
                <span class="nav-icon">👤</span>
                <span>Melihat Informasi Penumpang</span>
            </a>

            <div class="nav-label" style="margin-top: 10px;">Bantuan, Pelacakan & Arsip</div>

            <!-- 10. Melaporkan masalah perjalanan -->
            <a href="{{ route('dashboard.driver.lapor-masalah') }}" class="nav-link {{ request()->routeIs('dashboard.driver.lapor-masalah') ? 'active' : '' }}">
                <span class="nav-icon">⚠️</span>
                <span>Melaporkan Masalah Perjalanan</span>
            </a>

            <!-- 11. Emergency Center -->
            <a href="{{ route('dashboard.driver.emergency') }}" class="nav-link nav-link-emergency {{ request()->routeIs('dashboard.driver.emergency') ? 'active' : '' }}">
                <span class="nav-icon">🚨</span>
                <span>Emergency Center</span>
                <span class="menu-badge-sos">SOS</span>
            </a>

            <!-- 12. GPS Tracking saat perjalanan aktif -->
            <a href="{{ route('dashboard.driver.gps') }}" class="nav-link {{ request()->routeIs('dashboard.driver.gps') ? 'active' : '' }}">
                <span class="nav-icon">📍</span>
                <span>GPS Tracking Saat Perjalanan Aktif</span>
                <span class="menu-badge-live">LIVE</span>
            </a>

            <!-- 13. Melihat performa -->
            <a href="{{ route('dashboard.driver.performa') }}" class="nav-link {{ request()->routeIs('dashboard.driver.performa') ? 'active' : '' }}">
                <span class="nav-icon">⭐</span>
                <span>Melihat Performa</span>
            </a>

            <!-- 14. Melihat reward -->
            <a href="{{ route('dashboard.driver.reward') }}" class="nav-link {{ request()->routeIs('dashboard.driver.reward') ? 'active' : '' }}">
                <span class="nav-icon">🎁</span>
                <span>Melihat Reward</span>
            </a>

            <!-- 15. Melihat riwayat perjalanan (cukup di tab riwayat/arsip) -->
            <a href="{{ route('dashboard.driver.riwayat') }}" class="nav-link {{ request()->routeIs('dashboard.driver.riwayat') ? 'active' : '' }}">
                <span class="nav-icon">🕘</span>
                <span>Melihat Riwayat Perjalanan</span>
                <span class="menu-badge-pill">Arsip</span>
            </a>
        </nav>

        <div class="sidebar-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <span>↪</span>
                    <span>Keluar / Ganti Akun</span>
                </button>
            </form>
        </div>

    </aside>
