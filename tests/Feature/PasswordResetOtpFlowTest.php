<?php

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'traveler.otp@example.com',
        'password' => Hash::make('OldPassword123!'),
        'status' => 'active',
        'role' => 'user',
    ]);
});

test('halaman lupa password dapat diakses dengan form input email dan loading state', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
    $response->assertSee('Lupa password');
    $response->assertSee('data-auth-form', false);
    $response->assertSee('Kirim Kode OTP');
    $response->assertSee('data-busy-label="Mengirim kode OTP..."', false);
});

test('request OTP menolak email yang tidak terdaftar di database', function () {
    $response = $this->post(route('password.email'), [
        'email' => 'unregistered@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(DB::table('password_reset_tokens')->where('email', 'unregistered@example.com')->count())->toBe(0);
});

test('request OTP menolak akun yang dinonaktifkan atau diblokir', function () {
    $blockedUser = User::factory()->create([
        'email' => 'blocked.user@example.com',
        'status' => 'blocked',
    ]);

    $response = $this->post(route('password.email'), [
        'email' => $blockedUser->email,
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(DB::table('password_reset_tokens')->where('email', $blockedUser->email)->count())->toBe(0);
});

test('request OTP berhasil membuat 6 digit angka, expired dalam 15 menit, dan mengirim email mailable', function () {
    Mail::fake();

    $response = $this->post(route('password.email'), [
        'email' => $this->user->email,
    ]);

    $response->assertRedirect(route('password.otp.verify', ['email' => $this->user->email]));

    $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
    expect($record)->not->toBeNull();
    expect($record->otp)->toMatch('/^[0-9]{6}$/');
    expect(Carbon::parse($record->expires_at)->diffInMinutes(Carbon::now()))->toBeLessThanOrEqual(15);
    expect($record->verified_at)->toBeNull();

    Mail::assertSent(ResetPasswordOtpMail::class, function ($mail) use ($record) {
        return $mail->hasTo($this->user->email)
            && $mail->otp === $record->otp
            && $mail->expiresInMinutes === 15;
    });
});

test('request OTP via JSON / API mengembalikan status sukses dan redirect_url', function () {
    Mail::fake();

    $response = $this->postJson(route('password.email'), [
        'email' => $this->user->email,
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'email' => $this->user->email,
    ]);
    $response->assertJsonStructure(['redirect_url']);
});

test('halaman verifikasi OTP menolak akses tanpa parameter email', function () {
    $response = $this->get(route('password.otp.verify'));
    $response->assertRedirect(route('password.request'));
});

test('halaman verifikasi OTP dapat diakses dan memuat input 6 digit angka', function () {
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => 'init-token-123',
            'otp' => '654321',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => null,
        ]
    );

    $response = $this->get(route('password.otp.verify', ['email' => $this->user->email]));
    $response->assertOk();
    $response->assertSee('Verifikasi Kode OTP');
    $response->assertSee('654321');
    $response->assertSee('maxlength="6"', false);
    $response->assertSee('inputmode="numeric"', false);
    $response->assertSee('pattern="[0-9]*"', false);
});

test('validasi OTP menolak jika kode OTP salah', function () {
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => 'init-token-123',
            'otp' => '123456',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => null,
        ]
    );

    $response = $this->post(route('password.otp.check'), [
        'email' => $this->user->email,
        'otp' => '999999',
    ]);

    $response->assertSessionHasErrors(['otp']);
    $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
    expect($record->verified_at)->toBeNull();
});

test('validasi OTP menolak jika kode OTP telah kedaluwarsa lebih dari 15 menit', function () {
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => 'init-token-123',
            'otp' => '123456',
            'created_at' => Carbon::now()->subMinutes(20),
            'expires_at' => Carbon::now()->subMinutes(5),
            'verified_at' => null,
        ]
    );

    $response = $this->post(route('password.otp.check'), [
        'email' => $this->user->email,
        'otp' => '123456',
    ]);

    $response->assertSessionHasErrors(['otp']);
    $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
    expect($record->verified_at)->toBeNull();
});

