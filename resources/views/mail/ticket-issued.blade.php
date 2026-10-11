@php
    $schedule = $booking->schedule;
    $route = $schedule?->route;
@endphp
<x-mail::message>
# Pembayaranmu sudah masuk

Halo {{ $booking->customerName() }}, ini e-ticket untuk perjalananmu.

<x-mail::table>
| | |
|:--|:--|
| Kode booking | **{{ $booking->booking_code }}** |
| Rute | {{ $route?->origin?->name }} ke {{ $route?->destination?->name }} |
| Berangkat | {{ $schedule?->departure_date?->locale('id')->isoFormat('dddd, D MMMM YYYY') }}, {{ $schedule?->departureCarbon()->format('H:i') }} WIB |
| Kursi | {{ $booking->passengerSeatNumbers() }} |
| Penumpang | {{ $booking->passengers->pluck('name')->filter()->implode(', ') }} |
| Total dibayar | Rp {{ number_format($booking->total_price, 0, ',', '.') }} |
</x-mail::table>

Datang ke {{ $route?->origin?->name }} paling lambat 15 menit sebelum berangkat. Tunjukkan QR di halaman e-ticket ke petugas, atau sebutkan kode booking di atas.

<x-mail::button :url="$ticketUrl" color="success">
Buka e-ticket
</x-mail::button>

@if ($booking->isGuestBooking())
Kamu memesan tanpa akun, jadi simpan email ini. Kalau nanti masuk dengan Google memakai {{ $booking->customerEmail() }}, tiket ini ikut tersimpan di akunmu.
@endif

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
