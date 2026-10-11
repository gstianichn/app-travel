@extends('layouts.admin')

@section('title', 'Input Tiket Manual (Walk-in / WhatsApp)')

@section('page-title', 'Input Tiket Loket (Walk-in / WA)')

@section('page-subtitle', 'Formulir cepat pendaftaran tiket penumpang loket, WhatsApp, atau telepon dengan pemilihan kursi langsung dari denah.')

@section('content')

<style>
    .manual-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 950px;
        margin: 0 auto;
    }

    /* BREADCRUMB */
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

    /* FLASH ALERTS */
    .alert-box {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .alert-box.error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    /* MAIN FORM CARD */
    .form-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .form-card-header {
        padding: 22px 26px;
        border-bottom: 1px solid #edf1ef;
    }

    .form-card-header h1 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 800;
        color: #17221d;
    }

    .form-card-header p {
        margin: 0;
        font-size: 12px;
        color: #6a7972;
    }

    .form-card-body {
        padding: 26px;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* SECTION TITLE */
    .section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #e2eae6;
    }

    .section-head-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .section-head-text h3 {
        margin: 0 0 2px;
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
    }

    .section-head-text p {
        margin: 0;
        font-size: 12px;
        color: #7b8882;
    }

    /* FORM GRID & INPUTS */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 10px 14px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        color: #17221d;
        background: #fbfdfc;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        background: white;
    }

    .form-hint {
        font-size: 11px;
        color: #7b8882;
    }

    /* INTERACTIVE SEAT PICKER */
    .seat-picker-wrap {
        background: #f8faf9;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .seat-picker-chassis {
        width: 100%;
        max-width: 400px;
        background: white;
        border: 2px solid #cbd5e1;
        border-radius: 28px 28px 16px 16px;
        padding: 20px 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    }

    .picker-windshield {
        height: 38px;
        background: #e0f2fe;
        border: 1.5px solid #93c5fd;
        border-radius: 18px 18px 6px 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: #0284c7;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .picker-front-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 2px dashed #e2e8f0;
    }

    .picker-driver-seat {
        width: 64px;
        height: 64px;
        border-radius: 10px;
        background: #475569;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        gap: 2px;
    }

    .picker-cabin-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .picker-row {
        display: flex;
        align-items: center;
        justify-content: space-around;
        gap: 8px;
    }

    /* SEAT BUTTON IN PICKER */
    .pick-seat-btn {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px solid;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }

    .pick-seat-btn.available {
        background: #dcfce7;
        border-color: #86efac;
        color: #15803d;
    }

    .pick-seat-btn.available:hover {
        background: #bbf7d0;
        transform: scale(1.05);
    }

    .pick-seat-btn.selected {
        background: #087f55 !important;
        border-color: #066b47 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(8, 127, 85, 0.35);
        transform: scale(1.05);
    }

    .pick-seat-btn.occupied {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .pick-seat-btn.occupied-paid {
        background: #e0f2fe;
        border-color: #bae6fd;
        color: #0284c7;
        cursor: not-allowed;
    }

    .pick-seat-btn.occupied-pending {
        background: #fef3c7;
        border-color: #fde68a;
        color: #d97706;
        cursor: not-allowed;
    }

    /* SELECTION SUMMARY BADGE */
    .selection-summary-card {
        width: 100%;
        max-width: 400px;
        background: #e3f5ee;
        border: 1px solid #c8ebd8;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* FOOTER ACTIONS */
    .form-card-footer {
        padding: 18px 26px;
        background: #f9fbf9;
        border-top: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 10px 18px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        background: white;
        color: #52635c;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-submit {
        padding: 10px 24px;
        border: none;
        border-radius: 10px;
        background: #087f55;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(8, 127, 85, 0.2);
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        background: #066b47;
    }

    @media (max-width: 768px) {
        .grid-2, .grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="manual-page">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-trail">
        <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
        <span>&rsaquo;</span>
        <a href="{{ route('dashboard.admin.pemesanan.index') }}">Pemesanan</a>
        <span>&rsaquo;</span>
        <span>Input Tiket Loket (Walk-in / WA)</span>
    </div>

    @if(session('error'))
        <div class="alert-box error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- MAIN FORM --}}
    <div class="form-card">
        <div class="form-card-header">
            <h1>Formulir Cepat Tiket Loket & WhatsApp</h1>
            <p>Pilih jadwal keberangkatan travel, pilih kursi dari denah visual, dan masukkan identitas penumpang.</p>
        </div>

        <form action="{{ route('dashboard.admin.pemesanan.store-manual') }}" method="POST" id="manualBookingForm">
            @csrf

            <div class="form-card-body">

                {{-- =========================================
                     1. PILIH JADWAL KEBERANGKATAN
                ========================================== --}}
                <div class="section-head">
                    <div class="section-head-text">
                        <h3>1. Jadwal Keberangkatan Travel</h3>
                        <p>Pilih jam jalan dan rute yang diinginkan oleh penumpang loket</p>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="schedule_select">
                        Pilih Jadwal Travel <span class="required">*</span>
                    </label>
                    <select
                        name="schedule_id"
                        id="schedule_select"
                        class="form-select"
                        onchange="changeSchedule(this.value)"
                        required
                    >
                        <option value="">-- Pilih Jadwal Keberangkatan --</option>
                        @foreach($schedules as $sch)
                            @php
                                $rLabel = $sch->route 
                                    ? ($sch->route->origin?->name . ' ke ' . $sch->route->destination?->name) 
                                    : ($sch->travelRoute?->name ?? 'Rute');
                                $dDate = $sch->departure_date ? $sch->departure_date->format('d/m/Y') : '-';
                                $dTime = $sch->departure_time ? \Carbon\Carbon::parse($sch->departure_time)->format('H:i') : '-';
                                $vName = $sch->vehicle ? ($sch->vehicle->name . ' [' . $sch->vehicle->plate_number . ']') : 'Minibus';
                                $priceFormatted = number_format($sch->route?->price ?? 100000, 0, ',', '.');
                            @endphp
                            <option
                                value="{{ $sch->id }}"
                                data-price="{{ $sch->route?->price ?? 100000 }}"
                                {{ ($selectedSchedule && $selectedSchedule->id == $sch->id) ? 'selected' : '' }}
                            >
                                {{ $dDate }} pukul {{ $dTime }} WIB &bull; {{ $rLabel }} &bull; {{ $vName }} &bull; Rp {{ $priceFormatted }} (Sisa {{ $sch->available_seats }} Kursi)
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Memilih jadwal akan otomatis memuat denah kursi kendaraan di bawah.</div>
                </div>

                {{-- =========================================
                     2. DENAH KURSI INTERAKTIF
                ========================================== --}}
                <div class="section-head">
                    <div class="section-head-text">
                        <h3>2. Pilih Kursi dari Denah Visual</h3>
                        <p>Klik pada kursi berwarna hijau (tersedia) untuk memilih kursi penumpang</p>
                    </div>
                </div>

                @if($selectedSchedule && count($occupancyMap) > 0)
                    <div class="seat-picker-wrap">
                        <div class="seat-picker-chassis">
                            <div class="picker-windshield">Kaca Depan Minibus</div>

                            {{-- BARIS DEPAN (STANDAR SETIR KANAN INDONESIA) --}}
                            <div class="picker-front-row">
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
                                    @php
                                        $isAvail = $seat1A['occupancy']['status'] === 'available';
                                        $cls = $isAvail ? 'available' : ($seat1A['occupancy']['status'] === 'paid' ? 'occupied-paid' : 'occupied-pending');
                                    @endphp
                                    <button
                                        type="button"
                                        class="pick-seat-btn {{ $cls }}"
                                        data-seat-id="{{ $seat1A['seat_id'] }}"
                                        data-seat-num="{{ $seat1A['seat_number'] }}"
                                        title="Kursi 1A (Depan Kiri Samping Pintu)"
                                        @if(!$isAvail) disabled title="Kursi sudah terisi" @else onclick="toggleSeat(this)" @endif
                                    >
                                        {{ $seat1A['seat_number'] }}
                                        <span style="font-size: 8px; font-weight: 700;">
                                            {{ $isAvail ? 'Pilih' : 'Terisi' }}
                                        </span>
                                    </button>
                                @else
                                    <div style="width: 64px; height: 64px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94a3b8;">
                                        Pintu Kiri
                                    </div>
                                @endif

                                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">
                                    Lorong / Pintu
                                </div>

                                <div class="picker-driver-seat" title="Kursi Kemudi Sopir (Setir Kanan Indonesia)">
                                    <span style="font-size: 16px;"></span>
                                    <span>Sopir</span>
                                    <span style="font-size: 8px; opacity: 0.85; font-weight: 800;">(KANAN)</span>
                                </div>
                            </div>

                            {{-- BARIS BELAKANG --}}
                            <div class="picker-cabin-grid">
                                @foreach($occupancyMap as $rowNum => $rowSeats)
                                    @if((string) $rowNum === '1') @continue @endif
                                    @php $seatCount = count($rowSeats); @endphp
                                    <div class="picker-row" @if($seatCount === 2) style="justify-content: space-between; padding: 0 4px;" @endif>
                                        @foreach($rowSeats as $seatIdx => $seat)
                                            @php
                                                $isAvail = $seat['occupancy']['status'] === 'available';
                                                $cls = $isAvail ? 'available' : ($seat['occupancy']['status'] === 'paid' ? 'occupied-paid' : 'occupied-pending');
                                            @endphp
                                            <button
                                                type="button"
                                                class="pick-seat-btn {{ $cls }}"
                                                data-seat-id="{{ $seat['seat_id'] }}"
                                                data-seat-num="{{ $seat['seat_number'] }}"
                                                title="Kursi {{ $seat['seat_number'] }} ({{ $seatIdx === 0 ? 'Kiri' : 'Kanan Belakang Sopir' }})"
                                                @if(!$isAvail) disabled title="Kursi sudah terisi" @else onclick="toggleSeat(this)" @endif
                                            >
                                                {{ $seat['seat_number'] }}
                                                <span style="font-size: 8px; font-weight: 700;">
                                                    {{ $isAvail ? 'Pilih' : 'Terisi' }}
                                                </span>
                                            </button>

                                            {{-- Sisipkan lorong tengah jika 2 kursi --}}
                                            @if($seatCount === 2 && $seatIdx === 0)
                                                <div style="font-size: 9px; font-weight: 700; color: #cbd5e1; text-transform: uppercase;">
                                                    Lorong
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- SEAT SELECTION SUMMARY --}}
                        <div class="selection-summary-card">
                            <div>
                                <span style="font-size: 11px; font-weight: 700; color: #087f55; text-transform: uppercase;">Kursi Terpilih:</span>
                                <div id="selectedSeatsDisplay" style="font-size: 15px; font-weight: 800; color: #17221d;">
                                    Belum memilih kursi
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 11px; color: #6a7972;">Estimasi Tarif:</span>
                                <div id="totalPriceDisplay" style="font-size: 16px; font-weight: 800; color: #087f55;">
                                    Rp 0
                                </div>
                            </div>
                        </div>

                        {{-- Hidden Container for Inputs --}}
                        <div id="hiddenSeatsContainer"></div>
                    </div>
                @else
                    <div style="padding: 24px; text-align: center; background: #f8faf9; border-radius: 12px; border: 1px dashed #d4dfda; color: #72817a; font-size: 13px;">
                        Silakan pilih jadwal travel di atas terlebih dahulu untuk menampilkan denah kursi armada minibus.
                    </div>
                @endif

                {{-- =========================================
                     3. DATA PEMESAN & PENUMPANG
                ========================================== --}}
                <div class="section-head">
                    <div class="section-head-text">
                        <h3>3. Identitas Pemesan & Penumpang</h3>
                        <p>Kontak WhatsApp aktif untuk pengiriman e-ticket dan koordinasi keberangkatan</p>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="customer_name">
                            Nama Lengkap Pemesan <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="customer_name"
                            id="customer_name"
                            class="form-input"
                            placeholder="Contoh: Budi Santoso"
                            value="{{ old('customer_name') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_phone">
                            Nomor WhatsApp / Telepon <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="customer_phone"
                            id="customer_phone"
                            class="form-input"
                            placeholder="Contoh: 08123456789"
                            value="{{ old('customer_phone') }}"
                            required
                        >
                        <div class="form-hint">Nomor kontak utama untuk dikirimi e-ticket dan jadwal penjemputan.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_email">
                            Alamat Email (Opsional)
                        </label>
                        <input
                            type="email"
                            name="customer_email"
                            id="customer_email"
                            class="form-input"
                            placeholder="Contoh: budi@gmail.com"
                            value="{{ old('customer_email') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">
                            Titik Jemput / Titik Turun / Catatan Khusus
                        </label>
                        <textarea
                            name="notes"
                            id="notes"
                            rows="2"
                            class="form-textarea"
                            placeholder="Contoh: Jemput di depan SPBU Pasteur, bawa 1 koper sedang..."
                        >{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- =========================================
                     4. SALURAN & PEMBAYARAN LOKET
                ========================================== --}}
                <div class="section-head">
                    <div class="section-head-text">
                        <h3>4. Saluran Pesanan & Pembayaran</h3>
                        <p>Sumber masuknya pemesanan dan status pelunasan pembayaran loket</p>
                    </div>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="source">
                            Saluran Pemesanan <span class="required">*</span>
                        </label>
                        <select name="source" id="source" class="form-select" required>
                            <option value="walk_in">Loket / Datang Langsung (Walk-in)</option>
                            <option value="whatsapp">Pesan via WhatsApp CS</option>
                            <option value="phone">Pesan via Telepon</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_method">
                            Metode Pembayaran <span class="required">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="Cash">Cash / Tunai di Loket</option>
                            <option value="Transfer Bank">Transfer Bank Rekening</option>
                            <option value="QRIS">QRIS Loket / Statis</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_status">
                            Status Pembayaran <span class="required">*</span>
                        </label>
                        <select name="payment_status" id="payment_status" class="form-select" required>
                            <option value="paid">Langsung Lunas (Terbitkan Tiket)</option>
                            <option value="pending">⏳ Bayar Nanti di Loket (Pending)</option>
                        </select>
                    </div>
                </div>

            </div>

            {{-- FOOTER SUBMIT --}}
            <div class="form-card-footer">
                <a href="{{ route('dashboard.admin.pemesanan.index') }}" class="btn-cancel">
                    Batal
                </a>
                <button type="submit" class="btn-submit" id="submitBookingBtn" disabled>
                    Simpan Pemesanan Tiket
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    let selectedSeatIds = [];
    let selectedSeatNums = [];
    let unitPrice = {{ $selectedSchedule ? ($selectedSchedule->route?->price ?? 100000) : 100000 }};

    function changeSchedule(scheduleId) {
        if (scheduleId) {
            window.location.href = `{{ route('dashboard.admin.pemesanan.create-manual') }}?schedule_id=${scheduleId}`;
        }
    }

    function toggleSeat(btn) {
        const seatId = btn.getAttribute('data-seat-id');
        const seatNum = btn.getAttribute('data-seat-num');

        const index = selectedSeatIds.indexOf(seatId);
        if (index > -1) {
            // Unselect
            selectedSeatIds.splice(index, 1);
            selectedSeatNums.splice(index, 1);
            btn.classList.remove('selected');
        } else {
            // Select
            selectedSeatIds.push(seatId);
            selectedSeatNums.push(seatNum);
            btn.classList.add('selected');
        }

        updateSelectionUI();
    }

    function updateSelectionUI() {
        const display = document.getElementById('selectedSeatsDisplay');
        const priceDisplay = document.getElementById('totalPriceDisplay');
        const hiddenContainer = document.getElementById('hiddenSeatsContainer');
        const submitBtn = document.getElementById('submitBookingBtn');

        if (!display || !hiddenContainer) return;

        hiddenContainer.innerHTML = '';

        if (selectedSeatIds.length === 0) {
            display.innerText = 'Belum memilih kursi';
            priceDisplay.innerText = 'Rp 0';
            if (submitBtn) submitBtn.disabled = true;
        } else {
            display.innerText = selectedSeatNums.join(', ') + ` (${selectedSeatIds.length} Kursi)`;
            const total = selectedSeatIds.length * unitPrice;
            priceDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
            if (submitBtn) submitBtn.disabled = false;

            selectedSeatIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_seats[]';
                input.value = id;
                hiddenContainer.appendChild(input);
            });
        }
    }
</script>

@endsection
