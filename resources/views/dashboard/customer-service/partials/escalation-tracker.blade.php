<!-- ============================================================
     4. PAPAN KASUS ESKALASI BERJALAN (ESCALATION TRACKER)
     Daftar tiket kendala yang masih menunggu solusi
============================================================ -->

<div class="card escalation-card" id="eskalasi-tracker">
    <div class="card-header">
        <div class="card-header-left">
            <span class="card-icon-bullet" style="background: var(--purple);"></span>
            <div>
                <h2 class="card-title">Papan Kasus Eskalasi Berjalan (Escalation Tracker)</h2>
                <p class="card-subtitle-small">Pemantauan kasus komplain yang diteruskan ke Admin Utama, Operasional, atau Keuangan.</p>
            </div>
        </div>
        <span class="card-tag" style="background: var(--purple-soft); color: var(--purple); font-weight: 700;">
            3 Kasus Aktif
        </span>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive-wrapper">
            <table class="cs-data-table">
                <thead>
                    <tr>
                        <th>ID Tiket</th>
                        <th>Kategori Kendala</th>
                        <th>Penumpang &amp; No. HP</th>
                        <th>Waktu Dilaporkan</th>
                        <th>Status Eskalasi</th>
                        <th>Aksi Follow-Up</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Kasus 1: Refund Tiket (Keuangan) -->
                    <tr>
                        <td>
                            <strong class="text-purple">#ESK-104</strong>
                        </td>
                        <td>
                            <div class="escalation-cat-cell">
                                <span class="cat-pill pill-refund">Permohonan Refund</span>
                                <small class="text-muted">Klaim pembatalan karena sakit</small>
                            </div>
                        </td>
                        <td>
                            <div class="escalation-user-cell">
                                <strong>Budi Prasetyo</strong>
                                <small class="text-muted">0812-9988-7766 (TRV-2026-0925)</small>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted text-xs">15 menit lalu</span>
                        </td>
                        <td>
                            <span class="status-pill-badge badge-pending-admin">
                                Menunggu Approval Admin
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-followup" onclick="followUpEscalation('ESK-104', 'Budi Prasetyo', 'Admin Utama')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Cek Respon</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Kasus 2: Barang Tertinggal (Operasional / Driver) -->
                    <tr>
                        <td>
                            <strong class="text-purple">#ESK-103</strong>
                        </td>
                        <td>
                            <div class="escalation-cat-cell">
                                <span class="cat-pill pill-lostfound">Barang Tertinggal</span>
                                <small class="text-muted">Koper kabin hitam di bagasi D 8911 JKT</small>
                            </div>
                        </td>
                        <td>
                            <div class="escalation-user-cell">
                                <strong>Hendra Gunawan</strong>
                                <small class="text-muted">0813-2233-4455 (TRV-2026-0811)</small>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted text-xs">42 menit lalu</span>
                        </td>
                        <td>
                            <span class="status-pill-badge badge-investigating">
                                Investigasi Pool / Driver
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-followup" onclick="followUpEscalation('ESK-103', 'Hendra Gunawan', 'Tim Operasional Pool')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Cek Respon</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Kasus 3: Kendala Teknis Pembayaran -->
                    <tr>
                        <td>
                            <strong class="text-purple">#ESK-102</strong>
                        </td>
                        <td>
                            <div class="escalation-cat-cell">
                                <span class="cat-pill pill-system">Kendala Payment VA</span>
                                <small class="text-muted">Sudah transfer BCA, status belum auto-sync</small>
                            </div>
                        </td>
                        <td>
                            <div class="escalation-user-cell">
                                <strong>Siti Rahma</strong>
                                <small class="text-muted">0878-1122-3344 (TRV-2026-0774)</small>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted text-xs">1.5 jam lalu</span>
                        </td>
                        <td>
                            <span class="status-pill-badge badge-finance">
                                Verifikasi Tim Finance
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-followup" onclick="followUpEscalation('ESK-102', 'Siti Rahma', 'Tim Keuangan')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Cek Respon</span>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
