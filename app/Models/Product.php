<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // HAPUS BAGIAN INI JIKA ADA:
    // protected $primaryKey = 'id_produk';  <-- HAPUS INI

    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'harga',
        'stok',
        'kategori',
        'url_gambar',
        'status'
        // Jangan ada 'id_produk' atau 'user_id' disini
    ];
    
    // ... sisa kode relasi ...
}