<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
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
     * Kirim link reset password ke email Google/Gmail yang bersangkutan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email tersebut belum terdaftar. Pastikan Anda memasukkan alamat email Google yang terdaftar di TravelKu.',
                ]);
        }

        if (in_array($user->status, ['inactive', 'blocked'], true)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan atau diblokir. Hubungi Customer Service untuk bantuan.',
                ]);
        }

        $token = Password::broker()->createToken($user);

        try {
            $user->sendPasswordResetNotification($token);
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim email reset password ke Gmail', [
                'email' => $user->email,
                'mailer' => config('mail.default'),
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Gagal mengirim email reset password ke server Gmail. Periksa konfigurasi SMTP Gmail (MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, dan Google App Password) atau masuk langsung memakai tombol Google di bawah.',
                ]);
        }

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $isLocalMailer = in_array((string) config('mail.default'), ['log', 'array'], true);
        $statusMessage = $isLocalMailer
            ? "Tautan verifikasi reset password untuk ({$user->email}) telah dibuat. Catatan: Server masih memakai driver email lokal ('".config('mail.default')."'); agar masuk ke kotak masuk Gmail sungguhan, aktifkan SMTP Gmail di .env."
            : "Tautan verifikasi reset password telah dikirim ke akun Google ({$user->email}). Silakan cek kotak masuk atau folder Spam Gmail Anda.";

        return back()
            ->with('status', $statusMessage)
            ->with('reset_email_sent_to', $user->email)
            ->with('mail_driver_is_local', $isLocalMailer)
            ->with('reset_preview_url', ! app()->isProduction() ? $resetUrl : null);
    }
}
