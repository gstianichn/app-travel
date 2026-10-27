<nav class="navbar" aria-label="Navigasi utama">
    <div class="container navbar-content">

        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('images/logo-travelku.png') }}" alt="TravelKu" style="height: 44px; width: auto; object-fit: contain;">
        </a>

        <button type="button" class="navbar-toggle" aria-expanded="false" aria-controls="navbar-panel" data-navbar-toggle>
            <span class="navbar-toggle-bars" aria-hidden="true"></span>
            <span>Menu</span>
        </button>

        <div class="navbar-panel" id="navbar-panel">
            <div class="navbar-menu">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'navbar-link-active' : '' }}" data-nav-section="home">Beranda</a>
                <a href="{{ route('home') }}#rute" data-nav-section="rute">Rute</a>
                <a href="{{ route('home') }}#cara-pesan" data-nav-section="cara-pesan">Cara pesan</a>
                <a href="{{ route('home') }}#faq" data-nav-section="faq">Tanya jawab</a>
                <a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'navbar-link-active' : '' }}">Cari Travel</a>
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
