<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Aset;

class AsetSeeder extends Seeder
{
    public function run(): void
    {
        // sesuaikan ID ini dengan isi tabel kamu
        $periodeId = 1;
        $opdId1 = 1;
        $opdId2 = 2;
        $klasifikasiId = 2;
        $subklasifikasiId = 12;

        Aset::create([
            'periode_id' => $periodeId,
            'kode_aset' => 'PL-0001',
            'nama_aset' => 'Contoh Web PDP',
            'keterangan' => 'Website contoh penerapan PDP untuk publik',
            'klasifikasiaset_id' => $klasifikasiId,
            'subklasifikasiaset_id' => $subklasifikasiId,
            'lokasi' => 'PDNS',
            'opd_id' => $opdId1,
            'penyedia_aset' => 'UPTD PLID',
            'status_aktif' => 'Aktif',
            'kondisi_aset' => 'Baik',
            'kerahasiaan' => 1,
            'integritas' => 1,
            'ketersediaan' => 1,
            'keaslian' => 1,
            'kenirsangkalan' => 1,
        ]);

        Aset::create([
            'periode_id' => $periodeId,
            'kode_aset' => 'PL-0002',
            'nama_aset' => 'Whistle Blowing System (WBS)',
            'keterangan' => 'Untuk pengaduan internal Pemprov Bali',
            'klasifikasiaset_id' => $klasifikasiId,
            'subklasifikasiaset_id' => $subklasifikasiId,
            'lokasi' => 'Data Center Provinsi',
            'opd_id' => $opdId2,
            'penyedia_aset' => 'UPTD PLID',
            'status_aktif' => 'Aktif',
            'kondisi_aset' => 'Baik',
            'kerahasiaan' => 1,
            'integritas' => 1,
            'ketersediaan' => 1,
            'keaslian' => 1,
            'kenirsangkalan' => 1,
            // 'periode_id' => $periodeId,
            // 'kode_aset' => 'A-002',
            // 'nama_aset' => 'Sistem Informasi PPID',
            // 'keterangan' => 'Digunakan oleh Dinas Kominfo',
            // 'klasifikasiaset_id' => $klasifikasiId,
            // 'subklasifikasiaset_id' => $subklasifikasiId,
            // 'spesifikasi_aset' => 'Berbasis WordPress',
            // 'lokasi' => 'Server Lokal Diskominfo',
            // 'format_penyimpanan' => 'Fisik dan Dokumen Elektronik',
            // 'opd_id' => $opdId,
            // 'masa_berlaku' => '2025',
            // 'penyedia_aset' => 'PT Solusi Digital',
            // 'status_aktif' => 'Aktif',
            // 'kondisi_aset' => 'Baik',
            // 'kerahasiaan' => 2,
            // 'integritas' => 2,
            // 'ketersediaan' => 3,
            // 'keaslian' => 2,
            // 'kenirsangkalan' => 1,
            // 'kategori_se' => null,
            // 'status_personil' => 'Pihak Ketiga',
            // 'nip_personil' => null,
            // 'jabatan_personil' => 'Developer Freelance',
            // 'fungsi_personil' => 'Pengelola Sistem',
            // 'unit_personil' => 'Bidang Aplikasi Informatika',
        ]);
    }
}
