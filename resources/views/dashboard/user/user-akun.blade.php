@extends('layouts.dashboard')

@section('page-title', 'Akun')

@php
    $fieldClass = 'w-full min-h-11 rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700/15';
    $labelClass = 'block text-sm font-semibold text-slate-800';
    $whatsappBaseUrl = \App\Models\AppSetting::whatsappUrl();
    $companyName = \App\Models\AppSetting::companyName();
    $userInitial = strtoupper(mb_substr(trim((string) $user->name) ?: 'U', 0, 1));
    $helpBookings = $recentBookings->map(fn ($booking) => [
        'id' => (string) $booking->id,
        'code' => $booking->booking_code,
        'route' => ($booking->schedule?->route?->origin?->name ?? '-').' ke '.($booking->schedule?->route?->destination?->name ?? '-'),
        'date' => $booking->schedule?->departureCarbon()->locale('id')->isoFormat('D MMM YYYY, HH:mm').' WIB',
    ])->values();
@endphp

@section('content')
<div class="space-y-6 max-w-4xl mx-auto"
     x-data="{ editProfileOpen: {{ $errors->hasAny(['name', 'email', 'phone']) ? 'true' : 'false' }} }">

    @include('dashboard.user.partials.flash')

    {{-- KARTU PROFIL UTAMA --}}
    <section aria-labelledby="profil" class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4 min-w-0">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-800 text-2xl font-extrabold text-white">
                    {{ $userInitial }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500">Profil Saya</p>
                    <h1 id="profil" class="text-2xl sm:text-3xl font-extrabold tracking-tight text-brand-900 truncate">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-slate-600 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                        <span>{{ $user->email }}</span>
                        <span class="text-slate-300" aria-hidden="true">&bull;</span>
                        <span>{{ $user->phone ?: 'Nomor HP belum diisi' }}</span>
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="editProfileOpen = !editProfileOpen"
                    class="shrink-0 inline-flex items-center justify-center gap-2 min-h-10 px-4 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-800 hover:border-brand-700 hover:text-brand-800 transition cursor-pointer">
                <span x-text="editProfileOpen ? 'Tutup' : 'Ubah profil'">Ubah profil</span>
            </button>
        </div>

        <div x-show="editProfileOpen" x-cloak class="border-t border-slate-200 bg-slate-50/60 p-6 sm:p-7">
            <form action="{{ route('dashboard.user.profil.update') }}" method="POST" class="grid sm:grid-cols-2 gap-4">
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
                    <label for="profile-phone" class="{{ $labelClass }}">Nomor HP (WhatsApp) <span class="text-xs font-normal text-slate-500">(angka saja)</span></label>
                    <input id="profile-phone" type="tel" name="phone" maxlength="20" autocomplete="tel" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="{{ $fieldClass }}">
                </div>
                <div class="sm:col-span-2 flex items-center gap-3 pt-1">
                    <button type="submit" class="lp-btn lp-btn--green">Simpan profil</button>
                    <button type="button" @click="editProfileOpen = false" class="text-sm font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 cursor-pointer">Batal</button>
                </div>
            </form>
        </div>
    </section>

    {{-- GRID 2 KOLOM SEIMBANG --}}
    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-6">
            {{-- DOMPET VOUCHER --}}
            <section aria-labelledby="voucher" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="voucher" class="text-lg font-extrabold text-brand-900">Dompet Voucher</h2>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $activeVouchersCount }} voucher masih berlaku.</p>
                    </div>
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-900">{{ $activeVouchersCount }} aktif</span>
                </div>
                <a href="{{ route('dashboard.user.user-promo') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-900 underline underline-offset-4">
                    <span>Lihat dan simpan voucher</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </section>

            {{-- BANTUAN CS --}}
            <section aria-labelledby="bantuan" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4" x-data="helpMessage({ bookings: @js($helpBookings), baseUrl: @js($whatsappBaseUrl ?? ''), userName: @js($user->name), companyName: @js($companyName) })">
                <div>
                    <h2 id="bantuan" class="text-lg font-extrabold text-brand-900">Bantuan</h2>
                    <p class="mt-0.5 text-sm text-slate-600">Hubungi CS jika ada kendala pesanan atau perjalanan.</p>
                </div>

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
                    <p class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 text-xs leading-relaxed text-slate-700 whitespace-pre-line" x-text="message"></p>
                    <a :href="url" target="_blank" rel="noopener" class="lp-btn lp-btn--green w-full text-sm">Kirim lewat WhatsApp</a>
                @else
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                        <p class="text-sm font-bold text-slate-900">Nomor WhatsApp CS belum diisi</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">Sementara itu, jawaban soal pembatalan, refund, dan boarding ada di <a href="{{ route('home') }}#faq" class="font-semibold text-brand-700 underline underline-offset-2">halaman bantuan</a>.</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- KEAMANAN --}}
        <section aria-labelledby="keamanan" class="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
            <div>
                <h2 id="keamanan" class="text-lg font-extrabold text-brand-900">Keamanan</h2>
                <p class="mt-0.5 text-sm text-slate-600">Atur kata sandi dan sesi masuk akunmu.</p>
            </div>

            @if ($user->google_id)
                <p class="rounded-lg bg-slate-50 border border-slate-200 px-3.5 py-2.5 text-xs text-slate-700">
                    Akun ini masuk dengan Google (<b>{{ $user->email }}</b>).
                </p>
            @endif

            <details class="rounded-xl border border-slate-200" @if ($errors->hasAny(['current_password', 'password'])) open @endif>
                <summary class="cursor-pointer list-none flex items-center justify-between min-h-11 px-4 py-2.5 text-sm font-semibold text-brand-800 hover:bg-slate-50">
                    <span>Ganti password</span>
                    <span class="text-slate-400" aria-hidden="true">▾</span>
                </summary>
                <form action="{{ route('dashboard.user.profil.password') }}" method="POST" class="space-y-3.5 border-t border-slate-200 p-4">
                    @csrf
                    @if ($user->google_id)
                        <p class="text-xs text-slate-600">Belum pernah membuat password? Keluar dulu, lalu pakai <b>Lupa password</b> di halaman masuk.</p>
                    @endif
                    <div class="space-y-1">
                        <label for="current_password" class="{{ $labelClass }}">Password sekarang</label>
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="{{ $fieldClass }}">
                    </div>
                    <div class="space-y-1">
                        <label for="new_password" class="{{ $labelClass }}">Password baru</label>
                        <input id="new_password" type="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="new_password-hint" class="{{ $fieldClass }}">
                        <p id="new_password-hint" class="text-xs text-slate-500">Minimal 8 karakter.</p>
                    </div>
                    <div class="space-y-1">
                        <label for="new_password_confirmation" class="{{ $labelClass }}">Ulangi password baru</label>
                        <input id="new_password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="{{ $fieldClass }}">
                    </div>
                    <button type="submit" class="lp-btn lp-btn--green w-full text-sm">Simpan password</button>
                </form>
            </details>

            <form action="{{ route('user.logout') }}" method="POST" class="pt-1">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center min-h-11 px-4 rounded-xl border border-rose-300 text-sm font-bold text-rose-700 hover:bg-rose-50 transition cursor-pointer">
                    Keluar dari akun
                </button>
            </form>
        </section>
    </div>
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
