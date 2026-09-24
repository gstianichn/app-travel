<!-- 1. INFORMASI SHIFT & LOKASI + TOMBOL PINTAS (SHORTCUT) -->

            <div class="shift-banner">

                <div class="shift-details">

                    <div class="shift-badge-row">
                        <span class="badge-duty">
                            <span class="pulse-green"></span>
                            {{ $shiftInfo['status'] }}
                        </span>
                        <span class="badge-duty" style="background: rgba(255,255,255,0.12);">
                            🕒 {{ $shiftInfo['shift_name'] }}
                        </span>
                    </div>

                    <h2 class="shift-title">
                        Halo, {{ $shiftInfo['staff_name'] }} 👋
                    </h2>

                    <div class="shift-meta">
                        <div class="shift-meta-item">
                            <span>📍</span>
                            <strong>{{ $shiftInfo['pool_name'] }}</strong>
                        </div>
                        <div class="shift-meta-item">
                            <span>📅</span>
                            <span>{{ $shiftInfo['active_date'] }}</span>
                        </div>
                        <div class="shift-meta-item">
                            <span>🏢</span>
                            <span>Loket Keberangkatan Utama</span>
                        </div>
                    </div>

                </div>

                <!-- TOMBOL PINTAS (SHORTCUT): SCAN QR -->
                <div class="shift-actions">
                    <button type="button" class="btn-shortcut-scanner" onclick="openScannerModal()">
                        <div class="scan-icon-box">
                            📷
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; line-height: 1.2;">Buka Kamera Pemindai</div>
                            <div style="font-size: 11px; font-weight: 500; opacity: 0.8;">Tombol Pintas (Scan QR)</div>
                        </div>
                    </button>
                </div>

            </div>
