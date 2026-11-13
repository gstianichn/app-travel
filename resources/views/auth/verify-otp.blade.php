@extends('layouts.auth')

@section('title', 'Verifikasi Kode OTP')

@section('content')
    <h1 class="au-title">Verifikasi Kode OTP</h1>
    <p class="au-lead">
        Masukkan 6 digit kode verifikasi yang telah dikirim ke <strong>{{ $email }}</strong>.
        Kode ini berlaku selama <strong>15 menit</strong>.
    </p>

    @if (session('status'))
        <div class="au-alert au-alert--ok" role="status">
            <p style="margin: 0; font-weight: 700;">{{ session('status') }}</p>
        </div>
    @endif

    @if (!empty($otpPreview))
        <div class="au-alert au-alert--ok" role="status" style="border: 2px dashed #0C7950; background: #E8F5E9;">
            <p style="margin: 0; font-size: 0.88rem; color: #0C392F;">
                <strong>Mode Pengujian / Driver Log:</strong> Kode OTP Anda adalah:
                <span style="font-size: 1.25rem; font-weight: 800; letter-spacing: 3px; color: #0C7950; font-family: monospace; display: inline-block; margin-left: 6px;">{{ $otpPreview }}</span>
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="au-alert au-alert--error" role="alert">
            <p style="margin: 0; font-weight: 700;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.check') }}" class="au-form" data-auth-form>
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="au-field">
            <label for="otp">Kode Verifikasi (6 Digit Angka)</label>
            <input
                type="tel"
                id="otp"
                name="otp"
                value="{{ old('otp') }}"
                placeholder="123456"
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]*"
                required
                autofocus
                autocomplete="one-time-code"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                style="font-size: 1.5rem; letter-spacing: 8px; text-align: center; font-weight: 800; font-family: monospace;"
                @error('otp') aria-invalid="true" aria-describedby="otp-error" @enderror
            >
            @error('otp')
                <p class="au-field-error" id="otp-error">{{ $message }}</p>
            @enderror
            <p style="font-size: 0.82rem; color: #4B6358; margin: 4px 0 0 0;">
                Hanya angka 0-9. Pastikan batas waktu 15 menit belum terlewati.
            </p>
        </div>

        <button type="submit" class="au-btn" data-label="Verifikasi Kode OTP" data-busy-label="Memverifikasi OTP...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Verifikasi Kode OTP</span>
        </button>
    </form>

    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #E0E7E3; display: grid; gap: 12px; text-align: center;">
        <p style="margin: 0; font-size: 0.9rem; color: #4B6358;">
            Tidak menerima email atau kode kedaluwarsa?
        </p>

        <form method="POST" action="{{ route('password.otp.resend') }}" data-auth-form>
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button
                type="submit"
                class="au-link"
                style="background: none; border: none; cursor: pointer; font-weight: 800; font-size: 0.92rem;"
                data-label="Kirim Ulang Kode OTP"
                data-busy-label="Mengirim ulang..."
            >
                Kirim Ulang Kode OTP
            </button>
        </form>

        <p style="margin: 4px 0 0 0; font-size: 0.88rem;">
            Salah alamat email? <a href="{{ route('password.request') }}" class="au-link">Ganti email</a>
        </p>
    </div>
@endsection

