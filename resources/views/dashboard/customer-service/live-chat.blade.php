@extends('layouts.customer-service')

@section('title', 'Pusat Antrean Live Chat & Penanganan Penumpang - TravelKu')

@section('content')
<div class="cs-chat-page-wrapper">

    <!-- Header Operasional Live Chat -->
    <div class="chat-hub-header">
        <div class="hub-header-left">
            <h1 class="hub-title">Pusat Antrean Live Chat</h1>
            <p class="hub-subtitle">Antrean pesan masuk terpadu dengan pemilahan skala prioritas waktu keberangkatan armada.</p>
        </div>
        <div class="hub-header-badges">
            <div class="hub-kpi-pill">
                <span class="status-dot-pulse"></span>
                <span>Response SLA: <strong>&lt; 2 Menit</strong></span>
            </div>
            <div class="hub-kpi-pill" id="urgency-counter-badge">
                <span class="kpi-critical-dot"></span>
                <span>Armada Kritis (&lt; 1 Jam): <strong id="critical-count-label">2 Sesi</strong></span>
            </div>
        </div>
    </div>

    <!-- Container Utama 3-Kolom Live Chat -->
    <div class="livechat-container">

        <!-- ========================================================
             KOLOM 1: DAFTAR PESAN & FILTER 3 TAB
             (Unassigned, Assigned to Me, Resolved/Closed)
        ======================================================== -->
        <aside class="chat-sidebar-inbox">

            <!-- Tab Navigasi 3 Kategori Sesuai Permintaan -->
            <div class="inbox-tabs">
                <button type="button"
                        class="inbox-tab-btn active"
                        id="tab-btn-unassigned"
                        onclick="switchChatTab('unassigned')">
                    <span>Unassigned</span>
                    <span class="tab-badge badge-unassigned" id="count-unassigned">3</span>
                </button>
                <button type="button"
                        class="inbox-tab-btn"
                        id="tab-btn-assigned"
                        onclick="switchChatTab('assigned')">
                    <span>Assigned to Me</span>
                    <span class="tab-badge badge-assigned" id="count-assigned">2</span>
                </button>
                <button type="button"
                        class="inbox-tab-btn"
                        id="tab-btn-resolved"
                        onclick="switchChatTab('resolved')">
                    <span>Resolved</span>
                    <span class="tab-badge badge-resolved" id="count-resolved">4</span>
                </button>
            </div>

            <!-- Toolbar Pencarian & Filter Urgensi Armada -->
            <div class="inbox-filters-bar">
                <div class="inbox-search-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text"
                           id="chat-search-input"
                           placeholder="Cari nama, booking, atau rute..."
                           onkeyup="filterChatConversations()">
                </div>
                <div class="inbox-urgency-select-wrap">
                    <select id="urgency-filter" class="inbox-urgency-select" onchange="filterChatConversations()">
                        <option value="all">Semua Urgensi Armada</option>
                        <option value="kritis">Kritis (&lt; 1 Jam Keberangkatan)</option>
                        <option value="mendesak">Mendesak (1 - 3 Jam)</option>
                        <option value="normal">Normal (&gt; 3 Jam / Umum)</option>
                    </select>
                </div>
            </div>

            <!-- Daftar Percakapan Dinamis -->
            <div class="inbox-conversations-list" id="conversations-list-container">
                <!-- Diisi via JavaScript / Template Item -->
            </div>

        </aside>

        <!-- ========================================================
             KOLOM 2: RUANG PERCAKAPAN AKTIF
        ======================================================== -->
        <section class="chat-main-conversation" id="conversation-panel">

            <!-- Header Percakapan Aktif -->
            <div class="conversation-topbar">
                <div class="conv-user-meta">
                    <div class="conv-avatar" id="active-user-avatar">BP</div>
                    <div class="conv-details">
                        <div class="conv-name-row">
                            <h2 class="conv-user-name" id="active-user-name">Budi Prasetyo</h2>
                            <span class="conv-online-tag" id="active-user-status">&bull; Online</span>
                        </div>
                        <div class="conv-route-row" id="active-user-route">
                            <span class="route-pill">Bandung (Pasteur) ke Jakarta (Semanggi)</span>
                            <span class="time-meta">&bull; Armada Berangkat: <strong>14:00 WIB</strong></span>
                        </div>
                    </div>
                </div>

                <div class="conv-top-actions">
                    <!-- Tombol Buka/Tutup Panel Konteks Booking -->
                    <button type="button"
                            class="cs-btn cs-btn-secondary"
                            id="btn-toggle-booking-context"
                            onclick="toggleContextSidebar()"
                            title="Buka / Sembunyikan Panel Melihat Konteks Booking">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span class="btn-lbl-full">Konteks Booking</span>
                        <span class="btn-lbl-short">Konteks</span>
                    </button>

                    <!-- Tombol Ambil Percakapan (Hanya Tampil Jika Status Unassigned) -->
                    <button type="button"
                            class="cs-btn cs-btn-primary"
                            id="btn-claim-chat"
                            onclick="claimActiveChat()"
                            style="display: none;"
                            title="Ambil Percakapan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        <span class="btn-lbl-full">Ambil Percakapan</span>
                        <span class="btn-lbl-short">Ambil</span>
                    </button>

                    <!-- Tombol Selesaikan Sesi (Tampil Jika Status Assigned to Me) -->
                    <button type="button"
                            class="cs-btn cs-btn-outline-success"
                            id="btn-resolve-chat"
                            onclick="resolveActiveChat()"
                            title="Tandai Selesai">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span class="btn-lbl-full">Tandai Selesai</span>
                        <span class="btn-lbl-short">Selesai</span>
                    </button>

                    <!-- Tombol Eskalasi ke Admin Utama (PRD 4.5 & 40) -->
                    <button type="button"
                            class="cs-btn cs-btn-outline-danger"
                            onclick="openEscalateModal()"
                            title="Eskalasi ke Admin Utama">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span class="btn-lbl-full">Eskalasi ke Admin</span>
                        <span class="btn-lbl-short">Eskalasi</span>
                    </button>
                </div>
            </div>

            <!-- Bar Pencarian Kata Kunci dalam Percakapan (Search in Chat - PRD 4.5) -->
            <div class="conv-search-bar" id="conv-search-bar">
                <div class="conv-search-input-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="conv-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text"
                           id="search-in-chat-input"
                           placeholder="Cari kata kunci dalam chat (misal: nomor rekening, nama, lansia, alamat jemput)..."
                           oninput="handleSearchInChat(this.value)"
                           autocomplete="off">
                    <span class="conv-search-counter" id="search-chat-counter" style="display: none;">0 pesan cocok</span>
                    <button type="button" class="conv-search-clear" id="btn-clear-search-chat" onclick="clearSearchInChat()" style="display: none;">&times;</button>
                </div>
            </div>

            <!-- Banner Peringatan Urgensi Armada -->
            <div class="conv-urgency-banner banner-kritis" id="conv-urgency-banner">
                <div class="urgency-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="urgency-text">
                    <strong id="banner-urgency-title">Urgensi Kritis: Armada Berangkat 45 Menit Lagi!</strong>
                    <span id="banner-urgency-desc">Toyota HiAce Premio (D 7182 AB) sedang proses boarding di Pool Pasteur. Prioritaskan konfirmasi titik jemput penumpang.</span>
                </div>
            </div>

            <!-- Area Gelembung Pesan (Messages Area) -->
            <div class="conv-messages-scroll" id="messages-container">
                <!-- Messages populated by JS -->
            </div>

            <!-- Bar Jawaban Cepat SOP CS (Canned Responses) -->
            <div class="canned-responses-bar">
                <span class="canned-label">Jawaban Cepat SOP:</span>
                <div class="canned-chips">
                    <button type="button" class="canned-chip canned-chip-ticket" onclick="openInstantTicketModal()" title="Generator Tiket Instan: Tombol satu klik melampirkan e-tiket resmi PDF/Gambar">
                        Generator Tiket Instan (PDF)
                    </button>
                    <button type="button" class="canned-chip" onclick="applyCannedReply('Halo kak! Armada saat ini sedang proses persiapan di pool dan siap berangkat tepat waktu.')">
                        Status Armada Tepat Waktu
                    </button>
                    <button type="button" class="canned-chip" onclick="applyCannedReply('Untuk titik temu penumpang di Pool Pasteur berada di ruang tunggu keberangkatan depan loket 2 ya kak.')">
                        Panduan Lokasi Pool
                    </button>
                    <button type="button" class="canned-chip" onclick="applyCannedReply('Ketentuan bagasi gratis minibus Toyota HiAce adalah 1 koper sedang (maks 15 kg) + 1 tas ransel.')">
                        Ketentuan Bagasi HiAce
                    </button>
                    <button type="button" class="canned-chip" onclick="applyCannedReply('Permintaan perubahan jadwal/data penumpang telah kami catat dan akan dibantu verifikasi.')">
                        Reschedule / Ganti Data
                    </button>
                </div>
            </div>

            <!-- Switcher Mode Kirim: Balas Penumpang vs Catatan Internal Antar-CS (Komentar Kuning) -->
            <div class="chat-mode-tabs-bar" id="chat-mode-tabs-bar">
                <div class="mode-tabs-group">
                    <button type="button" class="mode-tab-btn active" id="tab-mode-customer" onclick="setChatSendMode('customer')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span>Balas Penumpang</span>
                    </button>
                    <button type="button" class="mode-tab-btn" id="tab-mode-internal" onclick="setChatSendMode('internal')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Catatan Internal Antar-CS (Kuning)</span>
                    </button>
                </div>
                <span class="mode-hint-text" id="mode-hint-text">Pesan dikirim langsung ke penumpang</span>
            </div>

            <!-- Form Input Pengiriman Pesan & Lampiran Media (Format: JPG, PNG, PDF + Kompresi Otomatis) -->
            <form class="conv-input-form" id="conv-input-form" onsubmit="handleSendMessage(event)">
                <input type="file"
                       id="chat-file-upload"
                       accept=".jpg,.jpeg,.png,.pdf"
                       onchange="handleFileUpload(event)"
                       hidden>

                <label for="chat-file-upload" class="attach-btn" id="btn-attach-file" title="Kirim gambar (JPG, PNG) atau dokumen (PDF) dengan kompresi otomatis">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                </label>

                <!-- Tombol Generator Tiket Instan 1-Klik -->
                <button type="button" class="attach-btn ticket-quick-btn" id="btn-ticket-modal-trigger" onclick="openInstantTicketModal()" title="Generator Tiket Instan: Tombol 1-klik melampirkan e-tiket PDF ke chat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="6" cy="12" r="2"></circle><circle cx="18" cy="12" r="2"></circle><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                </button>

                <div class="input-text-wrapper" id="input-text-wrapper">
                    <div id="file-preview-pill" class="file-preview-pill" style="display: none;">
                        <span class="file-pill-icon" id="file-preview-icon"></span>
                        <div class="file-pill-info">
                            <span id="file-preview-name">foto-tiket.jpg</span>
                            <span id="file-preview-compression" class="compression-tag-pill">Kompresi Otomatis: Hemat kuota di jalan</span>
                        </div>
                        <button type="button" onclick="clearAttachedFile()">&times;</button>
                    </div>
                    <input type="text"
                           id="chat-message-input"
                           placeholder="Ketik balasan untuk penumpang..."
                           autocomplete="off">
                </div>

                <button type="submit" class="cs-btn cs-btn-primary send-btn" id="btn-send-message">
                    <span id="btn-send-label">Kirim</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </form>

        </section>

        <!-- ========================================================
             KOLOM 3: MELIHAT KONTEKS BOOKING (PRD 4.5 & 40)
             Panel data otomatis tersinkronisasi nomor/email terdaftar
        ======================================================== -->
        <aside class="chat-context-sidebar" id="booking-context">

            <!-- Header Panel: Otomatis muncul ketika pelanggan chat menggunakan nomor/email terdaftar -->
            <div class="context-sidebar-header">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="context-sync-badge">
                        <span class="sync-dot pulse"></span>
                        <span>Sinkronisasi Otomatis Terhubung</span>
                    </div>
                    <button type="button" class="drawer-close-btn" onclick="toggleContextSidebar()" title="Tutup Panel">&times;</button>
                </div>
                <h3 class="context-header-title">Melihat Konteks Booking</h3>
                <div class="context-header-subtitle">Konteks Pemesanan &amp; Detail Tiket Pelanggan</div>
            </div>

            <!-- 1. IDENTITAS TIKET -->
            <div class="context-card-box">
                <div class="ctx-card-header-line">
                    <div class="ctx-sec-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
                        <span>Identitas Tiket</span>
                    </div>
                    <button type="button" class="ctx-pnr-copy-btn" onclick="copyBookingCode()" title="Klik untuk salin Kode Booking (PNR)">
                        <span id="ctx-booking-code">TRV-2026-0925</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    </button>
                </div>

                <div class="ctx-field-group">
                    <label class="ctx-label">Nama Pemesan:</label>
                    <div class="ctx-val font-bold text-slate-800" id="ctx-passenger-name">Budi Prasetyo</div>
                </div>

                <div class="ctx-field-group">
                    <label class="ctx-label">Rute Perjalanan:</label>
                    <div class="ctx-val font-semibold text-brand-dark" id="ctx-booking-route">Bandung (Pasteur) ke Jakarta (Semanggi)</div>
                </div>

                <div class="ctx-grid-2col">
                    <div class="ctx-field-group">
                        <label class="ctx-label">Tanggal Keberangkatan:</label>
                        <div class="ctx-val font-medium" id="ctx-booking-date">Jumat, 25 Sep 2026</div>
                    </div>
                    <div class="ctx-field-group">
                        <label class="ctx-label">Jam Keberangkatan:</label>
                        <div class="ctx-val font-bold text-brand" id="ctx-booking-time">14:00 WIB</div>
                    </div>
                </div>

                <div class="ctx-countdown-banner" id="ctx-countdown-banner">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span id="ctx-countdown">Sisa Waktu: 45 Menit Menuju Keberangkatan</span>
                </div>
            </div>

            <!-- 2. DETAIL KURSI & ARMADA -->
            <div class="context-card-box">
                <div class="ctx-sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 9h10"/><path d="M7 15h10"/></svg>
                    <span>Detail Kursi &amp; Armada</span>
                </div>

                <div class="ctx-grid-2col">
                    <div class="ctx-field-group">
                        <label class="ctx-label">Nomor Kursi Dipilih:</label>
                        <div class="ctx-seat-pill-display" id="ctx-seat-badge">
                            <span class="seat-badge-no" id="ctx-seat">03A</span>
                            <span class="seat-badge-desc" id="ctx-seat-type">Single Window</span>
                        </div>
                    </div>
                    <div class="ctx-field-group">
                        <label class="ctx-label">Nomor Polisi Armada:</label>
                        <div class="ctx-plate-pill-display">
                            <span class="plate-code-no" id="ctx-plate">D 7182 AB</span>
                            <span class="plate-vehicle-desc" id="ctx-vehicle">HiAce Premio</span>
                        </div>
                    </div>
                </div>

                <div class="ctx-driver-block">
                    <div class="driver-avatar-circle" title="Sopir Bertugas">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="driver-info-main">
                        <span class="driver-role-tag">Sopir Bertugas:</span>
                        <strong class="driver-fullname" id="ctx-driver-name">Pak Suryanto</strong>
                        <span class="driver-contact-no" id="ctx-driver-phone">0812-7788-9900</span>
                    </div>
                    <div class="driver-quick-contacts">
                        <a href="tel:081277889900" id="ctx-driver-call-link" class="driver-btn driver-btn-call" title="Hubungi Sopir via Telepon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>Telp</span>
                        </a>
                        <a href="https://wa.me/6281277889900" id="ctx-driver-wa-link" target="_blank" rel="noopener noreferrer" class="driver-btn driver-btn-wa" title="Kirim Pesan WhatsApp ke Sopir">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. TITIK JEMPUT & TURUN -->
            <div class="context-card-box">
                <div class="ctx-card-header-line">
                    <div class="ctx-sec-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Titik Jemput &amp; Turun</span>
                    </div>
                    <span class="ctx-mode-badge" id="ctx-pickup-type">Pool to Pool</span>
                </div>

                <!-- Penjemputan -->
                <div class="ctx-stop-point point-pickup">
                    <div class="stop-point-header">
                        <div class="stop-dot dot-pickup"></div>
                        <span class="stop-label">Alamat Penjemputan:</span>
                    </div>
                    <div class="stop-address-text" id="ctx-pickup-address">Pool Pasteur - Jl. Dr. Djunjunan No. 135, Bandung</div>

                    <!-- Instruksi Penjemputan Spesifik -->
                    <div class="pickup-instruction-callout">
                        <div class="inst-content">
                            <span class="inst-heading">Instruksi Penjemputan:</span>
                            <span class="inst-body" id="ctx-pickup-instruction">Depan loket tiket 2, samping minimarket Indomaret Point</span>
                        </div>
                    </div>

                    <!-- Tautan Titik Google Maps -->
                    <a href="https://maps.google.com" id="ctx-pickup-maps-link" target="_blank" rel="noopener noreferrer" class="ctx-maps-anchor">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        <span>Buka Titik Penjemputan di Google Maps</span>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                </div>

                <!-- Penurunan -->
                <div class="ctx-stop-point point-dropoff">
                    <div class="stop-point-header">
                        <div class="stop-dot dot-dropoff"></div>
                        <span class="stop-label">Alamat Penurunan:</span>
                    </div>
                    <div class="stop-address-text" id="ctx-dropoff-address">Plaza Semanggi - Pintu Lobby Selatan, Jl. Jend. Sudirman Kav. 50, Jakarta</div>
                </div>
            </div>

            <!-- 4. STATUS PEMBAYARAN -->
            <div class="context-card-box">
                <div class="ctx-card-header-line">
                    <div class="ctx-sec-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        <span>Status Pembayaran</span>
                    </div>
                    <span class="ctx-payment-pill pill-paid" id="ctx-payment-status">Lunas</span>
                </div>

                <div class="ctx-payment-details">
                    <div class="pay-detail-row">
                        <span class="pay-lbl">Metode Bayar:</span>
                        <strong class="pay-val" id="ctx-payment-method">BCA Virtual Account</strong>
                    </div>
                    <div class="pay-detail-row">
                        <span class="pay-lbl">Total Tagihan:</span>
                        <strong class="pay-val text-brand" id="ctx-payment-amount">Rp 175.000</strong>
                    </div>
                    <div class="pay-detail-row">
                        <span class="pay-lbl">Waktu Pembayaran:</span>
                        <span class="pay-val text-slate-600" id="ctx-payment-time">25 Sep 2026, 09:15 WIB</span>
                    </div>
                </div>
            </div>

            <!-- 5. TOMBOL AKSI CEPAT (QUICK ACTION) -->
            <div class="context-card-box quick-actions-panel">
                <div class="ctx-sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    <span>Tombol Aksi Cepat</span>
                </div>
                <div class="quick-action-buttons-list">
                    <button type="button" class="qa-action-btn qa-btn-seat" onclick="openChangeSeatModal()">
                        <div class="qa-btn-content">
                            <strong>Ubah Kursi</strong>
                            <span>Pindah nomor kursi armada</span>
                        </div>
                    </button>
                    <button type="button" class="qa-action-btn qa-btn-reschedule" onclick="openRescheduleModal()">
                        <div class="qa-btn-content">
                            <strong>Reschedule</strong>
                            <span>Ganti jam / tanggal tiket</span>
                        </div>
                    </button>
                    <button type="button" class="qa-action-btn qa-btn-cancellation" onclick="openCancellationModal()">
                        <div class="qa-btn-content">
                            <strong>Ajukan Pembatalan</strong>
                            <span>Proses pengajuan &amp; refund</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Profil Kontak Penumpang Terdaftar -->
            <div class="context-card-box">
                <div class="ctx-sec-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profil Kontak Terdaftar</span>
                </div>
                <div class="ctx-user-card">
                    <div class="user-row">
                        <span class="ctx-label">WhatsApp:</span>
                        <strong id="ctx-passenger-phone">0812-9988-7766</strong>
                    </div>
                    <div class="user-row">
                        <span class="ctx-label">Email Akun:</span>
                        <span id="ctx-passenger-email">budi.p@mail.com</span>
                    </div>
                    <div class="user-row">
                        <span class="ctx-label">Status Member:</span>
                        <span class="tier-badge-silver" id="ctx-passenger-tier">Silver Member</span>
                    </div>
                </div>
            </div>

        </aside>

    </div>

