@extends('layouts.admin')

@section('title', 'Denah Kursi Visual - Jadwal #' . $schedule->id)

@section('page-title', 'Denah Kursi Visual (Seat Map)')

@section('page-subtitle', 'Visualisasi tata letak kursi minibus, ketersediaan kursi, dan status okupansi penumpang secara real-time.')

@section('content')

<style>
    .seatmap-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1050px;
        margin: 0 auto;
    }

    /* BREADCRUMB & HEADER */
    .nav-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-trail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #7b8882;
    }

    .breadcrumb-trail a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-trail a:hover {
        text-decoration: underline;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        background: white;
        color: #55665e;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-back:hover {
        background: #f3f6f4;
        color: #17221d;
    }

    .btn-action-manifest {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        background: #087f55;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-manifest:hover {
        background: #066b47;
    }

    /* =========================================
       SCHEDULE HERO OVERVIEW
    ========================================== */
    .schedule-hero-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 22px 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .hero-route h1 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 800;
        color: #17221d;
    }

    .hero-details {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 13px;
        color: #6a7972;
        flex-wrap: wrap;
    }

    .badge-hero-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        color: #17221d;
    }

    /* =========================================
       STATS & COLOR LEGEND BAR
    ========================================== */
    .legend-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .legend-items-list {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .legend-swatch {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        display: inline-block;
        border: 1.5px solid;
    }

    .legend-swatch.available {
        background: #dcfce7;
        border-color: #86efac;
    }

    .legend-swatch.pending {
        background: #fef3c7;
        border-color: #fcd34d;
    }

    .legend-swatch.paid {
        background: #dbeafe;
        border-color: #93c5fd;
    }

    .stats-pill-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-pill {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .stat-pill.avail {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-pill.pend {
        background: #fef3c7;
        color: #b45309;
    }

    .stat-pill.pd {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* =========================================
       VISUAL MINIBUS CABIN LAYOUT
    ========================================== */
    .van-wrapper {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 20px;
        padding: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .van-chassis {
        width: 100%;
        max-width: 440px;
        background: #f8faf9;
        border: 2px solid #cbd5e1;
        border-radius: 36px 36px 20px 20px;
        padding: 24px 20px 24px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
    }

    /* Windshield Front */
    .van-windshield {
        height: 48px;
        background: linear-gradient(180deg, #bae6fd 0%, #e0f2fe 100%);
        border: 2px solid #93c5fd;
        border-radius: 24px 24px 8px 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #0369a1;
        text-transform: uppercase;
    }

    /* Driver & Front Row */
    .van-front-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px dashed #cbd5e1;
    }

    .driver-seat {
        width: 72px;
        height: 72px;
        border-radius: 12px;
        background: #334155;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        gap: 3px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }

    .driver-steering-icon {
        font-size: 20px;
        line-height: 1;
    }

    /* Passenger Rows Grid */
    .passenger-cabin {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .cabin-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .row-number-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        width: 24px;
        text-align: center;
    }

    .row-seats-container {
        display: flex;
        align-items: center;
        justify-content: space-around;
        flex: 1;
        gap: 10px;
    }

    /* INDIVIDUAL SEAT STYLING */
    .seat-card {
        width: 76px;
        height: 76px;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        padding: 4px;
        border: 2px solid;
    }

    .seat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .seat-card.available {
        background: #dcfce7;
        border-color: #86efac;
        color: #15803d;
    }

    .seat-card.pending {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #b45309;
    }

    .seat-card.paid {
        background: #dbeafe;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .seat-num {
        font-size: 16px;
        font-weight: 800;
        line-height: 1.1;
    }

    .seat-status-mini {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 3px;
        letter-spacing: 0.3px;
    }

    .seat-name-mini {
        font-size: 9px;
        font-weight: 600;
        max-width: 68px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 1px;
    }

    /* Minibus Rear Trunk */
    .van-rear-trunk {
        height: 24px;
        background: #e2e8f0;
        border: 2px solid #cbd5e1;
        border-radius: 4px 4px 16px 16px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* SEAT DETAIL POPUP / SIDE INFO */
    .seat-info-modal {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .seat-info-modal.show {
        display: flex;
    }

    .seat-modal-card {
        background: white;
        border-radius: 18px;
        width: 100%;
        max-width: 420px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .modal-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 22px;
        color: #9ca3af;
        cursor: pointer;
    }

    .modal-spec-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f3;
        font-size: 13px;
    }

    .modal-spec-label {
        color: #6b7280;
    }

    .modal-spec-val {
        font-weight: 700;
        color: #111827;
    }
</style>

<div class="seatmap-page">

    {{-- TOP NAV & BREADCRUMB --}}
    <div class="nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>&rsaquo;</span>
            <a href="{{ route('dashboard.admin.pemesanan.index') }}">Pemesanan</a>
            <span>&rsaquo;</span>
            <span>Denah Kursi Visual</span>
        </div>

        <div class="header-actions">
            <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="btn-action-back">
                &larr; Kembali ke Daftar
            </a>
            <a href="{{ route('dashboard.admin.pemesanan.manifes', $schedule->id) }}" class="btn-action-manifest">
                Manifes Perjalanan
            </a>
            <a href="{{ route('dashboard.admin.pemesanan.create-manual', ['schedule_id' => $schedule->id]) }}" class="btn-action-manifest" style="background: #0284c7;">
                + Input Tiket Loket
            </a>
        </div>
    </div>

    {{-- SCHEDULE OVERVIEW CARD --}}
    @php
        $routeLabel = $schedule->route 
            ? ($schedule->route->origin?->name . ' ke ' . $schedule->route->destination?->name) 
            : ($schedule->travelRoute?->name ?? 'Rute Travel');
    @endphp
    <div class="schedule-hero-card">
        <div class="hero-route">
            <span style="font-size: 11px; font-weight: 700; color: #087f55; text-transform: uppercase; letter-spacing: 0.5px;">
                Jadwal Perjalanan Travel
            </span>
            <h1>{{ $routeLabel }}</h1>
            <div class="hero-details">
                <span class="badge-hero-item">
                    {{ $schedule->departure_date ? $schedule->departure_date->format('d M Y') : '-' }}
                </span>
                <span>&bull;</span>
                <span class="badge-hero-item">
                    ⏰ Pukul {{ $schedule->departure_time ? \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') : '-' }} WIB
                </span>
                <span>&bull;</span>
                <span class="badge-hero-item">
                    {{ $schedule->vehicle?->name ?? 'Minibus' }} ({{ $schedule->vehicle?->plate_number ?? '-' }})
                </span>
                <span>&bull;</span>
                <span class="badge-hero-item">
                    Sopir: {{ $schedule->driver?->user?->name ?? 'Belum ditentukan' }}
                </span>
            </div>
        </div>

        <div>
            <span style="font-size: 12px; color: #6a7972;">Tarif Tiket:</span>
            <div style="font-size: 20px; font-weight: 800; color: #087f55;">
                Rp {{ number_format($schedule->route?->price ?? 100000, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- LEGEND & OKUPANSI PILLS --}}
    <div class="legend-card">
        <div class="legend-items-list">
            <div class="legend-item">
                <span class="legend-swatch available"></span>
                <span>Kosong / Tersedia</span>
            </div>
            <div class="legend-item">
                <span class="legend-swatch pending"></span>
                <span>Dipesan Sementara (Pending)</span>
            </div>
            <div class="legend-item">
                <span class="legend-swatch paid"></span>
                <span>Sudah Lunas (Terisi)</span>
            </div>
        </div>

        <div class="stats-pill-group">
            <span class="stat-pill avail">
                Kosong: {{ $availableCount }} Kursi
            </span>
            <span class="stat-pill pend">
                Pending: {{ $pendingCount }} Kursi
            </span>
            <span class="stat-pill pd">
                Lunas: {{ $occupiedCount }} Kursi
            </span>
        </div>
    </div>

    {{-- VISUAL MINIBUS CABIN --}}
    <div class="van-wrapper">
        <div class="van-chassis">
            {{-- KACA DEPAN --}}
            <div class="van-windshield">
                Kaca Depan Minibus
            </div>

            {{-- BARIS DEPAN: KURSI 1A (KIRI) & DRIVER (KANAN - STANDAR INDONESIA) --}}
            <div class="van-front-row">
                {{-- Cari kursi 1A pada baris 1 (Depan Kiri samping pintu) --}}
                @php
                    $seat1A = null;
                    if (isset($occupancyMap['1'])) {
                        foreach ($occupancyMap['1'] as $s) {
                            if ($s['seat_number'] === '1A') {
                                $seat1A = $s;
                                break;
                            }
                        }
                    }
                @endphp

                @if($seat1A)
                    <div
                        class="seat-card {{ $seat1A['occupancy']['status'] }}"
                        onclick='showSeatDetail(@json($seat1A))'
                        title="Kursi 1A (Depan Kiri Samping Pintu)"
                    >
                        <span class="seat-num">{{ $seat1A['seat_number'] }}</span>
                        <span class="seat-status-mini">
                            {{ $seat1A['occupancy']['status'] === 'available' ? 'Kosong' : ($seat1A['occupancy']['status'] === 'paid' ? 'Lunas' : 'Pending') }}
                        </span>
                        @if($seat1A['occupancy']['passenger_name'])
                            <span class="seat-name-mini" title="{{ $seat1A['occupancy']['passenger_name'] }}">
                                {{ $seat1A['occupancy']['passenger_name'] }}
                            </span>
                        @endif
                    </div>
                @else
                    <div style="width: 76px; height: 76px; border: 2px dashed #cbd5e1; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8;">
                        Pintu Kiri
                    </div>
                @endif

                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; text-align: center;">
                    Pintu / Lorong
                </div>

                <div class="driver-seat" title="Kursi Kemudi Sopir (Setir Kanan Indonesia)">
                    <span class="driver-steering-icon"></span>
                    <span>Sopir</span>
                    <span style="font-size: 8px; opacity: 0.85; font-weight: 800;">(KANAN)</span>
                </div>
            </div>

            {{-- BARIS PENUMPANG BELAKANG --}}
            <div class="passenger-cabin">
                @foreach($occupancyMap as $rowNum => $rowSeats)
                    @if((string) $rowNum === '1')
                        @continue
                    @endif

                    <div class="cabin-row">
                        <span class="row-number-label">R{{ $rowNum }}</span>
                        <div class="row-seats-container" @if(count($rowSeats) === 2) style="justify-content: space-between; padding: 0 4px;" @endif>
                            @php $seatCount = count($rowSeats); @endphp
                            @foreach($rowSeats as $seatIdx => $seat)
                                <div
                                    class="seat-card {{ $seat['occupancy']['status'] }}"
                                    onclick='showSeatDetail(@json($seat))'
                                    title="Klik untuk detail kursi {{ $seat['seat_number'] }} ({{ $seatIdx === 0 ? 'Kiri' : 'Kanan Belakang Sopir' }})"
                                >
                                    <span class="seat-num">{{ $seat['seat_number'] }}</span>
                                    <span class="seat-status-mini">
                                        {{ $seat['occupancy']['status'] === 'available' ? 'Kosong' : ($seat['occupancy']['status'] === 'paid' ? 'Lunas' : 'Pending') }}
                                    </span>
                                    @if($seat['occupancy']['passenger_name'])
                                        <span class="seat-name-mini" title="{{ $seat['occupancy']['passenger_name'] }}">
                                            {{ $seat['occupancy']['passenger_name'] }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Sisipkan lorong tengah jika baris berisi 2 kursi (kiri dan kanan belakang supir) --}}
                                @if($seatCount === 2 && $seatIdx === 0)
                                    <div style="font-size: 10px; font-weight: 700; color: #cbd5e1; text-transform: uppercase;">
                                        Lorong
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- BAGASI BELAKANG --}}
            <div class="van-rear-trunk">
                Bagasi Belakang Armada
            </div>
        </div>
    </div>

</div>

{{-- MODAL DETAIL KURSI --}}
<div id="seatModal" class="seat-info-modal">
    <div class="seat-modal-card">
        <div class="modal-header-row">
            <h3 id="modalSeatTitle" style="margin: 0; font-size: 18px; font-weight: 800; color: #111827;"></h3>
            <button type="button" class="btn-close-modal" onclick="closeSeatModal()">&times;</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <div class="modal-spec-row">
                <span class="modal-spec-label">Status Kursi</span>
                <span id="modalSeatStatus" class="modal-spec-val"></span>
            </div>

            <div class="modal-spec-row">
                <span class="modal-spec-label">Nama Penumpang</span>
                <span id="modalPassengerName" class="modal-spec-val"></span>
            </div>

            <div class="modal-spec-row">
                <span class="modal-spec-label">Nomor WhatsApp / HP</span>
                <span id="modalPassengerPhone" class="modal-spec-val"></span>
            </div>

            <div class="modal-spec-row">
                <span class="modal-spec-label">Kode Booking</span>
                <span id="modalBookingCode" class="modal-spec-val"></span>
            </div>

            <div class="modal-spec-row">
                <span class="modal-spec-label">Saluran Pemesanan</span>
                <span id="modalBookingSource" class="modal-spec-val"></span>
            </div>

            <div class="modal-spec-row">
                <span class="modal-spec-label">Titik Jemput / Catatan</span>
                <span id="modalSpecialReq" class="modal-spec-val"></span>
            </div>
        </div>

        <div id="modalActionContainer" style="margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" class="btn-action-back" onclick="closeSeatModal()">Tutup</button>
            <a id="modalBookingLink" href="#" class="btn-action-manifest" style="display: none;">
                Lihat E-Ticket</a>
        </div>
    </div>
</div>

<script>
    function showSeatDetail(seat) {
        const modal = document.getElementById('seatModal');
        const title = document.getElementById('modalSeatTitle');
        const status = document.getElementById('modalSeatStatus');
        const name = document.getElementById('modalPassengerName');
        const phone = document.getElementById('modalPassengerPhone');
        const code = document.getElementById('modalBookingCode');
        const source = document.getElementById('modalBookingSource');
        const notes = document.getElementById('modalSpecialReq');
        const link = document.getElementById('modalBookingLink');

        title.innerText = `Detail Kursi ${seat.seat_number}`;

        if (seat.occupancy.status === 'available') {
            status.innerHTML = `<span style="color: #15803d; font-weight: 700;">Kosong / Tersedia</span>`;
            name.innerText = 'Belum Dipesan';
            phone.innerText = '-';
            code.innerText = '-';
            source.innerText = '-';
            notes.innerText = '-';
            link.style.display = 'none';
        } else {
            const isPaid = seat.occupancy.status === 'paid';
            status.innerHTML = isPaid 
                ? `<span style="color: #1d4ed8; font-weight: 700;">Sudah Lunas</span>`
                : `<span style="color: #b45309; font-weight: 700;">Dipesan Sementara (Pending)</span>`;

            name.innerText = seat.occupancy.passenger_name || 'Tamu';
            phone.innerText = seat.occupancy.passenger_phone || '-';
            code.innerText = seat.occupancy.booking_code || '-';
            source.innerText = seat.occupancy.booking_source || 'Website';
            notes.innerText = seat.occupancy.special_request || '-';

            if (seat.occupancy.booking_id) {
                link.href = `/pemesanan/${seat.occupancy.booking_id}`;
                link.style.display = 'inline-flex';
            } else {
                link.style.display = 'none';
            }
        }

        modal.classList.add('show');
    }

    function closeSeatModal() {
        document.getElementById('seatModal').classList.remove('show');
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('seatModal');
        if (e.target === modal) {
            closeSeatModal();
        }
    });
</script>

@endsection
