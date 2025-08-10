<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsetsTable extends Migration
{
    public function up(): void
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();

            // Tahun Anggaran Aktif
            $table->foreignId('periode_id')->constrained('periodes')->restrictOnDelete();

            // Informasi Aset
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('keterangan')->nullable();
            $table->foreignId('klasifikasiaset_id')->constrained('klasifikasi_asets')->restrictOnDelete();
            $table->foreignId('subklasifikasiaset_id')->constrained('sub_klasifikasi_asets')->restrictOnDelete();
            $table->string('spesifikasi_aset')->nullable();

            // Identifikasi Keberadaan
            $table->string('lokasi')->nullable();

            $table->enum('format_penyimpanan', ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'])->nullable();
            $table->foreignId('opd_id')->constrained('opds')->restrictOnDelete();
            $table->string('masa_berlaku')->nullable();
            $table->string('penyedia_aset')->nullable();
            $table->enum('status_aktif', ['Aktif', 'Tidak Aktif'])->nullable();
            $table->enum('kondisi_aset', ['Baik', 'Tidak Layak', 'Rusak'])->nullable();

            // Identifikasi Keamanan Informasi (CIAAA)
            $table->tinyInteger('kerahasiaan');
            $table->tinyInteger('integritas');
            $table->tinyInteger('ketersediaan');
            $table->tinyInteger('keaslian');
            $table->tinyInteger('kenirsangkalan');

            // Kategori Strategis Elektronik (opsional)
            $table->string('kategori_se')->nullable();

            // Identifikasi Personil
            $table->enum('status_personil', ['SDM', 'Pihak Ketiga'])->nullable();
            $table->string('nip_personil')->nullable();
            $table->string('jabatan_personil')->nullable();
            $table->string('fungsi_personil')->nullable();
            $table->string('unit_personil')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
}
