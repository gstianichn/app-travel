@extends('layouts.admin')

@section('title', 'Detail Staf - ' . $staff->name)

@section('page-title', 'Detail Profil & Wewenang Staf')

@section('page-subtitle', 'Informasi profil staf, penempatan cabang/loket, matriks hak akses, dan kontrol status kepegawaian.')

@section('content')

<style>
    .staff-detail-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* BREADCRUMB & TOP NAV */
    .detail-nav-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-trail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #7b8882;
    }

    .breadcrumb-trail a {
        color: #087f55;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-trail a:hover {
        text-decoration: underline;
    }

    .detail-top-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border: 1px solid #dce5e1;
        border-radius: 10px;
        background: white;
        color: #55665e;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-back:hover {
        background: #f3f6f4;
        color: #17221d;
    }

    .btn-action-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        background: #087f55;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .btn-action-edit:hover {
        background: #066b47;
    }

    /* FLASH NOTIFICATIONS */
    .flash-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .flash-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .flash-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    /* =========================================
       1. HERO PROFIL STAF
    ========================================== */
    .profile-hero-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .profile-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 28px 30px;
        background: linear-gradient(135deg, #087f55 0%, #066b47 100%);
        color: white;
        gap: 20px;
        flex-wrap: wrap;
    }

    .profile-hero-top.inactive-hero {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
    }

    .hero-staff-main {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .hero-avatar {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        border: 2px solid rgba(255, 255, 255, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    .hero-staff-info h2 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 800;
        color: white;
    }

    .hero-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.18);
        color: white;
        backdrop-filter: blur(4px);
    }

    .hero-tag.tag-status-active {
        background: #10b981;
        color: white;
    }

    .hero-tag.tag-status-inactive {
        background: #ef4444;
        color: white;
    }

    /* HERO GRID DETAILS */
    .profile-hero-details {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 22px 30px;
        gap: 20px;
        background: #fafcfb;
        border-bottom: 1px solid #edf1ef;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 600;
        color: #7d8e86;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 13px;
        font-weight: 700;
        color: #17221d;
    }

    .detail-wa-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: #16a34a;
        font-weight: 600;
        text-decoration: none;
        margin-top: 2px;
    }

    .detail-wa-btn:hover {
        text-decoration: underline;
    }

    /* =========================================
       MAIN TWO-COLUMN LAYOUT
    ========================================== */
    .detail-grid-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .detail-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* CONTENT CARD */
    .content-card {
        background: white;
        border: 1px solid #e2eae6;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #edf1ef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-head-title {
        font-size: 14px;
        font-weight: 700;
        color: #17221d;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 22px;
    }

    /* BRANCH CARD HIGHLIGHT */
    .branch-highlight-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 18px;
        border-radius: 12px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .branch-icon-large {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #087f55;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .branch-info h4 {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 700;
        color: #14532d;
    }

    .branch-info p {
        margin: 0;
        font-size: 12px;
        color: #276749;
    }

    /* PERMISSION MATRIX ITEMS */
    .perm-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .perm-list-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid #e8eee9;
        background: white;
    }

    .perm-list-item.granted {
        border-color: #bbf7d0;
        background: #fafdfb;
    }

    .perm-list-item.denied {
        border-color: #f1f5f9;
        background: #f8fafc;
        opacity: 0.8;
    }

    .perm-list-item.restricted-denied {
        border-color: #fecdd3;
        background: #fffafa;
    }

    .perm-item-left {
        flex: 1;
    }

    .perm-item-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .perm-item-desc {
        font-size: 11px;
        color: #64748b;
        line-height: 1.4;
        margin: 0;
    }

    .perm-state-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 6px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .perm-state-badge.granted {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .perm-state-badge.denied {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .perm-state-badge.restricted-locked {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* STATUS CONTROL CARD */
    .status-action-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        border-radius: 12px;
        margin-bottom: 18px;
    }

    .status-action-banner.active-banner {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .status-action-banner.inactive-banner {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .banner-text h4 {
        margin: 0 0 3px;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .banner-text p {
        margin: 0;
        font-size: 11px;
        line-height: 1.4;
    }

    .active-banner .banner-text h4 {
        color: #14532d;
    }

    .active-banner .banner-text p {
        color: #166534;
    }

    .inactive-banner .banner-text h4 {
        color: #991b1b;
    }

    .inactive-banner .banner-text p {
        color: #b91c1c;
    }

    .btn-toggle-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 9px;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .btn-toggle-deactivate {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    .btn-toggle-deactivate:hover {
        background: #ef4444;
        color: white;
    }

    .btn-toggle-activate {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .btn-toggle-activate:hover {
        background: #16a34a;
        color: white;
    }

    /* PASSWORD RESET FORM */
    .reset-pwd-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding-top: 14px;
        border-top: 1px dashed #e2eae6;
    }

    .form-group-compact {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group-compact label {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
    }

    .form-group-compact input {
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 12px;
        outline: none;
        transition: border 0.15s ease;
    }

    .form-group-compact input:focus {
        border-color: #087f55;
        box-shadow: 0 0 0 2px rgba(8, 127, 85, 0.1);
    }

    .btn-submit-pwd {
        padding: 9px 16px;
        border-radius: 8px;
        background: #1e293b;
        color: white;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-submit-pwd:hover {
        background: #0f172a;
    }

    @media (max-width: 900px) {
        .detail-grid-layout {
            grid-template-columns: 1fr;
        }

        .profile-hero-details {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .profile-hero-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="staff-detail-container">

    {{-- BREADCRUMB & NAV --}}
    <div class="detail-nav-row">
        <div class="breadcrumb-trail">
            <a href="{{ route('dashboard.admin.index') }}">Dashboard</a>
            <span>/</span>
            <a href="{{ route('dashboard.admin.kelola-staff.index') }}">Kelola Staff</a>
            <span>/</span>
            <span>Detail: {{ $staff->name }}</span>
        </div>

        <div class="detail-top-actions">
            <a href="{{ route('dashboard.admin.kelola-staff.index') }}" class="btn-action-back">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar Staf</span>
            </a>
            <a href="{{ route('dashboard.admin.kelola-staff.edit', $staff->id) }}" class="btn-action-edit">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Profil & Wewenang</span>
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flash-alert flash-success">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-alert flash-error">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================
         1. HERO PROFIL STAF
    ========================================== --}}
    <div class="profile-hero-card">
        <div class="profile-hero-top {{ $staff->status === 'inactive' ? 'inactive-hero' : '' }}">
            <div class="hero-staff-main">
                <div class="hero-avatar">
                    {{ strtoupper(substr($staff->name, 0, 2)) }}
                </div>
                <div class="hero-staff-info">
                    <h2>{{ $staff->name }}</h2>
                    <div class="hero-meta-tags">
                        <span class="hero-tag">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ $staff->position ?? 'Staf Loket' }}</span>
                        </span>
                        <span class="hero-tag">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>{{ $staff->branch ?? 'Cabang Belum Ditentukan' }}</span>
                        </span>
                        @if($staff->status === 'active')
                            <span class="hero-tag tag-status-active">
                                <svg width="10" height="10" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="3.5"/></svg>
                                <span>Aktif Bekerja</span>
                            </span>
                        @else
                            <span class="hero-tag tag-status-inactive">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-width="2"/></svg>
                                <span>Berhenti Bekerja / Nonaktif</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-hero-details">
            <div class="detail-item">
                <span class="detail-label">Penempatan Cabang / Loket</span>
                <span class="detail-value">{{ $staff->branch ?? '-' }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Nomor Kontak / WhatsApp</span>
                <span class="detail-value">{{ $staff->phone ?? '-' }}</span>
                @if($staff->phone)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $staff->phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="detail-wa-btn">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Hubungi WhatsApp</span>
                    </a>
                @endif
            </div>

            <div class="detail-item">
                <span class="detail-label">Email Login</span>
                <span class="detail-value">{{ $staff->email }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">NIK / Kode Akses</span>
                <span class="detail-value">{{ $staff->access_code ?? 'STF-' . str_pad($staff->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    {{-- =========================================
         2. DETAIL GRID (HAK AKSES & KONTROL AKUN)
    ========================================== --}}
    <div class="detail-grid-layout">

        {{-- KOLOM KIRI: MATRIKS HAK AKSES & PEMISAHAN WEWENANG --}}
        <div class="detail-column">
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke-width="2"/></svg>
                        <span>Matriks Hak Akses (Role & Permission)</span>
                    </div>
                    @php
                        $perms = $staff->permissions ?? [];
                        $totalPerms = count($availablePermissions);
                        $grantedCount = count($perms);
                    @endphp
                    <span style="font-size: 11px; font-weight: 700; color: #087f55; background: #e3f5ee; padding: 3px 8px; border-radius: 6px;">
                        {{ $grantedCount }} / {{ $totalPerms }} Wewenang
                    </span>
                </div>
                <div class="card-body">
                    <div class="perm-list">
                        @foreach($availablePermissions as $key => $perm)
                            @php
                                $isGranted = in_array($key, $perms);
                                $isRestricted = $perm['restricted'] ?? false;
                            @endphp

                            <div class="perm-list-item {{ $isGranted ? 'granted' : ($isRestricted ? 'restricted-denied' : 'denied') }}">
                                <div class="perm-item-left">
                                    <div class="perm-item-title">
                                        <span>{{ $perm['label'] }}</span>
                                        @if($isRestricted)
                                            <span style="font-size: 9px; font-weight: 700; background: #fee2e2; color: #b91c1c; padding: 1px 6px; border-radius: 4px;">
                                                Khusus Admin
                                            </span>
                                        @endif
                                    </div>
                                    <p class="perm-item-desc">{{ $perm['description'] }}</p>
                                </div>

                                <div>
                                    @if($isGranted)
                                        <span class="perm-state-badge granted">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Diberikan</span>
                                        </span>
                                    @elseif($isRestricted)
                                        <span class="perm-state-badge restricted-locked">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke-width="2"/></svg>
                                            <span>Dibatasi</span>
                                        </span>
                                    @else
                                        <span class="perm-state-badge denied">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Tidak Aktif</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed #e2eae6; display: flex; justify-content: flex-end;">
                        <a href="{{ route('dashboard.admin.kelola-staff.edit', $staff->id) }}" class="btn-action-edit" style="font-size: 11px; padding: 7px 14px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            <span>Sesuaikan Hak Akses Staf</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATUS AKUN & KONTROL KEPEGAWAIAN --}}
        <div class="detail-column">

            {{-- PENEMPATAN CABANG CARD --}}
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Lokasi Operasional & Penempatan</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="branch-highlight-box">
                        <div class="branch-icon-large">
                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="branch-info">
                            <h4>{{ $staff->branch ?? 'Belum Ditentukan' }}</h4>
                            <p>Penugasan aktif staf untuk operasional layanan loket, verifikasi penumpang, dan keberangkatan travel.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KONTROL STATUS KEPEGAWAIAN --}}
            <div class="content-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                        <span>Kontrol Status Kepegawaian</span>
                    </div>
                </div>
                <div class="card-body">

                    @if($staff->status === 'active')
                        <div class="status-action-banner active-banner">
                            <div class="banner-text">
                                <h4>
                                    <svg width="10" height="10" viewBox="0 0 8 8" fill="#10b981"><circle cx="4" cy="4" r="3.5"/></svg>
                                    <span>Staf Aktif Bekerja</span>
                                </h4>
                                <p>Akun aktif dan memiliki akses operasional loket di {{ $staff->branch }}.</p>
                            </div>
                            <form
                                action="{{ route('dashboard.admin.kelola-staff.toggle-status', $staff->id) }}"
                                method="POST"
                                onsubmit="return confirm('Nonaktifkan akun staf {{ $staff->name }}? Staf yang berhenti bekerja tidak dapat lagi masuk ke aplikasi loket.');"
                            >
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-toggle-status btn-toggle-deactivate">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-width="2"/></svg>
                                    <span>Nonaktifkan (Berhenti)</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="status-action-banner inactive-banner">
                            <div class="banner-text">
                                <h4>
                                    <svg width="14" height="14" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-width="2"/></svg>
                                    <span>Berhenti Bekerja / Nonaktif</span>
                                </h4>
                                <p>Akses akun diblokir karena staf sudah berhenti. Klik tombol berikut untuk mengaktifkan kembali.</p>
                            </div>
                            <form
                                action="{{ route('dashboard.admin.kelola-staff.toggle-status', $staff->id) }}"
                                method="POST"
                                onsubmit="return confirm('Aktifkan kembali akun staf {{ $staff->name }} agar dapat bertugas di loket?');"
                            >
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-toggle-status btn-toggle-activate">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Aktifkan Kembali</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- RESET PASSWORD MANUAL FORM --}}
                    <div class="reset-pwd-form">
                        <div style="font-size: 12px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <span>Reset Kata Sandi Manual Staf</span>
                        </div>
                        <p style="margin: 0; font-size: 11px; color: #6b7280;">
                            Jika staf lupa kata sandi login, Admin Utama dapat menetapkan kata sandi baru secara langsung.
                        </p>

                        <form action="{{ route('dashboard.admin.kelola-staff.reset-password', $staff->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                <div class="form-group-compact">
                                    <label for="pwd_new">Kata Sandi Baru</label>
                                    <input type="password" id="pwd_new" name="password" placeholder="Min. 8 karakter" required>
                                </div>
                                <div class="form-group-compact">
                                    <label for="pwd_conf">Ulangi Sandi</label>
                                    <input type="password" id="pwd_conf" name="password_confirmation" placeholder="Konfirmasi sandi" required>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit-pwd">
                                Tetapkan Kata Sandi Baru
                            </button>
                        </form>
                    </div>

                    {{-- HAPUS AKUN STAF --}}
                    <div style="margin-top: 20px; padding-top: 14px; border-top: 1px dashed #e2eae6; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; color: #9ca3af;">
                            Hapus akun staf jika sudah tidak diperlukan
                        </span>
                        <form
                            action="{{ route('dashboard.admin.kelola-staff.destroy', $staff->id) }}"
                            method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen akun staf {{ $staff->name }}?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #dc2626; font-size: 11px; font-weight: 600; cursor: pointer; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus Akun Staf</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection
