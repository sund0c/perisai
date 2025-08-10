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
            'nama_aset' => 'Portal Bali Satu Data',
            'keterangan' => 'Portal Satu Data Prov Bali',
            'klasifikasiaset_id' => $klasifikasiId,
            'subklasifikasiaset_id' => $subklasifikasiId,
            'lokasi' => 'PDNS',
            'opd_id' => $opdId1,
            'penyedia_aset' => 'UPTD PLID',
            'status_aktif' => 'Aktif',
            'kondisi_aset' => 'Baik',
            'kerahasiaan' => 3,
            'integritas' => 3,
            'ketersediaan' => 3,
            'keaslian' => 3,
            'kenirsangkalan' => 3,
        ]);

        Aset::create([
            'periode_id' => $periodeId,
            'kode_aset' => 'PL-0003',
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
        ]);

        Aset::create([
            'periode_id' => $periodeId,
            'kode_aset' => 'PL-0004',
            'nama_aset' => 'Sistem Pengaduan Online',
            'keterangan' => 'Pengaduan publik',
            'klasifikasiaset_id' => $klasifikasiId,
            'subklasifikasiaset_id' => $subklasifikasiId,
            'lokasi' => 'PDNS',
            'opd_id' => $opdId2,
            'penyedia_aset' => 'Mandiri',
            'status_aktif' => 'Aktif',
            'kondisi_aset' => 'Baik',
            'kerahasiaan' => 1,
            'integritas' => 2,
            'ketersediaan' => 3,
            'keaslian' => 1,
            'kenirsangkalan' => 2,
        ]);
    }
}
