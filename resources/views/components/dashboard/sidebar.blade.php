<aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">

    <div class="flex h-full flex-col">

        {{-- Logo --}}
        <div class="flex h-16 items-center border-b border-slate-200 px-6">
            <a
                href="{{ url('/') }}"
                class="text-xl font-bold text-blue-600"
            >
                Travel
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 p-4">

        @php
            $dashboardUrl = match (auth()->user()->role) {
                'admin' => route('dashboard.admin'),
                default => url('/dashboard'),
            };
        @endphp

        <a
            href="{{ $dashboardUrl }}"
            class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600"
        >
            Dashboard
        </a>


            {{-- ADMIN UTAMA --}}
            @if(auth()->check() && auth()->user()->role === 'admin')

                <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Manajemen
                </div>

                <a
                    href="{{ url('/dashboard/admin/users') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Kelola User
                </a>

                <a
                    href="{{ url('/dashboard/admin/drivers') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Kelola Driver
                </a>

                <a
                    href="{{ url('/dashboard/admin/customers') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Customer
                </a>

                <a
                    href="{{ url('/dashboard/admin/vehicles') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Kendaraan
                </a>

                <a
                    href="{{ url('/dashboard/admin/routes') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Rute
                </a>

                <a
                    href="{{ url('/dashboard/admin/schedules') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Jadwal
                </a>

                <a
                    href="{{ url('/dashboard/admin/bookings') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Pemesanan
                </a>

                <a
                    href="{{ url('/dashboard/admin/reports') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Laporan
                </a>

            {{-- ADMIN BIASA --}}
            @elseif(auth()->check() && auth()->user()->role === 'admin_biasa')

                <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Operasional
                </div>

                <a
                    href="{{ url('/dashboard/admin-biasa/schedules') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Jadwal
                </a>

                <a
                    href="{{ url('/dashboard/admin-biasa/bookings') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Pemesanan
                </a>

                <a
                    href="{{ url('/dashboard/admin-biasa/customers') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Customer
                </a>

            {{-- DRIVER --}}
            @elseif(auth()->check() && auth()->user()->role === 'driver')

                <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Perjalanan
                </div>

                <a
                    href="{{ url('/dashboard/driver/schedules') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Jadwal Saya
                </a>

                <a
                    href="{{ url('/dashboard/driver/trips') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Perjalanan
                </a>

                <a
                    href="{{ url('/dashboard/driver/profile') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Profil
                </a>

            {{-- CUSTOMER SERVICE --}}
            @elseif(auth()->check() && auth()->user()->role === 'customer_service')

                <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Layanan
                </div>

                <a
                    href="{{ url('/dashboard/customer-service/bookings') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Pemesanan
                </a>

                <a
                    href="{{ url('/dashboard/customer-service/customers') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Customer
                </a>

                <a
                    href="{{ url('/dashboard/customer-service/complaints') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Keluhan
                </a>

            {{-- USER --}}
            @elseif(auth()->check() && auth()->user()->role === 'user')

                <div class="px-4 pb-2 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Akun Saya
                </div>

                <a
                    href="{{ url('/dashboard/user/bookings') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Pemesanan Saya
                </a>

                <a
                    href="{{ url('/dashboard/user/profile') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Profil
                </a>

                <a
                    href="{{ url('/dashboard/user/notifications') }}"
                    class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600"
                >
                    Notifikasi
                </a>

            @endif

        </nav>

        {{-- Logout --}}
        <div class="border-t border-slate-200 p-4">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="w-full rounded-lg px-4 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                >
                    Logout
                </button>
            </form>

        </div>

    </div>

</aside>