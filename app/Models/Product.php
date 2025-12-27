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
    'user_id', // <--- Tambah ini
    'nama_barang',
    'deskripsi',
    'harga',
    'stok',
    'kategori',
    'url_gambar',
    'status'
];

// Relasi: Produk milik satu User (Penjual)
public function user()
{
    return $this->belongsTo(User::class);
}
    // ... sisa kode relasi ...
}