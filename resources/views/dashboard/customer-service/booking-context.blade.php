@extends('layouts.customer-service')

@section('title', 'Melihat Konteks Booking - Customer Service')

@section('content')
<div class="cs-booking-context-wrapper">

    <!-- Top Hub Header -->
    <div class="ctx-page-header">
        <div class="header-info">
            <div class="ctx-live-sync-pill">
                <span class="sync-dot pulse"></span>
                <span>Sinkronisasi Otomatis Profil Pelanggan</span>
            </div>
            <h1 class="header-title">Melihat Konteks Booking</h1>
            <p class="header-subtitle">
                Panel data operasional tiket terintegrasi secara otomatis saat pelanggan menghubungi CS via nomor telepon atau email terdaftar.
            </p>
        </div>

        <div class="header-action-group">
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Buka Live Chat</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="ctx-search-bar-card">
        <div class="search-input-wrapper">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="search-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text"
                   id="booking-search-input"
                   placeholder="Cari otomatis: Kode Booking (PNR), Nomor WhatsApp/HP, Nama Pemesan, atau Plat Mobil..."
                   oninput="filterBookingList()"
                   autocomplete="off">
            <button type="button" class="search-clear-btn" id="btn-clear-search" onclick="clearBookingSearch()" style="display: none;">&times;</button>
        </div>

        <div class="quick-filter-chips">
            <button type="button" class="filter-chip active" onclick="setBookingFilter('all', this)">Semua Booking</button>
            <button type="button" class="filter-chip" onclick="setBookingFilter('kritis', this)">Berangkat &lt; 60 Mnt</button>
            <button type="button" class="filter-chip" onclick="setBookingFilter('today', this)">Hari Ini</button>
            <button type="button" class="filter-chip" onclick="setBookingFilter('pending', this)">Menunggu Bayar</button>
        </div>
    </div>

    <!-- Main Content Grid (2 Columns: List on Left, Full Context Details on Right) -->
    <div class="ctx-main-grid">

        <!-- Column 1: Daftar Pemesanan Pelanggan -->
        <div class="ctx-list-sidebar">
            <div class="list-sidebar-header">
                <div class="list-title-wrap">
                    <span class="list-title">Antrean Booking Pelanggan</span>
                    <span class="list-count-badge" id="booking-total-badge">6 Tiket</span>
                </div>
                <span class="list-subhint">Klik tiket untuk melihat konteks lengkap</span>
            </div>

            <div class="booking-cards-scroll" id="booking-cards-container">
                <!-- Dynamically populated via JavaScript -->
            </div>
        </div>

        <!-- Column 2: Panel Utama Melihat Konteks Booking -->
        <div class="ctx-detail-panel" id="booking-detail-panel">

            <!-- Panel Header Info -->
            <div class="panel-hero-header">
                <div class="hero-left">
                    <span class="hero-sync-label">
                        <span class="sync-dot pulse"></span>
                        <span>Data Terkoneksi Otomatis</span>
                    </span>
                    <h2 class="hero-title" id="detail-passenger-name">Budi Prasetyo</h2>
                    <div class="hero-meta-row">
                        <span class="meta-tag tier-silver" id="detail-passenger-tier">Silver Member</span>
                        <span class="meta-separator">&bull;</span>
                        <span class="meta-text" id="detail-passenger-phone">0812-9988-7766</span>
                        <span class="meta-separator">&bull;</span>
                        <span class="meta-text" id="detail-passenger-email">budi.p@mail.com</span>
                    </div>
                </div>

                <div class="hero-right">
                    <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-secondary" id="btn-chat-with-passenger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>Chat Pelanggan Ini</span>
                    </a>
                </div>
            </div>

            <!-- 5 Section Cards Grid -->
            <div class="ctx-cards-flow">

                <!-- 1. IDENTITAS TIKET -->
                <div class="ctx-section-card">
                    <div class="card-title-line">
                        <div class="card-title-group">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                                <path d="M13 5v2"></path>
                                <path d="M13 17v2"></path>
                                <path d="M13 11v2"></path>
                            </svg>
                            <h3>Identitas Tiket</h3>
                        </div>
                        <button type="button" class="copy-pnr-button" onclick="copyActivePNR()" title="Klik untuk menyalin Kode PNR">
                            <span id="detail-booking-pnr">TRV-2026-0925</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="ctx-grid-3col">
                        <div class="data-field">
                            <span class="field-label">Nama Pemesan:</span>
                            <strong class="field-value font-lg text-slate-800" id="detail-order-name">Budi Prasetyo</strong>
                        </div>
                        <div class="data-field">
                            <span class="field-label">Rute Perjalanan:</span>
                            <strong class="field-value text-brand-dark" id="detail-booking-route">Bandung (Pasteur) ke Jakarta (Semanggi)</strong>
                        </div>
                        <div class="data-field">
                            <span class="field-label">Jadwal Keberangkatan:</span>
                            <div class="field-value font-bold text-brand" id="detail-departure-schedule">Jumat, 25 Sep 2026 &bull; 14:00 WIB</div>
                        </div>
                    </div>

                    <div class="ctx-alert-banner" id="detail-countdown-banner">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span id="detail-countdown-text">Sisa Waktu: 45 Menit Menuju Keberangkatan (Proses Boarding)</span>
                    </div>
                </div>

                <!-- 2. DETAIL KURSI & ARMADA -->
                <div class="ctx-section-card">
                    <div class="card-title-line">
                        <div class="card-title-group">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M7 9h10"></path>
                                <path d="M7 15h10"></path>
                            </svg>
                            <h3>Detail Kursi &amp; Armada</h3>
                        </div>
                        <span class="vehicle-model-pill" id="detail-vehicle-model">Toyota HiAce Premio</span>
                    </div>

                    <div class="fleet-seat-grid">
                        <!-- Kursi -->
                        <div class="seat-visual-box">
                            <span class="box-label">Nomor Kursi Dipilih:</span>
                            <div class="seat-badge-hero">
                                <span class="seat-no" id="detail-seat-no">03A</span>
                                <span class="seat-type" id="detail-seat-type">Single Window (Baris 3)</span>
                            </div>
                        </div>

                        <!-- Plat Nomor -->
                        <div class="plate-visual-box">
                            <span class="box-label">Nomor Polisi Armada:</span>
                            <div class="plate-display-badge">
                                <span class="plate-number" id="detail-plate-no">D 7182 AB</span>
                                <span class="plate-brand" id="detail-plate-fleet">Unit #04 - TravelKu Bandung</span>
                            </div>
                        </div>

                        <!-- Sopir & Kontak -->
                        <div class="driver-profile-box">
                            <span class="box-label">Sopir Bertugas:</span>
                            <div class="driver-inner-row">
                                <div class="driver-avatar"></div>
                                <div class="driver-info">
                                    <strong class="driver-name" id="detail-driver-name">Pak Suryanto</strong>
                                    <span class="driver-phone" id="detail-driver-phone">0812-7788-9900</span>
                                </div>
                                <div class="driver-actions">
                                    <a href="tel:081277889900" id="detail-driver-call" class="driver-btn driver-btn-call" title="Panggil Sopir">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span>Telp</span>
                                    </a>
                                    <a href="https://wa.me/6281277889900" id="detail-driver-wa" target="_blank" rel="noopener noreferrer" class="driver-btn driver-btn-wa" title="WhatsApp Sopir">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                        <span>WhatsApp</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. TITIK JEMPUT & TURUN -->
                <div class="ctx-section-card">
                    <div class="card-title-line">
                        <div class="card-title-group">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <h3>Titik Jemput &amp; Turun</h3>
                        </div>
                        <span class="mode-badge" id="detail-pickup-type">Pool to Pool</span>
                    </div>

                    <div class="locations-flow-grid">
                        <!-- Titik Jemput -->
                        <div class="location-card pickup-card">
                            <div class="loc-card-top">
                                <span class="loc-pin-dot pin-pickup"></span>
                                <span class="loc-title">Lokasi Penjemputan:</span>
                            </div>
                            <div class="loc-address-text" id="detail-pickup-address">
                                Pool Pasteur - Jl. Dr. Djunjunan No. 135, Pasteur, Sukajadi, Kota Bandung
                            </div>

                            <!-- Instruksi Spesifik -->
                            <div class="instruction-box">
                                <div class="inst-content">
                                    <span class="inst-label">Instruksi Penjemputan:</span>
                                    <span class="inst-text" id="detail-pickup-instruction">
                                        Depan loket tiket 2, samping minimarket Indomaret Point
                                    </span>
                                </div>
                            </div>

                            <!-- Tautan Google Maps -->
                            <a href="https://maps.google.com" id="detail-pickup-maps" target="_blank" rel="noopener noreferrer" class="maps-direct-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                                </svg>
                                <span>Buka Titik Penjemputan di Google Maps</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                            </a>
                        </div>

                        <!-- Titik Turun -->
                        <div class="location-card dropoff-card">
                            <div class="loc-card-top">
                                <span class="loc-pin-dot pin-dropoff"></span>
                                <span class="loc-title">Lokasi Penurunan:</span>
                            </div>
                            <div class="loc-address-text" id="detail-dropoff-address">
                                Plaza Semanggi - Pintu Lobby Selatan, Jl. Jend. Sudirman Kav. 50, Jakarta Selatan
                            </div>

                            <a href="https://maps.google.com" id="detail-dropoff-maps" target="_blank" rel="noopener noreferrer" class="maps-direct-btn dropoff-maps">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                                </svg>
                                <span>Buka Titik Penurunan di Google Maps</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 4. STATUS PEMBAYARAN -->
                <div class="ctx-section-card">
                    <div class="card-title-line">
                        <div class="card-title-group">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                <line x1="2" x2="22" y1="10" y2="10"></line>
                            </svg>
                            <h3>Status Pembayaran</h3>
                        </div>
                        <span class="payment-badge pill-paid" id="detail-payment-status">Lunas</span>
                    </div>

                    <div class="payment-overview-grid">
                        <div class="pay-stat-item">
                            <span class="pay-lbl">Metode Pembayaran:</span>
                            <strong class="pay-val font-semibold" id="detail-payment-method">BCA Virtual Account</strong>
                        </div>
                        <div class="pay-stat-item">
                            <span class="pay-lbl">Total Tagihan Tiket:</span>
                            <strong class="pay-val text-brand text-xl font-extrabold" id="detail-payment-amount">Rp 175.000</strong>
                        </div>
                        <div class="pay-stat-item">
                            <span class="pay-lbl">Waktu Pembayaran Sukses:</span>
                            <span class="pay-val text-slate-600" id="detail-payment-time">25 Sep 2026, 09:15 WIB</span>
                        </div>
                    </div>
                </div>

                <!-- 5. TOMBOL AKSI CEPAT (QUICK ACTION) -->
                <div class="ctx-section-card quick-action-highlight-card">
                    <div class="card-title-line">
                        <div class="card-title-group">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                            <h3>Tombol Aksi Cepat (Quick Action)</h3>
                        </div>
                        <span class="action-hint">Aksi langsung untuk penyesuaian tiket pelanggan</span>
                    </div>

                    <div class="qa-grid-triad">
                        <!-- 1. Ubah Kursi -->
                        <button type="button" class="qa-feature-card qa-card-seat" onclick="openChangeSeatModal()">
                            <div class="qa-card-content">
                                <strong class="qa-card-title">Ubah Kursi</strong>
                                <span class="qa-card-desc">Ganti nomor kursi di armada yang sama sesuai permintaan penumpang.</span>
                            </div>
                            <span class="qa-action-link">Buka Pilihan Kursi</span>
                        </button>

                        <!-- 2. Reschedule -->
                        <button type="button" class="qa-feature-card qa-card-reschedule" onclick="openRescheduleModal()">
                            <div class="qa-card-content">
                                <strong class="qa-card-title">Reschedule</strong>
                                <span class="qa-card-desc">Ubah tanggal atau jam keberangkatan jika penumpang berhalangan.</span>
                            </div>
                            <span class="qa-action-link">Pilih Jadwal Baru</span>
                        </button>

                        <!-- 3. Ajukan Pembatalan -->
                        <button type="button" class="qa-feature-card qa-card-cancel" onclick="openCancellationModal()">
                            <div class="qa-card-content">
                                <strong class="qa-card-title">Ajukan Pembatalan</strong>
                                <span class="qa-card-desc">Teruskan pembatalan &amp; rekening refund ke Admin Utama (PRD 4.5).</span>
                            </div>
                            <span class="qa-action-link">Kirim Pengajuan</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('modals')
