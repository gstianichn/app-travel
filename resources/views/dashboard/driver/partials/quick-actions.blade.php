        <!-- ================= 5. TOMBOL KENDALA OPERASIONAL (QUICK ACTIONS) ================= -->
        <section class="quick-action-strip" id="section-quick-actions" style="margin-bottom: 24px;">
            <div class="quick-action-strip-title">
                <span style="font-size:22px;">⚡</span>
                <div>
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.5px;">BAGIAN 5: OPERASIONAL LAPANGAN</div>
                    <strong style="font-size:15px; color:var(--brand-dark);">Tombol Kendala Operasional (Quick Actions)</strong>
                </div>
            </div>

            <div class="quick-action-buttons-group">
                <!-- Lapor Kendala: Tombol lapis kedua untuk memilih masalah lapangan (ban bocor, macet parah, mogok, penumpang tidak bisa dihubungi) -->
                <button type="button" class="btn-quick-shortcut warning" onclick="openIncidentModal()" title="Buka menu lapis kedua memilih kendala lapangan">
                    <span>⚠️</span>
                    <span>Lapor Kendala Lapangan</span>
                </button>

                <!-- Denah Kursi (Seat Map): Tombol pintasan untuk melihat denah visual penataan bangku kabin -->
                <a href="#section-seatmap" class="btn-quick-shortcut" title="Buka denah visual tata letak kursi kabin">
                    <span>💺</span>
                    <span>Denah Kursi (Seat Map)</span>
                </a>

                <!-- Emergency Center SOS -->
                <button type="button" class="btn-quick-shortcut danger" onclick="openSosModal()" title="Akses darurat instan">
                    <span>🚨</span>
                    <span>Emergency Center SOS</span>
                </button>

                <!-- Hubungi Dispatcher Pool -->
                <a href="tel:081122334455" class="btn-quick-shortcut" title="Hubungi Dispatcher Pool Resmi">
                    <span>📞</span>
                    <span>Hubungi Dispatcher Pool</span>
                </a>
            </div>
        </section>

