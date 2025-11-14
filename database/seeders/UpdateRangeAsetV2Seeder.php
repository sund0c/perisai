<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RangeAset;

class UpdateRangeAsetV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // RENDAH
        RangeAset::updateOrCreate(
            ['nilai_akhir_aset' => 'RENDAH'],
            [
                'deskripsi' => 'Pemilik risiko menerima risiko (ACCEPTED)',
            ]
        );

        // SEDANG
        RangeAset::updateOrCreate(
            ['nilai_akhir_aset' => 'SEDANG'],
            [
                'deskripsi' => 'Pemilik risiko BOLEH menerima risiko (ACCEPTED) atau BOLEH harus melakukan mitigasi (MITIGATE). Diserahkan sepenuhnya kepada pemilik risiko.',
            ]
        );

        // TINGGI
        RangeAset::updateOrCreate(
            ['nilai_akhir_aset' => 'TINGGI'],
            [
                'deskripsi' => 'Pemilik risiko WAJIB melakukan mitigasi risiko (MITIGATE)',
            ]
        );
    }
}


