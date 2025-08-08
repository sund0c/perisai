<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Aset KAMI</title>
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
    </style>
</head>

<body>
    <h2>Rekap Aset KAMI Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($namaOpd) }}</h3>
    <table>
        <thead>
            <tr>
                <th style="width:auto;text-align: center;"rowspan="2">KLASIFIKASI ASET</th>
                <th style="width:10%;text-align: center;" rowspan="2">JUMLAH ASET</th>
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
                <tr>
                    <td>{{ $klasifikasi->klasifikasiaset }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_aset }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_tinggi ?? 0 }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_sedang ?? 0 }}</td>
                    <td style="text-align: center;">{{ $klasifikasi->jumlah_rendah ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table><BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset</b> dalam PERISAI adalah <span class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li>Periode pemutahiran data PERISAI <b>wajib dilakukan sekali setahun oleh Pemilik Aset.</b> </li>
    </ol>
</body>

</html>
