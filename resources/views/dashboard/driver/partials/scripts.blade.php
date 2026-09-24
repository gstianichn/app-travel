<!-- JAVASCRIPT LOGIC: GPS TRACKING REAL-TIME, MANIFEST FILTER, MODALS -->
<script>
    // 1. INSIALISASI GEOLOCATION API & BACKGROUND GPS SYNC
    let currentLat = null;
    let currentLng = null;
    const scheduleId = {{ $activeSchedule->id ?? 1 }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 0. TAB SWITCHER AGENDA PENUGASAN (HARI INI, BESOK, MENDATANG)
    function switchScheduleTab(tabName) {
        document.querySelectorAll('.agenda-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.agenda-tab-panel').forEach(panel => panel.classList.remove('active'));

        const activeBtn = document.getElementById('tab-btn-' + tabName);
        const activePanel = document.getElementById('tab-panel-' + tabName);
        if (activeBtn) activeBtn.classList.add('active');
        if (activePanel) activePanel.classList.add('active');
    }

    function initGeolocation() {
        if (!navigator.geolocation) {
            updateGpsUI(false, "Browser Anda tidak mendukung Geolocation API.");
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        // Watch Position live
        navigator.geolocation.watchPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                updateGpsUI(true, `Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)} (Akurasi: ±${Math.round(accuracy)}m)`);
                syncGpsWithServer(currentLat, currentLng, accuracy, pos.coords.speed, pos.coords.heading);

                // Set form hidden values
                const fLat = document.getElementById('formCurrentLat');
                const fLng = document.getElementById('formCurrentLng');
                const iLat = document.getElementById('incidentLat');
                const iLng = document.getElementById('incidentLng');
                const sLat = document.getElementById('sosLat');
                const sLng = document.getElementById('sosLng');

                const slLat = document.getElementById('sliderLat');
                const slLng = document.getElementById('sliderLng');

                if (fLat) fLat.value = currentLat;
                if (fLng) fLng.value = currentLng;
                if (slLat) slLat.value = currentLat;
                if (slLng) slLng.value = currentLng;
                if (iLat) iLat.value = currentLat;
                if (iLng) iLng.value = currentLng;
                if (sLat) sLat.value = currentLat;
                if (sLng) sLng.value = currentLng;
            },
            (err) => {
                console.warn('GPS watch error:', err.message);
                updateGpsUI(false, "Sinyal GPS terputus atau izin akses lokasi belum aktif.");
            },
            options
        );

        // Auto sync interval setiap 45 detik selama di jalan
        setInterval(() => {
            if (currentLat && currentLng) {
                syncGpsWithServer(currentLat, currentLng);
            }
        }, 45000);
    }

    function updateGpsUI(isActive, message) {
        const topbarGpsBadge = document.getElementById('topbarGpsBadge');
        const gpsTextIndicator = document.getElementById('gpsTextIndicator');
        const gpsCoordsDisplay = document.getElementById('gpsCoordsDisplay');

        if (isActive) {
            if (topbarGpsBadge) {
                topbarGpsBadge.style.background = '#ecfdf5';
                topbarGpsBadge.style.borderColor = '#a7f3d0';
                topbarGpsBadge.style.color = '#065f46';
            }
            if (gpsTextIndicator) gpsTextIndicator.textContent = "GPS: Terhubung & Aktif";
            if (gpsCoordsDisplay) gpsCoordsDisplay.textContent = message;
        } else {
            if (topbarGpsBadge) {
                topbarGpsBadge.style.background = '#fef3c7';
                topbarGpsBadge.style.borderColor = '#fcd34d';
                topbarGpsBadge.style.color = '#92400e';
            }
            if (gpsTextIndicator) gpsTextIndicator.textContent = "GPS: Standby";
            if (gpsCoordsDisplay) gpsCoordsDisplay.textContent = message;
        }
    }

    function syncGpsWithServer(lat, lng, accuracy, speed, heading) {
        if (!scheduleId || !csrfToken) return;

        fetch(`/driver/schedule/${scheduleId}/gps`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                accuracy: accuracy || null,
                speed: speed || null,
                heading: heading || null
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log('GPS synced with server:', data);
        })
        .catch(err => {
            console.error('Error syncing GPS:', err);
        });
    }

    function triggerManualGpsSync() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;
                syncGpsWithServer(currentLat, currentLng, pos.coords.accuracy);
                alert(`Posisi berhasil diperbarui: Lat ${currentLat.toFixed(5)}, Lng ${currentLng.toFixed(5)}`);
            }, (err) => {
                alert('Gagal membaca koordinat GPS: ' + err.message);
            });
        }
    }

    // 2. FILTER MANIFEST PENUMPANG
    function filterManifest() {
        const input = document.getElementById('manifestSearchInput');
        const filter = input.value.toLowerCase();
        const items = document.querySelectorAll('.passenger-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const seat = item.getAttribute('data-seat') || '';
            if (name.includes(filter) || seat.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // 3. SEAT MAP INFO POPUP
    function showSeatInfo(seatNumber, passengerName, statusClass) {
        let statusText = 'Kosong';
        if (statusClass === 'boarded') statusText = 'Sudah Naik (Boarded)';
        else if (statusClass === 'pending') statusText = 'Dipesan (Belum Naik)';

        alert(`Nomor Kursi: ${seatNumber}\nPenumpang: ${passengerName}\nStatus: ${statusText}`);
    }

    // 4. MODALS CONTROL
    function openIncidentModal() {
        document.getElementById('modalIncident').classList.add('show');
    }
    function closeIncidentModal() {
        document.getElementById('modalIncident').classList.remove('show');
    }

    function openSosModal() {
        document.getElementById('modalSos').classList.add('show');
    }
    function closeSosModal() {
        document.getElementById('modalSos').classList.remove('show');
    }

    // Close on backdrop click
    window.addEventListener('click', (e) => {
        const modalInc = document.getElementById('modalIncident');
        const modalSos = document.getElementById('modalSos');
        if (e.target === modalInc) closeIncidentModal();
        if (e.target === modalSos) closeSosModal();
    });

    // 5. SIDEBAR ACTIVE CLICK HANDLER
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // 6. INTERACTIVE STATUS SLIDER CONFIRMATION
    function initStatusSlider() {
        const sliderWrap = document.getElementById('statusSliderWrap');
        const sliderThumb = document.getElementById('statusSliderThumb');
        const sliderProgress = document.getElementById('statusSliderProgress');
        const sliderForm = document.getElementById('mainStatusForm');

        if (!sliderWrap || !sliderThumb || !sliderForm) return;

        let isDragging = false;
        let startX = 0;
        let maxSlide = 0;

        function updateDimensions() {
            maxSlide = sliderWrap.clientWidth - sliderThumb.clientWidth - 6;
        }
        updateDimensions();
        window.addEventListener('resize', updateDimensions);

        function startDrag(clientX) {
            isDragging = true;
            startX = clientX;
            sliderThumb.style.transition = 'none';
            if (sliderProgress) sliderProgress.style.transition = 'none';
        }

        function onDrag(clientX) {
            if (!isDragging) return;
            let delta = clientX - startX;
            if (delta < 0) delta = 0;
            if (delta > maxSlide) delta = maxSlide;

            sliderThumb.style.transform = `translateX(${delta}px)`;
            if (sliderProgress) {
                const pct = (delta / maxSlide) * 100;
                sliderProgress.style.width = `${pct}%`;
            }

            // If dragged >= 88%, trigger confirmation and submit
            if (delta >= maxSlide * 0.88) {
                isDragging = false;
                sliderThumb.style.transform = `translateX(${maxSlide}px)`;
                if (sliderProgress) sliderProgress.style.width = '100%';

                const slLat = document.getElementById('sliderLat');
                const slLng = document.getElementById('sliderLng');
                if (slLat && typeof currentLat !== 'undefined' && currentLat) slLat.value = currentLat;
                if (slLng && typeof currentLng !== 'undefined' && currentLng) slLng.value = currentLng;

                setTimeout(() => {
                    sliderForm.submit();
                }, 160);
            }
        }

        function stopDrag() {
            if (!isDragging) return;
            isDragging = false;
            sliderThumb.style.transition = 'transform 0.25s ease-out';
            sliderThumb.style.transform = 'translateX(0px)';
            if (sliderProgress) {
                sliderProgress.style.transition = 'width 0.25s ease-out';
                sliderProgress.style.width = '0%';
            }
        }

        // Mouse Listeners
        sliderThumb.addEventListener('mousedown', (e) => {
            e.preventDefault();
            startDrag(e.clientX);
        });
        window.addEventListener('mousemove', (e) => onDrag(e.clientX));
        window.addEventListener('mouseup', stopDrag);

        // Touch Listeners for mobile/tablet drivers
        sliderThumb.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches[0]) {
                startDrag(e.touches[0].clientX);
            }
        }, { passive: true });
        window.addEventListener('touchmove', (e) => {
            if (e.touches && e.touches[0]) {
                onDrag(e.touches[0].clientX);
            }
        }, { passive: true });
        window.addEventListener('touchend', stopDrag);
    }

    // Toggle Mobile Sidebar Drawer
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    }

    // Start GPS & Slider on load
    document.addEventListener('DOMContentLoaded', () => {
        initGeolocation();
        initStatusSlider();
    });
</script>
