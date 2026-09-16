<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

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

        $user = User::where(
            'email',
            $credentials['email']
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Cek akun yang sedang terkunci
        |--------------------------------------------------------------------------
        */
        if ($user && $user->locked_until) {

            if ($user->locked_until->isFuture()) {
                return back()
                    ->withErrors([
                        'email' => 'Akun Anda terkunci sementara.',
                    ])
                    ->onlyInput('email');
            }

            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Proses Login
        |--------------------------------------------------------------------------
        */
        if (! Auth::attempt($credentials)) {

            if ($user) {

                $attempts = $user->failed_login_attempts + 1;

                $data = [
                    'failed_login_attempts' => $attempts,
                ];

                /*
                |--------------------------------------------------------------------------
                | Kunci akun setelah 5 kali gagal
                |--------------------------------------------------------------------------
                */
                if ($attempts >= 5) {
                    $data['locked_until'] = now()->addMinutes(15);
                    $data['failed_login_attempts'] = 0;
                }

                $user->update($data);
            }

            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */
        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Update Informasi Login
        |--------------------------------------------------------------------------
        */
        $request->user()->update([
            'last_login_at' => now(),
            'last_login_device' => $request->userAgent(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect Berdasarkan Role
        |--------------------------------------------------------------------------
        */
        return $this->redirectBasedOnRole($request->user());
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect Dashboard Berdasarkan Role
    |--------------------------------------------------------------------------
    */
    private function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {

            'admin' => redirect()->route(
                'dashboard.admin.index'
            ),

            'admin_staff' => redirect()->route(
                'dashboard.admin-biasa.index'
            ),

            'driver' => redirect()->route(
                'dashboard.driver.index'
            ),

            'customer_service' => redirect()->route(
                'dashboard.customer-service.index'
            ),

            'user' => redirect()->route(
                'dashboard.user.index'
            ),

            default => redirect()->route('login')
                ->withErrors([
                    'email' => 'Role akun tidak dikenali.',
                ]),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
