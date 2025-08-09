<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PTKKA Aset TIK Pemprov Bali</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Daftar PTKKA Aset TIK</h2>
    <h4 style="text-align: center;">data diekspor Tgl. {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h4>

    <table>
        <thead>
            <tr>
                <th width="70">KODE ASET</th>
                <th>NAMA ASET</th>
                <th width="70">STANDAR</th>
                <th>PEMILIK ASET</th>
                <th>TGL PTKKA</th>
                <th width="100">TINGKAT KEPATUHAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $r)
                <tr>
                    <td>{{ $r['kode_aset'] }}</td>
                    <td>{{ $r['nama_aset'] }}</td>
                    <td>{{ $r['kategori'] }}</td>
                    <td>{{ $r['opd'] }}</td>
                    <td class="text-center">{{ $r['tanggal'] }}</td>
                    <td class="text-center">{{ $r['skor_text'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
