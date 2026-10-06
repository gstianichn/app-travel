<!-- ============================================================
     3. PINTASAN PENCARIAN CEPAT (OMNISEARCH BAR)
     Cari instan: Kode Booking (PNR), No. WA, Nama, Plat Nomor
============================================================ -->

<div class="card omnisearch-card" id="omnisearch-section">
    <div class="card-header">
        <div class="card-header-left">
            <span class="card-icon-bullet" style="background: var(--brand);"></span>
            <h2 class="card-title">Pencarian Cepat Omnisearch Pelanggan & Armada</h2>
        </div>
        <div class="omnisearch-chips-hint">
            <span class="hint-tag">Kode Booking (PNR)</span>
            <span class="hint-tag">No. WhatsApp</span>
            <span class="hint-tag">Nama Penumpang</span>
            <span class="hint-tag">Plat Nomor Armada</span>
        </div>
    </div>

    <div class="card-body">
        <div class="omnisearch-input-wrapper">
            <div class="omnisearch-field-box">
                <span class="omnisearch-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text"
                       id="omnisearch-input"
                       class="omnisearch-input"
                       placeholder="Ketik Kode Booking (TRV-...), No. WhatsApp (0812...), Nama Penumpang, atau Plat Nomor (D 7182 AB)..."
                       autocomplete="off"
                       onkeyup="handleOmnisearchInput(event)">
                <button type="button" class="cs-btn cs-btn-primary omnisearch-btn" onclick="executeOmnisearch()">
                    <span>Cari Data</span>
                </button>
            </div>
        </div>

        <!-- Hasil Pencarian Instan (Live Result Box) -->
        <div id="omnisearch-result-container" class="omnisearch-result-container" style="display: none;">
            <div class="omnisearch-card-result">
                <div class="result-header">
                    <div class="result-title-group">
                        <span class="result-badge-pnr" id="res-code">TRV-2026-0925</span>
                        <span class="badge-status-confirmed" id="res-status">Terkonfirmasi (Lunas)</span>
                    </div>
                    <div class="result-actions">
                        <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-light" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <span>Hubungkan ke Live Chat</span>
                        </a>
                    </div>
                </div>

                <div class="result-grid-info">
                    <div class="res-col">
                        <span class="res-label">Nama Penumpang:</span>
                        <strong class="res-val" id="res-name">Budi Prasetyo</strong>
                        <span class="res-subval" id="res-phone">WhatsApp: 0812-9988-7766</span>
                    </div>
                    <div class="res-col">
                        <span class="res-label">Rute & Jadwal:</span>
                        <strong class="res-val" id="res-route">Bandung (Pasteur) &rarr; Jakarta (Semanggi)</strong>
                        <span class="res-subval" id="res-time">Hari Ini, 14:00 WIB &bull; Kursi 03A</span>
                    </div>
                    <div class="res-col">
                        <span class="res-label">Armada & Pengemudi:</span>
                        <strong class="res-val" id="res-vehicle">Toyota HiAce Premio (D 7182 AB)</strong>
                        <span class="res-subval" id="res-driver">Pak Suryanto (0812-7788-9900)</span>
                    </div>
                    <div class="res-col">
                        <span class="res-label">Status Boarding:</span>
                        <strong class="res-val text-brand" id="res-boarding">Proses Persiapan Pool</strong>
                        <span class="res-subval" id="res-pool">Pool Pasteur Loket 2</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
