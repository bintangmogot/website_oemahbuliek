<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\PegawaiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// // API Routes untuk testing CRUD
// Route::prefix('pegawai')->group(function () {
//     Route::get('/', [PegawaiController::class, 'apiIndex']);
//     Route::post('/', [PegawaiController::class, 'apiStore']);
//     Route::get('/{id}', [PegawaiController::class, 'apiShow']);
//     Route::put('/{id}', [PegawaiController::class, 'apiUpdate']);
//     Route::delete('/{id}', [PegawaiController::class, 'apiDestroy']);
// });

// // API dengan middleware auth (jika diperlukan)
// Route::middleware('auth:sanctum')->prefix('secure/pegawai')->group(function () {
//     Route::get('/', [PegawaiController::class, 'apiIndex']);
//     Route::post('/', [PegawaiController::class, 'apiStore']);
//     Route::get('/{id}', [PegawaiController::class, 'apiShow']);
//     Route::put('/{id}', [PegawaiController::class, 'apiUpdate']);
//     Route::delete('/{id}', [PegawaiController::class, 'apiDestroy']);
// });