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

    <a href="{{ route('auth.google.redirect') }}" class="au-google">
        @include('auth.partials.google-mark')
        Masuk dengan Google
    </a>

    <p class="au-or">atau pakai email</p>

    <form method="POST" action="{{ route('user.login.store') }}" class="au-form" data-auth-form>
        @csrf

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
        <p>Belum punya akun? <a href="{{ route('register') }}" class="au-link">Daftar akun</a></p>
        <p>Atau <a href="{{ route('search') }}" class="au-link">pesan tiket tanpa akun</a>; e-ticket dikirim ke email.</p>
    </div>
@endsection
