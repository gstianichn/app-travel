@extends('layouts.admin')

@section('title', 'Edit Staf - ' . $staff->name)

@section('page-title', 'Edit Staf')

@section('page-subtitle', 'Perbarui data profil penempatan, wewenang hak akses operasional, atau status kepegawaian staf.')

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
    .form-select {
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

    .form-input:focus,
    .form-select:focus {
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

    /* =========================================
       STATUS SELECTION CARDS
    ========================================== */
    .status-options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .status-option-card {
        border: 2px solid #e5ebe8;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        background: white;
        transition: all 0.2s ease;
    }

    .status-option-card:hover {
        border-color: #cbd5e1;
    }

    .status-option-card.active-state {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .status-option-card.inactive-state {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .status-option-radio {
        margin-top: 3px;
        accent-color: #087f55;
    }

    .status-option-text h4 {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
    }

    .status-option-text p {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        line-height: 1.4;
    }

    /* =========================================
       PRESET BUTTONS
    ========================================== */
    .preset-toolbar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: #f8faf9;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #e5ebe8;
    }

    .preset-toolbar-label {
        font-size: 11px;
        font-weight: 700;
        color: #55665e;
        margin-right: 4px;
    }

    .btn-preset {
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #d1dbd6;
        background: white;
        color: #33443c;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-preset:hover {
        background: #e3f5ee;
        color: #087f55;
        border-color: #087f55;
    }

    .btn-preset.active-preset {
        background: #087f55;
        color: white;
        border-color: #087f55;
    }

    /* =========================================
       PERMISSION CARDS GRID
    ========================================== */
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .perm-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #e1ebe6;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .perm-card:hover {
        border-color: #087f55;
        background: #fafcfb;
    }

    .perm-card.restricted-card {
        border-color: #fecdd3;
        background: #fffafa;
    }

    .perm-card.restricted-card:hover {
        border-color: #e11d48;
    }

    .perm-checkbox {
        width: 18px;
        height: 18px;
        accent-color: #087f55;
        cursor: pointer;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .perm-card.restricted-card .perm-checkbox {
        accent-color: #e11d48;
    }

    .perm-info {
        flex: 1;
    }

    .perm-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }

    .perm-title {
        font-size: 13px;
        font-weight: 700;
        color: #1a2721;
    }

    .perm-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .perm-badge.standard {
        background: #e3f5ee;
        color: #087f55;
    }

    .perm-badge.restricted {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .perm-desc {
        font-size: 11px;
        color: #6c7c74;
        line-height: 1.4;
        margin: 0;
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
        .status-options-grid,
        .permissions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-page">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('dashboard.admin.kelola-staff.index') }}">Kelola Staff</a>
        <span>/</span>
        <span>Edit Staf: {{ $staff->name }}</span>
    </div>

    {{-- CARD FORM --}}
    <div class="form-card">

        <div class="form-card-header">
            <div>
                <h1>Perbarui Data & Wewenang Staf</h1>
                <p>Ubah profil penempatan, sesuaikan hak akses loket, atau perbarui status kepegawaian staf.</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('dashboard.admin.kelola-staff.show', $staff->id) }}" class="btn-cancel">
                    Lihat Detail
                </a>
                <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="btn-cancel">
                    ← Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('dashboard.admin.kelola-staff.update', $staff->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-body">

                {{-- =========================================
                     BAGIAN 1: PROFIL & PENEMPATAN CABANG
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-text">
                            <h3>1. Profil & Penempatan Staf</h3>
                            <p>Informasi data pribadi dan cabang/loket penugasan staf.</p>
                        </div>
                    </div>

                    <div class="form-grid">

                        {{-- NAMA LENGKAP --}}
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Nama Lengkap Staf <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-input @error('name') is-invalid @enderror"
                                placeholder="Contoh: Ahmad Rizky"
                                value="{{ old('name', $staff->name) }}"
                                required
                            >
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Alamat Email Login <span class="required">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input @error('email') is-invalid @enderror"
                                placeholder="Contoh: ahmad.loket@travelku.id"
                                value="{{ old('email', $staff->email) }}"
                                required
                            >
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NOMOR KONTAK / WHATSAPP --}}
                        <div class="form-group">
                            <label class="form-label" for="phone">
                                Nomor Kontak / WhatsApp <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-input @error('phone') is-invalid @enderror"
                                placeholder="Contoh: 081234567890"
                                value="{{ old('phone', $staff->phone) }}"
                                required
                            >
                            <span class="form-help">Digunakan untuk koordinasi operasional loket dan armada.</span>
                            @error('phone')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- PENEMPATAN CABANG / LOKET --}}
                        <div class="form-group">
                            <label class="form-label" for="branch">
                                Penempatan Cabang / Loket <span class="required">*</span>
                            </label>
                            <input
                                list="branch-list"
                                id="branch"
                                name="branch"
                                class="form-input @error('branch') is-invalid @enderror"
                                placeholder="Pilih atau ketik cabang/loket..."
                                value="{{ old('branch', $staff->branch) }}"
                                required
                            >
                            <datalist id="branch-list">
                                @foreach($availableBranches as $branch)
                                    <option value="{{ $branch }}">
                                @endforeach
                            </datalist>
                            <span class="form-help">Misal: Pool Jakarta (Pusat), Agen Bandung, Loket Gambir.</span>
                            @error('branch')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- POSISI / JABATAN --}}
                        <div class="form-group">
                            <label class="form-label" for="position">
                                Posisi / Jabatan
                            </label>
                            <input
                                type="text"
                                id="position"
                                name="position"
                                class="form-input @error('position') is-invalid @enderror"
                                placeholder="Contoh: Staf Loket & Kasir"
                                value="{{ old('position', $staff->position) }}"
                            >
                            @error('position')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NIK / KODE AKSES STAF --}}
                        <div class="form-group">
                            <label class="form-label" for="access_code">
                                Kode Akses / NIK Staf
                            </label>
                            <input
                                type="text"
                                id="access_code"
                                name="access_code"
                                class="form-input @error('access_code') is-invalid @enderror"
                                placeholder="Contoh: STF-BDO-001"
                                value="{{ old('access_code', $staff->access_code) }}"
                            >
                            @error('access_code')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- =========================================
                     BAGIAN 2: STATUS KEPEGAWAIAN (KONTROL AKUN)
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-text">
                            <h3>2. Status Kepegawaian & Akses Akun</h3>
                            <p>Aktifkan untuk staf yang bertugas, atau nonaktifkan akun staf yang telah berhenti bekerja.</p>
                        </div>
                    </div>

                    @php
                        $currentStatus = old('status', $staff->status ?? 'active');
                    @endphp

                    <div class="status-options-grid">
                        <label class="status-option-card {{ $currentStatus === 'active' ? 'active-state' : '' }}" onclick="selectStatusCard(this)">
                            <input
                                type="radio"
                                name="status"
                                value="active"
                                class="status-option-radio"
                                {{ $currentStatus === 'active' ? 'checked' : '' }}
                            >
                            <div class="status-option-text">
                                <h4>Aktif Bekerja</h4>
                                <p>Staf masih aktif bertugas. Akun dapat digunakan untuk login ke sistem loket dan memproses pesanan tiket.</p>
                            </div>
                        </label>

                        <label class="status-option-card {{ $currentStatus === 'inactive' ? 'inactive-state' : '' }}" onclick="selectStatusCard(this)">
                            <input
                                type="radio"
                                name="status"
                                value="inactive"
                                class="status-option-radio"
                                {{ $currentStatus === 'inactive' ? 'checked' : '' }}
                            >
                            <div class="status-option-text">
                                <h4>Berhenti Bekerja / Nonaktif</h4>
                                <p>Staf telah keluar atau dinonaktifkan. Akses login dan otorisasi operasional staf akan langsung diblokir.</p>
                            </div>
                        </label>
                    </div>

                    @error('status')
                        <div class="field-error" style="margin-top: 8px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- =========================================
                     BAGIAN 3: PENGATURAN HAK AKSES (ROLE & PERMISSION)
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-text">
                            <h3>3. Pengaturan Hak Akses (Role & Permission)</h3>
                            <p>Pemisahan wewenang operasional. Staf loket standar dibatasi dari persetujuan refund dan hapus data.</p>
                        </div>
                    </div>

                    {{-- PRESET TOOLBAR --}}
                    <div class="preset-toolbar" style="margin-bottom: 16px;">
                        <span class="preset-toolbar-label">Pilihan Cepat Wewenang:</span>
                        <button type="button" class="btn-preset" onclick="applyPreset('loket')">
                            Staf Loket Standar
                        </button>
                        <button type="button" class="btn-preset" onclick="applyPreset('supervisor')">
                            Supervisor Cabang / Pool
                        </button>
                        <button type="button" class="btn-preset" onclick="applyPreset('all')">
                            Pilih Semua
                        </button>
                        <button type="button" class="btn-preset" onclick="applyPreset('none')">
                            Kosongkan
                        </button>
                    </div>

                    {{-- PERMISSION CHECKBOXES --}}
                    <div class="permissions-grid">
                        @php
                            $userPermissions = old('permissions', $staff->permissions ?? []);
                        @endphp
                        @foreach($availablePermissions as $key => $perm)
                            @php
                                $isRestricted = $perm['restricted'] ?? false;
                                $isChecked = in_array($key, $userPermissions);
                            @endphp
                            <label class="perm-card {{ $isRestricted ? 'restricted-card' : '' }}" for="perm_{{ $key }}">
                                <input
                                    type="checkbox"
                                    id="perm_{{ $key }}"
                                    name="permissions[]"
                                    value="{{ $key }}"
                                    class="perm-checkbox perm-check-item {{ $isRestricted ? 'perm-restricted' : 'perm-standard' }}"
                                    {{ $isChecked ? 'checked' : '' }}
                                >
                                <div class="perm-info">
                                    <div class="perm-top">
                                        <span class="perm-title">{{ $perm['label'] }}</span>
                                        @if($isRestricted)
                                            <span class="perm-badge restricted">Khusus Admin</span>
                                        @else
                                            <span class="perm-badge standard">Standar Loket</span>
                                        @endif
                                    </div>
                                    <p class="perm-desc">{{ $perm['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('permissions')
                        <div class="field-error" style="margin-top: 10px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- =========================================
                     BAGIAN 4: UBAH KATA SANDI (OPSIONAL)
                ========================================== --}}
                <div>
                    <div class="section-header">
                        <div class="section-header-text">
                            <h3>4. Ubah Kata Sandi Akun (Opsional)</h3>
                            <p>Biarkan kedua kolom di bawah kosong jika tidak bermaksud mengubah kata sandi akun staf ini.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                Kata Sandi Baru
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak diubah (min 8 karakter)"
                            >
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">
                                Ulangi Kata Sandi Baru
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Ulangi kata sandi baru"
                            >
                        </div>
                    </div>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="form-actions">
                    <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="btn-save">
                        <span></span>
                        <span>Simpan Perubahan Staf</span>
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>

<script>
    function selectStatusCard(cardElement) {
        document.querySelectorAll('.status-option-card').forEach(c => {
            c.classList.remove('active-state', 'inactive-state');
        });
        const radio = cardElement.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            if (radio.value === 'active') {
                cardElement.classList.add('active-state');
            } else {
                cardElement.classList.add('inactive-state');
            }
        }
    }

    function applyPreset(type) {
        const checkboxes = document.querySelectorAll('.perm-check-item');
        
        // Reset all buttons visual state
        document.querySelectorAll('.btn-preset').forEach(btn => btn.classList.remove('active-preset'));
        if (event && event.target) {
            event.target.classList.add('active-preset');
        }

        checkboxes.forEach(cb => {
            const val = cb.value;
            if (type === 'loket') {
                cb.checked = ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket'].includes(val);
            } else if (type === 'supervisor') {
                cb.checked = ['create_booking', 'view_routes', 'view_passengers', 'checkin_ticket', 'view_reports'].includes(val);
            } else if (type === 'all') {
                cb.checked = true;
            } else if (type === 'none') {
                cb.checked = false;
            }
        });
    }
</script>

@endsection
