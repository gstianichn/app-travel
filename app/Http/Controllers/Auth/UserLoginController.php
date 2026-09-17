<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    /**
     * Menampilkan halaman login khusus User.
     */
    public function create()
    {
        return view('auth.user-login');
    }

    /**
     * Proses login khusus User.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login hanya untuk role user
        |--------------------------------------------------------------------------
        */
        if (
            Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'role' => 'user',
            ])
        ) {
            $request->session()->regenerate();

            $request->user()->update([
                'last_login_at' => now(),
                'last_login_device' => $request->userAgent(),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);

            return redirect()->route('dashboard.user.index');
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password User tidak sesuai.',
            ])
            ->onlyInput('email');
    }

    /**
     * Logout User.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
