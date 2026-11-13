<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Validasi Tiket (Scan QR) - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @include('dashboard.admin-biasa.partials.styles')

    <style>
        /* ==========================================================
           ROOT THEMING & COMPONENT STYLES
        =========================================================== */
        :root {
            --primary: #0C7950;
            --primary-dark: #095c3c;
            --primary-light: #EEFBF5;
            --primary-border: #8FD3B4;
            --accent: #2563EB;
            --bg-body: #F4F6F8;
            --bg-card: #FFFFFF;
            --text-dark: #1F2937;
            --text-light: #64748B;
            --border: #E2E8F0;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --danger-border: #FECACA;
            --warning: #D97706;
            --warning-bg: #FFFBEB;
        }

        .ds-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* CARD CONTAINERS */
        .panel-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .panel-header {
            padding: 16px 20px;
            background: #FAFCFB;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .panel-header h2,
        .panel-header h3 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-body {
            padding: 20px;
        }

        /* ==========================================================
           AREA KAMERA PEMINDAI (SCANNER - BAGIAN ATAS)
        =========================================================== */
        .scanner-wrapper {
            display: grid;
            grid-template-columns: minmax(0, 520px) minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 960px) {
            .scanner-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .scanner-container {
            position: relative;
            width: 100%;
            height: 300px;
            background: #090D16;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.85);
        }

        #scannerVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .camera-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .reticle-box {
            position: relative;
            width: 200px;
            height: 200px;
            border: 1.5px dashed rgba(255, 255, 255, 0.45);
            border-radius: 16px;
        }

        .corner {
            position: absolute;
            width: 24px;
            height: 24px;
            border-color: #10B981;
            border-style: solid;
        }

        .corner-tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 8px; }
        .corner-tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 8px; }
        .corner-bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 8px; }
        .corner-br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 8px; }

        .laser-line {
            position: absolute;
            left: 6px;
            right: 6px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #10B981 20%, #34D399 50%, #10B981 80%, transparent);
            box-shadow: 0 0 12px #10B981, 0 0 4px #FFFFFF;
            animation: scanLaser 2.2s ease-in-out infinite alternate;
        }

        @keyframes scanLaser {
            0% { top: 12px; opacity: 0.3; }
            50% { opacity: 1; }
            100% { top: 180px; opacity: 0.3; }
        }

        .scanner-hud-text {
            margin-top: 14px;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.7);
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9);
            text-align: center;
        }

        /* Controls bar */
        .camera-controls-bar {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-camera-action {
            flex: 1;
            min-height: 40px;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: #FFFFFF;
            color: var(--text-dark);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-camera-action:hover {
            background: #F1F5F9;
            border-color: #CBD5E1;
        }

        .btn-torch-active {
            background: #FEF3C7 !important;
            border-color: #F59E0B !important;
            color: #B45309 !important;
        }

        /* Manual Input & Simulation */
        .manual-section {
            display: grid;
            gap: 8px;
            margin-top: 14px;
            background: #F8FAF9;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
        }

        .manual-input-group {
            display: flex;
            gap: 8px;
        }

        .manual-input {
            flex: 1;
            min-height: 42px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .manual-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(12, 121, 80, 0.15);
        }

        .btn-validate-manual {
            padding: 8px 16px;
            background: var(--primary);
            color: #FFFFFF;
            font-weight: 800;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-validate-manual:hover {
            background: var(--primary-dark);
        }

        .quick-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .chip-btn {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #FFFFFF;
            cursor: pointer;
            transition: all 0.1s ease;
        }

        .chip-btn.success:hover { background: #EEFBF5; border-color: #8FD3B4; color: #0C5A3E; }
        .chip-btn.fail:hover { background: #FEF2F2; border-color: #FECACA; color: #DC2626; }

        /* VISUAL ALERT FEEDBACK CARD (SUKSES & GAGAL) */
        .feedback-card {
            display: none;
            border-radius: 14px;
            padding: 20px;
            border: 2px solid;
            animation: popIn 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .feedback-card.active {
            display: block;
        }

        @keyframes popIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .feedback-success {
            background: #EEFBF5;
            border-color: #0C7950;
            color: #0C392F;
        }

        .feedback-danger {
            background: #FEF2F2;
            border-color: #DC2626;
            color: #991B1B;
        }

        .fb-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 12px;
            border-bottom: 1.5px dashed rgba(0, 0, 0, 0.12);
            padding-bottom: 10px;
        }

        .fb-title {
            font-size: 1.05rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.3px;
        }

        .fb-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
        }

        .fb-badge-success { background: #0C7950; color: #FFFFFF; }
        .fb-badge-danger { background: #DC2626; color: #FFFFFF; }

        .fb-passenger-box {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            margin-bottom: 12px;
            display: grid;
            gap: 8px;
        }

        .fb-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
        }

        .fb-grid-2 strong {
            display: block;
            font-size: 13px;
            color: var(--text-dark);
        }

        .fb-btn-dismiss {
            width: 100%;
            min-height: 42px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-success-action { background: #0C7950; color: #FFFFFF; }
        .btn-success-action:hover { background: #095C3C; }
        .btn-danger-action { background: #DC2626; color: #FFFFFF; }
        .btn-danger-action:hover { background: #B91C1C; }

        /* ==========================================================
           TABEL DATA PENUMPANG BOARDING HARI INI (BAGIAN BAWAH)
        =========================================================== */
        .boarded-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .dest-filter-tabs {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .dest-tab-btn {
            padding: 6px 14px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            background: #FFFFFF;
            color: var(--text-dark);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .dest-tab-btn:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .dest-tab-btn.is-active {
            background: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary);
        }

        .dest-tab-badge {
            background: rgba(0, 0, 0, 0.12);
            padding: 1px 6px;
            border-radius: 999px;
            font-size: 11px;
        }

        .dest-tab-btn.is-active .dest-tab-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
        }

        /* Group Card per Tujuan */
        .dest-group-card {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
            background: #FFFFFF;
        }

        .dest-group-header {
            padding: 12px 18px;
            background: #F1F5F9;
            border-bottom: 1.5px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dest-title-text {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Tabel data */
        .boarded-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .boarded-table th {
            background: #FAFCFB;
            color: var(--text-light);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .boarded-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: var(--text-dark);
        }

        .boarded-table tr:hover td {
            background: #F8FAF9;
        }

        .seat-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 26px;
            padding: 0 8px;
            background: #0C392F;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 12px;
            border-radius: 6px;
        }

        .status-badge-boarded {
            background: #EEFBF5;
            color: #0C5A3E;
            border: 1px solid #8FD3B4;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Toast notification */
        .toast-notify {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1F2937;
            color: #FFFFFF;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            z-index: 9999;
            display: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.2s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .toast-notify.show { display: flex; align-items: center; gap: 8px; }
        .toast-notify.success { background: #065F46; }
        .toast-notify.danger { background: #991B1B; }
        .toast-notify.info { background: #1E40AF; }
    </style>
    @fonts('overpass')
    @vite(['resources/css/dashboard-staff.css'])
</head>

<body data-staff="staff">

<div class="dashboard-wrapper">

    @include('dashboard.admin-biasa.partials.sidebar')

    <main class="main-content">

        @include('dashboard.admin-biasa.partials.topbar', [
            'topbarTitle' => 'Validasi Tiket (Scan QR / Barcode)',
            'topbarSubtitle' => 'Memverifikasi penumpang online yang akan naik ke dalam armada hari ini.',
        ])

        <section class="content">
            <div class="ds-container">

                <header class="ds-head" style="margin-bottom: 0;">
                    <div>
                        <h1>Modul Validasi E-Ticket Penumpang</h1>
                        <p>Arahkan kamera ke QR Code e-ticket di HP penumpang. Sistem otomatis memvalidasi jadwal, status lunas, dan mencatat boarding.</p>
                    </div>
                    <div class="ds-head-actions">
                        <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="ds-btn ds-btn--primary">
                            <span>+ Buat Pesanan Baru</span>
                        </a>
                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="ds-btn ds-btn--secondary">
                            <span>Manajemen Booking</span>
                        </a>
                    </div>
                </header>

                @if (session('success'))
                    <div class="ds-alert ds-alert--success" role="status">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="ds-alert ds-alert--error" role="alert">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ==========================================================
                     BAGIAN ATAS: AREA KAMERA PEMINDAI (SCANNER) & VISUAL ALERT
                =========================================================== --}}
                <div class="panel-card">
                    <div class="panel-header">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                <rect x="7" y="7" width="10" height="10" rx="1" />
                            </svg>
                            <span>Area Pemindai Kamera (Scan QR Code)</span>
                        </h2>
                        <span style="font-size: 11px; font-weight: 800; background: #EEFBF5; color: #0C5A3E; border: 1px solid #8FD3B4; padding: 3px 10px; border-radius: 999px;">
                            Sensor Aktif Real-Time
                        </span>
                    </div>

                    <div class="panel-body">
                        <div class="scanner-wrapper">
                            {{-- KOLOM KIRI: JENDELA KAMERA & KONTROL --}}
                            <div>
                                <div class="scanner-container">
                                    <video id="scannerVideo" autoplay playsinline muted></video>

                                    {{-- RETICLE HUD & LASER SENSOR --}}
                                    <div class="camera-overlay">
                                        <div class="reticle-box">
                                            <div class="corner corner-tl"></div>
                                            <div class="corner corner-tr"></div>
                                            <div class="corner corner-bl"></div>
                                            <div class="corner corner-br"></div>
                                            <div class="laser-line"></div>
                                        </div>
                                        <div class="scanner-hud-text" id="scannerStatusText">
                                            Arahkan QR Code Tiket ke Kotak Sensor
                                        </div>
                                    </div>
                                </div>

                                {{-- KONTROL KAMERA --}}
                                <div class="camera-controls-bar">
                                    <button type="button" class="btn-camera-action" id="btnToggleCamera" onclick="toggleCamera()">
                                        <span id="cameraBtnIcon">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m22 8-6 4 6 4V8Z" />
                                                <rect width="14" height="12" x="2" y="6" rx="2" ry="2" />
                                            </svg>
                                        </span>
                                        <span id="cameraBtnText">Mulai Kamera</span>
                                    </button>

                                    <button type="button" class="btn-camera-action" id="btnToggleTorch" onclick="toggleFlashlight()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                        </svg>
                                        <span id="torchBtnText">Lampu Kilat (Off)</span>
                                    </button>

                                    <button type="button" class="btn-camera-action" onclick="switchCamera()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                                            <path d="M21 3v5h-5" />
                                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                                            <path d="M8 16H3v5" />
                                        </svg>
                                        <span>Balik Kamera</span>
                                    </button>
                                </div>

                                {{-- MODE MANUAL (FALLBACK KODE BOOKING / SCAN GUN) --}}
                                <div class="manual-section">
                                    <label for="manualCodeInput" style="font-size: 12px; font-weight: 700; color: var(--text-dark);">
                                        Input Kode Booking Manual (Barcode Gun / Ketik):
                                    </label>
                                    <div class="manual-input-group">
                                        <input
                                            type="text"
                                            id="manualCodeInput"
                                            class="manual-input"
                                            placeholder="Contoh: TRV-XYZ123"
                                            onkeydown="if(event.key==='Enter'){ event.preventDefault(); handleManualSubmit(); }"
                                        >
                                        <button type="button" class="btn-validate-manual" onclick="handleManualSubmit()">
                                            Validasi
                                        </button>
                                    </div>
                                    <div class="quick-chips">
                                        <span style="font-size: 10px; color: var(--text-light); font-weight: 700;">Simulasi:</span>
                                        <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-ALREADY-SCAN')">Sudah Di-Scan</button>
                                        <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-UNPAID-123')">Belum Lunas</button>
                                        <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-WRONG-TIME')">Salah Jam</button>
                                        <button type="button" class="chip-btn fail" onclick="runSimulatedCode('TKV-EXPIRED')">Kadaluwarsa</button>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: STATUS PANEL & VISUAL ALERT POPUP (SUKSES/GAGAL) --}}
                            <div>
                                {{-- FEEDBACK CARD (MUNCUL OTOMATIS SAAT SCAN) --}}
                                <div class="feedback-card" id="instantFeedbackCard">
                                    {{-- KONTEN SUKSES --}}
                                    <div id="feedbackSuccessBox" style="display: none;">
                                        <div class="fb-header">
                                            <div class="fb-title" style="color: #0C5A3E;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                <span>TIKET VALID • SUDAH CHECK-IN</span>
                                            </div>
                                            <span class="fb-badge fb-badge-success">BOARDED</span>
                                        </div>

                                        <div class="fb-passenger-box">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <span style="font-size: 11px; color: var(--text-light); text-transform: uppercase; font-weight: 700;">Nama Penumpang:</span>
                                                    <h3 id="fbPassengerName" style="font-size: 1.15rem; color: #0C392F; font-weight: 900; margin: 2px 0;">-</h3>
                                                </div>
                                                <span class="seat-pill" id="fbSeatNumber" style="font-size: 14px; min-width: 50px; height: 32px;">-</span>
                                            </div>

                                            <div class="fb-grid-2" style="border-top: 1px dashed #E2E8F0; padding-top: 8px;">
                                                <div>
                                                    <span style="color: var(--text-light);">Tujuan Keberangkatan:</span>
                                                    <strong id="fbDestination" style="color: #0C7950;">-</strong>
                                                </div>
                                                <div>
                                                    <span style="color: var(--text-light);">Mobil / Armada:</span>
                                                    <strong id="fbVehicleInfo">-</strong>
                                                </div>
                                                <div>
                                                    <span style="color: var(--text-light);">Kode Booking / Tiket:</span>
                                                    <strong id="fbBookingCode" style="font-family: monospace;">-</strong>
                                                </div>
                                                <div>
                                                    <span style="color: var(--text-light);">Waktu Scan Boarding:</span>
                                                    <strong id="fbBoardedAt">-</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="fb-btn-dismiss btn-success-action" onclick="dismissFeedback()">
                                            &check; Siap Masuk Kabin &bull; Lanjut Scan Berikutnya
                                        </button>
                                    </div>

                                    {{-- KONTEN GAGAL / ERROR --}}
                                    <div id="feedbackFailedBox" style="display: none;">
                                        <div class="fb-header">
                                            <div class="fb-title" style="color: #991B1B;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="15" y1="9" x2="9" y2="15" />
                                                    <line x1="9" y1="9" x2="15" y2="15" />
                                                </svg>
                                                <span id="fbErrorTitle">TIKET DITOLAK</span>
                                            </div>
                                            <span class="fb-badge fb-badge-danger">GAGAL</span>
                                        </div>

                                        <div class="fb-passenger-box" style="border-color: #FECACA; background: #FFF5F5;">
                                            <p id="fbErrorMessage" style="font-size: 13px; font-weight: 700; color: #991B1B; line-height: 1.5; margin: 0;">
                                                Alasan penolakan spesifik tiket.
                                            </p>
                                        </div>

                                        {{-- JIKA STATUS BELUM LUNAS: OPSI PELUNASAN KASIR LANGSUNG --}}
                                        <div id="fbSettleBox" style="display: none; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 12px; margin-bottom: 12px;">
                                            <div style="font-size: 12px; font-weight: 700; color: #92400E; margin-bottom: 8px;">
                                                Tagihan <span id="fbSettleBookingCode">-</span> (<span id="fbSettleAmount">Rp 0</span>) belum dibayar. Terima pelunasan kasir langsung:
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                                <select id="fbSettleMethod" style="padding: 7px 10px; border-radius: 8px; border: 1px solid #FCD34D; font-size: 12px; font-weight: 700;">
                                                    <option value="Cash">Tunai (Cash di Loket)</option>
                                                    <option value="QRIS">QRIS Loket</option>
                                                    <option value="Transfer Bank">Transfer Bank</option>
                                                </select>
                                                <button type="button" id="fbSettleConfirmBtn" onclick="confirmSettleFromScanner()" style="padding: 8px 14px; border: none; border-radius: 8px; background: #0C7950; color: #FFFFFF; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                    Lunasi &amp; Boarding
                                                </button>
                                            </div>
                                        </div>

                                        <button type="button" class="fb-btn-dismiss btn-danger-action" onclick="dismissFeedback()">
                                            &times; Tutup Peringatan &amp; Cek Kembali
                                        </button>
                                    </div>
                                </div>

                                {{-- PANDUAN STANDBY KETIKA BELUM ADA SCAN --}}
                                <div id="standbyInstructionBox" style="background: #F8FAF9; border: 1.5px dashed #CBD5E1; border-radius: 14px; padding: 24px; text-align: center; display: grid; gap: 8px;">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #EEFBF5; color: #0C7950; display: grid; place-items: center; margin: 0 auto;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                    <strong style="color: var(--text-dark); font-size: 0.95rem;">Siap Memindai E-Ticket</strong>
                                    <p style="font-size: 12px; color: var(--text-light); margin: 0; line-height: 1.5;">
                                        Nyalakan kamera di sebelah kiri atau ketik kode booking tiket. Penumpang yang sukses diverifikasi otomatis masuk ke tabel rekap di bawah.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==========================================================
                     BAGIAN BAWAH: TABEL DATA PENUMPANG BOARDING HARI INI
                     HANYA MEMANGGIL DATA HARI INI YANG STATUSNYA is_boarded = true
                     DIKELOMPOKKAN / DIFILTER BERDASARKAN TUJUAN KEBERANGKATAN
                =========================================================== --}}
                <div class="panel-card" id="boardedSection">
                    <div class="panel-header">
                        <div>
                            <h2>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                                    <path d="M9 12h6" />
                                    <path d="M9 16h6" />
                                </svg>
                                <span>Data Penumpang Sudah Check-in (is_boarded = true)</span>
                            </h2>
                            <p style="font-size: 12px; color: var(--text-light); margin: 2px 0 0;">
                                Rekap real-time penumpang yang sudah memindai QR code tiket dan berada di armada hari ini ({{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}).
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12px; font-weight: 800; background: #EEFBF5; color: #0C5A3E; border: 1px solid #8FD3B4; padding: 4px 12px; border-radius: 999px;">
                                Total Boarding Hari Ini: <strong id="totalBoardedCountDisplay">{{ $totalBoardedTodayCount ?? 0 }}</strong> Penumpang
                            </span>
                            <button type="button" class="btn-camera-action" onclick="fetchBoardedPassengers()" style="min-height: 32px; padding: 4px 10px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 4 23 10 17 10" />
                                    <polyline points="1 20 1 14 7 14" />
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                                </svg>
                                <span>Muat Ulang</span>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body">
                        {{-- TOOLBAR: FILTER TUJUAN KEBERANGKATAN & PENCARIAN --}}
                        <div class="boarded-toolbar">
                            {{-- TAB FILTER PER TUJUAN --}}
                            <div class="dest-filter-tabs" id="destinationFilterTabs">
                                <button type="button" class="dest-tab-btn is-active" data-dest="all" onclick="setDestinationFilter('all', this)">
                                    <span>Semua Tujuan</span>
                                    <span class="dest-tab-badge" id="tabBadgeAll">{{ $totalBoardedTodayCount ?? 0 }}</span>
                                </button>
                                @foreach ($availableDestinations ?? [] as $dest)
                                    @php
                                        $cnt = isset($groupedBoardedByDestination[$dest]) ? $groupedBoardedByDestination[$dest]->count() : 0;
                                    @endphp
                                    <button type="button" class="dest-tab-btn" data-dest="{{ $dest }}" onclick="setDestinationFilter('{{ $dest }}', this)">
                                        <span>Tujuan: {{ $dest }}</span>
                                        <span class="dest-tab-badge">{{ $cnt }}</span>
                                    </button>
                                @endforeach
                            </div>

                            {{-- TOGGLE VIEW MODE & SEARCH --}}
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <div style="display: inline-flex; border: 1.5px solid var(--border); border-radius: 8px; overflow: hidden;">
                                    <button type="button" id="btnViewGrouped" class="dest-tab-btn is-active" style="border: none; border-radius: 0;" onclick="toggleViewMode('grouped')">
                                        <span>Kelompok Mobil &amp; Tujuan</span>
                                    </button>
                                    <button type="button" id="btnViewFlat" class="dest-tab-btn" style="border: none; border-radius: 0;" onclick="toggleViewMode('flat')">
                                        <span>Tabel Lengkap</span>
                                    </button>
                                </div>

                                <input
                                    type="text"
                                    id="boardedSearchInput"
                                    class="manual-input"
                                    style="min-height: 36px; padding: 6px 12px; font-size: 12px; max-width: 240px;"
                                    placeholder="Cari nama, kursi, booking..."
                                    oninput="handleSearchBoarded(this.value)"
                                >
                            </div>
                        </div>

                        {{-- WADAH DINAMIS DAFTAR PENUMPANG BOARDED HARI INI --}}
                        <div id="boardedContentContainer">
                            {{-- Dirender oleh Javascript (renderBoardedData) --}}
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </main>

</div>

{{-- TOAST NOTIFICATION --}}
<div id="toastNotification" class="toast-notify">
    <span id="toastIcon"></span>
    <span id="toastMessage">Pemberitahuan</span>
</div>

{{-- LIBRARY JSQR UNTUK CAMERA QR DECODING REAL-TIME --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    // Inisialisasi Data Awal dari Backend
    let currentBoardedData = @json($boardedPassengersToday ?? []);
    let currentAvailableDestinations = @json($availableDestinations ?? []);
    let selectedDestinationFilter = 'all';
    let currentViewMode = 'grouped'; // 'grouped' atau 'flat'
    let searchQuery = '';

    // ---------------------------------------------------------
    // 1. WEB AUDIO API SYNTHESIZER (BEEP SUKSES & GAGAL)
    // ---------------------------------------------------------
    let audioCtx = null;

    function getAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                audioCtx = new AudioContext();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Suara Beep Sukses (Two-Tone Chime Positif: 587Hz -> 880Hz)
    function playSuccessSound() {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.setValueAtTime(880.00, now + 0.08); // A5

            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.35, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + 0.35);
        } catch (e) {
            console.warn('Audio playback error:', e);
        }
    }

    // Suara Beep Peringatan / Gagal (Low Buzz Beep: 220Hz / 160Hz)
    function playErrorSound() {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, now);
            osc.frequency.setValueAtTime(160, now + 0.12);

            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.4, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now);
            osc.stop(now + 0.35);
        } catch (e) {
            console.warn('Audio playback error:', e);
        }
    }

    // ---------------------------------------------------------
    // 2. CAMERA SCANNER & FLASHLIGHT (TORCH) CONTROL
    // ---------------------------------------------------------
    let cameraStream = null;
    let videoTrack = null;
    let isTorchOn = false;
    let currentFacingMode = 'environment';
    let scanInterval = null;

    async function toggleCamera() {
        const videoElem = document.getElementById('scannerVideo');
        const btnText = document.getElementById('cameraBtnText');
        const statusText = document.getElementById('scannerStatusText');

        if (cameraStream) {
            stopCameraStream();
            btnText.innerText = 'Mulai Kamera';
            statusText.innerText = 'Kamera Dihentikan (Mode Manual Aktif)';
        } else {
            try {
                const constraints = {
                    video: {
                        facingMode: { ideal: currentFacingMode },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                };

                cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
                videoElem.srcObject = cameraStream;
                videoTrack = cameraStream.getVideoTracks()[0];

                btnText.innerText = 'Hentikan Kamera';
                statusText.innerText = 'Kamera Aktif • Arahkan QR Code Tiket';
                showToast('Kamera pemindai QR aktif.', 'info');

                startBarcodeDetector(videoElem);
            } catch (err) {
                console.warn('Camera access issue:', err);
                statusText.innerText = 'Kamera tidak dapat diakses. Gunakan input manual.';
                showToast('Gagal membuka kamera: ' + (err.message || 'Izin ditolak'), 'danger');
            }
        }
    }

    function stopCameraStream() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
            videoTrack = null;
        }
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
        document.getElementById('scannerVideo').srcObject = null;
        isTorchOn = false;
        updateTorchButtonUI();
    }

    async function toggleFlashlight() {
        if (!videoTrack) {
            showToast('Nyalakan kamera terlebih dahulu untuk mengaktifkan lampu kilat.', 'danger');
            return;
        }

        try {
            const capabilities = videoTrack.getCapabilities ? videoTrack.getCapabilities() : {};
            if (!capabilities.torch) {
                isTorchOn = !isTorchOn;
                updateTorchButtonUI();
                showToast(isTorchOn ? 'Lampu kilat aktif (simulasi)' : 'Lampu kilat mati', 'info');
                return;
            }

            isTorchOn = !isTorchOn;
            await videoTrack.applyConstraints({ advanced: [{ torch: isTorchOn }] });
            updateTorchButtonUI();
            showToast(isTorchOn ? 'Lampu kilat menyala' : 'Lampu kilat mati', 'info');
        } catch (e) {
            isTorchOn = !isTorchOn;
            updateTorchButtonUI();
        }
    }

    function updateTorchButtonUI() {
        const torchBtn = document.getElementById('btnToggleTorch');
        const torchText = document.getElementById('torchBtnText');
        if (isTorchOn) {
            torchBtn.classList.add('btn-torch-active');
            torchText.innerText = 'Lampu Kilat (ON)';
        } else {
            torchBtn.classList.remove('btn-torch-active');
            torchText.innerText = 'Lampu Kilat (Off)';
        }
    }

    async function switchCamera() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        if (cameraStream) {
            stopCameraStream();
            toggleCamera();
        }
    }

    function startBarcodeDetector(videoElem) {
        let isProcessing = false;
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        scanInterval = setInterval(async () => {
            if (isProcessing || !cameraStream || videoElem.readyState < 2) return;
            isProcessing = true;

            try {
                // 1. Coba native BarcodeDetector jika tersedia di browser
                if ('BarcodeDetector' in window) {
                    const detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13'] });
                    const barcodes = await detector.detect(videoElem);
                    if (barcodes.length > 0 && barcodes[0].rawValue) {
                        validateTicketCode(barcodes[0].rawValue);
                        isProcessing = false;
                        return;
                    }
                }

                // 2. Fallback jsQR via hidden in-memory canvas
                if (window.jsQR && videoElem.videoWidth > 0) {
                    canvas.width = videoElem.videoWidth;
                    canvas.height = videoElem.videoHeight;
                    ctx.drawImage(videoElem, 0, 0, canvas.width, canvas.height);
                    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = window.jsQR(imgData.data, imgData.width, imgData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code && code.data) {
                        validateTicketCode(code.data);
                        isProcessing = false;
                        return;
                    }
                }
            } catch (err) {
                // frame error ignored
            } finally {
                isProcessing = false;
            }
        }, 350);
    }

    // ---------------------------------------------------------
    // 3. INPUT MANUAL & SIMULASI CODE
    // ---------------------------------------------------------
    function handleManualSubmit() {
        const input = document.getElementById('manualCodeInput');
        const code = input.value.trim();
        if (!code) {
            showToast('Ketik kode booking atau e-ticket terlebih dahulu.', 'danger');
            input.focus();
            return;
        }
        validateTicketCode(code);
    }

    function runSimulatedCode(code) {
        document.getElementById('manualCodeInput').value = code;
        validateTicketCode(code);
    }

    // ---------------------------------------------------------
    // 4. AJAX VALIDASI TIKET & FEEDBACK ALERT
    // ---------------------------------------------------------
    let isSubmitting = false;

    async function validateTicketCode(code) {
        if (isSubmitting) return;
        isSubmitting = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const feedbackCard = document.getElementById('instantFeedbackCard');
        const successBox = document.getElementById('feedbackSuccessBox');
        const failedBox = document.getElementById('feedbackFailedBox');
        const standbyBox = document.getElementById('standbyInstructionBox');

        try {
            const response = await fetch("{{ route('dashboard.admin-biasa.validasi-tiket.validate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();

            standbyBox.style.display = 'none';
            feedbackCard.classList.remove('feedback-success', 'feedback-danger');
            feedbackCard.classList.add('active');

            if (response.ok && data.success) {
                // HASIL VALIDASI: SUKSES
                playSuccessSound();

                feedbackCard.classList.add('feedback-success');
                successBox.style.display = 'block';
                failedBox.style.display = 'none';

                document.getElementById('fbPassengerName').innerText = data.passenger_name;
                document.getElementById('fbSeatNumber').innerText = 'Kursi ' + data.seat_number;
                document.getElementById('fbDestination').innerText = data.destination || data.route_name;
                document.getElementById('fbVehicleInfo').innerText = (data.vehicle_name || 'Armada') + ' (' + (data.plate_number || '-') + ')';
                document.getElementById('fbBookingCode').innerText = data.booking_code;
                document.getElementById('fbBoardedAt').innerText = data.boarded_at;

                showToast(data.message, 'success');

                // Update real-time tabel penumpang di bawah kamera
                if (data.boarded_passengers) {
                    currentBoardedData = data.boarded_passengers;
                    renderBoardedData();
                } else {
                    fetchBoardedPassengers();
                }

                document.getElementById('manualCodeInput').value = '';
            } else {
                // HASIL VALIDASI: GAGAL / SUDAH DI-SCAN
                playErrorSound();

                feedbackCard.classList.add('feedback-danger');
                successBox.style.display = 'none';
                failedBox.style.display = 'block';

                document.getElementById('fbErrorTitle').innerText = data.title || 'TIKET DITOLAK';
                document.getElementById('fbErrorMessage').innerText = data.message || 'Tiket tidak memenuhi syarat boarding.';

                const settleBox = document.getElementById('fbSettleBox');
                if (settleBox) {
                    if (data.can_settle && data.booking_id) {
                        window.__pendingSettleBookingId = data.booking_id;
                        document.getElementById('fbSettleBookingCode').innerText = data.booking_code || '-';
                        document.getElementById('fbSettleAmount').innerText = 'Rp ' + Number(data.total_price || 0).toLocaleString('id-ID');
                        settleBox.style.display = 'block';
                    } else {
                        window.__pendingSettleBookingId = null;
                        settleBox.style.display = 'none';
                    }
                }

                showToast(data.title || 'Validasi Gagal', 'danger');
            }
        } catch (err) {
            console.error('Validation error:', err);
            playErrorSound();

            standbyBox.style.display = 'none';
            feedbackCard.classList.remove('feedback-success');
            feedbackCard.classList.add('feedback-danger', 'active');
            successBox.style.display = 'none';
            failedBox.style.display = 'block';

            document.getElementById('fbErrorTitle').innerText = 'KONEKSI TERKENDALA';
            document.getElementById('fbErrorMessage').innerText = 'Gagal menghubungi server loket. Pastikan koneksi jaringan aktif.';
        } finally {
            isSubmitting = false;
        }
    }

    const settleRouteTemplate = @json(route('dashboard.admin-biasa.booking.settle', ['booking' => '__BOOKING_ID__']));

    async function confirmSettleFromScanner() {
        const bookingId = window.__pendingSettleBookingId;
        if (!bookingId) return;

        const method = document.getElementById('fbSettleMethod')?.value || 'Cash';
        const btn = document.getElementById('fbSettleConfirmBtn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Memproses...';
        }

        try {
            const res = await fetch(settleRouteTemplate.replace('__BOOKING_ID__', bookingId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method: method,
                    mark_boarded: true
                })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                playSuccessSound();
                showToast(data.message || 'Pelunasan & Boarding berhasil!', 'success');
                dismissFeedback();
                fetchBoardedPassengers();
            } else {
                playErrorSound();
                showToast(data.message || 'Gagal memproses pelunasan.', 'danger');
            }
        } catch (err) {
            console.error(err);
            playErrorSound();
            showToast('Koneksi gagal saat melunasi tiket.', 'danger');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Lunasi & Boarding';
            }
        }
    }

    function dismissFeedback() {
        const feedbackCard = document.getElementById('instantFeedbackCard');
        const standbyBox = document.getElementById('standbyInstructionBox');
        feedbackCard.classList.remove('active');
        standbyBox.style.display = 'grid';
        document.getElementById('manualCodeInput').focus();
    }

    // ---------------------------------------------------------
    // 5. UPDATE TABEL DATA REAL-TIME & FILTER TUJUAN KEBERANGKATAN
    // ---------------------------------------------------------
    async function fetchBoardedPassengers() {
        try {
            const res = await fetch("{{ route('dashboard.admin-biasa.validasi-tiket.boarded') }}", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && data.passengers) {
                currentBoardedData = data.passengers;
                renderBoardedData();
                showToast('Tabel penumpang boarding diperbarui.', 'info');
            }
        } catch (e) {
            console.warn('Gagal memuat data penumpang:', e);
        }
    }

    function setDestinationFilter(dest, btn) {
        selectedDestinationFilter = dest;
        document.querySelectorAll('.dest-tab-btn').forEach(b => {
            if (b.dataset.dest === dest) {
                b.classList.add('is-active');
            } else {
                b.classList.remove('is-active');
            }
        });
        renderBoardedData();
    }

    function toggleViewMode(mode) {
        currentViewMode = mode;
        const btnGrouped = document.getElementById('btnViewGrouped');
        const btnFlat = document.getElementById('btnViewFlat');

        if (mode === 'grouped') {
            btnGrouped.classList.add('is-active');
            btnFlat.classList.remove('is-active');
        } else {
            btnFlat.classList.add('is-active');
            btnGrouped.classList.remove('is-active');
        }
        renderBoardedData();
    }

    function handleSearchBoarded(val) {
        searchQuery = (val || '').trim().toLowerCase();
        renderBoardedData();
    }

    function renderBoardedData() {
        const container = document.getElementById('boardedContentContainer');
        const counterDisplay = document.getElementById('totalBoardedCountDisplay');
        const tabBadgeAll = document.getElementById('tabBadgeAll');

        if (!container) return;

        // 1. Filter Data
        let filtered = currentBoardedData.filter(item => {
            const matchDest = (selectedDestinationFilter === 'all') || (item.destination === selectedDestinationFilter);
            if (!matchDest) return false;

            if (!searchQuery) return true;

            const name = (item.passenger_name || '').toLowerCase();
            const seat = (item.seat_number || '').toLowerCase();
            const code = (item.booking_code || '').toLowerCase();
            const plate = (item.plate_number || '').toLowerCase();
            const dest = (item.destination || '').toLowerCase();

            return name.includes(searchQuery) || seat.includes(searchQuery) || code.includes(searchQuery) || plate.includes(searchQuery) || dest.includes(searchQuery);
        });

        if (counterDisplay) counterDisplay.innerText = currentBoardedData.length;
        if (tabBadgeAll) tabBadgeAll.innerText = currentBoardedData.length;

        // State Kosong
        if (filtered.length === 0) {
            container.innerHTML = `
                <div style="padding: 40px 20px; text-align: center; color: var(--text-light); background: #F8FAF9; border-radius: 12px; border: 1.5px dashed var(--border);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 10px; color: #94A3B8;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <strong style="display: block; font-size: 14px; color: var(--text-dark);">Belum ada penumpang boarding terdata</strong>
                    <span style="font-size: 12px;">Data penumpang online hari ini yang statusnya is_boarded = true akan otomatis muncul di sini setelah tiket berhasil di-scan.</span>
                </div>
            `;
            return;
        }

        // 2. Render Mode Grouped atau Flat
        if (currentViewMode === 'grouped') {
            renderGroupedView(container, filtered);
        } else {
            renderFlatView(container, filtered);
        }
    }

    function renderGroupedView(container, items) {
        // Group By Destination & Vehicle
        const groups = {};
        items.forEach(item => {
            const destKey = item.destination || 'Tujuan Lain';
            if (!groups[destKey]) groups[destKey] = [];
            groups[destKey].push(item);
        });

        let html = '';
        Object.keys(groups).sort().forEach(destName => {
            const groupItems = groups[destName];
            const first = groupItems[0] || {};
            const vehicleInfo = first.vehicle_name ? `${first.vehicle_name} (${first.plate_number})` : 'Armada Pool';
            const depTime = first.departure_time || '-';

            html += `
                <div class="dest-group-card">
                    <div class="dest-group-header">
                        <div class="dest-title-text">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #0C7950;"></span>
                            <span>Tujuan: <strong>${escapeHtml(destName)}</strong></span>
                            <span style="font-size: 12px; font-weight: 600; color: var(--text-light);">&bull; Armada: ${escapeHtml(vehicleInfo)} &bull; Berangkat: ${escapeHtml(depTime)}</span>
                        </div>
                        <span class="status-badge-boarded">
                            &check; ${groupItems.length} Penumpang Masuk Mobil
                        </span>
                    </div>

                    <table class="boarded-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 80px; text-align: center;">Kursi</th>
                                <th>Nama Penumpang</th>
                                <th>Kontak HP</th>
                                <th>Kode Booking / Tiket</th>
                                <th style="width: 140px;">Waktu Scan</th>
                                <th style="width: 130px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${groupItems.map((p, idx) => `
                                <tr>
                                    <td style="text-align: center; color: var(--text-light); font-weight: 700;">${idx + 1}</td>
                                    <td style="text-align: center;"><span class="seat-pill">${escapeHtml(p.seat_number)}</span></td>
                                    <td><strong>${escapeHtml(p.passenger_name)}</strong></td>
                                    <td>${escapeHtml(p.phone)}</td>
                                    <td><span style="font-family: monospace; font-weight: 700;">${escapeHtml(p.booking_code)}</span></td>
                                    <td><span style="font-size: 11px; font-weight: 700; color: #0C5A3E;">${escapeHtml(p.boarded_at)}</span></td>
                                    <td style="text-align: center;">
                                        <span class="status-badge-boarded">&check; Sudah Check-in</span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function renderFlatView(container, items) {
        let html = `
            <div style="border: 1.5px solid var(--border); border-radius: 12px; overflow: hidden;">
                <table class="boarded-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th style="width: 70px; text-align: center;">Kursi</th>
                            <th>Nama Penumpang</th>
                            <th>Kode Booking</th>
                            <th>Tujuan Keberangkatan</th>
                            <th>Mobil / Plat</th>
                            <th style="width: 130px;">Waktu Scan</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map((p, idx) => `
                            <tr>
                                <td style="text-align: center; color: var(--text-light); font-weight: 700;">${idx + 1}</td>
                                <td style="text-align: center;"><span class="seat-pill">${escapeHtml(p.seat_number)}</span></td>
                                <td>
                                    <strong>${escapeHtml(p.passenger_name)}</strong>
                                    <small style="display: block; color: var(--text-light); font-size: 11px;">${escapeHtml(p.phone)}</small>
                                </td>
                                <td><span style="font-family: monospace; font-weight: 700;">${escapeHtml(p.booking_code)}</span></td>
                                <td><strong style="color: #0C7950;">${escapeHtml(p.destination)}</strong></td>
                                <td>${escapeHtml(p.vehicle_name || '')} (${escapeHtml(p.plate_number || '-')})</td>
                                <td><span style="font-size: 11px; font-weight: 700; color: #0C5A3E;">${escapeHtml(p.boarded_at)}</span></td>
                                <td style="text-align: center;">
                                    <span class="status-badge-boarded">&check; Sudah Check-in</span>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function showToast(message, type = 'info') {
        const toast = document.getElementById('toastNotification');
        const msgEl = document.getElementById('toastMessage');
        if (!toast || !msgEl) return;

        toast.className = `toast-notify show ${type}`;
        msgEl.innerText = message;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3200);
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderBoardedData();
        const input = document.getElementById('manualCodeInput');
        if (input) input.focus();
    });
</script>

</body>
</html>
