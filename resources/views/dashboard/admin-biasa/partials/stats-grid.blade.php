<!-- 2. METRIK OPERASIONAL HARI INI -->

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Jadwal Aktif Hari Ini</span>
                        <h3>{{ $stats['active_schedules'] }} Trip</h3>
                    </div>
                    <div class="stat-icon blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="13" rx="2" />
                            <circle cx="7" cy="18" r="2" />
                            <circle cx="17" cy="18" r="2" />
                            <path d="M2 10h20" />
                            <path d="M7 5v5" />
                            <path d="M12 5v5" />
                            <path d="M17 5v5" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Boarding Dibuka</span>
                        <h3 style="color: #087f55;">{{ $stats['boarding_now'] }} Armada</h3>
                    </div>
                    <div class="stat-icon green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 20V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v14" />
                            <path d="M2 20h20" />
                            <path d="M14 12v.01" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Tahap Persiapan</span>
                        <h3 style="color: #b45309;">{{ $stats['ready_preparation'] }} Armada</h3>
                    </div>
                    <div class="stat-icon orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Pemesanan Hari Ini</span>
                        <h3>{{ $stats['today_bookings'] }} Tiket</h3>
                    </div>
                    <div class="stat-icon cyan">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                            <path d="M13 5v2" />
                            <path d="M13 17v2" />
                            <path d="M13 11v2" />
                        </svg>
                    </div>
                </div>

            </div>
