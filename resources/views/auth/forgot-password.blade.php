@extends('layouts.auth')

@section('title', 'Lupa password')

@section('content')
    <h1 class="au-title">Lupa password</h1>
    <p class="au-lead">Masukkan alamat email Google / Gmail akunmu. Kami akan mengirimkan tautan verifikasi untuk mengatur ulang password ke akun Google yang bersangkutan.</p>

    @if (session('status'))
        <div class="au-alert au-alert--ok" role="status" style="display: grid; gap: 10px;">
            <p style="margin: 0; font-weight: 700;">{{ session('status') }}</p>
            @if (session('reset_email_sent_to'))
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px;">
                    <a
                        href="https://mail.google.com/mail/u/?authuser={{ urlencode(session('reset_email_sent_to')) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="au-google"
                        style="min-height: 40px; padding: 0 14px; font-size: 0.88rem; width: auto;"
                    >
                        @include('auth.partials.google-mark')
                        Buka Gmail ({{ session('reset_email_sent_to') }})
                    </a>
                    @if (session('reset_preview_url'))
                        <a
                            href="{{ session('reset_preview_url') }}"
                            class="au-link"
                            style="display: inline-flex; align-items: center; min-height: 40px; padding: 0 12px; border-radius: 8px; border: 1.5px solid #0C7950; background: #FFFFFF; font-size: 0.85rem; font-weight: 800;"
                        >
                            Buka Tautan Verifikasi Reset Password →
                        </a>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="au-form" data-auth-form>
        @csrf

        <div class="au-field">
            <label for="email">Email Akun Google (Gmail)</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', session('reset_email_sent_to')) }}"
                placeholder="nama@gmail.com"
                autocomplete="email"
                required
                autofocus
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="au-btn" data-label="Kirim Tautan ke Gmail" data-busy-label="Mengirim ke Gmail...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Kirim Tautan ke Gmail</span>
        </button>
    </form>

    <div class="au-switch" style="display: grid; gap: 12px;">
        <p class="au-or" style="margin: 4px 0;">atau masuk langsung tanpa password lama</p>
        <a href="{{ route('auth.google.redirect') }}" class="au-google">
            @include('auth.partials.google-mark')
            Masuk dengan Akun Google
        </a>
        <p style="margin: 0;">Sudah ingat? <a href="{{ route('user.login') }}" class="au-link">Kembali ke halaman masuk</a></p>
    </div>
@endsection
