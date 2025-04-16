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
        Schema::table('bookings', function (Blueprint $table) {
            // Menambahkan foreign keys baru
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade');
            $table->foreignId('fish_id')->constrained('fishes')->onDelete('cascade');
            $table->foreignId('pond_type_id')->constrained('pond_types')->onDelete('cascade');

            // Menambahkan kolom berat ikan
            $table->decimal('weight', 10, 2); // Berat Ikan yang diambil (kg)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Menghapus foreign keys dan kolom weight
            $table->dropForeign(['field_id']);
            $table->dropForeign(['fish_id']);
            $table->dropForeign(['pond_type_id']);

            $table->dropColumn(['field_id', 'fish_id', 'pond_type_id', 'weight']);
        });
    }
};
