@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - Admin Utama')

@section('page-title', 'Pengaturan')

@section('page-subtitle', 'Konfigurasi profil perusahaan, kebijakan tiket, integrasi pembayaran, titik operasional, dan keamanan sistem.')

@section('content')

<style>
    .setting-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    .page-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #72817a;
        margin-bottom: 6px;
    }

    .breadcrumb-nav a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .header-text h1 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 800;
        color: #17221d;
    }

    .header-text p {
        margin: 0;
        font-size: 13px;
        color: #6a7972;
    }

    .alert-banner {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .alert-success { background: #e3f5ee; border-color: #a7dfc6; color: #066b47; }
    .alert-error   { background: #fff1f2; border-color: #fecdd3; color: #be123c; }

    /* TAB BAR */
    .setting-tabs {
        display: flex;
        gap: 4px;
        background: #ffffff;
        border-radius: 14px;
        padding: 6px;
        border: 1px solid #e2eae6;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .setting-tabs::-webkit-scrollbar { display: none; }

    .tab-btn {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        color: #6a7972;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        font-family: inherit;
    }

    .tab-btn:hover { background: #f0f8f5; color: #087f55; }

    .tab-btn.active {
        background: #087f55;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(8, 127, 85, 0.3);
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* CARD */
    .setting-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2eae6;
        overflow: hidden;
    }

    .setting-card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f0f5f2;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .setting-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #e3f5ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .setting-card-title { font-size: 16px; font-weight: 700; color: #17221d; margin: 0 0 2px; }
    .setting-card-subtitle { font-size: 12px; color: #72817a; margin: 0; }

    .setting-card-body { padding: 24px; }

    /* FORM */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
    .form-grid.cols-1 { grid-template-columns: 1fr; }
    .form-full { grid-column: 1 / -1; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #3d4f46;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .required-star { color: #dc3545; margin-left: 2px; }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #dce8e0;
        font-size: 14px;
        color: #17221d;
        background: #f8fdf9;
        transition: all 0.18s ease;
        outline: none;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #087f55;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(8, 127, 85, 0.1);
    }

    .form-input.is-invalid { border-color: #dc3545; background: #fff1f2; }
    .form-textarea { resize: vertical; min-height: 90px; }

    .form-input-icon { position: relative; }
    .form-input-icon .form-input { padding-left: 40px; }
    .form-input-icon .icon-prefix {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
    }

    .form-hint  { font-size: 11px; color: #8a9990; margin-top: 2px; }
    .form-error { font-size: 11px; color: #dc3545; margin-top: 2px; }

    /* LOGO UPLOAD */
    .logo-upload-wrap {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 16px;
        background: #f8fdf9;
        border-radius: 12px;
        border: 1.5px dashed #b0d9c4;
    }

    .logo-preview {
        min-width: 80px;
        max-width: 180px;
        height: 80px;
        border-radius: 12px;
        background: #e3f5ee;
        border: 2px solid #b0d9c4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        overflow: hidden;
        flex-shrink: 0;
        padding: 6px;
    }

    .logo-preview img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .logo-upload-info h4 { font-size: 14px; font-weight: 700; color: #17221d; margin: 0 0 4px; }
    .logo-upload-info p  { font-size: 12px; color: #8a9990; margin: 0 0 10px; }

    .btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #e3f5ee;
        color: #087f55;
        border: 1.5px solid #a7dfc6;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .btn-upload:hover { background: #087f55; color: #fff; border-color: #087f55; }

    /* FORM ACTIONS */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #f0f5f2;
        margin-top: 24px;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        background: #087f55;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 3px 10px rgba(8, 127, 85, 0.3);
        font-family: inherit;
    }

    .btn-save:hover { background: #066b47; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(8, 127, 85, 0.35); }
    .btn-save:active { transform: translateY(0); }

    /* TIER TABLE */
    .tier-table { width: 100%; border-radius: 10px; overflow: hidden; border: 1px solid #e2eae6; border-collapse: separate; border-spacing: 0; }
    .tier-table th { background: #f0f8f5; color: #3d4f46; font-size: 12px; font-weight: 700; padding: 10px 14px; text-align: left; border-bottom: 1px solid #e2eae6; }
    .tier-row td { padding: 10px 14px; border-bottom: 1px solid #f0f5f2; vertical-align: middle; }
    .tier-row:last-child td { border-bottom: none; }
    .tier-row .form-input { min-width: 80px; padding: 7px 10px; font-size: 13px; }

    /* GATEWAY */
    .gateway-provider-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .gateway-option { flex: 1; min-width: 130px; }
    .gateway-option input[type="radio"] { display: none; }
    .gateway-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 16px 20px;
        border-radius: 12px;
        border: 2px solid #dce8e0;
        background: #f8fdf9;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 13px;
        font-weight: 600;
        color: #6a7972;
        text-align: center;
    }

    .gateway-option label .gw-logo { font-size: 24px; }

    .gateway-option input[type="radio"]:checked + label {
        border-color: #087f55;
        background: #e3f5ee;
        color: #066b47;
        box-shadow: 0 3px 10px rgba(8, 127, 85, 0.18);
    }

    /* API KEYS */
    .api-key-field { position: relative; }
    .api-key-field .form-input { padding-right: 44px; font-family: 'Courier New', monospace; letter-spacing: 0.04em; font-size: 13px; }
    .api-key-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #8a9990;
        padding: 4px;
        transition: color 0.15s;
    }
    .api-key-toggle:hover { color: #087f55; }

    /* LOCATION TABLE */
    .location-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
    .location-table th { background: #f0f8f5; color: #3d4f46; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 1px solid #e2eae6; text-align: left; }
    .location-table td { padding: 14px 16px; border-bottom: 1px solid #f0f5f2; color: #17221d; vertical-align: top; }
    .location-table tr:last-child td { border-bottom: none; }
    .location-table tr:hover td { background: #fafefb; }
    .location-name { font-weight: 700; color: #17221d; }
    .location-phone { display: inline-flex; align-items: center; gap: 4px; color: #087f55; font-weight: 600; }
    .location-maps { display: inline-flex; align-items: center; gap: 4px; color: #087f55; font-size: 12px; text-decoration: none; font-weight: 600; }
    .location-maps:hover { text-decoration: underline; }

    .btn-add-loc {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #087f55;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .btn-add-loc:hover { background: #066b47; transform: translateY(-1px); }

    .btn-edit-loc, .btn-del-loc {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.15s ease;
    }

    .btn-edit-loc { background: #e3f5ee; color: #087f55; }
    .btn-edit-loc:hover { background: #087f55; color: #fff; }
    .btn-del-loc { background: #fff1f2; color: #dc3545; }
    .btn-del-loc:hover { background: #dc3545; color: #fff; }

    .empty-locations { text-align: center; padding: 40px 20px; color: #8a9990; }
    .empty-locations .empty-icon { font-size: 40px; margin-bottom: 8px; }

    /* MODAL */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalIn 0.2s ease;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 16px; border-bottom: 1px solid #f0f5f2; }
    .modal-title { font-size: 17px; font-weight: 800; color: #17221d; margin: 0; }
    .modal-close { background: #f0f5f2; border: none; border-radius: 8px; width: 32px; height: 32px; cursor: pointer; font-size: 18px; color: #6a7972; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
    .modal-close:hover { background: #e0ede6; color: #17221d; }
    .modal-body { padding: 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f5f2; display: flex; justify-content: flex-end; gap: 10px; }

    .btn-cancel { padding: 10px 20px; background: #f0f5f2; color: #6a7972; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; font-family: inherit; }
    .btn-cancel:hover { background: #e0ede6; color: #17221d; }

    /* SECURITY */
    .security-section { display: flex; flex-direction: column; gap: 24px; }

    .toggle-2fa {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
        background: #f8fdf9;
        border-radius: 12px;
        border: 1.5px solid #dce8e0;
    }

    .toggle-2fa-info h4 { font-size: 14px; font-weight: 700; color: #17221d; margin: 0 0 3px; }
    .toggle-2fa-info p  { font-size: 12px; color: #8a9990; margin: 0; }

    .toggle-switch { position: relative; width: 50px; height: 26px; flex-shrink: 0; }
    .toggle-switch input { display: none; }
    .toggle-slider { position: absolute; inset: 0; background: #dce8e0; border-radius: 99px; cursor: pointer; transition: all 0.25s ease; }
    .toggle-slider::before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: all 0.25s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.18); }
    .toggle-switch input:checked + .toggle-slider { background: #087f55; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(24px); }

    .section-divider { height: 1px; background: linear-gradient(to right, transparent, #e2eae6, transparent); margin: 8px 0; }

    /* SESSION TABLE */
    .session-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
    .session-table th { background: #f0f8f5; color: #3d4f46; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 14px; border-bottom: 1px solid #e2eae6; text-align: left; }
    .session-table td { padding: 12px 14px; border-bottom: 1px solid #f0f5f2; color: #17221d; vertical-align: middle; }
    .session-table tr:last-child td { border-bottom: none; }

    .session-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .session-badge.current { background: #e3f5ee; color: #066b47; }
    .session-badge.other   { background: #f0f5f2; color: #6a7972; }

    @media (max-width: 768px) {
        .form-grid, .form-grid.cols-3 { grid-template-columns: 1fr; }
        .gateway-provider-row { flex-direction: column; }
    }
</style>

<div class="setting-page">

    {{-- HEADER --}}
    <div class="page-header-row">
        <div class="header-text">
            <div class="breadcrumb-nav">
                <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
                <span>›</span>
                <span>Pengaturan</span>
            </div>
            <h1>Pengaturan Sistem</h1>
            <p>Konfigurasi lengkap sistem Travel Trans Utama</p>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="alert-banner alert-success" id="alert-success">
            <span></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-banner alert-error" id="alert-error">
            <span></span>
            <div>
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin: 4px 0 0 16px; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- TAB BAR --}}
    <div class="setting-tabs" id="settingTabBar">
        <button class="tab-btn" data-tab="profil" id="tab-btn-profil">Profil Perusahaan</button>
        <button class="tab-btn" data-tab="kebijakan" id="tab-btn-kebijakan">Kebijakan Tiket</button>
        <button class="tab-btn" data-tab="pembayaran" id="tab-btn-pembayaran">Integrasi Pembayaran</button>
        <button class="tab-btn" data-tab="lokasi" id="tab-btn-lokasi">Titik Operasional</button>
        <button class="tab-btn" data-tab="keamanan" id="tab-btn-keamanan">Keamanan Sistem</button>
    </div>

    {{-- ══════════════════════════════════════════
         TAB 1 — PROFIL PERUSAHAAN
    ══════════════════════════════════════════ --}}
    <div class="tab-panel" id="panel-profil">
        <form action="{{ route('dashboard.admin.pengaturan.profil') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="setting-card">
                <div class="setting-card-header">
                    <div>
                        <p class="setting-card-title">Profil Perusahaan</p>
                        <p class="setting-card-subtitle">Identitas resmi dan kontak perusahaan yang tampil di tiket dan dokumen resmi.</p>
                    </div>
                </div>
                <div class="setting-card-body">

                    {{-- LOGO --}}
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label class="form-label">Logo Perusahaan</label>
                        <div class="logo-upload-wrap">
                            <div class="logo-preview" id="logoPreviewBox">
                                @if ($settings->get('company_logo'))
                                    <img src="{{ asset('storage/' . $settings->get('company_logo')) }}" alt="Logo Perusahaan" id="logoPreviewImg">
                                @else
                                    <span id="logoPlaceholder" style="font-size:32px;">🏢</span>
                                    <img src="" alt="Logo" id="logoPreviewImg" style="display:none; max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain;">
                                @endif
                            </div>
                            <div class="logo-upload-info">
                                <h4>Upload Logo</h4>
                                <p>Format JPG, PNG, atau WEBP. Maks. 2 MB.<br>Rekomendasi: ukuran transparan 200×200 px.</p>
                                <label class="btn-upload" for="company_logo_input">Pilih File</label>
                                <input type="file" id="company_logo_input" name="company_logo" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;" onchange="previewLogo(this)">
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group form-full">
                            <label class="form-label">Nama Lengkap Administrator <span class="required-star">*</span></label>
                            <input type="text" name="admin_name" class="form-input @error('admin_name') is-invalid @enderror" value="{{ old('admin_name', Auth::user()?->name) }}" placeholder="cth: Super Administrator" required id="input-admin-name">
                            @error('admin_name') <span class="form-error">{{ $message }}</span> @enderror
                            <span class="form-hint" style="font-size:12px; color:#8a9990;">Nama profil administrator yang tampil di pojok kanan atas sistem.</span>
                        </div>

                        <div class="form-group form-full">
                            <label class="form-label">Nama Legal / Merek Travel <span class="required-star">*</span></label>
                            <input type="text" name="company_name" class="form-input @error('company_name') is-invalid @enderror" value="{{ old('company_name', $settings->get('company_name', 'Travel Trans Utama')) }}" placeholder="cth: PT. Trans Utama Mandiri / Travel Trans Utama" required id="input-company-name">
                            @error('company_name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nomor Telepon Kantor</label>
                            <div class="form-input-icon">
                                <span class="icon-prefix"></span>
                                <input type="text" name="company_phone" class="form-input @error('company_phone') is-invalid @enderror" value="{{ old('company_phone', $settings->get('company_phone')) }}" placeholder="cth: (022) 1234-5678" id="input-company-phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kontak WhatsApp Darurat (Call Center)</label>
                            <div class="form-input-icon">
                                <span class="icon-prefix"></span>
                                <input type="text" name="company_whatsapp" class="form-input @error('company_whatsapp') is-invalid @enderror" value="{{ old('company_whatsapp', $settings->get('company_whatsapp')) }}" placeholder="cth: 0812-3456-7890" id="input-company-whatsapp">
                            </div>
                        </div>

                        <div class="form-group form-full">
                            <label class="form-label">Email Resmi</label>
                            <div class="form-input-icon">
                                <span class="icon-prefix"></span>
                                <input type="email" name="company_email" class="form-input @error('company_email') is-invalid @enderror" value="{{ old('company_email', $settings->get('company_email')) }}" placeholder="cth: info@transutama.co.id" id="input-company-email">
                            </div>
                        </div>

                        <div class="form-group form-full">
                            <label class="form-label">Alamat Kantor Pusat</label>
                            <textarea name="company_address" class="form-textarea @error('company_address') is-invalid @enderror" placeholder="Alamat lengkap kantor pusat termasuk kota dan kode pos..." id="input-company-address">{{ old('company_address', $settings->get('company_address')) }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save" id="btn-save-profil">Simpan Profil Perusahaan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         TAB 2 — KEBIJAKAN TIKET & PERJALANAN
    ══════════════════════════════════════════ --}}
    <div class="tab-panel" id="panel-kebijakan">
        <form action="{{ route('dashboard.admin.pengaturan.kebijakan') }}" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:20px;">

                {{-- PAYMENT TIMEOUT --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div class="setting-card-icon">⏱</div>
                        <div>
                            <p class="setting-card-title">Batas Waktu Pembayaran (Payment Timeout)</p>
                            <p class="setting-card-subtitle">Durasi kursi ditahan sebelum otomatis dibatalkan jika belum dibayar.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Timeout QRIS (menit) <span class="required-star">*</span></label>
                                <input type="number" name="payment_timeout_qris" class="form-input @error('payment_timeout_qris') is-invalid @enderror" value="{{ old('payment_timeout_qris', $settings->get('payment_timeout_qris', 15)) }}" min="1" max="1440" required id="input-timeout-qris">
                                <span class="form-hint">Rekomendasi: 15 menit untuk QRIS / e-wallet.</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Timeout Transfer Manual (menit) <span class="required-star">*</span></label>
                                <input type="number" name="payment_timeout_transfer" class="form-input @error('payment_timeout_transfer') is-invalid @enderror" value="{{ old('payment_timeout_transfer', $settings->get('payment_timeout_transfer', 60)) }}" min="1" max="1440" required id="input-timeout-transfer">
                                <span class="form-hint">Rekomendasi: 60 menit untuk transfer bank manual.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REFUND TIERS --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div>
                            <p class="setting-card-title">Aturan Refund dan Pembatalan</p>
                            <p class="setting-card-subtitle">Persentase potongan biaya admin berdasarkan waktu pembatalan sebelum keberangkatan.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div style="overflow-x:auto;">
                            <table class="tier-table">
                                <thead>
                                    <tr>
                                        <th>Kondisi Waktu Pembatalan</th>
                                        <th style="width:160px;">Potongan Admin (%)</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="tier-row">
                                        <td>Lebih dari 7 hari sebelum berangkat</td>
                                        <td><input type="number" name="refund_tier_7d_pct" class="form-input" value="{{ old('refund_tier_7d_pct', $settings->get('refund_tier_7d_pct', 0)) }}" min="0" max="100" id="input-tier-7d"></td>
                                        <td style="color:#8a9990; font-size:12px;">Misal: 0% — refund penuh</td>
                                    </tr>
                                    <tr class="tier-row">
                                        <td>H-3 sampai H-7 sebelum berangkat</td>
                                        <td><input type="number" name="refund_tier_3d_pct" class="form-input" value="{{ old('refund_tier_3d_pct', $settings->get('refund_tier_3d_pct', 10)) }}" min="0" max="100" id="input-tier-3d"></td>
                                        <td style="color:#8a9990; font-size:12px;">Misal: 10% potongan admin</td>
                                    </tr>
                                    <tr class="tier-row">
                                        <td>H-1 sampai H-3 sebelum berangkat</td>
                                        <td><input type="number" name="refund_tier_1d_pct" class="form-input" value="{{ old('refund_tier_1d_pct', $settings->get('refund_tier_1d_pct', 25)) }}" min="0" max="100" id="input-tier-1d"></td>
                                        <td style="color:#8a9990; font-size:12px;">Misal: 25% potongan admin</td>
                                    </tr>
                                    <tr class="tier-row">
                                        <td>Kurang dari 3 jam sebelum berangkat</td>
                                        <td><input type="number" name="refund_tier_3h_pct" class="form-input" value="{{ old('refund_tier_3h_pct', $settings->get('refund_tier_3h_pct', 100)) }}" min="0" max="100" id="input-tier-3h"></td>
                                        <td style="color:#8a9990; font-size:12px;">Misal: 100% — tidak bisa refund</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="refund_tiers" id="refundTiersJson">
                    </div>
                </div>

                {{-- RESCHEDULE --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div>
                            <p class="setting-card-title">Aturan Reschedule (Ubah Jadwal)</p>
                            <p class="setting-card-subtitle">Batas waktu pengajuan ubah jadwal dan biaya penyesuaiannya.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Batas Maksimal Pengajuan (jam sebelum berangkat) <span class="required-star">*</span></label>
                                <input type="number" name="reschedule_cutoff_hours" class="form-input @error('reschedule_cutoff_hours') is-invalid @enderror" value="{{ old('reschedule_cutoff_hours', $settings->get('reschedule_cutoff_hours', 6)) }}" min="0" max="168" required id="input-reschedule-cutoff">
                                <span class="form-hint">cth: 6 = maksimal reschedule sampai 6 jam sebelum berangkat.</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Biaya Reschedule (Rp) <span class="required-star">*</span></label>
                                <input type="number" name="reschedule_fee" class="form-input @error('reschedule_fee') is-invalid @enderror" value="{{ old('reschedule_fee', $settings->get('reschedule_fee', 0)) }}" min="0" step="1000" required id="input-reschedule-fee">
                                <span class="form-hint">Isi 0 jika reschedule gratis.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAGASI --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div>
                            <p class="setting-card-title">Ketentuan Bagasi</p>
                            <p class="setting-card-subtitle">Batas berat bawaan gratis dan tarif kelebihan muatan per kilogram.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Batas Bagasi Gratis (kg) <span class="required-star">*</span></label>
                                <input type="number" name="baggage_free_kg" class="form-input @error('baggage_free_kg') is-invalid @enderror" value="{{ old('baggage_free_kg', $settings->get('baggage_free_kg', 10)) }}" min="0" step="0.5" required id="input-baggage-free">
                                <span class="form-hint">cth: 10 kg gratis per penumpang.</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tarif Kelebihan Bagasi (Rp/kg) <span class="required-star">*</span></label>
                                <input type="number" name="baggage_excess_fee_per_kg" class="form-input @error('baggage_excess_fee_per_kg') is-invalid @enderror" value="{{ old('baggage_excess_fee_per_kg', $settings->get('baggage_excess_fee_per_kg', 5000)) }}" min="0" step="500" required id="input-baggage-fee">
                                <span class="form-hint">Tarif per kg untuk berat di atas batas gratis.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="margin-top:0;">
                    <button type="submit" class="btn-save" id="btn-save-kebijakan">Simpan Kebijakan</button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         TAB 3 — INTEGRASI PEMBAYARAN
    ══════════════════════════════════════════ --}}
    <div class="tab-panel" id="panel-pembayaran">
        <form action="{{ route('dashboard.admin.pengaturan.pembayaran') }}" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:20px;">

                {{-- REKENING BANK --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div>
                            <p class="setting-card-title">Rekening Bank Manual</p>
                            <p class="setting-card-subtitle">Rekening resmi perusahaan untuk penerimaan transfer langsung dari penumpang.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div class="form-grid cols-3">
                            <div class="form-group">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" name="bank_name" class="form-input @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $settings->get('bank_name')) }}" placeholder="cth: BCA, BRI, BNI, Mandiri" id="input-bank-name">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" class="form-input @error('bank_account_number') is-invalid @enderror" value="{{ old('bank_account_number', $settings->get('bank_account_number')) }}" placeholder="cth: 1234567890" id="input-bank-account">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_holder" class="form-input @error('bank_account_holder') is-invalid @enderror" value="{{ old('bank_account_holder', $settings->get('bank_account_holder')) }}" placeholder="cth: PT. Trans Utama Mandiri" id="input-bank-holder">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PAYMENT GATEWAY --}}
                <div class="setting-card">
                    <div class="setting-card-header">
                        <div>
                            <p class="setting-card-title">Kredensial Payment Gateway</p>
                            <p class="setting-card-subtitle">API key untuk mengaktifkan QRIS, virtual account, dan e-wallet otomatis.</p>
                        </div>
                    </div>
                    <div class="setting-card-body">
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label">Pilih Provider Gateway</label>
                            @php $gw = old('gateway_provider', $settings->get('gateway_provider', 'none')); @endphp
                            <div class="gateway-provider-row">
                                <div class="gateway-option">
                                    <input type="radio" name="gateway_provider" id="gw-none" value="none" {{ $gw === 'none' ? 'checked' : '' }}>
                                    <label for="gw-none"><span class="gw-logo"></span>Tidak Aktif</label>
                                </div>
                                <div class="gateway-option">
                                    <input type="radio" name="gateway_provider" id="gw-midtrans" value="midtrans" {{ $gw === 'midtrans' ? 'checked' : '' }}>
                                    <label for="gw-midtrans"><span class="gw-logo"></span>Midtrans</label>
                                </div>
                                <div class="gateway-option">
                                    <input type="radio" name="gateway_provider" id="gw-xendit" value="xendit" {{ $gw === 'xendit' ? 'checked' : '' }}>
                                    <label for="gw-xendit"><span class="gw-logo"></span>Xendit</label>
                                </div>
                            </div>
                        </div>

                        <div id="gatewayKeysSection">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Server Key (Secret Key)</label>
                                    <div class="api-key-field">
                                        <input type="password" name="gateway_server_key" class="form-input @error('gateway_server_key') is-invalid @enderror" value="{{ old('gateway_server_key', $settings->get('gateway_server_key')) }}" placeholder="sk-••••••••••••••••••••••" autocomplete="off" id="input-server-key">
                                        <button type="button" class="api-key-toggle" onclick="toggleApiKey('input-server-key', this)" title="Tampilkan/Sembunyikan">Lihat</button>
                                    </div>
                                    <span class="form-hint">Jangan bagikan server key. Disimpan aman di database.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Client Key (Public Key)</label>
                                    <div class="api-key-field">
                                        <input type="password" name="gateway_client_key" class="form-input @error('gateway_client_key') is-invalid @enderror" value="{{ old('gateway_client_key', $settings->get('gateway_client_key')) }}" placeholder="pk-••••••••••••••••••••••" autocomplete="off" id="input-client-key">
                                        <button type="button" class="api-key-toggle" onclick="toggleApiKey('input-client-key', this)" title="Tampilkan/Sembunyikan">Lihat</button>
                                    </div>
                                    <span class="form-hint">Digunakan di frontend untuk inisialisasi payment widget.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save" id="btn-save-pembayaran">Simpan Integrasi Pembayaran</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════
         TAB 4 — TITIK OPERASIONAL
    ══════════════════════════════════════════ --}}
    <div class="tab-panel" id="panel-lokasi">
        <div class="setting-card">
            <div class="setting-card-header">
                <div>
                    <p class="setting-card-title">Titik Operasional (Pool &amp; Cabang)</p>
                    <p class="setting-card-subtitle">Daftar pool keberangkatan, loket agen, dan titik antar-jemput beserta kontak pengurus.</p>
                </div>
                <div style="margin-left:auto; flex-shrink:0;">
                    <button type="button" class="btn-add-loc" onclick="openLocationModal()" id="btn-add-location">
                        ＋ Tambah Titik
                    </button>
                </div>
            </div>
            <div class="setting-card-body" style="padding:0;">
                @if ($locations->isEmpty())
                    <div class="empty-locations">
                        <p style="font-weight:600; color:#3d4f46; margin:0 0 4px;">Belum ada titik operasional</p>
                        <p style="font-size:12px; margin:0;">Tambahkan pool keberangkatan, loket, atau titik jemput pertama.</p>
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="location-table" id="locationTable">
                            <thead>
                                <tr>
                                    <th>Nama Pool / Cabang</th>
                                    <th>Alamat</th>
                                    <th>Koordinat / Maps</th>
                                    <th>Telepon Pengurus</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $loc)
                                    <tr id="loc-row-{{ $loc->id }}">
                                        <td><span class="location-name">{{ $loc->name }}</span></td>
                                        <td style="max-width:220px; font-size:12px; color:#6a7972;">{{ $loc->address ?: '—' }}</td>
                                        <td>
                                            @if ($loc->maps_url)
                                                <a href="{{ $loc->maps_url }}" target="_blank" class="location-maps">Buka Maps</a>
                                            @elseif ($loc->coordinates)
                                                <span style="font-size:12px; color:#8a9990;">{{ $loc->coordinates }}</span>
                                            @else
                                                <span style="color:#c8d5ce;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($loc->phone)
                                                <span class="location-phone">{{ $loc->phone }}</span>
                                            @else
                                                <span style="color:#c8d5ce;">—</span>
                                            @endif
                                        </td>
                                        <td style="text-align:center;">
                                            <div style="display:flex; gap:6px; justify-content:center;">
                                                <button
                                                    type="button"
                                                    class="btn-edit-loc"
                                                    title="Edit"
                                                    onclick="openEditModal({{ $loc->id }}, {{ json_encode($loc->name) }}, {{ json_encode($loc->address) }}, {{ json_encode($loc->maps_url) }}, {{ json_encode($loc->coordinates) }}, {{ json_encode($loc->phone) }})"
                                                    id="btn-edit-loc-{{ $loc->id }}"
                                                >Ubah</button>
                                                <form action="{{ route('dashboard.admin.pengaturan.lokasi.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Hapus {{ addslashes($loc->name) }}?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-del-loc" title="Hapus" id="btn-del-loc-{{ $loc->id }}">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TAB 5 — KEAMANAN SISTEM
    ══════════════════════════════════════════ --}}
    <div class="tab-panel" id="panel-keamanan">
        <div class="security-section">

            {{-- CHANGE PASSWORD --}}
            <div class="setting-card">
                <div class="setting-card-header">
                    <div>
                        <p class="setting-card-title">Ubah Kata Sandi Admin Utama</p>
                        <p class="setting-card-subtitle">Perbarui kata sandi akun superadmin secara berkala untuk menjaga keamanan sistem.</p>
                    </div>
                </div>
                <div class="setting-card-body">
                    <form action="{{ route('dashboard.admin.pengaturan.keamanan') }}" method="POST">
                        @csrf
                        <div class="form-grid" style="max-width:600px;">
                            <div class="form-group form-full">
                                <label class="form-label">Kata Sandi Saat Ini <span class="required-star">*</span></label>
                                <div class="api-key-field">
                                    <input type="password" name="current_password" class="form-input @error('current_password') is-invalid @enderror" placeholder="Masukkan kata sandi aktif saat ini..." autocomplete="current-password" id="input-current-password">
                                    <button type="button" class="api-key-toggle" onclick="toggleApiKey('input-current-password', this)">Lihat</button>
                                </div>
                                @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kata Sandi Baru <span class="required-star">*</span></label>
                                <div class="api-key-field">
                                    <input type="password" name="new_password" class="form-input @error('new_password') is-invalid @enderror" placeholder="Minimal 8 karakter..." autocomplete="new-password" id="input-new-password">
                                    <button type="button" class="api-key-toggle" onclick="toggleApiKey('input-new-password', this)">Lihat</button>
                                </div>
                                @error('new_password') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Konfirmasi Kata Sandi Baru <span class="required-star">*</span></label>
                                <div class="api-key-field">
                                    <input type="password" name="new_password_confirmation" class="form-input" placeholder="Ulangi kata sandi baru..." autocomplete="new-password" id="input-confirm-password">
                                    <button type="button" class="api-key-toggle" onclick="toggleApiKey('input-confirm-password', this)">Lihat</button>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider" style="margin:24px 0;"></div>

                        <div class="form-group" style="max-width:600px; margin-bottom:0;">
                            <label class="form-label">Verifikasi Dua Langkah (2FA)</label>
                            <div class="toggle-2fa">
                                <div class="toggle-2fa-info">
                                    <h4>Two-Factor Authentication</h4>
                                    <p>Aktifkan verifikasi tambahan via email/SMS setiap kali login sebagai admin utama.</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="two_factor_enabled" value="1" id="toggle-2fa-input" {{ $settings->get('two_factor_enabled') === '1' ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save" id="btn-save-keamanan">Perbarui Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SESSION LOG --}}
            <div class="setting-card">
                <div class="setting-card-header">
                    <div>
                        <p class="setting-card-title">Riwayat Sesi Masuk</p>
                        <p class="setting-card-subtitle">Log perangkat dan IP address yang pernah mengakses akun admin utama ini.</p>
                    </div>
                </div>
                <div class="setting-card-body" style="padding:0;">
                    @if ($loginSessions->isEmpty())
                        <div class="empty-locations">
                            <p style="font-weight:600; color:#3d4f46; margin:0 0 4px;">Belum ada riwayat sesi</p>
                            <p style="font-size:12px; margin:0;">Riwayat login akan muncul setelah terjadi perubahan kata sandi.</p>
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="session-table" id="sessionTable">
                                <thead>
                                    <tr>
                                        <th>Waktu Aktivitas</th>
                                        <th>IP Address</th>
                                        <th>Perangkat / Browser</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loginSessions as $idx => $sess)
                                        <tr>
                                            <td style="font-size:12px; color:#6a7972;">
                                                {{ $sess->last_active_at?->format('d M Y H:i') ?? $sess->created_at->format('d M Y H:i') }}
                                            </td>
                                            <td>
                                                <code style="background:#f0f5f2; padding:2px 6px; border-radius:4px; font-size:12px;">{{ $sess->ip_address ?? '—' }}</code>
                                            </td>
                                            <td style="font-size:12px; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#3d4f46;">
                                                {{ Str::limit($sess->user_agent ?? '—', 60) }}
                                            </td>
                                            <td>
                                                @if ($idx === 0)
                                                    <span class="session-badge current">● Terbaru</span>
                                                @else
                                                    <span class="session-badge other">● Lalu</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- MODAL — TAMBAH / EDIT TITIK OPERASIONAL --}}
<div class="modal-overlay" id="locationModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="locationModalTitle">Tambah Titik Operasional</h3>
            <button type="button" class="modal-close" onclick="closeLocationModal()">×</button>
        </div>
        <form id="locationModalForm" method="POST">
            @csrf
            <span id="locationMethodSpoof"></span>
            <div class="modal-body">
                <div class="form-grid cols-1">
                    <div class="form-group">
                        <label class="form-label">Nama Pool / Cabang <span class="required-star">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="cth: Pool Bandung – Jl. Soekarno Hatta" required id="modal-loc-name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-textarea" placeholder="Alamat lengkap termasuk kota dan kode pos..." rows="2" id="modal-loc-address"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tautan Google Maps</label>
                        <input type="url" name="maps_url" class="form-input" placeholder="https://maps.google.com/?q=..." id="modal-loc-maps">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Koordinat (opsional)</label>
                        <input type="text" name="coordinates" class="form-input" placeholder="cth: -6.914744, 107.609810" id="modal-loc-coords">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon Pengurus</label>
                        <input type="text" name="phone" class="form-input" placeholder="cth: 0812-3456-7890" id="modal-loc-phone">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeLocationModal()">Batal</button>
                <button type="submit" class="btn-save" id="modal-loc-submit">Simpan Titik</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── TAB SWITCHING ────────────────────────────────────────────────────────
    var TAB_KEYS = ['profil', 'kebijakan', 'pembayaran', 'lokasi', 'keamanan'];

    function activateTab(key) {
        TAB_KEYS.forEach(function(k) {
            document.getElementById('tab-btn-' + k).classList.toggle('active', k === key);
            document.getElementById('panel-' + k).classList.toggle('active', k === key);
        });
        history.replaceState(null, '', '?tab=' + key);
    }

    TAB_KEYS.forEach(function(key) {
        document.getElementById('tab-btn-' + key).addEventListener('click', function() {
            activateTab(key);
        });
    });

    (function() {
        var urlTab = new URLSearchParams(window.location.search).get('tab');
        @if (session('tab'))
        if (!urlTab) { urlTab = '{{ session('tab') }}'; }
        @endif
        activateTab(TAB_KEYS.indexOf(urlTab) !== -1 ? urlTab : 'profil');
    })();

    // Auto-dismiss success alert
    setTimeout(function() {
        var el = document.getElementById('alert-success');
        if (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        }
    }, 5000);

    // ── LOGO PREVIEW & VALIDATION ───────────────────────────────────────────
    function previewLogo(input) {
        if (!input.files || !input.files[0]) { return; }
        var file = input.files[0];

        // Validasi ukuran maksimal 2 MB (2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file logo terlalu besar (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB). Maksimal ukuran file yang diperbolehkan adalah 2 MB.');
            input.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('logoPreviewImg');
            var ph  = document.getElementById('logoPlaceholder');
            img.src = e.target.result;
            img.style.display = 'block';
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.style.width = 'auto';
            img.style.height = 'auto';
            img.style.objectFit = 'contain';
            if (ph) { ph.style.display = 'none'; }
        };
        reader.readAsDataURL(file);
    }

    // ── API KEY VISIBILITY ────────────────────────────────────────────────────
    function toggleApiKey(inputId, btn) {
        var inp = document.getElementById(inputId);
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.textContent = '';
        } else {
            inp.type = 'password';
            btn.textContent = '';
        }
    }

    // ── GATEWAY KEYS SECTION TOGGLE ───────────────────────────────────────────
    function updateGatewaySection() {
        var current = document.querySelector('input[name="gateway_provider"]:checked');
        var section = document.getElementById('gatewayKeysSection');
        if (section) {
            section.style.display = (current && current.value === 'none') ? 'none' : 'block';
        }
    }

    document.querySelectorAll('input[name="gateway_provider"]').forEach(function(r) {
        r.addEventListener('change', updateGatewaySection);
    });
    updateGatewaySection();

    // ── LOCATION MODAL ────────────────────────────────────────────────────────
    function openLocationModal() {
        var form = document.getElementById('locationModalForm');
        form.action = '{{ route('dashboard.admin.pengaturan.lokasi.store') }}';
        document.getElementById('locationMethodSpoof').innerHTML = '';
        document.getElementById('locationModalTitle').textContent = 'Tambah Titik Operasional';
        document.getElementById('modal-loc-name').value    = '';
        document.getElementById('modal-loc-address').value = '';
        document.getElementById('modal-loc-maps').value    = '';
        document.getElementById('modal-loc-coords').value  = '';
        document.getElementById('modal-loc-phone').value   = '';
        document.getElementById('modal-loc-submit').textContent = 'Simpan Titik';
        document.getElementById('locationModal').classList.add('open');
    }

    function openEditModal(id, name, address, maps_url, coordinates, phone) {
        var form = document.getElementById('locationModalForm');
        form.action = '/pengaturan/lokasi/' + id;
        document.getElementById('locationMethodSpoof').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('locationModalTitle').textContent = 'Edit Titik Operasional';
        document.getElementById('modal-loc-name').value    = name        || '';
        document.getElementById('modal-loc-address').value = address     || '';
        document.getElementById('modal-loc-maps').value    = maps_url    || '';
        document.getElementById('modal-loc-coords').value  = coordinates || '';
        document.getElementById('modal-loc-phone').value   = phone       || '';
        document.getElementById('modal-loc-submit').textContent = 'Perbarui Titik';
        document.getElementById('locationModal').classList.add('open');
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.remove('open');
    }

    document.getElementById('locationModal').addEventListener('click', function(e) {
        if (e.target === this) { closeLocationModal(); }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeLocationModal(); }
    });
</script>
@endpush
