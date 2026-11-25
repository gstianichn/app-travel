{{-- Sidebar driver operasional (5 menu inti + notifikasi) --}}
@php
    $driverNav = [
        [
            'route' => 'dashboard.driver.index',
            'label' => 'Beranda',
            'short' => 'Beranda',
            'icon' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z',
        ],
        [
            'route' => 'dashboard.driver.perjalanan',
            'label' => 'Perjalanan Saya',
            'short' => 'Perjalanan',
            'icon' => 'M3 12h18M13 5l7 7-7 7',
        ],
        [
            'route' => 'dashboard.driver.penumpang',
            'label' => 'Penumpang & Kursi',
            'short' => 'Penumpang',
            'badge' => $totalBookedPassengers ?? 0,
            'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 21v-2a4 4 0 0 0-3-3.87',
        ],
        [
            'route' => 'dashboard.driver.jadwal',
            'label' => 'Jadwal Hari Ini',
            'short' => 'Jadwal',
            'icon' => 'M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z',
        ],
        [
            'route' => 'dashboard.driver.lapor-masalah',
            'label' => 'Lapor Kendala',
            'short' => 'Kendala',
            'icon' => 'M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01',
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar" aria-label="Navigasi driver">
    <div class="logo-area">
        <div class="logo-icon" aria-hidden="true">
            <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="logo-text">
            <h2>TravelKu</h2>
            <span>Driver Shuttle</span>
        </div>
        <button type="button" class="sidebar-close-btn" onclick="toggleSidebar(false)" aria-label="Tutup Menu" style="display: none; margin-left: auto; background: none; border: none; color: white; cursor: pointer; padding: 4px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <nav class="menu" id="driver-menu" aria-label="Menu utama driver">
        <div class="menu-title">Menu Driver</div>

        @foreach ($driverNav as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" @class(['active' => $isActive]) @if ($isActive) aria-current="page" @endif>
                <div class="menu-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $item['icon'] }}" />
                    </svg>
                </div>
                <span>{{ $item['label'] }}</span>
                @if (! empty($item['badge']) && $item['badge'] > 0)
                    <span style="margin-left: auto; background: rgba(255,255,255,0.22); color: inherit; padding: 2px 7px; border-radius: 999px; font-size: 11px; font-weight: 700;">{{ $item['badge'] }}</span>
                @endif
            </a>
        @endforeach

        <div class="menu-title" style="margin-top: 12px;">Lainnya</div>

        @php $isNotif = request()->routeIs('dashboard.driver.notifikasi'); @endphp
        <a href="{{ route('dashboard.driver.notifikasi') }}" @class(['active' => $isNotif]) @if ($isNotif) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <span>Notifikasi</span>
            @if (($unreadNotificationsCount ?? 0) > 0)
                <span style="margin-left: auto; background: #dc2626; color: #fff; padding: 2px 7px; border-radius: 999px; font-size: 11px; font-weight: 700;">{{ $unreadNotificationsCount }}</span>
            @endif
        </a>

        @php $isRiwayat = request()->routeIs('dashboard.driver.riwayat'); @endphp
        <a href="{{ route('dashboard.driver.riwayat') }}" @class(['active' => $isRiwayat]) @if ($isRiwayat) aria-current="page" @endif>
            <div class="menu-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <span>Riwayat Perjalanan</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="menu-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- Navigasi Bawah Khusus Mobile (Responsive Bottom Bar) --}}
<nav class="driver-mobile-bottom-nav md:hidden fixed bottom-0 inset-x-0 z-50 bg-[#FFFDF8] border-t border-[#DEDAC9] px-1 py-1.5 shadow-lg" aria-label="Navigasi bawah driver">
    <div class="grid grid-cols-5 gap-1">
        @foreach ($driverNav as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center justify-center py-1.5 px-1 rounded-lg text-[11px] font-semibold transition {{ $isActive ? 'text-[#0C7950] bg-[#EEF6F1]' : 'text-[#5C6862]' }}">
                <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $item['icon'] }}" />
                </svg>
                <span class="truncate max-w-full">{{ $item['short'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
