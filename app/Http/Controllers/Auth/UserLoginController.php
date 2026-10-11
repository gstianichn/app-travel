<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserLoginController extends Controller
{
    /**
     * Menampilkan halaman login khusus User.
     */
    public function create(Request $request): View
    {
        // Pages such as search results send ?redirect= so the passenger lands back where they were.
        $redirect = $request->query('redirect');

        if (is_string($redirect) && Str::startsWith($redirect, url('/'))) {
            $request->session()->put('url.intended', $redirect);
        }

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
            ], $request->boolean('remember'))
        ) {
            $request->session()->regenerate();

            $request->user()->update([
                'last_login_at' => now(),
                'last_login_device' => $request->userAgent(),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);

            return redirect()->intended(route('dashboard.user.index'));
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
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
