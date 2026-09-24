<!-- =========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="logo-area">

            <div class="logo-icon">
                T
            </div>

            <div class="logo-text">

                <h2>Travel</h2>

                <span>
                    Management System
                </span>

            </div>

        </div>


        <nav class="menu">

            <div class="menu-title">
                Menu Utama
            </div>


            <a
                href="{{ route('dashboard.admin-biasa.index') }}"
                class="active"
            >
                <div class="menu-icon">
                    ⌂
                </div>

                <span>
                    Dashboard
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.booking') }}">
                <div class="menu-icon">
                    🎫
                </div>

                <span>
                    Manajemen Booking
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}">
                <div class="menu-icon">
                    🗺️
                </div>

                <span>
                    Melihat Informasi Perjalanan
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}">
                <div class="menu-icon">
                    🎟️
                </div>

                <span>
                    Validasi E-Ticket
                </span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                >

                    <div class="menu-icon">
                        ↪
                    </div>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>
