<?php

namespace Database\Seeders;

use App\Models\StandarIndikator;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndikatorStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indikatorstandardList = [
            [
                'fungsi_standar_id' => '1',
                'urutan' => '1',
                'indikator' => 'Menerapkan Manajemen Kata Sandi untuk Proses Otentikasi',
                'tujuan' => 'Untuk menjamin otentikasi pengguna dilakukan melalui pengelolaan kata sandi yang terkontrol dan tidak manual',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '2',
                'indikator' => 'Menerapkan Verifikasi Kata Sandi di Sisi Server',
                'tujuan' => 'Untuk menjamin kata sandi tidak bisa dimanipulasi oleh penyerang di sisi client',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '3',
                'indikator' => 'Mengatur Jumlah Karakter, Kombinasi Jenis Karakter, dan Masa Berlaku Kata Sandi',
                'tujuan' => 'Meningkatkan kekuatan kata sandi dan mencegah kata sandi reuse jangka panjang',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '4',
                'indikator' => 'Mengatur Jumlah Maksimum Kesalahan dalam Pemasukan Kata Sandi',
                'tujuan' => 'Menghindari brute-force attack',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '5',
                'indikator' => 'Mengatur Mekanisme Pemulihan Kata Sandi',
                'tujuan' => 'Mengatur Jumlah Maksimum Kesalahan dalam Pemasukan Kata Sandi',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '6',
                'indikator' => 'Menjaga Kerahasiaan Kata Sandi yang Disimpan melalui Mekanisme Kriptografi',
                'tujuan' => 'Mengatur Mekanisme Pemulihan Kata Sandi',
            ],
            [
                'fungsi_standar_id' => '1',
                'urutan' => '7',
                'indikator' => 'Menggunakan Jalur Komunikasi yang Diamankan untuk Proses Autentikasi',
                'tujuan' => 'Menjaga Kerahasiaan Kata Sandi yang Disimpan melalui Mekanisme Kriptografi',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '1',
                'indikator' => 'Menggunakan Pengendali Sesi untuk Proses Manajemen Sesi',
                'tujuan' => 'Untuk menjaga status otentikasi secara aman antar request',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '2',
                'indikator' => 'Menggunakan Pengendali Sesi yang Disediakan oleh Kerangka Kerja Aplikasi',
                'tujuan' => 'Kerangka kerja (framework) menyediakan session handler yang sudah diuji dan dilindungi terhadap serangan umum (XSS, CSRF, Session Fixation).',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '3',
                'indikator' => 'Mengatur Pembuatan dan Keacakan Token Sesi',
                'tujuan' => 'Token yang lemah dapat ditebak oleh penyerang 🡪 hijacking session (Session Prediction Attack)',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '4',
                'indikator' => 'Mengatur Kondisi dan Jangka Waktu Habis Sesi (Session Expiry)',
                'tujuan' => 'Mencegah akses tidak sah akibat sesi yang tetap aktif terlalu lama.',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '5',
                'indikator' => 'Validasi dan Pencantuman Session ID',
                'tujuan' => 'Pencantuman session ID di URL sangat rawan session hijacking via referrer log, history, dsb',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '6',
                'indikator' => 'Pelindungan terhadap Lokasi dan Pengiriman Token untuk Sesi Terautentikasi',
                'tujuan' => 'Mencegah cookie diambil via XSS (tanpa HttpOnly) atau dicuri via CSRF (tanpa SameSite)',
            ],
            [
                'fungsi_standar_id' => '2',
                'urutan' => '7',
                'indikator' => 'Pelindungan terhadap Duplikasi dan Mekanisme Persetujuan Pengguna',
                'tujuan' => 'Pelindungan terhadap Duplikasi dan Mekanisme Persetujuan Pengguna',
            ],
            [
                'fungsi_standar_id' => '3',
                'urutan' => '1',
                'indikator' => 'Menetapkan otorisasi pengguna untuk membatasi kontrol akses',
                'tujuan' => 'Untuk mencegah privilege escalation atau data leakage antar pengguna. Memenuhi prinsip least privilege dan segregation of duties.',
            ],
            [
                'fungsi_standar_id' => '3',
                'urutan' => '2',
                'indikator' => 'Mengatur peringatan terhadap bahaya serangan otomatis (brute force /flooding /DDOS)',
                'tujuan' => 'Untuk mencegah brute force, credential stuffing, maupun flood attack yang bisa menurunkan performa server dan menyebabkan kebocoran akses',
            ],
            [
                'fungsi_standar_id' => '3',
                'urutan' => '3',
                'indikator' => 'Mengatur antarmuka pada sisi administrator',
                'tujuan' => 'Untuk memastikan separation of role dan mengurangi risiko human error dari pihak non-admin',
            ],
            [
                'fungsi_standar_id' => '3',
                'urutan' => '4',
                'indikator' => 'Mengatur verifikasi kebenaran token ketika mengakses data dan informasi yang dikecualikan ',
                'tujuan' => 'Mengatur verifikasi kebenaran token ketika mengakses data dan informasi yang dikecualikan',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '1',
                'indikator' => 'Menerapkan fungsi validasi input pada sisi server',
                'tujuan' => 'Validasi sisi klien bisa dilewati. Validasi server menjamin keamanan walaupun input dikirim via cURL/Insomnia',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '2',
                'indikator' => 'Menerapkan mekanisme penolakan input jika terjadi kesalahan validasi',
                'tujuan' => 'Mencegah data tidak valid atau berbahaya masuk ke dalam sistem',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '3',
                'indikator' => 'Memastikan runtime environment aplikasi tidak rentan terhadap serangan validasi input',
                'tujuan' => 'Error detail dapat dimanfaatkan untuk serangan lanjutan (information leakage)',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '4',
                'indikator' => 'Melakukan validasi positif pada seluruh input',
                'tujuan' => 'Validasi positif lebih aman dibanding validasi negatif (blacklist), yang mudah dilewati dengan teknik obfuscation',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '5',
                'indikator' => 'Melakukan filter terhadap data yang tidak dipercaya',
                'tujuan' => 'Mencegah XSS, command injection, atau manipulasi output',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '6',
                'indikator' => 'Menggunakan fitur source code dinamis',
                'tujuan' => 'Mencegah eksekusi skrip berbahaya di sisi klien',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '7',
                'indikator' => 'Melakukan pelindungan terhadap akses yang mengandung konten skrip',
                'tujuan' => 'Mencegah serangan Cross-Site Scripting (XSS)',
            ],
            [
                'fungsi_standar_id' => '4',
                'urutan' => '8',
                'indikator' => 'Melakukan pelindungan dari serangan injeksi basis data',
                'tujuan' => 'Menghindari SQL Injection',
            ],
            [
                'fungsi_standar_id' => '5',
                'urutan' => '1',
                'indikator' => 'Menggunakan algoritma/modul/protokol/kunci kriptografi sesuai peraturan',
                'tujuan' => 'Menghindari penggunaan algoritma yang sudah rentan dan tidak direkomendasikan',
            ],
            [
                'fungsi_standar_id' => '5',
                'urutan' => '2',
                'indikator' => 'Melakukan autentikasi data yang dienkripsi',
                'tujuan' => 'Tanpa autentikasi, data terenkripsi bisa diubah dan hasil dekripsi bisa tidak valid (integrity attack)',
            ],
            [
                'fungsi_standar_id' => '5',
                'urutan' => '3',
                'indikator' => 'Menerapkan manajemen kunci kriptografi',
                'tujuan' => 'Kunci adalah titik paling kritikal dalam sistem kriptografi. Jika bocor, semua perlindungan jadi tidak berguna',
            ],
            [
                'fungsi_standar_id' => '5',
                'urutan' => '4',
                'indikator' => 'Membuat angka acak dengan generator angka acak kriptografi',
                'tujuan' => 'Non-CSPRNG (Cryptographically Secure Pseudo-Random Number Generator) bisa diprediksi dan dimanfaatkan untuk serangan seperti token hijacking, session fixation, dll',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '1',
                'indikator' => 'Mengatur konten pesan yang ditampilkan ketika terjadi kesalahan',
                'tujuan' => 'Untuk menghindari informasi teknis bocor yang bisa digunakan penyerang untuk eksploitasi',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '2',
                'indikator' => 'Menggunakan metode penanganan error untuk mencegah kesalahan terprediksi dan tidak terduga serta menangani seluruh pengecualian yang tidak ditangani',
                'tujuan' => 'Meningkatkan resiliensi aplikasi dan mencegah celah keamanan akibat error tak tertangani',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '3',
                'indikator' => 'Tidak mencantumkan informasi yang dikecualikan dalam pencatatan log',
                'tujuan' => 'Membantu proses forensik digital dan deteksi insiden keamanan',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '4',
                'indikator' => 'Mengatur cakupan log yang dicatat untuk mendukung upaya penyelidikan ketika terjadi insiden',
                'tujuan' => 'Membantu proses forensik digital dan deteksi insiden keamanan',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '5',
                'indikator' => 'Mengatur pelindungan log aplikasi dari akses dan modifikasi yang tidak sah',
                'tujuan' => 'Log tidak bisa diubah atau dihapus sembarangan',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '6',
                'indikator' => 'Melakukan enkripsi pada data yang disimpan untuk mencegah injeksi log',
                'tujuan' => 'Melindungi sistem SIEM/log viewer dari serangan injeksi log dan enkripsi sebagai lapis proteksi tambahan',
            ],
            [
                'fungsi_standar_id' => '6',
                'urutan' => '7',
                'indikator' => 'Melakukan sinkronisasi sumber waktu sesuai dengan zona waktu dan waktu yang benar',
                'tujuan' => 'Waktu log yang tepat krusial untuk investigasi insiden dan korelasi antar sistem.',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '1',
                'indikator' => 'Melakukan identifikasi dan penyimpanan salinan informasi yang dikecualikan',
                'tujuan' => 'Melindungi data sensitif dari paparan tidak sengaja atau akses tidak sah',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '2',
                'indikator' => 'Melakukan pelindungan dari akses yang tidak sah terhadap informasi yang dikecualikan yang disimpan sementara dalam aplikasi',
                'tujuan' => 'Data sensitif yang disimpan sementara tetap berisiko jika tidak dilindungi',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '3',
                'indikator' => 'Melakukan pertukaran, penghapusan, dan audit informasi yang dikecualikan',
                'tujuan' => 'Menjawab kewajiban auditabilitas dan hak subjek data',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '4',
                'indikator' => 'Melakukan penentuan jumlah parameter',
                'tujuan' => 'Mencegah serangan seperti over-posting, mass assignment, dan buffer overflow',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '5',
                'indikator' => 'Memastikan data disimpan dengan aman',
                'tujuan' => 'Mencegah pencurian data dari penyimpanan langsung',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '6',
                'indikator' => 'Menentukan metode untuk menghapus dan mengekspor data sesuai permintaan pengguna',
                'tujuan' => 'Kepatuhan terhadap UU PDP dan praktik privasi global (GDPR, dll)',
            ],
            [
                'fungsi_standar_id' => '7',
                'urutan' => '7',
                'indikator' => 'Membersihkan memori setelah tidak diperlukan',
                'tujuan' => 'Menghindari kebocoran melalui cache, memory dump, atau rekonstruksi data',
            ],
            [
                'fungsi_standar_id' => '8',
                'urutan' => '1',
                'indikator' => 'Menggunakan Komunikasi Terenkripsi',
                'tujuan' => 'Untuk mencegah man-in-the-middle attack, sniffing, dan integritas data yang rusak selama transmisi',
            ],
            [
                'fungsi_standar_id' => '8',
                'urutan' => '2',
                'indikator' => 'Mengatur Koneksi Masuk & Keluar yang Aman dan Terenkripsi dari Sisi Pengguna',
                'tujuan' => 'Agar data pengguna (seperti kata sandi/token) tidak bocor melalui jaringan publik',
            ],
            [
                'fungsi_standar_id' => '8',
                'urutan' => '3',
                'indikator' => 'Mengatur Jenis Algoritma yang Digunakan dan Alat Pengujiannya',
                'tujuan' => 'Algoritma lemah dapat dibobol oleh serangan brute-force atau collision',
            ],
            [
                'fungsi_standar_id' => '8',
                'urutan' => '4',
                'indikator' => 'Mengatur Aktivasi & Konfigurasi Sertifikat Elektronik oleh Penyelenggara Sertifikasi Elektronik',
                'tujuan' => 'Sertifikat dari PSrE memberi jaminan hukum dan teknis yang kuat terhadap integritas identitas',
            ],
            [
                'fungsi_standar_id' => '9',
                'urutan' => '1',
                'indikator' => 'Menggunakan analisis source code dalam kontrol kode berbahaya',
                'tujuan' => 'Mendeteksi potensi kerentanan seperti RCE (Remote Code Execution), XSS, LFI, dll sebelum aplikasi dijalankan',
            ],
            [
                'fungsi_standar_id' => '9',
                'urutan' => '2',
                'indikator' => 'Memastikan source code sumber aplikasi dan pustaka tidak mengandung kode berbahaya dan fungsionalitas lain yang tidak diinginkan',
                'tujuan' => 'Pustaka pihak ketiga sering menjadi pintu masuk malware (supply chain attack)',
            ],
            [
                'fungsi_standar_id' => '9',
                'urutan' => '3',
                'indikator' => 'Mengatur izin terkait fitur atau sensor terkait privasi',
                'tujuan' => 'Menghindari pelanggaran privasi dan ketidaksengajaan pengambilan data sensitif',
            ],
            [
                'fungsi_standar_id' => '9',
                'urutan' => '4',
                'indikator' => 'Mengatur pelindungan integritas',
                'tujuan' => 'Mencegah manipulasi file JS/CSS dari CDN atau penyerang melalui MITM',
            ],
            [
                'fungsi_standar_id' => '9',
                'urutan' => '5',
                'indikator' => 'Mengatur mekanisme fitur pembaruan',
                'tujuan' => 'Update aplikasi sering menjadi target backdoor jika tidak dilindungi (contoh: SolarWinds breach)',
            ],
            [
                'fungsi_standar_id' => '10',
                'urutan' => '1',
                'indikator' => 'Memproses alur logika bisnis dalam urutan langkah dan waktu yang realistis',
                'tujuan' => 'Mencegah manipulasi alur (e.g. bypass pembayaran, akses tanpa otorisasi)',
            ],
            [
                'fungsi_standar_id' => '10',
                'urutan' => '2',
                'indikator' => 'Memastikan logika bisnis memiliki batasan dan validasi',
                'tujuan' => 'Mencegah eksploitasi aturan logika seperti potongan harga berlebihan, kuantitas transaksi tidak logis, dll',
            ],
            [
                'fungsi_standar_id' => '10',
                'urutan' => '3',
                'indikator' => 'Memonitor aktivitas yang tidak biasa',
                'tujuan' => 'Memberikan peringatan awal terhadap serangan logika bisnis (fraud, abuse)',
            ],
            [
                'fungsi_standar_id' => '10',
                'urutan' => '4',
                'indikator' => 'Membantu dalam kontrol antiotomatisasi',
                'tujuan' => 'Menghindari spam, brute-force, dan abuse otomatisasi',
            ],
            [
                'fungsi_standar_id' => '10',
                'urutan' => '5',
                'indikator' => 'Memberikan peringatan ketika terjadi serangan otomatis atau aktivitas yang tidak biasa',
                'tujuan' => 'Respon cepat mencegah eskalasi serangan logika bisnis',
            ],
            [
                'fungsi_standar_id' => '11',
                'urutan' => '1',
                'indikator' => 'Mengatur Jumlah File dan Kuota Ukuran File per Pengguna',
                'tujuan' => 'Untuk menghindari penyalahgunaan penyimpanan (storage abuse) atau DoS (Denial of Service) melalui unggahan massal',
            ],
            [
                'fungsi_standar_id' => '11',
                'urutan' => '2',
                'indikator' => 'Validasi File Sesuai Tipe Konten',
                'tujuan' => 'Mencegah file polyglot attack, upload web shell, atau eksekusi file berbahaya',
            ],
            [
                'fungsi_standar_id' => '11',
                'urutan' => '3',
                'indikator' => 'Perlindungan Metadata Input dan File',
                'tujuan' => 'Menghindari kebocoran informasi sensitif yang tidak disadari dari metadata file',
            ],
            [
                'fungsi_standar_id' => '11',
                'urutan' => '4',
                'indikator' => 'Pemindaian File dari Sumber Tidak Dipercaya',
                'tujuan' => 'Melindungi sistem dari infeksi malware melalui unggahan file',
            ],
            [
                'fungsi_standar_id' => '11',
                'urutan' => '5',
                'indikator' => 'Konfigurasi Server Unduh File Sesuai Ekstensi',
                'tujuan' => 'Mencegah file execution attack, seperti RCE dari folder upload',
            ],
            [
                'fungsi_standar_id' => '12',
                'urutan' => '1',
                'indikator' => 'Mengonfigurasi server sesuai Standard server aplikasi dan kerangka kerja aplikasi yang digunakan',
                'tujuan' => 'Konfigurasi default sering membuka celah keamanan (contoh: menampilkan stack trace atau konfigurasi server ke publik)',
            ],
            [
                'fungsi_standar_id' => '12',
                'urutan' => '2',
                'indikator' => 'Mendokumentasi, menyalin konfigurasi, dan semua dependensi',
                'tujuan' => 'Membantu audit keamanan dan pemulihan jika terjadi insiden atau migrasi',
            ],
            [
                'fungsi_standar_id' => '12',
                'urutan' => '3',
                'indikator' => 'Menghapus fitur, dokumentasi, sampel, dan konfigurasi yang tidak diperlukan',
                'tujuan' => 'Sampel dan dokumentasi sering menjadi pintu masuk penyerang untuk eksploitasi',
            ],
            [
                'fungsi_standar_id' => '12',
                'urutan' => '4',
                'indikator' => 'Memvalidasi integritas aset jika aset aplikasi diakses secara eksternal',
                'tujuan' => 'File yang diakses publik dapat disusupi malware jika tidak dijaga integritasnya',
            ],
            [
                'fungsi_standar_id' => '12',
                'urutan' => '5',
                'indikator' => 'Menggunakan respons aplikasi dan konten yang aman',
                'tujuan' => 'Header yang tepat dapat mencegah eksploitasi browser dan penyisipan konten berbahaya (seperti XSS)',
            ],

        ];

        foreach ($indikatorstandardList as $item) {
            StandarIndikator::create($item);
        }
    }
}
