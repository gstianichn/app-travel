@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <h1 class="au-title">Masuk ke akunmu</h1>
    <p class="au-lead">E-ticket, riwayat perjalanan, dan pengajuan refund ada di sini.</p>

    @if (session('status'))
        <p class="au-alert au-alert--ok" role="status">{{ session('status') }}</p>
    @endif

    @error('google')
        <p class="au-alert au-alert--error" role="alert">{{ $message }}</p>
    @enderror

    <a href="{{ route('auth.google.redirect', array_filter(['redirect' => request('redirect')])) }}" class="au-google">
        @include('auth.partials.google-mark')
        Masuk dengan Google
    </a>

    <p class="au-or">atau pakai email</p>

    <form method="POST" action="{{ route('user.login.store') }}" class="au-form" data-auth-form>
        @csrf
        @if (request('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <div class="au-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="email@contoh.com"
                autocomplete="email"
                required
                autofocus
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        @include('auth.partials.password-field', [
            'id' => 'password',
            'name' => 'password',
            'label' => 'Password',
            'autocomplete' => 'current-password',
        ])

        <div class="au-row">
            <label class="au-check">
                <input type="checkbox" name="remember" @checked(old('remember'))>
                Ingat saya di perangkat ini
            </label>
            <a href="{{ route('password.request') }}" class="au-link">Lupa password?</a>
        </div>

        <button type="submit" class="au-btn" data-label="Masuk" data-busy-label="Sedang masuk...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Masuk</span>
        </button>
    </form>

    <div class="au-switch">
        <div style="margin-bottom: 10px; padding: 12px 14px; border-radius: 10px; border: 1.5px dashed #0C7950; background: #E3F2EA; color: #0C392F; font-size: 0.9rem;">
            <strong>Belum punya akun?</strong> Kami rekomendasikan untuk <a href="{{ route('register', array_filter(['redirect' => request('redirect')])) }}" class="au-link" style="font-weight: 800;">Daftar akun baru (Register)</a> terlebih dahulu agar dapat memesan tiket.
        </div>
        <p><a href="{{ route('search') }}" class="au-link">← Kembali ke pencarian jadwal keberangkatan</a></p>
    </div>
@endsection
