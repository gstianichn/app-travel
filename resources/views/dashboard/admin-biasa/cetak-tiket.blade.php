<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Tiket {{ $booking->booking_code }} - TravelKu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@400;600;700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ticket-font: 'Overpass', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --mono-font: 'Space Mono', monospace;
            --primary: #0C7950;
            --forest: #0C392F;
            --ink: #1F2937;
            --muted: #4B5563;
            --line: #D1D5DB;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--ticket-font);
            background: #E5E7EB;
            color: var(--ink);
            min-height: 100vh;
            padding: 24px 16px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Top Action Bar (Hidden on Print) */
        .ticket-actions {
            max-width: 420px;
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ticket-actions-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .btn-print {
            background: var(--primary);
            color: #FFFFFF;
        }

        .btn-print:hover {
            background: #095c3c;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #FFFFFF;
            color: var(--forest);
            border: 1.5px solid var(--line);
        }

        .btn-secondary:hover {
            background: #F3F4F6;
        }

        /* Thermal Ticket Container */
        .ticket-card {
            width: 100%;
            max-width: 400px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 28px 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Notch decor on edges */
        .ticket-card::before,
        .ticket-card::after {
            content: '';
            position: absolute;
            top: 210px;
            width: 20px;
            height: 20px;
            background: #E5E7EB;
            border-radius: 50%;
            z-index: 2;
        }

        .ticket-card::before { left: -10px; }
        .ticket-card::after { right: -10px; }

        .ticket-header {
            text-align: center;
            display: grid;
            gap: 4px;
        }

        .ticket-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .ticket-logo img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .ticket-logo span {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--forest);
            letter-spacing: -0.5px;
        }

        .ticket-badge-tag {
            display: inline-block;
            background: #EEFBF5;
            color: #0C5A3E;
            border: 1px solid #8FD3B4;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            margin: 4px auto 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ticket-meta {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .ticket-dash {
            border-top: 2px dashed #CBD5E1;
            margin: 2px 0;
        }

        /* Booking Main Identifiers */
        .ticket-code-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #F8FAF9;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
        }

        .code-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--muted);
        }

        .code-val {
            font-family: var(--mono-font);
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--forest);
            letter-spacing: 0.5px;
        }

        .status-pill {
            background: #0C7950;
            color: #FFFFFF;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        /* Route and Departure */
        .ticket-route {
            display: grid;
            gap: 10px;
        }

        .route-endpoints {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
            color: var(--forest);
            font-size: 1.05rem;
        }

        .route-arrow {
            color: var(--primary);
            font-size: 1.2rem;
            padding: 0 8px;
        }

        .ticket-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.82rem;
            background: #F8FAF9;
            padding: 12px;
            border-radius: 10px;
        }

        .info-cell span {
            display: block;
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-cell strong {
            color: var(--ink);
            font-weight: 700;
        }

        /* Passenger list */
        .passenger-list {
            display: grid;
            gap: 8px;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.5px;
        }

        .passenger-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            background: #FFFDF8;
            border: 1px solid #F1E5D1;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .seat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 28px;
            padding: 0 8px;
            background: var(--forest);
            color: #FFFFFF;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.82rem;
        }

        /* Payment Breakdown */
        .payment-summary {
            display: grid;
            gap: 6px;
            font-size: 0.85rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
        }

        .summary-row.total-row {
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--forest);
            border-top: 1.5px solid #E2E8F0;
            padding-top: 8px;
            margin-top: 2px;
        }

        /* QR Code section */
        .qr-section {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
        }

        .qr-box {
            width: 140px;
            height: 140px;
            background: #FFFFFF;
            padding: 8px;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-caption {
            font-size: 0.7rem;
            color: var(--muted);
            max-width: 240px;
            line-height: 1.3;
        }

        .ticket-footer {
            text-align: center;
            font-size: 0.7rem;
            color: var(--muted);
            line-height: 1.4;
            display: grid;
            gap: 4px;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                color: #000000 !important;
            }

            .ticket-actions {
                display: none !important;
            }

            .ticket-card {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 10px 0 !important;
            }

            .ticket-card::before,
            .ticket-card::after {
                display: none !important;
            }

            .btn-action,
            .ticket-actions-bar {
                display: none !important;
            }

            .ticket-dash {
                border-top: 1px dashed #000000 !important;
            }

            .status-pill {
                background: #000000 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .seat-badge {
                background: #000000 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- ACTION BAR UNTUK STAF LOKET --}}
    <div class="ticket-actions no-print">
        <div style="background: #EEFBF5; border: 1.5px solid #8FD3B4; border-radius: 10px; padding: 12px 14px; font-size: 0.85rem; color: #0C5A3E; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
            <span><strong>Tiket berhasil dicatat LUNAS.</strong> Siap dicetak untuk penumpang go-show loket.</span>
        </div>

        <div class="ticket-actions-bar">
            <button type="button" class="btn-action btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak Struk Tiket</span>
            </button>

            <a href="{{ route('dashboard.admin-biasa.booking', ['schedule_id' => $booking->schedule_id]) }}" class="btn-action btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" />
                </svg>
                <span>Ke Manifes Booking</span>
            </a>

            <a href="{{ route('dashboard.admin-biasa.offline-booking') }}" class="btn-action btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Buat Pesanan Baru</span>
            </a>
        </div>
    </div>

    {{-- KARTU STRUK TIKET FISIK --}}
    <div class="ticket-card" id="printableTicket">
        {{-- HEADER LOGO & INFO LOKET --}}
        <header class="ticket-header">
            <div class="ticket-logo">
                <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu">
                <span>TravelKu Shuttle</span>
            </div>
            <p style="font-size: 0.78rem; font-weight: 700; color: var(--forest);">TIKET &amp; BUKTI PEMBAYARAN LOKET</p>
            <div class="ticket-badge-tag">Transaksi Go-Show (Offline)</div>
            <p class="ticket-meta">
                Loket: {{ $booking->schedule?->route?->origin?->name ?? 'Pool TravelKu' }}<br>
                Kasir: {{ $user->name ?? 'Staf Loket' }} • {{ $booking->created_at ? $booking->created_at->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y, H:i') }} WIB
            </p>
        </header>

        <div class="ticket-dash"></div>

        {{-- KODE BOOKING & NOMOR TIKET --}}
        <div class="ticket-code-row">
            <div>
                <span class="code-label">Kode Booking</span>
                <div class="code-val">{{ $booking->booking_code }}</div>
                <div style="font-size: 0.72rem; color: var(--muted); font-family: var(--mono-font); margin-top: 2px;">
                    No: {{ $booking->ticket?->ticket_number ?? 'TKT-'.$booking->booking_code }}
                </div>
            </div>
            <div>
                <span class="status-pill">LUNAS</span>
            </div>
        </div>

        {{-- JADWAL & RUTE PERJALANAN --}}
        <div class="ticket-route">
            <div class="route-endpoints">
                <span>{{ $booking->schedule?->route?->origin?->name ?? 'Asal' }}</span>
                <span class="route-arrow">&rarr;</span>
                <span>{{ $booking->schedule?->route?->destination?->name ?? 'Tujuan' }}</span>
            </div>

            <div class="ticket-info-grid">
                <div class="info-cell">
                    <span>Keberangkatan</span>
                    <strong>{{ $booking->schedule?->departure_date ? \Carbon\Carbon::parse($booking->schedule->departure_date)->translatedFormat('l, d M Y') : '-' }}</strong>
                </div>
                <div class="info-cell">
                    <span>Jam Berangkat</span>
                    <strong>{{ $booking->schedule?->departure_time ? \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') : '-' }} WIB</strong>
                </div>
                <div class="info-cell">
                    <span>Armada</span>
                    <strong>{{ $booking->schedule?->vehicle?->plate_number ?? '-' }}</strong>
                    <small style="display:block; font-size: 0.7rem; color: var(--muted);">{{ $booking->schedule?->vehicle?->name ?? 'Minibus' }}</small>
                </div>
                <div class="info-cell">
                    <span>Pengemudi</span>
                    <strong>{{ $booking->schedule?->driver?->user?->name ?? 'Staf Operasional' }}</strong>
                </div>
            </div>
        </div>

        <div class="ticket-dash"></div>

        {{-- DATA PENUMPANG & NOMOR KURSI --}}
        <div class="passenger-list">
            <div class="section-label">Daftar Penumpang &amp; Kursi ({{ $booking->passengers->count() }} Orang)</div>
            @foreach($booking->passengers as $p)
                <div class="passenger-item">
                    <div>
                        <strong style="color: var(--forest); display: block;">{{ $p->name }}</strong>
                        <span style="font-size: 0.72rem; color: var(--muted);">{{ $p->phone ?: '-' }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span class="seat-badge">Kursi {{ $p->seat?->seat_number ?? '-' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="ticket-dash"></div>

        {{-- RINCIAN PEMBAYARAN KASIR --}}
        <div class="payment-summary">
            <div class="section-label">Rincian Pembayaran Loket</div>
            <div class="summary-row">
                <span>Tarif Tiket</span>
                <span>Rp {{ number_format((float) ($booking->schedule?->effectivePrice() ?? 0), 0, ',', '.') }} &times; {{ $booking->total_passenger }}</span>
            </div>
            <div class="summary-row">
                <span>Metode Pembayaran</span>
                <span><strong>{{ $booking->payment?->payment_method ?? 'Tunai (Cash)' }}</strong></span>
            </div>
            <div class="summary-row total-row">
                <span>TOTAL DIBAYAR</span>
                <span>Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="ticket-dash"></div>

        {{-- QR CODE UNTUK VALIDASI SCAN SAAT NAIK --}}
        <div class="qr-section">
            <div class="qr-box" data-qr="{{ $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code }}"></div>
            <div class="code-val" style="font-size: 0.85rem; color: var(--forest);">{{ $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code }}</div>
            <p class="qr-caption">Tunjukkan QR code ini kepada petugas loket/sopir armada saat proses boarding naik kendaraan.</p>
        </div>

        {{-- KETENTUAN SINGKAT --}}
        <footer class="ticket-footer">
            <p><strong>Ketentuan Perjalanan:</strong></p>
            <p>1. Penumpang wajib tiba di loket pool paling lambat 15 menit sebelum jam keberangkatan.</p>
            <p>2. Tiket yang sudah dibayar di loket sah untuk jadwal dan nomor kursi yang tertera di atas.</p>
            <p>Terima kasih telah mempercayakan perjalanan Anda kepada TravelKu.</p>
        </footer>
    </div>

    {{-- SCRIPT DRAW QR CODE & AUTO-PRINT --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        function drawQrCodes() {
            document.querySelectorAll('[data-qr]').forEach(element => {
                const value = element.dataset.qr;
                if (!value) return;

                if (typeof window.qrcode === 'function') {
                    try {
                        const code = window.qrcode(0, 'M');
                        code.addData(value);
                        code.make();
                        element.innerHTML = code.createSvgTag({ cellSize: 3, margin: 0, scalable: true });
                        const svg = element.querySelector('svg');
                        if (svg) {
                            svg.style.width = '100%';
                            svg.style.height = '100%';
                            svg.style.display = 'block';
                        }
                        return;
                    } catch (e) {
                        console.warn('QR render fallback:', e);
                    }
                }

                const encoded = encodeURIComponent(value);
                element.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=${encoded}" alt="QR ${value}" style="width: 100%; height: 100%; object-fit: contain;">`;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            drawQrCodes();

            @if(request()->query('auto_print') == '1' || session('auto_print'))
                setTimeout(() => {
                    window.print();
                }, 600);
            @endif
        });
    </script>
</body>
</html>

