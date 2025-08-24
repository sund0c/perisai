<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Aset KamI</title>
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
            margin: 170px 50px 70px 40px;
        }


        .header {
            position: fixed;
            top: -120px;
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
            line-height: 1.2;
            font-size: 0.9em;
            margin-right: 100px;
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
        <img src="{{ public_path('images/tlp/tlp_teaser_green.png') }}" alt="TLP:GREEN" class="tlp">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
        <h2>Rekap Jumlah dan Nilai Aset KamI :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
        <h3>Pemilik Risiko: {{ strtoupper($namaOpd) }}</h3>
        <h3 style="line-height:1; margin-bottom:0;">
            {{-- Klasifikasi Aset : {{ $klasifikasi->klasifikasiaset }} --}}
        </h3>

        <div class="subs">
            {{-- {{ $subs->pluck('subklasifikasiaset')->implode(', ') ?: '-' }} --}}
        </div>
    </div>
    <table>
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
    <h4>B. KETERANGAN NILAI ASET</h4>
    <ol>
        @foreach ($ranges as $range)
            <li><b>{{ $range->nilai_akhir_aset }} :</b> {{ $range->deskripsi }}</li>
        @endforeach
    </ol><BR>
    <h4>C. CATATAN LAIN</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitivitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:GREEN berarti boleh dibagikan di dalam komunitas/sektor (misalnya antar instansi pemerintah),
                tapi tidak untuk publik luas.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset KamI</b> dalam PERISAI adalah Aset Keamanan Informasi, yaitu <span
                class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan minimal sekali setahun oleh Pemilik Aset. Pemutahiran akan dilakukan serempak, menunggu
                informasi dari Diskominfos Prov Bali.</b> </li>
    </ol>
</body>

</html>
