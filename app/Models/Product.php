<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Pastikan nama tabel benar

    // DAFTAR KOLOM YANG BOLEH DIISI (WAJIB LENGKAP)
    protected $fillable = [
        'user_id',
        'nama_barang',
        'kategori',      // <--- Pastikan ada
        'harga',
        'stok',
        'deskripsi',
        'url_gambar',    // <--- Pastikan ada
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Review
    public function reviews()
    {
        return $this->hasMany(Review::class) ->latest();
    }
}