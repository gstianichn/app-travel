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
