@extends('layouts.auth')

@section('title', 'Masuk dengan Google')

@section('content')
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
        @include('auth.partials.google-mark')
        <span style="font-size: 0.9rem; font-weight: 800; color: #56635B;">Login dengan Google</span>
    </div>

    <h1 class="au-title">Pilih akun Google</h1>
    <p class="au-lead">Untuk melanjutkan ke <strong>TravelKu</strong>. Notifikasi verifikasi login akan dikirim langsung ke alamat Gmail yang Anda gunakan.</p>

    @if ($suggestedAccounts->isNotEmpty())
        <div style="display: grid; gap: 8px; margin-top: 16px;">
            <p style="font-size: 0.82rem; font-weight: 700; color: #56635B; text-transform: uppercase; letter-spacing: 0.06em;">Pilih Cepat Akun Terdaftar</p>
            @foreach ($suggestedAccounts as $acc)
                <form method="POST" action="{{ route('auth.google.verify') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $acc->email }}">
                    <input type="hidden" name="name" value="{{ $acc->name }}">
                    <button
                        type="submit"
                        style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border-radius: 12px; border: 1.5px solid #D9D4C4; background: #FFFFFF; color: #19241E; text-align: left; cursor: pointer;"
                    >
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <span style="width: 36px; height: 36px; border-radius: 999px; background: #0C7950; color: #FFFFFF; display: inline-grid; place-items: center; font-weight: 800; font-size: 0.95rem;">
                                {{ strtoupper(substr($acc->name, 0, 1)) }}
                            </span>
                            <span>
                                <strong style="display: block; font-size: 0.95rem; color: #0C392F;">{{ $acc->name }}</strong>
                                <small style="color: #56635B; font-size: 0.83rem;">{{ $acc->email }}</small>
                            </span>
                        </span>
                        <span style="color: #0C7950; font-size: 0.85rem; font-weight: 800;">Lanjutkan →</span>
                    </button>
                </form>
            @endforeach
        </div>

        <p class="au-or">atau gunakan akun Gmail lain</p>
    @endif

    <form method="POST" action="{{ route('auth.google.verify') }}" class="au-form" data-auth-form>
        @csrf

        <div class="au-field">
            <label for="google_email">Alamat Email Google (Gmail)</label>
            <input
                type="email"
                id="google_email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@gmail.com"
                autocomplete="email"
                required
                autofocus
            >
            @error('email')
                <p class="au-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="au-field">
            <label for="google_name">Nama Profil Google (Opsional)</label>
            <input
                type="text"
                id="google_name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nama Lengkap Anda"
                autocomplete="name"
            >
        </div>

        <button type="submit" class="au-btn" data-label="Verifikasi & Masuk dengan Gmail" data-busy-label="Memverifikasi Gmail...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Verifikasi & Masuk dengan Gmail</span>
        </button>
    </form>

    <div class="au-switch">
        <p><a href="{{ route('user.login') }}" class="au-link">← Kembali ke halaman masuk</a></p>
    </div>
@endsection

