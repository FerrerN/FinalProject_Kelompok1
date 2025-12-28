<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // 1. SOLUSI ERROR MASS ASSIGNMENT
    // Kita izinkan kolom-kolom ini diisi data dari formulir
    protected $fillable = [
        'user_id',
        'product_id',
        'shipping_date',
        'total_price',
        'status',
        'notes'
    ];

    // 2. DEFINISI RELASI (Agar tidak error saat menampilkan tabel)
    
    // Relasi ke User (Pembeli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Product (Barang yang dibeli)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review() {
    return $this->hasOne(Review::class);
    }
}