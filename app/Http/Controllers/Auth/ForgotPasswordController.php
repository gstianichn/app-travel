<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        // Proses OTP/reset password akan dibuat di tahap berikutnya.

        return back()->with(
            'status',
            'Permintaan reset password berhasil dikirim.'
        );
    }
}