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
        Schema::create('booking', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer_name'); // Nama Pelanggan
            $table->boolean('is_member')->default(false); // Status Member
            $table->decimal('discount', 10, 2)->default(0); // Diskon jika ada
            $table->decimal('total_subtotal', 10, 2)->nullable(); // Total biaya berdasarkan berat ikan
            $table->integer('booking_status')->default(-1); // Status Booking (-1: Pending, 0: Selesai, 1: Dibatalkan)

            // Foreign Keys
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade'); // FK ke fields
            $table->foreignId('fish_id')->constrained('fishes')->onDelete('cascade'); // FK ke fishes
            $table->foreignId('pond_type_id')->constrained('pond_types')->onDelete('cascade'); // FK ke pondtypes

            $table->decimal('weight', 10, 2); // Berat Ikan yang diambil (kg)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
