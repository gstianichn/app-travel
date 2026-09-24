            <!-- ================= 6. MELIHAT RUTE PERJALANAN ================= -->
            <div class="card-panel" id="section-rute" style="margin-bottom: 24px;">
                <div class="card-panel-header">
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                            <span>FITUR 6</span>
                            <span>•</span>
                            <span>RUTE &amp; NAVIGASI WAYPOINTS</span>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <span>🗺️</span>
                            <span>Melihat Rute Perjalanan</span>
                        </h3>
                        <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                            Fokus: Peta rute navigasi langsung (Google Maps &amp; Waze) dan urutan waypoints jemput, rest area, hingga pool tujuan.
                        </p>
                    </div>
                    <span class="pill-badge" style="font-size:11px;">1-Klik Maps &amp; Waze</span>
                </div>

                <div class="card-panel-body">
                    <div class="waypoint-timeline">
                        @foreach ($waypoints as $wp)
                            <div class="waypoint-node {{ $wp['status'] }}">
                                <div class="waypoint-marker">
                                    {{ $loop->iteration }}
                                </div>

                                <div class="waypoint-box">
                                    <div class="waypoint-info">
                                        <span class="waypoint-tag">{{ $wp['badge'] }}</span>
                                        <h4>{{ $wp['title'] }}</h4>
                                        <p>{{ $wp['subtitle'] }}</p>
                                    </div>

                                    <div class="waypoint-actions">
                                        <a href="{{ $wp['gmaps_url'] }}" target="_blank" class="btn-nav btn-nav-gmaps" title="Buka Google Maps">
                                            <span>🗺️</span>
                                            <span>Maps</span>
                                        </a>

                                        <a href="{{ $wp['waze_url'] }}" target="_blank" class="btn-nav btn-nav-waze" title="Buka Waze">
                                            <span>🚙</span>
                                            <span>Waze</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

