@extends('layouts.customer-service')

@section('title', 'Melihat Riwayat Chat - Customer Service')

@section('content')
<div class="cs-chat-history-wrapper">

    <!-- Top Hub Header -->
    <div class="hist-page-header">
        <div class="header-info">
            <div class="hist-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Rekam Jejak Komunikasi Multi-Sesi Pelanggan</span>
            </div>
            <h1 class="header-title">Melihat Riwayat Chat</h1>
            <p class="header-subtitle">
                Rekam jejak komunikasi agar CS tidak perlu menanyakan hal yang sama berulang kali. Dilengkapi pencarian kata kunci riwayat, catatan internal kuning khusus antar-CS, dan log pergantian agen.
            </p>
        </div>

        <div class="header-action-group">
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Buka Live Chat</span>
            </a>
            <a href="{{ route('dashboard.customer-service.booking-context') }}" class="cs-btn cs-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span>Melihat Konteks Booking</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik Riwayat Komunikasi -->
    <div class="hist-stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap stat-green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
            </div>
            <div class="stat-details">
                <span class="stat-num" id="stat-total-sessions">1.248</span>
                <span class="stat-label">Total Riwayat Percakapan</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="stat-details">
                <span class="stat-num" id="stat-internal-notes">312</span>
                <span class="stat-label">Catatan Internal Antar-CS</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
            </div>
            <div class="stat-details">
                <span class="stat-num" id="stat-handover-count">86</span>
                <span class="stat-label">Log Pergantian Agen</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <div class="stat-details">
                <span class="stat-num">4.9 / 5.0</span>
                <span class="stat-label">Tingkat Kepuasan (CSAT)</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="hist-controls-bar">
        <div class="hist-search-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="hist-search-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text"
                   id="history-global-search"
                   placeholder="Cari arsip: Nama Pelanggan, Kode PNR, Nomor HP, atau Kata Kunci Chat..."
                   oninput="handleHistoryGlobalSearch(this.value)"
                   autocomplete="off">
            <button type="button" class="hist-clear-btn" id="btn-clear-global-search" onclick="clearHistoryGlobalSearch()" style="display: none;">&times;</button>
        </div>

        <div class="hist-filter-chips">
            <button type="button" class="filter-chip active" onclick="setHistoryFilter('all', this)">Semua Riwayat</button>
            <button type="button" class="filter-chip chip-amber" onclick="setHistoryFilter('notes', this)">🔒 Ada Catatan Internal</button>
            <button type="button" class="filter-chip chip-blue" onclick="setHistoryFilter('handover', this)">🔄 Ada Pergantian Agen</button>
            <button type="button" class="filter-chip" onclick="setHistoryFilter('resolved', this)">Selesai (Closed)</button>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="hist-main-grid">

        <!-- Kolom 1: Daftar Sesi Percakapan Arsip (Left Column) -->
        <aside class="hist-sessions-sidebar">
            <div class="sessions-sidebar-header">
                <div>
                    <h2 class="sidebar-title">Daftar Rekam Jejak Percakapan</h2>
                    <span class="sidebar-subtext">Pilih percakapan untuk meninjau riwayat lengkap</span>
                </div>
                <span class="sessions-count-pill" id="sessions-count-badge">6 Sesi</span>
            </div>

            <div class="sessions-cards-list" id="sessions-container">
                <!-- Dynamically rendered by JavaScript -->
            </div>
        </aside>

        <!-- Kolom 2: Transkrip Riwayat Komunikasi & Fitur Lengkap (Right Column) -->
        <main class="hist-detail-panel">

            <!-- Detail Header Pelanggan & Booking -->
            <div class="transcript-header-card">
                <div class="customer-profile-strip">
                    <div class="customer-avatar-lg" id="viewer-avatar">BP</div>
                    <div class="customer-info-main">
                        <div class="name-tier-row">
                            <h3 class="customer-name-lg" id="viewer-name">Budi Prasetyo</h3>
                            <span class="tier-pill" id="viewer-tier">Silver Member</span>
                            <span class="status-pill status-closed" id="viewer-status-pill">Selesai Ditangani</span>
                        </div>
                        <div class="customer-meta-row">
                            <span>📞 <strong id="viewer-phone">0812-9988-7766</strong></span>
                            <span>✉️ <span id="viewer-email">budi.p@mail.com</span></span>
                            <span>🎫 PNR: <strong id="viewer-pnr">TRV-2026-0925</strong></span>
                            <span>🚌 <span id="viewer-route">Bandung &rarr; Jakarta</span></span>
                        </div>
                    </div>
                </div>

                <div class="transcript-quick-actions">
                    <button type="button" class="cs-btn-sm cs-btn-secondary" onclick="exportTranscript()" title="Ekspor atau Cetak Riwayat Chat">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Cetak Transkrip</span>
                    </button>
                    <a href="{{ route('dashboard.customer-service.booking-context') }}" class="cs-btn-sm cs-btn-primary" id="btn-view-booking-context">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Lihat Konteks Tiket</span>
                    </a>
                </div>
            </div>

            <!-- FITUR UTAMA 1: Pencarian Kata Kunci (Search in Chat - PRD 4.5) -->
            <div class="search-in-chat-card">
                <div class="search-in-chat-header">
                    <div class="search-label-group">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="search-icon-amber">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <strong>Pencarian Kata Kunci (Search in Chat)</strong>
                        <span class="search-sublabel">Cari teks lama: nomor rekening, nama yang pernah disebut, alamat, refund, atau instruksi lansia</span>
                    </div>
                    <span class="search-match-count" id="inchat-match-badge" style="display: none;">0 pesan ditemukan</span>
                </div>

                <div class="search-in-chat-input-row">
                    <div class="inchat-input-wrap">
                        <input type="text"
                               id="inchat-search-input"
                               placeholder="Ketik kata kunci untuk mencari di dalam riwayat chat ini..."
                               oninput="handleSearchInCurrentChat(this.value)"
                               autocomplete="off">
                        <button type="button" class="inchat-clear-btn" id="btn-clear-inchat" onclick="clearSearchInCurrentChat()" style="display: none;">&times;</button>
                    </div>
                </div>

                <div class="quick-keyword-chips">
                    <span class="chips-title">Kata Kunci Cepat:</span>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('nomor rekening')">🔍 nomor rekening</button>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('lansia')">🔍 lansia</button>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('BCA')">🔍 BCA</button>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('reschedule')">🔍 reschedule</button>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('jemput')">🔍 jemput</button>
                    <button type="button" class="keyword-chip" onclick="applyKeywordSearch('bagasi')">🔍 bagasi</button>
                </div>
            </div>

            <!-- FITUR UTAMA 2: Rekam Jejak Komunikasi Lengkap & Balon Percakapan -->
            <div class="transcript-feed-container" id="transcript-feed">
                <!-- Messages populated by JavaScript -->
            </div>

            <!-- FITUR UTAMA 3: Form Tambah Catatan Internal Baru (Kuning Khusus Antar-CS) -->
            <div class="add-internal-note-card">
                <div class="internal-note-form-header">
                    <div class="note-form-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="lock-icon-amber">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <strong>Tambah Catatan Internal (Warna Kuning Khusus Antar-CS)</strong>
                    </div>
                    <span class="note-confidential-pill">🔒 Tidak Dapat Dilihat oleh Pelanggan</span>
                </div>
                <p class="note-form-desc">
                    Kolom komentar kuning khusus antar-CS agar agen berikutnya langsung mengetahui konteks spesifik penumpang tanpa perlu menanyakan ulang.
                </p>

                <form onsubmit="handleSaveInternalNote(event)" class="internal-note-form">
                    <textarea id="new-internal-note-input"
                              rows="2"
                              class="internal-note-textarea"
                              placeholder="Misal: 'Pelanggan ini minta jemput duluan karena bawa lansia' atau 'Sudah konfirmasi nomor rekening refund BCA 883019281 a.n Budi Prasetyo'..."
                              required></textarea>
                    <div class="internal-note-form-footer">
                        <span class="note-author-hint">Dicatat oleh: <strong>{{ auth()->user()->name ?? 'Customer Service' }}</strong></span>
                        <button type="submit" class="cs-btn cs-btn-amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Simpan Catatan Internal (Kuning)</span>
                        </button>
                    </div>
                </form>
            </div>

        </main>
    </div>

