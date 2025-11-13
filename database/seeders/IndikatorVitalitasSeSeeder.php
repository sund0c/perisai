<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndikatorVitalitasSe;


class IndikatorVitalitasSeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'I1',
                'pertanyaan' => 'Dampak Operasional jika SE terganggu',
                'opsi_a' => 'Membuat LAYANAN UTAMA skup NASIONAL berhenti total',
                'opsi_b' => 'Membuat LAYANAN UTAMA skup PROVINSI berhenti total, pemulihan butuh waktu > 24 jam',
                'opsi_c' => 'Menimbulkan GANGGUAN TERBATAS pada LAYANAN UTAMA skup PROVINSI, pemulihan butuh waktu < 24 jam',
                'opsi_d' => 'Tidak menimbulkan gangguan pada layanan utama skup provinsi',
                'urutan' => 1,
            ],
            [
                'kode' => 'I2',
                'pertanyaan' => 'Dampak terhadap Data/Informasi jika SE terganggu (misalkan bocor)',
                'opsi_a' => 'BERPOTENSI membahayakan KEAMANAN NEGARA',
                'opsi_b' => 'BERPOTENSI menimbulkan kegaduhan lintas sektor skala PROVINSI',
                'opsi_c' => 'BERPOTENSI menimbukan kegaduhan pribadi bukan massal atau lebih kecil dari skala PROVINSI',
                'opsi_d' => 'Tidak ada data/informasi sensitif',
                'urutan' => 2,
            ],
            [
                'kode' => 'I3',
                'pertanyaan' => 'Dampak terhadap Finansial jika SE terganggu',
                'opsi_a' => 'Kerugian SANGAT BESAR sampai mempengaruhi kestabilan FISKAL NEGARA',
                'opsi_b' => 'Kerugian BESAR (hilang) dan hanya berdampak skala PROVINSI',
                'opsi_c' => 'Kerugian bisa didapatkan kembali (tidak hilang) dan hanya berdampak skala PROVINSI',
                'opsi_d' => 'Hampir tidak berdampak',
                'urutan' => 3,
            ],
            [
                'kode' => 'I4',
                'pertanyaan' => 'Dampak terhadap Umum jika SE terganggu',
                'opsi_a' => 'BERPOTENSI membahayakan keamanan NEGARA, keselamatan PUBLIK dan kestabilan POLITIK',
                'opsi_b' => 'BERPOTENSI menimbulkan kegaduhan lintas sektor skala PROVINSI',
                'opsi_c' => 'BERPOTENSI menimbukan kegaduhan pribadi bukan massal atau lebih kecil dari skala PROVINSI',
                'opsi_d' => 'Tidak ada',
                'urutan' => 4,
            ],
            [
                'kode' => 'I5',
                'pertanyaan' => 'Dampak terhadap Saling Ketergantungan jika SE terganggu',
                'opsi_a' => 'Dapat menyebabkan kegagalan SE berantai LINTAS SEKTOR skala NASIONAL',
                'opsi_b' => 'Dapat menyebabkan kegagalan SE berantai LINTAS SEKTOR skala PROVINSI',
                'opsi_c' => 'Dapat menyebabkan kegagalan SE berantai di INTERNAL PEMPROV',
                'opsi_d' => 'Tidak ada',
                'urutan' => 5,
            ],
        ];

        foreach ($data as $item) {
            IndikatorVitalitasSe::create(array_merge($item, [
                'nilai_a' => 15,
                'nilai_b' => 5,
                'nilai_c' => 1,
                'nilai_d' => 0,
            ]));
        }
    }
}
