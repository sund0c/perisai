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
        Schema::create('ptkka_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptkka_session_id')->constrained('ptkka_sessions')->restrictOnDelete();
            $table->foreignId('rekomendasi_standard_id')->constrained()->restrictOnDelete();
            $table->tinyInteger('jawaban')->comment('0 = Tidak Diterapkan, 1 = Sebagian, 2 = Seluruhnya');
            $table->text('penjelasanopd')->nullable();
            $table->text('catatanadmin')->nullable();
            $table->text('linkbuktidukung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ptkka_jawabans');
    }
};
