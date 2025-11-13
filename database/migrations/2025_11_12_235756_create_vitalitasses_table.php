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
        Schema::create('vitalitas_ses', function (Blueprint $table) {
            $table->id();

            // UUID publik
            $table->uuid('uuid')->unique();

            $table->foreignId('aset_id')
                ->constrained('asets')
                ->restrictOnDelete();

            $table->json('jawaban')->nullable()
                ->comment('Jawaban indikator: {I1:{jawaban,keterangan},...}');

            $table->unsignedInteger('skor_total')->default(0)
                ->comment('Total bobot nilai jawaban');

            $table->timestamps();

            $table->unique('aset_id', 'vitalitas_ses_aset_unique');
            $table->index('aset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitalitasses');
    }
};
