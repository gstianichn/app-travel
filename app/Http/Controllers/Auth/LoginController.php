<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $user = User::where(
            'email',
            $credentials['email']
        )->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
        }

        if (in_array($user->status, ['inactive', 'blocked'], true)) {
            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi password
        |--------------------------------------------------------------------------
        */

        $isPasswordValid = false;
        if (password_get_info((string) $user->password)['algoName'] === 'bcrypt') {
            $isPasswordValid = Hash::check(
                $credentials['password'],
                $user->password
            );
        } elseif ((string) $user->password === (string) $credentials['password']) {
            $isPasswordValid = true;
            $user->password = Hash::make($credentials['password']);
            $user->save();
        }

        if (! $isPasswordValid) {
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

        $isAccessCodeValid = false;
        if (! empty($user->access_code)) {
            if (password_get_info((string) $user->access_code)['algoName'] === 'bcrypt') {
                $isAccessCodeValid = Hash::check(
                    $credentials['access_code'],
                    $user->access_code
                );
            } elseif ((string) $user->access_code === (string) $credentials['access_code']) {
                $isAccessCodeValid = true;
                $user->access_code = Hash::make($credentials['access_code']);
                $user->save();
            }
        }

        if (! $isAccessCodeValid) {
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

        Auth::login($user, $request->boolean('remember'));

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
