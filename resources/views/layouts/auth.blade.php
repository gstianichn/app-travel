<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Masuk') - TravelKu</title>

    @fonts('overpass')
    @vite(['resources/css/auth.css', 'resources/js/auth.js'])
</head>

<body @class(['au', 'au--staff' => trim($__env->yieldContent('variant')) === 'staff'])>
    <main class="au-shell">

        {{-- Scene: the same sun, hills and minibus as the landing page --}}
        <aside class="au-scene">
            <a href="{{ route('home') }}" class="au-brand">
                <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="height: 44px; width: auto; object-fit: contain;">
                <span>TravelKu</span>
            </a>


            <div class="au-sun" aria-hidden="true"></div>

            <div class="au-hills" aria-hidden="true">
                <svg class="au-hill au-hill--far" viewBox="0 0 1440 400" preserveAspectRatio="none">
                    <path d="M0 300C160 262 300 236 430 242s210-34 330-92c80-40 140-82 190-88h30c50 8 100 46 140 88 80 52 200 62 320 40V400H0z" />
                </svg>
                <svg class="au-hill au-hill--mid" viewBox="0 0 1440 300" preserveAspectRatio="none">
                    <path d="M0 120C180 60 360 70 520 120s240 30 360-10c140-50 360-40 560 20V300H0z" />
                </svg>
                <div class="au-road">
                    <svg class="au-van" viewBox="0 0 160 70">
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
            </div>
        </aside>

        <section class="au-panel">
            <div class="au-panel-inner">
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
