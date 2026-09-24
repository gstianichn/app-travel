        <!-- ================= 3. KARTU AKSI UTAMA (NEXT ACTION CARD) ================= -->
        <section class="next-action-card" id="section-ubah-status">
            <div class="next-action-header">
                <div class="next-action-tag">
                    <span>⚡</span>
                    <span>3. Kartu Aksi Utama (Next Action Card)</span>
                    <span style="background:#059669; color:white; font-size:10px; padding:2px 8px; border-radius:12px; margin-left:6px; font-weight:800;">
                        Aksi Cepat Driver Saat Bergerak
                    </span>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="pill-badge" style="background:#ffffff; border-color:#86efac; color:#15803d; font-size:11px; font-weight:800;">
                        💺 {{ $nextSeatNumber }}
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
                    <h3>
                        <span>👤</span>
                        <span>{{ $nextTargetTitle }}</span>
                        @if ($unboardedPassenger && !$unboardedPassenger->is_paid)
                            <span class="badge-cod" style="font-size:11px;">Tagihan Tunai COD: Rp {{ number_format($unboardedPassenger->amount_due, 0, ',', '.') }}</span>
                        @elseif ($unboardedPassenger && $unboardedPassenger->is_paid)
                            <span class="badge-paid" style="font-size:11px;">Tiket Lunas</span>
                        @endif
                    </h3>

                    <div class="next-address-box">
                        <div style="font-weight:700; color:var(--brand-dark); display:flex; align-items:center; gap:6px;">
                            <span>📍</span>
                            <span>Alamat Lengkap Titik Jemput / Turun:</span>
                        </div>
                        <div style="margin-top:4px; font-size:13.5px; color:#1e293b; font-weight:700;">
                            {{ $nextTargetAddress }}
                        </div>
                    </div>

                    <div class="next-notes-box">
                        <span>🏡</span>
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
                        <a href="{{ $nextGmapsUrl }}" target="_blank" class="btn-nav-action gmaps" id="btnNextActionGmaps" title="Buka rute titik tugas di Google Maps">
                            <span>🗺️</span>
                            <span>Google Maps</span>
                        </a>
                        <a href="{{ $nextWazeUrl }}" target="_blank" class="btn-nav-action waze" id="btnNextActionWaze" title="Buka rute titik tugas di Waze">
                            <span>🚙</span>
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
                                        <span>👉 Geser untuk Konfirmasi: {{ $nextAction['label'] }}</span>
                                    </div>
                                    <div class="status-slider-thumb" id="statusSliderThumb">
                                        ➔
                                    </div>
                                </div>

                                <!-- Direct Click Trigger -->
                                <button type="submit" class="btn-main-status-trigger" style="margin-top:8px;">
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
                                <button type="submit" class="btn-main-status-trigger">
                                    <span>✓</span>
                                    <span>Penumpang Sudah Naik ({{ $unboardedPassenger->name }})</span>
                                </button>
                            </form>
                        @else
                            <div class="pill-badge" style="background:#f1f5f9; color:#475569; font-weight:700; width:100%; justify-content:center; padding:12px;">
                                ✓ Perjalanan Ini Telah Ditutup &amp; Selesai
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

