<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    // Izinkan semua kolom diisi
    protected $guarded = [];

    // Relasi: Review milik User (Penulis)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Review milik Product (Barang yang diulas)
    // --- INI YANG TADI KURANG ---
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: Review berdasarkan Transaksi (Bukti beli)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}