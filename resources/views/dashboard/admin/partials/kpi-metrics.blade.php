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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                            <path d="M13 5v2"/>
                            <path d="M13 11v2"/>
                            <path d="M13 17v2"/>
                        </svg>
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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h13l4 4v7H3V6z"/>
                            <circle cx="7.5" cy="17.5" r="2"/>
                            <circle cx="16.5" cy="17.5" r="2"/>
                            <path d="M16 6v4h4"/>
                            <path d="M7 10h4"/>
                        </svg>
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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 19v2"/>
                            <path d="M17 19v2"/>
                            <path d="M5 8v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/>
                            <path d="M4 11h16"/>
                            <path d="M7 3h10a2 2 0 0 1 2 2v3H5V5a2 2 0 0 1 2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-bottom">
                    <span>Siap untuk dipesan</span>
                    <span class="metric-badge-status purple">Tersedia</span>
                </div>
            </div>

        </div>
    </div>
