<!-- ================= 9. MELIHAT INFORMASI PENUMPANG ================= -->
<section class="card-panel" id="section-info-penumpang-detail" style="margin-bottom: 24px;">
    <div class="card-panel-header">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(30, 41, 59, 0.06); padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                <span>FITUR 9</span>
                <span>•</span>
                <span>INFORMASI &amp; KONTAK PENUMPANG LENGKAP</span>
            </div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <span>👤</span>
                <span>Melihat Informasi Penumpang</span>
            </h3>
            <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #64748b;">
                Fokus: Rincian lengkap identitas penumpang, titik jemput/turun spesifik, patokan rumah, kontak langsung, dan catatan khusus.
            </p>
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="#section-manifest" class="btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <span>📋</span>
                <span>Kembali ke Manifes</span>
            </a>
        </div>
    </div>

    <div class="card-panel-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px;">
            @forelse ($manifestPassengers as $p)
                <div class="passenger-item {{ $p->is_boarded ? 'is-boarded' : '' }}" style="background: #ffffff; border: 1px solid var(--brand-border); border-radius: 12px; padding: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="seat-badge-pill" style="font-size: 13px; padding: 6px 12px;">
                                Seat {{ $p->seat_number }}
                            </div>
                            <div>
                                <strong style="font-size: 14px; color: var(--brand-dark); display: block;">{{ $p->name }}</strong>
                                <small style="color: var(--text-muted); font-size: 11px;">Tiket: {{ $p->booking_code }}</small>
                            </div>
                        </div>

                        <div>
                            @if ($p->is_paid)
                                <span class="badge-paid" style="font-size: 10.5px; padding: 3px 8px; border-radius: 6px;">✓ Lunas Online</span>
                            @else
                                <span class="badge-cod" style="font-size: 10.5px; padding: 3px 8px; border-radius: 6px;">💵 COD Rp {{ number_format($p->amount_due, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div style="background: #f8fafc; border-radius: 8px; padding: 10px 12px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;">
                        <div>
                            <span style="color: #64748b;">Titik Jemput:</span>
                            <strong style="color: #0f172a; display: block;">📍 {{ $p->pickup_point }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b;">Titik Turun / Tujuan:</span>
                            <strong style="color: #0f172a; display: block;">🏁 {{ $p->dropoff_point }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b;">Catatan Bawaan / Bagasi:</span>
                            <span style="color: #334155; font-weight: 600; display: block;">🧳 {{ $p->baggage_notes }}</span>
                        </div>
                        @if (!empty($p->special_request) && $p->special_request !== '-')
                            <div style="color: #b45309; background: #fffbeb; border: 1px solid #fef3c7; padding: 6px 8px; border-radius: 6px;">
                                💬 <strong>Patokan / Permintaan Khusus:</strong> {{ $p->special_request }}
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div class="contact-group">
                            <a href="https://wa.me/{{ $p->wa_phone }}?text={{ urlencode('Halo Kak ' . $p->name . ', saya sopir travel Anda untuk rute hari ini.') }}" target="_blank" class="btn-contact btn-contact-wa" style="font-size: 11.5px; padding: 6px 12px;">
                                <span>💬</span> WhatsApp
                            </a>
                            <a href="tel:{{ $p->phone }}" class="btn-contact btn-contact-tel" style="font-size: 11.5px; padding: 6px 12px;">
                                <span>📞</span> Telepon
                            </a>
                        </div>

                        <div>
                            @if ($p->is_boarded)
                                <span style="font-size: 11px; font-weight: 800; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 5px 10px; border-radius: 6px;">
                                    ✓ Sudah Naik
                                </span>
                            @else
                                <span style="font-size: 11px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 5px 10px; border-radius: 6px;">
                                    Belum Naik
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: var(--text-muted);">
                    Belum ada data penumpang pada jadwal ini.
                </div>
            @endforelse
        </div>
    </div>
</section>
