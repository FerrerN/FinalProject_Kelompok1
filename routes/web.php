<?php

use Illuminate\Support\Facades\Route;
// Import semua Controller yang dibutuhkan
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. RUTE PUBLIK (Tamu) ---
// Jika membuka halaman awal ('/'), langsung arahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// --- 2. RUTE TERPROTEKSI (Harus Login) ---
Route::middleware('auth')->group(function () {

    // Logout (Bisa diakses semua role)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // ====================================================
    // A. GRUP ADMIN
    // ====================================================
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

            // Manajemen User (CRUD User)
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::post('/users/sync', [UserManagementController::class, 'sync'])->name('users.sync');
            Route::put('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
            Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        });


    // ====================================================
    // B. GRUP PENJUAL (SELLER)
    // ====================================================
    Route::middleware('role:seller')
        ->prefix('seller')
        ->name('seller.')
        ->group(function () {
            // Dashboard Penjual
            Route::get('/dashboard', [SellerController::class, 'index'])->name('dashboard');

            // Nanti tambahkan rute kelola produk di sini
            // Route::resource('products', ProductController::class);
        });


    // ====================================================
    // C. GRUP PEMBELI (BUYER)
    // ====================================================
    Route::middleware('role:buyer')
        ->group(function () {
            // Dashboard Pembeli / List Produk
            // (Sementara kita arahkan ke transaksi atau buat method index di TransactionController)
            Route::get('/products', [TransactionController::class, 'index'])->name('products.index');

            // Fitur Belanja / Transaksi
            Route::resource('transactions', TransactionController::class);
        });

});
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