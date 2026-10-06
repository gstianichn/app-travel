        <!-- ================= 7. LAPOR KENDALA AKTIF ================= -->
        @if ($activeIncidents->isNotEmpty())
            <section class="card-panel" style="margin-bottom: 24px; border-color: #fcd34d;">
                <div class="card-panel-header" style="background:#fffbeb;">
                    <h3 style="color:#b45309; display:flex; align-items:center; gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Kendala Jalan Terlaporkan pada Perjalanan Ini</span>
                    </h3>
                </div>
                <div class="card-panel-body">
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($activeIncidents as $inc)
                            <div style="background:#fef3c7; padding:12px 16px; border-radius:10px; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <strong style="color:#92400e;">{{ $inc->categoryLabel() }}</strong>
                                    <p style="font-size:12px; color:#78350f; margin-top:2px;">{{ $inc->description }}</p>
                                    <small style="font-size:10.5px; color:#b45309;">Lokasi: {{ $inc->location_name }} • Dilaporkan: {{ $inc->reported_at?->format('H:i') }} WIB</small>
                                </div>
                                <span class="pill-badge" style="background:white; font-size:11px; font-weight:700;">
                                    {{ $inc->statusLabel() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif


<!-- ================= MODAL: 7. LAPOR KENDALA JALAN ================= -->
<div class="modal-backdrop" id="modalIncident">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="display:flex; align-items:center; gap:8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Laporkan Kendala di Perjalanan</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeIncidentModal()" style="display:inline-flex; align-items:center; justify-content:center;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('dashboard.driver.schedule.incident', $activeSchedule->id ?? 1) }}">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="latitude" id="incidentLat" value="">
                <input type="hidden" name="longitude" id="incidentLng" value="">

                <div class="form-group">
                    <label>Jenis Kendala Jalan</label>
                    <select name="category" class="form-control" required>
                        <option value="ban_bocor">Ban Bocor / Kempes</option>
                        <option value="macet_parah">Macet Parah / Total (Keterlambatan)</option>
                        <option value="mesin_mogok">Kendala Mesin / Radiator Panas</option>
                        <option value="penumpang_batal">Penumpang Batal Mendadak / Tidak Datang</option>
                        <option value="kecelakaan">Insiden Senggolan / Kecelakaan Jalan</option>
                        <option value="darurat_medis">Darurat Medis Penumpang</option>
                        <option value="lainnya">Kendala Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tingkat Urgensi</label>
                    <select name="severity" class="form-control" required>
                        <option value="sedang">Sedang (Perjalanan terhambat sementara)</option>
                        <option value="rendah">Rendah (Pemberitahuan operasional)</option>
                        <option value="tinggi">Tinggi (Memerlukan armada cadangan / mekanik)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lokasi Terkini Armada</label>
                    <input type="text" name="location_name" id="incidentLocationInput" class="form-control" placeholder="Contoh: Tol Cipularang KM 88 Jalur A">
                </div>

                <div class="form-group">
                    <label>Keterangan Tambahan & Estimasi Solusi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Ceritakan kondisi kendala di lapangan secara singkat..." required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeIncidentModal()">Batal</button>
                <button type="submit" class="btn-primary">Kirim Laporan ke Pusat Kendali</button>
            </div>
        </form>
    </div>
</div>
