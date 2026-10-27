<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BookingAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

/**
 * Passenger login with a Google account. Only passenger accounts (role "user") can use it;
 * staff keep their own login with an access code.
 */
class GoogleLoginController extends Controller
{
    public function redirect(Request $request): SymfonyRedirectResponse
    {
        if (! $this->isConfigured()) {
            Log::warning('Login Google dipanggil tetapi GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET belum diisi.');

            return redirect()->route('user.login')
                ->withErrors(['google' => 'Masuk dengan Google belum bisa dipakai saat ini. Masuk dengan email dan password dulu, ya.']);
        }

        $redirect = $request->query('redirect');

        if (is_string($redirect) && Str::startsWith($redirect, url('/'))) {
            $request->session()->put('url.intended', $redirect);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, BookingAccess $bookingAccess): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('user.login');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::info('Login Google gagal', ['message' => $exception->getMessage()]);

            return redirect()->route('user.login')
                ->withErrors(['google' => 'Login dengan Google dibatalkan atau gagal. Coba lagi, atau masuk dengan email.']);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('user.login')
                ->withErrors(['google' => 'Akun Google ini tidak membagikan alamat email, jadi belum bisa dipakai untuk masuk.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if ($user && $user->role !== 'user') {
            return redirect()->route('user.login')
                ->withErrors(['google' => 'Email ini terdaftar sebagai akun petugas. Masuk lewat halaman login petugas.']);
        }

        if ($user && in_array($user->status, ['inactive', 'blocked'], true)) {
            return redirect()->route('user.login')
                ->withErrors(['google' => 'akun anda diblokir karena suatu kesalahan']);
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'password' => Str::password(32),
                'role' => 'user',
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_device' => $request->userAgent(),
        ]);

        // Google has verified this email, so earlier guest bookings made with it can move to the account too.
        $bookingAccess->claimFor($user);
        $bookingAccess->claimByVerifiedEmail($user);

        return redirect()->intended(route('dashboard.user.index'));
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }
}
