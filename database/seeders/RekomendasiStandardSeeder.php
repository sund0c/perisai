<?php

namespace Database\Seeders;

use App\Models\RekomendasiStandard;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RekomendasiStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rekomendasistandardList = [
            [
                'standar_indikator_id' => '1',
                'rekomendasi' => 'Menggunakan username/email + kata sandi dan menyimpan data pengguna di tabel users atau sejenisnya',
                'buktidukung' => 'Screenshot tabel yang menyimpan kata sandi dan screenshot source code untuk menyimpan kata sandi',
            ],
            [
                'standar_indikator_id' => '2',
                'rekomendasi' => 'Melakukan verifikasi kata sandi di lakukan di sisi server',
                'buktidukung' => 'Screenshot source code untuk melakukan verifikasi (contoh: hash:check(), dan lain-lain)',
            ],
            [
                'standar_indikator_id' => '3',
                'rekomendasi' => 'Mengatur jumlah karakter kata sandi minimal 12 karakter dan maksimal 64 karakter',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '3',
                'rekomendasi' => 'Mengatur kombinasi karakter kata sandi minimal terdiri dari 1 huruf besar, 1 huruf kecil, 1 angka dan 1 simbol',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '3',
                'rekomendasi' => 'Mengatur masa berlaku kata sandi maksimal 90 hari dan akan memaksa user untuk melakukan penggantian',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '4',
                'rekomendasi' => 'Mengatur jumlah gagal login maksimal 5x dalam waktu maksimal 15 menit mengunci akun untuk sementara (throttling)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '5',
                'rekomendasi' => 'Token reset tidak membawa informasi sensitif pengguna',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '5',
                'rekomendasi' => 'Tautan reset maksimal satu kali pakai',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '5',
                'rekomendasi' => 'Tautan reset kadaluwarsa maksimal 60 menit',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '6',
                'rekomendasi' => 'Menerapkan penyimpanan kata sandi dalam bentuk hash/terenkripsi, bukan plaintext',
                'buktidukung' => 'Screenshot tabel yang menyimpan kata sandi',
            ],
            [
                'standar_indikator_id' => '6',
                'rekomendasi' => 'Menerapkan algoritma hash bcrypt, argon2, atau scrypt',
                'buktidukung' => 'Screenshot source code untuk melakukan password hashing',
            ],
            [
                'standar_indikator_id' => '7',
                'rekomendasi' => 'Tidak ada mixed content HTTP saat login',
                'buktidukung' => 'Screenshot DevTools > Network > Url (menunjukkan semua protokol = https)',
            ],
            [
                'standar_indikator_id' => '7',
                'rekomendasi' => 'Menggunakan Sertifikat SSL yang valid dan tidak self-signed ',
                'buktidukung' => 'Screenshot hasil test Hostname https://www.ssllabs.com/ssltest/',
            ],
            [
                'standar_indikator_id' => '8',
                'rekomendasi' => 'Menggunakan metode stateful (PHPSESSID, laravel_session, JSESSIONID dll atau Menggunakan token autentikasi seperti JWT yang divalidasi di server) untuk penyimpanan sesi login',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '9',
                'rekomendasi' => 'Menggunakan pengendali sesi dari kerangka kerja aplikasi',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '10',
                'rekomendasi' => 'Panjang token > 32 character',
                'buktidukung' => 'Screenshot hasil https://gchq.github.io/CyberChef/',
            ],
            [
                'standar_indikator_id' => '10',
                'rekomendasi' => 'Entropy token > 4.5 bits/char',
                'buktidukung' => 'Screenshot hasil https://gchq.github.io/CyberChef/',
            ],
            [
                'standar_indikator_id' => '10',
                'rekomendasi' => 'Mengatur agar tidak ada reuse token yang sama antar sesi atau antar pengguna',
                'buktidukung' => 'Screenshot verifikasi gagal login ketika mencoba intercept (dengan BurpSuite atau DevTools) untuk menggunakan token sebelumnya',
            ],
            [
                'standar_indikator_id' => '11',
                'rekomendasi' => 'Mengatur waktu otomatis logout setelah periode inaktif (idle timeout) maksimal 30 menit',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '11',
                'rekomendasi' => 'Mengatur waktu expiry absolut maksimal 1 jam sejak login',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '12',
                'rekomendasi' => 'Mengatur URL tidak ada sisipan Session ID (ke halaman pertama setelah login)',
                'buktidukung' => 'Screenshot isi URL halaman pertama setelah login/halaman sensitif lainnya',
            ],
            [
                'standar_indikator_id' => '12',
                'rekomendasi' => 'Menerapkan validasi perubahan IP/user-agent atau fingerprinting browser',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '13',
                'rekomendasi' => 'Menggunakan atribut cookie ke Secure, HttpOnly, dan SameSite ( samesite =None wajib kombinasi dengan secure)',
                'buktidukung' => 'Screenshot atribut cookie di DevTools > Application > Storage > Cookies > domainkita',
            ],
            [
                'standar_indikator_id' => '14',
                'rekomendasi' => 'Menerapkan mekanisme tidak bisa login ganda untuk pengguna yang sama',
                'buktidukung' => 'Screenshot login error atau kondisi saat login aplikasi ketika login ganda',
            ],
            [
                'standar_indikator_id' => '15',
                'rekomendasi' => 'Setiap user hanya dapat mengakses fitur sesuai haknya',
                'buktidukung' => 'Screenshot halaman masing-masing role users',
            ],
            [
                'standar_indikator_id' => '15',
                'rekomendasi' => 'Tidak bisa akses data pengguna/entitas lain',
                'buktidukung' => 'Screenshot pesan gagal dari aplikasi jika dipaksakan user A mencoba mengakses user lainnya atau role lainnya',
            ],
            [
                'standar_indikator_id' => '16',
                'rekomendasi' => 'Menerapkan mekanisme Rate limiting',
                'buktidukung' => 'Screenshot pesan peringatan dari aplikasi saat rate limiting bekerja ketika bruteforce',
            ],
            [
                'standar_indikator_id' => '16',
                'rekomendasi' => 'Menerapkan Firewall (WAF atau Fail2Ban)',
                'buktidukung' => 'Screenshot isi WAF atau Fail2ban ketika terjadi bruteforce',
            ],
            [
                'standar_indikator_id' => '17',
                'rekomendasi' => 'Admin mempunyai fitur memonitor user, perubahan data dan log akses',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '17',
                'rekomendasi' => 'Fungsi seperti CRUD, verifikasi data, dan pengaturan sistem hanya tersedia bagi admin',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '18',
                'rekomendasi' => 'Memiliki mekanisme verifikasi token',
                'buktidukung' => 'Screenshot verifikasi gagal ketika mencoba token yang salah',
            ],
            [
                'standar_indikator_id' => '19',
                'rekomendasi' => 'Menerapkan mekanisme validasi input pada sisi server',
                'buktidukung' => 'Screenshot verifikasi gagal simpan ketika mencoba intercept dengan mengganti inputan yang tidak valid',
            ],
            [
                'standar_indikator_id' => '20',
                'rekomendasi' => 'Menerapkan mekanisme penolakan input jika tidak valid di seluruh inputan',
                'buktidukung' => 'Screenshot gagal simpan dari aplikasi ketika input tidak valid',
            ],
            [
                'standar_indikator_id' => '21',
                'rekomendasi' => 'Mengatur konfigurasi pesan error off, seperti display_errors=Off, error_reporting=E_ALL & ~E_NOTICE)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '22',
                'rekomendasi' => 'Menerapkan validasi whitelist (contoh: hanya angka, huruf tertentu, format email). Input hanya boleh sesuai format yang diperbolehkan, bukan hanya menolak input tertentu. Misalkan /^[a-zA-Z\s]{3,30}$/',
                'buktidukung' => 'Screenshot source code untuk validasi positif',
            ],
            [
                'standar_indikator_id' => '23',
                'rekomendasi' => 'Menerapkan filter dari sumber tidak terpercaya dibersihkan sebelum ditampilkan atau disimpan. Sumber yang tidak dipercaya bisa datang dari semua hal seperti formulir, cache, API, url dll. Bisa Menggunakan htmlspecialchars(), strip_tags() (PHP), express-validator (Node.js), dsb',
                'buktidukung' => 'Screenshot source code yang digunakan untuk filter',
            ],
            [
                'standar_indikator_id' => '24',
                'rekomendasi' => 'Menggunakan escape output digunakan secara default',
                'buktidukung' => 'Screenshot tampilan sistem untuk script berbahaya  <script>alert("XSS")</script> namun tidak bisa dieksekusi',
            ],
            [
                'standar_indikator_id' => '25',
                'rekomendasi' => 'Konten skrip tidak dijalankan, melainkan di-encode',
                'buktidukung' => 'Screenshot tampilan sistem untuk script berbahaya  <script>alert("XSS")</script> namun tidak bisa dieksekusi',
            ],
            [
                'standar_indikator_id' => '26',
                'rekomendasi' => 'Menggunakan prepared statement / ORM. Beberapa framework sudah melakukan ini di balik layar',
                'buktidukung' => 'Screenshot prilaku sistem ketika diinputkan injection',
            ],
            [
                'standar_indikator_id' => '27',
                'rekomendasi' => 'Menggunakan algoritma hash  : bcrypt, Argon2, atau PBKDF2',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '27',
                'rekomendasi' => 'Menggunakan algoritma enkripsi AES-256 (hanya jika menyimpan data sensitif)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '27',
                'rekomendasi' => 'Menggunakan TLS minimal TLS 1.2 (idealnya TLS 1.3)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '28',
                'rekomendasi' => 'Menggunakan mode AES-GCM (jika menggunakan enkripsi)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '29',
                'rekomendasi' => 'Harus disimpan di Key Management Service (KMS), vault, environment variable terenkripsi, atau file di luar web root. Contoh di Laravel APP_KEY di file .env.',
                'buktidukung' => 'Screenshot struktur folder tempat kunci',
            ],
            [
                'standar_indikator_id' => '29',
                'rekomendasi' => 'Melakukan rotasi berkala (misalnya tiap 6 bulan)',
                'buktidukung' => 'Screenshot catatan rotasi (log)',
            ],
            [
                'standar_indikator_id' => '30',
                'rekomendasi' => 'Semua angka acak yang berhubungan dengan keamanan tidak dapat ditebak ',
                'buktidukung' => 'Screenshot source code untuk menciptakan angka acak dan screenshot berturut-turut kode acak token',
            ],
            [
                'standar_indikator_id' => '31',
                'rekomendasi' => 'Saat login salah, hanya menampilkan pesan umum dan ramah pengguna, seperti: "Terjadi kesalahan. Silakan coba kembali nanti"',
                'buktidukung' => 'Screenshot pesan kesalahan sistem',
            ],
            [
                'standar_indikator_id' => '32',
                'rekomendasi' => 'Tidak ada error yang "lepas" ke pengguna atau menimbulkan blank page. Semua error tercatat dan tertangani',
                'buktidukung' => 'Screenshot source code try-catch atau source code konfigurasi global exception handler',
            ],
            [
                'standar_indikator_id' => '33',
                'rekomendasi' => 'Log hanya mencatat metadata, tanpa menyimpan informasi sensitif (contoh: hanya mencatat "login failed" bukan kata sandi: 123456)',
                'buktidukung' => 'Screenshot isi log sistem ketika terjadi kesalahan login',
            ],
            [
                'standar_indikator_id' => '34',
                'rekomendasi' => 'Log mencatat Timestamp, IP address, User ID / username, URL yang diakses. Metode HTTP',
                'buktidukung' => 'Screenshot isi log sistem',
            ],
            [
                'standar_indikator_id' => '35',
                'rekomendasi' => 'Pastikan log hanya bisa ditulis oleh aplikasi, dan hanya bisa dibaca oleh pengguna tertentu (misal: root, syslog). Misalnya di Laravel log di simpan di /var/www/laravel/storage/logs/laravel.log. Ubah owner menjadi www-data:root (www-data boleh nulis, root yang mengawasi). Set akses file ke 640. (6=RW www-data, 4=R root, 0= others tidak boleh akses sama sekali. Agar file hanya bisa ditambah tidak bisa dihapus bisa Menggunakan chattr +a path',
                'buktidukung' => 'Screenshot source code chmod dan chown file log',
            ],
            [
                'standar_indikator_id' => '35',
                'rekomendasi' => 'Menerapkan log immutability  (misal: via rsyslog, logstash, atau WORM/ Write Once Read Many storage)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '36',
                'rekomendasi' => 'Log tidak bisa disusupi payload berbahaya seperti skrip XSS atau perintah palsu',
                'buktidukung' => 'Screenshot source code filter kode berbahaya di log',
            ],
            [
                'standar_indikator_id' => '37',
                'rekomendasi' => 'Aktifkan NTP di server',
                'buktidukung' => 'Screenshot setting NTP',
            ],
            [
                'standar_indikator_id' => '37',
                'rekomendasi' => 'Pastikan zona waktu server sesuai',
                'buktidukung' => 'Screenshot zona waktu',
            ],
            [
                'standar_indikator_id' => '38',
                'rekomendasi' => 'Mempunyai dokumen penilaian Kategorisasi SE dan Data Inventori Register
',
                'buktidukung' => 'Screenshot/Foto/Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '38',
                'rekomendasi' => 'Salinan Informasi/backup yang mengandung field dikecualikan/data pribadi sensitif tetap tersimpan dalam format terenkripsi
',
                'buktidukung' => 'Screenshot/Foto/Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '38',
                'rekomendasi' => 'Standard b disimpan di Lokasi yang tidak dapat diakses publik.
',
                'buktidukung' => 'Screenshot/Foto/Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '39',
                'rekomendasi' => 'Melakukan perlindungan data sementara seperti session, cache, form',
                'buktidukung' => 'Screenshot php.ini atau konfigurasi framework lainnya seperti contohnya  session.cookie_httponly = 1, session.cookie_secure = 1, session.use_strict_mode = 1 atau sejenisnya',
            ],
            [
                'standar_indikator_id' => '40',
                'rekomendasi' => 'User dapat memodifikasi, menghapus dan mengekspor datanya sendiri',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '40',
                'rekomendasi' => 'User dapat melihat log siapa yang mengakses/menghapus datanya',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '41',
                'rekomendasi' => 'Aplikasi hanya menerima parameter yang diharapkan dan membatasi kelebihan input',
                'buktidukung' => 'Screenshot source code yang melakukan pembatasan parameter yang diharapkan',
            ],
            [
                'standar_indikator_id' => '42',
                'rekomendasi' => 'Enkripsi at rest',
                'buktidukung' => 'Screenshot data terenkripsi di database',
            ],
            [
                'standar_indikator_id' => '43',
                'rekomendasi' => 'Ada SOP penghapusan data yang dimiliki oleh pengguna',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '43',
                'rekomendasi' => 'Ada SOP ekspor data yang dimiliki oleh pengguna',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '44',
                'rekomendasi' => 'Header cache Cache-Control: no-store, no-cache, must-revalidate, max-age=0, Pragma: no-cache, Expires: 0',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],


            [
                'standar_indikator_id' => '45',
                'rekomendasi' => 'Semua komunikasi terenkripsi Menggunakan TLS 1.2 atau 1.3 🡪 Valid (tidak expired), Diterbitkan oleh CA (Certificate Authority) terpercaya',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '45',
                'rekomendasi' => 'Tidak ada mixed content',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '46',
                'rekomendasi' => 'Tidak ada komunikasi plaintext',
                'buktidukung' => 'Screenshot DevTools terkait respon server yang tidak ada plaintext saat login',
            ],
            [
                'standar_indikator_id' => '46',
                'rekomendasi' => 'Mengatur hanya membuka port yang diperlukan saja',
                'buktidukung' => 'Screenshot konfigurasi port yang dibuka',
            ],
            [
                'standar_indikator_id' => '46',
                'rekomendasi' => 'Mengatur redirect otomatis ke HTTPS (RewriteRule di .htaccess, atau di server config)',
                'buktidukung' => 'Screenshot konfigurasi redirect ke HTTPS',
            ],
            [
                'standar_indikator_id' => '46',
                'rekomendasi' => 'Mengatur cookie menggunakan atribut Secure dan HttpOnly',
                'buktidukung' => 'Screenshot DevTools tab Application-Storage-Cookies-domainkita',
            ],
            [
                'standar_indikator_id' => '47',
                'rekomendasi' => 'Sertifikat dan channel komunikasi Menggunakan enkripsi modern ',
                'buktidukung' => 'Screenshot hasil https://www.ssllabs.com/ssltest/',
            ],
            [
                'standar_indikator_id' => '48',
                'rekomendasi' => 'Menggunakan SSL Pemprov Bali ',
                'buktidukung' => 'Screenshot hasil https://www.ssllabs.com/ssltest/',
            ],
            [
                'standar_indikator_id' => '49',
                'rekomendasi' => 'Melakukan Uji Keamanan Aplikasi atau menerapkan static code analysis (SAST) seperti SonarQube, ESLint, PHPStan dll dalam CI/CD pipeline dan semua issue beresiko tinggi sudah ditindaklanjuti',
                'buktidukung' => 'Dokumen Laporan Hasil Uji Keamanan aplikasi dan tindak lanjutnya atau screenshot penggunaan SAST',
            ],
            [
                'standar_indikator_id' => '50',
                'rekomendasi' => 'Melakukan Uji Keamanan Aplikasi atau menerapkan static code analysis (SAST) seperti SonarQube, ESLint, PHPStan dll dalam CI/CD pipeline dan semua issue beresiko tinggi sudah ditindaklanjuti',
                'buktidukung' => 'Dokumen Laporan Hasil Uji Keamanan aplikasi dan tindak lanjutnya atau screenshot penggunaan SAST',
            ],
            [
                'standar_indikator_id' => '51',
                'rekomendasi' => 'Fitur kamera, location, microphone, akses files lokal hanya aktif setelah pengguna menyetujui',
                'buktidukung' => 'Screenshot ijin ke pengguna sebelum menggunakan fitur privasi',
            ],
            [
                'standar_indikator_id' => '52',
                'rekomendasi' => 'Mengaktifkan Subresource Integrity (SRI) pada script CDN: <script src="..." integrity="sha384-..."></script>',
                'buktidukung' => 'Screenshot penggunaan SRI',
            ],
            [
                'standar_indikator_id' => '52',
                'rekomendasi' => 'Menggunakan  Content Security Policy (CSP) untuk mencegah load script asing',
                'buktidukung' => 'Screenshot hasil https://securityheaders.com/ untuk melihat CSP',
            ],
            [
                'standar_indikator_id' => '53',
                'rekomendasi' => 'Melakukan dokumentasi proses update (history) berisikan Versi yang digunakan, Siapa yang men-deploy, Tanggal dan waktu update dan Hasil verifikasi.',
                'buktidukung' => 'Dokumentasi history update',
            ],
            [
                'standar_indikator_id' => '54',
                'rekomendasi' => 'Mempunyai Dokumen sah Proses Bisnis aplikasi ',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '54',
                'rekomendasi' => 'Mempunyai Dokumen sah SOP dalam memberikan layanan',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '54',
                'rekomendasi' => 'Mempunyai Dokumen sah Juknis/Pedoman/Manual Book bagi pengguna dalam menjalankan aplikasi',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '54',
                'rekomendasi' => 'Mempunyai Dokumen sah User Acceptance Test (UAT) aplikasi (uji aplikasi sesuai spesifikasi kebutuhan)',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '55',
                'rekomendasi' => 'Mempunyai Dokumen sah Proses Bisnis aplikasi ',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '55',
                'rekomendasi' => 'Mempunyai Dokumen sah SOP dalam memberikan layanan',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '55',
                'rekomendasi' => 'Mempunyai Dokumen sah Juknis/Pedoman/Manual Book bagi pengguna dalam menjalankan aplikasi',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '55',
                'rekomendasi' => 'Mempunyai Dokumen sah User Acceptance Test (UAT) aplikasi (uji aplikasi sesuai spesifikasi kebutuhan)',
                'buktidukung' => 'Dokumen yang mendukung',
            ],
            [
                'standar_indikator_id' => '56',
                'rekomendasi' => 'Implementasikan sistem pemantauan/alert untuk mendeteksi aktivitas tidak biasa
',
                'buktidukung' => 'Screenshot sistem pemantauan',
            ],
            [
                'standar_indikator_id' => '56',
                'rekomendasi' => 'Menerapkan CAPTCHA',
                'buktidukung' => 'Screenshot implementasi CAPTCHA',
            ],
            [
                'standar_indikator_id' => '57',
                'rekomendasi' => 'Menerapkan rate limiting',
                'buktidukung' => 'Screenshot pesan peringatan dari aplikasi saat rate limiting bekerja ketika bruteforce',
            ],
            [
                'standar_indikator_id' => '57',
                'rekomendasi' => 'Menggunakan CSRF token',
                'buktidukung' => 'Screenshot DevTools untuk CSRF saat submit form',
            ],
            [
                'standar_indikator_id' => '58',
                'rekomendasi' => 'Terdapat sistem peringatan serangan/aktifitas tidak biasa melalui Email/Telegram/dll',
                'buktidukung' => 'Screenshot sistem peringatan',
            ],
            [
                'standar_indikator_id' => '59',
                'rekomendasi' => 'Mengatur limit jumlah file dan kuota penyimpanan per user',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '60',
                'rekomendasi' => 'Mempunyai mekanisme File ditolak jika tidak sesuai tipe yang diizinkan (misalnya hanya PDF, JPEG, PNG)',
                'buktidukung' => 'Screenshot pesan dari aplikasi jika tipe file tidak sesuai',
            ],
            [
                'standar_indikator_id' => '60',
                'rekomendasi' => 'Mempunyai MIME type validation di backend',
                'buktidukung' => 'Screenshot source code fungsi upload untuk memvalidasi MIME',
            ],
            [
                'standar_indikator_id' => '61',
                'rekomendasi' => 'Mempunyai mekanisme pembersihan metadata pada file sebelum disimpan/export',
                'buktidukung' => 'Screenshot source code pembersihan metadata pada file sebelum disimpan/export',
            ],
            [
                'standar_indikator_id' => '62',
                'rekomendasi' => 'Integrasi dengan antivirus / antimalware',
                'buktidukung' => 'Screenshot penggunaan AntiVirus/Malware aktif',
            ],
            [
                'standar_indikator_id' => '62',
                'rekomendasi' => 'Implementasi pemindaian sebelum file disimpan',
                'buktidukung' => 'Screenshot AntiVirus/Malware tidak expired',
            ],
            [
                'standar_indikator_id' => '63',
                'rekomendasi' => 'File .php atau script hanya dapat diunduh, bukan dijalankan dari browser (tidak dapat dieksekusi)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '64',
                'rekomendasi' => 'Nonaktifkan tampilan error di production',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '64',
                'rekomendasi' => 'Melakukan Disable directory listing di server (Apache/nginx)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '64',
                'rekomendasi' => 'Nonaktifkan debug mode dan hanya aktifkan jika di development environment.',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '65',
                'rekomendasi' => 'Melakukan penyimpanan salinan: File konfigurasi (.env, nginx.conf, php.ini), Dependensi (composer.lock, package-lock.json, requirements.txt)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '66',
                'rekomendasi' => 'Menghapus File contoh (/examples, /samples, README, .gitignore dan lainnya) di production, Endpoint (/phpinfo.php, /server-status, /debug, /docs dan lainnya)',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '66',
                'rekomendasi' => 'Memastikan direktori deployment hanya berisi file yang dibutuhkan untuk menjalankan aplikasi.',
                'buktidukung' => 'Screenshot source code konfigurasi',
            ],
            [
                'standar_indikator_id' => '67',
                'rekomendasi' => 'Menggunakan hash atau checksum untuk memverifikasi file public SHA256 hash saat file diunggah/dipublish',
                'buktidukung' => 'Screenshot SRI',
            ],
            [
                'standar_indikator_id' => '67',
                'rekomendasi' => 'Menggunakan Subresource Integrity (SRI) untuk file JS/CSS dari CDN',
                'buktidukung' => 'Screenshot SRI',
            ],
            [
                'standar_indikator_id' => '67',
                'rekomendasi' => 'Melakukan validasi integritas file secara berkala dengan script',
                'buktidukung' => 'Screenshot SRI',
            ],
            [
                'standar_indikator_id' => '68',
                'rekomendasi' => 'Menggunakan HTTP Security Headers: Content-Security-Policy, X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Referrer-Policy: strict-origin-when-cross-origin, Strict-Transport-Security',
                'buktidukung' => 'Screenshot hasil scan dengan https://securityheaders.com menunjukkan grade A',
            ],

        ];

        foreach ($rekomendasistandardList as $item) {
            RekomendasiStandard::create($item);
        }
    }
}
