<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\JadwalShiftController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PengaturanGajiController;
use App\Http\Controllers\GajiLemburController;
use App\Http\Controllers\GajiPokokController;
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


// TABEL PENGATURAN GAJI
Route::middleware('role:admin')->name('pengaturan_gaji.')->group(function(){
    Route::get('pengaturan-gaji',[PengaturanGajiController::class,'index'])->name('index');
    Route::get('pengaturan-gaji/create',[PengaturanGajiController::class,'create'])->name('create');
    Route::post('pengaturan-gaji',[PengaturanGajiController::class,'store'])->name('store');
    Route::get('pengaturan-gaji/{pengaturan_gaji}/edit',[PengaturanGajiController::class,'edit'])->name('edit');
    Route::put('pengaturan-gaji/{pengaturan_gaji}',[PengaturanGajiController::class,'update'])->name('update');
    Route::delete('pengaturan-gaji/{pengaturan_gaji}',[PengaturanGajiController::class,'destroy'])->name('destroy');
});

    // TABEL PRESENSI
// ————— Khusus presensi pegawai bisa semua kecuali hapus —————

// Routes untuk Admin
Route::middleware('role:admin')->group(function () {
    Route::get('/admin/presensi', [PresensiController::class, 'adminIndex'])->name('admin.presensi.index');
    Route::post('/admin/presensi/{presensi}/approve', [PresensiController::class, 'approve'])->name('admin.presensi.approve');
    Route::post('/admin/presensi/{presensi}/reject', [PresensiController::class, 'reject'])->name('admin.presensi.reject');
});

// Routes untuk Pegawai
Route::middleware('role:pegawai')->group(function () {
    Route::get('/pegawai/presensi', [PresensiController::class, 'pegawaiIndex'])->name('pegawai.presensi.index');
    Route::get('/pegawai/presensi/jadwal/{jadwalShift}', [PresensiController::class, 'show'])->name('pegawai.presensi.show');
    Route::post('/pegawai/presensi/checkin', [PresensiController::class, 'checkIn'])->name('pegawai.presensi.checkin');
    Route::post('/pegawai/presensi/checkout', [PresensiController::class, 'checkOut'])->name('pegawai.presensi.checkout');
    Route::get('/presensi/riwayat-saya', [PresensiController::class, 'riwayatLengkap'])->name('presensi.riwayat-lengkap');

});

// Routes untuk kedua role
Route::middleware('auth')->group(function () {
    Route::get('/presensi/{presensi}/detail', [PresensiController::class, 'detail'])->name('presensi.detail');
});

// ————— TABEL JADWAL SHIFTS —————

// Admin‑only (create, store, edit, update, destroy)
Route::middleware('role:admin')->name('jadwal-shift.')->group(function () {
        Route::get('jadwal-shift/create',              [JadwalShiftController::class,'create'])->name('create');
        Route::post('jadwal-shift',                    [JadwalShiftController::class,'store'])->name('store');
        Route::get('jadwal-shift/{jadwal_shift}/edit', [JadwalShiftController::class,'edit'])->name('edit');
        Route::put('jadwal-shift/{jadwal_shift}',      [JadwalShiftController::class,'update'])->name('update');
        Route::get('jadwal-shift/pilih-shift',         [JadwalShiftController::class,'pilihShift'])->name('pilih-shift');
        Route::get('/jadwal-shift/detail/{shift_id}/{tanggal}',[JadwalShiftController::class, 'detailShift'])->name('detail');
        Route::delete('jadwal-shift/{jadwal_shift}',   [JadwalShiftController::class, 'destroy'])->name('destroy');
        
     });
     
// View‑only (index & show)
Route::middleware('role:admin|pegawai')->name('jadwal-shift.')->group(function () {
         Route::get('jadwal-shift',                [JadwalShiftController::class,'index'])->name('index');
         Route::get('jadwal-shift/{jadwal_shift}', [JadwalShiftController::class,'show'])->name('show');
     });


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


// ————— TABEL GAJI LEMBUR —————
    // Admin routes
    Route::middleware('role:admin')->name('gaji-lembur.')->group(function () {
        Route::post('gaji-lembur/batch-payment', [GajiLemburController::class, 'batchUpdatePayment'])->name('batch-payment');
        Route::get('gaji-lembur/', [GajiLemburController::class, 'index'])->name('index');
        Route::get('/detail-pegawai/{user_id}', [GajiLemburController::class, 'detailPegawai'])->name('detail-pegawai');
        Route::get('gaji-lembur/laporan', [GajiLemburController::class, 'laporan'])->name('laporan');
        Route::put('dashboard/gaji-lembur/{gajiLembur}/payment', [GajiLemburController::class, 'updatePayment'])->name('update-payment');

    });
    
    // Pegawai routes
    Route::middleware('role:pegawai')->name('gaji-lembur.')->group(function () {
        Route::get('gaji-lembur/saya', [GajiLemburController::class, 'pegawaiIndex'])->name('pegawai.index');
    });
    
    // Shared routes
    Route::get('gaji-lembur/{gajiLembur}', [GajiLemburController::class, 'show'])->name('gaji-lembur.show');



// ————— TABEL GAJI POKOK —————

    // ============ ADMIN ROUTES ============
    Route::middleware('role:admin')->prefix('gaji-pokok')->name('admin.gaji-pokok.')->group(function () {
        // List semua gaji pokok dengan filter
        Route::get('/', [GajiPokokController::class, 'adminIndex'])->name('index');
        
        // Detail gaji pokok per karyawan
        Route::get('/user/{user}', [GajiPokokController::class, 'adminShow'])->name('show');
        
        // Ringkasan pembayaran
        Route::get('/summary', [GajiPokokController::class, 'adminSummary'])->name('summary');
        
        
        // Update status pembayaran
        Route::post('/payment', [GajiPokokController::class, 'updatePembayaran'])->name('update-pembayaran');
        
        Route::post('/generate-from-realtime', [GajiPokokController::class, 'generateFromRealtime'])->name('generate-from-realtime');
    // Detail gaji pokok realtime (belum tersimpan)
    Route::get('/realtime/detail', [GajiPokokController::class, 'adminDetailRealtime'])
        ->name('detail-realtime');
        
            Route::post('/preview', [GajiPokokController::class, 'previewGaji'])
        ->name('preview');

        Route::get('/generated', [GajiPokokController::class, 'adminGenerated'])->name('generated');

    });

    // ============ PEGAWAI ROUTES ============
    Route::middleware('role:pegawai')->prefix('gaji-pokok')->name('pegawai.gaji-pokok.')->group(function () {
        // List gaji pokok sendiri
        Route::get('/saya', [GajiPokokController::class, 'pegawaiIndex'])->name('index');
        
        // Detail gaji pokok sendiri
        Route::get('/detail/{gajiPokok}', [GajiPokokController::class, 'pegawaiDetail'])->name('detail');
    });

});

