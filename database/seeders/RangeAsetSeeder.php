<?php

namespace Database\Seeders;

use App\Models\RangeAset;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nilai_akhir_aset' => 'RENDAH',
                'warna_hexa'       => '#28a745', // hijau
                'nilai_bawah'      => 0,
                'nilai_atas'       => 5,
                'deskripsi'        => 'Aset bernilai rendah menjadi prioritas paling kecil apabila diperlukan tindakan perbaikan.'
            ],
            [
                'nilai_akhir_aset' => 'SEDANG',
                'warna_hexa'       => '#ffc107', // kuning
                'nilai_bawah'      => 6,
                'nilai_atas'       => 10,
                'deskripsi'        => 'Dibutuhkan penilaian risiko untuk menentukan tindakan perbaikan dan rencana pengembangan selanjutnya perlu dievaluasi secara berkala dari risiko yang telah didefinisikan'
            ],
            [
                'nilai_akhir_aset' => 'TINGGI',
                'warna_hexa'       => '#dc3545', // merah
                'nilai_bawah'      => 11,
                'nilai_atas'       => 15,
                'deskripsi'        => 'Diperlukan penilaian risiko untuk menentukan tindakan perbaikan yang terukur atas risiko yang telah didefinisikan.'
            ]
        ];

        foreach ($data as $item) {
            RangeAset::create($item);
        }
    }
}
