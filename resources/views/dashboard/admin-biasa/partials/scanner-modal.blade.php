<!-- =========================================
     MODAL: KAMERA PEMINDAI (SCAN QR E-TICKET)
========================================== -->

<div id="scannerModal" class="scanner-modal-backdrop" onclick="handleBackdropClick(event)">

    <div class="scanner-modal-content">

        <div class="scanner-header">
            <h4 style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                    <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                    <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                    <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                    <rect x="7" y="7" width="10" height="10" rx="1" />
                </svg>
                <span id="modalTitle">Kamera Pemindai QR Code E-Ticket</span>
            </h4>
            <button type="button" class="scanner-close-btn" onclick="closeScannerModal()" title="Tutup Pemindai">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <div class="scanner-body">

            <!-- Viewfinder Kamera dengan Animasi Laser -->
            <div class="scanner-camera-box">
                <div class="scanner-hud-frame">
                    <div class="scanner-laser"></div>
                </div>
                <div class="scanner-instruction">
                    Arahkan kamera ke QR Code pada E-Ticket / Boarding Pass penumpang
                </div>
            </div>

            <!-- Form Manual Kode Booking -->
            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                    Atau Masukkan Kode Booking / E-Ticket:
                </label>
                <div class="scanner-manual-form">
                    <input
                        type="text"
                        id="bookingCodeInput"
                        class="scanner-manual-input"
                        placeholder="Contoh: TKV-882190-JKT"
                        onkeydown="if(event.key==='Enter'){ validateTicketCode(); }"
                    >
                    <button type="button" class="scanner-manual-btn" onclick="validateTicketCode()">
                        Periksa
                    </button>
                </div>
            </div>

            <!-- Quick Test Chips -->
            <div class="scanner-test-chips">
                <span>Simulasi Cepat:</span>
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-7821-JKT', 'Budi Santoso', '03A', 'Bandung → Jakarta', 'D 7812 AB')" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    <span>TKV-7821-JKT (Budi - 03A)</span>
                </button>
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-9904-CGK', 'Siti Rahma', '01B', 'Bandung → Bandara Soetta', 'B 1945 TKL')" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    <span>TKV-9904-CGK (Siti - 01B)</span>
                </button>
            </div>

            <!-- Kartu Hasil Pemindaian / Validasi -->
            <div id="scanResultCard" class="scan-result-card">
                <div class="scan-result-header">
                    <strong style="display: flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                        <span>E-TICKET VALID & SIAP BOARDING</span>
                    </strong>
                    <span style="font-size: 11px; font-weight: 700; background: #b1eed1; color: #064932; padding: 2px 8px; border-radius: 6px;" id="resultTripStatus">
                        Lunas
                    </span>
                </div>

                <div class="scan-result-grid">
                    <div>
                        <span>Nama Penumpang</span>
                        <b id="resultPassengerName">-</b>
                    </div>
                    <div>
                        <span>Nomor Kursi</span>
                        <b id="resultSeatNumber" style="color: #087f55; font-size: 14px;">-</b>
                    </div>
                    <div>
                        <span>Kode Booking</span>
                        <b id="resultBookingCode" style="font-family: monospace;">-</b>
                    </div>
                    <div>
                        <span>Rute & Armada</span>
                        <b id="resultRoute">-</b>
                    </div>
                </div>

                <button type="button" class="btn-confirm-boarding" onclick="confirmBoarding()">
                    Konfirmasi Masuk Armada (Boarding Pass Valid)
                </button>
            </div>

        </div>

    </div>

</div>
