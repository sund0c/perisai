<?php

namespace Database\Seeders;
use App\Models\KlasifikasiAset;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KlasifikasiAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klasifikasiList = [
            [
                'klasifikasiaset' => 'Data dan Informasi',
                'kodeklas' => 'DI',
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","klasifikasiaset_id","subklasifikasiaset_id","lokasi","format_penyimpanan","opd_id","masa_berlaku","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan"]',
            ],
            [
                'klasifikasiaset' => 'Perangkat Lunak',
                'kodeklas' => 'PL',
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","klasifikasiaset_id","subklasifikasiaset_id","lokasi","opd_id","penyedia_aset","status_aktif","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan","kategori_se"]',
            ],
            [
                'klasifikasiaset' => 'Perangkat Keras',
                'kodeklas' => 'PK',
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","klasifikasiaset_id","subklasifikasiaset_id","spesifikasi_aset","lokasi","opd_id","kondisi_aset","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan"]',
            ],
            [
                'klasifikasiaset' => 'Sarana Pendukung',
                'kodeklas' => 'SP',
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","klasifikasiaset_id","subklasifikasiaset_id","spesifikasi_aset","lokasi","opd_id","kondisi_aset","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan"]',
            ],
            [
                'klasifikasiaset' => 'SDM dan Pihak Ketiga',
                'kodeklas' => 'SK',
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","klasifikasiaset_id","subklasifikasiaset_id","opd_id","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan","status_personil","nip_personil","jabatan_personil","fungsi_personil","unit_personil"]',
            ],
        ];

        foreach ($klasifikasiList as $item) {
            KlasifikasiAset::create($item);
        }
    }
}
