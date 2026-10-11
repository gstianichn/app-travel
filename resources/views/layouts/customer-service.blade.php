<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Customer Service - TravelKu')</title>


    <!-- Vite Assets -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <!-- Customer Service Theme Stylesheet -->
    @include('dashboard.customer-service.partials.styles')

    @stack('styles')
    @fonts('overpass')
    {{-- Brand skin and ds- components: loaded last so it wins over the role's own styles --}}
    @vite(['resources/css/dashboard-staff.css'])
</head>
<body class="cs-body" data-staff="cs">

    <!-- Backdrop untuk Tampilan Mobile saat Sidebar Aktif -->
    <div id="cs-sidebar-overlay" class="sidebar-overlay" onclick="toggleCsSidebar(false)"></div>

    <div class="cs-app-layout">

        <!-- Sidebar Navigasi Customer Service -->
        @include('dashboard.customer-service.partials.sidebar')

        <!-- Wrapper Konten Utama (Header + Body Konten) -->
        <div class="cs-main-wrapper">

            <!-- Topbar Header Customer Service -->
            @include('dashboard.customer-service.partials.topbar')

            <!-- Flash Alert Messages -->
            @if (session('success'))
                <div class="cs-alert cs-alert-success" role="alert">
                    <div class="cs-alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="cs-alert-text">{{ session('success') }}</div>
                    <button type="button" class="cs-alert-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="cs-alert cs-alert-danger" role="alert">
                    <div class="cs-alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="cs-alert-text">{{ session('error') }}</div>
                    <button type="button" class="cs-alert-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
                </div>
            @endif

            @if (session('info'))
                <div class="cs-alert cs-alert-info" role="alert">
                    <div class="cs-alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <div class="cs-alert-text">{{ session('info') }}</div>
                    <button type="button" class="cs-alert-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
                </div>
            @endif

            <!-- Dynamic Content Area -->
            <main class="cs-main-content">
                @unless (request()->routeIs('dashboard.customer-service.index'))
                    {{-- TODO: live chat, chat history, file sending and escalation have no backend yet --}}
                    <p class="ds-notice" role="note" style="margin-bottom: 20px;">
                        <b>Contoh tampilan.</b> Percakapan, nama, dan nomor di halaman ini belum tersambung ke data sistem.
                        Untuk mencari pesanan sungguhan, pakai <a href="{{ route('dashboard.customer-service.index') }}" style="font-weight:700; text-decoration:underline;">pencarian di beranda</a>.
                    </p>
                @endunless
                @yield('content')
            </main>

            <!-- Footer CS Minimalis -->
            <footer class="cs-footer">
                <div class="cs-footer-inner">
                    <span>&copy; {{ date('Y') }} TravelKu</span>
                </div>
            </footer>

        </div>

    </div>

    <!-- Modals Stack -->
    @stack('modals')

    <!-- Customer Service Interaction Scripts -->
    @include('dashboard.customer-service.partials.scripts')

    @stack('scripts')

</body>
</html>
