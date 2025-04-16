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
        Schema::create('fishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_type_id')->constrained('pond_types')->onDelete('cascade'); // Relasi ke jenis kolam
            // Relasi ke tabel fields
            $table->string('type_ikan'); // Jenis ikan (contoh: Lele, Gurame)
            $table->integer('perkg_stock'); // Stok ikan per kg
            $table->decimal('perkg_price', 10, 2); // Harga ikan per kg
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishes');
    }
};
