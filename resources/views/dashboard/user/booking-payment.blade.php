@extends('layouts.dashboard')

@section('page-title', 'Pembayaran')

@php
    $raw = $payment->raw_response ?? [];
    $method = strtolower(($payment->payment_method ?? '').' '.($payment->bank_name ?? ''));
    $isSimulated = (bool) ($raw['simulated'] ?? false);

    $channel = match (true) {
        str_contains($method, 'mandiri') => 'mandiri',
        str_contains($method, 'seabank') => 'seabank',
        str_contains($method, 'bsi') => 'bsi',
        str_contains($method, 'bca') => 'bca',
        str_contains($method, 'bni') => 'bni',
        str_contains($method, 'bri') => 'bri',
        str_contains($method, 'qris') => 'qris',
        str_contains($method, 'gopay') => 'gopay',
        str_contains($method, 'shopee') => 'shopeepay',
        default => 'va',
    };

    $qrImageUrl = null;
    $deeplinkUrl = null;
    foreach ((array) ($raw['actions'] ?? []) as $action) {
        if (($action['name'] ?? '') === 'generate-qr-code') {
            $qrImageUrl = $action['url'] ?? null;
        } elseif (($action['name'] ?? '') === 'deeplink-redirect') {
            $deeplinkUrl = $action['url'] ?? null;
        }
    }
    $qrString = $raw['qr_string'] ?? null;

    $billerCode = $raw['biller_code'] ?? '70012';
    $billKey = $raw['bill_key'] ?? $payment->account_number;
    $companyName = $payment->account_name ?: \App\Models\AppSetting::companyName();
    $amount = 'Rp '.number_format($booking->total_price, 0, ',', '.');
    $deadline = ($payment->expired_at ?? $booking->expired_at)?->copy()->timezone(config('app.timezone'));
    $searchUrl = auth()->check() ? route('dashboard.user.search') : route('search');

    $channelLabels = [
        'bca' => 'BCA', 'mandiri' => 'Mandiri', 'bni' => 'BNI', 'bri' => 'BRI (BRIVA)', 'bsi' => 'BSI',
        'seabank' => 'SeaBank', 'qris' => 'QRIS', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay',
    ];
    $copyClass = 'inline-flex items-center justify-center min-h-11 px-4 rounded-lg border-2 border-brand-800 font-bold text-brand-800 hover:bg-brand-50';
@endphp

