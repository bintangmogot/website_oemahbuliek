<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboard;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;

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


// Halaman index
Route::get('/', [HomeController::class, 'index']);

Route::middleware('guest')->group(function(){
    Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class,'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// GROUP UNTUK SEMUA ROUTE YANG BUTUH AUTHENTIKASI
// Group untuk admin
Route::middleware(['auth','role:admin'])
     ->prefix('dashboard')
     ->name('admin.')            // prefix nama route
     ->group(function () {

// CRUD User
    Route::resource('user', UserController::class)
         ->except(['show'])   // kita tidak butuh view detail
         ->names([
             'index'   => 'user.index',
             'create'  => 'user.create',
             'store'   => 'user.store',
             'edit'    => 'user.edit',
             'update'  => 'user.update',
             'destroy' => 'user.destroy',
         ]);

    // Resource pegawai → otomatis index,create,store,edit,update,destroy
    Route::resource('pegawai', PegawaiController::class);

    // User pegawai
    Route::get('user/create', [UserController::class,'create'])->name('user.create');
    Route::post('user',       [UserController::class,'store'])->name('user.store');
});

// Group untuk admin
Route::middleware(['auth','role:admin'])
     ->prefix('dashboard')
     ->name('admin.')
     ->group(function() {
    Route::get('/user/profile', [UserController::class,'showSelf'])->name('profile');
});

// Group untuk pegawai
Route::middleware(['auth','role:pegawai'])
     ->prefix('dashboard')
     ->name('pegawai.')
     ->group(function() {
    Route::get('profile', [PegawaiController::class,'showSelf'])->name('profile');
});


//DASHBOARD
// Route untuk dashboard yang berbeda berdasarkan role
Route::get('/dashboard', function () {
    if (!session()->has('email') || !session()->has('nama_user')) {
        return redirect('/login');
    }

    // Ambil data user berdasarkan email yang disimpan di session
    $email = session('email');
    $user = \App\Models\User::where('email', $email)->first();

    if (!$user) {
        abort(403, 'Akun tidak ditemukan.');
    }

    // Cek role dan tampilkan view sesuai role
    if ($user->role === 'admin') {
        return view('dashboard.admin');
    } elseif ($user->role === 'pegawai') {
        return view('dashboard.pegawai');
    }

    abort(403, 'Role tidak dikenali.');
});
