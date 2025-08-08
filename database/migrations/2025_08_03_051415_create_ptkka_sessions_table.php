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
        Schema::create('ptkka_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('aset_id')->constrained()->onDelete('cascade');
            $table->foreignId('standar_kategori_id')->constrained('standar_kategori')->onDelete('cascade');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Pengisian, 1=Pengajuan, 2=Verifikasi, 3=Klarifikasi, 4=Rampung');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ptkka_sessions');
    }
};
