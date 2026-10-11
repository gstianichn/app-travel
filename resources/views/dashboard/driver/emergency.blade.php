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
                <h2 style="display: flex; align-items: center; gap: 8px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01"/></svg>
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
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 9999px; background: #fee2e2; color: #dc2626; margin-bottom: 12px;">
                    <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01"/></svg>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: #991b1b; margin: 0 0 8px 0;">TOMBOL SIRENE SOS DARURAT</h3>
                <p style="font-size: 14px; color: #7f1d1d; max-width: 600px; margin: 0 auto 20px auto;">
                    Menekan tombol ini akan langsung mengirimkan sinyal SOS, koordinat GPS armada saat ini, dan membunyikan alarm di dashboard koordinator operasional serta dispatcher pusat.
                </p>

                <form method="POST" action="{{ route('dashboard.driver.schedule.sos', $activeSchedule->id ?? 1) }}" onsubmit="return confirm('PERINGATAN DARURAT: Apakah Anda yakin ingin memicu sinyal SOS Darurat ke seluruh tim operasional?');">
                    @csrf
                    <input type="hidden" name="latitude" id="emergencyPageLat" value="">
                    <input type="hidden" name="longitude" id="emergencyPageLng" value="">
                    <button type="submit" style="background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); color: white; border: none; padding: 16px 36px; border-radius: 12px; font-size: 16px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>PICU SINYAL SOS DARURAT SEKARANG</span>
                    </button>
                </form>
            </div>

            <!-- Kontak Darurat 24 Jam -->
            <h4 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0;">Kontak bantuan di jalan</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 28px;">
                <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #e3f5ee; color: #087f55;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">Pusat Kendali Operasional (OCC)</strong>
                            <div style="font-size: 12px; color: #4B5D55;">Nomor kantor dari Pengaturan</div>
                        </div>
                    </div>
                    @php $officePhone = \App\Models\AppSetting::companyPhone(); @endphp
                    @if ($officePhone)
                        <a href="{{ $officePhone['tel'] }}" style="display: flex; align-items: center; justify-content: center; gap: 6px; background: #DFF0E8; color: #0C4535; border: 1px solid #0C7950; padding: 10px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 14px; min-height: 44px;">
                            <span>Hubungi: {{ $officePhone['display'] }}</span>
                        </a>
                    @else
                        <p style="font-size: 14px; color: #4B5D55; margin: 0;">Nomor kantor belum diisi. Admin utama bisa mengisinya di menu Pengaturan.</p>
                    @endif
                </div>

                <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #1d4ed8;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </span>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">Call center Jasa Marga</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Gangguan dan kecelakaan di jalan tol</div>
                        </div>
                    </div>
                    <a href="tel:14080" style="display: flex; align-items: center; justify-content: center; gap: 6px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 10px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 13.5px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Hubungi: 14080 (Jasamarga)</span>
                    </a>
                </div>
            </div>

            <!-- Prosedur Standar Keselamatan (SOP) -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                <h4 style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 0 0 10px 0; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Prosedur Penanganan Darurat bagi Pengemudi:</span>
                </h4>
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
