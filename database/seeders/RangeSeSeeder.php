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
                'deskripsi'        => 'Wajib menerapkan SNI ISO/IEC 27001 atau standar keamanan dari BSSN.'
            ],
            [
                'nilai_akhir_aset' => 'SEDANG',
                'warna_hexa'       => '#ffc107', // kuning
                'nilai_bawah'      => 16,
                'nilai_atas'       => 34,
                'deskripsi'        => 'Berdampak terbatas pada kepentingan sektor dan/atau daerah tertentu. Wajib menerapkan SNI ISO/IEC 27001 dan/atau standar keamanan siber dari BSSN, standar keamanan siber lainnya dari Kementrian/Lembaga.'
            ],
            [
                'nilai_akhir_aset' => 'TINGGI',
                'warna_hexa'       => '#dc3545', // merah
                'nilai_bawah'      => 35,
                'nilai_atas'       => 50,
                'deskripsi'        => 'Berdampak serus terhadap kepentingan umum, pelayanan publik, kelancaran penyelenggaraan negara atau pertahanan dan keamanan negara. Wajib menerapkan SNI ISO/IEC 27001, standar keamanan siber dari BSSN, standar keamanan siber lainnya dari Kementrian/Lembaga.'
            ]
        ];

        foreach ($data as $item) {
            RangeSe::create($item);
        }
    }
}
