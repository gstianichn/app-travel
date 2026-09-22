<aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">

    <div class="flex h-full flex-col">

        {{-- =====================================================
             LOGO
        ====================================================== --}}
        <div class="flex h-16 items-center border-b border-slate-200 px-6">

            <a
                href="{{ route('home') }}"
                class="text-xl font-bold text-blue-600"
            >
                TravelKu
            </a>

        </div>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}
        <nav class="flex-1 space-y-1 overflow-y-auto p-4">

            {{-- =================================================
                 DASHBOARD
            ================================================== --}}

            @if(auth()->check())

                @php
                    $role = auth()->user()->role;
                @endphp


                {{-- ================================
                     ADMIN UTAMA
                ================================= --}}
                @if($role === 'admin_utama')

                    <a
                        href="{{ route('dashboard.admin.index') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        <span>Dashboard</span>
                    </a>


                    <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Manajemen
                    </div>


                    <a
                        href="{{ url('/dashboard/admin/users') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Kelola User
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/drivers') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Kelola Driver
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/customers') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Customer
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/vehicles') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Kendaraan
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/routes') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Rute
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/schedules') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Jadwal
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/bookings') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Pemesanan
                    </a>

                    <a
                        href="{{ url('/dashboard/admin/reports') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Laporan
                    </a>


                {{-- ================================
                     STAFF
                ================================= --}}
                @elseif($role === 'staff')

                    <a
                        href="{{ route('dashboard.admin-biasa.index') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Dashboard
                    </a>


                    <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Menu Utama
                    </div>


                    <a
                        href="{{ route('dashboard.admin-biasa.booking') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Manajemen Booking
                    </a>

                    <a
                        href="{{ route('dashboard.admin-biasa.perjalanan') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Melihat Informasi Perjalanan
                    </a>

                    <a
                        href="#"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Validasi E-Ticket
                    </a>


                {{-- ================================
                     DRIVER
                ================================= --}}
                @elseif($role === 'driver')

                    <a
                        href="{{ route('dashboard.driver.index') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Dashboard
                    </a>


                    <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Perjalanan
                    </div>


                    <a
                        href="{{ url('/dashboard/driver/schedules') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Jadwal Saya
                    </a>

                    <a
                        href="{{ url('/dashboard/driver/trips') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Perjalanan
                    </a>

                    <a
                        href="{{ url('/dashboard/driver/profile') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Profil
                    </a>


                {{-- ================================
                     CUSTOMER SERVICE
                ================================= --}}
                @elseif($role === 'cs')

                    <a
                        href="{{ route('dashboard.customer-service.index') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Dashboard
                    </a>


                    <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Layanan Pelanggan
                    </div>


                    {{-- Live Chat --}}
                    <a
                        href="{{ route('customer-service.live-chat') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        <span>Live Chat</span>
                    </a>


                    {{-- Pemesanan --}}
                    <a
                        href="{{ url('/dashboard/customer-service/bookings') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        <span>Pemesanan</span>
                    </a>


                    {{-- Customer --}}
                    <a
                        href="{{ url('/dashboard/customer-service/customers') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        <span>Customer</span>
                    </a>


                    {{-- Keluhan --}}
                    <a
                        href="{{ url('/dashboard/customer-service/complaints') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        <span>Keluhan</span>
                    </a>


                {{-- ================================
                     USER
                ================================= --}}
                @elseif($role === 'user')

                    <a
                        href="{{ route('dashboard.user.index') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Dashboard
                    </a>


                    <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Akun Saya
                    </div>


                    <a
                        href="{{ url('/dashboard/user/bookings') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Pemesanan Saya
                    </a>

                    <a
                        href="{{ url('/dashboard/user/profile') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Profil
                    </a>

                    <a
                        href="{{ url('/dashboard/user/notifications') }}"
                        class="flex items-center rounded-lg px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-600"
                    >
                        Notifikasi
                    </a>

                @endif

            @endif

        </nav>


        {{-- =====================================================
             USER PROFILE + LOGOUT
        ====================================================== --}}
        <div class="border-t border-slate-200 p-4">

            @if(auth()->check())

                {{-- Profile --}}
                <div class="mb-3 flex items-center gap-3 px-2">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-600">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-semibold text-slate-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="truncate text-xs text-slate-500">
                            {{ auth()->user()->role }}
                        </p>

                    </div>

                </div>


                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center rounded-lg px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50"
                    >
                        Logout
                    </button>

                </form>

            @endif

        </div>

    </div>

</aside>