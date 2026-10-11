@extends('layouts.auth')

@section('title', 'Password baru')

@section('content')
    <h1 class="au-title">Buat password baru</h1>
    <p class="au-lead">Setelah disimpan, kamu langsung bisa masuk dengan password ini.</p>

    <form method="POST" action="{{ route('password.update') }}" class="au-form" data-auth-form>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="au-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', request('email')) }}"
                autocomplete="email"
                required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            @include('auth.partials.password-field', [
                'id' => 'password',
                'name' => 'password',
                'label' => 'Password baru',
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
                'label' => 'Ulangi password baru',
                'autocomplete' => 'new-password',
                'describedBy' => 'password-match',
                'dataAttribute' => 'data-password-confirm',
            ])
            <p class="au-live" id="password-match" data-match-text aria-live="polite"></p>
        </div>

        <button type="submit" class="au-btn" data-label="Simpan password" data-busy-label="Menyimpan...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Simpan password</span>
        </button>
    </form>

    <div class="au-switch">
        <p><a href="{{ route('user.login') }}" class="au-link">Kembali ke halaman masuk</a></p>
    </div>
@endsection
