<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aspek</title>
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
    <h2>Daftar Aspek Keamanan Informasi</h2>
    <h4>Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi</h4>
    <hr>
    <h4>Kategori {{ $kategori->nama }}</h4><BR>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">URUTAN</th>
                <th>ASPEK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kategori->fungsi as $fungsi)
                <tr>
                    <td style="text-align: center">{{ $fungsi->urutan }}</td>
                    <td>
                        {{ $fungsi->nama }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
