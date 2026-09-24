<!-- =========================================
     JAVASCRIPT SCANNER LOGIC
========================================== -->

<script>
    function openScannerModal() {
        document.getElementById('modalTitle').innerText = 'Kamera Pemindai QR Code E-Ticket';
        document.getElementById('bookingCodeInput').value = '';
        document.getElementById('scanResultCard').classList.remove('active');
        document.getElementById('scannerModal').classList.add('active');
        setTimeout(() => {
            document.getElementById('bookingCodeInput').focus();
        }, 300);
    }

    function openScannerForTrip(tripId, routeName, plateNumber) {
        openScannerModal();
        document.getElementById('modalTitle').innerText = 'Scan Boarding: ' + plateNumber + ' (' + routeName + ')';
    }

    function closeScannerModal() {
        document.getElementById('scannerModal').classList.remove('active');
    }

    function handleBackdropClick(event) {
        if (event.target === document.getElementById('scannerModal')) {
            closeScannerModal();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeScannerModal();
        }
    });

    function simulateScan(code, name, seat, route, plate) {
        document.getElementById('bookingCodeInput').value = code;
        displayScanResult(code, name, seat, route + ' (' + plate + ')');
    }

    function validateTicketCode() {
        const code = document.getElementById('bookingCodeInput').value.trim();
        if (!code) {
            alert('Silakan ketik atau tempelkan kode booking e-ticket terlebih dahulu.');
            return;
        }

        displayScanResult(code.toUpperCase(), 'Rian Pratama', '04A', 'Bandung (Dipatiukur) → Jakarta (D 7812 AB)');
    }

    function displayScanResult(code, name, seat, route) {
        document.getElementById('resultBookingCode').innerText = code;
        document.getElementById('resultPassengerName').innerText = name;
        document.getElementById('resultSeatNumber').innerText = 'Kursi ' + seat;
        document.getElementById('resultRoute').innerText = route;
        document.getElementById('scanResultCard').classList.add('active');
    }

    function confirmBoarding() {
        const name = document.getElementById('resultPassengerName').innerText;
        const seat = document.getElementById('resultSeatNumber').innerText;
        alert('🎉 Sukses! Penumpang ' + name + ' (' + seat + ') telah berhasil divalidasi dan tercatat Masuk Armada.');
        closeScannerModal();
    }
</script>
