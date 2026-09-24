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
                        Lihat Detail Spesifikasi & Dokumen Kendaraan &rarr;
                    </a>
                </div>
            </div>

            <!-- Info Ritme & Waktu Keberangkatan -->
            <div style="border-left: 4px solid #0284c7; padding-left: 16px;">
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    Panduan Ritme Waktu & Jam Keberangkatan
                </div>
                <h4 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                    Jam Keberangkatan: {{ $activeSchedule->formattedDepartureTime() }} WIB
                </h4>
                <div style="display: flex; flex-direction: column; gap: 4px; font-size: 12.5px; color: #334155;">
                    <div>Estimasi Waktu Tiba: <strong>{{ $activeSchedule->estimatedArrivalTime() }} WIB</strong> (Durasi: ± {{ $activeSchedule->route?->duration ?? '2 Jam 45 Menit' }})</div>
                    <div>Tahapan Operasional: <span class="badge-pill" style="background:#ecfdf5; color:#065f46; font-weight:800; padding:2px 8px;">Persiapan Armada</span></div>
                </div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('dashboard.driver.jadwal') }}" style="font-size: 12px; font-weight: 700; color: #0284c7; text-decoration: none;">
                        Lihat Seluruh Kalender & Agenda Penugasan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- PINTASAN LENGKAP KE SETIAP HALAMAN FITUR OPERASIONAL (PER FITUR PER HALAMAN) --}}
    <section class="cockpit-master-card" style="margin-top: 24px;">
        <div class="cockpit-hero-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="cockpit-title-group">
                <div class="cockpit-tag-pill" style="background: rgba(255,255,255,0.15); color: #f8fafc;">
                    <span>🧭</span>
                    <span>NAVIGASI MODUL OPERASIONAL</span>
                </div>
                <h2>
                    <span>🚀</span>
                    <span>Akses Seluruh Fitur Pengemudi Per-Halaman</span>
                </h2>
                <p>Setiap fitur kini telah memiliki halaman khusus tersendiri tanpa menumpuk di dashboard utama.</p>
            </div>
        </div>

        <div style="padding: 24px; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
            <!-- 2. Melihat Jadwal Perjalanan -->
            <a href="{{ route('dashboard.driver.jadwal') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">📅</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Jadwal Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Kalender penugasan hari ini, besok, & jadwal mendatang.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Halaman Jadwal &rarr;</div>
            </a>

            <!-- 3. Melihat Kendaraan yang Ditugaskan -->
            <a href="{{ route('dashboard.driver.kendaraan') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🚐</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Kendaraan Ditugaskan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Spesifikasi unit HiAce, pelat, STNK, KIR, & lokasi parkir.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Info Kendaraan &rarr;</div>
            </a>

            <!-- 4. Melihat Daftar Penumpang -->
            <a href="{{ route('dashboard.driver.penumpang') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">👥</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Daftar Penumpang</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Manifes absensi, status naik/belum, dan tagihan COD.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Manifes Penumpang &rarr;</div>
            </a>

            <!-- 5. Melihat Seat Map -->
            <a href="{{ route('dashboard.driver.seatmap') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">💺</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Seat Map Kabin</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Denah tata letak kursi & status keterisian per nomor bangku.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Denah Kursi &rarr;</div>
            </a>

            <!-- 6. Melihat Rute Perjalanan -->
            <a href="{{ route('dashboard.driver.rute') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🗺️</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Rute Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Jalur rute asal-tujuan, waypoints, dan navigasi GPS.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Rute & Waypoints &rarr;</div>
            </a>

            <!-- 7. Melihat Status Perjalanan -->
            <a href="{{ route('dashboard.driver.status') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🧭</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Status Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Stepper alur tahapan perjalanan aktif dari awal s/d akhir.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Stepper Status &rarr;</div>
            </a>

            <!-- 8. Mengubah Status Perjalanan -->
            <a href="{{ route('dashboard.driver.ubah-status') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">⚡</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Ubah Status Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Tombol eksekusi aksi berikutnya & slider konfirmasi status.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Kontrol Status &rarr;</div>
            </a>

            <!-- 9. Melihat Informasi Penumpang -->
            <a href="{{ route('dashboard.driver.info-penumpang') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">👤</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Informasi Penumpang</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Kontak WhatsApp, telepon, titik jemput, & catatan khusus.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Info Penumpang &rarr;</div>
            </a>

            <!-- 10. Melaporkan Masalah Perjalanan -->
            <a href="{{ route('dashboard.driver.lapor-masalah') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">⚠️</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #b45309;">Melaporkan Masalah</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Form pelaporan insiden, kendala teknis, ban, atau macet.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #b45309;">Buka Form Kendala &rarr;</div>
            </a>

            <!-- 11. Emergency Center -->
            <a href="{{ route('dashboard.driver.emergency') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🚨</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #dc2626;">Emergency Center</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Tombol SOS 1-klik & panggilan bantuan darurat lapangan.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #dc2626;">Buka Pusat Darurat &rarr;</div>
            </a>

            <!-- 12. GPS Tracking Saat Perjalanan Aktif -->
            <a href="{{ route('dashboard.driver.gps') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">📍</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">GPS Live Tracking</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Pantau sinyal GPS, koordinat terkini, & kirim lokasi.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Pelacakan GPS &rarr;</div>
            </a>

            <!-- 13. Melihat Performa -->
            <a href="{{ route('dashboard.driver.performa') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">⭐</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Melihat Performa</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Rating bintang, persentase tepat waktu, & ulasan penumpang.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Data Performa &rarr;</div>
            </a>

            <!-- 14. Melihat Reward -->
            <a href="{{ route('dashboard.driver.reward') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🎁</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Melihat Reward</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Poin ritase, bonus bulanan, dan level pengemudi.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Halaman Reward &rarr;</div>
            </a>

            <!-- 15. Melihat Riwayat Perjalanan -->
            <a href="{{ route('dashboard.driver.riwayat') }}" class="cockpit-subcard" style="text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
                <div style="font-size: 26px; margin-bottom: 8px;">🕘</div>
                <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 800; color: #0f172a;">Riwayat Perjalanan</h4>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Rekapitulasi ritase dan daftar perjalanan yang selesai.</p>
                <div style="margin-top: 10px; font-size: 12px; font-weight: 700; color: #087f55;">Buka Arsip Riwayat &rarr;</div>
            </a>
        </div>
    </section>
@endsection