<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GoogleAccountVerifiedNotification;
use App\Services\BookingAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
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

        if ($this->usesLocalGoogleChooser()) {
            return redirect()->route('auth.google.choose');
        }

        return Socialite::driver('google')->redirect();
    }

    public function showChooser(): View|RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('user.login');
        }

        $suggestedAccounts = User::where('role', 'user')
            ->where('status', 'active')
            ->orderByDesc('last_login_at')
            ->take(3)
            ->get(['name', 'email']);

        return view('auth.google-choose', compact('suggestedAccounts'));
    }

    public function verifyLocalChooser(Request $request, BookingAccess $bookingAccess): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $email = strtolower(trim($validated['email']));
        $name = trim((string) ($validated['name'] ?? '')) ?: Str::headline(Str::before($email, '@'));
        $googleId = 'google-'.substr(sha1($email), 0, 16);

        return $this->completeGoogleSignIn($request, $bookingAccess, $email, $googleId, $name);
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

        return $this->completeGoogleSignIn(
            $request,
            $bookingAccess,
            $email,
            (string) $googleUser->getId(),
            $googleUser->getName() ?: Str::before($email, '@')
        );
    }

    private function completeGoogleSignIn(
        Request $request,
        BookingAccess $bookingAccess,
        string $email,
        string $googleId,
        string $name
    ): RedirectResponse {
        $user = User::where('google_id', $googleId)->first()
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
                'name' => $name ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleId,
                'password' => Str::password(32),
                'role' => 'user',
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $googleId]);
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

        try {
            $user->notify(new GoogleAccountVerifiedNotification(
                gmailAddress: $user->email,
                loginTime: now()->locale('id')->translatedFormat('d M Y, H:i').' WIB',
                deviceInfo: Str::limit((string) ($request->userAgent() ?: 'Browser Web'), 80),
            ));
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim notifikasi verifikasi Google ke email', ['error' => $e->getMessage()]);
        }

        return redirect()->intended(route('dashboard.user.index'))
            ->with('success', "Berhasil masuk dengan akun Google ({$user->email}). Notifikasi verifikasi telah dikirim ke Gmail Anda.");
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    private function usesLocalGoogleChooser(): bool
    {
        return config('services.google.client_id') === 'google-oauth-local';
    }
}
