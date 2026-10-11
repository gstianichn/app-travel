/*
 * Ticket-shaped search form: swap cities, quick dates, a live summary on the stub,
 * route shortcuts ([data-route-fill]) and a loading state on submit.
 */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

const ticket = document.querySelector('[data-ticket]');

if (ticket) {
    const origin = ticket.querySelector('#origin');
    const destination = ticket.querySelector('#destination');
    const departureDate = ticket.querySelector('#departure_date');
    const status = ticket.querySelector('[data-ticket-status]');
    const scrollBehavior = () => (prefersReducedMotion.matches ? 'auto' : 'smooth');

    const announce = (message, isError = false) => {
        status.textContent = message;
        status.classList.toggle('is-error', isError);
    };

    const localDate = (offsetDays) => {
        const date = new Date();
        date.setDate(date.getDate() + offsetDays);

        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${date.getFullYear()}-${month}-${day}`;
    };

    const passengers = ticket.querySelector('#passengers');
    const summary = (key) => ticket.querySelector(`[data-summary="${key}"]`);

    const setSummary = (key, value) => {
        const target = summary(key);
        target.textContent = value || 'Belum dipilih';
        target.classList.toggle('is-empty', !value);
    };

    // The stub mirrors the choices like a printed ticket, so the reader sees what they are about to search.
    const updateSummary = () => {
        const placeName = (select) => (select.value ? select.selectedOptions[0].textContent.trim() : '');
        const from = placeName(origin);
        const to = placeName(destination);

        setSummary('route', from && to ? `${from} ke ${to}` : from || to ? `${from || '?'} ke ${to || '?'}` : '');

        const [year, month, day] = departureDate.value.split('-').map(Number);
        setSummary('date', departureDate.value
            ? new Date(year, month - 1, day).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' })
            : '');

        setSummary('passengers', `${passengers.value} orang`);
    };

    ticket.addEventListener('change', updateSummary);
    ticket.addEventListener('input', updateSummary);
    updateSummary();

    const bringTicketIntoView = (fieldToFocus) => {
        document.getElementById('cari').scrollIntoView({ behavior: scrollBehavior(), block: 'start' });
        fieldToFocus.focus({ preventScroll: true });
    };

    ticket.querySelector('[data-swap]').addEventListener('click', () => {
        [origin.value, destination.value] = [destination.value, origin.value];
        updateSummary();
        announce(origin.value && destination.value ? 'Kota asal dan tujuan sudah ditukar.' : '');
    });

    ticket.querySelectorAll('[data-date-offset]').forEach((button) => {
        button.addEventListener('click', () => {
            departureDate.value = localDate(Number(button.dataset.dateOffset));
            departureDate.removeAttribute('aria-invalid');
            updateSummary();
            announce(`Tanggal berangkat diisi ${button.textContent.trim().toLowerCase()}.`);
        });
    });

    [origin, destination, departureDate].forEach((field) => {
        field.addEventListener('change', () => field.removeAttribute('aria-invalid'));
    });

    ticket.addEventListener('submit', (event) => {
        if (origin.value && origin.value === destination.value) {
            event.preventDefault();
            destination.setAttribute('aria-invalid', 'true');
            announce('Kota asal dan tujuan tidak boleh sama. Pilih kota tujuan lain.', true);
            destination.focus();

            return;
        }

        // Loading state: the results page can take a moment while schedules are prepared.
        const submitButton = ticket.querySelector('[data-ticket-submit]');
        submitButton.textContent = 'Mencari jadwal...';
        submitButton.setAttribute('aria-busy', 'true');
        ticket.setAttribute('aria-busy', 'true');
    });

    // Coming back with the browser's back button restores the page from cache; reset the button.
    window.addEventListener('pageshow', () => {
        const submitButton = ticket.querySelector('[data-ticket-submit]');
        submitButton.textContent = 'Lihat jadwal';
        submitButton.removeAttribute('aria-busy');
        ticket.removeAttribute('aria-busy');
    });

    ticket.addEventListener('invalid', (event) => {
        event.target.setAttribute('aria-invalid', 'true');
    }, true);

    document.querySelectorAll('[data-route-fill]').forEach((sign) => {
        sign.addEventListener('click', (event) => {
            const hasOrigin = origin.querySelector(`option[value="${sign.dataset.origin}"]`);
            const hasDestination = destination.querySelector(`option[value="${sign.dataset.destination}"]`);

            if (!hasOrigin || !hasDestination) {
                return;
            }

            event.preventDefault();
            origin.value = sign.dataset.origin;
            destination.value = sign.dataset.destination;
            [origin, destination].forEach((field) => field.removeAttribute('aria-invalid'));
            updateSummary();
            announce(`Rute ${sign.dataset.label} sudah diisi. Tinggal pilih tanggal berangkat.`);
            bringTicketIntoView(departureDate);
        });
    });

    document.querySelectorAll('[data-focus-search]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            bringTicketIntoView(origin);
        });
    });
}
