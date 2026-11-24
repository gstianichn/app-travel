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
                    <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" class="object-contain" style="width: 100%; height: 55px; padding: 4px;">
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
                                class="flex items-center gap-2 min-h-10 pl-1 pr-3 rounded-full border border-slate-300/90 bg-white hover:border-brand-700 transition-colors cursor-pointer">
                            <span class="w-8 h-8 rounded-full bg-brand-800 text-white text-sm font-bold flex items-center justify-center" aria-hidden="true">
                                {{ strtoupper(mb_substr($layoutUser->name ?? 'P', 0, 1)) }}
                            </span>
                            <span class="hidden sm:block text-sm font-semibold text-slate-800 max-w-28 truncate">{{ \Illuminate\Support\Str::before($layoutUser->name ?? 'Penumpang', ' ') }}</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform duration-150" :class="userMenuOpen && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                        </button>

                        <div x-show="userMenuOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-120"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-90"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-60 rounded-xl bg-white border border-slate-200 shadow-lg z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $layoutUser->name ?? 'Penumpang' }}</p>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $layoutUser->email ?? '' }}</p>
                            </div>

                            <div class="py-1.5">
                                <a href="{{ route('dashboard.user.user-akun') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-900 transition-colors">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <span>Profil dan akun</span>
                                </a>

                                <a href="{{ route('home') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-900 transition-colors">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                    <span>Halaman utama TravelKu</span>
                                </a>
                            </div>

                            <form action="{{ route('user.logout') }}" method="POST" class="border-t border-slate-100 py-1.5">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer">
                                    <svg class="h-4 w-4 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" y1="12" x2="9" y2="12" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
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

    @include('dashboard.user.partials.feedback-taxonomy')
    @include('components.numeric-input-enforcer')
    @stack('scripts')
</body>
</html>
