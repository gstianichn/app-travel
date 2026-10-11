<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($tab === 'financial') Laporan Rekonsiliasi Keuangan
        @elseif($tab === 'occupancy') Laporan Keterisian Kursi (Load Factor)
        @elseif($tab === 'manifest') Manifes Penumpang Travel
        @else Rekapitulasi Pembatalan & Refund
        @endif
        - {{ $startDate }} s/d {{ $endDate }}
    </title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #f8fafc;
            font-size: 12px;
            line-height: 1.5;
            padding: 24px;
        }

        .print-container {
            max-width: 1050px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        /* ACTION BAR (NO PRINT) */
        .action-bar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding: 12px 18px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            max-width: 1050px;
            margin: 0 auto 20px;
        }

        .btn-print {
            padding: 9px 18px;
            background: #087f55;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #066b47;
        }

        .btn-close-print {
            padding: 8px 16px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        /* KOP SURAT */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 20px;
            border-bottom: 3px double #0f172a;
            margin-bottom: 24px;
        }

        .company-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .company-logo-badge {
            width: 58px;
            height: 58px;
            background: #087f55;
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
        }

        .company-name h1 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .company-name p {
            font-size: 11px;
            color: #475569;
        }

        .doc-meta {
            text-align: right;
            font-size: 11px;
            color: #64748b;
        }

        .doc-meta strong {
            color: #0f172a;
        }

        /* TITLE SECTION */
        .report-title-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .report-title-box h2 {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-period {
            display: inline-block;
            margin-top: 4px;
            padding: 3px 12px;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: #087f55;
        }

        /* SUMMARY BOXES */
        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-box {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .summary-box-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
        }

        .summary-box-val {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* REPORT TABLE */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 24px;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }

        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }

        .report-table tfoot td {
            background-color: #f8fafc;
            font-weight: 800;
            font-size: 11px;
            border-top: 2px solid #0f172a;
        }

        /* SIGNATURE SECTION */
        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            page-break-inside: avoid;
        }

        .sig-block {
            text-align: center;
            width: 220px;
        }

        .sig-space {
            height: 60px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-role {
            font-size: 11px;
            color: #64748b;
        }

        /* PRINT MEDIA STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .action-bar-top {
                display: none !important;
            }

            .print-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }

            .report-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-box {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- TOP ACTION BAR (HIDDEN DURING PRINT) --}}
    <div class="action-bar-top">
        <div>
            <strong>Mode Pratinjau Dokumen Cetak / PDF</strong>
            <div style="font-size: 11px; color: #64748b;">Klik tombol cetak untuk menyimpan langsung sebagai file PDF atau cetak ke printer.</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-print" onclick="window.print()">
                <span></span> Cetak / Simpan sebagai PDF
            </button>
            <button type="button" class="btn-close-print" onclick="window.close()">
                Tutup Jendela
            </button>
        </div>
    </div>

    {{-- PRINT CONTAINER --}}
    <div class="print-container">

        {{-- KOP SURAT RESMI --}}
        <div class="kop-surat">
            <div class="company-identity">
                <div class="company-name">
                    <h1>TRAVEL TRANS UTAMA</h1>
                    <p>Layanan Shuttle Antarkota & Minibus Eksekutif Modern</p>
                    <p>Kantor Pusat: Jl. Dr. Djunjunan No. 120, Bandung &bull; Telp: (022) 8765-4321 &bull; info@travel.test</p>
                </div>
            </div>

            <div class="doc-meta">
                <div>No. Dokumen: <strong>DOC/LAP/{{ date('Ymd') }}/{{ strtoupper($tab) }}</strong></div>
                <div>Tanggal Cetak: <strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                <div>Oleh: <strong>{{ $user->name }} ({{ $user->role }})</strong></div>
            </div>
        </div>

        {{-- REPORT TITLE --}}
        <div class="report-title-box">
            <h2>
                @if($tab === 'financial') LAPORAN REKONSILIASI KEUANGAN & PENDAPATAN TIKET
                @elseif($tab === 'occupancy') LAPORAN TINGKAT KETERISIAN KURSI (LOAD FACTOR OPERASIONAL)
                @elseif($tab === 'manifest') MANIFES PENUMPANG OPERASIONAL TRAVEL
                @else REKAPITULASI PEMBATALAN TIKET & PENGELUARAN REFUND
                @endif
            </h2>
            <div class="report-period">
                Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}
                @if($selectedRoute)
                    &bull; Rute: {{ $selectedRoute->routeTitle() }}
                @else
                    &bull; Seluruh Rute Perjalanan
                @endif
            </div>
        </div>

        {{-- TAB 1: FINANCIAL RECONCILIATION --}}
        @if($tab === 'financial')
            <div class="summary-row">
                <div class="summary-box">
                    <div class="summary-box-label">Total Pendapatan Kotor</div>
                    <div class="summary-box-val">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="summary-box">
                    <div class="summary-box-label">Total Refund Ditransfer</div>
                    <div class="summary-box-val" style="color: #dc2626;">- Rp {{ number_format($totalRefund, 0, ',', '.') }}</div>
                </div>
                <div class="summary-box">
                    <div class="summary-box-label">Biaya Admin Ditahan</div>
                    <div class="summary-box-val">Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
                </div>
                <div class="summary-box" style="background: #e3f5ee; border-color: #bbf7d0;">
                    <div class="summary-box-label" style="color: #087f55;">Pendapatan Bersih (Net)</div>
                    <div class="summary-box-val" style="color: #087f55;">Rp {{ number_format($netRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kode Booking</th>
                        <th>No. Tagihan (Invoice)</th>
                        <th>Tanggal Transaksi</th>
                        <th>Nama Pemesan</th>
                        <th>Rute Perjalanan</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th style="text-align: right;">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $idx => $b)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-family: monospace; font-weight: 700;">{{ $b->booking_code }}</td>
                            <td style="font-family: monospace;">{{ $b->payment?->order_id ?? '-' }}</td>
                            <td>{{ $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $b->user?->name ?? 'Pelanggan' }}</td>
                            <td>{{ $b->schedule?->route?->origin?->city }} - {{ $b->schedule?->route?->destination?->city }}</td>
                            <td>{{ $b->payment?->payment_method ?? 'Online' }}</td>
                            <td>Lunas</td>
                            <td style="text-align: right;">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: #64748b;">Tidak ada transaksi tiket pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="text-align: right;">TOTAL PENDAPATAN KOTOR:</td>
                        <td style="text-align: right;">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align: right; color: #dc2626;">PENGEMBALIAN DANA REFUND:</td>
                        <td style="text-align: right; color: #dc2626;">- Rp {{ number_format($totalRefund, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align: right; color: #087f55;">PENDAPATAN BERSIH REALISTIS:</td>
                        <td style="text-align: right; color: #087f55;">Rp {{ number_format($netRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

        {{-- TAB 2: OCCUPANCY & LOAD FACTOR --}}
        @elseif($tab === 'occupancy')
            @php
                $totCap = 0;
                $totOcc = 0;
            @endphp
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Rute Perjalanan</th>
                        <th>Unit Minibus</th>
                        <th>No. Polisi</th>
                        <th>Sopir</th>
                        <th style="text-align: center;">Kapasitas</th>
                        <th style="text-align: center;">Terisi</th>
                        <th style="text-align: center;">Kosong</th>
                        <th style="text-align: right;">Load Factor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $idx => $s)
                        @php
                            $cap = $s->vehicle?->capacity ?? 10;
                            $occ = 0;
                            foreach ($s->bookings as $sb) {
                                if (!in_array(strtolower($sb->status), ['cancelled', 'batal'])) {
                                    $occ += $sb->passengers->count() > 0 ? $sb->passengers->count() : ($sb->total_passenger ?? 1);
                                }
                            }
                            $empty = max(0, $cap - $occ);
                            $lf = $cap > 0 ? round(($occ / $cap) * 100, 1) : 0;
                            $totCap += $cap;
                            $totOcc += $occ;
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $s->departure_date ? $s->departure_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $s->departure_time }}</td>
                            <td>{{ $s->route?->origin?->city }} - {{ $s->route?->destination?->city }}</td>
                            <td>{{ $s->vehicle?->name ?? 'Minibus' }}</td>
                            <td style="font-family: monospace;">{{ $s->vehicle?->plate_number ?? '-' }}</td>
                            <td>{{ $s->driver?->user?->name ?? 'Belum Ditugaskan' }}</td>
                            <td style="text-align: center;">{{ $cap }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $occ }}</td>
                            <td style="text-align: center;">{{ $empty }}</td>
                            <td style="text-align: right; font-weight: 700;">{{ $lf }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; color: #64748b;">Tidak ada jadwal perjalanan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right;">TOTAL OPERASIONAL:</td>
                        <td style="text-align: center;">{{ $totCap }}</td>
                        <td style="text-align: center;">{{ $totOcc }}</td>
                        <td style="text-align: center;">{{ max(0, $totCap - $totOcc) }}</td>
                        <td style="text-align: right; color: #087f55;">
                            {{ $totCap > 0 ? round(($totOcc / $totCap) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tfoot>
            </table>

        {{-- TAB 3: MANIFEST PENUMPANG --}}
        @elseif($tab === 'manifest')
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Tanggal Jalan</th>
                        <th>Jam</th>
                        <th>No. Tiket</th>
                        <th>Kode Booking</th>
                        <th>Nama Penumpang</th>
                        <th style="text-align: center;">Kursi</th>
                        <th>Kontak Telepon</th>
                        <th>Rute</th>
                        <th>Titik Naik (Pool)</th>
                        <th>Titik Turun</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($passengers as $idx => $p)
                        @php $sch = $p->booking?->schedule; @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $sch?->departure_date ? $sch->departure_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $sch?->departure_time ?? '-' }}</td>
                            <td style="font-family: monospace; font-weight: 700;">{{ $p->booking?->ticket?->ticket_number ?? '-' }}</td>
                            <td style="font-family: monospace;">{{ $p->booking?->booking_code ?? '-' }}</td>
                            <td style="font-weight: 700;">{{ $p->name }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $p->seat?->seat_number ?? '-' }}</td>
                            <td>{{ $p->phone ?? $p->booking?->user?->phone ?? '-' }}</td>
                            <td>{{ $sch?->route?->origin?->city }} - {{ $sch?->route?->destination?->city }}</td>
                            <td>{{ $sch?->route?->origin?->name ?? '-' }}</td>
                            <td>{{ $sch?->route?->destination?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; color: #64748b;">Tidak ada data penumpang pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        {{-- TAB 4: REKAP PEMBATALAN & REFUND --}}
        @else
            @php
                $totOrig = 0;
                $totFee = 0;
                $totNet = 0;
            @endphp
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kode Booking</th>
                        <th>Tanggal Permohonan</th>
                        <th>Nama Pemesan</th>
                        <th>Alasan Pembatalan</th>
                        <th style="text-align: right;">Nominal Asli</th>
                        <th style="text-align: right;">Potongan Admin</th>
                        <th style="text-align: right;">Dana Ditransfer</th>
                        <th>Rekening Tujuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellations as $idx => $rf)
                        @php
                            $orig = (float) ($rf->original_amount ?? ($rf->booking ? $rf->booking->total_price : $rf->amount));
                            $fee = (float) $rf->cancellation_fee;
                            $net = (float) $rf->amount;
                            $totOrig += $orig;
                            $totFee += $fee;
                            $totNet += $net;
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-family: monospace; font-weight: 700;">{{ $rf->booking?->booking_code ?? '#'.$rf->booking_id }}</td>
                            <td>{{ $rf->created_at ? $rf->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $rf->booking?->user?->name ?? $rf->user?->name ?? 'Pelanggan' }}</td>
                            <td>{{ $rf->reason ?? '-' }}</td>
                            <td style="text-align: right;">Rp {{ number_format($orig, 0, ',', '.') }}</td>
                            <td style="text-align: right; color: #dc2626;">Rp {{ number_format($fee, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700; color: #087f55;">Rp {{ number_format($net, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($rf->bank_name ?? 'BANK') }} - {{ $rf->account_number ?? '-' }}</td>
                            <td>{{ $rf->statusLabel() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; color: #64748b;">Tidak ada data pembatalan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;">TOTAL:</td>
                        <td style="text-align: right;">Rp {{ number_format($totOrig, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #dc2626;">Rp {{ number_format($totFee, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #087f55;">Rp {{ number_format($totNet, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- OTORISASI & TANDA TANGAN --}}
        <div class="signature-grid">
            <div class="sig-block">
                <div>Disiapkan Oleh:</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $user->name }}</div>
                <div class="sig-role">Staf Administrasi & Akuntansi</div>
            </div>

            <div class="sig-block">
                <div>Diperiksa & Disetujui Oleh:</div>
                <div class="sig-space"></div>
                <div class="sig-name">Pimpinan Operasional</div>
                <div class="sig-role">Direktur / General Manager</div>
            </div>
        </div>

    </div>

</body>
</html>
