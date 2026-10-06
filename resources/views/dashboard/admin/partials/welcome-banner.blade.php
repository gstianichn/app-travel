{{-- =========================================
     WELCOME BANNER (ANTISLOP EXECUTIVE CONSOLE)
========================================== --}}
<div class="welcome-banner">
    <div class="welcome-text">
        <div class="welcome-kicker">
            <span class="pulse-dot"></span>
            <span>LIVE DISPATCH CENTER &bull; TRAVELKU FASTSHUTTLE</span>
        </div>
        <h1>Selamat Datang, {{ $user->name ?? auth()->user()->name ?? 'Admin Utama' }}</h1>
        <p>
            Pantau metrik armada minibus, keterisian kursi hari ini, pendapatan real-time, dan kepatuhan operasional.
        </p>
    </div>

    <div class="welcome-chips">
        <div class="chip-date">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>{{ date('d F Y') }}</span>
        </div>
        <div class="chip-status-live">
            <span class="pulse-dot"></span>
            <span>Sistem Operasional Aktif</span>
        </div>
    </div>
</div>
