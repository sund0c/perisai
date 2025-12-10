<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Vitalitas SE per Kategori</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .font-dejavu {
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        @page {
            margin: 160px 50px 70px 50px;
        }

        .header {
            position: fixed;
            top: -130px;
            left: 0px;
            right: 0px;
            text-align: left;
        }

        .header img.tlp {
            position: absolute;
            top: 0;
            right: -30;
            width: 150px;
        }

        .header h2,
        .header h3 {
            margin: 6px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%" style="border:none;">
            <tr>
                <td style="vertical-align: top;border:none;">
                    @php
                        $k = [
                            'novital' => 'Non Vital',
                            'belum' => 'Belum Dinilai',
                            'vital' => 'VITAL',
                        ][$kategori] ?? 'Belum Dinilai';
                    @endphp
                    <h2 style="margin:0;">SE {{ $k }} :: Tahun
                        {{ $tahunAktifGlobal ?? '-' }}
                    </h2>
                    <h3 style="margin:0;">Pemilik Aset: {{ strtoupper($namaOpd ?? 'SEMUA OPD') }}</h3>

                     <div class="subs" style="margin-top:2px;">
                        Pemilik Aset bertanggung jawab terhadap proses bisnis/layanannya, pengelolaan aset informasi,
                        pengukuran nilai aset,
                        klasifikasi aset, kategorisasi Sistem Elektronik, penilaian vitalitas, penilaian kepatuhan,
                        pemetaan risiko, analisis risiko, serta penyusunan dan implementasi mitigasi risiko.</br>
                        Kepala OPD/UPTD sebagai Pemilik Risiko bertanggung jawab untuk menyetujui rencana
                        mitigasi risiko, menetapkan tingkat risiko yang
                        dapat diterima (acceptable risk), menyetujui residual risk, serta memastikan dukungan sumber
                        daya yang diperlukan.
                    </div>
                </td>

                <!-- KANAN: dua logo sejajar -->
                <td style="width: 100px; vertical-align: top; text-align: right; white-space: nowrap;border:none;">
                    <img src="{{ public_path('images/logobaliprovcsirt.png') }}" alt="Logo"
                        style="height:70px; vertical-align: top; margin-right:0px;">
                    @if ($kategori === 'vital')
                        <img src="{{ public_path('images/tlp/tlp_teaser_red.jpg') }}" alt="TLP:RED"
                            style="height:70px; vertical-align: top;">
                    @elseif ($kategori === 'novital')
                        <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.jpg') }}" alt="TLP:AMBER+STRICT"
                            style="height:70px; vertical-align: top;">
                    @else
                        <img src="{{ public_path('images/tlp/tlp_teaser_green.jpg') }}" alt="TLP:GREEN"
                            style="height:70px; vertical-align: top;">
                    @endif
                </td>
            </tr>
        </table>
    </div>


    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 90px;">KODE ASET</th>
                <th style="width: auto;">NAMA ASET</th>
                <th style="width: 160px;">OPD</th>
                <th style="width: 200px;">SUB KLASIFIKASI</th>
                <th style="width: 200px;">LOKASI</th>
                <th style="width: 100px;">VITALITAS</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1;@endphp
            @foreach ($data as $aset)
                @php
                    $skor = $aset->vitalitasSe->skor_total ?? null;

                    $skor = $aset->vitalitasSe->skor_total ?? null;

                    if (is_null($skor)) {
                        $label = '(belum dinilai)';
                        $warna = '#6c757d'; // abu
                        $warnaTeks = '#fff';
                    } elseif ($skor >= 15) {
                        $label = 'VITAL';
                        $warna = '#dc3545'; // merah
                        $warnaTeks = '#fff';
                    } else {
                        $label = 'Non Vital';
                        $warna = '#28a745'; // hijau
                        $warnaTeks = '#fff';
                    }
                @endphp
                <tr>
                    <td style="text-align: right">{{ $no++ }}</td>
                    <td>{{ $aset->kode_aset }}</td>
                    <td>{{ $aset->nama_aset }}</br>
                        <small>{{ $aset->keterangan }}</small>
                    </td>
                    <td>{{ $aset->opd->namaopd ?? '-' }}</td>
                    <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</br>
                        <small>{{ $aset->spesifikasi_aset }}</small>
                    </td>
                    <td>{{ $aset->lokasi }}</br>
                        <small>{{ $aset->link_url }}</small>
                    </td>
                    <td style="background-color: {{ $warna }}; color: {{ $warnaTeks }}; font-weight: bold;">
                        {{ $label }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <h4>Catatan</h4>
    <ol>
        <li>Pengukuran ini adalah self-assessment dari sudut pemilik aset/risiko.
            Seluruh aset harus diajukan ke BSSN untuk dilakukan evaluasi.
            Aset yang terkonfirmasi termasuk dalam aset Vital oleh BSSN, akan ditetapkan oleh Kepala BSSN.
        </li>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitifitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            TLP:GREEN = Pengungkapan terbatas, penerima dapat menyebarkan ini dalam komunitasnya.
            Sumber dapat menggunakan TLP:GREEN ketika informasi berguna untuk meningkatkan kesadaran dalam
            komunitas mereka yang lebih luas. Penerima dapat berbagi informasi TLP:GREEN dengan rekan dan
            organisasi mitra dalam komunitas mereka, tetapi tidak melalui saluran yang dapat diakses publik.
            Informasi TLP:GREEN tidak boleh dibagikan di luar komunitas. Jika "komunitas" tidak ditentukan,
            asumsikan komunitas keamanan/pertahanan siber.
        </li>
        <li>PERISAI adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan reviu dan pemutahiran data PERISAI yang
            dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
            jadwal dari Diskominfos Prov Bali. </li>
    </ol>
</body>

</html>
