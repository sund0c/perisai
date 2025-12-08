<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Vitalitas SE</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
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

        body {
            font-family: sans-serif;
            font-size: 12px;
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

        .header .subs {
            margin-top: -5px;
            line-height: 1.2;
            font-size: 0.9em;
            margin-right: 0px;
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

                    <h2 style="margin:0;">Status Vital SE :: Tahun
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
                    <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.jpg') }}" alt="TLP:AMBER+STRICT"
                        style="height:70px; vertical-align: top;">
                </td>
            </tr>
        </table>
    </div>


    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 90px;">KODE ASET</th>
                <th style="width: 160px;">NAMA ASET</th>
                <th style="width: 160px;">OPD</th>
                <th style="width: 180px;">SUB KLASIFIKASI</th>
                <th style="width: 160px;">LOKASI</th>
                <th style="width: 100px;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1;@endphp
            @foreach ($data as $aset)
                @php
                    $skor = $aset->vitalitasSe->skor_total ?? null;

                    // Default jika belum dinilai
                    $label = 'BELUM DINILAI';
                    $warna = '#6c757d'; // abu
                    $warnaTeks = '#fff';

                    if (!is_null($skor)) {
                        if ($skor >= 15) {
                            $label = 'VITAL';
                            $warna = '#dc3545'; // merah
                        } else {
                            $label = 'Tidak Vital';
                            $warna = '#28a745'; // hijau
                        }
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
    <h4>CATATAN</h4>
    <ol>
        <li>
            Pengukuran ini adalah self-assessment oleh Pemilik Aset.
            Seluruh aset harus diajukan ke BSSN untuk dilakukan validasi dan konfirmasi. Aset yang terkonfirmasi
            termasuk dalam aset Vital oleh BSSN, akan ditetapkan oleh Kepala BSSN.
        </li>
        <li>Dokumen ini menggunakan Kode TLP:AMBER+STRICT mengindikasikan mengandung informasi yang cukup sensitif.
            Informasi di dalam dokumen ini hanya boleh didistribusikan kepada Pemilik Aset dan Dinas Kominfos Prov
            Bali.
            Tidak boleh diberikan secara langsung dan atau otomatis ke pihak lain.
        </li>
        <li>PERISAI adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
    </ol>

</body>

</html>
