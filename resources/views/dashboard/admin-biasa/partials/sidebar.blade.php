<!-- =========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="logo-area">

            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="13" rx="2" />
                    <circle cx="7" cy="18" r="2" />
                    <circle cx="17" cy="18" r="2" />
                    <path d="M2 10h20" />
                    <path d="M7 5v5" />
                    <path d="M12 5v5" />
                    <path d="M17 5v5" />
                </svg>
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>

                <span>
                    Dashboard
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.booking') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                        <path d="M13 5v2" />
                        <path d="M13 17v2" />
                        <path d="M13 11v2" />
                    </svg>
                </div>

                <span>
                    Manajemen Booking
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.perjalanan') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                        <line x1="9" x2="9" y1="3" y2="18" />
                        <line x1="15" x2="15" y1="6" y2="21" />
                    </svg>
                </div>

                <span>
                    Melihat Informasi Perjalanan
                </span>
            </a>


            <a href="{{ route('dashboard.admin-biasa.validasi-tiket') }}">
                <div class="menu-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="5" height="5" x="3" y="3" rx="1" />
                        <rect width="5" height="5" x="16" y="3" rx="1" />
                        <rect width="5" height="5" x="3" y="16" rx="1" />
                        <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                        <path d="M21 21v.01" />
                        <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                        <path d="M12 12v4" />
                        <path d="M12 20v1" />
                        <path d="M17 12h4" />
                    </svg>
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
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" x2="9" y1="12" y2="12" />
                        </svg>
                    </div>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>
