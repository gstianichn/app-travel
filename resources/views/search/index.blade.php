@extends('layouts.app')

@php
    $cityOf = fn ($location) => $location->city ?: $location->name;
    $searchDate = $searchPerformed && ! $errors->any() && request('departure_date')
        ? \Illuminate\Support\Carbon::parse(request('departure_date'))->locale('id')
        : null;
    $hasRoute = $searchDate && $originLoc && $destLoc;
@endphp

@section('title', $hasRoute ? $cityOf($originLoc).' ke '.$cityOf($destLoc).' - TravelKu' : 'Cari Travel - TravelKu')

@section('body-class', 'is-landing is-search')

@push('head')
    @fonts('overpass')
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
@endpush

@section('content')

{{-- Short version of the landing hero: same hills, same parallax, so the search feels like the same trip --}}
<section class="sr-band" data-scene="exit" aria-labelledby="sr-title">
    <div class="sr-band-scene" aria-hidden="true">
        <div class="lp-sun sr-sun" data-depth="0.5"></div>
        <svg class="lp-layer sr-layer--far" data-depth="0.4" viewBox="0 0 1440 400" preserveAspectRatio="none">
            <path fill="#B7D0C2" d="M0 300C160 262 300 236 430 242s210-34 330-92c80-40 140-82 190-88h30c50 8 100 46 140 88 80 52 200 62 320 40V400H0z" />
        </svg>
        <svg class="lp-layer sr-layer--mid" data-depth="0.25" viewBox="0 0 1440 300" preserveAspectRatio="none">
            <path fill="#5E9D80" d="M0 120C180 60 360 70 520 120s240 30 360-10c140-50 360-40 560 20V300H0z" />
        </svg>
        <svg class="lp-layer sr-layer--front" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="var(--lp-paper)" d="M0 70C300 30 600 40 900 70s400 10 540-10V120H0z" />
        </svg>
    </div>

    <div class="container sr-band-copy" data-depth="0.08">
        @if ($hasRoute)
            <p class="sr-band-kicker">Jadwal {{ $searchDate->isoFormat('dddd, D MMMM YYYY') }}</p>
            <h1 id="sr-title">{{ $cityOf($originLoc) }} <span>ke</span> {{ $cityOf($destLoc) }}</h1>
        @else
            <h1 id="sr-title">Cari jadwal travel</h1>
            <p class="sr-band-lead">Pilih titik berangkat, tujuan, dan tanggal. Setiap jadwal menampilkan jam, harga, dan sisa kursinya.</p>
        @endif
    </div>
</section>

@include('components.search-ticket', [
    'heading' => $hasRoute ? 'Ubah pencarian' : 'Mau berangkat ke mana?',
    'selected' => [
        'origin'         => request('origin'),
        'destination'    => request('destination'),
        'departure_date' => request('departure_date'),
    ],
])

