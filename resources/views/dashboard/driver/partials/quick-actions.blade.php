        <!-- ================= 5. TOMBOL KENDALA OPERASIONAL (QUICK ACTIONS) ================= -->
        <section class="quick-action-strip" id="section-quick-actions" style="margin-bottom: 24px;">
            <div class="quick-action-strip-title" style="display:flex; align-items:center; gap:10px;">
                <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; background:#e3f5ee; color:#087f55;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
                </span>
                <div>
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.5px;">BAGIAN 5: OPERASIONAL LAPANGAN</div>
                    <strong style="font-size:15px; color:var(--brand-dark);">Tombol Kendala Operasional (Quick Actions)</strong>
                </div>
            </div>

            <div class="quick-action-buttons-group">
                <!-- Lapor Kendala: Tombol lapis kedua untuk memilih masalah lapangan (ban bocor, macet parah, mogok, penumpang tidak bisa dihubungi) -->
                <button type="button" class="btn-quick-shortcut warning" onclick="openIncidentModal()" title="Buka menu lapis kedua memilih kendala lapangan" style="display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Lapor Kendala Lapangan</span>
                </button>

                <!-- Denah Kursi (Seat Map): Tombol pintasan untuk melihat denah visual penataan bangku kabin -->
                <a href="#section-seatmap" class="btn-quick-shortcut" title="Buka denah visual tata letak kursi kabin" style="display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h14a1 1 0 011 1v9H4V5a1 1 0 011-1zM4 14v4a2 2 0 002 2h12a2 2 0 002-2v-4M7 20v2M17 20v2"/></svg>
                    <span>Denah Kursi (Seat Map)</span>
                </a>

                <!-- Emergency Center SOS -->
                <button type="button" class="btn-quick-shortcut danger" onclick="openSosModal()" title="Akses darurat instan" style="display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01"/></svg>
                    <span>Emergency Center SOS</span>
                </button>

                <!-- Hubungi Dispatcher Pool -->
                <a href="tel:081122334455" class="btn-quick-shortcut" title="Hubungi Dispatcher Pool Resmi" style="display:inline-flex; align-items:center; gap:8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>Hubungi Dispatcher Pool</span>
                </a>
            </div>
        </section>

