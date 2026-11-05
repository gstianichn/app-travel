<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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

        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return back()
            ->with('status', "Tautan verifikasi reset password telah dikirim ke akun Google ({$user->email}). Silakan cek kotak masuk Gmail Anda.")
            ->with('reset_email_sent_to', $user->email)
            ->with('reset_preview_url', ! app()->isProduction() ? $resetUrl : null);
    }
}
