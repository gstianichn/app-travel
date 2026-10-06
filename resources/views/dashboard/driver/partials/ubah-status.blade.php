        <!-- ================= 3. KARTU AKSI UTAMA (NEXT ACTION CARD) ================= -->
        <section class="next-action-card" id="section-ubah-status">
            <div class="next-action-header">
                <div class="next-action-tag" style="display:flex; align-items:center; gap:6px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
                    <span>3. Kartu Aksi Utama (Next Action Card)</span>
                    <span style="background:#059669; color:white; font-size:10px; padding:2px 8px; border-radius:12px; margin-left:6px; font-weight:800;">
                        Aksi Cepat Driver Saat Bergerak
                    </span>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="pill-badge" style="background:#ffffff; border-color:#86efac; color:#15803d; font-size:11px; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h14a1 1 0 011 1v9H4V5a1 1 0 011-1zM4 14v4a2 2 0 002 2h12a2 2 0 002-2v-4M7 20v2M17 20v2"/></svg>
                        {{ $nextSeatNumber }}
                    </span>
                    <span class="pill-badge" style="background:#f0fdf4; border-color:#86efac; color:#065f46; font-size:11px; font-weight:700;">
                        {{ $nextTargetType }}
                    </span>
                </div>
            </div>

            <div class="next-action-body">
                <!-- Titik Tugas Berikutnya -->
                <div class="next-target-details">
                    <span style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">
                        Titik Tugas Berikutnya
                    </span>
                    <h3 style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $nextTargetTitle }}</span>
                        @if ($unboardedPassenger && !$unboardedPassenger->is_paid)
                            <span class="badge-cod" style="font-size:11px;">Tagihan Tunai COD: Rp {{ number_format($unboardedPassenger->amount_due, 0, ',', '.') }}</span>
                        @elseif ($unboardedPassenger && $unboardedPassenger->is_paid)
                            <span class="badge-paid" style="font-size:11px;">Tiket Lunas</span>
                        @endif
                    </h3>

                    <div class="next-address-box">
                        <div style="font-weight:700; color:var(--brand-dark); display:flex; align-items:center; gap:6px;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3" stroke-width="2"/></svg>
                            <span>Alamat Lengkap Titik Jemput / Turun:</span>
                        </div>
                        <div style="margin-top:4px; font-size:13.5px; color:#1e293b; font-weight:700;">
                            {{ $nextTargetAddress }}
                        </div>
                    </div>

                    <div class="next-notes-box" style="display:flex; align-items:flex-start; gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span><strong>Catatan Patokan Rumah & Bawaan:</strong> {{ $nextTargetNotes }}</span>
                    </div>
                </div>

                <!-- Tombol Navigasi 1-Klik & Tombol Pengubah Status -->
                <div class="next-action-buttons">
                    <!-- Tombol Navigasi 1-Klik: Google Maps & Waze -->
                    <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                        Tombol Navigasi 1-Klik Langsung
                    </div>
                    <div class="nav-buttons-row">
                        <a href="{{ $nextGmapsUrl }}" target="_blank" class="btn-nav-action gmaps" id="btnNextActionGmaps" title="Buka rute titik tugas di Google Maps" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="18" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke-width="2"/></svg>
                            <span>Google Maps</span>
                        </a>
                        <a href="{{ $nextWazeUrl }}" target="_blank" class="btn-nav-action waze" id="btnNextActionWaze" title="Buka rute titik tugas di Waze" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/></svg>
                            <span>Waze</span>
                        </a>
                    </div>

                    <!-- Tombol Pengubah Status: Slider konfirmasi atau tombol aksi bertahap -->
                    <div style="margin-top:6px;">
                        <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">
                            Tombol Pengubah Status
                        </div>

                        @if ($nextAction)
                            <form method="POST" id="mainStatusForm" action="{{ route('dashboard.driver.schedule.status', $activeSchedule->id ?? 1) }}" onsubmit="return confirm('{{ $nextAction['prompt'] }}');">
                                @csrf
                                <input type="hidden" name="status" value="{{ $nextAction['next_key'] }}">
                                <input type="hidden" name="latitude" id="sliderLat" value="">
                                <input type="hidden" name="longitude" id="sliderLng" value="">

                                <!-- Interactive Slider Confirmation -->
                                <div class="status-slider-wrap" id="statusSliderWrap" title="Geser ke kanan untuk konfirmasi status">
                                    <div class="status-slider-progress" id="statusSliderProgress"></div>
                                    <div class="status-slider-track-text" id="statusSliderText">
                                        <span>Geser untuk Konfirmasi: {{ $nextAction['label'] }}</span>
                                    </div>
                                    <div class="status-slider-thumb" id="statusSliderThumb" style="display:flex; align-items:center; justify-content:center;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                </div>

                                <!-- Direct Click Trigger -->
                                <button type="submit" class="btn-main-status-trigger" style="margin-top:8px;">
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
                                <button type="submit" class="btn-main-status-trigger" style="display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Penumpang Sudah Naik ({{ $unboardedPassenger->name }})</span>
                                </button>
                            </form>
                        @else
                            <div class="pill-badge" style="background:#f1f5f9; color:#475569; font-weight:700; width:100%; justify-content:center; padding:12px; display:inline-flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Perjalanan Ini Telah Ditutup &amp; Selesai</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

