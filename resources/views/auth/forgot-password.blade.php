@extends('layouts.auth')

@section('title', 'Lupa password')

@section('content')
    <h1 class="au-title">Lupa password</h1>
    <p class="au-lead">Masukkan alamat email akun TravelKu Anda. Kami akan mengirimkan 6 digit kode verifikasi (OTP) untuk mengatur ulang password Anda.</p>

    @if (session('status'))
        <div class="au-alert au-alert--ok" role="status">
            <p style="margin: 0; font-weight: 700;">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="au-alert au-alert--error" role="alert">
            <p style="margin: 0; font-weight: 700;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="au-form" data-auth-form>
        @csrf

        <div class="au-field">
            <label for="email">Alamat Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', session('reset_email')) }}"
                placeholder="nama@email.com"
                autocomplete="email"
                required
                autofocus
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="au-btn" data-label="Kirim Kode OTP" data-busy-label="Mengirim kode OTP...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Kirim Kode OTP</span>
        </button>
    </form>

    <div class="au-switch" style="display: grid; gap: 12px;">
        <p class="au-or" style="margin: 4px 0;">atau masuk langsung</p>
        <a href="{{ route('auth.google.redirect') }}" class="au-google">
            @include('auth.partials.google-mark')
            Masuk dengan Akun Google
        </a>
        <p style="margin: 0;">Sudah ingat kata sandi? <a href="{{ route('user.login') }}" class="au-link">Kembali ke halaman masuk</a></p>
    </div>
@endsection
