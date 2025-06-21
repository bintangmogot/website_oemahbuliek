<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\JadwalShiftController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PegawaiJadwalController;
use App\Models\JadwalShift;

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

// Semua route di bawah ini sudah di‑prefix dashboard
Route::prefix('dashboard')->middleware('auth')->group(function () {        
// TABEL USERS
// CRUD Users (termasuk data pegawai)
    Route::middleware('role:admin')->group(function(){
        Route::get('user',                     [UserController::class,'index'])->name('user.index');
        Route::get('user/create',              [UserController::class,'create'])->name('user.create');
        Route::post('user',                    [UserController::class,'store'])->name('user.store');
        Route::get('user/{user}',              [UserController::class,'show'])->name('user.show');
        Route::get('user/{user}/edit',         [UserController::class,'edit'])->name('user.edit');
        Route::put('user/{user}',              [UserController::class,'update'])->name('user.update');
        Route::delete('user/{user}',           [UserController::class,'destroy'])->name('user.destroy');
    });
        
// Profil (bisa diakses semua yang sudah login)
Route::middleware('role:admin|pegawai')->name('profile.')->group(function () {
        Route::get('profile', [UserController::class, 'showSelf'])->name('me');
    });

    // TABEL PRESENSI
// ————— Khusus presensi pegawai bisa semua kecuali hapus —————
Route::middleware('role:admin|pegawai')->name('presensi.')->group(function () {
        Route::get('presensi',                     [PresensiController::class,'index'])->name('index');
        Route::get('presensi/create',              [PresensiController::class,'create'])->name('create');
        Route::post('presensi',                    [PresensiController::class,'store'])->name('store');
        Route::get('presensi/{presensi}',              [PresensiController::class,'show'])->name('show');
        Route::get('presensi/{presensi}/edit',         [PresensiController::class,'edit'])->name('edit');
        Route::put('presensi/{presensi}',              [PresensiController::class,'update'])->name('update');
     });
// ————— ADMIN‐ONLY (create/store/edit/update/destroy) —————
    Route::middleware('role:admin')->name('presensi')->group(function(){
        Route::delete('presensi/{presensi}',           [PresensiController::class,'destroy'])->name('destroy');
    });


// ————— TABEL JADWAL SHIFTS —————

// Admin‑only (create, store, edit, update, destroy)
Route::middleware('role:admin')->name('jadwal.')->group(function () {
        Route::get('jadwal-shift/create',              [JadwalShiftController::class,'create'])->name('create');
        Route::post('jadwal-shift',                    [JadwalShiftController::class,'store'])->name('store');
        Route::get('jadwal-shift/{jadwal_shift}/edit', [JadwalShiftController::class,'edit'])->name('edit');
        Route::put('jadwal-shift/{jadwal_shift}',      [JadwalShiftController::class,'update'])->name('update');
        Route::delete('jadwal-shift/{jadwal_shift}',   [JadwalShiftController::class, 'destroy'])->name('destroy');
        
     });
     
// View‑only (index & show)
Route::middleware('role:admin|pegawai')->name('jadwal.')->group(function () {
         Route::get('jadwal-shift',                [JadwalShiftController::class,'index'])->name('index');
         Route::get('jadwal-shift/{jadwal_shift}', [JadwalShiftController::class,'show'])->name('show');
     });

// Matrix UI Routes
Route::get('jadwal-shift/bulk-create-matrix', [JadwalShiftController::class, 'bulkCreateMatrix'])
     ->name('jadwal-shift.bulk-create-matrix');

Route::post('jadwal-shift/bulk-store-matrix', [JadwalShiftController::class, 'bulkStoreMatrix'])
     ->name('jadwal-shift.bulk-store-matrix');

// Optional: AJAX route untuk load data bulan lain
Route::get('jadwal-shift/matrix-data', [JadwalShiftController::class, 'getJadwalMatrix'])
     ->name('jadwal-shift.matrix-data');


// ————— TABEL SHIFTS —————

// Admin‑only (create, store, edit, update, destroy)
Route::middleware('role:admin')->name('shift.')->group(function () {
    Route::get('shift/create',              [ShiftController::class,'create'])->name('create');
    Route::post('shift',                    [ShiftController::class,'store'])->name('store');
    Route::get('shift/{shift}/edit',        [ShiftController::class,'edit'])->name('edit');
    Route::put('shift/{shift}',             [ShiftController::class,'update'])->name('update');
    Route::delete('shift/{shift}',          [ShiftController::class,'destroy'])->name('destroy');
});

// View‑only (index & show)
Route::middleware('role:admin|pegawai')->name('shift.')->group(function () {
    Route::get('shift',                     [ShiftController::class,'index'])->name('index');
    Route::get('shift/{shift}',             [ShiftController::class,'show'])->name('show');
});


// ————— TABEL PEGAWAI JADWAL —————

// Admin‑only (create, store, edit, update, destroy)
// Route::middleware('role:admin')->name('pegawai-jadwal.')->group(function () {
//     Route::get('pegawai-jadwal/create',                      [PegawaiJadwalController::class,'create'])->name('create');
//     Route::post('pegawai-jadwal',                            [PegawaiJadwalController::class,'store'])->name('store');
//     Route::get('pegawai-jadwal/{pegawai_jadwal}/edit',       [PegawaiJadwalController::class,'edit'])->name('edit');
//     Route::put('pegawai-jadwal/{pegawai_jadwal}',            [PegawaiJadwalController::class,'update'])->name('update');
//     Route::delete('pegawai-jadwal/{pegawai_jadwal}',         [PegawaiJadwalController::class,'destroy'])->name('destroy');
// });

// // View‑only (index & show)
// Route::middleware('role:admin|pegawai')->name('pegawai-jadwal.')->group(function () {
//     Route::get('pegawai-jadwal',                             [PegawaiJadwalController::class,'index'])->name('index');
//     Route::get('pegawai-jadwal/{pegawai_jadwal}',            [PegawaiJadwalController::class,'show'])->name('show');
// });


    
    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('pegawai-jadwal/create', [PegawaiJadwalController::class, 'create'])->name('pegawai-jadwal.create');
        Route::post('pegawai-jadwal', [PegawaiJadwalController::class, 'store'])->name('pegawai-jadwal.store');
        Route::get('pegawai-jadwal/{users_id}/{jadwal_shift_id}/edit', [PegawaiJadwalController::class, 'edit'])->name('pegawai-jadwal.edit');
        Route::put('pegawai-jadwal/{users_id}/{jadwal_shift_id}', [PegawaiJadwalController::class, 'update'])->name('pegawai-jadwal.update');
        Route::delete('pegawai-jadwal/{users_id}/{jadwal_shift_id}', [PegawaiJadwalController::class, 'destroy'])->name('pegawai-jadwal.destroy');
    });

    // View only
    Route::middleware('role:admin|pegawai')->group(function () {
        Route::get('pegawai-jadwal', [PegawaiJadwalController::class, 'index'])->name('pegawai-jadwal.index');
        Route::get('pegawai-jadwal/{users_id}/{jadwal_shift_id}', [PegawaiJadwalController::class, 'show'])->name('pegawai-jadwal.show');
    });
    

});