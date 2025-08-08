<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KlasifikasiAset;
use App\Models\SubKlasifikasiAset;
class SubKlasifikasiAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datainformasi = KlasifikasiAset::where('klasifikasiaset', 'Data dan Informasi')->first();
        $perangkatKeras = KlasifikasiAset::where('klasifikasiaset', 'Perangkat Keras')->first();
        $perangkatLunak = KlasifikasiAset::where('klasifikasiaset', 'Perangkat Lunak')->first();
        $saranapendukung = KlasifikasiAset::where('klasifikasiaset', 'Sarana Pendukung')->first();
        $sdm = KlasifikasiAset::where('klasifikasiaset', 'SDM dan Pihak Ketiga')->first();

        // Data Sub Klasifikasi
        $data = [
            [
                'klasifikasi_aset_id' => $datainformasi->id,
                'subklasifikasiaset' => 'Business Process/Prosedur',
                'penjelasan' => 'Dokumen yang berisikan panduan atau instruksi untuk melakukan suatu kegiatan. Contoh : SOP tentang keamanan informasi, Pedoman Penanganan Insiden, Renstra Organisasi.'
            ],
            [
                'klasifikasi_aset_id' => $datainformasi->id,
                'subklasifikasiaset' => 'Formulir',
                'penjelasan' => 'Dokumen yang berisikan sejumlah pertanyaan atau kolom isian. Contoh : checklist backup data,  formulir permintaan akses.'
            ],
            [
                'klasifikasi_aset_id' => $datainformasi->id,
                'subklasifikasiaset' => 'Data Log dan Audit ',
                'penjelasan' => 'Dokumen yang berisikan log/riwayat dan/atau hasil audit. Contoh : data kepegawaian, change request, laporan hasil indeks kami, laporan hasil audit keamanan, laporan IT security assessment.'
            ],
            [
                'klasifikasi_aset_id' => $datainformasi->id,
                'subklasifikasiaset' => 'Database dan data files ',
                'penjelasan' => 'Dokumen yang tersimpan dalam database atau data yang berupa sumber program. Contoh : source code aplikasi.'
            ],
            [
                'klasifikasi_aset_id' => $datainformasi->id,
                'subklasifikasiaset' => 'Dokumen Kontrak dan Legal',
                'penjelasan' => 'Dokumen kontrak yang terkait dengan layanan organisasi dan hukum. Contoh : kontrak dengan penyedia ISP, dokumen Perjanjian Kerahasiaan/Non-Disclosure Agreement, dokumen MoU / Perjanjian Kerjasama.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatKeras->id,
                'subklasifikasiaset' => 'PC/Laptop/Smartphone',
                'penjelasan' => 'Perangkat operasional yang mendukung layanan organisasi. Contoh : PC, Laptop, Smartphone. '
            ],
            [
                'klasifikasi_aset_id' => $perangkatKeras->id,
                'subklasifikasiaset' => 'Server',
                'penjelasan' => 'Perangkat operasional yang mendukung pengembangan perangkat lunak. Contoh : rak server, server development, server production.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatKeras->id,
                'subklasifikasiaset' => 'Perangkat Jaringan (Network Device)',
                'penjelasan' => 'Perangkat Jaringan TI. Contoh: firewall, router, switch, repeater, bridge, access point, kabel jaringan.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatKeras->id,
                'subklasifikasiaset' => 'Perangkat Penyimpanan (Storage Device)',
                'penjelasan' => 'Perangkat yang digunakan untuk menyimpan data/informasi. Contoh: hardisk, flashdisk.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatLunak->id,
                'subklasifikasiaset' => 'Sistem Operasi',
                'penjelasan' => 'Sistem operasi. Contoh : OS server.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatLunak->id,
                'subklasifikasiaset' => 'Sistem Utility',
                'penjelasan' => 'Perangkat lunak yang digunakan untuk membantu mengelola, memelihara, dan mengoptimalkan kinerja sistem komputer (diluar bawaan sistem operasi). Contoh: Antivirus Kaspersky, WSUS, Citrix.'
            ],
            [
                'klasifikasi_aset_id' => $perangkatLunak->id,
                'subklasifikasiaset' => 'Aplikasi berbasis Website',
                'penjelasan' => 'Perangkat lunak yang diakses melalui browser'
            ],
            [
                'klasifikasi_aset_id' => $perangkatLunak->id,
                'subklasifikasiaset' => 'Aplikasi berbasis Mobile',
                'penjelasan' => 'Perangkat lunak yang diakses/dijalankan melalui perangkat mobile'
            ],
            [
                'klasifikasi_aset_id' => $saranapendukung->id,
                'subklasifikasiaset' => 'Support Appliance',
                'penjelasan' => 'Perangkat pendukung sebagai bagian dari fasilitas pendukung. Contoh : Genset, UPS, APAR, Smoke Detector, Sensor suhu dan kelembapan.'
            ],
            [
                'klasifikasi_aset_id' => $saranapendukung->id,
                'subklasifikasiaset' => 'Support Facility',
                'penjelasan' => 'Lokasi yang mendukung operasional data center. Contoh: lokasi DRC, backup site.'
            ],

                        [
                'klasifikasi_aset_id' => $sdm->id,
                'subklasifikasiaset' => 'Management',
                'penjelasan' => 'Personil pelaksana teknis proses penyediaan layanan TI'
            ],
            [
                'klasifikasi_aset_id' => $sdm->id,
                'subklasifikasiaset' => 'Technical',
                'penjelasan' => 'Personil pelaksana proses penyediaan layanan TI pada tingkat manajerial'
            ],
            [
                'klasifikasi_aset_id' => $sdm->id,
                'subklasifikasiaset' => 'Tenaga Outsource',
                'penjelasan' => 'Pihak ketiga/personil yang bekerja sama dalam pelaksanaan pekerjaan selama jangka waktu tertentu'
            ],
        ];

        foreach ($data as $item) {
            SubKlasifikasiAset::create($item);
        }
    }
}
