/**
 * ============================================================
 * ADMIN PANEL — Global Enhancement Script
 * Features:
 *   1. Real-time Live Search (Debounced 300ms, AJAX No-Reload)
 *   2. Auto-filter Dropdowns & Date Pickers (Instant AJAX)
 *   3. Auto-Slide / Smooth Scroll to Data Table on Search/Select
 *   4. Smooth Slide & Fade CSS Animation for Table Data
 *   5. AJAX Pagination & Reset Handling
 *   6. Sidebar Smooth Slide Transition
 *   7. Action Confirmations, Flash Auto-Hide & Modals
 * ============================================================
 */

let filterAbortController = null;

document.addEventListener("DOMContentLoaded", () => {
    injectGlobalFilterStyles();
    initSidebar();
    initAdminFilterSystem();
    initActionConfirmations();
    initFlashMessages();
    initInitialPageScroll();
});

// ----------------------------------------------------------------
// STYLES INJECTION — Smooth scrolling and table slide animation
// ----------------------------------------------------------------
function injectGlobalFilterStyles() {
    const style = document.createElement("style");
    style.id = "admin-panel-dynamic-styles";
    style.textContent = `
        html {
            scroll-behavior: smooth;
        }

        @keyframes adminDataSlideIn {
            0% {
                opacity: 0.35;
                transform: translateY(18px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-slide-in {
            animation: adminDataSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }

        .table-card, .table-container {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .table-filtering {
            opacity: 0.55 !important;
            transition: opacity 0.2s ease;
            pointer-events: none;
            position: relative;
        }

        .table-filtering::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #087f55, #20c997, #087f55);
            background-size: 200% 100%;
            animation: filterProgress 1s infinite linear;
            z-index: 10;
        }

        @keyframes filterProgress {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }

        .admin-sidebar {
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), width 0.3s ease !important;
        }
        .admin-main {
            transition: margin-left 0.3s ease, width 0.3s ease !important;
        }
        @media (max-width: 700px) {
            .admin-sidebar { transform: translateX(0); }
            .admin-sidebar.sidebar-collapsed { transform: translateX(-100%); }
            .admin-main.sidebar-collapsed-main {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    `;
    document.head.appendChild(style);
}

// ----------------------------------------------------------------
// AUTO-SLIDE HELPER — Smoothly scrolls viewport to data section
// ----------------------------------------------------------------
function slideIntoData(dataContainer, form) {
    const target = dataContainer || (form ? form.closest(".filter-card") : null);
    if (!target) return;

    const rect = target.getBoundingClientRect();
    // Scroll if element is above top or too far down
    if (rect.top < 70 || rect.top > window.innerHeight * 0.42) {
        const navbarHeight = 80;
        const targetTop = rect.top + window.pageYOffset - navbarHeight;
        window.scrollTo({
            top: Math.max(0, targetTop),
            behavior: "smooth"
        });
    }
}