</div>

@push('styles')
<style>
    /* ============================================================
       DESIGN TOKENS & ROOT STYLES (MATCHING ADMIN UTAMA)
    ============================================================ */
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
        --danger: #dc2626;
        --warning: #d97706;
        --info: #2563eb;
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 14px;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.07);
    }

    .cs-chat-history-wrapper {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding-bottom: 40px;
    }

    /* Page Header */
    .hist-page-header {
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

    .hist-badge-pill {
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
        letter-spacing: -0.01em;
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

    .cs-btn-primary {
        background: var(--brand);
        color: white;
    }

    .cs-btn-primary:hover {
        background: var(--brand-dark);
        color: white;
    }

    .cs-btn-secondary {
        background: var(--page);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .cs-btn-secondary:hover {
        background: #e2e8f0;
        color: var(--text);
    }

    .cs-btn-amber {
        background: #eab308;
        color: #713f12;
        border: 1px solid #ca8a04;
    }

    .cs-btn-amber:hover {
        background: #ca8a04;
        color: white;
    }

    .cs-btn-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: 0.15s ease;
        border: none;
        font-family: inherit;
    }

    /* Statistics Grid */
    .hist-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
    }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-green { background: #dcfce7; color: #15803d; }
    .stat-amber { background: #fef3c7; color: #b45309; }
    .stat-blue  { background: #dbeafe; color: #1d4ed8; }
    .stat-purple{ background: #f3e8ff; color: #7e22ce; }

    .stat-details {
        display: flex;
        flex-direction: column;
    }

    .stat-num {
        font-size: 19px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 600;
        margin-top: 2px;
    }

    /* Search & Filter Controls */
    .hist-controls-bar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: var(--shadow-sm);
    }

    .hist-search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        max-width: 580px;
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 8px 14px;
        transition: 0.15s ease;
    }

    .hist-search-box:focus-within {
        border-color: var(--brand);
        background: var(--surface);
        box-shadow: 0 0 0 2px rgba(8, 127, 85, 0.12);
    }

    .hist-search-icon {
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .hist-search-box input {
        border: none;
        outline: none;
        width: 100%;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
        color: var(--text);
    }

    .hist-clear-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        padding: 0 4px;
    }

    .hist-clear-btn:hover {
        color: var(--danger);
    }

    .hist-filter-chips {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--page);
        color: var(--text-secondary);
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .filter-chip:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .filter-chip.active {
        background: var(--brand);
        border-color: var(--brand-dark);
        color: white;
    }

    .filter-chip.chip-amber.active {
        background: #facc15;
        border-color: #ca8a04;
        color: #713f12;
    }

    .filter-chip.chip-blue.active {
        background: #3b82f6;
        border-color: #2563eb;
        color: white;
    }

    /* Main 2-Column Grid */
    .hist-main-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 18px;
        align-items: flex-start;
    }

    /* Left Sidebar: Session Cards List */
    .hist-sessions-sidebar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 820px;
    }

    .sessions-sidebar-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        background: #fafcfb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sidebar-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .sidebar-subtext {
        font-size: 11px;
        color: var(--text-muted);
        display: block;
        margin-top: 2px;
    }

    .sessions-count-pill {
        font-size: 11px;
        font-weight: 700;
        background: var(--brand-light);
        color: var(--brand-dark);
        padding: 3px 10px;
        border-radius: 99px;
    }

    .sessions-cards-list {
        padding: 10px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .session-card {
        padding: 14px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--surface);
        cursor: pointer;
        transition: 0.15s ease;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .session-card:hover {
        border-color: var(--brand);
        background: #fbfdfc;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(8, 127, 85, 0.08);
    }

    .session-card.active {
        border-color: var(--brand);
        background: #f4faf7;
        box-shadow: 0 0 0 2px rgba(8, 127, 85, 0.15);
    }

    .session-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .session-user-info {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .session-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-weight: 800;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .session-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        display: block;
        line-height: 1.2;
    }

    .session-pnr {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .session-time {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .session-route-row {
        font-size: 11px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .session-snippet {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .session-badges-wrap {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    .tag-note {
        font-size: 10px;
        font-weight: 800;
        background: #fef08a;
        color: #854d0e;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #fde047;
    }

    .tag-handover {
        font-size: 10px;
        font-weight: 800;
        background: #e0f2fe;
        color: #0369a1;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #bae6fd;
    }

    .tag-closed {
        font-size: 10px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Right Column: Detail Panel */
    .hist-detail-panel {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .transcript-header-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 22px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .customer-profile-strip {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .customer-avatar-lg {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .customer-info-main {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .name-tier-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .customer-name-lg {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .tier-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
        background: #f1f5f9;
        color: #475569;
    }

    .status-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
    }

    .status-closed {
        background: #dcfce7;
        color: #15803d;
    }

    .customer-meta-row {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 12px;
        color: var(--text-secondary);
        flex-wrap: wrap;
    }

    .transcript-quick-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    /* FITUR 1: Search in Chat Card */
    .search-in-chat-card {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: var(--shadow-sm);
    }

    .search-in-chat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .search-label-group {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #854d0e;
        flex-wrap: wrap;
    }

    .search-sublabel {
        font-size: 12px;
        color: #a16207;
        font-weight: 500;
    }

    .search-icon-amber {
        color: #b45309;
        flex-shrink: 0;
    }

    .search-match-count {
        font-size: 11px;
        font-weight: 800;
        background: #fde047;
        color: #713f12;
        padding: 3px 10px;
        border-radius: 99px;
        white-space: nowrap;
    }

    .inchat-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--surface);
        border: 1.5px solid #facc15;
        border-radius: var(--radius-sm);
        padding: 8px 14px;
        transition: 0.15s ease;
    }

    .inchat-input-wrap:focus-within {
        border-color: #ca8a04;
        box-shadow: 0 0 0 2px rgba(202, 138, 4, 0.15);
    }

    .inchat-input-wrap input {
        border: none;
        outline: none;
        width: 100%;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
        color: var(--text);
    }

    .inchat-clear-btn {
        background: none;
        border: none;
        color: #a16207;
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
    }

    .quick-keyword-chips {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .chips-title {
        font-size: 11px;
        font-weight: 700;
        color: #854d0e;
    }

    .keyword-chip {
        font-size: 11px;
        font-weight: 700;
        background: var(--surface);
        border: 1px solid #fde047;
        color: #713f12;
        padding: 3px 9px;
        border-radius: 99px;
        cursor: pointer;
        transition: 0.15s ease;
        font-family: inherit;
    }

    .keyword-chip:hover {
        background: #fef08a;
        border-color: #ca8a04;
    }

    /* Highlight Search Match */
    mark.search-highlight {
        background: #facc15;
        color: #713f12;
        padding: 1px 4px;
        border-radius: 3px;
        font-weight: 800;
    }

    /* Transcript Feed Area */
    .transcript-feed-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        min-height: 480px;
        max-height: 580px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
        box-shadow: var(--shadow-sm);
    }

    .date-separator {
        text-align: center;
        position: relative;
        margin: 6px 0;
    }

    .date-separator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--border);
        z-index: 1;
    }

    .date-separator span {
        position: relative;
        z-index: 2;
        background: var(--surface);
        padding: 2px 14px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        border: 1px solid var(--border);
    }

    .feed-system-event {
        text-align: center;
        font-size: 11px;
        color: var(--text-muted);
        font-style: italic;
        padding: 2px 0;
    }

    /* Gelembung Pesan */
    .feed-bubble-row {
        display: flex;
        flex-direction: column;
        max-width: 72%;
    }

    .feed-bubble-row.customer {
        align-self: flex-start;
    }

    .feed-bubble-row.cs {
        align-self: flex-end;
        align-items: flex-end;
    }

    .feed-bubble-meta {
        font-size: 11px;
        color: var(--text-muted);
        margin-bottom: 3px;
        font-weight: 600;
    }

    .feed-bubble-box {
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.5;
        word-break: break-word;
    }

    .feed-bubble-row.customer .feed-bubble-box {
        background: #f1f5f9;
        color: var(--text);
        border-bottom-left-radius: 2px;
    }

    .feed-bubble-row.cs .feed-bubble-box {
        background: var(--brand);
        color: white;
        border-bottom-right-radius: 2px;
    }

    .feed-bubble-time {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 3px;
    }

    /* FITUR 3: Catatan Internal (Kolom Komentar Kuning Khusus Antar-CS) */
    .feed-internal-note-box {
        background: #fefce8;
        border: 1.5px solid #fde047;
        border-radius: var(--radius-md);
        padding: 12px 16px;
        box-shadow: 0 2px 6px rgba(234, 179, 8, 0.1);
        margin: 4px 0;
    }

    .note-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #fde047;
    }

    .note-lock-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #854d0e;
        letter-spacing: 0.02em;
    }

    .note-author-stamp {
        font-size: 11px;
        color: #a16207;
        font-weight: 700;
    }

    .note-box-body {
        font-size: 13px;
        color: #713f12;
        line-height: 1.5;
        font-weight: 500;
    }

    /* FITUR 4: Log Pergantian Agen */
    .feed-handover-log-box {
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: var(--radius-md);
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 4px 0;
    }

    .handover-icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dcfce7;
        color: #15803d;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .handover-log-body {
        flex: 1;
        font-size: 12px;
        color: #166534;
        line-height: 1.4;
    }

    .handover-tag-label {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-size: 10px;
        display: block;
        margin-bottom: 2px;
        color: #15803d;
    }

    .handover-log-stamp {
        font-size: 11px;
        font-weight: 700;
        color: #15803d;
        white-space: nowrap;
    }

    /* Form Tambah Catatan Internal Baru */
    .add-internal-note-card {
        background: #fefce8;
        border: 1.5px solid #fde047;
        border-radius: var(--radius-lg);
        padding: 18px 22px;
        box-shadow: var(--shadow-sm);
    }

    .internal-note-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .note-form-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 800;
        color: #854d0e;
    }

    .lock-icon-amber {
        color: #b45309;
    }

    .note-confidential-pill {
        font-size: 11px;
        font-weight: 700;
        background: #fef08a;
        color: #854d0e;
        padding: 3px 10px;
        border-radius: 99px;
        border: 1px solid #fde047;
    }

    .note-form-desc {
        font-size: 12px;
        color: #a16207;
        margin: 0 0 12px 0;
        line-height: 1.4;
    }

    .internal-note-textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #facc15;
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        background: var(--surface);
        color: #713f12;
        resize: vertical;
        transition: 0.15s ease;
    }

    .internal-note-textarea:focus {
        border-color: #ca8a04;
        box-shadow: 0 0 0 2px rgba(202, 138, 4, 0.18);
    }

    .internal-note-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        gap: 12px;
    }

    .note-author-hint {
        font-size: 11px;
        color: #854d0e;
    }

    /* Responsive */
    @media (max-width: 1080px) {
        .hist-main-grid {
            grid-template-columns: 1fr;
        }
        .hist-sessions-sidebar {
            height: 380px;
        }
        .hist-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .hist-page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .hist-stats-grid {
            grid-template-columns: 1fr;
        }
        .hist-controls-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .hist-search-box {
            max-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ============================================================
    // DATASET ARSIP RIWAYAT CHAT & REKAM JEJAK KOMUNIKASI (PRD 4.5)
    // ============================================================
    const currentCsUser = "{{ auth()->user()->name ?? 'Customer Service' }}";

    let historySessions = [
        {
            id: 'SESS-2026-0925-01',
            status: 'resolved',
            hasNotes: true,
            hasHandover: true,
            user: {
                name: 'Dewi Lestari',
                phone: '0812-9911-2233',
                email: 'dewi.lestari@mail.com',
                avatar: 'DL',
                tier: 'Gold Member'
            },
            booking: {
                pnr: 'TRV-2026-0612',
                route: 'Bekasi (Cyber Park) &rarr; Bandung (Dipatiukur)',
                departure: '25 Sep 2026, 13:00 WIB',
                vehicle: 'HiAce Commuter (B 1092 PQR)',
                seat: '01B (Depan samping driver)'
            },
            lastUpdated: '12:48 WIB',
            snippet: 'Pelanggan minta jemput duluan karena bawa lansia. Driver standby di lobby Barat.',
            messages: [
                { type: 'system', text: 'Percakapan dimulai via WhatsApp Official TravelKu', time: '12:35' },
                { type: 'customer', text: 'Halo selamat siang CS TravelKu, saya Dewi pemegang tiket TRV-2026-0612.', time: '12:36' },
                { type: 'customer', text: 'Apakah armadanya sudah standby di Pool Bekasi Barat? Saya bersama ibu saya yang sudah lansia.', time: '12:37' },
                {
                    type: 'internal_note',
                    author: 'CS Sarah Wijaya',
                    time: '12:38 WIB',
                    text: 'Pelanggan ini minta jemput duluan karena bawa lansia. Kursi depan 01B sudah diamankan & driver Pak Ridwan siap bantu naik koper.'
                },
                {
                    type: 'agent_handover',
                    time: '12:39 WIB',
                    fromAgent: 'CS Sarah Wijaya',
                    toAgent: currentCsUser,
                    reason: 'Pergantian shift siang & koordinasi khusus penjemputan lansia di Pool Bekasi Barat.',
                    text: `Log Pergantian Agen: Sesi dialihkan dari Agen CS Sarah Wijaya ke ${currentCsUser} pada 25 Sep 2026 pukul 12:39 WIB. Alasan: Pergantian shift siang & koordinasi khusus penjemputan lansia.`
                },
                { type: 'cs', author: currentCsUser, text: 'Halo Kak Dewi! Driver Pak Ridwan sudah berada di Pool Bekasi Barat ya. Nomor kontak beliau 0812-9911-2233. Kami sudah instruksikan driver untuk membantu ibu Kakak naik ke armada.', time: '12:42' },
                { type: 'customer', text: 'Terima kasih banyak atas perhatiannya kak, kami sudah di ruang tunggu pool ya.', time: '12:45' },
                { type: 'cs', author: currentCsUser, text: 'Sama-sama Kak Dewi, semoga perjalanan Kakak dan keluarga menyenangkan!', time: '12:46' },
                { type: 'system', text: 'Tiket ditandai Selesai (Closed) oleh CS. CSAT: 5 Bintang.', time: '12:48' }
            ]
        },
        {
            id: 'SESS-2026-0925-02',
            status: 'resolved',
            hasNotes: true,
            hasHandover: false,
            user: {
                name: 'Budi Prasetyo',
                phone: '0812-9988-7766',
                email: 'budi.p@mail.com',
                avatar: 'BP',
                tier: 'Silver Member'
            },
            booking: {
                pnr: 'TRV-2026-0925',
                route: 'Bandung (Pasteur) &rarr; Jakarta (Semanggi)',
                departure: '25 Sep 2026, 14:00 WIB',
                vehicle: 'HiAce Premio (D 7182 AB)',
                seat: '03A (Single Window)'
            },
            lastUpdated: '13:20 WIB',
            snippet: 'Nomor rekening BCA 883019281 a.n Budi Prasetyo telah dicatat untuk antisipasi SLA.',
            messages: [
                { type: 'system', text: 'Percakapan terhubung dengan CS Desk', time: '13:10' },
                { type: 'customer', text: 'Halo CS TravelKu, selamat siang.', time: '13:12' },
                { type: 'customer', text: 'Min, posisi van saya jam 14:00 rute Bandung - Jakarta sudah siap di pool belum? Takut telat jalan dari kantor.', time: '13:14' },
                {
                    type: 'internal_note',
                    author: 'CS Budi Santoso',
                    time: '13:15 WIB',
                    text: 'Pelanggan telah memberikan konfirmasi nomor rekening BCA 883019281 a.n Budi Prasetyo untuk jaminan SLA ketepatan waktu armada jika terlambat > 15 menit.'
                },
                { type: 'cs', author: 'CS Budi Santoso', text: 'Halo Pak Budi! Armada HiAce Premio D 7182 AB saat ini sedang dalam proses pembersihan kabin dan siap berangkat tepat waktu pukul 14:00 WIB.', time: '13:16' },
                { type: 'customer', text: 'Baik min terima kasih infonya, saya segera jalan ke Pool Pasteur.', time: '13:18' }
            ]
        },
        {
            id: 'SESS-2026-0925-03',
            status: 'resolved',
            hasNotes: true,
            hasHandover: true,
            user: {
                name: 'Anisa Melani',
                phone: '0819-3344-5566',
                email: 'anisa.m@mail.com',
                avatar: 'AM',
                tier: 'Gold Member'
            },
            booking: {
                pnr: 'TRV-2026-0421',
                route: 'Jakarta (Semanggi) &rarr; Bandung (Pasteur)',
                departure: '25 Sep 2026, 11:00 WIB',
                vehicle: 'HiAce Premio (B 2011 SH)',
                seat: '01A'
            },
            lastUpdated: '11:00 WIB',
            snippet: 'Sesi dialihkan dari Agen Citra ke CS Budi Santoso. E-ticket berhasil dikirim ulang.',
            messages: [
                { type: 'system', text: 'Sesi percakapan dibuka oleh penumpang', time: '10:48' },
                { type: 'customer', text: 'Kak, email e-ticket saya belum masuk tadi di gmail.', time: '10:50' },
                {
                    type: 'agent_handover',
                    time: '10:51 WIB',
                    fromAgent: 'Agen Citra',
                    toAgent: 'CS Budi Santoso',
                    reason: 'Pergantian shift pagi ke siang & verifikasi email e-ticket.',
                    text: 'Log Pergantian Agen: Sesi dialihkan dari Agen Citra ke CS Budi Santoso pada 25 Sep 2026 pukul 10:51 WIB. Alasan: Pergantian shift pagi ke siang.'
                },
                {
                    type: 'internal_note',
                    author: 'Agen Citra',
                    time: '10:52 WIB',
                    text: 'Sudah verifikasi nomor rekening BCA refund sebelumnya dan status pembayaran lunas via QRIS ShopeePay.'
                },
                { type: 'cs', author: 'CS Budi Santoso', text: 'Sudah kami bantu kirim ulang ke anisa.m@mail.com ya kak, mohon dicek di folder inbox / spam.', time: '10:53' },
                { type: 'customer', text: 'Sudah beres ya kak, e-ticket sudah terbit di email saya. Makasih banyak!', time: '10:55' }
            ]
        },
        {
            id: 'SESS-2026-0925-04',
            status: 'resolved',
            hasNotes: true,
            hasHandover: false,
            user: {
                name: 'Hendra Gunawan',
                phone: '0813-2233-4455',
                email: 'hendra.g@mail.com',
                avatar: 'HG',
                tier: 'Gold Member'
            },
            booking: {
                pnr: 'TRV-2026-0811',
                route: 'Jakarta (Semanggi) &rarr; Bandung (Dipatiukur)',
                departure: '25 Sep 2026, 13:45 WIB',
                vehicle: 'HiAce Luxury (B 8911 JKT)',
                seat: '01B'
            },
            lastUpdated: '13:40 WIB',
            snippet: 'Penumpang terjebak macet di tol Pancoran. Driver sepakat menunggu 10 menit.',
            messages: [
                { type: 'customer', text: 'Gawat min, saya macet parah di jalan tol Pancoran, apakah van bisa tunggu 10 menit?', time: '13:15' },
                {
                    type: 'internal_note',
                    author: 'CS Anda',
                    time: '13:16 WIB',
                    text: 'Sopir Pak Joko telah dihubungi dan bersedia menunggu maksimal 10 menit karena 5 penumpang lain sudah lengkap di pool.'
                },
                { type: 'cs', author: 'CS Anda', text: 'Halo Pak Hendra, kami sudah koordinasikan dengan Pak Joko (driver). Armada dapat menunggu toleransi 10 menit ya pak.', time: '13:17' },
                { type: 'customer', text: 'Terima kasih banyak min! Saya sudah mendekati pintu keluar tol Semanggi.', time: '13:25' }
            ]
        },
        {
            id: 'SESS-2026-0925-05',
            status: 'resolved',
            hasNotes: true,
            hasHandover: false,
            user: {
                name: 'Siti Rahma',
                phone: '0878-1122-3344',
                email: 'siti.rahma@mail.com',
                avatar: 'SR',
                tier: 'Regular'
            },
            booking: {
                pnr: 'TRV-2026-0774',
                route: 'Bandung (Pasteur) &rarr; Bandara Soetta (T3)',
                departure: '25 Sep 2026, 15:30 WIB',
                vehicle: 'HiAce Premio (D 9021 CG)',
                seat: '02B'
            },
            lastUpdated: '13:20 WIB',
            snippet: 'Ketentuan bagasi koper 28 inch untuk penerbangan internasional disetujui.',
            messages: [
                { type: 'customer', text: 'Permisi min, saya bawa koper 28 inch untuk flight malam, muat di bagasi belakang van?', time: '13:08' },
                {
                    type: 'internal_note',
                    author: 'CS Budi Santoso',
                    time: '13:10 WIB',
                    text: 'Bagasi belakang masih longgar karena hanya 4 penumpang yang membawa koper besar pada trip 15:30 WIB ini.'
                },
                { type: 'cs', author: 'CS Budi Santoso', text: 'Bisa Kak Siti, kapasitas bagasi belakang HiAce Premio muat untuk 1 koper besar 28 inch ya.', time: '13:11' }
            ]
        },
        {
            id: 'SESS-2026-0925-06',
            status: 'resolved',
            hasNotes: false,
            hasHandover: true,
            user: {
                name: 'Rian Anggara',
                phone: '0856-7788-9900',
                email: 'rian.a@mail.com',
                avatar: 'RA',
                tier: 'Regular'
            },
            booking: {
                pnr: 'TRV-2026-0599',
                route: 'Bandung (Dipatiukur) &rarr; Jakarta (Semanggi)',
                departure: '26 Sep 2026, 07:00 WIB',
                vehicle: 'HiAce Premio (D 7721 Z)',
                seat: '02A'
            },
            lastUpdated: '13:10 WIB',
            snippet: 'Perubahan nama tiket penumpang berhasil diproses.',
            messages: [
                { type: 'customer', text: 'Siang kak, tiket saya TRV-2026-0599 untuk besok pagi bisa diganti nama penumpang?', time: '13:00' },
                {
                    type: 'agent_handover',
                    time: '13:02 WIB',
                    fromAgent: 'CS Nadia',
                    toAgent: currentCsUser,
                    reason: 'Perubahan data identitas tiket berbayar.',
                    text: `Log Pergantian Agen: Sesi dialihkan dari CS Nadia ke ${currentCsUser} pada 25 Sep 2026 pukul 13:02 WIB. Alasan: Eskalasi perubahan data identitas tiket.`
                },
                { type: 'cs', author: currentCsUser, text: 'Bisa Kak Rian. Silakan kirimkan nama lengkap dan nomor HP adik Kakak ya, akan langsung kami bantu update di sistem.', time: '13:04' },
                { type: 'customer', text: 'Namanya Dimas Anggara, no HP 0856-1122-3344 kak. Terima kasih!', time: '13:05' }
            ]
        }
    ];

    let activeSessionId = 'SESS-2026-0925-01';
    let currentGlobalFilter = 'all';
    let currentGlobalSearchQuery = '';
    let currentInChatSearchQuery = '';

    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function () {
        renderSessionsList();
        loadSessionDetails(activeSessionId);
    });

    // 1. Render Daftar Sesi Percakapan Arsip (Left Column)
    function renderSessionsList() {
        const container = document.getElementById('sessions-container');
        if (!container) return;

        let filtered = historySessions.filter(s => {
            // Filter kategori
            if (currentGlobalFilter === 'notes' && !s.hasNotes) return false;
            if (currentGlobalFilter === 'handover' && !s.hasHandover) return false;
            if (currentGlobalFilter === 'resolved' && s.status !== 'resolved') return false;

            // Search query
            if (currentGlobalSearchQuery) {
                const q = currentGlobalSearchQuery.toLowerCase();
                const matchName = s.user.name.toLowerCase().includes(q);
                const matchPhone = s.user.phone.toLowerCase().includes(q);
                const matchPnr = s.booking.pnr.toLowerCase().includes(q);
                const matchSnippet = s.snippet.toLowerCase().includes(q);
                const matchMessages = s.messages.some(m => (m.text || '').toLowerCase().includes(q));
                return matchName || matchPhone || matchPnr || matchSnippet || matchMessages;
            }

            return true;
        });

        const badge = document.getElementById('sessions-count-badge');
        if (badge) badge.innerText = `${filtered.length} Sesi`;

        if (filtered.length === 0) {
            container.innerHTML = `
                <div style="padding: 30px 16px; text-align: center; color: var(--text-muted); font-size: 13px;">
                    Tidak ada riwayat chat yang sesuai dengan pencarian atau filter.
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(s => {
            const isActive = s.id === activeSessionId ? 'active' : '';
            html += `
                <div class="session-card ${isActive}" onclick="loadSessionDetails('${s.id}')">
                    <div class="session-card-header">
                        <div class="session-user-info">
                            <div class="session-avatar">${s.user.avatar}</div>
                            <div>
                                <span class="session-name">${s.user.name}</span>
                                <span class="session-pnr">${s.booking.pnr}</span>
                            </div>
                        </div>
                        <span class="session-time">${s.lastUpdated}</span>
                    </div>

                    <div class="session-route-row">
                        <span>🚌</span>
                        <span>${s.booking.route}</span>
                    </div>

                    <div class="session-snippet">
                        ${s.snippet}
                    </div>

                    <div class="session-badges-wrap">
                        ${s.hasNotes ? '<span class="tag-note">🔒 Catatan Internal</span>' : ''}
                        ${s.hasHandover ? '<span class="tag-handover">🔄 Log Pergantian</span>' : ''}
                        <span class="tag-closed">Selesai</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // 2. Load Detail Sesi Percakapan & Transkrip (Right Column)
    function loadSessionDetails(sessionId) {
        activeSessionId = sessionId;
        const session = historySessions.find(s => s.id === sessionId);
        if (!session) return;

        // Reset search in chat
        clearSearchInCurrentChat(false);

        // Update list card active state
        document.querySelectorAll('.session-card').forEach(el => el.classList.remove('active'));
        renderSessionsList();

        // Populate Header Data
        const vAvatar = document.getElementById('viewer-avatar');
        const vName = document.getElementById('viewer-name');
        const vTier = document.getElementById('viewer-tier');
        const vPhone = document.getElementById('viewer-phone');
        const vEmail = document.getElementById('viewer-email');
        const vPnr = document.getElementById('viewer-pnr');
        const vRoute = document.getElementById('viewer-route');

        if (vAvatar) vAvatar.innerText = session.user.avatar;
        if (vName) vName.innerText = session.user.name;
        if (vTier) vTier.innerText = session.user.tier;
        if (vPhone) vPhone.innerText = session.user.phone;
        if (vEmail) vEmail.innerText = session.user.email;
        if (vPnr) vPnr.innerText = session.booking.pnr;
        if (vRoute) vRoute.innerHTML = session.booking.route;

        // Render Transkrip
        renderTranscriptMessages(session.messages);
    }

    // Highlight helper untuk kata kunci
    function highlightSearchText(text, query) {
        if (!query || !text) return text;
        const safe = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safe})`, 'gi');
        return text.replace(regex, '<mark class="search-highlight">$1</mark>');
    }

    // 3. Render Transkrip Percakapan Lengkap
    function renderTranscriptMessages(messages, searchQuery = '') {
        const feed = document.getElementById('transcript-feed');
        if (!feed) return;

        let html = `
            <div class="date-separator">
                <span>Jumat, 25 September 2026</span>
            </div>
        `;

        messages.forEach(m => {
            if (m.type === 'system') {
                html += `
                    <div class="feed-system-event">
                        &bull; ${highlightSearchText(m.text, searchQuery)} (${m.time || ''})
                    </div>
                `;
            } else if (m.type === 'agent_handover') {
                html += `
                    <div class="feed-handover-log-box">
                        <div class="handover-icon-circle">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </div>
                        <div class="handover-log-body">
                            <span class="handover-tag-label">🔄 Log Pergantian Agen</span>
                            <div>${highlightSearchText(m.text, searchQuery)}</div>
                        </div>
                        <span class="handover-log-stamp">${m.time}</span>
                    </div>
                `;
            } else if (m.type === 'internal_note') {
                html += `
                    <div class="feed-internal-note-box">
                        <div class="note-box-header">
                            <span class="note-lock-tag">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                🔒 Catatan Internal Khusus Antar-CS (Tidak Bisa Dilihat Pelanggan)
                            </span>
                            <span class="note-author-stamp">${m.author} &bull; ${m.time}</span>
                        </div>
                        <div class="note-box-body">
                            ${highlightSearchText(m.text, searchQuery)}
                        </div>
                    </div>
                `;
            } else if (m.type === 'customer') {
                html += `
                    <div class="feed-bubble-row customer">
                        <span class="feed-bubble-meta">Penumpang</span>
                        <div class="feed-bubble-box">
                            ${highlightSearchText(m.text, searchQuery)}
                        </div>
                        <span class="feed-bubble-time">${m.time}</span>
                    </div>
                `;
            } else {
                html += `
                    <div class="feed-bubble-row cs">
                        <span class="feed-bubble-meta">${m.author || currentCsUser} (CS)</span>
                        <div class="feed-bubble-box">
                            ${highlightSearchText(m.text, searchQuery)}
                        </div>
                        <span class="feed-bubble-time">${m.time}</span>
                    </div>
                `;
            }
        });

        feed.innerHTML = html;
        feed.scrollTop = feed.scrollHeight;
    }

    // 4. FITUR PENCARIAN KATA KUNCI (Search in Chat - PRD 4.5)
    function handleSearchInCurrentChat(query) {
        currentInChatSearchQuery = (query || '').trim();
        const badge = document.getElementById('inchat-match-badge');
        const clearBtn = document.getElementById('btn-clear-inchat');
        const session = historySessions.find(s => s.id === activeSessionId);

        if (!session) return;

        if (!currentInChatSearchQuery) {
            if (badge) badge.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
            renderTranscriptMessages(session.messages);
            return;
        }

        if (clearBtn) clearBtn.style.display = 'inline-block';

        const safe = currentInChatSearchQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(safe, 'gi');
        let count = 0;

        session.messages.forEach(m => {
            const matches = (m.text || '').match(regex);
            if (matches) count += matches.length;
        });

        if (badge) {
            badge.style.display = 'inline-block';
            badge.innerText = `${count} pesan cocok`;
        }

        renderTranscriptMessages(session.messages, currentInChatSearchQuery);
    }

    function clearSearchInCurrentChat(doRender = true) {
        currentInChatSearchQuery = '';
        const input = document.getElementById('inchat-search-input');
        const badge = document.getElementById('inchat-match-badge');
        const clearBtn = document.getElementById('btn-clear-inchat');

        if (input) input.value = '';
        if (badge) badge.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';

        if (doRender) {
            const session = historySessions.find(s => s.id === activeSessionId);
            if (session) renderTranscriptMessages(session.messages);
        }
    }

    function applyKeywordSearch(keyword) {
        const input = document.getElementById('inchat-search-input');
        if (input) {
            input.value = keyword;
            handleSearchInCurrentChat(keyword);
            input.focus();
        }
    }

    // 5. FITUR TAMBAH CATATAN INTERNAL BARU (Komentar Kuning Antar-CS)
    function handleSaveInternalNote(e) {
        e.preventDefault();
        const textarea = document.getElementById('new-internal-note-input');
        const noteText = textarea ? textarea.value.trim() : '';

        if (!noteText) return;

        const session = historySessions.find(s => s.id === activeSessionId);
        if (!session) return;

        const nowTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const newNote = {
            type: 'internal_note',
            author: currentCsUser,
            time: nowTime,
            text: noteText
        };

        session.messages.push(newNote);
        session.hasNotes = true;
        session.snippet = 'Catatan: ' + noteText;

        // Update Counter
        const noteStat = document.getElementById('stat-internal-notes');
        if (noteStat) {
            const currentCount = parseInt(noteStat.innerText) || 312;
            noteStat.innerText = currentCount + 1;
        }

        renderTranscriptMessages(session.messages, currentInChatSearchQuery);
        renderSessionsList();

        textarea.value = '';
        alert('Catatan internal (kuning) berhasil disimpan dan tercatat dalam rekam jejak tiket pelanggan.');
    }

    // 6. Global Search & Filter Handlers
    function handleHistoryGlobalSearch(query) {
        currentGlobalSearchQuery = (query || '').trim();
        const clearBtn = document.getElementById('btn-clear-global-search');
        if (clearBtn) {
            clearBtn.style.display = currentGlobalSearchQuery ? 'inline-block' : 'none';
        }
        renderSessionsList();
    }

    function clearHistoryGlobalSearch() {
        currentGlobalSearchQuery = '';
        const input = document.getElementById('history-global-search');
        const clearBtn = document.getElementById('btn-clear-global-search');
        if (input) input.value = '';
        if (clearBtn) clearBtn.style.display = 'none';
        renderSessionsList();
    }

    function setHistoryFilter(filterName, btnElement) {
        currentGlobalFilter = filterName;
        document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
        if (btnElement) btnElement.classList.add('active');
        renderSessionsList();
    }

    function exportTranscript() {
        window.print();
    }
</script>
@endpush
@endsection
