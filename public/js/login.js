document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

    if (!button || !password || !icon) {
        return;
    }

    button.addEventListener('click', function () {
        const hidden = password.type === 'password';

        password.type = hidden ? 'text' : 'password';

        button.setAttribute(
            'aria-label',
            hidden
                ? 'Sembunyikan password'
                : 'Tampilkan password'
        );

        if (hidden) {
            icon.innerHTML = `
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.458 12C2.732 16.057 6.523 19 11 19c1.61 0 3.13-.386 4.46-1.07"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6.228 6.228A10.45 10.45 0 0111 5c4.477 0 8.268 2.943 9.542 7a10.47 10.47 0 01-2.328 4.332"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6.228 6.228L3 3m3.228 3.228l3.07 3.07m0 0a3 3 0 104.243 4.243M9.298 9.298l4.243 4.243M21 21l-4.5-4.5"
                />
            `;
        } else {
            icon.innerHTML = `
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
                <circle
                    cx="12"
                    cy="12"
                    r="3"
                />
            `;
        }
    });
});