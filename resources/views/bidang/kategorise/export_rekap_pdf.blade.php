<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Kategori SE</title>
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
    <h2>Kategorisasi Sistem Elektronik (SE) :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemerintah Provinsi Bali</h3>
    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th style="width:200px">KATEGORI SE</th>
                <th>JUMLAH ASET</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background-color:#FF0000; color:#fff;text-align: left">TINGGI</td>
                <td>
                    {{ $tinggi }}
                </td>
            </tr>
            <tr>
                <td style="width:10%;background-color:#FFD700; color:#000;text-align: left">SEDANG</td>
                <td> {{ $sedang }}
                </td>
            </tr>
            <tr>
                <td style="width:10%;background-color:#00B050; color:#fff;text-align: left">RENDAH</td>
                <td> {{ $rendah }}
                </td>
            </tr>
            <tr>
                <td class="bg-secondary text-white" style="text-align: left">Belum Dinilai</td>
                <td> {{ $belum }}
                </td>
            </tr>
            <tr class="font-weight-bold">
                <td>Total</td>
                <td>{{ $total }}</td>
            </tr>
        </tbody>
    </table>
    <BR><BR><BR>
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
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
    </ol>
</body>

</html>
