<?php
return [
    // key = field name
    'kerahasiaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Memang boleh diakses publik'],
            ['value' => '2', 'label' => 'Penting: Hanya untuk kepentingan internal, tidak mengandung data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Memang boleh diakses publik'],
            ['value' => '2', 'label' => 'Penting: Hanya untuk kepentingan internal, tidak menyimpan data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Menyimpan data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Tidak menyimpan data kedinasan'],
            ['value' => '2', 'label' => 'Penting: Mengandung data kedinasan'],
            ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Fasilitas umum tanpa risiko keamanan informasi'],
            ['value' => '2', 'label' => 'Penting: Fasilitas menyimpan aset IT tapi tidak kritikal'],
            ['value' => '3', 'label' => 'Sangat Penting: Fasilitas dengan akses terbatas'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Menangani tugas umum'],
            ['value' => '2', 'label' => 'Penting: Punya akses ke informasi internal'],
            ['value' => '3', 'label' => 'Sangat Penting: Punya akses ke data pribadi sensitif UU PDP atau informasi dikecualikan UU KIP'],
        ],
        '_DEFAULT_' => [
            ['value' => '1', 'label' => 'HALO'],
            ['value' => '2', 'label' => 'SIOs'],
            ['value' => '3', 'label' => 'Whats'],
        ],
    ],
    'integritas' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Bisa menimbulkan kebingungan internal tapi tidak mengganggu layanan utama'],
            ['value' => '3', 'label' => 'Sangat Penting: Berdampak langsung serius pada operasional / pengambilan keputusan / tata kelola'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Bisa mengganggu operasional layanan namun bisa dipulihkan cepat'],
            ['value' => '3', 'label' => 'Sangat Penting: Bisa membuat layanan berhenti atau salah mengambil keputusan'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Bisa mengganggu operasional namun bisa dipulihkan cepat, ada alternatif'],
            ['value' => '3', 'label' => 'Sangat Penting: Bisa membuat kerusakan/malfungsi menyebabkan kesalahan data atau kegagalan besar'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Gangguan kecil tapi tidak kritis'],
            ['value' => '3', 'label' => 'Sangat Penting: Bisa langsung merusak data/perangkat utama'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan: kesalahan SDM tidak berdampak besar'],
            ['value' => '2', 'label' => 'Penting: Kesalahan SDM mem buat gangguan unit tertentu'],
            ['value' => '3', 'label' => 'Sangat Penting: Kesalahan SDM langsung berdampak pada keamanan dan layanan publik'],
        ],
    ],
    'ketersediaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Bisa menimbulkan gangguan sementara, masih bisa jalan dengan alternatif'],
            ['value' => '3', 'label' => 'Sangat Penting: Berdampak langsung menghentikan layanan/operasional'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Jarang digunakan, ada alternatif lainnya'],
            ['value' => '2', 'label' => 'Penting: Digunakan rutin, dapat cepat dipulihkan'],
            ['value' => '3', 'label' => 'Sangat Penting: Tidak boleh mati, bisa menghentikan layanan'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Digunakan harian, ada alternatif'],
            ['value' => '3', 'label' => 'Sangat Penting: Bisa menghentikan layanan jika terganggu'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Pendukung operasional, ada cadangan'],
            ['value' => '3', 'label' => 'Sangat Penting: Wajib ada, kalau terganggu menghentikan layanan'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan: SDM bisa dengan mudah digantikan tanpa mengganggu operasional'],
            ['value' => '2', 'label' => 'Penting: Mengganggu operasional, ada backup'],
            ['value' => '3', 'label' => 'Sangat Penting: Membuat layanan terhenti'],
        ],
    ],
    'keaslian' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Tidak digunakan sebagai referensi resmi'],
            ['value' => '2', 'label' => 'Penting: Sebagai referensi internal tapi tidak menjadi dasar hukum, regulasi dan audit'],
            ['value' => '3', 'label' => 'Sangat Penting: Menjadi dasar hukum, regulasi dan audit'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Untuk kebutuhan internal'],
            ['value' => '3', 'label' => 'Sangat Penting: Untuk pelayanan publik atau dasar hukum'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Tidak kritikal'],
            ['value' => '3', 'label' => 'Sangat Penting: Bisa terjadi serangan pemalsuan'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Tercatat pada inventaris, dampak terbatas'],
            ['value' => '3', 'label' => 'Sangat Penting: Tercatat pada inventaris, mendukung keamanan kritikal'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: SDM perlu dibuktikan sah'],
            ['value' => '3', 'label' => 'Sangat Penting: Posisinya menentukan legalitas tindakan/akses'],
        ],
    ],
    'kenirsangkalan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Tidak ada konsekuensinya'],
            ['value' => '2', 'label' => 'Penting: Akuntabilitas internal untuk penelusuran jika diperlukan'],
            ['value' => '3', 'label' => 'Sangat Penting: Menjadi dokumen resmi yang disahkan'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Akuntabilitas internal diperlukan'],
            ['value' => '3', 'label' => 'Sangat Penting: Aplikasi mengikat hukum/pelayanan publik'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Ruang publik/umum'],
            ['value' => '2', 'label' => 'Penting: Pembuktian audit internal kepemilikan/penggunaan perangkat'],
            ['value' => '3', 'label' => 'Sangat Penting: Menghasilkan log/bukti hukum'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Fasilitas dipakai bebas tanpa log'],
            ['value' => '2', 'label' => 'Penting: Dampak internal tapi perlu bukti siapa yang menggunakan'],
            ['value' => '3', 'label' => 'Sangat Penting: Untuk audit dan hukum'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan'],
            ['value' => '2', 'label' => 'Penting: Tindakan SDM perlu dilacak'],
            ['value' => '3', 'label' => 'Sangat Penting: Berimplikasi hukum'],
        ],
    ],
    // kamu bisa tambah field lain (integritas, ketersediaan, dst) dengan struktur serupa
];