</div>
@endsection

@push('modals')
<!-- Modal Eskalasi ke Admin Utama (PRD 4.5 & 40) -->
<div id="escalate-modal" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge"></span>
                <h3 class="modal-title">Eskalasi Kasus ke Admin Utama</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeEscalateModal()">&times;</button>
        </div>
        <div class="modal-card-body">
            <p class="modal-desc">
                Gunakan formulir ini untuk meneruskan kendala yang berada di luar batas kewenangan CS (seperti refund dana, perubahan armada, atau gangguan sistem).
            </p>

            <form onsubmit="submitEscalation(event)">
                <div class="form-group-cs">
                    <label class="form-label">Pilihan Kategori Eskalasi:</label>
                    <select id="escalate-category" class="form-select-cs" required>
                        <option value="Pengajuan Refund / Keuangan">Pengajuan Refund / Keuangan</option>
                        <option value="Armada Rusak / Ganti Mobil (Operasional)">Armada Rusak / Ganti Mobil (Operasional)</option>
                        <option value="Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)">Komplain Khusus (Sopir ugal-ugalan / Pelecehan / Barang hilang)</option>
                    </select>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Prioritas Kendala (Severity Level):</label>
                    <select id="escalate-severity" class="form-select-cs" required>
                        <option value="Low">Low (&le; 24 Jam)</option>
                        <option value="Medium" selected>Medium (&le; 2 Jam)</option>
                        <option value="High/Emergency">High/Emergency (&le; 15 Menit)</option>
                    </select>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Form Ringkasan Masalah (Akar Masalah - Wajib):</label>
                    <textarea id="escalate-root-cause"
                              rows="2"
                              class="form-textarea-cs"
                              placeholder="Tuliskan akar masalah / inti kendala yang melampaui wewenang CS..."
                              required></textarea>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Data Pendukung (Wajib):</label>
                    <textarea id="escalate-supporting-data"
                              rows="2"
                              class="form-textarea-cs"
                              placeholder="Contoh: No rekening & bank, nopol armada & nama sopir, atau saksi kejadian..."
                              required></textarea>
                </div>

                <div class="modal-card-footer">
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="closeEscalateModal()">Batal</button>
                    <button type="submit" class="cs-btn cs-btn-danger">Kirim Eskalasi ke Admin (Status: Pending)</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1. Modal Aksi Cepat: Ubah Kursi (Quick Action) -->
<div id="modal-change-seat" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge"></span>
                <h3 class="modal-title">Ubah Kursi Penumpang</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeChangeSeatModal()">&times;</button>
        </div>
        <div class="modal-card-body">
            <div class="modal-summary-box">
                <div class="sum-row">
                    <span>Kode Booking:</span>
                    <strong id="modal-seat-pnr">TRV-2026-0925</strong>
                </div>
                <div class="sum-row">
                    <span>Nama Penumpang:</span>
                    <strong id="modal-seat-passenger">Budi Prasetyo</strong>
                </div>
                <div class="sum-row">
                    <span>Kursi Saat Ini:</span>
                    <span class="badge-seat-current" id="modal-seat-current">03A (Single Window)</span>
                </div>
            </div>

            <form onsubmit="submitChangeSeat(event)">
                <div class="form-group-cs">
                    <label class="form-label">Pilih Kursi Baru yang Tersedia:</label>
                    <div class="seat-picker-grid" id="seat-picker-container">
                        <!-- Populated by JS -->
                    </div>
                    <input type="hidden" id="selected-new-seat" required>
                    <span class="form-help-text" id="seat-selection-hint">Klik salah satu kursi di atas untuk memilih.</span>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Alasan Perubahan Kursi:</label>
                    <select id="change-seat-reason" class="form-select-cs">
                        <option value="Permintaan Penumpang Ingin Kursi Jendela">Permintaan Penumpang - Ingin Kursi Jendela</option>
                        <option value="Permintaan Penumpang Ingin Kursi Depan">Permintaan Penumpang - Ingin Kursi Depan (Mabuk Perjalanan)</option>
                        <option value="Duduk Berdampingan dengan Rekan/Keluarga">Duduk Berdampingan dengan Rekan/Keluarga</option>
                        <option value="Penyesuaian Operasional Armada">Penyesuaian Operasional Armada</option>
                    </select>
                </div>

                <div class="modal-card-footer">
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="closeChangeSeatModal()">Batal</button>
                    <button type="submit" class="cs-btn cs-btn-primary">Konfirmasi Ubah Kursi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal Aksi Cepat: Reschedule Jadwal (Quick Action) -->
<div id="modal-reschedule" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge"></span>
                <h3 class="modal-title">Reschedule Jadwal Keberangkatan</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeRescheduleModal()">&times;</button>
        </div>
        <div class="modal-card-body">
            <div class="modal-summary-box">
                <div class="sum-row">
                    <span>Kode Booking:</span>
                    <strong id="modal-reschedule-pnr">TRV-2026-0925</strong>
                </div>
                <div class="sum-row">
                    <span>Rute:</span>
                    <strong id="modal-reschedule-route">Bandung ke Jakarta</strong>
                </div>
                <div class="sum-row">
                    <span>Jadwal Semula:</span>
                    <span class="badge-schedule-old" id="modal-reschedule-old">Jumat, 25 Sep 2026 &bull; 14:00 WIB</span>
                </div>
            </div>

            <form onsubmit="submitReschedule(event)">
                <div class="form-group-cs">
                    <label class="form-label">Pilih Tanggal Baru:</label>
                    <input type="date" id="reschedule-new-date" class="form-select-cs" required value="2026-09-26">
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Pilih Jam Keberangkatan Baru:</label>
                    <select id="reschedule-new-time" class="form-select-cs" required>
                        <option value="06:30 WIB">Pagi 06:30 WIB (Tersedia 4 Kursi)</option>
                        <option value="09:00 WIB">Pagi 09:00 WIB (Tersedia 2 Kursi)</option>
                        <option value="11:30 WIB">Siang 11:30 WIB (Tersedia 6 Kursi)</option>
                        <option value="14:00 WIB" selected>Siang 14:00 WIB (Tersedia 5 Kursi)</option>
                        <option value="16:30 WIB">Sore 16:30 WIB (Tersedia 3 Kursi)</option>
                        <option value="19:00 WIB">Malam 19:00 WIB (Tersedia 7 Kursi)</option>
                    </select>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Alasan Reschedule:</label>
                    <input type="text" id="reschedule-reason" class="form-select-cs" placeholder="Misal: Penumpang ada urusan mendadak / terjebak banjir" required>
                </div>

                <div class="modal-card-footer">
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="closeRescheduleModal()">Batal</button>
                    <button type="submit" class="cs-btn cs-btn-primary">Simpan Perubahan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Modal Aksi Cepat: Ajukan Pembatalan (Quick Action - PRD 4.5) -->
