<!-- ========================================
     MENU CEPAT CUSTOMER SERVICE
========================================= -->

<div class="card">

    <div class="card-header">
        <div class="card-header-left">
            <span class="card-icon-bullet"></span>
            <h2 class="card-title">Menu Cepat</h2>
        </div>
        <a href="{{ route('dashboard.customer-service.live-chat') }}" class="card-link">
            Buka Live Chat &rarr;
        </a>
    </div>

    <div class="card-body">
        <div class="quick-menu">

            <!-- 1. Layanan Pelanggan (Live Chat) -->
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="quick-item quick-item-featured">
                <div class="quick-icon icon-teal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <div class="quick-text">
                    <div class="quick-header-row">
                        <span class="quick-title">Layanan Pelanggan</span>
                        <span class="quick-pill-active">Aktif</span>
                    </div>
                    <span class="quick-desc">Tangani pertanyaan pelanggan & sesi live chat</span>
                </div>
            </a>

            <!-- 2. Pemesanan (Lookup Booking) -->
            <a href="#quick-lookup" class="quick-item" onclick="focusQuickSearch(event)">
                <div class="quick-icon icon-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="quick-text">
                    <span class="quick-title">Pemesanan</span>
                    <span class="quick-desc">Cek dan kelola pemesanan, tiket & status kursi</span>
                </div>
            </a>

            <!-- 3. Data Pelanggan -->
            <a href="#data-pelanggan" class="quick-item" onclick="scrollToSection('data-pelanggan', event)">
                <div class="quick-icon icon-blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="quick-text">
                    <span class="quick-title">Data Pelanggan</span>
                    <span class="quick-desc">Lihat informasi kontak & riwayat tiket penumpang</span>
                </div>
            </a>

            <!-- 4. Jadwal Travel -->
            <a href="#jadwal-travel" class="quick-item" onclick="scrollToSection('jadwal-travel', event)">
                <div class="quick-icon icon-purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="quick-text">
                    <span class="quick-title">Jadwal Travel</span>
                    <span class="quick-desc">Lihat jadwal perjalanan, rute & ketersediaan armada</span>
                </div>
            </a>

        </div>
    </div>

</div>