@section('content')
<div class="space-y-8 max-w-4xl" x-data="paymentHandler({
    remainingSeconds: {{ $remainingPaymentSeconds }},
    statusCheckUrl: @js(route('dashboard.user.booking.payment-status', $booking)),
    successUrl: @js(route('dashboard.user.booking.sukses', $booking))
})">

    @include('dashboard.user.partials.booking-steps', ['current' => 3])

    @include('dashboard.user.partials.flash')

    <header class="space-y-2">
        <p class="font-semibold text-slate-600">Kode booking <span class="font-extrabold text-slate-900 tracking-wide">{{ $booking->booking_code }}</span></p>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Bayar {{ $amount }}</h1>
        <p class="text-slate-600">
            {{ $booking->schedule?->route?->origin?->name }} ke {{ $booking->schedule?->route?->destination?->name }},
            {{ $booking->schedule?->departureCarbon()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB, kursi {{ $booking->passengerSeatNumbers() }}.
        </p>
    </header>

    {{-- Deadline first: unpaid bookings are cancelled and the seats released when it runs out --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border-2 border-amber-600 bg-amber-50 px-5 py-4 text-amber-950">
        <p x-show="remainingSeconds > 0">Bayar sebelum <b>{{ $deadline?->format('H:i') }} WIB</b>. Lewat dari itu, pesanan batal dan kursinya dilepas.</p>
        <p class="font-mono text-2xl font-extrabold tabular-nums" x-show="remainingSeconds > 0" x-text="formatTimer(remainingSeconds)" role="timer"></p>
        <p x-show="remainingSeconds <= 0" x-cloak>
            <b>Batas waktu bayar sudah lewat.</b> Kalau kamu sudah transfer, tunggu sebentar lalu cek status di bawah. Kalau belum, <a href="{{ $searchUrl }}" class="font-semibold underline underline-offset-2">pesan ulang</a>.
        </p>
    </div>

    <section class="rounded-xl border border-slate-300 bg-white" aria-labelledby="cara-bayar">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
            <div class="flex items-center gap-4">
                @if ($channel !== 'va')
                    <img src="{{ asset('images/payments/'.$channel.'.svg') }}" alt="" class="h-8 w-auto max-w-32 object-contain">
                @endif
                <h2 id="cara-bayar" class="text-xl font-extrabold text-brand-900">{{ $payment->payment_method ?: 'Virtual account' }}</h2>
            </div>
            <p class="text-sm text-slate-600">Status dicek otomatis setiap beberapa detik.</p>
        </div>

        <div class="p-6 space-y-6">
            @if (in_array($channel, ['bca', 'bni', 'bri', 'bsi', 'seabank', 'va'], true))
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-slate-600">Nomor virtual account</p>
                        <p class="font-mono text-3xl sm:text-4xl font-extrabold tracking-wider text-slate-900 break-all">{{ $payment->account_number }}</p>
                        <p class="mt-1 text-slate-600">Atas nama <b class="text-slate-900">{{ $companyName }}</b></p>
                    </div>
                    <button type="button" class="{{ $copyClass }}" @click="copyText(@js((string) $payment->account_number), 'Nomor virtual account tersalin.')">Salin nomor</button>
                </div>

                <div class="space-y-2 border-t border-slate-200 pt-5">
                    @switch($channel)
                        @case('bca')
                            <h3 class="font-bold text-slate-900">Bayar lewat m-BCA, myBCA, atau ATM BCA</h3>
                            <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                                <li>Pilih <b>m-Transfer</b>, lalu <b>BCA Virtual Account</b>.</li>
                                <li>Masukkan nomor <b class="font-mono">{{ $payment->account_number }}</b>.</li>
                                <li>Pastikan penerima {{ $companyName }} dan nominal {{ $amount }}, lalu masukkan PIN.</li>
                            </ol>
                            @break
                        @case('bni')
                            <h3 class="font-bold text-slate-900">Bayar lewat BNI Mobile atau ATM BNI</h3>
                            <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                                <li>Pilih <b>Pembayaran</b>, lalu <b>Virtual Account Billing</b>.</li>
                                <li>Masukkan nomor <b class="font-mono">{{ $payment->account_number }}</b>.</li>
                                <li>Cek nominal {{ $amount }} dan nama penerima, lalu masukkan password transaksi.</li>
                            </ol>
                            @break
                        @case('bri')
                            <h3 class="font-bold text-slate-900">Bayar lewat BRImo</h3>
                            <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                                <li>Pilih <b>BRIVA</b>, lalu <b>Pembayaran Baru</b>.</li>
                                <li>Masukkan nomor <b class="font-mono">{{ $payment->account_number }}</b>.</li>
                                <li>Cek rincian, pilih sumber dana, lalu masukkan PIN.</li>
                            </ol>
                            @break
                        @case('bsi')
                            <h3 class="font-bold text-slate-900">Bayar lewat BSI Mobile</h3>
                            <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                                <li>Pilih <b>Transfer</b>, lalu <b>Transfer Antar Bank / Virtual Account</b>.</li>
                                <li>Bank tujuan: <b>Bank Permata</b> (kode 013).</li>
                                <li>Masukkan nomor <b class="font-mono">{{ $payment->account_number }}</b>, cek penerima {{ $companyName }}, lalu konfirmasi dengan PIN.</li>
                            </ol>
                            @break
                        @case('seabank')
                            <h3 class="font-bold text-slate-900">Bayar dari aplikasi SeaBank</h3>
                            <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                                <li>Pilih <b>Transfer</b>, lalu <b>Bank Lain</b>.</li>
                                <li>Bank tujuan: <b>Bank Permata</b> (kode 013).</li>
                                <li>Masukkan nomor <b class="font-mono">{{ $payment->account_number }}</b> dan pilih <b>BI-FAST</b>.</li>
                                <li>Nominal {{ $amount }} muncul sendiri. Konfirmasi dengan PIN SeaBank.</li>
                            </ol>
                            @break
                    @endswitch
                </div>
            @elseif ($channel === 'mandiri')
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-slate-600">Kode perusahaan (Biller Code)</p>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">{{ $billerCode }}</p>
                        <button type="button" class="mt-2 {{ $copyClass }}" @click="copyText(@js((string) $billerCode), 'Kode perusahaan tersalin.')">Salin kode</button>
                    </div>
                    <div>
                        <p class="text-slate-600">Nomor tagihan (Bill Key)</p>
                        <p class="font-mono text-3xl font-extrabold text-slate-900 break-all">{{ $billKey }}</p>
                        <button type="button" class="mt-2 {{ $copyClass }}" @click="copyText(@js((string) $billKey), 'Nomor tagihan tersalin.')">Salin nomor</button>
                    </div>
                </div>
                <div class="space-y-2 border-t border-slate-200 pt-5">
                    <h3 class="font-bold text-slate-900">Bayar lewat Livin' by Mandiri</h3>
                    <ol class="list-decimal pl-5 space-y-1 text-slate-700">
                        <li>Pilih <b>Bayar</b>, lalu cari <b>Midtrans</b> ({{ $billerCode }}).</li>
                        <li>Masukkan nomor tagihan <b class="font-mono">{{ $billKey }}</b>.</li>
                        <li>Pastikan nominal {{ $amount }}, lalu masukkan PIN Livin'.</li>
                    </ol>
                </div>
            @elseif ($channel === 'qris')
                <div class="grid sm:grid-cols-[220px_minmax(0,1fr)] gap-6 items-start">
                    <div class="space-y-3">
                        <div class="w-[220px] aspect-square rounded-lg border border-slate-300 bg-white p-3">
                            @if ($qrImageUrl)
                                <img src="{{ $qrImageUrl }}" alt="Kode QRIS untuk membayar {{ $amount }}" class="w-full h-full object-contain">
                            @elseif ($qrString)
                                <div class="w-full h-full" data-qr="{{ $qrString }}" role="img" aria-label="Kode QRIS untuk membayar {{ $amount }}"></div>
                            @endif
                        </div>
                        @if ($isSimulated)
                            <p class="text-sm font-semibold text-amber-900">Mode uji: QR ini tidak terhubung ke Midtrans, jadi tidak bisa dibayar.</p>
                        @endif
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-bold text-slate-900">Pindai dengan aplikasi apa pun yang mendukung QRIS</h3>
                        <p class="text-slate-700">GoPay, OVO, DANA, ShopeePay, LinkAja, atau aplikasi m-banking. Nominal {{ $amount }} terisi sendiri setelah dipindai.</p>
                        <div class="flex flex-wrap gap-2">
                            @if ($qrString)
                                <button type="button" class="{{ $copyClass }}" @click="copyText(@js($qrString), 'Kode QR tersalin.')">Salin Kode QR</button>
                            @endif
                            @if ($qrImageUrl)
                                <a href="{{ $qrImageUrl }}" download="qris-{{ $booking->booking_code }}.png" target="_blank" rel="noopener" class="{{ $copyClass }}">Unduh gambar QRIS</a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-3 max-w-md">
                    <h3 class="font-bold text-slate-900">Selesaikan di aplikasi {{ $channel === 'gopay' ? 'Gojek atau GoPay' : 'Shopee' }}</h3>
                    <p class="text-slate-700">Halaman ini pindah ke e-ticket sendiri begitu pembayaran masuk.</p>
                    @if ($deeplinkUrl)
                        <a href="{{ $deeplinkUrl }}" target="_blank" rel="noopener" class="lp-btn lp-btn--green">Buka {{ $channel === 'gopay' ? 'GoPay' : 'ShopeePay' }}</a>
                    @else
                        <p class="font-semibold text-amber-900">Tautan aplikasi belum diterima dari Midtrans. Pilih cara bayar lain di bawah, atau coba lagi sebentar.</p>
                    @endif
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5">
                <button type="button" class="{{ $copyClass }}" @click="copyText(@js((string) (int) $booking->total_price), 'Nominal tersalin.')">Salin nominal {{ $amount }}</button>
                <button type="button" @click="checkStatusManual()" :disabled="isChecking"
                        class="inline-flex items-center min-h-11 px-4 rounded-lg font-semibold text-brand-700 underline underline-offset-2 disabled:opacity-60">
                    <span x-text="isChecking ? 'Mengecek...' : 'Saya sudah bayar, cek status'"></span>
                </button>
            </div>
            <p class="text-sm font-semibold text-brand-800" role="status" aria-live="polite" x-text="feedback"></p>
        </div>
    </section>

    {{-- Changing the method is inline, not a modal: it is a short list and the page stays readable around it --}}
    <details class="rounded-xl border border-slate-300 bg-white">
        <summary class="cursor-pointer list-none flex items-center justify-between gap-3 min-h-11 px-6 py-4 font-bold text-slate-900">
            Ganti cara bayar
            <svg class="w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
        </summary>
        <form action="{{ route('dashboard.user.booking.switch-payment', $booking) }}" method="POST" class="border-t border-slate-200 p-6 space-y-4">
            @csrf
            <div class="grid sm:grid-cols-3 gap-2">
                @foreach ($paymentChannels ?? [] as $code => $paymentChannel)
                    <label class="flex items-center gap-3 min-h-11 rounded-lg border border-slate-300 px-4 py-2 cursor-pointer hover:border-brand-600 has-[:checked]:border-2 has-[:checked]:border-brand-700 has-[:checked]:bg-brand-50">
                        <input type="radio" name="payment_channel" value="{{ $code }}" class="w-4 h-4 accent-[#0C5A3E]" @checked($channel === $code)>
                        <img src="{{ asset('images/payments/'.$code.'.svg') }}" alt="" class="h-5 w-12 object-contain" loading="lazy">
                        <span class="font-semibold text-slate-900">{{ $channelLabels[$code] ?? $paymentChannel['name'] }}</span>
                    </label>
                @endforeach
            </div>
            <p class="text-sm text-slate-600">Nomor bayar yang lama tidak berlaku lagi setelah diganti.</p>
            <button type="submit" class="lp-btn lp-btn--green">Pakai cara bayar ini</button>
        </form>
    </details>

    @auth
        <p class="text-slate-600">Bisa ditinggal dulu. Pesanan ini ada di <a href="{{ route('dashboard.user.index') }}" class="font-semibold text-brand-700 underline underline-offset-2">Beranda</a> sampai batas waktu bayar.</p>
    @else
        <p class="text-slate-600">Simpan kode booking <b>{{ $booking->booking_code }}</b>. Setelah lunas, e-ticket dikirim ke <b>{{ $booking->customerEmail() }}</b>.</p>
    @endauth

    @unless (app()->isProduction())
        {{-- TODO: developer shortcut, hidden in production where the Midtrans webhook confirms payments --}}
        <details class="rounded-xl border-2 border-dashed border-slate-400 px-6 py-4 text-slate-700">
            <summary class="cursor-pointer font-semibold">Mode uji (tidak tampil di production)</summary>
            <form action="{{ route('dashboard.user.booking.simulate-payment', $booking) }}" method="POST" class="mt-3 space-y-2">
                @csrf
                <p class="text-sm">Menandai pesanan ini lunas tanpa membayar, sama seperti webhook Midtrans mengirim status settlement.</p>
                <button type="submit" class="inline-flex items-center min-h-11 px-4 rounded-lg bg-slate-800 font-bold text-white hover:bg-slate-900">Tandai sudah dibayar</button>
            </form>
        </details>
    @endunless
</div>

@include('dashboard.user.partials.qr-script')

<script>
function paymentHandler(config) {
    return {
        remainingSeconds: config.remainingSeconds,
        statusCheckUrl: config.statusCheckUrl,
        successUrl: config.successUrl,
        isChecking: false,
        feedback: '',

        init() {
            const timer = setInterval(() => {
                this.remainingSeconds = Math.max(0, this.remainingSeconds - 1);

                if (this.remainingSeconds === 0) {
                    clearInterval(timer);
                }
            }, 1000);

            setInterval(() => this.fetchStatus().catch(() => {}), 5000);
        },

        formatTimer(totalSeconds) {
            const seconds = Math.max(0, Math.floor(totalSeconds));

            return String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
        },

        async copyText(text, message) {
            try {
                await navigator.clipboard.writeText(text);
                this.feedback = message;
            } catch (error) {
                this.feedback = 'Browser menolak menyalin. Pilih teksnya lalu salin manual.';
            }
        },

        async fetchStatus() {
            const response = await fetch(this.statusCheckUrl, { headers: { Accept: 'application/json' } });
            const data = await response.json();

            if (data.is_paid) {
                window.location.href = this.successUrl;
            }

            return data;
        },

        async checkStatusManual() {
            this.isChecking = true;

            try {
                const data = await this.fetchStatus();

                if (!data.is_paid) {
                    this.feedback = 'Pembayaran belum masuk. Transfer bank bisa butuh 1 sampai 2 menit, halaman ini akan pindah sendiri.';
                }
            } catch (error) {
                this.feedback = 'Status belum bisa dicek. Periksa koneksi internet lalu coba lagi.';
            } finally {
                this.isChecking = false;
            }
        },
    };
}
</script>
@endsection
