            <!-- ================= 2. KENDARAAN YANG DITUGASKAN ================= -->
            <div class="assigned-vehicle-segment" id="section-kendaraan">
                <!-- Header Kartu Kendaraan -->
                <div class="assigned-vehicle-header">
                    <div class="assigned-vehicle-header-info">
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                            <span>FITUR 3</span>
                            <span>•</span>
                            <span>SPESIFIKASI FISIK & DOKUMEN ARMADA</span>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <span></span>
                            <span>Melihat Kendaraan yang Ditugaskan</span>
                        </h3>
                        <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                            Fokus: Spesifikasi teknis dan fisik unit mini bus serta legalitas dokumen operasional aktif.
                        </p>
                    </div>

                    <div class="spj-badge-box">
                        <div class="spj-pill">
                            <span class="spj-label">Surat Perintah Jalan (SPJ)</span>
                            <span class="spj-code">{{ $activeSchedule->vehicle?->spjNumber($activeSchedule) ?? 'SPJ/20260924/HCE-07' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 4 Metrik Inti: Pelat Nomor, Jenis/Tipe Mobil, Nomor Lambung, Lokasi Parkir -->
                <div class="vehicle-metric-grid">
                    <!-- 1. Nomor Polisi (Pelat Nomor) -->
                    <div class="vehicle-metric-card">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon"></span>
                            <span class="vehicle-metric-tag">Nomor Polisi (Pelat)</span>
                        </div>
                        <div class="plate-emblem" style="margin-top: 4px; margin-bottom: 6px; width: fit-content;">
                            <span>POL</span>
                            <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>
                        </div>
                        <div class="vehicle-metric-sub">
                            Pelat Hitam Angkutan Khusus Pariwisata & Travel
                        </div>
                    </div>

                    <!-- 2. Jenis / Tipe Mobil -->
                    <div class="vehicle-metric-card">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon"></span>
                            <span class="vehicle-metric-tag">Jenis / Tipe Mobil</span>
                        </div>
                        <div class="vehicle-metric-val">
                            {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Tipe: <strong>{{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio Luxury' }}</strong> • Kapasitas: <strong>{{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi</strong>
                        </div>
                    </div>

                    <!-- 3. Nomor Lambung Armada -->
                    <div class="vehicle-metric-card highlight-amber">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon"></span>
                            <span class="vehicle-metric-tag" style="color: #b45309;">Nomor Lambung Armada</span>
                        </div>
                        <div class="vehicle-metric-val" style="font-family:'JetBrains Mono', monospace; font-size:22px; color:#b45309; font-weight:800;">
                            {{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Tercetak pada Kaca Depan & Bodi Samping
                        </div>
                    </div>

                    <!-- 4. Lokasi Fisik Kendaraan Diparkir -->
                    <div class="vehicle-metric-card highlight-emerald">
                        <div class="vehicle-metric-top">
                            <span class="vehicle-metric-icon"></span>
                            <span class="vehicle-metric-tag" style="color: #065f46;">Lokasi Fisik Parkir</span>
                        </div>
                        <div class="vehicle-metric-val" style="color: #065f46; font-size:15px; font-weight:800;">
                            {{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}
                        </div>
                        <div class="vehicle-metric-sub">
                            Jalur Standby Keberangkatan Siap Jalan
                        </div>
                    </div>
                </div>

                <!-- Spesifikasi Teknis & Fisik Unit Mini Bus (Baru) -->
                <div class="vehicle-specs-container">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <h4 style="margin: 0; font-size: 13.5px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                            <span></span>
                            <span>Spesifikasi Teknis & Fisik Unit Mini Bus</span>
                        </h4>
                        <span style="font-size: 11px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 3px 8px; border-radius: 6px; border: 1px solid #bfdbfe;">
                            Standar Armada Travel Eksekutif
                        </span>
                    </div>

                    <div class="vehicle-specs-grid">
                        <!-- 1. Merk & Model -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Merk & Model Varian</span>
                            </div>
                            <div class="spec-val">
                                {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Luxury' }}
                            </div>
                            <div class="spec-sub">
                                Model: {{ $activeSchedule->vehicle?->type ?? 'Toyota HiAce Premio' }} (Semi-Bonnet LWB)
                            </div>
                        </div>

                        <!-- 2. Kapasitas Total Bangku -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Kapasitas Total Bangku</span>
                            </div>
                            <div class="spec-val">
                                {{ $activeSchedule->vehicle?->capacity ?? 10 }} Kursi Penumpang (+ 1 Driver)
                            </div>
                            <div class="spec-sub">
                                Konfigurasi 1-2 Executive Reclining & Seatbelt 3-Titik
                            </div>
                        </div>

                        <!-- 3. Posisi Parkir Armada -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Posisi Parkir Armada</span>
                            </div>
                            <div class="spec-val" style="color: #065f46;">
                                {{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}
                            </div>
                            <div class="spec-sub">
                                Akses cepat lajur gate keberangkatan pool
                            </div>
                        </div>

                        <!-- 4. Dapur Pacu / Mesin -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Spesifikasi Dapur Pacu</span>
                            </div>
                            <div class="spec-val">
                                2.8L 1GD-FTV Turbo Diesel
                            </div>
                            <div class="spec-sub">
                                4-Silinder DOHC 16-Valve D-4D Common Rail Intercooler
                            </div>
                        </div>

                        <!-- 5. Transmisi & Rangka -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Nomor Rangka & Mesin</span>
                            </div>
                            <div class="spec-val" style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px;">
                                {{ $activeSchedule->vehicle?->chassis_number ?? 'MHF21TRP008891' }}
                            </div>
                            <div class="spec-sub" style="font-family: 'JetBrains Mono', monospace;">
                                Mesin: {{ $activeSchedule->vehicle?->engine_number ?? '1GD-FTV-992144' }} • Thn {{ $activeSchedule->vehicle?->manufacture_year ?? 2024 }}
                            </div>
                        </div>

                        <!-- 6. Fasilitas Fisik Kabin -->
                        <div class="spec-item-box">
                            <div class="spec-label">
                                <span></span>
                                <span>Fasilitas Fisik Kabin</span>
                            </div>
                            <div class="spec-val">
                                AC Double Blower & USB Charger
                            </div>
                            <div class="spec-sub">
                                Android TV, Audio Stereo, APAR Siaga & Kotak P3K
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Masa Berlaku Dokumen Armada (Pajak STNK & KIR) yang Sedang Aktif -->
                @php
                    $stnkInfo = $activeSchedule->vehicle?->stnkStatus() ?? ['status' => 'valid', 'label' => 'Berlaku Baik', 'badge_class' => 'badge-legal-valid', 'days_left' => 240];
                    $kirInfo = $activeSchedule->vehicle?->kirStatus() ?? ['status' => 'valid', 'label' => 'Berlaku Baik', 'badge_class' => 'badge-legal-valid', 'days_left' => 150];
                @endphp
                <div class="vehicle-docs-grid">
                    <!-- Dokumen 1: Masa Berlaku Pajak STNK -->
                    <div class="doc-status-card {{ $stnkInfo['status'] === 'valid' ? 'valid' : ($stnkInfo['status'] === 'expiring_soon' ? 'warning' : '') }}">
                        <div class="doc-details">
                            <div class="doc-title-row">
                                <strong>Masa Berlaku Pajak STNK</strong>
                                <span class="badge {{ $stnkInfo['badge_class'] }}">
                                    {{ $stnkInfo['label'] }}
                                </span>
                            </div>
                            <div class="doc-date-val">
                                {{ $activeSchedule->vehicle?->stnk_tax_due_date ? $activeSchedule->vehicle->stnk_tax_due_date->isoFormat('D MMMM YYYY') : '18 November 2026' }}
                            </div>
                            <div class="doc-sub">
                                Status: Pajak tahunan aktif terdaftar resmi Samsat. Dokumen STNK fisik tersimpan di laci dashboard sopir.
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen 2: Masa Berlaku Uji Kelayakan KIR Dishub -->
                    <div class="doc-status-card {{ $kirInfo['status'] === 'valid' ? 'valid' : ($kirInfo['status'] === 'expiring_soon' ? 'warning' : '') }}">
                        <div class="doc-details">
                            <div class="doc-title-row">
                                <strong>Masa Berlaku Uji Berkala (KIR Dishub)</strong>
                                <span class="badge {{ $kirInfo['badge_class'] }}">
                                    {{ $kirInfo['label'] }}
                                </span>
                            </div>
                            <div class="doc-date-val">
                                {{ $activeSchedule->vehicle?->kir_expiry_date ? $activeSchedule->vehicle->kir_expiry_date->isoFormat('D MMMM YYYY') : '25 Oktober 2026' }}
                            </div>
                            <div class="doc-sub">
                                Status: Uji berkala kendaraan Dishub aktif (Rem, Ban, Emisi Gas Buang & Kelayakan Teknis Laik Jalan).
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner Verifikasi SPJ: Memastikan Unit Tepat & Tanpa Tertukar (Kegunaan) -->
                <div class="vehicle-verification-banner">
                    <div class="verification-notice">
                        <div class="verification-text">
                            <h4>Pencocokan Fisik Unit &amp; Verifikasi SPJ (Cegah Tertukar)</h4>
                            <p>Memastikan sopir membawa unit yang tepat sesuai surat perintah jalan tanpa tertukar dengan armada rekan lainnya di area pool atau garasi.</p>
                        </div>
                    </div>

                    <div class="verification-checklist">
                        <div class="check-item-chip">
                            <span></span>
                            <span>Lambung: <strong>{{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}</strong></span>
                        </div>
                        <div class="check-item-chip">
                            <span></span>
                            <span>Pelat: <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong></span>
                        </div>
                        <div class="check-item-chip">
                            <span></span>
                            <span>Posisi: <strong>{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3' }}</strong></span>
                        </div>
                        <div class="check-item-chip" style="background:#f1f5f9; border-color:#cbd5e1; color:#334155;">
                            <span></span>
                            <span>Status: <strong>{{ $activeSchedule->vehicle?->statusLabel() ?? 'Siap Jalan' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

