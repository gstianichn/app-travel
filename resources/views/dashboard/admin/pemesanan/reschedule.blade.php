@extends('layouts.admin')

@section('title', 'Ubah Jadwal & Geser Kursi - ' . $booking->booking_code)

@section('page-title', 'Ubah Jadwal (Reschedule)')

@section('page-subtitle', 'Fitur pemindahan jadwal keberangkatan atau penggeseran kursi penumpang secara resmi.')

@section('content')

<style>
    .reschedule-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 900px;
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

    /* CURRENT INFO CARD */
    .current-card {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .current-card h3 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .current-meta {
        font-size: 13px;
        color: #475569;
    }

    .seat-current-pill {
        display: inline-block;
        background: #e2e8f0;
        color: #0f172a;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
    }

    /* RESCHEDULE FORM CARD */
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

    .form-card-header h2 {
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
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-select,
    .form-input,
    .form-textarea {
        padding: 10px 14px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        color: #17221d;
        background: #fbfdfc;
        outline: none;
    }

    .form-select:focus,
    .form-input:focus,
    .form-textarea:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        background: white;
    }

    /* SEAT PICKER */
    .new-seats-picker {
        background: #f8faf9;
        border: 1px solid #d4dfda;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .seats-grid-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .seat-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: white;
        border: 1.5px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .seat-checkbox-label:hover {
        border-color: #087f55;
        background: #f0fdf4;
    }

    .seat-checkbox-label input[type="checkbox"] {
        accent-color: #087f55;
        width: 16px;
        height: 16px;
    }

    /* FOOTER */
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
        background: #0284c7;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .btn-submit:hover {
        background: #0369a1;
    }
</style>

<div class="reschedule-page">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-trail">
        <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
        <span>&rsaquo;</span>
        <a href="{{ route('dashboard.admin.pemesanan.index') }}">Pemesanan</a>
        <span>&rsaquo;</span>
        <a href="{{ route('dashboard.admin.pemesanan.show', $booking->id) }}">{{ $booking->booking_code }}</a>
        <span>&rsaquo;</span>
        <span>Reschedule</span>
    </div>

    {{-- CURRENT JADWAL INFO CARD --}}
    @php
        $currRoute = $booking->schedule?->route 
            ? ($booking->schedule->route->origin?->name . ' → ' . $booking->schedule->route->destination?->name) 
            : 'Rute Travel';
        $currDate = $booking->schedule?->departure_date ? $booking->schedule->departure_date->format('d M Y') : '-';
        $currTime = $booking->schedule?->departure_time ? \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') : '-';
    @endphp
    <div class="current-card">
        <div>
            <span style="font-size: 11px; font-weight: 700; color: #b45309; text-transform: uppercase;">Jadwal Saat Ini:</span>
            <h3>{{ $currRoute }}</h3>
            <div class="current-meta">
                📅 {{ $currDate }} &bull; Pukul {{ $currTime }} WIB &bull; Pemesan: <strong>{{ $booking->customerName() }}</strong> ({{ $booking->total_passenger }} Orang)
            </div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; display: block; margin-bottom: 4px;">Kursi Terdaftar:</span>
            <div style="display: flex; gap: 6px;">
                @foreach($booking->passengers as $p)
                    <span class="seat-current-pill">
                        💺 {{ $p->seat?->seat_number ?? '-' }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RESCHEDULE FORM --}}
    <div class="form-card">
        <div class="form-card-header">
            <h2>Pilih Jadwal Baru & Alokasikan Kursi</h2>
            <p>Pilih jadwal keberangkatan tujuan dan centang {{ $booking->total_passenger }} kursi kosong.</p>
        </div>

        <form action="{{ route('dashboard.admin.pemesanan.reschedule.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-card-body">

                {{-- PILIH JADWAL BARU --}}
                <div class="form-group">
                    <label class="form-label" for="new_schedule_select">
                        Pilih Jadwal Baru <span class="required">*</span>
                    </label>
                    <select
                        name="new_schedule_id"
                        id="new_schedule_select"
                        class="form-select"
                        onchange="loadScheduleSeats(this)"
                        required
                    >
                        <option value="">-- Pilih Jadwal Keberangkatan Baru --</option>
                        @foreach($availableSchedules as $sch)
                            @php
                                $rLabel = $sch->route 
                                    ? ($sch->route->origin?->name . ' → ' . $sch->route->destination?->name) 
                                    : ($sch->travelRoute?->name ?? 'Rute');
                                $dDate = $sch->departure_date ? $sch->departure_date->format('d/m/Y') : '-';
                                $dTime = $sch->departure_time ? \Carbon\Carbon::parse($sch->departure_time)->format('H:i') : '-';
                                $vName = $sch->vehicle ? ($sch->vehicle->name . ' [' . $sch->vehicle->plate_number . ']') : 'Minibus';
                            @endphp
                            <option value="{{ $sch->id }}" data-capacity="{{ $sch->vehicle?->capacity ?? 10 }}">
                                {{ $dDate }} pukul {{ $dTime }} WIB &bull; {{ $rLabel }} &bull; {{ $vName }} (Sisa {{ $sch->available_seats }} Kursi)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PICK SEATS UNTUK JADWAL BARU --}}
                <div class="new-seats-picker">
                    <span class="form-label">
                        Pilih {{ $booking->total_passenger }} Kursi Baru di Jadwal Tujuan <span class="required">*</span>
                    </span>
                    <div id="seatsContainer">
                        <span style="font-size: 13px; color: #64748b;">
                            Silakan pilih jadwal baru di atas untuk memuat daftar kursi yang tersedia.
                        </span>
                    </div>
                </div>

                {{-- ALASAN RESCHEDULE --}}
                <div class="form-group">
                    <label class="form-label" for="reschedule_reason">
                        Alasan Perubahan Jadwal (Reschedule) <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="reschedule_reason"
                        id="reschedule_reason"
                        class="form-input"
                        placeholder="Contoh: Permintaan penumpang ada keperluan mendadak, geser jam keberangkatan..."
                        value="{{ old('reschedule_reason') }}"
                        required
                    >
                </div>

            </div>

            <div class="form-card-footer">
                <a href="{{ route('dashboard.admin.pemesanan.show', $booking->id) }}" class="btn-cancel">
                    Batal
                </a>
                <button type="submit" class="btn-submit">
                    Simpan Perubahan Jadwal (Reschedule)
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // Data jadwal dan kursi
    const schedulesData = {
        @foreach($availableSchedules as $sch)
            "{{ $sch->id }}": [
                @if($sch->vehicle)
                    @php $sch->vehicle->ensureSeatsExist(); @endphp
                    @foreach($sch->vehicle->seats as $seat)
                        @php
                            $isBooked = \App\Models\BookingPassenger::whereHas('booking', function ($q) use ($sch) {
                                $q->where('schedule_id', $sch->id)->whereNotIn('status', ['Cancelled', 'cancelled']);
                            })->where('seat_id', $seat->id)->exists();
                        @endphp
                        {
                            id: {{ $seat->id }},
                            number: "{{ $seat->seat_number }}",
                            is_booked: {{ $isBooked ? 'true' : 'false' }}
                        },
                    @endforeach
                @endif
            ],
        @endforeach
    };

    const maxSeats = {{ $booking->total_passenger }};

    function loadScheduleSeats(select) {
        const scheduleId = select.value;
        const container = document.getElementById('seatsContainer');
        if (!scheduleId || !schedulesData[scheduleId]) {
            container.innerHTML = `<span style="font-size: 13px; color: #64748b;">Silakan pilih jadwal baru di atas.</span>`;
            return;
        }

        const seats = schedulesData[scheduleId];
        let html = `<div class="seats-grid-wrap">`;

        seats.forEach(s => {
            if (s.is_booked) {
                html += `
                    <label class="seat-checkbox-label" style="opacity: 0.5; background: #f1f5f9; cursor: not-allowed;" title="Kursi sudah terisi">
                        <input type="checkbox" disabled>
                        <span>${s.number} (Terisi)</span>
                    </label>
                `;
            } else {
                html += `
                    <label class="seat-checkbox-label">
                        <input type="checkbox" name="new_seats[]" value="${s.id}" onchange="checkLimit(this)">
                        <span>💺 ${s.number}</span>
                    </label>
                `;
            }
        });

        html += `</div>`;
        html += `<div style="font-size: 11px; color: #087f55; font-weight: 600; margin-top: 8px;">Pilih tepat ${maxSeats} kursi pengganti.</div>`;
        container.innerHTML = html;
    }

    function checkLimit(checkbox) {
        const checked = document.querySelectorAll('input[name="new_seats[]"]:checked');
        if (checked.length > maxSeats) {
            alert(`Anda hanya dapat memilih ${maxSeats} kursi sesuai jumlah penumpang pada tiket ini.`);
            checkbox.checked = false;
        }
    }
</script>

@endsection
