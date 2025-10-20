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
        Schema::create('data_pribadi_master', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['spesifik', 'umum'])->comment('Tipe data pribadi: spesifik atau umum');
            $table->string('kode', 50)->comment('Kode data pribadi');
            $table->string('deskripsi')->nullable()->comment('Deskripsi data pribadi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pribadi_master');
    }
};
