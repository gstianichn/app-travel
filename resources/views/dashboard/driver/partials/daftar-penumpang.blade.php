        <!-- ================= 4. MELIHAT DAFTAR PENUMPANG (MANIFES ABSENSI) ================= -->
        <section class="card-panel" id="section-manifest" style="margin-bottom: 24px;">
            <div class="card-panel-header">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                        <span>FITUR 4</span>
                        <span>•</span>
                        <span>MANIFES ABSENSI &amp; KEHADIRAN PENUMPANG</span>
                    </div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span></span>
                        <span>Melihat Daftar Penumpang</span>
                    </h3>
                    <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                        Fokus: Manifes absensi dan status kehadiran seluruh penumpang dalam satu perjalanan.
                    </p>
                </div>

                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <a href="#section-seatmap" class="btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                        <span></span>
                        <span>Denah Kursi Kabin</span>
                    </a>
                </div>
            </div>

            <div class="card-panel-body">
                <!-- REKAPITULASI JUMLAH PENUMPANG (Total, Sudah Naik, Belum Naik) -->
                @php
                    $attendanceRate = $totalBookedPassengers > 0 ? round(($boardedCount / $totalBookedPassengers) * 100) : 0;
                @endphp
                <div class="rekapitulasi-penumpang-grid">
                    <!-- 1. Total Penumpang -->
                    <div class="rekap-card rekap-total">
                        <div class="rekap-top">
                            <span class="rekap-icon"></span>
                            <span class="rekap-label">Total Penumpang</span>
                        </div>
                        <div class="rekap-val">
                            {{ $totalBookedPassengers }} Orang
                        </div>
                        <div class="rekap-sub">
                            Kapasitas Armada: {{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi
                        </div>
                    </div>

                    <!-- 2. Sudah Naik -->
                    <div class="rekap-card rekap-boarded">
                        <div class="rekap-top">
                            <span class="rekap-icon"></span>
                            <span class="rekap-label">Sudah Naik</span>
                        </div>
                        <div class="rekap-val" style="color: #059669;">
                            {{ $boardedCount }} Penumpang
                        </div>
                        <div class="rekap-sub">
                            Terkonfirmasi Masuk Kabin (Boarded)
                        </div>
                    </div>

                    <!-- 3. Belum Naik -->
                    <div class="rekap-card rekap-pending">
                        <div class="rekap-top">
                            <span class="rekap-icon">⏳</span>
                            <span class="rekap-label">Belum Naik</span>
                        </div>
                        <div class="rekap-val" style="color: #d97706;">
                            {{ $pendingBoardingCount }} Penumpang
                        </div>
                        <div class="rekap-sub">
                            Menunggu di Titik Jemput / Pool
                        </div>
                    </div>
                </div>

                <!-- PROGRESS BAR KEHADIRAN (ABSENSI) -->
                <div class="attendance-progress-wrap">
                    <div class="attendance-progress-info">
                        <span><strong>Progress Absensi:</strong> {{ $boardedCount }} dari {{ $totalBookedPassengers }} penumpang telah terkonfirmasi naik</span>
                        <strong>{{ $attendanceRate }}% Selesai</strong>
                    </div>
                    <div class="attendance-progress-bar">
                        <div class="attendance-progress-fill" style="width: {{ $attendanceRate }}%;"></div>
                    </div>
                </div>

                <!-- PENCARIAN & FILTER PENUMPANG -->
                <div class="manifest-search-box">
                    <span class="manifest-search-icon"></span>
                    <input type="text" id="manifestSearchInput" placeholder="Ketik nama penumpang atau nomor kursi (misal: Seat 1A) untuk menyaring..." onkeyup="filterManifest()">
                    <span style="font-size: 11.5px; color: #64748b; white-space: nowrap; padding-right: 14px;">Urut per nomor kursi</span>
                </div>

                <!-- DAFTAR BARIS NAMA RINGKAS PENUMPANG PER NOMOR KURSI -->
                <div class="manifest-list" id="section-info-penumpang">
                    @forelse ($manifestPassengers as $p)
                        <div class="passenger-item {{ $p->is_boarded ? 'is-boarded' : '' }}" data-name="{{ strtolower($p->name) }}" data-seat="{{ strtolower($p->seat_number) }}">
                            <div class="passenger-top">
                                <div class="passenger-avatar-box">
                                    <!-- Nomor Kursi Penumpang -->
                                    <div class="seat-badge-pill">
                                        Seat {{ $p->seat_number }}
                                    </div>
                                    <div class="passenger-name-wrap">
                                        <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">{{ $p->name }}</h4>
                                        <p style="margin: 2px 0 0 0; font-size: 11px; color: #64748b;">
                                            Kode Tiket: <strong>{{ $p->booking_code }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <div class="passenger-badges">
                                    <!-- Status Pembayaran Tiket: Lunas Online vs Tagih Tunai/COD -->
                                    @if ($p->is_paid)
                                        <span class="badge-paid" style="font-size: 11px; font-weight: 800; padding: 4px 9px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                                            Lunas Online
                                        </span>
                                    @else
                                        <span class="badge-cod" style="font-size: 11px; font-weight: 800; padding: 4px 9px; border-radius: 6px; background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; display: inline-flex; align-items: center; gap: 4px;">
                                            Tagih Tunai / COD: Rp {{ number_format($p->amount_due, 0, ',', '.') }}
                                        </span>
                                    @endif

                                    <!-- Status Kehadiran Absensi -->
                                    @if ($p->is_boarded)
                                        <span class="badge-boarded" style="font-size: 11px; font-weight: 800; padding: 4px 9px; border-radius: 6px; background: #059669; color: white; display: inline-flex; align-items: center; gap: 4px;">
                                            Sudah Naik (Boarded) {{ $p->boarded_at ? '• ' . $p->boarded_at : '' }}
                                        </span>
                                    @else
                                        <span class="badge-waiting" style="font-size: 11px; font-weight: 700; padding: 4px 9px; border-radius: 6px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 4px;">
                                            ⏳ Belum Naik
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- DETAIL RUTE JEMPUT & CATATAN BAWAAN -->
                            <div class="passenger-details-strip">
                                <div>Titik Jemput: <strong>{{ $p->pickup_point }}</strong> &nbsp; ke &nbsp; Turun: <strong>{{ $p->dropoff_point }}</strong></div>
                                <div>Catatan Bawaan: <strong>{{ $p->baggage_notes }}</strong></div>
                                @if (!empty($p->special_request) && $p->special_request !== '-')
                                    <div style="color: #b45309;">Catatan Tambahan: {{ $p->special_request }}</div>
                                @endif
                                @if (!$p->is_paid && $p->amount_due > 0)
                                    <div style="color: #b91c1c; font-weight: 700; background: #fef2f2; border: 1px dashed #fca5a5; padding: 6px 10px; border-radius: 6px; margin-top: 4px;">
                                        Perhatian Sopir: Tagih uang tunai Rp {{ number_format($p->amount_due, 0, ',', '.') }} sebelum penumpang duduk di kursi armada.
                                    </div>
                                @endif
                            </div>

                            <!-- ACTION BAR: KONTAK CEPAT & TOMBOL CENTANG CEPAT ABSENSI -->
                            <div class="passenger-actions">
                                <div class="contact-group">
                                    <!-- Tombol WhatsApp langsung tanpa simpan nomor -->
                                    <a href="https://wa.me/{{ $p->wa_phone }}?text={{ urlencode('Halo Kak ' . $p->name . ', saya pengemudi Travel Minibus untuk jadwal hari ini. Saya ingin mengonfirmasi penjemputan di ' . $p->pickup_point . '.') }}"
                                       target="_blank"
                                       class="btn-contact btn-contact-wa"
                                       title="Kirim Pesan WhatsApp Langsung Tanpa Simpan Nomor">
                                        <span></span>
                                        <span>WhatsApp</span>
                                    </a>

                                    <!-- Tombol Panggil Telepon langsung -->
                                    <a href="tel:{{ $p->phone }}"
                                       class="btn-contact btn-contact-tel"
                                       title="Panggil Telepon Langsung">
                                        <span></span>
                                        <span>Telepon</span>
                                    </a>
                                </div>

                                <div>
                                    @if ($p->passenger_model)
                                        <form method="POST" action="{{ route('dashboard.driver.passenger.checkin', $p->id) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')

                                            @if (!$p->is_boarded)
                                                <input type="hidden" name="boarding_status" value="boarded">
                                                @if (!$p->is_paid)
                                                    <input type="hidden" name="mark_paid" value="1">
                                                    <button type="submit" class="btn-checkin-action btn-board" title="Terima uang tunai dan konfirmasi absensi naik">
                                                        <span></span>
                                                        <span>Terima COD &amp; Check-in</span>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn-checkin-action btn-board" title="Centang cepat absensi penumpang naik">
                                                        <span></span>
                                                        <span>Check-in / Boarded</span>
                                                    </button>
                                                @endif
                                            @else
                                                <input type="hidden" name="boarding_status" value="belum_hadir">
                                                <button type="submit" class="btn-checkin-action btn-unboard" title="Batalkan absensi jika salah tekan">
                                                    <span>↩ Batalkan Absensi</span>
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <!-- Demo fallback action -->
                                        <button type="button" class="btn-checkin-action {{ $p->is_boarded ? 'btn-unboard' : 'btn-board' }}" onclick="alert('Simulasi: Status absensi untuk {{ $p->name }} (Kursi {{ $p->seat_number }}) berhasil diperbarui!')">
                                            <span></span>
                                            <span>{{ $p->is_boarded ? 'Batalkan Absensi' : ($p->is_paid ? 'Check-in / Boarded' : 'Terima COD & Check-in') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center; padding:30px; color:var(--text-muted);">
                            Belum ada manifes penumpang untuk jadwal ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

