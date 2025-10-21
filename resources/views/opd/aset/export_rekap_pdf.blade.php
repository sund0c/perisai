<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Aset</title>
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

        .f12 {
            font-size: 14px;
        }

        h2,
        h3,
        h4,
        {
        margin: 0;
        padding: 0;
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
            margin: 170px 50px 70px 50px;
        }


        .header {
            position: fixed;
            top: -120px;
            left: 0px;
            right: 5px;
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
            /* ruang kosong supaya teks turun & tidak timpa gambar */
        }

        .header h2,
        .header h3 {
            margin: 6px 0;
        }

        .matik-list ul {
            margin: 0;
            padding-left: 1.2rem;
            /* default untuk nested */
        }

        /* level pertama */
        .matik-list>ul {
            padding-left: 1em;
            /* mepet kiri */
            list-style-type: disc;
            /* bullet bulat */
        }

        /* level kedua */
        .matik-list>ul>li>ul {
            padding-left: 1.5rem;
            list-style-type: square;
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%" style="border:none;">
            <tr>
                <!-- KIRI: judul + subs (tetap) -->
                <td style="vertical-align: top;border:none;">
                    <h2 style="margin:0;">Rekap Jumlah dan Nilai Aset Informasi :: Tahun {{ $tahunAktifGlobal ?? '-' }}
                    </h2>
                    <h3 style="margin:0;">Pemilik Risiko: {{ strtoupper($namaOpd) }}</h3>

                    <div class="subs" style="margin-top:2px;">
                        Pemilik Risiko bertanggungjawab terhadap proses bisnis/layanannya dengan cara pengelolaan aset
                        yaitu dari
                        mulai melakukan Pengukuran Nilai Aset, Kategorisasi SE termasuk pemenuhan standar, Pemetaan
                        Risiko, Analisa
                        Risiko, pembuatan Rencana Tindak Lanjut dan implementasi mitigasi risiko, sampai menjadi
                        <i>Lead Auditee</i> dalam Audit Keamanan
                    </div>
                </td>

                <!-- KANAN: dua logo sejajar -->
                <td style="width: 100px; vertical-align: top; text-align: right; white-space: nowrap;border:none;">
                    <img src="{{ public_path('images/logobaliprovcsirt.png') }}" alt="Logo"
                        style="height:70px; vertical-align: top; margin-right:0px;">
                    <img src="{{ public_path('images/tlp/tlp_teaser_green.jpg') }}" alt="TLP:GREEN"
                        style="height:70px; vertical-align: top;">
                </td>
            </tr>
        </table>
    </div>

     <!-- Add space for bottom -->
    <table style="margin-top: -10px;">
        <thead>
            <tr>
                <th style="width:auto;text-align: center;"rowspan="2">KLASIFIKASI ASET</th>
                <th style="width:15%;text-align: center;" rowspan="2">JUMLAH ASET</th>
                <th style="text-align: center;" colspan="3">NILAI ASET</th>
            </tr>
            <tr>
                <th style="width:10%;background-color:#FF0000; color:#fff;text-align: center;">TINGGI</th>
                <th style="width:10%;background-color:#FFD700; color:#000;text-align: center;">SEDANG</th>
                <th style="width:10%;background-color:#00B050; color:#fff;text-align: center;">RENDAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($klasifikasis as $klasifikasi)
                <tr">
                    <td><b>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}</b>

                    </td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_aset }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_tinggi ?? 0 }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_sedang ?? 0 }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_rendah ?? 0 }}</td>
                    </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color:#eeeeee; font-weight:bold;height:200px;">
                <td style="text-align: center;">TOTAL JUMLAH ASET</td>
                <td style="text-align: center;">
                    {{ $klasifikasis->sum('jumlah_aset') }}
                </td>
                <td style="text-align: center;">
                    {{ $klasifikasis->sum('jumlah_tinggi') }}
                </td>
                <td style="text-align: center;">
                    {{ $klasifikasis->sum('jumlah_sedang') }}
                </td>
                <td style="text-align: center;">
                    {{ $klasifikasis->sum('jumlah_rendah') }}
                </td>
            </tr>
        </tfoot>
    </table><BR>
    <h4>A. KETERANGAN KLASIFIKASI ASET</h4>
    <div class="matik-list">
        <ul>
            @foreach ($klasifikasis as $klasifikasi)
                <li><b>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}</b>
                    <ul>
                        @foreach ($klasifikasi->subklasifikasi as $sub)
                            <li>{{ $sub->subklasifikasiaset }} : {{ $sub->penjelasan }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
    <BR>
    <h4>B. KETERANGAN NILAI ASET (CIAAN )</h4>
    <ol>
        @foreach ($ranges as $range)
            <li><b>{{ $range->nilai_akhir_aset }} :</b> {{ $range->deskripsi }}</li>
        @endforeach
    </ol><BR>
    <h4>C. CATATAN LAIN</h4>
    <ol>
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
        {{-- <li>Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
                bisnis/layanannya.</strong>
        </li> --}}
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan reviu dan pemutahiran data PERISAI yang
            dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
            jadwal dari Diskominfos Prov Bali. </li>
    </ol>
</body>

</html>
