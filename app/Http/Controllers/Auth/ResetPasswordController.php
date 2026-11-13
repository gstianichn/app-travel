<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Menampilkan form reset password.
     * Halaman ini hanya bisa diakses jika OTP divalidasi dengan benar.
     */
    public function create(Request $request, ?string $token = null)
    {
        $token = $token ?: (string) $request->query('token', session('password_reset_token'));
        $email = strtolower(trim((string) $request->query('email', session('password_reset_verified_email'))));

        $record = null;
        if ($email && $token) {
            $record = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();
        }

        $isValid = $record
            && hash_equals((string) $record->token, (string) $token)
            && ! empty($record->verified_at)
            && Carbon::parse($record->expires_at)->isFuture();

        if (! $isValid) {
            return redirect()->route('password.request')
                ->withErrors([
                    'email' => 'Halaman reset password hanya dapat diakses setelah kode OTP berhasil diverifikasi. Silakan masukkan email Anda.',
                ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Endpoint Update Password:
     * Terima password baru, validasi (min length, confirmation matching),
     * hashing password baru, update ke database, lalu hapus/invalidate kode OTP.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'Token verifikasi tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        $email = strtolower(trim($validated['email']));
        $token = trim($validated['token']);

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        $isValid = $record
            && hash_equals((string) $record->token, (string) $token)
            && ! empty($record->verified_at)
            && Carbon::parse($record->expires_at)->isFuture();

        if (! $isValid) {
            $errorMessage = 'Sesi verifikasi OTP tidak valid atau telah kedaluwarsa. Silakan lakukan proses verifikasi ulang.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['password' => [$errorMessage]],
                ], 422);
            }

            return redirect()->route('password.request')
                ->withErrors(['email' => $errorMessage]);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            $errorMessage = 'Pengguna dengan email tersebut tidak ditemukan.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['email' => [$errorMessage]],
                ], 422);
            }

            return back()->withErrors(['email' => $errorMessage]);
        }

        // Hashing password baru dan update ke database
        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        // Hapus / Invalidate kode OTP agar tidak bisa digunakan ulang
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['password_reset_verified_email', 'password_reset_token', 'reset_email', 'otp_sent_to']);

        $successMessage = 'Password berhasil diubah! Silakan masuk dengan password baru Anda.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect_url' => route('user.login'),
            ]);
        }

        return redirect()->route('user.login')
            ->with('status', $successMessage);
    }
}
