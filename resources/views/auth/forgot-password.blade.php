@extends('layouts.auth')

@section('title', 'Lupa password')

@section('content')
    <h1 class="au-title">Lupa password</h1>
    <p class="au-lead">Tulis email akunmu. Kami kirim tautan untuk membuat password baru ke email itu.</p>

    @if (session('status'))
        <p class="au-alert au-alert--ok" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="au-form" data-auth-form>
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

        <button type="submit" class="au-btn" data-label="Kirim tautan" data-busy-label="Mengirim...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Kirim tautan</span>
        </button>
    </form>

    <div class="au-switch">
        <p>Sudah ingat? <a href="{{ route('user.login') }}" class="au-link">Kembali ke halaman masuk</a></p>
    </div>
@endsection
