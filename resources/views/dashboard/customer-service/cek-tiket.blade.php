@extends('layouts.customer-service')

@section('title', 'Cek Tiket Penumpang - Customer Service TravelKu')

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name ?: '-';

    $paymentStatusMeta = fn (string $status): array => match (strtolower($status)) {
        'paid', 'confirmed', 'success', 'lunas', 'checked_in' => ['Lunas', 'bg-[#DFF0E8] text-[#0C5A3E] border-[#B8DFC9]'],
        'pending' => ['Menunggu Bayar', 'bg-amber-50 text-amber-800 border-amber-200'],
        'completed' => ['Selesai', 'bg-slate-100 text-slate-700 border-slate-200'],
        'refund_pending' => ['Menunggu Refund', 'bg-purple-50 text-purple-800 border-purple-200'],
        'cancelled', 'expired', 'batal', 'failed' => ['Batal', 'bg-rose-50 text-rose-700 border-rose-200'],
        default => [ucfirst($status), 'bg-slate-100 text-slate-700 border-slate-200'],
    };
@endphp

@section('content')
<div class="space-y-6 text-[#13201B]">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Cek Tiket Penumpang</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                Pencarian cepat data perjalanan pelanggan secara read-only berdasarkan Kode Booking, Nama Penumpang, atau Nomor HP.
            </p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full border border-[#DEDAC9] bg-[#FFFDF8] px-3 py-1 text-xs font-bold text-[#5C6862]">
            Read-Only &middot; Tanpa Edit Transaksi
        </span>
    </header>

    {{-- Omnisearch Card --}}
    <section class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs space-y-5">
        <form method="GET" action="{{ route('dashboard.customer-service.cek-tiket') }}" role="search" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-[#5C6862]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <label for="cs-omnisearch" class="sr-only">Cari tiket penumpang</label>
                <input type="search"
                       id="cs-omnisearch"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Ketik Kode Booking (mis. TRV-...), Nama Penumpang, atau Nomor HP..."
                       class="w-full rounded-xl border border-[#DEDAC9] bg-white py-3 pl-10 pr-4 text-sm font-medium text-[#13201B] placeholder:text-[#8A9690] focus:border-[#0C7950] focus:outline-none focus:ring-2 focus:ring-[#0C7950]/20">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-xl bg-[#0C7950] px-5 py-3 text-sm font-bold text-white hover:bg-[#0C5A3E] transition">
                    Cari Tiket
                </button>
                @if ($query !== '')
                    <a href="{{ route('dashboard.customer-service.cek-tiket') }}" class="inline-flex items-center justify-center rounded-xl border border-[#DEDAC9] bg-[#F6F4EC] px-4 py-3 text-sm font-bold text-[#4B5D55] hover:bg-[#ECE8DA] transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if ($query !== '')
            <div class="pt-2 space-y-4">
                <div class="flex items-center justify-between gap-2 text-xs font-bold text-[#5C6862]">
                    <span>Hasil pencarian untuk <strong class="text-[#0C392F]">"{{ $query }}"</strong></span>
                    <span>{{ $searchResults->count() }} tiket ditemukan</span>
                </div>

                @if ($searchResults->isEmpty())
                    <div class="rounded-xl border border-dashed border-[#DEDAC9] bg-[#F6F4EC]/70 p-8 text-center">
                        <p class="text-sm font-bold text-[#0C392F]">Tidak ada pesanan yang cocok dengan "{{ $query }}".</p>
                        <p class="text-xs text-[#5C6862] mt-1">Periksa kembali kode booking, ejaan nama penumpang, atau nomor HP tanpa spasi.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        @foreach ($searchResults as $booking)
                            @php
                                [$payLabel, $payBadgeClass] = $paymentStatusMeta((string) $booking->status);
                                $bookerName = $booking->contact_name ?: ($booking->user?->name ?: null);
                                $passengersList = $booking->passengers->pluck('name')->filter()->unique()->values();
                                $primaryName = $bookerName ?: ($passengersList->first() ?: '-');
                                $customerPhone = $booking->contact_phone ?: ($booking->user?->phone ?: ($booking->passengers->first()?->phone ?: '-'));
                                $originCity = $cityOf($booking->schedule?->route?->origin);
                                $destinationCity = $cityOf($booking->schedule?->route?->destination);
                                $originPool = $booking->schedule?->route?->origin?->name ?? $originCity;
                                $destinationPool = $booking->schedule?->route?->destination?->name ?? $destinationCity;
                                $seatNumbers = $booking->passengerSeatNumbers() ?: $booking->total_passenger.' kursi';
                                $vehicleName = $booking->schedule?->vehicle?->name ?? 'Armada belum ditugaskan';
                                $plateNumber = $booking->schedule?->vehicle?->plate_number ?? '-';
                                $driverName = $booking->schedule?->driver?->user?->name ?? 'Sopir belum ditugaskan';
                                $driverPhoneRaw = $booking->schedule?->driver?->phone ?: ($booking->schedule?->driver?->user?->phone ?? '');
                                $driverWaDigits = preg_replace('/[^0-9]/', '', (string) $driverPhoneRaw);
                                if (str_starts_with((string) $driverWaDigits, '0')) {
                                    $driverWaDigits = '62'.substr((string) $driverWaDigits, 1);
                                }
                            @endphp
                            <article class="rounded-2xl border border-[#DEDAC9] bg-white p-5 shadow-xs space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-2 border-b border-[#ECE8DA] pb-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-base font-extrabold text-[#0C392F]">{{ $booking->booking_code }}</span>
                                            <span class="rounded-md bg-[#F6F4EC] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#5C6862]">Read-Only</span>
                                        </div>
                                        @if ($booking->ticket)
                                            <p class="text-xs text-[#5C6862] mt-0.5">No. Tiket: <span class="font-mono font-semibold">{{ $booking->ticket->ticket_number }}</span></p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $payBadgeClass }}">
                                        {{ $payLabel }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-xs">
                                    <div class="rounded-xl bg-[#F6F4EC]/70 p-3">
                                        <p class="font-bold uppercase tracking-wider text-[10px] text-[#5C6862]">Penumpang &amp; Kontak</p>
                                        <p class="mt-1 text-sm font-extrabold text-[#13201B]">{{ $primaryName }}</p>
                                        @if ($passengersList->isNotEmpty() && ($passengersList->count() > 1 || ($bookerName && ! $passengersList->contains($bookerName))))
                                            <p class="mt-0.5 text-[#4B5D55]">Penumpang: {{ $passengersList->implode(', ') }}</p>
                                        @endif
                                        <p class="mt-1 font-semibold text-[#5C6862]">HP: {{ $customerPhone }}</p>
                                    </div>

                                    <div class="rounded-xl bg-[#F6F4EC]/70 p-3">
                                        <p class="font-bold uppercase tracking-wider text-[10px] text-[#5C6862]">Rute &amp; Keberangkatan</p>
                                        <p class="mt-1 text-sm font-extrabold text-[#0C392F]">{{ $originCity }} ke {{ $destinationCity }}</p>
                                        <p class="mt-0.5 text-[#4B5D55]">{{ $originPool }} &rarr; {{ $destinationPool }}</p>
                                        @if ($booking->schedule)
                                            <p class="mt-1 font-bold text-[#13201B]">
                                                {{ $booking->schedule->departureCarbon()->translatedFormat('d M Y, H:i') }} WIB
                                            </p>
                                        @endif
                                    </div>

                                    <div class="rounded-xl bg-[#F6F4EC]/70 p-3">
                                        <p class="font-bold uppercase tracking-wider text-[10px] text-[#5C6862]">Nomor Kursi &amp; Pembayaran</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                            <span class="inline-flex items-center rounded-lg bg-[#0C392F] px-2.5 py-0.5 text-xs font-extrabold text-white">
                                                Kursi {{ $seatNumbers }}
                                            </span>
                                            <span class="font-semibold text-[#4B5D55]">
                                                Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        @if ($booking->payment?->payment_method)
                                            <p class="mt-1 text-[#5C6862]">Metode: {{ $booking->payment->payment_method }}</p>
                                        @endif
                                    </div>

                                    <div class="rounded-xl bg-[#F6F4EC]/70 p-3">
                                        <p class="font-bold uppercase tracking-wider text-[10px] text-[#5C6862]">Armada &amp; Sopir Bertugas</p>
                                        <p class="mt-1 text-sm font-extrabold text-[#13201B]">
                                            {{ $plateNumber }} <span class="font-semibold text-xs text-[#5C6862]">({{ $vehicleName }})</span>
                                        </p>
                                        <p class="mt-0.5 font-semibold text-[#4B5D55]">Sopir: {{ $driverName }}</p>
                                        @if (! empty($driverWaDigits))
                                            <a href="https://wa.me/{{ $driverWaDigits }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="mt-1 inline-flex items-center gap-1 font-bold text-[#0C7950] hover:underline">
                                                Hubungi Sopir (WA) &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                    <span class="text-[11px] text-[#5C6862]">Sumber: {{ $booking->sourceLabel() }}</span>
                                    <a href="{{ route('dashboard.customer-service.laporan-kendala', [
                                        'booking_code' => $booking->booking_code,
                                        'customer_name' => $primaryName !== '-' ? $primaryName : '',
                                        'customer_phone' => $customerPhone !== '-' ? $customerPhone : '',
                                    ]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-[#0C7950] bg-[#EEF6F1] px-3 py-1.5 text-xs font-bold text-[#0C5A3E] hover:bg-[#DFF0E8] transition">
                                        + Catat Kendala untuk Tiket Ini
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-dashed border-[#DEDAC9] bg-[#F6F4EC]/50 px-5 py-8 text-center text-xs text-[#5C6862]">
                Masukkan <strong class="text-[#0C392F]">Kode Booking</strong>, <strong class="text-[#0C392F]">Nama Penumpang</strong>, atau <strong class="text-[#0C392F]">Nomor HP</strong> di atas untuk melihat detail lengkap tiket (rute, jadwal, kursi, status bayar, plat armada, dan sopir).
            </div>
        @endif
    </section>
</div>
@endsection
