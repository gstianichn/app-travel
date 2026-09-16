<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\AdminBiasaDashboardController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\CustomerServiceDashboardController;
use App\Http\Controllers\UserDashboardController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');

require __DIR__.'/auth.php';

// Route::post('/logout', function () {
//     Auth::logout();

//     request()->session()->invalidate();
//     request()->session()->regenerateToken();

//     return redirect('/login');
// })->name('logout');

// Route::middleware('auth')->group(function () {

//     Route::get('/dashboard', [DashboardController::class,'index'
//     ])->name('dashboard');
// });

Route::middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard/admin', [
            AdminDashboardController::class,
            'index'
        ])->name('dashboard.admin.index');

    });


Route::middleware(['auth', 'role:admin_staff'])
    ->group(function () {

        Route::get('/admin/dashboard', [
            AdminBiasaDashboardController::class,
            'index'
        ])->name('dashboard.admin-biasa.index');

    });


Route::middleware(['auth', 'role:driver'])
    ->group(function () {

        Route::get('/driver/dashboard', [
            DriverDashboardController::class,
            'index'
        ])->name('dashboard.driver.index');

    });


Route::middleware(['auth', 'role:customer_service'])
    ->group(function () {

        Route::get('/customer-service/dashboard', [
            CustomerServiceDashboardController::class,
            'index'
        ])->name('dashboard.customer-service.index');

    });


Route::middleware(['auth', 'role:user'])
    ->group(function () {

        Route::get('/user/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.index');

    });



