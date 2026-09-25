@extends('layouts.customer-service')

@section('title', 'Melakukan Eskalasi ke Admin - Customer Service')

@section('content')
<div class="cs-escalation-wrapper">

    <!-- Top Page Header -->
    <div class="esc-page-header">
        <div class="header-info">
            <div class="esc-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Jalur Pengalihan Kendala Melampaui Wewenang CS</span>
            </div>
            <h1 class="header-title">Melakukan Eskalasi ke Admin</h1>
            <p class="header-subtitle">
                Jalur pengalihan masalah yang melampaui wewenang CS garis depan. Sesuai batasan operasional, CS tidak mengeksekusi refund finansial, penggantian unit kendaraan, atau sanksi disiplin secara langsung. Gunakan formulir terstandar untuk menyampaikan akar masalah dan data pendukung kepada Admin Utama, Operasional, dan Keuangan.
            </p>
        </div>

        <div class="header-action-group">
            <button type="button" class="cs-btn cs-btn-primary" onclick="scrollToForm()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Buat Tiket Eskalasi Baru</span>
            </button>
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Kembali ke Live Chat</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Counter -->
    <div class="esc-kpi-grid">
        <div class="esc-kpi-card" onclick="filterByStatus('all')">
            <div class="kpi-icon-wrap icon-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Total Tiket Eskalasi</span>
                <div class="kpi-number" id="count-total">6</div>
                <span class="kpi-desc">Keseluruhan kasus eskalasi tercatat</span>
            </div>
        </div>

        <div class="esc-kpi-card highlight-pending" onclick="filterByStatus('Pending di Admin')">
            <div class="kpi-icon-wrap icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Pending di Admin</span>
                <div class="kpi-number text-amber" id="count-pending">3</div>
                <span class="kpi-desc">Menunggu tinjauan &amp; persetujuan</span>
            </div>
        </div>

        <div class="esc-kpi-card highlight-process" onclick="filterByStatus('Diproses')">
            <div class="kpi-icon-wrap icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Diproses</span>
                <div class="kpi-number text-blue" id="count-process">2</div>
                <span class="kpi-desc">Ditangani Operasional / Keuangan</span>
            </div>
        </div>

        <div class="esc-kpi-card highlight-done" onclick="filterByStatus('Selesai')">
            <div class="kpi-icon-wrap icon-green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Selesai</span>
                <div class="kpi-number text-green" id="count-done">1</div>
                <span class="kpi-desc">Kasus tuntas &amp; solusi terkirim</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Form + Tracker Table) -->
    <div class="esc-main-layout">

        <!-- Left Column: Form Ringkasan Masalah (Eskalasi Baru) -->
        <div class="esc-form-card" id="form-eskalasi-section">
            <div class="card-header-styled">
                <div class="header-left-title">
                    <span class="header-icon-badge">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </span>
                    <div>
                        <h2 class="card-heading">Form Ringkasan Masalah</h2>
                        <p class="card-subheading">Isi kolom wajib akar masalah dan data pendukung sebelum diteruskan ke Admin</p>
                    </div>
                </div>
                <span class="required-flag">* Wajib Diisi</span>
            </div>

            <form id="escalation-form" onsubmit="handleCreateEscalation(event)" class="esc-form-body">
                <!-- 1. Pilihan Kategori Eskalasi (PRD 4.5) -->
                <div class="form-group-block">
                    <label class="input-label" for="category-select">
                        Pilihan Kategori Eskalasi <span class="text-danger">*</span>
                    </label>
                    <select id="category-select" class="styled-select" required onchange="onCategoryChange()">
                        <option value="" disabled selected>-- Pilih Kategori Eskalasi --</option>
                        <option value="Pengajuan Refund / Keuangan">Pengajuan Refund / Keuangan</option>
                        <option value="Armada Rusak / Ganti Mobil (Operasional)">Armada Rusak / Ganti Mobil (Operasional)</option>
                        <option value="Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)">Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)</option>
                    </select>
                    <span class="input-hint" id="category-hint">
                        Pilih salah satu dari 3 ranah kewenangan otorisasi Admin Utama.
                    </span>
                </div>

                <!-- 2. Prioritas Kendala (Severity Level) -->
                <div class="form-group-block">
                    <label class="input-label">
                        Prioritas Kendala (Severity Level) <span class="text-danger">*</span>
                    </label>
                    <div class="severity-radio-group">
                        <label class="severity-card sev-low">
                            <input type="radio" name="severity_level" value="Low" required>
                            <div class="sev-card-inner">
                                <div class="sev-tag">Low</div>
                                <span class="sev-time">&le; 24 Jam</span>
                                <p class="sev-text">Pertanyaan penyesuaian non-kritis atau koreksi data ringan.</p>
                            </div>
                        </label>
                        <label class="severity-card sev-medium">
                            <input type="radio" name="severity_level" value="Medium" checked>
                            <div class="sev-card-inner">
                                <div class="sev-tag">Medium</div>
                                <span class="sev-time">&le; 2 Jam</span>
                                <p class="sev-text">Permohonan refund resmi, kendala jadwal sebelum hari H.</p>
                            </div>
                        </label>
                        <label class="severity-card sev-emergency">
                            <input type="radio" name="severity_level" value="High/Emergency">
                            <div class="sev-card-inner">
                                <div class="sev-tag">High/Emergency</div>
                                <span class="sev-time">&le; 15 Menit</span>
                                <p class="sev-text">Mogok di jalan, sopir membahayakan, atau barang darurat hilang.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Identitas Pemesan & Booking -->
                <div class="form-grid-2col">
                    <div class="form-group-block">
                        <label class="input-label" for="booking-code-input">
                            Kode Booking (PNR) <span class="text-danger">*</span>
                        </label>
                        <div class="input-with-icon">
                            <span class="input-icon">🎫</span>
                            <input type="text" id="booking-code-input" class="styled-input" placeholder="Misal: TRV-2026-0925" required>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <label class="input-label" for="passenger-name-input">
                            Nama Pelanggan &amp; WhatsApp <span class="text-danger">*</span>
                        </label>
                        <div class="input-with-icon">
                            <span class="input-icon">👤</span>
                            <input type="text" id="passenger-name-input" class="styled-input" placeholder="Budi Prasetyo (0812-9988-7766)" required>
                        </div>
                    </div>
                </div>

                <!-- 4. Kolom Wajib: Akar Masalah (Root Cause) -->
                <div class="form-group-block">
                    <label class="input-label" for="root-cause-input">
                        Akar Masalah (Root Cause) <span class="text-danger">* (Wajib)</span>
                    </label>
                    <textarea id="root-cause-input"
                              class="styled-textarea"
                              rows="3"
                              placeholder="Tuliskan secara jelas akar masalah, kronologi urutan kejadian, dan alasan kendala ini melampaui wewenang CS..."
                              required></textarea>
                    <span class="input-hint">Jelaskan mengapa masalah membutuhkan tindakan Admin Utama/Operasional/Keuangan.</span>
                </div>

                <!-- 5. Kolom Wajib: Data Pendukung -->
                <div class="form-group-block">
                    <label class="input-label" for="supporting-data-input">
                        Data Pendukung <span class="text-danger">* (Wajib)</span>
                    </label>
                    <textarea id="supporting-data-input"
                              class="styled-textarea"
                              rows="3"
                              placeholder="Tuliskan nomor rekening & bank (jika refund), plat armada & nama sopir (jika armada/komplain), saksi kejadian, atau nomor tiket terkait..."
                              required></textarea>
                    <span class="input-hint">Contoh data valid: BCA 5420998811 a/n Budi Prasetyo, atau Unit D 1928 AB driver Pak Jajang.</span>
                </div>

                <!-- 6. Lampiran Opsional Bukti Foto/Dokumen -->
                <div class="form-group-block">
                    <label class="input-label" for="attachment-file-input">
                        Unggah Bukti Pendukung (JPG, PNG, PDF maks 5MB)
                    </label>
                    <div class="file-dropzone" onclick="document.getElementById('attachment-file-input').click()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                        </svg>
                        <span id="file-label-text">Klik untuk pilih berkas bukti / screenshot percakapan</span>
                        <input type="file" id="attachment-file-input" style="display: none;" accept=".jpg,.jpeg,.png,.pdf" onchange="onFileSelected(this)">
                    </div>
                </div>

                <div class="form-action-footer">
                    <button type="reset" class="cs-btn cs-btn-secondary" onclick="resetFormHint()">
                        Reset Formulir
                    </button>
                    <button type="submit" class="cs-btn cs-btn-danger">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <span>Kirim Eskalasi ke Admin</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Pelacakan Status Eskalasi (Status Tracker) -->
        <div class="esc-tracker-card" id="pelacakan-status-section">
            <div class="tracker-header">
                <div class="tracker-header-left">
                    <span class="header-icon-badge badge-tracker">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                    </span>
                    <div>
                        <h2 class="card-heading">Pelacakan Status Eskalasi</h2>
                        <p class="card-subheading">Pantau apakah kasus masih Pending di Admin, Diproses, atau Selesai</p>
                    </div>
                </div>

                <div class="tracker-header-right">
                    <div class="search-mini-wrap">
                        <input type="text"
                               id="search-tracker"
                               class="styled-search-input"
                               placeholder="Cari ID, nama, kode booking..."
                               oninput="filterTickets()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filter Status Tabs: Pending di Admin, Diproses, Selesai -->
            <div class="tracker-filter-bar">
                <div class="filter-tabs">
                    <button type="button" class="filter-tab active" data-status="all" onclick="filterByStatus('all')">
                        Semua Status (<span id="tab-count-all">6</span>)
                    </button>
                    <button type="button" class="filter-tab" data-status="Pending di Admin" onclick="filterByStatus('Pending di Admin')">
                        <span class="status-dot dot-amber"></span>
                        Pending di Admin (<span id="tab-count-pending">3</span>)
                    </button>
                    <button type="button" class="filter-tab" data-status="Diproses" onclick="filterByStatus('Diproses')">
                        <span class="status-dot dot-blue"></span>
                        Diproses (<span id="tab-count-process">2</span>)
                    </button>
                    <button type="button" class="filter-tab" data-status="Selesai" onclick="filterByStatus('Selesai')">
                        <span class="status-dot dot-green"></span>
                        Selesai (<span id="tab-count-done">1</span>)
                    </button>
                </div>

                <div class="filter-cat-select">
                    <select id="filter-category" class="styled-select-mini" onchange="filterTickets()">
                        <option value="all">Semua Kategori</option>
                        <option value="Pengajuan Refund / Keuangan">Refund / Keuangan</option>
                        <option value="Armada Rusak / Ganti Mobil (Operasional)">Armada Rusak / Ops</option>
                        <option value="Komplain Khusus">Komplain Khusus</option>
                    </select>
                </div>
            </div>

            <!-- Ticket List Container -->
            <div class="esc-ticket-list" id="ticket-items-container">
                <!-- Javascript will render tickets dynamically here -->
            </div>
        </div>

    </div>

