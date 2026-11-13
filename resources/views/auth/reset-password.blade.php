@extends('layouts.auth')

@section('title', 'Buat Password Baru')

@section('content')
    <h1 class="au-title">Buat Password Baru</h1>
    <p class="au-lead">Kode OTP berhasil diverifikasi untuk <strong>{{ $email }}</strong>. Silakan masukkan password baru Anda.</p>

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

    <form method="POST" action="{{ route('password.update') }}" class="au-form" data-auth-form>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            @include('auth.partials.password-field', [
                'id' => 'password',
                'name' => 'password',
                'label' => 'Password Baru',
                'hint' => 'Minimal 8 karakter.',
                'autocomplete' => 'new-password',
                'describedBy' => 'password-hint password-strength',
                'dataAttribute' => 'data-password-strength',
            ])
            <div class="au-meter" data-strength-meter data-level="0" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
            <p class="au-live" id="password-strength" data-strength-text aria-live="polite"></p>
            @error('password')
                <p class="au-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            @include('auth.partials.password-field', [
                'id' => 'password_confirmation',
                'name' => 'password_confirmation',
                'label' => 'Konfirmasi Password Baru',
                'autocomplete' => 'new-password',
                'describedBy' => 'password-match',
                'dataAttribute' => 'data-password-confirm',
            ])
            <p class="au-live" id="password-match" data-match-text aria-live="polite"></p>
        </div>

        <button type="submit" class="au-btn" data-label="Simpan Password Baru" data-busy-label="Menyimpan password baru...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Simpan Password Baru</span>
        </button>
    </form>

    <div class="au-switch">
        <p><a href="{{ route('user.login') }}" class="au-link">Batal dan kembali ke halaman masuk</a></p>
    </div>
@endsection
