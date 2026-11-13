<script>
/**
 * Global Numeric Input Enforcer (TravelKu)
 * Memastikan semua kolom nomor handphone, nomor rekening, kontak darurat, dsb
 * hanya bisa menerima input angka (0-9) tanpa huruf maupun simbol di semua role.
 */
(function () {
    function isNumericTarget(el) {
        if (!el || el.tagName !== 'INPUT') return false;
        var type = (el.type || 'text').toLowerCase();
        if (type === 'hidden' || type === 'password' || type === 'radio' || type === 'checkbox' || type === 'file' || type === 'submit') {
            return false;
        }

        if (type === 'tel' || el.getAttribute('inputmode') === 'numeric') {
            return true;
        }

        var pattern = el.getAttribute('pattern') || '';
        if (pattern.includes('[0-9]')) {
            return true;
        }

        var nameOrId = ((el.name || '') + ' ' + (el.id || '') + ' ' + (el.className || '')).toLowerCase();
        return /(phone|rekening|account_number|telepon|wa_number|emergency_contact|no_rek|nik)/i.test(nameOrId);
    }

    // Blokir pengetikan huruf & simbol langsung di keyboard
    document.addEventListener('keydown', function (e) {
        var el = e.target;
        if (!isNumericTarget(el)) return;

        // Izinkan tombol kontrol, navigasi, dan kombinasi shortcut (Ctrl+C, Ctrl+V, dsb)
        if (e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' || e.key === 'Enter' || e.key === 'Escape' ||
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
            e.key === 'Home' || e.key === 'End' || e.ctrlKey || e.metaKey || e.altKey) {
            return;
        }

        // Blokir jika karakter bukan angka 0-9
        if (!/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }
    }, true);

    // Sanitasi instan saat ada input/perubahan data
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!isNumericTarget(el)) return;

        var clean = el.value.replace(/[^0-9]/g, '');
        if (el.value !== clean) {
            el.value = clean;
        }
    }, true);

    // Sanitasi saat data di-paste dari clipboard
    document.addEventListener('paste', function (e) {
        var el = e.target;
        if (!isNumericTarget(el)) return;

        var clipboardData = e.clipboardData || window.clipboardData;
        if (!clipboardData) return;

        var pastedData = clipboardData.getData('text');
        if (!pastedData) return;

        var cleanData = pastedData.replace(/[^0-9]/g, '');
        if (pastedData !== cleanData) {
            e.preventDefault();
            var start = el.selectionStart || 0;
            var end = el.selectionEnd || 0;
            var val = el.value || '';
            el.value = val.slice(0, start) + cleanData + val.slice(end);
            el.selectionStart = el.selectionEnd = start + cleanData.length;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }, true);
})();
</script>

