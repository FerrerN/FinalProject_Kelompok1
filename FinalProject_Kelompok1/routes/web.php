<?php

use Illuminate\Support\Facades\Route;
// Import Controller yang dibutuhkan
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
// Import Controller Produk yang tadi kita buat (Perhatikan namespace-nya)
use App\Http\Controllers\Api\ProductController; 

// 1. Halaman Awal (Landing Page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Rute Halaman Dashboard (Hanya bisa diakses jika sudah login)
// Kita bungkus semua halaman admin/dashboard dengan middleware 'auth' agar aman
Route::middleware('auth')->group(function () {
    
    // Rute Transaksi (Resource Controller)
    Route::resource('transactions', TransactionController::class);

    // Rute Manajemen Produk (Inventory)
    // Ini akan menampilkan halaman UI yang baru saja kita buat
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    
});