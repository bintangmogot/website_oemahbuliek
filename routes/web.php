<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Publik
Route::get('/', [HomeController::class, 'index']);

// Auth: login & logout
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class,'login']);
});
Route::post('logout', [LoginController::class,'logout'])
    ->middleware('auth')
    ->name('logout');

// Dashboard (setelah login, redirect ke view sesuai role)
Route::middleware('auth')->get('dashboard', function () {
    $user = Auth::user();
    return $user->role === 'admin'
        ? view('dashboard.admin')
        : view('dashboard.pegawai');
        })->name('dashboard');

    // Admin-only routes
    Route::middleware(['auth','role:admin'])
        ->prefix('dashboard')
        ->name('admin.')
        ->group(function () {


        // CRUD Users (termasuk data pegawai)
    Route::resource('user', UserController::class)
        ->names([
        'index'   => 'user.index',
        'show'    => 'user.show',
        'create'  => 'user.create',
        'store'   => 'user.store',
        'edit'    => 'user.edit',
        'update'  => 'user.update',
        'destroy' => 'user.destroy',
        ]); 
        });

// Profil (bisa diakses semua yang sudah login)
Route::middleware('auth')
    ->prefix('dashboard')
    ->name('profile.')
    ->group(function () {
        Route::get('profile', [UserController::class, 'showSelf'])
            ->name('me');
    });