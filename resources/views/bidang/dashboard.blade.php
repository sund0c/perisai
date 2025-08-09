@extends('adminlte::page')

@section('title', 'Welcome PERISAI !')

{{-- @section('content_header')
    <h4>Perangkat Daerah : {{ auth()->user()->opd->namaopd }}</h4>
@endsection --}}
@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
        </span>
    </li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h3>Welcome PERISAI !</h3>

            <ul>
                <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
                    <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali
                </li>
                <li>Fitur PERISAI adalah mulai dari Inventarisir aset sampai ke Analisa Risiko aset.</li>
                <li>Yang dimaksud dengan <b>Aset</b> dalam PERISAI adalah <b>khusus aset yang terkait
                        dengan pelindungan data dan keamanan informasi.</b>
                </li>
                <li>PERISAI dikelola oleh
                    Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)</li>
                <li>Periode pemutahiran data aset PERISAI wajib dilakukan sekali setahun oleh Pemilik Aset.</li>
                <li>Review tindak lanjut dari analisa risiko setiap aset wajib dilakukan minimal setiap 6 bulan sekali.
                </li>
            </ul>
        </div>
    </div>
    {{-- <div class="card">
        <div class="card-body">
            <h5>Kepada Yth <b>{{ auth()->user()->opd->namaopd }},</b> sebagai salah satu Perangkat Daerah di Pemprov Bali
                yang mempunyai proses bisnis dan layanan, mempunyai dan mengelola aset informasi, sangat penting untuk
                mengetahui hal-hal sebagai berikut : </h5>
            <ol>
                <li>Pemprov Bali telah mempunyai kebijakan keamanan yaitu <B>KEPGUB BALI
                        NO 584/03-E/HK/2024
                        tentang PEDOMAN MANAJEMEN
                        KEAMANAN INFORMASI DAN STANDAR TEKNIS
                        DAN PROSEDUR KEAMANAN SPBE DI LINGKUNGAN PEMPROV BALI</b>. Seluruh aset informasi baik Sistem
                    Elektronik, Perangkat Keras dan lainnya, wajib mematuhi standard di
                    atas</li>
                <li>Khusus untuk aset Perangkat Lunak /Sistem Elektronik /Aplikasi, harus dilakukan IT Security Assessment
                    (ITSA) secara berkala.
                    ITSA dari Dinas Kominfos terbagi dalam 3(tiga) tahapan yaitu T1.Black-Box (sebelum pertama publish),
                    T2.PTKKA dan T3.Grey-Box (PoC dari PTKKA).
                    Laporan hasil ITSA akan menghasilkan rekomendasi terkait keberlangsungan operasional aset. Tidak menutup
                    kemungkinan aset akan dinonaktifkan jika ditemukan berdampak risiko kritis (terutama yang menyangkut
                    kebocoran data sensitif).</li>
                <li>Pemutahiran data aset informasi dalam PERISAI wajib dilakukan setiap tahun sekali. Analisa risiko dan
                    review tindak lanjutnya wajib dilakukan setiap 6 bulan sekali (semester). Periode akan dibuka secara
                    bersamaan oleh Dinas Kominfos Prov Balli</li>
                <li>{{ auth()->user()->opd->namaopd }} sebagai pemilik proses bisnis dan layanan, pemilik dan pengelola
                    aset, bertanggungjawab penuh terhadap upaya pengamanan aset informasinya sendiri dan tetap berkoordinasi
                    dengan Dinas Kominfos Prov Bali. </li>
                <li>Dalam rangka mematuhi amanat UU No 27 tahun 2022 tentang Pelindungan Data Pribadi, agar masing-masing
                    Perangkat Daerah/UPTD termasuk
                    {{ auth()->user()->opd->namaopd }} dapat menunjuk Pejabat Pengendali Data Pribadinya yang akan bertugas
                    untuk : <ul>
                        <li>melakukan pemrosesan Data Pribadi secara terbatas dan spesifik, sah secara hukum, dan
                            transparan;
                        <li>melakukan pemrosesan Data Pribadi sesuai dengan tujuan pemrosesan Data Pribadi</li>
                        <li>melindungi Data Pribadi dari pemrosesan yang tidak sah</li>
                        <li>menjaga kerahasiaan Data Pribadi saat melakukan pemrosesan Data Pribadi</li>
                        <li>melakukan pengawasan terhadap setiap pihak yang terlibat dalam pemrosesan Data Pribadi</li>
                    </ul>
                </li>

            </ol>
            <h1>#sec_rityWithoutUisNotCompleted! #jagaRuangSiber</h1>
        </div>

    </div> --}}
@endsection
