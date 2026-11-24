@once
    @push('scripts')
        <script>
            window.downloadTicketPng = function (ticketData) {
                const width = 1400;
                const height = 620;
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');

                const roundRect = (x, y, w, h, r) => {
                    ctx.beginPath();
                    ctx.moveTo(x + r, y);
                    ctx.arcTo(x + w, y, x + w, y + h, r);
                    ctx.arcTo(x + w, y + h, x, y + h, r);
                    ctx.arcTo(x, y + h, x, y, r);
                    ctx.arcTo(x, y, x + w, y, r);
                    ctx.closePath();
                };

                const wrapText = (text, x, y, maxWidth, lineHeight, maxLines = 2) => {
                    const words = String(text || '-').split(/\s+/);
                    let line = '';
                    let currentY = y;
                    let linesDrawn = 0;

                    for (let i = 0; i < words.length; i++) {
                        const testLine = line ? line + ' ' + words[i] : words[i];
                        if (ctx.measureText(testLine).width > maxWidth && line) {
                            linesDrawn++;
                            if (linesDrawn >= maxLines) {
                                ctx.fillText(line + '...', x, currentY);
                                return currentY + lineHeight;
                            }
                            ctx.fillText(line, x, currentY);
                            line = words[i];
                            currentY += lineHeight;
                        } else {
                            line = testLine;
                        }
                    }
                    if (line) {
                        ctx.fillText(line, x, currentY);
                        currentY += lineHeight;
                    }
                    return currentY;
                };

                // Outer background
                ctx.fillStyle = '#F6F4EC';
                ctx.fillRect(0, 0, width, height);

                // Card container
                const cardX = 28;
                const cardY = 28;
                const cardW = width - 56;
                const cardH = height - 56;
                const stubW = 390;
                const mainW = cardW - stubW;

                ctx.save();
                roundRect(cardX, cardY, cardW, cardH, 24);
                ctx.fillStyle = '#FFFFFF';
                ctx.fill();
                ctx.clip();

                // Right dark green QR stub
                ctx.fillStyle = '#0C4A36';
                ctx.fillRect(cardX + mainW, cardY, stubW, cardH);

                // Dashed tear line
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.55)';
                ctx.lineWidth = 3;
                ctx.setLineDash([10, 10]);
                ctx.beginPath();
                ctx.moveTo(cardX + mainW, cardY + 12);
                ctx.lineTo(cardX + mainW, cardY + cardH - 12);
                ctx.stroke();
                ctx.setLineDash([]);

                // Left side content
                const padX = cardX + 44;
                const contentMaxW = mainW - 88;

                // Top meta: ticket number & ticket index badge
                ctx.fillStyle = '#64748B';
                ctx.font = '600 20px "Segoe UI", Arial, sans-serif';
                ctx.fillText('No. tiket ' + (ticketData.ticketNumber || '-'), padX, cardY + 58);

                if (ticketData.ticketIndexLabel) {
                    const badgeText = ticketData.ticketIndexLabel;
                    ctx.font = '700 18px "Segoe UI", Arial, sans-serif';
                    const badgeW = ctx.measureText(badgeText).width + 28;
                    const badgeX = cardX + mainW - 44 - badgeW;
                    roundRect(badgeX, cardY + 32, badgeW, 34, 17);
                    ctx.fillStyle = '#DFF0E8';
                    ctx.fill();
                    ctx.fillStyle = '#0C5A3E';
                    ctx.fillText(badgeText, badgeX + 14, cardY + 55);
                }

                // Route title
                ctx.fillStyle = '#0C392F';
                ctx.font = '800 34px "Segoe UI", Arial, sans-serif';
                const routeText = `${ticketData.origin || '-'}  ke  ${ticketData.destination || '-'}`;
                const afterRouteY = wrapText(routeText, padX, cardY + 114, contentMaxW, 42, 2);

                // Info grid (Tanggal, Berangkat, Kursi, Kendaraan)
                const gridY = Math.max(afterRouteY + 20, cardY + 188);
                const cols = [
                    { label: 'Tanggal', value: ticketData.date || '-' },
                    { label: 'Berangkat', value: ticketData.time || '-' },
                    { label: 'Kursi', value: ticketData.seatNumber ? 'Kursi ' + ticketData.seatNumber : '-' },
                    { label: 'Kendaraan', value: ticketData.vehiclePlate || 'Diumumkan hari-H' },
                ];
                const colW = contentMaxW / 4;
                cols.forEach((col, i) => {
                    const cx = padX + i * colW;
                    ctx.fillStyle = '#64748B';
                    ctx.font = '600 18px "Segoe UI", Arial, sans-serif';
                    ctx.fillText(col.label, cx, gridY);

                    ctx.fillStyle = '#0F172A';
                    ctx.font = '800 23px "Segoe UI", Arial, sans-serif';
                    wrapText(col.value, cx, gridY + 32, colW - 14, 26, 2);
                });

                // Divider & Passenger row
                const passY = gridY + 86;
                ctx.fillStyle = '#64748B';
                ctx.font = '600 18px "Segoe UI", Arial, sans-serif';
                ctx.fillText('Penumpang', padX, passY);

                ctx.strokeStyle = '#E2E8F0';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(padX, passY + 16);
                ctx.lineTo(cardX + mainW - 44, passY + 16);
                ctx.stroke();

                ctx.fillStyle = '#0F172A';
                ctx.font = '700 26px "Segoe UI", Arial, sans-serif';
                ctx.fillText(ticketData.passengerName || '-', padX, passY + 56);

                // Seat pill on right of passenger row
                const seatPillText = 'Kursi ' + (ticketData.seatNumber || '-');
                ctx.font = '800 20px "Segoe UI", Arial, sans-serif';
                const pillW = ctx.measureText(seatPillText).width + 30;
                const pillX = cardX + mainW - 44 - pillW;
                roundRect(pillX, passY + 28, pillW, 38, 10);
                ctx.fillStyle = '#EEFBF5';
                ctx.fill();
                ctx.strokeStyle = '#8FD3B4';
                ctx.lineWidth = 2;
                ctx.stroke();
                ctx.fillStyle = '#0C5A3E';
                ctx.fillText(seatPillText, pillX + 15, passY + 54);

                ctx.strokeStyle = '#E2E8F0';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(padX, passY + 82);
                ctx.lineTo(cardX + mainW - 44, passY + 82);
                ctx.stroke();

                // Boarding instructions footer
                ctx.fillStyle = '#334155';
                ctx.font = '500 19px "Segoe UI", Arial, sans-serif';
                wrapText(ticketData.boardingNote || '', padX, passY + 122, contentMaxW, 28, 3);

                // Right stub: White QR box
                const qrBoxSize = 236;
                const qrBoxX = cardX + mainW + (stubW - qrBoxSize) / 2;
                const qrBoxY = cardY + 58;
                roundRect(qrBoxX, qrBoxY, qrBoxSize, qrBoxSize, 16);
                ctx.fillStyle = '#FFFFFF';
                ctx.fill();

                // Draw crisp scannable QR code using qrcode-generator
                const qrValue = ticketData.qrValue || ticketData.bookingCode || '';
                if (typeof window.qrcode === 'function' && qrValue) {
                    const qr = window.qrcode(0, 'M');
                    qr.addData(qrValue);
                    qr.make();
                    const moduleCount = qr.getModuleCount();
                    const qrPad = 18;
                    const drawSize = qrBoxSize - qrPad * 2;
                    const cellSize = drawSize / moduleCount;

                    ctx.fillStyle = '#0F172A';
                    for (let r = 0; r < moduleCount; r++) {
                        for (let c = 0; c < moduleCount; c++) {
                            if (qr.isDark(r, c)) {
                                ctx.fillRect(
                                    qrBoxX + qrPad + c * cellSize,
                                    qrBoxY + qrPad + r * cellSize,
                                    Math.ceil(cellSize),
                                    Math.ceil(cellSize)
                                );
                            }
                        }
                    }
                }

                // Booking code under QR
                const stubCenterX = cardX + mainW + stubW / 2;
                ctx.textAlign = 'center';
                ctx.fillStyle = '#CFECE0';
                ctx.font = '600 18px "Segoe UI", Arial, sans-serif';
                ctx.fillText('Kode booking', stubCenterX, qrBoxY + qrBoxSize + 44);

                ctx.fillStyle = '#FFFFFF';
                ctx.font = '800 28px "Segoe UI", Arial, sans-serif';
                ctx.fillText(ticketData.bookingCode || '-', stubCenterX, qrBoxY + qrBoxSize + 84);

                ctx.fillStyle = '#CFECE0';
                ctx.font = '500 16px "Segoe UI", Arial, sans-serif';
                ctx.fillText('Tunjukkan QR ini ke petugas saat', stubCenterX, qrBoxY + qrBoxSize + 124);
                ctx.fillText('boarding di pool keberangkatan.', stubCenterX, qrBoxY + qrBoxSize + 148);
                ctx.textAlign = 'left';

                ctx.restore();

                // Card border
                roundRect(cardX, cardY, cardW, cardH, 24);
                ctx.strokeStyle = '#0C7950';
                ctx.lineWidth = 3;
                ctx.stroke();

                // Trigger file download
                const safeBooking = String(ticketData.bookingCode || 'Tiket').replace(/[^A-Za-z0-9_-]/g, '');
                const safeSeat = String(ticketData.seatNumber || '1').replace(/[^A-Za-z0-9_-]/g, '');
                const link = document.createElement('a');
                link.download = `E-Ticket-${safeBooking}-Kursi-${safeSeat}.png`;
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };

            window.downloadAllTicketsPng = function (ticketsList) {
                if (!Array.isArray(ticketsList) || ticketsList.length === 0) return;
                ticketsList.forEach((ticket, idx) => {
                    setTimeout(() => {
                        window.downloadTicketPng(ticket);
                    }, idx * 280);
                });
            };
        </script>
    @endpush
@endonce
