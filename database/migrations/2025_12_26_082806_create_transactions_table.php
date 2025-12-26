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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        
        // Relasi: Siapa yang beli? (User)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Relasi: Beli apa? (Produk)
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        // Data Transaksi
        $table->string('status')->default('pending'); // pending, dikirim, selesai
        $table->date('shipping_date'); // Untuk validasi API Libur Nasional
        $table->decimal('total_price', 15, 2);
        $table->text('notes')->nullable();
        
        $table->timestamps();
        $table->softDeletes(); // Wajib untuk fitur hapus/batal pesanan
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
