<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kategori Standard</title>
    <style>
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
    </style>
</head>

<body>
    <h2>Kategori Standard</h2>
    <h4>Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi</h4><br>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;text-align: center">NO</th>
                <th>NAMA KATEGORI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kategoris as $kategori)
                <tr>
                    <td style="text-align: center">{{ $kategori->id }}</td>
                    <td>
                        {{ $kategori->nama }}
                    </td>v>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
