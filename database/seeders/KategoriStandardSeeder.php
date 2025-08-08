<?php

namespace Database\Seeders;

use App\Models\StandarKategori;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoristandardList = [
            [
                'nama' => 'Data dan Informasi',
            ],
            [
                'nama' => 'Aplikasi WEB',
            ],
            [
                'nama' => 'Aplikasi MOBILE',
            ],
            [
                'nama' => 'Jaringan Intra',
            ],
            [
                'nama' => 'Sistem Penghubung Layanan/API',
            ],
            [
                'nama' => 'Pusat Data',
            ],

        ];

        foreach ($kategoristandardList as $item) {
            StandarKategori::create($item);
        }
    }
}
