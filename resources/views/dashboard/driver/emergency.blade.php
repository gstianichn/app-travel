@extends('layouts.driver')

@section('title', 'Emergency Center - Cockpit Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="btn-action-ghost" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; font-weight: 700; color: var(--text-muted); background: white; padding: 8px 14px; border-radius: 10px; border: 1px solid var(--border);">
            <span>←</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    <!-- EMERGENCY CENTER HEADER -->
    <section class="cockpit-master-card" style="border-color: #fca5a5;">
        <div class="cockpit-hero-header" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 60%, #b91c1c 100%);">
            <div class="cockpit-title-group">
                <div class="cockpit-tag-pill" style="background: rgba(255,255,255,0.22); color: white;">
                    <span>FITUR 11</span>
                    <span>•</span>
                    <span>PUSAT DARURAT & KESELAMATAN (SOS)</span>
                </div>
                <h2>
                    <span>🚨</span>
                    <span>Emergency Center — Pusat Kendali & Bantuan Darurat</span>
                </h2>
                <p>Gunakan halaman ini dalam situasi darurat kritis lapangan untuk memicu sirene pertolongan cepat dan menghubungi tim siaga 24 jam.</p>
            </div>

            <div class="cockpit-status-chips">
                <div style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 10px; padding: 6px 14px; color: white; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                    <span class="dot" style="background: #22c55e;"></span>
                    <span>Saluran Darurat Siaga 24 Jam</span>
                </div>
            </div>
        </div>

        <div style="padding: 24px;">
            <!-- Trigger SOS Cepat -->
            <div style="background: #fef2f2; border: 2px dashed #f87171; border-radius: 16px; padding: 28px; text-align: center; margin-bottom: 28px;">
                <div style="font-size: 48px; margin-bottom: 8px; animation: pulse 1.5s infinite;">🚨</div>
                <h3 style="font-size: 22px; font-weight: 800; color: #991b1b; margin: 0 0 8px 0;">TOMBOL SIRENE SOS DARURAT</h3>
                <p style="font-size: 14px; color: #7f1d1d; max-width: 600px; margin: 0 auto 20px auto;">
                    Menekan tombol ini akan langsung mengirimkan sinyal SOS, koordinat GPS armada saat ini, dan membunyikan alarm di dashboard koordinator operasional serta dispatcher pusat.
                </p>

                <form method="POST" action="{{ route('dashboard.driver.schedule.sos', $activeSchedule->id ?? 1) }}" onsubmit="return confirm('PERINGATAN DARURAT: Apakah Anda yakin ingin memicu sinyal SOS Darurat ke seluruh tim operasional?');">
                    @csrf
                    <input type="hidden" name="latitude" id="emergencyPageLat" value="">
                    <input type="hidden" name="longitude" id="emergencyPageLng" value="">
                    <button type="submit" style="background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); color: white; border: none; padding: 16px 36px; border-radius: 12px; font-size: 16px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);">
                        <span>🚨</span>
                        <span>PICU SINYAL SOS DARURAT SEKARANG</span>
                    </button>
                </form>
            </div>

            <!-- Kontak Darurat 24 Jam -->
            <h4 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0;">Kontak Cepat Panggilan Bantuan Lapangan (24 Jam)</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 28px;">
                <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="font-size: 24px;">🏢</span>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">Pusat Kendali Operasional (OCC)</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Dispatcher 24/7</div>
                        </div>
                    </div>
                    <a href="tel:081122334455" style="display: block; text-align: center; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 10px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 13.5px;">
                        📞 Hubungi: 0811-2233-4455
                    </a>
                </div>

                <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="font-size: 24px;">🔧</span>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">Mekanik Siaga & Derek Tol</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Bantuan Teknis Lapangan</div>
                        </div>
                    </div>
                    <a href="tel:081288990011" style="display: block; text-align: center; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 10px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 13.5px;">
                        📞 Hubungi: 0812-8899-0011
                    </a>
                </div>

                <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="font-size: 24px;">👮</span>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">Call Center Tol Cipularang & Polisi</strong>
                            <div style="font-size: 11.5px; color: #64748b;">PJR & Jasamarga Traffic</div>
                        </div>
                    </div>
                    <a href="tel:14080" style="display: block; text-align: center; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 10px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 13.5px;">
                        📞 Hubungi: 14080 (Jasamarga)
                    </a>
                </div>
            </div>

            <!-- Prosedur Standar Keselamatan (SOP) -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 0 0 10px 0;">🛡️ Prosedur Penanganan Darurat bagi Pengemudi:</h4>
                <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #475569; line-height: 1.6;">
                    <li>Segera tepikan armada ke bahu jalan yang aman, nyalakan lampu hazard, dan pasang segitiga pengaman ± 30 meter di belakang unit.</li>
                    <li>Pastikan seluruh penumpang tetap tenang dan berada di dalam kabin atau tempat aman di balik pembatas jalan.</li>
                    <li>Picu tombol SOS atau telepon pusat kendali untuk konfirmasi koordinat tepat dan unit armada pengganti.</li>
                    <li>Jangan tinggalkan armada dan penumpang tanpa pengawasan hingga tim bantuan resmi tiba di lokasi.</li>
                </ol>
            </div>
        </div>
    </section>
@endsection
