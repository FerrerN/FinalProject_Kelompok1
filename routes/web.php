<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController; // <--- PENTING: Tambahkan baris ini

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. FITUR TRANSAKSI (Kodingan Lama Kamu)
// Redirect halaman utama ke halaman transaksi
Route::get('/', function () {
    return redirect()->route('transactions.index');
});

// Daftarkan semua rute transaksi
Route::resource('transactions', TransactionController::class);


// 2. FITUR MANAJEMEN PENGGUNA (Kodingan Baru)
// Grouping route khusus untuk Admin agar URL-nya rapi (misal: /admin/users)
Route::prefix('admin')->name('admin.')->group(function () {

    // Halaman List User
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');

    // Proses Sync dari API External
    Route::post('/users/sync', [UserManagementController::class, 'sync'])->name('users.sync');

    // Proses Ganti Role
    Route::put('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');

    // Proses Hapus User
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});
