<!-- 1. INFORMASI SHIFT & LOKASI + TOMBOL PINTAS (SHORTCUT) -->

            <div class="shift-banner">

                <div class="shift-details">

                    <div class="shift-badge-row">
                        <span class="badge-duty">
                            <span class="pulse-green"></span>
                            {{ $shiftInfo['status'] }}
                        </span>
                        <span class="badge-duty" style="background: rgba(255,255,255,0.12); display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ $shiftInfo['shift_name'] }}
                        </span>
                    </div>

                    <h2 class="shift-title" style="display: flex; align-items: center; gap: 8px;">
                        <span>Halo, {{ $shiftInfo['staff_name'] }}</span>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #fef08a;">
                            <path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0" />
                            <path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2" />
                            <path d="M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8" />
                            <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15" />
                        </svg>
                    </h2>

                    <div class="shift-meta">
                        <div class="shift-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <strong>{{ $shiftInfo['pool_name'] }}</strong>
                        </div>
                        <div class="shift-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <span>{{ $shiftInfo['active_date'] }}</span>
                        </div>
                        <div class="shift-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2" />
                                <path d="M9 22v-4h6v4" />
                                <path d="M8 6h.01" />
                                <path d="M16 6h.01" />
                                <path d="M12 6h.01" />
                                <path d="M12 10h.01" />
                                <path d="M12 14h.01" />
                                <path d="M16 10h.01" />
                                <path d="M16 14h.01" />
                                <path d="M8 10h.01" />
                                <path d="M8 14h.01" />
                            </svg>
                            <span>Loket Keberangkatan Utama</span>
                        </div>
                    </div>

                </div>

                <!-- TOMBOL PINTAS (SHORTCUT): SCAN QR -->
                <div class="shift-actions">
                    <button type="button" class="btn-shortcut-scanner" onclick="openScannerModal()">
                        <div class="scan-icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                <rect x="7" y="7" width="10" height="10" rx="1" />
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; line-height: 1.2;">Buka Kamera Pemindai</div>
                            <div style="font-size: 11px; font-weight: 500; opacity: 0.8;">Tombol Pintas (Scan QR)</div>
                        </div>
                    </button>
                </div>

            </div>
