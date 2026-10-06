@extends('layouts.customer-service')

@section('title', 'Mengirim Gambar & File - Customer Service')

@section('content')
<div class="cs-send-file-wrapper">

    <!-- Top Hub Header -->
    <div class="sf-page-header">
        <div class="header-info">
            <div class="sf-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                </svg>
                <span>Validasi Dokumen, Kompresi Media &amp; Tiket Instan</span>
            </div>
            <h1 class="header-title">Mengirim Gambar / File</h1>
            <p class="header-subtitle">
                Dukungan lampiran media untuk validasi dokumen dan bukti operasional. Format yang didukung: Gambar (JPG, PNG) dan dokumen (PDF) dengan kompresi otomatis untuk hemat kuota pelanggan saat koneksi internet di jalan tidak stabil, serta generator tiket instan.
            </p>
        </div>

        <div class="header-action-group">
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Buka Live Chat</span>
            </a>
            <a href="{{ route('dashboard.customer-service.booking-context') }}" class="cs-btn cs-btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                </svg>
                <span>Melihat Konteks Booking</span>
            </a>
        </div>
    </div>

    <!-- 4 Fitur Utama Highlight Cards -->
    <div class="sf-feature-cards-grid">
        <div class="sf-feature-card">
            <div class="feat-icon feat-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
            <div class="feat-text">
                <span class="feat-title">Format yang Didukung</span>
                <span class="feat-desc">Gambar (<strong>JPG, PNG</strong>) dan dokumen (<strong>PDF</strong>) resmi maks 5MB</span>
            </div>
        </div>

        <div class="sf-feature-card">
            <div class="feat-icon feat-green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </div>
            <div class="feat-text">
                <span class="feat-title">Kompresi Otomatis</span>
                <span class="feat-desc">Gambar otomatis diperkecil agar pengiriman cepat &amp; hemat kuota di jalan</span>
            </div>
        </div>

        <div class="sf-feature-card">
            <div class="feat-icon feat-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                    <circle cx="6" cy="12" r="2"></circle>
                    <circle cx="18" cy="12" r="2"></circle>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
            </div>
            <div class="feat-text">
                <span class="feat-title">Generator Tiket Instan</span>
                <span class="feat-desc">Tombol 1-klik melampirkan e-tiket PDF/Gambar resmi langsung ke chat</span>
            </div>
        </div>

        <div class="sf-feature-card">
            <div class="feat-icon feat-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div class="feat-text">
                <span class="feat-title">Validasi Operasional</span>
                <span class="feat-desc">Verifikasi bukti transfer, foto bagasi lansia, KTP &amp; surat jalan armada</span>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="sf-main-grid">

        <!-- KOLOM 1: Uploader Media & Simulasi Kompresi Otomatis (Left Column) -->
        <div class="sf-column-card">
            <div class="column-card-header">
                <div class="card-title-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="title-icon-brand">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <h2 class="card-title">Unggah Media &amp; Kompresi Otomatis</h2>
                </div>
                <span class="card-badge-pill">JPG, PNG, PDF</span>
            </div>

            <!-- Kategori Validasi Dokumen -->
            <div class="doc-category-bar">
                <label class="section-sublabel">Pilih Jenis Validasi Operasional:</label>
                <div class="category-chips-group">
                    <button type="button" class="cat-chip active" onclick="setDocCategory('transfer', this)">Bukti Transfer / Rekening</button>
                    <button type="button" class="cat-chip" onclick="setDocCategory('ktp', this)">Identitas (KTP / SIM)</button>
                    <button type="button" class="cat-chip" onclick="setDocCategory('bagasi', this)">Kondisi Bagasi / Lansia</button>
                    <button type="button" class="cat-chip" onclick="setDocCategory('operasional', this)">Surat Jalan Armada</button>
                </div>
            </div>

            <!-- Dropzone Area -->
            <div class="sf-dropzone" id="sf-dropzone" onclick="triggerFileInput()">
                <input type="file" id="sf-file-input" accept=".jpg,.jpeg,.png,.pdf" onchange="handleHubFileUpload(event)" hidden>
                <div class="dropzone-icon-wrap">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                </div>
                <h3 class="dropzone-title">Tarik &amp; Lepaskan file ke sini, atau <span class="highlight-browse">Pilih File</span></h3>
                <p class="dropzone-sub">Format yang didukung: <strong>Gambar (JPG, PNG)</strong> dan <strong>Dokumen (PDF)</strong> maks. 5MB</p>
                <div class="dropzone-tags">
                    <span class="tag-pill">JPG</span>
                    <span class="tag-pill">PNG</span>
                    <span class="tag-pill tag-pdf">PDF</span>
                    <span class="tag-pill tag-opt"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>Kompresi Otomatis Aktif</span>
                </div>
            </div>

            <!-- Sampel Cepat untuk Pengujian CS -->
            <div class="quick-samples-bar">
                <span class="samples-title">Atau gunakan file simulasi cepat:</span>
                <div class="samples-buttons">
                    <button type="button" class="sample-btn" onclick="loadSampleFile('transfer')">
                        <svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Bukti-Transfer-BCA.jpg (3.4 MB)
                    </button>
                    <button type="button" class="sample-btn" onclick="loadSampleFile('bagasi')">
                        <svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Foto-Koper-Lansia.png (2.8 MB)
                    </button>
                    <button type="button" class="sample-btn" onclick="loadSampleFile('surat')">
                        <svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Surat-Jalan-HiAce-Pasteur.pdf (185 KB)
                    </button>
                </div>
            </div>

            <!-- Panel Hasil Kompresi Otomatis (Live Compression Result) -->
            <div class="compression-result-card" id="compression-result-box">
                <div class="comp-header">
                    <div class="comp-title-group">
                        <span class="comp-badge-live"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>Hasil Kompresi Otomatis</span>
                        <strong id="comp-file-name">Bukti-Transfer-BCA.jpg</strong>
                    </div>
                    <span class="comp-ratio-badge" id="comp-ratio-badge">Hemat Kuota 89%</span>
                </div>

                <div class="comp-metrics-row">
                    <div class="metric-block">
                        <span class="metric-lbl">Ukuran Asli File:</span>
                        <span class="metric-val text-muted" id="comp-size-original">3.40 MB</span>
                    </div>
                    <div class="metric-arrow">&rarr;</div>
                    <div class="metric-block">
                        <span class="metric-lbl">Ukuran Terkompresi:</span>
                        <span class="metric-val text-brand" id="comp-size-result">374 KB</span>
                    </div>
                    <div class="metric-block metric-benefit">
                        <span class="metric-lbl">Kecepatan Kirim:</span>
                        <span class="metric-val text-success" id="comp-speed-benefit">9x Lebih Cepat di Jalan</span>
                    </div>
                </div>

                <div class="comp-explanation">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="info-icon-green"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span>Kompresi otomatis mempertahankan ketajaman teks/angka rekening namun memangkas byte metadata gambar agar pelanggan di perjalanan tidak kehabisan kuota atau gagal unduh saat sinyal 3G/Edge.</span>
                </div>

                <!-- Pilihan Tujuan Kirim Chat Pelanggan -->
                <div class="send-target-row">
                    <div class="target-select-wrap">
                        <label class="target-label">Kirim ke Sesi Live Chat Penumpang:</label>
                        <select id="sf-target-chat-select" class="form-select-cs">
                            <option value="1">Budi Prasetyo &bull; TRV-2026-0925 (Bandung &rarr; Jakarta 14:00)</option>
                            <option value="4">Dewi Lestari &bull; TRV-2026-0612 (Bekasi &rarr; Bandung 13:00 - Lansia)</option>
                            <option value="2">Hendra Gunawan &bull; TRV-2026-0811 (Jakarta &rarr; Bandung 13:45)</option>
                            <option value="3">Siti Rahma &bull; TRV-2026-0774 (Bandung &rarr; Soetta 15:30)</option>
                        </select>
                    </div>
                    <button type="button" class="cs-btn cs-btn-primary" onclick="dispatchFileToChat()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        <span>Kirim File Terkompresi ke Chat</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- KOLOM 2: Generator Tiket Instan (Right Column) -->
        <div class="sf-column-card">
            <div class="column-card-header">
                <div class="card-title-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="title-icon-amber">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <circle cx="6" cy="12" r="2"></circle>
                        <circle cx="18" cy="12" r="2"></circle>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                    </svg>
                    <h2 class="card-title">Generator Tiket Instan (1-Klik)</h2>
                </div>
                <span class="card-badge-pill pill-amber">PDF &amp; Gambar Resmi</span>
            </div>

            <p class="section-guide-text">
                Tombol satu klik untuk langsung melampirkan e-tiket berformat PDF/gambar resmi ke dalam ruang chat tanpa perlu mencari atau mengunggah ulang file secara manual.
            </p>

            <!-- Pilih Tiket Booking Pelanggan -->
            <div class="ticket-selector-group">
                <label class="target-label">Pilih Tiket Pemesanan Aktif:</label>
                <select id="ticket-source-select" class="form-select-cs" onchange="updateTicketPreview(this.value)">
                    <option value="1">TRV-2026-0925 &bull; Budi Prasetyo (Bandung &rarr; Jakarta 14:00 WIB)</option>
                    <option value="4">TRV-2026-0612 &bull; Dewi Lestari (Bekasi &rarr; Bandung 13:00 WIB)</option>
                    <option value="2">TRV-2026-0811 &bull; Hendra Gunawan (Jakarta &rarr; Bandung 13:45 WIB)</option>
                    <option value="3">TRV-2026-0774 &bull; Siti Rahma (Bandung &rarr; Soetta T3 15:30 WIB)</option>
                </select>
            </div>

            <!-- Visual Kartu E-Tiket Instan Resmi -->
            <div class="instant-ticket-card-visual" id="ticket-visual-preview">
                <div class="ticket-visual-header">
                    <div class="ticket-brand">
                        <div class="ticket-brand-circle">TK</div>
                        <div>
                            <span class="ticket-company-title">TravelKu Indonesia</span>
                            <span class="ticket-doc-type">Official Boarding Pass &amp; E-Ticket</span>
                        </div>
                    </div>
                    <div class="ticket-status-stamp">LUNAS &bull; TERKONFIRMASI</div>
                </div>

                <div class="ticket-visual-body">
                    <div class="ticket-meta-grid">
                        <div class="t-cell">
                            <span class="t-lbl">KODE BOOKING (PNR)</span>
                            <strong class="t-val-code" id="t-code">TRV-2026-0925</strong>
                        </div>
                        <div class="t-cell">
                            <span class="t-lbl">NAMA PENUMPANG</span>
                            <strong class="t-val" id="t-passenger">Budi Prasetyo</strong>
                        </div>
                        <div class="t-cell">
                            <span class="t-lbl">RUTE PERJALANAN</span>
                            <strong class="t-val text-brand" id="t-route">Bandung (Pasteur) &rarr; Jakarta (Semanggi)</strong>
                        </div>
                        <div class="t-cell">
                            <span class="t-lbl">WAKTU KEBERANGKATAN</span>
                            <strong class="t-val" id="t-departure">Jumat, 25 Sep 2026 &bull; 14:00 WIB</strong>
                        </div>
                        <div class="t-cell">
                            <span class="t-lbl">ARMADA &amp; PLAT NOMOR</span>
                            <strong class="t-val" id="t-vehicle">Toyota HiAce Premio (D 7182 AB)</strong>
                        </div>
                        <div class="t-cell">
                            <span class="t-lbl">NOMOR KURSI</span>
                            <strong class="t-val-seat" id="t-seat">03A (Single Window)</strong>
                        </div>
                    </div>

                    <!-- Barcode & QR Simulation -->
                    <div class="ticket-barcode-strip">
                        <div class="barcode-lines">
                            <span>||| | |||| | ||| |||| | ||||| | || |||| | |||</span>
                            <span class="barcode-code" id="t-barcode-num">TRV9250926887182</span>
                        </div>
                        <div class="qr-mock-box">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h4v4h-4v-4zm-4 0h2v2h-2v-2zm0 4h2v4h-2v-4zm4 2h2v2h-2v-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="ticket-visual-footer">
                    <span class="footer-note">Ukuran File: <strong>124 KB (Format PDF Resmi Terverifikasi)</strong></span>
                    <span class="footer-security"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Dilengkapi QR Validasi Pool</span>
                </div>
            </div>

            <!-- Opsi Format & Tombol Aksi 1-Klik -->
            <div class="ticket-actions-bar">
                <div class="format-choice-tabs">
                    <button type="button" class="format-tab active" id="tab-fmt-pdf" onclick="setTicketFormat('pdf')">
                        <svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Format PDF (Resmi)
                    </button>
                    <button type="button" class="format-tab" id="tab-fmt-img" onclick="setTicketFormat('image')">
                        <svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Format Gambar PNG
                    </button>
                </div>

                <div class="ticket-instant-btns">
                    <button type="button" class="cs-btn cs-btn-amber" id="btn-generate-instant" onclick="sendInstantTicketToChat()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 2L11 13"></path>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <span id="btn-ticket-send-label">Kirim E-Tiket Instan (PDF) ke Chat</span>
                    </button>
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="downloadInstantTicket()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh File</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Riwayat Lampiran Media & Dokumen yang Pernah Dikirim -->
    <div class="sf-history-table-card">
        <div class="table-card-header">
            <h3 class="table-card-title">Riwayat Lampiran Media &amp; Dokumen Operasional Terkirim</h3>
            <span class="table-count-badge">Terakhir Diperbarui: Hari Ini</span>
        </div>

        <div class="table-responsive">
            <table class="sf-table">
                <thead>
                    <tr>
                        <th>Waktu &bull; CS Bertugas</th>
                        <th>Penerima / Sesi Chat</th>
                        <th>Nama File &amp; Format</th>
                        <th>Jenis Dokumen</th>
                        <th>Kompresi &bull; Efisiensi Kuota</th>
                        <th>Status Pengiriman</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="sf-history-tbody">
                    <tr>
                        <td>13:16 WIB &bull; <strong>CS Anda</strong></td>
                        <td>Budi Prasetyo (TRV-2026-0925)</td>
                        <td><span class="file-link-pill"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>E-Tiket-TRV-2026-0925.pdf</span></td>
                        <td><span class="badge-cat-ticket">E-Tiket Resmi</span></td>
                        <td><span class="badge-comp-opt">124 KB (PDF Siap Cetak)</span></td>
                        <td><span class="badge-sent-ok">✓ Diterima Penumpang</span></td>
                        <td>
                            <button type="button" class="btn-table-action" onclick="alert('Mengunduh e-tiket PDF...')">Unduh</button>
                        </td>
                    </tr>
                    <tr>
                        <td>12:42 WIB &bull; <strong>CS Sarah</strong></td>
                        <td>Dewi Lestari (TRV-2026-0612)</td>
                        <td><span class="file-link-pill"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Foto-Titik-Kumpul-Pasteur.jpg</span></td>
                        <td><span class="badge-cat-photo">Panduan Pool</span></td>
                        <td><span class="badge-comp-opt">3.8 MB &rarr; 380 KB (-90%)</span></td>
                        <td><span class="badge-sent-ok">✓ Diterima Penumpang</span></td>
                        <td>
                            <button type="button" class="btn-table-action" onclick="alert('Membuka foto panduan...')">Pratinjau</button>
                        </td>
                    </tr>
                    <tr>
                        <td>11:05 WIB &bull; <strong>CS Budi</strong></td>
                        <td>Anisa Melani (TRV-2026-0421)</td>
                        <td><span class="file-link-pill"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Bukti-Transfer-Refund-BCA.pdf</span></td>
                        <td><span class="badge-cat-transfer">Bukti Rekening</span></td>
                        <td><span class="badge-comp-opt">210 KB (PDF Dokumen)</span></td>
                        <td><span class="badge-sent-ok">✓ Diterima Penumpang</span></td>
                        <td>
                            <button type="button" class="btn-table-action" onclick="alert('Mengunduh bukti refund...')">Unduh</button>
                        </td>
                    </tr>
                    <tr>
                        <td>10:40 WIB &bull; <strong>CS Nadia</strong></td>
                        <td>Siti Rahma (TRV-2026-0774)</td>
                        <td><span class="file-link-pill"><svg style="display:inline-block; vertical-align:-2px; margin-right:4px;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Foto-Bagasi-Koper-28inch.png</span></td>
                        <td><span class="badge-cat-photo">Validasi Bagasi</span></td>
                        <td><span class="badge-comp-opt">2.4 MB &rarr; 290 KB (-88%)</span></td>
                        <td><span class="badge-sent-ok">✓ Diterima Penumpang</span></td>
                        <td>
                            <button type="button" class="btn-table-action" onclick="alert('Membuka foto koper...')">Pratinjau</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('styles')
