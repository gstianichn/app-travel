// import './bootstrap';

const navbarToggle = document.querySelector('[data-navbar-toggle]');
const navbarPanel = document.getElementById('navbar-panel');

if (navbarToggle && navbarPanel) {
    const setNavbarOpen = (isOpen) => {
        navbarPanel.classList.toggle('is-open', isOpen);
        navbarToggle.setAttribute('aria-expanded', String(isOpen));
    };

    navbarToggle.addEventListener('click', () => {
        setNavbarOpen(!navbarPanel.classList.contains('is-open'));
    });

    navbarPanel.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setNavbarOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navbarPanel.classList.contains('is-open')) {
            setNavbarOpen(false);
            navbarToggle.focus();
        }
    });
}

/* ---------- Navbar Section Active State (ScrollSpy & Hash) ---------- */
const navbarMenu = document.querySelector('.navbar-menu');

if (navbarMenu) {
    const homeLink = navbarMenu.querySelector('a[data-nav-section="home"]');
    const sectionLinks = Array.from(navbarMenu.querySelectorAll('a[data-nav-section]:not([data-nav-section="home"])'));

    const sections = sectionLinks
        .map((link) => {
            const sectionId = link.getAttribute('data-nav-section');
            const element = document.getElementById(sectionId);
            return element ? { id: sectionId, link, element } : null;
        })
        .filter(Boolean);

    // Only apply section scrollspy if section elements exist on this page
    if (sections.length > 0 && homeLink) {
        const updateActiveNavSection = () => {
            const scrollY = window.scrollY;
            const viewportHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const isAtBottom = scrollY + viewportHeight >= documentHeight - 60;

            let activeSection = null;

            if (isAtBottom) {
                activeSection = sections[sections.length - 1];
            } else {
                const headerOffset = 120;
                for (let i = sections.length - 1; i >= 0; i--) {
                    const sectionTop = sections[i].element.getBoundingClientRect().top + scrollY;
                    if (scrollY + headerOffset >= sectionTop) {
                        activeSection = sections[i];
                        break;
                    }
                }
            }

            homeLink.classList.remove('navbar-link-active');
            for (const { link } of sections) {
                link.classList.remove('navbar-link-active');
            }

            if (activeSection) {
                activeSection.link.classList.add('navbar-link-active');
            } else {
                homeLink.classList.add('navbar-link-active');
            }
        };

        let rafId = null;
        window.addEventListener('scroll', () => {
            if (!rafId) {
                rafId = window.requestAnimationFrame(() => {
                    updateActiveNavSection();
                    rafId = null;
                });
            }
        }, { passive: true });

        window.addEventListener('hashchange', updateActiveNavSection);

        // Run on initial load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateActiveNavSection);
        } else {
            updateActiveNavSection();
        }
    }
}
