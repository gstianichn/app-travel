{{-- =========================================
         1. METRIK UTAMA (SUMMARY CARDS)
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Metrik Utama Hari Ini</h2>
                <p>Ringkasan penjualan tiket, arus kas pendapatan, armada jalan, dan kursi kosong</p>
            </div>
        </div>

        <div class="metrics-grid" style="margin-top: 14px;">

            {{-- 1. TOTAL TIKET TERJUAL HARI INI --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Tiket Terjual Hari Ini</div>
                        <div class="metric-value">{{ number_format($ticketsSoldToday ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box tickets">
                        🎟️
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Tiket penumpang terbit</span>
                    <span class="metric-badge-status green">Hari Ini</span>
                </div>
            </div>

            {{-- 2. PENDAPATAN HARI INI --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Pendapatan Hari Ini</div>
                        <div class="metric-value">Rp {{ number_format($revenueToday ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="metric-icon-box revenue">
                        💰
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Transaksi terbayar</span>
                    <span class="metric-badge-status blue">Terverifikasi</span>
                </div>
            </div>

            {{-- 3. JUMLAH ARMADA SEDANG JALAN --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Armada Sedang Jalan</div>
                        <div class="metric-value">{{ number_format($activeFleetCount ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box fleet">
                        🚐
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Minibus & Van di jalan</span>
                    <span class="metric-badge-status orange">Aktif</span>
                </div>
            </div>

            {{-- 4. TOTAL KURSI KOSONG --}}
            <div class="metric-card">
                <div class="metric-card-top">
                    <div>
                        <div class="metric-label">Total Kursi Kosong</div>
                        <div class="metric-value">{{ number_format($emptySeatsToday ?? 0) }}</div>
                    </div>
                    <div class="metric-icon-box seats">
                        💺
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Siap untuk dipesan</span>
                    <span class="metric-badge-status purple">Tersedia</span>
                </div>
            </div>

        </div>
    </div>
