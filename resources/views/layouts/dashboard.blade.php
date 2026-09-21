<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('page-title', 'Portal Penumpang') — FastShuttle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand:        #087f55;
            --brand-dark:   #066b47;
            --brand-deep:   #055c3d;
            --brand-light:  #e3f5ee;
            --brand-soft:   #f0f8f5;
            --accent:       #0a9e6a;
            --accent-light: #d4f0e5;
            --page:         #f5f8f7;
            --surface:      #ffffff;
            --text:         #17221d;
            --text-sec:     #68766f;
            --text-muted:   #98a39e;
            --border:       #e2eae6;
            --sidebar-w:    268px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: var(--page);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ============================================================
           SIDEBAR
        ============================================================ */
        .u-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            z-index: 100;
            background: linear-gradient(175deg, #055c3d 0%, #087f55 45%, #0a9e6a 100%);
            box-shadow: 6px 0 40px rgba(8,127,85,.20);
            overflow: hidden;
        }

        /* subtle decorative circles */
        .u-sidebar::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            pointer-events: none;
        }
        .u-sidebar::after {
            content: '';
            position: absolute;
            bottom: 60px; left: -80px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(10,158,106,.20);
            pointer-events: none;
        }

        /* ---- BRAND ---- */
        .u-brand {
            padding: 22px 22px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,.10);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        .u-brand-logo {
            width: 42px; height: 42px;
            border-radius: 14px;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(4px);
        }
        .u-brand-name  { font-size: 17px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -.3px; }
        .u-brand-sub   { font-size: 10px; color: rgba(255,255,255,.55); font-weight: 500; }

        /* ---- NAV SECTIONS ---- */
        .u-nav { flex: 1; padding: 18px 14px; overflow-y: auto; position: relative; z-index: 1; }

        .u-nav::-webkit-scrollbar { width: 4px; }
        .u-nav::-webkit-scrollbar-track { background: transparent; }
        .u-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }

        .u-nav-section-title {
            font-size: 9.5px; font-weight: 700; letter-spacing: 1.4px;
            text-transform: uppercase; color: rgba(255,255,255,.40);
            padding: 0 10px; margin-bottom: 8px;
        }
        .u-nav-section + .u-nav-section { margin-top: 20px; }

        .u-nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 12px;
            border-radius: 12px;
            margin-bottom: 3px;
            color: rgba(255,255,255,.72);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all .18s ease;
            position: relative;
        }
        .u-nav-link:hover {
            background: rgba(255,255,255,.10);
            color: #fff;
            transform: translateX(3px);
        }
        .u-nav-link.active {
            background: #fff;
            color: var(--brand);
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(0,0,0,.12), 0 1px 4px rgba(0,0,0,.08);
        }
        .u-nav-link.active svg { color: var(--brand); }
        .u-nav-icon { width: 20px; min-width: 20px; display: flex; align-items: center; justify-content: center; }

        .u-nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 9px; font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
        }
        .u-nav-link.active .u-nav-badge { background: var(--brand); }

        .u-nav-sublink {
            display: flex; align-items: center; gap: 11px;
            padding: 8px 12px;
            border-radius: 10px; margin-bottom: 2px;
            color: rgba(255,255,255,.50);
            font-size: 12.5px; font-weight: 500;
            text-decoration: none; transition: .16s;
        }
        .u-nav-sublink:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.8); }
        .u-nav-sublink.active { background: rgba(255,255,255,.14); color: #fff; }

        .u-nav-divider { border: none; border-top: 1px solid rgba(255,255,255,.09); margin: 12px 0; }

        /* ---- SIDEBAR PROFILE ---- */
        .u-sidebar-profile {
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,.10);
            flex-shrink: 0;
            position: relative; z-index: 1;
        }
        .u-profile-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.10);
            backdrop-filter: blur(4px);
        }
        .u-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(255,255,255,.30), rgba(255,255,255,.10));
            border: 1px solid rgba(255,255,255,.20);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .u-profile-name  { font-size: 12px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
        .u-profile-role  { font-size: 10px; color: rgba(255,255,255,.50); }
        .u-logout-btn {
            margin-left: auto; background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,.45); padding: 6px; border-radius: 8px;
            transition: .18s; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .u-logout-btn:hover { background: rgba(239,68,68,.75); color: #fff; }

        /* ============================================================
           MAIN CONTENT
        ============================================================ */
        .u-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ---- TOPBAR ---- */
        .u-topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(245,248,247,.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            height: 66px;
            display: flex; align-items: center;
            padding: 0 28px;
            gap: 16px;
        }

        /* Breadcrumb / page title area */
        .u-topbar-title {
            font-size: 15px; font-weight: 700;
            color: var(--text);
            flex: 1;
        }
        .u-topbar-subtitle {
            font-size: 11px; color: var(--text-muted); font-weight: 400;
            margin-top: 1px;
        }

        /* Mobile brand (hidden on desktop) */
        .u-topbar-mobile-brand {
            display: none;
            font-size: 16px; font-weight: 800; color: var(--brand);
            align-items: center; gap: 8px;
        }

        .u-topbar-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

        /* Notif button */
        .u-notif-btn {
            position: relative;
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 11px; border: 1px solid var(--border);
            background: #fff;
            color: var(--text-sec);
            text-decoration: none;
            transition: .18s;
        }
        .u-notif-btn:hover { background: var(--brand-light); color: var(--brand); border-color: #cdeadd; }
        .u-notif-dot {
            position: absolute; top: 7px; right: 7px;
            width: 8px; height: 8px;
            background: #ef4444; border-radius: 50%;
            border: 2px solid var(--page);
        }

        /* Help button */
        .u-help-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0 14px; height: 38px;
            border-radius: 11px;
            background: var(--brand); color: #fff;
            font-size: 12px; font-weight: 600;
            text-decoration: none; transition: .18s;
            border: none; cursor: pointer;
        }
        .u-help-btn:hover { background: var(--brand-dark); }

        /* User chip (topbar right) */
        .u-user-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 12px 4px 4px;
            border-radius: 99px;
            background: #fff; border: 1px solid var(--border);
            cursor: default;
        }
        .u-user-chip-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            color: #fff; font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .u-user-chip-name { font-size: 12px; font-weight: 600; color: var(--text); }

        /* ---- CONTENT AREA ---- */
        .u-content { padding: 24px 28px 60px; flex: 1; }

        /* ============================================================
           BOTTOM NAV (Mobile only)
        ============================================================ */
        .u-bottom-nav {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(16px);
            border-top: 1px solid var(--border);
            z-index: 200;
            padding: 6px 4px calc(6px + env(safe-area-inset-bottom));
            box-shadow: 0 -8px 30px rgba(8,127,85,.08);
        }

        .u-bottom-nav-inner {
            display: flex;
            max-width: 420px;
            margin: 0 auto;
        }

        .u-tab {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px;
            padding: 6px 4px;
            text-decoration: none;
            font-size: 9.5px; font-weight: 500;
            color: var(--text-muted);
            border-radius: 12px;
            transition: all .18s;
            position: relative;
        }
        .u-tab:not(.active):hover { color: var(--text-sec); background: var(--brand-light); }
        .u-tab.active { color: var(--brand); font-weight: 700; }

        /* Pill indicator on active tab */
        .u-tab-pill {
            position: absolute;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 32px; height: 3px;
            border-radius: 0 0 6px 6px;
            background: var(--brand);
        }

        .u-tab svg { width: 22px; height: 22px; transition: .18s; }
        .u-tab.active svg { transform: scale(1.08); }

        /* Notif badge on tab */
        .u-tab-badge {
            position: absolute;
            top: 4px;
            width: 16px; height: 16px;
            background: #ef4444; color: #fff;
            font-size: 8px; font-weight: 700;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }
        #tab-notifikasi .u-tab-badge { right: calc(50% - 20px); }

        /* Avatar tab */
        .u-tab-avatar {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            color: #fff; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 230px; }
        }

        @media (max-width: 768px) {
            .u-sidebar { display: none; }
            .u-main { margin-left: 0; }
            .u-content { padding: 16px 16px 100px; }
            .u-topbar { padding: 0 16px; height: 58px; }
            .u-topbar-title { display: none; }
            .u-topbar-mobile-brand { display: flex; }
            .u-user-chip { display: none; }
            .u-help-btn span { display: none; }
            .u-help-btn { padding: 0; width: 38px; border-radius: 11px; justify-content: center; }
            .u-bottom-nav { display: block; }
        }
    </style>
</head>
<body>

<div style="display:flex; min-height:100vh;">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="u-sidebar">

        {{-- Brand --}}
        <div class="u-brand">
            <div class="u-brand-logo">🚌</div>
            <div>
                <div class="u-brand-name">FastShuttle</div>
                <div class="u-brand-sub">Portal Penumpang</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="u-nav">

            {{-- Menu Utama --}}
            <div class="u-nav-section">
                <div class="u-nav-section-title">Menu Utama</div>

                <a href="{{ route('dashboard.user.index') }}"
                   class="u-nav-link {{ request()->routeIs('dashboard.user.index') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('dashboard.user.search') }}"
                   class="u-nav-link {{ request()->routeIs('dashboard.user.search') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                        </svg>
                    </span>
                    <span>Cari Jadwal</span>
                </a>

                <a href="{{ route('dashboard.user.user-tiket') }}"
                   class="u-nav-link {{ request()->routeIs('dashboard.user.user-tiket') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/>
                        </svg>
                    </span>
                    <span>Tiket Saya</span>
                </a>

                <a href="{{ route('dashboard.user.user-notifikasi') }}"
                   class="u-nav-link {{ request()->routeIs('dashboard.user.user-notifikasi') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </span>
                    <span>Notifikasi</span>
                    <span class="u-nav-badge">3</span>
                </a>

                <a href="{{ route('dashboard.user.user-akun') }}"
                   class="u-nav-link {{ request()->routeIs('dashboard.user.user-akun') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                        </svg>
                    </span>
                    <span>Akun & Profil</span>
                </a>
            </div>

            <hr class="u-nav-divider">

            {{-- Perjalanan --}}
            <div class="u-nav-section">
                <div class="u-nav-section-title">Perjalanan</div>

                <a href="{{ route('dashboard.user.user-promo') }}"
                   class="u-nav-sublink {{ request()->routeIs('dashboard.user.user-promo') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m9 9 6 6m0-6-6 6"/>
                        </svg>
                    </span>
                    <span>Voucher Promo</span>
                </a>

                <a href="{{ route('dashboard.user.user-refund') }}"
                   class="u-nav-sublink {{ request()->routeIs('dashboard.user.user-refund') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6 6-6"/>
                        </svg>
                    </span>
                    <span>Refund & Pembatalan</span>
                </a>

                <a href="{{ route('dashboard.user.user-history') }}"
                   class="u-nav-sublink {{ request()->routeIs('dashboard.user.user-history') ? 'active' : '' }}">
                    <span class="u-nav-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </span>
                    <span>Riwayat Perjalanan</span>
                </a>
            </div>

            <hr class="u-nav-divider">

            {{-- Bantuan --}}
            <div class="u-nav-section">
                <div class="u-nav-section-title">Bantuan</div>
                <a href="https://wa.me/62812345678" target="_blank" class="u-nav-sublink">
                    <span class="u-nav-icon">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                        </svg>
                    </span>
                    <span>WhatsApp CS</span>
                </a>
            </div>

        </nav>

        {{-- Profile & Logout --}}
        <div class="u-sidebar-profile">
            <div class="u-profile-card">
                <div class="u-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div style="flex:1; min-width:0;">
                    <div class="u-profile-name">{{ auth()->user()->name ?? 'Penumpang' }}</div>
                    <div class="u-profile-role">Pelanggan</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="u-logout-btn" title="Keluar">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ===================== MAIN WRAPPER ===================== --}}
    <div class="u-main">

        {{-- TOPBAR --}}
        <header class="u-topbar">

            {{-- Mobile brand --}}
            <div class="u-topbar-mobile-brand">
                <span style="font-size:20px;">🚌</span>
                <span>FastShuttle</span>
            </div>

            {{-- Desktop title --}}
            <div class="u-topbar-title" style="display:block;">
                <div style="font-size:15px; font-weight:700; color:var(--text);">@yield('page-title', 'Dashboard')</div>
                <div class="u-topbar-subtitle">Selamat datang, {{ auth()->user()->name ?? 'Penumpang' }} 👋</div>
            </div>

            <div class="u-topbar-actions">

                {{-- Notifikasi --}}
                <a href="{{ route('dashboard.user.user-notifikasi') }}" class="u-notif-btn" id="topbar-notif-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="u-notif-dot"></span>
                </a>

                {{-- Bantuan --}}
                <a href="https://wa.me/62812345678" target="_blank" class="u-help-btn">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                    </svg>
                    <span>Bantuan</span>
                </a>

                {{-- User chip --}}
                <div class="u-user-chip">
                    <div class="u-user-chip-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="u-user-chip-name">{{ auth()->user()->name ?? 'Penumpang' }}</span>
                </div>

            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="u-content">
            @yield('content')
        </main>

    </div>

    {{-- ===================== BOTTOM NAV (Mobile) ===================== --}}
    <nav class="u-bottom-nav" role="navigation" aria-label="Navigasi Utama">
        <div class="u-bottom-nav-inner">

            {{-- Beranda --}}
            @php $isHome = request()->routeIs('dashboard.user.index'); @endphp
            <a href="{{ route('dashboard.user.index') }}" id="tab-beranda" class="u-tab {{ $isHome ? 'active' : '' }}">
                @if($isHome)<span class="u-tab-pill"></span>@endif
                <svg fill="{{ $isHome ? 'rgba(8,127,85,.15)' : 'none' }}" stroke="{{ $isHome ? 'var(--brand)' : 'var(--text-muted)' }}" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Beranda</span>
            </a>

            {{-- Tiket --}}
            @php $isTicket = request()->routeIs('dashboard.user.user-tiket'); @endphp
            <a href="{{ route('dashboard.user.user-tiket') }}" id="tab-tiket" class="u-tab {{ $isTicket ? 'active' : '' }}">
                @if($isTicket)<span class="u-tab-pill"></span>@endif
                <svg fill="{{ $isTicket ? 'rgba(8,127,85,.15)' : 'none' }}" stroke="{{ $isTicket ? 'var(--brand)' : 'var(--text-muted)' }}" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2H5z"/>
                </svg>
                <span>Tiket Saya</span>
            </a>

            {{-- Notifikasi --}}
            @php $isNotif = request()->routeIs('dashboard.user.user-notifikasi'); @endphp
            <a href="{{ route('dashboard.user.user-notifikasi') }}" id="tab-notifikasi" class="u-tab {{ $isNotif ? 'active' : '' }}" style="position:relative;">
                @if($isNotif)<span class="u-tab-pill"></span>@endif
                <svg fill="{{ $isNotif ? 'rgba(8,127,85,.15)' : 'none' }}" stroke="{{ $isNotif ? 'var(--brand)' : 'var(--text-muted)' }}" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="u-tab-badge">3</span>
                <span>Notifikasi</span>
            </a>

            {{-- Akun --}}
            @php $isAccount = request()->routeIs('dashboard.user.user-akun'); @endphp
            <a href="{{ route('dashboard.user.user-akun') }}" id="tab-akun" class="u-tab {{ $isAccount ? 'active' : '' }}">
                @if($isAccount)<span class="u-tab-pill"></span>@endif
                @if($isAccount)
                    <div class="u-tab-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                @else
                    <svg fill="none" stroke="var(--text-muted)" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                @endif
                <span>Akun</span>
            </a>

        </div>
    </nav>

</div>

</body>
</html>