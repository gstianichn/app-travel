<script>
    // ==========================================
    // CUSTOMER SERVICE DASHBOARD INTERACTIVITY
    // ==========================================

    // 1. Mobile Sidebar Drawer Toggle
    function toggleCsSidebar(forceState) {
        const sidebar = document.getElementById('cs-sidebar');
        const overlay = document.getElementById('cs-sidebar-overlay');
        if (!sidebar) return;

        const isOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('open');

        if (isOpen) {
            sidebar.classList.add('open');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // 2. CS Agent Status Switcher (Online / Sibuk / Istirahat)
    function toggleStatusDropdown() {
        const menu = document.getElementById('status-menu');
        if (menu) menu.classList.toggle('show');
    }

    function setAgentStatus(status, label, pillClass) {
        const pillBtn = document.getElementById('status-pill-btn');
        const textLabel = document.getElementById('status-text-label');
        const dot = document.getElementById('status-dot');
        const sidebarDot = document.getElementById('sidebar-status-dot');
        const menu = document.getElementById('status-menu');

        if (textLabel) textLabel.innerText = label;

        if (pillBtn) {
            pillBtn.className = 'status-pill ' + pillClass;
        }

        if (dot) {
            dot.className = 'status-indicator-dot dot-' + status;
        }

        if (sidebarDot) {
            sidebarDot.className = 'agent-status-dot ' + status;
            sidebarDot.title = 'Status: ' + label;
        }

        // Simpan status di localStorage agar konsisten antar tab
        try {
            localStorage.setItem('travelku_cs_status', JSON.stringify({ status, label, pillClass }));
        } catch (e) {}

        if (menu) menu.classList.remove('show');
    }

    // 3. Profile Dropdown Toggle
    function toggleProfileDropdown() {
        const menu = document.getElementById('profile-dropdown');
        if (menu) menu.classList.toggle('show');
    }

    // 4. Quick Lookup / Search Focus
    function focusQuickSearch(e) {
        if (e && e.preventDefault) e.preventDefault();
        const input = document.getElementById('global-booking-search');
        if (input) {
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            input.parentElement.style.transform = 'scale(1.03)';
            setTimeout(() => {
                input.parentElement.style.transform = '';
            }, 300);
        }
    }

    function handleQuickSearch(e) {
        if (e.key === 'Enter') {
            const query = e.target.value.trim();
            if (query.length > 0) {
                // Scroll ke tabel atau tampilkan prompt
                const targetSec = document.getElementById('quick-lookup');
                if (targetSec) {
                    targetSec.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }
    }

    // 5. Scroll to Section
    function scrollToSection(id, e) {
        if (e && e.preventDefault) e.preventDefault();
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Close Dropdowns When Clicking Outside
    document.addEventListener('click', function (e) {
        const statusWrapper = document.querySelector('.agent-status-dropdown-wrapper');
        const statusMenu = document.getElementById('status-menu');
        if (statusWrapper && !statusWrapper.contains(e.target) && statusMenu) {
            statusMenu.classList.remove('show');
        }

        const profileEl = document.querySelector('.profile');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileEl && !profileEl.contains(e.target) && profileDropdown) {
            profileDropdown.classList.remove('show');
        }
    });

    // Global Shortcut: Ctrl+K / Cmd+K to focus search
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            focusQuickSearch();
        }
    });

    // Restore saved agent status
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const saved = localStorage.getItem('travelku_cs_status');
            if (saved) {
                const data = JSON.parse(saved);
                if (data && data.status) {
                    setAgentStatus(data.status, data.label, data.pillClass);
                }
            }
        } catch (e) {}
    });
    // 6. Omnisearch Logic (PNR, WhatsApp, Nama, Plat Nomor)
    function handleOmnisearchInput(e) {
        if (e.key === 'Enter') {
            executeOmnisearch();
        }
    }

    function executeOmnisearch() {
        const input = document.getElementById('omnisearch-input');
        const container = document.getElementById('omnisearch-result-container');
        if (!input || !container) return;

        const val = input.value.trim().toLowerCase();
        if (val.length === 0) {
            input.focus();
            return;
        }

        // Database mock lookup interaktif
        const records = [
            {
                pnr: 'TRV-2026-0925',
                name: 'Budi Prasetyo',
                phone: '0812-9988-7766',
                plate: 'D 7182 AB',
                route: 'Bandung (Pasteur) ke Jakarta (Semanggi)',
                time: 'Hari Ini, 14:00 WIB • Kursi 03A',
                vehicle: 'Toyota HiAce Premio (D 7182 AB)',
                driver: 'Pak Suryanto (0812-7788-9900)',
                boarding: 'Proses Persiapan Pool Pasteur',
                status: 'Terkonfirmasi (Lunas)'
            },
            {
                pnr: 'TRV-2026-0811',
                name: 'Hendra Gunawan',
                phone: '0813-2233-4455',
                plate: 'B 8911 JKT',
                route: 'Jakarta (Semanggi) ke Bandung (Dipatiukur)',
                time: 'Hari Ini, 13:45 WIB • Kursi 01B',
                vehicle: 'Toyota HiAce Luxury (B 8911 JKT)',
                driver: 'Pak Joko (0811-3322-1144)',
                boarding: 'Boarding Gate 1 Semanggi',
                status: 'Terkonfirmasi (Lunas)'
            },
            {
                pnr: 'TRV-2026-0774',
                name: 'Siti Rahma',
                phone: '0878-1122-3344',
                plate: 'D 9021 CG',
                route: 'Bandung (Pasteur) ke Bandara Soetta (T3)',
                time: 'Hari Ini, 15:30 WIB • Kursi 02B',
                vehicle: 'Toyota HiAce Premio (D 9021 CG)',
                driver: 'Pak Dadang (0821-4455-6677)',
                boarding: 'Menunggu Jam Boarding',
                status: 'Lunas'
            }
        ];

        const match = records.find(r =>
            r.pnr.toLowerCase().includes(val) ||
            r.name.toLowerCase().includes(val) ||
            r.phone.replace(/[^0-9]/g, '').includes(val.replace(/[^0-9]/g, '')) ||
            r.plate.toLowerCase().replace(/\s/g, '').includes(val.replace(/\s/g, ''))
        ) || {
            pnr: val.toUpperCase().startsWith('TRV') ? val.toUpperCase() : 'TRV-' + Math.floor(1000 + Math.random() * 9000),
            name: val.length > 3 && isNaN(val) ? val.toUpperCase() : 'Penumpang Terdaftar',
            phone: val.startsWith('08') ? val : '0812-3456-7890',
            plate: 'D 7182 AB',
            route: 'Bandung (Pasteur) ke Jakarta (Semanggi)',
            time: 'Hari Ini, 14:00 WIB • Kursi 03A',
            vehicle: 'Toyota HiAce Premio (D 7182 AB)',
            driver: 'Pak Suryanto (0812-7788-9900)',
            boarding: 'Data Valid di Database',
            status: 'Terkonfirmasi (Lunas)'
        };

        document.getElementById('res-code').innerText = match.pnr;
        document.getElementById('res-status').innerText = match.status;
        document.getElementById('res-name').innerText = match.name;
        document.getElementById('res-phone').innerText = 'WhatsApp: ' + match.phone;
        document.getElementById('res-route').innerText = match.route;
        document.getElementById('res-time').innerText = match.time;
        document.getElementById('res-vehicle').innerText = match.vehicle;
        document.getElementById('res-driver').innerText = match.driver;
        document.getElementById('res-boarding').innerText = match.boarding;

        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // 7. Filter Trip Monitor Table
    function filterMonitorTable(status, btn) {
        document.querySelectorAll('.monitor-filter-bar .filter-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const rows = document.querySelectorAll('#trip-monitor-tbody tr');
        rows.forEach(row => {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function refreshTripMonitor() {
        const btn = event?.currentTarget;
        if (btn) {
            btn.innerHTML = '<span>Memperbarui...</span>';
            setTimeout(() => {
                btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg><span>Perbarui Data</span>';
                alert('Data pantauan armada dan delay keberangkatan berhasil diperbarui ke kondisi terkini.');
            }, 500);
        }
    }

    function triggerProactiveBroadcast(pnr, route, reason) {
        if (confirm(`Kirim pesan proaktif & buka Live Chat untuk seluruh penumpang armada ${route}?\n\nAlasan: ${reason}`)) {
            window.location.href = "{{ route('dashboard.customer-service.live-chat') }}";
        }
    }

    // 8. Follow-up Escalation
    function followUpEscalation(id, passenger, target) {
        alert(`Follow-Up Tiket Kasus #${id} (${passenger}):\n\nTiket saat ini sedang ditinjau oleh ${target}. Respon terbaru: "Sedang dalam verifikasi data operasional & rekam GPS armada."`);
    }
</script>
