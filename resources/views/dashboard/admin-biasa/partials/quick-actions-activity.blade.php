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

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #087f55; box-shadow: 0 0 0 4px #eefbf5;"></div>
                            <div>
                                <strong>Boarding Pass Divalidasi (Kursi 03A)</strong>
                                <span>Rute Bandung ➔ Jakarta (Semanggi) • 2 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <strong>Status Armada D 7812 AB: Boarding Dibuka</strong>
                                <span>Pemberitahuan gate keberangkatan • 10 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #b45309; box-shadow: 0 0 0 4px #fff8eb;"></div>
                            <div>
                                <strong>Armada B 1945 TKL Memasuki Tahap Persiapan</strong>
                                <span>Pengecekan unit HiAce dan supir standby • 35 menit yang lalu</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-dot" style="background: #64748b; box-shadow: 0 0 0 4px #f1f5f9;"></div>
                            <div>
                                <strong>Trip D 7551 GH Telah Diberangkatkan Tepat Waktu</strong>
                                <span>10/10 Penumpang boarding penuh • 55 menit yang lalu</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
