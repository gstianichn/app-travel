{{-- =========================================
         WELCOME BANNER
    ========================================== --}}
    <div class="welcome-banner">
        <div class="welcome-text">
            <h1>Selamat Datang, {{ $user->name ?? auth()->user()->name ?? 'Admin Utama' }} 👋</h1>
            <p>
                Pantau performa tiket, pendapatan hari ini, keterisian kursi armada, dan tindak lanjuti notifikasi operasional TravelKu secara real-time.
            </p>
        </div>

        <div class="welcome-chips">
            <div class="chip-date">
                <span>📅</span>
                <span>{{ date('d F Y') }}</span>
            </div>
            <div class="chip-status-live">
                <span class="pulse-dot"></span>
                <span>Sistem Operasional Aktif</span>
            </div>
        </div>
    </div>