// ----------------------------------------------------------------
// FILTER SYSTEM — Live Search, Dropdown & Date Picker via AJAX
// ----------------------------------------------------------------
function initAdminFilterSystem() {
    const forms = document.querySelectorAll(".admin-filter-form");
    if (!forms.length) return;

    forms.forEach(form => {
        // 1. Prevent default full-page reload on submit
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            performAdminFilter(form, { scroll: true, preserveFocus: false });
        });

        // 2. Real-time Live Search with 280ms debounce
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener("input", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performAdminFilter(form, {
                        scroll: true,
                        preserveFocus: true,
                        focusTarget: searchInput
                    });
                }, 280);
            });
        }

        // 3. Auto-filter when selecting any dropdown
        const selects = form.querySelectorAll("select");
        selects.forEach(select => {
            select.addEventListener("change", () => {
                performAdminFilter(form, { scroll: true, preserveFocus: false });
            });
        });

        // 4. Auto-filter on Date Inputs
        const dateInputs = form.querySelectorAll('input[type="date"]');
        dateInputs.forEach(dateInput => {
            dateInput.addEventListener("change", () => {
                performAdminFilter(form, { scroll: true, preserveFocus: false });
            });
        });

        // 5. Reset button handler
        const resetBtn = form.querySelector(".btn-filter-reset, a[href*='index']");
        if (resetBtn && resetBtn.classList.contains("btn-filter-reset")) {
            resetBtn.addEventListener("click", (e) => {
                e.preventDefault();
                // Clear all form inputs
                if (searchInput) searchInput.value = "";
                selects.forEach(sel => { sel.selectedIndex = 0; });
                dateInputs.forEach(d => { d.value = ""; });

                performAdminFilter(form, { scroll: true, preserveFocus: false, clearParams: true });
            });
        }
    });

    // Handle preset buttons in Laporan if present
    document.querySelectorAll(".btn-preset-filter").forEach(presetBtn => {
        presetBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const url = presetBtn.getAttribute("href");
            if (url) {
                fetchAndUpdateData(url, { scroll: true });
            }
        });
    });

    // Bind initial pagination links
    const currentTable = document.querySelector(".table-card, .table-container");
    if (currentTable) {
        bindPaginationLinks(currentTable);
    }
}

// ----------------------------------------------------------------
// PERFORM FILTER — AJAX Fetch, DOM Replace & Slide Animation
// ----------------------------------------------------------------
async function performAdminFilter(form, options = { scroll: true, preserveFocus: false, focusTarget: null, clearParams: false }) {
    if (!form) return;

    const dataContainer = document.querySelector(".table-card, .table-container");
    const actionUrl = form.getAttribute("action") || window.location.pathname;

    // Build URL parameters
    const searchParams = new URLSearchParams();
    if (!options.clearParams) {
        const formData = new FormData(form);
        for (const [key, value] of formData.entries()) {
            if (value && String(value).trim() !== "") {
                searchParams.append(key, String(value).trim());
            }
        }
    }

    const queryString = searchParams.toString();
    const finalUrl = `${actionUrl.split("?")[0]}${queryString ? "?" + queryString : ""}`;

    // Update browser URL without reload
    window.history.replaceState(null, "", finalUrl);

    if (!dataContainer) {
        form.submit();
        return;
    }

    // Preserve cursor position if typing in search input
    let selectionStart = null;
    let selectionEnd = null;
    if (options.preserveFocus && options.focusTarget) {
        selectionStart = options.focusTarget.selectionStart;
        selectionEnd = options.focusTarget.selectionEnd;
    }

    // Slide viewport smoothly to show data
    if (options.scroll) {
        slideIntoData(dataContainer, form);
    }

    await fetchAndUpdateData(finalUrl, options, {
        selectionStart,
        selectionEnd,
        focusTarget: options.focusTarget
    });
}

