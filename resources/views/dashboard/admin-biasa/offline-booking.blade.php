<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Loket (Offline) - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @include('dashboard.admin-biasa.partials.styles')
    @fonts('overpass')
    @vite(['resources/css/dashboard-staff.css'])

    <style>
        .loket-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.25fr);
            gap: 24px;
            align-items: start;
        }

        .loket-form-group {
            display: grid;
            gap: 6px;
        }

        .loket-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--lp-forest);
        }

        .loket-input,
        .loket-select,
        .loket-textarea {
            width: 100%;
            min-height: 44px;
            padding: 10px 14px;
            border: 1.5px solid var(--lp-field-border);
            border-radius: 10px;
            background: #FFFFFF;
            color: var(--lp-ink);
            font: inherit;
            font-size: 0.95rem;
        }

        .loket-textarea {
            min-height: 76px;
            resize: vertical;
        }

        .loket-input:focus,
        .loket-select:focus,
        .loket-textarea:focus {
            outline: none;
            border-color: var(--lp-green);
            box-shadow: 0 0 0 3px rgb(12 121 80 / 0.14);
        }

        .loket-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .loket-seat-chassis {
            max-width: 360px;
            margin: 0 auto;
            padding: 18px 16px;
            border: 2px solid var(--lp-line);
            border-radius: 24px 24px 14px 14px;
            background: #FFFFFF;
            display: grid;
            gap: 12px;
        }

        .loket-windshield {
            padding: 8px;
            border-radius: 14px 14px 6px 6px;
            background: #E3EEF6;
            color: #174A6E;
            font-size: 0.75rem;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .loket-seat-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            align-items: center;
        }

        .loket-driver-box {
            grid-column: 4;
            min-height: 52px;
            border-radius: 10px;
            background: #22403A;
            color: #FFFFFF;
            font-size: 0.72rem;
            font-weight: 800;
            display: grid;
            place-items: center;
            text-align: center;
        }

        .loket-seat-btn {
            min-height: 52px;
            border-radius: 10px;
            border: 2px solid;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            transition: transform 0.12s ease, background-color 0.15s ease;
        }

        .loket-seat-btn small {
            font-size: 0.65rem;
            font-weight: 600;
        }

        .loket-seat-btn--available {
            background: #EEFBF5;
            border-color: #8FD3B4;
            color: #0C5A3E;
        }

        .loket-seat-btn--available:hover {
            background: #DFF0E8;
            transform: translateY(-1px);
        }

        .loket-seat-btn--selected {
            background: #0C7950 !important;
            border-color: #0C392F !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 10px rgb(12 121 80 / 0.3);
        }

        .loket-seat-btn--occupied {
            background: #F1F5F9;
            border-color: #CBD5E1;
            color: #64748B;
            cursor: not-allowed;
        }

        .loket-seat-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            font-size: 0.8rem;
            color: var(--lp-muted);
        }

        .loket-seat-legend span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .loket-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            display: inline-block;
            border: 1.5px solid;
        }

        .loket-summary-box {
            padding: 16px 18px;
            border-radius: 12px;
            background: #EEF6F1;
            border: 1px solid #C6E2D2;
            display: grid;
            gap: 6px;
        }

        .loket-passenger-card {
            padding: 14px;
            border-radius: 10px;
            border: 1px solid var(--lp-line);
            background: #FFFDF8;
            display: grid;
            gap: 10px;
        }

        @media (max-width: 1024px) {
            .loket-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .loket-row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body data-staff="staff">

<div class="dashboard-wrapper">

    @include('dashboard.admin-biasa.partials.sidebar')

    <main class="main-content">

        @include('dashboard.admin-biasa.partials.topbar', [
            'topbarTitle' => 'Pemesanan Loket (Customer Offline)',
            'topbarSubtitle' => 'Input pesanan pelanggan walk-in, WhatsApp, atau telepon secara langsung tanpa lewat website.',
        ])

        <section class="content">
            <div class="ds">
                <header class="ds-head">
                    <div>
                        <h1>Pemesanan Loket Pool (Non-Website)</h1>
                        <p>Kursi sinkron real-time dengan pemesanan Online Web/App agar tidak terjadi bentrok kursi.</p>
                    </div>
                    <div class="ds-head-actions">
                        <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}" class="ds-btn ds-btn--secondary">Validasi E-Ticket (Online)</a>
                        <a href="{{ route('dashboard.admin-biasa.booking') }}" class="ds-btn ds-btn--secondary">Lihat Manifes</a>
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

                @if ($errors->any())
                    <div class="ds-alert ds-alert--error" role="alert" style="display: grid; gap: 4px;">
                        <strong>Mohon periksa kembali isian formulir:</strong>
                        <ul style="padding-left: 18px; margin: 0;">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($schedules->isEmpty())
                    <section class="ds-panel">
                        <p class="ds-empty">
                            <b>Belum ada jadwal keberangkatan aktif.</b>
                            Jadwal harus dibuat terlebih dahulu oleh Admin Utama sebelum loket dapat melayani pemesanan tiket offline.
                        </p>
                    </section>
                @else
                    <form method="POST" action="{{ route('dashboard.admin-biasa.offline-booking.store') }}" id="offlineBookingForm">
                        @csrf

                        <div class="loket-grid">
                            {{-- PANEL KIRI: JADWAL & PILIH KURSI REAL-TIME --}}
                            <section class="ds-panel" aria-labelledby="loket-kursi-title">
                                <div class="ds-panel-head">
                                    <div>
                                        <h2 id="loket-kursi-title">1. Pilih Jadwal &amp; Kursi</h2>
                                        <p>Kursi terisi Online maupun Offline otomatis terkunci.</p>
                                    </div>
                                </div>

                                <div class="ds-panel-body" style="display: grid; gap: 18px;">
                                    <div class="loket-form-group">
                                        <label class="loket-label" for="schedule_id">Jadwal Keberangkatan</label>
                                        <select name="schedule_id" id="schedule_id" class="loket-select" onchange="loadScheduleSeats(this.value)" required>
                                            @foreach ($schedules as $sched)
                                                @php
                                                    $routeText = ($sched->route?->origin?->name ?? 'Asal').' → '.($sched->route?->destination?->name ?? 'Tujuan');
                                                    $dateText = $sched->departure_date ? \Carbon\Carbon::parse($sched->departure_date)->translatedFormat('d M Y') : '-';
                                                    $timeText = $sched->departure_time ? \Carbon\Carbon::parse($sched->departure_time)->format('H:i').' WIB' : '-';
                                                    $priceText = 'Rp '.number_format((float) $sched->effectivePrice(), 0, ',', '.');
                                                @endphp
                                                <option value="{{ $sched->id }}" @selected(old('schedule_id', $selectedSchedule?->id) == $sched->id)>
                                                    {{ $dateText }} • {{ $timeText }} — {{ $routeText }} ({{ $sched->vehicle?->plate_number ?? '-' }} • {{ $sched->available_seats }} kursi sisa • {{ $priceText }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="loket-seat-legend">
                                        <span><i class="loket-dot" style="background: #EEFBF5; border-color: #8FD3B4;"></i> Tersedia</span>
                                        <span><i class="loket-dot" style="background: #0C7950; border-color: #0C392F;"></i> Dipilih</span>
                                        <span><i class="loket-dot" style="background: #F1F5F9; border-color: #CBD5E1;"></i> Terisi (Online / Loket)</span>
                                    </div>

                                    <div class="loket-seat-chassis" id="seatMapContainer">
                                        <div class="loket-windshield">Kaca Depan Armada</div>
                                        <div id="seatGridBody" style="display: grid; gap: 10px;">
                                            <p class="ds-sub" style="text-align: center;">Memuat denah kursi...</p>
                                        </div>
                                    </div>

                                    <div class="loket-summary-box">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                            <span>Kursi dipilih:</span>
                                            <strong id="summarySeatsText">Belum ada kursi dipilih</strong>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 1.05rem; color: var(--lp-forest);">
                                            <span>Total Tarif:</span>
                                            <strong id="summaryTotalText">Rp 0</strong>
                                        </div>
                                    </div>

                                    <div id="selectedSeatInputs"></div>
                                </div>
                            </section>

                            {{-- PANEL KANAN: DATA PEMESAN, PENUMPANG & PEMBAYARAN LOKET --}}
                            <section class="ds-panel" aria-labelledby="loket-data-title">
                                <div class="ds-panel-head">
                                    <div>
                                        <h2 id="loket-data-title">2. Data Pelanggan &amp; Pembayaran Loket</h2>
                                        <p>Untuk pelanggan walk-in, WhatsApp, atau telepon.</p>
                                    </div>
                                </div>

                                <div class="ds-panel-body" style="display: grid; gap: 16px;">
                                    <div class="loket-form-group">
                                        <label class="loket-label" for="source">Saluran Pemesanan Offline</label>
                                        <select name="source" id="source" class="loket-select" required>
                                            <option value="walk_in" @selected(old('source', 'walk_in') === 'walk_in')>Datang Langsung di Pool (Walk-In)</option>
                                            <option value="whatsapp" @selected(old('source') === 'whatsapp')>Pesan via WhatsApp Pool / CS</option>
                                            <option value="phone" @selected(old('source') === 'phone')>Pesan via Telepon Pool</option>
                                        </select>
                                    </div>

                                    <div class="loket-row-2">
                                        <div class="loket-form-group">
                                            <label class="loket-label" for="customer_name">Nama Pemesan / Penumpang Utama <span style="color: #A3341F;">*</span></label>
                                            <input type="text" id="customer_name" name="customer_name" class="loket-input" value="{{ old('customer_name') }}" placeholder="Contoh: Budi Santoso" required maxlength="100" oninput="syncDefaultPassengerNames()">
                                        </div>

                                        <div class="loket-form-group">
                                            <label class="loket-label" for="customer_phone">Nomor HP / WA (Angka saja) <span style="color: #A3341F;">*</span></label>
                                            <input type="tel" id="customer_phone" name="customer_phone" class="loket-input" value="{{ old('customer_phone') }}" placeholder="081234567890" required maxlength="20" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>

                                    <div class="loket-form-group">
                                        <label class="loket-label" for="customer_email">Email (Opsional)</label>
                                        <input type="email" id="customer_email" name="customer_email" class="loket-input" value="{{ old('customer_email') }}" placeholder="email@contoh.com" maxlength="100">
                                    </div>

                                    <div id="passengersDetailSection" style="display: none; gap: 10px;">
                                        <span class="loket-label">Rincian Penumpang per Kursi Terpilih</span>
                                        <div id="passengerCardsList" style="display: grid; gap: 10px;"></div>
                                    </div>

                                    <div class="loket-row-2">
                                        <div class="loket-form-group">
                                            <label class="loket-label" for="payment_method">Metode Pembayaran Loket</label>
                                            <select name="payment_method" id="payment_method" class="loket-select" required>
                                                <option value="Cash" @selected(old('payment_method', 'Cash') === 'Cash')>Tunai (Cash di Loket)</option>
                                                <option value="QRIS" @selected(old('payment_method') === 'QRIS')>QRIS Loket Pool</option>
                                                <option value="Transfer Bank" @selected(old('payment_method') === 'Transfer Bank')>Transfer Bank (Verifikasi Staf)</option>
                                            </select>
                                        </div>

                                        <div class="loket-form-group">
                                            <label class="loket-label" for="payment_status">Status Pembayaran</label>
                                            <select name="payment_status" id="payment_status" class="loket-select" onchange="toggleAutoBoardOption()" required>
                                                <option value="paid" @selected(old('payment_status', 'paid') === 'paid')>Lunas Sekarang (Terbitkan Tiket)</option>
                                                <option value="pending" @selected(old('payment_status') === 'pending')>Bayar Saat Tiba di Pool (Pending 30 Menit)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <label id="autoBoardWrap" style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 10px; background: #F8FAF9; border: 1px solid var(--lp-line); cursor: pointer;">
                                        <input type="checkbox" name="auto_board" value="1" @checked(old('auto_board'))>
                                        <span style="font-size: 0.9rem; font-weight: 700; color: var(--lp-forest);">
                                            Langsung tandai <em>Boarded</em> (penumpang langsung masuk kabin armada sekarang)
                                        </span>
                                    </label>

                                    <div class="loket-form-group">
                                        <label class="loket-label" for="notes">Catatan Khusus / Titik Jemput (Opsional)</label>
                                        <textarea id="notes" name="notes" class="loket-textarea" placeholder="Contoh: Bawa 1 koper sedang / tunggu di gerbang pool" maxlength="500">{{ old('notes') }}</textarea>
                                    </div>

                                    <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; padding-top: 6px;">
                                        <a href="{{ route('dashboard.admin-biasa.index') }}" class="ds-btn ds-btn--secondary">Batal</a>
                                        <button type="submit" id="submitLoketBtn" class="ds-btn ds-btn--primary" disabled>
                                            Simpan &amp; Terbitkan Tiket Loket
                                        </button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </form>
                @endif
            </div>
        </section>

    </main>

</div>

<script>
    const seatsEndpointTemplate = @json(route('dashboard.admin-biasa.offline-booking.seats', ['schedule' => '__SCHED_ID__']));
    let selectedSeats = new Map(); // seatId => seatNumber
    let currentUnitPrice = 0;

    async function loadScheduleSeats(scheduleId) {
        if (!scheduleId) return;

        selectedSeats.clear();
        renderSelectionState();

        const container = document.getElementById('seatGridBody');
        container.innerHTML = '<p class="ds-sub" style="text-align: center;">Memuat denah kursi...</p>';

        try {
            const url = seatsEndpointTemplate.replace('__SCHED_ID__', scheduleId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            if (!res.ok || !data.success) {
                container.innerHTML = '<p class="ds-sub" style="color: #A3341F; text-align: center;">' + (data.message || 'Gagal memuat kursi.') + '</p>';
                return;
            }

            currentUnitPrice = Number(data.schedule.price || 0);
            renderSeatGrid(data.seats);
        } catch (err) {
            console.error(err);
            container.innerHTML = '<p class="ds-sub" style="color: #A3341F; text-align: center;">Gagal mengambil data kursi armada.</p>';
        }
    }

    function renderSeatGrid(seats) {
        const container = document.getElementById('seatGridBody');
        container.innerHTML = '';

        const rows = {};
        seats.forEach(s => {
            const r = String(s.seat_row || '1');
            if (!rows[r]) rows[r] = [];
            rows[r].push(s);
        });

        Object.keys(rows).sort((a, b) => Number(a) - Number(b)).forEach(rowNum => {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'loket-seat-row';

            const rowSeats = rows[rowNum].sort((a, b) => a.seat_column - b.seat_column);

            for (let col = 1; col <= 4; col++) {
                if (rowNum === '1' && col === 4) {
                    const driverEl = document.createElement('div');
                    driverEl.className = 'loket-driver-box';
                    driverEl.textContent = 'SOPIR';
                    rowDiv.appendChild(driverEl);
                    continue;
                }

                const seat = rowSeats.find(item => Number(item.seat_column) === col);
                if (!seat) {
                    const spacer = document.createElement('div');
                    rowDiv.appendChild(spacer);
                    continue;
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.seatId = String(seat.id);
                btn.dataset.seatNum = seat.seat_number;

                if (seat.status === 'available') {
                    btn.className = 'loket-seat-btn loket-seat-btn--available';
                    btn.innerHTML = `<span>${seat.seat_number}</span><small>Kosong</small>`;
                    btn.onclick = () => toggleLoketSeat(btn);
                } else {
                    btn.className = 'loket-seat-btn loket-seat-btn--occupied';
                    btn.disabled = true;
                    const tag = seat.status === 'paid' ? 'Terisi' : 'Ditahan';
                    btn.title = `${seat.passenger_name || 'Terisi'} (${seat.channel || 'Booking'})`;
                    btn.innerHTML = `<span>${seat.seat_number}</span><small>${tag}</small>`;
                }

                rowDiv.appendChild(btn);
            }

            container.appendChild(rowDiv);
        });
    }

    function toggleLoketSeat(btn) {
        const id = btn.dataset.seatId;
        const num = btn.dataset.seatNum;

        if (selectedSeats.has(id)) {
            selectedSeats.delete(id);
            btn.classList.remove('loket-seat-btn--selected');
        } else {
            selectedSeats.set(id, num);
            btn.classList.add('loket-seat-btn--selected');
        }

        renderSelectionState();
    }

    function renderSelectionState() {
        const summarySeats = document.getElementById('summarySeatsText');
        const summaryTotal = document.getElementById('summaryTotalText');
        const inputsWrap = document.getElementById('selectedSeatInputs');
        const cardsSection = document.getElementById('passengersDetailSection');
        const cardsList = document.getElementById('passengerCardsList');
        const submitBtn = document.getElementById('submitLoketBtn');

        if (!summarySeats || !inputsWrap) return;

        inputsWrap.innerHTML = '';
        cardsList.innerHTML = '';

        if (selectedSeats.size === 0) {
            summarySeats.textContent = 'Belum ada kursi dipilih';
            summaryTotal.textContent = 'Rp 0';
            cardsSection.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        const seatNumbers = Array.from(selectedSeats.values());
        const total = selectedSeats.size * currentUnitPrice;

        summarySeats.textContent = seatNumbers.join(', ') + ` (${selectedSeats.size} kursi)`;
        summaryTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        cardsSection.style.display = 'grid';
        submitBtn.disabled = false;

        const defaultName = document.getElementById('customer_name')?.value.trim() || '';
        const defaultPhone = document.getElementById('customer_phone')?.value.trim() || '';

        let idx = 0;
        selectedSeats.forEach((seatNum, seatId) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'selected_seats[]';
            hidden.value = seatId;
            inputsWrap.appendChild(hidden);

            const card = document.createElement('div');
            card.className = 'loket-passenger-card';
            const suggestedName = idx === 0 ? defaultName : (defaultName ? `${defaultName} (${idx + 1})` : '');
            card.innerHTML = `
                <strong style="font-size: 0.85rem; color: var(--lp-forest);">Penumpang Kursi ${seatNum}</strong>
                <div class="loket-row-2">
                    <input type="text" name="passengers[${seatId}][name]" class="loket-input" value="${escapeHtml(suggestedName)}" placeholder="Nama penumpang kursi ${seatNum}" required maxlength="100">
                    <input type="tel" name="passengers[${seatId}][phone]" class="loket-input" value="${escapeHtml(defaultPhone)}" placeholder="No. HP (opsional, angka saja)" maxlength="20" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            `;
            cardsList.appendChild(card);
            idx++;
        });
    }

    function syncDefaultPassengerNames() {
        const defaultName = document.getElementById('customer_name')?.value.trim() || '';
        const inputs = document.querySelectorAll('#passengerCardsList input[name$="[name]"]');
        inputs.forEach((input, idx) => {
            if (!input.value.trim()) {
                input.value = idx === 0 ? defaultName : (defaultName ? `${defaultName} (${idx + 1})` : '');
            }
        });
    }

    function toggleAutoBoardOption() {
        const status = document.getElementById('payment_status')?.value;
        const wrap = document.getElementById('autoBoardWrap');
        if (!wrap) return;
        const cb = wrap.querySelector('input[type="checkbox"]');
        if (status === 'paid') {
            wrap.style.display = 'flex';
        } else {
            wrap.style.display = 'none';
            if (cb) cb.checked = false;
        }
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const selectEl = document.getElementById('schedule_id');
        if (selectEl && selectEl.value) {
            loadScheduleSeats(selectEl.value);
        }
        toggleAutoBoardOption();
    });
</script>

</body>
</html>

