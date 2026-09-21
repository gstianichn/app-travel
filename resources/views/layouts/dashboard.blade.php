<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('page-title', 'Client Area Penumpang') — TravelKu FastShuttle</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & AlpineJS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eefbf5',
                            100: '#d7f6e7',
                            200: '#b1eed1',
                            300: '#7be1b4',
                            400: '#3ecc91',
                            500: '#087f55', // Primary brand color
                            600: '#066b47',
                            700: '#055c3d',
                            800: '#064932',
                            900: '#063c2a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">

@php
    $layoutUser = auth()->user();
    $layoutUnreadNotif = $layoutUser ? $layoutUser->unreadNotifications()->count() : 0;
@endphp

    <!-- ============================================================
         TOP NAVIGATION BAR (FRONT-END CLIENT AREA HEADER)
    ============================================================ -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">

                <!-- Left: Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard.user.index') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-xl shadow-md group-hover:scale-105 transition-transform duration-200">
                            🚐
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-slate-900 text-lg tracking-tight group-hover:text-brand-600 transition">TravelKu</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-brand-50 text-brand-700 border border-brand-200">FastShuttle</span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">Client Area & Portal Penumpang</p>
                        </div>
                    </a>
                </div>

                <!-- Center: Front-End Navigation Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-1 text-sm font-semibold text-slate-600">
                    {{-- Beranda --}}
                    @php $isHome = request()->routeIs('dashboard.user.index'); @endphp
                    <a href="{{ route('dashboard.user.index') }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $isHome ? 'bg-brand-50 text-brand-700 font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                        <svg class="w-4 h-4 {{ $isHome ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Beranda</span>
                    </a>

                    {{-- Cari Tiket --}}
                    @php $isSearch = request()->routeIs('dashboard.user.search*') || request()->routeIs('dashboard.user.booking*'); @endphp
                    <a href="{{ route('dashboard.user.search') }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $isSearch ? 'bg-brand-50 text-brand-700 font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                        <svg class="w-4 h-4 {{ $isSearch ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                        <span>Cari Jadwal</span>
                    </a>

                    {{-- Tiket Saya --}}
                    @php $isTicket = request()->routeIs('dashboard.user.user-tiket*'); @endphp
                    <a href="{{ route('dashboard.user.user-tiket') }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $isTicket ? 'bg-brand-50 text-brand-700 font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                        <svg class="w-4 h-4 {{ $isTicket ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                        <span>Tiket Saya</span>
                    </a>

                    {{-- Promo --}}
                    @php $isPromo = request()->routeIs('dashboard.user.user-promo*'); @endphp
                    <a href="{{ route('dashboard.user.user-promo') }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ $isPromo ? 'bg-brand-50 text-brand-700 font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                        <svg class="w-4 h-4 {{ $isPromo ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        <span>Promo</span>
                    </a>

                    {{-- Notifikasi --}}
                    @php $isNotif = request()->routeIs('dashboard.user.user-notifikasi*'); @endphp
                    <a href="{{ route('dashboard.user.user-notifikasi') }}"
                       class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 relative {{ $isNotif ? 'bg-brand-50 text-brand-700 font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                        <svg class="w-4 h-4 {{ $isNotif ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/></svg>
                        <span>Notifikasi</span>
                        @if($layoutUnreadNotif > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-500 text-white">
                                {{ $layoutUnreadNotif }}
                            </span>
                        @endif
                    </a>

                    {{-- Bantuan WhatsApp --}}
                    <a href="https://wa.me/62812345678" target="_blank"
                       class="px-3 py-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                        </svg>
                        <span>CS 24/7</span>
                    </a>
                </nav>

                <!-- Right: Quick Actions & User Dropdown -->
                <div class="flex items-center gap-2 sm:gap-3">

                    <!-- Notifikasi Icon Shortcut -->
                    <a href="{{ route('dashboard.user.user-notifikasi') }}"
                       class="relative w-10 h-10 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-600 flex items-center justify-center transition"
                       title="Pemberitahuan Sistem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                        </svg>
                        @if($layoutUnreadNotif > 0)
                            <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-ping"></span>
                            <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        @endif
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="relative" @click.outside="userMenuOpen = false">
                        <button type="button" @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 transition shadow-2xs">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-xs flex-shrink-0">
                                {{ strtoupper(substr($layoutUser->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-xs font-bold text-slate-800 leading-tight truncate max-w-[120px]">
                                    {{ $layoutUser->name ?? 'Penumpang' }}
                                </p>
                                <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Terverifikasi
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 hidden sm:block transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen" x-cloak x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50">
                            
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70 rounded-t-2xl">
                                <p class="text-xs text-slate-400 font-medium">Masuk sebagai Penumpang</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $layoutUser->name ?? 'Penumpang' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $layoutUser->email ?? '' }}</p>
                            </div>

                            <div class="py-1 text-xs font-semibold text-slate-700">
                                <a href="{{ route('dashboard.user.user-akun') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    <span>Profil & Akun Penumpang</span>
                                </a>

                                <a href="{{ route('dashboard.user.user-tiket') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                                    <span>Tiket & E-Ticket Boarding Pass</span>
                                </a>

                                <a href="{{ route('dashboard.user.user-notifikasi') }}" class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/></svg>
                                        <span>Notifikasi Sistem</span>
                                    </div>
                                    @if($layoutUnreadNotif > 0)
                                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-500 text-white">{{ $layoutUnreadNotif }}</span>
                                    @endif
                                </a>

                                <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 transition text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    <span>Halaman Utama Website</span>
                                </a>
                            </div>

                            <div class="pt-1 border-t border-slate-100">
                                <form action="{{ route('user.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Hamburger Button -->
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="!mobileMenuOpen"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="mobileMenuOpen" x-cloak><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>

                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1 shadow-lg">
            <a href="{{ route('dashboard.user.index') }}" class="block px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.index') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                🏠 Beranda Client Area
            </a>
            <a href="{{ route('dashboard.user.search') }}" class="block px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.search*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                🔍 Cari Jadwal Shuttle
            </a>
            <a href="{{ route('dashboard.user.user-tiket') }}" class="block px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.user-tiket*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                🎫 Tiket Saya & Boarding Pass
            </a>
            <a href="{{ route('dashboard.user.user-promo') }}" class="block px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.user-promo*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                🏷️ Voucher Promo & Diskon
            </a>
            <a href="{{ route('dashboard.user.user-notifikasi') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.user-notifikasi*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <span>🔔 Notifikasi Sistem</span>
                @if($layoutUnreadNotif > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white">{{ $layoutUnreadNotif }}</span>
                @endif
            </a>
            <a href="{{ route('dashboard.user.user-akun') }}" class="block px-3 py-2.5 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard.user.user-akun*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}">
                👤 Profil & Pengaturan Akun
            </a>
            <div class="pt-2 border-t border-slate-100">
                <form action="{{ route('user.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-xl font-bold text-sm text-rose-600 hover:bg-rose-50">
                        🚪 Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ============================================================
         MAIN CONTENT WRAPPER (CONSUMER CLIENT AREA CONTAINER)
    ============================================================ -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-12">
        @yield('content')
    </main>

    <!-- ============================================================
         CONSUMER CLIENT AREA FOOTER
    ============================================================ -->
    <footer class="bg-white border-t border-slate-200 mt-auto hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Col 1: Brand & Desc -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center text-base font-bold">🚐</div>
                        <span class="font-extrabold text-slate-900 text-base">TravelKu FastShuttle</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Layanan pemesanan tiket travel minibus eksekutif antar kota dengan armada Toyota HiAce Premio pilihan, jadwal tepat waktu, dan kenyamanan terjamin.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            🛡️ Berasuransi Resmi Jasa Raharja
                        </span>
                    </div>
                </div>

                <!-- Col 2: Rute Populer -->
                <div class="space-y-2">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900">Rute Shuttle Favorit</h5>
                    <ul class="text-xs text-slate-500 space-y-1.5 font-medium">
                        <li><a href="{{ route('dashboard.user.search') }}" class="hover:text-brand-600 transition">Bandung (Dipatiukur) ➔ Jakarta (Semanggi)</a></li>
                        <li><a href="{{ route('dashboard.user.search') }}" class="hover:text-brand-600 transition">Jakarta (Semanggi) ➔ Bandung (Dipatiukur)</a></li>
                        <li><a href="{{ route('dashboard.user.search') }}" class="hover:text-brand-600 transition">Bandung (Pasteur) ➔ Bandara Soekarno-Hatta</a></li>
                        <li><a href="{{ route('dashboard.user.search') }}" class="hover:text-brand-600 transition">Bekasi / Tangerang ➔ Bandung Kota</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan Penumpang -->
                <div class="space-y-2">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900">Layanan Penumpang</h5>
                    <ul class="text-xs text-slate-500 space-y-1.5 font-medium">
                        <li><a href="{{ route('dashboard.user.search') }}" class="hover:text-brand-600 transition">Cari Jadwal & Harga Tiket</a></li>
                        <li><a href="{{ route('dashboard.user.user-tiket') }}" class="hover:text-brand-600 transition">Lihat E-Ticket & Boarding Pass</a></li>
                        <li><a href="{{ route('dashboard.user.user-promo') }}" class="hover:text-brand-600 transition">Kupon Diskon & Voucher Promo</a></li>
                        <li><a href="{{ route('dashboard.user.user-notifikasi') }}" class="hover:text-brand-600 transition">Pemberitahuan & Info Operasional</a></li>
                    </ul>
                </div>

                <!-- Col 4: Bantuan & Pembayaran -->
                <div class="space-y-2">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900">Pusat Bantuan & Pembayaran</h5>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Layanan Pelanggan 24 Jam via WhatsApp Official:
                    </p>
                    <a href="https://wa.me/62812345678" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 hover:bg-emerald-100 transition">
                        💬 +62 812-3456-7890 (CS FastShuttle)
                    </a>
                    <div class="pt-2 text-[11px] text-slate-400">
                        Metode Bayar: Virtual Account BCA, Mandiri, BRI, BNI, QRIS & E-Wallet
                    </div>
                </div>

            </div>

            <div class="pt-8 mt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                <p>© {{ date('Y') }} PT Fast Travel Indonesia. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-slate-600 transition">Syarat & Ketentuan</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-600 transition">Kebijakan Privasi</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-600 transition">Panduan Refund & Reschedule</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         MOBILE BOTTOM NAVIGATION (ERGONOMIC 1-THUMB EXPERIENCE)
    ============================================================ -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg px-2 py-1.5">
        <div class="grid grid-cols-5 gap-1 text-center">
            
            {{-- Beranda --}}
            @php $isHome = request()->routeIs('dashboard.user.index'); @endphp
            <a href="{{ route('dashboard.user.index') }}" class="py-1 flex flex-col items-center gap-0.5 {{ $isHome ? 'text-brand-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span class="text-[10px]">Beranda</span>
            </a>

            {{-- Cari --}}
            @php $isSearch = request()->routeIs('dashboard.user.search*') || request()->routeIs('dashboard.user.booking*'); @endphp
            <a href="{{ route('dashboard.user.search') }}" class="py-1 flex flex-col items-center gap-0.5 {{ $isSearch ? 'text-brand-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                <span class="text-[10px]">Cari Tiket</span>
            </a>

            {{-- Tiket --}}
            @php $isTicket = request()->routeIs('dashboard.user.user-tiket*'); @endphp
            <a href="{{ route('dashboard.user.user-tiket') }}" class="py-1 flex flex-col items-center gap-0.5 {{ $isTicket ? 'text-brand-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                <span class="text-[10px]">Tiket Saya</span>
            </a>

            {{-- Notifikasi --}}
            @php $isNotif = request()->routeIs('dashboard.user.user-notifikasi*'); @endphp
            <a href="{{ route('dashboard.user.user-notifikasi') }}" class="py-1 flex flex-col items-center gap-0.5 relative {{ $isNotif ? 'text-brand-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/></svg>
                @if($layoutUnreadNotif > 0)
                    <span class="absolute top-0 right-4 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center">
                        {{ $layoutUnreadNotif }}
                    </span>
                @endif
                <span class="text-[10px]">Notifikasi</span>
            </a>

            {{-- Akun --}}
            @php $isAccount = request()->routeIs('dashboard.user.user-akun*'); @endphp
            <a href="{{ route('dashboard.user.user-akun') }}" class="py-1 flex flex-col items-center gap-0.5 {{ $isAccount ? 'text-brand-600 font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <span class="text-[10px]">Akun</span>
            </a>

        </div>
    </nav>

</body>
</html>