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
        Schema::table('products', function (Blueprint $table) {
            // Tambahkan kolom 'kategori' jika belum ada
            if (!Schema::hasColumn('products', 'kategori')) {
                $table->string('kategori')->after('nama_barang')->default('Umum');
            }
            
            // Tambahkan kolom 'url_gambar' jika belum ada (jaga-jaga)
            if (!Schema::hasColumn('products', 'url_gambar')) {
                $table->string('url_gambar')->nullable()->after('deskripsi');
            }

            // Tambahkan kolom 'status' jika belum ada (jaga-jaga)
            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['aktif', 'arsip'])->default('aktif')->after('stok');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'url_gambar', 'status']);
        });
    }
};