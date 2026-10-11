<nav class="navbar">
    <div class="container navbar-content">

        <a href="{{ url('/') }}" class="navbar-brand">
            <span style="display: inline-flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h13l4 4v7H3V6z"/>
                    <circle cx="7.5" cy="17.5" r="2"/>
                    <circle cx="16.5" cy="17.5" r="2"/>
                    <path d="M16 6v4h4"/>
                    <path d="M7 10h4"/>
                </svg>
            </span>
            <span>TravelKu</span>
        </a>

        <div class="navbar-menu">
            <a href="{{ url('/') }}" class="{{ request()->routeIs('home') ? 'navbar-link-active' : '' }}">Beranda</a>
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