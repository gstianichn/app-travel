@extends('layouts.driver')

@section('title', 'Melaporkan Masalah Perjalanan - Cockpit Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; min-height: 38px; padding: 0 16px;">
            <span>&larr;</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    <!-- DAFTAR KENDALA AKTIF -->
    @if ($activeIncidents->isNotEmpty())
        <section class="cockpit-master-card" style="margin-bottom: 24px; border-color: #fcd34d;">
            <div class="cockpit-hero-header" style="background: linear-gradient(135deg, #78350f 0%, #b45309 100%);">
                <div class="cockpit-title-group">
                    <div class="cockpit-tag-pill" style="background: rgba(255,255,255,0.18); color: white;">
                        <span></span>
                        <span>STATUS LAPORAN AKTIF</span>
                    </div>
                    <h2>
                        <span></span>
                        <span>Kendala Jalan Terlaporkan pada Perjalanan Ini</span>
                    </h2>
                    <p>Kendala berikut sedang ditindaklanjuti oleh koordinator pool dan dispatcher.</p>
                </div>
            </div>
            <div style="padding: 20px;">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach ($activeIncidents as $inc)
                        <div style="background:#fef3c7; border: 1px solid #fde68a; padding:14px 18px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <strong style="color:#92400e; font-size: 15px;">{{ $inc->categoryIcon() }} {{ $inc->categoryLabel() }}</strong>
                                <p style="font-size:13px; color:#78350f; margin: 4px 0 6px 0;">{{ $inc->description }}</p>
                                <small style="font-size:11.5px; color:#b45309;">Lokasi: {{ $inc->location_name }} • ⏰ Dilaporkan: {{ $inc->reported_at?->format('H:i') }} WIB • Estimasi Tertunda: {{ $inc->estimated_delay_minutes ?? 15 }} menit</small>
                            </div>
                            <span class="pill-badge" style="background:white; font-size:12px; font-weight:800; color: #b45309; padding: 6px 14px; border-radius: 8px; border: 1px solid #fcd34d;">
                                {{ $inc->statusLabel() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- FORM PELAPORAN MASALAH PERJALANAN -->
    <section class="cockpit-master-card">
        <div class="cockpit-hero-header" style="background: linear-gradient(135deg, #b45309 0%, #d97706 100%);">
            <div class="cockpit-title-group">
                <div class="cockpit-tag-pill" style="background: rgba(255,255,255,0.18); color: white;">
                    <span>FITUR 10</span>
                    <span>•</span>
                    <span>BANTUAN OPERASIONAL & KENDALA</span>
                </div>
                <h2>
                    <span></span>
                    <span>Form Melaporkan Masalah Perjalanan</span>
                </h2>
                <p>Laporkan kendala lapangan seperti ban bocor, kemacetan parah, mogok, atau kendala penumpang secara instan ke pusat operasional.</p>
            </div>
        </div>

        <div style="padding: 24px;">
            <form method="POST" action="{{ route('dashboard.driver.schedule.incident', $activeSchedule->id ?? 1) }}">
                @csrf
                <input type="hidden" name="latitude" id="pageIncidentLat" value="">
                <input type="hidden" name="longitude" id="pageIncidentLng" value="">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Kategori Kendala Operasional</label>
                        <select name="category" class="form-control" style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px;" required>
                            <option value="ban_bocor">Ban Bocor / Kempes</option>
                            <option value="macet_parah">Macet Parah / Total (Keterlambatan)</option>
                            <option value="mesin_mogok">Kendala Mesin / Radiator Panas</option>
                            <option value="penumpang_batal">Penumpang Batal Mendadak / Tidak Datang</option>
                            <option value="kecelakaan">Insiden Senggolan / Kecelakaan Jalan</option>
                            <option value="darurat_medis">Darurat Medis Penumpang</option>
                            <option value="lainnya">Kendala Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Perkiraan Keterlambatan Waktu</label>
                        <select name="estimated_delay_minutes" class="form-control" style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px;">
                            <option value="15">± 15 Menit</option>
                            <option value="30">± 30 Menit</option>
                            <option value="45">± 45 Menit</option>
                            <option value="60">± 1 Jam</option>
                            <option value="120">> 1 - 2 Jam (Kendala Berat)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Patokan Lokasi Kendala / Titik Kejadian</label>
                    <input type="text" name="location_name" class="form-control" style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px;" placeholder="Contoh: KM 78 Tol Cipularang arah Jakarta" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Deskripsi & Kronologi Singkat Kendala</label>
                    <textarea name="description" rows="4" class="form-control" style="width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px; resize: vertical;" placeholder="Jelaskan kondisi unit, dampak terhadap penumpang, dan bantuan yang dibutuhkan segera..." required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('dashboard.driver.index') }}" class="btn-action-ghost" style="padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; color: #64748b; border: 1px solid var(--border);">
                        Batal
                    </a>
                    <button type="submit" class="btn-main-status-trigger" style="margin-top: 0; width: auto; padding: 12px 28px; background: linear-gradient(135deg, #b45309 0%, #d97706 100%);">
                        <span>Kirim Laporan Kendala ke Pusat Kendali</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
