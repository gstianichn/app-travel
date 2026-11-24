@extends('layouts.dashboard')

@section('page-title', 'Tiket saya')

@php
    $dateOf = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->locale('id') : null;
    $highlightId = (string) request('booking_id');
    $tabs = [
        'aktif' => ['Akan berangkat', $activeBookings->count()],
        'riwayat' => ['Riwayat', $historyBookings->count()],
        'refund' => ['Refund', $refunds->count()],
    ];
    $fieldClass = 'w-full min-h-11 rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700/15';
@endphp

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{ tab: 'aktif' }">

    @include('dashboard.user.partials.flash')

    {{-- HEADER --}}
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-brand-900">Tiket saya</h1>
            <p class="mt-1 text-sm text-slate-600">Tunjukkan QR di tiket ke petugas saat naik. Pembatalan bisa diajukan sampai 1 jam sebelum berangkat.</p>
        </div>
        <a href="{{ route('dashboard.user.search') }}" class="lp-btn lp-btn--green text-sm">Pesan tiket</a>
    </header>

    {{-- BANNER BERI NILAI PERJALANAN --}}
    @if ($pendingReviewBooking)
        @php $reviewRoute = $pendingReviewBooking->schedule?->route; @endphp
        <details id="ulasan" class="rounded-2xl border border-amber-300 bg-amber-50/70 overflow-hidden" @if (request('action') === 'review') open @endif>
            <summary class="cursor-pointer list-none flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                <div>
                    <span class="block text-sm font-bold text-amber-950">
                        Perjalanan {{ $reviewRoute?->origin?->name }} ke {{ $reviewRoute?->destination?->name }}
                        ({{ $dateOf($pendingReviewBooking->schedule?->departure_date)?->isoFormat('D MMM') }}) sudah selesai.
                    </span>
                    <span class="block text-xs text-amber-800 mt-0.5">Bantu kami meningkatkan layanan dengan memberi penilaian singkat.</span>
                </div>
                <span class="inline-flex items-center px-3.5 py-2 rounded-lg bg-amber-700 text-white text-xs font-bold hover:bg-amber-800 transition-colors">
                    Beri nilai perjalanan
                </span>
            </summary>

            <form action="{{ route('dashboard.user.tiket.review', $pendingReviewBooking) }}" method="POST" class="space-y-5 border-t border-amber-200 bg-white px-6 py-5">
                @csrf
                @foreach ([
                    'cleanliness_rating' => 'Kebersihan mobil dan kursi',
                    'driver_rating' => 'Sopir: sopan dan aman',
                    'punctuality_rating' => 'Berangkat tepat waktu',
                ] as $field => $question)
                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-900">{{ $question }}</legend>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @for ($score = 1; $score <= 5; $score++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="{{ $field }}" value="{{ $score }}" class="peer sr-only" @checked($score === 5) required>
                                    <span class="flex w-10 h-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-sm font-bold text-slate-700 peer-checked:border-brand-800 peer-checked:bg-brand-800 peer-checked:text-white">{{ $score }}</span>
                                </label>
                            @endfor
                            <span class="text-xs text-slate-500">1 buruk, 5 sangat baik</span>
                        </div>
                    </fieldset>
                @endforeach

                <div class="space-y-1.5">
                    <label for="review-comment" class="block text-sm font-semibold text-slate-900">Cerita singkat <span class="font-normal text-slate-500">(boleh kosong)</span></label>
                    <textarea id="review-comment" name="comment" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700/15"></textarea>
                </div>

                <button type="submit" class="lp-btn lp-btn--green text-sm">Kirim nilai</button>
            </form>
        </details>
    @endif

    {{-- TAB NAVIGASI --}}
    <div class="flex gap-1 border-b border-slate-200" role="tablist" aria-label="Kelompok tiket">
        @foreach ($tabs as $key => [$label, $count])
            <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    @click="tab = '{{ $key }}'"
                    :aria-selected="(tab === '{{ $key }}').toString()"
                    :class="tab === '{{ $key }}' ? 'border-brand-800 text-brand-900' : 'border-transparent text-slate-500 hover:text-brand-800'"
                    class="-mb-px inline-flex items-center gap-2 min-h-11 px-4 border-b-2 text-sm font-bold transition-colors cursor-pointer">
                <span>{{ $label }}</span>
                <span class="text-xs font-semibold text-slate-400">{{ $count }}</span>
            </button>
        @endforeach
    </div>

    {{-- PANEL 1: AKAN BERANGKAT --}}
    <section id="panel-aktif" role="tabpanel" aria-labelledby="tab-aktif" x-show="tab === 'aktif'" class="space-y-5">
        @forelse ($activeBookings as $booking)
            @php
                $schedule = $booking->schedule;
                $tripRoute = $schedule?->route;
                $departure = $schedule?->departureCarbon()->locale('id');
                $boardingOpens = $departure?->copy()->subMinutes(15);
                $isBoarded = $booking->ticket?->boarded_at !== null
                    || in_array(strtolower($booking->status), ['checked_in', 'boarded'], true)
                    || ($booking->passengers->isNotEmpty() && $booking->passengers->every(fn ($passenger) => $passenger->boarding_status === 'boarded'));
                $waMessage = 'Halo CS '.\App\Models\AppSetting::companyName().", saya butuh bantuan untuk tiket ini:\n"
                    .'Kode booking: '.$booking->booking_code."\n"
                    .'Rute: '.$tripRoute?->origin?->name.' ke '.$tripRoute?->destination?->name."\n"
                    .'Jadwal: '.$departure?->isoFormat('D MMM YYYY, HH:mm')." WIB\n\n"
                    .'Kendala: ';
                $waUrl = \App\Models\AppSetting::whatsappUrl($waMessage);
                $qrValue = $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code;
                $baseTicketNo = $booking->ticket?->ticket_number ?? ('TKT-'.$booking->booking_code);
                $passengersList = $booking->passengers->values();
                $bookingTicketCards = $passengersList->isNotEmpty()
                    ? $passengersList->map(function ($passenger, $idx) use ($passengersList, $baseTicketNo, $tripRoute, $departure, $schedule, $booking, $boardingOpens, $qrValue) {
                        $seatNo = $passenger->seat?->seat_number ?? '-';
                        $totalCount = $passengersList->count();
                        $ticketNo = $totalCount > 1 ? "{$baseTicketNo}-".($idx + 1) : $baseTicketNo;
                        $boardingNote = 'Boarding dibuka '.($boardingOpens?->format('H:i') ?? '-').' WIB di '.($tripRoute?->origin?->name ?? '-')
                            .($tripRoute?->origin?->address ? ', '.$tripRoute->origin->address : '')
                            .'. Dibayar lewat '.($booking->payment?->payment_method ?? 'Midtrans').'.';

                        return [
                            'ticketNumber' => $ticketNo,
                            'ticketIndexLabel' => $totalCount > 1 ? 'Tiket '.($idx + 1).' dari '.$totalCount : '',
                            'origin' => $tripRoute?->origin?->name ?? '-',
                            'destination' => $tripRoute?->destination?->name ?? '-',
                            'date' => $departure?->isoFormat('D MMM YYYY') ?? '-',
                            'time' => ($departure?->format('H:i') ?? '-').' WIB',
                            'seatNumber' => (string) $seatNo,
                            'vehiclePlate' => $schedule?->vehicle?->plate_number ?? 'Diumumkan hari-H',
                            'passengerName' => $passenger->name ?: $booking->customerName(),
                            'boardingNote' => $boardingNote,
                            'qrValue' => $qrValue,
                            'bookingCode' => $booking->booking_code,
                        ];
                    })
                    : collect([[
                        'ticketNumber' => $baseTicketNo,
                        'ticketIndexLabel' => '',
                        'origin' => $tripRoute?->origin?->name ?? '-',
                        'destination' => $tripRoute?->destination?->name ?? '-',
                        'date' => $departure?->isoFormat('D MMM YYYY') ?? '-',
                        'time' => ($departure?->format('H:i') ?? '-').' WIB',
                        'seatNumber' => $booking->passengerSeatNumbers(),
                        'vehiclePlate' => $schedule?->vehicle?->plate_number ?? 'Diumumkan hari-H',
                        'passengerName' => $booking->customerName(),
                        'boardingNote' => 'Boarding dibuka '.($boardingOpens?->format('H:i') ?? '-').' WIB di '.($tripRoute?->origin?->name ?? '-').'.',
                        'qrValue' => $qrValue,
                        'bookingCode' => $booking->booking_code,
                    ]]);
            @endphp

            <article id="booking-card-{{ $booking->id }}"
                     @class([
                         'rounded-2xl overflow-hidden border bg-white',
                         'border-brand-700/40' => $highlightId !== (string) $booking->id,
                         'border-brand-800 outline-4 outline-offset-2 outline-amber-400' => $highlightId === (string) $booking->id,
                     ])
                     @if ($highlightId === (string) $booking->id) x-init="$el.scrollIntoView({ block: 'center' })" @endif>
                <div class="divide-y divide-slate-200">
                    @foreach ($bookingTicketCards as $ticketCard)
                        <div class="grid md:grid-cols-[minmax(0,1fr)_240px]">
                            <div class="p-6 space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-semibold text-slate-500">No. tiket {{ $ticketCard['ticketNumber'] }}</p>
                                    <div class="flex items-center gap-2">
                                        @if ($ticketCard['ticketIndexLabel'])
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-900">
                                                {{ $ticketCard['ticketIndexLabel'] }}
                                            </span>
                                        @endif
                                        @if ($loop->first && $isBoarded)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Sudah naik</span>
                                        @elseif ($loop->first && strtolower($booking->status) === 'refund_pending')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu ACC Refund</span>
                                        @endif
                                    </div>
                                </div>

                                <h2 class="flex flex-wrap items-baseline gap-x-2 text-xl sm:text-2xl font-extrabold text-brand-900">
                                    <span>{{ $ticketCard['origin'] }}</span>
                                    <span class="text-sm font-semibold text-brand-600">ke</span>
                                    <span>{{ $ticketCard['destination'] }}</span>
                                </h2>

                                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-5 gap-y-3 pt-1">
                                    <div>
                                        <dt class="text-xs text-slate-500">Penumpang</dt>
                                        <dd class="text-sm font-bold text-slate-900 truncate">{{ $ticketCard['passengerName'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Tanggal</dt>
                                        <dd class="text-sm font-bold text-slate-900">{{ $ticketCard['date'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Berangkat</dt>
                                        <dd class="text-sm font-bold text-slate-900">{{ $ticketCard['time'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Kursi</dt>
                                        <dd class="text-sm font-bold text-brand-900">Kursi {{ $ticketCard['seatNumber'] }}</dd>
                                    </div>
                                </dl>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                    <p class="text-xs sm:text-sm text-slate-600">
                                        @if ($schedule?->vehicle)
                                            {{ $schedule->vehicle->type ?: $schedule->vehicle->name }} ({{ $schedule->vehicle->plate_number }})
                                        @else
                                            Kendaraan diumumkan di hari keberangkatan
                                        @endif
                                        &bull; Sopir: {{ $schedule?->driver?->user?->name ?? 'belum ditentukan' }}
                                    </p>

                                    <button
                                        type="button"
                                        onclick='window.downloadTicketPng(@json($ticketCard))'
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-brand-700 bg-white text-brand-800 text-xs font-bold hover:bg-brand-50 transition-colors cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                        <span>Download tiket</span>
                                    </button>
                                </div>

                                @if ($loop->first && $isBoarded)
                                    <p class="text-xs font-semibold text-brand-800">
                                        Sudah naik{{ $booking->ticket?->boarded_at ? ', divalidasi petugas jam '.\Illuminate\Support\Carbon::parse($booking->ticket->boarded_at)->format('H:i').' WIB' : '' }}.
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-col items-center justify-center gap-3 p-6 bg-brand-800 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/40">
                                <div class="w-36 h-36 rounded-lg bg-white p-2.5" data-qr="{{ $ticketCard['qrValue'] }}" role="img" aria-label="QR e-ticket {{ $booking->booking_code }}"></div>
                                <p class="text-center">
                                    <span class="block text-xs text-brand-100">Kode booking</span>
                                    <span class="text-lg font-extrabold tracking-wide">{{ $booking->booking_code }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        <strong class="text-slate-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong> &bull;
                        @if (strtolower($booking->status) === 'refund_pending')
                            <span class="font-semibold text-amber-700">Pengajuan refund sedang diperiksa admin.</span>
                        @elseif ($booking->canRefund())
                            Bisa dibatalkan sampai {{ $departure?->copy()->subHour()->format('H:i') }} WIB ({{ $booking->hoursUntilDeparture() }} jam lagi).
                        @else
                            Sudah lewat batas pembatalan (1 jam sebelum berangkat).
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        @if ($bookingTicketCards->count() > 1)
                            <button type="button" onclick='window.downloadAllTicketsPng(@json($bookingTicketCards->values()))' class="font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-2 cursor-pointer">
                                Download semua ({{ $bookingTicketCards->count() }})
                            </button>
                        @endif
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-2">Bantuan CS di WhatsApp</a>
                        @endif
                        <a href="{{ route('dashboard.user.booking.sukses', $booking) }}" class="font-bold text-brand-800 hover:text-brand-950 underline underline-offset-2">Buka e-ticket</a>
                    </div>
                </div>

                @if ($booking->canRefund())
                    <details class="border-t border-slate-200">
                        <summary class="cursor-pointer list-none flex items-center justify-between min-h-11 px-6 py-3 text-sm font-semibold text-rose-800 hover:bg-rose-50/40">
                            <span>Ajukan refund (Perlu ACC Admin terlebih dahulu)</span>
                            <span aria-hidden="true">▾</span>
                        </summary>
                        <form action="{{ route('dashboard.user.tiket.refund', $booking) }}" method="POST" class="space-y-4 border-t border-slate-200 bg-white px-6 py-5">
                            @csrf
                            <p class="rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-sm text-amber-900">
                                <strong>Alur Persetujuan Refund:</strong> Pengajuan refund Anda akan diperiksa untuk mendapatkan <strong>ACC dari Admin</strong> terlebih dahulu. Selama menunggu ACC, kursi Anda tetap aman dan setelah di-ACC dana otomatis dikembalikan ke rekening/e-wallet tujuan Anda.
                            </p>
                            <dl class="max-w-sm space-y-1 text-sm">
                                <div class="flex justify-between gap-4 text-slate-600"><dt>Dibayar</dt><dd>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd></div>
                                <div class="flex justify-between gap-4 text-slate-600"><dt>Biaya pembatalan 25%</dt><dd>- Rp {{ number_format($booking->total_price * 0.25, 0, ',', '.') }}</dd></div>
                                <div class="flex justify-between gap-4 font-extrabold text-slate-900"><dt>Dana kembali</dt><dd>Rp {{ number_format($booking->total_price * 0.75, 0, ',', '.') }}</dd></div>
                            </dl>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="reason-{{ $booking->id }}" class="block text-sm font-semibold text-slate-900">Alasan</label>
                                    <select id="reason-{{ $booking->id }}" name="reason" required class="{{ $fieldClass }}">
                                        <option value="">Pilih alasan</option>
                                        <option>Rencana perjalanan berubah</option>
                                        <option>Sakit atau keadaan darurat</option>
                                        <option>Salah pilih tanggal atau jam</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="bank-{{ $booking->id }}" class="block text-sm font-semibold text-slate-900">Bank atau e-wallet</label>
                                    <select id="bank-{{ $booking->id }}" name="bank_name" required class="{{ $fieldClass }}">
                                        @foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'BSI', 'GoPay', 'OVO', 'DANA'] as $bank)
                                            <option>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="account-{{ $booking->id }}" class="block text-sm font-semibold text-slate-900">Nomor rekening atau HP (angka saja)</label>
                                    <input id="account-{{ $booking->id }}" type="text" name="account_number" required maxlength="50" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Hanya angka, contoh: 1234567890" class="{{ $fieldClass }}">
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="holder-{{ $booking->id }}" class="block text-sm font-semibold text-slate-900">Nama pemilik rekening</label>
                                    <input id="holder-{{ $booking->id }}" type="text" name="account_holder" required maxlength="100" class="{{ $fieldClass }}">
                                </div>
                            </div>

                            <button type="submit" class="inline-flex items-center min-h-11 px-5 rounded-lg bg-rose-800 text-sm font-bold text-white hover:bg-rose-900 cursor-pointer">Kirim Pengajuan Refund</button>
                        </form>
                    </details>
                @endif

                @if (! $isBoarded && ! app()->isProduction() && $booking->ticket)
                    <form action="{{ route('dashboard.user.tiket.check-in', $booking->ticket) }}" method="POST" class="border-t border-dashed border-slate-200 px-6 py-2.5">
                        @csrf
                        <button type="submit" class="inline-flex items-center min-h-9 text-xs font-semibold text-slate-500 underline underline-offset-2 cursor-pointer">Mode uji: tandai sudah naik</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                <p class="font-bold text-slate-900">Belum ada perjalanan yang akan datang.</p>
                <p class="mt-1 text-sm text-slate-600">Tiket yang sudah dibayar muncul di sini, lengkap dengan QR untuk naik.</p>
                <a href="{{ route('dashboard.user.search') }}" class="mt-4 inline-flex items-center font-semibold text-brand-700 underline underline-offset-2 text-sm">Cari jadwal</a>
            </div>
        @endforelse
    </section>

    {{-- PANEL 2: RIWAYAT --}}
    <section id="panel-riwayat" role="tabpanel" aria-labelledby="tab-riwayat" x-show="tab === 'riwayat'" x-cloak>
        @if ($historyBookings->isEmpty())
            <p class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-8 text-sm text-slate-600 text-center">Belum ada perjalanan yang selesai atau dibatalkan.</p>
        @else
            <ul class="space-y-3">
                @foreach ($historyBookings as $history)
                    @php $isCompleted = strtolower($history->status) === 'completed'; @endphp
                    <li class="rounded-xl border border-slate-200 bg-white px-5 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900">
                                {{ $history->schedule?->route?->origin?->name ?? '-' }} <span class="font-normal text-slate-500">ke</span> {{ $history->schedule?->route?->destination?->name ?? '-' }}
                            </p>
                            <p class="text-sm text-slate-600 mt-0.5">
                                {{ $dateOf($history->schedule?->departure_date)?->isoFormat('D MMM YYYY') ?? '-' }} &bull; Kursi {{ $history->passengerSeatNumbers() }} &bull; {{ $history->booking_code }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-900">Rp {{ number_format($history->total_price, 0, ',', '.') }}</span>
                            @if ($isCompleted && $history->review)
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-700">Selesai, nilai {{ number_format((float) $history->review->overall_rating, 1, ',', '.') }}</span>
                            @elseif ($isCompleted && $pendingReviewBooking?->id === $history->id)
                                <a href="#ulasan" @click="document.getElementById('ulasan').open = true" class="text-sm font-semibold text-brand-700 underline underline-offset-2">Beri nilai</a>
                            @elseif ($isCompleted)
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-700">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-xs font-semibold text-rose-700">Batal</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- PANEL 3: REFUND --}}
    <section id="panel-refund" role="tabpanel" aria-labelledby="tab-refund" x-show="tab === 'refund'" x-cloak class="space-y-4">
        @forelse ($refunds as $refund)
            @php
                $refundStatus = strtolower($refund->status);
                $step = match (true) {
                    in_array($refundStatus, ['disetujui', 'approved', 'dana ditransfer', 'selesai'], true) => 3,
                    in_array($refundStatus, ['diverifikasi', 'diproses', 'pending_approval'], true) => 2,
                    default => 1,
                };
                $isRejected = in_array($refundStatus, ['ditolak', 'rejected'], true);
            @endphp
            <article class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-900">
                            {{ $refund->booking?->schedule?->route?->origin?->name }} ke {{ $refund->booking?->schedule?->route?->destination?->name }}
                        </h2>
                        <p class="text-sm text-slate-600 mt-0.5">Kode {{ $refund->booking?->booking_code }}, diajukan {{ $refund->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    </div>
                    <p class="text-left sm:text-right">
                        <span class="block text-lg font-extrabold text-slate-900">Rp {{ number_format($refund->amount, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500">setelah biaya Rp {{ number_format($refund->cancellation_fee, 0, ',', '.') }}</span>
                    </p>
                </div>

                @if ($isRejected)
                    <p class="text-sm font-bold text-rose-800">Pengajuan ditolak admin.@if ($refund->admin_note ?? $refund->admin_notes ?? null) Alasan: {{ $refund->admin_note ?? $refund->admin_notes }}@endif</p>
                @else
                    <ol class="grid grid-cols-3 gap-2 text-xs sm:text-sm">
                        @foreach ([1 => 'Diajukan', 2 => 'Diperiksa admin', 3 => 'Dana dikirim'] as $stepNumber => $stepLabel)
                            <li @class([
                                'border-t-4 pt-2 font-semibold',
                                'border-brand-700 text-brand-900' => $step >= $stepNumber,
                                'border-slate-200 text-slate-400' => $step < $stepNumber,
                            ])>
                                {{ $stepLabel }}
                                @if ($step === $stepNumber)
                                    <span class="sr-only">(tahap sekarang)</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif

                <p class="text-xs sm:text-sm text-slate-600">Ke {{ $refund->bank_name }} {{ $refund->account_number }} a.n. {{ $refund->account_holder }}. Alasan: {{ $refund->reason }}.</p>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-8 text-sm text-slate-600 text-center">Belum ada pengajuan refund.</p>
        @endforelse
    </section>
</div>

@include('dashboard.user.partials.qr-script')
@include('dashboard.user.partials.ticket-download-script')
@endsection