<div id="modal-cancellation" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge cancel-icon-badge"></span>
                <h3 class="modal-title">Ajukan Pembatalan Tiket &amp; Refund</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeCancellationModal()">&times;</button>
        </div>
        <div class="modal-card-body">
            <div class="modal-summary-box summary-cancel-theme">
                <div class="sum-row">
                    <span>Kode Booking:</span>
                    <strong id="modal-cancel-pnr">TRV-2026-0925</strong>
                </div>
                <div class="sum-row">
                    <span>Penumpang:</span>
                    <strong id="modal-cancel-passenger">Budi Prasetyo</strong>
                </div>
                <div class="sum-row">
                    <span>Nominal Pembayaran:</span>
                    <strong class="text-brand font-bold" id="modal-cancel-amount">Rp 175.000</strong>
                </div>
            </div>

            <div class="prd-notice-alert">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                <span><strong>SOP Customer Service (PRD 4.5):</strong> CS mengajukan tiket pembatalan ke Admin Utama &amp; Tim Keuangan untuk verifikasi dan eksekusi pengembalian dana (refund).</span>
            </div>

            <form onsubmit="submitCancellation(event)">
                <div class="form-group-cs">
                    <label class="form-label">Alasan Pembatalan:</label>
                    <select id="cancel-reason-select" class="form-select-cs" required>
                        <option value="Penumpang Berhalangan Karena Sakit/Darurat">Penumpang Berhalangan Karena Sakit / Darurat</option>
                        <option value="Perubahan Rencana Perjalanan Pribadi">Perubahan Rencana Perjalanan Pribadi</option>
                        <option value="Keterlambatan Armada Lebih Dari 30 Menit">Keterlambatan Armada &gt; 30 Menit</option>
                        <option value="Salah Pilih Jadwal/Rute Oleh Penumpang">Salah Pilih Jadwal / Rute</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Rekening Bank / E-Wallet Tujuan Refund:</label>
                    <input type="text" id="cancel-refund-account" class="form-select-cs" placeholder="Contoh: BCA 123456789 a.n Budi Prasetyo / GoPay 081299887766" required>
                </div>

                <div class="form-group-cs">
                    <label class="form-label">Catatan Tambahan untuk Admin Keuangan:</label>
                    <textarea id="cancel-notes" rows="2" class="form-textarea-cs" placeholder="Tuliskan catatan kondisi penumpang saat chat berlangsung..."></textarea>
                </div>

                <div class="modal-card-footer">
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="closeCancellationModal()">Batal</button>
                    <button type="submit" class="cs-btn cs-btn-danger">Kirim Pengajuan Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Modal Generator Tiket Instan (PDF/Gambar - PRD 4.5) -->
