@extends('layouts.admin')

@section('title', 'Tambah Unit Armada Baru')

@section('page-title', 'Tambah Unit Armada')

@section('page-subtitle', 'Daftarkan unit minibus baru, lengkapi identitas kendaraan, dokumen uji KIR, dan jatuh tempo pajak STNK.')

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
        font-size: 15px;
        font-weight: 700;
        color: #17221d;
    }

    .section-header-text p {
        margin: 0;
        font-size: 12px;
        color: #7b8882;
    }

    /* FORM GRID & INPUTS */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
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
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 10px 14px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        font-size: 13px;
        color: #17221d;
        background: #fbfdfc;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.12);
        background: white;
    }

    .form-input.is-invalid,
    .form-select.is-invalid,
    .form-textarea.is-invalid {
        border-color: #dc2626;
        background: #fffcfc;
    }

    .invalid-feedback {
        font-size: 11px;
        color: #dc2626;
        margin-top: 2px;
    }

    .form-hint {
        font-size: 11px;
        color: #7b8882;
        margin-top: 2px;
    }

    /* CHECKBOX TILES FOR FACILITIES */
    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .facility-tile {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid #d4dfda;
        border-radius: 9px;
        background: #fbfdfc;
        font-size: 12px;
        font-weight: 500;
        color: #2e3e37;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .facility-tile:hover {
        border-color: #087f55;
        background: #f3f9f6;
    }

    .facility-tile input[type="checkbox"] {
        accent-color: #087f55;
        width: 15px;
        height: 15px;
        cursor: pointer;
    }

    /* FOOTER ACTIONS */
    .form-card-footer {
        padding: 18px 26px;
        background: #f9fbf9;
        border-top: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 10px 18px;
        border: 1px solid #d4dfda;
        border-radius: 10px;
        background: white;
        color: #52635c;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: #f1f5f3;
    }

    .btn-submit {
        padding: 10px 22px;
        border: none;
        border-radius: 10px;
        background: #087f55;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(8, 127, 85, 0.2);
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        background: #066b47;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(8, 127, 85, 0.28);
    }

    @media (max-width: 768px) {
        .grid-2, .grid-3, .facilities-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-page">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
        <span>&rsaquo;</span>
        <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}">Memantau Kendaraan</a>
        <span>&rsaquo;</span>
        <span>Tambah Unit Armada</span>
    </div>

    {{-- MAIN FORM --}}
    <div class="form-card">
        <div class="form-card-header">
            <div>
                <h1>Formulir Tambah Unit Armada</h1>
                <p>Silakan isi informasi identitas armada, legalitas kelaikan jalan, dan fasilitas minibus.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.admin.memantau-kendaraan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-body">

                {{-- =========================================
                     1. IDENTITAS UNIT MINIBUS
                ========================================== --}}
                <div class="section-header">
                    <div class="section-header-icon">🚐</div>
                    <div class="section-header-text">
                        <h3>1. Identitas Unit Minibus</h3>
                        <p>Nomor polisi, tipe kendaraan travel, nama armada, dan kapasitas penumpang</p>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- NOMOR POLISI --}}
                    <div class="form-group">
                        <label class="form-label" for="plate_number">
                            Nomor Polisi (Pelat Nomor) <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="plate_number"
                            id="plate_number"
                            class="form-input @error('plate_number') is-invalid @enderror"
                            placeholder="Contoh: B 1234 TAA / D 7890 XYZ"
                            value="{{ old('plate_number') }}"
                            required
                            style="text-transform: uppercase; font-family: monospace; font-weight: 700; letter-spacing: 0.5px;"
                        >
                        @error('plate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-hint">Format huruf kapital sesuai STNK resmi kendaraan.</div>
                        @enderror
                    </div>

                    {{-- TIPE MINIBUS --}}
                    <div class="form-group">
                        <label class="form-label" for="type">
                            Tipe Minibus <span class="required">*</span>
                        </label>
                        <select
                            name="type"
                            id="type"
                            class="form-select @error('type') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Pilih Tipe Minibus --</option>
                            @foreach($types as $t)
                                <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NAMA PANGGILAN / UNIT --}}
                    <div class="form-group">
                        <label class="form-label" for="name">
                            Nama Unit / Panggilan Armada <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-input @error('name') is-invalid @enderror"
                            placeholder="Contoh: HiAce Eksekutif 01 / Elf Pariwisata 03"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KAPASITAS TOTAL KURSI --}}
                    <div class="form-group">
                        <label class="form-label" for="capacity">
                            Kapasitas Total Kursi <span class="required">*</span>
                        </label>
                        <input
                            type="number"
                            name="capacity"
                            id="capacity"
                            class="form-input @error('capacity') is-invalid @enderror"
                            placeholder="Contoh: 10 / 14 / 19"
                            min="4"
                            max="60"
                            value="{{ old('capacity', 10) }}"
                            required
                        >
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-hint">Jumlah kursi penumpang yang tersedia untuk pemesanan tiket.</div>
                        @enderror
                    </div>
                </div>

                <div class="grid-3">
                    {{-- TAHUN PEMBUATAN --}}
                    <div class="form-group">
                        <label class="form-label" for="manufacture_year">Tahun Pembuatan Unit</label>
                        <input
                            type="number"
                            name="manufacture_year"
                            id="manufacture_year"
                            class="form-input @error('manufacture_year') is-invalid @enderror"
                            placeholder="Contoh: 2023"
                            min="1990"
                            max="{{ date('Y') + 1 }}"
                            value="{{ old('manufacture_year', date('Y')) }}"
                        >
                        @error('manufacture_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NOMOR RANGKA --}}
                    <div class="form-group">
                        <label class="form-label" for="chassis_number">Nomor Rangka (VIN)</label>
                        <input
                            type="text"
                            name="chassis_number"
                            id="chassis_number"
                            class="form-input @error('chassis_number') is-invalid @enderror"
                            placeholder="Contoh: MHF21..."
                            value="{{ old('chassis_number') }}"
                            style="text-transform: uppercase;"
                        >
                        @error('chassis_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NOMOR MESIN --}}
                    <div class="form-group">
                        <label class="form-label" for="engine_number">Nomor Mesin</label>
                        <input
                            type="text"
                            name="engine_number"
                            id="engine_number"
                            class="form-input @error('engine_number') is-invalid @enderror"
                            placeholder="Contoh: 1GD-FTV..."
                            value="{{ old('engine_number') }}"
                            style="text-transform: uppercase;"
                        >
                        @error('engine_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- =========================================
                     2. LEGALITAS & KELAIKAN ARMADA
                ========================================== --}}
                <div class="section-header">
                    <div class="section-header-icon">📑</div>
                    <div class="section-header-text">
                        <h3>2. Legalitas & Kelaikan Jalan</h3>
                        <p>Batas waktu uji kelayakan berkala KIR Dishub dan jatuh tempo perpanjangan pajak STNK</p>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- JATUH TEMPO PAJAK STNK --}}
                    <div class="form-group">
                        <label class="form-label" for="stnk_tax_due_date">
                            Tanggal Jatuh Tempo Pajak STNK
                        </label>
                        <input
                            type="date"
                            name="stnk_tax_due_date"
                            id="stnk_tax_due_date"
                            class="form-input @error('stnk_tax_due_date') is-invalid @enderror"
                            value="{{ old('stnk_tax_due_date') }}"
                        >
                        @error('stnk_tax_due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-hint">Sistem akan memberi peringatan jika sisa &le; 30 hari.</div>
                        @enderror
                    </div>

                    {{-- MASA BERLAKU UJI KIR --}}
                    <div class="form-group">
                        <label class="form-label" for="kir_expiry_date">
                            Masa Berlaku Uji KIR Dishub
                        </label>
                        <input
                            type="date"
                            name="kir_expiry_date"
                            id="kir_expiry_date"
                            class="form-input @error('kir_expiry_date') is-invalid @enderror"
                            value="{{ old('kir_expiry_date') }}"
                        >
                        @error('kir_expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-hint">Batas waktu uji kelaikan teknis kendaraan travel.</div>
                        @enderror
                    </div>
                </div>

                {{-- =========================================
                     3. STATUS OPERASIONAL & FASILITAS
                ========================================== --}}
                <div class="section-header">
                    <div class="section-header-icon">⚙️</div>
                    <div class="section-header-text">
                        <h3>3. Status Unit & Fasilitas Minibus</h3>
                        <p>Kondisi kesiapan operasional saat ini dan kenyamanan pendukung bagi penumpang</p>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- STATUS UNIT --}}
                    <div class="form-group full">
                        <label class="form-label" for="status">
                            Status Awal Unit <span class="required">*</span>
                        </label>
                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="siap_jalan" {{ old('status', 'siap_jalan') === 'siap_jalan' ? 'selected' : '' }}>
                                🟢 Siap Jalan (Standby di Pool / Prima Siap Ditugaskan)
                            </option>
                            <option value="beroperasi" {{ old('status') === 'beroperasi' ? 'selected' : '' }}>
                                🔵 Sedang Beroperasi (Aktif di Perjalanan Membawa Penumpang)
                            </option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>
                                🔴 Masuk Bengkel (Servis Berkala / Perbaikan Mesin)
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- FASILITAS CHECKBOXES --}}
                <div class="form-group">
                    <label class="form-label">Fasilitas Kendaraan</label>
                    <div class="facilities-grid">
                        @php
                            $oldFacilities = old('facilities', ['AC Dingin', 'Reclining Seat (Kursi Rebah)', 'Port USB Charger']);
                        @endphp
                        @foreach($availableFacilities as $facility)
                            <label class="facility-tile">
                                <input
                                    type="checkbox"
                                    name="facilities[]"
                                    value="{{ $facility }}"
                                    {{ in_array($facility, $oldFacilities) ? 'checked' : '' }}
                                >
                                <span>{{ $facility }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- =========================================
                     4. FOTO & CATATAN PEMELIHARAAN
                ========================================== --}}
                <div class="section-header">
                    <div class="section-header-icon">📷</div>
                    <div class="section-header-text">
                        <h3>4. Foto & Catatan Unit</h3>
                        <p>Dokumentasi fisik kendaraan dan histori khusus atau catatan kondisi armada</p>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- FOTO UNIT --}}
                    <div class="form-group">
                        <label class="form-label" for="photo">Upload Foto Minibus</label>
                        <input
                            type="file"
                            name="photo"
                            id="photo"
                            class="form-input @error('photo') is-invalid @enderror"
                            accept="image/*"
                        >
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-hint">Format JPG, PNG, WEBP. Maksimal 2MB.</div>
                        @enderror
                    </div>

                    {{-- CATATAN PERAWATAN --}}
                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan Kondisi / Histori Armada</label>
                        <textarea
                            name="notes"
                            id="notes"
                            rows="3"
                            class="form-textarea @error('notes') is-invalid @enderror"
                            placeholder="Contoh: Unit baru servis berkala 20.000 KM di dealer resmi, ban depan baru diganti..."
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- FORM FOOTER ACTIONS --}}
            <div class="form-card-footer">
                <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="btn-cancel">
                    Batal
                </a>
                <button type="submit" class="btn-submit">
                    Simpan Unit Armada
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
