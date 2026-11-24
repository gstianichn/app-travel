@extends('layouts.dashboard')

@section('page-title', 'Beranda')

@php
    $firstName = \Illuminate\Support\Str::before(auth()->user()->name ?? 'Penumpang', ' ');
    $dateOf = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->locale('id') : null;
    $nextSchedule = $activeBooking?->schedule;
    $pendingBooking = $recentBookings->first(
        fn ($booking) => strtolower($booking->status) === 'pending' && $booking->expired_at && $booking->expired_at->isFuture()
    );
@endphp

@section('content')
<div class="space-y-10">

    @if (session('success'))
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 px-5 py-4" role="status">
            <p class="font-semibold text-emerald-950">{{ session('success') }}</p>
            @if (auth()->user()?->email)
                <div class="flex flex-wrap items-center gap-2">
                    <a href="https://mail.google.com/mail/u/?authuser={{ urlencode(auth()->user()->email) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center min-h-10 px-3.5 rounded-lg bg-emerald-700 text-white text-sm font-bold hover:bg-emerald-800">
                        Buka Gmail ({{ auth()->user()->email }})
                    </a>
                    <a href="{{ route('dashboard.user.user-notifikasi') }}"
                       class="inline-flex items-center min-h-10 px-3.5 rounded-lg border border-emerald-700 bg-white text-emerald-800 text-sm font-bold hover:bg-emerald-100">
                        Lihat Notifikasi Verifikasi
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- SAPAAN & RINGKASAN SINGKAT --}}
    <section aria-labelledby="sapaan" class="flex flex-col md:flex-row md:items-end justify-between gap-5">
        <div>
            <h1 id="sapaan" class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Halo, {{ $firstName }}.</h1>
            <p class="mt-1.5 text-base sm:text-lg text-slate-600">
                @if ($nextSchedule)
                    Perjalanan berikutnya <span class="font-semibold text-slate-800">{{ $dateOf($nextSchedule->departure_date)->isoFormat('dddd, D MMMM') }}</span>, jam <span class="font-semibold text-brand-800">{{ $nextSchedule->departureCarbon()->format('H:i') }} WIB</span>.
                @else
                    Belum ada perjalanan terjadwal. Cari jadwal di bawah untuk memesan.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('dashboard.user.user-tiket') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 hover:border-brand-600 transition-colors">
                <span class="block text-xs font-semibold text-slate-500">Tiket aktif</span>
                <span class="block text-lg font-extrabold text-brand-900 leading-tight mt-0.5">{{ $activeTicketsCount }}</span>
            </a>
            <a href="{{ route('dashboard.user.user-tiket') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 hover:border-brand-600 transition-colors">
                <span class="block text-xs font-semibold text-slate-500">Selesai</span>
                <span class="block text-lg font-extrabold text-slate-900 leading-tight mt-0.5">{{ $completedTripsCount }}</span>
            </a>
            @if ($unreadNotifCount > 0)
                <a href="{{ route('dashboard.user.user-notifikasi') }}" class="rounded-xl border border-brand-200 bg-brand-50/70 px-4 py-2.5 hover:border-brand-600 transition-colors">
                    <span class="block text-xs font-semibold text-brand-800">Notifikasi</span>
                    <span class="block text-lg font-extrabold text-brand-900 leading-tight mt-0.5">{{ $unreadNotifCount }} baru</span>
                </a>
            @endif
        </div>
    </section>

    @if ($pendingPaymentsCount > 0)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-300 bg-amber-50/80 px-5 py-4" role="status">
            <p class="text-sm sm:text-base text-amber-950">
                <b>{{ $pendingPaymentsCount }} pembayaran belum selesai.</b>
                Kursinya hanya ditahan sampai batas waktu bayar.
            </p>
            <a href="{{ $pendingBooking ? route('dashboard.user.booking.pembayaran', $pendingBooking) : route('dashboard.user.user-tiket') }}"
               class="inline-flex items-center min-h-10 px-4 rounded-lg bg-amber-800 text-white text-sm font-bold hover:bg-amber-900 transition-colors">
                Lanjutkan pembayaran
            </a>
        </div>
    @endif

    {{-- PERJALANAN BERIKUTNYA --}}
    @if ($activeBooking && $nextSchedule)
        <section aria-labelledby="perjalanan-berikutnya" class="grid md:grid-cols-[1fr_256px] rounded-2xl overflow-hidden border border-brand-700/40 bg-white shadow-xs">
            <div class="p-6 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="perjalanan-berikutnya" class="text-xs font-bold uppercase tracking-wider text-slate-500">Perjalanan berikutnya</h2>
                    @if ($nextSchedule->vehicle)
                        <span class="text-xs font-semibold text-slate-500">{{ $nextSchedule->vehicle->type ?: $nextSchedule->vehicle->name }} &bull; {{ $nextSchedule->vehicle->plate_number }}</span>
                    @endif
                </div>
                <p class="mt-2.5 flex flex-wrap items-baseline gap-x-2.5 text-2xl sm:text-3xl font-extrabold text-brand-900">
                    <span>{{ $nextSchedule->route?->origin?->city ?: $nextSchedule->route?->origin?->name }}</span>
                    <span class="text-base font-semibold text-brand-600">ke</span>
                    <span>{{ $nextSchedule->route?->destination?->city ?: $nextSchedule->route?->destination?->name }}</span>
                </p>
                <dl class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-x-5 gap-y-3 pt-4 border-t border-slate-100">
                    <div>
                        <dt class="text-xs text-slate-500">Tanggal</dt>
                        <dd class="text-sm font-bold text-slate-900 mt-0.5">{{ $dateOf($nextSchedule->departure_date)->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Berangkat</dt>
                        <dd class="text-sm font-bold text-brand-800 mt-0.5">{{ $nextSchedule->departureCarbon()->format('H:i') }} WIB</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Kursi</dt>
                        <dd class="text-sm font-bold text-slate-900 mt-0.5">{{ $activeBooking->passengerSeatNumbers() ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Titik naik</dt>
                        <dd class="text-sm font-bold text-slate-900 mt-0.5">{{ $nextSchedule->route?->origin?->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="relative flex flex-col justify-between gap-5 p-6 sm:p-7 bg-brand-800 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/40">
                <div>
                    <p class="text-xs text-brand-100">Kode booking</p>
                    <p class="mt-1 text-xl font-extrabold tracking-wide">{{ $activeBooking->booking_code }}</p>
                </div>
                <a href="{{ route('dashboard.user.user-tiket', ['booking_id' => $activeBooking->id, 'action' => 'ticket']) }}" class="lp-btn lp-btn--primary w-full text-sm">
                    Buka e-ticket
                </a>
            </div>
        </section>
    @endif

    @include('components.search-ticket', [
        'heading' => 'Cari jadwal',
        'action' => route('dashboard.user.search'),
    ])

    <div class="grid lg:grid-cols-[1.55fr_1fr] gap-8 items-start">
        <section aria-labelledby="pesanan-terakhir" class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 id="pesanan-terakhir" class="text-lg font-extrabold text-brand-900">Pesanan terakhir</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Riwayat transaksi tiket terbaru akunmu.</p>
                </div>
                <a href="{{ route('dashboard.user.user-tiket') }}" class="shrink-0 text-sm font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-2">Lihat semua</a>
            </div>

            @if ($recentBookings->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="font-bold text-slate-900">Belum ada pesanan.</p>
                    <p class="mt-1 text-sm text-slate-600">Pesanan pertamamu akan muncul di sini, lengkap dengan status pembayarannya.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentBookings as $recent)
                        @php
                            $status = strtolower($recent->status);
                            $statusLabel = match (true) {
                                in_array($status, ['paid', 'confirmed', 'success', 'lunas']) => ['Lunas', 'text-brand-800 bg-brand-100'],
                                $status === 'refund_pending' => ['Refund', 'text-amber-900 bg-amber-100'],
                                $status === 'pending' => ['Pending', 'text-amber-900 bg-amber-100'],
                                $status === 'completed' => ['Selesai', 'text-slate-700 bg-slate-100'],
                                in_array($status, ['cancelled', 'expired']) => ['Batal', 'text-rose-800 bg-rose-50'],
                                default => [ucfirst($recent->status), 'text-slate-700 bg-slate-100'],
                            };
                            $recentSchedule = $recent->schedule;
                        @endphp
                        <li class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-5 hover:bg-slate-50/70 transition-colors">
                            <div class="min-w-0 flex-1 space-y-1">
                                <p class="text-sm sm:text-[15px] font-bold text-slate-900 leading-snug">
                                    {{ $recentSchedule?->route?->origin?->name ?? '-' }}
                                    <span class="font-normal text-slate-400 mx-0.5">&rarr;</span>
                                    {{ $recentSchedule?->route?->destination?->name ?? '-' }}
                                </p>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                                    <span>{{ $recentSchedule ? $dateOf($recentSchedule->departure_date)->isoFormat('D MMM YYYY') : '-' }}</span>
                                    <span class="text-slate-300" aria-hidden="true">&bull;</span>
                                    <span>{{ $recent->total_passenger }} penumpang</span>
                                    <span class="text-slate-300" aria-hidden="true">&bull;</span>
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] font-semibold text-slate-600">{{ $recent->booking_code }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <span class="text-sm sm:text-[15px] font-extrabold text-slate-900 tabular-nums sm:text-right sm:min-w-[96px]">
                                    Rp {{ number_format($recent->total_price, 0, ',', '.') }}
                                </span>
                                @if ($status === 'pending' && $recent->expired_at?->isFuture())
                                    <a href="{{ route('dashboard.user.booking.pembayaran', $recent) }}" class="inline-flex items-center justify-center min-w-[76px] min-h-8 px-3 rounded-lg bg-amber-800 text-white text-xs font-bold hover:bg-amber-900 transition-colors">Bayar</a>
                                @else
                                    <span class="inline-flex items-center justify-center min-w-[76px] px-2.5 py-1 rounded-full text-xs font-bold {{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section aria-labelledby="promo" class="space-y-3">
            <div class="flex items-baseline justify-between gap-3">
                <div>
                    <h2 id="promo" class="text-lg font-extrabold text-brand-900">Kode promo</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Tempel kodenya di halaman pembayaran.</p>
                </div>
                <a href="{{ route('dashboard.user.user-promo') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900 underline underline-offset-2">Semua voucher</a>
            </div>
            <ul class="space-y-3">
                @foreach ($promos as $promo)
                    <li class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-bold text-slate-900 leading-snug">{{ $promo['title'] }}</p>
                            @if (! empty($promo['discount_label']))
                                <span class="shrink-0 rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-900">{{ $promo['discount_label'] }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $promo['description'] }}</p>
                        <p class="text-xs text-slate-500">Berlaku sampai {{ $promo['valid_until'] }}</p>
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-dashed border-brand-600/70 bg-slate-50/70 pl-3.5 pr-1.5 py-1">
                            <code class="text-sm font-extrabold text-brand-900 tracking-wide">{{ $promo['code'] }}</code>
                            <button type="button" class="min-h-8 px-3 rounded-md text-xs font-bold text-brand-800 hover:bg-brand-100/70 transition-colors cursor-pointer" data-copy-code="{{ $promo['code'] }}">Salin</button>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p class="sr-only" role="status" aria-live="polite" data-copy-status></p>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-copy-code]').forEach((button) => {
            button.addEventListener('click', async () => {
                const status = document.querySelector('[data-copy-status]');

                try {
                    await navigator.clipboard.writeText(button.dataset.copyCode);
                    button.textContent = 'Tersalin';
                    status.textContent = `Kode ${button.dataset.copyCode} tersalin.`;
                } catch {
                    button.textContent = 'Salin manual';
                    status.textContent = 'Browser menolak menyalin. Pilih kodenya lalu salin manual.';
                }

                window.setTimeout(() => { button.textContent = 'Salin'; }, 2000);
            });
        });
    </script>
@endpush
