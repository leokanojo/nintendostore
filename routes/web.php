<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Kelompok Guest (Sebelum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'authenticate'])
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'registerProcess'])
        ->name('register.process');
});

// Kelompok Auth (Setelah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Rute Profil Admin
    Route::get('/profil', [DashboardController::class, 'editProfile'])
        ->name('profile.edit');

    Route::put('/profil', [DashboardController::class, 'updateProfile'])
        ->name('profile.update');

    // Master Data
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('/kategori-barang', CategoryController::class);
        Route::resource('/daftar-barang', ProductController::class);
        Route::resource('/manajemen-pengguna', UserManagementController::class);
    });

    // Logistik / Mutasi Persediaan
    Route::get('/persediaan-barang', [InventoryController::class, 'index'])
        ->name('inventory.index');

    Route::post('/persediaan-barang/transaksi', [InventoryController::class, 'storeTransaction'])
        ->name('inventory.transaction.store');

    // Pelaporan
    Route::get('/laporan', [ReportController::class, 'index'])
        ->name('reports.index');
});