// ----------------------------------------------------------------
// FETCH & UPDATE DATA — Parses returned HTML, replaces DOM
// ----------------------------------------------------------------
async function fetchAndUpdateData(url, options = {}, focusInfo = null) {
    const dataContainer = document.querySelector(".table-card, .table-container");
    if (!dataContainer) {
        window.location.href = url;
        return;
    }

    if (filterAbortController) {
        filterAbortController.abort();
    }
    filterAbortController = new AbortController();

    dataContainer.classList.add("table-filtering");

    try {
        const response = await fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
            signal: filterAbortController.signal
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");

        // 1. Replace Table Content
        const newTable = doc.querySelector(".table-card, .table-container");
        if (newTable) {
            dataContainer.innerHTML = newTable.innerHTML;
            dataContainer.classList.remove("table-filtering");
            dataContainer.classList.remove("table-slide-in");

            // Trigger slide animation on new content
            void dataContainer.offsetWidth;
            dataContainer.classList.add("table-slide-in");

            // Re-bind pagination inside new table
            bindPaginationLinks(dataContainer);
        }

        // 2. Update Stats / Counters if available
        const currentStats = document.querySelector(".stats-row, .metrics-grid, .breakdown-row");
        const newStats = doc.querySelector(".stats-row, .metrics-grid, .breakdown-row");
        if (currentStats && newStats) {
            currentStats.innerHTML = newStats.innerHTML;
        }

        // 3. Update Table Card Count badge if outside table
        const currentCount = document.querySelector(".table-card-count");
        const newCount = doc.querySelector(".table-card-count");
        if (currentCount && newCount) {
            currentCount.innerHTML = newCount.innerHTML;
        }

        // 4. Restore focus and cursor position in search box
        if (focusInfo && focusInfo.focusTarget) {
            focusInfo.focusTarget.focus();
            if (focusInfo.selectionStart !== null && focusInfo.selectionEnd !== null) {
                focusInfo.focusTarget.setSelectionRange(focusInfo.selectionStart, focusInfo.selectionEnd);
            }
        }

        // Slide viewport to data if requested
        if (options.scroll) {
            slideIntoData(dataContainer);
        }

    } catch (err) {
        if (err.name !== "AbortError") {
            console.error("Filter request error:", err);
            dataContainer.classList.remove("table-filtering");
        }
    }
}

// ----------------------------------------------------------------
// PAGINATION HELPER — Intercept pagination links for smooth AJAX
// ----------------------------------------------------------------
function bindPaginationLinks(container) {
    if (!container) return;
    const paginationLinks = container.querySelectorAll(".pagination a, .pagination-wrapper a, a.page-link");
    paginationLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href");
            if (href && !href.startsWith("#") && !href.startsWith("javascript:")) {
                e.preventDefault();
                window.history.pushState(null, "", href);
                fetchAndUpdateData(href, { scroll: true });
            }
        });
    });
}

// ----------------------------------------------------------------
// INITIAL PAGE LOAD AUTO-SLIDE
// ----------------------------------------------------------------
function initInitialPageScroll() {
    const urlParams = new URLSearchParams(window.location.search);
    const filterKeys = ["search", "status", "branch", "verification", "license_status", "date_from", "date_to", "route_id", "tab", "page"];
    const hasFilterParams = filterKeys.some(key => urlParams.has(key) && urlParams.get(key) !== "");

    if (hasFilterParams) {
        setTimeout(() => {
            const dataContainer = document.querySelector(".table-card, .table-container");
            if (dataContainer) {
                slideIntoData(dataContainer);
                dataContainer.classList.add("table-slide-in");
            }
        }, 220);
    }
}

// ----------------------------------------------------------------
// SIDEBAR — Smooth slide transition (Desktop & Mobile)
// ----------------------------------------------------------------
function initSidebar() {
    const sidebar = document.querySelector(".admin-sidebar");
    const main    = document.querySelector(".admin-main");
    const backdrop = document.getElementById("sidebar-backdrop");
    const toggleBtn = document.getElementById("sidebar-toggle");
    if (!sidebar) return;

    // Restore desktop preference
    const isDesktop = window.innerWidth > 900;
    if (isDesktop && localStorage.getItem("admin_sidebar_collapsed") === "true") {
        sidebar.classList.add("sidebar-collapsed");
        if (main) main.classList.add("sidebar-collapsed-main");
    }

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            if (window.innerWidth <= 900) {
                // Mobile slide drawer
                const isOpen = sidebar.classList.toggle("sidebar-mobile-open");
                if (backdrop) backdrop.classList.toggle("active", isOpen);
                document.body.style.overflow = isOpen ? "hidden" : "";
            } else {
                // Desktop collapse slide
                const isCollapsed = sidebar.classList.toggle("sidebar-collapsed");
                if (main) main.classList.toggle("sidebar-collapsed-main", isCollapsed);
                localStorage.setItem("admin_sidebar_collapsed", isCollapsed ? "true" : "false");
            }
        });
    }

    // Close mobile sidebar on backdrop click
    if (backdrop) {
        backdrop.addEventListener("click", () => {
            sidebar.classList.remove("sidebar-mobile-open");
            backdrop.classList.remove("active");
            document.body.style.overflow = "";
        });
    }

    // Auto-close mobile sidebar when clicking menu link
    sidebar.querySelectorAll(".admin-menu-link").forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 900) {
                sidebar.classList.remove("sidebar-mobile-open");
                if (backdrop) backdrop.classList.remove("active");
                document.body.style.overflow = "";
            }
        });
    });

    // Persistensi posisi scroll sidebar agar tidak melompat ke atas saat tombol bawah diklik
    initSidebarScrollPersistence();
}

