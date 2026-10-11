import './ticket.js';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const wideViewport = window.matchMedia('(min-width: 900px)');

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

/* ---------- Navbar: solid background once the page leaves the sky ---------- */
const navbar = document.querySelector('.navbar');

/* ---------- Parallax scenes ----------
 * "exit" scenes (hero) drift as they scroll away; "enter" scenes (dusk) settle into place as they arrive.
 * Each [data-depth] layer moves by depth * half the scene height, so far layers lag behind near ones.
 */
const scenes = [...document.querySelectorAll('[data-scene]')].map((scene) => ({
    scene,
    mode: scene.dataset.scene,
    layers: [...scene.querySelectorAll('[data-depth]')].map((element) => ({
        element,
        depth: Number.parseFloat(element.dataset.depth) || 0,
        fades: element.hasAttribute('data-fade'),
    })),
    van: scene.querySelector('[data-drive]'),
}));

function updateScenes(viewportHeight) {
    for (const { scene, mode, layers, van } of scenes) {
        const rect = scene.getBoundingClientRect();

        if (rect.bottom < 0 || rect.top > viewportHeight) {
            continue;
        }

        const travel = mode === 'exit'
            ? clamp(-rect.top / rect.height, 0, 1)
            : clamp((rect.bottom - viewportHeight) / rect.height, 0, 1);

        for (const { element, depth, fades } of layers) {
            element.style.transform = `translate3d(0, ${(travel * depth * rect.height * 0.5).toFixed(1)}px, 0)`;

            if (fades) {
                element.style.opacity = String(clamp(1 - travel * 2.2, 0, 1));
            }
        }

        if (van) {
            van.style.transform = `translate3d(${(travel * rect.width * 0.55).toFixed(1)}px, 0, 0)`;
        }
    }
}

/* ---------- Routes: pin the section and slide the signs sideways ---------- */
const routes = document.querySelector('[data-routes]');
const routesViewport = routes?.querySelector('[data-routes-viewport]');
const routesTrack = routes?.querySelector('[data-routes-track]');
let routesOverflow = 0;

function canPinRoutes() {
    return Boolean(routesTrack) && wideViewport.matches && !prefersReducedMotion.matches;
}

function layoutRoutes() {
    if (!routes || !routesTrack) {
        return;
    }

    routes.classList.remove('is-pinned');
    routes.style.height = '';
    routesTrack.style.transform = '';
    routesViewport.style.removeProperty('--road-x');
    routesViewport.style.removeProperty('--hills-x');

    if (!canPinRoutes()) {
        return;
    }

    routesOverflow = Math.max(0, routesTrack.scrollWidth - routesViewport.clientWidth);

    if (routesOverflow < 40) {
        return;
    }

    routes.classList.add('is-pinned');
    routes.style.height = `${routes.querySelector('.lp-routes-sticky').offsetHeight + routesOverflow}px`;
}

function routesProgress() {
    const rect = routes.getBoundingClientRect();
    const scrollable = rect.height - window.innerHeight;

    return scrollable > 0 ? clamp(-rect.top / scrollable, 0, 1) : 0;
}

function updateRoutes() {
    if (!routes?.classList.contains('is-pinned')) {
        return;
    }

    const shift = routesProgress() * routesOverflow;

    routesTrack.style.transform = `translate3d(${-shift.toFixed(1)}px, 0, 0)`;
    // The road is closer than the signs and the hills are further away, so they move faster and slower.
    routesViewport.style.setProperty('--road-x', `${(-shift * 1.5).toFixed(1)}px`);
    routesViewport.style.setProperty('--hills-x', `${(-shift * 0.4).toFixed(1)}px`);
}

// Keyboard users tabbing through signs in pinned mode: scroll the page so the focused sign is on screen.
routesTrack?.addEventListener('focusin', (event) => {
    if (!routes.classList.contains('is-pinned')) {
        return;
    }

    const post = event.target.closest('.lp-sign-post');

    if (!post) {
        return;
    }

    const desiredShift = clamp(post.offsetLeft - routesViewport.clientWidth / 2 + post.offsetWidth / 2, 0, routesOverflow);
    const routesTop = routes.getBoundingClientRect().top + window.scrollY;
    const scrollable = routes.offsetHeight - window.innerHeight;

    window.scrollTo({ top: routesTop + (desiredShift / routesOverflow) * scrollable, behavior: 'instant' });
});

/* ---------- How-to-book road: van follows reading progress ---------- */
const steps = document.querySelector('[data-steps]');
const stepsVan = steps?.querySelector('[data-steps-van]');
const stepItems = steps ? [...steps.querySelectorAll('[data-step]')] : [];

function updateSteps(viewportHeight) {
    if (!steps) {
        return;
    }

    const rect = steps.getBoundingClientRect();
    const readingLine = viewportHeight * 0.6;
    const progress = prefersReducedMotion.matches ? 1 : clamp((readingLine - rect.top) / rect.height, 0, 1);
    const vanTravel = Math.max(0, rect.height - stepsVan.getBoundingClientRect().height - 16);

    steps.style.setProperty('--steps-progress', progress.toFixed(3));
    steps.style.setProperty('--van-y', `${(8 + progress * vanTravel).toFixed(1)}px`);

    for (const item of stepItems) {
        item.classList.toggle('is-reached', prefersReducedMotion.matches || item.getBoundingClientRect().top < readingLine);
    }
}

/* ---------- Frame loop ---------- */
let frameRequested = false;

function render() {
    frameRequested = false;

    const viewportHeight = window.innerHeight;

    navbar?.classList.toggle('is-scrolled', window.scrollY > 24);

    if (!prefersReducedMotion.matches) {
        updateScenes(viewportHeight);
        updateRoutes();
    }

    updateSteps(viewportHeight);
}

function requestRender() {
    if (!frameRequested) {
        frameRequested = true;
        window.requestAnimationFrame(render);
    }
}

function resetMotion() {
    for (const { layers, van } of scenes) {
        layers.forEach(({ element }) => {
            element.style.transform = '';
            element.style.opacity = '';
        });

        if (van) {
            van.style.transform = '';
        }
    }

    layoutRoutes();
    requestRender();
}

window.addEventListener('scroll', requestRender, { passive: true });
window.addEventListener('resize', () => {
    layoutRoutes();
    requestRender();
});
prefersReducedMotion.addEventListener('change', resetMotion);
wideViewport.addEventListener('change', resetMotion);
document.fonts?.ready.then(() => {
    layoutRoutes();
    requestRender();
});

layoutRoutes();
render();
