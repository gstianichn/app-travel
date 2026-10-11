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

    {{-- Greeting: one line of real context instead of a promo banner --}}
    <section aria-labelledby="sapaan">
        <h1 id="sapaan" class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Halo, {{ $firstName }}.</h1>
        <p class="mt-2 text-lg text-slate-600">
            @if ($nextSchedule)
                Perjalanan berikutnya {{ $dateOf($nextSchedule->departure_date)->isoFormat('dddd, D MMMM') }}, jam {{ $nextSchedule->departureCarbon()->format('H:i') }} WIB.
            @else
                Belum ada perjalanan terjadwal. Cari jadwal di bawah untuk memesan.
            @endif
        </p>
        <p class="mt-1 text-slate-500">
            {{ $activeTicketsCount }} tiket aktif, {{ $completedTripsCount }} perjalanan selesai{{ $unreadNotifCount > 0 ? ',' : '.' }}
            @if ($unreadNotifCount > 0)
                <a href="{{ route('dashboard.user.user-notifikasi') }}" class="font-semibold text-brand-700 underline underline-offset-2">{{ $unreadNotifCount }} notifikasi belum dibaca</a>.
            @endif
        </p>
    </section>

    @if ($pendingPaymentsCount > 0)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border-2 border-amber-500 bg-amber-50 px-5 py-4" role="status">
            <p class="text-amber-900">
                <b>{{ $pendingPaymentsCount }} pembayaran belum selesai.</b>
                Kursinya hanya ditahan sampai batas waktu bayar.
            </p>
            <a href="{{ $pendingBooking ? route('dashboard.user.booking.pembayaran', $pendingBooking) : route('dashboard.user.user-tiket') }}"
               class="inline-flex items-center min-h-11 px-4 rounded-lg bg-amber-800 text-white font-bold hover:bg-amber-900">
                Lanjutkan pembayaran
            </a>
        </div>
    @endif

    {{-- Next trip as a boarding pass: the one thing most people open this page for --}}
    @if ($activeBooking && $nextSchedule)
        <section aria-labelledby="perjalanan-berikutnya" class="grid md:grid-cols-[1fr_260px] rounded-2xl overflow-hidden border border-brand-700 bg-white">
            <div class="p-6 sm:p-8">
                <h2 id="perjalanan-berikutnya" class="text-sm font-semibold text-slate-500">Perjalanan berikutnya</h2>
                <p class="mt-3 flex flex-wrap items-baseline gap-x-3 text-2xl sm:text-3xl font-extrabold text-brand-900">
                    <span>{{ $nextSchedule->route?->origin?->city ?: $nextSchedule->route?->origin?->name }}</span>
                    <span class="text-lg font-semibold text-brand-600">ke</span>
                    <span>{{ $nextSchedule->route?->destination?->city ?: $nextSchedule->route?->destination?->name }}</span>
                </p>
                <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm text-slate-500">Tanggal</dt>
                        <dd class="font-bold text-slate-900">{{ $dateOf($nextSchedule->departure_date)->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">Berangkat</dt>
                        <dd class="font-bold text-slate-900">{{ $nextSchedule->departureCarbon()->format('H:i') }} WIB</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">Kursi</dt>
                        <dd class="font-bold text-slate-900">{{ $activeBooking->passengerSeatNumbers() ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">Titik naik</dt>
                        <dd class="font-bold text-slate-900">{{ $nextSchedule->route?->origin?->name ?? '-' }}</dd>
                    </div>
                </dl>
                @if ($nextSchedule->vehicle)
                    <p class="mt-4 text-slate-600">{{ $nextSchedule->vehicle->type ?: $nextSchedule->vehicle->name }}, pelat {{ $nextSchedule->vehicle->plate_number }}</p>
                @endif
            </div>
            <div class="relative flex flex-col justify-between gap-6 p-6 sm:p-8 bg-brand-700 text-white border-t-2 md:border-t-0 md:border-l-2 border-dashed border-white/50">
                <div>
                    <p class="text-sm text-brand-100">Kode booking</p>
                    <p class="text-2xl font-extrabold tracking-wide">{{ $activeBooking->booking_code }}</p>
                </div>
                <a href="{{ route('dashboard.user.user-tiket', ['booking_id' => $activeBooking->id, 'action' => 'ticket']) }}" class="lp-btn lp-btn--primary w-full">
                    Buka e-ticket
                </a>
            </div>
        </section>
    @endif

    @include('components.search-ticket', [
        'heading' => 'Cari jadwal',
        'action' => route('dashboard.user.search'),
    ])

    <div class="grid lg:grid-cols-[1.6fr_1fr] gap-10">
        <section aria-labelledby="pesanan-terakhir">
            <div class="flex items-baseline justify-between gap-4">
                <h2 id="pesanan-terakhir" class="text-2xl font-extrabold text-brand-900">Pesanan terakhir</h2>
                <a href="{{ route('dashboard.user.user-tiket') }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Lihat semua</a>
            </div>

            @if ($recentBookings->isEmpty())
                <div class="mt-4 rounded-xl border border-dashed border-slate-300 px-6 py-8">
                    <p class="font-bold text-slate-900">Belum ada pesanan.</p>
                    <p class="mt-1 text-slate-600">Pesanan pertamamu akan muncul di sini, lengkap dengan status pembayarannya.</p>
                </div>
            @else
                <ul class="mt-3 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($recentBookings as $recent)
                        @php
                            $status = strtolower($recent->status);
                            $statusLabel = match (true) {
                                in_array($status, ['paid', 'confirmed', 'success', 'lunas']) => ['Lunas', 'text-brand-700 bg-brand-100'],
                                $status === 'pending' => ['Menunggu bayar', 'text-amber-900 bg-amber-100'],
                                $status === 'completed' => ['Selesai', 'text-slate-700 bg-slate-100'],
                                in_array($status, ['cancelled', 'expired']) => ['Batal', 'text-rose-800 bg-rose-50'],
                                default => [ucfirst($recent->status), 'text-slate-700 bg-slate-100'],
                            };
                            $recentSchedule = $recent->schedule;
                        @endphp
                        <li class="py-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900">
                                    {{ $recentSchedule?->route?->origin?->name ?? '-' }}
                                    <span class="font-normal text-slate-500">ke</span>
                                    {{ $recentSchedule?->route?->destination?->name ?? '-' }}
                                </p>
                                <p class="text-slate-600">
                                    {{ $recentSchedule ? $dateOf($recentSchedule->departure_date)->isoFormat('D MMM YYYY') : '-' }},
                                    {{ $recent->total_passenger }} orang, kode {{ $recent->booking_code }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-bold text-slate-900">Rp {{ number_format($recent->total_price, 0, ',', '.') }}</span>
                                @if ($status === 'pending' && $recent->expired_at?->isFuture())
                                    <a href="{{ route('dashboard.user.booking.pembayaran', $recent) }}" class="inline-flex items-center min-h-11 px-4 rounded-lg bg-amber-800 text-white font-bold hover:bg-amber-900">Bayar</a>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-sm font-semibold {{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section aria-labelledby="promo">
            <h2 id="promo" class="text-2xl font-extrabold text-brand-900">Kode promo</h2>
            <p class="mt-1 text-slate-600">Tempel kodenya di halaman pembayaran.</p>
            <ul class="mt-4 space-y-4">
                @foreach ($promos as $promo)
                    <li class="rounded-xl border border-slate-300 bg-white p-5">
                        <p class="font-bold text-slate-900">{{ $promo['title'] }}</p>
                        <p class="mt-1 text-slate-600">{{ $promo['description'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">Berlaku sampai {{ $promo['valid_until'] }}</p>
                        <div class="mt-4 flex items-center justify-between gap-3 rounded-lg border-2 border-dashed border-brand-600 pl-4">
                            <code class="font-bold text-brand-800 tracking-wide">{{ $promo['code'] }}</code>
                            <button type="button" class="min-h-11 px-4 font-semibold text-brand-700 hover:bg-brand-50 rounded-r-lg" data-copy-code="{{ $promo['code'] }}">Salin</button>
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
