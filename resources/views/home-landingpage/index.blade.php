@extends('layouts.app')

@section('title', 'TravelKu - Travel minibus antar kota')

@section('body-class', 'is-landing')

@push('head')
    @fonts('overpass')
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
@endpush

@section('content')
@php
    $cityOf = fn ($location) => $location->city ?: $location->name;
@endphp

{{-- HERO: layered hills, each layer drifts at its own depth while scrolling --}}
<section class="lp-hero" data-scene="exit" aria-labelledby="hero-title">
    <div class="lp-hero-scene" aria-hidden="true">
        <div class="lp-sun" data-depth="0.55"></div>

        <svg class="lp-clouds" data-depth="0.5" viewBox="0 0 1440 260" preserveAspectRatio="xMidYMin slice">
            <path d="M520 70c0-22 18-40 40-40 10 0 19 4 26 10 8-16 25-27 44-27 27 0 49 22 49 49v8H520z" />
            <path d="M980 70c0-17 14-31 31-31 8 0 15 3 20 8 6-12 19-21 34-21 21 0 38 17 38 38v6H980z" />
            <path d="M1180 190c0-14 11-25 25-25 6 0 12 2 16 6 5-10 15-17 27-17 17 0 30 13 30 30v6h-98z" />
        </svg>

        <svg class="lp-layer lp-layer--far" data-depth="0.42" viewBox="0 0 1440 400" preserveAspectRatio="none">
            <path fill="#B7D0C2" d="M0 300C160 262 300 236 430 242s210-34 330-92c80-40 140-82 190-88h30c50 8 100 46 140 88 80 52 200 62 320 40V400H0z" />
            <path fill="#97BCA9" d="M0 332c200-30 360-50 520-30s300-52 460-56c160-4 320 28 460 22V400H0z" />
        </svg>

        <svg class="lp-layer lp-layer--mid" data-depth="0.28" viewBox="0 0 1440 300" preserveAspectRatio="none">
            <path fill="#5E9D80" d="M0 120C180 60 360 70 520 120s240 30 360-10c140-50 360-40 560 20V300H0z" />
            <g fill="none" stroke="#4C8B6E" stroke-width="9" stroke-linecap="round" stroke-dasharray="14 12" vector-effect="non-scaling-stroke">
                <path d="M0 158C180 98 360 108 520 158s240 30 360-10c140-50 360-40 560 20" />
                <path d="M0 196C180 136 360 146 520 196s240 30 360-10c140-50 360-40 560 20" />
                <path d="M0 234C180 174 360 184 520 234s240 30 360-10c140-50 360-40 560 20" />
                <path d="M0 272C180 212 360 222 520 272s240 30 360-10c140-50 360-40 560 20" />
            </g>
        </svg>

        <div class="lp-layer lp-layer--near" data-depth="0.12">
            <svg viewBox="0 0 1440 220" preserveAspectRatio="none">
                <path fill="#2F7357" d="M0 70C240 30 520 40 760 62s460 0 680-22V220H0z" />
                <path fill="#36423C" d="M0 104c360-8 720-6 1080-4 140 0 260 0 360-2v48c-360 4-720 6-1080 6-140 0-260 0-360 2z" />
                <path fill="none" stroke="#F6F4EC" stroke-width="3" stroke-dasharray="26 22" vector-effect="non-scaling-stroke" d="M0 127c360-4 720-4 1440-4" />
            </svg>
            <svg class="lp-van" data-drive viewBox="0 0 160 70">
                <path fill="#FBFAF6" stroke="#0C392F" stroke-width="2" d="M10 20q0-12 12-12h90q10 0 18 8l16 18q6 2 6 10v8q0 6-6 6H14q-6 0-6-6z" />
                <path fill="#22403A" d="M20 14h20v16H20zm24 0h20v16H44zm24 0h20v16H68zm24 0h16v16H92zm22 0h12l14 18h-26z" />
                <path fill="#0C7950" d="M8 40h144v6H8z" />
                <path fill="#F2B25C" d="M144 36h8v5h-8z" />
                <circle cx="38" cy="58" r="10" fill="#1B2420" />
                <circle cx="38" cy="58" r="4" fill="#C9D3CE" />
                <circle cx="124" cy="58" r="10" fill="#1B2420" />
                <circle cx="124" cy="58" r="4" fill="#C9D3CE" />
            </svg>
        </div>

        <svg class="lp-layer lp-layer--front" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="var(--lp-paper)" d="M0 70C300 30 600 40 900 70s400 10 540-10V120H0z" />
        </svg>
    </div>

    <div class="container lp-hero-copy" data-depth="0.08">
        <p class="lp-plate">Travel minibus antar kota</p>
        <h1 id="hero-title">Antar kota, tanpa antre di loket.</h1>
        <p class="lp-hero-lead">
            Pilih rute, jam berangkat, dan kursimu dari HP. Bayar lewat virtual account, QRIS, atau e-wallet,
            lalu tunjukkan QR e-ticket ke petugas saat naik.
        </p>
        @if ($activeRouteCount > 0)
            <p class="lp-hero-facts">
                <span>{{ $activeRouteCount }} rute aktif</span>
                @if ($servedCityCount > 0)
                    <span>{{ $servedCityCount }} kota terhubung</span>
                @endif
            </p>
        @endif
    </div>
