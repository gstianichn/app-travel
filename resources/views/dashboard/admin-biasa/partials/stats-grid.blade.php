<!-- 2. METRIK OPERASIONAL HARI INI -->

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Jadwal Aktif Hari Ini</span>
                        <h3>{{ $stats['active_schedules'] }} Trip</h3>
                    </div>
                    <div class="stat-icon blue">
                        🚐
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Boarding Dibuka</span>
                        <h3 style="color: #087f55;">{{ $stats['boarding_now'] }} Armada</h3>
                    </div>
                    <div class="stat-icon green">
                        🟢
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Tahap Persiapan</span>
                        <h3 style="color: #b45309;">{{ $stats['ready_preparation'] }} Armada</h3>
                    </div>
                    <div class="stat-icon orange">
                        ⏳
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <span>Pemesanan Hari Ini</span>
                        <h3>{{ $stats['today_bookings'] }} Tiket</h3>
                    </div>
                    <div class="stat-icon cyan">
                        🎫
                    </div>
                </div>

            </div>
