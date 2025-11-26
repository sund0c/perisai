<?php
return [
    // key = field name
    // 'kerahasiaan' => [
    //     // per klasifikasi
    //     'Data dan Informasi' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Bisa diakses publik'],
    //         ['value' => '2', 'label' => 'Penting: Hanya untuk kepentingan internal, tidak mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //     ],
    //     'Perangkat Lunak' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Bisa diakses publik tanpa login'],
    //         ['value' => '2', 'label' => 'Penting: Harus login, tidak menyimpan data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Menyimpan data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //     ],
    //     'Perangkat Keras' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Tidak menyimpan data'],
    //         ['value' => '2', 'label' => 'Penting: Mengandung data kedinasan, tidak mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //     ],
    //     'Sarana Pendukung' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Berada di ruangan publik atau aset bisa diakses tanpa ijin khusus'],
    //         ['value' => '2', 'label' => 'Penting: Berada di ruangan dengan akses terbatas dan aset bisa diakses tanpa ijin khusus'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Berada di ruangan dengan akses terbatas atau aset hanya bisa diakses dengn ijin khusus'],
    //     ],
    //     'SDM dan Pihak Ketiga' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Tidak punya akses informasi'],
    //         ['value' => '2', 'label' => 'Penting: Punya akses ke informasi internal tapi tidak ke data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Punya akses ke data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
    //     ],
    // ],
    'kerahasiaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
    ],
    // 'integritas' => [
    //     'Data dan Informasi' => [
    //     ['value' => '3', 'label' => 'Sangat Penting'],
    //     ],
    //      'Perangkat Lunak' => [
    //         ['value' => '3', 'label' => 'Sangat Penting'],
    //     ],
    //     'Perangkat Keras' => [
    //         ['value' => '3', 'label' => 'Sangat Penting'],
    //     ],
    //     'Sarana Pendukung' => [
    //         ['value' => '3', 'label' => 'Sangat Penting'],
    //     ],
    //     'SDM dan Pihak Ketiga' => [
    //  ['value' => '3', 'label' => 'Sangat Penting'],
    //     ],
    // ],
    'integritas' => [
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan']
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan']
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan']
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan']
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan']
        ],
    ],
    // 'ketersediaan' => [
    //     // per klasifikasi
    //     'Data dan Informasi' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24'],
    //         ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
    //     ],
    //     'Perangkat Lunak' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Toleransi donwtime bisa lebih dari 1x24 jam'],
    //         ['value' => '2', 'label' => 'Penting: Toleransi downtime maksimal 1x24 jam'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Tidak boleh ada downtime'],

    //     ],
    //     'Perangkat Keras' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Toleransi donwtime bisa lebih dari 1x24 jam'],
    //         ['value' => '2', 'label' => 'Penting: Toleransi downtime maksimal 1x24 jam'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Tidak boleh ada downtime'],
    //     ],
    //     'Sarana Pendukung' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24 jam'],
    //         ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
    //     ],
    //     'SDM dan Pihak Ketiga' => [
    //         ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24 jam'],
    //         ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
    //         ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
    //     ],
    // ],
    'ketersediaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
        ],
    ],
    'keaslian' => [
        'Data dan Informasi' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Perangkat Lunak' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Perangkat Keras' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Sarana Pendukung' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
    ],
    'kenirsangkalan' => [
        'Data dan Informasi' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Perangkat Lunak' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Perangkat Keras' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'Sarana Pendukung' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '0', 'label' => 'N/A'],
        ],
    ],
    // kamu bisa tambah field lain (integritas, ketersediaan, dst) dengan struktur serupa
];
