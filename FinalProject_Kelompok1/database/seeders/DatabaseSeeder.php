<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User untuk Login (Ketua)
        User::create([
            'name' => 'user',
            'email' => 'user@admin.com',
            'password' => Hash::make('password'), // Password login: password
            //'role' => 'admin', // Opsional jika ada role
        ]);

        // 2. Buat User Pembeli (Anggota)
        $buyer = User::create([
            'name' => 'Daven',
            'email' => 'daven@user.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Buat Data Produk Dummy (Agar bisa ditransaksikan)
        Product::create([
            
            'nama_barang' => 'Laptop Gaming ROG',
            'deskripsi' => 'Laptop spek dewa untuk koding Laravel',
            'harga' => 15000000,
            'stok' => 5,
            'kategori' => 'Elektronik',
            'status' => 'aktif'
        ]);

        Product::create([
            
            'nama_barang' => 'Mouse Wireless Logitech',
            'deskripsi' => 'Mouse anti delay',
            'harga' => 250000,
            'stok' => 10,
            'kategori' => 'Aksesoris',
            'status' => 'aktif'
        ]);
    }
}