</section>

{{-- SEARCH: the ticket form is the page's main job, so it sits right under the hero --}}
@include('components.search-ticket', ['heading' => 'Mau berangkat ke mana?'])

{{-- ROUTES: road signs pass by horizontally while the section is pinned (desktop) --}}
<section class="lp-routes" id="rute" aria-labelledby="rute-title" data-routes>
    <div class="lp-routes-sticky">
        <div class="container lp-routes-head">
            <h2 id="rute-title">Rute yang sudah jalan</h2>
            <p>
                Pilih salah satu rambu untuk mengisi pencarian di atas. Harga di rambu adalah tarif hari biasa;
                akhir pekan dan hari libur bisa berbeda.
            </p>
        </div>

        @if ($routeSigns->isEmpty())
            <div class="container">
                <div class="lp-routes-empty">
                    <p class="lp-routes-empty-title">Rute sedang disiapkan</p>
                    <p>Belum ada rute aktif saat ini. Begitu admin membuka rute, rambunya muncul di sini.</p>
                </div>
            </div>
        @else
            <div class="lp-routes-viewport" data-routes-viewport>
                <div class="lp-routes-hills" aria-hidden="true" data-routes-hills></div>
                <ol class="lp-routes-track" data-routes-track>
                    @foreach ($routeSigns as $routeSign)
                        @php
                            $fromCity = $cityOf($routeSign->origin);
                            $toCity = $cityOf($routeSign->destination);
                        @endphp
                        <li class="lp-sign-post">
                            <a
                                href="#cari"
                                class="lp-sign"
                                data-route-fill
                                data-origin="{{ $routeSign->origin_id }}"
                                data-destination="{{ $routeSign->destination_id }}"
                                data-label="{{ $fromCity }} ke {{ $toCity }}"
                                aria-label="Isi pencarian {{ $fromCity }} ke {{ $toCity }}, mulai {{ $routeSign->formattedPrice() }}"
                            >
                                <span class="lp-sign-face">
                                    <span class="lp-sign-from">dari {{ $fromCity }}</span>
                                    <span class="lp-sign-to">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2 4 11h5v11h6V11h5z" /></svg>
                                        {{ $toCity }}
                                    </span>
                                    <span class="lp-sign-meta">
                                        {{ $routeSign->duration }}@if ($routeSign->distance_km), {{ $routeSign->distance_km }} km @endif
                                    </span>
                                </span>
                                <span class="lp-sign-plate">mulai {{ $routeSign->formattedPrice() }}</span>
                            </a>
                            <span class="lp-sign-legs" aria-hidden="true"></span>
                        </li>
                    @endforeach
                </ol>
                {{-- The minibus drives along the road while the signs pass by --}}
                <div class="lp-routes-van" aria-hidden="true" data-routes-van>
                    <svg viewBox="0 0 160 70">
                        <path fill="#FBFAF6" stroke="#0C392F" stroke-width="2" d="M10 20q0-12 12-12h90q10 0 18 8l16 18q6 2 6 10v8q0 6-6 6H14q-6 0-6-6z" />
                        <path fill="#22403A" d="M20 14h20v16H20zm24 0h20v16H44zm24 0h20v16H68zm24 0h16v16H92zm22 0h12l14 18h-26z" />
                        <path fill="#0C7950" d="M8 40h144v6H8z" />
                        <path fill="#F2B25C" d="M144 36h8v5h-8z" />
                        <g class="lp-wheel">
                            <circle cx="38" cy="58" r="10" fill="#1B2420" />
                            <circle cx="38" cy="58" r="4" fill="#C9D3CE" />
                            <path stroke="#C9D3CE" stroke-width="2" d="M38 49v18M29 58h18" />
                        </g>
                        <g class="lp-wheel">
                            <circle cx="124" cy="58" r="10" fill="#1B2420" />
                            <circle cx="124" cy="58" r="4" fill="#C9D3CE" />
                            <path stroke="#C9D3CE" stroke-width="2" d="M124 49v18M115 58h18" />
                        </g>
                    </svg>
                </div>
                <div class="lp-road" aria-hidden="true" data-routes-road></div>
            </div>
        @endif
    </div>
</section>

{{-- HOW TO BOOK: the van drives down the road as the reader moves through the steps --}}
<section class="lp-steps" id="cara-pesan" aria-labelledby="cara-pesan-title">
    <div class="container lp-steps-grid">
        <header class="lp-steps-head">
            <h2 id="cara-pesan-title">Dari pilih kursi sampai naik mobil</h2>
            <p>Semua lewat HP. Di pool, kamu cukup menunjukkan QR e-ticket ke petugas.</p>
            <a href="#cari" class="lp-btn lp-btn--ghost" data-focus-search>Isi pencarian</a>
        </header>

        <div class="lp-steps-road" data-steps>
            <div class="lp-steps-lane" aria-hidden="true">
                <span class="lp-steps-progress" data-steps-progress></span>
                <svg class="lp-steps-van" data-steps-van viewBox="0 0 40 72">
                    <rect x="4" y="2" width="32" height="68" rx="9" fill="#FBFAF6" stroke="#0C392F" stroke-width="2" />
                    <path fill="#22403A" d="M9 12h22l-2 12H11zM10 50h20v10H10z" />
                    <path fill="#0C7950" d="M18 28h4v18h-4z" />
                </svg>
            </div>

            <ol class="lp-steps-list">
                <li data-step>
                    <span class="lp-km" aria-hidden="true">KM<b>1</b></span>
                    <h3>Cari rute dan tanggal</h3>
                    <p>Tiap jadwal menampilkan jam berangkat, harga, dan sisa kursi, jadi kamu tahu sebelum memesan.</p>
                </li>
                <li data-step>
                    <span class="lp-km" aria-hidden="true">KM<b>2</b></span>
                    <h3>Pilih kursi sendiri</h3>
                    <p>Denah kursinya mengikuti susunan di mobil. Kursi yang kamu pilih ditahan 15 menit selama kamu membayar.</p>
                </li>
                <li data-step>
                    <span class="lp-km" aria-hidden="true">KM<b>3</b></span>
                    <h3>Bayar</h3>
                    <p>Virtual account BCA, Mandiri, BNI, BRI, BSI, dan SeaBank, QRIS, GoPay, atau ShopeePay. Pembayaran diproses Midtrans.</p>
                </li>
                <li data-step>
                    <span class="lp-km" aria-hidden="true">KM<b>4</b></span>
                    <h3>Tunjukkan QR saat naik</h3>
                    <p>E-ticket dikirim ke emailmu begitu pembayaran masuk, dan tersimpan di akun kalau kamu masuk. Boarding dibuka 15 menit sebelum berangkat; petugas memindai QR-nya, atau mencari kode booking kalau QR tidak terbaca.</p>
                </li>
            </ol>
        </div>
    </div>
</section>

{{-- FLEET: seat dots mirror the seat map passengers will pick from --}}
<section class="lp-fleet" id="armada" aria-labelledby="armada-title">
    <div class="container">
        <div class="lp-fleet-head">
            <h2 id="armada-title">Mobil yang akan kamu naiki</h2>
            <p>Jenis armada yang terdaftar saat ini. Kendaraan untuk tiap jadwal tertulis di halaman hasil pencarian.</p>
        </div>

        @if ($fleetTypes->isEmpty())
            <p class="lp-fleet-empty">Data armada belum diisi. Detail kendaraan tetap muncul di setiap jadwal.</p>
        @else
            <ul class="lp-fleet-list">
                @foreach ($fleetTypes as $fleetType)
                    @php
                        $cols = 4;
                    @endphp
                    <li class="lp-fleet-row">
                        <div class="lp-fleet-name">
                            <span class="inline-block px-2.5 py-0.5 mb-2 text-xs font-bold rounded-full {{ $fleetType['capacity'] <= 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $fleetType['category'] }}
                            </span>
                            <h3>{{ $fleetType['type'] }}</h3>
                            <p>{{ $fleetType['capacity'] }} kursi penumpang, {{ $fleetType['units'] }} unit aktif</p>
                        </div>
                        <div class="lp-seats" style="--seat-columns: 4; grid-template-columns: repeat(4, 24px); gap: 6px; padding: 14px;" role="img" aria-label="Denah {{ $fleetType['capacity'] }} kursi penumpang {{ $fleetType['type'] }}">
                            {{-- Baris 1: Kursi 1 di sisi kiri dan Supir di sisi kanan (Standar Kemudi Kanan Indonesia) --}}
                            <span class="lp-seat" style="grid-column: 1; grid-row: 1; font-size: 0.65rem; font-weight: 800;" title="Kursi 1">1</span>
                            <span class="lp-seat lp-seat--driver" style="grid-column: 4; grid-row: 1;" title="Posisi Kemudi Sopir (Kanan)">S</span>

                            {{-- Baris Kabin 2 dst: Sesuai Foto Denah Asli --}}
                            @foreach ($fleetType['seatRows'] as $rowNumber => $rowSeats)
                                @if ((string) $rowNumber !== '1')
                                    @foreach ($rowSeats as $seat)
                                        <span class="lp-seat" style="grid-column: {{ $seat->seat_column }}; grid-row: {{ (int) $rowNumber }}; font-size: 0.65rem; font-weight: 800;" title="Kursi {{ $seat->seat_number }}">
                                            {{ $seat->seat_number }}
                                        </span>
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Area Full Bagasi untuk Seluruh Armada Travel --}}
                            @php
                                $bagasiRow = $fleetType['capacity'] <= 10 ? 5 : 6;
                            @endphp
                            <div style="grid-column: 1 / span 4; grid-row: {{ $bagasiRow }}; background: #fbbf24; color: #78350f; font-size: 0.55rem; font-weight: 900; text-align: center; padding: 4px 2px; border-radius: 4px; border: 1px solid #d97706; letter-spacing: 0.5px;">
                                FULL BAGASI
                            </div>
                        </div>
                        <p class="lp-fleet-facilities">
                            {{ $fleetType['facilities'] ? implode(', ', $fleetType['facilities']) : 'AC, Reclining Seat, USB Charger' }}
                        </p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>

