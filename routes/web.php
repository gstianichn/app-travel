<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\AdminBiasaDashboardController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\CustomerServiceDashboardController;
use App\Http\Controllers\UserDashboardController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');


/*
|--------------------------------------------------------------------------
| Admin Utama
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin_utama'])
    ->group(function () {

        Route::get('/dashboard/admin', [
            AdminDashboardController::class,
            'index'
        ])->name('dashboard.admin.index');

        Route::get( '/kelola-user', [
            AdminDashboardController::class,
            'users'
        ])->name('dashboard.admin.kelola-user.index');

        Route::get( '/kelola-user/create', [
            AdminDashboardController::class,
            'createUser'
        ])->name('dashboard.admin.kelola-user.create');

        Route::post( '/kelola-user', [
            AdminDashboardController::class,
            'storeUser'
        ])->name('dashboard.admin.kelola-user.store');

        Route::get( '/kelola-user/{user}', [
            AdminDashboardController::class,
            'showUser'
        ])->name('dashboard.admin.kelola-user.show');
        
        Route::get( '/kelola-user/{user}/edit', [
            AdminDashboardController::class,
            'editUser'
        ])->name('dashboard.admin.kelola-user.edit');
        
        Route::put( '/kelola-user/{user}', [
            AdminDashboardController::class,
            'updateUser'
        ])->name('dashboard.admin.kelola-user.update');
        
        Route::patch( '/kelola-user/{user}/toggle-status', [
            AdminDashboardController::class,
            'toggleUserStatus'
        ])->name('dashboard.admin.kelola-user.toggle-status');
        
        Route::delete( '/kelola-user/{user}', [
            AdminDashboardController::class,
            'destroyUser'
        ])->name('dashboard.admin.kelola-user.destroy');

    });


/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])
    ->group(function () {

        Route::get('/admin/dashboard', [
            AdminBiasaDashboardController::class,
            'index'
        ])->name('dashboard.admin-biasa.index');

    });


/*
|--------------------------------------------------------------------------
| Driver
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:driver'])
    ->group(function () {

        Route::get('/driver/dashboard', [
            DriverDashboardController::class,
            'index'
        ])->name('dashboard.driver.index');

    });


/*
|--------------------------------------------------------------------------
| Customer Service
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:cs'])
    ->group(function () {

        Route::get('/customer-service/dashboard', [
            CustomerServiceDashboardController::class,
            'index'
        ])->name('dashboard.customer-service.index');

        Route::get('/customer-service/live-chat', [
            CustomerServiceDashboardController::class,
            'liveChat'
        ])->name('dashboard.customer-service.live-chat');

    });


/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->group(function () {

        Route::get('/user/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.index');

        Route::get('/booking/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.user-booking');

        Route::get('/history/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.user-history');

        Route::get('/refund/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.user-refund');

        Route::get('/promo/dashboard', [
            UserDashboardController::class,
            'index'
        ])->name('dashboard.user.user-promo');

    });