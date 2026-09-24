<style>
    .admin-dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 26px;
        width: 100%;
    }

    /* =========================================
       WELCOME BANNER
    ========================================== */
    .welcome-banner {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 24px 28px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 60%, #045236 100%);
        border-radius: 18px;
        color: white;
        box-shadow: 0 8px 24px rgba(8, 127, 85, 0.16);
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-text h1 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.2px;
    }

    .welcome-text p {
        margin: 0;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.88);
        max-width: 620px;
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
        gap: 7px;
        padding: 7px 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 12px;
        font-weight: 600;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .chip-status-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #bbf7d0;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.35);
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
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        gap: 14px;
        border-left: 5px solid;
    }

    .alert-card.refund-alert {
        border-color: #f59e0b;
        background: linear-gradient(to right, #fffdf8, #ffffff);
    }

    .alert-card.maintenance-alert {
        border-color: #ef4444;
        background: linear-gradient(to right, #fffaf9, #ffffff);
    }

    .alert-card.clean-alert {
        border-color: #10b981;
        background: linear-gradient(to right, #f7fdf9, #ffffff);
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
        gap: 10px;
    }

    .alert-icon-pill {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .alert-card.refund-alert .alert-icon-pill {
        background: #fef3c7;
        color: #d97706;
    }

    .alert-card.maintenance-alert .alert-icon-pill {
        background: #fee2e2;
        color: #dc2626;
    }

    .alert-card.clean-alert .alert-icon-pill {
        background: #dcfce7;
        color: #15803d;
    }

    .alert-card-title h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
    }

    .alert-card-title p {
        margin: 2px 0 0;
        font-size: 11px;
        color: #7b8882;
    }

    .alert-badge-count {
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .refund-alert .alert-badge-count {
        background: #fef3c7;
        color: #b45309;
    }

    .maintenance-alert .alert-badge-count {
        background: #fee2e2;
        color: #b91c1c;
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
        padding: 10px 12px;
        border-radius: 10px;
        background: #fbfdfc;
        border: 1px solid #edf2ef;
        font-size: 12px;
    }

    .alert-item-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .alert-item-code {
        font-weight: 700;
        color: #17221d;
    }

    .alert-item-sub {
        font-size: 11px;
        color: #6e7d76;
    }

    .alert-item-action {
        font-weight: 700;
        font-size: 11px;
        color: #087f55;
        text-decoration: none;
    }

    .alert-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-top: 4px;
    }

    .alert-footer-link {
        font-size: 12px;
        font-weight: 600;
        color: #087f55;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .alert-footer-link:hover {
        text-decoration: underline;
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
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        padding: 22px 20px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(8, 127, 85, 0.08);
    }

    .metric-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .metric-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #72817a;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 26px;
        font-weight: 800;
        color: #17221d;
        line-height: 1.1;
    }

    .metric-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .metric-icon-box.tickets {
        background: #e3f5ee;
        color: #087f55;
    }

    .metric-icon-box.revenue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .metric-icon-box.fleet {
        background: #fef3c7;
        color: #d97706;
    }

    .metric-icon-box.seats {
        background: #f3e8ff;
        color: #9333ea;
    }

    .metric-bottom {
        padding-top: 12px;
        border-top: 1px solid #f1f5f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #87948e;
    }

    .metric-badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 10px;
    }

    .metric-badge-status.green {
        background: #dcfce7;
        color: #15803d;
    }

    .metric-badge-status.blue {
        background: #e0f2fe;
        color: #0369a1;
    }

    .metric-badge-status.orange {
        background: #fef3c7;
        color: #b45309;
    }

    .metric-badge-status.purple {
        background: #f3e8ff;
        color: #7e22ce;
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
        font-weight: 700;
        color: #17221d;
        margin: 0;
    }

    .section-header-wrap p {
        margin: 3px 0 0;
        font-size: 12px;
        color: #7b8882;
    }

    /* =========================================
       2. JADWAL PERJALANAN TERDEKAT (TABLE)
    ========================================== */
    .schedule-panel-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .schedule-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid #edf2ef;
    }

    .schedule-panel-title {
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .schedule-badge-total {
        padding: 3px 9px;
        border-radius: 12px;
        background: #e3f5ee;
        color: #087f55;
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
        background: #fbfdfc;
        color: #72817a;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf1ef;
    }

    .schedule-table td {
        padding: 15px 20px;
        color: #27342e;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f3;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .schedule-table tbody tr:hover {
        background: #fbfdfc;
    }

    /* ROUTE CELL */
    .route-cell {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 700;
        color: #17221d;
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
        color: #17221d;
        font-size: 13px;
    }

    .time-date {
        font-size: 11px;
        color: #7b8882;
    }

    /* DRIVER CELL */
    .driver-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .driver-avatar-mini {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #e3f5ee;
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
        color: #27342e;
    }

    .driver-unassigned {
        color: #92a099;
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
        background: #e9eeeb;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.3s ease;
    }

    .progress-bar-fill.safe {
        background: #10b981;
    }

    .progress-bar-fill.medium {
        background: #f59e0b;
    }

    .progress-bar-fill.full {
        background: #ef4444;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-pill.scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-pill.active-trip {
        background: #dcfce7;
        color: #15803d;
    }

    .empty-schedules-notice {
        padding: 45px 20px;
        text-align: center;
        color: #8a9791;
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
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 14px;
        padding: 16px;
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .quick-nav-item:hover {
        border-color: #087f55;
        box-shadow: 0 6px 18px rgba(8, 127, 85, 0.10);
        transform: translateY(-2px);
    }

    .quick-nav-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f0f7f4;
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
        color: #17221d;
    }

    .quick-nav-text p {
        margin: 0;
        font-size: 11px;
        color: #7b8882;
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
