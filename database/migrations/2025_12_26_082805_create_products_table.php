<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            
            $table->id('id_produk'); 
            $table->unsignedBigInteger('id_penjual'); 
            
            $table->string('nama_barang');  
            $table->text('deskripsi');  
            $table->decimal('harga', 10, 2); 
            $table->integer('stok'); 
            $table->string('kategori'); 
            $table->string('url_gambar')->nullable(); 
            
            
            $table->enum('status', ['Aktif', 'Arsip'])->default('Aktif');
            
            $table->softDeletes(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};