<nav class="navbar">
    <div class="container navbar-content">

        <a href="{{ url('/') }}" class="navbar-brand">
            TravelKu
        </a>

        <div class="navbar-menu">
            <a href="{{ url('/') }}" class="{{ request()->routeIs('home') ? 'navbar-link-active' : '' }}">Beranda</a>
            <a href="{{ url('/search') }}" class="{{ request()->routeIs('search') ? 'navbar-link-active' : '' }}">Cari Travel</a>
        </div>

        <div class="navbar-actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline">
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="btn btn-primary">
                    Daftar
                </a>
            @else
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    Dashboard
                </a>
            @endguest
        </div>

    </div>
</nav>