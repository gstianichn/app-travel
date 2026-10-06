<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - Travel</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
        
        
        
</head>

<body class="min-h-screen bg-page text-ink">

    <!-- ================= NAVBAR ================= -->
    <nav class="sticky top-0 z-50 border-b border-border bg-surface">

        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <!-- Logo -->
            <a href="#" class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand text-lg font-bold text-surface shadow-sm">
                    T
                </div>

                <div>
                    <h1 class="text-base font-bold leading-tight text-brand-dark">
                        TravelKu
                    </h1>

                    <p class="text-xs text-brand-support">
                        Perjalanan lebih nyaman
                    </p>
                </div>

            </a>


            <!-- User -->
            <div class="flex items-center gap-3">

                <div class="hidden text-right sm:block">

                    <p class="text-sm font-semibold text-ink">
                        {{ $user->name }}
                    </p>

                    <p class="text-xs text-brand-support">
                        Pelanggan
                    </p>

                </div>


                <!-- Avatar -->
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-dark text-sm font-bold text-surface">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>


                <!-- Logout -->
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg border border-border px-4 py-2 text-sm font-semibold text-brand-dark transition hover:bg-status"
                    >
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </nav>


    <!-- ================= MAIN ================= -->
    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">


        <!-- ================= WELCOME ================= -->
        <section class="mb-8">

            <p class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-brand">
                DASHBOARD PELANGGAN
            </p>

            <h2 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                Halo, {{ $user->name }}
            </h2>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-brand-support sm:text-base">
                Selamat datang kembali di Travel.
                Kelola perjalanan, booking, dan aktivitas akunmu dengan mudah.
            </p>

        </section>


        <!-- ================= HERO ================= -->
        <section class="relative mb-8 overflow-hidden bg-brand-dark px-6 py-8 shadow-lg sm:px-8 lg:px-10">

            <!-- Decoration -->
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full border-24 border-brand-support/25"></div>

            <div class="absolute -bottom-20 right-40 h-40 w-40 rounded-full border border-status/20"></div>


            <div class="relative z-10 max-w-2xl">

                <span class="inline-flex rounded-full bg-status px-3 py-1 text-xs font-bold text-brand-dark">
                    Perjalanan berikutnya
                </span>

                <h3 class="mt-4 text-2xl font-bold text-white sm:text-3xl">
                    Mau pergi ke mana hari ini?
                </h3>

                <p class="mt-3 max-w-xl text-sm leading-6 text-status/80 sm:text-base">
                    Cari jadwal perjalanan sesuai tujuanmu dan lakukan booking
                    dengan mudah melalui platform Travel.
                </p>


                <div class="mt-6 flex flex-wrap gap-3">

                    <a
                        href="{{ route('dashboard.user.search') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-status px-5 py-3 text-sm font-bold text-brand-dark transition hover:bg-status/90"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span>Cari Perjalanan</span>
                    </a>

                    <a
                        href="{{ route('dashboard.user.user-tiket') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-status/30 bg-transparent px-5 py-3 text-sm font-semibold text-surface transition hover:bg-status/10"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                        </svg>
                        <span>Pesanan Saya</span>
                    </a>

                </div>

            </div>

        </section>


        <!-- ================= STATISTICS ================= -->
        <section class="mb-10">

            <div class="mb-5">

                <p class="text-xs font-bold uppercase tracking-wider text-brand">
                    Ringkasan
                </p>

                <h3 class="mt-1 text-xl font-bold text-ink">
                    Aktivitas perjalananmu
                </h3>

            </div>


            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">


                <!-- Booking Aktif -->
                <div class="border border-border bg-surface p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-sm font-semibold text-brand-support">
                                Booking Aktif
                            </p>

                            <p class="mt-3 text-3xl font-bold text-ink">
                                0
                            </p>

                            <p class="mt-1 text-xs text-placeholder">
                                Perjalanan yang sedang berlangsung
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-status text-brand">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h13l4 4v7H3V6z"/>
                                <circle cx="7.5" cy="17.5" r="2"/>
                                <circle cx="16.5" cy="17.5" r="2"/>
                                <path d="M16 6v4h4"/>
                                <path d="M7 10h4"/>
                            </svg>
                        </div>

                    </div>

                </div>


                <!-- Selesai -->
                <div class="border border-border bg-surface p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-sm font-semibold text-brand-support">
                                Pesanan Selesai
                            </p>

                            <p class="mt-3 text-3xl font-bold text-ink">
                                0
                            </p>

                            <p class="mt-1 text-xs text-placeholder">
                                Total perjalanan selesai
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EAF3FB] text-info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>

                    </div>

                </div>


                <!-- Loyalty -->
                <div class="border border-border bg-surface p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-sm font-semibold text-brand-support">
                                Loyalty Points
                            </p>

                            <p class="mt-3 text-3xl font-bold text-ink">
                                0
                            </p>

                            <p class="mt-1 text-xs text-placeholder">
                                Poin yang tersedia
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-status text-xl text-brand">
                            ⭐
                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ================= QUICK MENU ================= -->
        <section class="mb-10">

            <div class="mb-5">

                <p class="text-xs font-bold uppercase tracking-wider text-brand">
                    Akses Cepat
                </p>

                <h3 class="mt-1 text-xl font-bold text-ink">
                    Mau melakukan apa?
                </h3>

            </div>


            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">


                <!-- Cari Travel -->
                <a
                    href="{{ route('dashboard.user.search') }}"
                    class="group border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-md"
                >

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-status text-brand">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>

                    <h4 class="mt-4 font-semibold text-ink">
                        Cari Travel
                    </h4>

                    <p class="mt-1 text-sm leading-5 text-brand-support">
                        Temukan jadwal perjalanan
                    </p>

                    <span class="mt-4 inline-block text-sm font-bold text-brand">
                        Cari sekarang →
                    </span>

                </a>


                <!-- Pesanan -->
                <a
                    href="{{ route('dashboard.user.user-tiket') }}"
                    class="group border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-md"
                >

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-status text-brand">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                        </svg>
                    </div>

                    <h4 class="mt-4 font-semibold text-ink">
                        Pesanan Saya
                    </h4>

                    <p class="mt-1 text-sm leading-5 text-brand-support">
                        Lihat tiket dan booking
                    </p>

                    <span class="mt-4 inline-block text-sm font-bold text-brand">
                        Lihat pesanan →
                    </span>

                </a>


                <!-- Pembayaran -->
                <a
                    href="{{ route('dashboard.user.user-tiket') }}"
                    class="group border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-md"
                >

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EAF3FB] text-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>

                    <h4 class="mt-4 font-semibold text-ink">
                        Pembayaran
                    </h4>

                    <p class="mt-1 text-sm leading-5 text-brand-support">
                        Kelola transaksi perjalanan
                    </p>

                    <span class="mt-4 inline-block text-sm font-bold text-brand">
                        Lihat transaksi →
                    </span>

                </a>


                <!-- Profil -->
                <a
                    href="{{ route('dashboard.user.user-akun') }}"
                    class="group border border-border bg-surface p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand hover:shadow-md"
                >

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-status text-brand">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>

                    <h4 class="mt-4 font-semibold text-ink">
                        Profil Saya
                    </h4>

                    <p class="mt-1 text-sm leading-5 text-brand-support">
                        Kelola informasi akun
                    </p>

                    <span class="mt-4 inline-block text-sm font-bold text-brand">
                        Edit profil →
                    </span>

                </a>

            </div>

        </section>


        <!-- ================= BOOKING ================= -->
        <section>

            <div class="mb-5 flex items-end justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-brand">
                        Perjalanan
                    </p>

                    <h3 class="mt-1 text-xl font-bold text-ink">
                        Booking Terbaru
                    </h3>

                </div>

                <a
                    href="{{ route('dashboard.user.user-tiket') }}"
                    class="text-sm font-bold text-brand transition hover:text-brand-dark"
                >
                    Lihat semua →
                </a>

            </div>


            <!-- Empty State -->
            <div class="border border-dashed border-border bg-surface px-6 py-12 text-center shadow-sm">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-status text-brand">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h13l4 4v7H3V6z"/>
                        <circle cx="7.5" cy="17.5" r="2"/>
                        <circle cx="16.5" cy="17.5" r="2"/>
                        <path d="M16 6v4h4"/>
                        <path d="M7 10h4"/>
                    </svg>
                </div>

                <h4 class="mt-5 text-lg font-bold text-ink">
                    Belum ada perjalanan
                </h4>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-brand-support">
                    Kamu belum memiliki booking perjalanan.
                    Cari jadwal travel yang tersedia dan mulai perjalananmu.
                </p>

                <a
                    href="{{ route('dashboard.user.search') }}"
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-brand px-5 py-3 text-sm font-bold text-surface transition hover:bg-brand-dark"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Cari Perjalanan</span>
                </a>

            </div>

        </section>

    </main>


    <!-- ================= FOOTER ================= -->
    <footer class="mt-12 border-t border-border bg-surface">

        <div class="mx-auto flex w-full max-w-7xl flex-col gap-2 px-4 py-6 text-center sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8 sm:text-left">

            <p class="text-xs text-brand-support sm:text-sm">
                © {{ date('Y') }} TravelKu. Semua hak dilindungi.
            </p>

            <p class="text-xs text-brand-support sm:text-sm">
                Perjalanan nyaman, tujuan menyenangkan.
            </p>

        </div>

    </footer>

</body>

</html>