@php
    $seatNumber = $seat['seat_number'];
    $seatId = $seat['seat_id'];
    $occStatus = $seat['occupancy']['status'] ?? 'available';

    $isBoarded = false;
    $pName = null;
    $pPhone = null;
    $pWa = null;
    $pPickup = null;
    $pDropoff = null;
    $pPayment = null;
    $pBaggage = null;
    $pBookingCode = null;
    $pId = null;
    $isPaid = false;

    if ($passenger) {
        $isBoarded = $passenger->is_boarded;
        $pName = $passenger->name;
        $pPhone = $passenger->phone;
        $pWa = $passenger->wa_phone;
        $pPickup = $passenger->pickup_point;
        $pDropoff = $passenger->dropoff_point;
        $pPayment = $passenger->payment_label;
        $pBaggage = $passenger->baggage_notes;
        $pBookingCode = $passenger->booking_code;
        $pId = $passenger->id;
        $isPaid = $passenger->is_paid;
    } elseif ($occStatus !== 'available') {
        $pName = $seat['occupancy']['passenger_name'] ?? 'Penumpang';
        $pPhone = $seat['occupancy']['passenger_phone'] ?? '-';
        $pWa = '6281234567890';
        $pPickup = $activeSchedule->route?->origin?->name ?? 'Pool Keberangkatan';
        $pDropoff = $activeSchedule->route?->destination?->name ?? 'Pool Kedatangan';
        $pPayment = $occStatus === 'paid' ? 'Lunas Online' : 'Tagih Tunai / COD';
        $pBaggage = '1 Tas Kabin Standar';
        $pBookingCode = $seat['occupancy']['booking_code'] ?? 'TRV-BOOKING';
        $isPaid = ($occStatus === 'paid');
    }

    $seatData = [
        'seatNumber' => $seatNumber,
        'seatId' => $seatId,
        'isAvailable' => ($occStatus === 'available' && ! $passenger),
        'isBoarded' => $isBoarded,
        'passengerName' => $pName ?? 'Kursi Kosong',
        'phone' => $pPhone ?? '-',
        'waPhone' => $pWa ?? '',
        'pickup' => $pPickup ?? '-',
        'dropoff' => $pDropoff ?? '-',
        'payment' => $pPayment ?? ($occStatus === 'available' ? 'Tersedia' : 'Lunas Online'),
        'baggage' => $pBaggage ?? '1 Tas Standar',
        'bookingCode' => $pBookingCode ?? '-',
        'passengerId' => $pId,
        'isPaid' => $isPaid,
        'checkinUrl' => $pId ? route('dashboard.driver.passenger.checkin', $pId) : '',
    ];
@endphp

@if ($isBoarded)
    {{-- 1. Kursi Sudah Naik (Boarded) - Hijau Emerald Sukses --}}
    <button type="button"
            class="seat-btn-driver w-12 h-12 rounded-lg bg-emerald-600 text-white flex flex-col items-center justify-center leading-none select-none transition-transform hover:scale-105 shadow-sm font-bold border-2 border-emerald-600"
            data-seat="{{ json_encode($seatData) }}"
            onclick="selectDriverSeat(this)"
            title="Kursi {{ $seatNumber }}: {{ $pName }} (Sudah Naik)">
        <span class="text-xs font-extrabold">{{ $seatNumber }}</span>
        <span class="text-[9px] mt-0.5 tracking-tight font-semibold uppercase">✓ Naik</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, {{ $pName }}, sudah naik kabin</span>
    </button>
@elseif ($pName || $occStatus !== 'available')
    {{-- 2. Kursi Dipesan / Terisi (Belum Naik) - Amber / Kuning Dashed identik Akun User --}}
    <button type="button"
            class="seat-btn-driver w-12 h-12 rounded-lg border-2 border-dashed border-amber-700 bg-amber-50 text-amber-900 flex flex-col items-center justify-center leading-none select-none transition-transform hover:scale-105 font-bold"
            data-seat="{{ json_encode($seatData) }}"
            onclick="selectDriverSeat(this)"
            title="Kursi {{ $seatNumber }}: {{ $pName }} (Belum Naik)">
        <span class="text-xs font-extrabold">{{ $seatNumber }}</span>
        <span class="text-[9px] mt-0.5 tracking-tight font-semibold uppercase text-amber-700">Terisi</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, {{ $pName }}, belum naik kabin</span>
    </button>
@else
    {{-- 3. Kursi Kosong - Putih dengan Border Hijau Brand identik Akun User --}}
    <button type="button"
            class="seat-btn-driver w-12 h-12 rounded-lg border-2 border-brand-600 bg-white text-brand-900 flex flex-col items-center justify-center leading-none select-none transition-transform hover:scale-105 font-bold hover:bg-slate-50"
            data-seat="{{ json_encode($seatData) }}"
            onclick="selectDriverSeat(this)"
            title="Kursi {{ $seatNumber }}: Kosong / Tersedia">
        <span class="text-xs font-bold text-slate-700">{{ $seatNumber }}</span>
        <span class="text-[9px] mt-0.5 tracking-tight font-medium text-slate-400">Kosong</span>
        <span class="sr-only">Kursi {{ $seatNumber }}, kosong</span>
    </button>
@endif
