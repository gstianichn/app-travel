<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        // Pastikan user sudah login
        if (! $request->user()) {
            abort(401);
        }

        // Pastikan status akun aktif (bukan inactive atau blocked)
        if (in_array($request->user()->status, ['inactive', 'blocked'], true)) {
            $isUser = $request->user()->role === 'user';
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($isUser) {
                return redirect()->route('user.login')->withErrors([
                    'email' => 'akun anda diblokir karena suatu kesalahan',
                ]);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        // Ambil role dari user yang sedang login
        $userRole = $request->user()->role;

        // Jika tidak ada role yang diberikan pada middleware
        if (empty($roles)) {
            abort(403, 'Role middleware belum dikonfigurasi.');
        }

        // Cek apakah role user termasuk role yang diizinkan
        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
