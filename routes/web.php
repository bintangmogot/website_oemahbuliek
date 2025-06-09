<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboard;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/test-role', function () {
$users = DB::table('users')->where('email', 'admin@resto.test')->get();
    

    // return $this->role === 'admin';
    print_r(  $users[0]->role);
});


// Halaman index
Route::get('/', [HomeController::class, 'index']);

Route::middleware('guest')->group(function(){
    Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class,'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard Admin
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function(){
    Route::get('/', [AdminDashboard::class,'index']);
});

// Dashboard Pegawai
Route::middleware(['auth','role:pegawai'])->prefix('pegawai')->group(function(){
    Route::get('/', [PegawaiDashboard::class,'index']);
});

