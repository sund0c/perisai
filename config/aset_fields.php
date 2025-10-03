<?php
return [
    // key = field name
    'kerahasiaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Bisa diakses publik'],
            ['value' => '2', 'label' => 'Penting: Hanya untuk kepentingan internal, tidak mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Bisa diakses publik tanpa login'],
            ['value' => '2', 'label' => 'Penting: Harus login, tidak menyimpan data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Menyimpan data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Tidak menyimpan data'],
            ['value' => '2', 'label' => 'Penting: Mengandung data kedinasan, tidak mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Mengandung data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Berada di ruangan publik atau aset bisa diakses tanpa ijin khusus'],
            ['value' => '2', 'label' => 'Penting: Berada di ruangan dengan akses terbatas dan aset bisa diakses tanpa ijin khusus'],
            ['value' => '3', 'label' => 'Sangat Penting: Berada di ruangan dengan akses terbatas atau aset hanya bisa diakses dengn ijin khusus'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Tidak punya akses informasi'],
            ['value' => '2', 'label' => 'Penting: Punya akses ke informasi internal tapi tidak ke data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
            ['value' => '3', 'label' => 'Sangat Penting: Punya akses ke data pribadi spesifik UU PDP atau informasi dikecualikan UU KIP'],
        ],
    ],
    'integritas' => [
        'Data dan Informasi' => [
        ['value' => '3', 'label' => 'Sangat Penting'],
        ],
         'Perangkat Lunak' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Perangkat Keras' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Sarana Pendukung' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'SDM dan Pihak Ketiga' => [
     ['value' => '3', 'label' => 'Sangat Penting'],
        ],
    ],
    'ketersediaan' => [
        // per klasifikasi
        'Data dan Informasi' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24'],
            ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
            ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
        ],
        'Perangkat Lunak' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Toleransi donwtime bisa lebih dari 1x24 jam'],
            ['value' => '2', 'label' => 'Penting: Toleransi downtime maksimal 1x24 jam'],
            ['value' => '3', 'label' => 'Sangat Penting: Tidak boleh ada downtime'],

        ],
        'Perangkat Keras' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Toleransi donwtime bisa lebih dari 1x24 jam'],
            ['value' => '2', 'label' => 'Penting: Toleransi downtime maksimal 1x24 jam'],
            ['value' => '3', 'label' => 'Sangat Penting: Tidak boleh ada downtime'],
        ],
        'Sarana Pendukung' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24 jam'],
            ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
            ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
        ],
        'SDM dan Pihak Ketiga' => [
            ['value' => '1', 'label' => 'Tidak signifikan: Toleransi ketidaktersediaan bisa lebih dari 1x24 jam'],
            ['value' => '2', 'label' => 'Penting: Toleransi ketidaktersediaan maksimal 1x24 jam'],
            ['value' => '3', 'label' => 'Sangat Penting: Harus selalu tersedia'],
        ],
    ],
    'keaslian' => [
        'Data dan Informasi' => [
        ['value' => '3', 'label' => 'Sangat Penting'],
        ],
         'Perangkat Lunak' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Perangkat Keras' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Sarana Pendukung' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'SDM dan Pihak Ketiga' => [
     ['value' => '3', 'label' => 'Sangat Penting'],
        ],
    ],
    'kenirsangkalan' => [
         'Data dan Informasi' => [
        ['value' => '3', 'label' => 'Sangat Penting'],
        ],
         'Perangkat Lunak' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Perangkat Keras' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'Sarana Pendukung' => [
            ['value' => '3', 'label' => 'Sangat Penting'],
        ],
        'SDM dan Pihak Ketiga' => [
     ['value' => '3', 'label' => 'Sangat Penting'],
        ],
    ],
    // kamu bisa tambah field lain (integritas, ketersediaan, dst) dengan struktur serupa
];
