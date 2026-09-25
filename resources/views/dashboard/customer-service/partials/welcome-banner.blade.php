<!-- ========================================
     WELCOME BANNER CUSTOMER SERVICE
========================================= -->

<div class="welcome">

    <div class="welcome-content">

        <div class="welcome-label-wrap">
            <span class="welcome-label">TravelKu</span>
            <span class="welcome-shift-badge">
                <span class="shift-dot"></span> Shift Aktif: 08:00 - 16:00 WIB
            </span>
        </div>

        <h1 class="welcome-heading">
            Selamat datang,
            <span class="welcome-user-name">{{ auth()->user()->name ?? 'Customer Service' }}</span> 👋
        </h1>

        <p class="welcome-description">
            Bantu pelanggan mendapatkan pengalaman perjalanan
            yang nyaman dan menyenangkan. Pastikan respons cepat dan eskalasikan kendala operasional ke tim terkait tepat waktu.
        </p>

        <div class="welcome-actions">
            <a href="{{ route('dashboard.customer-service.live-chat') }}" class="cs-btn cs-btn-light">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Mulai Sesi Live Chat</span>
            </a>
            <a href="#quick-lookup" class="cs-btn cs-btn-outline" onclick="focusQuickSearch(event)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Cari Status Tiket</span>
            </a>
        </div>

    </div>

    <div class="welcome-illustration-wrap">
        <div class="welcome-badge-float">
            <div class="float-icon">💬</div>
            <div class="float-text">
                <strong>98.4% Kepuasan</strong>
                <small>Berdasarkan 342 ulasan</small>
            </div>
        </div>
    </div>

</div>
