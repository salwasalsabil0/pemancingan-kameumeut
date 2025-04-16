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
        Schema::create('pond_types', function (Blueprint $table) {
            $table->id(); // ID kolam (1: Kilo Jebur, 2: Kilo Angkat)
            $table->string('name'); // Nama jenis kolam
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pond_types');
    }
};
