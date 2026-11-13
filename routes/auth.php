<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login Umum
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [
        LoginController::class,
        'create',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'store',
    ])->name('login.process');

    /*
    |--------------------------------------------------------------------------
    | Login Khusus User
    |--------------------------------------------------------------------------
    */

    Route::get('/user/login', [
        UserLoginController::class,
        'create',
    ])->name('user.login');

    Route::post('/user/login', [
        UserLoginController::class,
        'store',
    ])->name('user.login.store');

    /*
    |--------------------------------------------------------------------------
    | Login User dengan Google
    |--------------------------------------------------------------------------
    */

    Route::get('/auth/google', [
        GoogleLoginController::class,
        'redirect',
    ])->name('auth.google.redirect');

    Route::get('/auth/google/choose', [
        GoogleLoginController::class,
        'showChooser',
    ])->name('auth.google.choose');

    Route::post('/auth/google/verify', [
        GoogleLoginController::class,
        'verifyLocalChooser',
    ])->name('auth.google.verify');

    Route::get('/auth/google/callback', [
        GoogleLoginController::class,
        'callback',
    ])->name('auth.google.callback');

    /*
    |--------------------------------------------------------------------------
    | Register User
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [
        RegisterController::class,
        'create',
    ])->name('register');

    Route::post('/register', [
        RegisterController::class,
        'store',
    ])->name('register.store');

    Route::post('/user/register', [
        RegisterController::class,
        'store',
    ])->name('user.register');

    /*
    |--------------------------------------------------------------------------
    | Forgot & Reset Password (OTP Flow)
    |--------------------------------------------------------------------------
    */

    // Menampilkan halaman Forgot Password (Input Email)
    Route::get('/forgot-password', [
        ForgotPasswordController::class,
        'create',
    ])->name('password.request');

    // Endpoint Request OTP
    Route::post('/forgot-password', [
        ForgotPasswordController::class,
        'store',
    ])->name('password.email');

    // Halaman Verifikasi OTP
    Route::get('/forgot-password/verify', [
        ForgotPasswordController::class,
        'showVerifyOtp',
    ])->name('password.otp.verify');

    // Endpoint Validasi OTP
    Route::post('/forgot-password/verify', [
        ForgotPasswordController::class,
        'verifyOtp',
    ])->name('password.otp.check');

    // Kirim Ulang OTP
    Route::post('/forgot-password/resend-otp', [
        ForgotPasswordController::class,
        'resendOtp',
    ])->name('password.otp.resend');

});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

// Logout Login Umum
Route::post('/logout', [
    LoginController::class,
    'destroy',
])
    ->middleware('auth')
    ->name('logout');

// Logout User
Route::post('/user/logout', [
    UserLoginController::class,
    'destroy',
])
    ->middleware(['auth', 'role:user'])
    ->name('user.logout');
