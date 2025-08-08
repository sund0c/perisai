<?php

namespace Database\Seeders;

use App\Models\FungsiStandar;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FungsiStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fungsistandardList = [
            [
                'kategori_id' => '2',
                'urutan' => '1',
                'nama' => 'OTENTIKASI',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '2',
                'nama' => 'MANAJEMEN SESI',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '3',
                'nama' => 'PERSYARATAN KONTROL AKSES',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '4',
                'nama' => 'VALIDASI INPUT',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '5',
                'nama' => 'INPUT KRIPTOGRAFI DAN VERIFIKASI STATIS',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '6',
                'nama' => 'PENANGANAN EROR DAN PENCATATAN LOG',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '7',
                'nama' => 'PROTEKSI DATA',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '8',
                'nama' => 'KEAMANAN KOMUNIKASI',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '9',
                'nama' => 'PENGENDALIAN SOURCE CODE BERBAHAYA',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '10',
                'nama' => 'LOGIKA BISNIS',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '11',
                'nama' => 'FILE',
            ],
            [
                'kategori_id' => '2',
                'urutan' => '12',
                'nama' => 'KEAMANAN KONFIGURASI',
            ],
            [
                'kategori_id' => '1',
                'urutan' => '1',
                'nama' => 'KERAHASIAAN',
            ],
            [
                'kategori_id' => '1',
                'urutan' => '2',
                'nama' => 'KEASLIAN',
            ],
            [
                'kategori_id' => '1',
                'urutan' => '3',
                'nama' => 'KEUTUHAN',
            ],
            [
                'kategori_id' => '1',
                'urutan' => '4',
                'nama' => 'KENIRSANGKALAN',
            ],
            [
                'kategori_id' => '1',
                'urutan' => '5',
                'nama' => 'KETERSEDIAAN',
            ],


        ];

        foreach ($fungsistandardList as $item) {
            FungsiStandar::create($item);
        }
    }
}
