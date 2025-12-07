@extends('adminlte::page')

@section('title', 'Welcome PERISAI !')


{{-- @section('content_header')
    <h4>Perangkat Daerah : {{ auth()->user()->opd->namaopd }}</h4>
@endsection --}}
@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif {{ $tahunAktifGlobal ?? '-' }}
            @if (($kunci ?? null) === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd ?? '-') }}
        </span>
    </li>
@endsection

@section('content_header')
    <h3>Welcome PERISAI !</h3>

    <ul>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        <li>Fitur PERISAI adalah mulai dari inventarisir Aset sampai ke Analisa Risiko Keamanan aset.</li>
        <li>Yang dimaksud dengan <b>Aset</b> dalam PERISAI adalah <b>ASET INFORMASI</b> yang mendukung kinerja organisasi
            dalam menjalakan proses bisnis/layanannya.</b>
        </li>
        <li><b>Pemilik Aset</b> adalah Perangkat Daerah, mempunyai kewajiban untuk bertanggungjawab terhadap proses
            bisnis/layanannya, pengelolaan aset informasi, pengukuran
            nilai aset,
            klasifikasi aset, kategorisasi Sistem Elektronik, penilaian vitalitas, penilaian kepatuhan,
            pemetaan risiko, analisis risiko, serta penyusunan dan implementasi mitigasi risiko.</li>
        <li><b>Pemilik Risiko</b> adalah Kepala Perangkat Daerah / UPTD, mempunyai kewajiban untuk bertanggungjawab
            menyetujui rencana mitigasi risiko, menetapkan tingkat risiko yang
            dapat diterima (acceptable risk), menyetujui residual risk, serta memastikan dukungan sumber daya yang
            diperlukan.</li>
        <li>Periode pemutahiran data Aset Informasi pada PERISAI wajib dilakukan sekali setahun oleh Pemilik
            Risiko.</li>
    </ul>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
            <h5>Kepada Yth <b>{{ auth()->user()->opd->namaopd }},</b> sebagai salah satu Perangkat Daerah di Pemprov Bali
                yang menjadi <b>Pemilik Aset Informasi</b>, sangat penting untuk
                mengetahui hal-hal sebagai berikut : </h5>
            <ol>
                <li>Pemprov Bali telah mempunyai kebijakan keamanan yaitu <B>KEPGUB BALI
                        NO 584/03-E/HK/2024
                        tentang PEDOMAN MANAJEMEN
                        ASET KEAMANAN INFORMASI DAN STANDAR TEKNIS
                        DAN PROSEDUR KEAMANAN SPBE DI LINGKUNGAN PEMPROV BALI</b>. Seluruh aset informasi baik Sistem
                    Elektronik, Perangkat Keras dan lainnya, wajib mematuhi standar di
                    atas.</li>

                <li>Untuk memastikan keamanan aset, pemutahiran seluruh data dan informasi yang ada di dalam PERISAI wajib
                    dilakukan minimal 1(satu) tahun sekali.</li>

                <li>Dalam rangka mematuhi amanat UU No 27 tahun 2022 tentang Pelindungan Data Pribadi, agar masing-masing
                    Perangkat Daerah/UPTD termasuk
                    <b>{{ auth()->user()->opd->namaopd }}</b> dapat menunjuk Pejabat Pengendali Data Pribadinya yang akan
                    bertugas
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
            <h1>RISE Now ! [RECOGNISE.IDENTIFY.SECURE.ENHANCED]</h1>
        </div>

    </div>
@endsection

<script>
    // Reload jika halaman dipulihkan dari BFCache (Back/Forward)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted || (performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
            location.reload();
        }
    });

    // Safari/Firefox lama: handler unload kosong ini mematikan BFCache
    window.addEventListener('unload', function() {});
</script>
