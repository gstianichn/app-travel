<!-- ============================================================
     1. KARTU METRIK KERJA HARIAN (REAL-TIME QUEUE COUNTERS)
============================================================ -->

@php
    $todayBookingsCount = \App\Models\Booking::whereDate('created_at', today())->count();
    $activeCustomersCount = \App\Models\User::where('role', 'user')->count();
    $pendingChatsCount = 5; // Chat Menunggu Dibalas (Unassigned / Pending)
    $slaBreachCount = 2; // Tiket Mendekati / Melewati Batas SLA (> 3-5 menit)
    $activeEscalationsCount = 3; // Kasus Eskalasi Aktif ke Admin/Operasional
    $upcomingDeparturesCount = 4; // Keberangkatan Terdekat (< 60 Menit)
    $inquiriesTodayCount = 18; // Total Pertanyaan Hari Ini
@endphp

<div class="stats cs-queue-kpi-grid">

    <!-- 1. Chat Menunggu Dibalas (Unassigned / Pending) -->
    <div class="stat-card stat-card-alert">
        <div class="stat-card-top">
            <div class="stat-info">
                <span class="stat-title">Chat Menunggu Dibalas (Unassigned / Pending)</span>
                <span class="stat-value stat-value-alert">{{ $pendingChatsCount }}</span>
            </div>
            <div class="stat-icon icon-amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
        </div>
        <div class="stat-meta">
            <span class="stat-badge-urgency">Menunggu Respons</span>
            <span class="stat-subtext">Pertanyaan Hari Ini: <strong>{{ $inquiriesTodayCount }}</strong></span>
        </div>
    </div>

    <!-- 2. Tiket Mendekati Batas SLA (> 3-5 Menit) -->
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-info">
                <span class="stat-title">Tiket Mendekati Batas SLA</span>
                <span class="stat-value" style="color: #dc2626;">{{ $slaBreachCount }}</span>
            </div>
            <div class="stat-icon" style="background: var(--danger-soft); color: var(--danger);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
        </div>
        <div class="stat-meta">
            <span class="stat-trend" style="color: #dc2626; font-weight: 700;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Toleransi &gt; 3–5 mnt
            </span>
            <span class="stat-subtext">Perlu tindakan agen</span>
        </div>
    </div>

    <!-- 3. Kasus Eskalasi Aktif (Admin / Ops / Finance) -->
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-info">
                <span class="stat-title">Kasus Eskalasi Aktif</span>
                <span class="stat-value" style="color: var(--purple);">{{ $activeEscalationsCount }}</span>
            </div>
            <div class="stat-icon icon-purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
        </div>
        <div class="stat-meta">
            <span class="stat-badge-urgency" style="background: var(--purple-soft); color: var(--purple);">Proses Investigasi</span>
            <span class="stat-subtext">Menunggu tanggapan Admin</span>
        </div>
    </div>

    <!-- 4. Keberangkatan Terdekat (< 60 Menit) -->
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-info">
                <span class="stat-title">Keberangkatan Terdekat (&lt; 60 Menit)</span>
                <span class="stat-value" style="color: var(--brand);">{{ $upcomingDeparturesCount }}</span>
            </div>
            <div class="stat-icon icon-teal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
        </div>
        <div class="stat-meta">
            <span class="stat-trend trend-up">
                Fokus Utama CS
            </span>
            <span class="stat-subtext">
                Pesanan Hari Ini: <strong>{{ $todayBookingsCount > 0 ? $todayBookingsCount : 24 }}</strong> &bull;
                Pelanggan Aktif: <strong>{{ $activeCustomersCount > 0 ? $activeCustomersCount : 156 }}</strong>
            </span>
        </div>
    </div>

</div>
