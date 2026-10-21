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

    // 3. SEAT MAP DRIVER INSPECTOR & HIGHLIGHT
    function selectDriverSeat(btn) {
        if (!btn || !btn.dataset.seat) return;

        let data = {};
        try {
            data = JSON.parse(btn.dataset.seat);
        } catch(e) {
            console.error("Invalid seat data", e);
            return;
        }

        // Highlight active seat button
        document.querySelectorAll('.seat-btn-driver').forEach(b => {
            b.style.outline = 'none';
            b.style.boxShadow = '';
        });
        btn.style.outline = '3px solid #065f46';
        btn.style.boxShadow = '0 0 0 4px rgba(6, 95, 70, 0.2)';

        const inspectTitle = document.getElementById('inspectSeatTitle');
        const inspectBadge = document.getElementById('inspectSeatBadge');
        const contentDefault = document.getElementById('inspectContentDefault');
        const contentDetails = document.getElementById('inspectContentDetails');

        if (inspectTitle) inspectTitle.textContent = `Kursi ${data.seatNumber}`;

        if (data.isAvailable) {
            if (inspectBadge) {
                inspectBadge.className = 'ds-badge ds-badge--muted';
                inspectBadge.textContent = 'Kosong';
            }
            if (contentDefault) {
                contentDefault.style.display = 'block';
                contentDefault.innerHTML = `
                    <div style="font-size: 32px; margin-bottom: 8px;">💺</div>
                    <p class="font-bold text-slate-700 m-0 text-sm">Kursi ${data.seatNumber} Masih Kosong</p>
                    <p class="text-xs text-slate-500 mt-1 m-0">Belum ada pemesanan tiket untuk kursi ini pada jadwal aktif.</p>
                `;
            }
            if (contentDetails) contentDetails.style.display = 'none';
            return;
        }

        // Ada penumpang terisi
        if (contentDefault) contentDefault.style.display = 'none';
        if (contentDetails) contentDetails.style.display = 'block';

        if (inspectBadge) {
            if (data.isBoarded) {
                inspectBadge.className = 'ds-badge ds-badge--ok';
                inspectBadge.textContent = 'Sudah Naik (Boarded)';
            } else {
                inspectBadge.className = 'ds-badge ds-badge--warn';
                inspectBadge.textContent = 'Belum Naik (Menunggu)';
            }
        }

        const nameEl = document.getElementById('inspectPassengerName');
        const codeEl = document.getElementById('inspectTicketCode');
        const pickupEl = document.getElementById('inspectPickup');
        const dropoffEl = document.getElementById('inspectDropoff');
        const paymentEl = document.getElementById('inspectPayment');
        const baggageEl = document.getElementById('inspectBaggage');

        if (nameEl) nameEl.textContent = data.passengerName;
        if (codeEl) codeEl.textContent = `Kode Booking: ${data.bookingCode}`;
        if (pickupEl) pickupEl.textContent = data.pickup;
        if (dropoffEl) dropoffEl.textContent = data.dropoff;
        if (paymentEl) {
            paymentEl.textContent = data.payment;
            paymentEl.style.color = data.isPaid ? '#059669' : '#b45309';
        }
        if (baggageEl) baggageEl.textContent = data.baggage;

        // WA & Phone
        const waBtn = document.getElementById('inspectWaBtn');
        const telBtn = document.getElementById('inspectTelBtn');
        const contactWrap = document.getElementById('inspectContactBtns');

        if (data.phone && data.phone !== '-') {
            if (contactWrap) contactWrap.style.display = 'flex';
            if (waBtn) waBtn.href = `https://wa.me/${data.waPhone}?text=${encodeURIComponent('Halo ' + data.passengerName + ', saya pengemudi TravelKu untuk perjalanan Anda (Kursi ' + data.seatNumber + ').')}`;
            if (telBtn) telBtn.href = `tel:${data.phone}`;
        } else {
            if (contactWrap) contactWrap.style.display = 'none';
        }

        // Action Form
        const checkinForm = document.getElementById('inspectCheckinForm');
        const statusInput = document.getElementById('inspectFormStatus');
        const markPaidInput = document.getElementById('inspectFormMarkPaid');
        const submitBtn = document.getElementById('inspectSubmitBtn');
        const actionWrap = document.getElementById('inspectActionContainer');

        if (data.checkinUrl) {
            if (actionWrap) actionWrap.style.display = 'block';
            if (checkinForm) checkinForm.action = data.checkinUrl;

            if (data.isBoarded) {
                if (statusInput) statusInput.value = 'belum_hadir';
                if (markPaidInput) markPaidInput.value = '0';
                if (submitBtn) {
                    submitBtn.className = 'ds-btn ds-btn--secondary';
                    submitBtn.innerHTML = '<span>↩ Batalkan Absensi</span>';
                }
            } else {
                if (statusInput) statusInput.value = 'boarded';
                if (markPaidInput) markPaidInput.value = data.isPaid ? '0' : '1';
                if (submitBtn) {
                    submitBtn.className = 'ds-btn ds-btn--primary';
                    submitBtn.innerHTML = data.isPaid
                        ? '<span>✓ Check-in / Naik Kabin</span>'
                        : '<span>💵 Terima COD & Check-in</span>';
                }
            }
        } else {
            if (actionWrap) actionWrap.style.display = 'none';
        }
    }

    function showSeatInfo(seatNumber, passengerName, statusClass) {
        // Fallback backward compatibility
        const targetBtn = Array.from(document.querySelectorAll('.seat-btn-driver')).find(b => {
            try {
                const d = JSON.parse(b.dataset.seat);
                return d.seatNumber == seatNumber;
            } catch(e) { return false; }
        });
        if (targetBtn) {
            selectDriverSeat(targetBtn);
        } else {
            alert(`Nomor Kursi: ${seatNumber}\nPenumpang: ${passengerName}`);
        }
    }

    // Auto-select first occupied seat on page load
    document.addEventListener('DOMContentLoaded', () => {
        const firstOccupiedSeat = document.querySelector('.seat-btn-driver');
        if (firstOccupiedSeat) {
            selectDriverSeat(firstOccupiedSeat);
        }
    });

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
    function toggleSidebar(forceState) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('driver-sidebar-overlay');
        if (typeof forceState === 'boolean') {
            if (sidebar) sidebar.classList.toggle('mobile-open', forceState);
            if (overlay) overlay.classList.toggle('active', forceState);
        } else {
            if (sidebar) sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('active');
        }
    }

    // 7. PRESERVE SCROLL POSITION ON ACTION/FORM SUBMISSIONS
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            sessionStorage.setItem('driver_dashboard_scroll_pos', window.scrollY);
        });
    });

    // In-page smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            if (!targetId) return;
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                e.preventDefault();
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, null, '#' + targetId);
            }
        });
    });

    // Start GPS, Slider & Restore scroll position on load
    document.addEventListener('DOMContentLoaded', () => {
        initGeolocation();
        initStatusSlider();

        const savedPos = sessionStorage.getItem('driver_dashboard_scroll_pos');
        if (savedPos !== null) {
            window.scrollTo({
                top: parseInt(savedPos, 10),
                behavior: 'instant'
            });
            sessionStorage.removeItem('driver_dashboard_scroll_pos');
        }
    });
</script>
