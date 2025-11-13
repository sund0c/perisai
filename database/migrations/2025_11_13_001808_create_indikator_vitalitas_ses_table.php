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
        Schema::create('indikator_vitalitasses', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // contoh: I1, I2 dst
            $table->text('pertanyaan');
            $table->string('opsi_a');
            $table->string('opsi_b');
            $table->string('opsi_c');
            $table->string('opsi_d');
            $table->integer('nilai_a')->default(10);
            $table->integer('nilai_b')->default(5);
            $table->integer('nilai_c')->default(1);
            $table->integer('nilai_d')->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_vitalitas_ses');
    }
};
