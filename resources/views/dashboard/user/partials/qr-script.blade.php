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
                root.querySelectorAll('[data-qr]').forEach((element) => {
                    const value = element.dataset.qr;

                    if (!value || element.dataset.qrDrawn === value) {
                        return;
                    }

                    if (typeof window.qrcode === 'function') {
                        try {
                            const code = window.qrcode(0, 'M');
                            code.addData(value);
                            code.make();
                            element.innerHTML = code.createSvgTag({ cellSize: 4, margin: 0, scalable: true });
                            const svg = element.querySelector('svg');
                            if (svg) {
                                svg.setAttribute('aria-hidden', 'true');
                                svg.style.width = '100%';
                                svg.style.height = '100%';
                                svg.style.display = 'block';
                            }
                            element.dataset.qrDrawn = value;
                            return;
                        } catch (e) {
                            console.warn('QR generation error, using fallback:', e);
                        }
                    }

                    // Fallback to high-contrast scannable QR display
                    const encodedVal = encodeURIComponent(value);
                    element.innerHTML = `
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodedVal}"
                             alt="Kode QR ${value}"
                             style="width: 100%; height: 100%; object-fit: contain; display: block;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\'font-family:monospace; font-size:11px; font-weight:bold; color:#1e293b; text-align:center; padding:12px; background:#f8fafc; border:2px dashed #94a3b8; border-radius:6px;\'>${value}</div>';"
                        />
                    `;
                    element.dataset.qrDrawn = value;
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.drawTicketQrCodes());
            } else {
                window.drawTicketQrCodes();
            }
            setTimeout(() => window.drawTicketQrCodes(), 400);
        </script>
    @endpush
@endonce
