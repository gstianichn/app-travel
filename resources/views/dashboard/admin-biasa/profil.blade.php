<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Staff - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    @include('dashboard.admin-biasa.partials.styles')
    @fonts('overpass')
    @vite(['resources/css/dashboard-staff.css'])

    <style>
        .staff-profile-summary {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px 28px;
            padding: 22px 24px;
            border: 1px solid var(--lp-line);
            border-radius: 14px;
            background: var(--lp-card);
        }

        .staff-profile-identity {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .staff-profile-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1.5px solid #c5e2d4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .staff-profile-meta h2 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--lp-forest);
            line-height: 1.25;
        }

        .staff-profile-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 14px;
            margin-top: 5px;
            font-size: 0.9rem;
            color: var(--lp-muted);
        }

        .staff-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .staff-status-pill--active {
            background: var(--success-bg);
            color: var(--primary-dark);
        }

        .staff-status-pill--inactive {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .staff-profile-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 28px;
            padding-left: 24px;
            border-left: 1px solid var(--lp-line);
        }

        .staff-profile-metric span {
            display: block;
            font-size: 0.8rem;
            color: var(--lp-muted);
        }

        .staff-profile-metric strong {
            display: block;
            margin-top: 2px;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--lp-forest);
            font-variant-numeric: tabular-nums;
        }

        .staff-profile-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .staff-profile-stack {
            display: grid;
            gap: 24px;
        }

        .sp-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .sp-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sp-field--full {
            grid-column: 1 / -1;
        }

        .sp-field label {
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--lp-ink);
        }

        .sp-input {
            width: 100%;
            height: 44px;
            padding: 0 13px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #ffffff;
            color: var(--lp-ink);
            font-size: 0.94rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .sp-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(12, 121, 80, 0.12);
        }

        .sp-input[readonly] {
            background: var(--lp-paper);
            color: var(--lp-muted);
            cursor: default;
        }

        .sp-help {
            font-size: 0.8rem;
            color: var(--lp-muted);
            margin: 0;
        }

        .sp-error {
            font-size: 0.8rem;
            color: var(--danger);
            font-weight: 600;
        }

        .sp-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
        }

        .sp-alert {
            padding: 13px 16px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .sp-alert--success {
            background: var(--success-bg);
            border-color: #b7decb;
            color: var(--primary-dark);
        }

        .sp-alert--error {
            background: var(--danger-bg);
            border-color: #eabfb3;
            color: var(--danger);
        }

        .sp-perm-list {
            list-style: none;
            margin: 0;
            padding: 0;
            divide-y: 1px solid var(--border-subtle);
        }

        .sp-perm-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .sp-perm-item:first-child {
            padding-top: 0;
        }

        .sp-perm-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .sp-perm-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--lp-ink);
        }

        .sp-perm-desc {
            font-size: 0.82rem;
            color: var(--lp-muted);
            margin-top: 2px;
        }

        .sp-perm-badge {
            flex-shrink: 0;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .sp-perm-badge--granted {
            background: var(--success-bg);
            color: var(--primary-dark);
        }

        .sp-perm-badge--denied {
            background: var(--lp-paper);
            color: var(--lp-muted);
            border: 1px solid var(--border);
        }

        .sp-info-list {
            display: grid;
            gap: 12px;
            margin: 0;
        }

        .sp-info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
        }

        .sp-info-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .sp-info-row dt {
            color: var(--lp-muted);
        }

        .sp-info-row dd {
            margin: 0;
            font-weight: 700;
            color: var(--lp-ink);
            text-align: right;
        }

        @media (max-width: 960px) {
            .staff-profile-grid {
                grid-template-columns: 1fr;
            }

            .staff-profile-metrics {
                padding-left: 0;
                border-left: none;
                padding-top: 14px;
                border-top: 1px solid var(--lp-line);
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .sp-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body data-staff="staff">

<div class="dashboard-wrapper">

    @include('dashboard.admin-biasa.partials.sidebar')

    <main class="main-content">

        @include('dashboard.admin-biasa.partials.topbar', [
            'topbarTitle' => 'Profil Staff',
            'topbarSubtitle' => 'Informasi akun, penempatan loket, dan keamanan kata sandi',
        ])

        <section class="content">
            <div class="ds" style="gap: 24px;">
                <header class="ds-head">
                    <div>
                        <h1>Profil Saya</h1>
                        <p>Perbarui data kontak Anda atau ganti password akun staff operasional.</p>
                    </div>
                    <div class="ds-head-actions">
                        <a href="{{ route('dashboard.admin-biasa.index') }}" class="ds-btn ds-btn--secondary">Kembali ke Beranda</a>
                    </div>
                </header>

                @if (session('success'))
                    <div class="sp-alert sp-alert--success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="sp-alert sp-alert--error" role="alert">
                        Periksa kembali isian formulir Anda. {{ $errors->first() }}
                    </div>
                @endif

                {{-- Ringkasan Identitas Staff --}}
                <section class="staff-profile-summary" aria-label="Ringkasan akun staff">
                    <div class="staff-profile-identity">
                        <div class="staff-profile-avatar" aria-hidden="true">
                            {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="staff-profile-meta">
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                <h2>{{ $user->name }}</h2>
                                @if (($user->status ?? 'active') === 'active')
                                    <span class="staff-status-pill staff-status-pill--active">Aktif</span>
                                @else
                                    <span class="staff-status-pill staff-status-pill--inactive">{{ ucfirst((string) $user->status) }}</span>
                                @endif
                            </div>
                            <div class="staff-profile-meta-row">
                                <span>{{ $user->position ?: 'Staff Operasional' }}</span>
                                <span>&bull;</span>
                                <span>{{ $user->branch ?: 'Loket Belum Diatur' }}</span>
                                <span>&bull;</span>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="staff-profile-metrics">
                        <div class="staff-profile-metric">
                            <span>Verifikasi Hari Ini</span>
                            <strong>{{ $verifiedTodayCount }}</strong>
                        </div>
                        <div class="staff-profile-metric">
                            <span>Total Verifikasi</span>
                            <strong>{{ $verifiedPaymentsCount }}</strong>
                        </div>
                    </div>
                </section>

                <div class="staff-profile-grid">
                    {{-- Kolom Kiri: Data Profil & Hak Akses --}}
                    <div class="staff-profile-stack">
                        <section class="ds-panel" aria-labelledby="sp-info-title">
                            <div class="ds-panel-head">
                                <div>
                                    <h2 id="sp-info-title">Informasi Profil</h2>
                                    <p>Nama dan kontak ini tampil di dashboard operasional dan catatan loket.</p>
                                </div>
                            </div>
                            <div class="ds-panel-body">
                                <form method="POST" action="{{ route('dashboard.admin-biasa.profil.update') }}" autocomplete="off">
                                    @csrf
                                    <div class="sp-form-grid">
                                        <div class="sp-field sp-field--full">
                                            <label for="staff_name">Nama Lengkap</label>
                                            <input id="staff_name"
                                                   type="text"
                                                   name="name"
                                                   class="sp-input"
                                                   value="{{ old('name', $user->name) }}"
                                                   required
                                                   autocomplete="off">
                                            @error('name')
                                                <span class="sp-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sp-field">
                                            <label for="staff_email">Alamat Email</label>
                                            <input id="staff_email"
                                                   type="email"
                                                   name="email"
                                                   class="sp-input"
                                                   value="{{ old('email', $user->email) }}"
                                                   required
                                                   autocomplete="off">
                                            @error('email')
                                                <span class="sp-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sp-field">
                                            <label for="staff_phone">Nomor HP</label>
                                            <input id="staff_phone"
                                                   type="tel"
                                                   name="phone"
                                                   class="sp-input"
                                                   inputmode="numeric"
                                                   pattern="[0-9]*"
                                                   maxlength="20"
                                                   placeholder="08xxxxxxxxxx"
                                                   value="{{ old('phone', $user->phone) }}"
                                                   autocomplete="off">
                                            @error('phone')
                                                <span class="sp-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sp-field">
                                            <label for="staff_branch">Penempatan Loket</label>
                                            <input id="staff_branch"
                                                   type="text"
                                                   class="sp-input"
                                                   value="{{ $user->branch ?: 'Belum ditetapkan' }}"
                                                   readonly>
                                            <p class="sp-help">Diatur oleh Admin Utama.</p>
                                        </div>

                                        <div class="sp-field">
                                            <label for="staff_position">Jabatan</label>
                                            <input id="staff_position"
                                                   type="text"
                                                   class="sp-input"
                                                   value="{{ $user->position ?: 'Staff Operasional' }}"
                                                   readonly>
                                            <p class="sp-help">Diatur oleh Admin Utama.</p>
                                        </div>
                                    </div>

                                    <div class="sp-actions">
                                        <button type="submit" class="ds-btn ds-btn--primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <section class="ds-panel" aria-labelledby="sp-perm-title">
                            <div class="ds-panel-head">
                                <div>
                                    <h2 id="sp-perm-title">Hak Akses Operasional</h2>
                                    <p>Daftar kewenangan yang diberikan pada akun staff Anda.</p>
                                </div>
                            </div>
                            <div class="ds-panel-body">
                                <ul class="sp-perm-list">
                                    @foreach ($permissions as $perm)
                                        <li class="sp-perm-item">
                                            <div>
                                                <div class="sp-perm-title">{{ $perm['label'] }}</div>
                                                <div class="sp-perm-desc">{{ $perm['description'] }}</div>
                                            </div>
                                            @if ($perm['granted'])
                                                <span class="sp-perm-badge sp-perm-badge--granted">Aktif</span>
                                            @else
                                                <span class="sp-perm-badge sp-perm-badge--denied">Dibatasi</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </section>
                    </div>

                    {{-- Kolom Kanan: Ubah Password & Info Sesi --}}
                    <div class="staff-profile-stack">
                        <section class="ds-panel" aria-labelledby="sp-password-title">
                            <div class="ds-panel-head">
                                <div>
                                    <h2 id="sp-password-title">Ubah Password</h2>
                                    <p>Gunakan minimal 8 karakter untuk menjaga keamanan akun loket.</p>
                                </div>
                            </div>
                            <div class="ds-panel-body">
                                <form method="POST" action="{{ route('dashboard.admin-biasa.profil.password') }}" autocomplete="off">
                                    @csrf
                                    <div style="display: grid; gap: 14px;">
                                        <div class="sp-field">
                                            <label for="current_password">Password Saat Ini</label>
                                            <input id="current_password"
                                                   type="password"
                                                   name="current_password"
                                                   class="sp-input"
                                                   required
                                                   autocomplete="current-password">
                                            @error('current_password')
                                                <span class="sp-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sp-field">
                                            <label for="new_password">Password Baru</label>
                                            <input id="new_password"
                                                   type="password"
                                                   name="password"
                                                   class="sp-input"
                                                   minlength="8"
                                                   required
                                                   autocomplete="new-password">
                                            @error('password')
                                                <span class="sp-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sp-field">
                                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                            <input id="new_password_confirmation"
                                                   type="password"
                                                   name="password_confirmation"
                                                   class="sp-input"
                                                   minlength="8"
                                                   required
                                                   autocomplete="new-password">
                                        </div>
                                    </div>

                                    <div class="sp-actions">
                                        <button type="submit" class="ds-btn ds-btn--primary">Perbarui Password</button>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <section class="ds-panel" aria-labelledby="sp-account-title">
                            <div class="ds-panel-head">
                                <div>
                                    <h2 id="sp-account-title">Informasi Akun</h2>
                                </div>
                            </div>
                            <div class="ds-panel-body">
                                <dl class="sp-info-list">
                                    <div class="sp-info-row">
                                        <dt>Peran</dt>
                                        <dd>Staff Operasional</dd>
                                    </div>
                                    <div class="sp-info-row">
                                        <dt>Bergabung Sejak</dt>
                                        <dd>{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</dd>
                                    </div>
                                    <div class="sp-info-row">
                                        <dt>Login Terakhir</dt>
                                        <dd>{{ $user->last_login_at ? $user->last_login_at->translatedFormat('d M Y, H:i').' WIB' : 'Sesi aktif saat ini' }}</dd>
                                    </div>
                                </dl>

                                <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="ds-btn ds-btn--secondary" style="width: 100%; color: var(--danger); border-color: #e6c6bc;">
                                            Keluar dari Akun
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

    </main>

</div>

@include('components.numeric-input-enforcer')

</body>
</html>
