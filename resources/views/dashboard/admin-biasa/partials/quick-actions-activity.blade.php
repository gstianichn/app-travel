<!-- 4. MENU AKSES CEPAT & AKTIVITAS -->

            <div class="dashboard-grid">

                <!-- AKSES CEPAT -->
                <div class="card">

                    <div class="card-header">
                        <h3>Akses Cepat Operasional</h3>
                    </div>

                    <div class="quick-menu">

                        <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}" class="quick-item">
                            <div class="quick-icon" style="background: #eefbf5; color: #087f55;">
                                📷
                            </div>
                            <strong>Validasi E-Ticket (Scan QR)</strong>
                            <span>Pindai barcode & verifikasi tiket penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="quick-item">
                            <div class="quick-icon">
                                🎫
                            </div>
                            <strong>Manajemen Booking</strong>
                            <span>Lihat dan kelola pesanan penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.perjalanan') }}" class="quick-item">
                            <div class="quick-icon">
                                🗺️
                            </div>
                            <strong>Informasi Perjalanan</strong>
                            <span>Cek jadwal rute, jam, dan pool transit</span>
                        </a>

                        <a href="javascript:void(0)" onclick="alert('Membuka Laporan Boarding Harian')" class="quick-item">
                            <div class="quick-icon">
                                📋
                            </div>
                            <strong>Laporan Harian</strong>
                            <span>Rekapitulasi boarding pass loket</span>
                        </a>

                    </div>

                </div>


                <!-- AKTIVITAS TERBARU -->
                <div class="card">

                    <div class="card-header">
                        <h3>Aktivitas Terkini</h3>
                        <a href="javascript:void(0)" onclick="alert('Memuat seluruh riwayat aktivitas...')">
                            Lihat semua
                        </a>
                    </div>

                    <div class="activity">
                        @forelse($recentActivities ?? [] as $act)
                            <div class="activity-item">
                                <div class="activity-dot" style="background: {{ $act->dot_color }}; box-shadow: 0 0 0 4px {{ $act->dot_bg }};"></div>
                                <div>
                                    <strong>{{ $act->title }}</strong>
                                    <span>{{ $act->desc }} • {{ $act->time_text }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="activity-dot" style="background: #087f55; box-shadow: 0 0 0 4px #eefbf5;"></div>
                                <div>
                                    <strong>Sistem Operasional Siap</strong>
                                    <span>Menunggu pemesanan tiket baru atau validasi boarding hari ini</span>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>
