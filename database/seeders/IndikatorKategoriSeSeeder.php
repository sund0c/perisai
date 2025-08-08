<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndikatorKategoriSe;


class IndikatorKategoriSeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'I1',
                'pertanyaan' => 'Nilai investasi sistem elektronik yang terpasang',
                'opsi_a' => 'Lebih dari Rp.30 Miliar',
                'opsi_b' => 'Lebih dari Rp.3 Miliar s/d Rp.30 Miliar',
                'opsi_c' => 'Kurang dari Rp.3 Miliar',
                'urutan' => 1,
            ],
            [
                'kode' => 'I2',
                'pertanyaan' => 'Total anggaran operasional tahunan yang dialokasikan untuk pengelolaan Sistem Elektronik',
                'opsi_a' => 'Lebih dari Rp.10 Miliar',
                'opsi_b' => 'Lebih dari Rp.1 Miliar s/d Rp.10 Miliar',
                'opsi_c' => 'Kurang dari Rp.1 Miliar',
                'urutan' => 2,
            ],
            [
                'kode' => 'I3',
                'pertanyaan' => 'Memiliki kewajiban kepatuhan terhadap Peraturan atau Standar tertentu',
                'opsi_a' => 'Peraturan atau Standar nasional dan internasional',
                'opsi_b' => 'Peraturan atau Standar nasional',
                'opsi_c' => 'Tidak ada Peraturan khusus',
                'urutan' => 3,
            ],
            [
                'kode' => 'I4',
                'pertanyaan' => 'Menggunakan teknik kriptografi khusus untuk keamanan informasi dalam Sistem Elektronik',
                'opsi_a' => 'Teknik kriptografi khusus yang disertifikasi oleh Negara',
                'opsi_b' => 'Teknik kriptografi sesuai standar industri, tersedia secara publik atau dikembangkan sendiri',
                'opsi_c' => 'Tidak ada penggunaan teknik kriptografi',
                'urutan' => 4,
            ],
            [
                'kode' => 'I5',
                'pertanyaan' => 'Jumlah pengguna Sistem Elektronik',
                'opsi_a' => 'Lebih dari 5.000 pengguna',
                'opsi_b' => '1.000 sampai dengan 5.000 pengguna',
                'opsi_c' => 'Kurang dari 1.000 pengguna',
                'urutan' => 5,
            ],
            [
                'kode' => 'I6',
                'pertanyaan' => 'Data pribadi yang dikelola Sistem Elektronik',
                'opsi_a' => 'Data pribadi yang memiliki hubungan dengan Data Pribadi lainnya',
                'opsi_b' => 'Data pribadi individu dan/atau terkait kepemilikan badan usaha',
                'opsi_c' => 'Tidak ada data pribadi',
                'urutan' => 6,
            ],
            [
                'kode' => 'I7',
                'pertanyaan' => 'Tingkat klasifikasi/kekritisan Data terhadap ancaman keamanan informasi',
                'opsi_a' => 'Sangat Rahasia',
                'opsi_b' => 'Rahasia dan/ atau Terbatas',
                'opsi_c' => 'Biasa',
                'urutan' => 7,
            ],
            [
                'kode' => 'I8',
                'pertanyaan' => 'Tingkat kekritisan proses dalam Sistem Elektronik terhadap ancaman keamanan informasi',
                'opsi_a' => 'Proses berdampak langsung pada layanan publik dan hajat hidup orang banyak',
                'opsi_b' => 'Proses berdampak tidak langsung pada hajat hidup orang banyak',
                'opsi_c' => 'Proses hanya berdampak pada bisnis internal perusahaan',
                'urutan' => 8,
            ],
            [
                'kode' => 'I9',
                'pertanyaan' => 'Dampak dari kegagalan Sistem Elektronik',
                'opsi_a' => 'Membahayakan pertahanan keamanan negara',
                'opsi_b' => 'Layanan publik nasional atau sektor lain terganggu',
                'opsi_c' => 'Gangguan layanan publik 1 provinsi / instansi',
                'urutan' => 9,
            ],
            [
                'kode' => 'I10',
                'pertanyaan' => 'Potensi kerugian dari insiden keamanan informasi (sabotase, terorisme)',
                'opsi_a' => 'Menimbulkan korban jiwa',
                'opsi_b' => 'Kerugian finansial',
                'opsi_c' => 'Gangguan operasional sementara',
                'urutan' => 10,
            ],
        ];

        foreach ($data as $item) {
            IndikatorKategoriSe::create(array_merge($item, [
                'nilai_a' => 5,
                'nilai_b' => 2,
                'nilai_c' => 1,
            ]));
        }
    }
}