</div>

<!-- Modal Detail & Follow Up Respon Admin -->
<div id="modal-ticket-detail" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card modal-lg">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge" id="modal-sev-badge">⚠️</span>
                <div>
                    <h3 class="modal-title" id="modal-detail-title">Detail Kasus Eskalasi #ESK-104</h3>
                    <p class="modal-sub" id="modal-detail-sub">Pengajuan Refund / Keuangan &bull; Pelapor: CS Nadia</p>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeTicketModal()">&times;</button>
        </div>

        <div class="modal-card-body">
            <!-- Status Badge & Severity -->
            <div class="detail-header-status">
                <div class="dh-left">
                    <span class="dh-label">Status Terkini:</span>
                    <span id="modal-status-pill" class="badge-status-pill">Pending di Admin</span>
                </div>
                <div class="dh-right">
                    <span class="dh-label">Prioritas Kendala:</span>
                    <span id="modal-severity-badge" class="badge-sev-pill">Medium</span>
                </div>
            </div>

            <!-- Booking & Passenger Box -->
            <div class="detail-info-grid">
                <div class="info-cell">
                    <span class="cell-label">Kode Booking (PNR)</span>
                    <strong class="cell-val text-purple" id="modal-pnr">TRV-2026-0925</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Nama Penumpang</span>
                    <strong class="cell-val" id="modal-passenger">Budi Prasetyo</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Kontak Pelanggan</span>
                    <span class="cell-val" id="modal-phone">0812-9988-7766</span>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Waktu Dilaporkan</span>
                    <span class="cell-val" id="modal-time">25 Sep 2026, 14:15 WIB</span>
                </div>
            </div>

            <!-- Root Cause & Supporting Data Panels -->
            <div class="detail-section-block">
                <h4 class="sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Akar Masalah (Root Cause)
                </h4>
                <div class="sec-box sec-box-danger" id="modal-root-cause">
                    Penumpang membatalkan perjalanan karena mendadak sakit opname di RS. Menuntut pengembalian dana 100% dengan surat keterangan dokter terlampir. CS tidak memiliki wewenang eksekusi refund keuangan.
                </div>
            </div>

            <div class="detail-section-block">
                <h4 class="sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Data Pendukung
                </h4>
                <div class="sec-box sec-box-gray" id="modal-supporting-data">
                    Rekening Pengembalian: BCA 5420998811 a/n Budi Prasetyo. Lampiran Surat Sakit No: 082/RS-SBY/IX/2026.
                </div>
            </div>

            <!-- Timeline Respon Admin & Tindak Lanjut -->
            <div class="detail-section-block">
                <h4 class="sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Kronologi &amp; Catatan Tindak Lanjut Admin
                </h4>
                <div class="admin-timeline" id="modal-timeline">
                    <!-- Dynamic timeline logs -->
                </div>
            </div>

            <!-- Follow-up message from CS to Admin -->
            <div class="detail-section-block">
                <h4 class="sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Kirim Pesan Follow-up ke Admin / Update Status Kasus
                </h4>
                <div class="followup-compose-box">
                    <textarea id="followup-note-input"
                              class="styled-textarea"
                              rows="2"
                              placeholder="Ketik catatan tambahan dari pelanggan atau tanyakan progres terkini ke Admin..."></textarea>
                    <div class="compose-btn-row">
                        <select id="quick-status-update" class="styled-select-mini">
                            <option value="">-- Tetap Status Saat Ini --</option>
                            <option value="Pending di Admin">Set Pending di Admin</option>
                            <option value="Diproses">Tandai Diproses</option>
                            <option value="Selesai">Tandai Selesai</option>
                        </select>
                        <button type="button" class="cs-btn cs-btn-primary" onclick="submitFollowUpNote()">
                            Kirim Follow-Up
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-card-footer">
            <button type="button" class="cs-btn cs-btn-secondary" onclick="closeTicketModal()">Tutup</button>
            <a id="modal-chat-link" href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-primary">
                Buka Chat Penumpang Ini
            </a>
        </div>
    </div>
