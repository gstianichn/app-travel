<!-- ================= MODAL: 8. EMERGENCY SOS CENTER ================= -->
<div class="modal-backdrop" id="modalSos">
    <div class="modal-box" style="border: 2px solid #ef4444;">
        <div class="modal-header" style="background:#fee2e2;">
            <h3 style="color:#b91c1c;">
                <span>🚨</span>
                <span>KONFIRMASI AKTIVASI EMERGENCY SOS</span>
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeSosModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('dashboard.driver.schedule.sos', $activeSchedule->id ?? 1) }}" id="formSos">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="latitude" id="sosLat" value="">
                <input type="hidden" name="longitude" id="sosLng" value="">

                <p style="font-size:13.5px; color:#7f1d1d; line-height:1.6; margin-bottom:16px;">
                    <strong>PERHATIAN!</strong> Fitur ini memancarkan sinyal darurat langsung ke ruang kendali operasional travel beserta koordinat GPS live Anda.
                </p>

                <div class="form-group">
                    <label>Keterangan Darurat (Opsional)</label>
                    <input type="text" name="description" class="form-control" placeholder="Contoh: Tabrakan beruntun di Tol KM 92, butuh ambulans">
                </div>

                <div style="background:#fef2f2; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-bottom:14px;">
                    <div style="font-weight:700; font-size:12px; color:#991b1b; margin-bottom:6px;">Nomor Panggilan Darurat Cepat:</div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="tel:081122334455" class="btn-contact btn-contact-tel">HQ: 0811-2233-4455</a>
                        <a href="tel:118" class="btn-contact btn-nav-gmaps">Ambulans (118)</a>
                        <a href="tel:110" class="btn-contact" style="background:#1e293b; color:white;">Polisi (110)</a>
                    </div>
                </div>

                <button type="submit" class="btn-danger-sos">
                    <span>🚨</span>
                    <span>PANCARKAN SINYAL SOS SEKARANG</span>
                </button>
            </div>
        </form>
    </div>
</div>

