<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataPribadiMaster;

class DataPribadiMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'tipe' => 'spesifik',
                'kode' => 'NIK',
                'deskripsi' => 'Nomor Induk Kependudukan'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'KTP',
                'deskripsi' => 'Kartu Tanda Penduduk'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'NPWP',
                'deskripsi' => 'Nomor Pokok Wajib Pajak'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'NAMA',
                'deskripsi' => 'Nama Lengkap'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'EMAIL',
                'deskripsi' => 'Alamat Email'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'TELEPON',
                'deskripsi' => 'Nomor Telepon'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'PASSPORT',
                'deskripsi' => null // Contoh deskripsi null
            ]
        ];

        foreach ($data as $item) {
            DataPribadiMaster::create($item);
        }
    }
}
