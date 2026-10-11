        <!-- ================= 6. GPS TRACKING AKTIF (BACKGROUND & LIVE) ================= -->
        <section class="gps-panel" id="section-gps">
            <div class="gps-status-group">
                <div class="gps-info">
                    <h3>GPS Tracking Latar Belakang Aktif</h3>
                    <p id="gpsCoordsDisplay">
                        @if ($activeSchedule->current_latitude && $activeSchedule->current_longitude)
                            Lat: {{ $activeSchedule->current_latitude }}, Lng: {{ $activeSchedule->current_longitude }}
                            (Diperbarui {{ $activeSchedule->last_gps_at ? $activeSchedule->last_gps_at->diffForHumans() : 'baru saja' }})
                        @else
                            Menunggu izin browser & mendeteksi sinyal satelit GPS...
                        @endif
                    </p>
                </div>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="gps-action-btn" onclick="triggerManualGpsSync()">
                    <span></span>
                    <span>Perbarui Posisi Sekarang</span>
                </button>
            </div>
        </section>

        <!-- COCKPIT DUA KOLOM: NAVIGASI WAYPOINTS & DENAH KURSI -->
        <div class="cockpit-grid">

