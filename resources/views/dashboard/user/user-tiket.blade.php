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
    $fieldClass = 'w-full min-h-12 rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 text-slate-900';
@endphp

@section('content')
<div class="space-y-8 max-w-4xl" x-data="{ tab: 'aktif' }">

    @include('dashboard.user.partials.flash')

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Tiket saya</h1>
            <p class="mt-1 text-slate-600">Tunjukkan QR di tiket ke petugas saat naik. Pembatalan bisa diajukan sampai 1 jam sebelum berangkat.</p>
        </div>
        <a href="{{ route('dashboard.user.search') }}" class="lp-btn lp-btn--green">Pesan tiket</a>
    </header>

    {{-- A finished trip without a rating: the form sits inline instead of popping up over the page --}}
    @if ($pendingReviewBooking)
        @php $reviewRoute = $pendingReviewBooking->schedule?->route; @endphp
        <details id="ulasan" class="rounded-xl border-2 border-amber-600 bg-amber-50" @if (request('action') === 'review') open @endif>
            <summary class="cursor-pointer list-none px-6 py-4">
                <span class="block font-bold text-amber-950">
                    Perjalanan {{ $reviewRoute?->origin?->name }} ke {{ $reviewRoute?->destination?->name }}
                    ({{ $dateOf($pendingReviewBooking->schedule?->departure_date)?->isoFormat('D MMM') }}) sudah selesai.
                </span>
                <span class="mt-1 inline-flex items-center min-h-11 font-semibold text-brand-800 underline underline-offset-2">Beri nilai perjalanan</span>
            </summary>

            <form action="{{ route('dashboard.user.tiket.review', $pendingReviewBooking) }}" method="POST" class="space-y-5 border-t border-amber-300 px-6 py-5">
                @csrf
                @foreach ([
                    'cleanliness_rating' => 'Kebersihan mobil dan kursi',
                    'driver_rating' => 'Sopir: sopan dan aman',
                    'punctuality_rating' => 'Berangkat tepat waktu',
                ] as $field => $question)
                    <fieldset>
                        <legend class="font-semibold text-slate-900">{{ $question }}</legend>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @for ($score = 1; $score <= 5; $score++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="{{ $field }}" value="{{ $score }}" class="peer sr-only" @checked($score === 5) required>
                                    <span class="flex w-11 h-11 items-center justify-center rounded-lg border-2 border-slate-300 bg-white font-bold text-slate-700 peer-checked:border-brand-800 peer-checked:bg-brand-800 peer-checked:text-white peer-focus-visible:outline-3 peer-focus-visible:outline-slate-900">{{ $score }}</span>
                                </label>
                            @endfor
                            <span class="text-sm text-slate-600">1 buruk, 5 sangat baik</span>
                        </div>
                    </fieldset>
                @endforeach

                <div class="space-y-1.5">
                    <label for="review-comment" class="block font-semibold text-slate-900">Cerita singkat <span class="font-normal text-slate-600">(boleh kosong)</span></label>
                    <textarea id="review-comment" name="comment" rows="3" maxlength="1000" class="w-full rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 py-3"></textarea>
                </div>

                <button type="submit" class="lp-btn lp-btn--green">Kirim nilai</button>
            </form>
        </details>
    @endif

    <div class="flex gap-1 border-b border-slate-300" role="tablist" aria-label="Kelompok tiket">
        @foreach ($tabs as $key => [$label, $count])
            <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    @click="tab = '{{ $key }}'"
                    :aria-selected="(tab === '{{ $key }}').toString()"
                    :class="tab === '{{ $key }}' ? 'border-brand-800 text-brand-900' : 'border-transparent text-slate-600 hover:text-brand-800'"
                    class="-mb-px inline-flex items-center gap-2 min-h-11 px-4 border-b-[3px] font-bold">
                {{ $label }}
                <span class="text-sm font-semibold text-slate-500">{{ $count }}</span>
            </button>
        @endforeach
    </div>

    {{-- Upcoming trips --}}
    <section id="panel-aktif" role="tabpanel" aria-labelledby="tab-aktif" x-show="tab === 'aktif'" class="space-y-6">
        @forelse ($activeBookings as $booking)
            @php
                $schedule = $booking->schedule;
                $tripRoute = $schedule?->route;
                $departure = $schedule?->departureCarbon()->locale('id');
                $isBoarded = $booking->ticket?->boarded_at !== null
                    || in_array(strtolower($booking->status), ['checked_in', 'boarded'], true)
                    || ($booking->passengers->isNotEmpty() && $booking->passengers->every(fn ($passenger) => $passenger->boarding_status === 'boarded'));
                $waMessage = 'Halo CS '.\App\Models\AppSetting::companyName().", saya butuh bantuan untuk tiket ini:\n"
                    .'Kode booking: '.$booking->booking_code."\n"
                    .'Rute: '.$tripRoute?->origin?->name.' ke '.$tripRoute?->destination?->name."\n"
                    .'Jadwal: '.$departure?->isoFormat('D MMM YYYY, HH:mm')." WIB\n\n"
                    .'Kendala: ';
                $waUrl = \App\Models\AppSetting::whatsappUrl($waMessage);
            @endphp

            <article id="booking-card-{{ $booking->id }}"
                     @class([
                         'rounded-2xl overflow-hidden border bg-white',
                         'border-brand-700' => $highlightId !== (string) $booking->id,
                         'border-brand-800 outline-4 outline-offset-2 outline-amber-400' => $highlightId === (string) $booking->id,
                     ])
                     @if ($highlightId === (string) $booking->id) x-init="$el.scrollIntoView({ block: 'center' })" @endif>
                <div class="grid md:grid-cols-[minmax(0,1fr)_240px]">
                    <div class="p-6 space-y-5">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">No. tiket {{ $booking->ticket?->ticket_number ?? '-' }}</p>
                            <h2 class="mt-1 flex flex-wrap items-baseline gap-x-3 text-2xl font-extrabold text-brand-900">
                                <span>{{ $tripRoute?->origin?->name }}</span>
                                <span class="text-base font-semibold text-brand-600">ke</span>
                                <span>{{ $tripRoute?->destination?->name }}</span>
                            </h2>
                        </div>

                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-3">
                            <div>
                                <dt class="text-sm text-slate-500">Tanggal</dt>
                                <dd class="font-bold text-slate-900">{{ $departure?->isoFormat('D MMM YYYY') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">Berangkat</dt>
                                <dd class="font-bold text-slate-900">{{ $departure?->format('H:i') }} WIB</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">Kursi</dt>
                                <dd class="font-bold text-slate-900">{{ $booking->passengerSeatNumbers() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">Dibayar</dt>
                                <dd class="font-bold text-slate-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <p class="text-slate-600">
                            @if ($schedule?->vehicle)
                                {{ $schedule->vehicle->type ?: $schedule->vehicle->name }}, pelat {{ $schedule->vehicle->plate_number }}.
                            @else
                                Kendaraan diumumkan di hari keberangkatan.
                            @endif
                            Sopir: {{ $schedule?->driver?->user?->name ?? 'belum ditentukan' }}.
                        </p>

                        @if ($isBoarded)
                            <p class="font-bold text-brand-800">
                                Sudah naik{{ $booking->ticket?->boarded_at ? ', divalidasi petugas jam '.\Illuminate\Support\Carbon::parse($booking->ticket->boarded_at)->format('H:i') : '' }}.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col items-center justify-center gap-3 p-6 bg-brand-800 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/50">
                        <div class="w-36 h-36 rounded-lg bg-white p-2.5" data-qr="{{ $booking->ticket?->qr_code ?: 'QR-'.$booking->booking_code }}" role="img" aria-label="QR e-ticket {{ $booking->booking_code }}"></div>
                        <p class="text-center">
                            <span class="block text-sm text-brand-100">Kode booking</span>
                            <span class="text-xl font-extrabold tracking-wide">{{ $booking->booking_code }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-3">
                    <p class="text-sm text-slate-600">
                        @if ($booking->canRefund())
                            Bisa dibatalkan sampai {{ $departure?->copy()->subHour()->format('H:i') }} WIB ({{ $booking->hoursUntilDeparture() }} jam lagi).
                        @else
                            Sudah lewat batas pembatalan (1 jam sebelum berangkat).
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Bantuan CS di WhatsApp</a>
                        @endif
                        <a href="{{ route('dashboard.user.booking.sukses', $booking) }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Buka e-ticket</a>
                    </div>
                </div>

                @if ($booking->canRefund())
                    <details class="border-t border-slate-200">
                        <summary class="cursor-pointer list-none flex items-center min-h-11 px-6 py-3 font-semibold text-rose-800">Batalkan dan ajukan refund</summary>
                        <form action="{{ route('dashboard.user.tiket.refund', $booking) }}" method="POST" class="space-y-4 border-t border-slate-200 px-6 py-5">
                            @csrf
                            <dl class="max-w-sm space-y-1">
                                <div class="flex justify-between gap-4 text-slate-600"><dt>Dibayar</dt><dd>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd></div>
                                <div class="flex justify-between gap-4 text-slate-600"><dt>Biaya pembatalan 25%</dt><dd>- Rp {{ number_format($booking->total_price * 0.25, 0, ',', '.') }}</dd></div>
                                <div class="flex justify-between gap-4 font-extrabold text-slate-900"><dt>Dana kembali</dt><dd>Rp {{ number_format($booking->total_price * 0.75, 0, ',', '.') }}</dd></div>
                            </dl>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="reason-{{ $booking->id }}" class="block font-semibold text-slate-900">Alasan</label>
                                    <select id="reason-{{ $booking->id }}" name="reason" required class="{{ $fieldClass }}">
                                        <option value="">Pilih alasan</option>
                                        <option>Rencana perjalanan berubah</option>
                                        <option>Sakit atau keadaan darurat</option>
                                        <option>Salah pilih tanggal atau jam</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="bank-{{ $booking->id }}" class="block font-semibold text-slate-900">Bank atau e-wallet</label>
                                    <select id="bank-{{ $booking->id }}" name="bank_name" required class="{{ $fieldClass }}">
                                        @foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'BSI', 'GoPay', 'OVO', 'DANA'] as $bank)
                                            <option>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="account-{{ $booking->id }}" class="block font-semibold text-slate-900">Nomor rekening atau HP</label>
                                    <input id="account-{{ $booking->id }}" type="text" name="account_number" required maxlength="50" inputmode="numeric" class="{{ $fieldClass }}">
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="holder-{{ $booking->id }}" class="block font-semibold text-slate-900">Nama pemilik rekening</label>
                                    <input id="holder-{{ $booking->id }}" type="text" name="account_holder" required maxlength="100" class="{{ $fieldClass }}">
                                </div>
                            </div>

                            <p class="text-slate-700">Setelah dikirim, tiket langsung batal dan kursinya dibuka lagi untuk penumpang lain. Ini tidak bisa diurungkan.</p>
                            <button type="submit" class="inline-flex items-center min-h-11 px-5 rounded-lg bg-rose-800 font-bold text-white hover:bg-rose-900">Batalkan tiket ini</button>
                        </form>
                    </details>
                @endif

                @if (! $isBoarded && ! app()->isProduction() && $booking->ticket)
                    {{-- TODO: developer shortcut for testing boarding; staff do this from the ticket validator --}}
                    <form action="{{ route('dashboard.user.tiket.check-in', $booking->ticket) }}" method="POST" class="border-t border-dashed border-slate-300 px-6 py-3">
                        @csrf
                        <button type="submit" class="inline-flex items-center min-h-11 text-sm font-semibold text-slate-600 underline underline-offset-2">Mode uji: tandai sudah naik</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8">
                <p class="font-bold text-slate-900">Belum ada perjalanan yang akan datang.</p>
                <p class="mt-1 text-slate-600">Tiket yang sudah dibayar muncul di sini, lengkap dengan QR untuk naik.</p>
                <a href="{{ route('dashboard.user.search') }}" class="mt-4 inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Cari jadwal</a>
            </div>
        @endforelse
    </section>

    {{-- Finished and cancelled trips --}}
    <section id="panel-riwayat" role="tabpanel" aria-labelledby="tab-riwayat" x-show="tab === 'riwayat'" x-cloak>
        @if ($historyBookings->isEmpty())
            <p class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8 text-slate-700">Belum ada perjalanan yang selesai atau dibatalkan.</p>
        @else
            <ul class="divide-y divide-slate-200 border-y border-slate-200">
                @foreach ($historyBookings as $history)
                    @php $isCompleted = strtolower($history->status) === 'completed'; @endphp
                    <li class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2 py-4">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900">
                                {{ $history->schedule?->route?->origin?->name ?? '-' }} <span class="font-normal text-slate-500">ke</span> {{ $history->schedule?->route?->destination?->name ?? '-' }}
                            </p>
                            <p class="text-slate-600">
                                {{ $dateOf($history->schedule?->departure_date)?->isoFormat('D MMM YYYY') ?? '-' }}, kursi {{ $history->passengerSeatNumbers() }}, kode {{ $history->booking_code }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-slate-900">Rp {{ number_format($history->total_price, 0, ',', '.') }}</span>
                            @if ($isCompleted && $history->review)
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-sm font-semibold text-slate-700">Selesai, nilai {{ number_format((float) $history->review->overall_rating, 1, ',', '.') }}</span>
                            @elseif ($isCompleted && $pendingReviewBooking?->id === $history->id)
                                <a href="#ulasan" @click="document.getElementById('ulasan').open = true" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Beri nilai</a>
                            @elseif ($isCompleted)
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-sm font-semibold text-slate-700">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 rounded-md bg-rose-50 text-sm font-semibold text-rose-800">Batal</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Refund requests --}}
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
            <article class="rounded-xl border border-slate-300 bg-white p-6 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-slate-900">
                            {{ $refund->booking?->schedule?->route?->origin?->name }} ke {{ $refund->booking?->schedule?->route?->destination?->name }}
                        </h2>
                        <p class="text-slate-600">Kode {{ $refund->booking?->booking_code }}, diajukan {{ $refund->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    </div>
                    <p class="text-right">
                        <span class="block text-xl font-extrabold text-slate-900">Rp {{ number_format($refund->amount, 0, ',', '.') }}</span>
                        <span class="text-sm text-slate-600">setelah biaya Rp {{ number_format($refund->cancellation_fee, 0, ',', '.') }}</span>
                    </p>
                </div>

                @if ($isRejected)
                    <p class="font-bold text-rose-800">Pengajuan ditolak admin.@if ($refund->admin_notes ?? null) {{ $refund->admin_notes }}@endif</p>
                @else
                    <ol class="grid grid-cols-3 gap-2 text-sm">
                        @foreach ([1 => 'Diajukan', 2 => 'Diperiksa admin', 3 => 'Dana dikirim'] as $stepNumber => $stepLabel)
                            <li @class([
                                'border-t-4 pt-2 font-semibold',
                                'border-brand-700 text-brand-900' => $step >= $stepNumber,
                                'border-slate-300 text-slate-500' => $step < $stepNumber,
                            ])>
                                {{ $stepLabel }}
                                @if ($step === $stepNumber)
                                    <span class="sr-only">(tahap sekarang)</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif

                <p class="text-slate-600">Ke {{ $refund->bank_name }} {{ $refund->account_number }} a.n. {{ $refund->account_holder }}. Alasan: {{ $refund->reason }}.</p>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8 text-slate-700">Belum ada pengajuan refund.</p>
        @endforelse
    </section>
</div>

@include('dashboard.user.partials.qr-script')
@endsection
