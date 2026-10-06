@extends('layouts.admin')

@section('title', 'Edit Driver - ' . ($driver->user?->name ?? 'Pengemudi'))

@section('page-title', 'Edit Driver')

@section('page-subtitle', 'Perbarui profil pengemudi, legalitas SIM & masa berlaku, kontak darurat, dan status kerja.')

@section('content')

<style>
    .form-page {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* BREADCRUMB */
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #7b8882;
    }

    .breadcrumb-nav a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }

    /* MAIN FORM CARD */
    .form-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .form-card-header {
        padding: 22px 25px;
        border-bottom: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .form-card-header h1 {
        margin: 0 0 4px;
        font-size: 18px;
        color: #17221d;
        font-weight: 700;
    }

    .form-card-header p {
        margin: 0;
        color: #89938e;
        font-size: 12px;
    }

    .form-body {
        padding: 26px;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* SECTION TITLE */
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2eae6;
        margin-bottom: 8px;
    }

    .section-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e3f5ee;
        color: #087f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .section-header-text h3 {
        margin: 0 0 2px;
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
    }

    .section-header-text p {
        margin: 0;
        font-size: 11px;
        color: #7b8882;
    }

    /* FORM GRID */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #34423b;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .required {
        color: #dc3545;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #dce5e1;
        border-radius: 9px;
        background: white;
        color: #26332d;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: 0.2s ease;
    }

    .form-textarea {
        resize: vertical;
        min-height: 70px;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.08);
    }

    .form-help {
        color: #98a39e;
        font-size: 11px;
    }

    .field-error {
        color: #d33b49;
        font-size: 11px;
        font-weight: 500;
    }

    /* CURRENT PHOTO PREVIEW */
    .photo-preview-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 14px;
        background: #fafcfb;
        border: 1px solid #e1ebe6;
        border-radius: 10px;
    }

    .current-photo-thumb {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #cce8dc;
    }

    /* STATUS SELECT CARDS */
    .status-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .status-select-card {
        border: 2px solid #e5ebe8;
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        background: white;
        transition: all 0.2s ease;
    }

    .status-select-card:hover {
        border-color: #cbd5e1;
    }

    .status-select-card.active-standby {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .status-select-card.active-onduty {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .status-select-card.active-onleave {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .status-card-radio {
        margin-top: 2px;
        accent-color: #087f55;
    }

    .status-card-info h4 {
        margin: 0 0 2px;
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
    }

    .status-card-info p {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        line-height: 1.3;
    }

    /* FORM ACTIONS */
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid #edf1ef;
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        padding: 11px 18px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        color: #607068;
        background: #f3f6f4;
        border: 1px solid #dce5e1;
        transition: 0.15s ease;
    }

    .btn-cancel:hover {
        background: #e5ece8;
        color: #1a2721;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 11px 22px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        background: #087f55;
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(8, 127, 85, 0.22);
        transition: all 0.2s ease;
    }

    .btn-save:hover {
        background: #066b47;
        box-shadow: 0 6px 18px rgba(8, 127, 85, 0.30);
    }

    @media (max-width: 768px) {
        .form-grid,
        .status-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-page">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('dashboard.admin.kelola-driver.index') }}">Kelola Driver</a>
        <span>/</span>
        <span>Edit: {{ $driver->user?->name ?? 'Driver #' . $driver->id }}</span>
    </div>

    {{-- MAIN FORM CARD --}}
    <div class="form-card">

        <div class="form-card-header">
            <div>
                <h1>Perbarui Data & Legalitas Pengemudi</h1>
                <p>Ubah informasi pengemudi, perpanjangan lisensi SIM, kontak darurat, atau status kerja.</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('dashboard.admin.kelola-driver.show', $driver->id) }}" class="btn-cancel" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Detail</span>
                </a>
                <a href="{{ route('dashboard.admin.kelola-driver.index') }}" class="btn-cancel">
                    &larr; Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('dashboard.admin.kelola-driver.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-body">

                {{-- =========================================
                     BAGIAN 1: PROFIL & AKUN PENGEMUDI
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-icon" style="display:inline-flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="section-header-text">
                            <h3>1. Profil & Akun Pengemudi</h3>
                            <p>Data pribadi, nomor WhatsApp pengemudi, dan akun login aplikasi.</p>
                        </div>
                    </div>

                    <div class="form-grid">

                        {{-- NAMA LENGKAP --}}
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Nama Lengkap Pengemudi <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-input @error('name') is-invalid @enderror"
                                placeholder="Contoh: Joko Widodo"
                                value="{{ old('name', $driver->user?->name) }}"
                                required
                            >
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- EMAIL LOGIN --}}
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Alamat Email Login <span class="required">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input @error('email') is-invalid @enderror"
                                placeholder="Contoh: joko.driver@travelku.id"
                                value="{{ old('email', $driver->user?->email) }}"
                                required
                            >
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NOMOR WHATSAPP PENGEMUDI --}}
                        <div class="form-group">
                            <label class="form-label" for="phone">
                                Nomor WhatsApp Pengemudi <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-input @error('phone') is-invalid @enderror"
                                placeholder="Contoh: 081234567890"
                                value="{{ old('phone', $driver->phone ?? $driver->user?->phone) }}"
                                required
                            >
                            <span class="form-help">Digunakan untuk koordinasi rute & penjemputan penumpang.</span>
                            @error('phone')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- KATA SANDI LOGIN (OPSIONAL) --}}
                        <div class="form-group">
                            <label class="form-label" for="password">
                                Kata Sandi Baru (Opsional)
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak ingin mengubah"
                            >
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- FOTO PENGEMUDI --}}
                        <div class="form-group full">
                            <label class="form-label" for="photo">
                                Foto Profil Pengemudi
                            </label>
                            @if($driver->photo)
                                <div class="photo-preview-wrap" style="margin-bottom: 8px;">
                                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->user?->name }}" class="current-photo-thumb">
                                    <div>
                                        <div style="font-size: 12px; font-weight: 600; color: #1f2937;">Foto Saat Ini</div>
                                        <div style="font-size: 11px; color: #6b7280;">Pilih file baru di bawah jika ingin mengganti foto.</div>
                                    </div>
                                </div>
                            @endif
                            <input
                                type="file"
                                id="photo"
                                name="photo"
                                class="form-input @error('photo') is-invalid @enderror"
                                accept="image/png, image/jpeg, image/jpg, image/webp"
                            >
                            <span class="form-help">Format file: JPG, PNG, atau WEBP. Maksimal 2MB.</span>
                            @error('photo')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- ALAMAT DOMISILI --}}
                        <div class="form-group full">
                            <label class="form-label" for="address">
                                Alamat Domisili Pengemudi
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                class="form-textarea @error('address') is-invalid @enderror"
                                placeholder="Alamat lengkap tempat tinggal pengemudi saat ini..."
                            >{{ old('address', $driver->address) }}</textarea>
                            @error('address')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- =========================================
                     BAGIAN 2: LEGALITAS & LISENSI SIM
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-icon" style="display:inline-flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3" stroke-width="2"/><circle cx="9" cy="10" r="2" stroke-width="2"/><line x1="15" y1="9" x2="18" y2="9" stroke-width="2"/><line x1="15" y1="13" x2="18" y2="13" stroke-width="2"/><line x1="6" y1="16" x2="18" y2="16" stroke-width="2"/></svg>
                        </div>
                        <div class="section-header-text">
                            <h3>2. Legalitas & Lisensi SIM</h3>
                            <p>Nomor SIM, jenis golongan SIM, dan masa berlaku untuk verifikasi kelaikan jalan.</p>
                        </div>
                    </div>

                    <div class="form-grid">

                        {{-- NOMOR SIM --}}
                        <div class="form-group">
                            <label class="form-label" for="license_number">
                                Nomor Surat Izin Mengemudi (SIM) <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="license_number"
                                name="license_number"
                                class="form-input @error('license_number') is-invalid @enderror"
                                placeholder="Contoh: 9812-1234-5678"
                                value="{{ old('license_number', $driver->license_number) }}"
                                required
                            >
                            @error('license_number')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- JENIS SIM --}}
                        <div class="form-group">
                            <label class="form-label" for="license_type">
                                Golongan / Jenis SIM <span class="required">*</span>
                            </label>
                            @php
                                $currentLicType = old('license_type', $driver->license_type);
                            @endphp
                            <select id="license_type" name="license_type" class="form-select @error('license_type') is-invalid @enderror" required>
                                <option value="">Pilih Jenis SIM...</option>
                                <option value="SIM B1 Umum" {{ $currentLicType === 'SIM B1 Umum' ? 'selected' : '' }}>SIM B1 Umum (Minibus / Van Komersial)</option>
                                <option value="SIM B2 Umum" {{ $currentLicType === 'SIM B2 Umum' ? 'selected' : '' }}>SIM B2 Umum (Bus Besar / Kendaraan Berat)</option>
                                <option value="SIM A Umum" {{ $currentLicType === 'SIM A Umum' ? 'selected' : '' }}>SIM A Umum (Angkutan Penumpang)</option>
                                <option value="SIM B1" {{ $currentLicType === 'SIM B1' ? 'selected' : '' }}>SIM B1 Standar</option>
                                <option value="SIM A" {{ $currentLicType === 'SIM A' ? 'selected' : '' }}>SIM A Standar</option>
                            </select>
                            @error('license_type')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- MASA BERLAKU SIM --}}
                        <div class="form-group">
                            <label class="form-label" for="license_expired_at">
                                Masa Berlaku SIM Hingga <span class="required">*</span>
                            </label>
                            <input
                                type="date"
                                id="license_expired_at"
                                name="license_expired_at"
                                class="form-input @error('license_expired_at') is-invalid @enderror"
                                value="{{ old('license_expired_at', $driver->license_expired_at ? $driver->license_expired_at->format('Y-m-d') : '') }}"
                                required
                            >
                            <span class="form-help">Sistem akan memberi pengingat otomatis jika SIM mendekati kedaluwarsa.</span>
                            @error('license_expired_at')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- =========================================
                     BAGIAN 3: KONTAK DARURAT KELUARGA
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-icon" style="display:inline-flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="section-header-text">
                            <h3>3. Kontak Darurat Keluarga</h3>
                            <p>Data kontak keluarga terdekat yang wajib dihubungi saat situasi darurat di perjalanan.</p>
                        </div>
                    </div>

                    <div class="form-grid">

                        {{-- NAMA KONTAK DARURAT --}}
                        <div class="form-group">
                            <label class="form-label" for="emergency_contact_name">
                                Nama Kontak Darurat <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="emergency_contact_name"
                                name="emergency_contact_name"
                                class="form-input @error('emergency_contact_name') is-invalid @enderror"
                                placeholder="Contoh: Siti Aminah"
                                value="{{ old('emergency_contact_name', $driver->emergency_contact_name) }}"
                                required
                            >
                            @error('emergency_contact_name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- HUBUNGAN KELUARGA --}}
                        <div class="form-group">
                            <label class="form-label" for="emergency_contact_relation">
                                Hubungan Keluarga <span class="required">*</span>
                            </label>
                            @php
                                $currentRel = old('emergency_contact_relation', $driver->emergency_contact_relation);
                            @endphp
                            <select id="emergency_contact_relation" name="emergency_contact_relation" class="form-select @error('emergency_contact_relation') is-invalid @enderror" required>
                                <option value="">Pilih Hubungan...</option>
                                <option value="Istri" {{ $currentRel === 'Istri' ? 'selected' : '' }}>Istri</option>
                                <option value="Suami" {{ $currentRel === 'Suami' ? 'selected' : '' }}>Suami</option>
                                <option value="Orang Tua (Ayah/Ibu)" {{ $currentRel === 'Orang Tua (Ayah/Ibu)' ? 'selected' : '' }}>Orang Tua (Ayah/Ibu)</option>
                                <option value="Anak Kandung" {{ $currentRel === 'Anak Kandung' ? 'selected' : '' }}>Anak Kandung</option>
                                <option value="Saudara Kandung (Kakak/Adik)" {{ $currentRel === 'Saudara Kandung (Kakak/Adik)' ? 'selected' : '' }}>Saudara Kandung</option>
                                <option value="Kerabat Lainnya" {{ $currentRel === 'Kerabat Lainnya' ? 'selected' : '' }}>Kerabat Lainnya</option>
                            </select>
                            @error('emergency_contact_relation')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NOMOR TELEPON KONTAK DARURAT --}}
                        <div class="form-group">
                            <label class="form-label" for="emergency_contact_phone">
                                Nomor Telepon / WA Darurat <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="emergency_contact_phone"
                                name="emergency_contact_phone"
                                class="form-input @error('emergency_contact_phone') is-invalid @enderror"
                                placeholder="Contoh: 085298765432"
                                value="{{ old('emergency_contact_phone', $driver->emergency_contact_phone) }}"
                                required
                            >
                            @error('emergency_contact_phone')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- =========================================
                     BAGIAN 4: STATUS KERJA SAAT INI
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-icon" style="display:inline-flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/></svg>
                        </div>
                        <div class="section-header-text">
                            <h3>4. Status Kesiapan Kerja Pengemudi</h3>
                            <p>Perbarui status operasional kesiapan jalan driver.</p>
                        </div>
                    </div>

                    @php
                        $selectedStatus = old('status', $driver->status ?? 'standby');
                        if ($selectedStatus === 'Available') $selectedStatus = 'standby';
                    @endphp

                    <div class="status-cards-grid">
                        {{-- STANDBY --}}
                        <label class="status-select-card {{ $selectedStatus === 'standby' ? 'active-standby' : '' }}" onclick="selectStatus('standby', this)">
                            <input type="radio" name="status" value="standby" class="status-card-radio" {{ $selectedStatus === 'standby' ? 'checked' : '' }}>
                            <div class="status-card-info">
                                <h4>Siap Jalan (Standby)</h4>
                                <p>Pengemudi siap di pool/loket untuk ditugaskan jadwal keberangkatan armada.</p>
                            </div>
                        </label>

                        {{-- ON DUTY --}}
                        <label class="status-select-card {{ $selectedStatus === 'on_duty' ? 'active-onduty' : '' }}" onclick="selectStatus('on_duty', this)">
                            <input type="radio" name="status" value="on_duty" class="status-card-radio" {{ $selectedStatus === 'on_duty' ? 'checked' : '' }}>
                            <div class="status-card-info">
                                <h4>Sedang Bertugas</h4>
                                <p>Pengemudi sedang dalam perjalanan melayani rute travel aktif.</p>
                            </div>
                        </label>

                        {{-- ON LEAVE --}}
                        <label class="status-select-card {{ $selectedStatus === 'on_leave' ? 'active-onleave' : '' }}" onclick="selectStatus('on_leave', this)">
                            <input type="radio" name="status" value="on_leave" class="status-card-radio" {{ $selectedStatus === 'on_leave' ? 'checked' : '' }}>
                            <div class="status-card-info">
                                <h4>Sedang Libur / Cuti</h4>
                                <p>Pengemudi sedang mengambil jadwal libur atau cuti istirahat.</p>
                            </div>
                        </label>
                    </div>

                    @error('status')
                        <div class="field-error" style="margin-top: 8px;">{{ $message }}</div>
                    @enderror

                    {{-- CATATAN TAMBAHAN --}}
                    <div class="form-group full" style="margin-top: 14px;">
                        <label class="form-label" for="notes">
                            Catatan Khusus Pengemudi
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            class="form-textarea"
                            placeholder="Catatan rute favorit, pengalaman berkendara, atau rekam jejak pengemudi..."
                        >{{ old('notes', $driver->notes) }}</textarea>
                    </div>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="form-actions">
                    <a href="{{ route('dashboard.admin.kelola-driver.show', $driver->id) }}" class="btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="btn-save" style="display:inline-flex; align-items:center; gap:6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan Perubahan Driver</span>
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>

<script>
    function selectStatus(val, card) {
        document.querySelectorAll('.status-select-card').forEach(c => {
            c.classList.remove('active-standby', 'active-onduty', 'active-onleave');
        });
        const radio = card.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            if (val === 'standby') card.classList.add('active-standby');
            else if (val === 'on_duty') card.classList.add('active-onduty');
            else if (val === 'on_leave') card.classList.add('active-onleave');
        }
    }
</script>

@endsection
