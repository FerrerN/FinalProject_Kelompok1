<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

// 1. Halaman Awal (Landing Page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Rute Transaksi (Hanya bisa diakses jika sudah login)
// Kita bungkus dengan middleware 'auth'
Route::middleware('auth')->group(function () {
    Route::resource('transactions', TransactionController::class);
});