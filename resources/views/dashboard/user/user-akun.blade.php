@extends('layouts.dashboard')

@section('page-title', 'Akun')

@php
    $fieldClass = 'w-full min-h-12 rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 text-slate-900';
    $labelClass = 'block font-semibold text-slate-900';
    $whatsappBaseUrl = \App\Models\AppSetting::whatsappUrl();
    $companyName = \App\Models\AppSetting::companyName();
    $helpBookings = $recentBookings->map(fn ($booking) => [
        'id' => (string) $booking->id,
        'code' => $booking->booking_code,
        'route' => ($booking->schedule?->route?->origin?->name ?? '-').' ke '.($booking->schedule?->route?->destination?->name ?? '-'),
        'date' => $booking->schedule?->departureCarbon()->locale('id')->isoFormat('D MMM YYYY, HH:mm').' WIB',
    ])->values();
@endphp

@section('content')
<div class="space-y-10 max-w-3xl">

    @include('dashboard.user.partials.flash')

    {{-- Profile --}}
    <section aria-labelledby="profil" class="space-y-3">
        <h1 id="profil" class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">{{ $user->name }}</h1>
        <p class="text-slate-700">
            {{ $user->email }}<br>
            {{ $user->phone ?: 'Nomor HP belum diisi' }}
        </p>

        <details class="rounded-xl border border-slate-300 bg-white" @if ($errors->hasAny(['name', 'email', 'phone'])) open @endif>
            <summary class="cursor-pointer list-none flex items-center min-h-11 px-5 py-3 font-semibold text-brand-800">Ubah nama, email, atau nomor HP</summary>
            <form action="{{ route('dashboard.user.profil.update') }}" method="POST" class="grid sm:grid-cols-2 gap-4 border-t border-slate-200 p-5">
                @csrf
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="profile-name" class="{{ $labelClass }}">Nama lengkap</label>
                    <input id="profile-name" type="text" name="name" required maxlength="255" autocomplete="name" value="{{ old('name', $user->name) }}" class="{{ $fieldClass }}">
                </div>
                <div class="space-y-1.5">
                    <label for="profile-email" class="{{ $labelClass }}">Email</label>
                    <input id="profile-email" type="email" name="email" required maxlength="255" autocomplete="email" value="{{ old('email', $user->email) }}" class="{{ $fieldClass }}">
                </div>
                <div class="space-y-1.5">
                    <label for="profile-phone" class="{{ $labelClass }}">Nomor HP (WhatsApp)</label>
                    <input id="profile-phone" type="tel" name="phone" maxlength="25" autocomplete="tel" inputmode="tel" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="lp-btn lp-btn--green">Simpan profil</button>
                </div>
            </form>
        </details>
    </section>

    {{-- Saved passengers: filled once, offered again at checkout --}}
    <section aria-labelledby="penumpang-tersimpan" class="space-y-4">
        <div>
            <h2 id="penumpang-tersimpan" class="text-2xl font-extrabold text-brand-900">Daftar Penumpang Tersimpan</h2>
            <p class="text-slate-600">Orang yang sering ikut bepergian. Saat memesan, nama dan nomor HP-nya tinggal dipilih.</p>
        </div>

        @if ($savedPassengers->isEmpty())
            <p class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-6 text-slate-700">Belum ada penumpang tersimpan.</p>
        @else
            <ul class="divide-y divide-slate-200 border-y border-slate-200">
                @foreach ($savedPassengers as $savedPassenger)
                    <li class="py-4" id="saved-passenger-{{ $savedPassenger->id }}">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900">
                                    {{ $savedPassenger->name }}
                                    @if ($savedPassenger->is_primary)
                                        <span class="ml-1 px-2 py-0.5 rounded-md bg-brand-100 text-sm font-semibold text-brand-800">Utama</span>
                                    @endif
                                </p>
                                <p class="text-slate-600">HP {{ $savedPassenger->phone ?: '-' }}, NIK {{ $savedPassenger->maskedNik() }}</p>
                            </div>
                            <form action="{{ route('dashboard.user.saved-passenger.destroy', $savedPassenger) }}" method="POST"
                                  onsubmit="return confirm(@js('Hapus '.$savedPassenger->name.' dari daftar?'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center min-h-11 text-sm font-semibold text-slate-600 hover:text-rose-800">Hapus</button>
                            </form>
                        </div>

                        <details class="mt-1">
                            <summary class="cursor-pointer list-none inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Ubah</summary>
                            <form action="{{ route('dashboard.user.saved-passenger.update', $savedPassenger) }}" method="POST" class="mt-3 grid sm:grid-cols-2 gap-4 rounded-xl border border-slate-300 bg-white p-5">
                                @csrf
                                @method('PUT')
                                @include('dashboard.user.partials.saved-passenger-fields', ['savedPassenger' => $savedPassenger, 'prefix' => 'edit-'.$savedPassenger->id])
                                <div class="sm:col-span-2">
                                    <button type="submit" class="lp-btn lp-btn--green">Simpan perubahan</button>
                                </div>
                            </form>
                        </details>
                    </li>
                @endforeach
            </ul>
        @endif

        <details class="rounded-xl border border-slate-300 bg-white">
            <summary class="cursor-pointer list-none flex items-center min-h-11 px-5 py-3 font-semibold text-brand-800">Tambah penumpang</summary>
            <form action="{{ route('dashboard.user.saved-passenger.store') }}" method="POST" class="grid sm:grid-cols-2 gap-4 border-t border-slate-200 p-5">
                @csrf
                @include('dashboard.user.partials.saved-passenger-fields', ['savedPassenger' => null, 'prefix' => 'new'])
                <div class="sm:col-span-2">
                    <button type="submit" class="lp-btn lp-btn--green">Simpan penumpang</button>
                </div>
            </form>
        </details>
    </section>

    {{-- Vouchers live on their own page; here only the count and the way there --}}
    <section aria-labelledby="voucher" class="flex flex-wrap items-center justify-between gap-3 border-y border-slate-200 py-5">
        <div>
            <h2 id="voucher" class="text-xl font-extrabold text-brand-900">Dompet Voucher</h2>
            <p class="text-slate-600">{{ $activeVouchersCount }} voucher masih berlaku.</p>
        </div>
        <a href="{{ route('dashboard.user.user-promo') }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">Lihat dan simpan voucher</a>
    </section>

    {{-- Help: the WhatsApp message carries the booking code so CS does not have to ask --}}
    <section aria-labelledby="bantuan" class="space-y-4" x-data="helpMessage({ bookings: @js($helpBookings), baseUrl: @js($whatsappBaseUrl ?? ''), userName: @js($user->name), companyName: @js($companyName) })">
        <h2 id="bantuan" class="text-2xl font-extrabold text-brand-900">Bantuan</h2>

        @if ($whatsappBaseUrl)
            <div class="space-y-1.5">
                <label for="help-booking" class="{{ $labelClass }}">Pesanan yang ditanyakan</label>
                <select id="help-booking" x-model="selectedId" class="{{ $fieldClass }}">
                    <option value="">Pertanyaan umum, bukan soal pesanan tertentu</option>
                    @foreach ($helpBookings as $helpBooking)
                        <option value="{{ $helpBooking['id'] }}">{{ $helpBooking['code'] }}, {{ $helpBooking['route'] }}</option>
                    @endforeach
                </select>
            </div>
            <p class="rounded-lg bg-slate-100 px-4 py-3 text-slate-700 whitespace-pre-line" x-text="message"></p>
            <a :href="url" target="_blank" rel="noopener" class="lp-btn lp-btn--green">Kirim lewat WhatsApp</a>
        @else
            <div class="rounded-xl border border-slate-300 bg-white px-5 py-4">
                <p class="font-bold text-slate-900">Nomor WhatsApp CS belum diisi</p>
                <p class="mt-1 text-slate-600">Sementara itu, jawaban soal pembatalan, refund, dan boarding ada di <a href="{{ route('home') }}#faq" class="font-semibold text-brand-700 underline underline-offset-2">halaman bantuan</a>.</p>
            </div>
        @endif
    </section>

    {{-- Password and sign out --}}
    <section aria-labelledby="keamanan" class="space-y-4">
        <h2 id="keamanan" class="text-2xl font-extrabold text-brand-900">Keamanan</h2>

        @if ($user->google_id)
            <p class="text-slate-700">Akun ini masuk dengan Google ({{ $user->email }}).</p>
        @endif

        <details class="rounded-xl border border-slate-300 bg-white" @if ($errors->hasAny(['current_password', 'password'])) open @endif>
            <summary class="cursor-pointer list-none flex items-center min-h-11 px-5 py-3 font-semibold text-brand-800">Ganti password</summary>
            <form action="{{ route('dashboard.user.profil.password') }}" method="POST" class="space-y-4 border-t border-slate-200 p-5">
                @csrf
                @if ($user->google_id)
                    <p class="text-slate-600">Belum pernah membuat password? Keluar dulu, lalu pakai <b>Lupa password</b> di halaman masuk.</p>
                @endif
                <div class="space-y-1.5">
                    <label for="current_password" class="{{ $labelClass }}">Password sekarang</label>
                    <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="{{ $fieldClass }}">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="new_password" class="{{ $labelClass }}">Password baru</label>
                        <input id="new_password" type="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="new_password-hint" class="{{ $fieldClass }}">
                        <p id="new_password-hint" class="text-sm text-slate-600">Minimal 8 karakter.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label for="new_password_confirmation" class="{{ $labelClass }}">Ulangi password baru</label>
                        <input id="new_password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="{{ $fieldClass }}">
                    </div>
                </div>
                <button type="submit" class="lp-btn lp-btn--green">Simpan password</button>
            </form>
        </details>

        <form action="{{ route('user.logout') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center min-h-11 px-4 rounded-lg border-2 border-rose-800 font-bold text-rose-800 hover:bg-rose-50">Keluar dari akun</button>
        </form>
    </section>
</div>

<script>
function helpMessage(config) {
    return {
        selectedId: config.bookings[0]?.id ?? '',

        get message() {
            const booking = config.bookings.find((item) => item.id === this.selectedId);

            if (!booking) {
                return `Halo CS ${config.companyName}, saya ${config.userName} mau bertanya soal akun saya.`;
            }

            return `Halo CS ${config.companyName}, saya butuh bantuan untuk pesanan ini:\nKode booking: ${booking.code}\nRute: ${booking.route}\nJadwal: ${booking.date}\n\nKendala: `;
        },

        get url() {
            return config.baseUrl ? config.baseUrl.split('?')[0] + '?text=' + encodeURIComponent(this.message) : '';
        },
    };
}
</script>
@endsection