{{-- REVIEWS: only rendered when real passenger reviews exist --}}
@if ($reviewCount > 0 && $reviews->isNotEmpty())
    <section class="lp-reviews" id="ulasan" aria-labelledby="ulasan-title">
        <div class="container lp-reviews-grid">
            <div class="lp-reviews-score">
                <h2 id="ulasan-title">Kata penumpang</h2>
                <p class="lp-reviews-average"><b>{{ number_format($reviewAverage, 1, ',', '.') }}</b> dari 5</p>
                <p>Rata-rata dari {{ $reviewCount }} ulasan setelah perjalanan selesai.</p>
            </div>

            <ul class="lp-reviews-list">
                @foreach ($reviews as $review)
                    @php
                        $reviewRoute = $review->schedule?->route;
                    @endphp
                    <li>
                        <blockquote>{{ $review->comment }}</blockquote>
                        <p class="lp-review-meta">
                            <b>{{ \Illuminate\Support\Str::before($review->user?->name ?? 'Penumpang', ' ') ?: 'Penumpang' }}</b>
                            @if ($reviewRoute?->origin && $reviewRoute?->destination)
                                <span>{{ $cityOf($reviewRoute->origin) }} ke {{ $cityOf($reviewRoute->destination) }}</span>
                            @endif
                            <span>Nilai {{ number_format((float) $review->overall_rating, 1, ',', '.') }}</span>
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

