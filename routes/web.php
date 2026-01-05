<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC & HOME ---
// Halaman Utama langsung menampilkan produk real
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
    // Export PDF (Sesuai Proposal Ketua)
    Route::get('/transactions/{id}/export-pdf', [TransactionController::class, 'exportInvoice'])->name('transactions.export_pdf');
    // Untuk Checkout dari Keranjang
    Route::post('/checkout/process', [TransactionController::class, 'checkoutCart'])->name('checkout.process');

    // D. FORUM DISKUSI
    Route::resource('forums', ForumController::class);
    Route::post('/forums/{id}/reply', [ForumController::class, 'reply'])->name('forums.reply');

    // E. ULASAN & RATING (CRUD LENGKAP)
    Route::get('/reviews/create/{transaction}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // TAMBAHAN UNTUK EDIT & DELETE
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // F. PRODUK (UMUM - Semua user bisa lihat list)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // G. KHUSUS PENJUAL (TUGAS ANGGOTA 1)
    // Ditaruh SEBELUM route detail produk agar tidak bentrok
    Route::middleware(['role:penjual'])->group(function () {
        // Export Laporan Stok PDF
        Route::get('/my-products/export-stock', [ProductController::class, 'exportStockReport'])->name('products.export_stock');

        // CRUD Produk
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // H. DETAIL PRODUK (Wildcard)
    // WAJIB Ditaruh PALING BAWAH dalam urutan route produk
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // ... (Kode sebelumnya)

    // 1. Dashboard Admin
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        // Dashboard Home
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/{user}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        // ... Di dalam Route::middleware(['role:admin'])->prefix('admin')->group(function () { ...

    // MANAJEMEN PRODUK (CRUD LENGKAP)
    Route::get('/products', [App\Http\Controllers\AdminController::class, 'products'])->name('admin.products');
    Route::get('/products/create', [App\Http\Controllers\AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [App\Http\Controllers\AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

// ...
    });

    // ... (Kode selanjutnya)
});
