<!-- ========================================
         SIDEBAR
    ========================================= -->

    <aside class="sidebar">

        <div class="sidebar-header">

            <a
                href="{{ route('dashboard.customer-service.index') }}"
                class="brand"
            >

                <div class="brand-icon">
                    T
                </div>

                <div class="brand-text">

                    <span class="brand-title">
                        TravelKu
                    </span>

                    <span class="brand-subtitle">
                        Management System
                    </span>

                </div>

            </a>

        </div>


        <nav class="menu">

            <div class="menu-title">
                Menu Utama
            </div>


            <ul class="menu-list">

                <li class="menu-item">

                    <a
                        href="{{ route('dashboard.customer-service.index') }}"
                        class="menu-link active"
                    >

                        <span class="menu-icon">
                            ⌂
                        </span>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="{{ route('dashboard.customer-service.live-chat') }}"
                        class="quick-item"
                    >
                        <div class="quick-icon">
                            💬
                        </div>

                        <div class="quick-text">
                            <span class="quick-title">
                                Layanan Pelanggan
                            </span>

                            <span class="quick-desc">
                                Tangani pertanyaan pelanggan
                            </span>
                        </div>
                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ♙
                        </span>

                        <span>
                            Pelanggan
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ◷
                        </span>

                        <span>
                            Jadwal Travel
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            💬
                        </span>

                        <span>
                            Layanan Pelanggan
                        </span>

                    </a>

                </li>


                <li class="menu-item">

                    <a
                        href="#"
                        class="menu-link"
                    >

                        <span class="menu-icon">
                            ▤
                        </span>

                        <span>
                            Laporan
                        </span>

                    </a>

                </li>

            </ul>

        </nav>


        <!-- LOGOUT -->

        <div class="sidebar-footer">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >

                    <span class="menu-icon">
                        ⇥
                    </span>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </div>

    </aside>
