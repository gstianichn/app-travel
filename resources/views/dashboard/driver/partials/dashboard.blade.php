        <!-- ================= 1. FITUR DASHBOARD (KARTU KENDALI UTAMA OPERASIONAL) ================= -->
        <section class="cockpit-master-card" id="cockpit-dashboard">
            <!-- Header Kartu Kendali Utama -->
            <div class="cockpit-hero-header">
                <div class="cockpit-title-group">
                    <div class="cockpit-tag-pill" style="display:inline-flex; align-items:center; gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
                        <span>1. Dashboard — Kartu Kendali Utama Operasional</span>
                    </div>
                    <h2 style="display:flex; align-items:center; gap:8px;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="3" stroke-width="2"/><circle cx="7" cy="18" r="2" fill="currentColor"/><circle cx="17" cy="18" r="2" fill="currentColor"/><path stroke-linecap="round" stroke-width="2" d="M2 11h20M14 6v5M8 6v5"/></svg>
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
                            <div class="cockpit-subcard-title" style="display:flex; align-items:center; gap:6px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
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
                            <div class="cockpit-route-path" style="display:flex; align-items:center; gap:8px;">
                                <span style="display:inline-flex; align-items:center; gap:4px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3" stroke-width="2"/></svg>
                                    {{ $activeSchedule->route?->origin?->name ?? 'Bandung (Dipatiukur)' }}
                                </span>
                                <span class="cockpit-route-arrow"></span>
                                <span style="display:inline-flex; align-items:center; gap:4px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                    {{ $activeSchedule->route?->destination?->name ?? 'Jakarta (Semanggi)' }}
                                </span>
                            </div>
                            <div class="cockpit-route-meta" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                <span style="display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg> Jarak: <strong>{{ $activeSchedule->route?->distance_km ?? 150 }} km</strong></span>
                                <span style="display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg> Durasi: <strong>± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }}</strong></span>
                                <span style="display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"/></svg> Estimasi Tiba: <strong>{{ $activeSchedule->estimatedArrivalTime() }} WIB</strong></span>
                            </div>
                        </div>

                        <!-- Jam Keberangkatan & Tanggal -->
                        <div class="cockpit-task-meta-grid">
                            <div class="cockpit-meta-box">
                                <span class="meta-label" style="display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg> Jam Keberangkatan</span>
                                <div class="meta-val" style="color:#065f46;">{{ $activeSchedule->formattedDepartureTime() }}</div>
                            </div>
                            <div class="cockpit-meta-box">
                                <span class="meta-label" style="display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/></svg> Tanggal Perjalanan</span>
                                <div class="meta-val">{{ $activeSchedule->formattedDepartureDate() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. INDIKATOR CEPAT KETERSEDIAAN KURSI -->
                    <div class="cockpit-subcard cockpit-seat-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title" style="display:flex; align-items:center; gap:6px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h14a1 1 0 011 1v9H4V5a1 1 0 011-1zM4 14v4a2 2 0 002 2h12a2 2 0 002-2v-4M7 20v2M17 20v2"/></svg>
                                <span>Indikator Cepat Ketersediaan Kursi</span>
                            </div>
                            <a href="#section-seatmap" style="font-size:11.5px; font-weight:700; color:#0284c7; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                <span>Lihat Denah Kursi</span>
                                <span></span>
                            </a>
                        </div>

                        <!-- Ringkasan Cepat: Contoh 8/10 Penumpang Terisi -->
                        <div class="cockpit-seat-headline">
                            <div class="cockpit-seat-counter" style="display:flex; align-items:center; gap:6px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
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
                            <div class="cockpit-subcard-title" style="color:#065f46; display:flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
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
                            <div class="cockpit-next-target-name" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>{{ $nextTargetTitle }}</span>
                                @if ($unboardedPassenger && !$unboardedPassenger->is_paid)
                                    <span class="badge-cod" style="font-size:10.5px;">COD Rp {{ number_format($unboardedPassenger->amount_due, 0, ',', '.') }}</span>
                                @elseif ($unboardedPassenger && $unboardedPassenger->is_paid)
                                    <span class="badge-paid" style="font-size:10.5px;">Tiket Lunas</span>
                                @endif
                            </div>
                            <div class="cockpit-next-address" style="display:flex; align-items:center; gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3" stroke-width="2"/></svg>
                                <span>{{ $nextTargetAddress }}</span>
                            </div>
                            <div class="cockpit-next-notes" style="display:flex; align-items:flex-start; gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span><strong>Patokan / Catatan:</strong> {{ $nextTargetNotes }}</span>
                            </div>
                        </div>

                        <!-- Tombol Buka Navigasi GPS: Google Maps & Waze -->
                        <div>
                            <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                                Tombol Buka Navigasi GPS 1-Klik
                            </div>
                            <div class="cockpit-gps-nav-row">
                                <a href="{{ $nextGmapsUrl }}" target="_blank" class="btn-cockpit-gps gmaps" title="Buka navigasi GPS di Google Maps" style="display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="18" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke-width="2"/></svg>
                                    <span>Google Maps</span>
                                </a>
                                <a href="{{ $nextWazeUrl }}" target="_blank" class="btn-cockpit-gps waze" title="Buka navigasi GPS di Waze" style="display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/></svg>
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
                                    <button type="submit" class="btn-main-status-trigger" style="margin-top:0; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Konfirmasi Penumpang Naik ({{ $unboardedPassenger->name }})</span>
                                    </button>
                                </form>
                            @else
                                <div class="pill-badge" style="background:#ecfdf5; color:#065f46; font-weight:700; width:100%; justify-content:center; padding:10px; display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Semua Aksi Tahapan Perjalanan Telah Selesai</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 4. TOMBOL PINTAS CEPAT (QUICK SHORTCUTS: LAPOR MASALAH & EMERGENCY SOS) -->
                    <div class="cockpit-subcard cockpit-shortcuts-card">
                        <div class="cockpit-subcard-header">
                            <div class="cockpit-subcard-title" style="color:#b45309; display:flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
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
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="btn-cockpit-shortcut-text">
                                    <strong>Lapor Masalah</strong>
                                    <span>Kendala Jalan & Armada</span>
                                </div>
                            </button>

                            <!-- Tombol Emergency SOS -->
                            <button type="button" class="btn-cockpit-shortcut danger" onclick="openSosModal()" title="Pancarkan sinyal darurat SOS instan ke pusat kendali">
                                <div class="btn-cockpit-shortcut-icon">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01"/></svg>
                                </div>
                                <div class="btn-cockpit-shortcut-text">
                                    <strong>Emergency SOS</strong>
                                    <span>Pusat Darurat 24 Jam</span>
                                </div>
                            </button>
                        </div>

                        <!-- Tombol Bantuan Tambahan -->
                        <div class="cockpit-aux-row">
                            @php $officePhone = \App\Models\AppSetting::companyPhone(); @endphp
                @if ($officePhone)
                    <a href="{{ $officePhone['tel'] }}" class="btn-cockpit-aux" style="display:inline-flex; align-items:center; gap:8px;">
                        <span>Telepon kantor ({{ $officePhone['display'] }})</span>
                    </a>
                @endif
                            <a href="#section-manifest" class="btn-cockpit-aux" title="Buka daftar manifes penumpang" style="display:inline-flex; align-items:center; gap:6px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Buka Manifes Kabin</span>
                            </a>
                            <a href="#section-rute" class="btn-cockpit-aux" title="Lihat detail rute dan rest area" style="display:inline-flex; align-items:center; gap:6px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="18" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke-width="2"/></svg>
                                <span>Rute & Rest Area</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

