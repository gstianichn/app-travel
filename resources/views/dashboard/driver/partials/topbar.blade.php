<!-- TOPBAR DRIVER OPERASIONAL -->
<header class="topbar">
    <div class="topbar-left flex items-center gap-3 min-w-0">
        <button type="button" class="btn-sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka Menu Navigasi" style="display: none; background: none; border: none; cursor: pointer; color: var(--lp-ink); padding: 4px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="page-title min-w-0">
            <p class="m-0 text-sm text-slate-600 truncate">
                <strong class="text-emerald-950">TravelKu</strong> &middot; Panel Driver
            </p>
        </div>
    </div>

    <div class="topbar-right flex items-center gap-3 shrink-0">
        <!-- Ikon Lonceng Notifikasi -->
        <div class="relative">
            <a href="{{ route('dashboard.driver.notifikasi') }}"
               class="relative inline-flex items-center justify-center w-10 h-10 rounded-full border border-[#DEDAC9] bg-[#FFFDF8] text-[#13201B] hover:bg-[#EEF6F1] transition"
               title="Notifikasi Masuk"
               aria-label="Notifikasi Masuk ({{ $unreadNotificationsCount ?? 0 }} belum dibaca)">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                @if (($unreadNotificationsCount ?? 0) > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
                        {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Profil Driver -->
        <div class="profile">
            <div class="avatar">
                {{ strtoupper(substr($user->name ?? 'D', 0, 1)) }}
            </div>
            <div class="profile-info">
                <strong>{{ $user->name ?? 'Pengemudi' }}</strong>
                <span>{{ $activeSchedule->vehicle?->plate_number ?? 'Standby' }}</span>
            </div>
        </div>
    </div>
</header>
