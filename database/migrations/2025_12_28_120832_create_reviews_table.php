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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pembeli
        $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Produk yang diulas
        $table->foreignId('transaction_id')->constrained()->onDelete('cascade'); // Bukti beli
        $table->integer('rating'); // 1-5
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