<section class="sr-results" id="hasil" aria-label="Hasil pencarian">
    <div class="container">

        @if (session('error') || request('expired'))
            <div class="sr-alert" role="alert" style="border-left-color: #e11d48; margin-bottom: 1.5rem; background-color: #fff1f2; border-left-width: 4px; padding: 1rem 1.25rem; border-radius: 0.5rem;">
                <p class="sr-alert-title" style="color: #9f1239; font-weight: 700;">Waktu Pemesanan Habis</p>
                <p style="color: #4c0519; margin-top: 0.25rem;">{{ session('error') ?? 'Waktu tahan kursi (15 menit) sudah habis, kursi Anda telah dilepas kembali. Silakan pilih jadwal dan kursi kembali.' }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="sr-alert" role="alert">
                <p class="sr-alert-title">Pencarian belum bisa dijalankan</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($hasRoute && $routeConnected)
            {{-- Date strip: jump to nearby days without retyping the search --}}
            @php
                $stripStart = $searchDate->copy()->subDays(3)->max(now()->startOfDay());
            @endphp
            <nav class="sr-dates" aria-label="Pilih tanggal lain">
                @for ($day = 0; $day < 7; $day++)
                    @php
                        $stripDate = $stripStart->copy()->addDays($day)->locale('id');
                        $isCurrent = $stripDate->isSameDay($searchDate);
                    @endphp
                    <a
                        href="{{ route('search', ['origin' => $originLoc->id, 'destination' => $destLoc->id, 'departure_date' => $stripDate->toDateString()]) }}#hasil"
                        @class(['sr-date', 'is-current' => $isCurrent])
                        @if ($isCurrent) aria-current="date" @endif
                        aria-label="{{ $stripDate->isoFormat('dddd, D MMMM') }}"
                    >
                        <span>{{ $stripDate->isoFormat('ddd') }}</span>
                        <b>{{ $stripDate->isoFormat('D') }}</b>
                        <span>{{ $stripDate->isoFormat('MMM') }}</span>
                    </a>
                @endfor
            </nav>
        @endif

        @if ($hasRoute)
            @if ($schedules->isNotEmpty())
                <div class="sr-results-head">
                    <h2>{{ $schedules->count() }} keberangkatan</h2>
                    <p>
                        Jam dalam WIB. Jam tiba adalah perkiraan, bisa bergeser karena kondisi jalan.
                        @guest
                            Untuk memesan jadwal pilihanmu, silakan <b>Masuk</b> terlebih dahulu atau <b>Daftar Akun Baru</b> jika belum punya akun.
                        @endguest
                    </p>
                </div>

                @guest
                    <div class="sr-guest-banner" role="region" aria-label="Informasi pemesanan tiket" style="margin-bottom: 20px; padding: 16px 20px; border-radius: 14px; border: 1.5px solid #0C7950; background: #E3F2EA; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px;">
                        <div>
                            <p style="color: #0C392F; font-weight: 800; font-size: 1rem;">Sudah menemukan jadwal yang cocok?</p>
                            <p style="color: #2E4A40; font-size: 0.92rem; margin-top: 2px;">Silakan masuk ke akun Anda untuk memilih kursi dan memesan tiket. Belum punya akun? Kami rekomendasikan untuk membuat akun baru terlebih dahulu.</p>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <a href="{{ route('user.login', ['redirect' => request()->fullUrl()]) }}" class="lp-btn lp-btn--green" style="min-height: 42px; padding: 0 18px; font-size: 0.92rem;">Masuk</a>
                            <a href="{{ route('register', ['redirect' => request()->fullUrl()]) }}" class="lp-btn lp-btn--ghost" style="min-height: 42px; padding: 0 18px; font-size: 0.92rem; background: #FFFFFF;">Buat Akun / Register</a>
                        </div>
                    </div>
                @endguest

                <ol class="sr-trips">
                    @foreach ($schedules as $schedule)
                        @php
                            $seatUrl = route('dashboard.user.booking.pilih-kursi', array_filter(['schedule' => $schedule->id, 'passengers' => request('passengers')]));
                            $isLowOnSeats = $schedule->available_seats <= 3;
                            $routeSummary = $schedule->route->origin->name.' ke '.$schedule->route->destination->name.' ('.$schedule->departureCarbon()->format('H:i').' WIB)';
                        @endphp
                        <li class="sr-trip">
                            <div class="sr-trip-times">
                                <span class="sr-time">{{ $schedule->departureCarbon()->format('H:i') }}</span>
                                <span class="sr-time-line" aria-hidden="true"></span>
                                <span class="sr-time sr-time--arrive">
                                    <span class="sr-visually-hidden">perkiraan tiba</span>{{ str_replace(' WIB', '', $schedule->estimatedArrivalTime()) }}
                                </span>
                            </div>

                            <div class="sr-trip-route">
                                <p class="sr-trip-points">
                                    <b>{{ $schedule->route->origin->name }}</b>
                                    <span>ke</span>
                                    <b>{{ $schedule->route->destination->name }}</b>
                                </p>
                                <p class="sr-trip-meta">
                                    {{ $schedule->route->duration }}@if ($schedule->vehicle), {{ $schedule->vehicle->type ?: $schedule->vehicle->name }}@endif
                                </p>
                            </div>

                            <p @class(['sr-seats', 'is-low' => $isLowOnSeats])>
                                {{ $schedule->available_seats }} kursi tersisa
                            </p>

                            <div class="sr-trip-buy">
                                <p class="sr-price">
                                    Rp {{ number_format($schedule->effectivePrice(), 0, ',', '.') }}
                                    <small>per orang</small>
                                </p>
                                @auth
                                    <a href="{{ $seatUrl }}" onclick="window.TravelFeedback?.playAction(false)" class="lp-btn lp-btn--green">Pilih kursi</a>
                                @else
                                    <a
                                        href="{{ $seatUrl }}"
                                        data-guest-book-trigger
                                        data-login-url="{{ route('user.login', ['redirect' => $seatUrl]) }}"
                                        data-register-url="{{ route('register', ['redirect' => $seatUrl]) }}"
                                        data-google-url="{{ route('auth.google.redirect', ['redirect' => $seatUrl]) }}"
                                        data-route-label="{{ $routeSummary }}"
                                        class="lp-btn lp-btn--green"
                                    >Pilih kursi</a>
                                @endauth
                            </div>
                        </li>
                    @endforeach
                </ol>
            @elseif ($routeConnected)
                <div class="sr-empty">
                    <h2>Tidak ada keberangkatan pada tanggal ini</h2>
                    <p>
                        @if ($searchDate->isToday())
                            Keberangkatan hari ini mungkin sudah lewat atau penuh.
                        @else
                            Semua jadwal di tanggal ini sudah penuh.
                        @endif
                        Pilih tanggal lain di atas.
                    </p>
                </div>
            @else
                <div class="sr-empty">
                    <h2>Belum ada rute langsung dari {{ $cityOf($originLoc) }} ke {{ $cityOf($destLoc) }}</h2>

                    @if ($transitHub)
                        <p>Kamu bisa sampai lewat {{ $cityOf($transitHub) }} dengan dua perjalanan:</p>
                        <ol class="sr-transit">
                            <li>
                                <a href="{{ route('search', ['origin' => $originLoc->id, 'destination' => $transitHub->id, 'departure_date' => $searchDate->toDateString()]) }}#hasil">
                                    <span>Perjalanan 1</span>
                                    {{ $cityOf($originLoc) }} ke {{ $cityOf($transitHub) }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('search', ['origin' => $transitHub->id, 'destination' => $destLoc->id, 'departure_date' => $searchDate->toDateString()]) }}#hasil">
                                    <span>Perjalanan 2</span>
                                    {{ $cityOf($transitHub) }} ke {{ $cityOf($destLoc) }}
                                </a>
                            </li>
                        </ol>
                    @elseif ($routeSuggestions->isNotEmpty())
                        <p>Dari {{ $cityOf($originLoc) }}, rute yang sudah jalan:</p>
                    @else
                        <p>Belum ada rute aktif yang berangkat dari {{ $cityOf($originLoc) }}.</p>
                    @endif
                </div>
            @endif
        @endif

        @if (! $hasRoute || (! $routeConnected && ! $transitHub))
            @if ($routeSuggestions->isNotEmpty())
                <div class="sr-suggest">
                    @unless ($hasRoute)
                        <h2>Rute yang bisa dipesan</h2>
                        <p>Pilih satu untuk mengisi pencarian, lalu tentukan tanggalnya.</p>
                    @endunless
                    <ul class="sr-suggest-list">
                        @foreach ($routeSuggestions as $suggestion)
                            @php
                                $fromCity = $cityOf($suggestion->origin);
                                $toCity = $cityOf($suggestion->destination);
                            @endphp
                            <li>
                                <a
                                    href="#cari"
                                    class="sr-suggest-sign"
                                    data-route-fill
                                    data-origin="{{ $suggestion->origin_id }}"
                                    data-destination="{{ $suggestion->destination_id }}"
                                    data-label="{{ $fromCity }} ke {{ $toCity }}"
                                    aria-label="Isi pencarian {{ $fromCity }} ke {{ $toCity }}, mulai {{ $suggestion->formattedPrice() }}"
                                >
                                    <span class="sr-suggest-from">dari {{ $fromCity }}</span>
                                    <span class="sr-suggest-to">{{ $toCity }}</span>
                                    <span class="sr-suggest-price">mulai {{ $suggestion->formattedPrice() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @elseif (! $hasRoute)
                <div class="sr-empty">
                    <h2>Rute sedang disiapkan</h2>
                    <p>Belum ada rute aktif. Begitu admin membuka rute, jadwalnya bisa dicari di sini.</p>
                </div>
            @endif
        @endif
    </div>
</section>

@guest
<div id="guestAuthModal" role="dialog" aria-modal="true" aria-labelledby="guestAuthTitle" hidden style="position: fixed; inset: 0; z-index: 1000; display: none; place-items: center; padding: 16px; background: rgba(12, 57, 47, 0.6); backdrop-filter: blur(4px);">
    <div style="width: 100%; max-width: 480px; border-radius: 20px; background: #FBFAF6; border: 2px solid #D9D4C4; padding: 28px 24px; box-shadow: 0 24px 60px rgba(12, 57, 47, 0.28); color: #19241E;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
            <div>
                <p style="color: #0C7950; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;">Login Diperlukan untuk Memesan</p>
                <h3 id="guestAuthTitle" style="color: #0C392F; font-size: 1.35rem; font-weight: 800; margin-top: 4px; line-height: 1.2;">Masuk atau Buat Akun Terlebih Dahulu</h3>
            </div>
            <button type="button" data-close-guest-modal aria-label="Tutup" style="width: 36px; height: 36px; border-radius: 999px; border: 1.5px solid #D9D4C4; background: #FFFFFF; color: #0C392F; font-weight: 800; cursor: pointer;">✕</button>
        </div>

        <p id="guestAuthRouteLabel" style="margin-top: 12px; padding: 10px 14px; border-radius: 10px; background: #E3F2EA; color: #0C392F; font-size: 0.9rem; font-weight: 700;"></p>

        <p style="margin-top: 12px; color: #56635B; font-size: 0.94rem; line-height: 1.5;">
            Anda sudah menemukan jadwal perjalanan pilihan Anda. Untuk melanjutkan pemilihan kursi dan pembayaran tiket, silakan <b>masuk ke akun</b> Anda terlebih dahulu.
        </p>

        <div style="margin-top: 14px; padding: 12px 14px; border-radius: 12px; border: 1.5px dashed #0C7950; background: #FFFFFF;">
            <p style="color: #0C392F; font-size: 0.88rem; font-weight: 800;">Belum punya akun TravelKu?</p>
            <p style="color: #56635B; font-size: 0.84rem; margin-top: 2px;">Kami merekomendasikan untuk mendaftar akun baru (Register) atau masuk dengan Google agar e-ticket dan riwayat perjalanan tersimpan otomatis.</p>
        </div>

        <div style="display: grid; gap: 10px; margin-top: 20px;">
            <a id="guestModalLoginBtn" href="{{ route('user.login') }}" class="lp-btn lp-btn--green" style="width: 100%; min-height: 48px;">Masuk ke Akun</a>
            <a id="guestModalRegisterBtn" href="{{ route('register') }}" class="lp-btn lp-btn--primary" style="width: 100%; min-height: 48px;">Buat Akun Baru (Register) — Direkomendasikan</a>
            <a id="guestModalGoogleBtn" href="{{ route('auth.google.redirect') }}" class="lp-btn lp-btn--ghost" style="width: 100%; min-height: 46px; background: #FFFFFF;">Masuk Cepat dengan Google</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('guestAuthModal');
    if (!modal) return;

    const routeLabel = document.getElementById('guestAuthRouteLabel');
    const loginBtn = document.getElementById('guestModalLoginBtn');
    const registerBtn = document.getElementById('guestModalRegisterBtn');
    const googleBtn = document.getElementById('guestModalGoogleBtn');

    document.querySelectorAll('[data-guest-book-trigger]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            window.TravelFeedback?.playWarning('Silakan masuk atau daftar akun terlebih dahulu untuk memesan kursi.');
            if (routeLabel) {
                routeLabel.textContent = 'Jadwal dipilih: ' + (btn.dataset.routeLabel || '');
            }
            if (loginBtn && btn.dataset.loginUrl) loginBtn.href = btn.dataset.loginUrl;
            if (registerBtn && btn.dataset.registerUrl) registerBtn.href = btn.dataset.registerUrl;
            if (googleBtn && btn.dataset.googleUrl) googleBtn.href = btn.dataset.googleUrl;
            modal.hidden = false;
            modal.style.display = 'grid';
        });
    });

    modal.querySelectorAll('[data-close-guest-modal]').forEach((closeBtn) => {
        closeBtn.addEventListener('click', () => {
            modal.hidden = true;
            modal.style.display = 'none';
        });
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.hidden = true;
            modal.style.display = 'none';
        }
    });
});
</script>
@endguest
@endsection
