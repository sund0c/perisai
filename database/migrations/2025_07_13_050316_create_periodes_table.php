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
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique()->default('2025'); // Tahun anggaran, misal: 2025
            $table->enum('status', ['open', 'closed'])->default('open'); // Status periode
            $table->enum('kunci', ['open', 'locked'])->default('open'); // kunci periode
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
