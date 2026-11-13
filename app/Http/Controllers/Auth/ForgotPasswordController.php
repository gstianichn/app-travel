<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan form lupa password.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Endpoint Request OTP:
     * Validasi input email, generate 6 digit OTP acak, simpan ke database (expired 15 menit),
     * dan kirim OTP ke email user melalui SMTP.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $email = strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (! $user) {
            $errorMessage = 'Email tersebut belum terdaftar. Pastikan Anda memasukkan alamat email yang terdaftar di TravelKu.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['email' => [$errorMessage]],
                ], 422);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $errorMessage]);
        }

        if (in_array($user->status, ['inactive', 'blocked'], true)) {
            $errorMessage = 'Akun Anda sedang dinonaktifkan atau diblokir. Hubungi Customer Service untuk bantuan.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['email' => [$errorMessage]],
                ], 422);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $errorMessage]);
        }

        // Generate 6 digit OTP acak
        $otp = sprintf('%06d', random_int(0, 999999));
        $initialToken = Str::random(60);
        $expiresAt = Carbon::now()->addMinutes(15);

        // Simpan OTP dan waktu kedaluwarsa 15 menit ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $initialToken,
                'otp' => $otp,
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'verified_at' => null,
            ]
        );

        // Kirim OTP via SMTP Mail
        $mailSent = false;
        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($user, $otp, 15));
            $mailSent = true;
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim email OTP reset password ke pengguna', [
                'email' => $user->email,
                'mailer' => config('mail.default'),
                'message' => $exception->getMessage(),
            ]);
        }

        $isLocalMailer = in_array((string) config('mail.default'), ['log', 'array'], true);
        $statusMessage = $isLocalMailer
            ? "Kode verifikasi (OTP) 6 digit telah dibuat untuk ({$user->email}). Catatan: Driver mail lokal ('".config('mail.default')."') aktif; OTP dapat dicek pada log atau preview di bawah."
            : "Kode verifikasi (OTP) 6 digit telah dikirim ke email {$user->email}. Kode berlaku selama 15 menit.";

        session([
            'reset_email' => $user->email,
            'otp_sent_to' => $user->email,
        ]);

        $otpPreview = (! app()->isProduction() || $isLocalMailer) ? $otp : null;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $statusMessage,
                'email' => $user->email,
                'mail_sent' => $mailSent,
                'redirect_url' => route('password.otp.verify', ['email' => $user->email]),
                'otp_preview' => $otpPreview,
            ]);
        }

        return redirect()->route('password.otp.verify', ['email' => $user->email])
            ->with('status', $statusMessage)
            ->with('otp_sent_to', $user->email)
            ->with('otp_preview', $otpPreview);
    }

    /**
     * Halaman Verifikasi OTP:
     * Menampilkan form input kode verifikasi 6 digit angka.
     */
    public function showVerifyOtp(Request $request)
    {
        $email = strtolower(trim($request->query('email', (string) (session('otp_sent_to') ?: session('reset_email')))));

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Silakan masukkan email Anda terlebih dahulu untuk meminta kode OTP.']);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();
        if (! $record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Permintaan reset password belum ditemukan. Silakan masukkan email Anda.']);
        }

        return view('auth.verify-otp', [
            'email' => $email,
            'expiresAt' => $record->expires_at ? Carbon::parse($record->expires_at) : null,
            'otpPreview' => session('otp_preview') ?: ((! app()->isProduction() || in_array((string) config('mail.default'), ['log', 'array'], true)) ? $record->otp : null),
        ]);
    }

    /**
     * Endpoint Validasi OTP:
     * Cek apakah kombinasi email dan OTP valid, serta pastikan batas waktu 15 menit belum terlewati.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'digits:6'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'otp.required' => 'Kode OTP 6 digit wajib diisi.',
            'otp.digits' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            $errorMessage = 'Permintaan reset password tidak ditemukan. Silakan kirim ulang kode OTP.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['otp' => [$errorMessage]],
                ], 422);
            }

            return back()
                ->withInput($request->only('email', 'otp'))
                ->withErrors(['otp' => $errorMessage]);
        }

        // Cek kedaluwarsa 15 menit
        if ($record->expires_at && Carbon::parse($record->expires_at)->isPast()) {
            $errorMessage = 'Kode OTP telah kedaluwarsa (melebihi batas waktu 15 menit). Silakan minta kode OTP baru.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'expired' => true,
                    'errors' => ['otp' => [$errorMessage]],
                ], 422);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['otp' => $errorMessage]);
        }

        // Cek kecocokan OTP
        if ((string) $record->otp !== (string) $otp) {
            $errorMessage = 'Kode verifikasi (OTP) tidak sesuai. Pastikan 6 digit angka yang Anda masukkan benar.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['otp' => [$errorMessage]],
                ], 422);
            }

            return back()
                ->withInput($request->only('email', 'otp'))
                ->withErrors(['otp' => $errorMessage]);
        }

        // OTP Valid: buat verified reset token dan tandai verified_at
        $verifiedToken = Str::random(64);
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => $verifiedToken,
                'verified_at' => Carbon::now(),
            ]);

        session([
            'password_reset_verified_email' => $email,
            'password_reset_token' => $verifiedToken,
        ]);

        $resetUrl = route('password.reset', [
            'token' => $verifiedToken,
            'email' => $email,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP berhasil diverifikasi.',
                'token' => $verifiedToken,
                'redirect_url' => $resetUrl,
            ]);
        }

        return redirect()->to($resetUrl)
            ->with('status', 'Kode OTP berhasil diverifikasi. Silakan atur password baru Anda.');
    }

    /**
     * Kirim ulang kode OTP.
     */
    public function resendOtp(Request $request)
    {
        return $this->store($request);
    }
}