<style>
    /* DESIGN TOKENS */
    :root {
        --brand: #087f55;
        --brand-dark: #066b47;
        --brand-light: #e3f5ee;
        --page: #f5f8f7;
        --surface: #ffffff;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 14px;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .cs-send-file-wrapper {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding-bottom: 40px;
    }

    /* Header */
    .sf-page-header {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: var(--shadow-sm);
    }

    .sf-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 99px;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .header-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 4px 0;
    }

    .header-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        max-width: 820px;
        line-height: 1.5;
    }

    .header-action-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    /* Buttons */
    .cs-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: 0.15s ease;
        border: none;
        font-family: inherit;
    }

    .cs-btn-primary { background: var(--brand); color: white; }
    .cs-btn-primary:hover { background: var(--brand-dark); color: white; }
    .cs-btn-secondary { background: var(--page); color: var(--text-secondary); border: 1px solid var(--border); }
    .cs-btn-secondary:hover { background: #e2e8f0; color: var(--text); }
    .cs-btn-amber { background: #eab308; color: #713f12; border: 1px solid #ca8a04; }
    .cs-btn-amber:hover { background: #ca8a04; color: white; }

    /* 4 Feature Cards */
    .sf-feature-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .sf-feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: var(--shadow-sm);
    }

    .feat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feat-blue { background: #dbeafe; color: #1d4ed8; }
    .feat-green { background: #dcfce7; color: #15803d; }
    .feat-amber { background: #fef3c7; color: #b45309; }
    .feat-purple{ background: #f3e8ff; color: #7e22ce; }

    .feat-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .feat-title {
        font-size: 13px;
        font-weight: 800;
        color: var(--text);
    }

    .feat-desc {
        font-size: 11px;
        color: var(--text-secondary);
        line-height: 1.35;
    }

    /* Main Grid */
    .sf-main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        align-items: flex-start;
    }

    .sf-column-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .column-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .card-title-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .title-icon-brand { color: var(--brand); }
    .title-icon-amber { color: #d97706; }

    .card-badge-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 99px;
        background: var(--brand-light);
        color: var(--brand-dark);
    }

    .pill-amber {
        background: #fef08a;
        color: #854d0e;
    }

    .section-guide-text {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
    }

    .section-sublabel {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 6px;
    }

    /* Category Chips */
    .category-chips-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .cat-chip {
        padding: 5px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--page);
        color: var(--text-secondary);
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .cat-chip:hover { border-color: var(--brand); color: var(--brand); }
    .cat-chip.active { background: var(--brand); border-color: var(--brand-dark); color: white; }

    /* Dropzone */
    .sf-dropzone {
        border: 2px dashed #94a3b8;
        border-radius: var(--radius-md);
        padding: 28px 18px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .sf-dropzone:hover {
        border-color: var(--brand);
        background: #f4faf7;
    }

    .dropzone-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--brand-light);
        color: var(--brand);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }

    .dropzone-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 4px 0;
    }

    .highlight-browse {
        color: var(--brand);
        text-decoration: underline;
    }

    .dropzone-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin: 0 0 12px 0;
    }

    .dropzone-tags {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .tag-pill {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        background: #e2e8f0;
        color: #334155;
    }

    .tag-pdf { background: #fee2e2; color: #dc2626; }
    .tag-opt { background: #dcfce7; color: #15803d; }

    /* Samples Bar */
    .quick-samples-bar {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
    }

    .samples-title {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        display: block;
        margin-bottom: 6px;
    }

    .samples-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .sample-btn {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text);
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .sample-btn:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    /* Compression Result Box */
    .compression-result-card {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        border-radius: var(--radius-md);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .comp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .comp-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #14532d;
    }

    .comp-badge-live {
        font-size: 10px;
        font-weight: 800;
        background: #dcfce7;
        color: #15803d;
        padding: 2px 7px;
        border-radius: 4px;
        text-transform: uppercase;
    }

    .comp-ratio-badge {
        font-size: 11px;
        font-weight: 800;
        background: #15803d;
        color: white;
        padding: 3px 10px;
        border-radius: 99px;
    }

    .comp-metrics-row {
        display: flex;
        align-items: center;
        background: var(--surface);
        border: 1px solid #bbf7d0;
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        gap: 12px;
    }

    .metric-block {
        display: flex;
        flex-direction: column;
    }

    .metric-lbl {
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
    }

    .metric-val {
        font-size: 14px;
        font-weight: 800;
    }

    .text-brand { color: var(--brand); }
    .text-success { color: #16a34a; }
    .text-muted { color: #64748b; }
    .metric-arrow { font-size: 18px; color: var(--brand); font-weight: 700; }
    .metric-benefit { margin-left: auto; text-align: right; }

    .comp-explanation {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 11px;
        color: #166534;
        line-height: 1.45;
    }

    .info-icon-green {
        color: #15803d;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .send-target-row {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-top: 4px;
    }

    .target-select-wrap {
        flex: 1;
    }

    .target-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 4px;
    }

    .form-select-cs {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-family: inherit;
        outline: none;
        background: var(--surface);
    }

    /* Right Column: E-Ticket Visual Card */
    .ticket-selector-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .instant-ticket-card-visual {
        background: #fffbeb;
        border: 1.5px solid #fde047;
        border-radius: var(--radius-md);
        box-shadow: 0 4px 12px rgba(234, 179, 8, 0.12);
        overflow: hidden;
    }

    .ticket-visual-header {
        background: #fef08a;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1.5px dashed #ca8a04;
    }

    .ticket-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ticket-brand-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ca8a04;
        color: white;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ticket-company-title {
        font-size: 13px;
        font-weight: 800;
        color: #713f12;
        display: block;
    }

    .ticket-doc-type {
        font-size: 10px;
        color: #854d0e;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .ticket-status-stamp {
        font-size: 10px;
        font-weight: 800;
        background: #dcfce7;
        color: #15803d;
        padding: 3px 8px;
        border-radius: 99px;
        border: 1px solid #86efac;
    }

    .ticket-visual-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        background: var(--surface);
    }

    .ticket-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .t-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .t-lbl {
        font-size: 9px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .t-val-code {
        font-size: 14px;
        font-weight: 800;
        color: var(--brand);
        font-family: monospace;
    }

    .t-val {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
    }

    .t-val-seat {
        font-size: 13px;
        font-weight: 800;
        color: #b45309;
    }

    .ticket-barcode-strip {
        border-top: 1px dashed var(--border);
        padding-top: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .barcode-lines {
        display: flex;
        flex-direction: column;
        font-family: monospace;
        letter-spacing: 2px;
        font-size: 14px;
        color: #0f172a;
    }

    .barcode-code {
        font-size: 10px;
        color: var(--text-muted);
        letter-spacing: 1px;
    }

    .qr-mock-box {
        color: #0f172a;
    }

    .ticket-visual-footer {
        padding: 8px 16px;
        background: #fafaf9;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Format choice and actions */
    .ticket-actions-bar {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .format-choice-tabs {
        display: flex;
        gap: 8px;
    }

    .format-tab {
        flex: 1;
        padding: 8px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--page);
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .format-tab.active {
        background: #fef08a;
        border-color: #ca8a04;
        color: #713f12;
    }

    .ticket-instant-btns {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ticket-instant-btns .cs-btn-amber {
        flex: 1;
        justify-content: center;
    }

    /* History Table */
    .sf-history-table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-card-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .table-count-badge {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .sf-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: left;
    }

    .sf-table th {
        background: #f8fafc;
        padding: 12px 16px;
        font-weight: 700;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .sf-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
    }

    .file-link-pill {
        font-weight: 700;
        color: var(--brand);
    }

    .badge-cat-ticket { background: #fef08a; color: #854d0e; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10px; }
    .badge-cat-photo  { background: #dbeafe; color: #1e40af; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10px; }
    .badge-cat-transfer{ background: #dcfce7; color: #166534; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10px; }
    .badge-comp-opt   { color: #15803d; font-weight: 700; font-size: 11px; }
    .badge-sent-ok    { color: #16a34a; font-weight: 700; font-size: 11px; }

    .btn-table-action {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        color: var(--text);
    }

    .btn-table-action:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    /* Responsive */
    @media (max-width: 1080px) {
        .sf-feature-cards-grid { grid-template-columns: repeat(2, 1fr); }
        .sf-main-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .sf-page-header { flex-direction: column; align-items: flex-start; }
        .sf-feature-cards-grid { grid-template-columns: 1fr; }
        .send-target-row { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Dataset Tiket Pemesanan untuk Generator Instan
    const bookingDataset = {
        '1': {
            code: 'TRV-2026-0925',
            passenger: 'Budi Prasetyo',
            route: 'Bandung (Pasteur) &rarr; Jakarta (Semanggi)',
            departure: 'Jumat, 25 Sep 2026 &bull; 14:00 WIB',
            vehicle: 'Toyota HiAce Premio (D 7182 AB)',
            seat: '03A (Single Window)',
            barcode: 'TRV9250926887182'
        },
        '4': {
            code: 'TRV-2026-0612',
            passenger: 'Dewi Lestari (Bersama Lansia)',
            route: 'Bekasi (Cyber Park) &rarr; Bandung (Dipatiukur)',
            departure: 'Jumat, 25 Sep 2026 &bull; 13:00 WIB',
            vehicle: 'HiAce Commuter (B 1092 PQR)',
            seat: '01B (Depan Samping Driver)',
            barcode: 'TRV6120926881092'
        },
        '2': {
            code: 'TRV-2026-0811',
            passenger: 'Hendra Gunawan',
            route: 'Jakarta (Semanggi) &rarr; Bandung (Dipatiukur)',
            departure: 'Jumat, 25 Sep 2026 &bull; 13:45 WIB',
            vehicle: 'HiAce Luxury (B 8911 JKT)',
            seat: '01B (Depan Driver)',
            barcode: 'TRV8110926888911'
        },
        '3': {
            code: 'TRV-2026-0774',
            passenger: 'Siti Rahma',
            route: 'Bandung (Pasteur) &rarr; Bandara Soetta (T3)',
            departure: 'Jumat, 25 Sep 2026 &bull; 15:30 WIB',
            vehicle: 'HiAce Premio (D 9021 CG)',
            seat: '02B (Jendela Kanan)',
            barcode: 'TRV7740926889021'
        }
    };

    let currentSelectedCategory = 'transfer';
    let currentTicketFormat = 'pdf'; // 'pdf' | 'image'
    let currentActiveBookingId = '1';

    function setDocCategory(cat, btn) {
        currentSelectedCategory = cat;
        document.querySelectorAll('.cat-chip').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function triggerFileInput() {
        const input = document.getElementById('sf-file-input');
        if (input) input.click();
    }

    function handleHubFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        processUploadedFile(file.name, file.size, file.type);
    }

    function loadSampleFile(type) {
        if (type === 'transfer') {
            processUploadedFile('Bukti-Transfer-BCA.jpg', 3.4 * 1024 * 1024, 'image/jpeg');
        } else if (type === 'bagasi') {
            processUploadedFile('Foto-Koper-Lansia.png', 2.8 * 1024 * 1024, 'image/png');
        } else {
            processUploadedFile('Surat-Jalan-HiAce-Pasteur.pdf', 185 * 1024, 'application/pdf');
        }
    }

    function processUploadedFile(name, size, mimeType) {
        const isPdf = name.toLowerCase().endsWith('.pdf') || mimeType === 'application/pdf';
        const nameEl = document.getElementById('comp-file-name');
        const origEl = document.getElementById('comp-size-original');
        const resEl = document.getElementById('comp-size-result');
        const ratioEl = document.getElementById('comp-ratio-badge');
        const speedEl = document.getElementById('comp-speed-benefit');

        if (nameEl) nameEl.innerText = name;

        if (isPdf) {
            if (origEl) origEl.innerText = formatFileSize(size);
            if (resEl) resEl.innerText = formatFileSize(size) + ' (PDF Terverifikasi)';
            if (ratioEl) ratioEl.innerText = 'Dokumen PDF Resmi';
            if (speedEl) speedEl.innerText = 'Siap Verifikasi Dokumen';
        } else {
            // Simulasi kompresi otomatis gambar (JPG/PNG) hemat 88-90% kuota
            const origSizeFormatted = formatFileSize(size);
            const compressedSize = Math.round(size * 0.11);
            const compSizeFormatted = formatFileSize(compressedSize);

            if (origEl) origEl.innerText = origSizeFormatted;
            if (resEl) resEl.innerText = compSizeFormatted;
            if (ratioEl) ratioEl.innerText = 'Hemat Kuota 89%';
            if (speedEl) speedEl.innerText = '9x Lebih Cepat di Jalan';
        }

        const resultBox = document.getElementById('compression-result-box');
        if (resultBox) {
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function formatFileSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return Math.round(bytes / 1024) + ' KB';
    }

    function dispatchFileToChat() {
        const select = document.getElementById('sf-target-chat-select');
        const targetName = select ? select.options[select.selectedIndex].text : 'Penumpang';
        const fileName = document.getElementById('comp-file-name').innerText;

        alert(`File "${fileName}" telah berhasil dikompresi otomatis dan langsung dilampirkan ke ruang chat untuk ${targetName}!`);
        window.location.href = "{{ route('dashboard.customer-service.live-chat') }}";
    }

    // Generator Tiket Instan
    function updateTicketPreview(bookingId) {
        currentActiveBookingId = bookingId;
        const b = bookingDataset[bookingId];
        if (!b) return;

        document.getElementById('t-code').innerText = b.code;
        document.getElementById('t-passenger').innerText = b.passenger;
        document.getElementById('t-route').innerHTML = b.route;
        document.getElementById('t-departure').innerHTML = b.departure;
        document.getElementById('t-vehicle').innerText = b.vehicle;
        document.getElementById('t-seat').innerText = b.seat;
        document.getElementById('t-barcode-num').innerText = b.barcode;
    }

    function setTicketFormat(fmt) {
        currentTicketFormat = fmt;
        const btnPdf = document.getElementById('tab-fmt-pdf');
        const btnImg = document.getElementById('tab-fmt-img');
        const label = document.getElementById('btn-ticket-send-label');

        if (fmt === 'image') {
            btnPdf.classList.remove('active');
            btnImg.classList.add('active');
            if (label) label.innerText = 'Kirim E-Tiket Instan (Gambar PNG) ke Chat';
        } else {
            btnImg.classList.remove('active');
            btnPdf.classList.add('active');
            if (label) label.innerText = 'Kirim E-Tiket Instan (PDF) ke Chat';
        }
    }

    function sendInstantTicketToChat() {
        const b = bookingDataset[currentActiveBookingId];
        const fmtUpper = currentTicketFormat.toUpperCase();
        alert(`1-Klik Berhasil: E-Tiket resmi ${b.code} (${fmtUpper}) telah dibuat instan dan langsung dikirimkan ke ruang percakapan ${b.passenger}!`);
        window.location.href = "{{ route('dashboard.customer-service.live-chat') }}";
    }

    function downloadInstantTicket() {
        const b = bookingDataset[currentActiveBookingId];
        const ext = currentTicketFormat === 'pdf' ? 'pdf' : 'png';
        alert(`Mengunduh file resmi e-tiket: TravelKu-${b.code}.${ext}`);
    }
</script>
@endpush
@endsection