{{-- FAQ: answers follow the rules the booking system actually enforces --}}
<section class="lp-faq" id="faq" aria-labelledby="faq-title">
    <div class="container lp-faq-grid">
        <h2 id="faq-title">Yang sering ditanyakan sebelum berangkat</h2>

        <div class="lp-faq-list">
            <details>
                <summary>Sampai kapan tiket bisa dibatalkan?</summary>
                <p>Pembatalan bisa diajukan dari akunmu paling lambat 1 jam sebelum jam berangkat. Besaran refund ditentukan admin, dan dananya kembali lewat metode bayar yang kamu pakai. Kalau kamu memesan tanpa akun, masuk dengan Google memakai email yang sama dengan pesananmu; tiketnya ikut muncul di akun.</p>
            </details>
            <details>
                <summary>Berapa lama kursi ditahan saat saya membayar?</summary>
                <p>15 menit. Kalau pembayaran belum masuk sampai batas itu, booking batal otomatis dan kursinya dilepas untuk penumpang lain.</p>
            </details>
            <details>
                <summary>Kenapa harga akhir pekan berbeda?</summary>
                <p>Tiap rute punya tarif hari biasa, akhir pekan, dan hari libur. Harga yang kamu bayar mengikuti tanggal berangkat yang kamu pilih.</p>
            </details>
            <details>
                <summary>Kapan saya harus sudah di pool?</summary>
                <p>Boarding dibuka 15 menit sebelum jam berangkat. Siapkan QR e-ticket di HP supaya petugas bisa langsung memindainya.</p>
            </details>
            <details>
                <summary>Bagaimana kalau saya tidak datang?</summary>
                <p>Tiket dicatat sebagai tidak hadir (no show), hangus, dan tidak bisa direfund. Kalau rencanamu berubah, batalkan paling lambat 1 jam sebelum berangkat.</p>
            </details>
        </div>
    </div>
