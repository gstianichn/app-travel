@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <h1 class="au-title">Daftar akun</h1>
    <p class="au-lead">Akun menyimpan e-ticket dan riwayat perjalananmu, jadi data penumpang tidak perlu diketik ulang. Memesan tetap bisa tanpa akun.</p>

    <a href="{{ route('auth.google.redirect') }}" class="au-google">
        @include('auth.partials.google-mark')
        Daftar dengan Google
    </a>

    <p class="au-or">atau isi data di bawah</p>

    <form method="POST" action="{{ route('register.store') }}" class="au-form" data-auth-form>
        @csrf

        <div class="au-field">
            <label for="name">Nama lengkap</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nama sesuai KTP"
                autocomplete="name"
                maxlength="100"
                required
                autofocus
                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
            >
            @error('name')
                <p class="au-field-error" id="name-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="au-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="email@contoh.com"
                autocomplete="email"
                maxlength="150"
                required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="au-field">
            <label for="phone">Nomor HP <span class="au-hint">(boleh dikosongkan)</span></label>
            <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="08xxxxxxxxxx"
                autocomplete="tel"
                inputmode="tel"
                maxlength="20"
                @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
            >
            @error('phone')
                <p class="au-field-error" id="phone-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            @include('auth.partials.password-field', [
                'id' => 'password',
                'name' => 'password',
                'label' => 'Password',
                'hint' => 'Minimal 8 karakter.',
                'autocomplete' => 'new-password',
                'describedBy' => 'password-hint password-strength',
                'dataAttribute' => 'data-password-strength',
            ])
            <div class="au-meter" data-strength-meter data-level="0" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
            <p class="au-live" id="password-strength" data-strength-text aria-live="polite"></p>
        </div>

        <div>
            @include('auth.partials.password-field', [
                'id' => 'password_confirmation',
                'name' => 'password_confirmation',
                'label' => 'Ulangi password',
                'autocomplete' => 'new-password',
                'describedBy' => 'password-match',
                'dataAttribute' => 'data-password-confirm',
            ])
            <p class="au-live" id="password-match" data-match-text aria-live="polite"></p>
        </div>

        <button type="submit" class="au-btn" data-label="Buat akun" data-busy-label="Membuat akun...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Buat akun</span>
        </button>
    </form>

    <div class="au-switch">
        <p>Sudah punya akun? <a href="{{ route('user.login') }}" class="au-link">Masuk</a></p>
    </div>
@endsection
