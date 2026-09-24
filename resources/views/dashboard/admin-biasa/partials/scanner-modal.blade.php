<!-- =========================================
     MODAL: KAMERA PEMINDAI (SCAN QR E-TICKET)
========================================== -->

<div id="scannerModal" class="scanner-modal-backdrop" onclick="handleBackdropClick(event)">

    <div class="scanner-modal-content">

        <div class="scanner-header">
            <h4>
                <span>📷</span>
                <span id="modalTitle">Kamera Pemindai QR Code E-Ticket</span>
            </h4>
            <button type="button" class="scanner-close-btn" onclick="closeScannerModal()" title="Tutup Pemindai">
                ✕
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
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-7821-JKT', 'Budi Santoso', '03A', 'Bandung → Jakarta', 'D 7812 AB')">
                    ⚡ TKV-7821-JKT (Budi - 03A)
                </button>
                <button type="button" class="test-chip-btn" onclick="simulateScan('TKV-9904-CGK', 'Siti Rahma', '01B', 'Bandung → Bandara Soetta', 'B 1945 TKL')">
                    ⚡ TKV-9904-CGK (Siti - 01B)
                </button>
            </div>

            <!-- Kartu Hasil Pemindaian / Validasi -->
            <div id="scanResultCard" class="scan-result-card">
                <div class="scan-result-header">
                    <strong>
                        <span>✅</span>
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
