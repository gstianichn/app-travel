        <!-- ================= 1. FITUR DASHBOARD (KARTU KENDALI UTAMA OPERASIONAL) ================= -->
        <section class="cockpit-master-card" id="cockpit-dashboard">
            <!-- Header Kartu Kendali Utama -->
            <div class="cockpit-hero-header">
                <div class="cockpit-title-group">
                    <div class="cockpit-tag-pill">
                        <span>⚡</span>
                        <span>1. Dashboard — Kartu Kendali Utama Operasional</span>
                    </div>
                    <h2>
                        <span>🎛️</span>
                        <span>Ringkasan Operasional Harian Berjalan</span>
                    </h2>
                    <p>Pusat kendali harian pengemudi untuk mengontrol tugas aktif, muatan kursi, navigasi, dan keamanan lapangan.</p>
                </div>

                <div class="cockpit-status-chips">
                    <div class="trip-status-badge {{ $activeSchedule->tripStatusBadgeClass() }}">
                        <span>Status Trip:</span>
                        <strong>{{ $activeSchedule->tripStatusLabel() }}</strong>
                    </div>

                    <div style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.25); border-radius:10px; padding:6px 12px; font-size:12px; font-weight:700; color:white; display:flex; align-items:center; gap:6px;">
                        <span>🚐</span>
                        <span>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</span>
                    </div>
                </div>
            </div>

            <!-- Grid Konten Inti Kartu Kendali Utama -->
            <div class="cockpit-body-grid">
                <!-- Kolom Kiri: 1. Status Tugas Hari Ini & 2. Indikator Cepat Ketersediaan Kursi -->
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <!-- 1. KARTU STATUS TUGAS HARI INI -->
                    <div class="cockpit-subcard cockpit-task-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title">
                                <span>🎫</span>
                                <span>Status Tugas Hari Ini</span>
                            </div>
                            <span class="badge-pill" style="background:#ecfdf5; color:#065f46; border-color:#a7f3d0; font-size:10.5px; font-weight:800;">
                                Jadwal Aktif
                            </span>
                        </div>

                        <!-- Nomor Trip & Tipe Layanan -->
                        <div class="cockpit-trip-row">
                            <div class="cockpit-trip-code-box">
                                <span style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Nomor Trip:</span>
                                <span class="cockpit-trip-code">{{ $activeSchedule->tripCode() }}</span>
                            </div>
                            <span style="font-size:11.5px; font-weight:700; color:#065f46;">
                                Shuttle Reguler Eksekutif
                            </span>
                        </div>

                        <!-- Rute Asal - Tujuan -->
                        <div class="cockpit-route-display">
                            <div style="font-size:10.5px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                                Rute Asal – Tujuan
                            </div>
                            <div class="cockpit-route-path">
                                <span>📍 {{ $activeSchedule->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}</span>
                                <span class="cockpit-route-arrow">➔</span>
                                <span>🏁 {{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}</span>
                            </div>
                            <div class="cockpit-route-meta">
                                <span>🛣️ Jarak: <strong>{{ $activeSchedule->route?->distance_km ?? 150 }} km</strong></span>
                                <span>⏱️ Durasi: <strong>± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }}</strong></span>
                                <span>🏁 Estimasi Tiba: <strong>{{ $activeSchedule->estimatedArrivalTime() }} WIB</strong></span>
                            </div>
                        </div>

                        <!-- Jam Keberangkatan & Tanggal -->
                        <div class="cockpit-task-meta-grid">
                            <div class="cockpit-meta-box">
                                <span class="meta-label">⏰ Jam Keberangkatan</span>
                                <div class="meta-val" style="color:#065f46;">{{ $activeSchedule->formattedDepartureTime() }} WIB</div>
                            </div>
                            <div class="cockpit-meta-box">
                                <span class="meta-label">🗓️ Tanggal Perjalanan</span>
                                <div class="meta-val">{{ $activeSchedule->formattedDepartureDate() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. INDIKATOR CEPAT KETERSEDIAAN KURSI -->
                    <div class="cockpit-subcard cockpit-seat-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title">
                                <span>💺</span>
                                <span>Indikator Cepat Ketersediaan Kursi</span>
                            </div>
                            <a href="#section-seatmap" style="font-size:11.5px; font-weight:700; color:#0284c7; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                <span>Lihat Denah Kursi</span>
                                <span>➔</span>
                            </a>
                        </div>

                        <!-- Ringkasan Cepat: Contoh 8/10 Penumpang Terisi -->
                        <div class="cockpit-seat-headline">
                            <div class="cockpit-seat-counter">
                                <span>👥</span>
                                <strong>{{ $totalBookedPassengers }}/{{ $seatCapacity }}</strong>
                                <span>Penumpang Terisi</span>
                            </div>
                            <span class="cockpit-seat-pct-badge">
                                {{ $occupancyPct }}% Terisi
                            </span>
                        </div>

                        <!-- Progress Bar Visual Keterisian Kursi -->
                        <div>
                            <div style="display:flex; justify-content:space-between; font-size:11px; font-weight:700; color:var(--text-muted); margin-bottom:4px;">
                                <span>Kapasitas Kabin Total</span>
                                <span>{{ $emptySeats }} Kursi Kosong Tersedia</span>
                            </div>
                            <div class="cockpit-seat-progress-track">
                                <div class="cockpit-seat-progress-fill" style="width: {{ min(100, $occupancyPct) }}%;"></div>
                            </div>
                        </div>

                        <!-- 3 Detail Pill Status Penumpang -->
                        <div class="cockpit-seat-pills-row">
                            <div class="cockpit-seat-pill" style="background:#f0fdf4; border-color:#bbf7d0;">
                                <span style="color:#15803d;">Sudah Naik</span>
                                <strong style="color:#15803d;">{{ $boardedCount }} Penumpang</strong>
                            </div>
                            <div class="cockpit-seat-pill" style="background:#fef2f2; border-color:#fecaca;">
                                <span style="color:#991b1b;">Belum Naik</span>
                                <strong style="color:#991b1b;">{{ $pendingBoardingCount }} Penumpang</strong>
                            </div>
                            <div class="cockpit-seat-pill" style="background:#eff6ff; border-color:#bfdbfe;">
                                <span style="color:#1e40af;">Kursi Kosong</span>
                                <strong style="color:#1e40af;">{{ $emptySeats }} Kursi</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: 3. Pintasan Aksi Berikutnya & 4. Tombol Pintas Cepat -->
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <!-- 3. PINTASAN AKSI BERIKUTNYA (NEXT ACTION CARD) -->
                    <div class="cockpit-subcard cockpit-next-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title" style="color:#065f46;">
                                <span>⚡</span>
                                <span>Pintasan Aksi Berikutnya (Next Action Card)</span>
                            </div>
                            <span class="pill-badge" style="background:#ffffff; border-color:#86efac; color:#15803d; font-size:11px; font-weight:800;">
                                {{ $nextSeatNumber }}
                            </span>
                        </div>

                        <!-- Target Tugas Berikutnya -->
                        <div class="cockpit-next-target-box">
                            <div style="font-size:11px; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:3px;">
                                {{ $nextTargetType }}
                            </div>
                            <div class="cockpit-next-target-name">
                                <span>👤</span>
                                <span>{{ $nextTargetTitle }}</span>
                                @if ($unboardedPassenger && !$unboardedPassenger->is_paid)
                                    <span class="badge-cod" style="font-size:10.5px;">COD Rp {{ number_format($unboardedPassenger->amount_due, 0, ',', '.') }}</span>
                                @elseif ($unboardedPassenger && $unboardedPassenger->is_paid)
                                    <span class="badge-paid" style="font-size:10.5px;">Tiket Lunas</span>
                                @endif
                            </div>
                            <div class="cockpit-next-address">
                                📍 {{ $nextTargetAddress }}
                            </div>
                            <div class="cockpit-next-notes">
                                <span>🏡</span>
                                <span><strong>Patokan / Catatan:</strong> {{ $nextTargetNotes }}</span>
                            </div>
                        </div>

                        <!-- Tombol Buka Navigasi GPS: Google Maps & Waze -->
                        <div>
                            <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                                Tombol Buka Navigasi GPS 1-Klik
                            </div>
                            <div class="cockpit-gps-nav-row">
                                <a href="{{ $nextGmapsUrl }}" target="_blank" class="btn-cockpit-gps gmaps" title="Buka navigasi GPS di Google Maps">
                                    <span>🗺️</span>
                                    <span>Google Maps</span>
                                </a>
                                <a href="{{ $nextWazeUrl }}" target="_blank" class="btn-cockpit-gps waze" title="Buka navigasi GPS di Waze">
                                    <span>🚙</span>
                                    <span>Waze</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tombol Konfirmasi Aksi Cepat -->
                        <div>
                            @if ($nextAction)
                                <form method="POST" action="{{ route('dashboard.driver.schedule.status', $activeSchedule->id ?? 1) }}" onsubmit="return confirm('{{ $nextAction['prompt'] }}');">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $nextAction['next_key'] }}">
                                    <button type="submit" class="btn-main-status-trigger" style="margin-top:0;">
                                        <span>{{ $nextAction['icon'] }}</span>
                                        <span>{{ $nextAction['label'] }}</span>
                                    </button>
                                </form>
                            @elseif ($unboardedPassenger && $unboardedPassenger->passenger_model)
                                <form method="POST" action="{{ route('dashboard.driver.passenger.checkin', $unboardedPassenger->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="boarding_status" value="boarded">
                                    @if (!$unboardedPassenger->is_paid)
                                        <input type="hidden" name="mark_paid" value="1">
                                    @endif
                                    <button type="submit" class="btn-main-status-trigger" style="margin-top:0;">
                                        <span>✓</span>
                                        <span>Konfirmasi Penumpang Naik ({{ $unboardedPassenger->name }})</span>
                                    </button>
                                </form>
                            @else
                                <div class="pill-badge" style="background:#ecfdf5; color:#065f46; font-weight:700; width:100%; justify-content:center; padding:10px;">
                                    ✓ Semua Aksi Tahapan Perjalanan Telah Selesai
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 4. TOMBOL PINTAS CEPAT (QUICK SHORTCUTS: LAPOR MASALAH & EMERGENCY SOS) -->
                    <div class="cockpit-subcard cockpit-shortcuts-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title" style="color:#b45309;">
                                <span>⚡</span>
                                <span>Tombol Pintas Cepat (Quick Shortcuts)</span>
                            </div>
                            <span class="badge-pill" style="background:#fef3c7; color:#92400e; border-color:#fcd34d; font-size:10.5px; font-weight:800;">
                                Bantuan Lapangan
                            </span>
                        </div>

                        <!-- 2 Tombol Utama: Lapor Masalah & Emergency SOS -->
                        <div class="cockpit-quick-btn-grid">
                            <!-- Tombol Lapor Masalah -->
                            <button type="button" class="btn-cockpit-shortcut warning" onclick="openIncidentModal()" title="Laporkan kendala ban bocor, macet parah, mogok, dll.">
                                <div class="btn-cockpit-shortcut-icon">
                                    <span>⚠️</span>
                                </div>
                                <div class="btn-cockpit-shortcut-text">
                                    <strong>Lapor Masalah</strong>
                                    <span>Kendala Jalan & Armada</span>
                                </div>
                            </button>

                            <!-- Tombol Emergency SOS -->
                            <button type="button" class="btn-cockpit-shortcut danger" onclick="openSosModal()" title="Pancarkan sinyal darurat SOS instan ke pusat kendali">
                                <div class="btn-cockpit-shortcut-icon">
                                    <span>🚨</span>
                                </div>
                                <div class="btn-cockpit-shortcut-text">
                                    <strong>Emergency SOS</strong>
                                    <span>Pusat Darurat 24 Jam</span>
                                </div>
                            </button>
                        </div>

                        <!-- Tombol Bantuan Tambahan -->
                        <div class="cockpit-aux-row">
                            <a href="tel:081122334455" class="btn-cockpit-aux" title="Telepon Dispatcher Pool">
                                <span>📞</span>
                                <span>Hubungi Dispatcher</span>
                            </a>
                            <a href="#section-manifest" class="btn-cockpit-aux" title="Buka daftar manifes penumpang">
                                <span>📋</span>
                                <span>Buka Manifes Kabin</span>
                            </a>
                            <a href="#section-rute" class="btn-cockpit-aux" title="Lihat detail rute dan rest area">
                                <span>🗺️</span>
                                <span>Rute & Rest Area</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

