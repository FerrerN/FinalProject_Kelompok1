<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController; // <--- PENTING: Panggil Controllernya

// Redirect halaman utama ke halaman transaksi
Route::get('/', function () {
    return redirect()->route('transactions.index');
});

// Daftarkan semua rute transaksi (index, create, store, dll) otomatis
Route::resource('transactions', TransactionController::class);