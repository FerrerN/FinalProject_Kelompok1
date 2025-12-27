<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Http\Controllers\CartController; // <--- Import di paling atas

Route::middleware('auth')->group(function () {
    // ... route lain biarkan saja ...

    // --- FITUR KERANJANG ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. PUBLIC & HOME
// Halaman Utama langsung menampilkan produk real
Route::get('/', function () {
    $products = \App\Models\Product::where('status', 'aktif')->latest()->take(8)->get();
    return view('welcome', compact('products'));
})->name('home');

// 2. GUEST (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// 3. AUTHENTICATED (Sudah Login)
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // A. TRANSAKSI
    Route::resource('transactions', TransactionController::class);

    // B. KHUSUS PENJUAL (Ditaruh di ATAS agar prioritas lebih tinggi)
    Route::middleware(['role:penjual'])->group(function () {
        // Route ini harus dibaca duluan sebelum route {product}
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // C. PRODUK UMUM (Pembeli & Penjual bisa lihat)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    
    // Route {product} (Detail) ditaruh PALING BAWAH
    // Agar kata 'create' tidak dianggap sebagai ID produk
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
});