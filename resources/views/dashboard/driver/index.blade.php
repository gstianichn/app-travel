@extends('layouts.driver')

@section('title', 'Cockpit Driver Dashboard - Operasional Travel Minibus')

@section('content')
    {{-- 1. FITUR DASHBOARD (KARTU KENDALI UTAMA OPERASIONAL) --}}
    @include('dashboard.driver.partials.dashboard')

    {{-- RINGKASAN ARMADA & RITME WAKTU OPERASIONAL --}}
    <section class="cockpit-master-card" style="margin-top: 24px;">
        <div style="padding: 20px 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; background: white; border-radius: 16px;">
            <!-- Info Armada yang Ditugaskan -->
            <div style="border-left: 4px solid #087f55; padding-left: 16px;">
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    Kendaraan yang Ditugaskan
                </div>
                <h4 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                    {{ $activeSchedule->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif' }}
                </h4>
                <div style="display: flex; flex-direction: column; gap: 4px; font-size: 12.5px; color: #334155;">
                    <div>Nomor Lambung Armada: <strong>{{ $activeSchedule->vehicle?->hullNumber() ?? 'HCE-07' }}</strong> (Pelat: <strong>{{ $activeSchedule->vehicle?->plate_number ?? 'D 7812 AB' }}</strong>)</div>
                    <div>Lokasi Fisik Parkir: <strong>{{ $activeSchedule->vehicle?->parkingLocation() ?? 'Bay 3 - Garasi Barat Pool Dipatiukur' }}</strong></div>
                </div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('dashboard.driver.kendaraan') }}" style="font-size: 12px; font-weight: 700; color: #087f55; text-decoration: none;">
                        Lihat Detail Spesifikasi & Dokumen Kendaraan</a>
                </div>
            </div>

            <!-- Info Ritme & Waktu Keberangkatan -->
            <div style="border-left: 4px solid #0284c7; padding-left: 16px;">
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    Panduan Ritme Waktu & Jam Keberangkatan
                </div>
                <h4 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                    Jam Keberangkatan: {{ $activeSchedule->formattedDepartureTime() }}
                </h4>
                <div style="display: flex; flex-direction: column; gap: 4px; font-size: 12.5px; color: #334155;">
                    <div>Estimasi Waktu Tiba: <strong>{{ $activeSchedule->estimatedArrivalTime() }}</strong> (Durasi: ± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }})</div>
                    <div>Tahapan Operasional: <span class="badge-pill" style="background:#ecfdf5; color:#065f46; font-weight:800; padding:2px 8px;">Persiapan Armada</span></div>
                </div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('dashboard.driver.jadwal') }}" style="font-size: 12px; font-weight: 700; color: #0284c7; text-decoration: none;">
                        Lihat Seluruh Kalender & Agenda Penugasan</a>
                </div>
            </div>
        </div>
    </section>

    {{-- PINTASAN LENGKAP KE SETIAP HALAMAN FITUR OPERASIONAL (PER FITUR PER HALAMAN) --}}
    <section class="cockpit-master-card" style="margin-top: 24px;">
        <div class="cockpit-hero-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="cockpit-title-group">
                <div class="cockpit-tag-pill" style="background: rgba(255,255,255,0.15); color: #f8fafc; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/></svg>
                    <span>NAVIGASI MODUL OPERASIONAL</span>
                </div>
                <h2>Akses Seluruh Fitur Pengemudi Per-Halaman</h2>
                <p>Setiap fitur kini telah memiliki halaman khusus tersendiri tanpa menumpuk di dashboard utama.</p>
            </div>
        </div>

        <div style="padding: 24px; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
            <!-- 2. Melihat Jadwal Perjalanan -->
            <a href="{{ route('dashboard.driver.jadwal') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Jadwal Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Kalender penugasan hari ini, besok, & jadwal mendatang.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Halaman Jadwal ke </div>
            </a>

            <!-- 3. Melihat Kendaraan yang Ditugaskan -->
            <a href="{{ route('dashboard.driver.kendaraan') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="3" stroke-width="2"/><circle cx="7" cy="18" r="2" fill="currentColor"/><circle cx="17" cy="18" r="2" fill="currentColor"/><path stroke-linecap="round" stroke-width="2" d="M2 11h20M14 6v5M8 6v5"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Kendaraan Ditugaskan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Spesifikasi unit HiAce, pelat, STNK, KIR, & lokasi parkir.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Info Kendaraan ke </div>
            </a>

            <!-- 4. Melihat Daftar Penumpang -->
            <a href="{{ route('dashboard.driver.penumpang') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Daftar Penumpang</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Manifes absensi, status naik/belum, dan tagihan COD.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Manifes Penumpang ke </div>
            </a>

            <!-- 5. Melihat Seat Map -->
            <a href="{{ route('dashboard.driver.seatmap') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h14a1 1 0 011 1v9H4V5a1 1 0 011-1zM4 14v4a2 2 0 002 2h12a2 2 0 002-2v-4M7 20v2M17 20v2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Seat Map Kabin</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Denah tata letak kursi & status keterisian per nomor bangku.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Denah Kursi ke </div>
            </a>

            <!-- 6. Melihat Rute Perjalanan -->
            <a href="{{ route('dashboard.driver.rute') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="18" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Rute Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Jalur rute asal-tujuan, waypoints, dan navigasi GPS.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Rute & Waypoints ke </div>
            </a>

            <!-- 7. Melihat Status Perjalanan -->
            <a href="{{ route('dashboard.driver.status') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Status Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Stepper alur tahapan perjalanan aktif dari awal s/d akhir.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Stepper Status ke </div>
            </a>

            <!-- 8. Mengubah Status Perjalanan -->
            <a href="{{ route('dashboard.driver.ubah-status') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Ubah Status Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Tombol eksekusi aksi berikutnya & slider konfirmasi status.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Kontrol Status ke </div>
            </a>

            <!-- 9. Melihat Informasi Penumpang -->
            <a href="{{ route('dashboard.driver.info-penumpang') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Informasi Penumpang</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Kontak WhatsApp, telepon, titik jemput, & catatan khusus.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Info Penumpang ke </div>
            </a>

            <!-- 10. Melaporkan Masalah Perjalanan -->
            <a href="{{ route('dashboard.driver.lapor-masalah') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fef3c7; color: #b45309; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #b45309;">Melaporkan Masalah</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Form pelaporan insiden, kendala teknis, ban, atau macet.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #b45309;">Buka Form Kendala ke </div>
            </a>

            <!-- 11. Emergency Center -->
            <a href="{{ route('dashboard.driver.emergency') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fee2e2; color: #dc2626; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v4M12 16h.01"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #dc2626;">Emergency Center</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Tombol SOS 1-klik & panggilan bantuan darurat lapangan.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #dc2626;">Buka Pusat Darurat ke </div>
            </a>

            <!-- 12. GPS Tracking Saat Perjalanan Aktif -->
            <a href="{{ route('dashboard.driver.gps') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">GPS Live Tracking</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Pantau sinyal GPS, koordinat terkini, & kirim lokasi.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Pelacakan GPS ke </div>
            </a>

            <!-- 13. Melihat Performa -->
            <a href="{{ route('dashboard.driver.performa') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Melihat Performa</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Rating bintang, persentase tepat waktu, & ulasan penumpang.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Data Performa ke </div>
            </a>

            <!-- 14. Melihat Reward -->
            <a href="{{ route('dashboard.driver.reward') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="14" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v14M3 13h18M8 8V5a2 2 0 012-2 2 2 0 012 3v2M16 8V5a2 2 0 00-2-2 2 2 0 00-2 3v2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Melihat Reward</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Poin ritase, bonus bulanan, dan level pengemudi.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Halaman Reward ke </div>
            </a>

            <!-- 15. Melihat Riwayat Perjalanan -->
            <a href="{{ route('dashboard.driver.riwayat') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #e3f5ee; color: #087f55; margin-bottom: 12px;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
                </div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Riwayat Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Rekapitulasi ritase dan daftar perjalanan yang selesai.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Arsip Riwayat ke </div>
            </a>
        </div>
    </section>
@endsection