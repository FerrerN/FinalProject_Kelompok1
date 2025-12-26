<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        // Relasi ke User (Penjual)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Data Barang
        $table->string('nama_barang');
        $table->text('deskripsi');
        $table->decimal('harga', 15, 2); // Format harga
        $table->integer('stok');
        $table->string('kategori');
        $table->string('url_gambar')->nullable();
        $table->string('status')->default('aktif'); // aktif/habis
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }

    
};
