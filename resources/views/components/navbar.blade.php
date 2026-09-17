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

                @if(auth()->user()->role === 'admin')
                    <a href="{{ url('/dashboard/admin') }}" class="btn btn-primary">
                        Dashboard
                    </a>

                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary">
                        Dashboard
                    </a>

                @elseif(auth()->user()->role === 'driver')
                    <a href="{{ url('/driver/dashboard') }}" class="btn btn-primary">
                        Dashboard
                    </a>

                @elseif(auth()->user()->role === 'cs')
                    <a href="{{ url('/customer-service/dashboard') }}" class="btn btn-primary">
                        Dashboard
                    </a>

                @elseif(auth()->user()->role === 'user')
                    <a href="{{ url('/user/dashboard') }}" class="btn btn-primary">
                        Dashboard
                    </a>
                @endif

            @endguest
        </div>

    </div>
</nav>