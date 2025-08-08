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
        Schema::create('standar_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fungsi_standar_id')->constrained('fungsi_standar')->onDelete('cascade');
            $table->text('indikator');
            $table->text('tujuan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standar_indikator');
    }
};
