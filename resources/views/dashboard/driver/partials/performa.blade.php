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
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke-width="2"/></svg>
                <span>Melihat Performa Pengemudi</span>
            </h3>
            <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                Fokus: Evaluasi kepuasan penumpang, ketepatan waktu operasional (OTP), dan keselamatan perjalanan.
            </p>
        </div>

        <div class="pill-badge" style="background: #fef3c7; color: #92400e; font-weight: 800; border-color: #fde68a; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <span>Rating Pengemudi: {{ $driverRating ?? '4.9' }} / 5.0</span>
        </div>
    </div>

    <div class="card-panel-body">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 16px;">
            <div class="spec-item-box" style="border-left: 4px solid #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);">
                <div class="spec-label" style="display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke-width="2"/></svg>
                    <span>Rating Bintang</span>
                </div>
                <div class="spec-val" style="font-size: 24px; color: #b45309;">{{ $driverRating ?? '4.9' }} <span style="font-size: 14px; font-weight: 600; color: #92400e;">/ 5.0</span></div>
                <div class="spec-sub">Berdasarkan ulasan penumpang terverifikasi</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #10b981; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                <div class="spec-label" style="display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
                    <span>On-Time Performance</span>
                </div>
                <div class="spec-val" style="font-size: 24px; color: #059669;">98.5%</div>
                <div class="spec-sub">Keberangkatan &amp; kedatangan tepat waktu</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #3b82f6; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                <div class="spec-label" style="display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="3" stroke-width="2"/><circle cx="7" cy="18" r="2" fill="currentColor"/><circle cx="17" cy="18" r="2" fill="currentColor"/><path stroke-linecap="round" stroke-width="2" d="M2 11h20M14 6v5M8 6v5"/></svg>
                    <span>Total Ritase Selesai</span>
                </div>
                <div class="spec-val" style="font-size: 24px; color: #1d4ed8;">{{ $totalCompletedTrips ?? 120 }}</div>
                <div class="spec-sub">Perjalanan aman berhasil dituntaskan</div>
            </div>

            <div class="spec-item-box" style="border-left: 4px solid #8b5cf6; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);">
                <div class="spec-label" style="display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Indeks Keselamatan</span>
                </div>
                <div class="spec-val" style="font-size: 24px; color: #6d28d9;">100%</div>
                <div class="spec-sub">Zero Incident &amp; SOP berkendara aman</div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--brand-border); border-radius: 10px; padding: 14px 18px;">
            <div style="font-size: 13px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Ulasan &amp; Kesan Positif Penumpang Terbanyak:</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg> Mengemudi Sangat Halus</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Ramah &amp; Membantu Bawaan</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Kabin Dingin &amp; Bersih</span>
                <span class="pill-badge" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg> Berangkat Tepat Waktu</span>
            </div>
        </div>
    </div>
</section>