</div>

<style>
/* ============================================================
   DESIGN SYSTEM & TOKENS (MATCHING ADMIN UTAMA)
   ============================================================ */
.cs-escalation-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding-bottom: 60px;
    font-family: inherit;
    color: #1e293b;
}

/* Header */
.esc-page-header {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.esc-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e3f5ee;
    color: #087f55;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    margin-bottom: 10px;
}
.header-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px 0;
    letter-spacing: -0.02em;
}
.header-subtitle {
    font-size: 0.92rem;
    color: #475569;
    line-height: 1.55;
    margin: 0;
    max-width: 820px;
}
.header-action-group {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
    align-items: center;
}

/* Common Buttons */
.cs-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid transparent;
}
.cs-btn-primary {
    background: #087f55;
    color: #ffffff;
}
.cs-btn-primary:hover {
    background: #066b47;
}
.cs-btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border-color: #cbd5e1;
}
.cs-btn-secondary:hover {
    background: #e2e8f0;
}
.cs-btn-danger {
    background: #e03131;
    color: #ffffff;
}
.cs-btn-danger:hover {
    background: #c92a2a;
}

/* 4 KPI Cards */
.esc-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
.esc-kpi-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    cursor: pointer;
    transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s;
}
.esc-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.kpi-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.icon-purple { background: #f3f0ff; color: #7048e8; }
.icon-amber  { background: #fff9db; color: #f59f00; }
.icon-blue   { background: #e7f5ff; color: #1971c2; }
.icon-green  { background: #e6fcf5; color: #087f55; }

.kpi-content {
    display: flex;
    flex-direction: column;
}
.kpi-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
}
.kpi-number {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin: 2px 0;
}
.text-amber { color: #f59f00 !important; }
.text-blue  { color: #1971c2 !important; }
.text-green { color: #087f55 !important; }
.kpi-desc {
    font-size: 0.74rem;
    color: #94a3b8;
}

/* Layout: 2 Columns (Form on left, Tracker on right) */
.esc-main-layout {
    display: grid;
    grid-template-columns: 460px 1fr;
    gap: 24px;
    align-items: flex-start;
}

/* Form Card */
.esc-form-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.card-header-styled {
    padding: 18px 22px;
    background: #fafcfb;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header-left-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.header-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #e3f5ee;
    color: #087f55;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.card-heading {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.card-subheading {
    font-size: 0.78rem;
    color: #64748b;
    margin: 2px 0 0 0;
}
.required-flag {
    font-size: 0.74rem;
    color: #e03131;
    font-weight: 700;
}

.esc-form-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.form-group-block {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.input-label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
}
.text-danger { color: #e03131; }
.styled-select, .styled-input, .styled-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 0.88rem;
    font-family: inherit;
    background: #ffffff;
    color: #0f172a;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
}
.styled-select:focus, .styled-input:focus, .styled-textarea:focus {
    outline: none;
    border-color: #087f55;
    box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
}
.input-hint {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.3;
}
.form-grid-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.input-with-icon .input-icon {
    position: absolute;
    left: 10px;
    font-size: 0.88rem;
    color: #64748b;
}
.input-with-icon .styled-input {
    padding-left: 34px;
}

/* Severity Radio Cards */
.severity-radio-group {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.severity-card {
    position: relative;
    cursor: pointer;
}
.severity-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.sev-card-inner {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: all 0.15s ease;
    height: 100%;
    box-sizing: border-box;
}
.sev-tag {
    font-size: 0.78rem;
    font-weight: 800;
}
.sev-time {
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
}
.sev-text {
    font-size: 0.68rem;
    color: #64748b;
    margin: 0;
    line-height: 1.25;
}

.sev-low .sev-tag { color: #2b8a3e; }
.sev-medium .sev-tag { color: #d97706; }
.sev-emergency .sev-tag { color: #e03131; }

.severity-card input[type="radio"]:checked + .sev-card-inner {
    border-color: #087f55;
    background: #f0fdf4;
    box-shadow: 0 0 0 2px rgba(8,127,85,0.18);
}
.sev-emergency input[type="radio"]:checked + .sev-card-inner {
    border-color: #e03131;
    background: #fff5f5;
    box-shadow: 0 0 0 2px rgba(224,49,49,0.18);
}

.file-dropzone {
    border: 1.5px dashed #cbd5e1;
    border-radius: 8px;
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.8rem;
    color: #475569;
    cursor: pointer;
    background: #f8fafc;
    transition: border-color 0.15s, background 0.15s;
}
.file-dropzone:hover {
    border-color: #087f55;
    background: #f0fdf4;
    color: #087f55;
}
.form-action-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 8px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}

/* Right Tracker Card */
.esc-tracker-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
}
.tracker-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}
.tracker-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.badge-tracker {
    background: #f3f0ff;
    color: #7048e8;
}
.search-mini-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.styled-search-input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px 8px 32px;
    font-size: 0.82rem;
    width: 240px;
    outline: none;
    transition: all 0.15s;
}
.styled-search-input:focus {
    border-color: #087f55;
    box-shadow: 0 0 0 2px rgba(8,127,85,0.12);
}
.search-mini-wrap svg {
    position: absolute;
    left: 10px;
    color: #94a3b8;
}

/* Filter Bar with Tabs */
.tracker-filter-bar {
    padding: 12px 22px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.filter-tabs {
    display: flex;
    gap: 6px;
}
.filter-tab {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.filter-tab:hover {
    background: #f1f5f9;
}
.filter-tab.active {
    background: #087f55;
    border-color: #087f55;
    color: #ffffff;
}
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.dot-amber { background: #f59f00; }
.dot-blue  { background: #1971c2; }
.dot-green { background: #087f55; }

.styled-select-mini {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.8rem;
    background: #ffffff;
    color: #334155;
    outline: none;
}

/* Ticket List Item */
.esc-ticket-list {
    display: flex;
    flex-direction: column;
    divide-y: 1px solid #f1f5f9;
    max-height: 720px;
    overflow-y: auto;
}
.ticket-row-item {
    padding: 18px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: background 0.15s;
}
.ticket-row-item:hover {
    background: #fbfcfc;
}
.ticket-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}
.ticket-id-tag {
    font-weight: 800;
    font-size: 0.92rem;
    color: #7048e8;
}
.ticket-meta-left {
    display: flex;
    align-items: center;
    gap: 8px;
}
.cat-badge {
    font-size: 0.74rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}
.cat-refund  { background: #fff4e6; color: #d9480f; }
.cat-armada  { background: #e7f5ff; color: #1864ab; }
.cat-komplain{ background: #fff5f5; color: #c92a2a; }

.sev-badge-pill {
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 6px;
}
.sev-badge-low       { background: #ebfbee; color: #2b8a3e; }
.sev-badge-medium    { background: #fff9db; color: #d97706; }
.sev-badge-emergency { background: #ffe3e3; color: #c92a2a; }

.status-badge-pill {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-pending-admin {
    background: #fff9db;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-process {
    background: #e7f5ff;
    color: #1c7ed6;
    border: 1px solid #bfdbfe;
}
.badge-done {
    background: #e6fcf5;
    color: #087f55;
    border: 1px solid #a7f3d0;
}

.ticket-passenger-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
}
.passenger-name-text {
    font-weight: 700;
    color: #0f172a;
}
.booking-pnr-text {
    font-family: monospace;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.78rem;
}
.ticket-time-text {
    font-size: 0.75rem;
    color: #94a3b8;
}

.ticket-root-preview {
    font-size: 0.82rem;
    color: #334155;
    line-height: 1.45;
    background: #f8fafc;
    border-left: 3px solid #cbd5e1;
    padding: 8px 12px;
    border-radius: 0 6px 6px 0;
}
.ticket-root-preview strong {
    color: #0f172a;
}

.ticket-bottom-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2px;
}
.admin-last-response {
    font-size: 0.75rem;
    color: #64748b;
}
.admin-last-response strong {
    color: #1e293b;
}
.btn-action-group {
    display: flex;
    gap: 8px;
}
.btn-open-detail {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.btn-open-detail:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Empty State */
.empty-tickets {
    padding: 50px 20px;
    text-align: center;
    color: #94a3b8;
}
.empty-icon {
    font-size: 2.2rem;
    margin-bottom: 8px;
}

/* Modal Styling */
.cs-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
    box-sizing: border-box;
}
.cs-modal-card {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 650px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
}
.cs-modal-card.modal-lg {
    max-width: 720px;
}
.modal-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}
.modal-icon-badge {
    font-size: 1.4rem;
}
.modal-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.modal-sub {
    font-size: 0.78rem;
    color: #64748b;
    margin: 2px 0 0 0;
}
.modal-close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
}
.modal-close-btn:hover {
    color: #0f172a;
}
.modal-card-body {
    padding: 22px 24px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.modal-card-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-radius: 0 0 14px 14px;
}

/* Detail status inside modal */
.detail-header-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 10px 16px;
    border-radius: 8px;
}
.dh-label {
    font-size: 0.78rem;
    color: #64748b;
    margin-right: 8px;
}
.detail-info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    background: #fafcfb;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
}
.info-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.cell-label {
    font-size: 0.7rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 700;
}
.cell-val {
    font-size: 0.85rem;
    color: #1e293b;
}

.detail-section-block {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.sec-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sec-box {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 0.84rem;
    line-height: 1.5;
}
.sec-box-danger {
    background: #fff5f5;
    border: 1px solid #ffc9c9;
    color: #b02525;
}
.sec-box-gray {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
}

/* Timeline */
.admin-timeline {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.tl-item {
    display: flex;
    gap: 12px;
    font-size: 0.82rem;
    position: relative;
}
.tl-bullet {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 4px;
    flex-shrink: 0;
}
.tl-bullet-green { background: #087f55; }
.tl-bullet-blue  { background: #1971c2; }
.tl-bullet-amber { background: #f59f00; }
.tl-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.tl-actor {
    font-weight: 700;
    color: #0f172a;
}
.tl-text {
    color: #475569;
}
.tl-time {
    font-size: 0.72rem;
    color: #94a3b8;
}

.followup-compose-box {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.compose-btn-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

/* Responsive adjustments */
@media (max-width: 1100px) {
    .esc-main-layout {
        grid-template-columns: 1fr;
    }
    .esc-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 680px) {
    .esc-kpi-grid {
        grid-template-columns: 1fr;
    }
    .esc-page-header {
        flex-direction: column;
    }
    .detail-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .severity-radio-group {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Mock initial escalations database matching PRD 4.5
const initialEscalations = [
    {
        id: 'ESK-104',
        category: 'Pengajuan Refund / Keuangan',
        severity: 'Medium',
        bookingCode: 'TRV-2026-0925',
        passengerName: 'Budi Prasetyo',
        passengerPhone: '0812-9988-7766',
        reportedAt: '15 menit lalu',
        status: 'Pending di Admin',
        rootCause: 'Penumpang membatalkan perjalanan karena mendadak sakit opname di RS. Menuntut pengembalian dana 100% dengan surat keterangan dokter terlampir. Melampaui wewenang CS garis depan.',
        supportingData: 'Rekening Pengembalian: BCA 5420998811 a/n Budi Prasetyo. Lampiran: Surat Sakit No. 082/RS-SBY/IX/2026.',
        adminResponse: 'Tiket diterima sistem Admin Utama. Menunggu verifikasi rekening oleh Tim Keuangan.',
        timeline: [
            { actor: 'CS Nadia', time: '14:15 WIB', text: 'Tiket eskalasi refund dibuat dan diteruskan ke Admin Utama.', type: 'amber' },
            { actor: 'Sistem Admin', time: '14:16 WIB', text: 'Notifikasi dialihkan ke antrean persetujuan Finansial.', type: 'blue' }
        ]
    },
    {
        id: 'ESK-103',
        category: 'Armada Rusak / Ganti Mobil (Operasional)',
        severity: 'High/Emergency',
        bookingCode: 'TRV-2026-0811',
        passengerName: 'Hendra Gunawan',
        passengerPhone: '0813-2233-4455',
        reportedAt: '42 menit lalu',
        status: 'Diproses',
        rootCause: 'Minibus D 8911 JKT mengalami kebocoran radiator di Rest Area KM 72 Cipularang. Penumpang 11 orang tertahan. Diperlukan keputusan dispatching armada pengganti darurat dari Pool Purwakarta.',
        supportingData: 'Plat Armada: D 8911 JKT, Driver: Pak Yayan (0812-3344-5566). Titik mogok: Tol Cipularang KM 72A.',
        adminResponse: 'Admin Operasional mengirim armada pengganti D 7712 BD dari Pool terdekat.',
        timeline: [
            { actor: 'CS Ahmad', time: '13:48 WIB', text: 'Tiket darurat dibuat dengan level High/Emergency.', type: 'amber' },
            { actor: 'Admin Operasional (Pak Danang)', time: '13:55 WIB', text: 'Unit pengganti D 7712 BD diberangkatkan menuju KM 72, estimasi tiba 25 menit.', type: 'blue' }
        ]
    },
    {
        id: 'ESK-102',
        category: 'Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)',
        severity: 'High/Emergency',
        bookingCode: 'TRV-2026-0774',
        passengerName: 'Siti Rahma',
        passengerPhone: '0878-1122-3344',
        reportedAt: '1.5 jam lalu',
        status: 'Diproses',
        rootCause: 'Sopir dilaporkan mengemudi ugal-ugalan dan merokok aktif di dalam kabin ber-AC sambil memainkan HP di jalan tol. Penumpang komplain ketakutan dan minta ganti supir saat berhenti.',
        supportingData: 'Plat Armada: B 7001 JKT, Driver terlapor: Sdr. Anton, Video rekaman berdurasi 18 detik dikirim penumpang via WhatsApp.',
        adminResponse: 'Admin Utama sedang menghubungi koordinator driver rest area untuk pergantian driver cadangan.',
        timeline: [
            { actor: 'CS Rian', time: '13:00 WIB', text: 'Eskalasi komplain keselamatan penumpang diteruskan ke Admin Utama.', type: 'amber' },
            { actor: 'Admin Utama', time: '13:12 WIB', text: 'Panggilan investigasi ke Driver Anton, instruksi berhenti di rest area berikutnya.', type: 'blue' }
        ]
    },
    {
        id: 'ESK-101',
        category: 'Pengajuan Refund / Keuangan',
        severity: 'Low',
        bookingCode: 'TRV-2026-0610',
        passengerName: 'Dimas Anggara',
        passengerPhone: '0857-4455-6677',
        reportedAt: '3 jam lalu',
        status: 'Pending di Admin',
        rootCause: 'Kelebihan transfer pembayaran manual double transfer senilai Rp 150.000. Pelanggan meminta refund selisih pembayaran.',
        supportingData: 'Bukti transfer mutasi ganda BCA tertanggal 25 Sep 2026 pukul 10:00 WIB senilai Rp 150.000 x 2.',
        adminResponse: 'Menunggu rekap harian finance pukul 17:00 WIB untuk approval batch transfer.',
        timeline: [
            { actor: 'CS Nadia', time: '11:30 WIB', text: 'Tiket pengajuan pengembalian kelebihan dana disubmit ke Admin.', type: 'amber' }
        ]
    },
    {
        id: 'ESK-100',
        category: 'Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)',
        severity: 'Medium',
        bookingCode: 'TRV-2026-0599',
        passengerName: 'Dewi Lestari',
        passengerPhone: '0819-8877-6655',
        reportedAt: '5 jam lalu',
        status: 'Pending di Admin',
        rootCause: 'Koper kabin warna merah tertinggal di bagasi mobil rute Bandung - Gambir. Penumpang baru menyadari setelah sampai di rumah.',
        supportingData: 'Armada D 1022 KNT, Driver Sdr. Bambang. Ciri koper: Polo Twin merah ada gantungan kunci boneka beruang.',
        adminResponse: 'Admin menghubungi pool Gambir untuk cek gudang lost and found.',
        timeline: [
            { actor: 'CS Ahmad', time: '09:30 WIB', text: 'Laporan barang hilang diteruskan ke Admin Operasional.', type: 'amber' }
        ]
    },
    {
        id: 'ESK-099',
        category: 'Armada Rusak / Ganti Mobil (Operasional)',
        severity: 'Medium',
        bookingCode: 'TRV-2026-0412',
        passengerName: 'Fajar Nugraha',
        passengerPhone: '0821-3344-9988',
        reportedAt: '1 hari lalu',
        status: 'Selesai',
        rootCause: 'AC armada D 9901 BD mati di tengah jalan saat siang terik. Penumpang meminta kompensasi voucher potongan tiket.',
        supportingData: 'Tiket TRV-2026-0412, Armada D 9901 BD. Disetujui voucher diskon 50% untuk perjalanan berikutnya.',
        adminResponse: 'Admin Keuangan & Operasional telah menyetujui voucher kompensasi senilai Rp 75.000 terkirim ke WhatsApp pelanggan.',
        timeline: [
            { actor: 'CS Rian', time: '24 Sep, 10:15 WIB', text: 'Laporan AC rusak dan permohonan voucher dikirim ke Admin.', type: 'amber' },
            { actor: 'Admin Utama', time: '24 Sep, 11:00 WIB', text: 'Voucher TRV-DISC50 diterbitkan dan tiket ditutup selesai.', type: 'green' }
        ]
    }
];

let escalations = [...initialEscalations];
let currentFilterStatus = 'all';
let activeModalTicketId = null;

document.addEventListener('DOMContentLoaded', () => {
    updateKPIs();
    renderTickets();
});

function updateKPIs() {
    const total = escalations.length;
    const pending = escalations.filter(t => t.status === 'Pending di Admin').length;
    const process = escalations.filter(t => t.status === 'Diproses').length;
    const done = escalations.filter(t => t.status === 'Selesai').length;

    document.getElementById('count-total').textContent = total;
    document.getElementById('count-pending').textContent = pending;
    document.getElementById('count-process').textContent = process;
    document.getElementById('count-done').textContent = done;

    document.getElementById('tab-count-all').textContent = total;
    document.getElementById('tab-count-pending').textContent = pending;
    document.getElementById('tab-count-process').textContent = process;
    document.getElementById('tab-count-done').textContent = done;
}

function filterByStatus(status) {
    currentFilterStatus = status;

    // Update active tab buttons
    document.querySelectorAll('.filter-tab').forEach(tab => {
        if (tab.dataset.status === status) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    renderTickets();
}

function filterTickets() {
    renderTickets();
}

function renderTickets() {
    const container = document.getElementById('ticket-items-container');
    const searchQuery = (document.getElementById('search-tracker')?.value || '').toLowerCase().trim();
    const categoryFilter = document.getElementById('filter-category')?.value || 'all';

    let filtered = escalations.filter(ticket => {
        // Status filter
        if (currentFilterStatus !== 'all' && ticket.status !== currentFilterStatus) {
            return false;
        }
        // Category filter
        if (categoryFilter !== 'all') {
            if (categoryFilter === 'Komplain Khusus' && !ticket.category.includes('Komplain Khusus')) {
                return false;
            } else if (categoryFilter !== 'Komplain Khusus' && ticket.category !== categoryFilter) {
                return false;
            }
        }
        // Search filter
        if (searchQuery) {
            const haystack = `${ticket.id} ${ticket.bookingCode} ${ticket.passengerName} ${ticket.rootCause} ${ticket.supportingData}`.toLowerCase();
            if (!haystack.includes(searchQuery)) return false;
        }
        return true;
    });

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-tickets">
                <div class="empty-icon">📭</div>
                <h4>Tidak ada tiket eskalasi ditemukan</h4>
                <p>Coba ubah kata kunci pencarian atau ganti filter status di atas.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(t => {
        let catClass = 'cat-refund';
        if (t.category.includes('Armada')) catClass = 'cat-armada';
        if (t.category.includes('Komplain')) catClass = 'cat-komplain';

        let sevClass = 'sev-badge-medium';
        if (t.severity === 'Low') sevClass = 'sev-badge-low';
        if (t.severity === 'High/Emergency') sevClass = 'sev-badge-emergency';

        let statusClass = 'badge-pending-admin';
        if (t.status === 'Diproses') statusClass = 'badge-process';
        if (t.status === 'Selesai') statusClass = 'badge-done';

        return `
            <div class="ticket-row-item">
                <div class="ticket-top-row">
                    <div class="ticket-meta-left">
                        <span class="ticket-id-tag">#${t.id}</span>
                        <span class="cat-badge ${catClass}">${t.category}</span>
                        <span class="sev-badge-pill ${sevClass}">Prioritas: ${t.severity}</span>
                    </div>
                    <span class="status-badge-pill ${statusClass}">
                        <span class="status-dot ${t.status === 'Selesai' ? 'dot-green' : (t.status === 'Diproses' ? 'dot-blue' : 'dot-amber')}"></span>
                        ${t.status}
                    </span>
                </div>

                <div class="ticket-passenger-row">
                    <div>
                        <span class="passenger-name-text">${t.passengerName}</span>
                        <span class="booking-pnr-text">${t.bookingCode}</span>
                        <span style="font-size:0.75rem; color:#64748b; margin-left:6px;">(${t.passengerPhone})</span>
                    </div>
                    <span class="ticket-time-text">Dilaporkan: ${t.reportedAt}</span>
                </div>

                <div class="ticket-root-preview">
                    <strong>Akar Masalah:</strong> ${t.rootCause}
                </div>

                <div class="ticket-bottom-actions">
                    <div class="admin-last-response">
                        <strong>Tindak Lanjut Admin:</strong> ${t.adminResponse || 'Menunggu verifikasi admin.'}
                    </div>
                    <div class="btn-action-group">
                        <button type="button" class="btn-open-detail" onclick="openTicketDetail('${t.id}')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Lihat Respon &amp; Detail</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function handleCreateEscalation(e) {
    e.preventDefault();

    const category = document.getElementById('category-select').value;
    const severity = document.querySelector('input[name="severity_level"]:checked')?.value || 'Medium';
    const bookingCode = document.getElementById('booking-code-input').value.trim();
    const passengerInfo = document.getElementById('passenger-name-input').value.trim();
    const rootCause = document.getElementById('root-cause-input').value.trim();
    const supportingData = document.getElementById('supporting-data-input').value.trim();

    if (!category || !bookingCode || !passengerInfo || !rootCause || !supportingData) {
        alert('Mohon lengkapi semua kolom wajib (Kategori, Booking, Penumpang, Akar Masalah, dan Data Pendukung).');
        return;
    }

    // Generate new ticket
    const newId = `ESK-${105 + (escalations.length - 6)}`;
    const newTicket = {
        id: newId,
        category: category,
        severity: severity,
        bookingCode: bookingCode,
        passengerName: passengerInfo,
        passengerPhone: '0812-xxxx-xxxx',
        reportedAt: 'Baru saja',
        status: 'Pending di Admin',
        rootCause: rootCause,
        supportingData: supportingData,
        adminResponse: 'Tiket baru terkirim ke panel Admin Utama. Status: Pending di Admin.',
        timeline: [
            {
                actor: 'CS {{ auth()->user()->name ?? "Aktif" }}',
                time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' WIB',
                text: `Tiket eskalasi kategori "${category}" berhasil diajukan dengan prioritas ${severity}.`,
                type: 'amber'
            }
        ]
    };

    escalations.unshift(newTicket);
    updateKPIs();
    renderTickets();

    // Reset form
    document.getElementById('escalation-form').reset();
    document.getElementById('file-label-text').textContent = 'Klik untuk pilih berkas bukti / screenshot percakapan';
    resetFormHint();

    alert(`Tiket eskalasi #${newId} untuk booking ${bookingCode} BERHASIL dikirim ke Admin Utama!\nStatus pelacakan: Pending di Admin.`);

    // Scroll to tracker
    document.getElementById('pelacakan-status-section').scrollIntoView({ behavior: 'smooth' });
}

function onCategoryChange() {
    const cat = document.getElementById('category-select').value;
    const hint = document.getElementById('category-hint');
    if (cat === 'Pengajuan Refund / Keuangan') {
        hint.innerHTML = '<span class="text-amber">⚠️ Otorisasi Keuangan: Pastikan nomor rekening &amp; bank tujuan refund ditulis lengkap di kolom data pendukung.</span>';
    } else if (cat === 'Armada Rusak / Ganti Mobil (Operasional)') {
        hint.innerHTML = '<span class="text-blue">⚠️ Otorisasi Operasional: Sertakan plat mobil, titik mogok, dan jumlah penumpang untuk dispatch armada pengganti.</span>';
    } else if (cat.includes('Komplain Khusus')) {
        hint.innerHTML = '<span class="text-danger">⚠️ Penanganan Khusus: Masukkan kronologi jelas, nomor polisi, nama sopir, atau saksi kejadian.</span>';
    } else {
        hint.textContent = 'Pilih salah satu dari 3 ranah kewenangan otorisasi Admin Utama.';
    }
}

function resetFormHint() {
    document.getElementById('category-hint').textContent = 'Pilih salah satu dari 3 ranah kewenangan otorisasi Admin Utama.';
}

function onFileSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('file-label-text').textContent = `Berkas terpilih: ${input.files[0].name} (${(input.files[0].size/1024).toFixed(1)} KB)`;
    }
}

function scrollToForm() {
    document.getElementById('form-eskalasi-section').scrollIntoView({ behavior: 'smooth' });
    document.getElementById('booking-code-input').focus();
}

// Modal functions
function openTicketDetail(ticketId) {
    const ticket = escalations.find(t => t.id === ticketId);
    if (!ticket) return;

    activeModalTicketId = ticketId;

    document.getElementById('modal-detail-title').textContent = `Detail Kasus Eskalasi #${ticket.id}`;
    document.getElementById('modal-detail-sub').textContent = `${ticket.category} • Dilaporkan ${ticket.reportedAt}`;

    const statusPill = document.getElementById('modal-status-pill');
    statusPill.textContent = ticket.status;
    statusPill.className = 'status-badge-pill ' + (ticket.status === 'Selesai' ? 'badge-done' : (ticket.status === 'Diproses' ? 'badge-process' : 'badge-pending-admin'));

    const sevPill = document.getElementById('modal-severity-badge');
    sevPill.textContent = ticket.severity;
    sevPill.className = 'sev-badge-pill ' + (ticket.severity === 'Low' ? 'sev-badge-low' : (ticket.severity === 'High/Emergency' ? 'sev-badge-emergency' : 'sev-badge-medium'));

    document.getElementById('modal-pnr').textContent = ticket.bookingCode;
    document.getElementById('modal-passenger').textContent = ticket.passengerName;
    document.getElementById('modal-phone').textContent = ticket.passengerPhone;
    document.getElementById('modal-time').textContent = ticket.reportedAt;

    document.getElementById('modal-root-cause').textContent = ticket.rootCause;
    document.getElementById('modal-supporting-data').textContent = ticket.supportingData;

    // Render timeline
    const timelineEl = document.getElementById('modal-timeline');
    timelineEl.innerHTML = (ticket.timeline || []).map(tl => `
        <div class="tl-item">
            <span class="tl-bullet ${tl.type === 'green' ? 'tl-bullet-green' : (tl.type === 'blue' ? 'tl-bullet-blue' : 'tl-bullet-amber')}"></span>
            <div class="tl-content">
                <div><span class="tl-actor">${tl.actor}</span> <span class="tl-time">(${tl.time})</span></div>
                <div class="tl-text">${tl.text}</div>
            </div>
        </div>
    `).join('');

    document.getElementById('modal-ticket-detail').style.display = 'flex';
}

function closeTicketModal() {
    document.getElementById('modal-ticket-detail').style.display = 'none';
    activeModalTicketId = null;
    document.getElementById('followup-note-input').value = '';
}

function submitFollowUpNote() {
    if (!activeModalTicketId) return;

    const note = document.getElementById('followup-note-input').value.trim();
    const newStatus = document.getElementById('quick-status-update').value;

    const ticket = escalations.find(t => t.id === activeModalTicketId);
    if (!ticket) return;

    if (!note && !newStatus) {
        alert('Tulis catatan follow-up atau pilih status baru.');
        return;
    }

    if (newStatus) {
        ticket.status = newStatus;
    }

    const nowStr = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' WIB';

    if (note) {
        ticket.timeline.push({
            actor: 'CS Follow-Up',
            time: nowStr,
            text: note,
            type: 'blue'
        });
        ticket.adminResponse = `Follow up terbaru CS: "${note}"`;
    }

    if (newStatus) {
        ticket.timeline.push({
            actor: 'Status Update',
            time: nowStr,
            text: `Status kasus diubah menjadi "${newStatus}".`,
            type: newStatus === 'Selesai' ? 'green' : 'blue'
        });
    }

    updateKPIs();
    renderTickets();
    openTicketDetail(ticket.id); // refresh modal

    alert('Catatan follow-up & status berhasil diperbarui!');
    document.getElementById('followup-note-input').value = '';
}
</script>
@endsection