<!-- Modal Ubah Kursi -->
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

<!-- Modal Reschedule Jadwal -->
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

<!-- Modal Ajukan Pembatalan (Sesuai PRD 4.5) -->
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
                    <textarea id="cancel-notes" rows="2" class="form-textarea-cs" placeholder="Tuliskan catatan kondisi penumpang saat konfirmasi..."></textarea>
                </div>

                <div class="modal-card-footer">
                    <button type="button" class="cs-btn cs-btn-secondary" onclick="closeCancellationModal()">Batal</button>
                    <button type="submit" class="cs-btn cs-btn-danger">Kirim Pengajuan Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    /* Wrapper & Top Header */
    .cs-booking-context-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
    }

    .ctx-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        flex-wrap: wrap;
        gap: 16px;
    }

    .ctx-live-sync-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 6px;
    }

    .sync-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--brand);
    }

    .sync-dot.pulse {
        animation: pulse 1.8s infinite;
    }

    .header-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.3px;
        margin: 0;
    }

    .header-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 4px;
        max-width: 760px;
        line-height: 1.5;
    }

    /* Search & Filter Bar */
    .ctx-search-bar-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .search-input-wrapper .search-icon {
        position: absolute;
        left: 14px;
        color: var(--text-muted);
    }

    .search-input-wrapper input {
        width: 100%;
        background: var(--page);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 40px 12px 42px;
        font-size: 13.5px;
        font-family: inherit;
        outline: none;
        transition: 0.2s ease;
    }

    .search-input-wrapper input:focus {
        background: var(--surface);
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
    }

    .search-clear-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        font-size: 20px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .quick-filter-chips {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        background: var(--page);
        border: 1px solid var(--border);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: 0.2s ease;
        font-family: inherit;
    }

    .filter-chip:hover {
        border-color: var(--brand);
        color: var(--brand-dark);
    }

    .filter-chip.active {
        background: var(--brand);
        border-color: var(--brand-dark);
        color: white;
    }

    /* Main Grid */
    .ctx-main-grid {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 20px;
        align-items: flex-start;
    }

    /* List Sidebar */
    .ctx-list-sidebar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .list-sidebar-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-subtle);
        background: var(--page);
    }

    .list-title-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .list-title {
        font-size: 13px;
        font-weight: 800;
        color: var(--text);
    }

    .list-count-badge {
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .list-subhint {
        font-size: 10.5px;
        color: var(--text-muted);
        display: block;
        margin-top: 2px;
    }

    .booking-cards-scroll {
        display: flex;
        flex-direction: column;
        padding: 12px;
        gap: 10px;
        max-height: calc(100vh - 280px);
        overflow-y: auto;
    }

    .b-card-item {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .b-card-item:hover {
        border-color: var(--brand);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }

    .b-card-item.active {
        border-color: var(--brand);
        background: var(--brand-soft);
        box-shadow: 0 2px 8px rgba(8, 127, 85, 0.12);
    }

    .b-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .b-card-pnr {
        font-size: 12px;
        font-weight: 800;
        color: var(--text);
    }

    .b-card-name {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text);
    }

    .b-card-route {
        font-size: 11.5px;
        color: var(--brand-dark);
        font-weight: 600;
    }

    .b-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 2px;
        font-size: 11px;
    }

    .b-card-seat-pill {
        background: var(--page);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 1px 6px;
        font-weight: 700;
        color: var(--text);
    }

    /* Detail Panel */
    .ctx-detail-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .panel-hero-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
        padding-bottom: 18px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .hero-sync-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--brand-dark);
        letter-spacing: 0.6px;
        margin-bottom: 4px;
    }

    .hero-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .hero-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 4px;
        flex-wrap: wrap;
    }

    .tier-silver {
        background: #e2e8f0;
        color: #334155;
        font-weight: 700;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .ctx-cards-flow {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ctx-section-card {
        background: var(--page);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .card-title-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title-group svg {
        color: var(--brand);
    }

    .card-title-group h3 {
        font-size: 14px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .copy-pnr-button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 800;
        color: var(--text);
        cursor: pointer;
        transition: 0.2s ease;
        font-family: inherit;
    }

    .copy-pnr-button:hover {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand-dark);
    }

    .ctx-grid-3col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .data-field {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .field-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .field-value {
        font-size: 13px;
        color: var(--text);
    }

    .ctx-alert-banner {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 700;
    }

    /* Fleet & Seat */
    .fleet-seat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1.4fr;
        gap: 12px;
    }

    .seat-visual-box, .plate-visual-box, .driver-profile-box {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .box-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .seat-badge-hero {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .seat-no {
        font-size: 20px;
        font-weight: 800;
        color: var(--brand-dark);
    }

    .seat-type {
        font-size: 11px;
        color: var(--text-secondary);
    }

    .plate-display-badge {
        display: flex;
        flex-direction: column;
    }

    .plate-number {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #1e293b;
    }

    .plate-brand {
        font-size: 11px;
        color: var(--text-muted);
    }

    .driver-inner-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .driver-avatar {
        font-size: 24px;
    }

    .driver-info {
        display: flex;
        flex-direction: column;
        flex: 1;
        line-height: 1.25;
    }

    .driver-name {
        font-size: 13px;
        color: var(--text);
    }

    .driver-phone {
        font-size: 11px;
        color: var(--text-muted);
    }

    .driver-actions {
        display: flex;
        gap: 5px;
    }

    .driver-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10.5px;
        font-weight: 700;
        text-decoration: none;
    }

    .driver-btn-call {
        background: var(--page);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .driver-btn-wa {
        background: #25D366;
        color: white;
    }

    /* Locations */
    .locations-flow-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .location-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .loc-card-top {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .loc-pin-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .pin-pickup { background: var(--brand); }
    .pin-dropoff { background: #0284c7; }

    .loc-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-secondary);
        letter-spacing: 0.4px;
    }

    .loc-address-text {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.4;
    }

    .instruction-box {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: #fffbeb;
        border: 1px dashed #fde68a;
        padding: 8px 10px;
        border-radius: 4px;
        margin-top: 2px;
    }

    .inst-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #92400e;
        display: block;
    }

    .inst-text {
        font-size: 11.5px;
        color: #78350f;
        line-height: 1.35;
    }

    .maps-direct-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        font-size: 11px;
        font-weight: 700;
        color: var(--brand-dark);
        text-decoration: none;
        padding: 5px 10px;
        background: var(--brand-light);
        border-radius: 4px;
        width: fit-content;
        transition: 0.2s ease;
    }

    .maps-direct-btn:hover {
        background: var(--brand);
        color: white;
    }

    .dropoff-maps {
        color: #0369a1;
        background: #e0f2fe;
    }

    .dropoff-maps:hover {
        background: #0284c7;
        color: white;
    }

    /* Payment Overview */
    .payment-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
    }

    .pay-stat-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .pay-lbl {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .payment-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 4px;
    }

    .pill-paid { background: #dcfce7; color: #15803d; }
    .pill-pending { background: #fef3c7; color: #b45309; }
    .pill-expired { background: #fee2e2; color: #b91c1c; }

    /* Quick Action Triad */
    .quick-action-highlight-card {
        background: linear-gradient(180deg, var(--page) 0%, var(--brand-soft) 100%);
        border-color: rgba(8, 127, 85, 0.25);
    }

    .qa-grid-triad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .qa-feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        font-family: inherit;
    }

    .qa-feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .qa-card-seat:hover { border-color: var(--brand); }
    .qa-card-reschedule:hover { border-color: #0284c7; }
    .qa-card-cancel:hover { border-color: #dc2626; }

    .qa-card-icon {
        font-size: 26px;
        line-height: 1;
    }

    .qa-card-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--text);
        display: block;
    }

    .qa-card-desc {
        font-size: 11.5px;
        color: var(--text-secondary);
        line-height: 1.4;
        display: block;
        margin-top: 2px;
    }

    .qa-action-link {
        font-size: 11.5px;
        font-weight: 700;
        margin-top: auto;
        padding-top: 6px;
    }

    .qa-card-seat .qa-action-link { color: var(--brand-dark); }
    .qa-card-reschedule .qa-action-link { color: #0284c7; }
    .qa-card-cancel .qa-action-link { color: #dc2626; }

    /* Modals */
    .cs-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1200;
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
        margin: 0;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-card-body {
        padding: 20px 22px;
    }

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

    .modal-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .badge-seat-current, .badge-schedule-old {
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 11px;
    }

    .badge-seat-current { background: var(--brand-light); color: var(--brand-dark); }
    .badge-schedule-old { background: #f1f5f9; color: #334155; }

    @keyframes modalFade {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    /* Responsive */
    @media (max-width: 1080px) {
        .ctx-main-grid {
            grid-template-columns: 1fr;
        }
        .fleet-seat-grid, .ctx-grid-3col, .payment-overview-grid, .qa-grid-triad {
            grid-template-columns: 1fr;
        }
        .locations-flow-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Dataset Percakapan & Konteks Booking
    const currentCsName = "{{ auth()->user()->name ?? 'Customer Service' }}";

    let bookingsDataset = [
        {
            id: 1,
            code: 'TRV-2026-0925',
            passengerName: 'Budi Prasetyo',
            phone: '0812-9988-7766',
            email: 'budi.p@mail.com',
            tier: 'Silver Member',
            urgency: 'kritis',
            route: 'Bandung (Pasteur) ke Jakarta (Semanggi)',
            departureDate: 'Jumat, 25 Sep 2026',
            departureTime: '14:00 WIB',
            departure: 'Hari Ini, 14:00 WIB',
            countdownText: 'Sisa Waktu: 45 Menit Menuju Keberangkatan (Proses Boarding di Pool Pasteur)',
            seat: '03A',
            seatType: 'Single Window (Baris 3)',
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
        {
            id: 2,
            code: 'TRV-2026-0811',
            passengerName: 'Hendra Gunawan',
            phone: '0813-2233-4455',
            email: 'hendra.g@mail.com',
            tier: 'Gold Member',
            urgency: 'kritis',
            route: 'Jakarta (Semanggi) ke Bandung (Dipatiukur)',
            departureDate: 'Jumat, 25 Sep 2026',
            departureTime: '13:45 WIB',
            departure: 'Hari Ini, 13:45 WIB',
            countdownText: 'Sisa Waktu: 30 Menit (Penumpang Terjebak Macet di Tol Pancoran)',
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
        {
            id: 3,
            code: 'TRV-2026-0774',
            passengerName: 'Siti Rahma',
            phone: '0878-1122-3344',
            email: 'siti.rahma@mail.com',
            tier: 'Regular',
            urgency: 'today',
            route: 'Bandung (Pasteur) ke Bandara Soetta (T3)',
            departureDate: 'Jumat, 25 Sep 2026',
            departureTime: '15:30 WIB',
            departure: 'Hari Ini, 15:30 WIB',
            countdownText: 'Sisa Waktu: 2 Jam 15 Menit',
            seat: '02B',
            seatType: 'Jendela Kanan Baris 2',
            vehicle: 'Toyota HiAce Premio',
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
        {
            id: 4,
            code: 'TRV-2026-0612',
            passengerName: 'Dewi Ratnasari',
            phone: '0812-4455-6677',
            email: 'dewi.ratna@mail.com',
            tier: 'Silver Member',
            urgency: 'today',
            route: 'Bekasi Barat ke Bandung (Dipatiukur)',
            departureDate: 'Jumat, 25 Sep 2026',
            departureTime: '14:45 WIB',
            departure: 'Hari Ini, 14:45 WIB',
            countdownText: 'Sisa Waktu: 1 Jam 30 Menit',
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
        {
            id: 5,
            code: 'TRV-2026-0599',
            passengerName: 'Rian Anggara',
            phone: '0856-7788-9900',
            email: 'rian.a@mail.com',
            tier: 'Regular',
            urgency: 'pending',
            route: 'Bandung (Dipatiukur) ke Jakarta (Semanggi)',
            departureDate: 'Sabtu, 26 Sep 2026',
            departureTime: '07:00 WIB',
            departure: 'Besok, 07:00 WIB',
            countdownText: 'Jadwal Besok Pagi (Pemberangkatan Pertama 07:00 WIB)',
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
        {
            id: 6,
            code: 'TRV-2026-0421',
            passengerName: 'Anisa Melani',
            phone: '0819-3344-5566',
            email: 'anisa.m@mail.com',
            tier: 'Gold Member',
            urgency: 'all',
            route: 'Jakarta (Semanggi) ke Bandung (Pasteur)',
            departureDate: 'Jumat, 25 Sep 2026',
            departureTime: '11:00 WIB',
            departure: 'Telah Berangkat (11:00 WIB)',
            countdownText: 'Perjalanan Telah Berlangsung',
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
        }
    ];

    let currentSelectedId = 1;
    let currentFilterType = 'all';

    document.addEventListener('DOMContentLoaded', function () {
        renderBookingsSidebar();
        selectBooking(currentSelectedId);
    });

    function setBookingFilter(type, btn) {
        currentFilterType = type;
        document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        renderBookingsSidebar();
    }

    function filterBookingList() {
        const query = (document.getElementById('booking-search-input')?.value || '').trim();
        const clearBtn = document.getElementById('btn-clear-search');
        if (clearBtn) clearBtn.style.display = query.length > 0 ? 'inline' : 'none';
        renderBookingsSidebar();
    }

    function clearBookingSearch() {
        const input = document.getElementById('booking-search-input');
        if (input) input.value = '';
        const clearBtn = document.getElementById('btn-clear-search');
        if (clearBtn) clearBtn.style.display = 'none';
        renderBookingsSidebar();
    }

    function renderBookingsSidebar() {
        const container = document.getElementById('booking-cards-container');
        if (!container) return;

        const query = (document.getElementById('booking-search-input')?.value || '').toLowerCase().trim();

        let filtered = bookingsDataset;

        if (query.length > 0) {
            filtered = filtered.filter(b =>
                b.code.toLowerCase().includes(query) ||
                b.passengerName.toLowerCase().includes(query) ||
                b.phone.includes(query) ||
                b.email.toLowerCase().includes(query) ||
                b.plate.toLowerCase().includes(query) ||
                b.route.toLowerCase().includes(query)
            );
        } else {
            if (currentFilterType === 'kritis') {
                filtered = filtered.filter(b => b.urgency === 'kritis');
            } else if (currentFilterType === 'today') {
                filtered = filtered.filter(b => b.departure.toLowerCase().includes('hari ini') || b.urgency === 'today' || b.urgency === 'kritis');
            } else if (currentFilterType === 'pending') {
                filtered = filtered.filter(b => b.paymentStatus.toLowerCase().includes('menunggu'));
            }
        }

        document.getElementById('booking-total-badge').innerText = `${filtered.length} Tiket`;

        if (filtered.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px 14px; color: var(--text-muted); font-size: 13px;">
                    <div style="font-size: 30px; margin-bottom: 6px;"></div>
                    <strong>Tidak ada tiket ditemukan</strong>
                    <p style="font-size: 11px; margin-top: 4px;">Coba kata kunci lain atau pilih filter Semua Booking.</p>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(b => {
            const isActive = b.id === currentSelectedId ? 'active' : '';
            html += `
                <div class="b-card-item ${isActive}" onclick="selectBooking(${b.id})">
                    <div class="b-card-top">
                        <span class="b-card-pnr">${b.code}</span>
                        <span class="payment-badge ${b.paymentStatusClass}">${b.paymentStatus}</span>
                    </div>
                    <div class="b-card-name">${b.passengerName}</div>
                    <div class="b-card-route">${b.route.split(' ke ')[0].trim()} ke ${b.route.split(' ke ')[1].trim()}</div>
                    <div class="b-card-footer">
                        <span>Kursi: <strong class="b-card-seat-pill">${b.seat}</strong></span>
                        <span style="color: var(--text-muted);">${b.departureTime}</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function selectBooking(id) {
        currentSelectedId = id;
        const b = bookingsDataset.find(item => item.id === id);
        if (!b) return;

        // Update active class
        document.querySelectorAll('.b-card-item').forEach(el => el.classList.remove('active'));
        renderBookingsSidebar();

        // 1. Hero
        document.getElementById('detail-passenger-name').innerText = b.passengerName;
        document.getElementById('detail-passenger-tier').innerText = b.tier;
        document.getElementById('detail-passenger-phone').innerText = b.phone;
        document.getElementById('detail-passenger-email').innerText = b.email;

        // 2. Identitas Tiket
        document.getElementById('detail-booking-pnr').innerText = b.code;
        document.getElementById('detail-order-name').innerText = b.passengerName;
        document.getElementById('detail-booking-route').innerHTML = b.route;
        document.getElementById('detail-departure-schedule').innerHTML = `${b.departureDate} &bull; ${b.departureTime}`;
        document.getElementById('detail-countdown-text').innerText = b.countdownText;

        // 3. Detail Kursi & Armada
        document.getElementById('detail-seat-no').innerText = b.seat;
        document.getElementById('detail-seat-type').innerText = b.seatType;
        document.getElementById('detail-plate-no').innerText = b.plate;
        document.getElementById('detail-plate-fleet').innerText = `Unit armada ${b.vehicle}`;
        document.getElementById('detail-vehicle-model').innerText = b.vehicle;
        document.getElementById('detail-driver-name').innerText = b.driverName;
        document.getElementById('detail-driver-phone').innerText = b.driverPhone;

        const cleanPhone = b.driverPhone.replace(/[^0-9]/g, '');
        document.getElementById('detail-driver-call').href = 'tel:' + cleanPhone;
        document.getElementById('detail-driver-wa').href = b.driverWaLink || `https://wa.me/62${cleanPhone.replace(/^0/, '')}`;

        // 4. Titik Jemput & Turun
        document.getElementById('detail-pickup-type').innerText = b.pickupType;
        document.getElementById('detail-pickup-address').innerText = b.pickupAddress;
        document.getElementById('detail-pickup-instruction').innerText = b.pickupInstruction;
        document.getElementById('detail-pickup-maps').href = b.pickupMapsUrl;
        document.getElementById('detail-dropoff-address').innerText = b.dropoffAddress;
        document.getElementById('detail-dropoff-maps').href = b.dropoffMapsUrl;

        // 5. Status Pembayaran
        const payBadge = document.getElementById('detail-payment-status');
        payBadge.innerText = b.paymentStatus;
        payBadge.className = 'payment-badge ' + b.paymentStatusClass;

        document.getElementById('detail-payment-method').innerText = b.paymentMethod;
        document.getElementById('detail-payment-amount').innerText = b.paymentAmount;
        document.getElementById('detail-payment-time').innerText = b.paymentPaidAt;
    }

    function copyActivePNR() {
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        navigator.clipboard.writeText(b.code).then(() => {
            alert(`Kode Booking (PNR) "${b.code}" berhasil disalin.`);
        }).catch(() => {
            prompt('Salin Kode Booking:', b.code);
        });
    }

    // Modal Ubah Kursi
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
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        document.getElementById('modal-seat-pnr').innerText = b.code;
        document.getElementById('modal-seat-passenger').innerText = b.passengerName;
        document.getElementById('modal-seat-current').innerText = `${b.seat} (${b.seatType})`;

        const container = document.getElementById('seat-picker-container');
        if (container) {
            let html = '';
            availableSeatsList.forEach(s => {
                const isCurrent = s.no === b.seat;
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
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        const newSeatNo = document.getElementById('selected-new-seat').value;
        if (!newSeatNo) {
            alert('Silakan pilih salah satu kursi baru terlebih dahulu.');
            return;
        }

        const seatObj = availableSeatsList.find(s => s.no === newSeatNo);
        b.seat = newSeatNo;
        b.seatType = seatObj ? seatObj.type : 'Standar';

        alert(`Kursi penumpang ${b.passengerName} berhasil diubah ke ${newSeatNo}!`);
        closeChangeSeatModal();
        selectBooking(b.id);
    }

    // Modal Reschedule
    function openRescheduleModal() {
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        document.getElementById('modal-reschedule-pnr').innerText = b.code;
        document.getElementById('modal-reschedule-route').innerHTML = b.route;
        document.getElementById('modal-reschedule-old').innerText = `${b.departureDate} • ${b.departureTime}`;

        document.getElementById('modal-reschedule').style.display = 'flex';
    }

    function closeRescheduleModal() {
        const modal = document.getElementById('modal-reschedule');
        if (modal) modal.style.display = 'none';
    }

    function submitReschedule(e) {
        e.preventDefault();
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        const newDate = document.getElementById('reschedule-new-date').value;
        const newTime = document.getElementById('reschedule-new-time').value;

        b.departureDate = newDate;
        b.departureTime = newTime;
        b.departure = `${newDate}, ${newTime}`;
        b.countdownText = 'Jadwal Baru Terkonfirmasi';

        alert(`Jadwal tiket ${b.code} berhasil di-reschedule ke ${newDate} (${newTime}).`);
        closeRescheduleModal();
        selectBooking(b.id);
    }

    // Modal Ajukan Pembatalan
    function openCancellationModal() {
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        document.getElementById('modal-cancel-pnr').innerText = b.code;
        document.getElementById('modal-cancel-passenger').innerText = b.passengerName;
        document.getElementById('modal-cancel-amount').innerText = b.paymentAmount;

        document.getElementById('modal-cancellation').style.display = 'flex';
    }

    function closeCancellationModal() {
        const modal = document.getElementById('modal-cancellation');
        if (modal) modal.style.display = 'none';
    }

    function submitCancellation(e) {
        e.preventDefault();
        const b = bookingsDataset.find(item => item.id === currentSelectedId);
        if (!b) return;

        const reason = document.getElementById('cancel-reason-select').value;
        const refundAccount = document.getElementById('cancel-refund-account').value;

        b.paymentStatus = 'Pengajuan Pembatalan';
        b.paymentStatusClass = 'pill-expired';

        alert(`Pengajuan pembatalan tiket ${b.code} berhasil dikirimkan ke Admin & Tim Keuangan untuk diproses refund.`);
        closeCancellationModal();
        selectBooking(b.id);
    }
</script>
@endpush
