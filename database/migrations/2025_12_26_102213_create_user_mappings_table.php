<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_mappings', function (Blueprint $table) {
            $table->id('local_id');
            // Menyimpan ID unik dari sistem External (misal: Firebase UID / Auth0 ID)
            $table->string('external_uid')->unique();
            $table->string('email');
            $table->string('full_name')->nullable();
            // Role default adalah user biasa
            $table->enum('role', ['admin', 'penjual', 'user'])->default('user');
            $table->timestamp('last_synced')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mappings');
    }
};
