<!-- ================= 13. MELIHAT PERFORMA PENGEMUDI ================= -->
<section class="card-panel" id="section-performa" style="margin-bottom: 24px;">
    <div class="card-panel-header">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                <span>FITUR 13</span>
                <span>•</span>
                <span>METRIK PERFORMA &amp; KINERJA PENGEMUDI</span>
            </div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <span>⭐</span>
                <span>Melihat Performa Pengemudi</span>
            </h3>
            <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                Fokus: Evaluasi kepuasan penumpang, ketepatan waktu operasional (OTP), dan keselamatan perjalanan.
            </p>
        </div>

        <div class="pill-badge" style="background: #fef3c7; color: #92400e; font-weight: 800; border-color: #fde68a;">
            ⭐ Rating Pengemudi: {{ $driverRating ?? '4.9' }} / 5.0
        </div>
    </div>

    <div class="card-panel-body">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 16px;">
            <div class="spec-item-box" style="border-left: 4px solid #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);">
                <div class="spec-label">⭐ Rating Bintang</div>
                <div class="spec-val" style="font-size: 24px; color: #b45309;">{{ $driverRating ?? '4.9' }} <span style="font-size: 14px; font-weight: 600; color: #92400e;">/ 5.0</span></div>
                <div class="spec-sub">Berdasarkan ulasan penumpang terverifikasi</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #10b981; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                <div class="spec-label">⏱️ On-Time Performance</div>
                <div class="spec-val" style="font-size: 24px; color: #059669;">98.5%</div>
                <div class="spec-sub">Keberangkatan &amp; kedatangan tepat waktu</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #3b82f6; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                <div class="spec-label">🚐 Total Ritase Selesai</div>
                <div class="spec-val" style="font-size: 24px; color: #1d4ed8;">{{ $totalCompletedTrips ?? 120 }}</div>
                <div class="spec-sub">Perjalanan aman berhasil dituntaskan</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #8b5cf6; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);">
                <div class="spec-label">🛡️ Indeks Keselamatan</div>
                <div class="spec-val" style="font-size: 24px; color: #6d28d9;">100%</div>
                <div class="spec-sub">Zero Incident &amp; SOP berkendara aman</div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--brand-border); border-radius: 10px; padding: 14px 18px;">
            <div style="font-size: 13px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Ulasan &amp; Kesan Positif Penumpang Terbanyak:</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px;">👍 Mengemudi Sangat Halus</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px;">🤝 Ramah &amp; Membantu Bawaan</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px;">❄️ Kabin Dingin &amp; Bersih</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px;">⏰ Berangkat Tepat Waktu</span>
            </div>
        </div>
    </div>
</section>
