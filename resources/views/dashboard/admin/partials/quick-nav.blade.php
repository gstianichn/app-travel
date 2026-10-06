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
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Kelola User</h4>
                    <p>{{ number_format($totalUsers ?? 0) }} akun pengguna</p>
                </div>
            </a>

            {{-- KELOLA STAFF --}}
            <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                        <path d="m14 11-2 4-2-4"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Kelola Staff</h4>
                    <p>Hak akses & tiket kasir</p>
                </div>
            </a>

            {{-- KELOLA DRIVER --}}
            <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <circle cx="9" cy="10" r="2"/>
                        <path d="M15 8h2"/>
                        <path d="M15 12h2"/>
                        <path d="M7 16h10"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Kelola Driver</h4>
                    <p>{{ number_format($totalDrivers ?? 0) }} sopir terdaftar</p>
                </div>
            </a>

            {{-- MEMANTAU KENDARAAN --}}
            <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h13l4 4v7H3V6z"/>
                        <circle cx="7.5" cy="17.5" r="2"/>
                        <circle cx="16.5" cy="17.5" r="2"/>
                        <path d="M16 6v4h4"/>
                        <path d="M7 10h4"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Memantau Kendaraan</h4>
                    <p>{{ number_format($totalVehicles ?? 0) }} unit armada</p>
                </div>
            </a>

            {{-- PEMESANAN --}}
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                        <path d="M13 5v2"/>
                        <path d="M13 11v2"/>
                        <path d="M13 17v2"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Pemesanan</h4>
                    <p>{{ number_format($totalBookings ?? 0) }} total tiket</p>
                </div>
            </a>

            {{-- PEMBAYARAN --}}
            <a href="{{ route('dashboard.admin.pembayaran.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Pembayaran</h4>
                    <p>Verifikasi & arus kas</p>
                </div>
            </a>

            {{-- RUTE PERJALANAN --}}
            <a href="{{ route('dashboard.admin.rute.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="6" cy="19" r="3"/>
                        <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-7a3.5 3.5 0 0 1 0-7H18"/>
                        <circle cx="18" cy="5" r="3"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Rute Perjalanan</h4>
                    <p>Jalur & tarif travel</p>
                </div>
            </a>

            {{-- KELOLA REFUND --}}
            <a href="{{ route('dashboard.admin.refund.index') }}" class="quick-nav-item">
                <div class="quick-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                </div>
                <div class="quick-nav-text">
                    <h4>Kelola Refund</h4>
                    <p>{{ $totalPendingRefunds ?? 0 }} perlu tindakan</p>
                </div>
            </a>

        </div>
    </div>
