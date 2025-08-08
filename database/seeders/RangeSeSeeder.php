<?php

namespace Database\Seeders;

use App\Models\RangeSe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeSeSeeder extends Seeder
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
                'nilai_bawah'      => 10,
                'nilai_atas'       => 15,
                'deskripsi'        => '-'
            ],
            [
                'nilai_akhir_aset' => 'SEDANG',
                'warna_hexa'       => '#ffc107', // kuning
                'nilai_bawah'      => 16,
                'nilai_atas'       => 34,
                'deskripsi'        => '-'
            ],
            [
                'nilai_akhir_aset' => 'TINGGI',
                'warna_hexa'       => '#dc3545', // merah
                'nilai_bawah'      => 35,
                'nilai_atas'       => 50,
                'deskripsi'        => '-'
            ]
        ];

        foreach ($data as $item) {
            RangeSe::create($item);
        }
    }
}