<div id="modal-instant-ticket" class="cs-modal-backdrop" style="display: none;">
    <div class="cs-modal-card">
        <div class="modal-card-header">
            <div class="modal-title-wrap">
                <span class="modal-icon-badge" style="background: #fef08a; color: #854d0e;"></span>
                <h3 class="modal-title">Generator Tiket Instan (PDF / Gambar)</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeInstantTicketModal()">&times;</button>
        </div>
        <div class="modal-card-body">
            <p class="modal-desc">
                Tombol satu klik untuk langsung melampirkan e-tiket resmi berformat PDF/gambar ke dalam ruang chat penumpang aktif.
            </p>

            <div class="modal-summary-box" style="background: #fffbeb; border: 1.5px solid #fde047; border-radius: 8px; padding: 12px; margin-bottom: 14px;">
                <div class="sum-row" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Kode Booking:</span>
                    <strong id="modal-ticket-pnr" style="color: var(--brand); font-family: monospace;">TRV-2026-0925</strong>
                </div>
                <div class="sum-row" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Nama Penumpang:</span>
                    <strong id="modal-ticket-passenger">Budi Prasetyo</strong>
                </div>
                <div class="sum-row" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Rute Perjalanan:</span>
                    <span id="modal-ticket-route" style="font-weight: 600;">Bandung ke Jakarta</span>
                </div>
                <div class="sum-row" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Armada &amp; Kursi:</span>
                    <span id="modal-ticket-seat" style="font-weight: 600;">HiAce Premio &bull; Kursi 03A</span>
                </div>
                <div class="sum-row" style="display: flex; justify-content: space-between; font-size: 13px; border-top: 1px dashed #fde047; padding-top: 6px;">
                    <span style="color: var(--text-muted);">Ukuran Dokumen:</span>
                    <span style="font-weight: 700; color: #15803d;">124 KB (Format PDF Resmi Siap Unduh)</span>
                </div>
            </div>

            <div class="modal-card-footer">
                <button type="button" class="cs-btn cs-btn-secondary" onclick="closeInstantTicketModal()">Batal</button>
                <button type="button" class="cs-btn cs-btn-secondary" onclick="downloadInstantTicketPdf()">Unduh PDF</button>
                <button type="button" class="cs-btn cs-btn-amber" onclick="sendInstantTicketFromModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    <span>Kirim E-Tiket ke Chat (PDF)</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    /* ============================================================
       LIVE CHAT LAYOUT DENGAN PALET WARNA IDENTIK ADMIN UTAMA
    ============================================================ */
    .cs-chat-page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .chat-hub-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 8px 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        flex-wrap: wrap;
        gap: 8px;
    }

    .hub-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.2px;
        line-height: 1.2;
    }

    .hub-subtitle {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 1px;
    }

    .hub-header-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .hub-kpi-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--page);
        border: 1px solid var(--border);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        color: var(--text-secondary);
    }

    .kpi-critical-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #dc2626;
        animation: pulse 1.8s infinite;
    }

    /* Grid 3-Panel Chat: Smart Proportional Layout */
    .livechat-container {
        display: grid;
        grid-template-columns: 265px minmax(360px, 1fr) 280px;
        gap: 12px;
        height: calc(100vh - 180px);
        min-height: 540px;
    }

    /* ============================================================
       KOLOM 1: INBOX DENGAN 3 TAB (Unassigned, Assigned, Resolved)
    ============================================================ */
    .chat-sidebar-inbox {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        min-width: 0;
    }

    /* 3 Tabs Sesuai Permintaan */
    .inbox-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        background: var(--page);
        border-bottom: 1px solid var(--border);
        padding: 4px;
        gap: 3px;
    }

    .inbox-tab-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 7px 3px;
        border-radius: var(--radius-sm);
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-size: 11px;
        font-weight: 700;
        transition: 0.15s ease;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
    }

    .inbox-tab-btn span:first-child {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .inbox-tab-btn:hover {
        background: rgba(255, 255, 255, 0.6);
        color: var(--brand);
    }

    .inbox-tab-btn.active {
        background: var(--surface);
        color: var(--brand);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .tab-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 999px;
        line-height: 1.2;
    }

    .badge-unassigned { background: #fee2e2; color: #dc2626; }
    .badge-assigned { background: var(--brand-light); color: var(--brand-dark); }
    .badge-resolved { background: #f1f5f9; color: #475569; }

    /* Filters Bar */
    .inbox-filters-bar {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: var(--surface);
    }

    .inbox-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .inbox-search-box svg {
        position: absolute;
        left: 10px;
        color: var(--text-muted);
    }

    .inbox-search-box input {
        width: 100%;
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 7px 10px 7px 32px;
        font-size: 12px;
        outline: none;
        transition: 0.2s ease;
    }

    .inbox-search-box input:focus {
        border-color: var(--brand);
        background: var(--surface);
    }

    .inbox-urgency-select {
        width: 100%;
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text);
        outline: none;
        cursor: pointer;
    }

    /* List Percakapan */
    .inbox-conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .chat-item-card {
        padding: 12px 14px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-subtle);
        background: var(--surface);
        transition: 0.2s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
    }

    .chat-item-card:hover {
        border-color: var(--brand);
        background: var(--brand-soft);
        transform: translateY(-1px);
    }

    .chat-item-card.active {
        border-color: var(--brand);
        border-left: 3px solid var(--brand);
        background: var(--brand-soft);
        box-shadow: 0 3px 10px rgba(8, 127, 85, 0.10);
    }

    .item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .item-user-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .item-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--brand);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    .item-user-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .item-time {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Badge Urgensi Keberangkatan Armada */
    .item-urgency-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        align-self: flex-start;
    }

    .urgency-kritis {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    .urgency-mendesak {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .urgency-normal {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .item-preview-text {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-footer-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--text-muted);
        border-top: 1px dashed var(--border-subtle);
        padding-top: 5px;
        margin-top: 2px;
    }

    /* ============================================================
       KOLOM 2: CONVERSATION PANEL
    ============================================================ */
    .chat-main-conversation {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        min-width: 0;
        position: relative;
    }

    .conversation-topbar {
        padding: 8px 12px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface);
        gap: 8px 10px;
        flex-shrink: 0;
        flex-wrap: wrap;
        min-height: 52px;
    }

    .conv-user-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 170px;
        flex: 1 1 auto;
        overflow: hidden;
    }

    .conv-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--brand);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
    }

    .conv-details {
        min-width: 0;
        flex: 1;
        overflow: hidden;
    }

    .conv-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-user-name {
        font-size: 13px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-online-tag {
        font-size: 10px;
        font-weight: 700;
        color: #16a34a;
        flex-shrink: 0;
    }

    .conv-route-row {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        color: var(--text-secondary);
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .route-pill {
        background: var(--brand-light);
        color: var(--brand-dark);
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 3px;
        font-size: 10px;
        white-space: nowrap;
    }

    .time-meta {
        font-size: 10px;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .conv-top-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
        flex-shrink: 0;
        margin-left: auto;
    }

    .conv-top-actions .cs-btn {
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 6px;
        white-space: nowrap;
        gap: 4px;
        display: inline-flex;
        align-items: center;
    }

    .btn-lbl-short {
        display: none;
    }

    .btn-lbl-full {
        display: inline;
    }

    @media (max-width: 1400px) {
        .btn-lbl-full {
            display: none;
        }
        .btn-lbl-short {
            display: inline;
        }
        .conv-top-actions .cs-btn {
            padding: 4px 7px;
            font-size: 10.5px;
        }
    }

    .cs-btn-outline-success {
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px solid var(--brand-light);
    }

    .cs-btn-outline-success:hover {
        background: var(--brand-light);
    }

    .cs-btn-outline-danger {
        background: var(--danger-soft);
        color: var(--danger);
        border: 1px solid #fecaca;
    }

    .cs-btn-outline-danger:hover {
        background: #fee2e2;
    }

    /* Urgency Banner */
    .conv-urgency-banner {
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .conv-urgency-banner .urgency-icon {
        flex-shrink: 0;
    }

    .conv-urgency-banner .urgency-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
    }

    .urgency-text strong {
        display: inline;
        font-weight: 700;
        margin-right: 4px;
    }

    .banner-kritis {
        background: #fff1f2;
        color: #9f1239;
        border-color: #fecdd3;
    }

    .banner-mendesak {
        background: #fffbeb;
        color: #92400e;
        border-color: #fde68a;
    }

    .banner-normal {
        background: #f0fdf4;
        color: #166534;
        border-color: #bbf7d0;
    }

    /* Messages Scroll Area */
    .conv-messages-scroll {
        flex: 1 1 auto;
        min-height: 0;
        padding: 12px 14px;
        overflow-y: auto;
        background: #f9fbfb;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-bubble-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 72%;
    }

    .chat-bubble-wrapper.customer {
        align-self: flex-start;
    }

    .chat-bubble-wrapper.cs {
        align-self: flex-end;
    }

    .bubble-author {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 3px;
        color: var(--text-muted);
    }

    .chat-bubble-wrapper.cs .bubble-author {
        text-align: right;
        color: var(--brand-dark);
    }

    .bubble-box {
        padding: 11px 15px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.5;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
    }

    .customer .bubble-box {
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text);
        border-bottom-left-radius: 4px;
    }

    .cs .bubble-box {
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .bubble-time {
        font-size: 10px;
        margin-top: 4px;
        color: var(--text-muted);
    }

    .cs .bubble-time {
        text-align: right;
    }

    .chat-system-event {
        align-self: center;
        background: rgba(0, 0, 0, 0.05);
        color: var(--text-secondary);
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 20px;
        margin: 6px 0;
    }

    /* Canned Replies Bar */
    .canned-responses-bar {
        padding: 4px 10px;
        background: var(--surface);
        border-top: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        flex-shrink: 0;
    }

    .canned-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .canned-chips {
        display: flex;
        gap: 4px;
        overflow-x: auto;
        flex: 1;
        scrollbar-width: none;
    }

    .canned-chip {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 2px 7px;
        font-size: 10.5px;
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
        cursor: pointer;
        transition: 0.15s ease;
        flex-shrink: 0;
    }

    .canned-chip:hover {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand-dark);
    }

    /* Input Form */
    .conv-input-form {
        padding: 6px 10px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--surface);
        flex-shrink: 0;
    }

    .attach-btn {
        width: 30px;
        height: 30px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--page);
        color: var(--text-secondary);
        cursor: pointer;
        flex-shrink: 0;
        transition: 0.2s ease;
    }

    .attach-btn:hover {
        background: var(--page);
        color: var(--brand);
        border-color: var(--brand);
    }

    .input-text-wrapper {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-text-wrapper input {
        width: 100%;
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 10px 14px;
        font-size: 13px;
        outline: none;
        transition: 0.2s ease;
        font-family: inherit;
    }

    .input-text-wrapper input:focus {
        background: var(--surface);
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .file-preview-pill {
        position: absolute;
        right: 10px;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .file-preview-pill button {
        background: none;
        border: none;
        color: inherit;
        font-size: 14px;
        cursor: pointer;
    }

    .send-btn {
        padding: 10px 18px;
    }

    /* ============================================================
       KOLOM 3: MELIHAT KONTEKS BOOKING SIDEBAR (PRD 4.5 & 40)
    ============================================================ */
    .chat-context-sidebar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        display: flex;
        flex-direction: column;
        padding: 10px 12px;
        gap: 8px;
        overflow-y: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        min-width: 0;
    }

    .context-sidebar-header {
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .context-sync-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 3px 8px;
        border-radius: 20px;
        width: fit-content;
    }

    .sync-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--brand);
    }

    .sync-dot.pulse {
        animation: pulse 1.8s infinite;
    }

    .context-header-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.2px;
        margin: 0;
    }

    .context-header-subtitle {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .context-card-box {
        background: var(--page);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .ctx-card-header-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
    }

    .ctx-sec-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 800;
        color: var(--text);
    }

    .ctx-sec-title svg {
        color: var(--brand);
        flex-shrink: 0;
    }

    .ctx-pnr-copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: 800;
        color: var(--text);
        cursor: pointer;
        transition: 0.2s ease;
        font-family: inherit;
    }

    .ctx-pnr-copy-btn:hover {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand-dark);
    }

    .ctx-field-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ctx-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .ctx-val {
        font-size: 12.5px;
        color: var(--text);
        line-height: 1.4;
    }

    .text-brand-dark { color: var(--brand-dark); }

    .ctx-grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .ctx-countdown-banner {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 6px 10px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 700;
        margin-top: 2px;
    }

    /* Detail Kursi & Armada */
    .ctx-seat-pill-display {
        display: flex;
        flex-direction: column;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 5px 8px;
    }

    .seat-badge-no {
        font-size: 14px;
        font-weight: 800;
        color: var(--brand-dark);
    }

    .seat-badge-desc {
        font-size: 10px;
        color: var(--text-muted);
    }

    .ctx-plate-pill-display {
        display: flex;
        flex-direction: column;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 5px 8px;
    }

    .plate-code-no {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.5px;
    }

    .plate-vehicle-desc {
        font-size: 10px;
        color: var(--text-muted);
    }

    .ctx-driver-block {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 8px 10px;
        margin-top: 3px;
    }

    .driver-avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--brand-soft);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .driver-info-main {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
        line-height: 1.25;
    }

    .driver-role-tag {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .driver-fullname {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .driver-contact-no {
        font-size: 11px;
        color: var(--text-secondary);
    }

    .driver-quick-contacts {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .driver-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .driver-btn-call {
        background: var(--page);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .driver-btn-call:hover {
        background: var(--border);
        color: var(--text);
    }

    .driver-btn-wa {
        background: #25D366;
        color: white;
    }

    .driver-btn-wa:hover {
        background: #1eb857;
    }

    /* Titik Jemput & Turun */
    .ctx-mode-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        background: var(--brand-light);
        color: var(--brand-dark);
    }

    .ctx-stop-point {
        display: flex;
        flex-direction: column;
        gap: 4px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 8px 10px;
    }

    .stop-point-header {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stop-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot-pickup { background: var(--brand); }
    .dot-dropoff { background: #0284c7; }

    .stop-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .stop-address-text {
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.35;
    }

    .pickup-instruction-callout {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        background: #fffbeb;
        border: 1px dashed #fde68a;
        padding: 6px 8px;
        border-radius: 4px;
        margin-top: 3px;
    }

    .inst-icon {
        font-size: 12px;
        line-height: 1;
    }

    .inst-content {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .inst-heading {
        font-size: 10px;
        font-weight: 700;
        color: #92400e;
    }

    .inst-body {
        font-size: 11px;
        color: #78350f;
        line-height: 1.3;
    }

    .ctx-maps-anchor {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
        font-size: 11px;
        font-weight: 700;
        color: var(--brand-dark);
        text-decoration: none;
        padding: 4px 8px;
        background: var(--brand-light);
        border-radius: 4px;
        width: fit-content;
        transition: 0.2s ease;
    }

    .ctx-maps-anchor:hover {
        background: var(--brand);
        color: white;
    }

    /* Status Pembayaran */
    .ctx-payment-pill {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
    }

    .pill-paid {
        background: #dcfce7;
        color: #15803d;
    }

    .pill-pending {
        background: #fef3c7;
        color: #b45309;
    }

    .pill-expired {
        background: #fee2e2;
        color: #b91c1c;
    }

    .ctx-payment-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 8px 10px;
    }

    .pay-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11.5px;
    }

    .pay-lbl {
        color: var(--text-muted);
    }

    .pay-val {
        color: var(--text);
    }

    /* 5. Tombol Aksi Cepat (Quick Action) */
    .quick-actions-panel {
        background: linear-gradient(180deg, var(--page) 0%, var(--brand-soft) 100%);
        border-color: rgba(8, 127, 85, 0.2);
    }

    .quick-action-buttons-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .qa-action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        font-family: inherit;
    }

    .qa-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
    }

    .qa-btn-seat:hover {
        border-color: var(--brand);
        background: var(--brand-light);
    }

    .qa-btn-reschedule:hover {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .qa-btn-cancellation:hover {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .qa-btn-icon {
        font-size: 16px;
        line-height: 1;
    }

    .qa-btn-content {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }

    .qa-btn-content strong {
        font-size: 12px;
        color: var(--text);
    }

    .qa-btn-content span {
        font-size: 10px;
        color: var(--text-muted);
    }

    /* Modal Sub-Components */
    .modal-summary-box {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
    }

    .summary-cancel-theme {
        border-left: 3px solid #dc2626;
    }

    .sum-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .badge-seat-current {
        background: var(--brand-light);
        color: var(--brand-dark);
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
    }

    .badge-schedule-old {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
    }

    .cancel-icon-badge {
        background: #fef2f2;
        color: #dc2626;
    }

    .prd-notice-alert {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        font-size: 11px;
        color: #92400e;
        margin-bottom: 14px;
        line-height: 1.4;
    }

    .prd-notice-alert svg {
        flex-shrink: 0;
        margin-top: 1px;
    }

    .seat-picker-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        margin-top: 4px;
    }

    .seat-btn-pick {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 8px 4px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text);
        cursor: pointer;
        text-align: center;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .seat-btn-pick:hover:not(:disabled) {
        border-color: var(--brand);
        background: var(--brand-light);
        color: var(--brand-dark);
    }

    .seat-btn-pick.active {
        background: var(--brand);
        border-color: var(--brand-dark);
        color: white;
    }

    .seat-btn-pick:disabled {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .form-help-text {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ============================================================
       MODAL ESKALASI
    ============================================================ */
    .cs-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1100;
        padding: 20px;
    }

    .cs-modal-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalFade 0.2s ease-out;
    }

    @keyframes modalFade {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 22px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-card-body {
        padding: 20px 22px;
    }

    .modal-desc {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .form-group-cs {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
    }

    .form-select-cs, .form-textarea-cs {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        background: var(--surface);
    }

    .form-select-cs:focus, .form-textarea-cs:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 2px rgba(8, 127, 85, 0.15);
    }

    .modal-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .cs-btn-secondary {
        background: var(--page);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .cs-btn-danger {
        background: var(--danger);
        color: white;
    }

    .cs-btn-danger:hover {
        background: #b91c1c;
    }

    .drawer-close-btn {
        display: none;
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0 4px;
        line-height: 1;
    }

    /* Search in Chat (PRD 4.5) */
    .conv-search-bar {
        padding: 4px 10px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-subtle);
        flex-shrink: 0;
    }

    .conv-search-input-box {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 3px 8px;
        transition: 0.15s ease;
    }

    .conv-search-input-box:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 2px rgba(8, 127, 85, 0.12);
    }

    .conv-search-icon {
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .conv-search-input-box input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 11px;
        font-family: inherit;
        background: transparent;
        color: var(--text);
    }

    .conv-search-counter {
        font-size: 10px;
        font-weight: 700;
        color: var(--brand-dark);
        background: var(--brand-light);
        padding: 1px 6px;
        border-radius: 99px;
        white-space: nowrap;
    }

    .conv-search-clear {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        padding: 0 2px;
    }

    .conv-search-clear:hover {
        color: var(--danger);
    }

    mark.search-highlight {
        background: #fde047;
        color: #78350f;
        padding: 1px 3px;
        border-radius: 3px;
        font-weight: 700;
    }

    /* Mode Balas vs Catatan Internal (PRD 4.5) */
    .chat-mode-tabs-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 3px 10px;
        background: var(--surface);
        border-top: 1px solid var(--border-subtle);
        gap: 6px;
        flex-shrink: 0;
    }

    .mode-tabs-group {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    .mode-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: var(--radius-sm);
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--border);
        background: #f8fafc;
        color: var(--text-secondary);
        transition: 0.15s ease;
        font-family: inherit;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .mode-tab-btn:hover {
        background: #f1f5f9;
        color: var(--text);
    }

    .mode-tab-btn.active#tab-mode-customer {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand-dark);
    }

    .mode-tab-btn.active#tab-mode-internal {
        background: #fef9c3;
        border-color: #facc15;
        color: #854d0e;
    }

    .mode-hint-text {
        font-size: 10px;
        color: var(--text-muted);
        font-style: italic;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Catatan Internal Khusus Antar-CS (Kuning - PRD 4.5) */
    .chat-internal-note-box {
        margin: 10px 18px;
        background: #fefce8;
        border: 1.5px solid #fde047;
        border-radius: var(--radius-md);
        padding: 12px 14px;
        box-shadow: 0 2px 6px rgba(234, 179, 8, 0.08);
    }

    .internal-note-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #fde047;
    }

    .internal-note-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 800;
        color: #854d0e;
        letter-spacing: 0.02em;
    }

    .internal-note-meta {
        font-size: 11px;
        color: #a16207;
        font-weight: 600;
    }

    .internal-note-content {
        font-size: 13px;
        color: #713f12;
        line-height: 1.5;
        font-weight: 500;
    }

    /* Log Pergantian Agen (PRD 4.5) */
    .chat-agent-handover-log {
        margin: 10px 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-md);
        padding: 9px 14px;
        font-size: 12px;
        color: #334155;
    }

    .handover-log-icon {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .handover-log-text {
        flex: 1;
        line-height: 1.4;
    }

    .handover-log-time {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        white-space: nowrap;
    }

    /* Generator Tiket Instan & Lampiran Media (PRD 4.5) */
    .canned-chip-ticket {
        background: #fef08a !important;
        border-color: #facc15 !important;
        color: #854d0e !important;
        font-weight: 800 !important;
    }

    .canned-chip-ticket:hover {
        background: #fde047 !important;
    }

    .ticket-quick-btn {
        background: #fef08a;
        color: #854d0e;
        border: 1px solid #facc15;
    }

    .ticket-quick-btn:hover {
        background: #fde047;
    }

    .file-preview-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: var(--radius-sm);
        padding: 5px 10px;
        font-size: 11px;
    }

    .file-pill-info {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .compression-tag-pill {
        font-size: 10px;
        font-weight: 700;
        color: #15803d;
    }

    .compression-tag-pill.pill-pdf {
        color: #b91c1c;
    }

    /* Media Attachment Cards Inside Chat Bubbles */
    .chat-bubble-attachment {
        margin-top: 8px;
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    .chat-bubble-attachment.ticket-pdf {
        background: #fffbeb;
        border: 1.5px solid #fde047;
        padding: 10px 12px;
        color: #713f12;
    }

    .ticket-att-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .ticket-att-badge {
        font-size: 11px;
        font-weight: 800;
        color: #854d0e;
    }

    .ticket-att-size {
        font-size: 10px;
        font-weight: 700;
        color: #15803d;
        background: #dcfce7;
        padding: 1px 6px;
        border-radius: 4px;
    }

    .ticket-att-body {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .ticket-att-sub {
        font-size: 11px;
        color: #854d0e;
        margin-top: 2px;
    }

    .ticket-att-download-btn {
        width: 100%;
        background: #fef08a;
        border: 1px solid #ca8a04;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #713f12;
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .ticket-att-download-btn:hover {
        background: #fde047;
    }

    .chat-bubble-attachment.compressed-img {
        background: #f8fafc;
        border: 1px solid var(--border);
        padding: 8px 10px;
        border-radius: 6px;
    }

    .att-img-preview-box {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .comp-quota-tag {
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #15803d;
    }

    .chat-bubble-attachment.doc-pdf {
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 8px 12px;
        border-radius: 6px;
    }

    .att-pdf-preview-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pdf-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        font-size: 11px;
    }

    .pdf-details strong {
        color: #991b1b;
        font-size: 12px;
    }

    .pdf-size {
        color: #b91c1c;
    }

    .btn-pdf-dl {
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 1280px) {
        .livechat-container {
            grid-template-columns: 265px 1fr;
        }
        .chat-context-sidebar {
            display: none;
            position: fixed;
            top: 70px;
            right: 0;
            bottom: 0;
            width: 320px;
            max-width: 90vw;
            z-index: 1050;
            background: var(--surface);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            border-radius: 0;
        }
        .chat-context-sidebar.active-drawer {
            display: flex !important;
            animation: slideInRight 0.25s ease-out;
        }
        .drawer-close-btn {
            display: block !important;
        }
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @media (max-width: 768px) {
        .livechat-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .chat-sidebar-inbox {
            height: 340px;
        }
        .chat-main-conversation {
            height: 520px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ============================================================
    // DATA PERCAKAPAN LIVE CHAT DENGAN URGENSI ARMADA & 3 TAB
    // ============================================================
    const currentCsName = "{{ auth()->user()->name ?? 'Customer Service' }}";

    // Dataset Percakapan Awal dengan Data Lengkap Konteks Booking
    let conversations = [
        // TAB 1: UNASSIGNED (Pesan baru belum diambil CS)
        {
            id: 1,
            tab: 'unassigned', // unassigned | assigned | resolved
            urgency: 'kritis', // kritis | mendesak | normal
            urgencyLabel: 'Berangkat 45 Menit Lagi',
            bannerTitle: 'Urgensi Kritis: Armada Berangkat 45 Menit Lagi!',
            bannerDesc: 'Toyota HiAce Premio (D 7182 AB) sedang proses boarding di Pool Pasteur. Prioritaskan konfirmasi titik jemput penumpang.',
            bannerClass: 'banner-kritis',
            user: {
                name: 'Budi Prasetyo',
                phone: '0812-9988-7766',
                email: 'budi.p@mail.com',
                avatar: 'BP',
                tier: 'Silver Member'
            },
            booking: {
                code: 'TRV-2026-0925',
                status: 'Terkonfirmasi',
                route: 'Bandung (Pasteur) ke Jakarta (Semanggi)',
                departureDate: 'Jumat, 25 Sep 2026',
                departureTime: '14:00 WIB',
                departure: 'Hari Ini, 14:00 WIB',
                countdown: 'Sisa Waktu: 45 Menit Menuju Keberangkatan',
                seat: '03A',
                seatType: 'Single Window',
                vehicle: 'Toyota HiAce Premio',
                plate: 'D 7182 AB',
                driverName: 'Pak Suryanto',
                driverPhone: '0812-7788-9900',
                driverWaLink: 'https://wa.me/6281277889900',
                pickupType: 'Pool to Pool',
                pickupAddress: 'Pool Pasteur - Jl. Dr. Djunjunan No. 135, Pasteur, Sukajadi, Kota Bandung',
                pickupInstruction: 'Depan loket tiket 2, samping minimarket Indomaret Point',
                pickupMapsUrl: 'https://maps.google.com/?q=Pool+Pasteur+Bandung',
                dropoffAddress: 'Plaza Semanggi - Pintu Lobby Selatan, Jl. Jend. Sudirman Kav. 50, Jakarta Selatan',
                dropoffMapsUrl: 'https://maps.google.com/?q=Plaza+Semanggi+Jakarta',
                paymentStatus: 'Lunas',
                paymentStatusClass: 'pill-paid',
                paymentMethod: 'BCA Virtual Account',
                paymentAmount: 'Rp 175.000',
                paymentPaidAt: '25 Sep 2026, 09:15 WIB'
            },
            preview: 'Min, posisi van saya jam 14:00 rute Bandung - Jakarta sudah siap di pool belum?',
            timeAgo: '1 menit lalu',
            history: [
                { title: 'Pengecekan Pembayaran Sukses', date: '25 Sep 2026, 09:15 WIB • CS Nadia' },
                { title: 'Pemesanan Tiket Baru', date: '24 Sep 2026, 18:30 WIB • Portal Web' }
            ],
            messages: [
                { type: 'customer', text: 'Halo CS TravelKu, selamat siang.', time: '13:12' },
                { type: 'customer', text: 'Min, posisi van saya jam 14:00 rute Bandung - Jakarta sudah siap di pool belum? Takut telat jalan dari kantor.', time: '13:14' },
                { type: 'internal_note', text: 'Nomor rekening BCA 883019281 a.n Budi Prasetyo tersimpan untuk jaminan SLA ketepatan waktu armada.', author: 'CS Budi Santoso', time: '13:15' },
                {
                    type: 'cs',
                    author: 'CS Budi Santoso',
                    text: 'Halo Pak Budi! Armada HiAce Premio siap berangkat pukul 14:00. Berikut kami lampirkan e-tiket resmi Anda berformat PDF untuk proses boarding:',
                    attachment: {
                        type: 'ticket_pdf',
                        fileName: 'E-Tiket-Resmi-TRV-2026-0925.pdf',
                        fileSize: '124 KB',
                        pnr: 'TRV-2026-0925',
                        passenger: 'Budi Prasetyo',
                        route: 'Bandung ke Jakarta',
                        seat: '03A'
                    },
                    time: '13:16'
                }
            ]
        },
        {
            id: 2,
            tab: 'unassigned',
            urgency: 'kritis',
            urgencyLabel: 'Berangkat 30 Menit Lagi',
            bannerTitle: 'Urgensi Kritis: Penumpang Belum Tiba di Pool!',
            bannerDesc: 'Armada HiAce (B 8911 JKT) rute Jakarta - Bandung berangkat pukul 13:45 WIB. Penumpang terjebak macet di Pancoran.',
            bannerClass: 'banner-kritis',
            user: {
                name: 'Hendra Gunawan',
                phone: '0813-2233-4455',
                email: 'hendra.g@mail.com',
                avatar: 'HG',
                tier: 'Gold Member'
            },
            booking: {
                code: 'TRV-2026-0811',
                status: 'Terkonfirmasi',
                route: 'Jakarta (Semanggi) ke Bandung (Dipatiukur)',
                departureDate: 'Jumat, 25 Sep 2026',
                departureTime: '13:45 WIB',
                departure: 'Hari Ini, 13:45 WIB',
                countdown: 'Sisa Waktu: 30 Menit (Penumpang Terjebak Macet)',
                seat: '01B',
                seatType: 'Depan Samping Driver',
                vehicle: 'HiAce Luxury',
                plate: 'B 8911 JKT',
                driverName: 'Pak Joko Santoso',
                driverPhone: '0811-3322-1144',
                driverWaLink: 'https://wa.me/6281133221144',
                pickupType: 'Pool to Pool',
                pickupAddress: 'Pool Plaza Semanggi - Ground Floor Area Parkir Timur, Jakarta Selatan',
                pickupInstruction: 'Lobi timur samping ATM BCA, depan pos satpam',
                pickupMapsUrl: 'https://maps.google.com/?q=Plaza+Semanggi+Jakarta',
                dropoffAddress: 'Pool Dipatiukur - Jl. Dipatiukur No. 44, Lebakgede, Coblong, Bandung',
                dropoffMapsUrl: 'https://maps.google.com/?q=Dipatiukur+Bandung',
                paymentStatus: 'Lunas',
                paymentStatusClass: 'pill-paid',
                paymentMethod: 'QRIS Gopay',
                paymentAmount: 'Rp 195.000',
                paymentPaidAt: '25 Sep 2026, 07:05 WIB'
            },
            preview: 'Gawat min, saya macet parah di jalan tol, apakah van bisa tunggu 10 menit?',
            timeAgo: '3 menit lalu',
            history: [
                { title: 'Pemesanan via Aplikasi Mobile', date: '25 Sep 2026, 07:00 WIB' }
            ],
            messages: [
                { type: 'customer', text: 'Gawat min, saya macet parah di jalan tol, apakah van bisa tunggu 10 menit?', time: '13:15' }
            ]
        },
        {
            id: 3,
            tab: 'unassigned',
            urgency: 'mendesak',
            urgencyLabel: 'Berangkat 2 Jam Lagi',
            bannerTitle: 'Urgensi Mendesak: Keberangkatan Pukul 15:30 WIB',
            bannerDesc: 'Armada rute Bandung - Bandara Soekarno Hatta. Penumpang menanyakan kapasitas bagasi koper besar.',
            bannerClass: 'banner-mendesak',
            user: {
                name: 'Siti Rahma',
                phone: '0878-1122-3344',
                email: 'siti.rahma@mail.com',
                avatar: 'SR',
                tier: 'Regular'
            },
            booking: {
                code: 'TRV-2026-0774',
                status: 'Lunas',
                route: 'Bandung (Pasteur) ke Bandara Soetta (T3)',
                departureDate: 'Jumat, 25 Sep 2026',
                departureTime: '15:30 WIB',
                departure: 'Hari Ini, 15:30 WIB',
                countdown: 'Sisa Waktu: 2 Jam 15 Menit',
                seat: '02B',
                seatType: 'Jendela Kanan',
                vehicle: 'HiAce Premio',
                plate: 'D 9021 CG',
                driverName: 'Pak Dadang Sudrajat',
                driverPhone: '0821-4455-6677',
                driverWaLink: 'https://wa.me/6282144556677',
                pickupType: 'Door-to-Door (Antar Jemput)',
                pickupAddress: 'Jl. Sukajadi No. 120 (Rumah Pagar Cokelat Depan Apotek K-24), Bandung',
                pickupInstruction: 'Mobil bisa berhenti di bahu jalan depan pagar, ada bell di samping gerbang',
                pickupMapsUrl: 'https://maps.google.com/?q=Jl.+Sukajadi+No.+120+Bandung',
                dropoffAddress: 'Terminal 3 Ultimate Bandara Soekarno-Hatta (Drop Zone Gate 2 Keberangkatan)',
                dropoffMapsUrl: 'https://maps.google.com/?q=Terminal+3+Bandara+Soekarno+Hatta',
                paymentStatus: 'Lunas',
                paymentStatusClass: 'pill-paid',
                paymentMethod: 'Mandiri Virtual Account',
                paymentAmount: 'Rp 220.000',
                paymentPaidAt: '25 Sep 2026, 11:00 WIB'
            },
            preview: 'Apakah saya boleh bawa koper ukuran 28 inch untuk penerbangan internasional?',
            timeAgo: '7 menit lalu',
            history: [
                { title: 'Check-in Online Mandiri', date: '25 Sep 2026, 11:00 WIB' }
            ],
            messages: [
                { type: 'customer', text: 'Permisi min, saya bawa koper 28 inch untuk flight malam, muat di bagasi belakang van?', time: '13:08' }
            ]
        },

        // TAB 2: ASSIGNED TO ME (Percakapan aktif yang ditangani CS login)
        {
            id: 4,
            tab: 'assigned',
            urgency: 'mendesak',
            urgencyLabel: 'Berangkat 1.5 Jam Lagi',
            bannerTitle: 'Sedang Ditangani oleh Anda (Aktif)',
            bannerDesc: 'Armada rute Bekasi - Bandung berangkat 14:45 WIB. Penumpang meminta update nomor kontak driver.',
            bannerClass: 'banner-mendesak',
            user: {
                name: 'Dewi Ratnasari',
                phone: '0812-4455-6677',
                email: 'dewi.ratna@mail.com',
                avatar: 'DR',
                tier: 'Silver Member'
            },
            booking: {
                code: 'TRV-2026-0612',
                status: 'Terkonfirmasi',
                route: 'Bekasi Barat ke Bandung (Dipatiukur)',
                departureDate: 'Jumat, 25 Sep 2026',
                departureTime: '14:45 WIB',
                departure: 'Hari Ini, 14:45 WIB',
                countdown: 'Sisa Waktu: 1 Jam 30 Menit',
                seat: '04A',
                seatType: 'Lorong Tengah',
                vehicle: 'HiAce Commuter',
                plate: 'B 1204 KLS',
                driverName: 'Pak Ridwan Kamil',
                driverPhone: '0812-9911-2233',
                driverWaLink: 'https://wa.me/6281299112233',
                pickupType: 'Pool to Pool',
                pickupAddress: 'Pool Bekasi Cyber Park - Ground Floor Lobby Barat No. 12, Bekasi',
                pickupInstruction: 'Ruang tunggu ber-AC depan pangkalan taksi Bluebird',
                pickupMapsUrl: 'https://maps.google.com/?q=Bekasi+Cyber+Park',
                dropoffAddress: 'Pool Dipatiukur - Jl. Dipatiukur No. 44, Bandung',
                dropoffMapsUrl: 'https://maps.google.com/?q=Dipatiukur+Bandung',
                paymentStatus: 'Lunas',
                paymentStatusClass: 'pill-paid',
                paymentMethod: 'BCA KlikPay',
                paymentAmount: 'Rp 160.000',
                paymentPaidAt: '25 Sep 2026, 12:10 WIB'
            },
            preview: 'Terima kasih informasinya kak, saya sudah di ruang tunggu pool ya.',
            timeAgo: 'Aktif sekarang',
            history: [
                { title: 'Konsultasi Penjemputan', date: '25 Sep 2026, 12:40 WIB • CS Anda' }
            ],
            messages: [
                { type: 'customer', text: 'Halo min, saya Dewi nomor tiket TRV-2026-0612. Drivernya sudah standby?', time: '12:40' },
                { type: 'internal_note', text: 'Pelanggan ini minta jemput duluan karena bawa lansia. Kursi depan 01B sudah diamankan & driver Pak Ridwan siap bantu naik koper.', author: 'CS Sarah Wijaya', time: '12:41' },
                { type: 'cs', text: 'Halo Kak Dewi! Driver Pak Ridwan sudah berada di Pool Bekasi Barat ya. Nomor kontak beliau 0812-9911-2233.', time: '12:42' },
                { type: 'customer', text: 'Terima kasih informasinya kak, saya sudah di ruang tunggu pool ya.', time: '12:45' }
            ]
        },
        {
            id: 5,
            tab: 'assigned',
            urgency: 'normal',
            urgencyLabel: 'Jadwal Besok Pagi',
            bannerTitle: 'Sedang Ditangani oleh Anda (Aktif)',
            bannerDesc: 'Pertanyaan seputar tiket jadwal besok pagi pukul 07:00 WIB rute Bandung - Jakarta.',
            bannerClass: 'banner-normal',
            user: {
                name: 'Rian Anggara',
                phone: '0856-7788-9900',
                email: 'rian.a@mail.com',
                avatar: 'RA',
                tier: 'Regular'
            },
            booking: {
                code: 'TRV-2026-0599',
                status: 'Menunggu Pembayaran',
                route: 'Bandung (Dipatiukur) ke Jakarta (Semanggi)',
                departureDate: 'Sabtu, 26 Sep 2026',
                departureTime: '07:00 WIB',
                departure: 'Besok, 07:00 WIB',
                countdown: 'Jadwal Besok Pagi (Pemberangkatan Pertama)',
                seat: '02A',
                seatType: 'Single Window Baris 2',
                vehicle: 'HiAce Premio',
                plate: 'D 7721 Z',
                driverName: 'Pak Bambang (Standby)',
                driverPhone: '0857-1122-3344',
                driverWaLink: 'https://wa.me/6285711223344',
                pickupType: 'Pool to Pool',
                pickupAddress: 'Pool Dipatiukur - Jl. Dipatiukur No. 44, Coblong, Bandung',
                pickupInstruction: 'Lantai 1 ruang tunggu keberangkatan pagi',
                pickupMapsUrl: 'https://maps.google.com/?q=Dipatiukur+Bandung',
                dropoffAddress: 'Plaza Semanggi - Halte Transjakarta / Pintu Selatan, Jakarta',
                dropoffMapsUrl: 'https://maps.google.com/?q=Plaza+Semanggi+Jakarta',
                paymentStatus: 'Menunggu Pembayaran',
                paymentStatusClass: 'pill-pending',
                paymentMethod: 'BCA Virtual Account (Menunggu)',
                paymentAmount: 'Rp 175.000',
                paymentPaidAt: 'Batas Bayar: Hari Ini 23:59 WIB'
            },
            preview: 'Apakah saya bisa ganti nama penumpang di tiket untuk adik saya?',
            timeAgo: '15 menit lalu',
            history: [
                { title: 'Perubahan Tiket Dibuka', date: '25 Sep 2026, 13:00 WIB' }
            ],
            messages: [
                { type: 'customer', text: 'Siang kak, tiket saya TRV-2026-0599 untuk besok pagi bisa diganti nama penumpang?', time: '13:00' },
                { type: 'cs', text: 'Bisa Kak Rian. Silakan kirimkan nama lengkap dan nomor HP adik Kakak ya, akan langsung kami bantu update di sistem.', time: '13:03' }
            ]
        },

        // TAB 3: RESOLVED / CLOSED (Percakapan yang sudah selesai)
        {
            id: 6,
            tab: 'resolved',
            urgency: 'normal',
            urgencyLabel: 'Selesai (CSAT 5)',
            bannerTitle: 'Sesi Chat Telah Selesai (Closed)',
            bannerDesc: 'Tiket telah selesai ditangani dengan sukses. Penumpang memberikan ulasan bintang 5.',
            bannerClass: 'banner-normal',
            user: {
                name: 'Anisa Melani',
                phone: '0819-3344-5566',
                email: 'anisa.m@mail.com',
                avatar: 'AM',
                tier: 'Gold Member'
            },
            booking: {
                code: 'TRV-2026-0421',
                status: 'Selesai',
                route: 'Jakarta (Semanggi) ke Bandung (Pasteur)',
                departureDate: 'Jumat, 25 Sep 2026',
                departureTime: '11:00 WIB',
                departure: 'Telah Berangkat (11:00 WIB)',
                countdown: 'Perjalanan Telah Berlangsung',
                seat: '01A',
                seatType: 'Baris 1 Depan',
                vehicle: 'HiAce Premio',
                plate: 'B 2011 SH',
                driverName: 'Pak Bambang Waluyo',
                driverPhone: '0813-8899-0011',
                driverWaLink: 'https://wa.me/6281388990011',
                pickupType: 'Pool to Pool',
                pickupAddress: 'Plaza Semanggi Lt. Dasar, Kav. 50, Jakarta Selatan',
                pickupInstruction: 'Depan konter resmi TravelKu',
                pickupMapsUrl: 'https://maps.google.com/?q=Plaza+Semanggi+Jakarta',
                dropoffAddress: 'Pool Pasteur - Jl. Dr. Djunjunan No. 135, Bandung',
                dropoffMapsUrl: 'https://maps.google.com/?q=Pool+Pasteur+Bandung',
                paymentStatus: 'Lunas',
                paymentStatusClass: 'pill-paid',
                paymentMethod: 'QRIS ShopeePay',
                paymentAmount: 'Rp 175.000',
                paymentPaidAt: '25 Sep 2026, 10:45 WIB'
            },
            preview: 'Sudah beres ya kak, e-ticket sudah terbit di email saya. Makasih banyak!',
            timeAgo: '1 jam lalu',
            history: [
                { title: 'Tiket Berhasil Diselesaikan', date: '25 Sep 2026, 11:20 WIB • CS Anda' }
            ],
            messages: [
                { type: 'customer', text: 'Kak, email e-ticket saya belum masuk tadi.', time: '10:50' },
                { type: 'agent_handover', text: 'Log Pergantian Agen: Sesi dialihkan dari Agen Citra ke CS Budi Santoso pada 25 Sep 2026, 10:51 WIB. Alasan: Pergantian shift pagi ke siang.', time: '10:51' },
                { type: 'internal_note', text: 'Sudah verifikasi nomor rekening BCA refund sebelumnya dan status pembayaran lunas melalui ShopeePay.', author: 'Agen Citra', time: '10:52' },
                { type: 'cs', text: 'Sudah kami bantu kirim ulang ke anisa.m@mail.com ya kak, mohon dicek di folder inbox / spam.', time: '10:53' },
                { type: 'customer', text: 'Sudah beres ya kak, e-ticket sudah terbit di email saya. Makasih banyak!', time: '10:55' }
            ]
        }
    ];

    let currentActiveTab = 'unassigned';
    let currentActiveChatId = 1;
    let attachedFile = null;

    // Inisialisasi Tampilan Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function () {
        renderInboxList();
        loadConversation(currentActiveChatId);
    });

    // 1. Fungsi Switch Tab Antara Unassigned, Assigned, Resolved
    function switchChatTab(tabName) {
        currentActiveTab = tabName;

        // Update styling tombol tab
        document.querySelectorAll('.inbox-tab-btn').forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        if (activeBtn) activeBtn.classList.add('active');

        renderInboxList();

        // Otomatis pilih item pertama pada tab aktif jika ada
        const itemsInTab = conversations.filter(c => c.tab === tabName);
        if (itemsInTab.length > 0) {
            loadConversation(itemsInTab[0].id);
        } else {
            showEmptyConversationState();
        }
    }

    // 2. Render Daftar Percakapan di Sidebar Inbox
    function renderInboxList() {
        const container = document.getElementById('conversations-list-container');
        if (!container) return;

        // Hitung badge counter masing-masing tab
        const unassignedCount = conversations.filter(c => c.tab === 'unassigned').length;
        const assignedCount = conversations.filter(c => c.tab === 'assigned').length;
        const resolvedCount = conversations.filter(c => c.tab === 'resolved').length;

        const countUn = document.getElementById('count-unassigned');
        const countAs = document.getElementById('count-assigned');
        const countRe = document.getElementById('count-resolved');

        if (countUn) countUn.innerText = unassignedCount;
        if (countAs) countAs.innerText = assignedCount;
        if (countRe) countRe.innerText = resolvedCount;

        // Ambil filter query & urgensi
        const searchQuery = (document.getElementById('chat-search-input')?.value || '').toLowerCase().trim();
        const urgencyFilter = document.getElementById('urgency-filter')?.value || 'all';

        // Filter percakapan
        let filtered = conversations.filter(c => c.tab === currentActiveTab);

        if (searchQuery.length > 0) {
            filtered = filtered.filter(c =>
                c.user.name.toLowerCase().includes(searchQuery) ||
                c.booking.code.toLowerCase().includes(searchQuery) ||
                c.booking.route.toLowerCase().includes(searchQuery) ||
                c.preview.toLowerCase().includes(searchQuery)
            );
        }

        if (urgencyFilter !== 'all') {
            filtered = filtered.filter(c => c.urgency === urgencyFilter);
        }

        // Render HTML
        if (filtered.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px 16px; color: var(--text-muted); font-size: 13px;">
                    <div style="display: flex; justify-content: center; margin-bottom: 8px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color: var(--text-muted); opacity: 0.7;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <strong>Tidak ada percakapan</strong>
                    <p style="font-size: 11px; margin-top: 4px;">Tidak ditemukan tiket pada tab ${currentActiveTab.toUpperCase()}</p>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(chat => {
            const isActive = chat.id === currentActiveChatId ? 'active' : '';
            const urgencyClass = 'urgency-' + chat.urgency;

            html += `
                <div class="chat-item-card ${isActive}" onclick="loadConversation(${chat.id})">
                    <div class="item-top">
                        <div class="item-user-group">
                            <div class="item-avatar">${chat.user.avatar}</div>
                            <span class="item-user-name">${chat.user.name}</span>
                        </div>
                        <span class="item-time">${chat.timeAgo}</span>
                    </div>

                    <div class="item-urgency-pill ${urgencyClass}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>${chat.urgencyLabel}</span>
                    </div>

                    <p class="item-preview-text">${chat.preview}</p>

                    <div class="item-footer-meta">
                        <span>Tiket: <strong>${chat.booking.code}</strong></span>
                        <span>${chat.booking.route.split(' ke ')[0].trim()}</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // 3. Load Detail Percakapan & Konteks Booking Lengkap
    function loadConversation(chatId) {
        currentActiveChatId = chatId;
        const chat = conversations.find(c => c.id === chatId);
        if (!chat) return;

        clearSearchInChat(false);
        setChatSendMode('customer');

        // Update list card active
        document.querySelectorAll('.chat-item-card').forEach(el => el.classList.remove('active'));
        renderInboxList();

        // A. Kolom Percakapan
        const avatarEl = document.getElementById('active-user-avatar');
        const nameEl = document.getElementById('active-user-name');
        const routeEl = document.getElementById('active-user-route');

        if (avatarEl) avatarEl.innerText = chat.user.avatar;
        if (nameEl) nameEl.innerText = chat.user.name;
        if (routeEl) {
            routeEl.innerHTML = `
                <span class="route-pill">${chat.booking.route}</span>
                <span class="time-meta">&bull; Armada Berangkat: <strong>${chat.booking.departure}</strong></span>
            `;
        }

        // Atur tombol aksi atas (Ambil Chat vs Tandai Selesai)
        const claimBtn = document.getElementById('btn-claim-chat');
        const resolveBtn = document.getElementById('btn-resolve-chat');

        if (claimBtn && resolveBtn) {
            if (chat.tab === 'unassigned') {
                claimBtn.style.display = 'inline-flex';
                resolveBtn.style.display = 'none';
            } else if (chat.tab === 'assigned') {
                claimBtn.style.display = 'none';
                resolveBtn.style.display = 'inline-flex';
            } else {
                claimBtn.style.display = 'none';
                resolveBtn.style.display = 'none';
            }
        }

        // Update Banner Urgensi
        const banner = document.getElementById('conv-urgency-banner');
        if (banner) banner.className = 'conv-urgency-banner ' + chat.bannerClass;
        const bannerTitle = document.getElementById('banner-urgency-title');
        const bannerDesc = document.getElementById('banner-urgency-desc');
        if (bannerTitle) bannerTitle.innerText = chat.bannerTitle;
        if (bannerDesc) bannerDesc.innerText = chat.bannerDesc;

        // Render Balon Pesan
        renderMessages(chat.messages);

        // B. Kolom Konteks Booking (5 Bagian Sesuai Kebutuhan)
        // 1. Identitas Tiket
        const pnrEl = document.getElementById('ctx-booking-code');
        const passengerNameEl = document.getElementById('ctx-passenger-name');
        const bookingRouteEl = document.getElementById('ctx-booking-route');
        const bookingDateEl = document.getElementById('ctx-booking-date');
        const bookingTimeEl = document.getElementById('ctx-booking-time');
        const countdownEl = document.getElementById('ctx-countdown');

        if (pnrEl) pnrEl.innerText = chat.booking.code;
        if (passengerNameEl) passengerNameEl.innerText = chat.user.name;
        if (bookingRouteEl) bookingRouteEl.innerHTML = chat.booking.route;
        if (bookingDateEl) bookingDateEl.innerText = chat.booking.departureDate || 'Jumat, 25 Sep 2026';
        if (bookingTimeEl) bookingTimeEl.innerText = chat.booking.departureTime || chat.booking.departure;
        if (countdownEl) countdownEl.innerText = chat.booking.countdown;

        // 2. Detail Kursi & Armada
        const seatEl = document.getElementById('ctx-seat');
        const seatTypeEl = document.getElementById('ctx-seat-type');
        const plateEl = document.getElementById('ctx-plate');
        const vehicleEl = document.getElementById('ctx-vehicle');
        const driverNameEl = document.getElementById('ctx-driver-name');
        const driverPhoneEl = document.getElementById('ctx-driver-phone');
        const driverCallLink = document.getElementById('ctx-driver-call-link');
        const driverWaLink = document.getElementById('ctx-driver-wa-link');

        if (seatEl) seatEl.innerText = chat.booking.seat;
        if (seatTypeEl) seatTypeEl.innerText = chat.booking.seatType || 'Standar';
        if (plateEl) plateEl.innerText = chat.booking.plate;
        if (vehicleEl) vehicleEl.innerText = chat.booking.vehicle;
        if (driverNameEl) driverNameEl.innerText = chat.booking.driverName;
        if (driverPhoneEl) driverPhoneEl.innerText = chat.booking.driverPhone;

        const cleanPhone = chat.booking.driverPhone.replace(/[^0-9]/g, '');
        if (driverCallLink) driverCallLink.href = 'tel:' + cleanPhone;
        if (driverWaLink) driverWaLink.href = chat.booking.driverWaLink || `https://wa.me/62${cleanPhone.replace(/^0/, '')}`;

        // 3. Titik Jemput & Turun
        const pickupTypeEl = document.getElementById('ctx-pickup-type');
        const pickupAddressEl = document.getElementById('ctx-pickup-address');
        const pickupInstructionEl = document.getElementById('ctx-pickup-instruction');
        const pickupMapsLink = document.getElementById('ctx-pickup-maps-link');
        const dropoffAddressEl = document.getElementById('ctx-dropoff-address');

        if (pickupTypeEl) pickupTypeEl.innerText = chat.booking.pickupType;
        if (pickupAddressEl) pickupAddressEl.innerText = chat.booking.pickupAddress;
        if (pickupInstructionEl) pickupInstructionEl.innerText = chat.booking.pickupInstruction;
        if (pickupMapsLink) pickupMapsLink.href = chat.booking.pickupMapsUrl;
        if (dropoffAddressEl) dropoffAddressEl.innerText = chat.booking.dropoffAddress;

        // 4. Status Pembayaran
        const paymentStatusEl = document.getElementById('ctx-payment-status');
        const paymentMethodEl = document.getElementById('ctx-payment-method');
        const paymentAmountEl = document.getElementById('ctx-payment-amount');
        const paymentTimeEl = document.getElementById('ctx-payment-time');

        if (paymentStatusEl) {
            paymentStatusEl.innerText = chat.booking.paymentStatus;
            paymentStatusEl.className = 'ctx-payment-pill ' + (chat.booking.paymentStatusClass || 'pill-paid');
        }
        if (paymentMethodEl) paymentMethodEl.innerText = chat.booking.paymentMethod;
        if (paymentAmountEl) paymentAmountEl.innerText = chat.booking.paymentAmount;
        if (paymentTimeEl) paymentTimeEl.innerText = chat.booking.paymentPaidAt;

        // Profil Kontak Tambahan
        const pPhone = document.getElementById('ctx-passenger-phone');
        const pEmail = document.getElementById('ctx-passenger-email');
        const pTier = document.getElementById('ctx-passenger-tier');

        if (pPhone) pPhone.innerText = chat.user.phone;
        if (pEmail) pEmail.innerText = chat.user.email;
        if (pTier) pTier.innerText = chat.user.tier;
    }

    // Highlight Text untuk Pencarian Kata Kunci
    function highlightQuery(text, query) {
        if (!query || !text) return text;
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safeQuery})`, 'gi');
        return text.replace(regex, '<mark class="search-highlight">$1</mark>');
    }

    // 4. Render Balon Chat, Catatan Internal Antar-CS, dan Log Pergantian Agen (PRD 4.5)
    function renderMessages(messages, searchQuery = '') {
        const container = document.getElementById('messages-container');
        if (!container) return;

        let html = '';
        function renderAttachmentHtml(att) {
            if (!att) return '';
            if (att.type === 'ticket_pdf') {
                return `
                    <div class="chat-bubble-attachment ticket-pdf">
                        <div class="ticket-att-header">
                            <span class="ticket-att-badge">E-Tiket Resmi (PDF)</span>
                            <span class="ticket-att-size">${att.fileSize || '124 KB'}</span>
                        </div>
                        <div class="ticket-att-body">
                            <strong>${att.pnr || ''} &bull; ${att.passenger || ''}</strong>
                            <div class="ticket-att-sub">${att.route || ''} &bull; Kursi ${att.seat || ''}</div>
                        </div>
                        <button type="button" class="ticket-att-download-btn" onclick="alert('Mengunduh e-tiket resmi ${att.fileName}...')">
                            Unduh E-Tiket (${att.fileName})
                        </button>
                    </div>
                `;
            } else if (att.type === 'compressed_image') {
                return `
                    <div class="chat-bubble-attachment compressed-img">
                        <div class="att-img-preview-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <span>${att.fileName}</span>
                        </div>
                        <span class="comp-quota-tag">Kompresi Otomatis: ${att.origSize} ke ${att.compSize} (Hemat ${att.quotaSaved} kuota di jalan)</span>
                    </div>
                `;
            } else if (att.type === 'document_pdf') {
                return `
                    <div class="chat-bubble-attachment doc-pdf">
                        <div class="att-pdf-preview-box">
                            <span style="font-size: 20px;">📄</span>
                            <div class="pdf-details">
                                <strong>${att.fileName}</strong>
                                <span class="pdf-size">Dokumen PDF &bull; ${att.fileSize}</span>
                            </div>
                            <button type="button" class="btn-pdf-dl" onclick="alert('Mengunduh dokumen ${att.fileName}...')">Unduh</button>
                        </div>
                    </div>
                `;
            }
            return '';
        }

        messages.forEach(m => {
            if (m.type === 'system') {
                html += `<div class="chat-system-event">${highlightQuery(m.text, searchQuery)}</div>`;
            } else if (m.type === 'agent_handover') {
                html += `
                    <div class="chat-agent-handover-log">
                        <div class="handover-log-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        </div>
                        <div class="handover-log-text">
                            <strong>${highlightQuery(m.text, searchQuery)}</strong>
                        </div>
                        <div class="handover-log-time">${m.time}</div>
                    </div>
                `;
            } else if (m.type === 'internal_note') {
                html += `
                    <div class="chat-internal-note-box">
                        <div class="internal-note-header">
                            <span class="internal-note-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Catatan Internal Khusus Antar-CS (Tidak Terlihat Pelanggan)
                            </span>
                            <span class="internal-note-meta">${m.author || 'CS'} &bull; ${m.time}</span>
                        </div>
                        <div class="internal-note-content">
                            ${highlightQuery(m.text, searchQuery)}
                            ${renderAttachmentHtml(m.attachment)}
                        </div>
                    </div>
                `;
            } else if (m.type === 'customer') {
                html += `
                    <div class="chat-bubble-wrapper customer">
                        <div class="bubble-author">Penumpang</div>
                        <div class="bubble-box">
                            ${highlightQuery(m.text, searchQuery)}
                            ${renderAttachmentHtml(m.attachment)}
                        </div>
                        <div class="bubble-time">${m.time}</div>
                    </div>
                `;
            } else {
                html += `
                    <div class="chat-bubble-wrapper cs">
                        <div class="bubble-author">${m.author || currentCsName} (CS)</div>
                        <div class="bubble-box">
                            ${highlightQuery(m.text, searchQuery)}
                            ${renderAttachmentHtml(m.attachment)}
                        </div>
                        <div class="bubble-time">${m.time}</div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
    }

    // 4.B Fitur Pencarian Kata Kunci dalam Percakapan (Search in Chat - PRD 4.5)
    let currentSearchQuery = '';
    function handleSearchInChat(query) {
        currentSearchQuery = (query || '').trim();
        const counterEl = document.getElementById('search-chat-counter');
        const clearBtn = document.getElementById('btn-clear-search-chat');
        const chat = conversations.find(c => c.id === currentActiveChatId);

        if (!chat) return;

        if (!currentSearchQuery) {
            if (counterEl) counterEl.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
            renderMessages(chat.messages);
            return;
        }

        if (clearBtn) clearBtn.style.display = 'inline-block';

        const safeQuery = currentSearchQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(safeQuery, 'gi');
        let matchCount = 0;

        chat.messages.forEach(m => {
            const text = m.text || '';
            const matches = text.match(regex);
            if (matches) matchCount += matches.length;
        });

        if (counterEl) {
            counterEl.style.display = 'inline-block';
            counterEl.innerText = `${matchCount} pesan cocok`;
        }

        renderMessages(chat.messages, currentSearchQuery);
    }

    function clearSearchInChat(doRender = true) {
        currentSearchQuery = '';
        const input = document.getElementById('search-in-chat-input');
        const counterEl = document.getElementById('search-chat-counter');
        const clearBtn = document.getElementById('btn-clear-search-chat');

        if (input) input.value = '';
        if (counterEl) counterEl.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';

        if (doRender) {
            const chat = conversations.find(c => c.id === currentActiveChatId);
            if (chat) renderMessages(chat.messages);
        }
    }

    // 4.C Switch Mode Balas Penumpang vs Catatan Internal Antar-CS (Kuning - PRD 4.5)
    let currentChatMode = 'customer'; // 'customer' | 'internal'
    function setChatSendMode(mode) {
        currentChatMode = mode;
        const tabCustomer = document.getElementById('tab-mode-customer');
        const tabInternal = document.getElementById('tab-mode-internal');
        const hintText = document.getElementById('mode-hint-text');
        const input = document.getElementById('chat-message-input');
        const sendBtn = document.getElementById('btn-send-message');
        const sendLabel = document.getElementById('btn-send-label');

        if (mode === 'internal') {
            if (tabCustomer) tabCustomer.classList.remove('active');
            if (tabInternal) tabInternal.classList.add('active');
            if (hintText) hintText.innerHTML = '<strong style="color: #854d0e;">Catatan Internal Antar-CS:</strong> Tidak terlihat oleh penumpang';
            if (input) {
                input.placeholder = 'Tulis catatan kuning khusus antar-CS (misal: "Pelanggan minta jemput duluan karena bawa lansia")...';
                input.focus();
            }
            if (sendBtn) {
                sendBtn.style.background = '#eab308';
                sendBtn.style.borderColor = '#ca8a04';
                sendBtn.style.color = '#713f12';
            }
            if (sendLabel) sendLabel.innerText = 'Simpan Catatan';
        } else {
            if (tabCustomer) tabCustomer.classList.add('active');
            if (tabInternal) tabInternal.classList.remove('active');
            if (hintText) hintText.innerText = 'Pesan dikirim langsung ke penumpang';
            if (input) {
                input.placeholder = 'Ketik balasan untuk penumpang...';
                input.focus();
            }
            if (sendBtn) {
                sendBtn.style.background = '';
                sendBtn.style.borderColor = '';
                sendBtn.style.color = '';
            }
            if (sendLabel) sendLabel.innerText = 'Kirim';
        }
    }

    // 5. Aksi: Ambil Percakapan (Assign to Me)
    function claimActiveChat() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        chat.tab = 'assigned';
        chat.bannerTitle = 'Sedang Ditangani oleh Anda (Aktif)';
        chat.bannerClass = 'banner-mendesak';
        chat.messages.push({
            type: 'system',
            text: `Percakapan diambil alih oleh ${currentCsName} pada ${new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`
        });

        switchChatTab('assigned');
        loadConversation(chat.id);
    }

    // 6. Aksi: Tandai Selesai (Resolve / Close)
    function resolveActiveChat() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        if (!confirm(`Selesaikan tiket penanganan untuk ${chat.user.name}?`)) {
            return;
        }

        chat.tab = 'resolved';
        chat.urgencyLabel = 'Selesai Ditangani';
        chat.bannerTitle = 'Sesi Chat Telah Selesai (Closed)';
        chat.bannerClass = 'banner-normal';
        chat.messages.push({
            type: 'system',
            text: `Percakapan ditandai selesai oleh ${currentCsName}. Tiket dipindahkan ke tab Resolved.`
        });

        switchChatTab('resolved');
        loadConversation(chat.id);
    }

    // 7. Pengiriman Pesan, Lampiran Media & Catatan Internal Antar-CS
    function handleSendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-message-input');
        const text = input ? input.value.trim() : '';

        if (!text && !attachedFile) return;

        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        let attachmentObj = null;
        if (attachedFile) {
            if (attachedFile.isPdf) {
                attachmentObj = {
                    type: 'document_pdf',
                    fileName: attachedFile.name,
                    fileSize: attachedFile.origSize
                };
            } else {
                attachmentObj = {
                    type: 'compressed_image',
                    fileName: attachedFile.name,
                    origSize: attachedFile.origSize,
                    compSize: attachedFile.compSize,
                    quotaSaved: attachedFile.quotaSaved
                };
            }
        }

        if (currentChatMode === 'internal') {
            chat.messages.push({
                type: 'internal_note',
                text: text || `[Lampiran Operasional: ${attachedFile.name}]`,
                attachment: attachmentObj,
                author: currentCsName,
                time: now
            });
            chat.history.push({
                title: 'Catatan Internal & Media Antar-CS Ditambahkan',
                date: `${now} WIB • ${currentCsName}`
            });
            renderMessages(chat.messages, currentSearchQuery);
        } else {
            chat.messages.push({
                type: 'cs',
                text: text || (attachedFile ? `Berikut kami lampirkan dokumen/media terkait perjalanan Anda:` : ''),
                attachment: attachmentObj,
                author: currentCsName,
                time: now
            });

            chat.preview = 'Anda: ' + (text || (attachedFile ? `Melampirkan ${attachedFile.name}` : ''));
            chat.timeAgo = 'Baru saja';

            if (chat.tab === 'unassigned') {
                chat.tab = 'assigned';
                switchChatTab('assigned');
            } else {
                renderInboxList();
                renderMessages(chat.messages, currentSearchQuery);
            }
        }

        if (input) input.value = '';
        clearAttachedFile();
    }

    // 8. Canned Responses
    function applyCannedReply(text) {
        const input = document.getElementById('chat-message-input');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    // 9. Attachment Handling (Format: JPG, PNG, PDF + Kompresi Otomatis - PRD 4.5)
    function handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        if (!['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
            alert('Format tidak didukung. Sistem hanya mendukung Gambar (JPG, PNG) dan Dokumen (PDF).');
            e.target.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file melebihi batas maksimal 5 MB.');
            e.target.value = '';
            return;
        }

        const isPdf = ext === 'pdf';
        let origFormatted = formatFileSize(file.size);
        let compFormatted = origFormatted;
        let quotaSavings = '0%';

        if (!isPdf) {
            // Kompresi otomatis gambar hemat kuota di jalan (89%)
            const compBytes = Math.round(file.size * 0.11);
            compFormatted = formatFileSize(compBytes);
            quotaSavings = '89%';
        }

        attachedFile = {
            name: file.name,
            isPdf: isPdf,
            origSize: origFormatted,
            compSize: compFormatted,
            quotaSaved: quotaSavings
        };

        const pill = document.getElementById('file-preview-pill');
        const nameSpan = document.getElementById('file-preview-name');
        const iconSpan = document.getElementById('file-preview-icon');
        const compTag = document.getElementById('file-preview-compression');

        if (pill && nameSpan) {
            nameSpan.innerText = file.name;
            if (iconSpan) iconSpan.innerText = isPdf ? '' : '';
            if (compTag) {
                if (isPdf) {
                    compTag.innerText = `Dokumen PDF (${origFormatted})`;
                    compTag.className = 'compression-tag-pill pill-pdf';
                } else {
                    compTag.innerText = `Kompresi Otomatis: ${origFormatted} ke ${compFormatted} (-${quotaSavings} hemat kuota)`;
                    compTag.className = 'compression-tag-pill';
                }
            }
            pill.style.display = 'flex';
        }
    }

    function formatFileSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return Math.round(bytes / 1024) + ' KB';
    }

    function clearAttachedFile() {
        attachedFile = null;
        const input = document.getElementById('chat-file-upload');
        const pill = document.getElementById('file-preview-pill');
        if (input) input.value = '';
        if (pill) pill.style.display = 'none';
    }

    // 9.B Generator Tiket Instan 1-Klik (PDF/Gambar - PRD 4.5)
    function openInstantTicketModal() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const pnrEl = document.getElementById('modal-ticket-pnr');
        const passEl = document.getElementById('modal-ticket-passenger');
        const routeEl = document.getElementById('modal-ticket-route');
        const seatEl = document.getElementById('modal-ticket-seat');

        if (pnrEl) pnrEl.innerText = chat.booking.code;
        if (passEl) passEl.innerText = chat.user.name;
        if (routeEl) routeEl.innerHTML = chat.booking.route;
        if (seatEl) seatEl.innerText = `${chat.booking.vehicle} • Kursi ${chat.booking.seat}`;

        const modal = document.getElementById('modal-instant-ticket');
        if (modal) modal.style.display = 'flex';
    }

    function closeInstantTicketModal() {
        const modal = document.getElementById('modal-instant-ticket');
        if (modal) modal.style.display = 'none';
    }

    function downloadInstantTicketPdf() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;
        alert(`Mengunduh file resmi e-tiket: TravelKu-${chat.booking.code}.pdf (124 KB)`);
    }

    function sendInstantTicketFromModal() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        chat.messages.push({
            type: 'cs',
            author: currentCsName,
            text: `Halo ${chat.user.name}, berikut kami lampirkan e-tiket resmi perjalanan Anda berformat PDF. Silakan tunjukkan QR/Barcode saat boarding ke sopir/loket pool:`,
            attachment: {
                type: 'ticket_pdf',
                fileName: `E-Tiket-Resmi-${chat.booking.code}.pdf`,
                fileSize: '124 KB',
                pnr: chat.booking.code,
                passenger: chat.user.name,
                route: chat.booking.route,
                seat: chat.booking.seat,
                vehicle: chat.booking.vehicle
            },
            time: now
        });

        chat.preview = `Anda: Melampirkan E-Tiket Resmi (${chat.booking.code})`;
        chat.timeAgo = 'Baru saja';

        renderInboxList();
        renderMessages(chat.messages, currentSearchQuery);
        closeInstantTicketModal();
    }

    // 10. Filter & Search
    function filterChatConversations() {
        renderInboxList();
    }

    function showEmptyConversationState() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.innerHTML = `
                <div style="text-align: center; margin: auto; color: var(--text-muted);">
                    <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="color: var(--brand); opacity: 0.6;">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <strong style="color: var(--text);">Tidak ada percakapan aktif yang dipilih</strong>
                    <p style="font-size: 12px; margin-top: 4px;">Pilih salah satu pesan dari antrean di sebelah kiri.</p>
                </div>
            `;
        }
    }

    // 11. Salin Kode Booking (PNR)
    function copyBookingCode() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        navigator.clipboard.writeText(chat.booking.code).then(() => {
            alert(`Kode Booking (PNR) "${chat.booking.code}" berhasil disalin ke clipboard.`);
        }).catch(() => {
            prompt('Salin Kode Booking:', chat.booking.code);
        });
    }

    // 12. Modal Aksi Cepat: Ubah Kursi
    const availableSeatsList = [
        { no: '01A', type: 'Depan Jendela' },
        { no: '01B', type: 'Depan Driver' },
        { no: '02A', type: 'Jendela Kiri' },
        { no: '02B', type: 'Jendela Kanan' },
        { no: '03A', type: 'Single Window' },
        { no: '03B', type: 'Tengah Lorong' },
        { no: '04A', type: 'Baris 4 Kiri' },
        { no: '04B', type: 'Baris 4 Kanan' },
        { no: '05A', type: 'Belakang Kiri' },
        { no: '05B', type: 'Belakang Kanan' }
    ];

    function openChangeSeatModal() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        document.getElementById('modal-seat-pnr').innerText = chat.booking.code;
        document.getElementById('modal-seat-passenger').innerText = chat.user.name;
        document.getElementById('modal-seat-current').innerText = `${chat.booking.seat} (${chat.booking.seatType || 'Standar'})`;

        const container = document.getElementById('seat-picker-container');
        if (container) {
            let html = '';
            availableSeatsList.forEach(s => {
                const isCurrent = s.no === chat.booking.seat;
                html += `
                    <button type="button"
                            class="seat-btn-pick ${isCurrent ? 'disabled' : ''}"
                            ${isCurrent ? 'disabled title="Kursi saat ini"' : `onclick="pickSeatOption('${s.no}', '${s.type}')"`}
                            id="seat-opt-${s.no}">
                        <div>${s.no}</div>
                        <div style="font-size: 8px; opacity: 0.7;">${s.no.endsWith('A') ? 'Jendela' : 'Lorong'}</div>
                    </button>
                `;
            });
            container.innerHTML = html;
        }

        document.getElementById('selected-new-seat').value = '';
        document.getElementById('seat-selection-hint').innerText = 'Klik salah satu kursi di atas untuk memilih.';
        document.getElementById('modal-change-seat').style.display = 'flex';
    }

    function pickSeatOption(seatNo, seatType) {
        document.querySelectorAll('.seat-btn-pick').forEach(el => el.classList.remove('active'));
        const targetBtn = document.getElementById('seat-opt-' + seatNo);
        if (targetBtn) targetBtn.classList.add('active');

        document.getElementById('selected-new-seat').value = seatNo;
        document.getElementById('seat-selection-hint').innerHTML = `Kursi dipilih: <strong style="color: var(--brand-dark);">${seatNo} (${seatType})</strong>`;
    }

    function closeChangeSeatModal() {
        const modal = document.getElementById('modal-change-seat');
        if (modal) modal.style.display = 'none';
    }

    function submitChangeSeat(e) {
        e.preventDefault();
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const newSeatNo = document.getElementById('selected-new-seat').value;
        if (!newSeatNo) {
            alert('Silakan pilih salah satu kursi baru terlebih dahulu.');
            return;
        }

        const seatObj = availableSeatsList.find(s => s.no === newSeatNo);
        const reason = document.getElementById('change-seat-reason').value;
        const oldSeat = chat.booking.seat;

        chat.booking.seat = newSeatNo;
        chat.booking.seatType = seatObj ? seatObj.type : 'Standar';

        chat.messages.push({
            type: 'system',
            text: `Kursi penumpang berhasil diubah dari ${oldSeat} ke ${newSeatNo} (${chat.booking.seatType}). Alasan: "${reason}". Dilakukan oleh ${currentCsName}.`
        });

        alert(`Kursi penumpang ${chat.user.name} berhasil diubah ke ${newSeatNo}!`);
        closeChangeSeatModal();
        loadConversation(chat.id);
    }

    // 13. Modal Aksi Cepat: Reschedule
    function openRescheduleModal() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        document.getElementById('modal-reschedule-pnr').innerText = chat.booking.code;
        document.getElementById('modal-reschedule-route').innerHTML = chat.booking.route;
        document.getElementById('modal-reschedule-old').innerText = `${chat.booking.departureDate || 'Hari ini'} • ${chat.booking.departureTime || chat.booking.departure}`;

        document.getElementById('modal-reschedule').style.display = 'flex';
    }

    function closeRescheduleModal() {
        const modal = document.getElementById('modal-reschedule');
        if (modal) modal.style.display = 'none';
    }

    function submitReschedule(e) {
        e.preventDefault();
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const newDate = document.getElementById('reschedule-new-date').value;
        const newTime = document.getElementById('reschedule-new-time').value;
        const reason = document.getElementById('reschedule-reason').value;

        chat.booking.departureDate = newDate;
        chat.booking.departureTime = newTime;
        chat.booking.departure = `${newDate}, ${newTime}`;
        chat.booking.countdown = 'Jadwal Baru Terkonfirmasi';
        chat.urgency = 'normal';
        chat.urgencyLabel = 'Reschedule Terkonfirmasi';

        chat.messages.push({
            type: 'system',
            text: `Jadwal perjalanan berhasil di-reschedule ke ${newDate} pukul ${newTime}. Alasan: "${reason}". Diproses oleh CS ${currentCsName}.`
        });

        alert(`Jadwal tiket ${chat.booking.code} berhasil di-reschedule ke ${newDate} (${newTime}).`);
        closeRescheduleModal();
        loadConversation(chat.id);
    }

    // 14. Modal Aksi Cepat: Ajukan Pembatalan (PRD 4.5)
    function openCancellationModal() {
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        document.getElementById('modal-cancel-pnr').innerText = chat.booking.code;
        document.getElementById('modal-cancel-passenger').innerText = chat.user.name;
        document.getElementById('modal-cancel-amount').innerText = chat.booking.paymentAmount;

        document.getElementById('modal-cancellation').style.display = 'flex';
    }

    function closeCancellationModal() {
        const modal = document.getElementById('modal-cancellation');
        if (modal) modal.style.display = 'none';
    }

    function submitCancellation(e) {
        e.preventDefault();
        const chat = conversations.find(c => c.id === currentActiveChatId);
        if (!chat) return;

        const reason = document.getElementById('cancel-reason-select').value;
        const refundAccount = document.getElementById('cancel-refund-account').value;
        const notes = document.getElementById('cancel-notes').value;

        chat.booking.paymentStatus = 'Pengajuan Pembatalan';
        chat.booking.paymentStatusClass = 'pill-expired';
        chat.booking.status = 'Menunggu Refund Admin';

        chat.messages.push({
            type: 'system',
            text: `Pengajuan pembatalan tiket ${chat.booking.code} telah diteruskan ke Admin Utama & Tim Keuangan. Alasan: "${reason}". Rekening Refund: "${refundAccount}". Catatan CS: "${notes || '-'}"`
        });

        alert(`Pengajuan pembatalan tiket ${chat.booking.code} berhasil dikirimkan ke Admin & Tim Keuangan untuk diproses refund.`);
        closeCancellationModal();
        loadConversation(chat.id);
    }

    // 15. Modal Eskalasi ke Admin Utama
    function openEscalateModal() {
        const modal = document.getElementById('escalate-modal');
        if (modal) modal.style.display = 'flex';
    }

    function closeEscalateModal() {
        const modal = document.getElementById('escalate-modal');
        if (modal) modal.style.display = 'none';
    }

    function submitEscalation(e) {
        e.preventDefault();
        const cat = document.getElementById('escalate-category').value;
        const sev = document.getElementById('escalate-severity').value;
        const rootCause = document.getElementById('escalate-root-cause').value;
        const supportingData = document.getElementById('escalate-supporting-data').value;
        const chat = conversations.find(c => c.id === currentActiveChatId);

        if (!chat) return;

        chat.messages.push({
            type: 'system',
            text: `[ESKALASI KE ADMIN UTAMA] Kategori: ${cat} | Prioritas: ${sev} | Status: Pending di Admin\n• Akar Masalah: "${rootCause}"\n• Data Pendukung: "${supportingData}"`
        });

        alert(`Tiket eskalasi booking ${chat.booking.code} berhasil dikirim ke Admin Utama!\nKategori: ${cat}\nPrioritas: ${sev}\nStatus Pelacakan: Pending di Admin`);
        closeEscalateModal();
        renderMessages(chat.messages);
    }

    // 16. Toggle Sidebar Konteks Booking (Drawer pada Tablet/Mobile)
    function toggleContextSidebar() {
        const sidebar = document.getElementById('booking-context');
        if (sidebar) {
            sidebar.classList.toggle('active-drawer');
            if (window.innerWidth > 1280) {
                sidebar.style.display = (sidebar.style.display === 'none') ? 'flex' : '';
            }
        }
    }

    // Otomatis buka jika terdapat hash #booking-context
    if (window.location.hash === '#booking-context') {
        const sidebar = document.getElementById('booking-context');
        if (sidebar) {
            sidebar.classList.add('active-drawer');
            if (window.innerWidth <= 1280) {
                sidebar.style.display = 'flex';
            }
            sidebar.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endpush
