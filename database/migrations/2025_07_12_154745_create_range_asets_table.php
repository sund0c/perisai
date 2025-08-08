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
        Schema::create('range_asets', function (Blueprint $table) {
            $table->id();
            $table->string('nilai_akhir_aset');
            $table->string('warna_hexa', 7); // contoh: #00FF00
            $table->decimal('nilai_bawah', 15, 2);
            $table->decimal('nilai_atas', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('range_asets');
    }
};
