{{--
    Draws a real, scannable QR into every [data-qr] element from its data-qr value.
    Staff scan these with the ticket validator, so they must encode the actual code.
    If the library cannot load, the code printed under each QR still works.
--}}
@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
        <script>
            window.drawTicketQrCodes = function (root = document) {
                if (typeof window.qrcode !== 'function') {
                    return;
                }

                root.querySelectorAll('[data-qr]').forEach((element) => {
                    const value = element.dataset.qr;

                    if (!value || element.dataset.qrDrawn === value) {
                        return;
                    }

                    const code = window.qrcode(0, 'M');
                    code.addData(value);
                    code.make();
                    element.innerHTML = code.createSvgTag({ cellSize: 4, margin: 0, scalable: true });
                    element.querySelector('svg')?.setAttribute('aria-hidden', 'true');
                    element.dataset.qrDrawn = value;
                });
            };

            window.drawTicketQrCodes();
        </script>
    @endpush
@endonce
