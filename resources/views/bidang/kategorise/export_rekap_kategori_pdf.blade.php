<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Kategori per Kategori</title>
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

        h2,
        h3,
        h4 {
            margin: 0;
            padding: 0;
        }

        h3 {
            margin-bottom: 10px;
        }

        h4 {
            margin-bottom: 2px;
            padding-bottom: 0;
        }

        ol {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .underline {
            text-decoration: underline;
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
                    <h2 style="margin:0;">KATEGORI SE {{ strtoupper($kategori) }} :: Tahun
                        {{ $tahunAktifGlobal ?? '-' }}
                    </h2>
                    <h3 style="margin:0;">Pemilik Aset: {{ strtoupper($namaOpd) }}</h3>

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
                <th style="width: auto;">NAMA ASET</th>
                <th style="width: 160px;">OPD</th>
                <th style="width: 160px;">SUB KLASIFIKASI</th>
                <th style="width: 160px;">LOKASI</th>
                <th style="width: 100px;">KATEGORI</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1;@endphp
            @foreach ($data as $aset)
                <tr>
                    <td style="text-align: right">{{ $no++ }}</td>
                    <td>{{ $aset->kode_aset }}</td>
                    <td>{{ $aset->nama_aset }}</br>
                        <small>{{ $aset->keterangan }}</small>
                    </td>
                    <td>{{ optional($aset->opd)->namaopd ?? '-' }}</td>
                    <td>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}</br>
                        <small>{{ $aset->spesifikasi_aset }}</small>
                    </td>
                    <td>{{ $aset->lokasi }}</br>
                        <small>{{ $aset->link_url }}</small>
                    </td>


                    @php
                        $skor = $aset->kategoriSe->skor_total ?? null;

                        $warna = '#999';
                        $label = 'BELUM DINILAI';
                        $warnaTeks = '#000';

                        if ($skor !== null) {
                            $range = $rangeSes->first(function ($r) use ($skor) {
                                return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
                            });

                            if ($range) {
                                $warna = $range->warna_hexa ?? $warna;
                                $label = $range->nilai_akhir_aset ?? $label;
                            }
                        }
                    @endphp
                    <td style="background-color: {{ $warna }}; color: {{ $warnaTeks }}; font-weight: bold;">
                        {{ $label }}
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    <BR>
    <h4>CATATAN</h4>
    <ol>
        <li>Dokumen ini menggunakan Kode TLP:AMBER+STRICT mengindikasikan mengandung informasi yang cukup sensitif.
            Informasi di dalam dokumen ini hanya boleh didistribusikan kepada Pemilik Aset dan Dinas Kominfos Prov Bali.
            Tidak boleh diberikan secara langsung dan atau otomatis ke pihak lain.
        </li>
        <li>PERISAI adalah sistem elektronik untuk membantu pengelolaan risiko aset informasi di lingkup Pemprov Bali
            agar menjadi lebih efektif dengan mengusung konsep RISE : Recognise.Identify.Secure.Enhanced. PERISAI
            dikelola oleh Bidang Persandian Dinas Kominfos Provinsi Bali.
        </li>
        <li>Kategorisasi SE dimutahirkan setahun sekali</li>
    </ol>

</body>

</html>
