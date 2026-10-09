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
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                    <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                    <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                    <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                    <rect x="7" y="7" width="10" height="10" rx="1" />
                                </svg>
                            </div>
                            <strong>Validasi E-Ticket (Scan QR)</strong>
                            <span>Pindai barcode & verifikasi tiket penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="quick-item">
                            <div class="quick-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                                    <path d="M13 5v2" />
                                    <path d="M13 17v2" />
                                    <path d="M13 11v2" />
                                </svg>
                            </div>
                            <strong>Manajemen Booking</strong>
                            <span>Lihat dan kelola pesanan penumpang</span>
                        </a>

                        <a href="{{ route('dashboard.admin-biasa.perjalanan') }}" class="quick-item">
                            <div class="quick-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                                    <line x1="9" x2="9" y1="3" y2="18" />
                                    <line x1="15" x2="15" y1="6" y2="21" />
                                </svg>
                            </div>
                            <strong>Informasi Perjalanan</strong>
                            <span>Cek jadwal rute, jam, dan pool transit</span>
                        </a>

                        <a href="javascript:void(0)" onclick="alert('Membuka Laporan Boarding Harian')" class="quick-item">
                            <div class="quick-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                                    <path d="M9 12h6" />
                                    <path d="M9 16h6" />
                                </svg>
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
