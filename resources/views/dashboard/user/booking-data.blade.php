@extends('layouts.dashboard')

@section('page-title', 'Data pemesan')

@php
    $account = auth()->user();
    $returnUrl = url()->current();
    $holdUntil = $booking->expired_at?->copy()->timezone(config('app.timezone'));
    $channelGroups = [
        'Transfer virtual account' => [
            'bca' => ['BCA', 'm-BCA, myBCA, KlikBCA, atau ATM BCA'],
            'mandiri' => ['Mandiri', "Livin' by Mandiri atau ATM, pakai kode perusahaan dan nomor tagihan"],
            'bni' => ['BNI', 'BNI Mobile, internet banking, atau ATM BNI'],
            'bri' => ['BRI (BRIVA)', 'BRImo atau ATM BRI'],
            'bsi' => ['BSI', 'BSI Mobile atau ATM BSI'],
            'seabank' => ['SeaBank', 'Transfer dari aplikasi SeaBank ke virtual account Permata'],
        ],
        'QRIS dan e-wallet' => [
            'qris' => ['QRIS', 'Pindai dengan aplikasi bank atau e-wallet apa pun'],
            'gopay' => ['GoPay', 'Dibuka di aplikasi Gojek atau GoPay'],
            'shopeepay' => ['ShopeePay', 'Dibuka di aplikasi Shopee'],
        ],
    ];
    $selectedChannel = old('payment_channel', 'bca');
    $inputClass = 'w-full min-h-12 rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 text-slate-900 focus:border-brand-500';
@endphp

