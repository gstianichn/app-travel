<style>
    .admin-dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 28px;
        width: 100%;
    }

    /* =========================================
       WELCOME BANNER (ANTISLOP EXECUTIVE CONSOLE)
    ========================================== */
    .welcome-banner {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 24px 30px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 60%, #045236 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 18px;
        color: white;
        box-shadow: 0 8px 24px rgba(8, 127, 85, 0.16);
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(52, 211, 153, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #A7F3D0;
        margin-bottom: 6px;
    }

    .welcome-text h1 {
        margin: 0 0 6px;
        font-size: 23px;
        font-weight: 800;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }

    .welcome-text p {
        margin: 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.78);
        max-width: 640px;
        line-height: 1.5;
    }

    .welcome-chips {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
    }

    .chip-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.08);
        font-size: 12px;
        font-weight: 600;
        color: #FFFFFF;
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
    }

    .chip-status-live {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 600;
        color: #A7F3D0;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #34D399;
        box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.35);
        display: inline-block;
        animation: pulseLive 2s infinite ease-in-out;
    }

    @keyframes pulseLive {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.8; }
    }

    /* =========================================
       SECTION TITLES
    ========================================== */
    .section-header-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .section-header-wrap h2 {
        font-size: 17px;
        font-weight: 800;
        color: #15241D;
        letter-spacing: -0.2px;
        margin: 0;
    }

    .section-header-wrap p {
        margin: 3px 0 0;
        font-size: 12px;
        color: #657770;
    }

    /* =========================================
       1. METRIK UTAMA (SUMMARY CARDS)
    ========================================== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .metric-card {
        background: #FFFFFF;
        border: 1px solid #E1E8E4;
        border-radius: 18px;
        padding: 22px 22px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(17, 32, 25, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -4px rgba(17, 32, 25, 0.08);
        border-color: #CFDCD4;
    }

    .metric-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .metric-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #6A7C74;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 26px;
        font-weight: 800;
        color: #14241D;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .metric-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .metric-icon-box.tickets {
        background: #E8F6EF;
        border: 1px solid #CDE8DB;
        color: #0E7952;
    }

    .metric-icon-box.revenue {
        background: #EFF6FC;
        border: 1px solid #D5E7F7;
        color: #1D6DAE;
    }

    .metric-icon-box.fleet {
        background: #FDF3EA;
        border: 1px solid #F8DFCE;
        color: #C85A32;
    }

    .metric-icon-box.seats {
        background: #F4EFFC;
        border: 1px solid #E3D6F7;
        color: #7A35C5;
    }

    .metric-bottom {
        padding-top: 13px;
        border-top: 1px solid #F0F4F2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #798B83;
    }

    .metric-badge-status {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .metric-badge-status.green {
        background: #E8F7F0;
        color: #0D734E;
        border: 1px solid #BEE8D3;
    }

    .metric-badge-status.blue {
        background: #EBF4FC;
        color: #166AB2;
        border: 1px solid #C4E0F7;
    }

    .metric-badge-status.orange {
        background: #FDF1EA;
        color: #B44A22;
        border: 1px solid #F6D1BF;
    }

    .metric-badge-status.purple {
        background: #F3EDFA;
        color: #6C35B5;
        border: 1px solid #DECCF5;
    }

    /* =========================================
       NOTIFIKASI PENTING (ALERTS SECTION)
    ========================================== */
    .alerts-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .alert-card {
        background: #FFFFFF;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 4px 16px rgba(17, 32, 25, 0.03);
        display: flex;
        flex-direction: column;
        gap: 14px;
        border: 1px solid #E1E8E4;
        border-left: 4px solid #0E7952;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .alert-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(17, 32, 25, 0.06);
    }

    .alert-card.refund-alert {
        border-left-color: #F59E0B;
        background: linear-gradient(to right, #FFFDF9, #FFFFFF);
    }

    .alert-card.maintenance-alert {
        border-left-color: #EF4444;
        background: linear-gradient(to right, #FFFAF9, #FFFFFF);
    }

    .alert-card.clean-alert {
        border-left-color: #10B981;
        background: linear-gradient(to right, #F8FCFA, #FFFFFF);
    }

    .alert-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .alert-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-card-title h3 {
        font-size: 14px;
        font-weight: 700;
        color: #14231D;
        margin: 0;
    }

    .alert-card-title p {
        font-size: 12px;
        color: #6B7D76;
        margin: 2px 0 0;
    }

    .alert-icon-pill {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .alert-card.refund-alert .alert-icon-pill {
        background: #FEF3C7;
        color: #B45309;
        border: 1px solid #FDE68A;
    }

    .alert-card.maintenance-alert .alert-icon-pill {
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    .alert-card.clean-alert .alert-icon-pill {
        background: #D1FAE5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    .alert-badge-count {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
        white-space: nowrap;
    }

    .alert-card.refund-alert .alert-badge-count {
        background: #FEF3C7;
        color: #B45309;
        border-color: #FDE68A;
    }

    .alert-item-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 11px;
        background: #F7FAF8;
        border: 1px solid #E8EFEA;
    }

    .alert-item-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .alert-item-code {
        font-size: 12px;
        font-weight: 700;
        color: #17241F;
    }

    .alert-item-sub {
        font-size: 11px;
        color: #6C7D76;
    }

    .alert-card-footer {
        padding-top: 10px;
        border-top: 1px solid #EDF3EF;
        display: flex;
        justify-content: flex-end;
    }

    .alert-footer-link {
        font-size: 12px;
        font-weight: 700;
        color: #087f55;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .alert-footer-link:hover {
        color: #066b47;
        text-decoration: underline;
    }

    /* =========================================
       2. JADWAL PERJALANAN TERDEKAT (TABLE)
    ========================================== */
    .schedule-panel-card {
        background: #FFFFFF;
        border: 1px solid #E1E8E4;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(17, 32, 25, 0.03);
        overflow: hidden;
    }

    .schedule-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid #EDF2EE;
    }

    .schedule-panel-title {
        font-size: 15px;
        font-weight: 800;
        color: #14231D;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .schedule-badge-total {
        padding: 3px 10px;
        border-radius: 14px;
        background: #e3f5ee;
        color: #087f55;
        border: 1px solid #cdeadd;
        font-size: 11px;
        font-weight: 700;
    }

    .table-scroll-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .schedule-table th {
        padding: 13px 20px;
        background: #F7FAF8;
        color: #5D6F67;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid #E1E8E4;
    }

    .schedule-table td {
        padding: 15px 20px;
        color: #24332C;
        font-size: 13px;
        border-bottom: 1px solid #EEF3F0;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .schedule-table tbody tr:hover {
        background: #F9FBFA;
    }

    /* ROUTE CELL */
    .route-cell {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 700;
        color: #15241D;
    }

    .route-arrow {
        color: #087f55;
        font-weight: 900;
    }

    /* TIME CELL */
    .time-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .time-val {
        font-weight: 700;
        color: #15241D;
        font-size: 13px;
    }

    .time-date {
        font-size: 11px;
        color: #72837C;
    }

    /* DRIVER CELL */
    .driver-cell {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .driver-avatar-mini {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #e3f5ee;
        border: 1px solid #cdeadd;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 11px;
        flex-shrink: 0;
    }

    .driver-name-text {
        font-weight: 600;
        color: #24332C;
    }

    .driver-unassigned {
        color: #92A099;
        font-style: italic;
        font-size: 12px;
    }

    /* LOAD FACTOR BAR CELL */
    .load-factor-wrap {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 140px;
    }

    .load-factor-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 600;
    }

    .progress-bar-bg {
        width: 100%;
        height: 7px;
        border-radius: 6px;
        background: #E8EFEA;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.3s ease;
    }

    .progress-bar-fill.safe {
        background: #10B981;
    }

    .progress-bar-fill.medium {
        background: #F59E0B;
    }

    .progress-bar-fill.full {
        background: #EF4444;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-pill.scheduled {
        background: #EBF4FC;
        color: #1768AF;
        border: 1px solid #C6E0F7;
    }

    .status-pill.active-trip {
        background: #E7F7EF;
        color: #0D734E;
        border: 1px solid #BEE8D3;
    }

    .empty-schedules-notice {
        padding: 45px 20px;
        text-align: center;
        color: #7D8E87;
        font-size: 13px;
    }

    /* =========================================
       MENU CEPAT GRID
    ========================================== */
    .quick-nav-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .quick-nav-item {
        background: #FFFFFF;
        border: 1px solid #E1E8E4;
        border-radius: 16px;
        padding: 16px 18px;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(17, 32, 25, 0.02);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .quick-nav-item:hover {
        border-color: #087f55;
        box-shadow: 0 8px 22px rgba(8, 127, 85, 0.12);
        transform: translateY(-3px);
    }

    .quick-nav-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #F2F8F5;
        border: 1px solid #DEECE4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .quick-nav-text h4 {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 700;
        color: #15241D;
    }

    .quick-nav-text p {
        margin: 0;
        font-size: 11px;
        color: #72837C;
    }

    /* =========================================
       RESPONSIVE
    ========================================== */
    @media (max-width: 1150px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .alerts-section {
            grid-template-columns: 1fr;
        }

        .quick-nav-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 650px) {
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .welcome-chips {
            align-items: flex-start;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .quick-nav-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
