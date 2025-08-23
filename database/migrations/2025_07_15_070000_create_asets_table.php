<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsetsTable extends Migration
{
    public function up(): void
    {
        // Schema::create('asets', function (Blueprint $table) {
        //     $table->id();
        //     $table->uuid('uuid')->unique();
        //     $table->unique(['periode_id', 'kode_aset'], 'asets_opd_periode_kode_unique');
        //     $table->foreignId('periode_id')->constrained('periodes')->restrictOnDelete();
        //     $table->string('kode_aset'); //->unique();
        //     $table->string('nama_aset');
        //     $table->string('keterangan')->nullable();
        //     $table->foreignId('klasifikasiaset_id')->constrained('klasifikasi_asets')->restrictOnDelete();
        //     $table->foreignId('subklasifikasiaset_id')->constrained('sub_klasifikasi_asets')->restrictOnDelete();
        //     $table->string('spesifikasi_aset')->nullable();

        //     // Identifikasi Keberadaan
        //     $table->string('lokasi')->nullable();

        //     $table->enum('format_penyimpanan', ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'])->nullable();
        //     $table->foreignId('opd_id')->constrained('opds')->restrictOnDelete();
        //     $table->string('masa_berlaku')->nullable();
        //     $table->string('penyedia_aset')->nullable();
        //     $table->enum('status_aktif', ['Aktif', 'Tidak Aktif'])->nullable();
        //     $table->enum('kondisi_aset', ['Baik', 'Tidak Layak', 'Rusak'])->nullable();

        //     // Identifikasi Keamanan Informasi (CIAAA)
        //     $table->tinyInteger('kerahasiaan');
        //     $table->tinyInteger('integritas');
        //     $table->tinyInteger('ketersediaan');
        //     $table->tinyInteger('keaslian');
        //     $table->tinyInteger('kenirsangkalan');

        //     // Kategori Strategis Elektronik (opsional)
        //     $table->string('kategori_se')->nullable();

        //     // Identifikasi Personil
        //     $table->enum('status_personil', ['SDM', 'Pihak Ketiga'])->nullable();
        //     $table->string('nip_personil')->nullable();
        //     $table->string('jabatan_personil')->nullable();
        //     $table->string('fungsi_personil')->nullable();
        //     $table->string('unit_personil')->nullable();

        //     $table->timestamps();
        // });

        // 1) Master kepemilikan kode
        Schema::create('aset_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->restrictOnDelete();
            $table->string('kode_aset'); // contoh: PK-0001
            $table->timestamps();

            // SATU kode_aset hanya boleh dimiliki SATU OPD secara global
            $table->unique('kode_aset', 'aset_keys_kode_unique');
            // Guard tambahan agar tidak ada duplikat baris sama
            $table->unique(['opd_id', 'kode_aset'], 'aset_keys_opd_kode_unique');
        });

        // 2) Tabel asets
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // FK ke master kepemilikan kode
            $table->foreignId('aset_key_id')->constrained('aset_keys')->cascadeOnDelete();

            // Tahun Anggaran Aktif
            $table->foreignId('periode_id')->constrained('periodes')->restrictOnDelete();

            // Simpan kode_aset juga untuk kemudahan tampil/cari (TIDAK unik di sini)
            $table->string('kode_aset');

            $table->string('nama_aset');
            $table->string('keterangan')->nullable();

            $table->foreignId('klasifikasiaset_id')->constrained('klasifikasi_asets')->restrictOnDelete();
            $table->foreignId('subklasifikasiaset_id')->constrained('sub_klasifikasi_asets')->restrictOnDelete();
            $table->string('spesifikasi_aset')->nullable();

            // Identifikasi Keberadaan
            $table->string('lokasi')->nullable();
            $table->enum('format_penyimpanan', ['Fisik', 'Dokumen Elektronik', 'Fisik dan Dokumen Elektronik'])->nullable();

            // OPD pemilik baris aset (boleh sama dengan aset_keys.opd_id, tapi tetap disimpan demi query cepat)
            $table->foreignId('opd_id')->constrained('opds')->restrictOnDelete();

            $table->string('masa_berlaku')->nullable();
            $table->string('penyedia_aset')->nullable();
            $table->enum('status_aktif', ['Aktif', 'Tidak Aktif'])->nullable();
            $table->enum('kondisi_aset', ['Baik', 'Tidak Layak', 'Rusak'])->nullable();

            // CIAAA
            $table->tinyInteger('kerahasiaan');
            $table->tinyInteger('integritas');
            $table->tinyInteger('ketersediaan');
            $table->tinyInteger('keaslian');
            $table->tinyInteger('kenirsangkalan');

            // Kategori SE (opsional)
            $table->string('kategori_se')->nullable();

            // Personil
            $table->enum('status_personil', ['SDM', 'Pihak Ketiga'])->nullable();
            $table->string('nip_personil')->nullable();
            $table->string('jabatan_personil')->nullable();
            $table->string('fungsi_personil')->nullable();
            $table->string('unit_personil')->nullable();

            $table->timestamps();

            // ATURAN INTI:
            // kode_aset boleh muncul lagi untuk OPD yang sama di periode berbeda
            $table->unique(['aset_key_id', 'periode_id'], 'asets_key_periode_unique');

            // Index bantu untuk query umum
            $table->index(['opd_id', 'periode_id'], 'asets_opd_periode_idx');
            $table->index('kode_aset', 'asets_kode_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
}