@section('content')
<div class="space-y-8" x-data="bookingDataHandler({
    remainingSeconds: {{ $remainingSeconds }},
    bookingId: {{ $booking->id }},
    subtotal: {{ (float) $booking->subtotal }},
    csrfToken: '{{ csrf_token() }}'
})">

    @include('dashboard.user.partials.booking-steps', ['current' => 2])

    @include('dashboard.user.partials.flash')

    <header class="space-y-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Siapa yang berangkat?</h1>
        @include('dashboard.user.partials.trip-summary', ['schedule' => $booking->schedule])
    </header>

    {{-- Seat hold: the deadline is the one thing that can undo this page, so it stays visible --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border-2 border-amber-600 bg-amber-50 px-5 py-4 text-amber-950" role="timer" aria-live="off">
        <p x-show="remainingSeconds > 0">
            Kursi <b>{{ $booking->passengerSeatNumbers() }}</b> ditahan untukmu sampai
            <b>{{ $holdUntil?->format('H:i') }} WIB</b>.
        </p>
        <p class="font-mono text-2xl font-extrabold tabular-nums" x-show="remainingSeconds > 0" x-text="formatTimer(remainingSeconds)"></p>
        <p x-show="remainingSeconds <= 0" x-cloak>
            <b>Waktu tahan kursi sudah habis.</b> Kursinya dilepas untuk penumpang lain.
            <a href="{{ route('dashboard.user.booking.pilih-kursi', ['schedule' => $booking->schedule_id, 'passengers' => $booking->total_passenger]) }}" class="font-semibold underline underline-offset-2">Pilih kursi lagi</a>
        </p>
    </div>

    <form action="{{ route('dashboard.user.booking.save-passenger', $booking) }}" method="POST" id="form-booking-data" class="grid lg:grid-cols-[minmax(0,1fr)_340px] gap-8 items-start">
        @csrf

        <div class="space-y-10">

            {{-- Guests can book; logging in is offered, never required --}}
            @guest
                <section class="rounded-xl border border-brand-300 bg-brand-50 p-6" aria-labelledby="tanpa-akun">
                    <h2 id="tanpa-akun" class="text-lg font-extrabold text-brand-900">Tidak wajib punya akun</h2>
                    <p class="mt-2 text-slate-700">
                        Cukup isi nama, nomor HP, dan email di bawah, lalu bayar. Setelah pembayaran masuk, e-ticket kami kirim ke email itu.
                    </p>
                    <p class="mt-2 text-slate-700">
                        Kalau kamu masuk dulu, pesanan ini tersimpan di akunmu: tiket bisa dibuka lagi kapan saja, pembatalan bisa diajukan sendiri, dan data penumpang tidak perlu diketik ulang lain kali.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <a href="{{ route('auth.google.redirect', ['redirect' => $returnUrl]) }}"
                           class="inline-flex items-center gap-3 min-h-11 px-4 rounded-lg border border-slate-300 bg-white font-semibold text-slate-900 hover:border-slate-500">
                            @include('auth.partials.google-mark')
                            Masuk dengan Google
                        </a>
                        <a href="{{ route('user.login', ['redirect' => $returnUrl]) }}" class="inline-flex items-center min-h-11 px-4 rounded-lg border-2 border-brand-800 font-semibold text-brand-800 hover:bg-white">
                            Masuk dengan email
                        </a>
                        <a href="{{ route('register', ['redirect' => $returnUrl]) }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">
                            Belum punya akun? Daftar
                        </a>
                    </div>
                </section>
            @endguest

            <section class="space-y-4" aria-labelledby="pemesan">
                <div>
                    <h2 id="pemesan" class="text-xl font-extrabold text-brand-900">Kontak pemesan</h2>
                    <p class="text-slate-600">E-ticket dan kabar soal perjalanan ini dikirim ke kontak ini.</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="booker_name" class="block font-semibold text-slate-800">Nama lengkap</label>
                        <input type="text" id="booker_name" name="booker_name" required maxlength="150" autocomplete="name"
                               value="{{ old('booker_name', $booking->contact_name ?? $account?->name) }}"
                               placeholder="Nama sesuai KTP"
                               @error('booker_name') aria-invalid="true" aria-describedby="booker_name-error" @enderror
                               class="{{ $inputClass }}">
                        @error('booker_name')
                            <p id="booker_name-error" class="text-sm font-semibold text-rose-800">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="booker_phone" class="block font-semibold text-slate-800">Nomor HP (WhatsApp)</label>
                        <input type="tel" id="booker_phone" name="booker_phone" required maxlength="25" autocomplete="tel" inputmode="tel"
                               value="{{ old('booker_phone', $booking->contact_phone ?? $account?->phone) }}"
                               placeholder="08xxxxxxxxxx"
                               @error('booker_phone') aria-invalid="true" aria-describedby="booker_phone-error" @enderror
                               class="{{ $inputClass }}">
                        @error('booker_phone')
                            <p id="booker_phone-error" class="text-sm font-semibold text-rose-800">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="booker_email" class="block font-semibold text-slate-800">Email</label>
                        <input type="email" id="booker_email" name="booker_email" required maxlength="150" autocomplete="email"
                               value="{{ old('booker_email', $booking->contact_email ?? $account?->email) }}"
                               placeholder="email@contoh.com"
                               aria-describedby="booker_email-hint @error('booker_email') booker_email-error @enderror"
                               @error('booker_email') aria-invalid="true" @enderror
                               class="{{ $inputClass }}">
                        <p id="booker_email-hint" class="text-sm text-slate-600">E-ticket dikirim ke sini setelah lunas.</p>
                        @error('booker_email')
                            <p id="booker_email-error" class="text-sm font-semibold text-rose-800">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="space-y-4" aria-labelledby="penumpang">
                <div>
                    <h2 id="penumpang" class="text-xl font-extrabold text-brand-900">Penumpang</h2>
                    <p class="text-slate-600">Tulis nama sesuai kartu identitas. Petugas mencocokkannya saat naik.</p>
                </div>

                <ol class="space-y-4">
                    @foreach ($booking->passengers as $index => $passenger)
                        <li class="rounded-xl border border-slate-300 bg-white p-5 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h3 class="font-extrabold text-slate-900">Kursi {{ $passenger->seat?->seat_number ?? ($index + 1) }}</h3>
                                @if (count($quickPassengers) > 0)
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        <span class="text-slate-600">Isi dengan:</span>
                                        @foreach ($quickPassengers as $saved)
                                            <button type="button"
                                                    @click="fillPassenger({{ $passenger->id }}, @js($saved['name']), @js($saved['phone']))"
                                                    class="min-h-11 px-3 rounded-lg border border-slate-300 bg-white font-semibold text-slate-800 hover:border-brand-600">
                                                {{ \Illuminate\Support\Str::limit($saved['name'], 18) }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <input type="hidden" name="passengers[{{ $index }}][id]" value="{{ $passenger->id }}">

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="p_name_{{ $passenger->id }}" class="block font-semibold text-slate-800">Nama penumpang</label>
                                    <input type="text" name="passengers[{{ $index }}][name]" id="p_name_{{ $passenger->id }}" required maxlength="150"
                                           value="{{ old('passengers.'.$index.'.name', $passenger->name) }}"
                                           placeholder="Nama lengkap"
                                           class="{{ $inputClass }}">
                                </div>
                                <div class="space-y-1.5">
                                    <label for="p_phone_{{ $passenger->id }}" class="block font-semibold text-slate-800">Nomor HP <span class="font-normal text-slate-600">(boleh kosong)</span></label>
                                    <input type="tel" name="passengers[{{ $index }}][phone]" id="p_phone_{{ $passenger->id }}" maxlength="25" inputmode="tel"
                                           value="{{ old('passengers.'.$index.'.phone', $passenger->phone) }}"
                                           placeholder="08xxxxxxxxxx"
                                           class="{{ $inputClass }}">
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="p_note_{{ $passenger->id }}" class="block font-semibold text-slate-800">Catatan untuk sopir <span class="font-normal text-slate-600">(boleh kosong)</span></label>
                                    <input type="text" name="passengers[{{ $index }}][special_request]" id="p_note_{{ $passenger->id }}" maxlength="255"
                                           value="{{ old('passengers.'.$index.'.special_request', $passenger->special_request) }}"
                                           placeholder="Misalnya bawa koper besar"
                                           class="{{ $inputClass }}">
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            <section class="space-y-4" aria-labelledby="metode-bayar">
                <div>
                    <h2 id="metode-bayar" class="text-xl font-extrabold text-brand-900">Cara bayar</h2>
                    <p class="text-slate-600">Nomor virtual account atau kode QR muncul di langkah berikutnya. Pembayaran diproses Midtrans.</p>
                </div>

                @foreach ($channelGroups as $groupLabel => $channels)
                    <fieldset class="space-y-2">
                        <legend class="mb-2 font-semibold text-slate-800">{{ $groupLabel }}</legend>
                        <div class="grid sm:grid-cols-2 gap-2">
                            @foreach ($channels as $code => [$label, $hint])
                                @continue(! isset($paymentChannels[$code]))
                                <label class="flex items-start gap-3 min-h-11 rounded-lg border border-slate-300 bg-white px-4 py-3 cursor-pointer hover:border-brand-600 has-[:checked]:border-2 has-[:checked]:border-brand-700 has-[:checked]:bg-brand-50">
                                    <input type="radio" name="payment_channel" value="{{ $code }}" class="mt-1 w-4 h-4 accent-[#0C5A3E]" @checked($selectedChannel === $code)>
                                    <span class="min-w-0">
                                        <img src="{{ asset('images/payments/'.$code.'.svg') }}" alt="" class="h-6 w-auto max-w-28 object-contain object-left mb-1.5" loading="lazy">
                                        <span class="block font-bold text-slate-900">{{ $label }}</span>
                                        <span class="block text-sm text-slate-600">{{ $hint }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </section>
        </div>

        <aside class="lg:sticky lg:top-24 space-y-6 rounded-xl border border-slate-300 bg-white p-6" aria-labelledby="ringkasan-bayar">
            <h2 id="ringkasan-bayar" class="text-xl font-extrabold text-brand-900">Ringkasan</h2>

            <div class="space-y-2">
                <label for="promo_input" class="block font-semibold text-slate-800">Kode promo <span class="font-normal text-slate-600">(kalau ada)</span></label>
                <div class="flex gap-2">
                    <input type="text" id="promo_input" x-model="promoInput" @keydown.enter.prevent="applyPromo()" autocomplete="off"
                           class="{{ $inputClass }} uppercase font-bold">
                    <button type="button" @click="applyPromo()" class="min-h-12 px-4 rounded-lg border-2 border-brand-800 font-bold text-brand-800 hover:bg-brand-50">Pakai</button>
                </div>
                <p class="text-sm font-semibold" :class="promoSuccess ? 'text-brand-700' : 'text-rose-800'" role="status" aria-live="polite" x-text="promoMessage"></p>
                <input type="hidden" name="promo_code" :value="appliedPromoCode">
            </div>

            <dl class="space-y-2 border-t border-slate-200 pt-4">
                <div class="flex justify-between gap-4 text-slate-600">
                    <dt>{{ $booking->total_passenger }} kursi</dt>
                    <dd x-text="formatRupiah(subtotal)">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-brand-700" x-show="discount > 0" x-cloak>
                    <dt>Promo <span class="font-bold" x-text="appliedPromoCode"></span></dt>
                    <dd x-text="'- ' + formatRupiah(discount)"></dd>
                </div>
                <div class="flex justify-between gap-4 text-lg font-extrabold text-slate-900">
                    <dt>Total bayar</dt>
                    <dd x-text="formatRupiah(totalPrice)">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</dd>
                </div>
            </dl>

            <button type="submit" class="lp-btn lp-btn--primary w-full" :disabled="remainingSeconds <= 0">Lanjut ke pembayaran</button>
        </aside>
    </form>
</div>

<script>
function bookingDataHandler(config) {
    return {
        remainingSeconds: config.remainingSeconds,
        bookingId: config.bookingId,
        subtotal: config.subtotal,
        discount: 0,
        totalPrice: config.subtotal,
        promoInput: '',
        appliedPromoCode: '',
        promoMessage: '',
        promoSuccess: false,
        csrfToken: config.csrfToken,

        init() {
            const timer = setInterval(() => {
                this.remainingSeconds = Math.max(0, this.remainingSeconds - 1);

                if (this.remainingSeconds === 0) {
                    clearInterval(timer);
                    window.location.href = @js(auth()->check() ? route('dashboard.user.search', ['expired' => 1]) : route('search', ['expired' => 1]));
                }
            }, 1000);
        },

        formatTimer(totalSeconds) {
            const seconds = Math.max(0, Math.floor(totalSeconds));

            return String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },

        fillPassenger(id, name, phone) {
            const nameEl = document.getElementById('p_name_' + id);
            const phoneEl = document.getElementById('p_phone_' + id);
            if (nameEl) {
                nameEl.value = name;
                nameEl.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (phoneEl) {
                phoneEl.value = phone || '';
                phoneEl.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },

        resetPromo(message) {
            this.promoSuccess = false;
            this.promoMessage = message;
            this.appliedPromoCode = '';
            this.discount = 0;
            this.totalPrice = this.subtotal;
        },

        async applyPromo() {
            if (!this.promoInput.trim()) {
                this.resetPromo('Tulis kodenya dulu.');
                return;
            }

            try {
                const response = await fetch(@js(route('dashboard.user.booking.validate-promo')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ booking_id: this.bookingId, promo_code: this.promoInput.trim() }),
                });
                const data = await response.json();

                if (response.ok && data.valid) {
                    this.promoSuccess = true;
                    this.promoMessage = data.message;
                    this.appliedPromoCode = data.code;
                    this.discount = data.discount;
                    this.totalPrice = data.new_total;
                } else {
                    this.resetPromo(data.message || 'Kode ini tidak bisa dipakai untuk pesanan ini.');
                }
            } catch (error) {
                this.resetPromo('Kode belum bisa dicek. Periksa koneksi internet lalu coba lagi.');
            }
        },
    };
}
</script>
@endsection
