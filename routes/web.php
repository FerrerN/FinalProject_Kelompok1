<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController; // Pastikan controller ini ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC & HOME ---
Route::get('/', function () {
    $products = \App\Models\Product::where('status', 'aktif')->latest()->take(8)->get();
    return view('welcome', compact('products'));
})->name('home');

// --- 2. GUEST (BELUM LOGIN) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// --- 3. MEMBER AREA (SUDAH LOGIN) ---
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // A. MANAJEMEN PROFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // B. FITUR KERANJANG
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('carts.destroy');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // C. TRANSAKSI & INVOICE
    Route::resource('transactions', TransactionController::class);
    // Print HTML (Preview)
    Route::get('/transactions/{transaction}/print', [TransactionController::class, 'printInvoice'])->name('transactions.print');
    // Export PDF
    Route::get('/transactions/{id}/export-pdf', [TransactionController::class, 'exportInvoice'])->name('transactions.export_pdf');
    // Untuk Checkout dari Keranjang
    Route::post('/checkout/process', [TransactionController::class, 'checkoutCart'])->name('checkout.process');

    // D. FORUM DISKUSI (WEB)
    Route::resource('forums', ForumController::class);
    Route::post('/forums/{id}/reply', [ForumController::class, 'reply'])->name('forums.reply');

    // E. ULASAN & RATING
    Route::get('/reviews/create/{transaction}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // F. PRODUK (List Umum)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // G. KHUSUS PENJUAL
    Route::middleware(['role:penjual'])->group(function () {
        Route::get('/my-products/export-stock', [ProductController::class, 'exportStockReport'])->name('products.export_stock');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/my-products/export-stock', [ProductController::class, 'exportStockReport'])->name('products.export_stock');
    });

    // H. KHUSUS ADMIN (Dari Branch Main)
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        // Dashboard Home
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

        // Produk Management (Admin)
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
        Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
    // ... route admin lainnya ...

    // Tambahkan Route Export ini:
    Route::get('/export-report', [App\Http\Controllers\AdminController::class, 'exportReport'])->name('admin.export_report');
            });
    });

    // I. DETAIL PRODUK (Wajib paling bawah dalam grup auth/umum)
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

}); // <--- Tutup Group Auth Utama

// --- 4. API ROUTES (Dari Branch Jabir) ---
// Ditaruh di luar group 'auth' utama web, karena punya middleware auth sendiri di dalamnya jika perlu
Route::prefix('api')->group(function () {

    // Public API
    Route::get('/forums', [ForumController::class, 'index']);
    Route::get('/forums/{id}', [ForumController::class, 'show']);

    // Private API
    Route::middleware('auth')->group(function () {
        Route::post('/forums', [ForumController::class, 'store']);
        Route::put('/forums/{id}', [ForumController::class, 'update']);
        Route::delete('/forums/{id}', [ForumController::class, 'destroy']);
        Route::post('/forums/{id}/reply', [ForumController::class, 'reply']);
    });
});
