<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptkka_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptkka_session_id')->constrained()->onDelete('cascade');

            $table->unsignedTinyInteger('from_status')->nullable();
            $table->unsignedTinyInteger('to_status');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptkka_status_logs');
    }
};