test('validasi OTP berhasil memvalidasi kode yang cocok dalam batas 15 menit dan membuat verified token', function () {
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => 'init-token-123',
            'otp' => '482910',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => null,
        ]
    );

    $response = $this->post(route('password.otp.check'), [
        'email' => $this->user->email,
        'otp' => '482910',
    ]);

    $record = DB::table('password_reset_tokens')->where('email', $this->user->email)->first();
    expect($record->verified_at)->not->toBeNull();
    expect($record->token)->not->toBe('init-token-123');

    $response->assertRedirect(route('password.reset', ['token' => $record->token, 'email' => $this->user->email]));
});

test('halaman reset password menolak akses jika OTP belum divalidasi dengan benar', function () {
    // Tanpa record token apapun
    $response = $this->get(route('password.reset', ['token' => 'invalid-token', 'email' => $this->user->email]));
    $response->assertRedirect(route('password.request'));
    $response->assertSessionHasErrors(['email']);

    // Ada record tapi verified_at masih null
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => 'unverified-token',
            'otp' => '112233',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => null,
        ]
    );

    $response2 = $this->get(route('password.reset', ['token' => 'unverified-token', 'email' => $this->user->email]));
    $response2->assertRedirect(route('password.request'));
});

test('halaman reset password dapat diakses setelah OTP berhasil divalidasi', function () {
    $verifiedToken = 'verified-secret-token-12345';
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => $verifiedToken,
            'otp' => '112233',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => Carbon::now(),
        ]
    );

    $response = $this->get(route('password.reset', ['token' => $verifiedToken, 'email' => $this->user->email]));
    $response->assertOk();
    $response->assertSee('Buat Password Baru');
    $response->assertSee('Password Baru');
    $response->assertSee('Konfirmasi Password Baru');
});

test('update password menolak jika konfirmasi password tidak cocok atau kurang dari 8 karakter', function () {
    $verifiedToken = 'verified-token-xyz';
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => $verifiedToken,
            'otp' => '123456',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => Carbon::now(),
        ]
    );

    // Kurang dari 8 karakter
    $responseShort = $this->post(route('password.update'), [
        'token' => $verifiedToken,
        'email' => $this->user->email,
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);
    $responseShort->assertSessionHasErrors(['password']);

    // Mismatched confirmation
    $responseMismatch = $this->post(route('password.update'), [
        'token' => $verifiedToken,
        'email' => $this->user->email,
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);
    $responseMismatch->assertSessionHasErrors(['password']);
});

test('update password berhasil meng-update password di database, menghapus OTP, dan redirect ke halaman login dengan pesan sukses', function () {
    $verifiedToken = 'verified-token-final';
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $this->user->email],
        [
            'token' => $verifiedToken,
            'otp' => '987654',
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15),
            'verified_at' => Carbon::now(),
        ]
    );

    $response = $this->post(route('password.update'), [
        'token' => $verifiedToken,
        'email' => $this->user->email,
        'password' => 'BrandNewPassword2026!',
        'password_confirmation' => 'BrandNewPassword2026!',
    ]);

    $response->assertRedirect(route('user.login'));
    $response->assertSessionHas('status', 'Password berhasil diubah! Silakan masuk dengan password baru Anda.');

    // Password ter-hash dan ter-update di database
    $this->user->refresh();
    expect(Hash::check('BrandNewPassword2026!', $this->user->password))->toBeTrue();
    expect(Hash::check('OldPassword123!', $this->user->password))->toBeFalse();

    // Record OTP di database telah dihapus / di-invalidate
    expect(DB::table('password_reset_tokens')->where('email', $this->user->email)->count())->toBe(0);

    // Percobaan memakai token lagi harus ditolak
    $reuseAttempt = $this->post(route('password.update'), [
        'token' => $verifiedToken,
        'email' => $this->user->email,
        'password' => 'AnotherPassword2026!',
        'password_confirmation' => 'AnotherPassword2026!',
    ]);
    $reuseAttempt->assertRedirect(route('password.request'));
});
