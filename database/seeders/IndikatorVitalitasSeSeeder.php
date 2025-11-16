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
                'opsi_a' => 'SERIUS. Membuat layanan utama skup nasional berhenti total',
                'opsi_b' => 'SIGNIFIKAN. Membuat layanan utama skup provinsi berhenti total, pemulihan butuh waktu > 24 jam',
                'opsi_c' => 'TERBATAS. Menimbulkan gangguan terbatas pada layanan utama skup PROVINSI, pemulihan butuh waktu < 24 jam',
                'opsi_d' => 'MINOR. Tidak menimbulkan gangguan pada layanan utama skup provinsi',
                'urutan' => 1,
            ],
            [
                'kode' => 'I2',
                'pertanyaan' => 'Dampak terhadap Data/Informasi jika SE terganggu (misalkan bocor)',
                'opsi_a' => 'SERIUS. Berpotensi membahayakan keamanan negara',
                'opsi_b' => 'SIGNIFIKAN. Berpotensi menimbulkan kegaduhan lintas sektor skala provinsi',
                'opsi_c' => 'TERBATAS. Berpotensi menimbukan kegaduhan pribadi bukan massal atau lebih kecil dari skala provinsi',
                'opsi_d' => 'MINOR. Tidak ada data/informasi sensitif',
                'urutan' => 2,
            ],
            [
                'kode' => 'I3',
                'pertanyaan' => 'Dampak terhadap Finansial jika SE terganggu',
                'opsi_a' => 'SERIUS. Kerugian sangat besar sampai mempengaruhi kestabilan fiskal negaea',
                'opsi_b' => 'SIGNIFIKAN. Kerugian besar (hilang) dan hanya berdampak skala provinsi',
                'opsi_c' => 'TERBATAS. Kerugian bisa didapatkan kembali (tidak hilang) dan hanya berdampak skala provinsi',
                'opsi_d' => 'MINOR. Hampir tidak berdampak',
                'urutan' => 3,
            ],
            [
                'kode' => 'I4',
                'pertanyaan' => 'Dampak terhadap Umum jika SE terganggu',
                'opsi_a' => 'SERIUS. Berpotensi membahayakan keamanan negara, keselamatan publik dan kestabilan politik',
                'opsi_b' => 'SIGNIFIKAN. Berpotensi menimbulkan kegaduhan lintas sektor skala provinsi',
                'opsi_c' => 'TERBATAS. Berpotensi menimbukan kegaduhan pribadi bukan massal atau lebih kecil dari skala provinsi',
                'opsi_d' => 'MINOR. Tidak ada',
                'urutan' => 4,
            ],
            [
                'kode' => 'I5',
                'pertanyaan' => 'Dampak terhadap saling Ketergantungan jika SE terganggu',
                'opsi_a' => 'SERIUS. Dapat menyebabkan kegagalan SE berantai lintas sektor skala nasional',
                'opsi_b' => 'SIGNIFIKAN. Dapat menyebabkan kegagalan SE berantai lintas sektor skala provinsi',
                'opsi_c' => 'TERBATAS. Dapat menyebabkan kegagalan SE berantai di internal Pemprov',
                'opsi_d' => 'MINOR. Tidak ada',
                'urutan' => 5,
            ],
        ];

        IndikatorVitalitasSe::truncate();

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
