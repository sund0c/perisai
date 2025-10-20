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
        // Hapus semua data yang ada terlebih dahulu
        DataPribadiMaster::truncate();

        $data = [
            // 🟣 Data Pribadi Spesifik
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA DAN INFORMASI KESEHATAN',
                'deskripsi' => 'Data dan informasi kesehatan',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA BIOMETRIK',
                'deskripsi' => 'Data biometrik',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA GENETIKA',
                'deskripsi' => 'Data genetika',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'CATATAN KEJAHATAN',
                'deskripsi' => 'Catatan kejahatan',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA ANAK',
                'deskripsi' => 'Data anak',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA KEUANGAN',
                'deskripsi' => 'Data keuangan pribadi',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'spesifik',
                'kode' => 'DATA PRIBADI SPESIFIK LAINNYA SESUAI UU',
                'deskripsi' => 'Data pribadi spesifik lainnya sesuai UU',
                'status' => 'aktif'
            ],
            
            // 🟢 Data Pribadi Umum
            [
                'tipe' => 'umum',
                'kode' => 'NAMA LENGKAP',
                'deskripsi' => 'Nama lengkap',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'JENIS KELAMIN',
                'deskripsi' => 'Jenis kelamin',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'KEWARGANEGARAAN',
                'deskripsi' => 'Kewarganegaraan',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'AGAMA',
                'deskripsi' => 'Agama',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'STATUS PERKAWINAN',
                'deskripsi' => 'Status perkawinan',
                'status' => 'aktif'
            ],
            [
                'tipe' => 'umum',
                'kode' => 'DATA PRIBADI KOMBINASI (IDENTIFIKASI SESEORANG)',
                'deskripsi' => 'Data pribadi yang dikombinasikan untuk mengidentifikasi seseorang',
                'status' => 'aktif'
            ]
        ];

        foreach ($data as $item) {
            DataPribadiMaster::create($item);
        }
    }
}