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
        // 1. Buat Akun TOKO GADGET (Penjual 1)
        $seller1 = User::create([
            'name' => 'Juragan Gadget Tel-U', // Ini jadi Nama Toko
            'email' => 'gadget@toko.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
        ]);

        // 2. Buat Akun TOKO FASHION (Penjual 2)
        $seller2 = User::create([
            'name' => 'Fashion Mahasiswa', // Ini jadi Nama Toko
            'email' => 'fashion@toko.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
        ]);

        // 3. Buat Akun PEMBELI
        User::create([
            'name' => 'Si Pembeli Santuy',
            'email' => 'pembeli@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
        ]);

        // --- CONTOH PRODUK UNTUK TOKO GADGET ---
        Product::create([
            'user_id' => $seller1->id,
            'nama_barang' => 'Laptop Gaming ROG Bekas',
            'deskripsi' => 'Laptop mulus no minus, pemakaian wajar anak DKV. RAM 16GB SSD 512GB.',
            'harga' => 12500000,
            'stok' => 1,
            'url_gambar' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=600&q=80',
            'status' => 'aktif'
        ]);

        Product::create([
            'user_id' => $seller1->id,
            'nama_barang' => 'Mouse Wireless Logitech',
            'deskripsi' => 'Mouse enak buat nugas dan skripsian. Baterai awet.',
            'harga' => 150000,
            'stok' => 5,
            'url_gambar' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=600&q=80',
            'status' => 'aktif'
        ]);

        // --- CONTOH PRODUK UNTUK TOKO FASHION ---
        Product::create([
            'user_id' => $seller2->id,
            'nama_barang' => 'Hoodie Telkom University',
            'deskripsi' => 'Bahan cotton fleece tebal, sablon plastisol. Ready size L dan XL.',
            'harga' => 185000,
            'stok' => 10,
            'url_gambar' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=600&q=80',
            'status' => 'aktif'
        ]);

        Product::create([
            'user_id' => $seller2->id,
            'nama_barang' => 'Totebag Canvas Aesthetic',
            'deskripsi' => 'Cocok buat bawa laptop dan buku ke kampus. Desain minimalis.',
            'harga' => 45000,
            'stok' => 20,
            'url_gambar' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&q=80',
            'status' => 'aktif'
        ]);
    }
}