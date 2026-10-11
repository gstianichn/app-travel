<nav class="navbar" aria-label="Navigasi utama">
    <div class="container navbar-content">

        <a href="{{ url('/') }}" class="navbar-brand">
            <span style="display: inline-flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h13l4 4v7H3V6z"/>
                    <circle cx="7.5" cy="17.5" r="2"/>
                    <circle cx="16.5" cy="17.5" r="2"/>
                    <path d="M16 6v4h4"/>
                    <path d="M7 10h4"/>
                </svg>
            </span>
            <span>TravelKu</span>
        </a>

        <button type="button" class="navbar-toggle" aria-expanded="false" aria-controls="navbar-panel" data-navbar-toggle>
            <span class="navbar-toggle-bars" aria-hidden="true"></span>
            <span>Menu</span>
        </button>

        <div class="navbar-panel" id="navbar-panel">
            <div class="navbar-menu">
                <a href="{{ url('/') }}" class="{{ request()->routeIs('home') ? 'navbar-link-active' : '' }}">Beranda</a>
                <a href="{{ route('home') }}#rute">Rute</a>
                <a href="{{ route('home') }}#cara-pesan">Cara pesan</a>
                <a href="{{ route('home') }}#faq">Tanya jawab</a>
                <a href="{{ url('/search') }}" class="{{ request()->routeIs('search') ? 'navbar-link-active' : '' }}">Cari Travel</a>
            </div>

            <div class="navbar-actions">
                @guest
                    <a href="{{ route('user.login') }}" class="btn btn-outline">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Daftar
                    </a>
                @else
                    @php
                        $dashboardRoute = match (auth()->user()->role) {
                            'admin', 'admin_utama' => 'dashboard.admin.index',
                            'staff' => 'dashboard.admin-biasa.index',
                            'driver' => 'dashboard.driver.index',
                            'cs', 'customer_service' => 'dashboard.customer-service.index',
                            default => 'dashboard.user.index',
                        };
                    @endphp

                    <a href="{{ route($dashboardRoute) }}" class="btn btn-primary">
                        Dashboard
                    </a>
                @endguest
            </div>
        </div>

    </div>
</nav>