</section>

{{-- CLOSING: dusk hills rise into place as the section scrolls in --}}
<section class="lp-dusk" data-scene="enter" aria-labelledby="dusk-title">
    <div class="lp-dusk-scene" aria-hidden="true">
        <span class="lp-moon" data-depth="0.6"></span>
        <svg class="lp-layer lp-layer--far" data-depth="0.45" viewBox="0 0 1440 400" preserveAspectRatio="none">
            <path fill="#2E6152" d="M0 300C160 262 300 236 430 242s210-34 330-92c80-40 140-82 190-88h30c50 8 100 46 140 88 80 52 200 62 320 40V400H0z" />
        </svg>
        <svg class="lp-layer lp-layer--mid" data-depth="0.3" viewBox="0 0 1440 300" preserveAspectRatio="none">
            <path fill="#1F4C3F" d="M0 140C200 80 380 90 540 140s260 30 380-10c140-50 320-40 520 20V300H0z" />
        </svg>
        <svg class="lp-layer lp-layer--front" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="#0C392F" d="M0 60C300 20 600 30 900 60s400 10 540-10V120H0z" />
        </svg>
    </div>

    <div class="container lp-dusk-copy">
        <h2 id="dusk-title">Tinggal pilih tanggalnya.</h2>
        <p>Cek rute, jam, dan kursi, lalu pesan langsung. Tidak perlu daftar akun dulu.</p>
        <a href="#cari" class="lp-btn lp-btn--light" data-focus-search>Isi pencarian</a>
    </div>
</section>
@endsection
