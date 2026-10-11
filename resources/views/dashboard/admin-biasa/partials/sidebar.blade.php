{{-- Sidebar staf pool, dipakai semua halaman admin-biasa --}}
@php
    $staffMenu = [
        ['route' => 'dashboard.admin-biasa.index', 'label' => 'Beranda', 'icon' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z'],
        ['route' => 'dashboard.admin-biasa.validasi-tiket', 'label' => 'Validasi E-Ticket', 'icon' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2v2h-2zM18 14h2v2h-2zM14 18h6v2h-6z'],
        ['route' => 'dashboard.admin-biasa.booking', 'label' => 'Manajemen Booking', 'icon' => 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01'],
        ['route' => 'dashboard.admin-biasa.perjalanan', 'label' => 'Melihat Informasi Perjalanan', 'icon' => 'M9 4 3 6v14l6-2 6 2 6-2V4l-6 2-6-2zM9 4v14M15 6v14'],
    ];
@endphp

<aside class="sidebar">
    <div class="logo-area">
        <div class="logo-icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h13l4 4v7H3V6z" /><circle cx="7.5" cy="17.5" r="2" /><circle cx="16.5" cy="17.5" r="2" /><path d="M16 6v4h4" /></svg>
        </div>
        <div class="logo-text">
            <h2>TravelKu</h2>
            <span>Staf pool</span>
        </div>
    </div>

    <nav class="menu" aria-label="Menu staf pool">
        <div class="menu-title">Menu Utama</div>
        @foreach ($staffMenu as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" @class(['active' => $isActive]) @if ($isActive) aria-current="page" @endif>
                <div class="menu-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}" /></svg>
                </div>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="menu-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" /></svg>
                </div>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
