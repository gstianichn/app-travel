const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

/* ---------- Show / hide password ---------- */
document.querySelectorAll('[data-reveal]').forEach((button) => {
    const input = document.getElementById(button.getAttribute('aria-controls'));

    button.addEventListener('click', () => {
        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(willShow));
        button.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
        button.querySelector('[data-eye-open]').toggleAttribute('hidden', willShow);
        button.querySelector('[data-eye-closed]').toggleAttribute('hidden', !willShow);
    });
});

/* ---------- Submit: loading state, and the van leaves the scene ---------- */
document.querySelectorAll('[data-auth-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const confirmation = form.querySelector('[data-password-confirm]');
        const password = form.querySelector('[data-password-strength]');

        if (confirmation && password && confirmation.value !== password.value) {
            event.preventDefault();
            confirmation.setAttribute('aria-invalid', 'true');
            confirmation.focus();

            return;
        }

        const button = form.querySelector('[type="submit"]');
        button.setAttribute('aria-busy', 'true');
        button.querySelector('[data-label]').textContent = button.dataset.busyLabel || 'Memproses...';
        document.body.classList.add('is-departing');
    });
});

window.addEventListener('pageshow', () => {
    document.body.classList.remove('is-departing');
    document.querySelectorAll('[data-auth-form] [type="submit"]').forEach((button) => {
        button.removeAttribute('aria-busy');
        button.querySelector('[data-label]').textContent = button.dataset.label;
    });
});

/* ---------- Register: password strength and confirmation match ---------- */
const strengthInput = document.querySelector('[data-password-strength]');

if (strengthInput) {
    const meter = document.querySelector('[data-strength-meter]');
    const strengthText = document.querySelector('[data-strength-text]');
    const confirmInput = document.querySelector('[data-password-confirm]');
    const matchText = document.querySelector('[data-match-text]');
    const labels = ['', 'Terlalu pendek, minimal 8 karakter', 'Cukup', 'Kuat', 'Sangat kuat'];

    const strengthOf = (value) => {
        if (!value) {
            return 0;
        }

        if (value.length < 8) {
            return 1;
        }

        let score = 2;
        const varieties = [/[a-z]/, /[A-Z]/, /\d/, /[^A-Za-z0-9]/].filter((pattern) => pattern.test(value)).length;

        if (varieties >= 3) {
            score += 1;
        }

        if (value.length >= 12 && varieties >= 3) {
            score += 1;
        }

        return score;
    };

    const updateMatch = () => {
        if (!confirmInput.value) {
            matchText.textContent = '';
            matchText.className = 'au-live';

            return;
        }

        const matches = confirmInput.value === strengthInput.value;
        matchText.textContent = matches ? 'Password sudah sama.' : 'Belum sama dengan password di atas.';
        matchText.className = `au-live ${matches ? 'is-ok' : 'is-bad'}`;
        if (matches) {
            confirmInput.removeAttribute('aria-invalid');
        } else {
            confirmInput.setAttribute('aria-invalid', 'true');
        }
    };

    strengthInput.addEventListener('input', () => {
        const level = strengthOf(strengthInput.value);
        meter.dataset.level = String(level);
        strengthText.textContent = labels[level];
        strengthText.className = `au-live ${level === 1 ? 'is-bad' : level >= 3 ? 'is-ok' : ''}`;
        updateMatch();
    });

    confirmInput.addEventListener('input', updateMatch);
}
