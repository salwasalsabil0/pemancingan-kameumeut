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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama lapak (contoh: Lapak 1)
            $table->string('description')->nullable(); // Deskripsi lokasi lapak
            $table->foreignId('pond_type_id')->constrained('pond_types')->onDelete('cascade'); // Relasi ke jenis kolam
            $table->string('thumbnail')->nullable(); // Gambar lapak/kolam
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
