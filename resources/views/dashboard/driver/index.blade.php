@extends('layouts.driver')

@section('title', 'Beranda Driver - TravelKu')

@section('content')
@php
    $driverDisplayName = preg_replace('/^Pak\s+/i', '', trim((string) ($user->name ?? 'Pengemudi')));
    $todayIndo = \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
    $routeOriginName = $activeSchedule->route?->origin?->name ?? $originCity;
    $routeDestName = $activeSchedule->route?->destination?->name ?? $destinationCity;
@endphp

<div class="space-y-6">
    {{-- 1. Header Sapaan & Tanggal Aktif (Ringkas, tanpa tombol aksi operasional berat) --}}
    <header class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl p-5 sm:p-6">
        <p class="text-xs font-bold uppercase tracking-wider text-[#0C7950] mb-1">
            {{ $todayIndo }}
        </p>
        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0C392F] leading-snug">
            Halo, selamat datang Pak {{ $driverDisplayName }}, semangat beraktivitas!
        </h1>
        <p class="mt-2 text-sm text-[#5C6862]">
            @if ($hasAssignedSchedule)
                Rute aktif hari ini:
                <strong class="text-[#13201B]">{{ $routeOriginName }} &rarr; {{ $routeDestName }}</strong>
                &middot; Keberangkatan <strong class="text-[#13201B]">{{ $activeSchedule->formattedDepartureTime() }}</strong>
                ({{ $activeSchedule->vehicle?->name ?? 'Unit Shuttle' }} &bull; {{ $activeSchedule->vehicle?->plate_number ?? '-' }}).
            @else
                Saat ini belum ada penugasan jadwal aktif hari ini (Status: <strong class="text-[#0C7950]">Standby</strong>).
            @endif
        </p>
    </header>

    {{-- 2. Widget Ramalan Cuaca (Kota Asal & Kota Tujuan - OpenWeatherMap API) --}}
    <section aria-labelledby="weather-heading" class="space-y-3">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <div>
                <h2 id="weather-heading" class="text-base font-extrabold text-[#0C392F]">Ramalan Cuaca Perjalanan Hari Ini</h2>
                <p class="text-xs text-[#5C6862]">Pantauan cuaca terkini di kota asal dan kota tujuan rute Anda.</p>
            </div>
            <span id="weatherSourceBadge" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#EEF6F1] text-[#0C5A3E] border border-[#cde3d7]">
                OpenWeatherMap
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Cuaca Kota Asal --}}
            <div class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl p-5" id="weatherCardOrigin">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-[#5C6862]">Kota Asal</span>
                        <h3 class="text-lg font-extrabold text-[#0C392F] mt-0.5">{{ $originCity }}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-extrabold text-[#0C392F] tabular-nums" data-weather-temp>--&deg;C</div>
                        <div class="text-xs font-semibold text-[#0C7950]" data-weather-desc>Memuat cuaca...</div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-[#ECE8DA] grid grid-cols-2 gap-3 text-xs text-[#5C6862]">
                    <div>
                        <span class="block text-[11px]">Kelembaban</span>
                        <strong class="text-sm text-[#13201B]" data-weather-humidity>--%</strong>
                    </div>
                    <div>
                        <span class="block text-[11px]">Kecepatan Angin</span>
                        <strong class="text-sm text-[#13201B]" data-weather-wind>-- km/jam</strong>
                    </div>
                </div>
            </div>

            {{-- Cuaca Kota Tujuan --}}
            <div class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl p-5" id="weatherCardDestination">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-[#5C6862]">Kota Tujuan</span>
                        <h3 class="text-lg font-extrabold text-[#0C392F] mt-0.5">{{ $destinationCity }}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-extrabold text-[#0C392F] tabular-nums" data-weather-temp>--&deg;C</div>
                        <div class="text-xs font-semibold text-[#0C7950]" data-weather-desc>Memuat cuaca...</div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-[#ECE8DA] grid grid-cols-2 gap-3 text-xs text-[#5C6862]">
                    <div>
                        <span class="block text-[11px]">Kelembaban</span>
                        <strong class="text-sm text-[#13201B]" data-weather-humidity>--%</strong>
                    </div>
                    <div>
                        <span class="block text-[11px]">Kecepatan Angin</span>
                        <strong class="text-sm text-[#13201B]" data-weather-wind>-- km/jam</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Peta Kondisi Lalu Lintas (Live Traffic Map - Google Maps JS API + TrafficLayer) --}}
    <section aria-labelledby="traffic-heading" class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-[#ECE8DA] flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 id="traffic-heading" class="text-base font-extrabold text-[#0C392F]">Peta Kondisi Lalu Lintas (Live Traffic)</h2>
                <p class="text-xs text-[#5C6862]">
                    Rute {{ $originCity }} &rarr; {{ $destinationCity }} &middot; Pantau jalur lancar, padat, atau macet sebelum berangkat.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 text-[#13201B]">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Lancar
                </span>
                <span class="inline-flex items-center gap-1.5 text-[#13201B]">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Padat
                </span>
                <span class="inline-flex items-center gap-1.5 text-[#13201B]">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Macet
                </span>
            </div>
        </div>

        <div class="relative">
            <div id="driverLiveTrafficMap" class="w-full h-[360px] sm:h-[420px] bg-[#EEF6F1]"></div>

            {{-- Fallback tampilan peta rute + live traffic jika GOOGLE_MAPS_API_KEY belum diisi --}}
            <div id="trafficMapFallback" class="hidden p-4 sm:p-5 bg-[#FFFDF8] space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs sm:text-sm text-[#5C6862]">
                        Menampilkan peta jalur <strong class="text-[#13201B]">{{ $originCity }} &rarr; {{ $destinationCity }}</strong>. Klik tombol di kanan untuk navigasi live traffic layar penuh.
                    </p>
                    <a href="https://www.google.com/maps/dir/?api=1&origin={{ urlencode($originCity) }}&destination={{ urlencode($destinationCity) }}&travelmode=driving&layer=t"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0C7950] text-white text-xs font-bold hover:bg-[#0C5A3E] transition shrink-0">
                        Buka Live Traffic di Google Maps
                    </a>
                </div>
                <div class="w-full h-[360px] sm:h-[400px] rounded-xl overflow-hidden border border-[#DEDAC9]">
                    <iframe
                        title="Peta Rute {{ $originCity }} ke {{ $destinationCity }}"
                        class="w-full h-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?saddr={{ urlencode($originCity) }}&daddr={{ urlencode($destinationCity) }}&dirflg=d&layer=t&output=embed">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        // ============================================================
        // 1. INTEGRASI OPENWEATHERMAP API (Cuaca Kota Asal & Tujuan)
        // ============================================================
        const OPENWEATHER_API_KEY = @json($openWeatherApiKey ?: 'YOUR_OPENWEATHERMAP_API_KEY');
        const ORIGIN_CITY = @json($originCity);
        const DESTINATION_CITY = @json($destinationCity);

        const weatherFallbackSamples = {
            origin: { temp: 27, desc: 'Cerah Berawan', humidity: 74, wind: 11 },
            destination: { temp: 31, desc: 'Berawan', humidity: 68, wind: 14 },
        };

        function renderWeatherCard(cardId, data, isPlaceholder) {
            const card = document.getElementById(cardId);
            if (!card) return;
            card.querySelector('[data-weather-temp]').textContent = Math.round(data.temp) + '°C';
            card.querySelector('[data-weather-desc]').textContent = data.desc + (isPlaceholder ? ' (Estimasi)' : '');
            card.querySelector('[data-weather-humidity]').textContent = data.humidity + '%';
            card.querySelector('[data-weather-wind]').textContent = Math.round(data.wind) + ' km/jam';
        }

        async function fetchCityWeather(city, cardId, fallbackData) {
            if (!OPENWEATHER_API_KEY || OPENWEATHER_API_KEY === 'YOUR_OPENWEATHERMAP_API_KEY') {
                renderWeatherCard(cardId, fallbackData, true);
                const badge = document.getElementById('weatherSourceBadge');
                if (badge) badge.textContent = 'Prakiraan Hari Ini';
                return;
            }

            try {
                const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)},ID&units=metric&lang=id&appid=${encodeURIComponent(OPENWEATHER_API_KEY)}`;
                const res = await fetch(url);
                if (!res.ok) throw new Error('Weather API error');
                const json = await res.json();

                renderWeatherCard(cardId, {
                    temp: json.main?.temp ?? fallbackData.temp,
                    desc: (json.weather?.[0]?.description || fallbackData.desc).replace(/^\w/, (c) => c.toUpperCase()),
                    humidity: json.main?.humidity ?? fallbackData.humidity,
                    wind: (json.wind?.speed ?? 3) * 3.6,
                }, false);
            } catch (err) {
                renderWeatherCard(cardId, fallbackData, true);
            }
        }

        fetchCityWeather(ORIGIN_CITY, 'weatherCardOrigin', weatherFallbackSamples.origin);
        fetchCityWeather(DESTINATION_CITY, 'weatherCardDestination', weatherFallbackSamples.destination);

        // ============================================================
        // 2. INTEGRASI GOOGLE MAPS JS API + TrafficLayer()
        // ============================================================
        const GOOGLE_MAPS_API_KEY = @json($googleMapsApiKey ?: 'YOUR_GOOGLE_MAPS_API_KEY');
        const mapContainer = document.getElementById('driverLiveTrafficMap');
        const mapFallback = document.getElementById('trafficMapFallback');

        window.initDriverTrafficMap = function () {
            if (!mapContainer || typeof google === 'undefined' || !google.maps) {
                return;
            }

            const map = new google.maps.Map(mapContainer, {
                center: { lat: -6.5971, lng: 107.1850 },
                zoom: 9,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
            });

            // Aktifkan Live Traffic Layer (Hijau = Lancar, Kuning/Oranye = Padat, Merah = Macet)
            const trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(map);

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: false,
            });

            directionsService.route(
                {
                    origin: ORIGIN_CITY + ', Indonesia',
                    destination: DESTINATION_CITY + ', Indonesia',
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (result, status) => {
                    if (status === 'OK' && result) {
                        directionsRenderer.setDirections(result);
                    }
                }
            );
        };

        if (!GOOGLE_MAPS_API_KEY || GOOGLE_MAPS_API_KEY === 'YOUR_GOOGLE_MAPS_API_KEY') {
            if (mapContainer) mapContainer.classList.add('hidden');
            if (mapFallback) mapFallback.classList.remove('hidden');
        } else {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(GOOGLE_MAPS_API_KEY)}&callback=initDriverTrafficMap&language=id`;
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                if (mapContainer) mapContainer.classList.add('hidden');
                if (mapFallback) mapFallback.classList.remove('hidden');
            };
            document.head.appendChild(script);
        }
    })();
</script>
@endpush