function initSidebarScrollPersistence() {
    const menu = document.getElementById("admin-menu") || document.querySelector(".admin-menu");
    if (!menu) return;

    // 1. Pulihkan posisi scroll yang tersimpan
    const savedScroll = sessionStorage.getItem("admin_sidebar_scroll");
    if (savedScroll !== null) {
        menu.scrollTop = parseInt(savedScroll, 10);
    }

    // 2. Pastikan tombol/menu aktif yang sedang dibuka selalu terlihat di layar sidebar
    const activeLink = menu.querySelector(".admin-menu-link.active");
    if (activeLink) {
        const menuRect = menu.getBoundingClientRect();
        const linkRect = activeLink.getBoundingClientRect();
        if (linkRect.bottom > menuRect.bottom || linkRect.top < menuRect.top) {
            activeLink.scrollIntoView({ block: "nearest", behavior: "auto" });
        }
        sessionStorage.setItem("admin_sidebar_scroll", menu.scrollTop);
    }

    // 3. Simpan posisi scroll sidebar saat digulir
    menu.addEventListener("scroll", () => {
        sessionStorage.setItem("admin_sidebar_scroll", menu.scrollTop);
    }, { passive: true });

    // 4. Simpan posisi scroll sebelum halaman berpindah
    menu.querySelectorAll(".admin-menu-link").forEach(link => {
        link.addEventListener("click", () => {
            sessionStorage.setItem("admin_sidebar_scroll", menu.scrollTop);
        });
    });
}

// ----------------------------------------------------------------
// ACTION CONFIRMATIONS — data-confirm="Pesan?"
// ----------------------------------------------------------------
function initActionConfirmations() {
    document.addEventListener("click", e => {
        const el = e.target.closest("[data-confirm]");
        if (!el) return;
        if (!window.confirm(el.dataset.confirm || "Apakah Anda yakin?")) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

// ----------------------------------------------------------------
// FLASH MESSAGES — Auto-hide setelah 5 detik
// ----------------------------------------------------------------
function initFlashMessages() {
    document.querySelectorAll(".alert-banner").forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-8px)";
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

// ----------------------------------------------------------------
// MODAL HELPERS (Global Support for .custom-modal & .modal-backdrop)
// ----------------------------------------------------------------
window.AdminModal = {
    open(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add("show");
            el.classList.add("modal-visible");
            document.body.style.overflow = "hidden";
        }
    },
    close(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove("show");
            el.classList.remove("modal-visible");
            document.body.style.overflow = "";
        }
    }
};

// Global click listener to close modals when backdrop clicked
document.addEventListener("click", e => {
    if (e.target.classList.contains("modal-backdrop") || e.target.classList.contains("custom-modal")) {
        e.target.classList.remove("show");
        e.target.classList.remove("modal-visible");
        document.body.style.overflow = "";
    }
});

// Global Escape key listener to close active modals
document.addEventListener("keydown", e => {
    if (e.key === "Escape") {
        document.querySelectorAll(".modal-backdrop.show, .modal-backdrop.modal-visible, .custom-modal.show, .custom-modal.modal-visible").forEach(m => {
            m.classList.remove("show");
            m.classList.remove("modal-visible");
        });
        document.body.style.overflow = "";
    }
});

