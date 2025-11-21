<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KlasifikasiAset;

class UpdateKlasifikasiAsetV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KlasifikasiAset::updateOrCreate(
            ['kodeklas' => 'PL'],
            [
                'tampilan_field_aset' => '["periode_id","kode_aset","nama_aset","keterangan","link_pse","klasifikasiaset_id","subklasifikasiaset_id","link_url","lokasi","opd_id","penyedia_aset","status_aktif","kerahasiaan","integritas","ketersediaan","keaslian","kenirsangkalan","kategori_se"]'
            ]
        );
    }
}
