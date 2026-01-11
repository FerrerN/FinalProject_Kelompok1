<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
// 1. PUBLIC & HOME
// =======================
Route::get('/', function () {
    $products = \App\Models\Product::where('status', 'aktif')
        ->latest()
        ->take(8)
        ->get();

    return view('welcome', compact('products'));
})->name('home');

// =======================
// 2. GUEST (BELUM LOGIN)
// =======================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// =======================
// 3. AUTH (SUDAH LOGIN)
// =======================
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =======================
    // A. PROFIL USER
    // =======================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // =======================
    // B. KERANJANG (CART)
    // =======================
    // Hanya pembeli yang butuh akses keranjang
    Route::middleware(['role:pembeli'])->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        
        // [BARU] Route Checkout Keranjang (Satu Pintu)
        // 1. Menampilkan Halaman Review Order
        Route::get('/checkout', [TransactionController::class, 'checkoutPage'])->name('transactions.checkout_page');
        // 2. Memproses Simpan Transaksi (Bulk)
        Route::post('/checkout', [TransactionController::class, 'checkoutCart'])->name('transactions.checkout_process');
    });

    // =======================
    // C. TRANSAKSI (UMUM)
    // =======================
    // Resource Controller (Index, Create, Store, Show, Edit, Update, Destroy)
    Route::resource('transactions', TransactionController::class);

    // Cetak & Export Invoice
    Route::get('/transactions/{transaction}/print', [TransactionController::class, 'printInvoice'])->name('transactions.print');
    Route::get('/transactions/{id}/export-pdf', [TransactionController::class, 'exportInvoice'])->name('transactions.export_pdf');

    // =======================
    // D. ULASAN (REVIEWS)
    // =======================
    Route::get('/reviews/{review}/export', [ReviewController::class, 'exportPdf'])->name('reviews.export_pdf');
    Route::get('/reviews/create/{transaction}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // =======================
    // E. FORUM DISKUSI
    // =======================
    Route::get('/forums/print', [ForumController::class, 'print'])->name('forums.print');
    Route::post('/forums/{id}/reply', [ForumController::class, 'reply'])->name('forums.reply');
    Route::resource('forums', ForumController::class);

    // =======================
    // F. DETAIL PRODUK (PUBLIC VIEW)
    // =======================
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // =======================
    // G. PENJUAL (MANAJEMEN PRODUK)
    // =======================
    Route::middleware(['role:penjual'])->group(function () {
        // Export Laporan Stok
        Route::get('/my-products/export-stock', [ProductController::class, 'exportStockReport'])->name('products.export_stock');

        // CRUD Produk
        Route::get('/manage-products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/manage-products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/manage-products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/manage-products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/manage-products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // =======================
    // H. ADMIN DASHBOARD
    // =======================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

        // Produk Management (Admin Override)
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
        Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

        // Laporan
        Route::get('/export-report', [AdminController::class, 'exportReport'])->name('admin.export_report');
    });

});

// =======================
// 4. API ROUTES (OPSIONAL/TAMBAHAN)
// =======================
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