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
        Schema::create('kategori_ses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained()->onDelete('cascade'); // hanya untuk aset perangkat lunak
            /**
             * Format jawaban:
             * {
             *   "I1": { "jawaban": "A", "keterangan": "..." },
             *   "I2": { "jawaban": "C", "keterangan": "..." },
             *   ...
             * }
             */
            $table->json('jawaban')->nullable(); // jawaban + keterangan per indikator
            $table->integer('skor_total')->nullable(); // total bobot dari seluruh jawaban
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_ses');
    }
};
