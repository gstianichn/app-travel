{{-- =========================================
         MENU CEPAT MANAJEMEN OPERASIONAL
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Akses Modul Administrasi</h2>
                <p>Navigasi cepat ke seluruh fungsi manajemen TravelKu</p>
            </div>
        </div>

        <div class="quick-nav-grid" style="margin-top: 14px;">

            {{-- KELOLA USER --}}
            <a href="{{ route('dashboard.admin.kelola-user.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">👥</div>
                <div class="quick-nav-text">
                    <h4>Kelola User</h4>
                    <p>{{ number_format($totalUsers ?? 0) }} akun pengguna</p>
                </div>
            </a>

            {{-- KELOLA STAFF --}}
            <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🧑‍💼</div>
                <div class="quick-nav-text">
                    <h4>Kelola Staff</h4>
                    <p>Hak akses & tiket kasir</p>
                </div>
            </a>

            {{-- KELOLA DRIVER --}}
            <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🪪</div>
                <div class="quick-nav-text">
                    <h4>Kelola Driver</h4>
                    <p>{{ number_format($totalDrivers ?? 0) }} sopir terdaftar</p>
                </div>
            </a>

            {{-- MEMANTAU KENDARAAN --}}
            <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🚐</div>
                <div class="quick-nav-text">
                    <h4>Memantau Kendaraan</h4>
                    <p>{{ number_format($totalVehicles ?? 0) }} unit armada</p>
                </div>
            </a>

            {{-- PEMESANAN --}}
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">💺</div>
                <div class="quick-nav-text">
                    <h4>Pemesanan</h4>
                    <p>{{ number_format($totalBookings ?? 0) }} total tiket</p>
                </div>
            </a>

            {{-- PEMBAYARAN --}}
            <a href="{{ route('dashboard.admin.pembayaran.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">💳</div>
                <div class="quick-nav-text">
                    <h4>Pembayaran</h4>
                    <p>Verifikasi & arus kas</p>
                </div>
            </a>

            {{-- RUTE PERJALANAN --}}
            <a href="{{ route('dashboard.admin.rute.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">🕒</div>
                <div class="quick-nav-text">
                    <h4>Rute Perjalanan</h4>
                    <p>Jalur & tarif travel</p>
                </div>
            </a>

            {{-- KELOLA REFUND --}}
            <a href="#" class="quick-nav-item">
                <div class="quick-nav-icon">🔄</div>
                <div class="quick-nav-text">
                    <h4>Kelola Refund</h4>
                    <p>{{ $totalPendingRefunds ?? 0 }} perlu tindakan</p>
                </div>
            </a>

        </div>
    </div>
