<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('page-title', 'Akun penumpang') - TravelKu</title>

    @fonts('overpass')
    @vite(['resources/css/dashboard-user.css', 'resources/js/dashboard-user.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="ud min-h-full flex flex-col antialiased" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">

@php
    $layoutUser = auth()->user();
    $layoutUnreadNotif = $layoutUser ? $layoutUser->unreadNotifications()->count() : 0;
    $navItems = [
        ['route' => 'dashboard.user.index', 'active' => ['dashboard.user.index'], 'label' => 'Beranda'],
        ['route' => 'dashboard.user.search', 'active' => ['dashboard.user.search*', 'dashboard.user.booking*'], 'label' => 'Cari jadwal'],
        ['route' => 'dashboard.user.user-tiket', 'active' => ['dashboard.user.user-tiket*'], 'label' => 'Tiket saya'],
        ['route' => 'dashboard.user.user-promo', 'active' => ['dashboard.user.user-promo*'], 'label' => 'Promo'],
        ['route' => 'dashboard.user.user-notifikasi', 'active' => ['dashboard.user.user-notifikasi*'], 'label' => 'Notifikasi', 'count' => $layoutUnreadNotif],
    ];
@endphp

    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:rounded-lg">Lewati ke konten</a>

    <header class="sticky top-0 z-40 bg-slate-50/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between gap-4 h-16">
                <a href="{{ $layoutUser ? route('dashboard.user.index') : route('home') }}" class="flex items-center gap-2.5 min-h-11 text-brand-900 font-extrabold text-xl">
                    <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" class="w-8 h-8 object-contain">
                    <span>TravelKu</span>
                </a>

                @if ($layoutUser)
                <nav class="hidden md:flex items-center gap-1 font-semibold" aria-label="Menu akun penumpang">
                    @foreach ($navItems as $item)
                        @php $isActive = request()->routeIs(...$item['active']); @endphp
                        <a href="{{ route($item['route']) }}"
                           @if ($isActive) aria-current="page" @endif
                           class="relative inline-flex items-center gap-2 min-h-11 px-3 rounded-lg transition-colors {{ $isActive ? 'text-brand-700 bg-brand-100' : 'text-slate-600 hover:text-brand-700 hover:bg-slate-100' }}">
                            {{ $item['label'] }}
                            @if (! empty($item['count']))
                                <span class="min-w-5 h-5 px-1.5 rounded-full bg-brand-700 text-white text-xs font-bold leading-5 text-center">{{ $item['count'] }}</span>
                                <span class="sr-only">belum dibaca</span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <div class="relative" @click.outside="userMenuOpen = false" @keydown.escape.window="userMenuOpen = false">
                        <button type="button" @click="userMenuOpen = !userMenuOpen" :aria-expanded="userMenuOpen.toString()" aria-haspopup="true"
                                class="flex items-center gap-2 min-h-11 pl-1 pr-3 rounded-full border border-slate-300 bg-white hover:border-brand-500 transition-colors">
                            <span class="w-9 h-9 rounded-full bg-brand-700 text-white font-bold flex items-center justify-center" aria-hidden="true">
                                {{ strtoupper(mb_substr($layoutUser->name ?? 'P', 0, 1)) }}
                            </span>
                            <span class="hidden sm:block font-semibold text-slate-800 max-w-32 truncate">{{ \Illuminate\Support\Str::before($layoutUser->name ?? 'Penumpang', ' ') }}</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" :class="userMenuOpen && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                        </button>

                        <div x-show="userMenuOpen" x-cloak x-transition.origin.top.right
                             class="absolute right-0 mt-2 w-64 rounded-xl bg-white border border-slate-200 shadow-lg py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900 truncate">{{ $layoutUser->name ?? 'Penumpang' }}</p>
                                <p class="text-sm text-slate-500 truncate">{{ $layoutUser->email ?? '' }}</p>
                            </div>
                            <a href="{{ route('dashboard.user.user-akun') }}" class="flex items-center min-h-11 px-4 text-slate-700 hover:bg-slate-50">Profil dan akun</a>
                            <a href="{{ route('dashboard.user.user-tiket') }}" class="flex items-center min-h-11 px-4 text-slate-700 hover:bg-slate-50">Tiket dan riwayat</a>
                            <a href="{{ route('home') }}" class="flex items-center min-h-11 px-4 text-slate-700 hover:bg-slate-50">Halaman utama TravelKu</a>
                            <form action="{{ route('user.logout') }}" method="POST" class="border-t border-slate-100 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center min-h-11 px-4 font-semibold text-rose-700 hover:bg-rose-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                    <nav class="flex items-center gap-2 font-semibold" aria-label="Menu">
                        <a href="{{ route('search') }}" class="hidden sm:inline-flex items-center min-h-11 px-3 rounded-lg text-slate-600 hover:text-brand-700 hover:bg-slate-100">Cari jadwal</a>
                        <a href="{{ route('user.login', ['redirect' => url()->current()]) }}" class="inline-flex items-center min-h-11 px-4 rounded-lg border-2 border-brand-800 text-brand-800 hover:bg-brand-50">Masuk</a>
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <main id="konten" @class(['flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 py-8 md:pb-16', 'pb-28' => $layoutUser, 'pb-16' => ! $layoutUser])>
        @yield('content')
    </main>

    <footer @class(['border-t border-slate-200 mt-auto', 'hidden md:block' => $layoutUser])>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-wrap items-center justify-between gap-4 text-slate-600">
            <p>&copy; {{ date('Y') }} TravelKu</p>
            <nav class="flex flex-wrap gap-x-6" aria-label="Bantuan">
                <a href="{{ route('home') }}#cara-pesan" class="inline-flex items-center min-h-11 hover:text-brand-700">Cara pesan</a>
                <a href="{{ route('home') }}#faq" class="inline-flex items-center min-h-11 hover:text-brand-700">Pembatalan dan refund</a>
                <a href="{{ route('home') }}" class="inline-flex items-center min-h-11 hover:text-brand-700">Halaman utama</a>
            </nav>
        </div>
    </footer>

    {{-- Phone navigation: thumb-reachable, same destinations as the header --}}
    @if ($layoutUser)
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200 pb-[env(safe-area-inset-bottom)]" aria-label="Menu akun penumpang">
        <div class="grid grid-cols-5">
            @foreach ($navItems as $item)
                @php $isActive = request()->routeIs(...$item['active']); @endphp
                <a href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif
                   class="relative flex flex-col items-center justify-center min-h-14 text-xs font-semibold {{ $isActive ? 'text-brand-700' : 'text-slate-500' }}">
                    @if ($isActive)
                        <span class="absolute top-0 inset-x-4 h-0.5 bg-brand-700 rounded-full" aria-hidden="true"></span>
                    @endif
                    <span>{{ $item['label'] }}</span>
                    @if (! empty($item['count']))
                        <span class="absolute top-1.5 right-3 min-w-4 h-4 px-1 rounded-full bg-brand-700 text-white text-[10px] font-bold leading-4">{{ $item['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>
    @endif

    @stack('scripts')
</body>
</html>
