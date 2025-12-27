<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product; // Pastikan Model Product di-import
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT AKUN ADMIN
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'admin',     // Role Admin
            'password' => Hash::make('password'), // Password: password
        ]);

        // 2. BUAT AKUN PENJUAL (Disimpan ke variabel $seller agar bisa dipakai di produk)
        $seller = User::create([
            'name' => 'Penjual Barang',
            'email' => 'seller@test.com',
            'role' => 'seller',    // Role Penjual
            'password' => Hash::make('password'),
        ]);

        // 3. BUAT AKUN PEMBELI
        User::create([
            'name' => 'Daven Pembeli',
            'email' => 'buyer@test.com',
            'role' => 'buyer',     // Role Pembeli
            'password' => Hash::make('password'),
        ]);

        // 4. BUAT DATA PRODUK DUMMY (Milik si Penjual di atas)
        Product::create([
            'user_id' => $seller->id, // Menggunakan ID dari akun seller yang baru dibuat
            'nama_barang' => 'Laptop Gaming ROG',
            'deskripsi' => 'Laptop spek dewa untuk koding Laravel',
            'harga' => 15000000,
            'stok' => 5,
            'kategori' => 'Elektronik',
            'status' => 'aktif'
        ]);

        Product::create([
            'user_id' => $seller->id,
            'nama_barang' => 'Mouse Wireless Logitech',
            'deskripsi' => 'Mouse anti delay',
            'harga' => 250000,
            'stok' => 10,
            'kategori' => 'Elektronik',
            'status' => 'aktif'
        ]);
    }
}
