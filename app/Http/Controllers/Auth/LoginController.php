<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function create()
    {
        return $this->showLogin();
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        return $this->login($request);
    }

    public function login(Request $request)
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

            'role' => [
                'required',
                'in:admin_utama,staff,cs,driver',
            ],

            'access_code' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'role.required' => 'Silakan pilih role.',
            'access_code.required' => 'Kode akses wajib diisi.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cari user berdasarkan email
        |--------------------------------------------------------------------------
        */

        $user = \App\Models\User::where(
            'email',
            $credentials['email']
        )->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi role
        |--------------------------------------------------------------------------
        */

        if ($user->role !== $credentials['role']) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'role' => 'Role yang dipilih tidak sesuai dengan akun Anda.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi kode akses
        |--------------------------------------------------------------------------
        */

        if (
            empty($user->access_code) ||
            !Hash::check(
                $credentials['access_code'],
                $user->access_code
            )
        ) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'access_code' => 'Kode akses tidak valid.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Login berhasil
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        return match ($user->role) {

            'admin_utama' => redirect()->route(
                'dashboard.admin.index'
            ),

            'staff' => redirect()->route(
                'dashboard.admin-biasa.index'
            ),

            'cs' => redirect()->route(
                'dashboard.customer-service.index'
            ),

            'driver' => redirect()->route(
                'dashboard.driver.index'
            ),

            default => redirect()->route('login')
                ->withErrors([
                    'role' => 'Role akun tidak dikenali.',
                ]),
        };
    }

    public function destroy(Request $request)
    {
        return $this->logout($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}