<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BookingAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        // Same as the login page: ?redirect= brings the passenger back to the booking they were on.
        $redirect = $request->query('redirect');

        if (is_string($redirect) && Str::startsWith($redirect, url('/'))) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.register');
    }

    public function store(Request $request, BookingAccess $bookingAccess)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'user',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        $bookingAccess->claimFor($user);

        return redirect()->intended(route('dashboard.user.index'));
    }
}
